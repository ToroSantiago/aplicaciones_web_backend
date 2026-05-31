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

    /**
     * Actualizar los datos del usuario autenticado.
     *
     * - Cambios "livianos" (nombre, apellido, género) no piden password.
     * - Cambios "sensibles" (email, contraseña) requieren `password_actual`
     *   correcta. Esto evita que alguien que dejó el SPA abierto y se
     *   levantó del escritorio termine con su cuenta capturada.
     *
     * PUT /edp/me  (auth:sanctum)
     */
    public function updateMe(Request $request)
    {
        $usuario = $request->user();

        $data = $request->validate([
            'nombre'         => 'sometimes|required|string|max:255',
            'apellido'       => 'sometimes|required|string|max:255',
            'genero'         => 'sometimes|nullable|in:M,F,O',
            'email'          => 'sometimes|required|email|unique:usuarios,email,' . $usuario->id,
            'password'       => [
                'sometimes',
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
            // Solo lo exigimos cuando se quiere cambiar email o password.
            'password_actual' => 'required_with:email,password|string',
        ], [
            'password_actual.required_with' => 'Necesitás tu contraseña actual para cambiar el email o la contraseña.',
        ]);

        // Verificar la contraseña actual si se está cambiando email o password.
        $cambiaEmail    = $request->has('email')    && $request->email    !== $usuario->email;
        $cambiaPassword = $request->has('password') && $request->password !== null;

        if (($cambiaEmail || $cambiaPassword) && ! Hash::check($request->password_actual, $usuario->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password_actual' => ['La contraseña actual no es correcta.'],
            ]);
        }

        // Armar el set de cambios. Nunca permitimos que el cliente edite su
        // propio rol desde este endpoint — eso lo hace solo el admin desde
        // el backoffice (UsuarioController::update).
        $changes = [];
        foreach (['nombre', 'apellido', 'genero', 'email'] as $field) {
            if (array_key_exists($field, $data)) {
                $changes[$field] = $data[$field];
            }
        }
        if (! empty($data['password'] ?? null)) {
            $changes['password'] = Hash::make($data['password']);
        }
        // Si se cambia el email, mantenemos username == email para no
        // dejar inconsistencias (el username se setea así al registrarse).
        if (isset($changes['email'])) {
            $changes['username'] = $changes['email'];
        }

        if (! empty($changes)) {
            $usuario->update($changes);
        }

        return response()->json([
            'usuario' => $usuario->fresh(),
            'message' => 'Perfil actualizado correctamente',
        ]);
    }
}