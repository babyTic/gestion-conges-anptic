<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $table = 'journaux'; // Nom exact

    public $timestamps = false; // Si pas de created_at/updated_at

    protected $fillable = ['user_id', 'action', 'details'];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
