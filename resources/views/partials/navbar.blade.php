@vite(['resources/css/app.css', 'resources/js/app.js'])

<header class="container">
    <nav class="menu flex justify-between items-center">
        <div class="logo">
            <a href="{{ url('/') }}">
                <img src="{{ app()->environment() === 'production' ? secure_asset('images/logo.png') : asset('images/logo.png') }}" alt="Logo">
            </a>
        </div>

        <ul class="flex items-center">
            <li><a href="{{ route('home') }}">Início</a></li>

            <!-- DROPDOWN -->
              <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="dashboard">Dashboard ▾</a>

                <ul id="dropdown-dashboard" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="{{ route('dashboard.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Visão Geral
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('financial_reports.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Indicadores
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="cadastro">Cadastro ▾</a>
                <ul id="dropdown-cadastro" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50 min-w-[140px]" style="display: none;">
                    <li>
                        <a href="{{ route('products.index') }}" class="block w-full px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Produtos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('suppliers.index') }}" class="block w-full px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Fornecedores
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('clients.index') }}" class="block w-full px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Clientes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employee.index') }}" class="block w-full px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Funcionários
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('role.index') }}" class="block w-full px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Funções
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vehicle.index') }}" class="block w-full px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Veículos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('baccarat_accounts.index') }}" class="block w-full px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Depósitos
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="producao">Produção ▾</a>

                <ul id="dropdown-producao" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="{{ route('dashboard.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('production_orders.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Ordens de Produção
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Ficha Tecnica
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="estoque">Estoque ▾</a>

                <ul id="dropdown-estoque" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="{{ route('stock.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Movimentação
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('stock.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Inventários
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('stock.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Transferência
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="vendas">Vendas ▾</a>

                <ul id="dropdown-vendas" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="{{ route('requests.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Pedidos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('visits.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            CRM
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales_report.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Relatórios
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="compras">Compras ▾</a>

                <ul id="dropdown-compras" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="#" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Solicitações de compra
                        </a>
                    </li>
                    <li>
                        <a href="#" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Pedidos de compra
                        </a>
                    </li>
                    <li>
                        <a href="#" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Cotação com fornecedores
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="pedidos">Fiscal ▾</a>

                <ul id="dropdown-pedidos" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="{{ route('entrance.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            NF-e
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('entrance.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Entradas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('exit.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Saídas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('financial_reports.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Apuração
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="financeiro">Financeiro ▾</a>

                <ul id="dropdown-financeiro" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="{{ route('plans_of_accounts.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Plano de Contas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('baccarat_accounts.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Contas Bancárias
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('accounts_payable.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Formas de Pagamento
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('accounts_payable.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Contas a Pagar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('accounts_receivable.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Contas a Receber
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cash_flow.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Fluxo de Caixa
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('financial_reports.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Relatórios Financeiros
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('financial_reports.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            DRE
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="recursos">Recursos Humanos ▾</a>

                <ul id="dropdown-recursos" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="{{ route('working_day.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Jornada de Trabalho
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('stitch_beat.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Batida de Ponto
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payroll.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Folha de Pagamento
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employee_management.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Gerenciamento de Funcionários
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rh_reports.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Relatórios de RH
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a href="#" class="cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="transporte">Transporte ▾</a>

                <ul id="dropdown-transporte" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="{{ route('vehicle.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                           Frotas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('routing.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Roteirização
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('scheduling_of_deliveries.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Entregas
                        </a>
                    </li>
                </ul>
            </li>
            <li class="ml-auto relative">
                <a href="#" class="flex items-center justify-end cursor-pointer" onclick="toggleDropdown(event)" data-dropdown="usuario"><img src="{{ app()->environment() === 'production' ? secure_asset('images/usuario.png') : asset('images/usuario.png') }}" alt="Usuário" class="w-8 h-8 rounded-full">▾</a>

                <ul id="dropdown-usuario" class="dropdown-menu absolute left-0 mt-2 flex flex-col gap-1 shadow-lg rounded-md p-2 z-50" style="display: none;">
                    <li>
                        <a href="{{ route('profile.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Perfil
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Usuários
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('permissions.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Permissões
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logs.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                        Logs
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('configuration.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Configurações
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('configuration.index') }}" class="block px-3 py-2 text-xs text-black hover:bg-gray-200 rounded">
                            Sair
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
