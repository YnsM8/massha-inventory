<?php

namespace Database\Factories;

use App\Models\SolicitudItem;
use App\Models\Solicitud;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudItemFactory extends Factory
{
    protected $model = SolicitudItem::class;

    public function definition(): array
    {
        $cantidadSolicitada = fake()->randomFloat(2, 5, 50);
        $stockDisponible = fake()->randomFloat(2, 10, 100);
        
        return [
            'solicitud_id' => Solicitud::factory(),
            'item_id' => Item::factory(),
            'cantidad_solicitada' => $cantidadSolicitada,
            'cantidad_disponible' => $stockDisponible,
            'stock_suficiente' => $stockDisponible >= $cantidadSolicitada,
        ];
    }

    public function stockInsuficiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'cantidad_solicitada' => 100,
            'cantidad_disponible' => 10,
            'stock_suficiente' => false,
        ]);
    }
}