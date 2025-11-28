<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        $query = Proyecto::with('comuna');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        if ($request->has('comuna_id')) {
            $query->where('comuna_id', $request->comuna_id);
        }

        return $query->paginate($request->per_page ?? 15);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'comuna_id' => 'required|exists:comunas,id',
            'nombre' => 'required|string|max:255'
        ]);

        $proyecto = Proyecto::create($validated);

        return response()->json($proyecto->load('comuna'), 201);
    }

    public function show(Proyecto $proyecto)
    {
        return $proyecto->load('comuna');
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $validated = $request->validate([
            'comuna_id' => 'sometimes|required|exists:comunas,id',
            'nombre' => 'sometimes|required|string|max:255'
        ]);

        $proyecto->update($validated);

        return $proyecto->load('comuna');
    }

    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete();

        return response()->json(null, 204);
    }

    public function search($search)
    {
        return Proyecto::where('nombre', 'like', "%{$search}%")
            ->orWhereHas('comuna', function($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->with('comuna')
            ->get();
    }
}