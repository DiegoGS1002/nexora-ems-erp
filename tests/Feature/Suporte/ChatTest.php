<?php

use App\Enums\PrioridadeTicketSuporte;
use App\Enums\StatusTicketSuporte;
use App\Livewire\Suporte\Chat;
use App\Models\MensagemSuporte;
use App\Models\TicketSuporte;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user  = User::factory()->create(['is_admin' => false, 'last_login_at' => now()]);
    $this->admin = User::factory()->create(['is_admin' => true,  'last_login_at' => now()]);
});

// ── Acesso à página ──────────────────────────────────────────

it('usuario autenticado acessa a pagina de chat', function () {
    $this->actingAs($this->user);

    $this->get(route('suporte.chat'))->assertOk();
});

it('usuario nao autenticado e redirecionado para login', function () {
    $this->get(route('suporte.chat'))->assertRedirect(route('login'));
});

it('renderiza o componente chat de suporte', function () {
    $this->actingAs($this->user);

    Livewire::test(Chat::class)
        ->assertSee('Chat de Suporte')
        ->assertSee('Novo Ticket');
});

// ── Criação de ticket ────────────────────────────────────────

it('usuario pode criar um novo ticket de suporte', function () {
    $this->actingAs($this->user);

    Livewire::test(Chat::class)
        ->call('abrirNovoTicket')
        ->assertSet('showNovoTicket', true)
        ->set('novoTicket.assunto', 'Problema ao acessar o módulo financeiro')
        ->set('novoTicket.prioridade', PrioridadeTicketSuporte::Alta->value)
        ->set('novoTicket.mensagem', 'Ao tentar acessar o módulo financeiro, recebo um erro 403.')
        ->call('criarTicket')
        ->assertSet('showNovoTicket', false);

    expect(TicketSuporte::where('assunto', 'Problema ao acessar o módulo financeiro')->exists())->toBeTrue();
    expect(MensagemSuporte::where('conteudo', 'Ao tentar acessar o módulo financeiro, recebo um erro 403.')->exists())->toBeTrue();
});

it('valida campos obrigatorios ao criar ticket', function () {
    $this->actingAs($this->user);

    Livewire::test(Chat::class)
        ->call('abrirNovoTicket')
        ->call('criarTicket')
        ->assertHasErrors(['novoTicket.assunto', 'novoTicket.mensagem']);
});

it('valida tamanho minimo do assunto', function () {
    $this->actingAs($this->user);

    Livewire::test(Chat::class)
        ->call('abrirNovoTicket')
        ->set('novoTicket.assunto', 'Bug')
        ->call('criarTicket')
        ->assertHasErrors(['novoTicket.assunto']);
});

it('valida tamanho minimo da mensagem inicial', function () {
    $this->actingAs($this->user);

    Livewire::test(Chat::class)
        ->call('abrirNovoTicket')
        ->set('novoTicket.assunto', 'Assunto válido para o ticket')
        ->set('novoTicket.mensagem', 'Curto')
        ->call('criarTicket')
        ->assertHasErrors(['novoTicket.mensagem']);
});

it('ticket criado tem status aberto por padrao', function () {
    $this->actingAs($this->user);

    Livewire::test(Chat::class)
        ->call('abrirNovoTicket')
        ->set('novoTicket.assunto', 'Ticket com status padrão')
        ->set('novoTicket.prioridade', PrioridadeTicketSuporte::Baixa->value)
        ->set('novoTicket.mensagem', 'Esta é a mensagem inicial do ticket de teste.')
        ->call('criarTicket');

    $ticket = TicketSuporte::where('assunto', 'Ticket com status padrão')->first();

    expect($ticket->status)->toBe(StatusTicketSuporte::Aberto);
    expect($ticket->user_id)->toBe($this->user->id);
});

// ── Envio de mensagem ────────────────────────────────────────

it('usuario pode enviar mensagem em ticket aberto', function () {
    $this->actingAs($this->user);

    $ticket = TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Ticket para mensagem',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    Livewire::test(Chat::class)
        ->call('selecionarTicket', $ticket->id)
        ->set('novaMensagemTexto', 'Esta é uma resposta ao ticket de suporte.')
        ->call('enviarMensagem')
        ->assertSet('novaMensagemTexto', '');

    expect(MensagemSuporte::where('ticket_id', $ticket->id)->where('conteudo', 'Esta é uma resposta ao ticket de suporte.')->exists())->toBeTrue();
});

it('valida mensagem vazia ao enviar', function () {
    $this->actingAs($this->user);

    $ticket = TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Ticket para validação',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    Livewire::test(Chat::class)
        ->call('selecionarTicket', $ticket->id)
        ->set('novaMensagemTexto', '')
        ->call('enviarMensagem')
        ->assertHasErrors(['novaMensagemTexto']);
});

it('nao permite enviar mensagem em ticket fechado', function () {
    $this->actingAs($this->user);

    $ticket = TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Ticket fechado',
        'status'     => StatusTicketSuporte::Fechado->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    Livewire::test(Chat::class)
        ->call('selecionarTicket', $ticket->id)
        ->set('novaMensagemTexto', 'Tentando enviar em ticket fechado.')
        ->call('enviarMensagem');

    expect(MensagemSuporte::where('ticket_id', $ticket->id)->count())->toBe(0);
});

// ── Admin — Atualização de status ────────────────────────────

it('admin pode alterar o status do ticket', function () {
    $this->actingAs($this->admin);

    $ticket = TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Ticket para mudança de status',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    Livewire::test(Chat::class)
        ->call('selecionarTicket', $ticket->id)
        ->call('atualizarStatus', StatusTicketSuporte::Resolvido->value);

    expect($ticket->fresh()->status)->toBe(StatusTicketSuporte::Resolvido);
    expect($ticket->fresh()->fechado_em)->not->toBeNull();
});

it('usuario comum nao pode alterar o status do ticket', function () {
    $this->actingAs($this->user);

    $ticket = TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Ticket sem permissão de mudança',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    Livewire::test(Chat::class)
        ->call('selecionarTicket', $ticket->id)
        ->call('atualizarStatus', StatusTicketSuporte::Fechado->value);

    expect($ticket->fresh()->status)->toBe(StatusTicketSuporte::Aberto);
});

it('admin responde e ticket muda para em andamento', function () {
    $this->actingAs($this->admin);

    $ticket = TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Ticket para resposta do suporte',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Alta->value,
    ]);

    Livewire::test(Chat::class)
        ->call('selecionarTicket', $ticket->id)
        ->set('novaMensagemTexto', 'Olá, estamos analisando o seu problema.')
        ->call('enviarMensagem');

    $ticket->refresh();
    expect($ticket->status)->toBe(StatusTicketSuporte::EmAndamento);
});

// ── Filtragem e busca ────────────────────────────────────────

it('usuario vê somente seus proprios tickets', function () {
    $outroUser = User::factory()->create(['is_admin' => false]);

    TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Meu ticket pessoal',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    TicketSuporte::create([
        'user_id'    => $outroUser->id,
        'assunto'    => 'Ticket de outro usuário',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    $this->actingAs($this->user);

    Livewire::test(Chat::class)
        ->assertSee('Meu ticket pessoal')
        ->assertDontSee('Ticket de outro usuário');
});

it('admin ve todos os tickets', function () {
    $outroUser = User::factory()->create(['is_admin' => false]);

    TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Ticket do usuario',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    TicketSuporte::create([
        'user_id'    => $outroUser->id,
        'assunto'    => 'Ticket de outro',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    $this->actingAs($this->admin);

    Livewire::test(Chat::class)
        ->assertSee('Ticket do usuario')
        ->assertSee('Ticket de outro');
});

it('filtra tickets por status', function () {
    $this->actingAs($this->user);

    TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Ticket aberto',
        'status'     => StatusTicketSuporte::Aberto->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    TicketSuporte::create([
        'user_id'    => $this->user->id,
        'assunto'    => 'Ticket resolvido',
        'status'     => StatusTicketSuporte::Resolvido->value,
        'prioridade' => PrioridadeTicketSuporte::Media->value,
    ]);

    Livewire::test(Chat::class)
        ->set('filtroStatus', StatusTicketSuporte::Aberto->value)
        ->assertSee('Ticket aberto')
        ->assertDontSee('Ticket resolvido');
});


