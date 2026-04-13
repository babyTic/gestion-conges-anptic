<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametre;

class ParametreController extends Controller
{
    // 🔐 Page d’édition (RH uniquement)
    public function editDecision()
    {
        $parametre = Parametre::firstOrCreate(
            ['cle' => 'decision_conge'],
            ['valeur' => 'Décision par défaut (modifiable par le RH)']
        );

        return view('decision.edit', compact('parametre'));
    }

    // 💾 Mise à jour
    public function updateDecision(Request $request)
    {
        $request->validate([
            'decision' => 'required|string|max:255'
        ]);

        Parametre::updateOrCreate(
            ['cle' => 'decision_conge'],
            ['valeur' => $request->decision]
        );

        return back()->with('success', 'Décision mise à jour avec succès ✅');
    }
}
