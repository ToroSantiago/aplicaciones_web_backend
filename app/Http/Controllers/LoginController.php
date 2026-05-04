<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }
    
    public function showRegisterForm()
    {
        return view('register');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Las credenciales no son válidas.',
            ])->onlyInput('email');
        }

        $usuario = Auth::user();

        // Bloquear a los Clientes del backoffice: el panel Blade es solo para empleados/admins.
        if (! $usuario->canAccessBackoffice()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Esta cuenta no tiene permisos para acceder al backoffice.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Redirigir según el rol: admin al ABM de perfumes, empleado a estadísticas.
        $destino = $usuario->isAdmin()
            ? route('perfumes.index')
            : route('ventas.estadisticas');

        return redirect()->intended($destino);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:usuarios,email'],
            'genero'   => ['nullable', 'in:M,F,O'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $usuario = Usuario::create([
            'nombre'   => $data['nombre'],
            'apellido' => $data['apellido'],
            'username' => $data['email'],
            'email'    => $data['email'],
            'genero'   => $data['genero'] ?? null,
            'password' => Hash::make($data['password']),
            // Forzamos siempre el rol básico: nadie se vuelve admin solo por registrarse.
            'rol'      => Usuario::ROL_EMPLEADO,
        ]);

        Auth::login($usuario);

        return redirect()->route('ventas.estadisticas')
            ->with('success', 'Cuenta creada. Hablá con un administrador si necesitás más permisos.');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}