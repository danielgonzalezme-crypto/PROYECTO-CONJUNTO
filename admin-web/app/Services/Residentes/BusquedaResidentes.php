<?php

namespace App\Services\Residentes;

use App\Services\Propietarios\PropietariosApi;
use App\Services\Propietarios\PropietariosApiNoDisponible;
use Illuminate\Support\Str;

/**
 * Une cada residente (API de residentes) con sus datos de contacto (API de propietarios)
 * usando la cédula, y permite buscar por nombre, cédula, teléfono o correo.
 * Si la API de propietarios no responde, devuelve igual los residentes, sin contacto.
 */
class BusquedaResidentes
{
    public function __construct(
        private readonly ConsultaResidentes $residentes,
        private readonly PropietariosApi $propietarios,
    ) {}

    /**
     * @return array{residentes: array<int, array<string, mixed>>, error: ?string, aviso: ?string}
     */
    public function buscar(?string $texto = null, ?string $torre = null): array
    {
        ['residentes' => $lista, 'error' => $error] = $this->residentes->listar($torre);

        if ($error !== null) {
            return ['residentes' => [], 'error' => $error, 'aviso' => null];
        }

        [$contactos, $aviso] = $this->contactosPorCedula();

        $unidos = [];

        foreach ($lista as $residente) {
            $residente['contacto'] = $contactos[(string) ($residente['cedula'] ?? '')] ?? null;
            $unidos[] = $residente;
        }

        $texto = trim((string) $texto);

        if ($texto !== '') {
            $unidos = array_values(array_filter(
                $unidos,
                fn (array $residente): bool => $this->coincide($residente, $texto),
            ));
        }

        return ['residentes' => $unidos, 'error' => null, 'aviso' => $aviso];
    }

    /**
     * @return array{0: array<string, array<string, mixed>>, 1: ?string}
     */
    private function contactosPorCedula(): array
    {
        try {
            $propietarios = $this->propietarios->listar();
        } catch (PropietariosApiNoDisponible $e) {
            return [[], $e->getMessage().' Por eso no se muestran teléfono ni correo.'];
        }

        $porCedula = [];

        foreach ($propietarios as $propietario) {
            $porCedula[(string) ($propietario['cedula'] ?? '')] = $propietario;
        }

        return [$porCedula, null];
    }

    /**
     * @param  array<string, mixed>  $residente
     */
    private function coincide(array $residente, string $texto): bool
    {
        $contacto = $residente['contacto'] ?? [];
        $buscado = $this->simplificar($texto);

        $campos = [$residente['nombre'] ?? '', $residente['cedula'] ?? '', $contacto['correo'] ?? ''];

        foreach ($campos as $campo) {
            if (str_contains($this->simplificar((string) $campo), $buscado)) {
                return true;
            }
        }

        $digitos = $this->soloDigitos($texto);

        return $digitos !== ''
            && str_contains($this->soloDigitos((string) ($contacto['telefono'] ?? '')), $digitos);
    }

    private function simplificar(string $texto): string
    {
        return Str::lower(Str::ascii($texto));
    }

    private function soloDigitos(string $texto): string
    {
        return (string) preg_replace('/\D+/', '', $texto);
    }
}
