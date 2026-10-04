<?php

namespace App\Http\Controllers;

use App\Services\Residentes\ResidentesApi;
use App\Services\Residentes\ResidentesApiNoDisponible;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResidenteController extends Controller
{
    public function __construct(private readonly ResidentesApi $residentes)
    {
    }

    public function index(Request $request): View
    {
        $torre = $request->query('torre');
        $error = null;

        try {
            $lista = $this->residentes->listar($torre);
        } catch (ResidentesApiNoDisponible $e) {
            $lista = [];
            $error = $e->getMessage();
        }

        return view('residentes.index', compact('lista', 'torre', 'error'));
    }
}