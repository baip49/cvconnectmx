<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-100 font-sans text-zinc-800 antialiased dark:bg-zinc-950 dark:text-white">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-6">
                <a href="{{ route('home') }}" class="flex justify-center" wire:navigate>
                    @include('filament.components.brand-logo')
                </a>

                <flux:card class="flex flex-col gap-6">
                    {{ $slot }}
                </flux:card>
            </div>
        </div>
        <flux:toast />
        @fluxScripts
    </body>
</html>
