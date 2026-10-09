@extends('layouts.app')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <div class="caja-login">
        <h1>Iniciar sesión</h1>
        <form action="{{ route('login.store') }}" method="POST">
            @csrf

            <label for="email">Correo</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email') <small class="error">{{ $message }}</small> @enderror

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            @error('password') <small class="error">{{ $message }}</small> @enderror

            <label class="recordar">
                <input type="checkbox" name="recordar" value="1"> Recordarme en este equipo
            </label>

            <div class="acciones">
                <button type="submit" class="btn btn-grande">Entrar</button>
            </div>
        </form>
    </div>
@endsection
