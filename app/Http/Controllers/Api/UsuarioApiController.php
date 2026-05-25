<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Validation\Rules\Password;

class UsuarioApiController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:usuarios',
            'password' => [
                'required', 
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            // Rol por defecto será 'Cliente' para registros desde el frontend
        ]);

        $data['username'] = $data['email'];
        $data['password'] = Hash::make($data['password']);
        $data['rol'] = 'Cliente'; // Siempre será Cliente desde el frontend

        $usuario = Usuario::create($data);

        // Si usas Sanctum para tokens
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'usuario' => $usuario,
            'token' => $token,
            'message' => 'Usuario registrado exitosamente'
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('email', $credentials['email'])->first();

        if (!$usuario || !Hash::check($credentials['password'], $usuario->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'usuario' => $usuario,
            'token' => $token,
            'message' => 'Login exitoso'
        ]);
    }

    /**
     * Devuelve los datos del usuario autenticado.
     * Usado por el SPA para verificar que el token sigue siendo válido y
     * para refrescar info del usuario en pantalla.
     *
     * GET /edp/user  (auth:sanctum)
     */
    public function me(Request $request)
    {
        return response()->json([
            'usuario' => $request->user(),
        ]);
    }

    /**
     * Cierra la sesión actual revocando el token que vino en el header.
     * Otros tokens del mismo usuario (si los hubiera) siguen vivos —
     * solo se elimina el que se usó en esta request.
     *
     * POST /edp/logout  (auth:sanctum)
     */
    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }
}