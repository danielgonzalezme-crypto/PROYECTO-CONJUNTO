<?php

namespace App\Http\Controllers;

use App\Services\Residentes\BusquedaResidentes;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResidenteController extends Controller
{
    public function __construct(private readonly BusquedaResidentes $busqueda) {}

    public function index(Request $request): View
    {
        $torre = $request->string('torre')->trim()->toString();
        $buscar = $request->string('buscar')->trim()->toString();

        $filtroTorre = $torre === '' ? null : $torre;
        ['residentes' => $lista, 'error' => $error, 'aviso' => $aviso] = $this->busqueda->buscar($buscar, $filtroTorre);

        return view('residentes.index', compact('lista', 'torre', 'buscar', 'error', 'aviso'));
    }
}
