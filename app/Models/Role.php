<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Role",
 *     title="Role",
 *     description="Modelo que representa un rol en el sistema",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID del rol"),
 *     @OA\Property(property="rol", type="string", maxLength=50, description="Nombre del rol"),
 *     @OA\Property(property="descripcion", type="string", maxLength=500, description="Descripción detallada del rol"),
 *     @OA\Property(property="nivel_acceso", type="integer", description="Nivel de acceso del rol (mayor número = más privilegios)"),
 *     @OA\Property(property="estado", type="boolean", description="Estado del rol (activo/inactivo)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización")
 * )
 */
class Role extends Model
{
    use HasFactory;
    
    protected $table = 'roles';
    
    protected $fillable = [
        'rol',
        'descripcion',
        'nivel_acceso',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}
