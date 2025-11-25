<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\VotacionCentro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Eventos",
 *     description="Endpoints para la gestión de eventos"
 * )
 */
class EventoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/eventos",
     *     summary="Obtener lista de eventos",
     *     tags={"Eventos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de eventos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Evento")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Evento::with('votacion_centro')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/eventos",
     *     summary="Crear un nuevo evento",
     *     tags={"Eventos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "votacioncentro_id"},
     *             @OA\Property(property="nombre", type="string", example="Elecciones Presidenciales"),
     *             @OA\Property(property="votacioncentro_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Evento creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Evento")
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
            'votacioncentro_id' => 'required|exists:votacion_centros,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $evento = Evento::create($request->all());
        return response()->json($evento->load('votacion_centro'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/eventos/{id}",
     *     summary="Obtener un evento específico",
     *     tags={"Eventos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del evento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evento encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Evento")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Evento no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $evento = Evento::with('votacion_centro')->findOrFail($id);
        return response()->json($evento);
    }

    /**
     * @OA\Put(
     *     path="/api/eventos/{id}",
     *     summary="Actualizar un evento existente",
     *     tags={"Eventos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del evento a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Evento Actualizado"),
     *             @OA\Property(property="votacioncentro_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evento actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Evento")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Evento no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'votacioncentro_id' => 'sometimes|required|exists:votacion_centros,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $evento->update($request->all());
        return response()->json($evento->load('votacion_centro'));
    }

    /**
     * @OA\Delete(
     *     path="/api/eventos/{id}",
     *     summary="Eliminar un evento",
     *     tags={"Eventos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del evento a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Evento eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Evento no encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->delete();
        
        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/api/votacion-centros/{id}/eventos",
     *     summary="Obtener eventos por centro de votación",
     *     tags={"Eventos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del centro de votación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de eventos del centro de votación",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Evento")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Centro de votación no encontrado"
     *     )
     * )
     */
    public function porVotacionCentro($id)
    {
        $votacionCentro = VotacionCentro::findOrFail($id);
        $eventos = Evento::where('votacioncentro_id', $id)->get();
        
        return response()->json($eventos);
    }
}