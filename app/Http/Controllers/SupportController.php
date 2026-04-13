<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    /**
     * Affiche la page support
     */
    public function index()
    {
        return view('support.index');
    }

    /**
     * "Envoie" du message
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // ✅ On enregistre dans les logs au lieu d'une BDD
        Log::info('📩 Message support reçu', [
            'user' => auth()->user()->nom ?? 'Invité',
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Message envoyé au support (simulé).');
    }
}
