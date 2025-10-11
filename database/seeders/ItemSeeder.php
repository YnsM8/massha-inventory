<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener proveedores
        $carnesSupplier = Supplier::where('name', 'Carnes Premium EIRL')->first();
        $verdurasSupplier = Supplier::where('name', 'Verduras Frescas del Valle')->first();
        $lacteosSupplier = Supplier::where('name', 'Lácteos La Granja SA')->first();
        $especiasSupplier = Supplier::where('name', 'Especias y Condimentos Andinos')->first();
        $bebidasSupplier = Supplier::where('name', 'Bebidas Refrescantes del Centro')->first();
        $generalSupplier = Supplier::where('name', 'Distribuidora San Juan SAC')->first();

        $items = [
            // Carnes
            [
                'code' => 'CAR001',
                'name' => 'Pollo Entero',
                'category' => 'Carnes',
                'current_stock' => 25.50,
                'min_stock' => 10.00,
                'unit' => 'kg',
                'unit_price' => 8.50,
                'default_supplier_id' => $carnesSupplier->id,
                'description' => 'Pollo fresco de granja',
            ],
            [
                'code' => 'CAR002',
                'name' => 'Carne de Res (Lomo)',
                'category' => 'Carnes',
                'current_stock' => 12.00,
                'min_stock' => 8.00,
                'unit' => 'kg',
                'unit_price' => 25.00,
                'default_supplier_id' => $carnesSupplier->id,
                'description' => 'Lomo de res premium',
            ],
            [
                'code' => 'CAR003',
                'name' => 'Cerdo (Pierna)',
                'category' => 'Carnes',
                'current_stock' => 8.50,
                'min_stock' => 5.00,
                'unit' => 'kg',
                'unit_price' => 15.00,
                'default_supplier_id' => $carnesSupplier->id,
                'description' => 'Pierna de cerdo fresca',
            ],
            // Verduras
            [
                'code' => 'VER001',
                'name' => 'Tomate',
                'category' => 'Verduras',
                'current_stock' => 5.00,
                'min_stock' => 15.00,
                'unit' => 'kg',
                'unit_price' => 3.50,
                'default_supplier_id' => $verdurasSupplier->id,
                'description' => 'Tomate fresco de temporada',
            ],
            [
                'code' => 'VER002',
                'name' => 'Cebolla Roja',
                'category' => 'Verduras',
                'current_stock' => 18.00,
                'min_stock' => 10.00,
                'unit' => 'kg',
                'unit_price' => 2.80,
                'default_supplier_id' => $verdurasSupplier->id,
                'description' => 'Cebolla roja nacional',
            ],
            [
                'code' => 'VER003',
                'name' => 'Papa Amarilla',
                'category' => 'Verduras',
                'current_stock' => 30.00,
                'min_stock' => 20.00,
                'unit' => 'kg',
                'unit_price' => 2.20,
                'default_supplier_id' => $verdurasSupplier->id,
                'description' => 'Papa amarilla de primera',
            ],
            [
                'code' => 'VER004',
                'name' => 'Lechuga',
                'category' => 'Verduras',
                'current_stock' => 12.00,
                'min_stock' => 8.00,
                'unit' => 'unid',
                'unit_price' => 1.50,
                'default_supplier_id' => $verdurasSupplier->id,
                'description' => 'Lechuga criolla fresca',
            ],
            // Lácteos
            [
                'code' => 'LAC001',
                'name' => 'Leche Entera',
                'category' => 'Lácteos',
                'current_stock' => 15.00,
                'min_stock' => 8.00,
                'unit' => 'l',
                'unit_price' => 4.20,
                'default_supplier_id' => $lacteosSupplier->id,
                'description' => 'Leche entera pasteurizada',
            ],
            [
                'code' => 'LAC002',
                'name' => 'Queso Fresco',
                'category' => 'Lácteos',
                'current_stock' => 6.50,
                'min_stock' => 5.00,
                'unit' => 'kg',
                'unit_price' => 18.00,
                'default_supplier_id' => $lacteosSupplier->id,
                'description' => 'Queso fresco artesanal',
            ],
            [
                'code' => 'LAC003',
                'name' => 'Mantequilla',
                'category' => 'Lácteos',
                'current_stock' => 3.00,
                'min_stock' => 2.50,
                'unit' => 'kg',
                'unit_price' => 22.00,
                'default_supplier_id' => $lacteosSupplier->id,
                'description' => 'Mantequilla sin sal',
            ],
            // Especias
            [
                'code' => 'ESP001',
                'name' => 'Sal de Mesa',
                'category' => 'Especias',
                'current_stock' => 2.00,
                'min_stock' => 5.00,
                'unit' => 'kg',
                'unit_price' => 1.80,
                'default_supplier_id' => $especiasSupplier->id,
                'description' => 'Sal yodada refinada',
            ],
            [
                'code' => 'ESP002',
                'name' => 'Pimienta Negra',
                'category' => 'Especias',
                'current_stock' => 1.50,
                'min_stock' => 1.00,
                'unit' => 'kg',
                'unit_price' => 45.00,
                'default_supplier_id' => $especiasSupplier->id,
                'description' => 'Pimienta negra molida',
            ],
            [
                'code' => 'ESP003',
                'name' => 'Comino Molido',
                'category' => 'Especias',
                'current_stock' => 0.80,
                'min_stock' => 0.50,
                'unit' => 'kg',
                'unit_price' => 35.00,
                'default_supplier_id' => $especiasSupplier->id,
                'description' => 'Comino molido nacional',
            ],
            // Bebidas
            [
                'code' => 'BEB001',
                'name' => 'Agua Mineral',
                'category' => 'Bebidas',
                'current_stock' => 48.00,
                'min_stock' => 20.00,
                'unit' => 'l',
                'unit_price' => 1.50,
                'default_supplier_id' => $bebidasSupplier->id,
                'description' => 'Agua mineral sin gas',
            ],
            [
                'code' => 'BEB002',
                'name' => 'Jugo de Naranja',
                'category' => 'Bebidas',
                'current_stock' => 25.00,
                'min_stock' => 15.00,
                'unit' => 'l',
                'unit_price' => 8.50,
                'default_supplier_id' => $bebidasSupplier->id,
                'description' => 'Jugo natural 100%',
            ],
            // Otros
            [
                'code' => 'OTR001',
                'name' => 'Arroz Blanco',
                'category' => 'Otros',
                'current_stock' => 45.00,
                'min_stock' => 25.00,
                'unit' => 'kg',
                'unit_price' => 3.20,
                'default_supplier_id' => $generalSupplier->id,
                'description' => 'Arroz extra superior',
            ],
            [
                'code' => 'OTR002',
                'name' => 'Aceite Vegetal',
                'category' => 'Otros',
                'current_stock' => 8.00,
                'min_stock' => 5.00,
                'unit' => 'l',
                'unit_price' => 12.50,
                'default_supplier_id' => $generalSupplier->id,
                'description' => 'Aceite vegetal premium',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}