<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use App\Models\Comunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Ubicaciones",
 *     description="Endpoints para la gestión de ubicaciones"
 * )
 */
class UbicacionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ubicaciones",
     *     summary="Obtener lista de ubicaciones",
     *     tags={"Ubicaciones"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ubicaciones",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Ubicacion")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Ubicacion::with('comunidad')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/ubicaciones",
     *     summary="Crear una nueva ubicación",
     *     tags={"Ubicaciones"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comunidad_id", "nombre"},
     *             @OA\Property(property="comunidad_id", type="integer", example=1),
     *             @OA\Property(property="nombre", type="string", example="Ubicación Principal"),
     *             @OA\Property(property="calle", type="string", example="Calle Principal"),
     *             @OA\Property(property="avenida", type="string", example="Avenida Bolívar"),
     *             @OA\Property(property="referencia", type="string", example="Frente a la plaza")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ubicación creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Ubicacion")
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
            'comunidad_id' => 'required|exists:comunidades,id',
            'nombre' => 'required|string|max:255',
            'calle' => 'nullable|string|max:255',
            'avenida' => 'nullable|string|max:255',
            'referencia' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ubicacion = Ubicacion::create($request->all());
        return response()->json($ubicacion->load('comunidad'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ubicaciones/{id}",
     *     summary="Obtener una ubicación específica",
     *     tags={"Ubicaciones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la ubicación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ubicación encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Ubicacion")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ubicación no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $ubicacion = Ubicacion::with('comunidad')->find($id);
        
        if (!$ubicacion) {
            return response()->json(['message' => 'Ubicación no encontrada'], 404);
        }
        
        return response()->json($ubicacion);
    }

    /**
     * @OA\Put(
     *     path="/api/ubicaciones/{id}",
     *     summary="Actualizar una ubicación existente",
     *     tags={"Ubicaciones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la ubicación a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="comunidad_id", type="integer", example=1),
     *             @OA\Property(property="nombre", type="string", example="Ubicación Actualizada"),
     *             @OA\Property(property="calle", type="string", example="Calle Actualizada"),
     *             @OA\Property(property="avenida", type="string", example="Avenida Actualizada"),
     *             @OA\Property(property="referencia", type="string", example="Referencia actualizada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ubicación actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Ubicacion")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ubicación no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $ubicacion = Ubicacion::find($id);
        
        if (!$ubicacion) {
            return response()->json(['message' => 'Ubicación no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'comunidad_id' => 'exists:comunidades,id',
            'nombre' => 'string|max:255',
            'calle' => 'nullable|string|max:255',
            'avenida' => 'nullable|string|max:255',
            'referencia' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ubicacion->update($request->all());
        return response()->json($ubicacion->load('comunidad'));
    }

    /**
     * @OA\Delete(
     *     path="/api/ubicaciones/{id}",
     *     summary="Eliminar una ubicación",
     *     tags={"Ubicaciones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la ubicación a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Ubicación eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ubicación no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $ubicacion = Ubicacion::find($id);
        
        if (!$ubicacion) {
            return response()->json(['message' => 'Ubicación no encontrada'], 404);
        }
        
        $ubicacion->delete();
        return response()->json(null, 204);
    }
}
