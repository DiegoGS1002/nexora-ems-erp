<?php

namespace App\Services;

use App\Models\Company;
use App\Models\FiscalNote;
use App\Models\FiscalNumberSequence;
use App\Tenancy\TenantContext;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LogicException;

class FiscalNumberService
{
    public function reserveForCurrentCompany(
        string $documentType,
        string $series,
        string $environment
    ): array {
        return $this->reserve($this->resolveCompany(), $documentType, $series, $environment);
    }

    public function reserve(
        Company $company,
        string $documentType,
        string $series,
        string $environment
    ): array {
        if (! in_array($documentType, ['nfe', 'nfce'], true)) {
            throw new \InvalidArgumentException('Tipo de documento fiscal inválido.');
        }

        if (! preg_match('/^\d{1,3}$/', $series)) {
            throw new \InvalidArgumentException('A série fiscal deve possuir entre 1 e 3 dígitos.');
        }

        if (! in_array($environment, ['production', 'homologation'], true)) {
            throw new \InvalidArgumentException('Ambiente fiscal inválido.');
        }

        $scope = [
            'company_id' => $company->id,
            'document_type' => $documentType,
            'series' => $series,
            'environment' => $environment,
        ];

        $this->ensureSequenceExists($scope);

        $number = DB::transaction(function () use ($scope): int {
            $sequence = FiscalNumberSequence::query()
                ->where($scope)
                ->lockForUpdate()
                ->firstOrFail();
            $number = $sequence->next_number;

            if ($number > 999999999) {
                throw new LogicException('A sequência fiscal atingiu o limite de nove dígitos.');
            }

            $sequence->update(['next_number' => $number + 1]);

            return $number;
        }, 3);

        return [
            'company_id' => $company->id,
            'invoice_number' => str_pad((string) $number, 9, '0', STR_PAD_LEFT),
        ];
    }

    private function ensureSequenceExists(array $scope): void
    {
        if (FiscalNumberSequence::query()->where($scope)->exists()) {
            return;
        }

        $lastNumber = FiscalNote::query()
            ->where('company_id', $scope['company_id'])
            ->where('type', $scope['document_type'])
            ->where('series', $scope['series'])
            ->where('environment', $scope['environment'])
            ->pluck('invoice_number')
            ->map(fn (string $number) => (int) $number)
            ->max() ?? 0;

        try {
            FiscalNumberSequence::query()->create($scope + ['next_number' => $lastNumber + 1]);
        } catch (QueryException $exception) {
            if (! FiscalNumberSequence::query()->where($scope)->exists()) {
                throw $exception;
            }
        }
    }

    private function resolveCompany(): Company
    {
        $tenantContext = app(TenantContext::class);

        if ($tenantContext->hasCompany()) {
            return $tenantContext->company();
        }

        $companyId = Auth::user()?->company_id;

        if ($companyId && ($company = Company::find($companyId))) {
            return $company;
        }

        throw new LogicException('Não é possível reservar numeração fiscal sem empresa definida.');
    }
}
