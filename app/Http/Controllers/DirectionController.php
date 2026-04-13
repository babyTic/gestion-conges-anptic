<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:directions,nom',
        ]);

        Direction::create(['nom' => $request->nom]);

        return redirect()->route('settings.index')->with('success', 'Direction ajoutée avec succès.');
    }

    public function update(Request $request, Direction $direction)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:directions,nom,' . $direction->id,
        ]);

        $direction->update(['nom' => $request->nom]);

        return redirect()->route('settings.index')->with('success', 'Direction mise à jour avec succès.');
    }

    public function destroy(Direction $direction)
    {
        $direction->delete();

        return redirect()->route('settings.index')->with('success', 'Direction supprimée.');
    }
}
