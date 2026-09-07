@if(auth()->user()?->candidate?->is_blocked)
    <div class="fi-wi-widget mb-4">
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-red-600 via-red-500 to-orange-500 p-4 shadow-lg">
            <div class="absolute -right-4 -top-4 opacity-10 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-10 w-10">
                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="shrink-0">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                        <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-white" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">Tu perfil ha sido restringido</p>
                    <p class="text-xs text-red-100">No puedes postularte a vacantes en este momento. Si crees que esto es un error, contacta al administrador.</p>
                </div>
            </div>
        </div>
    </div>
@endif
