<?php

namespace Database\Seeders;

use App\Models\Direction;
use Illuminate\Database\Seeder;

class DirectionSeeder extends Seeder
{
    public function run()
    {
        $directions = [
            ['nom' => 'Direction Ressources Humaines'],
            ['nom' => 'Direction Informatique'],
            ['nom' => 'Direction Financière'],
            ['nom' => 'Direction Marketing']
        ];

        foreach ($directions as $direction) {
            Direction::firstOrCreate(
                ['nom' => $direction['nom']], // Recherche par nom unique
                $direction
            );
        }
    }
}
