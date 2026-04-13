<?php

namespace Database\Factories;

use App\Models\DemandeConge;
use App\Models\User;
use App\Models\TypeConge;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DemandeCongeFactory extends Factory
{
    protected $model = DemandeConge::class;

    public function definition()
    {
        $dateDebut = Carbon::today()->addDays(rand(1, 30));

        return [
            'user_id' => User::factory(),
            'type_conge_id' => TypeConge::factory(),
            'date_debut' => $dateDebut,
            'date_fin' => $dateDebut->copy()->addDays(rand(1, 14)),
            'statut' => $this->faker->randomElement(['en_attente', 'valide', 'refuse']),
            'commentaire' => $this->faker->optional()->sentence,
        ];
    }
}
