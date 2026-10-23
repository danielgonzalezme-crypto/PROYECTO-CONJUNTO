<?php

namespace App\Http\Controllers;

use App\Services\Propietarios\PropietariosApi;
use App\Services\Propietarios\PropietariosApiNoDisponible;
use App\Services\Residentes\ConsultaResidentes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParqueaderoController extends Controller
{
    public function __construct(
        private readonly ConsultaResidentes $residentes,
        private readonly PropietariosApi $propietarios,
    ) {}

    public function index(Request $request): View
    {
        $cedulaBuscada = trim($request->string('cedula')->toString());
        $resultadoConsulta = null;
        $errorResidentes = null;
        $errorPropietarios = null;

        if ($cedulaBuscada !== '') {
            $resultadoConsulta = $this->obtenerDatosCompletos($cedulaBuscada);
        }

        // Listado general de parqueaderos y vehículos
        ['residentes' => $residentesLista, 'error' => $errorResidentes] = $this->residentes->listar();

        $propietariosLista = [];
        try {
            $propietariosLista = $this->propietarios->listar();
        } catch (PropietariosApiNoDisponible $e) {
            $errorPropietarios = $e->getMessage();
        }

        $contactosPorCedula = [];
        foreach ($propietariosLista as $prop) {
            $c = (string) ($prop['cedula'] ?? '');
            if ($c !== '') {
                $contactosPorCedula[$c] = $prop;
            }
        }

        // Cruzar datos de parqueaderos
        $parqueaderos = [];
        $totalConDerechoParqueadero = 0;
        $totalBahiasAsignadas = 0;
        $totalVehiculosRegistrados = 0;

        foreach ($residentesLista as $res) {
            $ced = (string) ($res['cedula'] ?? '');
            $contacto = $contactosPorCedula[$ced] ?? null;

            $tieneParq = (bool) ($res['tieneParqueadero'] ?? false);
            $numeroBahia = $contacto['numeroParqueadero'] ?? null;
            $placa = $contacto['placaVehiculo'] ?? null;

            if ($tieneParq) {
                $totalConDerechoParqueadero++;
            }
            if (! empty($numeroBahia)) {
                $totalBahiasAsignadas++;
            }
            if (! empty($placa)) {
                $totalVehiculosRegistrados++;
            }

            // Solo mostrar en la tabla si tiene parqueadero en Java o bahía/placa en Node
            if ($tieneParq || ! empty($numeroBahia) || ! empty($placa)) {
                $parqueaderos[] = [
                    'cedula' => $ced,
                    'nombre' => $res['nombre'] ?? '—',
                    'torre' => $res['torre'] ?? '—',
                    'apartamento' => $res['apartamento'] ?? '—',
                    'tieneParqueadero' => $tieneParq,
                    'numeroParqueadero' => $numeroBahia ?: 'Sin asignar',
                    'placaVehiculo' => $placa ?: 'Sin registrar',
                    'telefono' => $contacto['telefono'] ?? '—',
                    'tipoResidente' => $contacto['tipoResidente'] ?? '—',
                ];
            }
        }

        $estadisticas = [
            'cuposAsignados' => $totalConDerechoParqueadero,
            'bahiasRegistradas' => $totalBahiasAsignadas,
            'vehiculosRegistrados' => $totalVehiculosRegistrados,
            'totalRegistros' => count($parqueaderos),
        ];

        return view('parqueaderos.index', compact(
            'cedulaBuscada',
            'resultadoConsulta',
            'parqueaderos',
            'estadisticas',
            'errorResidentes',
            'errorPropietarios'
        ));
    }

    /**
     * Endpoint API JSON para consumo desde JavaScript del Front
     */
    public function consultar(string $cedula): JsonResponse
    {
        $cedula = trim($cedula);
        $datos = $this->obtenerDatosCompletos($cedula);

        return response()->json($datos);
    }

    /**
     * @return array<string, mixed>
     */
    private function obtenerDatosCompletos(string $cedula): array
    {
        $residente = null;
        $propietario = null;
        $errorJava = null;
        $errorNode = null;

        // 1. Consultar en API Java (residentes-api)
        $respJava = $this->residentes->obtener($cedula);
        $residente = $respJava['residente'];
        $errorJava = $respJava['error'];

        // 2. Consultar en API Node.js (propietarios-api)
        try {
            $propietario = $this->propietarios->obtener($cedula);
        } catch (PropietariosApiNoDisponible $e) {
            $errorNode = $e->getMessage();
        }

        return [
            'cedula' => $cedula,
            'encontradoEnJava' => $residente !== null,
            'encontradoEnNode' => $propietario !== null,
            'residente' => $residente,
            'propietario' => $propietario,
            'errorJava' => $errorJava,
            'errorNode' => $errorNode,
        ];
    }
}
