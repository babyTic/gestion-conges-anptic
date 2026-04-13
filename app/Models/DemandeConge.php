<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeConge extends Model
{
    protected $table = 'demandes_conge';
    protected $fillable = [
        'user_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'lieu',
        'statut',
        'commentaire',
        'piece_jointe',
        'autorisation_signee_path',
        'certificat_path',
    ];

    public const STATUTS = [
    'SOUMIS' => 'soumis',
    'APPROUVE_RESPONSABLE' => 'approuve_responsable',
    'APPROUVE_RH' => 'approuve_rh',
    'APPROUVE_DG' => 'approuve_dg',
];


    // Relation: Une demande appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation: Une demande a un type de congé
    public function type()
    {
        return $this->belongsTo(TypeConge::class, 'type_conge_id');
    }

    // Relation: Une demande peut avoir plusieurs documents
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function interimaire()
{
    return $this->belongsTo(User::class, 'interimaire_id');
}

}
