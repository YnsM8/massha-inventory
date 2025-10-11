<?php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\Item;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovementFactory extends Factory
{
    protected $model = Movement::class;

    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'type' => $this->faker->randomElement(['incoming', 'outgoing']),
            'quantity' => $this->faker->randomFloat(2, 1, 50),
            'unit_price' => $this->faker->randomFloat(2, 5, 100),
            'reason' => $this->faker->randomElement(['purchase', 'event', 'production', 'adjustment']),
            'reference' => $this->faker->optional()->sentence(3),
            'user_id' => User::factory(),
        ];
    }
}