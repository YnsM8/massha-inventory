<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario Admin
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Crear usuario Producción
        User::firstOrCreate(
            ['email' => 'produccion@test.com'],
            [
                'name' => 'Producción Test',
                'password' => Hash::make('password123'),
                'role' => 'produccion',
            ]
        );

        // Crear usuario Ventas
        User::firstOrCreate(
            ['email' => 'ventas@test.com'],
            [
                'name' => 'Ventas Test',
                'password' => Hash::make('password123'),
                'role' => 'ventas',
            ]
        );

        // Crear usuario Gerencia
        User::firstOrCreate(
            ['email' => 'gerencia@test.com'],
            [
                'name' => 'Gerencia Test',
                'password' => Hash::make('password123'),
                'role' => 'gerencia',
            ]
        );

        echo "✅ Usuarios de prueba creados exitosamente\n";
    }
}