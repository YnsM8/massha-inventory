<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login con validaciones personalizadas
     */
    public function login(Request $request)
    {
        // Validar formato básico
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Ingresa un correo electrónico válido',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Verificar si el usuario existe
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['El correo electrónico no está registrado en el sistema'],
            ]);
        }

        // Intentar autenticar
        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'password' => ['La contraseña es incorrecta'],
            ]);
        }

        // Regenerar sesión
        $request->session()->regenerate();

        // Mensaje de bienvenida personalizado según el rol
        $user = Auth::user();
        $mensaje = "Has iniciado sesión exitosamente como {$user->role_name}";

        // Redireccionar al dashboard con mensaje de éxito
        return redirect()->intended($this->redirectTo)
            ->with('login_success', $mensaje);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Sesión cerrada correctamente');
    }
}