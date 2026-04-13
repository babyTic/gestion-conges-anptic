<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run()
    {
        $demandesMedicales = DemandeConge::where('type_conge_id', 2)->get();

        foreach ($demandesMedicales as $demande) {
            Document::create([
                'demande_conge_id' => $demande->id,
                'chemin' => 'documents/justificatifs/med_' . $demande->id . '.pdf',
                'nom_original' => 'certificat_medical.pdf',
                'type' => 'justificatif',
                'decision' => 'N°2025-____/MTDPCE/SG/ANPTIC/SG/DRH'
            ]);
        }
    }
}
