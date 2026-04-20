<?php

namespace App\Traits;

use App\Services\LogService;

/**
 * Trait Loggable
 *
 * Registra automaticamente eventos de criação, atualização e deleção
 * de modelos no log do sistema (system_logs).
 *
 * Uso: adicione `use Loggable;` no model e defina opcionalmente:
 *   protected string $logModule = 'Cadastros';
 *   protected string $logName   = 'Produto';       // nome legível do modelo
 */
trait Loggable
{
    public static function bootLoggable(): void
    {
        static::created(function ($model) {
            static::writeLog($model, 'created', 'criado');
        });

        static::updated(function ($model) {
            static::writeLog($model, 'updated', 'atualizado');
        });

        static::deleted(function ($model) {
            static::writeLog($model, 'deleted', 'removido');
        });
    }

    protected static function writeLog($model, string $action, string $verb): void
    {
        try {
            $module   = $model->logModule ?? static::resolveLogModule();
            $name     = $model->logName   ?? class_basename(static::class);
            $id       = $model->getKey();

            $displayName = $model->name
                ?? $model->social_name
                ?? $model->title
                ?? $model->description_title
                ?? $model->order_number
                ?? $model->invoice_number
                ?? "#$id";

            $actionLabel = match ($action) {
                'created' => 'CRIAR',
                'updated' => 'EDITAR',
                'deleted' => 'EXCLUIR',
                default   => strtoupper($action),
            };

            $level = match ($action) {
                'deleted' => 'warning',
                default   => 'success',
            };

            LogService::log(
                $level,
                $actionLabel . '_' . strtoupper(class_basename(static::class)),
                "{$name} \"{$displayName}\" foi {$verb} (ID: {$id}).",
                $module,
            );
        } catch (\Throwable $e) {
            // Nunca deixar falha de log quebrar a operação principal
            \Illuminate\Support\Facades\Log::warning('Loggable: falha ao registrar log do sistema — ' . $e->getMessage());
        }
    }

    protected static function resolveLogModule(): string
    {
        return match (true) {
            str_contains(static::class, 'Fiscal')         => 'Fiscal',
            str_contains(static::class, 'Account')        => 'Financeiro',
            str_contains(static::class, 'Payroll')        => 'RH',
            str_contains(static::class, 'Employee')       => 'RH',
            str_contains(static::class, 'Stock')          => 'Estoque',
            str_contains(static::class, 'SalesOrder')     => 'Vendas',
            str_contains(static::class, 'Product')        => 'Cadastros',
            str_contains(static::class, 'Client')         => 'Cadastros',
            str_contains(static::class, 'Supplier')       => 'Compras',
            str_contains(static::class, 'Cotacao')        => 'Compras',
            str_contains(static::class, 'Pedido')         => 'Compras',
            str_contains(static::class, 'Vehicle')        => 'Transporte',
            str_contains(static::class, 'Driver')         => 'Transporte',
            str_contains(static::class, 'Delivery')       => 'Transporte',
            default                                        => 'Sistema',
        };
    }
}

