<?php

namespace Tests\Feature\System;

use Tests\TestCase;
use App\Models\Solicitud;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * PRUEBAS DE SISTEMA - ISO 29119
 * Objetivo: Verificar configuración del sistema completo
 * Técnica: Caja Negra (probamos el sistema sin ver implementación)
 * Enfoque: Configuración ENV, Rutas, Middleware, Autenticación
 */
class SolicitudSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PS-001: Verificar configuración de Base de Datos
     * 
     * Objetivo: Validar que la conexión a BD funciona y las tablas existen
     * Entrada: Configuración de .env (DB_CONNECTION, DB_DATABASE)
     * Salida Esperada: Tablas 'solicitudes' y 'solicitud_items' existen
     * Técnica: Caja Negra - Solo verificamos que el sistema responde
     */
    public function test_configuracion_base_datos_y_tablas_existen()
    {
        // ASSERT - Verificar conexión a BD
        $this->assertNotNull(config('database.default'));
        // En testing puede ser sqlite o mysql, ambos son válidos
        $this->assertContains(config('database.default'), ['mysql', 'sqlite']);
        
        // Verificar que las tablas existen mediante Schema
        $this->assertTrue(\Schema::hasTable('solicitudes'));
        $this->assertTrue(\Schema::hasTable('solicitud_items'));
        $this->assertTrue(\Schema::hasTable('users'));
        $this->assertTrue(\Schema::hasTable('items'));

        // Verificar columnas críticas de la tabla solicitudes
        $this->assertTrue(\Schema::hasColumn('solicitudes', 'codigo_solicitud'));
        $this->assertTrue(\Schema::hasColumn('solicitudes', 'evento'));
        $this->assertTrue(\Schema::hasColumn('solicitudes', 'fecha_evento'));
        $this->assertTrue(\Schema::hasColumn('solicitudes', 'estado'));
        $this->assertTrue(\Schema::hasColumn('solicitudes', 'user_id'));
        $this->assertTrue(\Schema::hasColumn('solicitudes', 'aprobado_por'));

        // Verificar columnas de solicitud_items
        $this->assertTrue(\Schema::hasColumn('solicitud_items', 'solicitud_id'));
        $this->assertTrue(\Schema::hasColumn('solicitud_items', 'item_id'));
        $this->assertTrue(\Schema::hasColumn('solicitud_items', 'cantidad_solicitada'));
        $this->assertTrue(\Schema::hasColumn('solicitud_items', 'stock_suficiente'));

        echo "\n✓ PS-001: Configuración de BD y estructura de tablas correcta";
        echo "\n  - DB_CONNECTION: " . config('database.default');
    }

    /**
     * PS-002: Verificar rutas del sistema
     * 
     * Objetivo: Validar que todas las rutas de solicitudes están registradas
     * Entrada: Configuración de routes/web.php
     * Salida Esperada: Rutas accesibles con middleware auth
     * Técnica: Caja Negra - Probamos endpoints sin autenticación
     */
    public function test_rutas_solicitudes_estan_registradas_y_protegidas()
    {
        // ARRANGE - Crear usuario para autenticación
        $user = User::factory()->create();

        // TEST 1: Ruta INDEX sin autenticación debe redirigir a login
        $response = $this->get(route('solicitudes.index'));
        $response->assertRedirect(route('login'));

        // TEST 2: Ruta CREATE sin autenticación debe redirigir a login
        $response = $this->get(route('solicitudes.create'));
        $response->assertRedirect(route('login'));

        // TEST 3: Con autenticación, rutas deben ser accesibles
        $this->actingAs($user);
        
        $responseIndex = $this->get(route('solicitudes.index'));
        $responseIndex->assertStatus(200);
        
        $responseCreate = $this->get(route('solicitudes.create'));
        $responseCreate->assertStatus(200);

        // TEST 4: Verificar que las rutas existen en el sistema
        $this->assertTrue(route('solicitudes.index') !== null);
        $this->assertTrue(route('solicitudes.create') !== null);
        $this->assertTrue(route('solicitudes.store') !== null);

        echo "\n✓ PS-002: Rutas registradas y middleware auth funcionando";
    }

    /**
     * PS-003: Verificar variables de entorno críticas
     * 
     * Objetivo: Validar configuración de .env necesaria para el sistema
     * Entrada: Archivo .env (APP_NAME, APP_ENV, DB_*)
     * Salida Esperada: Variables configuradas correctamente
     * Criterio: Sistema debe tener configuración mínima para funcionar
     */
    public function test_variables_entorno_estan_configuradas_correctamente()
    {
        // ASSERT - Verificar variables críticas de aplicación
        $this->assertNotNull(config('app.name'));
        $this->assertNotEmpty(config('app.key'));
        $this->assertContains(config('app.env'), ['local', 'testing', 'production']);

        // Verificar configuración de base de datos
        $this->assertNotNull(config('database.connections.mysql.host'));
        $this->assertNotNull(config('database.connections.mysql.database'));
        $this->assertNotNull(config('database.connections.mysql.username'));

        // Verificar timezone (importante para fecha_evento)
        $this->assertNotNull(config('app.timezone'));

        // Verificar que estamos en modo testing
        $this->assertEquals('testing', config('app.env'));

        // Verificar configuración de sesión (necesaria para auth)
        $this->assertNotNull(config('session.driver'));

        // En testing, cache debe ser array (no redis/file)
        $this->assertEquals('array', config('cache.default'));

        echo "\n✓ PS-003: Variables de entorno configuradas correctamente";
        echo "\n  - APP_ENV: " . config('app.env');
        echo "\n  - DB_CONNECTION: " . config('database.default');
        echo "\n  - CACHE_DRIVER: " . config('cache.default');
    }
}