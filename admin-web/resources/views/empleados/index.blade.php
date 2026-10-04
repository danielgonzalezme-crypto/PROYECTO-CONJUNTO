@extends('layouts.app')

@section('titulo', 'Empleados')

@section('contenido')
    <h1>Empleados</h1>
    <p><a class="btn" href="{{ route('empleados.create') }}">Nuevo empleado</a></p>
 <div class="tabla-scroll">
    <table>
        <thead>
            <tr>
                <th>Cédula</th><th>Nombre</th><th>Cargo</th><th>Turno</th>
                <th>Torre</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($empleados as $empleado)
                <tr>
                    <td>{{ $empleado->cedula }}</td>
                    <td>{{ $empleado->nombre }}</td>
                    <td>{{ $empleado->cargo }}</td>
                    <td>{{ $empleado->turno }}</td>
                    <td>{{ $empleado->torre_asignada }}</td>
                    <td>{{ $empleado->activo ? 'Activo' : 'Inactivo' }}</td>
                    <td>
                        <a class="btn" href="{{ route('empleados.edit', $empleado) }}">Editar</a>
                        <form action="{{ route('empleados.destroy', $empleado) }}" method="POST"
                              style="display:inline" onsubmit="return confirm('¿Eliminar este empleado?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-rojo" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No hay empleados registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection