# 📊 Guia de Implementação: Plano de Contas (Nexora ERP)

O Plano de Contas é a estrutura ordenada que categoriza todas as operações financeiras do sistema. No Nexora, utilizamos uma estrutura **hierárquica (árvore)** para permitir análises detalhadas e consolidadas.

---

## 🎨 1. Interface e Experiência (UI/UX)

Diferente de uma tabela comum, o Plano de Contas deve ser visualizado como uma **Tree View** (Árvore).

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Níveis** | Indentação Progressiva | Diferenciar Contas Sintéticas (Grupos) de Analíticas (Lançáveis). |
| **Status** | Badge Ciano/Cinza | Identificar se a categoria está ativa para novos lançamentos. |
| **Natureza** | Cores: Verde (Receita) / Vermelho (Despesa) | Identificação visual rápida do fluxo. |

---

## 🏗️ 2. Estrutura de Dados (Backend Laravel)

Para suportar níveis infinitos (Ex: 1 -> 1.1 -> 1.1.01), utilizaremos o conceito de **Parent-Child**.

### Migration Sugerida:
```php
Schema::create('chart_of_accounts', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('parent_id')->nullable(); // Relaciona com a conta pai
    $table->string('code');      // Ex: 1.01.002
    $table->string('name');      // Ex: Aluguel
    $table->enum('type', ['receita', 'despesa', 'ativo', 'passivo']);
    $table->boolean('is_selectable')->default(true); // Se pode receber lançamentos
    $table->timestamps();

    $table->foreign('parent_id')->references('id')->on('chart_of_accounts');
});

```

---

## 3. Componente React (AccountTree.jsx)
   Para o frontend, utilizaremos recursividade ou uma biblioteca de Tree View para renderizar os níveis.

```JavaScript
import React from 'react';
import { ChevronRight, Folder, FileText, Plus } from 'lucide-react';

const AccountItem = ({ account }) => (
<div className="group border-b border-white/5 hover:bg-white/[0.02] transition-all">
<div className="flex items-center py-3 px-4 gap-3">
<span className="text-slate-500 font-mono text-xs w-20">{account.code}</span>

            {account.children?.length > 0 ? (
                <Folder size={16} className="text-cyan-500" />
            ) : (
                <FileText size={16} className="text-slate-400" />
            )}

            <span className={`flex-1 text-sm ${account.children?.length > 0 ? 'font-bold text-white' : 'text-slate-300'}`}>
                {account.name}
            </span>

            <div className="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                <button title="Adicionar Subconta" className="p-1 hover:text-cyan-400"><Plus size={14}/></button>
            </div>
        </div>
        
        {/* Renderização Recursiva dos Filhos */}
        {account.children?.map(child => (
            <div className="pl-6" key={child.id}>
                <AccountItem account={child} />
            </div>
        ))}
    </div>
);

```

---

## 4. Regras de Negócio Fundamentais
Contas Sintéticas vs. Analíticas:

Sintéticas: São os grupos (Ex: 1. Receitas). Elas não recebem lançamentos diretos, apenas somam os valores das contas abaixo delas.

Analíticas: São as contas finais (Ex: 1.1. Venda de Produtos). É aqui que o dinheiro é registrado.

Imutabilidade de Código: Uma vez que uma conta possui lançamentos financeiros vinculados, seu código e natureza (Receita/Despesa) não devem ser alterados para não corromper os relatórios históricos.

Sugestão de Estrutura Inicial (Seed):

1. Receitas (Vendas, Serviços, Rendimentos).

2. Custos (Matéria-prima, Mercadorias).

3. Despesas Operacionais (Aluguel, Energia, Salários).

4. Impostos (ICMS, PIS, COFINS).

---

## 5. Diferencial Nexora (IA Insights)
Ao implementar o Plano de Contas, sua IA de Suporte pode oferecer:

Auto-Classificação: Ao ler a descrição de uma nota fiscal (Ex: "Energia Elétrica 04/2026"), a IA sugere automaticamente a conta 3.1.002 - Utilidades.

Análise de Desvio: "Nexora AI detectou que os gastos na conta 'Manutenção' subiram 20% em relação ao mês anterior".

---

## Conclusão
O Plano de Contas é a espinha dorsal do módulo financeiro. Sua implementação cuidadosa,com foco em usabilidade e regras de negócio, garantirá que o Nexora ERP ofereça uma experiência robusta e intuitiva para os usuários, facilitando a gestão financeira e a tomada de decisões estratégicas.
