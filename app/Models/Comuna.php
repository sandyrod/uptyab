<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Comuna",
 *     type="object",
 *     title="Comuna",
 *     description="Modelo que representa una comuna",
 *     required={"codigo", "nombre", "cantidadelectores"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="ID único de la comuna"
 *     ),
 *     @OA\Property(
 *         property="codigo",
 *         type="string",
 *         description="Código único de la comuna"
 *     ),
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         description="Nombre de la comuna"
 *     ),
 *     @OA\Property(
 *         property="cantidadelectores",
 *         type="integer",
 *         description="Cantidad de electores en la comuna"
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
class Comuna extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'cantidadelectores',
    ];

    protected $casts = [
        'cantidadelectores' => 'integer',
    ];

    /**
     * Scope para búsqueda
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
    }
}