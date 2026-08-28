<?php

namespace App\Services;

use App\Enums\FiscalNoteEventType;
use App\Enums\FiscalNoteStatus;
use App\Enums\NFeTransmissionState;
use App\Enums\FiscalDestination;
use App\Enums\SalesOrderStatus;
use App\Models\Client;
use App\Models\Company;
use App\Models\FiscalNote;
use App\Models\FiscalNoteEvent;
use App\Models\FiscalNoteItem;
use App\Models\FiscalOperationLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificate;
use NFePHP\Common\UFList;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use stdClass;

class NFeService
{
    protected Tools $tools;

    protected ?Company $emitter = null;

    public function distributeDfe(int $lastNsu): string
    {
        return $this->tools->sefazDistDFe($lastNsu);
    }

    public function manifestRecipient(string $accessKey, int $eventType, string $justification = '', int $sequence = 1): string
    {
        return $this->tools->sefazManifesta($accessKey, $eventType, $justification, $sequence);
    }

    public function consultAccessKey(string $accessKey): string
    {
        return $this->tools->sefazConsultaChave($accessKey, config('nfe.environment'));
    }

    public function inutilize(int $series, int $start, int $end, string $reason): array
    {
        if (mb_strlen(trim($reason)) < 15 || $start > $end) {
            throw new \InvalidArgumentException('Informe uma faixa válida e justificativa com ao menos 15 caracteres.');
        }

        $response = $this->tools->sefazInutiliza($series, $start, $end, $reason, config('nfe.environment'));
        $document = $this->loadResponseDocument($response);
        $xpath = new \DOMXPath($document);
        $info = $xpath->query('//*[local-name()="infInut"]')->item(0);
        $result = [
            'success' => $this->xpathValue($xpath, './*[local-name()="cStat"]', $info) === '102',
            'code' => $this->xpathValue($xpath, './*[local-name()="cStat"]', $info),
            'message' => $this->xpathValue($xpath, './*[local-name()="xMotivo"]', $info),
            'protocol' => $this->xpathValue($xpath, './*[local-name()="nProt"]', $info),
        ];

        $path = config('nfe.xml_path')."/inutilizacoes/{$series}_{$start}_{$end}.xml";
        Storage::disk(config('nfe.xml_disk'))->put($path, $response);
        FiscalOperationLog::create([
            'company_id' => $this->emitter?->id,
            'operation' => 'inutilization',
            'payload' => compact('series', 'start', 'end', 'reason'),
            'result' => $result + ['xml_path' => $path],
            'user_id' => Auth::id(),
            'occurred_at' => now(),
        ]);

        return $result;
    }

    public function __construct()
    {
        $this->loadTools();
    }

    /**
     * Carrega certificado e configura Tools da biblioteca sped-nfe
     */
    protected function loadTools(): void
    {
        $configJson = $this->buildConfigJson();
        $certificate = $this->loadCertificate();

        $this->tools = new Tools($configJson, $certificate);
        $this->tools->model('55'); // NF-e modelo 55
    }

    /**
     * Constrói JSON de configuração para sped-nfe
     */
    protected function buildConfigJson(): string
    {
        $this->emitter = Company::where('is_active', true)->first();

        $razao = $this->emitter->social_name ?? config('nfe.razao_social');
        $cnpj = preg_replace('/\D/', '', $this->emitter->cnpj ?? config('nfe.cnpj'));

        $config = [
            'atualizacao' => now()->format('Y-m-d H:i:s'),
            'tpAmb' => config('nfe.environment'),
            'razaosocial' => $razao,
            'cnpj' => $cnpj,
            'siglaUF' => config('nfe.uf'),
            'schemes' => config('nfe.schemes_path'),
            'versao' => '4.00',
            'tokenIBPT' => config('nfe.ibpt_token'),
            'CSC' => config('nfe.csc'),
            'CSCid' => config('nfe.csc_id'),
        ];

        return json_encode($config);
    }

    /**
     * Carrega certificado digital do storage
     */
    protected function loadCertificate(): Certificate
    {
        $disk = config('nfe.cert_disk');
        $path = config('nfe.cert_path');
        $password = config('nfe.cert_password');

        if (! Storage::disk($disk)->exists($path)) {
            throw new \RuntimeException("Certificado digital não encontrado em: {$path}");
        }

        $pfxContent = Storage::disk($disk)->get($path);

        return Certificate::readPfx($pfxContent, $password);
    }

    /**
     * Transmite NF-e para SEFAZ
     */
    public function transmitir(FiscalNote $note, bool $sincrono = true): array
    {
        if ($note->status !== FiscalNoteStatus::Draft) {
            throw new \InvalidArgumentException('Apenas notas em rascunho podem ser transmitidas.');
        }

        $xml = $this->buildXml($note);

        // Assina o XML
        $xmlSigned = $this->tools->signNFe($xml);

        // Envia para SEFAZ
        $idLote = date('YmdHis').rand(1000, 9999);
        $indSinc = $sincrono ? 1 : 0;

        $response = $this->tools->sefazEnviaLote(
            [$xmlSigned],
            $idLote,
            $indSinc,
            false
        );

        $accessKey = $this->extractAccessKey($xmlSigned);
        $result = $this->parseTransmissionResponse($response, $accessKey);

        // Preserva os artefatos da transmissão antes de atualizar o estado local.
        $this->saveXml($note, $xmlSigned, 'enviado');
        $this->saveXml($note, $response, 'resposta');

        if ($result['state'] === NFeTransmissionState::Authorized) {
            $this->saveAuthorizedXml($note, $xmlSigned, $response, $accessKey);
        }

        $this->applyTransmissionResult($note, $result, $accessKey);

        return $result;
    }

    public function consultarRecibo(FiscalNote $note): array
    {
        if (! $note->receipt_number) {
            throw new \InvalidArgumentException('A NF-e não possui número de recibo para consulta.');
        }

        $response = $this->tools->sefazConsultaRecibo(
            $note->receipt_number,
            config('nfe.environment')
        );
        $result = $this->parseTransmissionResponse($response, $note->access_key);

        $this->saveXml($note, $response, 'resposta');

        if ($result['state'] === NFeTransmissionState::Authorized) {
            $signedPath = $note->xml_sent_path ?? $note->xml_path;
            $disk = config('nfe.xml_disk');

            if (! $signedPath || ! Storage::disk($disk)->exists($signedPath)) {
                throw new \RuntimeException('XML assinado não encontrado para compor o XML autorizado.');
            }

            $this->saveAuthorizedXml(
                $note,
                Storage::disk($disk)->get($signedPath),
                $response,
                $note->access_key
            );
        }

        $this->applyTransmissionResult($note, $result, $note->access_key);

        return $result;
    }

    /**
     * Constrói XML da NF-e
     */
    public function buildXml(FiscalNote $note): string
    {
        $this->validateIcmsRegime($note);
        $this->validateNoteTotal($note);
        $this->validatePaymentTotal($note);

        $make = new Make;
        $make->setCheckGtin();

        $infNFe = new stdClass;
        $infNFe->versao = '4.00';
        $make->taginfNFe($infNFe);

        // Tag IDE (identificação)
        $make->tagide($this->buildIde($note));

        if ($note->referenced_access_key) {
            $reference = new stdClass;
            $reference->refNFe = preg_replace('/\D/', '', $note->referenced_access_key);
            $make->tagrefNFe($reference);
        }

        // Tag EMIT (emitente)
        $make->tagEmit($this->buildEmit());
        $make->tagenderEmit($this->buildEnderEmit());

        // Tag DEST (destinatário)
        if ($note->client_id) {
            $client = Client::find($note->client_id);
            if ($client) {
                $make->tagdest($this->buildDest($client));
                $make->tagenderDest($this->buildEnderDest($client));
            }
        }

        if ($note->pickup_location) {
            $make->tagretirada($this->buildAlternativeLocation($note->pickup_location, $note));
        }

        if ($note->delivery_location) {
            $make->tagentrega($this->buildAlternativeLocation($note->delivery_location, $note));
        }

        if ($note->intermediary_cnpj && $note->intermediary_identifier) {
            $intermediary = new stdClass;
            $intermediary->CNPJ = preg_replace('/\D/', '', $note->intermediary_cnpj);
            $intermediary->idCadIntTran = $note->intermediary_identifier;
            $make->tagIntermed($intermediary);
        }

        // Itens
        foreach ($note->items as $item) {
            $make->tagprod($this->buildProd($item));
            $this->addProductSpecificGroups($make, $item);
            $make->tagimposto($this->buildImposto($item));

            if ($item->selective_tax_data) {
                $make->tagIS($this->itemGroup($item, $item->selective_tax_data));
            }

            if ($item->ibs_cbs_data) {
                $this->addTaxReformGroups($make, $item);
            }

            if ($item->csosn) {
                $make->tagICMSSN($this->buildIcmsSn($item));
            } else {
                $make->tagICMS($this->buildIcms($item));
            }

            if (! empty($item->icms_data['difal'])) {
                $make->tagICMSUFDest($this->buildIcmsUfDest($item));
            }

            if ($item->ipi_cst) {
                $make->tagIPI($this->buildIpi($item));
            }

            $make->tagPIS($this->buildPis($item));
            $make->tagCOFINS($this->buildCofins($item));
        }

        // Totais
        $make->tagICMSTot($this->buildIcmsTotal($note));

        if ($note->items->contains(fn (FiscalNoteItem $item): bool => (bool) $item->selective_tax_data)) {
            $make->tagISTot(new stdClass);
        }

        if ($note->items->contains(fn (FiscalNoteItem $item): bool => (bool) $item->ibs_cbs_data)) {
            $make->tagIBSCBSTot(new stdClass);
        }

        // Transporte
        $make->tagtransp($this->buildTransp($note));
        $this->addTransportGroups($make, $note);

        // Pagamento
        $make->tagpag($this->buildPag($note));
        foreach ($this->paymentDetails($note) as $payment) {
            $make->tagdetPag($this->arrayToObject($payment));
        }
        $this->addBillingGroups($make, $note);

        // Informações adicionais
        if ($note->notes) {
            $make->taginfAdic($this->buildInfAdic($note));
        }

        $xml = $make->montaNFe();

        if ($make->getErrors() !== []) {
            throw new \RuntimeException('Falha ao montar XML da NF-e: '.implode('; ', $make->getErrors()));
        }

        return $xml;
    }

    /**
     * Constrói stdClass para tag IDE
     */
    protected function buildIde(FiscalNote $note): stdClass
    {
        $std = new stdClass;
        $std->cUF = UFList::getCodeByUF(config('nfe.uf'));
        $std->cNF = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $std->natOp = $note->operation_nature;
        $std->mod = $note->type === 'nfce' ? 65 : 55;
        $std->serie = (int) $note->series;
        $std->nNF = (int) $note->invoice_number;
        $std->dhEmi = now()->format('Y-m-d\TH:i:sP');
        $std->tpNF = 1; // 0=Entrada, 1=Saída
        $std->idDest = ($note->destination ?? $this->resolveDestination($note))->value;
        $std->cMunFG = config('nfe.ibge_emitente');
        $std->tpImp = 1; // 1=Retrato, 2=Paisagem
        $std->tpEmis = $note->emission_type->value;
        $std->cDV = 0;
        $std->tpAmb = config('nfe.environment');
        $std->finNFe = $note->purpose->value;
        $std->indFinal = $note->consumer_type->value;
        $std->indPres = $note->presence_type->value;
        $std->indIntermed = $note->intermediary_cnpj ? 1 : 0;
        $std->procEmi = 0; // 0=Emissão própria
        $std->verProc = config('nfe.versao');

        return $std;
    }

    protected function resolveDestination(FiscalNote $note): FiscalDestination
    {
        $client = $note->client;

        if (! $client || ! $client->address_state) {
            return FiscalDestination::Internal;
        }

        if (strtoupper($client->address_state) === 'EX') {
            return FiscalDestination::Exterior;
        }

        return strtoupper($client->address_state) === strtoupper($this->emitter->address_state)
            ? FiscalDestination::Internal
            : FiscalDestination::Interstate;
    }

    protected function buildAlternativeLocation(array $location, FiscalNote $note): stdClass
    {
        $std = new stdClass;
        $taxNumber = preg_replace('/\D/', '', $location['tax_number'] ?? $note->client?->taxNumber ?? $this->emitter->cnpj);

        $std->{strlen($taxNumber) === 11 ? 'CPF' : 'CNPJ'} = $taxNumber;
        $std->xNome = $location['name'] ?? $note->display_client;
        $std->xLgr = $location['street'];
        $std->nro = $location['number'];
        $std->xCpl = $location['complement'] ?? null;
        $std->xBairro = $location['district'];
        $std->cMun = $location['ibge_code'];
        $std->xMun = $location['city'];
        $std->UF = $location['state'];
        $std->CEP = preg_replace('/\D/', '', $location['zip_code'] ?? '');
        $std->cPais = $location['country_code'] ?? '1058';
        $std->xPais = $location['country'] ?? 'BRASIL';

        return $std;
    }

    /**
     * Constrói stdClass para tag EMIT
     */
    protected function buildEmit(): stdClass
    {
        $std = new stdClass;
        $std->xNome = $this->emitter->social_name ?? config('nfe.razao_social');
        $std->xFant = $this->emitter->name;
        $std->IE = preg_replace('/\D/', '', $this->emitter->inscricao_estadual ?? config('nfe.inscricao_estadual'));
        $std->CNPJ = preg_replace('/\D/', '', $this->emitter->cnpj ?? config('nfe.cnpj'));
        $std->CRT = $this->emitter->crt ?? config('nfe.crt');

        if ($this->emitter->inscricao_municipal) {
            $std->IM = preg_replace('/\D/', '', $this->emitter->inscricao_municipal);
        }

        if ($this->emitter->cnae) {
            $std->CNAE = preg_replace('/\D/', '', $this->emitter->cnae);
        }

        return $std;
    }

    /**
     * Constrói stdClass para tag enderEmit
     */
    protected function buildEnderEmit(): stdClass
    {
        $std = new stdClass;
        $std->xLgr = $this->emitter->address_street;
        $std->nro = $this->emitter->address_number;
        $std->xCpl = $this->emitter->address_complement;
        $std->xBairro = $this->emitter->address_district;
        $std->cMun = $this->emitter->ibge_municipio ?? config('nfe.ibge_emitente');
        $std->xMun = $this->emitter->address_city;
        $std->UF = $this->emitter->address_state ?? config('nfe.uf');
        $std->CEP = preg_replace('/\D/', '', $this->emitter->address_zip_code);
        $std->cPais = '1058';
        $std->xPais = 'BRASIL';

        if ($this->emitter->phone) {
            $std->fone = preg_replace('/\D/', '', $this->emitter->phone);
        }

        return $std;
    }

    /**
     * Constrói stdClass para tag dest
     */
    protected function buildDest(Client $client): stdClass
    {
        $std = new stdClass;

        $taxNumber = preg_replace('/\D/', '', $client->taxNumber);
        if (strlen($taxNumber) === 14) {
            $std->CNPJ = $taxNumber;
        } else {
            $std->CPF = $taxNumber;
        }

        $std->xNome = $client->social_name ?? $client->name;
        $std->indIEDest = $client->ind_ie_dest ?? 9; // 9=Não contribuinte

        if ($client->inscricao_estadual && $client->ind_ie_dest == 1) {
            $std->IE = preg_replace('/\D/', '', $client->inscricao_estadual);
        }

        if ($client->email) {
            $std->email = $client->email;
        }

        return $std;
    }

    /**
     * Constrói stdClass para tag enderDest
     */
    protected function buildEnderDest(Client $client): stdClass
    {
        $std = new stdClass;
        $isExterior = strtoupper((string) $client->address_state) === 'EX';
        $std->xLgr = $client->address_street;
        $std->nro = $client->address_number;
        $std->xCpl = $client->address_complement;
        $std->xBairro = $client->address_district;
        $std->cMun = $isExterior ? '9999999' : $client->ibge_municipio;
        $std->xMun = $client->address_city;
        $std->UF = $client->address_state;
        $std->CEP = preg_replace('/\D/', '', $client->address_zip_code);
        $std->cPais = $isExterior ? '999' : '1058';
        $std->xPais = $isExterior ? 'EXTERIOR' : 'BRASIL';

        if ($client->phone_number) {
            $std->fone = preg_replace('/\D/', '', $client->phone_number);
        }

        return $std;
    }

    /**
     * Constrói stdClass para tag prod
     */
    protected function buildProd(FiscalNoteItem $item): stdClass
    {
        $std = new stdClass;
        $std->item = $item->item_number;
        $std->cProd = $item->product_code;
        $std->cEAN = $item->ean ?: 'SEM GTIN';
        $std->xProd = $item->description;
        $std->NCM = $item->ncm;
        $std->CEST = $item->cest;
        $std->cBenef = $item->tax_benefit_code;
        $std->EXTIPI = $item->ex_tipi;
        $std->CFOP = $item->cfop;
        $std->uCom = $item->unit;
        $std->qCom = $item->quantity;
        $std->vUnCom = $item->unit_price;
        $std->vProd = $item->total;
        $std->cEANTrib = $item->tax_ean ?: ($item->ean ?: 'SEM GTIN');
        $std->uTrib = $item->tax_unit ?: $item->unit;
        $std->qTrib = $item->tax_quantity ?? $item->quantity;
        $std->vUnTrib = $item->tax_unit_price ?? $item->unit_price;
        $std->indTot = 1;
        $std->indEscala = $item->relevant_scale === null ? null : ($item->relevant_scale ? 'S' : 'N');
        $std->CNPJFab = $item->manufacturer_cnpj;
        $std->xPed = $item->purchase_order_number;
        $std->nItemPed = $item->purchase_order_item;

        if ($item->discount > 0) {
            $std->vDesc = $item->discount;
        }
        if ($item->freight > 0) {
            $std->vFrete = $item->freight;
        }

        return $std;
    }

    protected function addProductSpecificGroups(Make $make, FiscalNoteItem $item): void
    {
        foreach ($item->traceability ?? [] as $trace) {
            $make->tagRastro($this->itemGroup($item, $trace));
        }

        foreach ([
            'medicine_data' => 'tagmed',
            'fuel_data' => 'tagcomb',
            'vehicle_data' => 'tagveicProd',
            'weapon_data' => 'tagarma',
            'dfe_reference' => 'tagDFeReferenciado',
        ] as $attribute => $method) {
            if ($item->{$attribute}) {
                $make->{$method}($this->itemGroup($item, $item->{$attribute}));
            }
        }

        foreach ($item->import_declarations ?? [] as $declaration) {
            $make->tagDI($this->itemGroup($item, $declaration));

            foreach ($declaration['additions'] ?? [] as $addition) {
                $addition['nDI'] = $declaration['nDI'];
                $make->tagadi($this->itemGroup($item, $addition));
            }
        }
    }

    protected function itemGroup(FiscalNoteItem $item, array $data): stdClass
    {
        $std = new stdClass;
        $std->item = $item->item_number;

        foreach ($data as $field => $value) {
            if ($field !== 'additions') {
                $std->{$field} = $value;
            }
        }

        return $std;
    }

    protected function addTaxReformGroups(Make $make, FiscalNoteItem $item): void
    {
        $data = $item->ibs_cbs_data;
        $make->tagIBSCBS($this->itemGroup($item, $data));

        foreach ([
            'regular_tax' => 'tagIBSCBSTribRegular',
            'monophase' => 'tagIBSCBSMono',
            'presumed_credit' => 'taggCredPresOper',
            'credit_transfer' => 'taggTransfCred',
            'credit_reversal' => 'taggEstornoCred',
            'period_adjustment' => 'taggAjusteCompet',
        ] as $group => $method) {
            if (! empty($data[$group])) {
                $make->{$method}($this->itemGroup($item, $data[$group]));
            }
        }
    }

    /**
     * Constrói stdClass para tag imposto
     */
    protected function buildImposto(FiscalNoteItem $item): stdClass
    {
        $std = new stdClass;
        $std->item = $item->item_number;
        $std->vTotTrib = $item->approximate_tax_amount;

        return $std;
    }

    protected function buildIcms(FiscalNoteItem $item): stdClass
    {
        $std = new stdClass;
        $std->item = $item->item_number;
        $std->orig = $item->origin;
        $std->CST = $item->cst;

        $std->modBC = $item->icms_mod_bc ?? 3;
        $std->vBC = $item->icms_base;
        $std->pICMS = $item->icms_percent;
        $std->vICMS = $item->icms_amount;
        $std->modBCST = $item->icms_data['modBCST'] ?? 4;
        $std->vBCST = $item->icms_st_base;
        $std->pICMSST = $item->icms_st_percent;
        $std->vICMSST = $item->icms_st_amount;

        $this->applyIcmsDetails($std, $item->icms_data ?? []);

        return $std;
    }

    protected function buildIcmsSn(FiscalNoteItem $item): stdClass
    {
        $std = new stdClass;
        $std->item = $item->item_number;
        $std->orig = $item->origin;
        $std->CSOSN = $item->csosn;

        $std->modBC = $item->icms_mod_bc ?? 3;
        $std->vBC = $item->icms_base;
        $std->pICMS = $item->icms_percent;
        $std->vICMS = $item->icms_amount;
        $std->modBCST = $item->icms_data['modBCST'] ?? 4;
        $std->vBCST = $item->icms_st_base;
        $std->pICMSST = $item->icms_st_percent;
        $std->vICMSST = $item->icms_st_amount;

        $this->applyIcmsDetails($std, $item->icms_data ?? []);

        return $std;
    }

    protected function applyIcmsDetails(stdClass $std, array $details): void
    {
        $allowed = [
            'pRedBC', 'pRedBCST', 'pDif', 'vICMSDif', 'vICMSOp',
            'vICMSDeson', 'motDesICMS', 'indDeduzDeson',
            'vBCFCP', 'pFCP', 'vFCP', 'vBCFCPST', 'pFCPST', 'vFCPST',
            'vBCSTRet', 'pST', 'vICMSSubstituto', 'vICMSSTRet',
            'vBCFCPSTRet', 'pFCPSTRet', 'vFCPSTRet',
            'pCredSN', 'vCredICMSSN', 'pMVAST',
            'qBCMono', 'adRemICMS', 'vICMSMono',
            'qBCMonoReten', 'adRemICMSReten', 'vICMSMonoReten',
            'qBCMonoRet', 'adRemICMSRet', 'vICMSMonoRet',
            'pRedAdRem', 'motRedAdRem',
        ];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $details)) {
                $std->{$field} = $details[$field];
            }
        }
    }

    protected function buildIcmsUfDest(FiscalNoteItem $item): stdClass
    {
        $std = new stdClass;
        $std->item = $item->item_number;

        foreach ($item->icms_data['difal'] as $field => $value) {
            if (in_array($field, [
                'vBCUFDest', 'vBCFCPUFDest', 'pFCPUFDest', 'pICMSUFDest',
                'pICMSInter', 'pICMSInterPart', 'vFCPUFDest', 'vICMSUFDest',
                'vICMSUFRemet',
            ], true)) {
                $std->{$field} = $value;
            }
        }

        return $std;
    }

    protected function validateIcmsRegime(FiscalNote $note): void
    {
        $crt = (int) ($this->emitter->crt ?? config('nfe.crt'));
        $isSimples = in_array($crt, [1, 2, 4], true);

        foreach ($note->items as $item) {
            if ($isSimples && (! $item->csosn || $item->cst)) {
                throw new \InvalidArgumentException("Item {$item->item_number}: CRT {$crt} exige CSOSN e não permite CST de ICMS.");
            }

            if (! $isSimples && (! $item->cst || $item->csosn)) {
                throw new \InvalidArgumentException("Item {$item->item_number}: CRT {$crt} exige CST e não permite CSOSN.");
            }
        }
    }

    protected function buildIpi(FiscalNoteItem $item): stdClass
    {
        $std = new stdClass;
        $std->item = $item->item_number;
        $std->cEnq = config('nfe.ipi_cenq_default');
        $std->CST = $item->ipi_cst;

        if (in_array($item->ipi_cst, ['00', '49', '50', '99'], true)) {
            $std->vBC = $item->ipi_base;
            $std->pIPI = $item->ipi_percent;
            $std->vIPI = $item->ipi_amount;
        }

        return $std;
    }

    protected function buildPis(FiscalNoteItem $item): stdClass
    {
        $std = new stdClass;
        $std->item = $item->item_number;
        $std->CST = $item->pis_cst ?? '99';
        $std->vBC = $item->pis_base;
        $std->pPIS = $item->pis_percent;
        $std->vPIS = $item->pis_amount;

        return $std;
    }

    protected function buildCofins(FiscalNoteItem $item): stdClass
    {
        $std = new stdClass;
        $std->item = $item->item_number;
        $std->CST = $item->cofins_cst ?? '99';
        $std->vBC = $item->cofins_base;
        $std->pCOFINS = $item->cofins_percent;
        $std->vCOFINS = $item->cofins_amount;

        return $std;
    }

    /**
     * Constrói stdClass para tag total
     */
    protected function buildIcmsTotal(FiscalNote $note): stdClass
    {
        $std = new stdClass;

        $vBC = $note->items->sum('icms_base');
        $vICMS = $note->items->sum('icms_amount');
        $vProd = $note->items->sum('total');
        $vDesc = $note->items->sum('discount');
        $vFrete = $note->items->sum('freight');
        $vSeg = $note->items->sum('insurance');
        $vOutro = $note->items->sum('other');
        $vIPI = $note->items->sum('ipi_amount');
        $vBCST = $note->items->sum('icms_st_base');
        $vST = $note->items->sum('icms_st_amount');
        $vICMSDeson = $this->sumIcmsDetail($note, 'vICMSDeson');
        $vFCP = $this->sumIcmsDetail($note, 'vFCP');
        $vFCPST = $this->sumIcmsDetail($note, 'vFCPST');
        $vFCPSTRet = $this->sumIcmsDetail($note, 'vFCPSTRet');
        $vII = $note->items->sum('import_tax_amount');
        $vIPIDevol = $note->items->sum('returned_ipi_amount');
        $vTotTrib = $note->items->sum('approximate_tax_amount');

        $std->vBC = $vBC;
        $std->vICMS = $vICMS;
        $std->vICMSDeson = $vICMSDeson;
        $std->vFCP = $vFCP;
        $std->vBCST = $vBCST;
        $std->vST = $vST;
        $std->vFCPST = $vFCPST;
        $std->vFCPSTRet = $vFCPSTRet;
        $std->vProd = $vProd;
        $std->vFrete = $vFrete;
        $std->vSeg = $vSeg;
        $std->vDesc = $vDesc;
        $std->vII = $vII;
        $std->vIPI = $vIPI;
        $std->vIPIDevol = $vIPIDevol;
        $std->vPIS = $note->items->sum('pis_amount');
        $std->vCOFINS = $note->items->sum('cofins_amount');
        $std->vOutro = $vOutro;
        $std->vNF = $vProd + $vFrete + $vSeg + $vOutro + $vST + $vIPI + $vIPIDevol - $vDesc - $this->deductibleDesoneration($note);
        $std->vTotTrib = $vTotTrib;

        return $std;
    }

    protected function sumIcmsDetail(FiscalNote $note, string $field): float
    {
        return $note->items->sum(fn (FiscalNoteItem $item): float => (float) ($item->icms_data[$field] ?? 0));
    }

    protected function deductibleDesoneration(FiscalNote $note): float
    {
        return $note->items->sum(fn (FiscalNoteItem $item): float =>
            ($item->icms_data['indDeduzDeson'] ?? 0) == 1
                ? (float) ($item->icms_data['vICMSDeson'] ?? 0)
                : 0
        );
    }

    protected function validateNoteTotal(FiscalNote $note): void
    {
        $calculated = $note->items->sum(fn (FiscalNoteItem $item): float =>
            (float) $item->total
            + (float) $item->freight
            + (float) $item->insurance
            + (float) $item->other
            + (float) $item->icms_st_amount
            + (float) $item->ipi_amount
            + (float) $item->returned_ipi_amount
            - (float) $item->discount
            - (($item->icms_data['indDeduzDeson'] ?? 0) == 1 ? (float) ($item->icms_data['vICMSDeson'] ?? 0) : 0)
        );

        if (abs((float) $note->amount - $calculated) > 0.01) {
            throw new \InvalidArgumentException(sprintf(
                'Total da NF-e (%.2f) diverge da soma dos itens (%.2f).',
                $note->amount,
                $calculated
            ));
        }
    }

    protected function validatePaymentTotal(FiscalNote $note): void
    {
        $payments = $this->paymentDetails($note);

        if (count($payments) === 1 && ($payments[0]['tPag'] ?? null) === '90') {
            return;
        }

        $paid = collect($payments)->sum(fn (array $payment): float => (float) ($payment['vPag'] ?? 0));
        $netPaid = $paid - (float) $note->change_amount;

        if (abs($netPaid - (float) $note->amount) > 0.01) {
            throw new \InvalidArgumentException(sprintf(
                'Total pago (%.2f) diverge do total da NF-e (%.2f).',
                $netPaid,
                $note->amount
            ));
        }
    }

    /**
     * Constrói stdClass para tag transp
     */
    protected function buildTransp(FiscalNote $note): stdClass
    {
        $std = new stdClass;
        $std->modFrete = $note->freight_mode->value;

        return $std;
    }

    protected function addTransportGroups(Make $make, FiscalNote $note): void
    {
        $data = $note->transport_data ?? [];

        foreach ([
            'carrier' => 'tagtransporta',
            'vehicle' => 'tagveicTransp',
            'retained_icms' => 'tagretTransp',
        ] as $group => $method) {
            if (! empty($data[$group])) {
                $make->{$method}($this->arrayToObject($data[$group]));
            }
        }

        foreach ($data['volumes'] ?? [] as $index => $volume) {
            $volume['item'] = $index + 1;
            $make->tagvol($this->arrayToObject($volume));

            foreach ($volume['seals'] ?? [] as $seal) {
                $make->taglacres($this->arrayToObject([
                    'item' => $index + 1,
                    'nLacre' => $seal,
                ]));
            }
        }
    }

    protected function arrayToObject(array $data): stdClass
    {
        $std = new stdClass;

        foreach ($data as $field => $value) {
            if (! is_array($value)) {
                $std->{$field} = $value;
            }
        }

        return $std;
    }

    /**
     * Constrói stdClass para tag pag
     */
    protected function buildPag(FiscalNote $note): stdClass
    {
        $std = new stdClass;
        $std->vTroco = $note->change_amount;

        return $std;
    }

    protected function paymentDetails(FiscalNote $note): array
    {
        return $note->payment_details ?: [[
            'indPag' => 0,
            'tPag' => '01',
            'vPag' => (float) $note->amount,
        ]];
    }

    protected function addBillingGroups(Make $make, FiscalNote $note): void
    {
        if ($note->invoice_data) {
            $make->tagfat($this->arrayToObject($note->invoice_data));
        }

        foreach ($note->installment_data ?? [] as $installment) {
            $make->tagdup($this->arrayToObject($installment));
        }
    }

    /**
     * Constrói stdClass para tag infAdic
     */
    protected function buildInfAdic(FiscalNote $note): stdClass
    {
        $std = new stdClass;
        $std->infCpl = $note->notes;

        return $std;
    }

    /**
     * Parseia resposta de transmissão
     */
    protected function parseTransmissionResponse(string $response, ?string $expectedAccessKey = null): array
    {
        $dom = $this->loadResponseDocument($response);
        $xpath = new \DOMXPath($dom);
        $responseNode = $xpath->query(
            '//*[local-name()="retEnviNFe" or local-name()="retConsReciNFe"]'
        )->item(0);

        if (! $responseNode) {
            throw new \RuntimeException('Resposta da SEFAZ não contém retEnviNFe ou retConsReciNFe.');
        }

        $batchCode = $this->xpathValue($xpath, './*[local-name()="cStat"]', $responseNode);
        $batchMessage = $this->xpathValue($xpath, './*[local-name()="xMotivo"]', $responseNode);
        $receipt = $this->xpathValue($xpath, './*[local-name()="nRec"]', $responseNode);
        $protocolNode = $this->findProtocolNode($xpath, $responseNode, $expectedAccessKey);

        $protocolCode = $this->xpathValue($xpath, './*[local-name()="cStat"]', $protocolNode);
        $protocolMessage = $this->xpathValue($xpath, './*[local-name()="xMotivo"]', $protocolNode);
        $accessKey = $this->xpathValue($xpath, './*[local-name()="chNFe"]', $protocolNode);
        $protocol = $this->xpathValue($xpath, './*[local-name()="nProt"]', $protocolNode);
        $receivedAt = $this->xpathValue($xpath, './*[local-name()="dhRecbto"]', $protocolNode);

        if ($protocolNode && ! $protocolCode) {
            throw new \RuntimeException('Protocolo da SEFAZ não contém cStat.');
        }

        $state = $protocolCode
            ? $this->protocolState($protocolCode)
            : $this->batchState($batchCode);

        if ($state === NFeTransmissionState::Authorized && (! $accessKey || ! $protocol)) {
            throw new \RuntimeException('Resposta de autorização não contém chave ou protocolo da NF-e.');
        }

        return [
            'state' => $state,
            'is_final' => $protocolCode !== null,
            'authorized' => $state === NFeTransmissionState::Authorized,
            'code' => $protocolCode ?? $batchCode,
            'message' => $protocolMessage ?? $batchMessage,
            'batch_code' => $batchCode,
            'batch_message' => $batchMessage,
            'receipt' => $receipt,
            'protocol_code' => $protocolCode,
            'protocol_message' => $protocolMessage,
            'access_key' => $accessKey,
            'protocol' => $protocol,
            'received_at' => $receivedAt,
            'raw_response' => $response,
        ];
    }

    protected function loadResponseDocument(string $response): \DOMDocument
    {
        if (trim($response) === '') {
            throw new \RuntimeException('Resposta vazia recebida da SEFAZ.');
        }

        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $loaded = $dom->loadXML($response, LIBXML_NONET);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            $message = $errors[0]->message ?? 'XML inválido.';
            throw new \RuntimeException('Resposta XML inválida da SEFAZ: '.trim($message));
        }

        return $dom;
    }

    protected function findProtocolNode(
        \DOMXPath $xpath,
        \DOMNode $responseNode,
        ?string $expectedAccessKey
    ): ?\DOMNode {
        $protocols = $xpath->query(
            './/*[local-name()="protNFe"]/*[local-name()="infProt"]',
            $responseNode
        );

        if ($protocols->length === 0) {
            return null;
        }

        if (! $expectedAccessKey) {
            return $protocols->item(0);
        }

        foreach ($protocols as $protocol) {
            $accessKey = $this->xpathValue($xpath, './*[local-name()="chNFe"]', $protocol);

            if ($accessKey === $expectedAccessKey) {
                return $protocol;
            }
        }

        throw new \RuntimeException('A chave retornada pela SEFAZ não corresponde à NF-e transmitida.');
    }

    protected function xpathValue(\DOMXPath $xpath, string $expression, ?\DOMNode $context): ?string
    {
        if (! $context) {
            return null;
        }

        $value = $xpath->query($expression, $context)->item(0)?->nodeValue;

        return $value !== null && trim($value) !== '' ? trim($value) : null;
    }

    protected function protocolState(string $code): NFeTransmissionState
    {
        if (in_array($code, ['100', '150'], true)) {
            return NFeTransmissionState::Authorized;
        }

        if (in_array($code, ['110', '205', '301', '302', '303'], true)) {
            return NFeTransmissionState::Denied;
        }

        return NFeTransmissionState::Rejected;
    }

    protected function batchState(?string $code): NFeTransmissionState
    {
        return match ($code) {
            '103', '105' => NFeTransmissionState::Processing,
            '104' => NFeTransmissionState::Indeterminate,
            default => NFeTransmissionState::Failed,
        };
    }

    protected function extractAccessKey(string $xml): string
    {
        $dom = $this->loadResponseDocument($xml);
        $xpath = new \DOMXPath($dom);
        $id = $xpath->query('//*[local-name()="infNFe"]/@Id')->item(0)?->nodeValue;

        if (! $id || ! preg_match('/^NFe\d{44}$/', $id)) {
            throw new \RuntimeException('Não foi possível identificar a chave da NF-e transmitida.');
        }

        return substr($id, 3);
    }

    protected function applyTransmissionResult(
        FiscalNote $note,
        array $result,
        ?string $expectedAccessKey = null
    ): void {
        $status = match ($result['state']) {
            NFeTransmissionState::Authorized => FiscalNoteStatus::Authorized,
            NFeTransmissionState::Rejected => FiscalNoteStatus::Rejected,
            NFeTransmissionState::Denied => FiscalNoteStatus::Denied,
            NFeTransmissionState::Processing,
            NFeTransmissionState::Indeterminate => FiscalNoteStatus::Sent,
            NFeTransmissionState::Failed => $note->status,
        };

        DB::transaction(function () use ($note, $result, $expectedAccessKey, $status): void {
            $note->update([
                'status' => $status,
                'access_key' => $result['access_key'] ?? $expectedAccessKey ?? $note->access_key,
                'protocol' => $result['protocol'] ?? $note->protocol,
                'receipt_number' => $result['receipt'] ?? $note->receipt_number,
                'sefaz_message' => $result['message'],
                'authorized_at' => $result['authorized'] ? ($result['received_at'] ?? now()) : null,
            ]);

            $salesOrder = $note->salesOrder;

            if (! $salesOrder) {
                return;
            }

            if ($result['state'] === NFeTransmissionState::Authorized) {
                $salesOrder->update([
                    'status' => SalesOrderStatus::NfTransmitida,
                    'fiscal_note_id' => $note->id,
                    'nfe_number' => $note->invoice_number,
                    'nfe_series' => $note->series,
                    'nfe_key' => $result['access_key'],
                    'nfe_protocol' => $result['protocol'],
                    'nfe_status' => NFeTransmissionState::Authorized->value,
                    'nfe_issued_at' => $salesOrder->nfe_issued_at ?? $result['received_at'] ?? now(),
                ]);

                return;
            }

            if ($salesOrder->nfe_status !== NFeTransmissionState::Authorized->value) {
                $salesOrder->update(['nfe_status' => $result['state']->value]);
            }
        });
    }

    /**
     * Salva XML no storage
     */
    protected function saveXml(FiscalNote $note, string $xml, string $type = 'enviado'): string
    {
        $disk = config('nfe.xml_disk');
        $path = config('nfe.xml_path').'/'.$note->invoice_number.'_'.$type.'.xml';

        Storage::disk($disk)->put($path, $xml);

        if ($type === 'enviado') {
            $note->update([
                'xml_sent_path' => $path,
                'xml_path' => $path,
            ]);
        } elseif ($type === 'autorizado') {
            $note->update(['xml_path' => $path]);
        }

        return $path;
    }

    protected function saveAuthorizedXml(
        FiscalNote $note,
        string $signedXml,
        string $response,
        string $expectedAccessKey
    ): string {
        try {
            $processedXml = Complements::toAuthorize($signedXml, $response);
        } catch (\Throwable $exception) {
            throw new \RuntimeException(
                'Não foi possível compor o XML autorizado da NF-e: '.$exception->getMessage(),
                previous: $exception
            );
        }

        $result = $this->validateAuthorizedXml($processedXml, $expectedAccessKey);

        if (! in_array($result['code'], ['100', '150'], true)) {
            throw new \RuntimeException('O XML processado não contém um protocolo de autorização válido.');
        }

        return $this->saveXml($note, $processedXml, 'autorizado');
    }

    protected function validateAuthorizedXml(string $xml, string $expectedAccessKey): array
    {
        $dom = $this->loadResponseDocument($xml);
        $xpath = new \DOMXPath($dom);
        $root = $xpath->query('/*[local-name()="nfeProc"]')->item(0);
        $noteKey = $xpath->query(
            './*[local-name()="NFe"]/*[local-name()="infNFe"]/@Id',
            $root
        )->item(0)?->nodeValue;
        $protocolNode = $xpath->query(
            './*[local-name()="protNFe"]/*[local-name()="infProt"]',
            $root
        )->item(0);
        $protocolKey = $this->xpathValue($xpath, './*[local-name()="chNFe"]', $protocolNode);
        $code = $this->xpathValue($xpath, './*[local-name()="cStat"]', $protocolNode);
        $protocol = $this->xpathValue($xpath, './*[local-name()="nProt"]', $protocolNode);

        if (! $root || $noteKey !== 'NFe'.$expectedAccessKey || $protocolKey !== $expectedAccessKey) {
            throw new \RuntimeException('As chaves do XML processado não correspondem à NF-e transmitida.');
        }

        if (! $code || ! $protocol) {
            throw new \RuntimeException('O XML processado não contém protocolo de autorização completo.');
        }

        return ['code' => $code, 'protocol' => $protocol];
    }

    public function cartaCorrecao(
        FiscalNote $note,
        string $correction,
        ?int $sequence = null
    ): array {
        if ($note->status !== FiscalNoteStatus::Authorized) {
            throw new \InvalidArgumentException('Apenas notas autorizadas podem receber Carta de Correção.');
        }

        if (! $note->access_key || ! preg_match('/^\d{44}$/', $note->access_key)) {
            throw new \InvalidArgumentException('A NF-e não possui uma chave de acesso válida.');
        }

        if (! $note->protocol) {
            throw new \InvalidArgumentException('A NF-e não possui protocolo de autorização.');
        }

        $correction = trim($correction);
        $length = mb_strlen($correction);

        if ($length < 15 || $length > 1000) {
            throw new \InvalidArgumentException('A correção deve possuir entre 15 e 1.000 caracteres.');
        }

        $sequence ??= $this->nextCceSequence($note);

        if ($sequence < 1 || $sequence > 20) {
            throw new \InvalidArgumentException('A sequência da Carta de Correção deve estar entre 1 e 20.');
        }

        $response = $this->sendCce($note->access_key, $correction, $sequence);
        $result = $this->parseCceResponse($response, $note->access_key, $sequence);
        $responsePath = $this->saveCceResponse($note, $response, $sequence);

        if ($result['registered']) {
            FiscalNoteEvent::create([
                'fiscal_note_id' => $note->id,
                'event_type' => FiscalNoteEventType::Cce,
                'description' => 'CC-e enviada: '.$correction,
                'protocol' => $result['protocol'],
                'response_data' => [
                    'sequence' => $sequence,
                    'code' => $result['code'],
                    'message' => $result['message'],
                    'registered_at' => $result['registered_at'],
                    'response_path' => $responsePath,
                ],
                'created_by' => Auth::id(),
            ]);
        }

        return $result + [
            'success' => $result['registered'],
            'sequence' => $sequence,
            'response_path' => $responsePath,
        ];
    }

    protected function sendCce(string $accessKey, string $correction, int $sequence): string
    {
        return $this->tools->sefazCCe($accessKey, $correction, $sequence);
    }

    protected function nextCceSequence(FiscalNote $note): int
    {
        $lastSequence = $note->events()
            ->where('event_type', FiscalNoteEventType::Cce->value)
            ->get()
            ->max(fn (FiscalNoteEvent $event) => (int) data_get($event->response_data, 'sequence', 0));

        return ((int) $lastSequence) + 1;
    }

    protected function parseCceResponse(string $response, string $expectedAccessKey, int $expectedSequence): array
    {
        $dom = $this->loadResponseDocument($response);
        $xpath = new \DOMXPath($dom);
        $responseNode = $xpath->query('//*[local-name()="retEnvEvento"]')->item(0);

        if (! $responseNode) {
            throw new \RuntimeException('Resposta da SEFAZ não contém retEnvEvento.');
        }

        $eventNodes = $xpath->query(
            './/*[local-name()="retEvento"]/*[local-name()="infEvento"]',
            $responseNode
        );

        foreach ($eventNodes as $eventNode) {
            $type = $this->xpathValue($xpath, './*[local-name()="tpEvento"]', $eventNode);
            $accessKey = $this->xpathValue($xpath, './*[local-name()="chNFe"]', $eventNode);
            $sequence = $this->xpathValue($xpath, './*[local-name()="nSeqEvento"]', $eventNode);

            if ($type !== '110110' || $accessKey !== $expectedAccessKey || (int) $sequence !== $expectedSequence) {
                continue;
            }

            $code = $this->xpathValue($xpath, './*[local-name()="cStat"]', $eventNode);
            $message = $this->xpathValue($xpath, './*[local-name()="xMotivo"]', $eventNode);
            $protocol = $this->xpathValue($xpath, './*[local-name()="nProt"]', $eventNode);
            $registered = in_array($code, ['135', '136'], true);

            if ($registered && ! $protocol) {
                throw new \RuntimeException('Resposta de registro da CC-e não contém protocolo.');
            }

            return [
                'registered' => $registered,
                'code' => $code,
                'message' => $message,
                'protocol' => $protocol,
                'registered_at' => $this->xpathValue($xpath, './*[local-name()="dhRegEvento"]', $eventNode),
                'raw_response' => $response,
            ];
        }

        if ($eventNodes->length > 0) {
            throw new \RuntimeException('A resposta da CC-e não corresponde à chave, tipo ou sequência enviados.');
        }

        return [
            'registered' => false,
            'code' => $this->xpathValue($xpath, './*[local-name()="cStat"]', $responseNode),
            'message' => $this->xpathValue($xpath, './*[local-name()="xMotivo"]', $responseNode),
            'protocol' => null,
            'registered_at' => null,
            'raw_response' => $response,
        ];
    }

    protected function saveCceResponse(FiscalNote $note, string $response, int $sequence): string
    {
        $disk = config('nfe.xml_disk');
        $path = config('nfe.xml_path').'/'.$note->invoice_number.'_cce_'.$sequence.'_resposta.xml';

        Storage::disk($disk)->put($path, $response);

        return $path;
    }

    /**
     * Cancela NF-e autorizada
     */
    public function cancelar(FiscalNote $note, string $justificativa): array
    {
        if ($note->status !== FiscalNoteStatus::Authorized) {
            throw new \InvalidArgumentException('Apenas notas autorizadas podem ser canceladas.');
        }

        if (strlen($justificativa) < 15) {
            throw new \InvalidArgumentException('Justificativa deve ter no mínimo 15 caracteres.');
        }

        $response = $this->sendCancellation(
            $note->access_key,
            $justificativa,
            $note->protocol
        );

        $result = $this->parseCancelResponse($response, $note->access_key);

        if ($result['cancelled']) {
            $note->update([
                'status' => FiscalNoteStatus::Cancelled,
                'cancel_protocol' => $result['protocol'],
                'cancel_reason' => $justificativa,
                'cancelled_at' => now(),
            ]);

            // Salva XML de cancelamento
            $this->saveXml($note, $response, 'cancelamento');

            FiscalNoteEvent::create([
                'fiscal_note_id' => $note->id,
                'event_type' => FiscalNoteEventType::Cancelled,
                'description' => 'NF-e cancelada: '.$justificativa,
                'protocol' => $result['protocol'],
                'response_data' => [
                    'code' => $result['code'],
                    'message' => $result['message'],
                    'registered_at' => $result['registered_at'],
                ],
                'created_by' => Auth::id(),
            ]);
        }

        return $result;
    }

    /**
     * Parseia resposta de cancelamento
     */
    protected function sendCancellation(string $accessKey, string $reason, string $protocol): string
    {
        return $this->tools->sefazCancela($accessKey, $reason, $protocol);
    }

    protected function parseCancelResponse(string $response, ?string $expectedAccessKey = null): array
    {
        $dom = $this->loadResponseDocument($response);
        $xpath = new \DOMXPath($dom);
        $responseNode = $xpath->query('//*[local-name()="retEnvEvento"]')->item(0);

        if ($responseNode) {
            $eventNodes = $xpath->query(
                './/*[local-name()="retEvento"]/*[local-name()="infEvento"]',
                $responseNode
            );

            foreach ($eventNodes as $eventNode) {
                $type = $this->xpathValue($xpath, './*[local-name()="tpEvento"]', $eventNode);
                $accessKey = $this->xpathValue($xpath, './*[local-name()="chNFe"]', $eventNode);

                if ($type !== '110111' || ($expectedAccessKey && $accessKey !== $expectedAccessKey)) {
                    continue;
                }

                $code = $this->xpathValue($xpath, './*[local-name()="cStat"]', $eventNode);

                return [
                    'cancelled' => in_array($code, ['135', '136', '155'], true),
                    'code' => $code,
                    'message' => $this->xpathValue($xpath, './*[local-name()="xMotivo"]', $eventNode),
                    'protocol' => $this->xpathValue($xpath, './*[local-name()="nProt"]', $eventNode),
                    'registered_at' => $this->xpathValue($xpath, './*[local-name()="dhRegEvento"]', $eventNode),
                    'raw_response' => $response,
                ];
            }

            if ($eventNodes->length > 0) {
                throw new \RuntimeException('A resposta do cancelamento não corresponde à chave ou ao tipo de evento enviados.');
            }
        }

        $code = $xpath->query('//*[local-name()="cStat"]')->item(0)?->nodeValue;

        return [
            'cancelled' => in_array($code, ['101', '135', '136', '155'], true),
            'code' => $code,
            'message' => $xpath->query('//*[local-name()="xMotivo"]')->item(0)?->nodeValue,
            'protocol' => $xpath->query('//*[local-name()="nProt"]')->item(0)?->nodeValue,
            'registered_at' => null,
            'raw_response' => $response,
        ];
    }

    /**
     * Consulta status do serviço SEFAZ
     */
    public function consultarStatus(): array
    {
        $response = $this->tools->sefazStatus(config('nfe.uf'));

        $dom = new \DOMDocument;
        $dom->loadXML($response);

        $cStat = $dom->getElementsByTagName('cStat')->item(0)?->nodeValue;
        $xMotivo = $dom->getElementsByTagName('xMotivo')->item(0)?->nodeValue;

        return [
            'online' => $cStat === '107',
            'code' => $cStat,
            'message' => $xMotivo,
        ];
    }

    /**
     * Consulta NF-e pela chave de acesso
     */
    public function consultarChave(string $chave): array
    {
        $response = $this->tools->sefazConsultaChave($chave);

        $dom = new \DOMDocument;
        $dom->loadXML($response);

        $cStat = $dom->getElementsByTagName('cStat')->item(0)?->nodeValue;
        $xMotivo = $dom->getElementsByTagName('xMotivo')->item(0)?->nodeValue;

        return [
            'found' => $cStat === '100',
            'code' => $cStat,
            'message' => $xMotivo,
            'xml' => $response,
        ];
    }
}
