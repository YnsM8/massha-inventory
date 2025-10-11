<?php

namespace Database\Factories;

use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudFactory extends Factory
{
    protected $model = Solicitud::class;

    public function definition(): array
    {
        // Generar código único con timestamp y random
        $uniqueCode = 'SOL-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . '-' . uniqid();
        
        return [
            'codigo_solicitud' => $uniqueCode,
            'evento' => $this->faker->sentence(4),
            'fecha_evento' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'estado' => 'pendiente',
            'observaciones' => $this->faker->optional()->paragraph(),
            'user_id' => User::factory(),
            'aprobado_por' => null,
            'fecha_aprobacion' => null,
        ];
    }

    public function aprobada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'aprobada',
            'aprobado_por' => User::factory(),
            'fecha_aprobacion' => now(),
        ]);
    }

    public function rechazada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'rechazada',
        ]);
    }
}