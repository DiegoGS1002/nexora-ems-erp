# Guia de Implementação: Batida de Ponto (Nexora ERP)

Este módulo é a interface de autosserviço para o colaborador registrar suas entradas, intervalos e saídas, com suporte a geolocalização e segurança de IP.

---

## 1. Interface e UX (User Interface)

Diferente do "Monitoramento de Jornada" (usado pelo RH), esta página deve ser minimalista, focada em um **Relógio Digital** de alta visibilidade e no botão de ação.

| Elemento | Estilo | Função |
| :--- | :--- | :--- |
| **Relógio Central** | Fonte Mono / Neon | Exibir hora oficial do servidor (não do cliente). |
| **Botão de Registro** | Circular / Grande | O clique principal que dispara a gravação do ponto. |
| **Mapa de Localização** | Miniatura (Leaflet/Google) | Confirmar onde o ponto está sendo batido. |
| **Histórico do Dia** | Timeline Vertical | Mostrar as batidas já realizadas hoje para conferência. |

---

## 🏗2. Arquitetura de Dados (Backend Laravel)

O registro deve ser gravado na tabela `time_records`. É crucial capturar o IP e o User-Agent para auditoria.

### Endpoint de Registro (`TimeRecordController.php`):
```php
public function store(Request $request) {
    // Validação de IP ou Cerca Eletrônica (opcional)
    
    $record = TimeRecord::create([
        'employee_id' => auth()->id(),
        'date' => now()->toDateString(),
        'clock_in' => now(), // Sempre usar a hora do servidor
        'ip_address' => $request->ip(),
        'latitude' => $request->lat,
        'longitude' => $request->lng,
    ]);

    return back()->with('success', 'Ponto registrado com sucesso!');
}

```

---

## 3. Componente React (ClockInPage.jsx)
   Um componente que foca na experiência "um clique".

``` JavaScript
import React, { useState, useEffect } from 'react';
import { Clock, MapPin, CheckCircle, Fingerprint } from 'lucide-react';

export default function ClockInPage() {
const [time, setTime] = useState(new Date());

    // Atualiza o relógio a cada segundo
    useEffect(() => {
        const timer = setInterval(() => setTime(new Date()), 1000);
        return () => clearInterval(timer);
    }, []);

    const handleClockIn = () => {
        // Lógica de envio para o Laravel via Inertia
        console.log("Ponto registrado em: ", time.toLocaleTimeString());
    };

    return (
        <div className="flex flex-col items-center justify-center min-h-[80vh] p-6">
            <div className="glass-card w-full max-w-md p-8 rounded-[2.5rem] border border-white/10 bg-white/5 text-center shadow-2xl">
                <header className="mb-8">
                    <h1 className="text-slate-400 text-xs font-black uppercase tracking-[0.3em]">Registro de Ponto</h1>
                    <div className="text-5xl font-mono font-bold text-white mt-4 tabular-nums">
                        {time.toLocaleTimeString('pt-BR')}
                    </div>
                    <p className="text-cyan-500 text-sm font-medium mt-2">
                        {time.toLocaleDateString('pt-BR', { dateStyle: 'full' })}
                    </p>
                </header>

                {/* Botão Principal de Batida */}
                <button 
                    onClick={handleClockIn}
                    className="group relative h-48 w-48 rounded-full bg-gradient-to-br from-cyan-600 to-blue-700 p-1 shadow-[0_0_50px_rgba(8,145,178,0.3)] hover:shadow-cyan-500/50 transition-all active:scale-95"
                >
                    <div className="h-full w-full rounded-full bg-[#0a0f1d] flex flex-col items-center justify-center gap-2 group-hover:bg-transparent transition-colors">
                        <Fingerprint size={48} className="text-cyan-400 group-hover:text-white" />
                        <span className="text-xs font-black text-white uppercase tracking-widest">Registrar</span>
                    </div>
                </button>

                <div className="mt-10 space-y-4">
                    <div className="flex items-center justify-center gap-2 text-slate-500 text-xs">
                        <MapPin size={14} /> 
                        <span>Ubá, Minas Gerais - Localização Identificada</span>
                    </div>
                    
                    {/* Histórico Simples */}
                    <div className="bg-white/5 rounded-2xl p-4 border border-white/5">
                        <h3 className="text-[10px] text-slate-500 font-bold uppercase mb-3">Batidas de Hoje</h3>
                        <div className="flex justify-around">
                            <div className="text-center">
                                <span className="block text-[10px] text-slate-500">Entrada</span>
                                <span className="text-white font-bold text-sm">08:02</span>
                            </div>
                            <div className="text-center border-x border-white/10 px-4">
                                <span className="block text-[10px] text-slate-500">Almoço</span>
                                <span className="text-slate-600 font-bold text-sm">--:--</span>
                            </div>
                            <div className="text-center">
                                <span className="block text-[10px] text-slate-500">Saída</span>
                                <span className="text-slate-600 font-bold text-sm">--:--</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
```

---

## 4. Regras de Negócio Fundamentais
Segurança de Horário: O horário registrado nunca deve vir do navegador do usuário (facilmente manipulável). O backend deve ignorar qualquer valor de hora enviado e usar now() do servidor.

Limite de Batida: Impedir batidas duplicadas em um intervalo muito curto (ex: menos de 1 minuto) para evitar erros de duplo clique.

Captura de Localização: Solicitar permissão de GPS no navegador. Caso o usuário negue, o sistema pode registrar o ponto, mas deve marcar um alerta de "Sem Coordenadas" para o RH.

Offline Support (PWA): Se o usuário estiver sem internet, o sistema pode armazenar a batida no localStorage e sincronizar assim que houver conexão (importante para motoristas ou técnicos de campo).

---

## 5. Diferencial Nexora (AI Check-in)
A Nexora AI adiciona uma camada de inteligência:

Lembrete Ativo: Se o colaborador logar no ERP e não tiver batido o ponto, a IA exibe um alerta flutuante: "Notei que você começou a trabalhar mas ainda não registrou o ponto. Deseja registrar agora?".

Previsão de Saída: Com base na entrada e no tempo de intervalo, a IA mostra: "Sua jornada de 8h se completa às 17:35. Bom trabalho!".

Análise de Rota: Para funcionários externos, a IA valida se o ponto foi batido no local correto do cliente programado no dia.

---

## 6. Conclusão
O módulo de Batida de Ponto é uma peça-chave para a gestão de jornada no Nexora ERP. Com uma interface intuitiva, segurança robusta e o diferencial da IA, ele não apenas facilita o registro de horas, mas também engaja o colaborador a manter uma rotina organizada e transparente. 
A implementação cuidadosa desses detalhes garantirá uma experiência fluida e confiável para todos os usuários.
