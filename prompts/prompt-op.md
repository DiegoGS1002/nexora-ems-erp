# Guia de Implementação: Ordem de Produção (Nexora ERP)

Este módulo gerencia o processo de fabricação, rastreando o consumo de componentes, tempo de execução e a entrada final do produto acabado no estoque.

---

## 1. Design e UI (Interface do Usuário)

A interface deve ser focada em **Etapas**. O gestor precisa visualizar o gargalo da produção em tempo real.

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Quadro Kanban** | Colunas (Aguardando, Produzindo, Finalizado) | Arrastar OPs para mudar o status rapidamente. |
| **Barra de Progresso** | Gradiente Ciano/Azul | Indicar a porcentagem de conclusão baseada nas tarefas. |
| **Lista de Insumos** | Tabela Compacta | Mostrar o que será consumido do estoque para fabricar o item. |
| **Cronômetro (Timer)** | Display Digital | Registrar o tempo real gasto em cada fase da produção. |

---

## 2. Arquitetura de Dados (Backend Laravel)

Uma Ordem de Produção precisa de uma **Estrutura de Produto (BOM - Bill of Materials)** para saber o que consumir.

### Migration Sugerida:
```php
Schema::create('production_orders', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_id');          // Produto a ser fabricado
    $table->decimal('target_quantity', 15, 3);         // Qtd planejada
    $table->decimal('produced_quantity', 15, 3)->default(0); // Qtd já feita
    $table->dateTime('start_date')->nullable();
    $table->dateTime('end_date')->nullable();
    $table->enum('status', ['planned', 'in_progress', 'paused', 'completed', 'cancelled']);
    $table->decimal('estimated_cost', 15, 2);          // Custo previsto (Insumos + Mão de obra)
    $table->timestamps();

    $table->foreign('product_id')->references('id')->on('products');
});

Schema::create('production_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('production_order_id');
    $table->unsignedBigInteger('component_id');        // Matéria-prima consumida
    $table->decimal('required_qty', 15, 3);            // Qtd necessária por unidade
    $table->decimal('consumed_qty', 15, 3)->default(0); 
    $table->timestamps();
});
```
---

## 3. Componente React (ProductionKanban.jsx)
   Utilizaremos uma visão de colunas para facilitar o gerenciamento pelo encarregado de fábrica.

``` JavaScript
import React from 'react';
import { Play, CheckCircle, Clock, Layers, AlertCircle } from 'lucide-react';

const OPCard = ({ op }) => (
<div className="p-4 rounded-xl border border-white/10 bg-white/5 hover:border-cyan-500/50 transition-all cursor-grab active:cursor-grabbing">
<div className="flex justify-between items-start mb-3">
<span className="text-[10px] font-mono text-cyan-400">#OP-{op.id}</span>
<span className={`h-2 w-2 rounded-full ${op.status === 'in_progress' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-500'}`} />
</div>

        <h4 className="text-sm font-bold text-white mb-1">{op.product_name}</h4>
        <div className="flex items-center gap-2 text-[10px] text-slate-500 uppercase font-bold">
            <Layers size={12} /> {op.produced_quantity} / {op.target_quantity} UN
        </div>

        <div className="mt-4 flex items-center justify-between">
            <div className="flex -space-x-2">
                {/* Avatares dos operadores */}
                <div className="h-6 w-6 rounded-full border-2 border-[#0a0f1d] bg-slate-700 text-[8px] flex items-center justify-center text-white">JD</div>
            </div>
            <button className="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-400">
                <Play size={14} />
            </button>
        </div>
    </div>
);

export default function ProductionKanban({ orders }) {
const columns = ['planned', 'in_progress', 'completed'];

    return (
        <div className="p-8 h-screen flex flex-col">
            <header className="mb-8 flex justify-between items-center">
                <div>
                    <h1 className="text-2xl font-bold text-white">Chão de Fábrica</h1>
                    <p className="text-slate-400 text-sm">Controle de Ordens de Produção e Insumos.</p>
                </div>
                <button className="bg-cyan-600 px-6 py-2 rounded-lg font-bold text-white hover:bg-cyan-500 transition-all">
                    + Nova OP
                </button>
            </header>

            <div className="flex-1 flex gap-6 overflow-x-auto pb-4">
                {columns.map(col => (
                    <div key={col} className="min-w-[300px] flex-1 flex flex-col rounded-2xl bg-white/[0.02] border border-white/5">
                        <div className="p-4 border-b border-white/5 flex items-center justify-between">
                            <h3 className="text-xs font-black uppercase text-slate-500 tracking-widest">{col.replace('_', ' ')}</h3>
                            <span className="text-[10px] bg-white/5 px-2 py-0.5 rounded text-slate-400">
                                {orders.filter(o => o.status === col).length}
                            </span>
                        </div>
                        <div className="p-3 space-y-3 overflow-y-auto">
                            {orders.filter(o => o.status === col).map(op => (
                                <OPCard key={op.id} op={op} />
                            ))}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
```

---

## 4. Regras de Negócio Fundamentais
Explosão de Insumos: Ao iniciar uma OP, o sistema deve verificar se há saldo de todos os componentes na tabela stock. Se não houver, deve emitir um alerta de Falta de Insumo.

Baixa Automática e Entrada de Acabado: * Ao finalizar a OP, o sistema deve dar saída automática nos componentes consumidos.

Simultaneamente, deve dar entrada no saldo do Produto Acabado.

Custo Real vs. Estimado: O sistema deve registrar o custo dos insumos no momento da produção para comparar se a fabricação foi mais cara ou barata do que o planejado.

Rastreabilidade (Lote): Cada OP deve gerar ou vincular um número de Lote, permitindo saber quais clientes compraram produtos de uma produção específica (vital para recalls).

---

## 5. Diferencial Nexora (IA Factory Intelligence)
A Nexora AI atua como um mestre de obras digital:

Previsão de Gargalo: "O setor de Montagem está com 5 OPs atrasadas. Sugiro redistribuir operadores do setor de Pintura".

Otimização de Matéria-Prima: "Baseado no histórico, você está perdendo 5% de tecido na produção. Deseja revisar o layout de corte?".

Cálculo de Lead Time: "A IA estima que esta produção levará 4 horas e 20 minutos baseada na performance média dos últimos 30 dias".

---

## 6. Conclusão
A implementação do módulo de Ordem de Produção no Nexora ERP é crucial para otimizar o processo fabril, garantindo eficiência, controle de custos e rastreabilidade. Com uma interface intuitiva e regras de negócio robustas, o gestor de produção terá as ferramentas necessárias para tomar decisões informadas e manter a fábrica operando no seu melhor desempenho. A integração da IA Factory Intelligence eleva o sistema a um novo patamar, oferecendo insights valiosos para antecipar problemas e otimizar recursos. 
A Nexora ERP se posiciona como uma solução completa e inovadora para a gestão industrial, pronta para atender às demandas do mercado moderno. 
