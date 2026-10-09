<?php

namespace Tests\Feature;

use App\Http\Controllers\LoginController;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public static function paginasProtegidas(): array
    {
        return [
            'inicio' => ['/'],
            'dashboard' => ['/dashboard'],
            'empleados' => ['/empleados'],
            'nuevo empleado' => ['/empleados/create'],
            'residentes' => ['/residentes'],
        ];
    }

    #[DataProvider('paginasProtegidas')]
    public function test_sin_sesion_redirige_al_login(string $ruta): void
    {
        $this->get($ruta)->assertRedirect(route('login'));
    }

    public function test_sin_sesion_no_se_puede_eliminar_un_empleado(): void
    {
        $this->delete('/empleados/1')->assertRedirect(route('login'));
    }

    public function test_muestra_el_formulario_de_login(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión');
    }

    public function test_inicia_sesion_con_credenciales_correctas(): void
    {
        $usuario = User::factory()->create();

        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard.index'));

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_rechaza_una_contrasena_incorrecta(): void
    {
        $usuario = User::factory()->create();

        $this->from('/login')
            ->post('/login', ['email' => $usuario->email, 'password' => 'incorrecta'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'El correo o la contraseña no son correctos.']);

        $this->assertGuest();
    }

    public function test_exige_correo_y_contrasena(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_bloquea_tras_demasiados_intentos_fallidos(): void
    {
        $usuario = User::factory()->create();

        for ($i = 0; $i < LoginController::MAX_INTENTOS; $i++) {
            $this->post('/login', ['email' => $usuario->email, 'password' => 'incorrecta']);
        }

        // Aun con la contraseña correcta, el acceso queda bloqueado temporalmente.
        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('email'));
    }

    public function test_el_usuario_de_demostracion_puede_iniciar_sesion(): void
    {
        $this->seed();
        $this->seed(); // ejecutarlo dos veces no debe duplicar ni fallar

        $this->post('/login', [
            'email' => DatabaseSeeder::ADMIN_EMAIL,
            'password' => DatabaseSeeder::ADMIN_PASSWORD,
        ])->assertRedirect(route('dashboard.index'));

        $this->assertAuthenticated();
        $this->assertSame(1, User::where('email', DatabaseSeeder::ADMIN_EMAIL)->count());
    }

    public function test_un_usuario_con_sesion_no_ve_el_login(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/login')
            ->assertRedirect();
    }

    public function test_cierra_sesion(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
