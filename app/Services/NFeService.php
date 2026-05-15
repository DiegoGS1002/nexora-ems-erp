<?php

namespace App\Services;

use App\Enums\FiscalNoteStatus;
use App\Models\Client;
use App\Models\Company;
use App\Models\FiscalNote;
use App\Models\FiscalNoteItem;
use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use stdClass;

class NFeService
{
    protected Tools $tools;
    protected ?Company $emitter = null;

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
            'tpAmb'       => config('nfe.environment'),
            'razaosocial' => $razao,
            'cnpj'        => $cnpj,
            'siglaUF'     => config('nfe.uf'),
            'schemes'     => config('nfe.schemes_path'),
            'versao'      => '4.00',
            'tokenIBPT'   => config('nfe.ibpt_token'),
            'CSC'         => config('nfe.csc'),
            'CSCid'       => config('nfe.csc_id'),
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

        if (!Storage::disk($disk)->exists($path)) {
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
        $idLote = date('YmdHis') . rand(1000, 9999);
        $indSinc = $sincrono ? 1 : 0;

        $response = $this->tools->sefazEnviaLote(
            [$xmlSigned],
            $idLote,
            $indSinc,
            false
        );

        $result = $this->parseTransmissionResponse($response);

        // Salva XML enviado
        $this->saveXml($note, $xmlSigned, 'enviado');

        // Atualiza nota
        $note->update([
            'status'        => $result['authorized'] ? FiscalNoteStatus::Authorized : FiscalNoteStatus::Sent,
            'access_key'    => $result['access_key'] ?? $note->access_key,
            'protocol'      => $result['protocol'] ?? null,
            'sefaz_message' => $result['message'] ?? null,
            'authorized_at' => $result['authorized'] ? now() : null,
        ]);

        return $result;
    }

    /**
     * Constrói XML da NF-e
     */
    public function buildXml(FiscalNote $note): string
    {
        $make = new Make();

        // Tag IDE (identificação)
        $make->tagide($this->buildIde($note));

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

        // Itens
        foreach ($note->items as $item) {
            $make->tagprod($this->buildProd($item));
            $make->tagimposto($this->buildImposto($item));
        }

        // Totais
        $make->tagtotal($this->buildTotal($note));

        // Transporte
        $make->tagtransp($this->buildTransp());

        // Pagamento
        $make->tagpag($this->buildPag($note));

        // Informações adicionais
        if ($note->notes) {
            $make->taginfAdic($this->buildInfAdic($note));
        }

        $make->montaNFe();

        return $make->getXML();
    }

    /**
     * Constrói stdClass para tag IDE
     */
    protected function buildIde(FiscalNote $note): stdClass
    {
        $std = new stdClass();
        $std->cUF = \NFePHP\Common\UFList::getCodeByUF(config('nfe.uf'));
        $std->cNF = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $std->natOp = 'Venda de Mercadoria';
        $std->mod = $note->type === 'nfce' ? 65 : 55;
        $std->serie = (int) $note->series;
        $std->nNF = (int) $note->invoice_number;
        $std->dhEmi = now()->format('Y-m-d\TH:i:sP');
        $std->tpNF = 1; // 0=Entrada, 1=Saída
        $std->idDest = 1; // 1=Operação interna, 2=Interestadual, 3=Exterior
        $std->cMunFG = config('nfe.ibge_emitente');
        $std->tpImp = 1; // 1=Retrato, 2=Paisagem
        $std->tpEmis = 1; // 1=Normal
        $std->cDV = 0;
        $std->tpAmb = config('nfe.environment');
        $std->finNFe = 1; // 1=Normal, 2=Complementar, 3=Ajuste, 4=Devolução
        $std->indFinal = 1; // 0=Normal, 1=Consumidor final
        $std->indPres = 1; // 1=Presencial
        $std->procEmi = 0; // 0=Emissão própria
        $std->verProc = config('nfe.versao');

        return $std;
    }

    /**
     * Constrói stdClass para tag EMIT
     */
    protected function buildEmit(): stdClass
    {
        $std = new stdClass();
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
        $std = new stdClass();
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
        $std = new stdClass();

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
        $std = new stdClass();
        $std->xLgr = $client->address_street;
        $std->nro = $client->address_number;
        $std->xCpl = $client->address_complement;
        $std->xBairro = $client->address_district;
        $std->cMun = $client->ibge_municipio;
        $std->xMun = $client->address_city;
        $std->UF = $client->address_state;
        $std->CEP = preg_replace('/\D/', '', $client->address_zip_code);
        $std->cPais = '1058';
        $std->xPais = 'BRASIL';

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
        $std = new stdClass();
        $std->item = $item->item_number;
        $std->cProd = $item->product_code;
        $std->cEAN = $item->ean ?: 'SEM GTIN';
        $std->xProd = $item->description;
        $std->NCM = $item->ncm;
        $std->CFOP = $item->cfop;
        $std->uCom = $item->unit;
        $std->qCom = $item->quantity;
        $std->vUnCom = $item->unit_price;
        $std->vProd = $item->total;
        $std->cEANTrib = $item->ean ?: 'SEM GTIN';
        $std->uTrib = $item->unit;
        $std->qTrib = $item->quantity;
        $std->vUnTrib = $item->unit_price;
        $std->indTot = 1;

        if ($item->discount > 0) {
            $std->vDesc = $item->discount;
        }
        if ($item->freight > 0) {
            $std->vFrete = $item->freight;
        }

        return $std;
    }

    /**
     * Constrói stdClass para tag imposto
     */
    protected function buildImposto(FiscalNoteItem $item): stdClass
    {
        $std = new stdClass();
        $std->item = $item->item_number;

        // ICMS
        $icms = new stdClass();
        $icms->item = $item->item_number;
        $icms->orig = $item->origin;

        if ($item->csosn) {
            $icms->CSOSN = $item->csosn;
        } else {
            $icms->CST = $item->cst;
        }

        if ($item->icms_amount > 0) {
            $icms->modBC = $item->icms_mod_bc ?? 3;
            $icms->vBC = $item->icms_base;
            $icms->pICMS = $item->icms_percent;
            $icms->vICMS = $item->icms_amount;
        }

        $std->icms = $icms;

        // IPI
        if ($item->ipi_amount > 0) {
            $ipi = new stdClass();
            $ipi->item = $item->item_number;
            $ipi->CST = $item->ipi_cst;
            $ipi->vBC = $item->ipi_base;
            $ipi->pIPI = $item->ipi_percent;
            $ipi->vIPI = $item->ipi_amount;
            $std->ipi = $ipi;
        }

        // PIS
        $pis = new stdClass();
        $pis->item = $item->item_number;
        $pis->CST = $item->pis_cst ?? '99';
        $pis->vBC = $item->pis_base;
        $pis->pPIS = $item->pis_percent;
        $pis->vPIS = $item->pis_amount;
        $std->pis = $pis;

        // COFINS
        $cofins = new stdClass();
        $cofins->item = $item->item_number;
        $cofins->CST = $item->cofins_cst ?? '99';
        $cofins->vBC = $item->cofins_base;
        $cofins->pCOFINS = $item->cofins_percent;
        $cofins->vCOFINS = $item->cofins_amount;
        $std->cofins = $cofins;

        return $std;
    }

    /**
     * Constrói stdClass para tag total
     */
    protected function buildTotal(FiscalNote $note): stdClass
    {
        $std = new stdClass();

        $vBC = $note->items->sum('icms_base');
        $vICMS = $note->items->sum('icms_amount');
        $vProd = $note->items->sum('total');
        $vDesc = $note->items->sum('discount');
        $vFrete = $note->items->sum('freight');
        $vIPI = $note->items->sum('ipi_amount');

        $std->vBC = $vBC;
        $std->vICMS = $vICMS;
        $std->vICMSDeson = 0;
        $std->vFCP = 0;
        $std->vBCST = 0;
        $std->vST = 0;
        $std->vFCPST = 0;
        $std->vFCPSTRet = 0;
        $std->vProd = $vProd;
        $std->vFrete = $vFrete;
        $std->vSeg = 0;
        $std->vDesc = $vDesc;
        $std->vII = 0;
        $std->vIPI = $vIPI;
        $std->vIPIDevol = 0;
        $std->vPIS = $note->items->sum('pis_amount');
        $std->vCOFINS = $note->items->sum('cofins_amount');
        $std->vOutro = 0;
        $std->vNF = $vProd + $vFrete + $vIPI - $vDesc;
        $std->vTotTrib = 0;

        return $std;
    }

    /**
     * Constrói stdClass para tag transp
     */
    protected function buildTransp(): stdClass
    {
        $std = new stdClass();
        $std->modFrete = 9; // 9=Sem frete
        return $std;
    }

    /**
     * Constrói stdClass para tag pag
     */
    protected function buildPag(FiscalNote $note): stdClass
    {
        $std = new stdClass();
        $std->tPag = '01'; // 01=Dinheiro
        $std->vPag = $note->amount;
        return $std;
    }

    /**
     * Constrói stdClass para tag infAdic
     */
    protected function buildInfAdic(FiscalNote $note): stdClass
    {
        $std = new stdClass();
        $std->infCpl = $note->notes;
        return $std;
    }

    /**
     * Parseia resposta de transmissão
     */
    protected function parseTransmissionResponse(string $response): array
    {
        $dom = new \DOMDocument();
        $dom->loadXML($response);

        $cStat = $dom->getElementsByTagName('cStat')->item(0)?->nodeValue;
        $xMotivo = $dom->getElementsByTagName('xMotivo')->item(0)?->nodeValue;
        $chNFe = $dom->getElementsByTagName('chNFe')->item(0)?->nodeValue;
        $nProt = $dom->getElementsByTagName('nProt')->item(0)?->nodeValue;

        return [
            'authorized'  => in_array($cStat, ['100', '150']),
            'code'        => $cStat,
            'message'     => $xMotivo,
            'access_key'  => $chNFe,
            'protocol'    => $nProt,
            'raw_response' => $response,
        ];
    }

    /**
     * Salva XML no storage
     */
    protected function saveXml(FiscalNote $note, string $xml, string $type = 'enviado'): string
    {
        $disk = config('nfe.xml_disk');
        $path = config('nfe.xml_path') . '/' . $note->invoice_number . '_' . $type . '.xml';

        Storage::disk($disk)->put($path, $xml);

        if ($type === 'enviado') {
            $note->update(['xml_path' => $path]);
        }

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

        $response = $this->tools->sefazCancela(
            $note->access_key,
            $justificativa,
            $note->protocol
        );

        $result = $this->parseCancelResponse($response);

        if ($result['canceled']) {
            $note->update([
                'status'          => FiscalNoteStatus::Cancelled,
                'cancel_protocol' => $result['protocol'],
                'cancel_reason'   => $justificativa,
                'cancelled_at'    => now(),
            ]);

            // Salva XML de cancelamento
            $this->saveXml($note, $response, 'cancelamento');
        }

        return $result;
    }

    /**
     * Parseia resposta de cancelamento
     */
    protected function parseCancelResponse(string $response): array
    {
        $dom = new \DOMDocument();
        $dom->loadXML($response);

        $cStat = $dom->getElementsByTagName('cStat')->item(0)?->nodeValue;
        $xMotivo = $dom->getElementsByTagName('xMotivo')->item(0)?->nodeValue;
        $nProt = $dom->getElementsByTagName('nProt')->item(0)?->nodeValue;

        return [
            'canceled' => in_array($cStat, ['101', '135', '155']),
            'code'     => $cStat,
            'message'  => $xMotivo,
            'protocol' => $nProt,
        ];
    }

    /**
     * Consulta status do serviço SEFAZ
     */
    public function consultarStatus(): array
    {
        $response = $this->tools->sefazStatus(config('nfe.uf'));

        $dom = new \DOMDocument();
        $dom->loadXML($response);

        $cStat = $dom->getElementsByTagName('cStat')->item(0)?->nodeValue;
        $xMotivo = $dom->getElementsByTagName('xMotivo')->item(0)?->nodeValue;

        return [
            'online'  => $cStat === '107',
            'code'    => $cStat,
            'message' => $xMotivo,
        ];
    }

    /**
     * Consulta NF-e pela chave de acesso
     */
    public function consultarChave(string $chave): array
    {
        $response = $this->tools->sefazConsultaChave($chave);

        $dom = new \DOMDocument();
        $dom->loadXML($response);

        $cStat = $dom->getElementsByTagName('cStat')->item(0)?->nodeValue;
        $xMotivo = $dom->getElementsByTagName('xMotivo')->item(0)?->nodeValue;

        return [
            'found'   => $cStat === '100',
            'code'    => $cStat,
            'message' => $xMotivo,
            'xml'     => $response,
        ];
    }
}

