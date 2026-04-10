<?php

use App\Enums\PayableStatus;
use App\Livewire\Financeiro\ContasPagar;
use App\Models\AccountPayable;
use App\Models\PlanOfAccount;
use App\Models\Supplier;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

/* ──────────────────────────────────────────────────────────
   PAGE RENDERING
 ──────────────────────────────────────────────────────────*/

it('renders the contas a pagar page successfully', function () {
    $this->get(route('contas_pagar.index'))
        ->assertOk()
        ->assertSeeLivewire(ContasPagar::class);
});

it('shows the page title and new account button', function () {
    Livewire::test(ContasPagar::class)
        ->assertSee('Contas a Pagar')
        ->assertSee('Nova Conta');
});

/* ──────────────────────────────────────────────────────────
   LISTING
 ──────────────────────────────────────────────────────────*/

it('lists existing payable accounts', function () {
    AccountPayable::create([
        'description_title' => 'Aluguel Escritório',
        'amount'            => 3500.00,
        'due_date_at'       => today()->addDays(5),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->assertSee('Aluguel Escritório');
});

it('shows empty state when no accounts exist', function () {
    Livewire::test(ContasPagar::class)
        ->assertSee('Nenhuma conta encontrada');
});

/* ──────────────────────────────────────────────────────────
   FILTER TABS
 ──────────────────────────────────────────────────────────*/

it('filters accounts by pending status', function () {
    AccountPayable::create([
        'description_title' => 'Conta Pendente',
        'amount'            => 500.00,
        'due_date_at'       => today()->addDays(3),
        'status'            => PayableStatus::Pending->value,
    ]);

    AccountPayable::create([
        'description_title' => 'Conta Paga',
        'amount'            => 200.00,
        'due_date_at'       => today()->subDays(2),
        'status'            => PayableStatus::Paid->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->set('filterStatus', PayableStatus::Pending->value)
        ->assertSee('Conta Pendente')
        ->assertDontSee('Conta Paga');
});

it('filters accounts by search term', function () {
    AccountPayable::create([
        'description_title' => 'Fatura de Energia',
        'amount'            => 350.00,
        'due_date_at'       => today()->addDays(10),
        'status'            => PayableStatus::Pending->value,
    ]);

    AccountPayable::create([
        'description_title' => 'Aluguel do Galpão',
        'amount'            => 5000.00,
        'due_date_at'       => today()->addDays(10),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->set('search', 'Energia')
        ->assertSee('Fatura de Energia')
        ->assertDontSee('Aluguel do Galpão');
});

/* ──────────────────────────────────────────────────────────
   CREATE
 ──────────────────────────────────────────────────────────*/

it('opens the create modal', function () {
    Livewire::test(ContasPagar::class)
        ->call('openCreate')
        ->assertSet('showModal', true)
        ->assertSet('isEditing', false);
});

it('creates a new payable account with valid data', function () {
    Livewire::test(ContasPagar::class)
        ->call('openCreate')
        ->set('form.description_title', 'Conta de Água')
        ->set('form.amount', '450.00')
        ->set('form.due_date_at', today()->addDays(5)->format('Y-m-d'))
        ->set('form.status', PayableStatus::Pending->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    expect(AccountPayable::where('description_title', 'Conta de Água')->exists())->toBeTrue();
});

it('validates required fields when creating', function () {
    Livewire::test(ContasPagar::class)
        ->call('openCreate')
        ->call('save')
        ->assertHasErrors(['form.description_title', 'form.amount', 'form.due_date_at']);
});

it('validates amount must be greater than zero', function () {
    Livewire::test(ContasPagar::class)
        ->call('openCreate')
        ->set('form.description_title', 'Teste')
        ->set('form.amount', '0')
        ->set('form.due_date_at', today()->format('Y-m-d'))
        ->call('save')
        ->assertHasErrors(['form.amount']);
});

/* ──────────────────────────────────────────────────────────
   EDIT
 ──────────────────────────────────────────────────────────*/

it('opens the edit modal with existing data pre-filled', function () {
    $account = AccountPayable::create([
        'description_title' => 'Seguro Predial',
        'amount'            => 1200.00,
        'due_date_at'       => today()->addDays(15),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->call('openEdit', $account->id)
        ->assertSet('showModal', true)
        ->assertSet('isEditing', true)
        ->assertSet('form.description_title', 'Seguro Predial');
});

it('updates an existing payable account', function () {
    $account = AccountPayable::create([
        'description_title' => 'Internet',
        'amount'            => 200.00,
        'due_date_at'       => today()->addDays(10),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->call('openEdit', $account->id)
        ->set('form.description_title', 'Internet e Telefone')
        ->set('form.amount', '350.00')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    expect(AccountPayable::find($account->id)->description_title)->toBe('Internet e Telefone');
});

/* ──────────────────────────────────────────────────────────
   PAYMENT (BAIXA)
 ──────────────────────────────────────────────────────────*/

it('opens the payment modal', function () {
    $account = AccountPayable::create([
        'description_title' => 'Fornecedor XYZ',
        'amount'            => 800.00,
        'due_date_at'       => today(),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->call('openPayment', $account->id)
        ->assertSet('showPaymentModal', true)
        ->assertSet('payingId', $account->id);
});

it('registers a payment successfully', function () {
    $account = AccountPayable::create([
        'description_title' => 'Boleto Fornecedor',
        'amount'            => 1500.00,
        'due_date_at'       => today(),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->call('openPayment', $account->id)
        ->set('pay_date', today()->format('Y-m-d'))
        ->set('pay_amount', '1500.00')
        ->call('registerPayment')
        ->assertHasNoErrors()
        ->assertSet('showPaymentModal', false);

    $account->refresh();
    expect($account->status)->toBe(PayableStatus::Paid);
    expect((float) $account->paid_amount)->toBe(1500.0);
});

it('validates required fields when registering payment', function () {
    $account = AccountPayable::create([
        'description_title' => 'Conta Teste',
        'amount'            => 300.00,
        'due_date_at'       => today(),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->call('openPayment', $account->id)
        ->call('registerPayment')
        ->assertHasErrors(['pay_date', 'pay_amount']);
});

/* ──────────────────────────────────────────────────────────
   RESCHEDULE
 ──────────────────────────────────────────────────────────*/

it('reschedules a payable account to a new due date', function () {
    $account = AccountPayable::create([
        'description_title' => 'IPTU',
        'amount'            => 2400.00,
        'due_date_at'       => today()->addDays(2),
        'status'            => PayableStatus::Pending->value,
    ]);

    $newDate = today()->addDays(30)->format('Y-m-d');

    Livewire::test(ContasPagar::class)
        ->call('openReschedule', $account->id)
        ->set('reschedule_date', $newDate)
        ->call('reschedule')
        ->assertHasNoErrors()
        ->assertSet('showRescheduleModal', false);

    expect(AccountPayable::find($account->id)->due_date_at->format('Y-m-d'))->toBe($newDate);
});

/* ──────────────────────────────────────────────────────────
   DELETE
 ──────────────────────────────────────────────────────────*/

it('opens the delete confirmation modal', function () {
    $account = AccountPayable::create([
        'description_title' => 'Conta para deletar',
        'amount'            => 100.00,
        'due_date_at'       => today(),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->call('confirmDelete', $account->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deletingId', $account->id);
});

it('deletes a payable account', function () {
    $account = AccountPayable::create([
        'description_title' => 'Conta a Deletar',
        'amount'            => 100.00,
        'due_date_at'       => today(),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->call('confirmDelete', $account->id)
        ->call('delete')
        ->assertSet('showDeleteModal', false);

    expect(AccountPayable::find($account->id))->toBeNull();
});

/* ──────────────────────────────────────────────────────────
   CANCEL ACCOUNT
 ──────────────────────────────────────────────────────────*/

it('cancels a payable account', function () {
    $account = AccountPayable::create([
        'description_title' => 'Conta a Cancelar',
        'amount'            => 450.00,
        'due_date_at'       => today()->addDays(5),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class)
        ->call('cancelAccount', $account->id)
        ->assertHasNoErrors();

    expect(AccountPayable::find($account->id)->status)->toBe(PayableStatus::Cancelled);
});

/* ──────────────────────────────────────────────────────────
   SERVICE — overdue sync
 ──────────────────────────────────────────────────────────*/

it('auto-syncs overdue status on page boot', function () {
    $account = AccountPayable::create([
        'description_title' => 'Conta Vencida',
        'amount'            => 600.00,
        'due_date_at'       => today()->subDays(3),
        'status'            => PayableStatus::Pending->value,
    ]);

    Livewire::test(ContasPagar::class);

    expect(AccountPayable::find($account->id)->status)->toBe(PayableStatus::Overdue);
});

