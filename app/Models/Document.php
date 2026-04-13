<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['demande_conge_id', 'chemin_fichier', 'type','decision'];

    // Relation: Un document appartient à une demande
    public function demande()
    {
        return $this->belongsTo(DemandeConge::class, 'demande_conge_id');
    }
}
