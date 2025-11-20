<?php

namespace App\Http\Controllers;

use App\Models\Comunidad;
use App\Models\Parroquia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Comunidades",
 *     description="Endpoints para la gestión de comunidades"
 * )
 */
class ComunidadController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/comunidades",
     *     summary="Obtener lista de comunidades",
     *     tags={"Comunidades"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de comunidades",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Comunidad")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Comunidad::with('parroquia.municipio.estado')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/comunidades",
     *     summary="Crear una nueva comunidad",
     *     tags={"Comunidades"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "parroquia_id"},
     *             @OA\Property(property="nombre", type="string", example="Comunidad Ejemplo"),
     *             @OA\Property(property="parroquia_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comunidad creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Comunidad")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'parroquia_id' => 'required|exists:parroquias,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comunidad = Comunidad::create($request->all());
        return response()->json($comunidad->load('parroquia'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/comunidades/{id}",
     *     summary="Obtener una comunidad específica",
     *     tags={"Comunidades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la comunidad",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comunidad encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Comunidad")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comunidad no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $comunidad = Comunidad::with('parroquia.municipio.estado')->findOrFail($id);
        return response()->json($comunidad);
    }

    /**
     * @OA\Put(
     *     path="/api/comunidades/{id}",
     *     summary="Actualizar una comunidad existente",
     *     tags={"Comunidades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la comunidad a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Comunidad Actualizada"),
     *             @OA\Property(property="parroquia_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comunidad actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Comunidad")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comunidad no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $comunidad = Comunidad::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'parroquia_id' => 'sometimes|required|exists:parroquias,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comunidad->update($request->all());
        return response()->json($comunidad->load('parroquia'));
    }

    /**
     * @OA\Delete(
     *     path="/api/comunidades/{id}",
     *     summary="Eliminar una comunidad",
     *     tags={"Comunidades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la comunidad a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Comunidad eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comunidad no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $comunidad = Comunidad::findOrFail($id);
        $comunidad->delete();
        
        return response()->json(null, 204);
    }
}
