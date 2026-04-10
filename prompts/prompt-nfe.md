# 📄 Guia de Implementação: Gerenciamento de Notas Fiscais (Nexora ERP)

Este módulo é o centro de comando fiscal do sistema, responsável pela transmissão, consulta de status, cancelamento e correção de documentos fiscais eletrônicos.

---

## 🎨 1. Design e UI (Interface do Usuário)

A interface deve ser focada em **Status de Transmissão**, utilizando cores semafóricas para indicar o sucesso ou erro perante a SEFAZ.

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Monitor de Status** | Ícones Animados | Indicar se a SEFAZ está Online, Instável ou Offline. |
| **Grid de Notas** | Tabela com Badges | Visualizar Numero, Série, Cliente e Status (Autorizada, Rejeitada, Cancelada). |
| **Ações Rápidas** | Grupo de Botões | "Baixar XML", "Imprimir DANFE", "Enviar por E-mail", "Cancelar". |
| **Filtro Fiscal** | Select | Filtrar por Ambiente (Produção/Homologação) e Tipo (NF-e/NFC-e). |

---

## 🏗️ 2. Arquitetura de Dados (Backend Laravel)

O registro da Nota Fiscal deve estar vinculado a uma **Venda** e armazenar as chaves de acesso únicas geradas pelo governo.

### Migration Sugerida:
```php
Schema::create('fiscal_notes', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('sale_id');
    $table->string('invoice_number');           // Número da Nota
    $table->string('series');                   // Série (ex: 1)
    $table->string('access_key', 44)->unique(); // Chave de 44 dígitos
    $table->enum('status', ['draft', 'sent', 'authorized', 'rejected', 'cancelled', 'denied']);
    $table->string('protocol')->nullable();     // Protocolo de autorização
    $table->text('sefaz_message')->nullable();  // Mensagem de erro/retorno da SEFAZ
    $table->string('xml_path')->nullable();     // Caminho do arquivo XML no storage
    $table->timestamps();

    $table->foreign('sale_id')->references('id')->on('sales');
});
```

---

## 3. Componente React (InvoiceManager.jsx)
   Focaremos em uma tabela de monitoramento com foco em ações pós-emissão.

``` JavaScript
import React from 'react';
import { FileText, Download, Send, XCircle, CheckCircle, AlertTriangle } from 'lucide-react';

const StatusBadge = ({ status }) => {
const config = {
authorized: { color: 'text-emerald-400 bg-emerald-500/10', icon: <CheckCircle size={12}/>, label: 'Autorizada' },
rejected: { color: 'text-rose-400 bg-rose-500/10', icon: <AlertTriangle size={12}/>, label: 'Rejeitada' },
cancelled: { color: 'text-slate-400 bg-slate-500/10', icon: <XCircle size={12}/>, label: 'Cancelada' },
};
const item = config[status] || { color: 'text-amber-400 bg-amber-500/10', icon: <FileText size={12}/>, label: 'Pendente' };

    return (
        <span className={`flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-black uppercase border border-current/20 ${item.color}`}>
            {item.icon} {item.label}
        </span>
    );
};

export default function InvoiceManager({ invoices }) {
return (
<div className="p-8 space-y-6">
<header className="flex justify-between items-center">
<div>
<h1 className="text-2xl font-bold text-white tracking-tight">Notas Fiscais</h1>
<p className="text-slate-400 text-sm">Monitoramento e transmissão de documentos eletrônicos.</p>
</div>
<div className="flex gap-3 bg-white/5 p-2 rounded-xl border border-white/10">
<div className="flex items-center gap-2 px-3 border-r border-white/10">
<div className="h-2 w-2 rounded-full bg-emerald-500 animate-pulse" />
<span className="text-[10px] text-slate-300 font-bold uppercase">SEFAZ MG: Online</span>
</div>
<button className="text-xs text-cyan-400 hover:text-white transition-colors px-2">Sincronizar</button>
</div>
</header>

            <div className="rounded-2xl border border-white/10 bg-white/5 overflow-hidden">
                <table className="w-full text-left">
                    <thead className="bg-white/5 text-[10px] text-slate-500 uppercase font-black">
                        <tr>
                            <th className="p-4">Número / Série</th>
                            <th className="p-4">Cliente</th>
                            <th className="p-4">Chave de Acesso</th>
                            <th className="p-4">Status</th>
                            <th className="p-4">Valor</th>
                            <th className="p-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-white/5 text-sm text-slate-300">
                        {invoices.map(inv => (
                            <tr key={inv.id} className="hover:bg-white/[0.02] transition-colors group">
                                <td className="p-4 font-bold text-white">{inv.invoice_number} / {inv.series}</td>
                                <td className="p-4 text-xs">{inv.client_name}</td>
                                <td className="p-4 font-mono text-[10px] text-slate-500 tracking-tighter">{inv.access_key}</td>
                                <td className="p-4"><StatusBadge status={inv.status} /></td>
                                <td className="p-4 font-bold text-white">R$ {inv.amount}</td>
                                <td className="p-4 text-right flex justify-end gap-2">
                                    <button title="Imprimir DANFE" className="p-2 hover:bg-white/10 rounded-lg text-slate-400 hover:text-cyan-400"><FileText size={16}/></button>
                                    <button title="Baixar XML" className="p-2 hover:bg-white/10 rounded-lg text-slate-400 hover:text-emerald-400"><Download size={16}/></button>
                                    <button title="Cancelar Nota" className="p-2 hover:bg-white/10 rounded-lg text-slate-400 hover:text-rose-400"><XCircle size={16}/></button>
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
Validação Pré-Emissão: Antes de enviar para a SEFAZ, o backend deve validar se o cadastro do cliente possui CPF/CNPJ, Endereço e se o produto possui NCM e CFOP válidos.

Armazenamento de XML: Por lei, o XML deve ser guardado por 5 anos. Utilize o Storage do Laravel (S3 ou Local) com subpastas organizadas por ano/mes/cnpj.

Contingência: Se a SEFAZ estiver offline, o sistema deve permitir a emissão em modo contingência (EPEC/FS-DA) conforme as regras tributárias.

Carta de Correção (CC-e): Implementar um modal para envio de texto de correção (sem alterar valores ou datas fiscais).

---

## 5. Diferencial Nexora (IA Fiscal Support)
A Nexora AI ajuda a desmistificar a complexidade tributária:

Tradutor de Rejeição: Se a SEFAZ retornar "Erro 225: Falha no Schema XML", a IA avisa ao usuário: "O CNPJ do cliente está com formato inválido. Por favor, revise o cadastro".

Sugestão de Tributação: Baseado no NCM do produto e UF do cliente, a IA sugere a melhor combinação de Alíquota e CFOP.

Automação de Envio: A IA envia o XML e o PDF automaticamente para o e-mail do cliente e para o contador no fechamento do mês.

---

## 6. Conclusão 
O módulo de Notas Fiscais é o coração fiscal do Nexora ERP, garantindo conformidade, eficiência e uma experiência fluida para os usuários. Com uma interface intuitiva e funcionalidades robustas, o Nexora se posiciona como a solução definitiva para o gerenciamento de documentos fiscais eletrônicos. 
A integração com a SEFAZ e o suporte da IA tornam o processo de emissão, monitoramento e correção de notas fiscais mais simples e eficiente do que nunca.
