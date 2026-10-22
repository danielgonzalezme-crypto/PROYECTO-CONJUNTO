<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ParqueaderosTest extends TestCase
{
    use RefreshDatabase;

    private const RESIDENTE_DEMO = [
        'id' => 1,
        'cedula' => '1000000001',
        'nombre' => 'Ana Torres',
        'torre' => 'A',
        'apartamento' => '101',
        'deudaActual' => 150000,
        'tieneParqueadero' => true,
    ];

    private const PROPIETARIO_DEMO = [
        'id' => 1,
        'cedula' => '1000000001',
        'telefono' => '3000000001',
        'correo' => 'ana@example.com',
        'tipoResidente' => 'PROPIETARIO',
        'placaVehiculo' => 'ABC123',
        'numeroParqueadero' => 'P-01',
        'tieneMascotas' => true,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_la_pantalla_de_parqueaderos_carga_correctamente(): void
    {
        Http::fake([
            '*/api/residentes' => Http::response([self::RESIDENTE_DEMO]),
            '*/api/propietarios' => Http::response([self::PROPIETARIO_DEMO]),
        ]);

        $this->get('/parqueaderos')
            ->assertOk()
            ->assertSee('Control de Parqueaderos')
            ->assertSee('P-01')
            ->assertSee('ABC123');
    }

    public function test_el_endpoint_api_retorna_informacion_cruzada_por_cedula(): void
    {
        Http::fake([
            '*/api/residentes/1000000001' => Http::response(self::RESIDENTE_DEMO),
            '*/api/propietarios/1000000001' => Http::response(self::PROPIETARIO_DEMO),
        ]);

        $respuesta = $this->getJson('/parqueaderos/api/1000000001');

        $respuesta->assertOk()
            ->assertJsonPath('encontradoEnJava', true)
            ->assertJsonPath('encontradoEnNode', true)
            ->assertJsonPath('residente.tieneParqueadero', true)
            ->assertJsonPath('propietario.numeroParqueadero', 'P-01')
            ->assertJsonPath('propietario.placaVehiculo', 'ABC123');
    }

    public function test_el_endpoint_api_maneja_cedula_no_encontrada(): void
    {
        Http::fake([
            '*/api/residentes/9999999999' => Http::response(null, 404),
            '*/api/propietarios/9999999999' => Http::response(null, 404),
        ]);

        $respuesta = $this->getJson('/parqueaderos/api/9999999999');

        $respuesta->assertOk()
            ->assertJsonPath('encontradoEnJava', false)
            ->assertJsonPath('encontradoEnNode', false);
    }
}
