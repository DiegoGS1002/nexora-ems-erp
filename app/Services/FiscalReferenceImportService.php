<?php

namespace App\Services;

use App\Models\FiscalReferenceCode;

class FiscalReferenceImportService
{
    public function import(string $type, string $path): int
    {
        if (! in_array($type, ['ncm', 'cest', 'cbenef', 'payment_method', 'cclasstrib'], true)) {
            throw new \InvalidArgumentException('Tipo de tabela fiscal não suportado.');
        }

        $handle = fopen($path, 'rb');
        if (! $handle) {
            throw new \RuntimeException('Não foi possível abrir o arquivo fiscal.');
        }
        $header = fgetcsv($handle, separator: ';');
        $count = 0;
        while (($row = fgetcsv($handle, separator: ';')) !== false) {
            $data = array_combine($header, $row);
            FiscalReferenceCode::updateOrCreate(
                ['type' => $type, 'code' => $data['code']],
                ['description' => $data['description'] ?? null, 'metadata' => $data]
            );
            $count++;
        }
        fclose($handle);

        return $count;
    }
}
