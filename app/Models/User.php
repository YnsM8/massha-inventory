<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si el usuario es de producción
     */
    public function isProduccion()
    {
        return $this->role === 'produccion';
    }

    /**
     * Verificar si el usuario es de ventas
     */
    public function isVentas()
    {
        return $this->role === 'ventas';
    }

    /**
     * Verificar si el usuario es gerencia
     */
    public function isGerencia()
    {
        return $this->role === 'gerencia';
    }

    /**
     * Obtener el nombre legible del rol
     */
    public function getRoleNameAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrador',
            'produccion' => 'Producción (Cocina)',
            'ventas' => 'Ventas',
            'gerencia' => 'Gerencia',
            default => 'Sin rol'
        };
    }

    /**
     * Obtener el color del badge según el rol
     */
    public function getRoleColorAttribute()
    {
        return match($this->role) {
            'admin' => 'danger',
            'produccion' => 'primary',
            'ventas' => 'success',
            'gerencia' => 'warning',
            default => 'secondary'
        };
    }
}
