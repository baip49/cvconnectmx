<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CVConnectMX</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Spinnaker&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="relative min-h-screen overflow-auto bg-[#050816] text-white">
        <div class="absolute inset-0 pointer-events-none bg-[radial-gradient(circle_at_top,rgba(249,115,22,0.20),transparent_32%),radial-gradient(circle_at_right,rgba(59,130,246,0.18),transparent_28%),linear-gradient(180deg,#08101f_0%,#02040a_100%)]"></div>
        <div class="absolute left-1/2 top-0 h-80 w-80 -translate-x-1/2 rounded-full pointer-events-none bg-orange-500/15 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full pointer-events-none bg-sky-500/10 blur-3xl"></div>

        <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-7xl flex-col px-6 py-6 sm:px-8 lg:px-10">
            @if (Route::has('login'))

                <header class="flex items-center justify-end">
                    <nav class="flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-3 py-3 backdrop-blur-xl z-20">
                        @if ($isAuthenticated)
                            <a
                                href="{{ $dashboardRoute }}"
                                class="inline-flex items-center rounded-full border border-white/15 bg-white px-5 py-2 text-sm font-semibold text-slate-950 transition hover:bg-orange-50"
                            >
                                {{ __('messages.Dashboard') }}
                            </a>
                        @else
                            <a
                                href="/login"
                                class="inline-flex items-center rounded-full border border-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:border-white/25 hover:bg-white/10"
                            >
                                {{ __('messages.Log in') }}
                            </a>

                            @if (Route::has('register'))
                                        <a
                                            href="/register"
                                            class="inline-flex items-center rounded-full border border-orange-300/30 bg-orange-400/15 px-5 py-2 text-sm font-semibold text-orange-50 transition hover:bg-orange-400/25"
                                        >
                                            {{ __('messages.Register') }}
                                        </a>
                            @endif
                        @endif
                    </nav>
                </header>
            @endif

            <main class="flex flex-1 items-center justify-center py-10 sm:py-16">
                <section class="grid w-full max-w-5xl gap-10 text-center">
                    <div class="mx-auto flex w-full max-w-3xl flex-col items-center gap-6 rounded-[2rem] border border-white/10 bg-white/5 px-6 py-10 shadow-2xl shadow-black/30 backdrop-blur-2xl sm:px-10 lg:px-14 lg:py-14">
                        <div class="flex items-center justify-center rounded-[1.75rem] border border-white/12 bg-white/8 p-6 shadow-[0_0_60px_rgba(255,255,255,0.08)]">
                            <x-app-logo-icon class="size-16 text-white" />
                        </div>

                        <div class="space-y-4">
                            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-orange-200/90">CVConnectMX</p>
                            <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl">
                                {{ __('messages.Talento, vacantes y expedientes en un solo lugar.') }}
                            </h1>
                            <p class="mx-auto max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                                {{ __('messages.Una puerta de entrada clara para candidatos, empresas y administradores. Entra a tu panel, publica oportunidades y da seguimiento sin perder contexto.') }}
                            </p>
                        </div>

                        @if (Route::has('login'))
                            <div class="flex flex-col items-center gap-3 sm:flex-row">
                                @if ($isAuthenticated)
                                    <a
                                        href="{{ $dashboardRoute }}"
                                        class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-50"
                                    >
                                        {{ __('messages.Ir al dashboard') }}
                                    </a>
                                @else
                                    <a
                                        href="/login"
                                        class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-50"
                                    >
                                        {{ __('messages.Log in') }}
                                    </a>

                                    @if (Route::has('register'))
                                        <a
                                            href="/register"
                                            class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                                        >
                                            {{ __('messages.Register') }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 text-left backdrop-blur-xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-200/80">{{ __('messages.Candidatos') }}</p>
                            <p class="mt-3 text-base leading-7 text-slate-300">{{ __('messages.Perfiles y documentos organizados para avanzar mas rapido en cada proceso.') }}</p>
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 text-left backdrop-blur-xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-200/80">{{ __('messages.Empresas') }}</p>
                            <p class="mt-3 text-base leading-7 text-slate-300">{{ __('messages.Vacantes, revisiones y acceso a talento con una experiencia limpia y directa.') }}</p>
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 text-left backdrop-blur-xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-200/80">{{ __('messages.Control') }}</p>
                            <p class="mt-3 text-base leading-7 text-slate-300">{{ __('messages.Un centro de operaciones para administrar accesos y mantener el flujo bajo control.') }}</p>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>