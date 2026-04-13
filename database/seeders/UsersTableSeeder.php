<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Direction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // 1. Admin système
        User::firstOrCreate(
            ['identifiant' => 'ADM001'],
            [
                'nom' => 'Admin',
                'prenom' => 'System',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'direction_id' => Direction::first()->id
            ]
        );

        // 2. Agents par direction
        Direction::all()->each(function ($direction) {
            $prefix = match(Str::before($direction->nom, ' ')) {
                'Direction' => 'DIR',
                default => Str::upper(Str::substr($direction->nom, 0, 3))
            };

            User::factory(rand(3, 8))->create([
                'role' => 'agent',
                'direction_id' => $direction->id
            ]);
        });
    }
}
