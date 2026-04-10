# Guia de Implementação: Pedidos de Venda (Fiscal e Gerencial) - Nexora ERP

Este módulo gerencia o ciclo de vida de uma venda, desde o orçamento até a entrega, diferenciando o tratamento tributário conforme a necessidade da operação.

---

## 1. Design e UI (Interface do Usuário)

A tela de pedidos deve ser rápida. Utilize um **Header fixo** com os totais e um corpo com abas ou seções claras para os itens.

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Seletor de Tipo** | Switch ou Toggle | Alternar entre **Fiscal** (NF-e) e **Gerencial** (Recibo). |
| **Busca de Itens** | Input com Autocomplete | Adicionar produtos via nome ou código de barras. |
| **Painel de Totais** | Sidebar Direita | Exibir Subtotal, Desconto, Frete e Valor Final em destaque. |
| **Status de Fluxo** | Stepper (Passos) | Orçamento → Aprovado → Faturado → Entregue. |

---

## 2. Arquitetura de Dados (Backend Laravel)

O segredo está na flag `is_fiscal`. Se `true`, o sistema exige dados tributários (NCM, CFOP). Se `false`, o fluxo é simplificado.

### Migration Sugerida:
```php
Schema::create('sales_orders', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('client_id');
    $table->unsignedBigInteger('user_id');           // Vendedor
    $table->boolean('is_fiscal')->default(false);    // Diferencia Fiscal de Gerencial
    $table->decimal('total_amount', 15, 2);
    $table->decimal('discount_amount', 15, 2)->default(0);
    $table->string('payment_condition');             // Ex: 30/60 dias, À vista
    $table->enum('status', ['draft', 'approved', 'invoiced', 'cancelled']);
    $table->text('internal_notes')->nullable();      // Notas que não saem na nota/recibo
    $table->timestamps();

    $table->foreign('client_id')->references('id')->on('clients');
});

Schema::create('sales_order_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('sales_order_id');
    $table->unsignedBigInteger('product_id');
    $table->decimal('quantity', 15, 3);
    $table->decimal('unit_price', 15, 2);
    $table->decimal('subtotal', 15, 2);
    $table->decimal('tax_amount', 15, 2)->default(0); // Só preenchido se is_fiscal for true
    $table->timestamps();
});
```

---

## 3. Componente React (SalesOrderForm.jsx)
   Focaremos no formulário dinâmico que muda de comportamento conforme o tipo de venda.

```JavaScript 
import React, { useState } from 'react';
import { ShoppingCart, User, FileText, ShieldCheck, Trash2, Save } from 'lucide-react';

export default function SalesOrderForm() {
const [isFiscal, setIsFiscal] = useState(false);

    return (
        <div className="p-8 grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div className="lg:col-span-3 space-y-6">
                {/* Header do Pedido */}
                <header className="flex justify-between items-center bg-white/5 p-6 rounded-2xl border border-white/10">
                    <div>
                        <h1 className="text-xl font-bold text-white">Novo Pedido</h1>
                        <p className="text-xs text-slate-500 uppercase tracking-widest">Controle de Saída</p>
                    </div>
                    
                    {/* Switch Fiscal/Gerencial */}
                    <div className="flex items-center gap-3 bg-[#0a0f1d] p-1 rounded-full border border-white/10">
                        <button 
                            onClick={() => setIsFiscal(false)}
                            className={`px-4 py-1.5 rounded-full text-[10px] font-black transition-all ${!isFiscal ? 'bg-slate-700 text-white' : 'text-slate-500'}`}
                        >
                            GERENCIAL
                        </button>
                        <button 
                            onClick={() => setIsFiscal(true)}
                            className={`px-4 py-1.5 rounded-full text-[10px] font-black transition-all ${isFiscal ? 'bg-cyan-600 text-white' : 'text-slate-500'}`}
                        >
                            FISCAL (NF-e)
                        </button>
                    </div>
                </header>

                {/* Adição de Produtos */}
                <section className="glass-card p-6 rounded-2xl border border-white/10 bg-white/5">
                    <div className="flex gap-4 mb-6">
                        <div className="flex-1 relative">
                            <ShoppingCart className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" size={18} />
                            <input type="text" placeholder="Buscar produto por nome ou SKU..." className="input-nexora pl-10" />
                        </div>
                        <button className="bg-cyan-600 px-6 rounded-xl font-bold text-white text-sm hover:bg-cyan-500 transition-all">Adicionar</button>
                    </div>

                    {/* Tabela de Itens */}
                    <table className="w-full text-left text-sm">
                        <thead className="text-[10px] text-slate-500 uppercase border-b border-white/5">
                            <tr>
                                <th className="pb-4">Produto</th>
                                <th className="pb-4">Qtd</th>
                                <th className="pb-4">Preço Unit.</th>
                                {isFiscal && <th className="pb-4">Impostos</th>}
                                <th className="pb-4 text-right">Subtotal</th>
                                <th className="pb-4"></th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-white/5">
                            <tr className="group">
                                <td className="py-4 font-medium text-white">Cimento CP II 50kg</td>
                                <td className="py-4">10</td>
                                <td className="py-4 text-slate-400">R$ 35,00</td>
                                {isFiscal && <td className="py-4 text-cyan-400 text-xs">R$ 4,20 (ICMS)</td>}
                                <td className="py-4 text-right font-bold text-white">R$ 350,00</td>
                                <td className="py-4 text-right"><button className="text-slate-600 hover:text-rose-500"><Trash2 size={16}/></button></td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </div>

            {/* Sidebar de Totais */}
            <aside className="lg:col-span-1 space-y-6">
                <div className="glass-card p-6 rounded-2xl border border-white/10 bg-white/5 sticky top-8">
                    <h3 className="text-sm font-bold text-white mb-6 flex items-center gap-2">
                        <FileText size={16} className="text-cyan-400" /> Resumo da Venda
                    </h3>
                    
                    <div className="space-y-4 border-b border-white/5 pb-6">
                        <div className="flex justify-between text-xs">
                            <span className="text-slate-500">Subtotal</span>
                            <span className="text-white">R$ 350,00</span>
                        </div>
                        <div className="flex justify-between text-xs">
                            <span className="text-slate-500">Desconto</span>
                            <input type="text" className="bg-transparent border-b border-white/10 w-16 text-right outline-none text-rose-400" placeholder="0,00" />
                        </div>
                    </div>

                    <div className="py-6 flex justify-between items-center">
                        <span className="text-sm font-bold text-white">Total</span>
                        <span className="text-2xl font-black text-white">R$ 350,00</span>
                    </div>

                    <button className="w-full py-4 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl font-black text-white text-sm shadow-xl shadow-cyan-500/20 hover:scale-[1.02] transition-all">
                        FINALIZAR VENDA
                    </button>
                </div>
            </aside>
        </div>
    );
}
```

---

## 4. Regras de Negócio Fundamentais
Venda Fiscal (NF-e): * Exige que o cliente tenha CPF/CNPJ e endereço completo.

Realiza a reserva de estoque mas só dá baixa real após o retorno "Autorizado" da SEFAZ.

Venda Gerencial:

Permite venda para "Consumidor Final" sem dados detalhados.

Dá baixa imediata no estoque e gera o título no Contas a Receber.

Integração Financeira: Ao finalizar, o pedido deve gerar as parcelas no financeiro. Se for pago em "Dinheiro", já cria o lançamento como "Pago" e alimenta o Fluxo de Caixa.

Estorno: O cancelamento de um pedido deve devolver os itens ao estoque e cancelar os títulos financeiros pendentes.

---

## 5. Diferencial Nexora (IA Sales Assistant)
A Nexora AI atua como um consultor de vendas:

Cross-Selling: "Notei que você adicionou Cimento. Geralmente os clientes também levam Argamassa e Desempenadeira. Deseja adicionar?".

Alerta de Margem: "Este desconto de 15% deixa a venda abaixo do custo operacional. Deseja solicitar autorização do gerente?".

Previsão de Entrega: "Baseado na logística atual, este pedido chegará ao cliente em 2 dias úteis".

---

## Conclusão
O módulo de Pedidos de Venda é o coração do processo comercial. Com uma interface ágil e regras de negócio claras, ele garante que as operações fluam sem atritos, seja para vendas fiscais ou gerenciais. 
A integração com o financeiro e o estoque, aliada à inteligência artificial, torna a experiência do usuário mais fluida e eficiente, aumentando a satisfação do cliente e a produtividade da equipe de vendas.
