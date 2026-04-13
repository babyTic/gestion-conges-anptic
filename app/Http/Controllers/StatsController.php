<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\DemandeConge;
use App\Models\Direction;
use App\Models\Document;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DemandesExport;


class StatsController extends Controller
{
    public function index()
    {
        try {
            // Totaux
            $totalAgents = User::count();

            // Utilisateurs en congé aujourd'hui (demandes approuvées qui couvrent la date actuelle)
            $agentsEnConge = DemandeConge::where('statut', 'approuve_dg')
                ->whereYear('date_debut', now()->year)
                ->distinct('user_id')
                ->count('user_id');


            $documentsArchives = Document::count();
            $totalDemandes = DemandeConge::count();
            $demandesEnAttente = DemandeConge::where('statut', 'En attente')->count();
            $congesApprouves = DemandeConge::where('statut', 'approuve_dg')->count();
            $congesRefuses = DemandeConge::where('statut', 'Rejeté')->count();

            // --- Evolution mensuelle pour l'année courante et l'année passée
            $currentYear = now()->year;
            $lastYear = now()->subYear()->year;

            // Utilise EXTRACT pour compatibilité Postgres / MySQL
            $thisYearRaw = DemandeConge::select(DB::raw('EXTRACT(MONTH FROM created_at) AS mois'), DB::raw('COUNT(*) AS total'))
                ->whereYear('created_at', $currentYear)
                ->groupBy('mois')
                ->pluck('total', 'mois')
                ->toArray();

            $lastYearRaw = DemandeConge::select(DB::raw('EXTRACT(MONTH FROM created_at) AS mois'), DB::raw('COUNT(*) AS total'))
                ->whereYear('created_at', $lastYear)
                ->groupBy('mois')
                ->pluck('total', 'mois')
                ->toArray();

            $monthlyLabels = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'];

            // Construire des arrays de 12 mois (valeurs 0 si pas de données)
            $monthlyDataThisYear = [];
            $monthlyDataLastYear = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthlyDataThisYear[] = isset($thisYearRaw[$m]) ? (int)$thisYearRaw[$m] : 0;
                $monthlyDataLastYear[] = isset($lastYearRaw[$m]) ? (int)$lastYearRaw[$m] : 0;
            }

            // --- Répartition par rôle
            $rolesLabelsRaw = User::select('role')->distinct()->pluck('role')->toArray();
            // Lisible : capitalize et RH -> RH
            $rolesLabels = array_map(function($r) {
                return $r === 'rh' ? 'RH' : ucfirst($r);
            }, $rolesLabelsRaw);

            $rolesData = [];
            foreach ($rolesLabelsRaw as $r) {
                $rolesData[] = User::where('role', $r)->count();
            }

            // --- Répartition par direction (utilise table directions)
            $directions = Direction::all();
            $departmentsLabels = $directions->pluck('nom')->toArray();
            $departmentsData = [];
            foreach ($directions as $dir) {
                $departmentsData[] = User::where('direction_id', $dir->id)->count();
            }

        } catch (\Exception $e) {
            // fallback safe values (ne plante pas la vue)
            $totalAgents = $agentsEnConge = $totalDemandes = $demandesEnAttente = $congesApprouves = $congesRefuses = 0;
            $monthlyLabels = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'];
            $monthlyDataThisYear = $monthlyDataLastYear = array_fill(0, 12, 0);
            $rolesLabels = ['Admin','Agent','Responsable','RH'];
            $rolesData = array_fill(0, count($rolesLabels), 0);
            $departmentsLabels = ['DSA','DICOM','DIG','Autres'];
            $departmentsData = array_fill(0, count($departmentsLabels), 0);
        }

        // Retourne la vue AVEC un array — évite compact() multi-lignes éventuel problème
        return view('stats.dashboard', [
            'totalAgents' => $totalAgents,
            'agentsEnConge' => $agentsEnConge,
            'totalDemandes' => $totalDemandes,
            'demandesEnAttente' => $demandesEnAttente,
            'congesApprouves' => $congesApprouves,
            'congesRefuses' => $congesRefuses,
            'monthlyLabels' => $monthlyLabels,
            'monthlyDataThisYear' => $monthlyDataThisYear,
            'monthlyDataLastYear' => $monthlyDataLastYear,
            'rolesLabels' => $rolesLabels,
            'rolesData' => $rolesData,
            'departmentsLabels' => $departmentsLabels,
            'departmentsData' => $departmentsData,
        ]);
    }
    public function export() { return Excel::download(new DemandesExport, 'demandes.xlsx'); }
public function reports()
{
    $demandes = DemandeConge::latest()->paginate(20);

    return view('stats.reports', [
        'demandes' => $demandes
    ]);
}

    
}
