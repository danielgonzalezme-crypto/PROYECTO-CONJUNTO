<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Residentes\ResidentesApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PantallasResidentesTest extends TestCase
{
    use RefreshDatabase;

    private const ANA = [
        'cedula' => '1000000001',
        'nombre' => 'Ana Torres',
        'torre' => 'A',
        'apartamento' => '101',
        'deudaActual' => 150000,
        'tieneParqueadero' => true,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_la_pantalla_de_residentes_muestra_los_datos_de_la_api(): void
    {
        Http::fake(['*/api/residentes*' => Http::response([self::ANA])]);

        $this->get('/residentes')->assertOk()->assertSee('Ana Torres');
    }

    public function test_el_filtro_por_torre_se_envia_a_la_api(): void
    {
        Http::fake(['*/api/residentes*' => Http::response([])]);

        $this->get('/residentes?torre=B')->assertOk();

        Http::assertSent(fn (Request $peticion) => str_contains($peticion->url(), '/api/residentes')
            && $peticion['torre'] === 'B');
    }

    public function test_sin_filtro_no_se_envia_el_parametro_torre(): void
    {
        Http::fake(['*/api/residentes*' => Http::response([])]);

        $this->get('/residentes')->assertOk();

        Http::assertSent(fn (Request $peticion) => ! str_contains($peticion->url(), 'torre='));
    }

    public function test_la_pantalla_de_residentes_avisa_si_la_api_esta_caida(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection refused'));

        $this->get('/residentes')
            ->assertOk()
            ->assertSee('No se pudo conectar con la API de residentes.');
    }

    public function test_el_dashboard_muestra_el_resumen_por_torre(): void
    {
        Http::fake(['*/api/residentes*' => Http::response([self::ANA])]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Torre A')
            ->assertSee('150.000');
    }

    public function test_el_dashboard_sigue_funcionando_si_la_api_esta_caida(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection refused'));

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('No se pudo conectar con la API de residentes.');
    }

    public function test_el_cliente_consulta_los_morosos_en_su_ruta(): void
    {
        Http::fake(['*/api/residentes/morosos' => Http::response([self::ANA])]);

        $morosos = app(ResidentesApi::class)->morosos();

        $this->assertSame('Ana Torres', $morosos[0]['nombre']);
    }
}
