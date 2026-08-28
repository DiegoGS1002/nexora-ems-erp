<?php

namespace App\Console\Commands;

use App\Services\FiscalReferenceImportService;
use Illuminate\Console\Command;

class ImportFiscalReference extends Command
{
    protected $signature = 'fiscal:import-reference {type} {file}';

    protected $description = 'Importa ou atualiza uma tabela fiscal oficial em CSV';

    public function handle(FiscalReferenceImportService $service): int
    {
        $count = $service->import($this->argument('type'), $this->argument('file'));
        $this->info("{$count} registros fiscais importados.");

        return self::SUCCESS;
    }
}
