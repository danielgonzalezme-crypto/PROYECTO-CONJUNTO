<?php

namespace App\Services\Dashboard;

use App\Models\Empleado;
use App\Services\Residentes\ResidentesApi;
use App\Services\Residentes\ResidentesApiNoDisponible;

class ResumenTorresService
{
    public function __construct(private readonly ResidentesApi $residentes)
    {
    }

    /**
     * @return array{torres: array<string, array<string, mixed>>, error: ?string}
     */
    public function generar(): array
    {
        $error = null;

        try {
            $residentes = $this->residentes->listar();
        } catch (ResidentesApiNoDisponible $e) {
            $residentes = [];
            $error = $e->getMessage();
        }

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