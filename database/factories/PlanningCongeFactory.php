<?php

namespace Database\Factories;

use App\Models\PlanningConge;
use App\Models\DemandeConge;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanningCongeFactory extends Factory
{
    protected $model = PlanningConge::class;

    public function definition()
    {
        return [
            'demande_conge_id' => DemandeConge::factory(),
            'jour' => $this->faker->dateTimeBetween('now', '+1 month'),
            'statut' => $this->faker->randomElement(['prevue', 'validee', 'refusee']),
        ];
    }
}
