<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Services\Dashboard\ResumenTorresService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ResumenTorresServiceTest extends TestCase
{
    use RefreshDatabase;

    private function residente(string $torre, float $deuda = 0, bool $parqueadero = false): array
    {
        static $cedula = 1;

        return [
            'cedula' => (string) $cedula++,
            'nombre' => "Residente {$cedula}",
            'torre' => $torre,
            'apartamento' => '101',
            'deudaActual' => $deuda,
            'tieneParqueadero' => $parqueadero,
        ];
    }

    private function generar(): array
    {
        return app(ResumenTorresService::class)->generar();
    }

    public function test_agrupa_residentes_por_torre_sin_importar_mayusculas_ni_la_palabra_torre(): void
    {
        Http::fake(['*/api/residentes*' => Http::response([
            $this->residente('A'),
            $this->residente('a'),
            $this->residente(' Torre A '),
            $this->residente('B'),
        ])]);

        $resumen = $this->generar();

        $this->assertNull($resumen['error']);
        $this->assertSame(['A', 'B'], array_keys($resumen['torres']));
        $this->assertSame(3, $resumen['torres']['A']['residentes']);
        $this->assertSame(1, $resumen['torres']['B']['residentes']);
    }

    public function test_calcula_morosos_deuda_y_parqueaderos(): void
    {
        Http::fake(['*/api/residentes*' => Http::response([
            $this->residente('A', 150000, true),
            $this->residente('A', 0, true),
            $this->residente('A', 50000.50, false),
        ])]);

        $torre = $this->generar()['torres']['A'];

        $this->assertSame(2, $torre['morosos']);
        $this->assertEqualsWithDelta(200000.50, $torre['deuda_total'], 0.001);
        $this->assertSame(2, $torre['con_parqueadero']);
    }

    public function test_un_residente_sin_torre_queda_en_sin_torre(): void
    {
        Http::fake(['*/api/residentes*' => Http::response([$this->residente('   ')])]);

        $this->assertArrayHasKey('SIN TORRE', $this->generar()['torres']);
    }

    public function test_asigna_solo_el_personal_activo_agrupado_por_cargo(): void
    {
        Http::fake(['*/api/residentes*' => Http::response([])]);
        Empleado::factory()->create(['nombre' => 'Ana', 'cargo' => 'Vigilante', 'torre_asignada' => 'b']);
        Empleado::factory()->create(['nombre' => 'Beto', 'cargo' => 'Vigilante', 'torre_asignada' => 'B']);
        Empleado::factory()->create(['nombre' => 'Caro', 'cargo' => 'Aseador', 'torre_asignada' => 'B']);
        Empleado::factory()->inactivo()->create(['nombre' => 'Inactivo', 'torre_asignada' => 'B']);

        $personal = $this->generar()['torres']['B']['personal'];

        $this->assertSame(['Ana', 'Beto'], $personal['Vigilante']);
        $this->assertSame(['Caro'], $personal['Aseador']);
        $this->assertNotContains('Inactivo', array_merge(...array_values($personal)));
    }

    public function test_si_la_api_no_responde_muestra_el_aviso_y_conserva_el_personal(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection refused'));
        Empleado::factory()->create(['nombre' => 'Ana', 'torre_asignada' => 'A']);

        $resumen = $this->generar();

        $this->assertSame('No se pudo conectar con la API de residentes.', $resumen['error']);
        $this->assertSame(0, $resumen['torres']['A']['residentes']);
        $this->assertNotEmpty($resumen['torres']['A']['personal']);
    }

    public function test_si_la_api_responde_con_error_muestra_el_codigo(): void
    {
        Http::fake(['*/api/residentes*' => Http::response(null, 500)]);

        $this->assertSame('La API de residentes respondió con error 500.', $this->generar()['error']);
    }
}
