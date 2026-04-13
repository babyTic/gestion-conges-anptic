<?php

namespace Database\Seeders;
namespace Database\Seeders;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Seeder;

class JournalApprovalSeeder extends Seeder
{
    public function run()
    {
        $demandes = DemandeConge::where('statut', 'valide')->get();
        $rhUsers = User::where('role', 'rh')->get();

        foreach ($demandes as $demande) {
            Journal::create([
                'demande_conge_id' => $demande->id,
                'user_id' => $rhUsers->random()->id,
                'action' => 'approbation',
                'commentaire' => 'Congé approuvé conformément à la politique'
            ]);
        }

        // Ajout de quelques refus
        $demandesRefusees = DemandeConge::where('statut', 'refuse')->take(3)->get();
        foreach ($demandesRefusees as $demande) {
            Journal::create([
                'demande_conge_id' => $demande->id,
                'user_id' => $rhUsers->random()->id,
                'action' => 'rejet rh',
                'commentaire' => 'Dépassement des jours disponibles'
            ]);
        }
    }
}
