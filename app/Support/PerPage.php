<?php

namespace App\Support;

final class PerPage
{
    /**
     * Opções permitidas de itens por página.
     * Padrão enterprise: 10, 25, 50, 100
     *
     * @var array<int>
     */
    public const OPTIONS = [10, 25, 50, 100];

    /** Valor padrão global */
    public const DEFAULT = 10;

    public static function resolve(int $default = 10, string $queryParam = 'per_page'): int
    {
        return self::sanitize((int) request()->integer($queryParam, $default), $default);
    }

    public static function sanitize(int $value, int $default = 10): int
    {
        return in_array($value, self::OPTIONS, true) ? $value : $default;
    }
}

