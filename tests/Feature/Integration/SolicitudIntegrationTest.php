<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;
use App\Models\Solicitud;
use App\Models\SolicitudItem;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * PRUEBAS DE INTEGRACIÓN - ISO 29119
 * Objetivo: Verificar integración entre modelos y base de datos
 * Técnica: Caja Gris (conocemos estructura pero probamos integración)
 */
class SolicitudIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PI-001: Integración Solicitud con Items
     * 
     * Objetivo: Verificar que la relación hasMany con SolicitudItem funciona
     * Entrada: Solicitud con 3 items asociados
     * Salida Esperada: Relación items() retorna 3 registros
     * Criterio: Integridad referencial y cascada en BD
     */
    public function test_solicitud_puede_tener_multiples_items_relacionados()
    {
        // ARRANGE - Crear solicitud con items
        $user = User::factory()->create();
        $solicitud = Solicitud::factory()->create(['user_id' => $user->id]);
        
        $items = Item::factory()->count(3)->create();
        
        foreach ($items as $item) {
            SolicitudItem::factory()->create([
                'solicitud_id' => $solicitud->id,
                'item_id' => $item->id,
            ]);
        }

        // ACT - Cargar relación desde la base de datos
        $solicitudConItems = Solicitud::with('items')->find($solicitud->id);

        // ASSERT - Verificar integridad de la relación
        $this->assertCount(3, $solicitudConItems->items);
        $this->assertInstanceOf(SolicitudItem::class, $solicitudConItems->items->first());
        
        // Verificar que cada SolicitudItem tiene su Item relacionado
        foreach ($solicitudConItems->items as $solicitudItem) {
            $this->assertNotNull($solicitudItem->item);
            $this->assertInstanceOf(Item::class, $solicitudItem->item);
        }

        // Verificar en base de datos
        $this->assertDatabaseCount('solicitud_items', 3);
        
        echo "\n✓ PI-001: Relación hasMany Solicitud->Items funciona correctamente";
    }

    /**
     * PI-002: Integridad referencial con usuarios
     * 
     * Objetivo: Verificar relaciones belongsTo con User (solicitante y aprobador)
     * Entrada: Solicitud con user_id y aprobado_por
     * Salida Esperada: Ambas relaciones se cargan correctamente
     * Validación: Foreign keys y onDelete funcionan
     */
    public function test_solicitud_mantiene_integridad_referencial_con_usuarios()
    {
        // ARRANGE - Crear usuarios y solicitud
        $solicitante = User::factory()->create(['name' => 'Juan Solicitante']);
        $aprobador = User::factory()->create(['name' => 'María Aprobadora']);
        
        $solicitud = Solicitud::factory()->create([
            'user_id' => $solicitante->id,
            'estado' => 'aprobada',
            'aprobado_por' => $aprobador->id,
            'fecha_aprobacion' => now(),
        ]);

        // ACT - Cargar relaciones
        $solicitudCargada = Solicitud::with(['user', 'aprobador'])->find($solicitud->id);

        // ASSERT - Verificar relaciones
        $this->assertEquals('Juan Solicitante', $solicitudCargada->user->name);
        $this->assertEquals('María Aprobadora', $solicitudCargada->aprobador->name);
        $this->assertEquals($solicitante->id, $solicitudCargada->user_id);
        $this->assertEquals($aprobador->id, $solicitudCargada->aprobado_por);

        // Verificar integridad referencial en BD
        $this->assertDatabaseHas('solicitudes', [
            'id' => $solicitud->id,
            'user_id' => $solicitante->id,
            'aprobado_por' => $aprobador->id,
        ]);

        echo "\n✓ PI-002: Integridad referencial con usuarios correcta";
    }

    /**
     * PI-003: Cascada de eliminación y constraints
     * 
     * Objetivo: Verificar que onDelete cascade funciona correctamente
     * Entrada: Solicitud con items, luego eliminar solicitud
     * Salida Esperada: Los SolicitudItems se eliminan automáticamente
     * Técnica: Prueba de migración y constraints de BD
     */
    public function test_eliminacion_en_cascada_funciona_correctamente()
    {
        // ARRANGE - Crear estructura completa
        $user = User::factory()->create();
        $solicitud = Solicitud::factory()->create(['user_id' => $user->id]);
        
        // Crear 5 items asociados
        $items = Item::factory()->count(5)->create();
        foreach ($items as $item) {
            SolicitudItem::factory()->create([
                'solicitud_id' => $solicitud->id,
                'item_id' => $item->id,
            ]);
        }

        // Verificar que existen 5 registros
        $this->assertDatabaseCount('solicitud_items', 5);
        $this->assertDatabaseHas('solicitudes', ['id' => $solicitud->id]);

        // ACT - Eliminar la solicitud
        $solicitudId = $solicitud->id;
        $solicitud->delete();

        // ASSERT - Verificar cascada
        $this->assertDatabaseMissing('solicitudes', ['id' => $solicitudId]);
        $this->assertDatabaseCount('solicitud_items', 0); // Todos eliminados en cascada
        
        // Los Items NO deben eliminarse (solo SolicitudItems)
        $this->assertDatabaseCount('items', 5);

        echo "\n✓ PI-003: Eliminación en cascada (onDelete cascade) funciona";
    }
}