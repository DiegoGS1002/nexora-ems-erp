# 📈 Guia de Implementação: Fluxo de Caixa (Nexora ERP)

O Fluxo de Caixa consolida todas as entradas e saídas, permitindo uma visão histórica (o que aconteceu) e projetada (o que vai acontecer) da saúde financeira da empresa.

---

## 🎨 1. Design e UI (Interface do Usuário)

Esta tela deve ser visualmente densa em dados, mas limpa em design, focando em saldos acumulados.

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Gráfico de Linha** | Neon (Ciano/Rosa) | Comparativo de Entradas vs Saídas ao longo do mês. |
| **Cards de Saldo** | Glassmorphism | Saldo Inicial, Entradas, Saídas e Saldo Final. |
| **Tabela Consolidada** | Linhas Agrupadas por Dia | Exibir o saldo operacional dia a dia. |
| **Filtro de Período** | Date Range Picker | Alternar entre visão Mensal, Trimestral ou Anual. |

---

## 🏗️ 2. Arquitetura de Dados (Backend Laravel)

O Fluxo de Caixa não possui uma tabela própria; ele é uma **Query Complexa** que une os dados de `receivable_accounts`, `payable_accounts` e `bank_transactions`.

### Lógica da Query (Eloquent Sugerido):
```php
public function getCashFlow(Request $request) {
    $start = $request->start_date;
    $end = $request->end_date;

    // Entradas Projetadas + Realizadas
    $entries = ReceivableAccount::whereBetween('due_date', [$start, $end])->get();

    // Saídas Projetadas + Realizadas
    $exits = PayableAccount::whereBetween('due_date', [$start, $end])->get();

    // Saldo Inicial (Soma de todos os saldos bancários até a data start)
    $initialBalance = BankAccount::sum('balance'); 

    return Inertia::render('Finance/CashFlow', [
        'data' => $this->formatToFlow($entries, $exits, $initialBalance)
    ]);
}

private function formatToFlow($entries, $exits, $initialBalance) {
    // Lógica para formatar os dados em um formato consolidado para o frontend
    // Agrupar por dia, calcular saldos acumulados, etc.
}
```

---

## 3. Componente React (CashFlowView.jsx)
   Utilizaremos o Recharts para o gráfico e uma tabela de alto contraste.

```JavaScript
import React from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';
import { ArrowUpCircle, ArrowDownCircle, Wallet } from 'lucide-react';

export default function CashFlowView({ flowData }) {
return (
<div className="p-8 space-y-8">
<header>
<h1 className="text-2xl font-bold text-white tracking-tight">Fluxo de Caixa</h1>
<p className="text-slate-400 text-sm">Visão consolidada de movimentações e projeções.</p>
</header>

            {/* Gráfico de Performance */}
            <div className="h-64 w-full glass-card rounded-2xl border border-white/10 bg-white/5 p-4">
                <ResponsiveContainer width="100%" height="100%">
                    <LineChart data={flowData.chart}>
                        <CartesianGrid strokeDasharray="3 3" stroke="#ffffff10" />
                        <XAxis dataKey="name" stroke="#64748b" fontSize={12} />
                        <YAxis stroke="#64748b" fontSize={12} />
                        <Tooltip contentStyle={{ backgroundColor: '#0f172a', border: 'none', borderRadius: '8px' }} />
                        <Line type="monotone" dataKey="entradas" stroke="#22d3ee" strokeWidth={3} dot={false} />
                        <Line type="monotone" dataKey="saidas" stroke="#f43f5e" strokeWidth={3} dot={false} />
                    </LineChart>
                </ResponsiveContainer>
            </div>

            {/* Resumo de Saldos */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div className="p-4 rounded-xl border border-white/10 bg-white/5">
                    <span className="text-[10px] text-slate-500 uppercase font-bold">Saldo Inicial</span>
                    <h4 className="text-lg font-bold text-white">R$ 15.000,00</h4>
                </div>
                <div className="p-4 rounded-xl border border-cyan-500/20 bg-cyan-500/5">
                    <span className="text-[10px] text-cyan-500 uppercase font-bold">Total Entradas</span>
                    <h4 className="text-lg font-bold text-cyan-400">+ R$ 8.450,00</h4>
                </div>
                <div className="p-4 rounded-xl border border-rose-500/20 bg-rose-500/5">
                    <span className="text-[10px] text-rose-500 uppercase font-bold">Total Saídas</span>
                    <h4 className="text-lg font-bold text-rose-400">- R$ 3.200,00</h4>
                </div>
                <div className="p-4 rounded-xl border border-white/20 bg-white/10">
                    <span className="text-[10px] text-slate-300 uppercase font-bold">Saldo Final</span>
                    <h4 className="text-lg font-bold text-white font-mono text-xl">R$ 20.250,00</h4>
                </div>
            </div>

            {/* Tabela Diária */}
            <div className="rounded-2xl border border-white/10 bg-white/5 overflow-hidden">
                <table className="w-full text-left text-sm">
                    <thead className="bg-white/5 text-slate-400 uppercase text-[10px] tracking-widest">
                        <tr>
                            <th className="p-4">Data</th>
                            <th className="p-4 text-emerald-400">Entradas (R$)</th>
                            <th className="p-4 text-rose-400">Saídas (R$)</th>
                            <th className="p-4 text-right">Saldo Acumulado (R$)</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-white/5 text-slate-300">
                        <tr className="hover:bg-white/[0.02]">
                            <td className="p-4">08/04/2026</td>
                            <td className="p-4 text-emerald-500">+ 1.200,00</td>
                            <td className="p-4 text-rose-500">- 450,00</td>
                            <td className="p-4 text-right font-bold text-white">15.750,00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );
}
```

---

## 4. Regras de Negócio Fundamentais
Diferença entre Regime de Caixa e Competência:

Competência: Mostra o valor na data em que a conta foi gerada (Venda/Compra).

Caixa: Mostra o valor apenas quando o dinheiro efetivamente entra ou sai do banco. O Fluxo de Caixa do Nexora deve permitir alternar entre as duas visões.

Saldo Previsto vs. Realizado: O sistema deve destacar o que já foi pago/recebido e o que ainda é apenas uma promessa (contas em aberto).

Transferências Internas: Devem ser ignoradas no Fluxo de Caixa consolidado (pois o dinheiro não saiu da empresa, apenas mudou de conta), evitando inflar os números.

---

## 5. Diferencial Nexora (AI Predictive Flow)
A Nexora AI transforma o Fluxo de Caixa em uma ferramenta preditiva:

Simulação de Cenários: "Se eu contratar um novo funcionário com salário de R$ 3.000,00, como ficará meu saldo final daqui a 6 meses?".

Identificação de Sazonalidade: "Notei que nos últimos 3 anos, suas saídas superam as entradas na segunda semana de Dezembro. Sugiro reforçar o caixa agora."

Análise de Quebra de Caixa: Avisa se o saldo projetado chegará a zero em algum ponto do futuro baseado nas contas fixas.

---

## 6. Conclusão
O Fluxo de Caixa é a espinha dorsal da gestão financeira. Com uma implementação robusta e uma interface intuitiva, a Nexora ERP não apenas mostra o que aconteceu, mas também ajuda a planejar o futuro, garantindo que os usuários tenham o controle total sobre suas finanças.
