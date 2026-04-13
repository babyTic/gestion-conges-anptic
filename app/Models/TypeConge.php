<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeConge extends Model
{
    protected $table = 'types_conge'; // Nom exact de la table

    protected $fillable = ['nom', 'jours_alloues', 'est_payee'];

    public function demandes()
    {
        return $this->hasMany(DemandeConge::class, 'type_conge_id');
    }
}
