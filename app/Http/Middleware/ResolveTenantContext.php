<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantContext
{
    public function __construct(private readonly TenantContext $tenantContext) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->tenantContext->clear();

        try {
            $user = $request->user();

            abort_unless($user instanceof User, 401, 'Usuário não autenticado.');

            if ($user->isPlatformAdmin()) {
                return $next($request);
            }

            abort_if($user->company_id === null, 403, 'Usuário sem empresa vinculada.');

            $company = $user->company()->first();

            abort_if($company === null, 403, 'Empresa vinculada não encontrada.');
            abort_unless($company->is_active, 403, 'Empresa vinculada está inativa.');

            $this->tenantContext->set($company);

            return $next($request);
        } finally {
            $this->tenantContext->clear();
        }
    }
}
