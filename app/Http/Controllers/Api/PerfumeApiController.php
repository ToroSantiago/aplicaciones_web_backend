<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perfume;

class PerfumeApiController extends Controller
{
    public function index()
    {
        return response()->json(Perfume::all());
    }

    public function show($id)
    {
        $perfume = Perfume::find($id);

        if (!$perfume) {
            return response()->json(['error' => 'Perfume no encontrado'], 404);
        }

        return response()->json($perfume);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'marca' => 'required|string',
            'descripcion' => 'nullable|string',
            'volumen' => 'required',
            'precio' => 'required|numeric',
            'genero' => 'required|in:M,F,U',
            'stock' => 'required|boolean',
        ]);

        $perfume = Perfume::create($data);
        return response()->json($perfume, 201);
    }

    public function update(Request $request, $id)
    {
        $perfume = Perfume::find($id);

        if (!$perfume) {
            return response()->json(['error' => 'Perfume no encontrado'], 404);
        }

        $data = $request->validate([
            'nombre' => 'required|string',
            'marca' => 'required|string',
            'descripcion' => 'nullable|string',
            'volumen' => 'required',
            'precio' => 'required|numeric',
            'genero' => 'required|in:M,F,U',
            'stock' => 'required|boolean',
        ]);

        $perfume->update($data);
        return response()->json($perfume);
    }

    public function destroy($id)
    {
        $perfume = Perfume::find($id);

        if (!$perfume) {
            return response()->json(['error' => 'Perfume no encontrado'], 404);
        }

        $perfume->delete();
        return response()->json(['message' => 'Perfume eliminado correctamente']);
    }
}
