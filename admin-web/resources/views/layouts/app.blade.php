<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'ConjuntoSmart') · Conjunto Residencial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --color-primary: #1e3a8a;
            --color-primary-hover: #1d4ed8;
            --color-primary-light: #eff6ff;
            --color-primary-border: #bfdbfe;
            --color-slate-900: #0f172a;
            --color-slate-800: #1e293b;
            --color-slate-700: #334155;
            --color-slate-600: #475569;
            --color-slate-500: #64748b;
            --color-slate-400: #94a3b8;
            --color-slate-300: #cbd5e1;
            --color-slate-200: #e2e8f0;
            --color-slate-100: #f1f5f9;
            --color-slate-50: #f8fafc;
            --color-emerald-500: #10b981;
            --color-emerald-600: #059669;
            --color-emerald-50: #ecfdf5;
            --color-emerald-200: #a7f3d0;
            --color-amber-500: #f59e0b;
            --color-amber-600: #d97706;
            --color-amber-50: #fffbeb;
            --color-amber-200: #fde68a;
            --color-rose-500: #ef4444;
            --color-rose-600: #dc2626;
            --color-rose-50: #fef2f2;
            --color-rose-200: #fecaca;
            --color-purple-500: #8b5cf6;
            --color-purple-600: #7c3aed;
            --color-purple-50: #f5f3ff;
            --color-purple-200: #ddd6fe;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.04);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.06);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            margin: 0;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Header Navigation */
        header.top-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            padding: 0 2rem;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.25);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 68px;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #fff;
        }

        .brand-logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.35);
        }

        .brand-logo-icon svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: #fff;
            stroke-width: 2;
        }

        .brand-name {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .brand-badge {
            font-size: 0.72rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.12);
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            color: #93c5fd;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        nav.main-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        nav.main-nav a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.95rem;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }

        nav.main-nav a svg {
            width: 17px;
            height: 17px;
            stroke-width: 2;
        }

        nav.main-nav a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
            text-decoration: none;
        }

        nav.main-nav a.active {
            color: #fff;
            background: rgba(59, 130, 246, 0.22);
            border: 1px solid rgba(147, 197, 253, 0.3);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .user-nav-section {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-left: auto;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(255, 255, 255, 0.07);
            padding: 0.35rem 0.75rem 0.35rem 0.45rem;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.82rem;
            color: #e2e8f0;
        }

        .user-avatar-mini {
            width: 26px;
            height: 26px;
            border-radius: 9999px;
            background: #2563eb;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .salir button {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #cbd5e1;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 500;
            padding: 0.4rem 0.8rem;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.15s ease-in-out;
        }

        .salir button:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.4);
        }

        /* Main Container */
        main {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Flash Notifications */
        .ok {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
        }

        .ok::before {
            content: "";
            display: inline-block;
            width: 18px;
            height: 18px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
            background-size: contain;
            flex-shrink: 0;
        }

        .aviso-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
        }

        .aviso-error::before {
            content: "";
            display: inline-block;
            width: 18px;
            height: 18px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23dc2626' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'%3E%3C/circle%3E%3Cline x1='12' y1='8' x2='12' y2='12'%3E%3C/line%3E%3Cline x1='12' y1='16' x2='12.01' y2='16'%3E%3C/line%3E%3C/svg%3E");
            background-size: contain;
            flex-shrink: 0;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.55rem 1.1rem;
            border: 1px solid transparent;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.88rem;
            font-weight: 600;
            font-family: inherit;
            line-height: 1.25;
            transition: all 0.15s ease-in-out;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .btn:hover {
            background: #1d4ed8;
            color: #fff;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-rojo {
            background: #ef4444;
            color: #fff;
        }

        .btn-rojo:hover {
            background: #dc2626;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
        }

        .btn-outline {
            background: #fff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-outline:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
            box-shadow: var(--shadow-sm);
        }

        .btn-outline-danger {
            background: #fff;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-outline-danger:hover {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #f87171;
            box-shadow: var(--shadow-sm);
        }

        .btn-sm {
            padding: 0.38rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        .btn-grande {
            padding: 0.75rem 1.6rem;
            font-size: 0.95rem;
            border-radius: 9px;
        }

        /* General Forms & Inputs */
        label {
            display: block;
            margin-bottom: 0.35rem;
            font-weight: 600;
            font-size: 0.88rem;
            color: #334155;
        }

        input[type=text], input[type=email], input[type=password], select {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            color: #0f172a;
            background-color: #fff;
            box-sizing: border-box;
            transition: all 0.15s ease-in-out;
        }

        input[type=text]:focus, input[type=email]:focus, input[type=password]:focus, select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        small.error {
            display: block;
            color: #dc2626;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.35rem;
        }

        .acciones {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-top: 1.5rem;
        }

        .acciones a {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.55rem 0.9rem;
            border-radius: 8px;
            transition: all 0.15s ease-in-out;
        }

        .acciones a:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        /* Legacy compatibility styling for dashboard and residentes */
        .resumen { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .dato {
            background: #fff;
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            flex: 1;
            min-width: 160px;
            box-shadow: var(--shadow-sm);
            border: 1px solid #e2e8f0;
        }
        .dato span { display: block; font-size: 1.8rem; font-weight: 800; color: #1e3a8a; line-height: 1.2; }
        .torres { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem; }
        .torre {
            background: #fff;
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            border-top: 4px solid #2563eb;
            box-shadow: var(--shadow-sm);
        }
        .torre h2 { margin: 0 0 0.75rem; font-size: 1.2rem; font-weight: 700; color: #0f172a; }
        .torre h3 { margin: 1.2rem 0 0.5rem; font-size: 0.92rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em; }
        .torre p { margin: 0.4rem 0; font-size: 0.9rem; color: #475569; }
        .alerta { color: #d97706; font-weight: 600; }
        .filtro { display: flex; gap: 0.75rem; margin-bottom: 1.25rem; }
        .filtro input { flex: 1; }
        .tabla-scroll {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            box-shadow: var(--shadow-sm);
        }
        table { width: 100%; border-collapse: collapse; background: #fff; text-align: left; }
        th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1.1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 0.9rem 1.1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
            color: #334155;
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background-color: #f8fafc; }

        /* Login box */
        .caja-login {
            max-width: 420px;
            margin: 3.5rem auto;
            background: #fff;
            padding: 2.25rem;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: var(--shadow-lg);
        }
        .caja-login h1 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
        }
        label.recordar {
            font-weight: 400;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            cursor: pointer;
            font-size: 0.85rem;
        }
        label.recordar input {
            width: auto;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            header.top-header { padding: 0 1rem; }
            .header-inner { min-height: 60px; padding: 0.75rem 0; }
            main { padding: 0 1rem; margin: 1.25rem auto; }
            .user-nav-section { margin-left: 0; width: 100%; justify-content: space-between; }
        }
    </style>
    @yield('estilos')
</head>
<body>
    <header class="top-header">
        <div class="header-inner">
            <a href="{{ auth()->check() ? route('dashboard.index') : '#' }}" class="brand-container">
                <div class="brand-logo-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 9h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="brand-name">
                    ConjuntoSmart
                    <span class="brand-badge">Admin</span>
                </div>
            </a>

            @auth
                <nav class="main-nav">
                    <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="7" height="9" rx="1"/>
                            <rect x="14" y="3" width="7" height="5" rx="1"/>
                            <rect x="14" y="12" width="7" height="9" rx="1"/>
                            <rect x="3" y="16" width="7" height="5" rx="1"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('empleados.index') }}" class="{{ request()->routeIs('empleados.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Empleados
                    </a>
                    <a href="{{ route('residentes.index') }}" class="{{ request()->routeIs('residentes.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        Residentes
                    </a>
                </nav>

                <div class="user-nav-section">
                    <div class="user-pill">
                        <span class="user-avatar-mini">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="salir">
                        @csrf
                        <button type="submit" title="Cerrar sesión">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Salir
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main>
        @if (session('ok'))
            <div class="ok">{{ session('ok') }}</div>
        @endif
        @if (session('error'))
            <div class="aviso-error">{{ session('error') }}</div>
        @endif

        @yield('contenido')
    </main>

    @yield('scripts')
</body>
</html>