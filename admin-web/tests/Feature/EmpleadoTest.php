<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpleadoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    private function datosValidos(array $cambios = []): array
    {
        return [
            'cedula' => '1100200300',
            'nombre' => 'Carlos Pérez',
            'cargo' => 'Vigilante',
            'turno' => 'Noche',
            'torre_asignada' => 'A',
            'activo' => '1',
            ...$cambios,
        ];
    }

    public function test_lista_los_empleados(): void
    {
        Empleado::factory()->create(['nombre' => 'Laura Gómez']);
        Empleado::factory()->create(['nombre' => 'Andrés Ruiz']);

        $this->get('/empleados')
            ->assertOk()
            ->assertSee('Laura Gómez')
            ->assertSee('Andrés Ruiz');
    }

    public function test_muestra_el_formulario_de_creacion(): void
    {
        $this->get('/empleados/create')->assertOk();
    }

    public function test_crea_un_empleado_con_datos_validos(): void
    {
        $this->post('/empleados', $this->datosValidos())
            ->assertRedirect(route('empleados.index'))
            ->assertSessionHas('ok', 'Empleado creado.');

        $this->assertDatabaseHas('empleados', [
            'cedula' => '1100200300',
            'nombre' => 'Carlos Pérez',
            'cargo' => 'Vigilante',
            'activo' => true,
        ]);
    }

    public function test_un_checkbox_activo_sin_marcar_guarda_inactivo(): void
    {
        $datos = $this->datosValidos();
        unset($datos['activo']);

        $this->post('/empleados', $datos)->assertRedirect(route('empleados.index'));

        $this->assertDatabaseHas('empleados', ['cedula' => '1100200300', 'activo' => false]);
    }

    public function test_rechaza_una_cedula_duplicada(): void
    {
        Empleado::factory()->create(['cedula' => '1100200300']);

        $this->post('/empleados', $this->datosValidos())
            ->assertSessionHasErrors(['cedula' => 'Ya existe un empleado con esa cédula.']);

        $this->assertDatabaseCount('empleados', 1);
    }

    public function test_exige_los_campos_obligatorios(): void
    {
        $this->post('/empleados', [])
            ->assertSessionHasErrors(['cedula', 'nombre', 'cargo', 'turno', 'torre_asignada']);

        $this->assertDatabaseCount('empleados', 0);
    }

    public function test_los_mensajes_de_error_estan_en_espanol(): void
    {
        $this->post('/empleados', $this->datosValidos(['nombre' => '']))
            ->assertSessionHasErrors(['nombre' => 'El campo nombre es obligatorio.']);
    }

    public function test_rechaza_un_cargo_o_turno_que_no_existe(): void
    {
        $this->post('/empleados', $this->datosValidos(['cargo' => 'Gerente', 'turno' => 'Madrugada']))
            ->assertSessionHasErrors(['cargo', 'turno']);

        $this->assertDatabaseCount('empleados', 0);
    }

    public function test_rechaza_textos_demasiado_largos(): void
    {
        $this->post('/empleados', $this->datosValidos([
            'cedula' => str_repeat('1', 21),
            'torre_asignada' => str_repeat('A', 11),
        ]))->assertSessionHasErrors(['cedula', 'torre_asignada']);
    }

    public function test_muestra_el_formulario_de_edicion(): void
    {
        $empleado = Empleado::factory()->create(['nombre' => 'Laura Gómez']);

        $this->get("/empleados/{$empleado->id}/edit")
            ->assertOk()
            ->assertSee('Laura Gómez');
    }

    public function test_editar_un_empleado_que_no_existe_responde_404(): void
    {
        $this->get('/empleados/999/edit')->assertNotFound();
    }

    public function test_actualiza_un_empleado_conservando_su_cedula(): void
    {
        $empleado = Empleado::factory()->create(['cedula' => '1100200300', 'turno' => 'Mañana']);

        $this->put("/empleados/{$empleado->id}", $this->datosValidos(['turno' => 'Tarde']))
            ->assertRedirect(route('empleados.index'))
            ->assertSessionHas('ok', 'Empleado actualizado.');

        $this->assertSame('Tarde', $empleado->fresh()->turno);
    }

    public function test_no_permite_cambiar_a_la_cedula_de_otro_empleado(): void
    {
        Empleado::factory()->create(['cedula' => '999']);
        $empleado = Empleado::factory()->create(['cedula' => '1100200300']);

        $this->put("/empleados/{$empleado->id}", $this->datosValidos(['cedula' => '999']))
            ->assertSessionHasErrors('cedula');

        $this->assertSame('1100200300', $empleado->fresh()->cedula);
    }

    public function test_elimina_un_empleado(): void
    {
        $empleado = Empleado::factory()->create();

        $this->delete("/empleados/{$empleado->id}")
            ->assertRedirect(route('empleados.index'))
            ->assertSessionHas('ok', 'Empleado eliminado.');

        $this->assertModelMissing($empleado);
    }

    public function test_escapa_el_html_escrito_por_el_usuario(): void
    {
        // Prueba de seguridad: un nombre con código no debe ejecutarse en el navegador (XSS).
        $this->post('/empleados', $this->datosValidos(['nombre' => '<script>alert(1)</script>']));

        $this->get('/empleados')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }
}
