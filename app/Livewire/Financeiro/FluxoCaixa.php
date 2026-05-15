<?php

namespace App\Livewire\Financeiro;

use App\Enums\PayableStatus;
use App\Enums\ReceivableStatus;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\BankAccount;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Fluxo de Caixa')]
class FluxoCaixa extends Component
{
    /* ─────────────────────────────────────
      FILTROS
     ─────────────────────────────────────*/
    public string $period      = 'month';   // week, month, quarter, year
    public string $viewMode    = 'caixa';   // caixa (cash) ou competencia
    public string $startDate   = '';
    public string $endDate     = '';

    /* ─────────────────────────────────────
      MOUNT
     ─────────────────────────────────────*/
    public function mount(): void
    {
        $this->setDefaultDates();
    }

    /* ─────────────────────────────────────
      SET DEFAULT DATES BY PERIOD
     ─────────────────────────────────────*/
    public function setDefaultDates(): void
    {
        $now = now();

        switch ($this->period) {
            case 'week':
                $this->startDate = $now->startOfWeek()->format('Y-m-d');
                $this->endDate   = $now->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'quarter':
                $this->startDate = $now->startOfQuarter()->format('Y-m-d');
                $this->endDate   = $now->copy()->endOfQuarter()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = $now->startOfYear()->format('Y-m-d');
                $this->endDate   = $now->copy()->endOfYear()->format('Y-m-d');
                break;
            default: // month
                $this->startDate = $now->startOfMonth()->format('Y-m-d');
                $this->endDate   = $now->copy()->endOfMonth()->format('Y-m-d');
        }
    }

    /* ─────────────────────────────────────
      WATCHERS
     ─────────────────────────────────────*/
    public function updatedPeriod(): void
    {
        $this->setDefaultDates();
        $this->dispatch('chartDataUpdated');
    }

    public function updatedViewMode(): void
    {
        $this->dispatch('chartDataUpdated');
    }

    public function updatedStartDate(): void
    {
        $this->dispatch('chartDataUpdated');
    }

    public function updatedEndDate(): void
    {
        $this->dispatch('chartDataUpdated');
    }

    /* ─────────────────────────────────────
      COMPUTED — Saldo Inicial (contas bancárias)
     ─────────────────────────────────────*/
    #[Computed]
    public function initialBalance(): float
    {
        return (float) BankAccount::where('is_active', true)->sum('balance');
    }

    /* ─────────────────────────────────────
      COMPUTED — Totais de Entradas
     ─────────────────────────────────────*/
    #[Computed]
    public function totalEntries(): float
    {
        $query = AccountReceivable::whereBetween('due_date_at', [$this->startDate, $this->endDate]);

        if ($this->viewMode === 'caixa') {
            // Regime de caixa: apenas recebidos
            $query->where('status', ReceivableStatus::Received->value);
        } else {
            // Regime de competência: todos (exceto cancelados)
            $query->where('status', '!=', ReceivableStatus::Cancelled->value);
        }

        return (float) $query->sum('amount');
    }

    /* ─────────────────────────────────────
      COMPUTED — Totais de Saídas
     ─────────────────────────────────────*/
    #[Computed]
    public function totalExits(): float
    {
        $query = AccountPayable::whereBetween('due_date_at', [$this->startDate, $this->endDate]);

        if ($this->viewMode === 'caixa') {
            // Regime de caixa: apenas pagos
            $query->where('status', PayableStatus::Paid->value);
        } else {
            // Regime de competência: todos (exceto cancelados)
            $query->where('status', '!=', PayableStatus::Cancelled->value);
        }

        return (float) $query->sum('amount');
    }

    /* ─────────────────────────────────────
      COMPUTED — Saldo Final
     ─────────────────────────────────────*/
    #[Computed]
    public function finalBalance(): float
    {
        return $this->initialBalance + $this->totalEntries - $this->totalExits;
    }

    /* ─────────────────────────────────────
      COMPUTED — Dados para o Gráfico
     ─────────────────────────────────────*/
    #[Computed]
    public function chartData(): array
    {
        $start  = Carbon::parse($this->startDate);
        $end    = Carbon::parse($this->endDate);
        $period = CarbonPeriod::create($start, $end);

        $labels   = [];
        $entradas = [];
        $saidas   = [];

        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');

            // Entradas do dia
            $entriesQuery = AccountReceivable::whereDate('due_date_at', $day);
            if ($this->viewMode === 'caixa') {
                $entriesQuery->where('status', ReceivableStatus::Received->value);
            } else {
                $entriesQuery->where('status', '!=', ReceivableStatus::Cancelled->value);
            }
            $entradas[] = (float) $entriesQuery->sum('amount');

            // Saídas do dia
            $exitsQuery = AccountPayable::whereDate('due_date_at', $day);
            if ($this->viewMode === 'caixa') {
                $exitsQuery->where('status', PayableStatus::Paid->value);
            } else {
                $exitsQuery->where('status', '!=', PayableStatus::Cancelled->value);
            }
            $saidas[] = (float) $exitsQuery->sum('amount');
        }

        return [
            'labels'   => $labels,
            'entradas' => $entradas,
            'saidas'   => $saidas,
        ];
    }

    /* ─────────────────────────────────────
      COMPUTED — Fluxo Diário (Tabela)
     ─────────────────────────────────────*/
    #[Computed]
    public function dailyFlow(): array
    {
        $start  = Carbon::parse($this->startDate);
        $end    = Carbon::parse($this->endDate);
        $period = CarbonPeriod::create($start, $end);

        $flow    = [];
        $saldo   = $this->initialBalance;

        foreach ($period as $date) {
            $day = $date->format('Y-m-d');

            // Entradas do dia
            $entriesQuery = AccountReceivable::whereDate('due_date_at', $day);
            if ($this->viewMode === 'caixa') {
                $entriesQuery->where('status', ReceivableStatus::Received->value);
            } else {
                $entriesQuery->where('status', '!=', ReceivableStatus::Cancelled->value);
            }
            $entradas = (float) $entriesQuery->sum('amount');

            // Saídas do dia
            $exitsQuery = AccountPayable::whereDate('due_date_at', $day);
            if ($this->viewMode === 'caixa') {
                $exitsQuery->where('status', PayableStatus::Paid->value);
            } else {
                $exitsQuery->where('status', '!=', PayableStatus::Cancelled->value);
            }
            $saidas = (float) $exitsQuery->sum('amount');

            // Calcular saldo acumulado
            $saldo += $entradas - $saidas;

            // Só adiciona se houver movimentação
            if ($entradas > 0 || $saidas > 0) {
                $flow[] = [
                    'date'       => $date->format('d/m/Y'),
                    'day_name'   => $date->translatedFormat('l'),
                    'entradas'   => $entradas,
                    'saidas'     => $saidas,
                    'saldo'      => $saldo,
                    'is_today'   => $date->isToday(),
                    'is_future'  => $date->isFuture(),
                ];
            }
        }

        return $flow;
    }

    /* ─────────────────────────────────────
      COMPUTED — Contas a Receber Pendentes
     ─────────────────────────────────────*/
    #[Computed]
    public function pendingReceivables(): float
    {
        return (float) AccountReceivable::whereBetween('due_date_at', [$this->startDate, $this->endDate])
            ->whereIn('status', [ReceivableStatus::Pending->value, ReceivableStatus::Overdue->value])
            ->sum('amount');
    }

    /* ─────────────────────────────────────
      COMPUTED — Contas a Pagar Pendentes
     ─────────────────────────────────────*/
    #[Computed]
    public function pendingPayables(): float
    {
        return (float) AccountPayable::whereBetween('due_date_at', [$this->startDate, $this->endDate])
            ->whereIn('status', [PayableStatus::Pending->value, PayableStatus::Overdue->value])
            ->sum('amount');
    }

    /* ─────────────────────────────────────
      COMPUTED — Saldo Projetado
     ─────────────────────────────────────*/
    #[Computed]
    public function projectedBalance(): float
    {
        return $this->initialBalance + $this->pendingReceivables - $this->pendingPayables;
    }

    /* ─────────────────────────────────────
      RENDER
     ─────────────────────────────────────*/
    public function render(): View
    {
        return view('livewire.financeiro.fluxo-caixa.index', [
            'initialBalance'     => $this->initialBalance,
            'totalEntries'       => $this->totalEntries,
            'totalExits'         => $this->totalExits,
            'finalBalance'       => $this->finalBalance,
            'chartData'          => $this->chartData,
            'dailyFlow'          => $this->dailyFlow,
            'pendingReceivables' => $this->pendingReceivables,
            'pendingPayables'    => $this->pendingPayables,
            'projectedBalance'   => $this->projectedBalance,
        ]);
    }
}


