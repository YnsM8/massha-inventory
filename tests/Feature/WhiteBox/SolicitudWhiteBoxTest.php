<?php

namespace Tests\Feature\WhiteBox;

use Tests\TestCase;
use App\Models\Solicitud;
use App\Models\SolicitudItem;
use App\Models\Item;
use App\Models\User;
use App\Models\Movement;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * PRUEBAS DE CAJA BLANCA - ISO 29119
 * Objetivo: Probar lógica interna conociendo el código
 * Técnica: Verificamos métodos específicos y flujos internos
 * Enfoque: Conocemos la implementación y probamos rutas críticas
 */
class SolicitudWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PCB-001: Verificar método aprobar() descuenta stock correctamente
     * 
     * Objetivo: Probar que el método aprobar() de Solicitud llama a removeStock()
     * Entrada: Solicitud con 2 items, stock suficiente
     * Salida Esperada: Stock descontado, Movement creado con reason='production'
     * Técnica: Caja Blanca - Conocemos que aprobar() usa removeStock() internamente
     */
    public function test_metodo_aprobar_descuenta_stock_y_crea_movements()
    {
        // ARRANGE - Crear estructura completa
        $user = User::factory()->create();
        $aprobador = User::factory()->create();
        
        // Items con stock suficiente
        $item1 = Item::factory()->create([
            'name' => 'Pollo',
            'current_stock' => 100,
            'unit' => 'kg'
        ]);
        
        $item2 = Item::factory()->create([
            'name' => 'Arroz',
            'current_stock' => 50,
            'unit' => 'kg'
        ]);

        // Crear solicitud pendiente
        $solicitud = Solicitud::factory()->create([
            'user_id' => $user->id,
            'estado' => 'pendiente',
            'codigo_solicitud' => 'SOL-20251008-TEST',
            'evento' => 'Boda Test'
        ]);

        // Agregar items a la solicitud
        SolicitudItem::create([
            'solicitud_id' => $solicitud->id,
            'item_id' => $item1->id,
            'cantidad_solicitada' => 20,
            'cantidad_disponible' => 100,
            'stock_suficiente' => true
        ]);

        SolicitudItem::create([
            'solicitud_id' => $solicitud->id,
            'item_id' => $item2->id,
            'cantidad_solicitada' => 10,
            'cantidad_disponible' => 50,
            'stock_suficiente' => true
        ]);

        // Verificar stock inicial
        $this->assertEquals(100, $item1->fresh()->current_stock);
        $this->assertEquals(50, $item2->fresh()->current_stock);
        $this->assertDatabaseCount('movements', 0);

        // ACT - Aprobar solicitud (esto internamente llama a removeStock)
        $solicitud->aprobar($aprobador->id);

        // ASSERT - Verificar que el stock se descontó
        $this->assertEquals(80, $item1->fresh()->current_stock); // 100 - 20
        $this->assertEquals(40, $item2->fresh()->current_stock); // 50 - 10

        // Verificar que se crearon 2 Movements con reason='production'
        $this->assertDatabaseCount('movements', 2);
        
        $this->assertDatabaseHas('movements', [
            'item_id' => $item1->id,
            'type' => 'outgoing',
            'quantity' => 20,
            'reason' => 'production',
            'user_id' => $aprobador->id
        ]);

        $this->assertDatabaseHas('movements', [
            'item_id' => $item2->id,
            'type' => 'outgoing',
            'quantity' => 10,
            'reason' => 'production',
            'user_id' => $aprobador->id
        ]);

        // Verificar que el reference incluye el código de solicitud
        $movements = Movement::all();
        foreach ($movements as $movement) {
            $this->assertStringContainsString('SOL-20251008-TEST', $movement->reference);
            $this->assertStringContainsString('Boda Test', $movement->reference);
        }

        // Verificar estado de la solicitud
        $this->assertEquals('aprobada', $solicitud->fresh()->estado);
        $this->assertEquals($aprobador->id, $solicitud->fresh()->aprobado_por);
        $this->assertNotNull($solicitud->fresh()->fecha_aprobacion);

        echo "\n✓ PCB-001: Método aprobar() descuenta stock y crea movements correctamente";
    }

    /**
     * PCB-002: Verificar generación de código único de solicitud
     * 
     * Objetivo: Probar la lógica de generación de código SOL-YYYYMMDD-XXX
     * Entrada: Crear múltiples solicitudes el mismo día
     * Salida Esperada: Códigos secuenciales (001, 002, 003)
     * Técnica: Caja Blanca - Conocemos el formato del código en store()
     */
    public function test_generacion_codigo_solicitud_es_secuencial_por_dia()
    {
        // ARRANGE
        $user = User::factory()->create();
        $item = Item::factory()->create(['current_stock' => 100]);
        
        $this->actingAs($user);

        $fechaEvento = now()->addDays(10)->format('Y-m-d');
        $hoy = date('Ymd');

        // ACT - Crear 3 solicitudes el mismo día
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->post(route('solicitudes.store'), [
                'evento' => "Evento Test {$i}",
                'fecha_evento' => $fechaEvento,
                'items' => [
                    ['item_id' => $item->id, 'cantidad' => 5]
                ]
            ]);
        }

        // ASSERT - Verificar códigos secuenciales
        $solicitudes = Solicitud::orderBy('id')->get();
        
        $this->assertCount(3, $solicitudes);
        
        // Verificar formato y secuencia
        $this->assertEquals("SOL-{$hoy}-001", $solicitudes[0]->codigo_solicitud);
        $this->assertEquals("SOL-{$hoy}-002", $solicitudes[1]->codigo_solicitud);
        $this->assertEquals("SOL-{$hoy}-003", $solicitudes[2]->codigo_solicitud);

        // Verificar que todos tienen el formato correcto
        foreach ($solicitudes as $solicitud) {
            $this->assertMatchesRegularExpression('/^SOL-\d{8}-\d{3}$/', $solicitud->codigo_solicitud);
        }

        echo "\n✓ PCB-002: Generación de código secuencial funciona correctamente";
        echo "\n  - Códigos generados: {$solicitudes[0]->codigo_solicitud}, {$solicitudes[1]->codigo_solicitud}, {$solicitudes[2]->codigo_solicitud}";
    }

    /**
     * PCB-003: Verificar transacción DB::beginTransaction en aprobar
     * 
     * Objetivo: Probar que el Controller usa transacciones correctamente
     * Entrada: Intentar aprobar solicitud con stock insuficiente via HTTP
     * Salida Esperada: Rollback, mensaje de error, solicitud sigue pendiente
     * Técnica: Caja Blanca - Conocemos que el Controller usa DB::beginTransaction
     */
    public function test_controller_hace_rollback_cuando_stock_insuficiente()
    {
        // ARRANGE
        $user = User::factory()->create();
        $aprobador = User::factory()->create();
        
        $item = Item::factory()->create([
            'name' => 'Leche',
            'current_stock' => 50, // Stock inicial: 50 litros
            'unit' => 'l'
        ]);

        // Crear solicitud solicitando 30 litros
        $solicitud = Solicitud::factory()->create([
            'user_id' => $user->id,
            'estado' => 'pendiente',
            'codigo_solicitud' => 'SOL-TEST-001'
        ]);

        SolicitudItem::create([
            'solicitud_id' => $solicitud->id,
            'item_id' => $item->id,
            'cantidad_solicitada' => 30,
            'cantidad_disponible' => 50,
            'stock_suficiente' => true
        ]);

        // SIMULACIÓN: Stock disminuyó antes de aprobar
        $item->current_stock = 5; // Ahora solo quedan 5 litros
        $item->save();

        // Verificar estado inicial
        $this->assertEquals(5, $item->fresh()->current_stock);
        $this->assertEquals('pendiente', $solicitud->fresh()->estado);
        $this->assertDatabaseCount('movements', 0);

        // ACT - Intentar aprobar via Controller (como usuario autenticado)
        $this->actingAs($aprobador);
        
        $response = $this->post(route('solicitudes.aprobar', $solicitud->id));

        // ASSERT - Verificar que detectó el problema
        $response->assertRedirect(); // Redirige de vuelta
        $response->assertSessionHas('error'); // Tiene mensaje de error
        
        // Verificar el mensaje específico
        $session = session('error');
        $this->assertStringContainsString('Stock insuficiente', $session);
        $this->assertStringContainsString('Leche', $session);

        // Verificar que el stock NO cambió (rollback exitoso)
        $this->assertEquals(5, $item->fresh()->current_stock);
        
        // Verificar que la solicitud NO se aprobó
        $this->assertEquals('pendiente', $solicitud->fresh()->estado);
        $this->assertNull($solicitud->fresh()->aprobado_por);
        $this->assertNull($solicitud->fresh()->fecha_aprobacion);
        
        // Verificar que NO se crearon Movements
        $this->assertDatabaseCount('movements', 0);

        echo "\n✓ PCB-003: Controller valida stock y hace rollback correctamente";
    }
}