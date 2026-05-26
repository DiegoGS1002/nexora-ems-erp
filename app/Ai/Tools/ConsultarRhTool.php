<?php

namespace App\Ai\Tools;

use App\Models\Employees;
use App\Models\Payroll;
use App\Models\TimeRecord;
use App\Models\WorkShift;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConsultarRhTool extends BaseTool
{
    public function name(): string
    {
        return 'consultar_rh';
    }

    public function description(): string
    {
        return 'Consulta informações de RH: funcionários, folha de pagamento, ponto eletrônico, jornadas e holerites. '
            . 'Use quando o usuário perguntar sobre folha, ponto, holerite, jornada, funcionário, admissão, demissão, férias, INSS, IRRF ou FGTS.';
    }

    public function parameters(): array
    {
        return [
            'tipo' => [
                'type'        => 'string',
                'enum'        => ['funcionarios', 'folha', 'ponto', 'jornada'],
                'description' => 'Tipo de consulta: funcionarios, folha (folha de pagamento), ponto (registros de ponto), jornada.',
            ],
            'funcionario_nome' => [
                'type'        => 'string',
                'description' => 'Nome ou parte do nome do funcionário para filtrar.',
            ],
            'competencia' => [
                'type'        => 'string',
                'description' => 'Mês/ano de competência no formato MM/YYYY (ex: 05/2026). Usado para folha e ponto.',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de registros (padrão: 10, máximo: 20).',
            ],
        ];
    }

    protected function requiredParams(): array
    {
        return ['tipo'];
    }

    public function execute(array $params, int $userId): array
    {
        $tipo   = $params['tipo'] ?? 'funcionarios';
        $limite = min((int) ($params['limite'] ?? 10), 20);

        return match ($tipo) {
            'funcionarios' => $this->consultarFuncionarios($params, $limite),
            'folha'        => $this->consultarFolha($params, $limite),
            'ponto'        => $this->consultarPonto($params, $limite),
            'jornada'      => $this->consultarJornada($limite),
            default        => ['erro' => 'Tipo inválido. Use: funcionarios, folha, ponto ou jornada.'],
        };
    }

    private function consultarFuncionarios(array $params, int $limite): array
    {
        $query = Employees::query()->latest()->limit($limite);

        if (! empty($params['funcionario_nome'])) {
            $query->where('name', 'like', '%' . $params['funcionario_nome'] . '%');
        }

        $funcionarios = $query->get(['id', 'name', 'cpf', 'role', 'admission_date', 'status', 'salary']);

        if ($funcionarios->isEmpty()) {
            return ['resultado' => 'Nenhum funcionário encontrado.'];
        }

        return [
            'total'        => $funcionarios->count(),
            'funcionarios' => $funcionarios->map(fn ($f) => [
                'id'            => $f->id,
                'nome'          => $f->name,
                'cpf'           => $f->cpf,
                'cargo'         => $f->role,
                'admissao'      => $this->formatDate($f->admission_date),
                'status'        => $f->status ?? 'Ativo',
                'salario'       => $this->formatMoney((float) ($f->salary ?? 0)),
            ])->toArray(),
        ];
    }

    private function consultarFolha(array $params, int $limite): array
    {
        $query = Payroll::query()->latest()->limit($limite);

        if (! empty($params['competencia'])) {
            [$mes, $ano] = explode('/', $params['competencia'] . '/0');
            if ($mes && $ano) {
                $query->where('competence_month', (int) $mes)
                      ->where('competence_year', (int) $ano);
            }
        }

        if (! empty($params['funcionario_nome'])) {
            $query->whereHas('employee', fn ($q) => $q->where('name', 'like', '%' . $params['funcionario_nome'] . '%'));
        }

        $folhas = $query->with('employee:id,name')->get();

        if ($folhas->isEmpty()) {
            return ['resultado' => 'Nenhum registro de folha encontrado para os critérios informados.'];
        }

        return [
            'total'  => $folhas->count(),
            'folhas' => $folhas->map(fn ($f) => [
                'funcionario'  => $f->employee?->name ?? 'N/A',
                'competencia'  => ($f->competence_month ?? '--') . '/' . ($f->competence_year ?? '--'),
                'salario_base' => $this->formatMoney((float) ($f->base_salary ?? 0)),
                'total_bruto'  => $this->formatMoney((float) ($f->gross_salary ?? 0)),
                'total_liquido'=> $this->formatMoney((float) ($f->net_salary ?? 0)),
                'inss'         => $this->formatMoney((float) ($f->inss ?? 0)),
                'irrf'         => $this->formatMoney((float) ($f->irrf ?? 0)),
                'fgts'         => $this->formatMoney((float) ($f->fgts ?? 0)),
                'status'       => $f->status ?? 'Processada',
            ])->toArray(),
        ];
    }

    private function consultarPonto(array $params, int $limite): array
    {
        $query = TimeRecord::query()->latest()->limit($limite);

        if (! empty($params['funcionario_nome'])) {
            $query->whereHas('employee', fn ($q) => $q->where('name', 'like', '%' . $params['funcionario_nome'] . '%'));
        }

        $registros = $query->with('employee:id,name')->get();

        if ($registros->isEmpty()) {
            return ['resultado' => 'Nenhum registro de ponto encontrado.'];
        }

        return [
            'total'     => $registros->count(),
            'registros' => $registros->map(fn ($r) => [
                'funcionario' => $r->employee?->name ?? 'N/A',
                'data'        => $this->formatDate($r->date ?? $r->created_at),
                'entrada'     => $r->entry_time ?? $r->check_in ?? 'N/A',
                'saida'       => $r->exit_time ?? $r->check_out ?? 'N/A',
                'tipo'        => $r->type ?? 'Normal',
            ])->toArray(),
        ];
    }

    private function consultarJornada(int $limite): array
    {
        $jornadas = WorkShift::query()->limit($limite)->get(['id', 'name', 'start_time', 'end_time', 'total_hours']);

        if ($jornadas->isEmpty()) {
            return ['resultado' => 'Nenhuma jornada cadastrada.'];
        }

        return [
            'total'    => $jornadas->count(),
            'jornadas' => $jornadas->map(fn ($j) => [
                'id'          => $j->id,
                'nome'        => $j->name,
                'entrada'     => $j->start_time,
                'saida'       => $j->end_time,
                'horas_total' => $j->total_hours,
            ])->toArray(),
        ];
    }
}

