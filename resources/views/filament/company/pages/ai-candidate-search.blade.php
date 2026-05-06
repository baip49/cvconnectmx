<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="runAiSearch" class="p-6 bg-white dark:bg-gray-900 shadow rounded-xl">
            {{ $this->form }}
            
            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                    Buscar con IA
                </x-filament::button>
            </div>
        </form>

        <div class="p-6 bg-white dark:bg-gray-900 shadow rounded-xl mt-8">
            <h2 class="text-xl font-bold mb-4">Candidatos Sugeridos</h2>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
