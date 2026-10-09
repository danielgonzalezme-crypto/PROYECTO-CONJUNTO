<?php

namespace App\Http\Controllers;

use App\Services\Residentes\ConsultaResidentes;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResidenteController extends Controller
{
    public function __construct(private readonly ConsultaResidentes $consulta) {}

    public function index(Request $request): View
    {
        $torre = $request->query('torre');
        ['residentes' => $lista, 'error' => $error] = $this->consulta->listar($torre);

        return view('residentes.index', compact('lista', 'torre', 'error'));
    }
}
