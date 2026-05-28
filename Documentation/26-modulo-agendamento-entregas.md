# Módulo de Agendamento de Entregas

## 1. Visão Geral

Gerencia o agendamento de entregas de pedidos, otimizando rotas, vinculando motoristas e veículos, controlando status de entrega.

### Funcionalidades
- Agendamento por data/hora
- Vínculo com pedido de venda
- Atribuição de motorista e veículo
- Status de entrega
- Comprovante de entrega
- Assinatura digital

---

## 2. Estrutura

### Model: `SchedulingOfDeliveries`
```php
protected $fillable = [
    'sales_order_id',
    'vehicle_id',
    'driver_id',
    'scheduled_date',
    'scheduled_time',
    'delivery_address',
    'status', // scheduled, in_transit, delivered, failed
    'delivered_at',
    'proof_of_delivery',
    'recipient_name',
    'recipient_signature',
    'notes',
];
```

### Fluxo
```
1. Pedido aprovado
2. Criar agendamento
3. Atribuir motorista e veículo
4. Status: scheduled
5. Motorista inicia entrega: in_transit
6. Chega no cliente: delivered
7. Upload comprovante
8. Pedido atualiza status: Entregue
```

---

**Caminho**: `app/Models/SchedulingOfDeliveries.php`, `app/Livewire/Logistica/AgendamentoEntregas.php`

