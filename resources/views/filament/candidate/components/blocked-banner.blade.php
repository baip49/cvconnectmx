@if(auth()->user()?->candidate?->is_blocked)
    <div class="fi-wi-widget mb-4">
        <flux:callout variant="danger" icon="exclamation-triangle" :heading="__('candidate.banners.blocked_heading')">
            <flux:callout.text>
                {{ __('candidate.banners.blocked_body') }}
            </flux:callout.text>
        </flux:callout>
    </div>
@endif
