# Módulo de Suporte (Tickets)

## 1. Visão Geral

O módulo de Suporte gerencia tickets de atendimento ao cliente e suporte interno, com sistema de prioridades, categorias, SLA e histórico completo de interações.

### Funcionalidades Principais
- Criação de tickets (usuário ou cliente)
- Sistema de prioridades (baixa, normal, alta, urgente)
- Categorias customizáveis
- Atribuição de responsáveis
- SLA (Service Level Agreement)
- Histórico de interações
- Anexos de arquivos
- Status workflow
- Base de conhecimento (FAQ)

---

## 2. Regras de Negócio

### 2.1 Status do Ticket
1. **Aberto**: Ticket criado, aguardando atendimento
2. **Em Atendimento**: Atribuído a técnico
3. **Aguardando Cliente**: Necessita resposta do cliente
4. **Resolvido**: Solução implementada
5. **Fechado**: Cliente confirmou resolução
6. **Cancelado**: Cancelado pelo solicitante

### 2.2 Prioridades
- **Baixa**: Prazo 7 dias
- **Normal**: Prazo 3 dias
- **Alta**: Prazo 1 dia
- **Urgente**: Prazo 4 horas

### 2.3 SLA (Tempo de Resposta)
```php
$sla = match($priority) {
    'baixa' => 7 * 24 * 60, // 7 dias em minutos
    'normal' => 3 * 24 * 60,
    'alta' => 24 * 60,
    'urgente' => 4 * 60,
};

$dueDate = now()->addMinutes($sla);
```

---

## 3. Estrutura Técnica

### 3.1 Model: `SupportTicket`

```php
protected $fillable = [
    'ticket_number',
    'user_id',
    'assigned_to',
    'category_id',
    'title',
    'description',
    'priority',
    'status',
    'due_date',
    'resolved_at',
    'closed_at',
    'satisfaction_rating',
];
```

### 3.2 Model: `TicketReply`

```php
protected $fillable = [
    'support_ticket_id',
    'user_id',
    'message',
    'is_internal', // nota interna
    'attachments',
];
```

### 3.3 Livewire: `App\Livewire\Suporte\Tickets`

**Features**:
- Kanban board por status
- Lista com filtros
- Chat de interações
- Upload de anexos
- Atribuição rápida
- Mudança de status

---

## 4. Integrações

### 4.1 Assistente IA
Ferramenta `consultar_ticket`:
```php
$ticket = SupportTicket::find($ticketId);
return [
    'numero' => $ticket->ticket_number,
    'titulo' => $ticket->title,
    'status' => $ticket->status,
    'prioridade' => $ticket->priority,
    'historico' => $ticket->replies->map(fn($r) => [
        'autor' => $r->user->name,
        'mensagem' => $r->message,
        'data' => $r->created_at,
    ]),
];
```

### 4.2 Notificações
- Email ao criar ticket
- Email ao atribuir
- Email ao responder
- Alerta de SLA próximo

---

## 5. Exemplos

```php
$ticket = SupportTicket::create([
    'user_id' => auth()->id(),
    'title' => 'Erro ao emitir NF-e',
    'description' => 'Rejeição 539 ao transmitir',
    'priority' => 'alta',
    'status' => 'aberto',
    'due_date' => now()->addDay(),
]);

// Atribuir
$ticket->update([
    'assigned_to' => $technicianId,
    'status' => 'em_atendimento',
]);

// Responder
$ticket->replies()->create([
    'user_id' => $technicianId,
    'message' => 'Verificando CNPJ do cliente...',
]);

// Resolver
$ticket->update([
    'status' => 'resolvido',
    'resolved_at' => now(),
]);
```

---

**Caminho**: `app/Models/SupportTicket.php`, `app/Livewire/Suporte/Tickets.php`

