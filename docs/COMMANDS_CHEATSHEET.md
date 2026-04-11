# 🚀 Guia Rápido - Comandos Úteis

## 📦 Comandos Docker

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f app

# Acessar bash do container
docker-compose exec app bash

# Reiniciar containers
docker-compose restart
```

---

## 🗄️ Comandos de Banco de Dados

```bash
# Executar todas as migrations
docker-compose exec app php artisan migrate

# Executar migration específica
docker-compose exec app php artisan migrate --path=database/migrations/2026_04_11_000001_update_sales_orders_add_full_fields.php

# Reverter última migration
docker-compose exec app php artisan migrate:rollback

# Reverter todas e executar novamente
docker-compose exec app php artisan migrate:fresh

# Ver status das migrations
docker-compose exec app php artisan migrate:status

# Executar seeders
docker-compose exec app php artisan db:seed --class=CarrierSeeder
docker-compose exec app php artisan db:seed --class=PriceTableSeeder
```

---

## 🔍 Comandos de Verificação

```bash
# Listar todas as rotas
docker-compose exec app php artisan route:list

# Listar rotas de sales-orders
docker-compose exec app php artisan route:list --name=sales-orders

# Ver configuração do banco
docker-compose exec app php artisan config:show database

# Limpar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Otimizar aplicação
docker-compose exec app php artisan optimize
```

---

## 🧪 Comandos de Teste

```bash
# Executar todos os testes
docker-compose exec app php artisan test

# Executar testes específicos
docker-compose exec app php artisan test --filter=SalesOrderTest

# Tinker (console interativo)
docker-compose exec app php artisan tinker
```

---

## 📊 Consultas Úteis no Tinker

```php
// Acessar tinker
docker-compose exec app php artisan tinker

// Ver total de pedidos
>>> \App\Models\SalesOrder::count()

// Ver pedidos de hoje
>>> \App\Models\SalesOrder::whereDate('order_date', today())->count()

// Ver último pedido criado
>>> \App\Models\SalesOrder::latest()->first()

// Ver pedidos com itens
>>> \App\Models\SalesOrder::with('items')->get()

// Ver transportadoras
>>> \App\Models\Carrier::all()

// Ver tabelas de preço
>>> \App\Models\PriceTable::all()

// Criar pedido de teste
>>> $service = new \App\Services\SalesOrderService();
>>> $order = $service->createOrder([
    'client_id' => 'uuid-cliente',
    'items' => [['product_id' => 'uuid-produto', 'quantity' => 1, 'unit_price' => 100]]
]);

// Ver logs de um pedido
>>> \App\Models\SalesOrder::find(1)->logs

// Calcular totais
>>> $order = \App\Models\SalesOrder::find(1);
>>> $order->calculateTotals();
>>> $order->save();
```

---

## 🔧 Comandos de Manutenção

```bash
# Ver versão do Laravel
docker-compose exec app php artisan --version

# Ver lista de comandos disponíveis
docker-compose exec app php artisan list

# Gerar chave de aplicação (se necessário)
docker-compose exec app php artisan key:generate

# Criar link simbólico do storage
docker-compose exec app php artisan storage:link

# Ver informações da aplicação
docker-compose exec app php artisan about
```

---

## 📝 Comandos de Geração de Código

```bash
# Criar novo controller
docker-compose exec app php artisan make:controller NomeController

# Criar novo model
docker-compose exec app php artisan make:model NomeModel

# Criar migration
docker-compose exec app php artisan make:migration create_nome_table

# Criar seeder
docker-compose exec app php artisan make:seeder NomeSeeder

# Criar request
docker-compose exec app php artisan make:request NomeRequest

# Criar observer
docker-compose exec app php artisan make:observer NomeObserver
```

---

## 🌐 Testar API com cURL

```bash
# Listar pedidos
curl -X GET http://localhost:8000/api/sales-orders

# Criar pedido
curl -X POST http://localhost:8000/api/sales-orders \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": "uuid-cliente",
    "items": [{
      "product_id": "uuid-produto",
      "quantity": 5,
      "unit_price": 100.00
    }]
  }'

# Ver pedido específico
curl -X GET http://localhost:8000/api/sales-orders/1

# Aprovar pedido
curl -X POST http://localhost:8000/api/sales-orders/1/approve \
  -H "Content-Type: application/json" \
  -d '{"reason": "Aprovado"}'

# Ver estatísticas
curl -X GET "http://localhost:8000/api/sales-orders/statistics?date_from=2026-04-01"

# Calcular totais
curl -X POST http://localhost:8000/api/sales-orders/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "items": [{
      "quantity": 10,
      "unit_price": 100.00,
      "discount_percent": 5
    }],
    "is_fiscal": false
  }'
```

---

## 🔍 Consultas SQL Diretas

```bash
# Acessar MySQL
docker-compose exec db mysql -u root -proot nexora

# Consultas úteis
SELECT * FROM sales_orders ORDER BY created_at DESC LIMIT 10;
SELECT * FROM sales_order_items WHERE sales_order_id = 1;
SELECT * FROM sales_order_logs ORDER BY created_at DESC LIMIT 20;
SELECT status, COUNT(*) FROM sales_orders GROUP BY status;
SELECT SUM(total_amount) FROM sales_orders WHERE status = 'invoiced';
```

---

## 📋 Atalhos Úteis

```bash
# Alias para facilitar
alias dce='docker-compose exec app'
alias dce-artisan='docker-compose exec app php artisan'

# Uso:
dce-artisan migrate
dce-artisan tinker
dce bash
```

---

## 🚨 Troubleshooting

```bash
# Erro de permissão
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

# Limpar tudo
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app composer dump-autoload

# Recriar banco (CUIDADO!)
docker-compose exec app php artisan migrate:fresh --seed

# Ver erros detalhados
docker-compose exec app tail -f storage/logs/laravel.log

# Verificar conexão com banco
docker-compose exec app php artisan db:show
```

---

## 📊 Monitoramento

```bash
# Ver uso de recursos
docker stats

# Ver processos no container
docker-compose exec app ps aux

# Ver espaço em disco
docker-compose exec app df -h

# Ver logs em tempo real
docker-compose logs -f --tail=100 app
```

---

## 🎯 Comandos Específicos do Módulo

```bash
# Ver todas as sales orders
docker-compose exec app php artisan tinker
>>> \App\Models\SalesOrder::count()

# Listar transportadoras
>>> \App\Models\Carrier::all(['id', 'name'])

# Listar tabelas de preço
>>> \App\Models\PriceTable::all(['id', 'name', 'code'])

# Ver pedidos que precisam aprovação
>>> \App\Models\SalesOrder::where('needs_approval', true)->count()

# Ver pedidos por status
>>> \App\Models\SalesOrder::groupBy('status')->selectRaw('status, count(*) as total')->get()

# Reprocessar totais de um pedido
>>> $order = \App\Models\SalesOrder::find(1);
>>> $order->calculateTotals();
>>> $order->save();
```

---

## 📚 Documentação Rápida

```bash
# Ver modelo
cat app/Models/SalesOrder.php | grep 'function'

# Ver rotas
cat routes/api.php | grep sales-orders

# Ver migrations
ls -la database/migrations/ | grep sales

# Ver seeders
ls -la database/seeders/
```

---

## ✅ Checklist Diário

```bash
# 1. Verificar se containers estão rodando
docker-compose ps

# 2. Ver logs recentes
docker-compose logs --tail=50 app

# 3. Limpar cache (se necessário)
docker-compose exec app php artisan cache:clear

# 4. Verificar status do banco
docker-compose exec app php artisan migrate:status

# 5. Executar testes (se houver)
docker-compose exec app php artisan test
```

---

**💡 Dica:** Salve este arquivo e consulte sempre que precisar!

**Versão:** 1.0  
**Data:** 10 de Abril de 2026

