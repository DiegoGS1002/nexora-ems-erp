<?php

use App\Models\Company;
use App\Enums\FiscalConsumer;
use App\Enums\FiscalDestination;
use App\Enums\FiscalEmissionType;
use App\Enums\FiscalPresence;
use App\Enums\FiscalPurpose;
use App\Enums\FiscalFreightMode;
use App\Models\Client;
use App\Models\FiscalNote;
use App\Services\NFeService;

function nfeXmlService(int $crt = 3): NFeService
{
    $emitter = new Company([
        'name' => 'Empresa Teste',
        'social_name' => 'Empresa Teste Ltda',
        'cnpj' => '00000000000191',
        'inscricao_estadual' => '123456789',
        'address_zip_code' => '01001000',
        'address_street' => 'Praça da Sé',
        'address_number' => '1',
        'address_district' => 'Sé',
        'address_city' => 'São Paulo',
        'address_state' => 'SP',
        'crt' => $crt,
    ]);

    return new class($emitter) extends NFeService
    {
        public function __construct(Company $emitter)
        {
            $this->emitter = $emitter;
        }
    };
}

function fiscalNoteForXml(array $itemOverrides = []): FiscalNote
{
    $itemData = array_merge([
        'item_number' => 1,
        'product_code' => 'PROD-1',
        'description' => 'Produto Teste',
        'ncm' => '84713012',
        'cfop' => '5102',
        'unit' => 'UN',
        'quantity' => 1,
        'unit_price' => 100,
        'total' => 100,
        'origin' => 0,
        'cst' => '00',
        'icms_mod_bc' => 3,
        'icms_base' => 100,
        'icms_percent' => 18,
        'icms_amount' => 18,
        'pis_cst' => '01',
        'pis_base' => 100,
        'pis_percent' => 1.65,
        'pis_amount' => 1.65,
        'cofins_cst' => '01',
        'cofins_base' => 100,
        'cofins_percent' => 7.6,
        'cofins_amount' => 7.6,
    ], $itemOverrides);

    $amount = $itemData['total']
        + ($itemData['freight'] ?? 0)
        + ($itemData['insurance'] ?? 0)
        + ($itemData['other'] ?? 0)
        + ($itemData['icms_st_amount'] ?? 0)
        + ($itemData['ipi_amount'] ?? 0)
        - ($itemData['discount'] ?? 0);

    $note = FiscalNote::factory()->create([
        'invoice_number' => '123456789',
        'amount' => $amount,
    ]);

    $note->items()->create($itemData);

    return $note->fresh('items');
}

function nfeXpath(string $xml): DOMXPath
{
    $document = new DOMDocument;
    $document->loadXML($xml);
    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');

    return $xpath;
}

it('builds normal regime taxes totals and payment in their correct XML groups', function () {
    $xml = nfeXmlService()->buildXml(fiscalNoteForXml());
    $xpath = nfeXpath($xml);

    expect($xpath->query('//nfe:det/nfe:imposto/nfe:ICMS/nfe:ICMS00'))->toHaveCount(1)
        ->and($xpath->query('//nfe:det/nfe:imposto/nfe:PIS/nfe:PISAliq'))->toHaveCount(1)
        ->and($xpath->query('//nfe:det/nfe:imposto/nfe:COFINS/nfe:COFINSAliq'))->toHaveCount(1)
        ->and($xpath->query('//nfe:total/nfe:ICMSTot'))->toHaveCount(1)
        ->and($xpath->evaluate('string(//nfe:total/nfe:ICMSTot/nfe:vICMS)'))->toBe('18.00')
        ->and($xpath->evaluate('string(//nfe:pag/nfe:detPag/nfe:tPag)'))->toBe('01')
        ->and($xpath->evaluate('string(//nfe:pag/nfe:detPag/nfe:vPag)'))->toBe('100.00');

});

it('uses ICMSSN for a Simples Nacional item', function () {
    $note = fiscalNoteForXml([
        'cst' => null,
        'csosn' => '102',
        'icms_base' => 0,
        'icms_percent' => 0,
        'icms_amount' => 0,
    ]);

    $xpath = nfeXpath(nfeXmlService(1)->buildXml($note));

    expect($xpath->query('//nfe:det/nfe:imposto/nfe:ICMS/nfe:ICMSSN102'))->toHaveCount(1)
        ->and($xpath->query('//nfe:det/nfe:imposto/nfe:ICMS/nfe:ICMS00'))->toHaveCount(0);
});

it('adds IPI with its required legal classification', function () {
    config(['nfe.ipi_cenq_default' => '999']);

    $note = fiscalNoteForXml([
        'ipi_cst' => '50',
        'ipi_base' => 100,
        'ipi_percent' => 5,
        'ipi_amount' => 5,
    ]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note));

    expect($xpath->query('//nfe:det/nfe:imposto/nfe:IPI/nfe:IPITrib'))->toHaveCount(1)
        ->and($xpath->evaluate('string(//nfe:det/nfe:imposto/nfe:IPI/nfe:cEnq)'))->toBe('999')
        ->and($xpath->evaluate('string(//nfe:det/nfe:imposto/nfe:IPI/nfe:IPITrib/nfe:vIPI)'))->toBe('5.00');
});

it('uses configurable operation identification and referenced document', function () {
    $note = fiscalNoteForXml();
    $note->update([
        'operation_nature' => 'Devolução de mercadoria',
        'destination' => FiscalDestination::Interstate,
        'consumer_type' => FiscalConsumer::Normal,
        'presence_type' => FiscalPresence::Internet,
        'purpose' => FiscalPurpose::Return,
        'emission_type' => FiscalEmissionType::SvcAn,
        'referenced_access_key' => str_repeat('1', 44),
    ]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note->fresh('items')));

    expect($xpath->evaluate('string(//nfe:ide/nfe:natOp)'))->toBe('Devolução de mercadoria')
        ->and($xpath->evaluate('string(//nfe:ide/nfe:idDest)'))->toBe('2')
        ->and($xpath->evaluate('string(//nfe:ide/nfe:indFinal)'))->toBe('0')
        ->and($xpath->evaluate('string(//nfe:ide/nfe:indPres)'))->toBe('2')
        ->and($xpath->evaluate('string(//nfe:ide/nfe:finNFe)'))->toBe('4')
        ->and($xpath->evaluate('string(//nfe:ide/nfe:tpEmis)'))->toBe('6')
        ->and($xpath->evaluate('string(//nfe:ide/nfe:NFref/nfe:refNFe)'))->toBe(str_repeat('1', 44));
});

it('derives the operation destination from emitter and recipient states', function (string $state, string $expected) {
    $client = Client::create([
        'name' => 'Cliente destino',
        'taxNumber' => '52998224725',
        'address_street' => 'Rua Teste',
        'address_number' => '10',
        'address_district' => 'Centro',
        'address_city' => $state === 'EX' ? 'Exterior' : 'Curitiba',
        'address_state' => $state,
        'address_zip_code' => '80000000',
    ]);
    $note = fiscalNoteForXml();
    $note->update(['client_id' => $client->id, 'destination' => null]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note->fresh(['client', 'items'])));

    expect($xpath->evaluate('string(//nfe:ide/nfe:idDest)'))->toBe($expected);
})->with([
    'interna' => ['SP', '1'],
    'interestadual' => ['PR', '2'],
    'exterior' => ['EX', '3'],
]);

it('adds intermediary and an alternative delivery location', function () {
    $note = fiscalNoteForXml();
    $note->update([
        'intermediary_cnpj' => '11222333000181',
        'intermediary_identifier' => 'seller-123',
        'delivery_location' => [
            'tax_number' => '52998224725',
            'name' => 'Cliente Teste',
            'street' => 'Rua da Entrega',
            'number' => '25',
            'district' => 'Centro',
            'ibge_code' => '3550308',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01001000',
        ],
    ]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note->fresh('items')));

    expect($xpath->evaluate('string(//nfe:ide/nfe:indIntermed)'))->toBe('1')
        ->and($xpath->evaluate('string(//nfe:infIntermed/nfe:CNPJ)'))->toBe('11222333000181')
        ->and($xpath->evaluate('string(//nfe:infIntermed/nfe:idCadIntTran)'))->toBe('seller-123')
        ->and($xpath->evaluate('string(//nfe:entrega/nfe:xLgr)'))->toBe('Rua da Entrega');
});

it('transmits reduction desoneration fcp st and difal in ICMS groups', function () {
    $note = fiscalNoteForXml([
        'cst' => '90',
        'icms_st_base' => 100,
        'icms_st_percent' => 18,
        'icms_st_amount' => 18,
        'icms_data' => [
            'pRedBC' => 10,
            'vICMSDeson' => 5,
            'motDesICMS' => 9,
            'vBCFCP' => 90,
            'pFCP' => 2,
            'vFCP' => 1.8,
            'vBCFCPST' => 100,
            'pFCPST' => 2,
            'vFCPST' => 2,
            'difal' => [
                'vBCUFDest' => 100,
                'vBCFCPUFDest' => 100,
                'pFCPUFDest' => 2,
                'pICMSUFDest' => 18,
                'pICMSInter' => 12,
                'pICMSInterPart' => 100,
                'vFCPUFDest' => 2,
                'vICMSUFDest' => 6,
                'vICMSUFRemet' => 0,
            ],
        ],
    ]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note));

    expect($xpath->evaluate('string(//nfe:ICMS90/nfe:pRedBC)'))->toBe('10.0000')
        ->and($xpath->evaluate('string(//nfe:ICMS90/nfe:vICMSDeson)'))->toBe('5.00')
        ->and($xpath->evaluate('string(//nfe:ICMS90/nfe:vFCP)'))->toBe('1.80')
        ->and($xpath->evaluate('string(//nfe:ICMSUFDest/nfe:vICMSUFDest)'))->toBe('6.00');
});

it('rejects CST incompatible with Simples Nacional CRT', function () {
    expect(fn () => nfeXmlService(1)->buildXml(fiscalNoteForXml()))
        ->toThrow(InvalidArgumentException::class, 'exige CSOSN');
});

it('transmits complete commercial and taxable product data', function () {
    $note = fiscalNoteForXml([
        'ean' => '7894900011517',
        'tax_ean' => '7894900011517',
        'cest' => '2106400',
        'tax_benefit_code' => 'SP123456',
        'ex_tipi' => '001',
        'tax_unit' => 'CX',
        'tax_quantity' => 2,
        'tax_unit_price' => 50,
        'relevant_scale' => false,
        'manufacturer_cnpj' => '11222333000181',
        'purchase_order_number' => 'PED-123',
        'purchase_order_item' => 7,
    ]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note));

    expect($xpath->evaluate('string(//nfe:prod/nfe:CEST)'))->toBe('2106400')
        ->and($xpath->evaluate('string(//nfe:prod/nfe:cBenef)'))->toBe('SP123456')
        ->and($xpath->evaluate('string(//nfe:prod/nfe:uTrib)'))->toBe('CX')
        ->and($xpath->evaluate('string(//nfe:prod/nfe:qTrib)'))->toBe('2.0000')
        ->and($xpath->evaluate('string(//nfe:prod/nfe:xPed)'))->toBe('PED-123');
});

it('rejects an invalid GTIN', function () {
    expect(fn () => nfeXmlService()->buildXml(fiscalNoteForXml(['ean' => '7894900011518'])))
        ->toThrow(Throwable::class);
});

it('transmits traceability and import declarations', function () {
    $note = fiscalNoteForXml([
        'traceability' => [[
            'nLote' => 'LOTE-1',
            'qLote' => 1,
            'dFab' => '2026-01-01',
            'dVal' => '2027-01-01',
        ]],
        'import_declarations' => [[
            'nDI' => 'DI123',
            'dDI' => '2026-01-01',
            'xLocDesemb' => 'Santos',
            'UFDesemb' => 'SP',
            'dDesemb' => '2026-01-02',
            'tpViaTransp' => 1,
            'tpIntermedio' => 1,
            'cExportador' => 'EXP1',
            'additions' => [[
                'nAdicao' => 1,
                'nSeqAdic' => 1,
                'cFabricante' => 'FAB1',
            ]],
        ]],
    ]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note));

    expect($xpath->evaluate('string(//nfe:prod/nfe:rastro/nfe:nLote)'))->toBe('LOTE-1')
        ->and($xpath->evaluate('string(//nfe:prod/nfe:DI/nfe:nDI)'))->toBe('DI123')
        ->and($xpath->evaluate('string(//nfe:prod/nfe:DI/nfe:adi/nfe:cFabricante)'))->toBe('FAB1');
});

it('calculates all legacy tax totals from items', function () {
    $note = fiscalNoteForXml([
        'insurance' => 3,
        'other' => 2,
        'import_tax_amount' => 4,
        'returned_ipi_amount' => 5,
        'approximate_tax_amount' => 30,
        'icms_data' => [
            'vICMSDeson' => 6,
            'indDeduzDeson' => 1,
            'vFCP' => 2,
            'vFCPST' => 1,
            'vFCPSTRet' => 0.5,
        ],
    ]);
    $note->update(['amount' => 104]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note->fresh('items')));

    expect($xpath->evaluate('string(//nfe:ICMSTot/nfe:vSeg)'))->toBe('3.00')
        ->and($xpath->evaluate('string(//nfe:ICMSTot/nfe:vOutro)'))->toBe('2.00')
        ->and($xpath->evaluate('string(//nfe:ICMSTot/nfe:vICMSDeson)'))->toBe('6.00')
        ->and($xpath->evaluate('string(//nfe:ICMSTot/nfe:vFCP)'))->toBe('2.00')
        ->and($xpath->evaluate('string(//nfe:ICMSTot/nfe:vII)'))->toBe('4.00')
        ->and($xpath->evaluate('string(//nfe:ICMSTot/nfe:vIPIDevol)'))->toBe('5.00')
        ->and($xpath->evaluate('string(//nfe:ICMSTot/nfe:vTotTrib)'))->toBe('30.00');
});

it('blocks a note whose total differs from its items', function () {
    $note = fiscalNoteForXml();
    $note->update(['amount' => 99]);

    expect(fn () => nfeXmlService()->buildXml($note->fresh('items')))
        ->toThrow(InvalidArgumentException::class, 'diverge da soma dos itens');
});

it('transmits carrier vehicle volumes seals and retained transport ICMS', function () {
    $note = fiscalNoteForXml();
    $note->update([
        'freight_mode' => FiscalFreightMode::Sender,
        'transport_data' => [
            'carrier' => [
                'xNome' => 'Transportadora Teste',
                'CNPJ' => '11222333000181',
                'IE' => '123456789',
                'xEnder' => 'Rua do Transporte, 10',
                'xMun' => 'São Paulo',
                'UF' => 'SP',
            ],
            'vehicle' => ['placa' => 'ABC1D23', 'UF' => 'SP', 'RNTC' => '12345678'],
            'volumes' => [[
                'qVol' => 2,
                'esp' => 'CAIXA',
                'marca' => 'NEXORA',
                'nVol' => '1-2',
                'pesoL' => 9.5,
                'pesoB' => 10,
                'seals' => ['LACRE-1'],
            ]],
            'retained_icms' => [
                'vServ' => 100,
                'vBCRet' => 100,
                'pICMSRet' => 12,
                'vICMSRet' => 12,
                'CFOP' => '5353',
                'cMunFG' => '3550308',
            ],
        ],
    ]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note->fresh('items')));

    expect($xpath->evaluate('string(//nfe:transp/nfe:modFrete)'))->toBe('0')
        ->and($xpath->evaluate('string(//nfe:transporta/nfe:xNome)'))->toBe('Transportadora Teste')
        ->and($xpath->evaluate('string(//nfe:veicTransp/nfe:placa)'))->toBe('ABC1D23')
        ->and($xpath->evaluate('string(//nfe:vol/nfe:pesoB)'))->toBe('10.000')
        ->and($xpath->evaluate('string(//nfe:vol/nfe:lacres/nfe:nLacre)'))->toBe('LACRE-1')
        ->and($xpath->evaluate('string(//nfe:retTransp/nfe:vICMSRet)'))->toBe('12.00');
});

it('transmits multiple payment methods card data change invoice and installments', function () {
    $note = fiscalNoteForXml();
    $note->update([
        'change_amount' => 5,
        'payment_details' => [
            ['indPag' => 0, 'tPag' => '17', 'vPag' => 40],
            [
                'indPag' => 1,
                'tPag' => '03',
                'vPag' => 65,
                'tpIntegra' => 1,
                'CNPJ' => '11222333000181',
                'tBand' => '01',
                'cAut' => 'AUTH123',
            ],
        ],
        'invoice_data' => ['nFat' => 'FAT-1', 'vOrig' => 100, 'vDesc' => 0, 'vLiq' => 100],
        'installment_data' => [
            ['nDup' => '001', 'dVenc' => '2026-09-27', 'vDup' => 50],
            ['nDup' => '002', 'dVenc' => '2026-10-27', 'vDup' => 50],
        ],
    ]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note->fresh('items')));

    expect($xpath->query('//nfe:pag/nfe:detPag'))->toHaveCount(2)
        ->and($xpath->evaluate('string(//nfe:pag/nfe:vTroco)'))->toBe('5.00')
        ->and($xpath->evaluate('string(//nfe:detPag[nfe:tPag="17"]/nfe:vPag)'))->toBe('40.00')
        ->and($xpath->evaluate('string(//nfe:detPag[nfe:tPag="03"]/nfe:card/nfe:cAut)'))->toBe('AUTH123')
        ->and($xpath->evaluate('string(//nfe:cobr/nfe:fat/nfe:nFat)'))->toBe('FAT-1')
        ->and($xpath->query('//nfe:cobr/nfe:dup'))->toHaveCount(2);
});

it('blocks payments that do not reconcile with the note total', function () {
    $note = fiscalNoteForXml();
    $note->update(['payment_details' => [['indPag' => 0, 'tPag' => '17', 'vPag' => 90]]]);

    expect(fn () => nfeXmlService()->buildXml($note->fresh('items')))
        ->toThrow(InvalidArgumentException::class, 'Total pago');
});

it('transmits IBS CBS selective tax and their totals', function () {
    $note = fiscalNoteForXml([
        'selective_tax_data' => [
            'CSTIS' => '000',
            'cClassTribIS' => '000001',
            'vBCIS' => 100,
            'pIS' => 1,
            'vIS' => 1,
        ],
        'ibs_cbs_data' => [
            'CST' => '000',
            'cClassTrib' => '000001',
            'vBC' => 100,
            'gIBSUF_pIBSUF' => 0.1,
            'gIBSUF_vIBSUF' => 0.1,
            'gIBSMun_pIBSMun' => 0,
            'gIBSMun_vIBSMun' => 0,
            'gCBS_pCBS' => 0.9,
            'gCBS_vCBS' => 0.9,
            'presumed_credit' => [
                'vBCCredPres' => 100,
                'cCredPres' => '01',
                'ibs_pCredPres' => 1,
                'ibs_vCredPres' => 1,
                'cbs_pCredPres' => 1,
                'cbs_vCredPres' => 1,
            ],
        ],
    ]);

    $xpath = nfeXpath(nfeXmlService()->buildXml($note));

    expect($xpath->evaluate('string(//nfe:IS/nfe:vIS)'))->toBe('1.00')
        ->and($xpath->evaluate('string(//nfe:IBSCBS/nfe:CST)'))->toBe('000')
        ->and($xpath->evaluate('string(//nfe:IBSCBS/nfe:cClassTrib)'))->toBe('000001')
        ->and($xpath->evaluate('string(//nfe:IBSCBS/nfe:gIBSCBS/nfe:gIBSUF/nfe:vIBSUF)'))->toBe('0.10')
        ->and($xpath->evaluate('string(//nfe:total/nfe:ISTot/nfe:vIS)'))->toBe('1.00')
        ->and($xpath->evaluate('string(//nfe:total/nfe:IBSCBSTot/nfe:vCBS)'))->toBe('0.90');
});
