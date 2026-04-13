<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Direction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserController extends Controller
{
    public function create()
    {
        $directions = Direction::all();
        return view('settings.users.create', compact('directions'));
    }

    /**
 * Liste des utilisateurs (page settings.index)
 */

public function index(Request $request)
{
    $directions = Direction::all();

    $query = User::with('direction')->orderBy('nom');

    // Filtre recherche (prénom / nom / identifiant)
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('prenom', 'like', "%{$search}%")
              ->orWhere('nom', 'like', "%{$search}%")
              ->orWhere('identifiant', 'like', "%{$search}%");
        });
    }

    // Pagination en conservant les query params
    $users = $query->paginate(15)->withQueryString();

    return view('settings.index', compact('users', 'directions'));
}


    public function store(Request $request)
{
    $request->validate([
        'prenom' => 'required|string|max:255',
        'nom' => 'required|string|max:255',
        'identifiant' => 'required|string|max:255' ,
        'sexe' => 'required|in:H,F',
        'role' => 'required|string|max:255',
        'direction_id' => 'nullable|exists:directions,id',
        'password' => 'required|string|min:6',
    ]);


    // Création utilisateur
    $user = User::create([
    'prenom'       => $request->prenom,
    'nom'          => $request->nom,
    'sexe'         => $request->sexe,
    'identifiant'  => $request->identifiant, // ✅ FIX
    'role'         => $request->role,
    'direction_id' => $request->direction_id,
    'password'     => Hash::make($request->password),
]);

    // ✅ Redirection avec données en session (flash data)
    return redirect()->route('users.success')
        ->with([
            'user_prenom' => $user->prenom,
            'user_nom' => $user->nom,
            'generated_id' => $user->identifiant,
        ]);
}



    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }

public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'prenom'       => 'required|string|max:255',
        'nom'          => 'required|string|max:255',
        'sexe'         => 'required|in:H,F',
        'role'         => 'required|string|in:admin,rh,responsable,agent,dg',
        'direction_id' => 'nullable|exists:directions,id',
        'password'     => 'nullable|string|min:6|confirmed', // confirmation via password_confirmation
    ]);

    if (!empty($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        unset($validated['password']);
    }

    $user->update($validated);

    return redirect()->route('settings.index')->with('success', '✅ Utilisateur mis à jour avec succès.');
}



    public function edit(User $user)

    {
    return view('users.edit', compact('user'));
    }

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,csv'
    ]);

    $import = new \App\Imports\UsersImport;
    Excel::import($import, $request->file('file'));

    if (!empty($import->customErrors)) {
    return back()->with('error', implode('<br>', $import->customErrors));
}

    return back()->with('success', '✅ Utilisateurs importés avec succès !');
}


}
