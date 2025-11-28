<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsejoComunal extends Model
{
    use HasFactory;

    protected $table = 'consejocomunal';

    protected $fillable = [
        'nombre',
        'rif',
        'nombres',
        'apellidos',
        'fechaelccion',
        'cantidadelectores',
    ];

    protected $casts = [
        'fechaelccion' => 'date',
    ];

    /**
     * Scope para búsqueda
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('rif', 'like', "%{$search}%")
                    ->orWhere('nombres', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%");
    }
}