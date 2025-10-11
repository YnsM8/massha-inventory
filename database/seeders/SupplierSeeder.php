<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Distribuidora San Juan SAC',
                'ruc' => '20123456789',
                'contact' => 'Juan Pérez',
                'phone' => '064-123456',
                'email' => 'ventas@sanjuan.com',
                'address' => 'Av. Real 123, Huancayo',
                'status' => 'active',
            ],
            [
                'name' => 'Carnes Premium EIRL',
                'ruc' => '20987654321',
                'contact' => 'María García',
                'phone' => '064-654321',
                'email' => 'pedidos@carnespremium.com',
                'address' => 'Jr. Comercio 456, El Tambo',
                'status' => 'active',
            ],
            [
                'name' => 'Verduras Frescas del Valle',
                'ruc' => '20456789123',
                'contact' => 'Carlos López',
                'phone' => '064-789123',
                'email' => 'info@verdurasvalle.com',
                'address' => 'Carretera Central Km 5, Huancayo',
                'status' => 'active',
            ],
            [
                'name' => 'Lácteos La Granja SA',
                'ruc' => '20789123456',
                'contact' => 'Ana Rodríguez',
                'phone' => '064-987654',
                'email' => 'ventas@lacteosgranja.com',
                'address' => 'Av. Ferrocarril 789, Huancayo',
                'status' => 'active',
            ],
            [
                'name' => 'Especias y Condimentos Andinos',
                'ruc' => '20321654987',
                'contact' => 'Pedro Martínez',
                'phone' => '064-456789',
                'email' => 'pedidos@especiasandinas.com',
                'address' => 'Jr. Huancavelica 321, Huancayo',
                'status' => 'active',
            ],
            [
                'name' => 'Bebidas Refrescantes del Centro',
                'ruc' => '20654987321',
                'contact' => 'Luis Fernández',
                'phone' => '064-789456',
                'email' => 'ventas@bebidascentro.com',
                'address' => 'Av. Giráldez 654, Huancayo',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}