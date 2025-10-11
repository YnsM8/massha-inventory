<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * PRUEBAS UNITARIAS - ISO 29119
 * Objetivo: Verificar el correcto funcionamiento de métodos individuales
 * Técnica: Caja Blanca (conocemos la implementación interna)
 */
class SolicitudUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PU-001: Verificar método rechazar()
     * 
     * Objetivo: Validar que el método rechazar() cambia el estado correctamente
     * Entrada: Solicitud con estado 'pendiente'
     * Salida Esperada: Estado cambia a 'rechazada'
     * Criterio de Aceptación: Estado debe ser 'rechazada' y persistir en BD
     */
    public function test_metodo_rechazar_cambia_estado_a_rechazada()
    {
        // ARRANGE (Preparar datos)
        $user = User::factory()->create();
        $solicitud = Solicitud::factory()->create([
            'user_id' => $user->id,
            'estado' => 'pendiente'
        ]);

        // Verificación inicial
        $this->assertEquals('pendiente', $solicitud->estado);

        // ACT (Ejecutar acción)
        $solicitud->rechazar();

        // ASSERT (Verificar resultados)
        $this->assertEquals('rechazada', $solicitud->fresh()->estado);
        $this->assertDatabaseHas('solicitudes', [
            'id' => $solicitud->id,
            'estado' => 'rechazada'
        ]);

        // Log para documentación
        echo "\n✓ PU-001: Método rechazar() funciona correctamente";
    }

    /**
     * PU-002: Verificar accessors de estado
     * 
     * Objetivo: Validar que getEstadoLabelAttribute retorna etiquetas correctas
     * Entrada: Solicitudes con diferentes estados
     * Salida Esperada: Etiquetas legibles para UI
     * Técnica: Caja Negra (probamos salidas sin ver implementación)
     */
    public function test_accessor_estado_label_retorna_valores_correctos()
    {
        // Test para cada estado posible
        $estados = [
            'pendiente' => 'Pendiente',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada'
        ];

        foreach ($estados as $estado => $labelEsperado) {
            // ARRANGE
            $solicitud = Solicitud::factory()->create(['estado' => $estado]);

            // ACT & ASSERT
            $this->assertEquals($labelEsperado, $solicitud->estado_label);
            $this->assertIsString($solicitud->estado_label);
            
            echo "\n✓ PU-002.{$estado}: Label '{$labelEsperado}' correcto";
        }
    }

    /**
     * PU-003: Verificar accessor de colores
     * 
     * Objetivo: Validar que getEstadoColorAttribute retorna colores Bootstrap correctos
     * Entrada: Solicitudes con diferentes estados
     * Salida Esperada: Clases CSS de Bootstrap válidas
     * Validación: warning, success, danger según estado
     */
    public function test_accessor_estado_color_retorna_clases_bootstrap_validas()
    {
        // Mapeo estado -> color Bootstrap
        $coloresEsperados = [
            'pendiente' => 'warning',
            'aprobada' => 'success',
            'rechazada' => 'danger'
        ];

        foreach ($coloresEsperados as $estado => $colorEsperado) {
            // ARRANGE
            $solicitud = Solicitud::factory()->create(['estado' => $estado]);

            // ACT
            $colorObtenido = $solicitud->estado_color;

            // ASSERT
            $this->assertEquals($colorEsperado, $colorObtenido);
            $this->assertContains($colorObtenido, ['warning', 'success', 'danger', 'secondary']);
            
            echo "\n✓ PU-003.{$estado}: Color '{$colorEsperado}' correcto para UI";
        }

        // Validación adicional de consistencia
        $solicitudPendiente = Solicitud::factory()->create(['estado' => 'pendiente']);
        $this->assertNotEquals('success', $solicitudPendiente->estado_color);
        $this->assertNotEquals('danger', $solicitudPendiente->estado_color);
    }
}