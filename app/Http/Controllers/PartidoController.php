<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Partidos",
 *     description="Endpoints para la gestión de partidos políticos"
 * )
 */
class PartidoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/partidos",
     *     summary="Obtener lista de partidos",
     *     tags={"Partidos"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Búsqueda por nombre",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de partidos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Partido")
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
            $query = Partido::query();
            
            if ($request->has('search') && $request->search != '') {
                $query->search($request->search);
            }
            
            $partidos = $query->orderBy('nombre')->get();
            
            return response()->json($partidos);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cargar los partidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/partidos",
     *     summary="Crear un nuevo partido",
     *     tags={"Partidos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "votos"},
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="votos", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Partido creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Partido")
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
                'votos' => 'required|integer|min:0',
            ]);

            $partido = Partido::create($validated);

            return response()->json($partido, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el partido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/partidos/{id}",
     *     summary="Obtener un partido específico",
     *     tags={"Partidos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Partido encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Partido")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Partido no encontrado"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $partido = Partido::findOrFail($id);
            return response()->json($partido);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Partido no encontrado'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/partidos/{id}",
     *     summary="Actualizar un partido",
     *     tags={"Partidos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "votos"},
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="votos", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Partido actualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Partido")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Partido no encontrado"
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
            $partido = Partido::findOrFail($id);
            
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'votos' => 'required|integer|min:0',
            ]);

            $partido->update($validated);

            return response()->json($partido);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Partido no encontrado'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el partido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/partidos/{id}",
     *     summary="Eliminar un partido",
     *     tags={"Partidos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Partido eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Partido no encontrado"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $partido = Partido::findOrFail($id);
            $partido->delete();

            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Partido no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el partido',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}