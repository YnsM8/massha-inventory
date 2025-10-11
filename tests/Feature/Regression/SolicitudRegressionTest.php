<?php

namespace Tests\Feature\Regression;

use Tests\TestCase;
use App\Models\Solicitud;
use App\Models\SolicitudItem;
use App\Models\Item;
use App\Models\User;
use App\Models\Movement;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * PRUEBAS DE REGRESIÓN - ISO 29119
 * Objetivo: Verificar que funcionalidades existentes siguen funcionando
 * Técnica: Re-ejecutar pruebas críticas después de agregar Solicitudes
 * Enfoque: Asegurar que nada se rompió en el sistema base
 */
class SolicitudRegressionTest extends TestCase
{
    use RefreshDatabase;

    // /**
    //  * PR-001: Verificar que Items sigue funcionando después de agregar Solicitudes
    //  * 
    //  * Objetivo: Asegurar que el CRUD de Items NO se afectó
    //  * Entrada: Crear, actualizar, eliminar items
    //  * Salida Esperada: Operaciones exitosas sin errores
    //  * Regresión: Módulo Items debe funcionar independientemente
    //  */
    // public function test_crud_items_sigue_funcionando_correctamente()
    // {
    //     // ARRANGE
    //     $adminUser = User::factory()->create(['role' => 'admin']);
    //     $supplier = Supplier::factory()->create(['status' => 'active']);
        
    //     $this->actingAs($adminUser);

    //     // TEST 1: Crear Item
    //     $uniqueCode = 'TEST-' . time() . '-' . rand(1000, 9999);
        
    //     $response = $this->post(route('inventory.store'), [
    //         'code' => $uniqueCode,
    //         'name' => 'Tomate Fresco',
    //         'category' => 'Verduras',
    //         'min_stock' => 10,
    //         'unit' => 'kg',
    //         'unit_price' => 5.50,
    //         'default_supplier_id' => $supplier->id,
    //         'description' => 'Tomate para ensaladas'
    //     ]);

    //     $response->assertRedirect(route('inventory.index'));
    //     $response->assertSessionHas('success');
        
    //     $item = Item::where('code', $uniqueCode)->first();
    //     $this->assertNotNull($item);
    //     $this->assertEquals('Tomate Fresco', $item->name);

    //         // // TEST 2: Actualizar Item (mantener el mismo código)
    //         // $response = $this->put(route('inventory.update', $item->id), [
    //         //     'code' => $uniqueCode, // MISMO código (es válido en update del mismo item)
    //         //     'name' => 'Tomate Cherry', // Nombre cambiado
    //         //     'category' => 'Verduras',
    //         //     'min_stock' => 15, // Stock mínimo cambiado
    //         //     'unit' => 'kg',
    //         //     'unit_price' => 6.00,
    //         //     'default_supplier_id' => $supplier->id,
    //         // ]);

    //         // $response->assertRedirect(route('inventory.index'));
    //         // $response->assertSessionHas('success');
            
    //         // // Refrescar el item desde la BD
    //         // $item->refresh();
            
    //         // $this->assertEquals('Tomate Cherry', $item->name);
    //         // $this->assertEquals(15, $item->min_stock);

    //     // // TEST 2: Actualizar Item (cambiar a un nuevo código único también)
    //     // $newUniqueCode = 'TEST-UPD-' . time() . '-' . rand(1000, 9999);
        
    //     // $response = $this->put(route('inventory.update', $item->id), [
    //     //     'code' => $newUniqueCode, // Nuevo código único
    //     //     'name' => 'Tomate Cherry', // Nombre cambiado
    //     //     'category' => 'Verduras',
    //     //     'min_stock' => 15, // Stock mínimo cambiado
    //     //     'unit' => 'kg',
    //     //     'unit_price' => 6.00,
    //     //     'default_supplier_id' => $supplier->id,
    //     // ]);

    //     // $response->assertRedirect(route('inventory.index'));
    //     // $response->assertSessionHas('success');
    //     // $this->assertEquals('Tomate Cherry', $item->fresh()->name);
    //     // $this->assertEquals(15, $item->fresh()->min_stock);

    //     // TEST 3: Eliminar Item (sin movimientos)
    //     $response = $this->delete(route('inventory.destroy', $item->id));
    //     $response->assertRedirect(route('inventory.index'));
    //     $this->assertDatabaseMissing('items', ['id' => $item->id]);

    //     echo "\n✓ PR-001: CRUD de Items funciona correctamente (regresión)";
    // }

    /**
     * PR-002: Verificar que Movements sigue funcionando con Solicitudes
     * 
     * Objetivo: Asegurar que movements normales NO se afectan
     * Entrada: Crear ingresos y salidas normales
     * Salida Esperada: Stock se actualiza, movements se registran
     * Regresión: Movements de compra/evento deben seguir funcionando
     */
    public function test_movements_normales_siguen_funcionando_con_solicitudes()
    {
        // ARRANGE
        $user = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::factory()->create(['status' => 'active']);
        $item = Item::factory()->create([
            'name' => 'Arroz Premium',
            'current_stock' => 50,
            'unit' => 'kg'
        ]);
        
        $this->actingAs($user);

        // TEST 1: Crear Ingreso (compra normal)
        $response = $this->post(route('movements.store'), [
            'type' => 'incoming',
            'item_id' => $item->id,
            'quantity' => 30,
            'unit_price' => 4.50,
            'supplier_id' => $supplier->id,
            'reason' => 'purchase',
            'reference' => 'Factura 001'
        ]);

        $response->assertRedirect(route('movements.incoming'));
        $this->assertEquals(80, $item->fresh()->current_stock); // 50 + 30

        // TEST 2: Crear Salida (evento normal)
        $response = $this->post(route('movements.store'), [
            'type' => 'outgoing',
            'item_id' => $item->id,
            'quantity' => 20,
            'reason' => 'event',
            'reference' => 'Evento XYZ'
        ]);

        $response->assertRedirect(route('movements.outgoing'));
        $this->assertEquals(60, $item->fresh()->current_stock); // 80 - 20

        // TEST 3: Ahora aprobar una Solicitud y verificar que NO interfiere
        $solicitud = Solicitud::factory()->create([
            'user_id' => $user->id,
            'estado' => 'pendiente'
        ]);

        SolicitudItem::create([
            'solicitud_id' => $solicitud->id,
            'item_id' => $item->id,
            'cantidad_solicitada' => 10,
            'cantidad_disponible' => 60,
            'stock_suficiente' => true
        ]);

        // Aprobar solicitud
        $response = $this->post(route('solicitudes.aprobar', $solicitud->id));
        $this->assertEquals(50, $item->fresh()->current_stock); // 60 - 10

        // Verificar que hay 3 movements (1 ingreso + 1 salida + 1 de solicitud)
        $this->assertDatabaseCount('movements', 3);
        
        // Verificar tipos de movements
        $this->assertDatabaseHas('movements', [
            'type' => 'incoming',
            'reason' => 'purchase',
            'quantity' => 30
        ]);

        $this->assertDatabaseHas('movements', [
            'type' => 'outgoing',
            'reason' => 'event',
            'quantity' => 20
        ]);

        $this->assertDatabaseHas('movements', [
            'type' => 'outgoing',
            'reason' => 'production', // De la solicitud
            'quantity' => 10
        ]);

        echo "\n✓ PR-002: Movements normales y de solicitudes coexisten correctamente";
    }

    /**
     * PR-003: Verificar integridad del diagrama de Base de Datos
     * 
     * Objetivo: Confirmar que todas las relaciones siguen intactas
     * Entrada: Estructura completa de BD con todas las tablas
     * Salida Esperada: Foreign keys, índices y constraints funcionan
     * Regresión: Diagrama de BD implementado en MySQL sigue válido
     */
    public function test_integridad_relaciones_base_datos_completa()
    {
        // ARRANGE - Crear estructura completa del sistema
        $user = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::factory()->create(['status' => 'active']);
        
        $item = Item::factory()->create([
            'default_supplier_id' => $supplier->id,
            'current_stock' => 100
        ]);

        // Crear Movement
        $movement = Movement::create([
            'type' => 'incoming',
            'item_id' => $item->id,
            'quantity' => 50,
            'supplier_id' => $supplier->id,
            'reason' => 'purchase',
            'user_id' => $user->id,
            'movement_date' => now()
        ]);

        // Crear Solicitud con Items
        $solicitud = Solicitud::factory()->create([
            'user_id' => $user->id,
            'estado' => 'aprobada',
            'aprobado_por' => $user->id
        ]);

        $solicitudItem = SolicitudItem::create([
            'solicitud_id' => $solicitud->id,
            'item_id' => $item->id,
            'cantidad_solicitada' => 10,
            'cantidad_disponible' => 100,
            'stock_suficiente' => true
        ]);

        // ASSERT - Verificar que todas las relaciones funcionan

        // 1. Item → Supplier
        $this->assertNotNull($item->supplier);
        $this->assertEquals($supplier->id, $item->supplier->id);

        // 2. Item → Movements
        $this->assertCount(1, $item->movements);
        $this->assertEquals($movement->id, $item->movements->first()->id);

        // 3. Movement → Item, Supplier, User
        $this->assertEquals($item->id, $movement->item->id);
        $this->assertEquals($supplier->id, $movement->supplier->id);
        $this->assertEquals($user->id, $movement->user->id);

        // 4. Supplier → Items, Movements
        $this->assertCount(1, $supplier->items);
        $this->assertCount(1, $supplier->movements);

        // 5. Solicitud → User, Items
        $this->assertEquals($user->id, $solicitud->user->id);
        $this->assertEquals($user->id, $solicitud->aprobador->id);
        $this->assertCount(1, $solicitud->items);

        // 6. SolicitudItem → Solicitud, Item
        $this->assertEquals($solicitud->id, $solicitudItem->solicitud->id);
        $this->assertEquals($item->id, $solicitudItem->item->id);

        // TEST CRÍTICO: Eliminar Solicitud en cascada
        $solicitudId = $solicitud->id;
        $solicitud->delete();

        // Verificar cascada
        $this->assertDatabaseMissing('solicitudes', ['id' => $solicitudId]);
        $this->assertDatabaseMissing('solicitud_items', ['solicitud_id' => $solicitudId]);

        // Items, Movements, Suppliers NO deben eliminarse
        $this->assertDatabaseHas('items', ['id' => $item->id]);
        $this->assertDatabaseHas('movements', ['id' => $movement->id]);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);

        echo "\n✓ PR-003: Integridad de BD y relaciones funcionan correctamente";
        echo "\n  - Tablas verificadas: users, items, suppliers, movements, solicitudes, solicitud_items";
        echo "\n  - Relaciones verificadas: belongsTo, hasMany, cascadas";
    }
}