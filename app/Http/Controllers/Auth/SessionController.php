<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! auth()->attempt($credentials, true)) {
            LogService::warning(
                'LOGIN_FALHOU',
                "Tentativa de login com e-mail \"{$credentials['email']}\" falhou.",
                'Segurança',
                ['email' => $credentials['email'], 'ip' => $request->ip()]
            );

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Regra: administradores sempre têm acesso livre
        // Regra: usuários inativos não podem fazer login
        if (! auth()->user()->is_admin && ! auth()->user()->is_active) {
            auth()->logout();
            throw ValidationException::withMessages([
                'email' => 'Usuário inativo, entre em contato com o suporte para mais informações.',
            ]);
        }

        $request->session()->regenerate();

        auth()->user()->update([
            'last_login_at' => now(),
        ]);

        LogService::success(
            'LOGIN',
            'Usuário realizou login no sistema.',
            'Segurança',
            ['ip' => $request->ip()]
        );

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            LogService::success(
                'LOGOUT',
                'Usuário realizou logout do sistema.',
                'Segurança',
                ['ip' => $request->ip()]
            );
        }

        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

