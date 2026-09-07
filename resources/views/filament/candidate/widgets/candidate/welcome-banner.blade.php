<x-filament-widgets::widget class="fi-wi-welcome-banner overflow-hidden">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-600 p-6 text-white shadow-xl">
        <div class="absolute -right-8 -top-8 opacity-10 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-12 w-12">
                <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.744 6.744 0 0018.75 15.75a.75.75 0 00-.75-.75h-7.5a.75.75 0 00-.75.75s0 .083.008.155a.75.75 0 01-.788.724c-.734-.053-1.49-.107-2.262-.244a.75.75 0 01-.183-1.49zM18.75 17.25h-7.5a.75.75 0 00-.75.75s0 .083.008.155a.75.75 0 01-.788.724c-.734-.053-1.49-.107-2.262-.244a.75.75 0 01-.183-1.49A6.744 6.744 0 0018.75 18a.75.75 0 000-1.5z" clip-rule="evenodd" />
            </svg>
        </div>

        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-1">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                    <x-heroicon-o-sparkles class="h-4 w-4 text-white" />
                </div>
                <h2 class="text-2xl font-bold">¡Hola, {{ $name }}!</h2>
            </div>
            <p class="mt-2 max-w-2xl text-emerald-50">
                Bienvenido a tu portal de candidato. Gestiona tu CV, revisa vacantes sugeridas y da seguimiento a tus postulaciones.
            </p>

            @if ($rating)
                <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 text-sm font-medium backdrop-blur-sm transition-all hover:bg-white/30">
                    <x-heroicon-o-sparkles class="h-4 w-4" />
                    <span>Tu perfil tiene una calificación IA de <strong>{{ $rating }}/100</strong></span>
                    <div class="ml-1 h-2 w-2 rounded-full {{ $rating >= 80 ? 'bg-green-300' : ($rating >= 50 ? 'bg-yellow-300' : 'bg-red-300') }}"></div>
                </div>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
