<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perfume;
use Illuminate\Http\Request;

class PerfumeApiController extends Controller
{
    /**
     * Listar perfumes (máximo 5 por defecto)
     * GET /api/perfumes
     */
    public function index()
    {
        try {
            $perfumes = Perfume::take(5)->get();
            
            return response()->json([
                'success' => true,
                'data' => $perfumes,
                'message' => 'Perfumes obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener perfumes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un perfume específico
     * GET /api/perfumes/{id}
     */
    public function show($id)
    {
        try {
            $perfume = Perfume::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $perfume,
                'message' => 'Perfume obtenido correctamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perfume no encontrado',
                'error' => 'No se encontró un perfume con el ID proporcionado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el perfume',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo perfume
     * POST /api/perfumes
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'marca' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'volumen' => 'required|integer|min:1',
                'precio' => 'required|integer|min:0',
                'genero' => 'required|in:M,F,U',
                'stock' => 'required|integer|min:0',
                'imagen_url' => 'nullable|url'
            ]);

            $perfume = Perfume::create($data);

            return response()->json([
                'success' => true,
                'data' => $perfume,
                'message' => 'Perfume creado correctamente'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el perfume',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un perfume existente
     * PUT /api/perfumes/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $perfume = Perfume::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'marca' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'volumen' => 'required|integer|min:1',
                'precio' => 'required|integer|min:0',
                'genero' => 'required|in:M,F,U',
                'stock' => 'required|integer|min:0',
                'imagen_url' => 'nullable|url'
            ]);

            $perfume->update($data);

            return response()->json([
                'success' => true,
                'data' => $perfume,
                'message' => 'Perfume actualizado correctamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perfume no encontrado',
                'error' => 'No se encontró un perfume con el ID proporcionado'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfume',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un perfume
     * DELETE /api/perfumes/{id}
     */
    public function destroy($id)
    {
        try {
            $perfume = Perfume::findOrFail($id);
            $perfume->delete();

            return response()->json([
                'success' => true,
                'message' => 'Perfume eliminado correctamente',
                'data' => null
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perfume no encontrado',
                'error' => 'No se encontró un perfume con el ID proporcionado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el perfume',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener perfumes con paginación
     * GET /api/perfumes/paginated?page=1&per_page=10
     */
    public function paginated(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $perfumes = Perfume::paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $perfumes->items(),
                'pagination' => [
                    'total' => $perfumes->total(),
                    'per_page' => $perfumes->perPage(),
                    'current_page' => $perfumes->currentPage(),
                    'last_page' => $perfumes->lastPage(),
                    'from' => $perfumes->firstItem(),
                    'to' => $perfumes->lastItem()
                ],
                'message' => 'Perfumes obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener perfumes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filtrar perfumes por género
     * GET /api/perfumes/genero/{genero}
     */
    public function byGenero($genero)
    {
        try {
            $generoUppercase = strtoupper($genero);
            
            if (!in_array($generoUppercase, ['M', 'F', 'U'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Género inválido. Use M, F o U'
                ], 400);
            }
            
            $perfumes = Perfume::where('genero', $generoUppercase)->get();
            
            return response()->json([
                'success' => true,
                'data' => $perfumes,
                'message' => 'Perfumes filtrados por género correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al filtrar perfumes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los perfumes (sin límite)
     * GET /api/perfumes/all
     */
    public function all()
    {
        try {
            $perfumes = Perfume::all();
            
            return response()->json([
                'success' => true,
                'data' => $perfumes,
                'message' => 'Todos los perfumes obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener perfumes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}