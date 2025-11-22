<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Persona",
 *     title="Persona",
 *     description="Modelo que representa una persona en el sistema",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID de la persona"),
 *     @OA\Property(property="usuario_id", type="integer", format="int64", nullable=true, description="ID del usuario asociado"),
 *     @OA\Property(property="cedula", type="string", maxLength=20, description="Número de cédula de la persona"),
 *     @OA\Property(property="nombres", type="string", maxLength=100, description="Nombres de la persona"),
 *     @OA\Property(property="apellidos", type="string", maxLength=100, description="Apellidos de la persona"),
 *     @OA\Property(property="cuenta", type="string", maxLength=50, nullable=true, description="Número de cuenta de la persona"),
 *     @OA\Property(property="telefono", type="string", maxLength=20, nullable=true, description="Número de teléfono de la persona"),
 *     @OA\Property(property="cargo", type="string", maxLength=100, nullable=true, description="Cargo o puesto de la persona"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Persona extends Model
{
    use HasFactory;
    
    protected $table = 'personas';
    
    protected $fillable = [
        'usuario_id',
        'cedula',
        'nombres',
        'apellidos',
        'cuenta',
        'telefono',
        'cargo'
    ];

    /**
     * Get the user associated with the person.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
