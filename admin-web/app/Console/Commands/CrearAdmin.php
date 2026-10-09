<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CrearAdmin extends Command
{
    protected $signature = 'app:crear-admin';

    protected $description = 'Crea un usuario que puede iniciar sesión en la administración';

    public function handle(): int
    {
        $datos = [
            'name' => $this->ask('Nombre'),
            'email' => $this->ask('Correo'),
            'password' => $this->secret('Contraseña (mínimo 8 caracteres)'),
        ];
        $datos['password_confirmation'] = $this->secret('Repite la contraseña');

        $validador = Validator::make($datos, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validador->fails()) {
            foreach ($validador->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create($validador->safe()->only(['name', 'email', 'password']));

        $this->info("Usuario {$datos['email']} creado. Ya puede iniciar sesión.");

        return self::SUCCESS;
    }
}
