<?php

namespace Database\Seeders;

use App\Models\Planning;
use Illuminate\Database\Seeder;

class PlanningCongeSeeder extends Seeder
{
    public function run()
    {
        $demandes = DemandeConge::with('type')->get();

        foreach ($demandes as $demande) {
            $date = $demande->date_debut;
            while ($date <= $demande->date_fin) {
                Planning::create([
                    'demande_conge_id' => $demande->id,
                    'jour' => $date,
                    'statut' => $demande->statut === 'valide' ? 'validee' : 'prevue'
                ]);
                $date = $date->addDay();
            }
        }
    }
}
