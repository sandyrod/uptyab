<?php

namespace App\Http\Controllers;

use App\Models\Comuna;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Comunas",
 *     description="Endpoints para la gestión de comunas"
 * )
 */
class ComunaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/comunas",
     *     summary="Obtener lista de comunas",
     *     tags={"Comunas"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Búsqueda por nombre o código",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de comunas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Comuna")
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
            $query = Comuna::query();
            
            if ($request->has('search') && $request->search != '') {
                $query->search($request->search);
            }
            
            $comunas = $query->orderBy('nombre')->get();
            
            return response()->json($comunas);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cargar las comunas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/comunas",
     *     summary="Crear una nueva comuna",
     *     tags={"Comunas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"codigo", "nombre", "cantidadelectores"},
     *             @OA\Property(property="codigo", type="string"),
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="cantidadelectores", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comuna creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Comuna")
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
                'codigo' => 'required|string|max:50|unique:comunas,codigo',
                'nombre' => 'required|string|max:255',
                'cantidadelectores' => 'required|integer|min:0',
            ]);

            $comuna = Comuna::create($validated);

            return response()->json($comuna, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la comuna',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/comunas/{id}",
     *     summary="Obtener una comuna específica",
     *     tags={"Comunas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la comuna",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comuna encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Comuna")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comuna no encontrada"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $comuna = Comuna::findOrFail($id);
            return response()->json($comuna);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Comuna no encontrada'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/comunas/{id}",
     *     summary="Actualizar una comuna",
     *     tags={"Comunas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la comuna",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"codigo", "nombre", "cantidadelectores"},
     *             @OA\Property(property="codigo", type="string"),
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="cantidadelectores", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comuna actualizada",
     *         @OA\JsonContent(ref="#/components/schemas/Comuna")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comuna no encontrada"
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
            $comuna = Comuna::findOrFail($id);
            
            $validated = $request->validate([
                'codigo' => 'required|string|max:50|unique:comunas,codigo,' . $comuna->id,
                'nombre' => 'required|string|max:255',
                'cantidadelectores' => 'required|integer|min:0',
            ]);

            $comuna->update($validated);

            return response()->json($comuna);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Comuna no encontrada'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la comuna',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/comunas/{id}",
     *     summary="Eliminar una comuna",
     *     tags={"Comunas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la comuna",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Comuna eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comuna no encontrada"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $comuna = Comuna::findOrFail($id);
            $comuna->delete();

            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Comuna no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la comuna',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}