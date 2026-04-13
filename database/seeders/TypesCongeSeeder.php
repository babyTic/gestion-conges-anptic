<?php

namespace Database\Seeders;

use App\Models\TypeConge;
use Illuminate\Database\Seeder;

class TypesCongeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'nom' => 'Congé annuel',
                'jours_alloues' => 30,
                'est_payee' => false
            ],
            [
                'nom' => 'Maternité',
                'jours_alloues' => 98,
                'est_payee' => false
            ]
        ];

        foreach ($types as $type) {
            TypeConge::firstOrCreate(
                ['nom' => $type['nom']],
                $type
            );
        }
    }
}
