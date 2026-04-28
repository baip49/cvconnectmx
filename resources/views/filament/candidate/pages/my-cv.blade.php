<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->getSchema('form') }}

        <div class="mt-6">
            {{ $this->getFormActions()[0] }}
        </div>
    </form>
</x-filament-panels::page>
