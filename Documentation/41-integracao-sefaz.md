# Integração com SEFAZ (Detalhamento Técnico)

## 1. Visão Geral

Detalhamento técnico da integração com SEFAZ para emissão de NF-e, incluindo bibliotecas, configurações, fluxo completo e tratamento de erros.

---

## 2. Biblioteca Utilizada

### nfephp-org/sped-nfe
```bash
composer require nfephp-org/sped-nfe:^5.2
composer require nfephp-org/sped-da:^1.1
```

**GitHub**: https://github.com/nfephp-org/sped-nfe

---

## 3. Configuração

### 3.1 Arquivo config/nfe.php
```php
return [
    'ambiente' => env('NFE_AMBIENTE', 'homologacao'),
    
    'certificado' => [
        'path' => storage_path('app/nfe/certificado.pfx'),
        'senha' => env('NFE_CERTIFICADO_SENHA'),
    ],
    
    'emitente' => [
        'razao_social' => env('NFE_RAZAO_SOCIAL'),
        'nome_fantasia' => env('NFE_NOME_FANTASIA'),
        'cnpj' => env('NFE_CNPJ'),
        'ie' => env('NFE_IE'),
        'crt' => env('NFE_CRT', 1),
        'uf' => env('NFE_UF', 'SP'),
        'endereco' => [
            'logradouro' => env('NFE_ENDERECO_LOGRADOURO'),
            'numero' => env('NFE_ENDERECO_NUMERO'),
            'bairro' => env('NFE_ENDERECO_BAIRRO'),
            'municipio' => env('NFE_ENDERECO_MUNICIPIO'),
            'uf' => env('NFE_UF'),
            'cep' => env('NFE_ENDERECO_CEP'),
            'codigo_municipio' => env('NFE_CODIGO_MUNICIPIO'),
        ],
    ],
    
    'csc' => [
        'codigo' => env('NFE_CSC_CODIGO'),
        'id' => env('NFE_CSC_ID', 1),
    ],
    
    'serie' => [
        'nfe' => env('NFE_SERIE', 1),
        'nfce' => env('NFCE_SERIE', 1),
    ],
    
    'timeout' => env('NFE_TIMEOUT', 30),
];
```

### 3.2 Arquivo .env
```env
NFE_AMBIENTE=homologacao
NFE_CERTIFICADO_SENHA=senha_do_certificado

NFE_RAZAO_SOCIAL="Minha Empresa Ltda"
NFE_NOME_FANTASIA="Minha Empresa"
NFE_CNPJ=12345678000199
NFE_IE=123456789
NFE_CRT=1
NFE_UF=SP

NFE_ENDERECO_LOGRADOURO="Rua Exemplo"
NFE_ENDERECO_NUMERO=123
NFE_ENDERECO_BAIRRO="Centro"
NFE_ENDERECO_MUNICIPIO="São Paulo"
NFE_ENDERECO_CEP=01310100
NFE_CODIGO_MUNICIPIO=3550308

NFE_CSC_CODIGO=CODIGO_CSC_AQUI
NFE_CSC_ID=1

NFE_SERIE=1
NFCE_SERIE=1
NFE_TIMEOUT=30
```

---

## 4. Fluxo Completo de Emissão

### 4.1 Preparar Configuração
```php
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;

$config = [
    "atualizacao" => date('Y-m-d H:i:s'),
    "tpAmb" => config('nfe.ambiente') === 'producao' ? 1 : 2,
    "razaosocial" => config('nfe.emitente.razao_social'),
    "siglaUF" => config('nfe.emitente.uf'),
    "cnpj" => config('nfe.emitente.cnpj'),
    "schemes" => "PL_009_V4",
    "versao" => "4.00",
    "tokenIBPT" => "AAAAAAA",
    "CSC" => config('nfe.csc.codigo'),
    "CSCid" => config('nfe.csc.id'),
];

$certificadoPath = config('nfe.certificado.path');
$certificadoSenha = config('nfe.certificado.senha');

$content = file_get_contents($certificadoPath);
$certificate = Certificate::readPfx($content, $certificadoSenha);

$tools = new Tools(json_encode($config), $certificate);
$tools->model('55'); // NF-e
```

### 4.2 Montar XML da NF-e
```php
use NFePHP\NFe\Make;

$nfe = new Make();

// Identificação da NF-e
$std = new \stdClass();
$std->versao = '4.00';
$std->Id = null; // será gerado automaticamente
$std->pk_nItem = null;
$nfe->taginfNFe($std);

// Dados da NFe (ide)
$std = new \stdClass();
$std->cUF = 35; // SP
$std->cNF = $codigoNumerico;
$std->natOp = 'Venda de mercadoria';
$std->mod = 55;
$std->serie = config('nfe.serie.nfe');
$std->nNF = $numeroNFe;
$std->dhEmi = date('Y-m-d\TH:i:sP');
$std->tpNF = 1; // 0=Entrada, 1=Saída
$std->idDest = 1; // 1=Operação interna, 2=Interestadual, 3=Exterior
$std->cMunFG = config('nfe.emitente.endereco.codigo_municipio');
$std->tpImp = 1; // 1=DANFE Retrato
$std->tpEmis = 1; // 1=Emissão normal
$std->cDV = 0; // Será calculado
$std->tpAmb = config('nfe.ambiente') === 'producao' ? 1 : 2;
$std->finNFe = 1; // 1=Normal, 2=Complementar, 3=Ajuste, 4=Devolução
$std->indFinal = 1; // 0=Não, 1=Consumidor final
$std->indPres = 1; // 1=Presencial
$std->procEmi = 0; // 0=Aplicação do contribuinte
$std->verProc = '1.0.0';
$nfe->tagide($std);

// Emitente
$std = new \stdClass();
$std->xNome = config('nfe.emitente.razao_social');
$std->xFant = config('nfe.emitente.nome_fantasia');
$std->IE = config('nfe.emitente.ie');
$std->CRT = config('nfe.emitente.crt');
$std->CNPJ = config('nfe.emitente.cnpj');
$nfe->tagemit($std);

// Endereço do emitente
$std = new \stdClass();
$std->xLgr = config('nfe.emitente.endereco.logradouro');
$std->nro = config('nfe.emitente.endereco.numero');
$std->xBairro = config('nfe.emitente.endereco.bairro');
$std->cMun = config('nfe.emitente.endereco.codigo_municipio');
$std->xMun = config('nfe.emitente.endereco.municipio');
$std->UF = config('nfe.emitente.uf');
$std->CEP = config('nfe.emitente.endereco.cep');
$std->cPais = 1058;
$std->xPais = 'BRASIL';
$nfe->tagenderEmit($std);

// Destinatário
$std = new \stdClass();
$std->xNome = $fiscalNote->client->name;
if ($fiscalNote->client->tipo_pessoa === 'juridica') {
    $std->CNPJ = $fiscalNote->client->taxNumber;
} else {
    $std->CPF = $fiscalNote->client->taxNumber;
}
$std->indIEDest = 9; // 9=Não Contribuinte
$nfe->tagdest($std);

// Endereço do destinatário
$std = new \stdClass();
$std->xLgr = $fiscalNote->client->address_street;
$std->nro = $fiscalNote->client->address_number;
$std->xBairro = $fiscalNote->client->address_district;
$std->cMun = $fiscalNote->client->ibge_code;
$std->xMun = $fiscalNote->client->address_city;
$std->UF = $fiscalNote->client->address_state;
$std->CEP = $fiscalNote->client->address_zip_code;
$std->cPais = 1058;
$std->xPais = 'BRASIL';
$nfe->tagenderDest($std);

// Itens
foreach ($fiscalNote->items as $index => $item) {
    $itemNumber = $index + 1;
    
    $std = new \stdClass();
    $std->item = $itemNumber;
    $std->cProd = $item->product_code;
    $std->cEAN = $item->ean ?? 'SEM GTIN';
    $std->xProd = $item->description;
    $std->NCM = $item->ncm;
    $std->CFOP = $item->cfop;
    $std->uCom = $item->unit;
    $std->qCom = $item->quantity;
    $std->vUnCom = $item->unit_value;
    $std->vProd = $item->total_value;
    $std->cEANTrib = $item->ean ?? 'SEM GTIN';
    $std->uTrib = $item->unit;
    $std->qTrib = $item->quantity;
    $std->vUnTrib = $item->unit_value;
    $std->indTot = 1;
    $nfe->tagprod($std);
    
    // Impostos do item
    $std = new \stdClass();
    $std->item = $itemNumber;
    $nfe->tagimposto($std);
    
    // ICMS
    $std = new \stdClass();
    $std->item = $itemNumber;
    $std->orig = $item->origin;
    $std->CST = $item->cst;
    $std->modBC = 0;
    $std->vBC = $item->icms_base;
    $std->pICMS = $item->icms_percent;
    $std->vICMS = $item->icms_value;
    $nfe->tagICMS($std);
    
    // PIS
    $std = new \stdClass();
    $std->item = $itemNumber;
    $std->CST = $item->pis_cst;
    $std->vBC = $item->pis_base;
    $std->pPIS = $item->pis_percent;
    $std->vPIS = $item->pis_value;
    $nfe->tagPIS($std);
    
    // COFINS
    $std = new \stdClass();
    $std->item = $itemNumber;
    $std->CST = $item->cofins_cst;
    $std->vBC = $item->cofins_base;
    $std->pCOFINS = $item->cofins_percent;
    $std->vCOFINS = $item->cofins_value;
    $nfe->tagCOFINS($std);
}

// Totais
$std = new \stdClass();
$std->vBC = $fiscalNote->items->sum('icms_base');
$std->vICMS = $fiscalNote->total_icms;
$std->vICMSDeson = 0;
$std->vBCST = 0;
$std->vST = 0;
$std->vProd = $fiscalNote->total_products;
$std->vFrete = $fiscalNote->freight_value ?? 0;
$std->vSeg = $fiscalNote->insurance_value ?? 0;
$std->vDesc = $fiscalNote->discount_value ?? 0;
$std->vII = 0;
$std->vIPI = $fiscalNote->total_ipi;
$std->vPIS = $fiscalNote->total_pis;
$std->vCOFINS = $fiscalNote->total_cofins;
$std->vOutro = $fiscalNote->other_expenses ?? 0;
$std->vNF = $fiscalNote->total_value;
$nfe->tagICMSTot($std);

// Transporte
$std = new \stdClass();
$std->modFrete = $fiscalNote->freight_type; // 0=Emitente, 1=Destinatário
$nfe->tagtransp($std);

// Gerar XML
$xml = $nfe->getXML();
```

### 4.3 Assinar XML
```php
$xml = $tools->signNFe($xml);
```

### 4.4 Transmitir para SEFAZ
```php
$idLote = str_pad($fiscalNote->numero, 15, '0', STR_PAD_LEFT);

try {
    $response = $tools->sefazEnvia($idLote, [$xml]);
    
    $stdCl = new \stdClass();
    $stdCl->versao = '4.00';
    $stdCl->tpAmb = $config['tpAmb'];
    $stdCl->chNFe = $nfe->getChave();
    $stdCl->nProt = $response->nProt;
    $stdCl->xMotivo = $response->xMotivo;
    
    if ($response->cStat == 100) { // Autorizada
        $fiscalNote->update([
            'status' => FiscalNoteStatus::Autorizada,
            'chave_acesso' => $nfe->getChave(),
            'protocol_number' => $response->nProt,
            'data_autorizacao' => now(),
        ]);
        
        // Salvar XML
        $xmlPath = storage_path("app/nfe/autorizadas/{$nfe->getChave()}.xml");
        file_put_contents($xmlPath, $xml);
        
        // Gerar DANFE
        $danfe = new \NFePHP\DA\NFe\Danfe($xml);
        $pdf = $danfe->render();
        file_put_contents(
            storage_path("app/nfe/danfe/{$nfe->getChave()}.pdf"),
            $pdf
        );
        
    } else {
        $fiscalNote->update([
            'status' => FiscalNoteStatus::Rejeitada,
            'rejection_reason' => "{$response->cStat} - {$response->xMotivo}",
        ]);
    }
    
} catch (\Exception $e) {
    Log::error('Erro ao transmitir NF-e', [
        'nfe_id' => $fiscalNote->id,
        'error' => $e->getMessage(),
    ]);
    
    throw $e;
}
```

---

## 5. Cancelamento

```php
$chave = $fiscalNote->chave_acesso;
$protocolo = $fiscalNote->protocol_number;
$motivo = 'Cliente solicitou cancelamento';

try {
    $response = $tools->sefazCancela(
        $chave,
        $protocolo,
        $motivo
    );
    
    if ($response->cStat == 135) { // Evento registrado
        $fiscalNote->update([
            'status' => FiscalNoteStatus::Cancelada,
            'data_cancelamento' => now(),
            'cancellation_reason' => $motivo,
        ]);
    }
    
} catch (\Exception $e) {
    Log::error('Erro ao cancelar NF-e', [
        'nfe_id' => $fiscalNote->id,
        'error' => $e->getMessage(),
    ]);
}
```

---

**Caminho**: `app/Http/Controllers/Api/NFeController.php`, `config/nfe.php`

