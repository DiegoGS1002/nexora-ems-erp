# Módulo de Notificações

## 1. Visão Geral

Sistema de notificações em tempo real para alertar usuários sobre eventos importantes: pedidos, vencimentos, estoque baixo, etc.

### Funcionalidades
- Notificações em tempo real
- Bell icon com contador
- Marcar como lida
- Tipos: info, success, warning, error
- Persistência em banco
- Email opcional
- Push notifications (PWA)

---

## 2. Estrutura

### Model: `Notification` (Laravel padrão)
```php
// Usar tabela notifications do Laravel
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->morphs('notifiable');
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
```

### Criação de Notificação
```php
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    protected $product;
    
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
    
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->product->stock,
            'min_stock' => $this->product->stock_min,
            'message' => "Estoque baixo: {$this->product->name}",
        ];
    }
}

// Enviar
$user->notify(new LowStockNotification($product));
```

---

## 3. Livewire: Bell Icon
```php
class NotificationsBell extends Component
{
    public $unreadCount = 0;
    
    public function mount()
    {
        $this->unreadCount = auth()->user()
            ->unreadNotifications()
            ->count();
    }
    
    #[On('notification-sent')]
    public function refreshCount()
    {
        $this->unreadCount = auth()->user()
            ->unreadNotifications()
            ->count();
    }
    
    public function markAsRead($notificationId)
    {
        auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);
            
        $this->refreshCount();
    }
}
```

---

## 4. Tipos de Notificação

### Estoque Baixo
```php
if ($product->stock < $product->stock_min) {
    Notification::send(
        User::where('is_admin', true)->get(),
        new LowStockNotification($product)
    );
}
```

### Pedido Aprovado
```php
$order->client->user->notify(
    new OrderApprovedNotification($order)
);
```

### Conta Vencida
```php
$account->client->user->notify(
    new OverdueAccountNotification($account)
);
```

---

**Caminho**: `app/Notifications/`, `app/Livewire/NotificationsBell.php`

