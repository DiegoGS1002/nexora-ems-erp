<?php

namespace App\Services;

use App\Models\SystemLog;

/**
 * Serviço centralizado para registro de eventos de auditoria do Nexora ERP.
 *
 * Uso:
 *   LogService::success('LOGIN', 'Usuário realizou login.', 'Segurança');
 *   LogService::warning('ACESSO_NEGADO', 'Tentativa de acesso sem permissão.', 'Segurança');
 *   LogService::error('ERRO_API', 'Falha ao consultar BrasilAPI.', 'Integrações', ['status' => 500]);
 */
class LogService
{
    public static function log(
        string $level,
        string $action,
        string $description,
        string $module = 'Sistema',
        ?array $context = null
    ): SystemLog {
        return SystemLog::create([
            'level'       => $level,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'ip'          => request()->ip(),
            'user_id'     => auth()->id(),
            'user_name'   => auth()->user()?->name,
            'user_email'  => auth()->user()?->email,
            'context'     => $context,
        ]);
    }

    public static function success(
        string $action,
        string $description,
        string $module = 'Sistema',
        ?array $context = null
    ): SystemLog {
        return self::log('success', $action, $description, $module, $context);
    }

    public static function warning(
        string $action,
        string $description,
        string $module = 'Sistema',
        ?array $context = null
    ): SystemLog {
        return self::log('warning', $action, $description, $module, $context);
    }

    public static function error(
        string $action,
        string $description,
        string $module = 'Sistema',
        ?array $context = null
    ): SystemLog {
        return self::log('error', $action, $description, $module, $context);
    }
}

