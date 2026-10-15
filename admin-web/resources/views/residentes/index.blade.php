@extends('layouts.app')

@section('titulo', 'Residentes')

@section('contenido')
    <h1>Residentes</h1>

    @if ($error)
        <div class="aviso-error">{{ $error }} Verifique que el servicio esté encendido.</div>
    @endif

    @if ($aviso)
        <div class="aviso-error">{{ $aviso }} Verifique que el servicio esté encendido.</div>
    @endif

    <form method="GET" action="{{ route('residentes.index') }}" class="filtro">
        <input type="text" name="buscar" placeholder="Buscar por nombre, cédula, teléfono o correo" value="{{ $buscar }}">
        <input type="text" name="torre" placeholder="Filtrar por torre" value="{{ $torre }}">
        <button class="btn" type="submit">Buscar</button>
    </form>
<div class="tabla-scroll">
    <table>
        <thead>
            <tr>
                <th>Cédula</th><th>Nombre</th><th>Torre</th><th>Apto</th>
                <th>Deuda actual</th><th>Parqueadero</th><th>Teléfono</th><th>Correo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lista as $residente)
                <tr>
                    <td>{{ $residente['cedula'] }}</td>
                    <td>{{ $residente['nombre'] }}</td>
                    <td>{{ $residente['torre'] }}</td>
                    <td>{{ $residente['apartamento'] }}</td>
                    <td>${{ number_format($residente['deudaActual'], 0, ',', '.') }}</td>
                    <td>{{ $residente['tieneParqueadero'] ? 'Sí' : 'No' }}</td>
                    <td>{{ $residente['contacto']['telefono'] ?? '—' }}</td>
                    <td>{{ $residente['contacto']['correo'] ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No hay residentes para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
