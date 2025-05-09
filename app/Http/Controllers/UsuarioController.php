<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    /**
     * Mostrar listado de usuarios (solo para admin)
     */
    public function index()
    {
        $usuarios = Usuario::all();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Formulario para crear usuario
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|unique:usuarios,username|min:3|max:20',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => [
                'required', 
                'confirmed', 
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
            ],
            'rol' => 'required|in:Cliente,Administrador',
            'genero' => 'nullable|in:M,F,O',
        ]);

        $data['password'] = Hash::make($data['password']);
        
        Usuario::create($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
    }

    /**
     * Mostrar usuario específico
     */
    public function show(Usuario $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Formulario para editar usuario
     */
    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'username' => 'required|unique:usuarios,username,'.$usuario->id.'|min:3|max:20',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,'.$usuario->id,
            'rol' => 'required|in:Cliente,Administrador',
            'genero' => 'nullable|in:M,F,O',
        ]);

        // Solo actualizar password si se proporcionó
        if ($request->filled('password')) {
            $request->validate([
                'password' => [
                    'confirmed', 
                    Password::min(8)
                        ->mixedCase()
                        ->letters()
                        ->numbers()
                        ->symbols()
                ]
            ]);
            
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);
        
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(Usuario $usuario)
    {
        // Evitar eliminar el propio usuario administrador
        if (auth()->id() === $usuario->id) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario');
        }
        
        $usuario->delete();
        
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}