<?php

namespace App\Services;

use App\Models\Company;
use App\Models\FiscalNote;
use Illuminate\Support\Facades\Storage;

class FiscalArchiveService
{
    public function backup(Company $company): int
    {
        $source = Storage::disk(config('nfe.xml_disk'));
        $backup = Storage::disk(config('nfe.backup_disk'));
        $count = 0;

        FiscalNote::where('company_id', $company->id)
            ->whereNotNull('xml_path')
            ->each(function (FiscalNote $note) use ($source, $backup, &$count): void {
                if (! $source->exists($note->xml_path)) {
                    return;
                }
                $destination = "fiscal-backup/{$note->company_id}/{$note->xml_path}";
                if (! $backup->exists($destination)) {
                    $backup->put($destination, $source->get($note->xml_path));
                    $count++;
                }
            });

        return $count;
    }
}
