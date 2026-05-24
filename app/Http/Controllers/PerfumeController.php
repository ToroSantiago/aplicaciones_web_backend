<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perfume;
use App\Models\PerfumeVariante;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PerfumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perfumes = Perfume::with('variantes.descuentos')->get();

        return view('listarPerfumes', compact('perfumes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crearPerfume');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'genero' => 'required|in:M,F,U',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'variante_75_precio' => 'required|numeric|min:0',
            'variante_75_stock' => 'required|integer|min:0',

            'variante_100_precio' => 'required|numeric|min:0',
            'variante_100_stock' => 'required|integer|min:0',

            'variante_200_precio' => 'required|numeric|min:0',
            'variante_200_stock' => 'required|integer|min:0',
        ]);

        $imagenUrl = null;

        if ($request->hasFile('imagen')) {
            $upload = Cloudinary::uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                [
                    'folder' => 'perfumes'
                ]
            );

            $imagenUrl = $upload['secure_url'];
        }

        $perfume = Perfume::create([
            'nombre' => $request->nombre,
            'marca' => $request->marca,
            'descripcion' => $request->descripcion,
            'genero' => $request->genero,
            'imagen_url' => $imagenUrl,
        ]);

        $variantes = [
            [
                'volumen' => 75,
                'precio' => $request->variante_75_precio,
                'stock' => $request->variante_75_stock
            ],
            [
                'volumen' => 100,
                'precio' => $request->variante_100_precio,
                'stock' => $request->variante_100_stock
            ],
            [
                'volumen' => 200,
                'precio' => $request->variante_200_precio,
                'stock' => $request->variante_200_stock
            ],
        ];

        foreach ($variantes as $variante) {
            $perfume->variantes()->create($variante);
        }

        return redirect()
            ->route('perfumes.index')
            ->with('success', 'Perfume creado correctamente con todas sus variantes');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perfume = Perfume::with('variantes.descuentos')->findOrFail($id);

        return view('mostrarPerfume', compact('perfume'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $perfume = Perfume::with('variantes.descuentos')->findOrFail($id);

        return view('editarPerfume', compact('perfume'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Perfume $perfume)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'genero' => 'required|in:M,F,U',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'variante_75_precio' => 'required|numeric|min:0',
            'variante_75_stock' => 'required|integer|min:0',

            'variante_100_precio' => 'required|numeric|min:0',
            'variante_100_stock' => 'required|integer|min:0',

            'variante_200_precio' => 'required|numeric|min:0',
            'variante_200_stock' => 'required|integer|min:0',
        ]);

        $perfumeData = [
            'nombre' => $request->nombre,
            'marca' => $request->marca,
            'descripcion' => $request->descripcion,
            'genero' => $request->genero,
        ];

        if ($request->hasFile('imagen')) {
            $upload = Cloudinary::uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                [
                    'folder' => 'perfumes'
                ]
            );

            $perfumeData['imagen_url'] = $upload['secure_url'];
        }

        $perfume->update($perfumeData);

        $variantes = [
            75 => [
                'precio' => $request->variante_75_precio,
                'stock' => $request->variante_75_stock
            ],
            100 => [
                'precio' => $request->variante_100_precio,
                'stock' => $request->variante_100_stock
            ],
            200 => [
                'precio' => $request->variante_200_precio,
                'stock' => $request->variante_200_stock
            ],
        ];

        foreach ($variantes as $volumen => $data) {
            $perfume->variantes()
                ->where('volumen', $volumen)
                ->update($data);
        }

        return redirect()
            ->route('perfumes.index')
            ->with('success', 'Perfume actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perfume $perfume)
    {
        $perfume->delete();

        return redirect()
            ->route('perfumes.index')
            ->with('success', 'Perfume eliminado correctamente');
    }
}