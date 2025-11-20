<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Comunidad",
 *     title="Comunidad",
 *     description="Modelo que representa una comunidad",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID de la comunidad"),
 *     @OA\Property(property="nombre", type="string", description="Nombre de la comunidad"),
 *     @OA\Property(property="parroquia_id", type="integer", format="int64", description="ID de la parroquia a la que pertenece"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Comunidad extends Model
{
    use HasFactory;
    
    protected $table = 'comunidades';
    
    protected $fillable = [
        'nombre',
        'parroquia_id'
    ];

    /**
     * Obtiene la parroquia a la que pertenece la comunidad.
     */
    public function parroquia(): BelongsTo
    {
        return $this->belongsTo(Parroquia::class);
    }
}
