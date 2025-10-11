<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Lista de usuarios (solo admin)
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $roles = [
            'admin' => 'Administrador',
            'produccion' => 'Producción (Cocina)',
            'ventas' => 'Ventas',
            'gerencia' => 'Gerencia',
        ];
        return view('users.create', compact('roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,produccion,ventas,gerencia'],
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            return redirect()->route('users.index')
                ->with('success', "Usuario {$user->name} creado exitosamente");
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear usuario: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de usuario
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $user)
    {
        $roles = [
            'admin' => 'Administrador',
            'produccion' => 'Producción (Cocina)',
            'ventas' => 'Ventas',
            'gerencia' => 'Gerencia',
        ];
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,produccion,ventas,gerencia'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];
            
            if ($request->filled('password')) {
                $user->password = Hash::make($validated['password']);
            }
            
            $user->save();

            return redirect()->route('users.index')
                ->with('success', "Usuario {$user->name} actualizado exitosamente");
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        // Evitar que el usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario');
        }

        try {
            $name = $user->name;
            $user->delete();

            return redirect()->route('users.index')
                ->with('success', "Usuario {$name} eliminado exitosamente");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    }
}