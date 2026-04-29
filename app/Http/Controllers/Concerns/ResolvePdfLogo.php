<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait ResolvePdfLogo
{
    /**
     * Mapeia extensão de arquivo para MIME type de imagem.
     */
    private function imageMime(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'webp'        => 'image/webp',
            'gif'         => 'image/gif',
            default       => 'image/png',
        };
    }

    /**
     * Retorna os dados da logo para o PDF:
     *  - logo: base64 data-uri da imagem (empresa ou fallback Nexora)
     *  - company_name: nome fantasia da empresa vinculada ao usuário
     */
    protected function resolvePdfLogo(): array
    {
        $user    = Auth::user();
        $company = $user && $user->company_id
            ? Company::find($user->company_id)
            : null;

        $companyName = $company?->name ?? config('app.name', 'Nexora');

        // Logo da empresa (salva em storage/app/public/...)
        if ($company && $company->logo) {
            $path = storage_path('app/public/' . $company->logo);

            if (file_exists($path)) {
                $mime   = $this->imageMime($path);
                $base64 = base64_encode(file_get_contents($path));

                return [
                    'logo'         => "data:{$mime};base64,{$base64}",
                    'company_name' => $companyName,
                ];
            }
        }

        // Fallback: logo padrão do Nexora
        $fallback = public_path('images/logo.png');

        if (file_exists($fallback)) {
            $mime   = $this->imageMime($fallback);
            $base64 = base64_encode(file_get_contents($fallback));

            return [
                'logo'         => "data:{$mime};base64,{$base64}",
                'company_name' => $companyName,
            ];
        }

        return [
            'logo'         => null,
            'company_name' => $companyName,
        ];
    }
}


