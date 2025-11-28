<?php

namespace App\Http\Controllers;

use App\Models\ConsejoComunal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Consejos Comunales",
 *     description="Endpoints para la gestión de consejos comunales"
 * )
 */
class ConsejoComunalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/consejoscomunales",
     *     summary="Obtener lista de consejos comunales",
     *     tags={"Consejos Comunales"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Búsqueda por nombre, RIF, nombres o apellidos",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de consejos comunales",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ConsejoComunal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ConsejoComunal::query();
            
            if ($request->has('search') && $request->search != '') {
                $query->search($request->search);
            }
            
            $consejosComunales = $query->orderBy('nombre')->get();
            
            return response()->json($consejosComunales);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cargar los consejos comunales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/consejoscomunales",
     *     summary="Crear un nuevo consejo comunal",
     *     tags={"Consejos Comunales"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "rif", "nombres", "apellidos", "fechaelccion", "cantidadelectores"},
     *             @OA\Property(property="nombre", type="string", example="Consejo Comunal Los Olivos"),
     *             @OA\Property(property="rif", type="string", example="J-123456789"),
     *             @OA\Property(property="nombres", type="string", example="María José"),
     *             @OA\Property(property="apellidos", type="string", example="Pérez García"),
     *             @OA\Property(property="fechaelccion", type="string", format="date", example="2024-01-15"),
     *             @OA\Property(property="cantidadelectores", type="integer", example=150)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Consejo comunal creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/ConsejoComunal")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'rif' => 'required|string|max:20|unique:consejocomunal,rif',
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'fechaelccion' => 'required|date',
                'cantidadelectores' => 'required|integer|min:0',
            ]);

            $consejoComunal = ConsejoComunal::create($validated);

            return response()->json($consejoComunal, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el consejo comunal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consejoscomunales/{id}",
     *     summary="Obtener un consejo comunal específico",
     *     tags={"Consejos Comunales"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del consejo comunal",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consejo comunal encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ConsejoComunal")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consejo comunal no encontrado"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $consejoComunal = ConsejoComunal::findOrFail($id);
            return response()->json($consejoComunal);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Consejo comunal no encontrado'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/consejoscomunales/{id}",
     *     summary="Actualizar un consejo comunal",
     *     tags={"Consejos Comunales"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del consejo comunal",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "rif", "nombres", "apellidos", "fechaelccion", "cantidadelectores"},
     *             @OA\Property(property="nombre", type="string", example="Consejo Comunal Los Olivos"),
     *             @OA\Property(property="rif", type="string", example="J-123456789"),
     *             @OA\Property(property="nombres", type="string", example="María José"),
     *             @OA\Property(property="apellidos", type="string", example="Pérez García"),
     *             @OA\Property(property="fechaelccion", type="string", format="date", example="2024-01-15"),
     *             @OA\Property(property="cantidadelectores", type="integer", example=150)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consejo comunal actualizado",
     *         @OA\JsonContent(ref="#/components/schemas/ConsejoComunal")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consejo comunal no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $consejoComunal = ConsejoComunal::findOrFail($id);
            
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'rif' => 'required|string|max:20|unique:consejocomunal,rif,' . $consejoComunal->id,
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'fechaelccion' => 'required|date',
                'cantidadelectores' => 'required|integer|min:0',
            ]);

            $consejoComunal->update($validated);

            return response()->json($consejoComunal);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Consejo comunal no encontrado'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el consejo comunal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/consejoscomunales/{id}",
     *     summary="Eliminar un consejo comunal",
     *     tags={"Consejos Comunales"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del consejo comunal",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Consejo comunal eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consejo comunal no encontrado"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $consejoComunal = ConsejoComunal::findOrFail($id);
            $consejoComunal->delete();

            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Consejo comunal no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el consejo comunal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}