<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ModulePageController extends Controller
{
    private static array $modules = [

        'dashboard' => [
            'name'        => 'Dashboard',
            'slug'        => 'dashboard',
            'description' => 'Painel de controle e indicadores de desempenho',
            'color'       => '#3B82F6',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
            'items' => [
                ['title' => 'Visão Geral',      'description' => 'Painel principal com métricas do sistema',   'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>', 'route' => 'dashboard.index'],
                ['title' => 'Indicadores KPI',  'description' => 'Relatórios e análise de desempenho',         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'route' => 'dashboard.kpi'],
            ],
        ],

        'cadastro' => [
            'name'        => 'Cadastro',
            'slug'        => 'cadastro',
            'description' => 'Gerenciamento de registros base do sistema',
            'color'       => '#8B5CF6',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>',
            'items' => [
                ['title' => 'Produtos',      'description' => 'Cadastro e gestão de produtos',         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>', 'route' => 'products.index'],
                ['title' => 'Categorias',    'description' => 'Categorias de produtos',                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>', 'route' => 'product-categories.index'],
                ['title' => 'Unidades',      'description' => 'Unidades de medida (KG, UN, L)',        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="7" y1="2" x2="7" y2="22"/><line x1="17" y1="2" x2="17" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="2" y1="7" x2="7" y2="7"/><line x1="2" y1="17" x2="7" y2="17"/><line x1="17" y1="17" x2="22" y2="17"/><line x1="17" y1="7" x2="22" y2="7"/></svg>', 'route' => 'unit-of-measures.index'],
                ['title' => 'Fornecedores',  'description' => 'Cadastro de fornecedores e parceiros',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>', 'route' => 'suppliers.index'],
                ['title' => 'Clientes',      'description' => 'Base de clientes e contatos',           'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>', 'route' => 'clients.index'],
                ['title' => 'Funcionários',  'description' => 'Registro de colaboradores',             'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>', 'route' => 'employees.index'],
                ['title' => 'Funções',       'description' => 'Cargos e funções dos colaboradores',    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>', 'route' => 'roles.index'],
                ['title' => 'Veículos',      'description' => 'Frota e gestão de veículos',            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>', 'route' => 'vehicles.index'],
            ],
        ],

        'producao' => [
            'name'        => 'Produção',
            'slug'        => 'producao',
            'description' => 'Controle e acompanhamento do processo produtivo',
            'color'       => '#F59E0B',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22V8"/><path d="m20 12-8-4-8 4"/><path d="M20 17v-5"/><path d="M4 17v-5"/><path d="M20 22v-5"/><path d="M4 22v-5"/></svg>',
            'items' => [
                ['title' => 'Dashboard',          'description' => 'Painel de acompanhamento de produção',    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>', 'route' => 'dashboard.index'],
                ['title' => 'Ordens de Produção', 'description' => 'Criação e controle de ordens',           'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>', 'route' => 'production_orders.index'],
                ['title' => 'Ficha Técnica',      'description' => 'Especificações e composição de produtos', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>', 'route' => 'products.index'],
            ],
        ],

        'estoque' => [
            'name'        => 'Estoque',
            'slug'        => 'estoque',
            'description' => 'Controle e movimentação de inventário',
            'color'       => '#10B981',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
            'items' => [
                ['title' => 'Movimentação',  'description' => 'Entradas e saídas de estoque',           'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>', 'route' => 'stock.index'],
                ['title' => 'Inventários',   'description' => 'Contagem e conferência de inventário',   'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>', 'route' => 'stock.index'],
                ['title' => 'Transferência', 'description' => 'Transferência entre locais de estoque',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>', 'route' => 'stock.index'],
            ],
        ],

        'vendas' => [
            'name'        => 'Vendas',
            'slug'        => 'vendas',
            'description' => 'Gestão comercial e relacionamento com clientes',
            'color'       => '#06B6D4',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
            'items' => [
                ['title' => 'Pedidos',    'description' => 'Gerenciamento de pedidos de venda',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>', 'route' => 'vendas.pedidos'],
                ['title' => 'CRM',        'description' => 'Gestão de visitas e clientes',       'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>', 'route' => 'visits.index'],
                ['title' => 'Relatórios', 'description' => 'Análise e relatórios de vendas',    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'route' => 'sales_report.index'],
            ],
        ],

        'compras' => [
            'name'        => 'Compras',
            'slug'        => 'compras',
            'description' => 'Gestão de aquisições e pedidos de compra',
            'color'       => '#F97316',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
            'items' => [
                ['title' => 'Solicitações',     'description' => 'Solicitações internas de compra',    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>', 'route' => 'compras.solicitacoes'],
                ['title' => 'Pedidos de Compra','description' => 'Ordens de compra para fornecedores', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>', 'route' => 'compras.pedidos'],
                ['title' => 'Cotações',         'description' => 'Comparação de preços e fornecedores','icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>', 'route' => 'compras.cotacoes'],
            ],
        ],

        'fiscal' => [
            'name'        => 'Fiscal',
            'slug'        => 'fiscal',
            'description' => 'Módulo fiscal, notas fiscais e apuração de impostos',
            'color'       => '#EC4899',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
            'items' => [
                ['title' => 'NF-e',                'description' => 'Notas fiscais eletrônicas',                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>', 'route' => 'fiscal.nfe.index'],
                ['title' => 'Grupos Tributários',  'description' => 'NCM, ICMS, IPI, PIS/COFINS por grupo',         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', 'route' => 'fiscal.grupo-tributario.index'],
                ['title' => 'Tipos de Operação',   'description' => 'CFOP, CST, ICMS, IPI, PIS e COFINS',          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>', 'route' => 'fiscal.tipo-operacao.index'],
                ['title' => 'Entradas',            'description' => 'Notas fiscais de entrada',                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>', 'route' => 'fiscal.entrance.index'],
                ['title' => 'Saídas',              'description' => 'Notas fiscais de saída',                      'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="8 16 12 12 16 16"/><line x1="12" y1="21" x2="12" y2="12"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/></svg>', 'route' => 'fiscal.exit.index'],
                ['title' => 'Apuração',            'description' => 'Apuração e relatórios fiscais',              'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'route' => 'financial_reports.index'],
            ],
        ],

        'financeiro' => [
            'name'        => 'Financeiro',
            'slug'        => 'financeiro',
            'description' => 'Gestão financeira, contas e fluxo de caixa',
            'color'       => '#22C55E',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
            'items' => [
                ['title' => 'Plano de Contas',    'description' => 'Estrutura do plano de contas',          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>', 'route' => 'plans_of_accounts.index'],
                ['title' => 'Contas Bancárias',   'description' => 'Gestão de contas correntes, poupanças e caixas', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>', 'route' => 'contas_bancarias.index'],
                ['title' => 'Contas a Pagar',     'description' => 'Controle de pagamentos e vencimentos',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>', 'route' => 'accounts_payable.index'],
                ['title' => 'Contas a Receber',   'description' => 'Controle de recebimentos e cobranças',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>', 'route' => 'accounts_receivable.index'],
                ['title' => 'Fluxo de Caixa',     'description' => 'Projeção e análise do fluxo de caixa',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>', 'route' => 'cash_flow.index'],
                ['title' => 'Relatórios / DRE',   'description' => 'Demonstrativo de resultados e relatórios','icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'route' => 'financial_reports.index'],
            ],
        ],

        'rh' => [
            'name'        => 'Recursos Humanos',
            'slug'        => 'rh',
            'description' => 'Gestão de pessoas, ponto e folha de pagamento',
            'color'       => '#A78BFA',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
            'items' => [
                ['title' => 'Jornada de Trabalho', 'description' => 'Configuração de jornadas e turnos',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>', 'route' => 'working_day.index'],
                ['title' => 'Batida de Ponto',     'description' => 'Controle de ponto dos funcionários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>', 'route' => 'stitch_beat.index'],
                ['title' => 'Folha de Pagamento',  'description' => 'Processamento da folha salarial',   'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>', 'route' => 'payroll.index'],
                ['title' => 'Gerenciamento',       'description' => 'Gestão geral de funcionários',      'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>', 'route' => 'employee_management.index'],
                ['title' => 'Relatórios de RH',    'description' => 'Relatórios e indicadores de RH',    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'route' => 'rh_reports.index'],
            ],
        ],

        'transporte' => [
            'name'        => 'Transporte',
            'slug'        => 'transporte',
            'description' => 'Logística, frota e gestão de entregas',
            'color'       => '#0EA5E9',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
            'items' => [
                ['title' => 'Frotas',      'description' => 'Controle e manutenção da frota',    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>', 'route' => 'vehicles.index'],
                ['title' => 'Roteirização','description' => 'Planejamento de rotas de entrega',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="3" cy="12" r="2"/><circle cx="21" cy="5" r="2"/><circle cx="21" cy="19" r="2"/><line x1="5" y1="12" x2="19" y2="5.5"/><line x1="5" y1="12" x2="19" y2="18.5"/></svg>', 'route' => 'routing.index'],
                ['title' => 'Entregas',    'description' => 'Agendamento e monitoramento',       'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>', 'route' => 'scheduling_of_deliveries.index'],
            ],
        ],

    ];

    public function show(string $module)
    {
        $data = self::$modules[$module] ?? null;

        if (! $data) {
            abort(404);
        }

        $data['items'] = self::resolveItems($data);

        return view('modules.show', [
            'module'  => $data,
            'modules' => self::$modules,
        ]);
    }

    public function featureDevelopment(string $module, string $item)
    {
        $data = self::$modules[$module] ?? null;

        if (! $data) {
            abort(404);
        }

        $resolvedItem = collect(self::resolveItems($data))->firstWhere('key', $item);

        if (! $resolvedItem) {
            abort(404);
        }

        if ($resolvedItem['available']) {
            return redirect()->to($resolvedItem['href']);
        }

        return view('system.desenvolvimento', [
            'title' => $resolvedItem['title'],
            'description' => $resolvedItem['description'],
            'moduleSlug' => $data['slug'],
            'moduleName' => $data['name'],
            'color' => $data['color'],
            'icon' => $resolvedItem['icon'],
        ]);
    }

    public static function moduleItemRouteNames(): array
    {
        return collect(self::$modules)
            ->pluck('items')
            ->flatten(1)
            ->pluck('route')
            ->filter(fn ($route) => is_string($route) && $route !== '')
            ->unique()
            ->values()
            ->all();
    }

    private static function resolveItems(array $module): array
    {
        return collect($module['items'])
            ->map(function (array $item) use ($module) {
                $routeName = $item['route'] ?? null;
                $available = is_string($routeName) && $routeName !== '' && Route::has($routeName);
                $key = Str::slug($item['title']);

                $item['key'] = $key;
                $item['available'] = $available;
                $item['href'] = $available
                    ? route($routeName)
                    : route('module.item.development', [
                        'module' => $module['slug'],
                        'item' => $key,
                    ]);

                return $item;
            })
            ->values()
            ->all();
    }

    /** Expõe a lista completa para uso em views (navbar) */
    public static function allModules(): array
    {
        return self::$modules;
    }
}

