<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @include('partials.head', ['title' => __('shared.welcome.page_title')])
</head>

<body class="min-h-screen bg-zinc-100 font-sans text-zinc-800 antialiased dark:bg-zinc-950 dark:text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <header class="flex h-20 items-center justify-between">
            @include('filament.components.brand-logo')

            @if (Route::has('login'))
                <nav class="flex items-center gap-2">
                    @if ($isAuthenticated)
                        <flux:button variant="primary" :href="$dashboardRoute">{{ __('shared.welcome.go_to_panel') }}</flux:button>
                    @else
                        <flux:button variant="ghost" href="/login">{{ __('shared.welcome.log_in') }}</flux:button>
                        @if (Route::has('register'))
                            <flux:button variant="primary" href="/register">{{ __('shared.welcome.join_now') }}</flux:button>
                        @endif
                    @endif
                </nav>
            @endif
        </header>

        {{-- Hero Section --}}
        <main class="py-12 sm:py-20 lg:py-24">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-8">
                <div class="max-w-xl space-y-8 text-left">
                    <h1 class="font-display text-4xl font-bold leading-[1.1] tracking-tight text-zinc-900 sm:text-5xl lg:text-6xl dark:text-white">
                        {{ __('shared.welcome.headline') }} <span class="text-(--color-accent-content)">{{ __('shared.welcome.headline_accent') }}</span>
                    </h1>
                    <p class="text-lg leading-relaxed text-zinc-600 sm:text-xl dark:text-zinc-300">
                        {{ __('shared.welcome.intro') }}
                    </p>

                    <div class="pt-4">
                        @if (! $isAuthenticated)
                            <flux:button variant="primary" href="/register" class="w-full sm:w-auto sm:px-12">
                                {{ __('shared.welcome.get_started') }}
                            </flux:button>
                        @else
                            <flux:button variant="primary" :href="$dashboardRoute" class="w-full sm:w-auto sm:px-12">
                                {{ __('shared.welcome.go_to_panel') }}
                            </flux:button>
                        @endif
                    </div>
                </div>

                <div class="relative hidden justify-end lg:flex">
                    <div class="relative size-120 overflow-hidden rounded-full border-12 border-white shadow-2xl ring-1 ring-zinc-900/10 dark:border-zinc-800 dark:ring-white/10">
                        <img src="/images/hero.png" alt="{{ __('shared.welcome.hero_alt') }}" class="h-full w-full object-cover">
                    </div>
                </div>
            </div>

            {{-- Features Section --}}
            <div class="mt-24 grid gap-16 sm:grid-cols-2 lg:mt-32 lg:gap-24">
                <div class="group flex flex-col items-start text-left">
                    <div class="mb-8 flex h-72 w-full items-center justify-center overflow-hidden rounded-xl border border-zinc-900/10 bg-blue-400/10 p-8 transition duration-300 group-hover:bg-blue-400/15 dark:border-white/10">
                        <img src="/images/feature_1.png" alt="{{ __('shared.welcome.feature_connect_alt') }}" class="h-full w-auto object-contain transition duration-500 group-hover:scale-105">
                    </div>
                    <h2 class="font-display text-3xl font-semibold leading-tight tracking-tight sm:text-4xl">
                        {{ __('shared.welcome.feature_connect') }}
                    </h2>
                </div>

                <div class="group flex flex-col items-start text-left">
                    <div class="mb-8 flex h-72 w-full items-center justify-center overflow-hidden rounded-xl border border-zinc-900/10 bg-sky-600/10 p-8 transition duration-300 group-hover:bg-sky-600/15 dark:border-white/10">
                        <img src="/images/feature_2.png" alt="{{ __('shared.welcome.feature_skills_alt') }}" class="h-full w-auto object-contain transition duration-500 group-hover:scale-105">
                    </div>
                    <h2 class="font-display text-3xl font-semibold leading-tight tracking-tight sm:text-4xl">
                        {{ __('shared.welcome.feature_skills') }}
                    </h2>
                </div>
            </div>

            <footer class="mt-24 border-t border-zinc-200 py-12 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                <p>&copy; {{ date('Y') }} CVConnectMX. {{ __('shared.welcome.rights') }}</p>
            </footer>
        </main>
    </div>

    @fluxScripts
</body>

</html>
