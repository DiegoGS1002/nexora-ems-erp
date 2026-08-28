<?php

namespace App\Services;

use App\Models\Company;
use App\Models\FiscalInvoiceIn;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Storage;

class NFeIncomingXmlValidator
{
    public function __construct(protected NFeService $nfeService) {}

    public function validateAndStore(string $xml, Company $company): array
    {
        if (strlen($xml) > 5 * 1024 * 1024) {
            throw new \InvalidArgumentException('O XML excede o limite de 5 MB.');
        }

        $document = $this->loadXml($xml);
        $xpath = new DOMXPath($document);
        $infNFe = $xpath->query('//*[local-name()="NFe"]/*[local-name()="infNFe"]')->item(0);

        if (! $infNFe instanceof DOMElement) {
            throw new \InvalidArgumentException('O arquivo não contém uma NF-e.');
        }

        $model = trim($xpath->evaluate('string(./*[local-name()="ide"]/*[local-name()="mod"])', $infNFe));
        if ($model !== '55') {
            throw new \InvalidArgumentException('Somente NF-e modelo 55 pode ser importada.');
        }

        $accessKey = str_replace('NFe', '', $infNFe->getAttribute('Id'));
        if (! preg_match('/^\d{44}$/', $accessKey) || ! $this->hasValidCheckDigit($accessKey)) {
            throw new \InvalidArgumentException('Chave de acesso da NF-e inválida.');
        }

        if (FiscalInvoiceIn::where('access_key', $accessKey)->exists()) {
            throw new \InvalidArgumentException('Esta NF-e já foi importada.');
        }

        $recipient = preg_replace('/\D/', '', $xpath->evaluate('string(./*[local-name()="dest"]/*[local-name()="CNPJ"])', $infNFe));
        if ($recipient !== preg_replace('/\D/', '', $company->cnpj)) {
            throw new \InvalidArgumentException('O CNPJ destinatário não corresponde à empresa ativa.');
        }

        $protocol = $xpath->query('//*[local-name()="protNFe"]/*[local-name()="infProt"]')->item(0);
        $protocolKey = $protocol ? trim($xpath->evaluate('string(./*[local-name()="chNFe"])', $protocol)) : '';
        $status = $protocol ? trim($xpath->evaluate('string(./*[local-name()="cStat"])', $protocol)) : '';
        if (! $protocol || $protocolKey !== $accessKey || $status !== '100') {
            throw new \InvalidArgumentException('NF-e sem protocolo de autorização válido.');
        }

        $this->validateSchema($document);
        $this->validateSignature($document, $xpath, $infNFe);
        $this->validateCurrentStatus($this->nfeService->consultAccessKey($accessKey));

        $path = "fiscal/entrada/originais/{$accessKey}.xml";
        Storage::disk(config('nfe.xml_disk'))->put($path, $xml);

        return [
            'access_key' => $accessKey,
            'protocol' => trim($xpath->evaluate('string(./*[local-name()="nProt"])', $protocol)),
            'status' => $status,
            'xml_path' => $path,
            'raw_xml' => $xml,
            'hash' => hash('sha256', $xml),
            'installments' => collect($xpath->query('//*[local-name()="cobr"]/*[local-name()="dup"]'))
                ->map(fn ($duplicate, int $index): array => [
                    'number' => (int) (trim($xpath->evaluate('string(./*[local-name()="nDup"])', $duplicate)) ?: $index + 1),
                    'due_date' => trim($xpath->evaluate('string(./*[local-name()="dVenc"])', $duplicate)),
                    'amount' => (float) $xpath->evaluate('string(./*[local-name()="vDup"])', $duplicate),
                ])->all(),
        ];
    }

    protected function loadXml(string $xml): DOMDocument
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_COMPACT);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded || $document->doctype || $errors !== []) {
            throw new \InvalidArgumentException('XML inválido ou com construções não permitidas.');
        }

        return $document;
    }

    protected function validateSchema(DOMDocument $document): void
    {
        $schema = base_path('vendor/nfephp-org/sped-nfe/schemes/PL_010_V1.30/procNFe_v4.00.xsd');
        libxml_use_internal_errors(true);
        $valid = $document->schemaValidate($schema);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        if (! $valid) {
            $message = trim($errors[0]->message ?? 'estrutura incompatível');
            throw new \InvalidArgumentException("XML fora do schema NF-e 4.00: {$message}");
        }
    }

    protected function validateSignature(DOMDocument $document, DOMXPath $xpath, DOMElement $infNFe): void
    {
        $signature = $xpath->query('//*[local-name()="Signature"]')->item(0);
        if (! $signature instanceof DOMElement) {
            throw new \InvalidArgumentException('Assinatura digital ausente ou incompleta.');
        }

        $signedInfo = $xpath->query('./*[local-name()="SignedInfo"]', $signature)->item(0);
        $signatureValue = $xpath->evaluate('string(./*[local-name()="SignatureValue"])', $signature);
        $certificate = $xpath->evaluate('string(.//*[local-name()="X509Certificate"])', $signature);
        $digest = $xpath->evaluate('string(.//*[local-name()="DigestValue"])', $signedInfo);

        if (! $signedInfo || ! $signatureValue || ! $certificate || ! $digest) {
            throw new \InvalidArgumentException('Assinatura digital ausente ou incompleta.');
        }

        $clone = $infNFe->cloneNode(true);
        $cloneDocument = new DOMDocument;
        $cloneDocument->appendChild($cloneDocument->importNode($clone, true));
        foreach ((new DOMXPath($cloneDocument))->query('//*[local-name()="Signature"]') as $node) {
            $node->parentNode?->removeChild($node);
        }

        if (! hash_equals($digest, base64_encode(hash('sha1', $cloneDocument->documentElement->C14N(), true)))) {
            throw new \InvalidArgumentException('O conteúdo da NF-e não corresponde ao digest assinado.');
        }

        $pem = "-----BEGIN CERTIFICATE-----\n".chunk_split(preg_replace('/\s+/', '', $certificate), 64, "\n")."-----END CERTIFICATE-----\n";
        if (openssl_verify($signedInfo->C14N(), base64_decode($signatureValue), $pem, OPENSSL_ALGO_SHA1) !== 1) {
            throw new \InvalidArgumentException('Assinatura digital da NF-e inválida.');
        }
    }

    protected function validateCurrentStatus(string $response): void
    {
        $document = $this->loadXml($response);
        $xpath = new DOMXPath($document);
        $status = trim($xpath->evaluate('string(//*[local-name()="retConsSitNFe"]/*[local-name()="cStat"])'));

        if ($status !== '100') {
            throw new \InvalidArgumentException("A situação atual da NF-e na SEFAZ não está autorizada (cStat {$status}).");
        }
    }

    protected function hasValidCheckDigit(string $key): bool
    {
        $sum = 0;
        $weight = 2;
        for ($index = 42; $index >= 0; $index--) {
            $sum += (int) $key[$index] * $weight;
            $weight = $weight === 9 ? 2 : $weight + 1;
        }
        $remainder = $sum % 11;
        $digit = $remainder < 2 ? 0 : 11 - $remainder;

        return $digit === (int) $key[43];
    }
}
