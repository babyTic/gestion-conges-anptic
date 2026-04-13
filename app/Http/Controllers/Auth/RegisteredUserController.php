<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Afficher le formulaire d'inscription
     */
    public function create()
    {
        return view('auth.register');
    }
    public function store(Request $request)
    {
        // LOG: Début du processus
        logger()->info('Début inscription - Données reçues:', $request->all());

        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'direction' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:admin,employe,rh'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Générer l'identifiant
        $identifiant = User::genererIdentifiant($request->direction);
        logger()->info('Identifiant généré: ' . $identifiant);

        // Créer l'utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'identifiant' => $identifiant,
            'direction' => $request->direction,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        logger()->info('Utilisateur créé: ' . $user->id);

        // Set session flash
        session()->flash('identifiant_genere', $identifiant);
        logger()->info('Session flash set: ' . $identifiant);

        // Redirection
        logger()->info('Redirection vers dashboard');
        return redirect()->route('dashboard');
    }
    /**
     * Traiter l'inscription
     */

}
