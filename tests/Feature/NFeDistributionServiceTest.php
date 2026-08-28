<?php

use App\Models\Company;
use App\Models\DfeDistributionState;
use App\Models\DfeDocument;
use App\Services\NFeDistributionService;
use App\Services\NFeService;
use Illuminate\Support\Facades\Storage;

it('stores distributed documents and advances NSU idempotently', function () {
    Storage::fake('local');
    config(['nfe.xml_disk' => 'local']);
    $company = Company::factory()->create(['is_active' => true]);
    $accessKey = str_repeat('1', 44);
    $document = "<resNFe><chNFe>{$accessKey}</chNFe></resNFe>";
    $docZip = base64_encode(gzencode($document));
    $response = <<<XML
    <retDistDFeInt xmlns="http://www.portalfiscal.inf.br/nfe" versao="1.01">
        <tpAmb>2</tpAmb><verAplic>1</verAplic><cStat>138</cStat><xMotivo>Documento localizado</xMotivo>
        <dhResp>2026-08-27T10:00:00-03:00</dhResp><ultNSU>10</ultNSU><maxNSU>10</maxNSU>
        <loteDistDFeInt><docZip NSU="10" schema="resNFe_v1.01.xsd">{$docZip}</docZip></loteDistDFeInt>
    </retDistDFeInt>
    XML;
    $nfe = Mockery::mock(NFeService::class);
    $nfe->shouldReceive('distributeDfe')->once()->with(0)->andReturn($response);

    $processed = (new NFeDistributionService($nfe))->synchronize($company, 'homologation');

    expect($processed)->toBe(1)
        ->and(DfeDocument::count())->toBe(1)
        ->and(DfeDocument::first()->access_key)->toBe($accessKey)
        ->and(DfeDistributionState::first()->last_nsu)->toBe(10);
    Storage::disk('local')->assertExists("fiscal/dfe/{$company->id}/homologation/10.xml");
});
