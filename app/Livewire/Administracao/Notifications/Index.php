<?php

namespace App\Livewire\Administracao\Notifications;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Notifications\DatabaseNotification;

#[Layout('layouts.app')]
#[Title('Central de Notificações')]
class Index extends Component
{
    use WithPagination;

    public string $filterType  = '';
    public string $filterRead  = '';
    public string $filterDate  = '';
    public int    $perPage     = 15;

    protected string $paginationTheme = 'tailwind';

    /* ── Reset de página ao alterar filtros ──────── */
    public function updatingFilterType(): void  { $this->resetPage(); }
    public function updatingFilterRead(): void  { $this->resetPage(); }
    public function updatingFilterDate(): void  { $this->resetPage(); }
    public function updatingPerPage(): void     { $this->resetPage(); }

    /* ── Ações ───────────────────────────────────── */

    public function markAsRead(string $id): void
    {
        $n = auth()->user()->notifications()->where('id', $id)->first();
        if ($n) {
            $n->markAsRead();
        }
        $this->dispatch('refreshNotifications');
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('refreshNotifications');
    }

    public function deleteNotification(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        $this->dispatch('refreshNotifications');
    }

    public function deleteAll(): void
    {
        auth()->user()->notifications()->delete();
        $this->dispatch('refreshNotifications');
    }

    /* ── KPIs ────────────────────────────────────── */

    public function getKpisProperty(): array
    {
        $user  = auth()->user();
        $total = $user->notifications()->count();
        $unread = $user->unreadNotifications()->count();
        $read  = $total - $unread;

        $byType = $user->notifications()
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.type")) as ntype, COUNT(*) as cnt')
            ->reorder()        // remove o ORDER BY created_at padrão (incompatível com GROUP BY no MySQL only_full_group_by)
            ->groupBy('ntype')
            ->pluck('cnt', 'ntype')
            ->toArray();

        return compact('total', 'unread', 'read', 'byType');
    }

    /* ── Types disponíveis ───────────────────────── */

    public function getTypesProperty(): array
    {
        return auth()->user()
            ->notifications()
            ->get()
            ->pluck('data.type')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /* ── Query base ──────────────────────────────── */

    private function buildQuery()
    {
        $query = auth()->user()->notifications()->latest();

        if ($this->filterRead === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filterRead === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($this->filterDate !== '') {
            $query->whereDate('created_at', $this->filterDate);
        }

        return $query;
    }

    /* ── Render ──────────────────────────────────── */

    public function render()
    {
        $notifications = $this->buildQuery()->paginate($this->perPage);

        if ($this->filterType !== '') {
            $all = $this->buildQuery()
                ->get()
                ->filter(fn($n) => ($n->data['type'] ?? '') === $this->filterType)
                ->values();

            $currentPage = $this->getPage();
            $total       = $all->count();

            $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
                $all->forPage($currentPage, $this->perPage),
                $total,
                $this->perPage,
                $currentPage,
                ['path' => request()->url()]
            );
        }

        return view('livewire.administracao.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}


