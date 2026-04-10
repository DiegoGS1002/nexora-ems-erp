# 📦 Guia de Implementação: Movimentação de Estoque (Nexora ERP)

Este módulo registra todo o histórico de fluxo de mercadorias, permitindo auditoria, rastreabilidade e controle de custos.

---

## 🎨 1. Design e UI (Interface do Usuário)

A interface deve ser focada em **Timeline**. O usuário precisa ver rapidamente o "quem, quando e por que" de cada alteração de saldo.

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Cards de Inventário** | Glassmorphism | Exibir Total de Itens, Valor do Estoque e Itens Abaixo do Mínimo. |
| **Badges de Operação** | Verde / Vermelho / Azul | Identificar Entrada, Saída ou Transferência. |
| **Filtro por Categoria** | Select Moderno | Filtrar movimentações por tipo de produto ou depósito. |
| **Botão de Ajuste Rápido** | Modal Pop-up | Realizar correções manuais (perda/quebra) com justificativa. |

---

## 🏗️ 2. Arquitetura de Dados (Backend Laravel)

Cada movimentação de estoque deve ser atômica e estar vinculada a um motivo (Origem).

### Migration Sugerida:
```php
Schema::create('stock_movements', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_id');
    $table->unsignedBigInteger('user_id');             // Quem realizou a ação
    $table->decimal('quantity', 15, 3);                // Aceita decimais (ex: 1.5kg)
    $table->enum('type', ['input', 'output', 'adjustment', 'transfer']);
    $table->string('origin');                          // Ex: Venda #102, Compra #45, Perda
    $table->decimal('unit_cost', 15, 2)->nullable();   // Valor do item no momento da mov.
    $table->text('observation')->nullable();
    $table->timestamps();

    $table->foreign('product_id')->references('id')->on('products');
    $table->foreign('user_id')->references('id')->on('users');
});
```
## 3. Componente React (StockMovement.jsx)
   Focaremos em uma tabela de histórico rica em detalhes visuais.

```JavaScript
import React from 'react';
import { ArrowUpRight, ArrowDownLeft, RefreshCcw, Search, AlertTriangle } from 'lucide-react';

export default function StockMovement({ movements }) {
return (
<div className="p-8 space-y-6">
<header className="flex justify-between items-center">
<div>
<h1 className="text-2xl font-bold text-white tracking-tight">Movimentação de Estoque</h1>
<p className="text-slate-400 text-sm">Rastreabilidade total de entradas e saídas.</p>
</div>
<div className="flex gap-3">
<button className="bg-white/5 border border-white/10 text-white px-4 py-2 rounded-lg hover:bg-white/10 transition-all">
Relatório Kardex
</button>
<button className="bg-cyan-600 hover:bg-cyan-500 text-white px-6 py-2 rounded-lg font-bold shadow-lg shadow-cyan-500/20">
Novo Ajuste Manual
</button>
</div>
</header>

            {/* Listagem de Movimentações */}
            <div className="rounded-2xl border border-white/10 bg-white/5 overflow-hidden backdrop-blur-md">
                <table className="w-full text-left">
                    <thead className="bg-white/5 text-[10px] text-slate-500 uppercase font-black tracking-widest">
                        <tr>
                            <th className="p-4">Data / Hora</th>
                            <th className="p-4">Produto</th>
                            <th className="p-4">Tipo</th>
                            <th className="p-4 text-right">Qtd</th>
                            <th className="p-4">Origem / Justificativa</th>
                            <th className="p-4 text-right">Usuário</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-white/5 text-sm text-slate-300">
                        {movements.map(mov => (
                            <tr key={mov.id} className="hover:bg-white/[0.02] transition-colors group">
                                <td className="p-4 text-xs font-mono">{mov.created_at}</td>
                                <td className="p-4 font-bold text-white">{mov.product_name}</td>
                                <td className="p-4">
                                    <div className={`flex items-center gap-1.5 font-bold text-[10px] uppercase
                                        ${mov.type === 'input' ? 'text-emerald-400' : 
                                          mov.type === 'output' ? 'text-rose-400' : 'text-cyan-400'}`}>
                                        {mov.type === 'input' ? <ArrowUpRight size={12}/> : 
                                         mov.type === 'output' ? <ArrowDownLeft size={12}/> : <RefreshCcw size={12}/>}
                                        {mov.type}
                                    </div>
                                </td>
                                <td className={`p-4 text-right font-bold ${mov.type === 'input' ? 'text-emerald-400' : 'text-rose-400'}`}>
                                    {mov.type === 'input' ? '+' : '-'}{mov.quantity}
                                </td>
                                <td className="p-4 italic text-slate-500 text-xs">{mov.origin}</td>
                                <td className="p-4 text-right text-xs text-slate-400">{mov.user_name}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

```

---

## 4. Regras de Negócio Fundamentais
Cálculo de Custo Médio: A cada nova Entrada (Compra), o sistema deve recalcular o custo médio do produto:
Novo Custo = (Saldo Atual * Custo Atual + Qtd Entrada * Custo Entrada) / (Saldo Total).

Venda Sem Estoque (Configuração): O sistema deve respeitar a flag definida nas Configurações de Venda. Se bloqueado, o backend deve lançar uma Exception se a movimentação de saída resultar em saldo negativo.

Reserva de Mercadoria: Movimentações do tipo "Pedido" podem apenas "reservar" o estoque (diminuir o saldo disponível, mas manter o saldo real) até que a nota fiscal seja emitida.

Auditoria de Ajuste: Todo ajuste manual (type: 'adjustment') deve obrigatoriamente ter uma observação preenchida pelo usuário.

---

## 5. Diferencial Nexora (IA Inventory)
A Nexora AI transforma dados brutos em inteligência logística:

Previsão de Ruptura: "Baseado no histórico de saídas, o produto 'X' ficará sem estoque em 4 dias. Deseja gerar uma sugestão de compra?".

Detector de Anomalias: "Identifiquei uma saída manual atípica de 50 unidades. Pode ser um erro de lançamento ou extravio".

Análise Curva ABC: Classifica automaticamente seus produtos em A (mais giram), B e C para otimizar o espaço físico do depósito.

---

## 6. Conclusão
A movimentação de estoque é o coração do controle logístico. Com uma interface intuitiva, regras de negócio robustas e inteligência artificial, a Nexora ERP oferece uma solução completa para manter seu estoque sempre sob controle, evitando surpresas e otimizando a gestão de mercadorias. Implementar este módulo com atenção aos detalhes garantirá uma experiência fluida para o usuário e um controle rigoroso para a empresa. Vamos garantir que cada movimento seja registrado com precisão, proporcionando uma visão clara e confiável do fluxo de mercadorias.     
