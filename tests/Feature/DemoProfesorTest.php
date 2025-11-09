<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Movement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DemoProfesorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ==========================================
     * 1. PRUEBA UNITARIA (Unit Test)
     * Objetivo: Probar una unidad mínima de código aislada (un método).
     * Qué prueba: Que el "Accessor" que calcula el valor total funcione.
     * ==========================================
     */
    public function test_unitaria_calculo_valor_total_item(): void
    {
        // Arrange (Preparar): Creamos un item en memoria (sin guardar en BD si fuera pura, 
        // pero aquí usamos factory para facilitar)
        $item = Item::factory()->make([
            'current_stock' => 10,
            'unit_price' => 5.50
        ]);

        // Act (Actuar): Accedemos al atributo calculado
        $totalValue = $item->total_value;

        // Assert (Verificar): 10 * 5.50 debe ser 55.00
        $this->assertEquals(55.00, $totalValue);
    }

    /**
     * ==========================================
     * 2. PRUEBA DE INTEGRACIÓN (Integration Test)
     * Objetivo: Verificar que dos o más módulos funcionan bien juntos.
     * Qué prueba: Que al usar el método de negocio del Item, se crea el Movement y se actualiza el stock.
     * ==========================================
     */
    public function test_integracion_movimiento_actualiza_stock_item(): void
    {
        // Arrange: Crear item con 100kg y un usuario
        $item = Item::factory()->create(['current_stock' => 100]);
        $user = User::factory()->create();

        // Act: Usar el método de negocio que integra ambos modelos
        // CORRECCIÓN: Usamos 'event' como motivo válido en lugar de 'outgoing'
        $item->removeStock(20, $user->id, 'event', 'Test Integración');

        // Assert 1: Verificar que el stock del ITEM bajó a 80
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'current_stock' => 80
        ]);

        // Assert 2: Verificar que el MOVIMIENTO se creó automáticamente con el motivo correcto
        $this->assertDatabaseHas('movements', [
            'item_id' => $item->id,
            'type' => 'outgoing',
            'quantity' => 20,
            'reason' => 'event', // <-- Verificamos que se guardó como 'event'
            'user_id' => $user->id
        ]);
    }

    /**
     * ==========================================
     * 3. PRUEBA DE CAJA BLANCA (White Box Test)
     * Objetivo: Probar la lógica interna conociendo el código (IFs, caminos).
     * Qué prueba: El método 'updateStatus()' y sus caminos (if stock <= 0, elseif stock <= min, else normal).
     * ==========================================
     */
    public function test_caja_blanca_actualizacion_estados_item(): void
    {
        $item = Item::factory()->create(['min_stock' => 10]);

        // CAMINO 1: Stock en 0 -> Debe ser 'expired'
        $item->current_stock = 0;
        $item->updateStatus(); // Llamamos al método interno directamente
        $this->assertEquals('expired', $item->status);

        // CAMINO 2: Stock bajo (5) -> Debe ser 'low'
        $item->current_stock = 5;
        $item->updateStatus();
        $this->assertEquals('low', $item->status);

        // CAMINO 3: Stock suficiente (20) -> Debe ser 'normal'
        $item->current_stock = 20;
        $item->updateStatus();
        $this->assertEquals('normal', $item->status);
    }

    /**
     * ==========================================
     * 4. PRUEBA DE CAJA NEGRA (Black Box Test)
     * Objetivo: Probar funcionalidad desde fuera, sin saber cómo está hecha por dentro.
     * Qué prueba: Intentar entrar al dashboard sin loguearse (debe rebotar al login).
     * ==========================================
     */
    public function test_caja_negra_acceso_denegado_a_invitados(): void
    {
        // Act: Intentar entrar a una ruta protegida como un usuario anónimo
        $response = $this->get('/dashboard');

        // Assert: El sistema debe redirigirnos al login (código 302)
        // No nos importa CÓMO lo hace (middleware, controller), solo que lo haga.
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}