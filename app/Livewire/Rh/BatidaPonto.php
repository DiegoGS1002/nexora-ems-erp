<?php

namespace App\Livewire\Rh;

use App\Models\Employees;
use App\Models\TimeRecord;
use App\Services\PontoService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Batida de Ponto')]
class BatidaPonto extends Component
{
    public ?Employees $employee = null;
    public ?TimeRecord $todayRecord = null;
    public string $currentTime = '';
    public ?float $latitude = null;
    public ?float $longitude = null;
    public string $locationStatus = 'pending';
    public string $locationName = 'Localizando...';
    public array $todayRecords = [];
    public string $nextAction = 'Registrar Entrada';
    public ?string $expectedEndTime = null;
    public bool $isJourneyComplete = false;

    public function mount(PontoService $pontoService): void
    {
        $user = auth()->user();

        if (! $user) {
            redirect()->route('login');
            return;
        }

        $this->employee = $pontoService->getEmployeeByEmail($user->email);

        if (! $this->employee) {
            session()->flash('error', 'Nenhum cadastro de funcionário encontrado para este usuário.');
            return;
        }

        $this->syncTodayState($pontoService);
        $this->currentTime = now()->format('H:i:s');
    }

    public function updateLocation(float $lat, float $lng, string $locationName = null): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->locationStatus = 'success';
        $this->locationName = $locationName ?? "Lat: {$lat}, Lng: {$lng}";
    }

    public function locationDenied(): void
    {
        $this->locationStatus = 'denied';
        $this->locationName = 'Localização não autorizada';
    }

    public function registerPonto(PontoService $pontoService): void
    {
        if (! $this->employee) {
            $this->dispatch('show-error', message: 'Funcionário não encontrado.');
            return;
        }

        $result = $pontoService->registerClockAction(
            $this->employee->id,
            request()->ip(),
            $this->latitude,
            $this->longitude
        );

        if ($result['success']) {
            $this->syncTodayState($pontoService);
            $this->dispatch('show-success', message: $result['message']);
            $this->dispatch('ponto-registered', action: $result['action']);
        } else {
            $this->dispatch('show-error', message: $result['message']);
        }
    }

    private function syncTodayState(PontoService $pontoService): void
    {
        if (! $this->employee) {
            $this->todayRecord = null;
            $this->todayRecords = [];
            $this->nextAction = 'Registrar Entrada';
            $this->expectedEndTime = null;
            $this->isJourneyComplete = false;

            return;
        }

        $this->todayRecord = $pontoService->getTodayRecord($this->employee->id);
        $this->todayRecords = $pontoService->getTodayRecords($this->employee->id)->values()->all();
        $this->nextAction = $this->todayRecord
            ? $pontoService->getNextAction($this->todayRecord)
            : 'Registrar Entrada';
        $this->expectedEndTime = $this->todayRecord
            ? $pontoService->calculateExpectedEndTime($this->todayRecord)
            : null;
        $this->isJourneyComplete = $this->todayRecord?->clock_out !== null;
    }

    public function render(): View
    {
        return view('livewire.rh.batida-ponto');
    }
}
