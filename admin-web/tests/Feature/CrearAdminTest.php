<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CrearAdminTest extends TestCase
{
    use RefreshDatabase;

    private function ejecutar(string $email, string $clave, string $repetida)
    {
        return $this->artisan('app:crear-admin')
            ->expectsQuestion('Nombre', 'Portería')
            ->expectsQuestion('Correo', $email)
            ->expectsQuestion('Contraseña (mínimo 8 caracteres)', $clave)
            ->expectsQuestion('Repite la contraseña', $repetida);
    }

    public function test_crea_un_usuario_con_la_contrasena_cifrada(): void
    {
        $this->ejecutar('porteria@conjuntosmart.test', 'Segura123', 'Segura123')
            ->expectsOutput('Usuario porteria@conjuntosmart.test creado. Ya puede iniciar sesión.')
            ->assertSuccessful();

        $usuario = User::where('email', 'porteria@conjuntosmart.test')->firstOrFail();
        $this->assertNotSame('Segura123', $usuario->password);
        $this->assertTrue(Hash::check('Segura123', $usuario->password));
    }

    public function test_rechaza_contrasenas_que_no_coinciden(): void
    {
        $this->ejecutar('porteria@conjuntosmart.test', 'Segura123', 'Otra12345')
            ->expectsOutput('Las contraseñas no coinciden.')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_rechaza_contrasenas_cortas_y_correos_invalidos(): void
    {
        $this->ejecutar('no-es-correo', 'corta', 'corta')->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_rechaza_un_correo_que_ya_existe(): void
    {
        User::factory()->create(['email' => 'porteria@conjuntosmart.test']);

        $this->ejecutar('porteria@conjuntosmart.test', 'Segura123', 'Segura123')
            ->expectsOutput('Ya existe un usuario con ese correo.')
            ->assertFailed();
    }
}
