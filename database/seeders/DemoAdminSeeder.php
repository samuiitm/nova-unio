<?php

namespace Database\Seeders;

use App\Enums\RolUsuario;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoAdminSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'nombre' => 'Admin',
                'apellidos' => 'Demo',
                'email' => 'admin@novaunio.local',
                'telefono' => '600111111',
                'rol' => RolUsuario::Admin,
                'activo' => true,
                'password_hash' => 'NovaUnio1234!',
            ],
            [
                'nombre' => 'Entrenador',
                'apellidos' => 'Admin Demo',
                'email' => 'entrenadoradmin@novaunio.local',
                'telefono' => '600222222',
                'rol' => RolUsuario::EntrenadorAdmin,
                'activo' => true,
                'password_hash' => 'NovaUnio1234!',
            ],
            [
                'nombre' => 'Entrenador',
                'apellidos' => 'Demo',
                'email' => 'entrenador@novaunio.local',
                'telefono' => '600333333',
                'rol' => RolUsuario::Entrenador,
                'activo' => true,
                'password_hash' => 'NovaUnio1234!',
            ],
        ];

        foreach ($usuarios as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}