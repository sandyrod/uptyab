<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="API Estados",
 *     version="1.0.0",
 *     description="API para gestión de estados"
 * )
 * 
 * @OA\Schema(
 *     schema="Estado",
 *     type="object",
 *     title="Estado",
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="nombre", type="string", maxLength=60, example="Activo"),
 *         @OA\Property(property="created_at", type="string", format="date-time"),
 *         @OA\Property(property="updated_at", type="string", format="date-time")
 *     }
 * )
 * 
 * @OA\Schema(
 *     schema="EstadoRequest",
 *     type="object",
 *     title="EstadoRequest",
 *     required={"nombre"},
 *     properties={
 *         @OA\Property(property="nombre", type="string", maxLength=60, example="Activo")
 *     }
 * )
 * 
 * @OA\Server(
 *     url="http://uptyab.test/api",
 *     description="Servidor local"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}