<?php

namespace App\Support;

final class PerPage
{
    /**
     * @var array<int>
     */
    public const OPTIONS = [10, 20, 50, 100, 200];

    public static function resolve(int $default = 10, string $queryParam = 'per_page'): int
    {
        return self::sanitize((int) request()->integer($queryParam, $default), $default);
    }

    public static function sanitize(int $value, int $default = 10): int
    {
        return in_array($value, self::OPTIONS, true) ? $value : $default;
    }
}

