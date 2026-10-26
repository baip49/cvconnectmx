{{-- One dark-mode switch for the whole app: Filament's user-menu switcher ("theme") is the source and Flux ("flux.appearance") follows it. --}}
<script>
    (() => {
        const sync = (theme) => theme && theme !== 'system'
            ? localStorage.setItem('flux.appearance', theme)
            : localStorage.removeItem('flux.appearance');

        sync(localStorage.getItem('theme'));
        window.addEventListener('theme-changed', (event) => sync(event.detail));
    })();
</script>
