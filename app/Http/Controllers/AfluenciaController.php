<?php

namespace App\Http\Controllers;

use App\Models\Afluencia;
use Illuminate\Http\Request;

class AfluenciaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/afluencias",
     *     summary="Obtener lista de afluencias",
     *     tags={"Afluencias"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de afluencias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Afluencia")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Afluencia::with(['evento', 'votacionCentro'])->get();
    }

    /**
     * @OA\Post(
     *     path="/api/afluencias",
     *     summary="Crear una nueva afluencia",
     *     tags={"Afluencias"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Afluencia")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Afluencia creada",
     *         @OA\JsonContent(ref="#/components/schemas/Afluencia")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'votacion_centro_id' => 'required|exists:votacion_centros,id', // Nueva validación
            'cantidadvotantes' => 'required|integer',
            'hora' => 'required|date_format:H:i',
        ]);

        return Afluencia::create($request->all());
    }

    /**
     * @OA\Get(
     *     path="/api/afluencias/{id}",
     *     summary="Obtener afluencia por ID",
     *     tags={"Afluencias"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Afluencia encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Afluencia")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Afluencia no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        return Afluencia::with(['evento', 'votacionCentro'])->findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/afluencias/{id}",
     *     summary="Actualizar afluencia",
     *     tags={"Afluencias"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Afluencia")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Afluencia actualizada",
     *         @OA\JsonContent(ref="#/components/schemas/Afluencia")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Afluencia no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $afluencia = Afluencia::findOrFail($id);

        $request->validate([
            'evento_id' => 'sometimes|required|exists:eventos,id',
            'votacion_centro_id' => 'sometimes|required|exists:votacion_centros,id', // Nueva validación
            'cantidadvotantes' => 'sometimes|required|integer',
            'hora' => 'sometimes|required|date_format:H:i',
        ]);

        $afluencia->update($request->all());

        return $afluencia;
    }

    /**
     * @OA\Delete(
     *     path="/api/afluencias/{id}",
     *     summary="Eliminar afluencia",
     *     tags={"Afluencias"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Afluencia eliminada"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Afluencia no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $afluencia = Afluencia::findOrFail($id);
        $afluencia->delete();

        return response()->noContent();
    }
}