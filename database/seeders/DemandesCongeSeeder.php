<?php

namespace Database\Seeders;

use App\Models\DemandeConge;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemandesCongeSeeder extends Seeder
{
    public function run()
    {
        $agents = User::where('role', 'agent')->get();

        foreach ($agents as $agent) {
            // Congé annuel
            DemandeConge::create([
                'user_id' => $agent->id,
                'type_conge_id' => 1, // Congé annuel
                'date_debut' => Carbon::today()->addDays(rand(10, 30)),
                'date_fin' => Carbon::today()->addDays(rand(35, 45)),
                'statut' => rand(0, 1) ? 'valide' : 'en_attente',
                'commentaire' => 'Congé annuel ' . Carbon::now()->year
            ]);

            // Congé maladie occasionnel
            if (rand(0, 1)) {
                DemandeConge::create([
                    'user_id' => $agent->id,
                    'type_conge_id' => 2, // Maladie
                    'date_debut' => Carbon::today()->subDays(rand(1, 30)),
                    'date_fin' => Carbon::today()->subDays(rand(1, 5)),
                    'statut' => 'valide',
                    'commentaire' => 'Grippe saisonnière'
                ]);
            }
        }
    }
}
