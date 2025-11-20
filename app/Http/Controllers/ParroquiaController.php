<?php

namespace App\Http\Controllers;

use App\Models\Parroquia;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Parroquias",
 *     description="Endpoints para la gestión de parroquias"
 * )
 */
class ParroquiaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/parroquias",
     *     summary="Obtener lista de parroquias",
     *     tags={"Parroquias"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de parroquias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Parroquia")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Parroquia::with('municipio.estado')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/parroquias",
     *     summary="Crear una nueva parroquia",
     *     tags={"Parroquias"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "municipio_id"},
     *             @OA\Property(property="nombre", type="string", example="Parroquia Ejemplo"),
     *             @OA\Property(property="municipio_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Parroquia creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Parroquia")
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
            'municipio_id' => 'required|exists:municipios,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parroquia = Parroquia::create($request->all());
        return response()->json($parroquia->load('municipio'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/parroquias/{id}",
     *     summary="Obtener una parroquia específica",
     *     tags={"Parroquias"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la parroquia",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Parroquia encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Parroquia")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parroquia no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $parroquia = Parroquia::with('municipio.estado')->findOrFail($id);
        return response()->json($parroquia);
    }

    /**
     * @OA\Put(
     *     path="/api/parroquias/{id}",
     *     summary="Actualizar una parroquia existente",
     *     tags={"Parroquias"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la parroquia a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Parroquia Actualizada"),
     *             @OA\Property(property="municipio_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Parroquia actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Parroquia")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parroquia no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $parroquia = Parroquia::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'municipio_id' => 'sometimes|required|exists:municipios,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parroquia->update($request->all());
        return response()->json($parroquia->load('municipio'));
    }

    /**
     * @OA\Delete(
     *     path="/api/parroquias/{id}",
     *     summary="Eliminar una parroquia",
     *     tags={"Parroquias"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la parroquia a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Parroquia eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parroquia no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $parroquia = Parroquia::findOrFail($id);
        $parroquia->delete();
        
        return response()->json(null, 204);
    }
}
