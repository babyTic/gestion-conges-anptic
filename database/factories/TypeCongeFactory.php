<?php

namespace Database\Factories;

use App\Models\TypeConge;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeCongeFactory extends Factory
{
    protected $model = TypeConge::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->unique()->word,
            'jours_alloues' => $this->faker->numberBetween(5, 30),
            'est_payee' => $this->faker->boolean(90),
        ];
    }
}
