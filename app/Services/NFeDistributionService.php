<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DfeDistributionState;
use App\Models\DfeDocument;
use App\Models\FiscalInvoiceIn;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NFeDistributionService
{
    public function __construct(
        protected NFeService $nfeService,
        protected ?FiscalEntryReversalService $reversalService = null
    ) {}

    public function synchronize(Company $company, string $environment): int
    {
        return DB::transaction(function () use ($company, $environment): int {
            $state = DfeDistributionState::firstOrCreate(
                ['company_id' => $company->id, 'environment' => $environment],
                ['last_nsu' => 0, 'max_nsu' => 0]
            );
            $state = DfeDistributionState::where('id', $state->id)->lockForUpdate()->firstOrFail();

            if ($state->next_query_at?->isFuture()) {
                return 0;
            }

            $response = $this->nfeService->distributeDfe((int) $state->last_nsu);
            $result = $this->parseResponse($response);
            $processed = 0;

            foreach ($result['documents'] as $document) {
                $this->storeDocument($company, $environment, $document);
                $processed++;
            }

            $state->update([
                'last_nsu' => max((int) $state->last_nsu, $result['last_nsu']),
                'max_nsu' => $result['max_nsu'],
                'next_query_at' => $result['last_nsu'] < $result['max_nsu'] ? now() : now()->addHour(),
            ]);

            return $processed;
        });
    }

    protected function parseResponse(string $response): array
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadXML($response, LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            throw new \RuntimeException('Resposta inválida do serviço de Distribuição DF-e.');
        }

        $xpath = new DOMXPath($document);
        $node = $xpath->query('//*[local-name()="retDistDFeInt"]')->item(0);

        if (! $node) {
            throw new \RuntimeException('Resposta sem o grupo retDistDFeInt.');
        }

        $code = trim($xpath->evaluate('string(./*[local-name()="cStat"])', $node));
        if (! in_array($code, ['137', '138'], true)) {
            $reason = trim($xpath->evaluate('string(./*[local-name()="xMotivo"])', $node));
            throw new \RuntimeException("Distribuição DF-e rejeitada ({$code}): {$reason}");
        }

        $documents = [];
        foreach ($xpath->query('.//*[local-name()="docZip"]', $node) as $docZip) {
            $xml = gzdecode(base64_decode(trim($docZip->textContent), true) ?: '');
            if ($xml === false) {
                throw new \RuntimeException('Documento DF-e compactado inválido.');
            }

            $schema = $docZip->attributes?->getNamedItem('schema')?->nodeValue ?? '';
            $documents[] = [
                'nsu' => (int) ($docZip->attributes?->getNamedItem('NSU')?->nodeValue ?? 0),
                'schema' => $schema,
                'document_type' => str_starts_with($schema, 'procEvento') ? 'event' : (str_starts_with($schema, 'res') ? 'summary' : 'nfe'),
                'access_key' => preg_match('/\b\d{44}\b/', $xml, $matches) ? $matches[0] : null,
                'xml' => $xml,
            ];
        }

        return [
            'last_nsu' => (int) $xpath->evaluate('string(./*[local-name()="ultNSU"])', $node),
            'max_nsu' => (int) $xpath->evaluate('string(./*[local-name()="maxNSU"])', $node),
            'documents' => $documents,
        ];
    }

    protected function storeDocument(Company $company, string $environment, array $document): void
    {
        $path = "fiscal/dfe/{$company->id}/{$environment}/{$document['nsu']}.xml";
        Storage::disk(config('nfe.xml_disk'))->put($path, $document['xml']);

        DfeDocument::updateOrCreate(
            ['company_id' => $company->id, 'environment' => $environment, 'nsu' => $document['nsu']],
            [
                'access_key' => $document['access_key'],
                'schema' => $document['schema'],
                'document_type' => $document['document_type'],
                'status' => 'received',
                'xml_path' => $path,
                'raw_xml' => $document['xml'],
                'processed_at' => now(),
            ]
        );

        if ($document['document_type'] === 'event' && str_contains($document['xml'], '<tpEvento>110111</tpEvento>')) {
            $invoice = FiscalInvoiceIn::where('access_key', $document['access_key'])->first();
            $user = $company->users()->where('is_active', true)->first();
            preg_match('/<nProt>([^<]+)<\/nProt>/', $document['xml'], $protocol);

            if ($invoice && $user && $this->reversalService) {
                $this->reversalService->reverse($invoice, $user, $protocol[1] ?? null);
            }
        }
    }
}
