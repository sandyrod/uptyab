<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="Usuario",
 *     title="Usuario",
 *     description="Modelo que representa un usuario del sistema",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID del usuario"),
 *     @OA\Property(property="rol_id", type="integer", format="int64", description="ID del rol del usuario"),
 *     @OA\Property(property="cedula", type="string", maxLength=20, description="Número de cédula del usuario"),
 *     @OA\Property(property="nombres", type="string", maxLength=100, description="Nombres del usuario"),
 *     @OA\Property(property="apellidos", type="string", maxLength=100, description="Apellidos del usuario"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=100, description="Correo electrónico del usuario"),
 *     @OA\Property(property="telefono", type="string", maxLength=20, nullable=true, description="Número de teléfono del usuario"),
 *     @OA\Property(property="estado", type="boolean", description="Estado del usuario (activo/inactivo)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Usuario extends Model
{
    use HasFactory, HasApiTokens;
    
    protected $table = 'usuarios';
    
    protected $fillable = [
        'rol_id',
        'cedula',
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'clave',
        'estado'
    ];

    protected $hidden = [
        'clave',
        'remember_token',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setClaveAttribute($value)
    {
        $this->attributes['clave'] = Hash::make($value);
    }

    /**
     * Get the role that owns the user.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
}
