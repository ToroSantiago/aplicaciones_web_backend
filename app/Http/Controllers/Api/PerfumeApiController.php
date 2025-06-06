<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perfume;
use Illuminate\Http\Request;

class PerfumeApiController extends Controller
{
    /**
     * Listar perfumes (máximo 5)
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
     * Obtener perfumes con paginación (opcional para el futuro)
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
}