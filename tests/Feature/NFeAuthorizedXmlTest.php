<?php

use App\Models\FiscalNote;
use App\Services\NFeService;
use Illuminate\Support\Facades\Storage;

function authorizedXmlService(): object
{
    return new class extends NFeService
    {
        public function __construct() {}

        public function saveSent(FiscalNote $note, string $xml): string
        {
            return $this->saveXml($note, $xml, 'enviado');
        }

        public function saveAuthorized(
            FiscalNote $note,
            string $signedXml,
            string $response,
            string $accessKey
        ): string {
            return $this->saveAuthorizedXml($note, $signedXml, $response, $accessKey);
        }
    };
}

function signedNFeXml(string $accessKey, string $digest = 'DIGEST-TESTE'): string
{
    return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <NFe xmlns="http://www.portalfiscal.inf.br/nfe">
            <infNFe Id="NFe{$accessKey}" versao="4.00">
                <ide><cUF>35</cUF></ide>
            </infNFe>
            <Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
                <SignedInfo><Reference URI="#NFe{$accessKey}"><DigestValue>{$digest}</DigestValue></Reference></SignedInfo>
                <SignatureValue>ASSINATURA</SignatureValue>
                <KeyInfo/>
            </Signature>
        </NFe>
        XML;
}

function authorizedNFeResponse(string $accessKey, string $digest = 'DIGEST-TESTE'): string
{
    return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <retEnviNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">
            <cStat>104</cStat>
            <xMotivo>Lote processado</xMotivo>
            <protNFe versao="4.00">
                <infProt>
                    <chNFe>{$accessKey}</chNFe>
                    <dhRecbto>2026-08-27T12:00:00-03:00</dhRecbto>
                    <nProt>123456789012345</nProt>
                    <digVal>{$digest}</digVal>
                    <cStat>100</cStat>
                    <xMotivo>Autorizado o uso da NF-e</xMotivo>
                </infProt>
            </protNFe>
        </retEnviNFe>
        XML;
}

it('preserves the signed XML and promotes nfeProc as the official XML', function () {
    config([
        'filesystems.disks.nfe-test' => [
            'driver' => 'local',
            'root' => sys_get_temp_dir().'/nexora-nfe-tests',
        ],
        'nfe.xml_disk' => 'nfe-test',
        'nfe.xml_path' => 'nfe/xmls',
    ]);

    $note = FiscalNote::factory()->create(['invoice_number' => '123456789']);
    $key = str_repeat('1', 44);
    $signedXml = signedNFeXml($key);
    $service = authorizedXmlService();

    $sentPath = $service->saveSent($note, $signedXml);
    $authorizedPath = $service->saveAuthorized($note, $signedXml, authorizedNFeResponse($key), $key);
    $note->refresh();

    expect($note->xml_sent_path)->toBe($sentPath)
        ->and($note->xml_path)->toBe($authorizedPath)
        ->and($note->xml_path)->not->toBe($note->xml_sent_path);

    Storage::disk('nfe-test')->assertExists($sentPath);
    Storage::disk('nfe-test')->assertExists($authorizedPath);

    $processedXml = Storage::disk('nfe-test')->get($authorizedPath);
    expect($processedXml)->toContain('<nfeProc')
        ->and($processedXml)->toContain('<protNFe')
        ->and($processedXml)->toContain('<nProt>123456789012345</nProt>')
        ->and($processedXml)->toContain("<chNFe>{$key}</chNFe>");
});

it('does not store an authorized XML when the protocol belongs to another key', function () {
    config([
        'filesystems.disks.nfe-test' => [
            'driver' => 'local',
            'root' => sys_get_temp_dir().'/nexora-nfe-tests',
        ],
        'nfe.xml_disk' => 'nfe-test',
        'nfe.xml_path' => 'nfe/xmls',
    ]);

    $note = FiscalNote::factory()->create(['invoice_number' => '987654321']);
    $key = str_repeat('1', 44);

    expect(fn () => authorizedXmlService()->saveAuthorized(
        $note,
        signedNFeXml($key),
        authorizedNFeResponse(str_repeat('2', 44)),
        $key,
    ))->toThrow(RuntimeException::class, 'As chaves do XML processado não correspondem');

    Storage::disk('nfe-test')->assertMissing('nfe/xmls/987654321_autorizado.xml');
});
