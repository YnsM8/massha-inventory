<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'code' => 'ITEM-' . strtoupper(fake()->bothify('???###')),
            'name' => fake()->word() . ' ' . fake()->word(),
            'category' => fake()->randomElement(['Carnes', 'Verduras', 'Lácteos', 'Especias', 'Bebidas', 'Otros']),
            'current_stock' => fake()->randomFloat(2, 50, 200),
            'min_stock' => fake()->randomFloat(2, 10, 30),
            'unit' => fake()->randomElement(['kg', 'g', 'l', 'ml', 'unid', 'paq']),
            'status' => 'normal',
            'unit_price' => fake()->randomFloat(2, 5, 100),
            'description' => fake()->optional()->sentence(),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_stock' => 5,
            'min_stock' => 20,
            'status' => 'low',
        ]);
    }

    public function withSupplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'default_supplier_id' => Supplier::factory(),
        ]);
    }
}