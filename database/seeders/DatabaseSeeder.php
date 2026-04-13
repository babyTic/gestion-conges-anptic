<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DirectionSeeder::class,
            TypesCongeSeeder::class,
            UsersTableSeeder::class,
            DemandesCongeSeeder::class,
            PlanningCongeSeeder::class,
            JournalApprovalSeeder::class,
            DocumentSeeder::class // Optionnel
        ]);
    }
}
