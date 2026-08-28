<?php

use App\Models\Company;
use App\Models\FiscalNote;
use App\Models\FiscalNumberSequence;
use App\Services\FiscalNumberService;
use Illuminate\Database\QueryException;

it('reserves sequential fiscal numbers atomically within a scope', function () {
    $company = Company::factory()->create();
    $service = app(FiscalNumberService::class);

    $first = $service->reserve($company, 'nfe', '1', 'production');
    $second = $service->reserve($company, 'nfe', '1', 'production');

    expect($first['invoice_number'])->toBe('000000001')
        ->and($second['invoice_number'])->toBe('000000002')
        ->and(FiscalNumberSequence::sole()->next_number)->toBe(3);
});

it('keeps independent sequences by company document series and environment', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $service = app(FiscalNumberService::class);

    $service->reserve($companyA, 'nfe', '1', 'production');

    expect($service->reserve($companyA, 'nfe', '2', 'production')['invoice_number'])->toBe('000000001')
        ->and($service->reserve($companyA, 'nfce', '1', 'production')['invoice_number'])->toBe('000000001')
        ->and($service->reserve($companyA, 'nfe', '1', 'homologation')['invoice_number'])->toBe('000000001')
        ->and($service->reserve($companyB, 'nfe', '1', 'production')['invoice_number'])->toBe('000000001');
});

it('initializes a sequence from the largest numeric value in its exact scope', function () {
    $company = Company::factory()->create();
    FiscalNote::factory()->create([
        'company_id' => $company->id,
        'invoice_number' => '000000099',
        'series' => '1',
        'type' => 'nfe',
        'environment' => 'production',
    ]);
    FiscalNote::factory()->create([
        'company_id' => $company->id,
        'invoice_number' => '000000900',
        'series' => '2',
        'type' => 'nfe',
        'environment' => 'production',
    ]);

    $reservation = app(FiscalNumberService::class)->reserve($company, 'nfe', '1', 'production');

    expect($reservation['invoice_number'])->toBe('000000100');
});

it('enforces unique fiscal numbers in the database for the same scope', function () {
    $company = Company::factory()->create();
    $attributes = [
        'company_id' => $company->id,
        'invoice_number' => '000000001',
        'series' => '1',
        'type' => 'nfe',
        'environment' => 'production',
    ];
    FiscalNote::factory()->create($attributes);

    expect(fn () => FiscalNote::factory()->create($attributes))
        ->toThrow(QueryException::class);
});
