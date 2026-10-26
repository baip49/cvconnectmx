@props([
    'account',
    'size' => 'size-4',
    'color' => 'text-(--color-accent-content)',
])

{{-- Blue check next to the name of an admin-verified user or company. --}}
@if ($account?->isVerifiedAccount())
    <flux:icon.check-badge
        variant="mini"
        role="img"
        :aria-label="__('shared.verification.badge')"
        :title="__('shared.verification.badge')"
        {{ $attributes->class(['inline shrink-0', $size, $color]) }}
    />
@endif
