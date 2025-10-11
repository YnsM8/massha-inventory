<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@massha-catering.com',
            'password' => bcrypt('password'),
        ]);

        // Ejecutar seeders en orden
        $this->call([
            SupplierSeeder::class,
            ItemSeeder::class,
            MovementSeeder::class,
        ]);
    }
}