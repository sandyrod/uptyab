<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Ubicacion",
 *     title="Ubicacion",
 *     description="Modelo que representa una ubicación",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID de la ubicación"),
 *     @OA\Property(property="comunidad_id", type="integer", format="int64", description="ID de la comunidad a la que pertenece"),
 *     @OA\Property(property="nombre", type="string", description="Nombre de la ubicación"),
 *     @OA\Property(property="calle", type="string", nullable=true, description="Calle de la ubicación"),
 *     @OA\Property(property="avenida", type="string", nullable=true, description="Avenida de la ubicación"),
 *     @OA\Property(property="referencia", type="string", nullable=true, description="Referencia de la ubicación"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Ubicacion extends Model
{
    use HasFactory;
    
    protected $table = 'ubicaciones';
    
    protected $fillable = [
        'comunidad_id',
        'nombre',
        'calle',
        'avenida',
        'referencia'
    ];

    /**
     * Obtiene la comunidad a la que pertenece la ubicación.
     */
    public function comunidad(): BelongsTo
    {
        return $this->belongsTo(Comunidad::class);
    }
}
