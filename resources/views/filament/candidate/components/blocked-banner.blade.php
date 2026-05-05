@if(auth()->user()?->candidate?->is_blocked)
    <x-filament::section style="background-color: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <x-filament::icon
                icon="heroicon-m-exclamation-triangle"
                style="width: 1.5rem; height: 1.5rem; color: #ef4444; flex-shrink: 0;"
            />
            <div>
                <h3 style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem; color: #ef4444;">Cuenta Bloqueada</h3>
                <p style="font-size: 0.875rem; margin: 0;">Tu perfil de candidato ha sido bloqueado por el administrador. No podrás postularte a nuevas vacantes.</p>
            </div>
        </div>
    </x-filament::section>
@endif
