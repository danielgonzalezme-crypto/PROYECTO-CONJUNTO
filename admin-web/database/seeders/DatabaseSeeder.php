<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'admin@conjuntosmart.test';
    public const ADMIN_PASSWORD = 'Admin12345';

    /**
     * Usuario fijo de demostración. Se crea al arrancar el contenedor si no existe;
     * si ya existe no se toca, así un cambio de contraseña posterior se conserva.
     * Para más usuarios: php artisan app:crear-admin
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => self::ADMIN_EMAIL],
            ['name' => 'Administrador', 'password' => self::ADMIN_PASSWORD],
        );
    }
}
