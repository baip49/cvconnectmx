@props([
    'account',
    'size' => 'size-4',
    'color' => null,
])

{{-- Check next to a name: gold for CVConnectMX staff, blue for admin-verified users and companies. --}}
@php($badge = $account?->accountBadge())

@if ($badge)
    @php($label = $badge === 'staff' ? __('shared.verification.staff_badge') : __('shared.verification.badge'))

    <flux:icon.check-badge
        variant="mini"
        role="img"
        :aria-label="$label"
        :title="$label"
        {{ $attributes->class(['inline shrink-0', $size, $color ?? ($badge === 'staff' ? 'text-amber-600 dark:text-amber-400' : 'text-(--color-accent-content)')]) }}
    />
@endif
