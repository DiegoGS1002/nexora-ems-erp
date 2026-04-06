<?php

namespace Database\Seeders;

use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class SystemLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_admin', true)->first();
        $user  = User::where('is_admin', false)->first();

        $entries = [
            // Segurança
            ['level' => 'success', 'action' => 'LOGIN',         'module' => 'Segurança',   'description' => 'Login realizado com sucesso.',                                                   'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'warning', 'action' => 'ACESSO_NEGADO', 'module' => 'Segurança',   'description' => 'Tentativa de acesso a rota protegida sem permissão.',                           'user' => $user,  'ip' => '192.168.1.22'],
            ['level' => 'success', 'action' => 'LOGOUT',        'module' => 'Segurança',   'description' => 'Sessão encerrada com sucesso.',                                                  'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'error',   'action' => 'LOGIN',         'module' => 'Segurança',   'description' => 'Falha na autenticação: credenciais inválidas.',                                  'user' => null,   'ip' => '200.177.45.88'],

            // Cadastros
            ['level' => 'success', 'action' => 'CRIACAO',       'module' => 'Cadastros',   'description' => 'Novo funcionário cadastrado: João da Silva.',                                   'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'success', 'action' => 'ALTERACAO',     'module' => 'Cadastros',   'description' => 'Dados do cliente "Tech Solutions Ltda" atualizados.',                           'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'success', 'action' => 'EXCLUSAO',      'module' => 'Cadastros',   'description' => 'Fornecedor "Distribuidora Sul" removido do sistema.',                           'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'warning', 'action' => 'ALTERACAO',     'module' => 'Cadastros',   'description' => 'Tentativa de exclusão de produto com estoque ativo rejeitada.',                 'user' => $user,  'ip' => '192.168.1.22'],

            // Vendas
            ['level' => 'success', 'action' => 'CRIACAO',       'module' => 'Vendas',      'description' => 'Pedido #1042 criado para cliente Tech Solutions Ltda. Valor: R$ 4.590,00.',    'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'success', 'action' => 'ALTERACAO',     'module' => 'Vendas',      'description' => 'Status do pedido #1041 alterado para "Aprovado".',                             'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'error',   'action' => 'EXCLUSAO',      'module' => 'Vendas',      'description' => 'Falha ao excluir pedido #1039 — pedido com nota fiscal emitida.',               'user' => $user,  'ip' => '192.168.1.22', 'context' => ['pedido_id' => 1039, 'motivo' => 'NF emitida', 'status_http' => 422]],

            // Financeiro
            ['level' => 'success', 'action' => 'CRIACAO',       'module' => 'Financeiro',  'description' => 'Lançamento de conta a pagar gerado: fornecedor Alfa — R$ 1.200,00.',           'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'success', 'action' => 'ALTERACAO',     'module' => 'Financeiro',  'description' => 'Vencimento da conta #88 prorrogado para 15/05/2026.',                           'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'warning', 'action' => 'ALTERACAO',     'module' => 'Financeiro',  'description' => 'Valor de conta a receber alterado sem aprovação de superior.',                  'user' => $user,  'ip' => '192.168.1.22'],

            // Estoque
            ['level' => 'success', 'action' => 'ENTRADA',       'module' => 'Estoque',     'description' => 'Entrada de 500 unidades do produto "Parafuso M8" registrada.',                 'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'warning', 'action' => 'SAIDA',         'module' => 'Estoque',     'description' => 'Estoque mínimo atingido para o produto "Tubo de PVC 50mm".',                   'user' => null,   'ip' => '127.0.0.1'],

            // Integrações
            ['level' => 'success', 'action' => 'SINCRONIZACAO', 'module' => 'Integrações', 'description' => 'Sincronização com API externa — BrasilAPI CEP — 15 registros atualizados.',   'user' => null,   'ip' => '127.0.0.1', 'context' => ['api' => 'BrasilAPI', 'endpoint' => 'CEP', 'registros' => 15, 'duracao_ms' => 342]],
            ['level' => 'error',   'action' => 'SINCRONIZACAO', 'module' => 'Integrações', 'description' => 'Falha na sincronização com BrasilAPI — timeout após 30s.',                     'user' => null,   'ip' => '127.0.0.1', 'context' => ['api' => 'BrasilAPI', 'status_http' => 504, 'mensagem' => 'Gateway Timeout']],

            // RH
            ['level' => 'success', 'action' => 'CRIACAO',       'module' => 'RH',          'description' => 'Folha de pagamento de Março/2026 gerada com sucesso.',                         'user' => $admin, 'ip' => '192.168.1.10'],
            ['level' => 'success', 'action' => 'BACKUP',        'module' => 'Sistema',     'description' => 'Backup automático realizado com sucesso. Arquivo: backup-2026-04-05.sql.gz',   'user' => null,   'ip' => '127.0.0.1'],

            // Fiscal
            ['level' => 'error',   'action' => 'EMISSAO_NF',    'module' => 'Fiscal',      'description' => 'Erro na emissão de NF-e para pedido #1040 — SEFAZ indisponível.',             'user' => $admin, 'ip' => '192.168.1.10', 'context' => ['pedido_id' => 1040, 'sefaz_codigo' => 109, 'mensagem' => 'Serviço temporariamente indisponível']],
            ['level' => 'success', 'action' => 'EMISSAO_NF',    'module' => 'Fiscal',      'description' => 'NF-e #000042 emitida com sucesso para Tech Solutions Ltda.',                   'user' => $admin, 'ip' => '192.168.1.10'],
        ];

        foreach ($entries as $entry) {
            SystemLog::create([
                'level'       => $entry['level'],
                'action'      => $entry['action'],
                'module'      => $entry['module'],
                'description' => $entry['description'],
                'ip'          => $entry['ip'] ?? '127.0.0.1',
                'user_id'     => $entry['user']?->id,
                'user_name'   => $entry['user']?->name,
                'user_email'  => $entry['user']?->email,
                'context'     => $entry['context'] ?? null,
                'created_at'  => now()->subMinutes(rand(1, 4320)),
                'updated_at'  => now()->subMinutes(rand(1, 4320)),
            ]);
        }
    }
}

