# 🏦 Guia de Implementação: Gestão de Contas Bancárias (Nexora ERP)

Este módulo permite o cadastro e monitoramento de contas correntes, poupanças, caixas internos (dinheiro vivo) e carteiras digitais.

---

## 🎨 1. Design e UI (Interface do Usuário)

A página deve apresentar as contas como **"Cards de Banco"** no topo, seguidos por uma tabela detalhada de conciliação.

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Cards de Saldo** | Glassmorphism com Gradiente | Exibir Saldo Atual e Saldo Previsto (Conciliado). |
| **Logotipos** | Ícones de Bancos (Nubank, Itaú, etc) | Identificação visual imediata da conta. |
| **Botão de Transferência** | Destaque em Ciano | Atalho para transferir entre contas internas. |
| **Status de Conciliação** | Badge Amarelo/Verde | Indica se o saldo do ERP bate com o extrato bancário. |

---

## 🏗️ 2. Arquitetura de Dados (Backend Laravel)

Uma conta bancária deve estar obrigatoriamente vinculada a uma conta do seu **Plano de Contas** (Geralmente no grupo de Ativo Circulante -> Disponibilidades).

### Migration Sugerida:
```php
Schema::create('bank_accounts', function (Blueprint $table) {
    $table->id();
    $table->string('name');             // Ex: Itaú Principal
    $table->string('bank_name');        // Ex: Banco Itaú (Cod: 341)
    $table->string('agency')->nullable();
    $table->string('number')->nullable();
    $table->enum('type', ['corrente', 'poupanca', 'caixa_interno', 'digital']);
    $table->decimal('balance', 15, 2)->default(0);
    $table->unsignedBigInteger('chart_of_account_id'); // Link com Plano de Contas
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
});
