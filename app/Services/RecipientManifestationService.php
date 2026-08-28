<?php

namespace App\Services;

use App\Enums\RecipientManifestationType;
use App\Models\DfeDocument;
use App\Models\DfeManifestation;
use DOMDocument;
use DOMXPath;

class RecipientManifestationService
{
    public function __construct(protected NFeService $nfeService) {}

    public function manifest(DfeDocument $document, RecipientManifestationType $type, ?string $justification = null): DfeManifestation
    {
        if (! $document->access_key) {
            throw new \InvalidArgumentException('Documento DF-e sem chave de acesso.');
        }

        if ($type === RecipientManifestationType::OperationNotPerformed && mb_strlen(trim((string) $justification)) < 15) {
            throw new \InvalidArgumentException('A operação não realizada exige justificativa com ao menos 15 caracteres.');
        }

        $manifestation = DfeManifestation::firstOrCreate(
            ['dfe_document_id' => $document->id, 'type' => $type->value],
            ['sequence' => 1, 'justification' => $justification, 'status' => 'pending']
        );

        if ($manifestation->status === 'registered') {
            return $manifestation;
        }

        $response = $this->nfeService->manifestRecipient(
            $document->access_key,
            $type->value,
            (string) $justification,
            $manifestation->sequence
        );
        $result = $this->parseResponse($response);

        $manifestation->update([
            'justification' => $justification,
            'status' => in_array($result['code'], ['135', '136'], true) ? 'registered' : 'rejected',
            'protocol' => $result['protocol'],
            'sefaz_code' => $result['code'],
            'sefaz_message' => $result['message'],
            'response_xml' => $response,
            'registered_at' => in_array($result['code'], ['135', '136'], true) ? now() : null,
        ]);

        return $manifestation->fresh();
    }

    protected function parseResponse(string $response): array
    {
        $document = new DOMDocument;
        if (! $document->loadXML($response, LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_COMPACT)) {
            throw new \RuntimeException('Resposta inválida da manifestação do destinatário.');
        }

        $xpath = new DOMXPath($document);
        $event = $xpath->query('//*[local-name()="retEvento"]/*[local-name()="infEvento"]')->item(0);

        if (! $event) {
            throw new \RuntimeException('Resposta da manifestação sem retorno de evento.');
        }

        return [
            'code' => trim($xpath->evaluate('string(./*[local-name()="cStat"])', $event)),
            'message' => trim($xpath->evaluate('string(./*[local-name()="xMotivo"])', $event)),
            'protocol' => trim($xpath->evaluate('string(./*[local-name()="nProt"])', $event)) ?: null,
        ];
    }
}
