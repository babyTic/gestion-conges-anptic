<?php

namespace Database\Factories;

use App\Models\Direction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'password' => bcrypt('password'), // Mot de passe par défaut
            'role' => 'agent',
            'direction_id' => Direction::factory()->create()->id,            // identifiant est généré automatiquement par le modèle
        ];
    }
}
