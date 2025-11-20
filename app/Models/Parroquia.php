<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Parroquia",
 *     title="Parroquia",
 *     description="Modelo que representa una parroquia",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID de la parroquia"),
 *     @OA\Property(property="nombre", type="string", description="Nombre de la parroquia"),
 *     @OA\Property(property="municipio_id", type="integer", format="int64", description="ID del municipio al que pertenece"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Parroquia extends Model
{
    use HasFactory;
    
    protected $table = 'parroquias';
    
    protected $fillable = [
        'nombre',
        'municipio_id'
    ];

    /**
     * Obtiene el municipio al que pertenece la parroquia.
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }
}
