<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\ResumenTorresService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly ResumenTorresService $resumen) {}

    public function index(): View
    {
        return view('dashboard.index', $this->resumen->generar());
    }
}
