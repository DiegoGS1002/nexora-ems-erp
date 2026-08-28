<?php

use App\Enums\RecipientManifestationType;
use App\Models\Company;
use App\Models\DfeDocument;
use App\Services\NFeService;
use App\Services\RecipientManifestationService;

it('registers a recipient manifestation idempotently', function () {
    $document = DfeDocument::create([
        'company_id' => Company::factory()->create()->id,
        'environment' => 'homologation',
        'nsu' => 1,
        'access_key' => str_repeat('1', 44),
        'schema' => 'resNFe_v1.01.xsd',
        'document_type' => 'summary',
        'raw_xml' => '<resNFe/>',
    ]);
    $response = '<retEnvEvento><retEvento><infEvento><cStat>135</cStat><xMotivo>Evento registrado</xMotivo><nProt>123</nProt></infEvento></retEvento></retEnvEvento>';
    $nfe = Mockery::mock(NFeService::class);
    $nfe->shouldReceive('manifestRecipient')->once()->andReturn($response);
    $service = new RecipientManifestationService($nfe);

    $first = $service->manifest($document, RecipientManifestationType::OperationAcknowledgement);
    $second = $service->manifest($document, RecipientManifestationType::OperationAcknowledgement);

    expect($first->status)->toBe('registered')
        ->and($first->protocol)->toBe('123')
        ->and($second->id)->toBe($first->id);
});
