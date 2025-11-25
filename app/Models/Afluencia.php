<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afluencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'evento_id',
        'votacion_centro_id', // Nuevo campo
        'cantidadvotantes',
        'hora',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    // Nueva relación con centro de votación
    public function votacionCentro()
    {
        return $this->belongsTo(VotacionCentro::class);
    }
}