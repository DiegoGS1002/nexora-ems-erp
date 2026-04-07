# 🔔 Guia de Implementação: Central de Notificações (Nexora ERP)

Este guia detalha a criação de um menu flutuante (Popover) para exibição de alertas em tempo real, utilizando a estética Glassmorphism do sistema.

---

## 🎨 1. Design e UX (User Experience)

O componente deve ser discreto, acionado por um ícone de sino no Header, e apresentar as notificações de forma cronológica.

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Trigger (Sino)** | Ícone com Badge Vermelho | Indica a quantidade de mensagens não lidas. |
| **Painel Flutuante** | Glassmorphism (`backdrop-blur`) | Sobrepõe o conteúdo com transparência. |
| **Categorias** | Ícones Coloridos | Azul (Info), Amarelo (Aviso), Verde (Sucesso). |
| **Ações Rápida** | "Marcar como lida" | Limpa o alerta sem fechar o menu. |

---

## 🏗️ 2. Arquitetura de Dados (Laravel)

Utilizaremos o sistema nativo de notificações do Laravel para suportar múltiplos canais (Database e Broadcast).

### Comando para Criar a Tabela:
```bash
php artisan notifications:table
php artisan migrate
```
## 3. Componente React (NotificationDropdown.jsx)
Utilizando Headless UI (opcional) ou estado simples do React para controlar o popover.

```jsx
import React, { useState } from 'react';
import { Bell, Package, DollarSign, AlertCircle, Check } from 'lucide-react';

export default function NotificationDropdown() {
    const [isOpen, setIsOpen] = useState(false);
    const [notifications, setNotifications] = useState([
        { id: 1, type: 'stock', text: 'Produto "Cimento CP II" atingiu estoque crítico.', time: '5 min atrás', read: false },
        { id: 2, type: 'sale', text: 'Nova venda gerencial finalizada: R$ 1.250,00', time: '12 min atrás', read: false },
    ]);

    const unreadCount = notifications.filter(n => !n.read).length;

    return (
        <div className="relative">
            {/* Ícone de Gatilho */}
            <button 
                onClick={() => setIsOpen(!isOpen)}
                className="relative p-2 text-slate-400 hover:text-cyan-400 transition-colors"
            >
                <Bell size={22} />
                {unreadCount > 0 && (
                    <span className="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-[#0a0f1d]">
                        {unreadCount}
                    </span>
                )}
            </button>

            {/* Painel Flutuante */}
            {isOpen && (
                <div className="absolute right-0 mt-4 w-80 rounded-2xl border border-white/10 bg-[#0a0f1d]/95 p-2 backdrop-blur-xl shadow-2xl z-[100]">
                    <div className="flex items-center justify-between p-3 border-b border-white/5">
                        <h3 className="text-sm font-bold text-white">Notificações</h3>
                        <button className="text-[10px] text-cyan-400 hover:underline font-medium uppercase">Limpar tudo</button>
                    </div>

                    <div className="max-h-96 overflow-y-auto py-2">
                        {notifications.length === 0 ? (
                            <p className="p-4 text-center text-xs text-slate-500">Nenhuma notificação nova.</p>
                        ) : (
                            notifications.map(n => (
                                <div key={n.id} className="flex gap-3 p-3 rounded-xl hover:bg-white/5 transition-colors cursor-pointer group">
                                    <div className={`mt-1 h-8 w-8 rounded-full flex items-center justify-center shrink-0 
                                        ${n.type === 'stock' ? 'bg-amber-500/10 text-amber-500' : 'bg-blue-500/10 text-blue-500'}`}>
                                        {n.type === 'stock' ? <Package size={16} /> : <DollarSign size={16} />}
                                    </div>
                                    <div className="flex-1">
                                        <p className="text-xs text-slate-200 leading-tight">{n.text}</p>
                                        <span className="text-[10px] text-slate-500 mt-1 block">{n.time}</span>
                                    </div>
                                    <button className="opacity-0 group-hover:opacity-100 p-1 text-slate-500 hover:text-emerald-400 transition-opacity">
                                        <Check size={14} />
                                    </button>
                                </div>
                            ))
                        )}
                    </div>

                    <div className="p-2 border-t border-white/5">
                        <button className="w-full py-2 text-center text-xs text-slate-400 hover:text-white transition-colors">
                            Ver todas as atividades
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
```

---

## Integração com Regras de Negócio
Estoque Negativo: Se a configuração de "Venda Sem Estoque" estiver ativa, o sistema deve gerar uma notificação para o almoxarifado toda vez que um saldo ficar negativo.

Licença Nexora: 5 dias antes do vencimento, o sistema deve disparar notificações diárias no painel flutuante, além do modal central.

Fiscal: Se uma nota fiscal for rejeitada pela SEFAZ, o erro deve aparecer instantaneamente aqui para o usuário.

---

## Diferencial Nexora (AI Push)
A Nexora AI pode filtrar o que é relevante para cada usuário:

Para o Vendedor: Notificações de promoções ativas e metas atingidas.

Para o Admin: Alertas de tentativas de login suspeitas e resumos financeiros de hora em hora.

---
## Conclusão
A implementação de uma Central de Notificações eficiente e visualmente atraente é crucial para manter os usuários informados e engajados. Com a abordagem descrita, o Nexora ERP se destacará pela sua capacidade de entregar informações críticas de forma rápida e elegante, alinhada com as necessidades do negócio e a experiência do usuário.

