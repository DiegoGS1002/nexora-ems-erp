<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificate;

class FiscalCertificateMonitor
{
    public function inspect(Company $company): array
    {
        $path = $company->certificate_path ?: config('nfe.cert_path');
        if (! Storage::disk(config('nfe.cert_disk'))->exists($path)) {
            return ['status' => 'missing', 'expires_at' => null, 'days' => null];
        }

        $certificate = Certificate::readPfx(
            Storage::disk(config('nfe.cert_disk'))->get($path),
            config('nfe.cert_password')
        );
        $expiresAt = $certificate->getValidTo();
        $company->update(['certificate_path' => $path, 'certificate_expires_at' => $expiresAt]);
        $days = now()->diffInDays($expiresAt, false);

        return [
            'status' => $days < 0 ? 'expired' : ($days <= 30 ? 'expiring' : 'valid'),
            'expires_at' => $expiresAt,
            'days' => $days,
        ];
    }
}
