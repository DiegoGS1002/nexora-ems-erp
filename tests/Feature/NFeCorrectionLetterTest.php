<?php

use App\Enums\FiscalNoteEventType;
use App\Enums\FiscalNoteStatus;
use App\Models\FiscalNote;
use App\Models\FiscalNoteEvent;
use App\Services\NFeService;
use Illuminate\Support\Facades\Storage;

function cceService(string $response): object
{
    return new class($response) extends NFeService
    {
        public function __construct(private readonly string $response) {}

        protected function sendCce(string $accessKey, string $correction, int $sequence): string
        {
            return $this->response;
        }

        public function parseCce(string $response, string $accessKey, int $sequence): array
        {
            return $this->parseCceResponse($response, $accessKey, $sequence);
        }
    };
}

function cceResponse(
    string $accessKey,
    int $sequence = 1,
    string $eventCode = '135',
    string $eventType = '110110'
): string {
    return <<<XML
        <retEnvEvento xmlns="http://www.portalfiscal.inf.br/nfe" versao="1.00">
            <idLote>1</idLote>
            <tpAmb>2</tpAmb>
            <cOrgao>35</cOrgao>
            <cStat>128</cStat>
            <xMotivo>Lote de Evento Processado</xMotivo>
            <retEvento versao="1.00">
                <infEvento>
                    <tpAmb>2</tpAmb>
                    <verAplic>TESTE</verAplic>
                    <cOrgao>35</cOrgao>
                    <cStat>{$eventCode}</cStat>
                    <xMotivo>Evento registrado e vinculado a NF-e</xMotivo>
                    <chNFe>{$accessKey}</chNFe>
                    <tpEvento>{$eventType}</tpEvento>
                    <nSeqEvento>{$sequence}</nSeqEvento>
                    <dhRegEvento>2026-08-27T14:00:00-03:00</dhRegEvento>
                    <nProt>135260000000001</nProt>
                </infEvento>
            </retEvento>
        </retEnvEvento>
        XML;
}

function authorizedNoteForCce(): FiscalNote
{
    return FiscalNote::factory()->create([
        'status' => FiscalNoteStatus::Authorized,
        'access_key' => str_repeat('1', 44),
        'protocol' => '135260000000000',
    ]);
}

beforeEach(function () {
    config([
        'filesystems.disks.nfe-cce-test' => [
            'driver' => 'local',
            'root' => sys_get_temp_dir().'/nexora-nfe-cce-tests',
        ],
        'nfe.xml_disk' => 'nfe-cce-test',
        'nfe.xml_path' => 'nfe/xmls',
    ]);
});

it('registers a correction letter from the individual event response', function () {
    $note = authorizedNoteForCce();
    $result = cceService(cceResponse($note->access_key))->cartaCorrecao(
        $note,
        'Corrigir a descrição complementar do produto.',
    );

    expect($result['success'])->toBeTrue()
        ->and($result['registered'])->toBeTrue()
        ->and($result['code'])->toBe('135')
        ->and($result['sequence'])->toBe(1)
        ->and($result['protocol'])->toBe('135260000000001');

    $event = FiscalNoteEvent::where('fiscal_note_id', $note->id)->sole();
    expect($event->event_type)->toBe(FiscalNoteEventType::Cce)
        ->and($event->protocol)->toBe('135260000000001')
        ->and($event->response_data['sequence'])->toBe(1);

    Storage::disk('nfe-cce-test')->assertExists($result['response_path']);
});

it('increments the sequence from the last registered correction letter', function () {
    $note = authorizedNoteForCce();
    $note->events()->create([
        'event_type' => FiscalNoteEventType::Cce,
        'description' => 'Primeira correção registrada',
        'response_data' => ['sequence' => 1],
    ]);

    $result = cceService(cceResponse($note->access_key, 2))->cartaCorrecao(
        $note,
        'Segunda correção complementar válida.',
    );

    expect($result['sequence'])->toBe(2)
        ->and($note->events()->count())->toBe(2);
});

it('rejects ineligible notes and invalid correction text', function () {
    $draft = FiscalNote::factory()->create();

    expect(fn () => cceService('')->cartaCorrecao($draft, str_repeat('a', 15)))
        ->toThrow(InvalidArgumentException::class, 'Apenas notas autorizadas');

    $authorized = authorizedNoteForCce();

    expect(fn () => cceService('')->cartaCorrecao($authorized, 'texto curto'))
        ->toThrow(InvalidArgumentException::class, 'entre 15 e 1.000 caracteres');
});

it('rejects an individual event for another key sequence or event type', function (string $key, int $sequence, string $type) {
    $expectedKey = str_repeat('1', 44);

    expect(fn () => cceService('')->parseCce(
        cceResponse($key, $sequence, eventType: $type),
        $expectedKey,
        1,
    ))->toThrow(RuntimeException::class, 'não corresponde à chave, tipo ou sequência');
})->with([
    'key' => [str_repeat('2', 44), 1, '110110'],
    'sequence' => [str_repeat('1', 44), 2, '110110'],
    'type' => [str_repeat('1', 44), 1, '110111'],
]);
