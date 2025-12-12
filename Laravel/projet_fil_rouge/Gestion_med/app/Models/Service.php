<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'prix',
        'duree',
        'statut',
        'medecin_id'
    ];
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }
}
