<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{


    use HasFactory;

    /**
     * Nom de la table (optionnel si différent de "plannings")
     */
    protected $table = 'plannings';

    /**
     * Champs remplissables
     */
    protected $fillable = [
        'direction_id',
        'user_id',
        'date_debut',
        'date_fin'
    ];

    /**
     * Relation: Un planning appartient à une direction
     */
    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    /**
     * Relation: Un planning peut concerner un utilisateur (optionnel)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
