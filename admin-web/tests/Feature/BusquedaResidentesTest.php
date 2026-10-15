<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BusquedaResidentesTest extends TestCase
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

    private const LUCERO = [
        'cedula' => '1000000002',
        'nombre' => 'Lucero Pérez',
        'torre' => 'B',
        'apartamento' => '202',
        'deudaActual' => 0,
        'tieneParqueadero' => false,
    ];

    private const CONTACTO_ANA = [
        'cedula' => '1000000001',
        'telefono' => '300 123-4567',
        'correo' => 'ana@example.com',
        'tipoResidente' => 'PROPIETARIO',
    ];

    private const CONTACTO_LUCERO = [
        'cedula' => '1000000002',
        'telefono' => '3109876543',
        'correo' => 'lucero@example.com',
        'tipoResidente' => 'ARRENDATARIO',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    private function fingirApis(): void
    {
        Http::fake([
            '*/api/residentes*' => Http::response([self::ANA, self::LUCERO]),
            '*/api/propietarios*' => Http::response([self::CONTACTO_ANA, self::CONTACTO_LUCERO]),
        ]);
    }

    private function buscar(string $texto): TestResponse
    {
        return $this->get('/residentes?'.http_build_query(['buscar' => $texto]));
    }

    public function test_muestra_telefono_y_correo_unidos_por_cedula(): void
    {
        $this->fingirApis();

        $this->get('/residentes')
            ->assertOk()
            ->assertSee('Ana Torres')
            ->assertSee('300 123-4567')
            ->assertSee('ana@example.com')
            ->assertSee('lucero@example.com');
    }

    public function test_busca_por_nombre_sin_importar_mayusculas_ni_tildes(): void
    {
        $this->fingirApis();

        $this->buscar('PEREZ')
            ->assertOk()
            ->assertSee('Lucero Pérez')
            ->assertDontSee('Ana Torres');
    }

    public function test_busca_por_correo(): void
    {
        $this->fingirApis();

        $this->buscar('lucero@example')
            ->assertOk()
            ->assertSee('Lucero Pérez')
            ->assertDontSee('Ana Torres');
    }

    public function test_busca_por_telefono_ignorando_espacios_y_guiones(): void
    {
        $this->fingirApis();

        $this->buscar('(300) 123-4567')
            ->assertOk()
            ->assertSee('Ana Torres')
            ->assertDontSee('Lucero Pérez');
    }

    public function test_busca_por_cedula(): void
    {
        $this->fingirApis();

        $this->buscar('1000000002')
            ->assertOk()
            ->assertSee('Lucero Pérez')
            ->assertDontSee('Ana Torres');
    }

    public function test_avisa_cuando_nadie_coincide_con_la_busqueda(): void
    {
        $this->fingirApis();

        $this->buscar('999999')
            ->assertOk()
            ->assertSee('No hay residentes para mostrar.');
    }

    public function test_un_residente_sin_ficha_de_contacto_se_muestra_con_guion(): void
    {
        Http::fake([
            '*/api/residentes*' => Http::response([self::ANA]),
            '*/api/propietarios*' => Http::response([]),
        ]);

        $this->get('/residentes')
            ->assertOk()
            ->assertSee('Ana Torres')
            ->assertSee('<td>—</td>', false);
    }

    public function test_si_la_api_de_propietarios_esta_caida_muestra_los_residentes_y_un_aviso(): void
    {
        Http::fake([
            '*/api/residentes*' => Http::response([self::ANA]),
            '*/api/propietarios*' => fn () => throw new ConnectionException('Connection refused'),
        ]);

        $this->get('/residentes')
            ->assertOk()
            ->assertSee('Ana Torres')
            ->assertSee('No se pudo conectar con la API de propietarios.');
    }

    public function test_si_la_api_de_propietarios_responde_con_error_muestra_un_aviso(): void
    {
        Http::fake([
            '*/api/residentes*' => Http::response([self::ANA]),
            '*/api/propietarios*' => Http::response([], 500),
        ]);

        $this->get('/residentes')
            ->assertOk()
            ->assertSee('Ana Torres')
            ->assertSee('La API de propietarios respondió con error 500.');
    }

    public function test_el_filtro_por_torre_se_combina_con_la_busqueda(): void
    {
        $this->fingirApis();

        $this->get('/residentes?'.http_build_query(['buscar' => 'ana', 'torre' => 'A']))->assertOk();

        Http::assertSent(fn (Request $peticion) => str_contains($peticion->url(), '/api/residentes')
            && $peticion['torre'] === 'A');
    }
}
