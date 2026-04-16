<?php

namespace App\Ai;

use App\Ai\Tools\BaseTool;
use App\Ai\Tools\BuscarPedidoTool;
use App\Ai\Tools\ConsultarClienteTool;
use App\Ai\Tools\ConsultarEstoqueTool;
use App\Ai\Tools\ConsultarFinanceiroTool;
use App\Ai\Tools\ConsultarTicketTool;

class ToolRegistry
{
    /** @var BaseTool[] */
    private array $tools = [];

    public function __construct()
    {
        $this->register(new BuscarPedidoTool());
        $this->register(new ConsultarClienteTool());
        $this->register(new ConsultarEstoqueTool());
        $this->register(new ConsultarFinanceiroTool());
        $this->register(new ConsultarTicketTool());
    }

    public function register(BaseTool $tool): void
    {
        $this->tools[$tool->name()] = $tool;
    }

    /**
     * Returns all tool definitions in OpenAI function-calling format.
     */
    public function definitions(): array
    {
        return array_values(
            array_map(fn (BaseTool $t) => $t->definition(), $this->tools)
        );
    }

    /**
     * Execute a tool by name with the given params and user context.
     */
    public function execute(string $name, array $params, int $userId): array
    {
        if (! isset($this->tools[$name])) {
            return ['erro' => "Ferramenta '{$name}' não encontrada."];
        }

        try {
            return $this->tools[$name]->execute($params, $userId);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("ToolRegistry: erro ao executar '{$name}'", [
                'error' => $e->getMessage(),
                'params' => $params,
            ]);

            return ['erro' => 'Não foi possível executar a consulta. Tente reformular sua pergunta.'];
        }
    }

    public function has(string $name): bool
    {
        return isset($this->tools[$name]);
    }
}

