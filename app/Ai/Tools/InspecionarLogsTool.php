<?php

namespace App\Ai\Tools;

use App\Models\SystemLog;
use App\Models\User;

/**
 * Tool: Inspecionar logs do sistema.
 *
 * Permite ao agente consultar os logs de operações do Nexora ERP
 * para diagnosticar erros, identificar ações recentes e rastrear
 * a causa raiz de problemas relatados pelo usuário.
 */
class InspecionarLogsTool extends BaseTool
{
    public function name(): string
    {
        return 'inspecionar_logs';
    }

    public function description(): string
    {
        return 'Consulta os logs de operações do sistema Nexora ERP para o usuário atual ou globalmente. '
            . 'Use para diagnosticar erros recentes, rastrear ações que causaram problemas, '
            . 'verificar falhas em módulos específicos (Fiscal, Financeiro, Estoque, etc.) e '
            . 'encontrar a causa raiz de problemas reportados pelo usuário.';
    }

    public function parameters(): array
    {
        return [
            'modulo' => [
                'type'        => 'string',
                'description' => 'Filtrar por módulo: Fiscal, Financeiro, Vendas, Compras, Estoque, RH, Transporte, Cadastros, Segurança, Sistema. Opcional.',
            ],
            'nivel' => [
                'type'        => 'string',
                'description' => 'Filtrar por nível: error, warning, success. Opcional.',
                'enum'        => ['error', 'warning', 'success'],
            ],
            'busca' => [
                'type'        => 'string',
                'description' => 'Texto para buscar na descrição ou ação dos logs. Opcional.',
            ],
            'apenas_erros' => [
                'type'        => 'boolean',
                'description' => 'Se true, retorna apenas logs de erro e warning. Padrão: false.',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número de logs a retornar (padrão: 10, máximo: 20).',
            ],
        ];
    }

    public function execute(array $params, int $userId): array
    {
        $limite     = min((int) ($params['limite'] ?? 10), 20);
        $apenasErros = (bool) ($params['apenas_erros'] ?? false);

        $query = SystemLog::query()
            ->where('user_id', $userId)
            ->latest()
            ->limit($limite);

        if (! empty($params['modulo'])) {
            $query->where('module', $params['modulo']);
        }

        if (! empty($params['nivel'])) {
            $query->where('level', $params['nivel']);
        } elseif ($apenasErros) {
            $query->whereIn('level', ['error', 'warning']);
        }

        if (! empty($params['busca'])) {
            $busca = $params['busca'];
            $query->where(function ($q) use ($busca) {
                $q->where('description', 'like', "%{$busca}%")
                  ->orWhere('action', 'like', "%{$busca}%");
            });
        }

        $logs = $query->get();

        if ($logs->isEmpty()) {
            return [
                'resultado'  => 'Nenhum log encontrado para os critérios informados.',
                'diagnostico' => 'O usuário pode não ter realizado operações recentes nos módulos filtrados.',
            ];
        }

        $dados = $logs->map(fn ($log) => [
            'data'      => $this->formatDate($log->created_at),
            'hora'      => $log->created_at?->format('H:i:s'),
            'nivel'     => match($log->level) {
                'error'   => '❌ Erro',
                'warning' => '⚠️ Aviso',
                'success' => '✅ Sucesso',
                default   => $log->level,
            },
            'modulo'    => $log->module,
            'acao'      => $log->action,
            'descricao' => $log->description,
            'ip'        => $log->ip,
            'contexto'  => $log->context,
        ])->toArray();

        // Análise rápida dos erros
        $erros   = $logs->where('level', 'error')->count();
        $avisos  = $logs->where('level', 'warning')->count();
        $modulos = $logs->pluck('module')->unique()->filter()->values()->toArray();

        return [
            'logs'    => $dados,
            'total'   => $logs->count(),
            'resumo'  => [
                'erros'   => $erros,
                'avisos'  => $avisos,
                'modulos' => $modulos,
            ],
            'diagnostico_ia' => $erros > 0
                ? "Encontrei {$erros} erro(s) nos logs. Analise as descrições acima para identificar a causa raiz."
                : "Nenhum erro crítico encontrado nos logs recentes.",
        ];
    }
}

