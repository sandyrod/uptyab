<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Municipio",
 *     title="Municipio",
 *     description="Modelo que representa un municipio",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID del municipio"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del municipio"),
 *     @OA\Property(property="estado_id", type="integer", format="int64", description="ID del estado al que pertenece"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Municipio extends Model
{
    use HasFactory;
    protected $table = 'municipios';
    protected $fillable = [
        'nombre',
        'estado_id'
    ];

    /**
     * Obtiene el estado al que pertenece el municipio.
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }
}
