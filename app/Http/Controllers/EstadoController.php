<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EstadoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/estados",
     *     summary="Obtener todos los estados",
     *     tags={"Estados"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de estados obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Estado")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $estados = Estado::all();
        return response()->json($estados);
    }

    /**
     * @OA\Post(
     *     path="/api/estados",
     *     summary="Crear un nuevo estado",
     *     tags={"Estados"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EstadoRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Estado creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Estado")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:60|unique:estados,nombre' // Cambiado de 'estado' a 'estados'
        ]);

        $estado = Estado::create($request->all());

        return response()->json($estado, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/estados/{id}",
     *     summary="Obtener un estado específico",
     *     tags={"Estados"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del estado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado obtenido exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Estado")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estado no encontrado"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $estado = Estado::findOrFail($id);
        return response()->json($estado);
    }

    /**
     * @OA\Put(
     *     path="/api/estados/{id}",
     *     summary="Actualizar un estado existente",
     *     tags={"Estados"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del estado a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EstadoRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Estado")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estado no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $estado = Estado::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:60|unique:estados,nombre,' . $estado->id // Cambiado de 'estado' a 'estados'
        ]);

        $estado->update($request->all());

        return response()->json($estado);
    }

    /**
     * @OA\Delete(
     *     path="/api/estados/{id}",
     *     summary="Eliminar un estado",
     *     tags={"Estados"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del estado a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estado eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estado no encontrado"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $estado = Estado::findOrFail($id);
        $estado->delete();

        return response()->json(['message' => 'Estado eliminado correctamente']);
    }
}