<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Usuarios",
 *     description="Endpoints para la gestión de usuarios"
 * )
 */
class UsuarioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/usuarios",
     *     summary="Obtener lista de usuarios",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Usuario")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Usuario::with('rol')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/usuarios",
     *     summary="Crear un nuevo usuario",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rol_id", "cedula", "nombres", "apellidos", "email", "clave"},
     *             @OA\Property(property="rol_id", type="integer", example=1),
     *             @OA\Property(property="cedula", type="string", maxLength=20, example="V12345678"),
     *             @OA\Property(property="nombres", type="string", maxLength=100, example="Juan"),
     *             @OA\Property(property="apellidos", type="string", maxLength=100, example="Pérez"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=100, example="juan@example.com"),
     *             @OA\Property(property="telefono", type="string", maxLength=20, nullable=true, example="+584123456789"),
     *             @OA\Property(property="clave", type="string", minLength=6, example="password123"),
     *             @OA\Property(property="estado", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Usuario")
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
            'rol_id' => 'required|exists:roles,id',
            'cedula' => 'required|string|max:20|unique:usuarios,cedula',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20',
            'clave' => 'required|string|min:6',
            'estado' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['clave'] = $data['clave']; // La encriptación se maneja en el modelo
        
        $usuario = Usuario::create($data);
        return response()->json($usuario->load('rol'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/usuarios/{id}",
     *     summary="Obtener un usuario por ID",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Usuario")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $usuario = Usuario::with('rol')->find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario);
    }

    /**
     * @OA\Put(
     *     path="/api/usuarios/{id}",
     *     summary="Actualizar un usuario existente",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="rol_id", type="integer", example=1),
     *             @OA\Property(property="cedula", type="string", maxLength=20, example="V12345678"),
     *             @OA\Property(property="nombres", type="string", maxLength=100, example="Juan"),
     *             @OA\Property(property="apellidos", type="string", maxLength=100, example="Pérez"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=100, example="juan@example.com"),
     *             @OA\Property(property="telefono", type="string", maxLength=20, nullable=true, example="+584123456789"),
     *             @OA\Property(property="clave", type="string", minLength=6, example="nuevapassword"),
     *             @OA\Property(property="estado", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Usuario")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rol_id' => 'sometimes|required|exists:roles,id',
            'cedula' => 'sometimes|required|string|max:20|unique:usuarios,cedula,' . $id,
            'nombres' => 'sometimes|required|string|max:100',
            'apellidos' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|string|email|max:100|unique:usuarios,email,' . $id,
            'telefono' => 'nullable|string|max:20',
            'clave' => 'sometimes|string|min:6',
            'estado' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        
        // Si se envía la contraseña, se actualiza (se encripta en el modelo)
        if (isset($data['clave'])) {
            $data['clave'] = $data['clave'];
        } else {
            unset($data['clave']);
        }
        
        $usuario->update($data);
        return response()->json($usuario->load('rol'));
    }

    /**
     * @OA\Delete(
     *     path="/api/usuarios/{id}",
     *     summary="Eliminar un usuario",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Usuario eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();
        return response()->json(null, 204);
    }
}
