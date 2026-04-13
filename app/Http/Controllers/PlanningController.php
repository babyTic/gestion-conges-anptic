<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemandeConge;
use App\Models\TypeConge;
use App\Models\Direction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PlanningController extends Controller
{
    /**
     * 📅 Affiche la planification des congés
     */
    public function planification(Request $request)
    {
        $query = DemandeConge::with(['user.direction', 'type'])
            ->whereIn('statut', [
    'soumis',
    'approuve_responsable',
    'approuve_rh',
    'approuve_dg',
    'rejete',
    'termine'
]);

        // 🔎 Filtre par direction si renseignée
        if ($request->filled('direction')) {
            $query->whereHas('user', fn($q) => $q->where('direction_id', $request->direction));
        }

        $demandes = $query->get();

        // 🛠️ Préparer les événements pour FullCalendar
        $events = $demandes->map(function ($d) {
            try {
                $dateDebut = Carbon::parse($d->date_debut)->format('Y-m-d');
                $dateFin   = Carbon::parse($d->date_fin)->addDay()->format('Y-m-d'); // inclut la fin

                return [
                    'title' => $d->user->prenom . ' ' . $d->user->nom . ' (' . $d->type->nom . ')',
                    'start' => $dateDebut,
                    'end'   => $dateFin,
                    'statut'=> $d->statut,
                    'direction' => $d->user->direction->nom ?? null,
                    'lieu' => $d->lieu,
                ];
            } catch (\Exception $e) {
                // En cas de date mal formée, on ignore cette demande
                return null;
            }
        })->filter()->values();

        $directions = Direction::all();

        return view('conges.planification', [
            'events' => $events,
            'directions' => $directions
        ]);
    }
public function getCongeEvents()
{
    $demandes = \App\Models\DemandeConge::with('user.direction', 'type')->get();

    $events = $demandes->map(function ($d) {
        $dateDebut = $d->date_debut->format('Y-m-d');
        $dateFin = $d->date_fin->format('Y-m-d');

        return [
    'title' => $d->user->prenom . ' ' . $d->user->nom . ' (' . $d->type->nom . ')',
    'start' => $dateDebut,
    'end'   => $dateFin,
    'allDay' => true,

    // ✅ Place toutes les infos personnalisées ici :
    'extendedProps' => [
        'statut' => strtolower($d->statut),
        'direction' => $d->user->direction->nom ?? null ,
        'lieu' => $d->lieu,
    ],
];

    });

    return response()->json($events);
}

    /**
     * 📊 Calcule et retourne le solde de congés de l'utilisateur connecté
     */
public function solde()
{
    $user = Auth::user();

    // Récupération de tous les types de congés
    $typesConge = TypeConge::all();

    // Calcul des soldes pour l'année en cours uniquement
    $soldes = $typesConge->map(function ($type) use ($user) {
        $anneeEnCours = now()->year; // ✅ Cette ligne gère la réinitialisation automatique

        // Jours déjà utilisés par ce user pour ce type, sur l'année courante
        $joursPris = DemandeConge::where('user_id', $user->id)
            ->where('type_conge_id', $type->id)
            ->whereYear('date_debut', $anneeEnCours) // ✅ filtre par année en cours
            ->whereIn('statut', ['approuve_dg']) // ✅ uniquement ceux définitivement validés
            ->get()
            ->sum(function ($demande) {
                return \Carbon\Carbon::parse($demande->date_debut)
                    ->diffInDays(\Carbon\Carbon::parse($demande->date_fin)) + 1;
            });

        // Calcul du solde restant pour cette année
        $restants = max(0, $type->jours_alloues - $joursPris);

        return (object)[
            'type' => $type,
            'utilises' => $joursPris,
            'restants' => $restants,
        ];
    });

    return view('conges.solde', compact('soldes'));
}


}
