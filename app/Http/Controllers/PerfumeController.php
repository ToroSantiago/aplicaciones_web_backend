<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perfume;

class PerfumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perfumes = Perfume::all();
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
        $data = $request->validate([
            'nombre' => 'required',
            'marca' => 'required',
            'descripcion' => 'required',
            'volumen' => 'required',
            'precio' => 'required|integer',
            'genero' => 'required|in:M,F,U',
            'stock' => 'required|boolean',
        ]);
    
        Perfume::create($data);
        return redirect()->route('perfumes.index')->with('success', 'Perfume creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perfume = Perfume::findOrFail($id);
        return view('mostrarPerfume', compact('perfume'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $perfume = Perfume::findOrFail($id);
        return view('editarPerfume', compact('perfume'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Perfume $perfume)
    {
        $data = $request->validate([
            'nombre' => 'required',
            'marca' => 'required',
            'precio' => 'required|numeric',
            'descripcion' => 'nullable',
            'volumen' => 'required',
            'genero' => 'required|in:M,F,U',
            'stock' => 'required|boolean',
        ]);
    
        $perfume->update($data);
        return redirect()->route('perfumes.index')->with('success', 'Perfume actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perfume $perfume)
    {
        $perfume->delete();
        return redirect()->route('perfumes.index')->with('success', 'Perfume eliminado correctamente');
    }
}
