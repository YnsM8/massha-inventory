<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PRUEBA 1: Usuario Admin puede acceder al dashboard
     */
    public function test_admin_puede_acceder_al_dashboard(): void
    {
        // Crear usuario admin
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        // Autenticar como admin
        $response = $this->actingAs($admin)->get('/dashboard');

        // Verificar que puede acceder
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /**
     * PRUEBA 2: Usuario Producción solo puede consultar stock
     */
    public function test_produccion_solo_puede_consultar_stock(): void
    {
        // Crear usuario de producción
        $produccion = User::factory()->create([
            'email' => 'produccion@test.com',
            'password' => bcrypt('password123'),
            'role' => 'produccion',
        ]);

        // Autenticar como producción
        $this->actingAs($produccion);

        // Puede ver inventario
        $response = $this->get('/inventory');
        $response->assertStatus(200);

        // NO puede acceder a la sección de usuarios (solo admin)
        $response = $this->get('/users');
        $response->assertStatus(403); // Forbidden
    }

    /**
     * PRUEBA 3: Usuario Ventas solo puede consultar disponibilidad
     */
    public function test_ventas_solo_puede_consultar_disponibilidad(): void
    {
        // Crear usuario de ventas
        $ventas = User::factory()->create([
            'email' => 'ventas@test.com',
            'password' => bcrypt('password123'),
            'role' => 'ventas',
        ]);

        // Autenticar como ventas
        $this->actingAs($ventas);

        // Puede ver inventario (para consultar disponibilidad)
        $response = $this->get('/inventory');
        $response->assertStatus(200);

        // NO puede acceder a usuarios
        $response = $this->get('/users');
        $response->assertStatus(403);
    }

    /**
     * PRUEBA 4: Usuario sin autenticar no puede acceder
     */
    public function test_usuario_sin_autenticar_no_puede_acceder(): void
    {
        // Intentar acceder al dashboard sin login
        $response = $this->get('/dashboard');

        // Debe redirigir al login
        $response->assertRedirect('/login');
    }

    /**
     * PRUEBA 5: Admin puede crear usuarios
     */
    public function test_admin_puede_crear_usuarios(): void
    {
        // Crear usuario admin
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        // Autenticar como admin
        $this->actingAs($admin);

        // Intentar acceder a la página de crear usuario
        $response = $this->get('/users/create');
        $response->assertStatus(200);

        // Crear un nuevo usuario
        $response = $this->post('/users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'produccion',
        ]);

        // Verificar que se creó correctamente
        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@test.com',
            'role' => 'produccion',
        ]);
    }

    /**
     * PRUEBA 6: Roles tienen permisos correctos
     */
    public function test_roles_tienen_permisos_correctos(): void
    {
        // Crear usuarios con diferentes roles
        $admin = User::factory()->create(['role' => 'admin']);
        $produccion = User::factory()->create(['role' => 'produccion']);
        $ventas = User::factory()->create(['role' => 'ventas']);
        $gerencia = User::factory()->create(['role' => 'gerencia']);

        // Verificar métodos de roles
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isProduccion());
        $this->assertFalse($admin->isVentas());
        $this->assertFalse($admin->isGerencia());

        $this->assertTrue($produccion->isProduccion());
        $this->assertFalse($produccion->isAdmin());

        $this->assertTrue($ventas->isVentas());
        $this->assertFalse($ventas->isAdmin());

        $this->assertTrue($gerencia->isGerencia());
        $this->assertFalse($gerencia->isAdmin());

        // Verificar que los roles están correctamente asignados
        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('produccion', $produccion->role);
        $this->assertEquals('ventas', $ventas->role);
        $this->assertEquals('gerencia', $gerencia->role);
    }
}