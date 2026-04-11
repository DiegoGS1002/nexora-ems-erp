# Módulo de Agendamento de Entregas

## ✅ Implementação Completa

### 📁 Arquivos Criados

#### Enums
- `app/Enums/DeliveryScheduleStatus.php` - Status do agendamento (Pendente, Agendado, Confirmado, Em Separação, Em Rota, Entregue, Não Entregue, Reagendado, Cancelado)
- `app/Enums/DeliveryPriority.php` - Prioridades (Normal, Urgente, Agendada)

#### Models
- `app/Models/SchedulingOfDeliveries.php` - Model principal do agendamento
- `app/Models/DeliveryTimeWindow.php` - Model para janelas de entrega

#### Livewire
- `app/Livewire/Logistica/AgendamentoEntregas.php` - Componente completo com CRUD
- `resources/views/livewire/logistica/agendamento-entregas.blade.php` - View do componente

#### Migrations
- `2026_04_11_400001_create_delivery_time_windows_table.php` - Tabela de janelas de entrega
- `2026_04_11_400002_update_scheduling_of_deliveries_table.php` - Atualização da tabela de agendamentos

#### CSS
- `resources/css/_agendamento-entrega.css` - Estilos específicos do módulo
- Importado em `resources/css/app.css`

### 🔧 Configurações

#### Rotas
```php
Route::get('/logistica/agendamento-entregas', AgendamentoEntregas::class)
    ->name('scheduling_of_deliveries.index');
```

#### Middleware
Adicionado à whitelist do `MaintenanceERP.php`:
```php
$request->routeIs('scheduling_of_deliveries.*')
```

### 🎯 Funcionalidades Implementadas

#### 1. Dashboard com KPIs
- Total de agendamentos
- Agendados
- Em rota
- Entregues
- Não entregues
- Reagendados

#### 2. Filtros Avançados
- Busca por cliente, endereço ou número
- Filtro por data
- Filtro por status
- Filtro por prioridade

#### 3. CRUD Completo
- ✅ Criar agendamento
- ✅ Editar agendamento
- ✅ Visualizar detalhes
- ✅ Excluir agendamento
- ✅ Alterar status
- ✅ Reagendar entrega

#### 4. Integração com Pedidos
- Vinculação opcional com pedidos de venda
- Auto-preenchimento de dados do cliente e endereço
- Sincronização com data prevista de entrega

#### 5. Logística
- Seleção de janela de entrega
- Alocação de veículo
- Atribuição de motorista
- Controle de peso e volume

#### 6. Reagendamento
- Modal específico para reagendamento
- Registro de motivo obrigatório
- Histórico de reagendamentos
- Criação de novo registro vinculado ao original

### 📊 Estrutura do Banco de Dados

#### Tabela: `scheduling_of_deliveries`
- `schedule_number` - Número único do agendamento (AG-00001)
- `order_id` - Vínculo opcional com pedido de venda
- `client_name` - Nome do cliente
- `delivery_address` - Endereço completo de entrega
- `delivery_date` - Data da entrega
- `time_window_id` - Janela de horário
- `vehicle_id` - Veículo alocado
- `driver_name` - Nome do motorista
- `weight_kg` - Peso da carga
- `volume_m3` - Volume da carga
- `priority` - Prioridade (normal, urgente, agendada)
- `status` - Status atual
- `notes` - Observações
- `receiver_name` - Nome do recebedor
- `receiver_document` - Documento do recebedor
- `delivered_at` - Data/hora da entrega
- `rescheduled_from_id` - ID do agendamento original (se reagendado)
- `reschedule_reason` - Motivo do reagendamento

#### Tabela: `delivery_time_windows`
- `name` - Nome da janela (ex: Manhã)
- `start_time` - Hora início
- `end_time` - Hora fim
- `capacity` - Capacidade máxima
- `region` - Região atendida
- `is_active` - Ativo/Inativo

### 🎨 UI/UX

#### Design
- Interface moderna seguindo o design system Nexora
- Cards KPI com cores diferenciadas por métrica
- Badges coloridos para status e prioridade
- Modais responsivos com abas
- Tabela com ações rápidas
- Empty states informativos

#### Responsividade
- Grid adaptativo para KPIs (6 → 3 → 2 → 1 coluna)
- Modais responsivos em mobile
- Filtros que se adaptam ao tamanho da tela

### 🚀 Como Usar

1. **Acesse o módulo:**
   - Navegue para: `http://localhost:8000/logistica/agendamento-entregas`
   - Ou através do menu: Início > Transporte > Agendamento de Entregas

2. **Criar novo agendamento:**
   - Clique em "Novo Agendamento"
   - Preencha os dados (cliente e data são obrigatórios)
   - Opcionalmente vincule a um pedido de venda
   - Defina janela, veículo e motorista
   - Salve

3. **Reagendar:**
   - Clique no ícone de reagendamento (laranja)
   - Informe nova data e motivo
   - Confirme
   - Um novo agendamento será criado vinculado ao original

4. **Alterar status:**
   - Abra os detalhes do agendamento
   - Use os botões de status rápido
   - Ou edite e altere manualmente

### 📝 Observações

- ✅ Página removida de "Em Desenvolvimento"
- ✅ Totalmente funcional e integrada
- ✅ Seguindo padrões do projeto Laravel + Livewire
- ✅ CSS compilado e caches limpos
- ✅ Migrations aplicadas com sucesso
- ✅ Rotas registradas e acessíveis

### 🔗 Relacionamentos

- `SchedulingOfDeliveries` → `SalesOrder` (order)
- `SchedulingOfDeliveries` → `DeliveryTimeWindow` (timeWindow)
- `SchedulingOfDeliveries` → `Vehicle` (vehicle)
- `SchedulingOfDeliveries` → `SchedulingOfDeliveries` (rescheduledFrom)

### ⚡ Próximos Passos (Opcionais)

1. Implementar notificações automáticas para cliente
2. Integração com sistema de rastreamento GPS
3. Dashboard de otimização de rotas
4. Relatórios de performance de entregas
5. API para aplicativo mobile do motorista

