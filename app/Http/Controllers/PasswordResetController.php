<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    // 1. Affiche le popup avec identifiant
    public function showIdentifiantForm()
    {
        return view('auth.passwords.identifiant');
    }

    // 2. Vérifie l’identifiant et redirige vers reset
    public function verifyIdentifiant(Request $request)
    {
        $request->validate([
            'identifiant' => 'required|string|exists:users,identifiant'
        ]);

        $user = User::where('identifiant', $request->identifiant)->first();

        return redirect()->route('password.reset', $user->id);
    }

    // 3. Affiche la page reset password
    public function showResetForm(User $user)
    {
        return view('auth.passwords.reset', compact('user'));
    }

    // 4. Met à jour le mot de passe
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', '✅ Mot de passe réinitialisé avec succès !');
    }
}
