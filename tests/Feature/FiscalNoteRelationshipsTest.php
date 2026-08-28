<?php

use App\Enums\FiscalNoteEventType;
use App\Livewire\Fiscal\NFeGestao;
use App\Models\FiscalNote;
use App\Models\FiscalNoteEvent;
use App\Models\FiscalNoteItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Livewire;

it('defines the fiscal note item and event relationships', function () {
    $note = FiscalNote::factory()->create();

    expect($note->items())->toBeInstanceOf(HasMany::class)
        ->and($note->events())->toBeInstanceOf(HasMany::class);
});

it('persists fiscal note items and events through their relationships', function () {
    $user = User::factory()->create();
    $note = FiscalNote::factory()->create();

    $item = $note->items()->create([
        'item_number' => 1,
        'description' => 'Produto Teste',
        'quantity' => 1,
        'unit_price' => 100,
        'total' => 100,
    ]);

    $event = $note->events()->create([
        'event_type' => FiscalNoteEventType::Rejected,
        'description' => 'Rejeição de teste',
        'response_data' => ['code' => '999'],
        'created_by' => $user->id,
    ]);

    expect($item->fiscalNote->is($note))->toBeTrue()
        ->and($event->fiscalNote->is($note))->toBeTrue()
        ->and($event->createdBy->is($user))->toBeTrue()
        ->and($event->event_type)->toBe(FiscalNoteEventType::Rejected)
        ->and($event->response_data)->toBe(['code' => '999']);
});

it('loads the management component when a fiscal note has no events', function () {
    $note = FiscalNote::factory()->create();

    Livewire::test(NFeGestao::class, ['noteId' => $note->id])
        ->assertSet('fiscalNote.id', $note->id)
        ->call('openEventsModal')
        ->assertSee('Nenhum evento registrado.');
});

it('orders fiscal note events from newest to oldest', function () {
    $note = FiscalNote::factory()->create();

    $oldEvent = $note->events()->create([
        'event_type' => FiscalNoteEventType::Authorized,
        'description' => 'Evento antigo',
    ]);
    $oldEvent->forceFill(['created_at' => now()->subMinute()])->save();

    $recentEvent = $note->events()->create([
        'event_type' => FiscalNoteEventType::Cce,
        'description' => 'Evento recente',
    ]);
    $recentEvent->forceFill(['created_at' => now()])->save();

    Livewire::test(NFeGestao::class, ['noteId' => $note->id])
        ->call('openEventsModal')
        ->assertSeeInOrder(['Evento recente', 'Evento antigo']);
});

it('applies the configured delete behavior to fiscal note events', function () {
    $user = User::factory()->create();
    $note = FiscalNote::factory()->create();
    $event = $note->events()->create([
        'event_type' => FiscalNoteEventType::Authorized,
        'description' => 'Autorização de teste',
        'created_by' => $user->id,
    ]);
    $item = $note->items()->create([
        'item_number' => 1,
        'description' => 'Produto Teste',
        'quantity' => 1,
        'unit_price' => 10,
        'total' => 10,
    ]);

    $user->delete();

    expect($event->fresh()->created_by)->toBeNull();

    $note->delete();

    expect(FiscalNoteEvent::find($event->id))->toBeNull()
        ->and(FiscalNoteItem::find($item->id))->toBeNull();
});
