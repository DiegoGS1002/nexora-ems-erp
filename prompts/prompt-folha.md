# 👥 Guia de Implementação: Folha de Pagamento (Nexora ERP)

Este módulo automatiza o cálculo de salários, benefícios, descontos e encargos dos colaboradores, garantindo a conformidade com as obrigações trabalhistas.

---

## 🎨 1. Design e UI (Interface do Usuário)

A página de folha de pagamento exige clareza absoluta sobre o que é **Provento** (ganho) e o que é **Desconto**.

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Resumo do Mês** | Cards Informativos | Total Bruto, Total de Descontos e Valor Líquido a Pagar. |
| **Lista de Colaboradores** | Tabela com Avatares | Visualizar status da folha individual (Aberta/Fechada). |
| **Modal de Holerite** | Glassmorphism | Visualização detalhada antes da emissão do PDF. |
| **Status de Pagamento** | Badge Dinâmico | Pendente, Agendado ou Pago. |

---

## 🏗️ 2. Arquitetura de Dados (Backend Laravel)

A folha de pagamento é composta pelo cabeçalho (Competência/Mês) e pelos itens (verbas).

### Migration Sugerida:
```php
Schema::create('payrolls', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('employee_id');
    $table->string('reference_month'); // Ex: 04/2026
    $table->decimal('base_salary', 15, 2);
    $table->decimal('total_earnings', 15, 2); // Soma de todos os proventos
    $table->decimal('total_deductions', 15, 2); // Soma de todos os descontos
    $table->decimal('net_salary', 15, 2);      // Valor líquido final
    $table->enum('status', ['draft', 'closed', 'paid'])->default('draft');
    $table->timestamps();

    $table->foreign('employee_id')->references('id')->on('employees');
});

Schema::create('payroll_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('payroll_id');
    $table->string('description'); // Ex: Horas Extras, INSS, Vale Transporte
    $table->enum('type', ['earning', 'deduction']);
    $table->decimal('amount', 15, 2);
    $table->timestamps();
});
```

---

## 3. Componente React (PayrollManager.jsx)
   Focaremos em uma interface que permita o fechamento em massa e a edição individual.

```JavaScript
import React from 'react';
import { Users, FileBadge, Download, CheckCircle2, AlertCircle } from 'lucide-react';

export default function PayrollManager({ payrolls }) {
return (
<div className="p-8 space-y-6">
<header className="flex justify-between items-center">
<div>
<h1 className="text-2xl font-bold text-white tracking-tight">Folha de Pagamento</h1>
<p className="text-slate-400 text-sm font-medium">Competência: Abril/2026</p>
</div>
<div className="flex gap-3">
<button className="bg-white/5 border border-white/10 text-white px-4 py-2 rounded-lg hover:bg-white/10 transition-all">
Gerar Relatórios
</button>
<button className="bg-gradient-to-r from-emerald-600 to-teal-500 text-white px-6 py-2 rounded-lg font-bold shadow-lg shadow-emerald-500/20">
Fechar Folha do Mês
</button>
</div>
</header>

            {/* Grid de Totais */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="glass-card p-6 rounded-2xl border border-white/10 bg-white/5">
                    <p className="text-[10px] text-slate-500 uppercase font-black">Total Proventos</p>
                    <h3 className="text-2xl font-bold text-white mt-1">R$ 142.500,00</h3>
                </div>
                <div className="glass-card p-6 rounded-2xl border border-rose-500/20 bg-rose-500/5">
                    <p className="text-[10px] text-rose-500 uppercase font-black">Total Descontos</p>
                    <h3 className="text-2xl font-bold text-rose-400 mt-1">R$ 38.200,00</h3>
                </div>
                <div className="glass-card p-6 rounded-2xl border border-cyan-500/20 bg-cyan-500/5 shadow-2xl shadow-cyan-500/10">
                    <p className="text-[10px] text-cyan-400 uppercase font-black">Líquido a Pagar</p>
                    <h3 className="text-2xl font-bold text-white mt-1 font-mono">R$ 104.300,00</h3>
                </div>
            </div>

            {/* Lista de Funcionários */}
            <div className="rounded-2xl border border-white/10 bg-white/5 overflow-hidden backdrop-blur-md">
                <table className="w-full text-left">
                    <thead className="bg-white/5 text-xs text-slate-500 uppercase font-bold">
                        <tr>
                            <th className="p-4">Colaborador</th>
                            <th className="p-4">Salário Base</th>
                            <th className="p-4">Proventos</th>
                            <th className="p-4">Descontos</th>
                            <th className="p-4">Líquido</th>
                            <th className="p-4 text-center">Status</th>
                            <th className="p-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-white/5 text-sm text-slate-300">
                        {payrolls.map(p => (
                            <tr key={p.id} className="hover:bg-white/[0.02] transition-colors group">
                                <td className="p-4">
                                    <div className="flex items-center gap-3">
                                        <div className="h-8 w-8 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-[10px] font-bold text-white">
                                            {p.employee.initials}
                                        </div>
                                        <div>
                                            <div className="font-bold text-white text-xs">{p.employee.name}</div>
                                            <div className="text-[10px] text-slate-500 uppercase">{p.employee.role}</div>
                                        </div>
                                    </div>
                                </td>
                                <td className="p-4 text-slate-400">R$ {p.base_salary}</td>
                                <td className="p-4 text-emerald-400 font-medium">+ R$ {p.total_earnings}</td>
                                <td className="p-4 text-rose-400 font-medium">- R$ {p.total_deductions}</td>
                                <td className="p-4 font-bold text-white">R$ {p.net_salary}</td>
                                <td className="p-4 text-center">
                                    <span className="px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase">Fechada</span>
                                </td>
                                <td className="p-4 text-right">
                                    <button className="p-2 text-slate-500 hover:text-cyan-400"><Download size={16}/></button>
                                </td>
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
Integração Financeira: Ao "Fechar a Folha", o sistema deve criar automaticamente uma entrada na tabela de Contas a Pagar para cada funcionário (ou uma única conta consolidada), com vencimento definido (Ex: 5º dia útil).

Cálculos de Encargos: O sistema deve calcular automaticamente INSS e IRRF baseado em tabelas configuráveis no módulo de Configurações Gerais.

Proventos Variáveis: Permitir o lançamento de Horas Extras, Comissões (integradas às vendas do usuário) e Bônus.

Emissão de Holerite: Gerar PDF profissional com o logotipo da empresa (configurado no módulo Empresa) e a listagem de todos os itens do payroll.

---

## 5. Diferencial Nexora (IA HR)
A Nexora AI pode transformar a gestão de pessoas:

Análise de Turnover: "Notei que a rotatividade no setor comercial aumentou. Deseja analisar o histórico salarial?".

Cálculo Preditivo de Rescisão: "Simule uma rescisão para o colaborador X baseado na data de hoje e saldo de férias".

Sugestão de Comissionamento: "Baseado nas metas batidas, a IA sugere um bônus de produtividade de 5% para o colaborador Y".

---

## 6. Conclusão
A implementação da folha de pagamento no Nexora ERP é um passo crucial para oferecer uma solução completa de gestão empresarial. Com uma interface intuitiva, cálculos automatizados e integração com outros módulos, a folha de pagamento não só simplifica a rotina do RH, mas também garante conformidade e eficiência. A IA HR é o diferencial que torna a gestão de pessoas mais estratégica, permitindo insights valiosos para a tomada de decisões.
