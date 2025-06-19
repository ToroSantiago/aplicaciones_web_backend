<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Perfume;
use App\Models\PerfumeVariante;
use Illuminate\Http\Request;

class PerfumeApiController extends Controller
{
    /**
     * Listar perfumes con sus variantes (máximo 5 por defecto)
     * GET /api/perfumes
     */
    public function index()
    {
        try {
            $perfumes = Perfume::with('variantes')->take(5)->get();
            
            // Agregar atributos calculados
            $perfumes = $perfumes->map(function ($perfume) {
                return $this->formatPerfumeWithVariantes($perfume);
            });
            
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
     * Mostrar un perfume específico con sus variantes
     * GET /api/perfumes/{id}
     */
    public function show($id)
    {
        try {
            $perfume = Perfume::with('variantes')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $this->formatPerfumeWithVariantes($perfume),
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
     * Crear un nuevo perfume con variantes
     * POST /api/perfumes
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'marca' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'genero' => 'required|in:M,F,U',
                'imagen_url' => 'nullable|url',
                'variantes' => 'required|array|min:1',
                'variantes.*.volumen' => 'required|integer|in:75,100,200',
                'variantes.*.precio' => 'required|numeric|min:0',
                'variantes.*.stock' => 'required|integer|min:0'
            ]);
            
            $perfumeData = $request->except('variantes');
            $perfume = Perfume::create($perfumeData);
            
            foreach ($request->variantes as $variante) {
                $perfume->variantes()->create($variante);
            }
            
            $perfume->load('variantes');
            
            return response()->json([
                'success' => true,
                'data' => $this->formatPerfumeWithVariantes($perfume),
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
     * Actualizar un perfume existente con sus variantes
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
                'genero' => 'required|in:M,F,U',
                'imagen_url' => 'nullable|url',
                'variantes' => 'sometimes|array|min:1',
                'variantes.*.volumen' => 'required_with:variantes|integer|in:75,100,200',
                'variantes.*.precio' => 'required_with:variantes|numeric|min:0',
                'variantes.*.stock' => 'required_with:variantes|integer|min:0'
            ]);
            
            $perfumeData = $request->except('variantes');
            $perfume->update($perfumeData);
            
            // Si se enviaron variantes, actualizar
            if ($request->has('variantes')) {
                // Eliminar variantes existentes y crear nuevas
                $perfume->variantes()->delete();
                
                foreach ($request->variantes as $variante) {
                    $perfume->variantes()->create($variante);
                }
            }
            
            $perfume->load('variantes');
            
            return response()->json([
                'success' => true,
                'data' => $this->formatPerfumeWithVariantes($perfume),
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
     * Eliminar un perfume (incluye sus variantes por cascade)
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
            $perfumes = Perfume::with('variantes')->paginate($perPage);
            
            // Formatear los perfumes
            $formattedPerfumes = $perfumes->getCollection()->map(function ($perfume) {
                return $this->formatPerfumeWithVariantes($perfume);
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedPerfumes,
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
            
            $perfumes = Perfume::with('variantes')->where('genero', $generoUppercase)->get();
            
            $formattedPerfumes = $perfumes->map(function ($perfume) {
                return $this->formatPerfumeWithVariantes($perfume);
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedPerfumes,
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
            $perfumes = Perfume::with('variantes')->get();
            
            $formattedPerfumes = $perfumes->map(function ($perfume) {
                return $this->formatPerfumeWithVariantes($perfume);
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedPerfumes,
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
    
    /**
     * Obtener una variante específica
     * GET /api/perfumes/{perfumeId}/variantes/{varianteId}
     */
    public function showVariante($perfumeId, $varianteId)
    {
        try {
            $perfume = Perfume::findOrFail($perfumeId);
            $variante = $perfume->variantes()->findOrFail($varianteId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'perfume' => $perfume->only(['id', 'nombre', 'marca', 'descripcion', 'genero', 'imagen_url']),
                    'variante' => $variante
                ],
                'message' => 'Variante obtenida correctamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perfume o variante no encontrada',
                'error' => 'No se encontró el recurso solicitado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la variante',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar stock de una variante específica
     * PATCH /api/perfumes/{perfumeId}/variantes/{varianteId}/stock
     */
    public function updateStock(Request $request, $perfumeId, $varianteId)
    {
        try {
            $data = $request->validate([
                'stock' => 'required|integer|min:0'
            ]);
            
            $perfume = Perfume::findOrFail($perfumeId);
            $variante = $perfume->variantes()->findOrFail($varianteId);
            
            $variante->update(['stock' => $data['stock']]);
            
            return response()->json([
                'success' => true,
                'data' => $variante,
                'message' => 'Stock actualizado correctamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perfume o variante no encontrada',
                'error' => 'No se encontró el recurso solicitado'
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
                'message' => 'Error al actualizar el stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Formatear perfume con sus variantes y atributos calculados
     */
    private function formatPerfumeWithVariantes($perfume)
    {
        $perfumeArray = $perfume->toArray();
        
        // Agregar atributos calculados
        $perfumeArray['precio_minimo'] = $perfume->precio_minimo;
        $perfumeArray['precio_maximo'] = $perfume->precio_maximo;
        $perfumeArray['hay_stock'] = $perfume->hay_stock;
        $perfumeArray['stock_total'] = $perfume->stock_total;
        
        // Para compatibilidad con el frontend actual, agregar precio y volumen del primer variante
        if ($perfume->variantes->count() > 0) {
            $primeraVariante = $perfume->variantes->first();
            $perfumeArray['precio'] = $primeraVariante->precio;
            $perfumeArray['volumen'] = $primeraVariante->volumen;
            $perfumeArray['stock'] = $primeraVariante->stock;
        }
        
        return $perfumeArray;
    }
}