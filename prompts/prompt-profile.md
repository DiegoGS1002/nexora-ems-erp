# 👤 Guia de Implementação: Perfil do Usuário (Nexora ERP)

Este documento define as funcionalidades e padrões visuais para a página de "Meu Perfil", onde o usuário gerencia suas informações pessoais, preferências de visualização e segurança.

---

## 🎨 1. Interface e UX (User Interface)

A tela deve seguir o padrão de **Cards Laterais** ou **Tabs Superiores**, mantendo o estilo Glassmorphism sobre o fundo escuro (`#0a0f1d`).

| Seção | Conteúdo | Elemento UI |
| :--- | :--- | :--- |
| **Identificação** | Foto, Nome, E-mail, Cargo e Unidade. | Card com Avatar (Gradient). |
| **Segurança** | Alteração de Senha e 2FA. | Form com validação em tempo real. |
| **Preferências** | Tema (Light/Dark) e Notificações. | Toggles (Switches) Ciano. |
| **Atividade** | Últimos logins e dispositivos. | Lista com ícones de monitor/celular. |

---

## 🛠️ 2. Estrutura do Componente React (`UserProfile.jsx`)

Utilizando o `useForm` do Inertia para garantir o processamento de dados e feedback de erros.

```jsx
import React from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { User, Key, Bell, Monitor } from 'lucide-react';

export default function UserProfile() {
    const { auth } = usePage().props;

    const { data, setData, patch, processing, errors } = useForm({
        name: auth.user.name,
        email: auth.user.email,
        current_password: '',
        new_password: '',
    });

    return (
        <div className="max-w-5xl mx-auto p-6 space-y-6">
            <header className="mb-8">
                <h1 className="text-2xl font-bold text-white">Meu Perfil</h1>
                <p className="text-slate-400 text-sm">Gerencie suas informações e segurança da conta.</p>
            </header>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Coluna Esquerda: Avatar e Info Rápida */}
                <div className="glass-card p-6 rounded-2xl border border-white/10 bg-white/5 text-center">
                    <div className="w-24 h-24 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-400 mx-auto mb-4 flex items-center justify-center text-3xl font-bold text-white">
                        {auth.user.name.charAt(0)}
                    </div>
                    <h2 className="text-xl font-bold text-white">{auth.user.name}</h2>
                    <p className="text-cyan-400 text-xs font-mono uppercase tracking-widest">{auth.user.role}</p>
                </div>

                {/* Coluna Direita: Formulários */}
                <div className="md:col-span-2 space-y-6">
                    {/* Card Informações Pessoais */}
                    <section className="glass-card p-8 rounded-2xl border border-white/10 bg-white/5">
                        <div className="flex items-center gap-3 mb-6">
                            <User className="text-cyan-400" size={20} />
                            <h3 className="text-lg font-semibold text-white">Dados Pessoais</h3>
                        </div>
                        <div className="grid grid-cols-1 gap-4">
                            <input 
                                value={data.name} 
                                onChange={e => setData('name', e.target.value)}
                                className="input-nexora" 
                                placeholder="Nome Completo" 
                            />
                            <input 
                                value={data.email} 
                                className="input-nexora opacity-50 cursor-not-allowed" 
                                disabled 
                            />
                        </div>
                    </section>

                    {/* Card Segurança */}
                    <section className="glass-card p-8 rounded-2xl border border-white/10 bg-white/5">
                        <div className="flex items-center gap-3 mb-6">
                            <Key className="text-amber-400" size={20} />
                            <h3 className="text-lg font-semibold text-white">Alterar Senha</h3>
                        </div>
                        <div className="space-y-4">
                            <input type="password" placeholder="Senha Atual" className="input-nexora" />
                            <input type="password" placeholder="Nova Senha" className="input-nexora" />
                            <button className="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all">
                                Atualizar Senha
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    );
}
```
---

## 3. Regras de Negócio e Segurança
   Troca de E-mail: Recomenda-se não permitir a troca direta. Se o usuário mudar o e-mail, o sistema deve enviar um link de verificação para o novo endereço antes de oficializar a mudança.

Histórico de Acesso: O Laravel deve registrar na tabela sessions (ou uma tabela customizada user_logs) o IP e o User-Agent (navegador) de cada login bem-sucedido.

Upload de Avatar: Utilize o Storage do Laravel. O arquivo deve ser redimensionado para 200x200px para economizar espaço e carregar rápido no Dashboard.

Preferência de Tema: Salve a escolha do usuário (dark ou light) no banco de dados e também no localStorage para evitar o "flash" de tela branca ao carregar a página.

---

## 4. Diferencial para o Nexora (IA Integration)
Como estamos usando um Agente de IA, o perfil do usuário pode ter uma seção de "Insights do Usuário":

A IA analisa a produtividade (ex: "Você finalizou 15 vendas hoje, 10% a mais que ontem").

Sugestões de atalhos baseadas no que o usuário mais acessa.

---
