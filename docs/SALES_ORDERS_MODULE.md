# Módulo de Pedidos de Venda - Documentação Completa

## 📋 Resumo das Implementações

Este documento descreve todas as alterações e implementações realizadas no módulo de Pedidos de Venda do sistema Nexora ERP.

---

## 🗂️ Estrutura de Tabelas Criadas/Atualizadas

### 1. **sales_orders** (Atualizada)
Tabela principal de pedidos de venda com todos os campos necessários para controle completo.

**Novos Campos Adicionados:**
- **Identificação e Controle:**
  - `order_date`: Data e hora do pedido
  - `expected_delivery_date`: Data prevista de entrega
  - `operation_type`: Tipo de operação (venda normal, bonificação, consignação, transferência)
  - `sales_channel`: Canal de venda (balcão, online, representante, etc)
  - `origin`: Origem do pedido (manual, API, e-commerce)
  - `company_branch`: Empresa/filial
  - `seller_id`: Vendedor/representante (FK users)

- **Dados do Cliente (cache):**
  - `client_cpf_cnpj`: CPF/CNPJ
  - `client_ie`: Inscrição Estadual
  - `client_type`: Tipo (PF/PJ)
  - `client_credit_limit`: Limite de crédito
  - `client_situation`: Situação (ativo/inadimplente)
  - `client_contact_phone`: Telefone
  - `client_contact_email`: Email

- **Tabela de Preço:**
  - `price_table_id`: FK para tabela de preços
  - `minimum_margin`: Margem mínima

- **Totais do Pedido:**
  - `gross_total`: Total bruto
  - `additions_amount`: Acréscimos
  - `insurance_amount`: Seguro
  - `other_expenses`: Outras despesas
  - `net_total`: Total líquido

- **Impostos (Totais):**
  - `icms_base`: Base de cálculo ICMS
  - `icms_amount`: Valor ICMS
  - `icms_st_amount`: Valor ICMS ST
  - `ipi_amount`: Valor IPI
  - `pis_amount`: Valor PIS
  - `cofins_amount`: Valor COFINS
  - `fcp_amount`: Valor FCP

- **Logística/Entrega:**
  - `carrier_id`: Transportadora (FK carriers)
  - `freight_type`: Tipo de frete (CIF/FOB)
  - `gross_weight`: Peso bruto
  - `net_weight`: Peso líquido
  - `volumes`: Quantidade de volumes
  - `tracking_code`: Código de rastreio
  - `delivery_notes`: Observações de entrega

- **Separação/Romaneio:**
  - `separation_status`: Status da separação
  - `separator_user_id`: Usuário separador (FK users)
  - `separation_date`: Data da separação
  - `conference_date`: Data da conferência

- **Faturamento/NF-e:**
  - `fiscal_note_id`: FK para nota fiscal
  - `nfe_number`: Número da NF-e
  - `nfe_series`: Série da NF-e
  - `nfe_key`: Chave da NF-e
  - `nfe_protocol`: Protocolo SEFAZ
  - `nfe_status`: Status da NF-e
  - `nfe_issued_at`: Data de emissão

- **Observações:**
  - `fiscal_notes_obs`: Observações fiscais (vai para NF-e)

- **Aprovações:**
  - `approved_by`: Usuário que aprovou (FK users)
  - `approved_at`: Data da aprovação
  - `approval_reason`: Motivo da aprovação
  - `needs_approval`: Flag se precisa aprovação

- **Auditoria:**
  - `created_by`: Usuário que criou (FK users)
  - `updated_by`: Usuário que atualizou (FK users)

---

### 2. **sales_order_items** (Atualizada)
Itens do pedido com informações fiscais completas.

**Novos Campos Adicionados:**
- **Identificação do Produto:**
  - `sku`: Código SKU
  - `ean`: Código de barras EAN
  - `description`: Descrição do produto
  - `unit`: Unidade de medida

- **Valores:**
  - `discount_percent`: Desconto em percentual
  - `addition`: Acréscimo em valor
  - `addition_percent`: Acréscimo em percentual
  - `total`: Total do item

- **Controle de Estoque:**
  - `batch`: Lote
  - `expiry_date`: Validade
  - `serial_number`: Número de série
  - `stock_location`: Localização no estoque

- **Fiscal:**
  - `cfop`: CFOP
  - `ncm`: NCM
  - `cst`: CST
  - `csosn`: CSOSN
  - `origin`: Origem da mercadoria

- **ICMS:**
  - `icms_base`: Base de cálculo
  - `icms_percent`: Alíquota
  - `icms_amount`: Valor

- **ICMS ST:**
  - `icms_st_base`: Base de cálculo
  - `icms_st_percent`: Alíquota
  - `icms_st_amount`: Valor

- **IPI:**
  - `ipi_base`: Base de cálculo
  - `ipi_percent`: Alíquota
  - `ipi_amount`: Valor

- **PIS:**
  - `pis_base`: Base de cálculo
  - `pis_percent`: Alíquota
  - `pis_amount`: Valor

- **COFINS:**
  - `cofins_base`: Base de cálculo
  - `cofins_percent`: Alíquota
  - `cofins_amount`: Valor

- **FCP:**
  - `fcp_base`: Base de cálculo
  - `fcp_percent`: Alíquota
  - `fcp_amount`: Valor

---

### 3. **sales_order_addresses** (Nova)
Endereços do pedido (faturamento, entrega, cobrança).

**Campos:**
- `sales_order_id`: FK pedido
- `type`: Tipo (billing/delivery/collection)
- `zip_code`: CEP
- `street`: Logradouro
- `number`: Número
- `complement`: Complemento
- `district`: Bairro
- `city`: Cidade
- `state`: Estado
- `country`: País
- `ibge_code`: Código IBGE

---

### 4. **sales_order_payments** (Nova)
Condições de pagamento do pedido.

**Campos:**
- `sales_order_id`: FK pedido
- `payment_condition`: Condição (à vista, 30/60, etc)
- `payment_method`: Forma (Dinheiro, Cartão, PIX, Boleto)
- `installments`: Número de parcelas
- `total_amount`: Valor total

---

### 5. **sales_order_installments** (Nova)
Parcelas do pedido.

**Campos:**
- `sales_order_payment_id`: FK pagamento
- `sales_order_id`: FK pedido
- `installment_number`: Número da parcela
- `amount`: Valor
- `due_date`: Data de vencimento
- `payment_date`: Data de pagamento
- `status`: Status (pending/paid/overdue/cancelled)
- `payment_method`: Forma de pagamento
- `notes`: Observações

---

### 6. **sales_order_logs** (Nova)
Histórico de alterações do pedido.

**Campos:**
- `sales_order_id`: FK pedido
- `user_id`: FK usuário
- `action`: Ação (created/updated/status_changed/approved/cancelled)
- `old_status`: Status anterior
- `new_status`: Novo status
- `description`: Descrição
- `changes`: JSON com todas as alterações
- `ip_address`: IP do usuário
- `user_agent`: User agent

---

### 7. **sales_order_attachments** (Nova)
Anexos do pedido.

**Campos:**
- `sales_order_id`: FK pedido
- `uploaded_by`: FK usuário que fez upload
- `type`: Tipo (pedido_assinado/contrato/documento_cliente/danfe)
- `file_name`: Nome do arquivo
- `file_path`: Caminho do arquivo
- `mime_type`: Tipo MIME
- `file_size`: Tamanho em bytes
- `description`: Descrição

---

### 8. **price_tables** (Nova)
Tabelas de preço.

**Campos:**
- `name`: Nome da tabela
- `code`: Código único
- `description`: Descrição
- `is_active`: Ativa
- `is_default`: Padrão
- `valid_from`: Válida de
- `valid_until`: Válida até

---

### 9. **price_table_items** (Nova)
Itens das tabelas de preço.

**Campos:**
- `price_table_id`: FK tabela de preço
- `product_id`: FK produto
- `price`: Preço
- `minimum_price`: Preço mínimo
- `promotional_price`: Preço promocional
- `promotional_valid_from`: Promoção válida de
- `promotional_valid_until`: Promoção válida até
- `quantity_from`: Quantidade de (faixa)
- `quantity_to`: Quantidade até (faixa)
- `quantity_price`: Preço por quantidade

---

### 10. **carriers** (Nova)
Transportadoras.

**Campos:**
- `name`: Nome
- `trade_name`: Nome fantasia
- `cnpj`: CNPJ
- `ie`: Inscrição Estadual
- `phone`: Telefone
- `email`: Email
- `address`: Endereço
- `is_active`: Ativa

---

### 11. **clients** (Atualizada)
Campos adicionados para controle de vendas.

**Novos Campos:**
- `inscricao_estadual`: Inscrição Estadual
- `credit_limit`: Limite de crédito
- `payment_condition_default`: Condição de pagamento padrão
- `situation`: Situação (active/inactive/defaulter)
- `price_table_id`: FK tabela de preço padrão
- `discount_limit`: Limite de desconto

---

## 🎯 Enums Criados

### 1. **SalesOrderStatus** (Atualizado)
```php
- Draft (Rascunho)
- Aberto
- Approved (Aprovado)
- EmSeparacao (Em Separação)
- Invoiced (Faturado)
- Delivered (Entregue)
- Cancelled (Cancelado)
```

### 2. **TipoOperacaoVenda** (Novo)
```php
- VendaNormal
- Bonificacao
- Consignacao
- Transferencia
```

### 3. **CanalVenda** (Novo)
```php
- Balcao
- Online
- Representante
- Televendas
- Mobile
```

### 4. **OrigemPedido** (Novo)
```php
- Manual
- API
- Ecommerce
- Importacao
- ForcaVendas
```

### 5. **TipoFrete** (Novo)
```php
- CIF (Por conta do Remetente)
- FOB (Por conta do Destinatário)
- SemFrete
- Terceiros
```

### 6. **StatusSeparacao** (Novo)
```php
- AguardandoSeparacao
- EmSeparacao
- Separado
- Conferido
- Expedido
```

---

## 📦 Models Criados/Atualizados

### Models Criados:
1. **SalesOrderAddress** - Endereços do pedido
2. **SalesOrderPayment** - Pagamentos
3. **SalesOrderInstallment** - Parcelas
4. **SalesOrderLog** - Logs/Auditoria
5. **SalesOrderAttachment** - Anexos
6. **PriceTable** - Tabelas de preço
7. **PriceTableItem** - Itens de tabela de preço
8. **Carrier** - Transportadoras

### Models Atualizados:
1. **SalesOrder** - Modelo principal com todos os relacionamentos
2. **SalesOrderItem** - Itens com cálculo de impostos
3. **Client** - Campos e métodos para vendas

---

## 🔧 Funcionalidades Implementadas

### No Model SalesOrder:

#### Relacionamentos:
- `client()` - Cliente
- `user()` - Usuário responsável
- `seller()` - Vendedor
- `items()` - Itens do pedido
- `addresses()` - Endereços
- `payments()` - Pagamentos
- `installments()` - Parcelas
- `logs()` - Histórico
- `attachments()` - Anexos
- `priceTable()` - Tabela de preço
- `carrier()` - Transportadora
- `separator()` - Separador
- `approver()` - Aprovador
- `creator()` - Criador
- `updater()` - Último atualizador
- `fiscalNote()` - Nota fiscal

#### Métodos:
- `billingAddress()` - Retorna endereço de faturamento
- `deliveryAddress()` - Retorna endereço de entrega
- `collectionAddress()` - Retorna endereço de cobrança
- `calculateTotals()` - Calcula todos os totais e impostos
- `logAction()` - Registra ação no histórico
- `approve()` - Aprova o pedido
- `cancel()` - Cancela o pedido
- `canEdit()` - Verifica se pode editar
- `canCancel()` - Verifica se pode cancelar

#### Observers/Events:
- Geração automática de número do pedido
- Registro de criação/atualização por usuário
- Log automático de criação e mudanças de status

---

### No Model SalesOrderItem:

#### Métodos:
- `calculateTaxes()` - Calcula todos os impostos do item
- Cálculo automático de desconto por percentual
- Cálculo automático de acréscimo por percentual
- Cálculo automático de subtotal e total

---

### No Model Client:

#### Métodos:
- `isActive()` - Verifica se cliente está ativo
- `isDefaulter()` - Verifica se cliente é inadimplente
- `hasAvailableCredit()` - Verifica limite de crédito disponível

---

### No Model PriceTable:

#### Métodos:
- `isValid()` - Verifica se tabela está válida
- `scopeActive()` - Scope para tabelas ativas
- `scopeValid()` - Scope para tabelas válidas

---

### No Model PriceTableItem:

#### Métodos:
- `getEffectivePrice()` - Retorna preço efetivo considerando promoção e quantidade
- `hasValidPromotion()` - Verifica se tem promoção válida

---

## 📊 Fluxo de Trabalho

### 1. Criação do Pedido
```
Draft → Aberto → Aprovado → Em Separação → Faturado → Entregue
```

### 2. Aprovações
- Sistema verifica se pedido precisa de aprovação
- Critérios: desconto alto, cliente com restrição, valor acima do limite
- Histórico completo de aprovações

### 3. Separação
```
Aguardando Separação → Em Separação → Separado → Conferido → Expedido
```

### 4. Faturamento
- Geração de NF-e integrada
- Registro de chave, protocolo e status
- Anexo automático do DANFE

### 5. Auditoria
- Todos os logs automáticos
- Rastreamento completo de alterações
- IP e user agent registrados

---

## 🎨 Recursos Avançados

### 1. Tabela de Preços
- Múltiplas tabelas por cliente
- Preços promocionais com período
- Preços por faixa de quantidade
- Preço mínimo por produto

### 2. Controle de Crédito
- Limite por cliente
- Verificação automática
- Bloqueio de inadimplentes

### 3. Impostos Completos
- ICMS, ICMS ST, IPI, PIS, COFINS, FCP
- Cálculo por item
- Totalização automática

### 4. Múltiplos Endereços
- Faturamento
- Entrega
- Cobrança

### 5. Formas de Pagamento
- Múltiplas formas
- Parcelamento
- Controle de vencimentos

---

## ✅ Checklist de Implementação

### Concluído ✓
- [x] Estrutura de banco de dados completa
- [x] Models com relacionamentos
- [x] Enums para controle de status
- [x] Cálculos automáticos de impostos
- [x] Sistema de logs e auditoria
- [x] Controle de aprovações
- [x] Tabelas de preço
- [x] Controle de crédito
- [x] Múltiplos endereços
- [x] Formas de pagamento e parcelas

### Próximos Passos 📝
- [ ] Criar recursos Filament para interface
- [ ] Implementar validações de negócio
- [ ] Criar relatórios
- [ ] Implementar integração com NF-e
- [ ] Criar API endpoints
- [ ] Implementar estoque (reserva/baixa)
- [ ] Integração financeira (contas a receber)
- [ ] Permissões por perfil
- [ ] Dashboards e gráficos

---

## 📁 Arquivos Criados/Modificados

### Migrations (11 arquivos):
1. `2026_04_11_000001_update_sales_orders_add_full_fields.php`
2. `2026_04_11_000002_create_sales_order_addresses_table.php`
3. `2026_04_11_000003_create_sales_order_payments_table.php`
4. `2026_04_11_000004_create_sales_order_installments_table.php`
5. `2026_04_11_000005_update_sales_order_items_add_full_fields.php`
6. `2026_04_11_000006_create_sales_order_logs_table.php`
7. `2026_04_11_000007_create_sales_order_attachments_table.php`
8. `2026_04_11_000008_create_price_tables_table.php`
9. `2026_04_11_000009_create_price_table_items_table.php`
10. `2026_04_11_000010_create_carriers_table.php`
11. `2026_04_11_000011_update_clients_add_sales_fields.php`

### Models (11 arquivos):
1. `app/Models/SalesOrder.php` (atualizado)
2. `app/Models/SalesOrderItem.php` (atualizado)
3. `app/Models/SalesOrderAddress.php` (novo)
4. `app/Models/SalesOrderPayment.php` (novo)
5. `app/Models/SalesOrderInstallment.php` (novo)
6. `app/Models/SalesOrderLog.php` (novo)
7. `app/Models/SalesOrderAttachment.php` (novo)
8. `app/Models/PriceTable.php` (novo)
9. `app/Models/PriceTableItem.php` (novo)
10. `app/Models/Carrier.php` (novo)
11. `app/Models/Client.php` (atualizado)

### Enums (6 arquivos):
1. `app/Enums/SalesOrderStatus.php` (atualizado)
2. `app/Enums/TipoOperacaoVenda.php` (novo)
3. `app/Enums/CanalVenda.php` (novo)
4. `app/Enums/OrigemPedido.php` (novo)
5. `app/Enums/TipoFrete.php` (novo)
6. `app/Enums/StatusSeparacao.php` (novo)

---

## 🚀 Comandos Úteis

### Executar migrations:
```bash
docker-compose exec app php artisan migrate
```

### Reverter última migração:
```bash
docker-compose exec app php artisan migrate:rollback
```

### Ver status das migrations:
```bash
docker-compose exec app php artisan migrate:status
```

---

## 📞 Suporte

Para dúvidas ou sugestões sobre o módulo de Pedidos de Venda, consulte a documentação completa do sistema ou entre em contato com a equipe de desenvolvimento.

---

**Versão:** 1.0.0  
**Data:** 10 de Abril de 2026  
**Autor:** Nexora Development Team

