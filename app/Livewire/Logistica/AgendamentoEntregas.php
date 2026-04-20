<?php

namespace App\Livewire\Logistica;

use App\Enums\DeliveryPriority;
use App\Enums\DeliveryScheduleStatus;
use App\Enums\SalesOrderStatus;
use App\Models\DeliveryTimeWindow;
use App\Models\SchedulingOfDeliveries;
use App\Models\SalesOrder;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Agendamento de Entregas')]
class AgendamentoEntregas extends Component
{
    use WithPagination;

    /* ─── Filtros ─── */
    public string $search         = '';
    public string $filterStatus   = '';
    public string $filterDate     = '';
    public string $filterPriority = '';

    /* ─── Modal State ─── */
    public bool   $showModal    = false;
    public bool   $showDetail   = false;
    public bool   $showReschedule = false;
    public ?int   $editingId    = null;
    public ?int   $viewingId    = null;
    public ?int   $reschedulingId = null;

    /* ─── Formulário ─── */
    public string $order_id        = '';
    public string $client_name     = '';
    public string $delivery_address= '';
    public string $delivery_date   = '';
    public string $time_window_id  = '';
    public string $vehicle_id      = '';
    public string $driver_name     = '';
    public string $weight_kg       = '';
    public string $volume_m3       = '';
    public string $priority        = '';
    public string $status          = '';
    public string $notes           = '';

    /* ─── Reagendamento ─── */
    public string $reschedule_date   = '';
    public string $reschedule_window = '';
    public string $reschedule_reason = '';

    /* ─────────────────────────────────────────
       COMPUTED
    ───────────────────────────────────────── */

    #[Computed]
    public function schedules()
    {
        return SchedulingOfDeliveries::with(['timeWindow', 'vehicle', 'order'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('schedule_number', 'like', '%' . $this->search . '%')
                       ->orWhere('client_name', 'like', '%' . $this->search . '%')
                       ->orWhere('delivery_address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDate,   fn($q) => $q->whereDate('delivery_date', $this->filterDate))
            ->when($this->filterPriority, fn($q) => $q->where('priority', $this->filterPriority))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        $counts = SchedulingOfDeliveries::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'total'         => array_sum($counts),
            'agendado'      => $counts[DeliveryScheduleStatus::Agendado->value]    ?? 0,
            'em_rota'       => $counts[DeliveryScheduleStatus::EmRota->value]      ?? 0,
            'entregue'      => $counts[DeliveryScheduleStatus::Entregue->value]    ?? 0,
            'nao_entregue'  => $counts[DeliveryScheduleStatus::NaoEntregue->value] ?? 0,
            'reagendado'    => $counts[DeliveryScheduleStatus::Reagendado->value]  ?? 0,
        ];
    }

    #[Computed]
    public function salesOrders()
    {
        return SalesOrder::with('client')
            ->whereIn('status', ['approved', 'em_separacao', 'invoiced'])
            ->latest()
            ->get(['id', 'order_number', 'client_id'])
            ->map(fn($o) => [
                'id'    => $o->id,
                'label' => $o->order_number . ' — ' . ($o->client?->name ?? 'N/A'),
            ]);
    }

    #[Computed]
    public function timeWindows()
    {
        return DeliveryTimeWindow::where('is_active', true)->orderBy('start_time')->get();
    }

    #[Computed]
    public function vehicles()
    {
        return Vehicle::where('is_active', true)->orderBy('name')->get(['id', 'name', 'brand', 'model', 'plate']);
    }

    #[Computed]
    public function viewingSchedule(): ?SchedulingOfDeliveries
    {
        if (!$this->viewingId) return null;
        return SchedulingOfDeliveries::with(['timeWindow', 'vehicle', 'order.client'])->find($this->viewingId);
    }

    /* ─────────────────────────────────────────
       MODAL OPEN / CLOSE
    ───────────────────────────────────────── */

    public function openModal(): void
    {
        $this->resetForm();
        $this->status   = DeliveryScheduleStatus::Agendado->value;
        $this->priority = DeliveryPriority::Normal->value;
        $this->delivery_date = now()->addDay()->format('Y-m-d');
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function openDetail(int $id): void
    {
        $this->viewingId  = $id;
        $this->showDetail = true;
        unset($this->viewingSchedule);
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->viewingId  = null;
        unset($this->viewingSchedule);
    }

    public function openReschedule(int $id): void
    {
        $this->reschedulingId    = $id;
        $this->reschedule_date   = now()->addDay()->format('Y-m-d');
        $this->reschedule_window = '';
        $this->reschedule_reason = '';
        $this->showReschedule    = true;
        $this->showDetail        = false;
    }

    public function closeReschedule(): void
    {
        $this->showReschedule = false;
        $this->reschedulingId = null;
        $this->reschedule_date = '';
        $this->reschedule_reason = '';
    }

    /* ─────────────────────────────────────────
       EDIT
    ───────────────────────────────────────── */

    public function edit(int $id): void
    {
        $s = SchedulingOfDeliveries::findOrFail($id);
        $this->editingId       = $s->id;
        $this->order_id        = (string) ($s->order_id ?? '');
        $this->client_name     = $s->client_name;
        $this->delivery_address= $s->delivery_address;
        $this->delivery_date   = $s->delivery_date?->format('Y-m-d') ?? '';
        $this->time_window_id  = (string) ($s->time_window_id ?? '');
        $this->vehicle_id      = (string) ($s->vehicle_id ?? '');
        $this->driver_name     = $s->driver_name ?? '';
        $this->weight_kg       = (string) ($s->weight_kg ?? '');
        $this->volume_m3       = (string) ($s->volume_m3 ?? '');
        $this->priority        = $s->priority?->value ?? DeliveryPriority::Normal->value;
        $this->status          = $s->status?->value ?? DeliveryScheduleStatus::Agendado->value;
        $this->notes           = $s->notes ?? '';
        $this->showDetail      = false;
        $this->showModal       = true;
    }

    /* ─────────────────────────────────────────
       SAVE
    ───────────────────────────────────────── */

    public function save(): void
    {
        $this->validate([
            'client_name'      => 'required|string|min:2',
            'delivery_address' => 'required|string|min:5',
            'delivery_date'    => 'required|date|after_or_equal:today',
            'status'           => 'required',
            'priority'         => 'required',
        ], [
            'client_name.required'      => 'Informe o nome do cliente.',
            'delivery_address.required' => 'Informe o endereço de entrega.',
            'delivery_date.required'    => 'Informe a data de entrega.',
            'delivery_date.after_or_equal' => 'A data não pode ser anterior a hoje.',
        ]);

        $data = [
            'order_id'         => $this->order_id         ?: null,
            'client_name'      => $this->client_name,
            'delivery_address' => $this->delivery_address,
            'delivery_date'    => $this->delivery_date,
            'time_window_id'   => $this->time_window_id   ?: null,
            'vehicle_id'       => $this->vehicle_id       ?: null,
            'driver_name'      => $this->driver_name      ?: null,
            'weight_kg'        => $this->weight_kg        ?: null,
            'volume_m3'        => $this->volume_m3        ?: null,
            'priority'         => $this->priority,
            'status'           => $this->status,
            'notes'            => $this->notes            ?: null,
        ];

        if ($this->editingId) {
            SchedulingOfDeliveries::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Agendamento atualizado com sucesso!');
        } else {
            SchedulingOfDeliveries::create($data);
            session()->flash('message', 'Agendamento criado com sucesso!');
        }

        $this->closeModal();
        unset($this->schedules, $this->stats);
    }

    /* ─────────────────────────────────────────
       REAGENDAR
    ───────────────────────────────────────── */

    public function confirmReschedule(): void
    {
        $this->validate([
            'reschedule_date'   => 'required|date|after_or_equal:today',
            'reschedule_reason' => 'required|string|min:5',
        ], [
            'reschedule_date.required'   => 'Informe a nova data.',
            'reschedule_date.after_or_equal' => 'A data não pode ser anterior a hoje.',
            'reschedule_reason.required' => 'Informe o motivo do reagendamento.',
        ]);

        $original = SchedulingOfDeliveries::findOrFail($this->reschedulingId);

        SchedulingOfDeliveries::create([
            'order_id'            => $original->order_id,
            'client_name'         => $original->client_name,
            'delivery_address'    => $original->delivery_address,
            'delivery_date'       => $this->reschedule_date,
            'time_window_id'      => $this->reschedule_window ?: $original->time_window_id,
            'vehicle_id'          => $original->vehicle_id,
            'driver_name'         => $original->driver_name,
            'weight_kg'           => $original->weight_kg,
            'volume_m3'           => $original->volume_m3,
            'priority'            => $original->priority?->value ?? DeliveryPriority::Normal->value,
            'status'              => DeliveryScheduleStatus::Reagendado->value,
            'notes'               => $original->notes,
            'rescheduled_from_id' => $original->id,
            'reschedule_reason'   => $this->reschedule_reason,
        ]);

        $original->update(['status' => DeliveryScheduleStatus::Reagendado->value]);

        session()->flash('message', 'Entrega reagendada com sucesso!');
        $this->closeReschedule();
        unset($this->schedules, $this->stats);
    }

    /* ─────────────────────────────────────────
       AÇÕES RÁPIDAS
    ───────────────────────────────────────── */

    public function changeStatus(int $id, string $status): void
    {
        $schedule = SchedulingOfDeliveries::findOrFail($id);
        $schedule->update(['status' => $status]);

        if ($status === DeliveryScheduleStatus::Entregue->value) {
            $schedule->update(['delivered_at' => now()]);

            // ── Integração: Entrega confirmada → atualiza SalesOrder para Delivered ──
            if ($schedule->order_id) {
                SalesOrder::where('id', $schedule->order_id)
                    ->whereNotIn('status', [
                        SalesOrderStatus::Cancelled->value,
                        SalesOrderStatus::Delivered->value,
                    ])
                    ->update(['status' => SalesOrderStatus::Delivered->value]);
            }
        }

        session()->flash('message', 'Status atualizado!');
        $this->closeDetail();
        unset($this->schedules, $this->stats);
    }

    public function delete(int $id): void
    {
        $schedule = SchedulingOfDeliveries::findOrFail($id);
        $schedule->delete();
        session()->flash('message', 'Agendamento removido.');
        $this->closeDetail();
        unset($this->schedules, $this->stats);
    }

    /* ─────────────────────────────────────────
       AUTO-FILL FROM ORDER
    ───────────────────────────────────────── */

    public function updatedOrderId($value): void
    {
        if (!$value) return;
        $order = SalesOrder::with(['client', 'addresses'])->find($value);
        if (!$order) return;

        $this->client_name = $order->client?->name ?? '';

        $deliveryAddr = $order->addresses->firstWhere('type', 'delivery')
                     ?? $order->addresses->first();

        if ($deliveryAddr) {
            $this->delivery_address = implode(', ', array_filter([
                $deliveryAddr->street,
                $deliveryAddr->number,
                $deliveryAddr->district,
                $deliveryAddr->city,
                $deliveryAddr->state,
            ]));
        }

        if ($order->expected_delivery_date) {
            $this->delivery_date = $order->expected_delivery_date->format('Y-m-d');
        }
    }

    /* ─────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────── */

    public function clearFilters(): void
    {
        $this->search         = '';
        $this->filterStatus   = '';
        $this->filterDate     = '';
        $this->filterPriority = '';
        $this->resetPage();
        unset($this->schedules);
    }

    public function updatingSearch()         { $this->resetPage(); unset($this->schedules); }
    public function updatingFilterStatus()   { $this->resetPage(); unset($this->schedules); }
    public function updatingFilterDate()     { $this->resetPage(); unset($this->schedules); }
    public function updatingFilterPriority() { $this->resetPage(); unset($this->schedules); }

    private function resetForm(): void
    {
        $this->editingId        = null;
        $this->order_id         = '';
        $this->client_name      = '';
        $this->delivery_address = '';
        $this->delivery_date    = '';
        $this->time_window_id   = '';
        $this->vehicle_id       = '';
        $this->driver_name      = '';
        $this->weight_kg        = '';
        $this->volume_m3        = '';
        $this->priority         = '';
        $this->status           = '';
        $this->notes            = '';
        $this->resetValidation();
    }

    /* ─────────────────────────────────────────
       RENDER
    ───────────────────────────────────────── */

    public function render(): View
    {
        return view('livewire.logistica.agendamento-entregas', [
            'schedules'   => $this->schedules,
            'stats'       => $this->stats,
            'salesOrders' => $this->salesOrders,
            'timeWindows' => $this->timeWindows,
            'vehicles'    => $this->vehicles,
            'statuses'    => DeliveryScheduleStatus::cases(),
            'priorities'  => DeliveryPriority::cases(),
        ]);
    }
}

