<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    /**
     * Afficher le formulaire d'inscription (WEB)
     */
    public function showRegisterForm()
    {
        $directions = \App\Models\Direction::all();
        return view('auth.register', compact('directions'));
    }

    /**
     * Traiter l'inscription (WEB)
     */
    public function registerWeb(Request $request)
    {
        // Validation des champs
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'role' => 'required|in:agent,responsable,rh,admin',
            'direction_id' => 'required|exists:directions,id',  // ✅ clé étrangère
            'password' => 'required|string|min:8|confirmed',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Générer un identifiant unique
        $identifiant = $this->generateUniqueId();

        // Création de l’utilisateur
        $user = User::create([
            'nom'        => $request->nom,
            'prenom'     => $request->prenom,
            'identifiant'=> $identifiant,
            'role'       => $request->role,
            'direction_id'  => $request->direction_id,
            'password'   => Hash::make($request->password),
        ]);

        // Redirection vers la page de succès
        return redirect()->route('register.success')
            ->with('generated_id', $identifiant)
            ->with('user_nom', $user->nom)
            ->with('user_prenom', $user->prenom);

        // ⚡ Notification personnalisée pour l'utilisateur connecté
        $user = Auth::user();

        return redirect()->route('dashboard')->with('notification', [
            'user'    => $user ? $user->prenom . ' ' . $user->nom : 'Système',
            'message' => 'Votre compte a été créé avec succès.',
            'avatar'  => asset('assets/images/avatar.png')
        ]);
    }

    /**
     * Afficher la page succès après inscription
     */
    public function showSuccessPage(Request $request)
    {
        if (!$request->session()->has('generated_id')) {
            return redirect()->route('register');
        }
        return view('auth.register-success');
    }

    /**
     * Connexion (WEB ou API)
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifiant' => 'required|string',
            'password'    => 'required|string',
        ]);

        $user = User::where('identifiant', $request->identifiant)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['identifiant' => 'Identifiants incorrects']);
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Connexion réussie !');
    }


    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Déconnexion réussie');
    }

    /**
     * Générer un identifiant unique (ex: ANPT-123456)
     */
    private function generateUniqueId()
    {
        $prefix = 'ANPT';
        do {
            $randomNumber = mt_rand(100000, 999999);
            $identifiant = $prefix . '-' . $randomNumber;
        } while (User::where('identifiant', $identifiant)->exists());

        return $identifiant;
    }
    public function showForgotIdentifiantForm()
{
    return view('auth.passwords.identifiant');
}

public function sendResetLink(Request $request)
{
    $request->validate([
        'identifiant' => 'required|string'
    ]);

    $user = User::where('identifiant', $request->identifiant)->first();

    if (!$user) {
        return back()->withErrors(['identifiant' => 'Identifiant non trouvé.']);
    }

    $token = Str::random(60);

    DB::table('password_resets')->updateOrInsert(
        ['identifiant' => $user->identifiant],
        [
            'identifiant' => $user->identifiant,
            'token' => $token,
            'created_at' => now()
        ]
    );

    // Pour l’instant on redirige direct vers la page reset (tu pourras ajouter un mail plus tard si besoin)
    return redirect()->route('password.reset', [
        'token' => $token,
        'identifiant' => $user->identifiant
    ]);
}

public function showResetForm(Request $request, $token)
{
    return view('auth.passwords.reset', [
        'token' => $token,
        'identifiant' => $request->identifiant
    ]);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'identifiant' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $reset = DB::table('password_resets')
        ->where('identifiant', $request->identifiant)
        ->where('token', $request->token)
        ->first();

    if (!$reset) {
        return back()->withErrors(['identifiant' => 'Lien de réinitialisation invalide ou expiré.']);
    }

    $user = User::where('identifiant', $request->identifiant)->firstOrFail();
    $user->password = Hash::make($request->password);
    $user->save();

    DB::table('password_resets')->where('identifiant', $request->identifiant)->delete();

    return redirect()->route('login')->with('status', 'Mot de passe réinitialisé avec succès !');
}
}
