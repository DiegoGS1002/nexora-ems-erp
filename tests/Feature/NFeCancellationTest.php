<?php

use App\Enums\FiscalNoteEventType;
use App\Enums\FiscalNoteStatus;
use App\Models\FiscalNote;
use App\Models\FiscalNoteEvent;
use App\Services\NFeService;

function cancellationService(string $response): object
{
    return new class($response) extends NFeService
    {
        public function __construct(private readonly string $response) {}

        protected function sendCancellation(string $accessKey, string $reason, string $protocol): string
        {
            return $this->response;
        }

        public function parseCancellation(string $response, string $accessKey): array
        {
            return $this->parseCancelResponse($response, $accessKey);
        }
    };
}

function cancellationResponse(string $accessKey, string $code = '135', string $type = '110111'): string
{
    return <<<XML
        <retEnvEvento xmlns="http://www.portalfiscal.inf.br/nfe" versao="1.00">
            <cStat>128</cStat>
            <xMotivo>Lote de Evento Processado</xMotivo>
            <retEvento versao="1.00">
                <infEvento>
                    <cStat>{$code}</cStat>
                    <xMotivo>Evento registrado e vinculado a NF-e</xMotivo>
                    <chNFe>{$accessKey}</chNFe>
                    <tpEvento>{$type}</tpEvento>
                    <nSeqEvento>1</nSeqEvento>
                    <dhRegEvento>2026-08-27T15:00:00-03:00</dhRegEvento>
                    <nProt>135260000000002</nProt>
                </infEvento>
            </retEvento>
        </retEnvEvento>
        XML;
}

beforeEach(function () {
    config([
        'filesystems.disks.nfe-cancel-test' => [
            'driver' => 'local',
            'root' => sys_get_temp_dir().'/nexora-nfe-cancel-tests',
        ],
        'nfe.xml_disk' => 'nfe-cancel-test',
        'nfe.xml_path' => 'nfe/xmls',
    ]);
});

it('returns the cancelled contract and persists cancellation only once', function () {
    $key = str_repeat('1', 44);
    $note = FiscalNote::factory()->create([
        'status' => FiscalNoteStatus::Authorized,
        'access_key' => $key,
        'protocol' => '135260000000001',
    ]);

    $result = cancellationService(cancellationResponse($key))->cancelar(
        $note,
        'Cancelamento solicitado por erro operacional.',
    );

    expect($result)->toHaveKey('cancelled')
        ->and($result)->not->toHaveKey('canceled')
        ->and($result['cancelled'])->toBeTrue()
        ->and($note->fresh()->status)->toBe(FiscalNoteStatus::Cancelled)
        ->and($note->fresh()->cancel_protocol)->toBe('135260000000002');

    $event = FiscalNoteEvent::where('fiscal_note_id', $note->id)->sole();
    expect($event->event_type)->toBe(FiscalNoteEventType::Cancelled)
        ->and($event->protocol)->toBe('135260000000002');
});

it('uses the individual cancellation result instead of the batch result', function () {
    $key = str_repeat('1', 44);
    $result = cancellationService('')->parseCancellation(cancellationResponse($key), $key);

    expect($result['code'])->toBe('135')
        ->and($result['cancelled'])->toBeTrue();
});

it('rejects cancellation responses for another key or event type', function (string $key, string $type) {
    expect(fn () => cancellationService('')->parseCancellation(
        cancellationResponse($key, type: $type),
        str_repeat('1', 44),
    ))->toThrow(RuntimeException::class, 'não corresponde à chave ou ao tipo');
})->with([
    'key' => [str_repeat('2', 44), '110111'],
    'type' => [str_repeat('1', 44), '110110'],
]);
