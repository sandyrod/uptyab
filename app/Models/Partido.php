<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Partido",
 *     type="object",
 *     title="Partido",
 *     description="Modelo que representa un partido político",
 *     required={"nombre", "votos"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="ID único del partido"
 *     ),
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         description="Nombre del partido político"
 *     ),
 *     @OA\Property(
 *         property="votos",
 *         type="integer",
 *         description="Cantidad de votos obtenidos por el partido"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha y hora de creación"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha y hora de última actualización"
 *     )
 * )
 */
class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'votos',
    ];

    protected $casts = [
        'votos' => 'integer',
    ];

    /**
     * Scope para búsqueda
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('nombre', 'like', "%{$search}%");
    }
}