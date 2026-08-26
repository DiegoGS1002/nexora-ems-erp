<?php

namespace App\Tenancy;

use App\Models\Company;
use LogicException;

class TenantContext
{
    private ?Company $company = null;

    public function set(Company $company): void
    {
        $this->company = $company;
    }

    public function hasCompany(): bool
    {
        return $this->company !== null;
    }

    public function company(): Company
    {
        return $this->company ?? throw new LogicException('O contexto empresarial não foi definido.');
    }

    public function companyId(): int
    {
        return $this->company()->id;
    }

    public function clear(): void
    {
        $this->company = null;
    }
}
