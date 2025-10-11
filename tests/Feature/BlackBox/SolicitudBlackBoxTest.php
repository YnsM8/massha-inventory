<?php

namespace Tests\Feature\BlackBox;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * PRUEBAS DE CAJA NEGRA - ISO 29119
 * Objetivo: Probar funcionalidad sin conocer implementación interna
 * Técnica: Solo inputs y outputs esperados
 * Enfoque: Comportamiento del sistema desde perspectiva del usuario
 */
class SolicitudBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PCN-001: Validación de formulario con datos inválidos
     * 
     * Objetivo: Verificar que el sistema rechaza datos incorrectos
     * Entrada: Formulario con fecha en el pasado, sin items, campos vacíos
     * Salida Esperada: Errores de validación, no se crea registro
     * Técnica: Caja Negra - Solo probamos que rechaza, no cómo lo hace
     */
    public function test_formulario_rechaza_datos_invalidos()
    {
        // ARRANGE
        $user = User::factory()->create();
        $this->actingAs($user);

        // ACT & ASSERT - Test 1: Fecha en el pasado
        $response = $this->post(route('solicitudes.store'), [
            'evento' => 'Evento Pasado',
            'fecha_evento' => '2020-01-01', // Fecha pasada
            'items' => [
                ['item_id' => 1, 'cantidad' => 10]
            ]
        ]);
        $response->assertSessionHasErrors('fecha_evento');

        // Test 2: Sin items
        $response = $this->post(route('solicitudes.store'), [
            'evento' => 'Evento Sin Items',
            'fecha_evento' => now()->addDays(5)->format('Y-m-d'),
            'items' => [] // Sin items
        ]);
        $response->assertSessionHasErrors('items');

        // Test 3: Evento vacío
        $response = $this->post(route('solicitudes.store'), [
            'evento' => '', // Vacío
            'fecha_evento' => now()->addDays(5)->format('Y-m-d'),
            'items' => [
                ['item_id' => 1, 'cantidad' => 10]
            ]
        ]);
        $response->assertSessionHasErrors('evento');

        // Test 4: Cantidad negativa
        $item = Item::factory()->create();
        $response = $this->post(route('solicitudes.store'), [
            'evento' => 'Evento Test',
            'fecha_evento' => now()->addDays(5)->format('Y-m-d'),
            'items' => [
                ['item_id' => $item->id, 'cantidad' => -10] // Negativo
            ]
        ]);
        $response->assertSessionHasErrors('items.0.cantidad');

        // Verificar que NO se creó ninguna solicitud
        $this->assertDatabaseCount('solicitudes', 0);

        echo "\n✓ PCN-001: Sistema rechaza correctamente datos inválidos";
    }

    /**
     * PCN-002: Flujo exitoso de creación de solicitud
     * 
     * Objetivo: Verificar que con datos correctos se crea la solicitud
     * Entrada: Formulario válido con evento, fecha futura, items con stock
     * Salida Esperada: Solicitud creada, redirección, mensaje de éxito
     * Técnica: Caja Negra - Solo verificamos resultado final
     */
    public function test_creacion_exitosa_con_datos_validos()
    {
        // ARRANGE
        $user = User::factory()->create();
        $items = Item::factory()->count(3)->create([
            'current_stock' => 100
        ]);
        
        $this->actingAs($user);

        // ACT - Enviar formulario válido
        $response = $this->post(route('solicitudes.store'), [
            'evento' => 'Boda Pérez 2025',
            'fecha_evento' => now()->addDays(30)->format('Y-m-d'),
            'observaciones' => 'Evento para 150 personas',
            'items' => [
                ['item_id' => $items[0]->id, 'cantidad' => 10],
                ['item_id' => $items[1]->id, 'cantidad' => 15],
                ['item_id' => $items[2]->id, 'cantidad' => 5],
            ]
        ]);

        // ASSERT - Verificar resultados esperados
        $response->assertRedirect(); // Redirige (no sabemos a dónde, caja negra)
        $response->assertSessionHas('success'); // Hay mensaje de éxito

        // Verificar que se creó en BD (resultado observable)
        $this->assertDatabaseCount('solicitudes', 1);
        $this->assertDatabaseCount('solicitud_items', 3);
        
        $this->assertDatabaseHas('solicitudes', [
            'evento' => 'Boda Pérez 2025',
            'estado' => 'pendiente',
            'user_id' => $user->id,
        ]);

        echo "\n✓ PCN-002: Creación exitosa con datos válidos";
    }

    /**
     * PCN-003: Comportamiento con stock insuficiente
     * 
     * Objetivo: Verificar respuesta del sistema cuando no hay stock suficiente
     * Entrada: Solicitud con cantidad mayor al stock disponible
     * Salida Esperada: Solicitud se crea pero con advertencia
     * Técnica: Caja Negra - Solo observamos comportamiento externo
     */
    public function test_sistema_advierte_cuando_stock_insuficiente()
    {
        // ARRANGE
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => 'Arroz',
            'current_stock' => 10, // Solo 10 kg disponibles
        ]);
        
        $this->actingAs($user);

        // ACT - Solicitar más de lo disponible
        $response = $this->post(route('solicitudes.store'), [
            'evento' => 'Evento Grande',
            'fecha_evento' => now()->addDays(15)->format('Y-m-d'),
            'items' => [
                ['item_id' => $item->id, 'cantidad' => 50] // Solicitar 50 kg (solo hay 10)
            ]
        ]);

        // ASSERT - Sistema debe crear la solicitud pero advertir
        $response->assertRedirect();
        
        // Debe haber mensaje de advertencia (warning)
        $response->assertSessionHas('warning');
        
        // La solicitud se crea de todos modos
        $this->assertDatabaseCount('solicitudes', 1);
        
        // Verificar que se marcó como stock insuficiente
        $this->assertDatabaseHas('solicitud_items', [
            'item_id' => $item->id,
            'cantidad_solicitada' => 50,
            'cantidad_disponible' => 10,
            'stock_suficiente' => false, // Marcado como insuficiente
        ]);

        echo "\n✓ PCN-003: Sistema advierte correctamente sobre stock insuficiente";
    }
}