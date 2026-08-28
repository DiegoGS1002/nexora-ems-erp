<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\NFeDistributionService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SynchronizeNFeDistribution implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 3600;

    public function __construct(public int $companyId, public string $environment) {}

    public function uniqueId(): string
    {
        return "{$this->companyId}:{$this->environment}";
    }

    public function handle(NFeDistributionService $service): void
    {
        $service->synchronize(Company::findOrFail($this->companyId), $this->environment);
    }
}
