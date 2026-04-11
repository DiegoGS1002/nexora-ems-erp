<div>
    @if(!$employee)
        <div class="nx-page-wrapper">
            <div class="nx-alert nx-alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Você não está cadastrado como funcionário no sistema. Entre em contato com o RH.</span>
            </div>
        </div>
    @else
        <div class="nx-page-wrapper" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 100vh;">
            <!-- Page Header -->
            <header class="nx-page-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div>
                    <h1 class="nx-page-title" style="color: #fff;">Batida de Ponto</h1>
                    <p class="nx-page-subtitle" style="color: #94a3b8;">Registro rápido de entrada, intervalos e saída</p>
                </div>
            </header>

            <!-- Main Content -->
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 1.5rem;">

                <!-- Glass Card -->
                <div style="width: 100%; max-width: 480px; padding: 3rem 2rem; border-radius: 2.5rem; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">

                    <!-- Header -->
                    <header style="margin-bottom: 2rem;">
                        <h2 style="color: #94a3b8; font-size: 0.75rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.3em; margin-bottom: 1rem;">
                            Registro de Ponto
                        </h2>

                        <!-- Real-time Clock -->
                        <div
                            x-data="{ time: '{{ now()->format('H:i:s') }}' }"
                            x-init="setInterval(() => {
                                const now = new Date();
                                time = now.toLocaleTimeString('pt-BR');
                            }, 1000)"
                            style="font-size: 3.5rem; font-family: 'Courier New', monospace; font-weight: bold; color: #fff; margin-top: 1rem; font-variant-numeric: tabular-nums;"
                            x-text="time"
                        ></div>

                        <p style="color: #06b6d4; font-size: 0.875rem; font-weight: 500; margin-top: 0.5rem;">
                            {{ now()->locale('pt_BR')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </p>
                    </header>

                    <!-- Employee Info -->
                    <div style="margin-bottom: 2rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 1rem; border: 1px solid rgba(255,255,255,0.05);">
                        <p style="color: #64748b; font-size: 0.75rem; margin-bottom: 0.25rem;">Funcionário</p>
                        <p style="color: #fff; font-weight: 600; font-size: 1rem;">{{ $employee->name }}</p>
                        <p style="color: #94a3b8; font-size: 0.75rem; margin-top: 0.25rem;">{{ $employee->role }}</p>
                    </div>

                    <!-- Clock Button -->
                    @if(!$isJourneyComplete)
                        <button
                            wire:click="registerPonto"
                            wire:loading.attr="disabled"
                            style="position: relative; height: 200px; width: 200px; border-radius: 50%; background: linear-gradient(to bottom right, #0891b2, #1e40af); padding: 4px; box-shadow: 0 0 50px rgba(8,145,178,0.3); transition: all 0.3s; border: none; cursor: pointer; margin: 2rem auto;"
                            onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 0 60px rgba(8,145,178,0.5)';"
                            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 0 50px rgba(8,145,178,0.3)';"
                            onmousedown="this.style.transform='scale(0.95)';"
                            onmouseup="this.style.transform='scale(1.05)';"
                        >
                            <div style="height: 100%; width: 100%; border-radius: 50%; background: #0a0f1d; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; transition: background 0.3s;"
                                 onmouseover="this.style.background='transparent';"
                                 onmouseout="this.style.background='#0a0f1d';">
                                <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="1.5">
                                    <path d="M12 2a5 5 0 0 0-5 5v3H5a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V12a2 2 0 0 0-2-2h-2V7a5 5 0 0 0-5-5z"/><path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                                </svg>
                                <svg wire:loading xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                    <animate attributeName="stroke-dasharray" from="0 100" to="100 0" dur="1.5s" repeatCount="indefinite"/>
                                </svg>
                                <span style="font-size: 0.75rem; font-weight: 900; color: #fff; text-transform: uppercase; letter-spacing: 0.1em;">
                                    {{ $nextAction }}
                                </span>
                            </div>
                        </button>
                    @else
                        <div style="padding: 2rem; background: rgba(16, 185, 129, 0.1); border-radius: 1rem; border: 1px solid rgba(16, 185, 129, 0.3);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" style="margin: 0 auto 1rem;">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <p style="color: #10b981; font-weight: 600; font-size: 1.125rem;">Jornada Completa</p>
                            <p style="color: #94a3b8; font-size: 0.875rem; margin-top: 0.5rem;">Todos os registros do dia foram realizados</p>
                        </div>
                    @endif

                    <!-- Location Info -->
                    <div style="margin-top: 2.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: #64748b; font-size: 0.75rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span x-data="{ location: 'Localizando...' }"
                              x-init="
                                if (navigator.geolocation) {
                                    navigator.geolocation.getCurrentPosition(
                                        (position) => {
                                            $wire.updateLocation(position.coords.latitude, position.coords.longitude);
                                            location = 'Localização identificada';
                                        },
                                        () => {
                                            $wire.locationDenied();
                                            location = 'Localização não autorizada';
                                        }
                                    );
                                } else {
                                    location = 'Navegador não suporta geolocalização';
                                }
                              "
                              x-text="location">
                        </span>
                    </div>

                    <!-- Today's Records -->
                    @if(!empty($todayRecords))
                        <div style="background: rgba(255,255,255,0.05); border-radius: 1.5rem; padding: 1.5rem; border: 1px solid rgba(255,255,255,0.05); margin-top: 2.5rem;">
                            <h3 style="font-size: 0.625rem; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 1rem; letter-spacing: 0.05em;">
                                Batidas de Hoje
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 1rem;">
                                @foreach($todayRecords as $record)
                                    <div style="text-align: center;">
                                        <span style="display: block; font-size: 0.625rem; color: #64748b; margin-bottom: 0.25rem;">
                                            {{ $record['type'] }}
                                        </span>
                                        <span style="color: #fff; font-weight: bold; font-size: 0.875rem; font-family: monospace;">
                                            {{ $record['time'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if($expectedEndTime && !$isJourneyComplete)
                                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                                    <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">Previsão de Término (8h)</p>
                                    <p style="color: #06b6d4; font-weight: 600; font-size: 1rem; font-family: monospace;">{{ $expectedEndTime }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($todayRecord && $todayRecord->worked_hours_formatted)
                        <div style="margin-top: 1rem; padding: 1rem; background: rgba(99, 102, 241, 0.1); border-radius: 1rem; border: 1px solid rgba(99, 102, 241, 0.2);">
                            <p style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.25rem;">Horas Trabalhadas</p>
                            <p style="color: #818cf8; font-weight: bold; font-size: 1.25rem; font-family: monospace;">{{ $todayRecord->worked_hours_formatted }}</p>
                        </div>
                    @endif
                </div>

                <!-- Info Box -->
                <div style="max-width: 480px; width: 100%; margin-top: 2rem; padding: 1.5rem; background: rgba(255,255,255,0.05); border-radius: 1rem; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="display: flex; gap: 0.75rem; align-items: start;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" style="flex-shrink: 0; margin-top: 0.125rem;">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        <div>
                            <p style="color: #fff; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Como funciona?</p>
                            <ul style="color: #94a3b8; font-size: 0.8125rem; line-height: 1.6; list-style: none; padding: 0;">
                                <li style="margin-bottom: 0.5rem;">✓ Clique no botão para registrar cada etapa da jornada</li>
                                <li style="margin-bottom: 0.5rem;">✓ Entrada → Intervalo → Retorno → Saída</li>
                                <li style="margin-bottom: 0.5rem;">✓ Todos os horários são registrados automaticamente</li>
                                <li>✓ Sua localização é capturada para segurança</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notifications -->
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('show-success', (event) => {
                    const toast = document.createElement('div');
                    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 1rem 1.5rem; border-radius: 0.75rem; box-shadow: 0 10px 40px rgba(0,0,0,0.3); z-index: 9999; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; animation: slideIn 0.3s ease-out;';
                    toast.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>${event.message}</span>
                    `;
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.style.animation = 'slideOut 0.3s ease-out';
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                });

                Livewire.on('show-error', (event) => {
                    const toast = document.createElement('div');
                    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 1rem 1.5rem; border-radius: 0.75rem; box-shadow: 0 10px 40px rgba(0,0,0,0.3); z-index: 9999; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; animation: slideIn 0.3s ease-out;';
                    toast.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        <span>${event.message}</span>
                    `;
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.style.animation = 'slideOut 0.3s ease-out';
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                });
            });
        </script>

        <style>
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        </style>
    @endif
</div>
