<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Direction extends Model
{
    use HasFactory;
    protected $fillable = ['nom'];

    public function utilisateurs()
    {
        return $this->hasMany(User::class);
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }
}
