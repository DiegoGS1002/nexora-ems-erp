<?php

namespace App\Livewire\Administracao\Logs;

use App\Models\SystemLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Logs do Sistema')]
class Index extends Component
{
    use WithPagination;

    /* ── Filtros ────────────────────────────────────────── */
    public string $search        = '';
    public string $filterLevel   = '';
    public string $filterModule  = '';
    public string $filterAction  = '';
    public string $filterDateStart = '';
    public string $filterDateEnd   = '';
    public int    $perPage       = 15;

    /* ── Modal de detalhes ──────────────────────────────── */
    public ?int  $selectedLogId = null;
    public bool  $showModal     = false;

    protected string $paginationTheme = 'tailwind';

    /* ── Reset de página ao alterar filtros ─────────────── */
    public function updatingSearch(): void        { $this->resetPage(); }
    public function updatingFilterLevel(): void   { $this->resetPage(); }
    public function updatingFilterModule(): void  { $this->resetPage(); }
    public function updatingFilterAction(): void  { $this->resetPage(); }
    public function updatingFilterDateStart(): void { $this->resetPage(); }
    public function updatingFilterDateEnd(): void   { $this->resetPage(); }
    public function updatingPerPage(): void       { $this->resetPage(); }

    /* ── Ações ──────────────────────────────────────────── */

    public function refresh(): void
    {
        // re-render é automático
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterLevel', 'filterModule', 'filterAction', 'filterDateStart', 'filterDateEnd']);
        $this->resetPage();
    }

    public function openModal(int $logId): void
    {
        $this->selectedLogId = $logId;
        $this->showModal     = true;
    }

    public function closeModal(): void
    {
        $this->showModal     = false;
        $this->selectedLogId = null;
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $logs     = $this->buildQuery()->get();
        $filename = 'logs-sistema-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 para compatibilidade com Excel
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Data/Hora', 'Nível', 'Usuário', 'E-mail',
                'Ação', 'Módulo', 'Descrição', 'IP',
            ], ';');

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->level_label,
                    $log->user_name  ?? 'Sistema',
                    $log->user_email ?? '-',
                    $log->action,
                    $log->module,
                    $log->description,
                    $log->ip ?? '-',
                ], ';');
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /* ── Query base ─────────────────────────────────────── */

    private function buildQuery()
    {
        return SystemLog::query()
            ->when($this->search !== '', fn ($q) =>
                $q->where(function ($sub) {
                    $sub->where('description', 'like', "%{$this->search}%")
                        ->orWhere('action',      'like', "%{$this->search}%")
                        ->orWhere('user_name',   'like', "%{$this->search}%")
                        ->orWhere('user_email',  'like', "%{$this->search}%")
                        ->orWhere('ip',          'like', "%{$this->search}%");
                })
            )
            ->when($this->filterLevel   !== '', fn ($q) => $q->where('level',  $this->filterLevel))
            ->when($this->filterModule  !== '', fn ($q) => $q->where('module', $this->filterModule))
            ->when($this->filterAction  !== '', fn ($q) => $q->where('action', $this->filterAction))
            ->when($this->filterDateStart !== '', fn ($q) =>
                $q->whereDate('created_at', '>=', $this->filterDateStart)
            )
            ->when($this->filterDateEnd !== '', fn ($q) =>
                $q->whereDate('created_at', '<=', $this->filterDateEnd)
            )
            ->latest();
    }

    /* ── Computed properties ─────────────────────────────── */

    public function getKpisProperty(): array
    {
        $total      = SystemLog::count();
        $success    = SystemLog::where('level', 'success')->count();
        $warning    = SystemLog::where('level', 'warning')->count();
        $error      = SystemLog::where('level', 'error')->count();
        $activeUsers = SystemLog::where('created_at', '>=', now()->subHours(24))
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $successPct = $total > 0 ? round($success / $total * 100, 1) : 0;
        $warningPct = $total > 0 ? round($warning / $total * 100, 1) : 0;
        $errorPct   = $total > 0 ? round($error   / $total * 100, 1) : 0;

        return compact(
            'total', 'success', 'warning', 'error',
            'activeUsers', 'successPct', 'warningPct', 'errorPct'
        );
    }

    public function getSelectedLogProperty(): ?SystemLog
    {
        return $this->selectedLogId ? SystemLog::find($this->selectedLogId) : null;
    }

    public function getModulesProperty(): array
    {
        return SystemLog::distinct()->orderBy('module')->pluck('module')->toArray();
    }

    public function getActionsProperty(): array
    {
        return SystemLog::distinct()->orderBy('action')->pluck('action')->toArray();
    }

    /* ── Render ──────────────────────────────────────────── */

    public function render()
    {
        return view('livewire.administracao.logs.index', [
            'logs' => $this->buildQuery()->paginate($this->perPage),
        ]);
    }
}

