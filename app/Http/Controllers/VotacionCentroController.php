<?php

namespace App\Http\Controllers;

use App\Models\VotacionCentro;
use App\Models\Ubicacion;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="VotacionCentros",
 *     description="Endpoints para la gestión de centros de votación"
 * )
 */
class VotacionCentroController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/votacion-centros",
     *     summary="Obtener lista de centros de votación",
     *     tags={"VotacionCentros"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de centros de votación",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/VotacionCentro")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(VotacionCentro::with(['ubicacion', 'persona'])->get());
    }

    /**
     * @OA\Post(
     *     path="/api/votacion-centros",
     *     summary="Crear un nuevo centro de votación",
     *     tags={"VotacionCentros"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ubicacion_id", "nombre", "cantidad_electores", "codigo"},
     *             @OA\Property(property="ubicacion_id", type="integer", example=1),
     *             @OA\Property(property="persona_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="nombre", type="string", maxLength=255, example="Unidad Educativa Nacional Bolivariana"),
     *             @OA\Property(property="cantidad_electores", type="integer", example=500),
     *             @OA\Property(property="codigo", type="integer", example=1234567890)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Centro de votación creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/VotacionCentro")
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
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'persona_id' => 'nullable|exists:personas,id',
            'nombre' => 'required|string|max:255',
            'cantidad_electores' => 'required|integer|min:0',
            'codigo' => 'required|integer|min:0|unique:votacion_centros,codigo',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $votacionCentro = VotacionCentro::create($request->all());
        return response()->json($votacionCentro->load(['ubicacion', 'persona']), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/votacion-centros/{id}",
     *     summary="Obtener un centro de votación por ID",
     *     tags={"VotacionCentros"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del centro de votación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Centro de votación encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/VotacionCentro")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Centro de votación no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $votacionCentro = VotacionCentro::with(['ubicacion', 'persona'])->find($id);
        if (!$votacionCentro) {
            return response()->json(['message' => 'Centro de votación no encontrado'], 404);
        }
        return response()->json($votacionCentro);
    }

    /**
     * @OA\Put(
     *     path="/api/votacion-centros/{id}",
     *     summary="Actualizar un centro de votación existente",
     *     tags={"VotacionCentros"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del centro de votación a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="ubicacion_id", type="integer", example=1),
     *             @OA\Property(property="persona_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="nombre", type="string", maxLength=255, example="Nuevo nombre del centro"),
     *             @OA\Property(property="cantidad_electores", type="integer", example=600),
     *             @OA\Property(property="codigo", type="integer", example=1234567890)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Centro de votación actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/VotacionCentro")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Centro de votación no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $votacionCentro = VotacionCentro::find($id);
        if (!$votacionCentro) {
            return response()->json(['message' => 'Centro de votación no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ubicacion_id' => 'sometimes|required|exists:ubicaciones,id',
            'persona_id' => 'nullable|exists:personas,id',
            'nombre' => 'sometimes|required|string|max:255',
            'cantidad_electores' => 'sometimes|required|integer|min:0',
            'codigo' => 'sometimes|required|integer|min:0|unique:votacion_centros,codigo,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $votacionCentro->update($request->all());
        return response()->json($votacionCentro->load(['ubicacion', 'persona']));
    }

    /**
     * @OA\Delete(
     *     path="/api/votacion-centros/{id}",
     *     summary="Eliminar un centro de votación",
     *     tags={"VotacionCentros"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del centro de votación a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Centro de votación eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Centro de votación no encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $votacionCentro = VotacionCentro::find($id);
        if (!$votacionCentro) {
            return response()->json(['message' => 'Centro de votación no encontrado'], 404);
        }

        $votacionCentro->delete();
        return response()->json(null, 204);
    }
}
