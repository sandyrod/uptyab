<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Personas",
 *     description="Endpoints para la gestión de personas"
 * )
 */
class PersonaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/personas",
     *     summary="Obtener lista de personas",
     *     tags={"Personas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de personas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Persona")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Persona::with('usuario')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/personas",
     *     summary="Crear una nueva persona",
     *     tags={"Personas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cedula", "nombres", "apellidos"},
     *             @OA\Property(property="usuario_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="cedula", type="string", maxLength=20, example="V12345678"),
     *             @OA\Property(property="nombres", type="string", maxLength=100, example="María"),
     *             @OA\Property(property="apellidos", type="string", maxLength=100, example="González"),
     *             @OA\Property(property="cuenta", type="string", maxLength=50, nullable=true, example="1234567890"),
     *             @OA\Property(property="telefono", type="string", maxLength=20, nullable=true, example="+584123456789"),
     *             @OA\Property(property="cargo", type="string", maxLength=100, nullable=true, example="Gerente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Persona creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Persona")
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
            'usuario_id' => 'nullable|exists:usuarios,id',
            'cedula' => 'required|string|max:20|unique:personas,cedula',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cuenta' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $persona = Persona::create($request->all());
        return response()->json($persona->load('usuario'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/personas/{id}",
     *     summary="Obtener una persona por ID",
     *     tags={"Personas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la persona",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Persona encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Persona")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Persona no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $persona = Persona::with('usuario')->find($id);
        if (!$persona) {
            return response()->json(['message' => 'Persona no encontrada'], 404);
        }
        return response()->json($persona);
    }

    /**
     * @OA\Put(
     *     path="/api/personas/{id}",
     *     summary="Actualizar una persona existente",
     *     tags={"Personas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la persona a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="usuario_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="cedula", type="string", maxLength=20, example="V12345678"),
     *             @OA\Property(property="nombres", type="string", maxLength=100, example="María"),
     *             @OA\Property(property="apellidos", type="string", maxLength=100, example="González"),
     *             @OA\Property(property="cuenta", type="string", maxLength=50, nullable=true, example="1234567890"),
     *             @OA\Property(property="telefono", type="string", maxLength=20, nullable=true, example="+584123456789"),
     *             @OA\Property(property="cargo", type="string", maxLength=100, nullable=true, example="Gerente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Persona actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Persona")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Persona no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $persona = Persona::find($id);
        if (!$persona) {
            return response()->json(['message' => 'Persona no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'usuario_id' => 'nullable|exists:usuarios,id',
            'cedula' => 'sometimes|required|string|max:20|unique:personas,cedula,' . $id,
            'nombres' => 'sometimes|required|string|max:100',
            'apellidos' => 'sometimes|required|string|max:100',
            'cuenta' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $persona->update($request->all());
        return response()->json($persona->load('usuario'));
    }

    /**
     * @OA\Delete(
     *     path="/api/personas/{id}",
     *     summary="Eliminar una persona",
     *     tags={"Personas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la persona a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Persona eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Persona no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $persona = Persona::find($id);
        if (!$persona) {
            return response()->json(['message' => 'Persona no encontrada'], 404);
        }

        $persona->delete();
        return response()->json(null, 204);
    }
}
