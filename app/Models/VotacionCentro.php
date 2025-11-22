<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="VotacionCentro",
 *     title="VotacionCentro",
 *     description="Modelo que representa un centro de votación",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID del centro de votación"),
 *     @OA\Property(property="ubicacion_id", type="integer", format="int64", description="ID de la ubicación del centro"),
 *     @OA\Property(property="persona_id", type="integer", format="int64", nullable=true, description="ID de la persona responsable"),
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del centro de votación"),
 *     @OA\Property(property="cantidad_electores", type="integer", description="Cantidad de electores en el centro"),
 *     @OA\Property(property="codigo", type="integer", format="int32", description="Código único del centro de votación"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class VotacionCentro extends Model
{
    use HasFactory;
    
    protected $table = 'votacion_centros';
    
    protected $fillable = [
        'ubicacion_id',
        'persona_id',
        'nombre',
        'cantidad_electores',
        'codigo'
    ];

    protected $casts = [
        'cantidad_electores' => 'integer',
        'codigo' => 'integer'
    ];

    /**
     * Get the ubicacion that owns the votacion centro.
     */
    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class);
    }

    /**
     * Get the persona that is in charge of the votacion centro.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
