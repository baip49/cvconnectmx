<x-filament-panels::page>
    <div class="space-y-6">
        <flux:card>
            <form wire:submit="runAiSearch">
                {{ $this->form }}

                <div class="mt-4 flex justify-end gap-2">
                    <x-filament::button type="button" color="gray" wire:click="resetAiSearch" icon="heroicon-o-arrow-path">
                        {{ __('company.ai_candidate_search.reset') }}
                    </x-filament::button>
                    <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                        {{ __('company.ai_candidate_search.search_with_ai') }}
                    </x-filament::button>
                </div>
            </form>
        </flux:card>

        <div class="space-y-4">
            <flux:heading size="lg">{{ __('company.ai_candidate_search.suggested_candidates') }}</flux:heading>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
