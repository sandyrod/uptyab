<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'votacioncentro_id',
        'nombre',
    ];

    /**
     * Get the votacion centro that owns the evento.
     */
    public function votacion_centro()
    {
        return $this->belongsTo(VotacionCentro::class, 'votacioncentro_id');
    }
}