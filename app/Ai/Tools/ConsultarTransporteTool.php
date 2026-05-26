<?php

namespace App\Ai\Tools;

use App\Models\SchedulingOfDeliveries;
use App\Models\MonitoringOfDeliveries;
use App\Models\Vehicle;
use App\Models\RouteManagement;

class ConsultarTransporteTool extends BaseTool
{
    public function name(): string
    {
        return 'consultar_transporte';
    }

    public function description(): string
    {
        return 'Consulta informações de transporte e logística: entregas agendadas, monitoramento, veículos e rotas. '
            . 'Use quando o usuário perguntar sobre entrega, rota, motorista, veículo, romaneio ou roteirização.';
    }

    public function parameters(): array
    {
        return [
            'tipo' => [
                'type'        => 'string',
                'enum'        => ['entregas', 'veiculos', 'rotas', 'monitoramento'],
                'description' => 'Tipo de consulta.',
            ],
            'status' => [
                'type'        => 'string',
                'description' => 'Status para filtrar (ex: pendente, em_rota, entregue, cancelado).',
            ],
            'placa' => [
                'type'        => 'string',
                'description' => 'Placa do veículo para busca.',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de registros (padrão: 8, máximo: 15).',
            ],
        ];
    }

    protected function requiredParams(): array
    {
        return ['tipo'];
    }

    public function execute(array $params, int $userId): array
    {
        $tipo   = $params['tipo'] ?? 'entregas';
        $limite = min((int) ($params['limite'] ?? 8), 15);

        return match ($tipo) {
            'entregas'      => $this->consultarEntregas($params, $limite),
            'veiculos'      => $this->consultarVeiculos($params, $limite),
            'rotas'         => $this->consultarRotas($limite),
            'monitoramento' => $this->consultarMonitoramento($params, $limite),
            default         => ['erro' => 'Tipo inválido. Use: entregas, veiculos, rotas, monitoramento.'],
        };
    }

    private function consultarEntregas(array $params, int $limite): array
    {
        $query = SchedulingOfDeliveries::query()->latest()->limit($limite);

        if (! empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        $entregas = $query->get();

        if ($entregas->isEmpty()) {
            return ['resultado' => 'Nenhuma entrega agendada encontrada.'];
        }

        return [
            'total'    => $entregas->count(),
            'entregas' => $entregas->map(fn ($e) => [
                'id'              => $e->id,
                'status'          => $e->status,
                'data_prevista'   => $this->formatDate($e->scheduled_date ?? $e->delivery_date ?? null),
                'destinatario'    => $e->recipient_name ?? $e->client_name ?? 'N/A',
                'endereco'        => $e->address ?? 'N/A',
                'motorista'       => $e->driver_name ?? 'N/A',
                'veiculo'         => $e->vehicle_plate ?? 'N/A',
            ])->toArray(),
        ];
    }

    private function consultarVeiculos(array $params, int $limite): array
    {
        $query = Vehicle::query()->limit($limite);

        if (! empty($params['placa'])) {
            $query->where('plate', 'like', '%' . $params['placa'] . '%');
        }

        $veiculos = $query->get(['id', 'plate', 'model', 'brand', 'year', 'status', 'km']);

        if ($veiculos->isEmpty()) {
            return ['resultado' => 'Nenhum veículo encontrado.'];
        }

        return [
            'total'    => $veiculos->count(),
            'veiculos' => $veiculos->map(fn ($v) => [
                'id'     => $v->id,
                'placa'  => $v->plate,
                'modelo' => $v->model,
                'marca'  => $v->brand,
                'ano'    => $v->year,
                'status' => $v->status ?? 'Ativo',
                'km'     => $v->km ?? 'N/A',
            ])->toArray(),
        ];
    }

    private function consultarRotas(int $limite): array
    {
        $rotas = RouteManagement::query()->latest()->limit($limite)->get();

        if ($rotas->isEmpty()) {
            return ['resultado' => 'Nenhuma rota cadastrada.'];
        }

        return [
            'total' => $rotas->count(),
            'rotas' => $rotas->map(fn ($r) => [
                'id'        => $r->id,
                'nome'      => $r->name ?? 'N/A',
                'origem'    => $r->origin ?? 'N/A',
                'destino'   => $r->destination ?? 'N/A',
                'distancia' => $r->distance_km ?? 'N/A',
                'status'    => $r->status ?? 'Ativa',
            ])->toArray(),
        ];
    }

    private function consultarMonitoramento(array $params, int $limite): array
    {
        $monitoramentos = MonitoringOfDeliveries::query()->latest()->limit($limite)->get();

        if ($monitoramentos->isEmpty()) {
            return ['resultado' => 'Nenhum registro de monitoramento encontrado.'];
        }

        return [
            'total'          => $monitoramentos->count(),
            'monitoramentos' => $monitoramentos->map(fn ($m) => [
                'id'        => $m->id,
                'status'    => $m->status ?? 'N/A',
                'motorista' => $m->driver_name ?? 'N/A',
                'veiculo'   => $m->vehicle_plate ?? 'N/A',
                'atualizado'=> $this->formatDate($m->updated_at),
            ])->toArray(),
        ];
    }
}

