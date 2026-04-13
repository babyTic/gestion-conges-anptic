<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DemandeConge;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition()
    {
        return [
            'demande_conge_id' => DemandeConge::factory(),
            'chemin' => 'documents/' . $this->faker->uuid . '.pdf',
            'nom_original' => $this->faker->word . '.pdf',
            'type' => $this->faker->randomElement(['justificatif', 'avis', 'autre']),
        ];
    }
}
