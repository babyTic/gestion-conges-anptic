<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class NotificationController extends Controller
{
    /**
     * Affiche les notifications (fictives pour l'exemple)
     */


    public function index()
    {
        return view('notifications.index');
    }

    public function data()
    {
        $demandes = DB::table('demandes_conge')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($d) {
                return [
                    'status' => $d->statut,
                    'message' => "Votre demande de {$d->date_debut} à {$d->date_fin} a été " .
                                ($d->statut === 'approuve_dg' ? '✅ approuvée' :
                                 ($d->statut === 'rejete' ? '❌ rejetée' : '⏳ est en attente')),
                    'created_at' => $d->created_at,
                    'read_at' => null,
                ];
            });

        return response()->json($demandes);
    }



    /**
     * Simule le marquage comme lu
     */
    public function markAllAsRead()
    {
        // Pas de BDD → juste message flash
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues (simulation).');
    }

    /**
     * Supprime une notification (simulation)
     */
    public function destroy($id)
    {
        return back()->with('success', "Notification $id supprimée (simulation).");
    }
}
