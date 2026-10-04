<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'ConjuntoSmart')</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2933; }
        header { background: #1f3a5f; color: #fff; padding: 1rem 2rem; }
        main { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: .6rem .8rem; border-bottom: 1px solid #e4e7eb; text-align: left; }
        .btn { display: inline-block; padding: .4rem .8rem; border: 0; border-radius: 4px; background: #2f6fed; color: #fff; text-decoration: none; cursor: pointer; font-size: .9rem; }
        .btn-rojo { background: #d64545; }
        .ok { background: #dff5e1; padding: .7rem 1rem; border-radius: 4px; margin-bottom: 1rem; }
        label { display: block; margin-top: .8rem; font-weight: 600; }
        input[type=text], select { width: 100%; padding: .5rem; box-sizing: border-box; }
        small.error { color: #d64545; }
        .btn { font-family: inherit; }
        .btn:hover { background: #1f57c9; }
        .btn-rojo:hover { background: #b83232; }
        .btn-grande { padding: .7rem 1.6rem; font-size: 1rem; }
        .acciones { display: flex; gap: 1.2rem; align-items: center; margin-top: 1.5rem; }
        .acciones a { color: #52606d; }
        header nav { margin-top: .5rem; }
        header nav a { color: #cfe0ff; margin-right: 1.2rem; text-decoration: none; }
        header nav a:hover { color: #fff; text-decoration: underline; }
        .aviso-error { background: #fde2e2; color: #8a1f1f; padding: .7rem 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .filtro { display: flex; gap: .6rem; margin-bottom: 1rem; }
        .filtro input { flex: 1; }
        .resumen { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .dato { background: #fff; padding: 1rem 1.4rem; border-radius: 6px; flex: 1; min-width: 150px; }
        .dato span { display: block; font-size: 1.6rem; font-weight: 700; color: #1f3a5f; }
        .torres { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem; }
        .torre { background: #fff; padding: 1rem 1.2rem; border-radius: 6px; border-top: 4px solid #2f6fed; }
        .torre h2 { margin: 0 0 .6rem; }
        .torre h3 { margin: 1rem 0 .4rem; font-size: 1rem; color: #52606d; }
        .torre p { margin: .3rem 0; }
        .alerta { color: #b45309; font-weight: 600; }
        .tabla-scroll { overflow-x: auto; }
    </style>
</head>
<body>
    <header>
        <strong>ConjuntoSmart</strong> · Administración
        <nav>
            <a href="{{ route('dashboard.index') }}">Dashboard</a>
            <a href="{{ route('empleados.index') }}">Empleados</a>
            <a href="{{ route('residentes.index') }}">Residentes</a>
        </nav>
    </header>
    <main>
        @if (session('ok'))
            <div class="ok">{{ session('ok') }}</div>
        @endif
        @yield('contenido')
    </main>
</body>
</html>