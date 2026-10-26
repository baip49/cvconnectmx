{{-- Lets visitors pick the language before signing in; the option is named in its own language so it is always recognizable. --}}
@php($otherLocale = app()->getLocale() === 'en' ? 'es' : 'en')

<flux:button variant="ghost" size="sm" icon="language" :href="route('locale.switch', $otherLocale)">
    {{ $otherLocale === 'es' ? 'Español' : 'English' }}
</flux:button>
