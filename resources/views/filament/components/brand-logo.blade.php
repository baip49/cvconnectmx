@php
    $color = match ($panel) {
        'admin' => 'text-indigo-600 dark:text-indigo-400',
        'candidate' => 'text-emerald-600 dark:text-emerald-400',
        'company' => 'text-sky-600 dark:text-sky-400',
        default => 'text-primary-600',
    };
@endphp

<div class="flex items-center gap-2 font-bold text-lg {{ $color }}">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
        <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.744 6.744 0 0018.75 15.75a.75.75 0 00-.75-.75h-7.5a.75.75 0 00-.75.75s0 .083.008.155a.75.75 0 01-.788.724c-.734-.053-1.49-.107-2.262-.244a.75.75 0 01-.183-1.49zM18.75 17.25h-7.5a.75.75 0 00-.75.75s0 .083.008.155a.75.75 0 01-.788.724c-.734-.053-1.49-.107-2.262-.244a.75.75 0 01-.183-1.49A6.744 6.744 0 0018.75 18a.75.75 0 000-1.5z" clip-rule="evenodd" />
    </svg>
    <span>CVConnectMX</span>
</div>
