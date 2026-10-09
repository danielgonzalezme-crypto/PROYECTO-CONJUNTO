<?php

namespace App\Services\Dashboard;

use App\Models\Empleado;
use App\Services\Residentes\ConsultaResidentes;

/**
 * @phpstan-type Torre array{
 *     nombre: string,
 *     residentes: int,
 *     morosos: int,
 *     deuda_total: float,
 *     con_parqueadero: int,
 *     personal: array<string, list<string>>
 * }
 */
class ResumenTorresService
{
    public function __construct(private readonly ConsultaResidentes $consulta) {}

    /**
     * @return array{torres: array<string, Torre>, error: ?string}
     */
    public function generar(): array
    {
        ['residentes' => $residentes, 'error' => $error] = $this->consulta->listar();

        $empleados = Empleado::where('activo', true)->orderBy('nombre')->get();

        $torres = [];

        foreach ($residentes as $residente) {
            $clave = $this->normalizar($residente['torre'] ?? '');
            $torres[$clave] ??= $this->torreVacia($clave);

            $deuda = (float) ($residente['deudaActual'] ?? 0);

            $torres[$clave]['residentes']++;
            $torres[$clave]['deuda_total'] += $deuda;

            if ($deuda > 0) {
                $torres[$clave]['morosos']++;
            }

            if (! empty($residente['tieneParqueadero'])) {
                $torres[$clave]['con_parqueadero']++;
            }
        }

        foreach ($empleados as $empleado) {
            $clave = $this->normalizar($empleado->torre_asignada);
            $torres[$clave] ??= $this->torreVacia($clave);
            $torres[$clave]['personal'][$empleado->cargo][] = $empleado->nombre;
        }

        ksort($torres);

        return ['torres' => $torres, 'error' => $error];
    }

    /**
     * @return Torre
     */
    private function torreVacia(string $nombre): array
    {
        return [
            'nombre' => $nombre,
            'residentes' => 0,
            'morosos' => 0,
            'deuda_total' => 0.0,
            'con_parqueadero' => 0,
            'personal' => [],
        ];
    }

    private function normalizar(string $torre): string
    {
        $limpia = mb_strtoupper(trim($torre));
        $limpia = preg_replace('/^TORRE\s+/u', '', $limpia);

        return $limpia === '' ? 'SIN TORRE' : $limpia;
    }
}
