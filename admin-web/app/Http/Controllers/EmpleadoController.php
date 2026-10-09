<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmpleadoRequest;
use App\Models\Empleado;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmpleadoController extends Controller
{
    public function index(): View
    {
        return view('empleados.index', [
            'empleados' => Empleado::orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('empleados.create', ['empleado' => new Empleado]);
    }

    public function store(EmpleadoRequest $request): RedirectResponse
    {
        Empleado::create($this->datos($request));

        return redirect()->route('empleados.index')->with('ok', 'Empleado creado.');
    }

    public function edit(Empleado $empleado): View
    {
        return view('empleados.edit', ['empleado' => $empleado]);
    }

    public function update(EmpleadoRequest $request, Empleado $empleado): RedirectResponse
    {
        $empleado->update($this->datos($request));

        return redirect()->route('empleados.index')->with('ok', 'Empleado actualizado.');
    }

    public function destroy(Empleado $empleado): RedirectResponse
    {
        $empleado->delete();

        return redirect()->route('empleados.index')->with('ok', 'Empleado eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datos(EmpleadoRequest $request): array
    {
        // Un checkbox sin marcar no se envía, por eso se normaliza aquí.
        return [...$request->validated(), 'activo' => $request->boolean('activo')];
    }
}
