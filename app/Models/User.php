<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'nom',
        'prenom',
        'sexe',
        'identifiant',
        'direction_id',
        'role',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Génère un identifiant unique basé sur la direction
     * Format: {DIRECTION}{NUMéro} ex: DRH001, DFIN002
     */
    public static function genererIdentifiant($direction)
    {
        $prefix = strtoupper(substr($direction, 0, 3)); // DRH, DFI, DTI

        // Trouver le dernier numéro pour cette direction
        $lastUser = self::where('identifiant', 'like', $prefix . '%')
            ->orderBy('identifiant', 'desc')
            ->first();

        if ($lastUser && preg_match('/\d+$/', $lastUser->identifiant, $matches)) {
            $nextNumber = (int)$matches[0] + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    public function direction()
    {
        return $this->belongsTo(\App\Models\Direction::class, 'direction_id');
    }

    public function demandes()
    {
        return $this->hasMany(\App\Models\DemandeConge::class, 'user_id');
    }


}
