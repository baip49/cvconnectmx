<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CVConnectMX - Tu Comunidad Profesional</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f2f0;
            scroll-behavior: smooth;
        }

        .hero-title {
            color: #b24020;
        }

        .btn-primary {
            background-color: #0a66c2;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: #004182;
        }

        .btn-outline {
            border: 1px solid #0a66c2;
            color: #0a66c2;
            transition: background-color 0.2s;
        }

        .btn-outline:hover {
            background-color: #eff6ff;
        }
    </style>
</head>

<body class="text-slate-900 antialiased">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <header class="flex h-20 items-center justify-between">
            <div class="flex items-center gap-2">
                <x-app-logo-icon class="h-8 w-auto text-[#0a66c2] sm:h-9" />
                <span class="text-xl font-bold tracking-tight text-[#0a66c2] sm:text-2xl">CVConnect<span
                        class="text-slate-800">MX</span></span>
            </div>

            @if (Route::has('login'))
                <nav class="flex items-center gap-1 sm:gap-4">
                    @if ($isAuthenticated)
                        <a href="{{ $dashboardRoute }}"
                            class="rounded-full btn-primary px-3 py-2.5 text-sm font-semibold text-white shadow-sm sm:px-6">
                            Ir a mi panel
                        </a>
                    @else
                        <a href="/login"
                            class="hidden rounded-full px-6 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-200/50 sm:block">
                            Iniciar sesión
                        </a>
                        <a href="/login" class="rounded-full btn-outline px-3 py-2.5 text-sm font-semibold sm:hidden">
                            Iniciar sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="/register"
                                class="rounded-full btn-primary px-3 py-2.5 text-sm font-semibold text-white shadow-sm sm:px-6">
                                Unirse ahora
                            </a>
                        @endif
                    @endif
                </nav>
            @endif
        </header>

        {{-- Hero Section --}}
        <main class="py-12 sm:py-20 lg:py-24">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-8">
                <div class="max-w-xl space-y-8 text-left">
                    <h1 class="hero-title text-4xl font-light leading-[1.1] sm:text-5xl lg:text-6xl">
                        Informa a las personas adecuadas de que buscas empleo
                    </h1>
                    <p class="text-lg leading-relaxed text-slate-600 sm:text-xl">
                        CVConnectMX te permite conectar con empresas, gestionar tus expedientes y encontrar tu próxima
                        oportunidad profesional, ya sea de forma privada o pública.
                    </p>

                    <div class="pt-4">
                        @if (!$isAuthenticated)
                            <a href="/register"
                                class="inline-flex w-full items-center justify-center rounded-full btn-primary py-4 text-xl font-semibold text-white shadow-md sm:w-auto sm:px-12">
                                Empieza ahora
                            </a>
                        @else
                            <a href="{{ $dashboardRoute }}"
                                class="inline-flex w-full items-center justify-center rounded-full btn-primary py-4 text-xl font-semibold text-white shadow-md sm:w-auto sm:px-12">
                                Ir a mi panel
                            </a>
                        @endif
                    </div>
                </div>

                <div class="relative hidden justify-end lg:flex">
                    <div
                        class="relative h-120 w-120 overflow-hidden rounded-full border-12 border-white shadow-2xl">
                        <img src="/images/hero.png" alt="Candidato Profesional" class="h-full w-full object-cover">
                    </div>
                </div>
            </div>

            {{-- Features Section --}}
            <div class="mt-24 grid gap-16 sm:grid-cols-2 lg:mt-32 lg:gap-24">
                <div class="group flex flex-col items-start text-left">
                    <div
                        class="mb-8 flex h-72 w-full items-center justify-center overflow-hidden rounded-3xl bg-blue-50/50 p-8 transition duration-300 group-hover:bg-blue-50">
                        <img src="/images/feature_1.png" alt="Conexión Profesional"
                            class="h-full w-auto object-contain transition duration-500 group-hover:scale-105">
                    </div>
                    <h2 class="text-3xl font-normal leading-tight text-slate-800 sm:text-4xl">
                        Conecta con personas que te pueden ayudar
                    </h2>
                </div>

                <div class="group flex flex-col items-start text-left">
                    <div
                        class="mb-8 flex h-72 w-full items-center justify-center overflow-hidden rounded-3xl bg-orange-50/50 p-8 transition duration-300 group-hover:bg-orange-50">
                        <img src="/images/feature_2.png" alt="Desarrollo de Aptitudes"
                            class="h-full w-auto object-contain transition duration-500 group-hover:scale-105">
                    </div>
                    <h2 class="text-3xl font-normal leading-tight text-slate-800 sm:text-4xl">
                        Adquiere las aptitudes necesarias para triunfar
                    </h2>
                </div>
            </div>

            {{-- Footer decorativo --}}
            <footer class="mt-24 border-t border-slate-200 py-12 text-center text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} CVConnectMX. Todos los derechos reservados.</p>
            </footer>
        </main>
    </div>
</body>

</html>
