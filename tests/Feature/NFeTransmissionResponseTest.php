<?php

use App\Enums\FiscalNoteStatus;
use App\Enums\NFeTransmissionState;
use App\Models\FiscalNote;
use App\Services\NFeService;

function transmissionResponseParser(): object
{
    return new class extends NFeService
    {
        public function __construct() {}

        public function parse(string $response, ?string $expectedAccessKey = null): array
        {
            return $this->parseTransmissionResponse($response, $expectedAccessKey);
        }

        public function applyResult(FiscalNote $note, array $result, ?string $expectedAccessKey = null): void
        {
            $this->applyTransmissionResult($note, $result, $expectedAccessKey);
        }
    };
}

function synchronousNFeResponse(string $protocolCode = '100', ?string $accessKey = null): string
{
    $accessKey ??= str_repeat('1', 44);

    return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <retEnviNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">
            <tpAmb>2</tpAmb>
            <verAplic>TESTE</verAplic>
            <cStat>104</cStat>
            <xMotivo>Lote processado</xMotivo>
            <protNFe versao="4.00">
                <infProt>
                    <tpAmb>2</tpAmb>
                    <verAplic>TESTE</verAplic>
                    <chNFe>{$accessKey}</chNFe>
                    <dhRecbto>2026-08-26T10:00:00-03:00</dhRecbto>
                    <nProt>123456789012345</nProt>
                    <digVal>digest</digVal>
                    <cStat>{$protocolCode}</cStat>
                    <xMotivo>Resultado do protocolo</xMotivo>
                </infProt>
            </protNFe>
        </retEnviNFe>
        XML;
}

it('uses the protocol result instead of the batch result for synchronous authorization', function () {
    $key = str_repeat('1', 44);
    $result = transmissionResponseParser()->parse(synchronousNFeResponse(accessKey: $key), $key);

    expect($result['state'])->toBe(NFeTransmissionState::Authorized)
        ->and($result['authorized'])->toBeTrue()
        ->and($result['is_final'])->toBeTrue()
        ->and($result['batch_code'])->toBe('104')
        ->and($result['protocol_code'])->toBe('100')
        ->and($result['code'])->toBe('100')
        ->and($result['access_key'])->toBe($key)
        ->and($result['protocol'])->toBe('123456789012345')
        ->and($result['message'])->toBe('Resultado do protocolo');
});

it('classifies authorization outside the deadline as authorized', function () {
    $result = transmissionResponseParser()->parse(synchronousNFeResponse('150'));

    expect($result['state'])->toBe(NFeTransmissionState::Authorized)
        ->and($result['authorized'])->toBeTrue();
});

it('distinguishes rejected and denied protocol results', function (string $code, NFeTransmissionState $state) {
    $result = transmissionResponseParser()->parse(synchronousNFeResponse($code));

    expect($result['state'])->toBe($state)
        ->and($result['authorized'])->toBeFalse()
        ->and($result['is_final'])->toBeTrue();
})->with([
    'rejected' => ['539', NFeTransmissionState::Rejected],
    'denied' => ['110', NFeTransmissionState::Denied],
]);

it('preserves the receipt when an asynchronous batch is processing', function () {
    $response = <<<'XML'
        <retEnviNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">
            <cStat>103</cStat>
            <xMotivo>Lote recebido com sucesso</xMotivo>
            <nRec>123456789012345</nRec>
        </retEnviNFe>
        XML;

    $result = transmissionResponseParser()->parse($response);

    expect($result['state'])->toBe(NFeTransmissionState::Processing)
        ->and($result['is_final'])->toBeFalse()
        ->and($result['receipt'])->toBe('123456789012345')
        ->and($result['code'])->toBe('103');
});

it('persists a processing response without classifying the note as rejected', function () {
    $key = str_repeat('1', 44);
    $response = <<<'XML'
        <retEnviNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">
            <cStat>103</cStat>
            <xMotivo>Lote recebido com sucesso</xMotivo>
            <nRec>123456789012345</nRec>
        </retEnviNFe>
        XML;
    $parser = transmissionResponseParser();
    $result = $parser->parse($response);
    $note = FiscalNote::factory()->create();

    $parser->applyResult($note, $result, $key);

    expect($note->fresh()->status)->toBe(FiscalNoteStatus::Sent)
        ->and($note->fresh()->receipt_number)->toBe('123456789012345')
        ->and($note->fresh()->access_key)->toBe($key);
});

it('marks a processed batch without a protocol as indeterminate', function () {
    $response = <<<'XML'
        <retEnviNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">
            <cStat>104</cStat>
            <xMotivo>Lote processado</xMotivo>
        </retEnviNFe>
        XML;

    $result = transmissionResponseParser()->parse($response);

    expect($result['state'])->toBe(NFeTransmissionState::Indeterminate)
        ->and($result['is_final'])->toBeFalse();
});

it('finds the protocol inside a SOAP envelope with namespaces', function () {
    $inner = synchronousNFeResponse();
    $inner = preg_replace('/<\?xml[^>]+>/', '', $inner);
    $response = <<<XML
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
            <soap:Body>{$inner}</soap:Body>
        </soap:Envelope>
        XML;

    $result = transmissionResponseParser()->parse($response);

    expect($result['state'])->toBe(NFeTransmissionState::Authorized)
        ->and($result['protocol_code'])->toBe('100');
});

it('rejects malformed responses and access keys from another note', function () {
    expect(fn () => transmissionResponseParser()->parse('<invalid'))
        ->toThrow(RuntimeException::class, 'Resposta XML inválida da SEFAZ');

    expect(fn () => transmissionResponseParser()->parse(
        synchronousNFeResponse(accessKey: str_repeat('1', 44)),
        str_repeat('2', 44),
    ))->toThrow(RuntimeException::class, 'não corresponde à NF-e transmitida');
});
