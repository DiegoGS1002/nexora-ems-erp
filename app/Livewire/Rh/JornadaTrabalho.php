<?php

namespace App\Livewire\Rh;

use App\Enums\TimeRecordStatus;
use App\Livewire\Forms\TimeRecordForm;
use App\Models\Employees;
use App\Models\WorkShift;
use App\Services\JornadaService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Jornada de Trabalho')]
class JornadaTrabalho extends Component
{
    /* ─────────────────────────────────────
      FORM OBJECT
     ─────────────────────────────────────*/
    public TimeRecordForm $form;

    /* ─────────────────────────────────────
      MODAL STATE
     ─────────────────────────────────────*/
    public bool $showModal    = false;
    public bool $isEditing    = false;
    public ?int $editingId    = null;
    public bool $showDeleteModal = false;
    public ?int $deletingId   = null;
    public bool $showShiftModal = false;

    /* ─────────────────────────────────────
      FILTERS
     ─────────────────────────────────────*/
    public string $filterDate   = '';
    public string $search       = '';
    public string $filterStatus = '';
    public string $viewMode     = 'grid';

    /* ─────────────────────────────────────
      SHIFT FORM
     ─────────────────────────────────────*/
    public string  $shiftName         = '';
    public string  $shiftDescription  = '';
    public string  $shiftStart        = '';
    public string  $shiftEnd          = '';
    public int     $shiftBreak        = 60;
    public ?int    $editingShiftId    = null;

    /* ─────────────────────────────────────
      BOOT / MOUNT
     ─────────────────────────────────────*/
    public function mount(): void
    {
        $this->filterDate = now()->format('Y-m-d');
        $this->form->date = $this->filterDate;
    }

    /* ─────────────────────────────────────
      COMPUTED PROPERTIES
     ─────────────────────────────────────*/
    #[Computed]
    public function kpis(): array
    {
        return app(JornadaService::class)->getKpis($this->filterDate);
    }

    #[Computed]
    public function presenceGrid()
    {
        $grid = app(JornadaService::class)->getPresenceGrid($this->filterDate);

        if ($this->search !== '') {
            $grid = $grid->filter(fn ($r) => str_contains(
                mb_strtolower($r->employee->name),
                mb_strtolower($this->search)
            ));
        }

        if ($this->filterStatus !== '') {
            $grid = $grid->filter(fn ($r) => $r->status->value === $this->filterStatus);
        }

        return $grid->values();
    }

    #[Computed]
    public function timelineRecords()
    {
        return app(JornadaService::class)->getTimelineRecords($this->filterDate);
    }

    #[Computed]
    public function employees()
    {
        return Employees::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function shifts()
    {
        return app(JornadaService::class)->getActiveShifts();
    }

    #[Computed]
    public function allShifts()
    {
        return WorkShift::orderBy('name')->get();
    }

    #[Computed]
    public function statuses(): array
    {
        return TimeRecordStatus::cases();
    }

    /* ─────────────────────────────────────
      MODAL ACTIONS — TIME RECORD
     ─────────────────────────────────────*/
    public function openCreate(): void
    {
        $this->resetForm();
        $this->form->date = $this->filterDate;
        $this->showModal  = true;
        $this->isEditing  = false;
    }

    public function openEdit(int $id): void
    {
        $record = \App\Models\TimeRecord::findOrFail($id);

        $this->form->fill([
            'employee_id'   => $record->employee_id,
            'work_shift_id' => $record->work_shift_id,
            'date'          => $record->date->format('Y-m-d'),
            'clock_in'      => $record->clock_in?->format('H:i') ?? '',
            'break_start'   => $record->break_start?->format('H:i') ?? '',
            'break_end'     => $record->break_end?->format('H:i') ?? '',
            'clock_out'     => $record->clock_out?->format('H:i') ?? '',
            'status'        => $record->status->value,
            'observation'   => $record->observation ?? '',
        ]);

        $this->editingId = $id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(JornadaService $service): void
    {
        $this->form->validate();

        $data = $this->form->all();

        $service->saveRecord($data);

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', $this->isEditing
            ? 'Registro de ponto atualizado com sucesso!'
            : 'Ponto registrado com sucesso!'
        );

        unset($this->kpis, $this->presenceGrid, $this->timelineRecords);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRecord(JornadaService $service): void
    {
        if ($this->deletingId) {
            $service->deleteRecord($this->deletingId);
            session()->flash('success', 'Registro excluído com sucesso!');
            unset($this->kpis, $this->presenceGrid, $this->timelineRecords);
        }

        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    /* ─────────────────────────────────────
      MODAL ACTIONS — WORK SHIFT
     ─────────────────────────────────────*/
    public function openShiftModal(?int $id = null): void
    {
        $this->resetShiftForm();

        if ($id) {
            $shift = WorkShift::findOrFail($id);
            $this->editingShiftId   = $id;
            $this->shiftName        = $shift->name;
            $this->shiftDescription = $shift->description ?? '';
            $this->shiftStart       = $shift->start_time;
            $this->shiftEnd         = $shift->end_time;
            $this->shiftBreak       = $shift->break_duration;
        }

        $this->showShiftModal = true;
    }

    public function saveShift(): void
    {
        $this->validate([
            'shiftName'  => 'required|string|max:100',
            'shiftStart' => 'required|date_format:H:i',
            'shiftEnd'   => 'required|date_format:H:i',
            'shiftBreak' => 'required|integer|min:0|max:480',
        ]);

        $payload = [
            'name'           => $this->shiftName,
            'description'    => $this->shiftDescription ?: null,
            'start_time'     => $this->shiftStart,
            'end_time'       => $this->shiftEnd,
            'break_duration' => $this->shiftBreak,
        ];

        if ($this->editingShiftId) {
            WorkShift::where('id', $this->editingShiftId)->update($payload);
            session()->flash('success', 'Turno atualizado com sucesso!');
        } else {
            WorkShift::create($payload);
            session()->flash('success', 'Turno criado com sucesso!');
        }

        $this->resetShiftForm();
        $this->showShiftModal = false;
        unset($this->allShifts, $this->shifts);
    }

    public function deleteShift(int $id): void
    {
        WorkShift::where('id', $id)->delete();
        session()->flash('success', 'Turno removido.');
        unset($this->allShifts, $this->shifts);
    }

    /* ─────────────────────────────────────
      HELPERS
     ─────────────────────────────────────*/
    private function resetForm(): void
    {
        $this->form->reset();
        $this->form->status = TimeRecordStatus::Active->value;
        $this->editingId    = null;
        $this->isEditing    = false;
    }

    private function resetShiftForm(): void
    {
        $this->shiftName        = '';
        $this->shiftDescription = '';
        $this->shiftStart       = '';
        $this->shiftEnd         = '';
        $this->shiftBreak       = 60;
        $this->editingShiftId   = null;
    }

    public function updatedFilterDate(): void
    {
        $this->form->date = $this->filterDate;
        unset($this->kpis, $this->presenceGrid, $this->timelineRecords);
    }

    public function updatedSearch(): void
    {
        unset($this->presenceGrid);
    }

    public function updatedFilterStatus(): void
    {
        unset($this->presenceGrid);
    }

    public function render(): View
    {
        return view('livewire.rh.jornada-trabalho.index');
    }
}

