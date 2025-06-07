<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UsuarioApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('username', $credentials['username'])->first();

        if (!$usuario || !Hash::check($credentials['password'], $usuario->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'usuario' => $usuario,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['mensaje' => 'Sesión cerrada correctamente']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:usuarios',
            'password' => [
                'required', 'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'rol' => 'in:Cliente,Administrador',
        ]);

        $data['username'] = $data['email'];
        $data['password'] = Hash::make($data['password']);

        $usuario = Usuario::create($data);
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'usuario' => $usuario,
            'token' => $token,
        ], 201);
    }

    public function index()
    {
        return response()->json(Usuario::all());
    }

    public function show($id)
    {
        return response()->json(Usuario::findOrFail($id));
    }
}
