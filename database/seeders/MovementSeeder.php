<?php

namespace Database\Seeders;

use App\Models\Movement;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MovementSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $items = Item::all();

        $movements = [];

        // Movimientos de hace 30 días (ingresos iniciales)
        foreach ($items as $item) {
            $movements[] = [
                'type' => 'incoming',
                'item_id' => $item->id,
                'quantity' => $item->current_stock + rand(10, 30),
                'unit_price' => $item->unit_price,
                'supplier_id' => $item->default_supplier_id,
                'reason' => 'purchase',
                'reference' => 'OC-' . rand(1000, 9999),
                'batch_number' => 'L2024' . rand(100, 999),
                'expiry_date' => in_array($item->category, ['Carnes', 'Verduras', 'Lácteos']) ? Carbon::now()->addDays(rand(15, 45)) : null,
                'user_id' => $user->id,
                'movement_date' => Carbon::now()->subDays(30),
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ];
        }

        // Algunos movimientos de salida recientes
        $movements[] = [
            'type' => 'outgoing',
            'item_id' => Item::where('code', 'CAR001')->first()->id,
            'quantity' => 8.0,
            'unit_price' => null,
            'supplier_id' => null,
            'reason' => 'event',
            'reference' => 'Matrimonio González',
            'batch_number' => null,
            'expiry_date' => null,
            'notes' => 'Evento para 100 personas',
            'user_id' => $user->id,
            'movement_date' => Carbon::now()->subDays(5),
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ];

        $movements[] = [
            'type' => 'outgoing',
            'item_id' => Item::where('code', 'VER001')->first()->id,
            'quantity' => 10.0,
            'unit_price' => null,
            'supplier_id' => null,
            'reason' => 'event',
            'reference' => 'Conferencia Empresarial',
            'batch_number' => null,
            'expiry_date' => null,
            'user_id' => $user->id,
            'movement_date' => Carbon::now()->subDays(3),
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(3),
        ];

        $movements[] = [
            'type' => 'outgoing',
            'item_id' => Item::where('code', 'ESP001')->first()->id,
            'quantity' => 1.0,
            'unit_price' => null,
            'supplier_id' => null,
            'reason' => 'production',
            'reference' => 'PROD-001',
            'batch_number' => null,
            'expiry_date' => null,
            'user_id' => $user->id,
            'movement_date' => Carbon::now()->subDays(2),
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ];

        // Insertar movimientos
        foreach ($movements as $movement) {
            Movement::create($movement);
        }
    }
}