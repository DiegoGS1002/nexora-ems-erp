<?php

namespace App\Ai\Tools;

abstract class BaseTool
{
    abstract public function name(): string;
    abstract public function description(): string;
    abstract public function parameters(): array;

    /**
     * Execute the tool with the given params in the context of a user.
     */
    abstract public function execute(array $params, int $userId): array;

    /**
     * Returns the OpenAI function definition for this tool.
     */
    public function definition(): array
    {
        return [
            'type'     => 'function',
            'function' => [
                'name'        => $this->name(),
                'description' => $this->description(),
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => $this->parameters(),
                    'required'   => $this->requiredParams(),
                ],
            ],
        ];
    }

    protected function requiredParams(): array
    {
        return [];
    }

    protected function formatMoney(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    protected function formatDate(?string $date): string
    {
        if (! $date) {
            return 'N/A';
        }

        try {
            return \Carbon\Carbon::parse($date)->format('d/m/Y');
        } catch (\Throwable) {
            return $date;
        }
    }
}

