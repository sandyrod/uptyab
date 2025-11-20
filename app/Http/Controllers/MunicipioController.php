<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Municipios",
 *     description="Endpoints para la gestión de municipios"
 * )
 */
class MunicipioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/municipios",
     *     summary="Obtener lista de municipios",
     *     tags={"Municipios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de municipios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Municipio")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Municipio::with('estado')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/municipios",
     *     summary="Crear un nuevo municipio",
     *     tags={"Municipios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "estado_id"},
     *             @OA\Property(property="nombre", type="string", example="Municipio Ejemplo"),
     *             @OA\Property(property="estado_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Municipio creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Municipio")
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
            'estado_id' => 'required|exists:estados,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $municipio = Municipio::create($request->all());
        return response()->json($municipio, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/municipios/{id}",
     *     summary="Obtener un municipio específico",
     *     tags={"Municipios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del municipio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipio encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Municipio")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Municipio no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $municipio = Municipio::with('estado')->findOrFail($id);
        return response()->json($municipio);
    }

    /**
     * @OA\Put(
     *     path="/api/municipios/{id}",
     *     summary="Actualizar un municipio existente",
     *     tags={"Municipios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del municipio a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Municipio Actualizado"),
     *             @OA\Property(property="estado_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipio actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Municipio")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Municipio no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $municipio = Municipio::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'estado_id' => 'sometimes|required|exists:estados,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $municipio->update($request->all());
        return response()->json($municipio);
    }

    /**
     * @OA\Delete(
     *     path="/api/municipios/{id}",
     *     summary="Eliminar un municipio",
     *     tags={"Municipios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del municipio a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Municipio eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Municipio no encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $municipio = Municipio::findOrFail($id);
        $municipio->delete();
        
        return response()->json(null, 204);
    }
}
