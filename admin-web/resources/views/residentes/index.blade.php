@extends('layouts.app')

@section('titulo', 'Residentes')

@section('contenido')
    <h1>Residentes</h1>

    @if ($error)
        <div class="aviso-error">{{ $error }} Verifique que el servicio esté encendido.</div>
    @endif

    <form method="GET" action="{{ route('residentes.index') }}" class="filtro">
        <input type="text" name="torre" placeholder="Filtrar por torre" value="{{ $torre }}">
        <button class="btn" type="submit">Filtrar</button>
    </form>
<div class="tabla-scroll">
    <table>
        <thead>
            <tr>
                <th>Cédula</th><th>Nombre</th><th>Torre</th><th>Apto</th>
                <th>Deuda actual</th><th>Parqueadero</th>
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
                </tr>
            @empty
                <tr><td colspan="6">No hay residentes para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection