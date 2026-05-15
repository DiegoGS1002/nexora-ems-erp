<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UploadAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Exibir página de perfil do usuário autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        return view('perfil.perfil.profile', compact('user'));
    }

    /**
     * Atualizar informações pessoais.
     */
    public function updateInfo(UpdateProfileInfoRequest $request)
    {
        $user = Auth::user();

        $user->update($request->validated());

        return back()->with('success', 'Informações atualizadas com sucesso.');
    }

    /**
     * Alterar senha do usuário.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();


        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'A senha atual está incorreta.'])
                ->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Senha alterada com sucesso.');
    }

    /**
     * Fazer upload do avatar do usuário.
     */
    public function uploadAvatar(UploadAvatarRequest $request)
    {
        $user = Auth::user();


        // Remover avatar anterior, se existir
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Avatar atualizado com sucesso.');
    }

    /**
     * Remover avatar (voltar ao avatar gerado).
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return back()->with('success', 'Avatar removido.');
    }
}
