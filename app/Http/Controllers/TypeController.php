<?php

namespace App\Http\Controllers;

use App\Models\TypeConge;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'jours_alloues' => 'required|integer|min:1',
        ]);

        TypeConge::create([
            'nom' => $request->nom,
            'jours_alloues' => $request->jours_alloues,
        ]);

        return redirect()->route('settings.index')->with('success', 'Type de congé ajouté avec succès.');
    }

    public function update(Request $request, TypeConge $type)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'jours_alloues' => 'required|integer|min:1',
        ]);

        $type->update([
            'nom' => $request->nom,
            'jours_alloues' => $request->jours_alloues,
        ]);

        return redirect()->route('settings.index')->with('success', 'Type de congé mis à jour avec succès.');
    }

    public function destroy(TypeConge $type)
    {
        $type->delete();

        return redirect()->route('settings.index')->with('success', 'Type de congé supprimé.');
    }
}
