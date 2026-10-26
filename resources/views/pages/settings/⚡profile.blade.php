<?php

use App\Concerns\ProfileValidationRules;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $last_name = '';
    public string $email = '';
    public $avatar = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->last_name = (string) $user->last_name;
        $this->email = $user->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate(array_merge(
            $this->profileRules($user->id),
            [
                'last_name' => ['nullable', 'string', 'max:255'],
                'avatar' => ['nullable', 'image', 'max:10240'],
            ]
        ));

        if (! empty($validated['avatar'])) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $user->avatar_path = $validated['avatar']->store('avatars', 'public');
        }

        $user->fill([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? $user->last_name,
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->reset('avatar');

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Ask the admins for the verified-account check.
     */
    public function requestVerification(): void
    {
        Auth::user()->requestVerification();

        Notification::make()->title(__('shared.verification.request_sent'))->success()->send();
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('home', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    <flux:heading size="lg">{{ __('Profile') }}</flux:heading>
    <flux:subheading>{{ __('Update your name and email address') }}</flux:subheading>

    <div class="my-6 flex flex-wrap items-center gap-4">
        <flux:avatar size="lg" circle :src="Auth::user()->getFilamentAvatarUrl() ?? (new \App\Filament\AvatarProviders\InitialsAvatarProvider)->get(Auth::user())" :name="Auth::user()->name" />
        {{-- min-w keeps name and email readable: on narrow screens the button wraps below instead of covering them. --}}
        <div class="min-w-48 flex-1">
            <div class="flex items-center gap-1">
                <flux:text class="font-medium">{{ Auth::user()->name }}</flux:text>
                <x-verified-badge :account="Auth::user()" />
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <flux:text class="text-sm break-all">{{ Auth::user()->email }}</flux:text>
                @if ($this->hasUnverifiedEmail)
                    <flux:badge size="sm" color="amber" icon="exclamation-triangle">{{ __('Email not verified') }}</flux:badge>
                @else
                    <flux:badge size="sm" color="emerald" icon="check-badge">{{ __('Email verified') }}</flux:badge>
                @endif
            </div>
        </div>

        @if ($this->hasUnverifiedEmail)
            <flux:button size="sm" variant="primary" icon="envelope" class="w-full sm:w-auto" wire:click="resendVerificationNotification" data-test="resend-verification-button">
                {{ __('Resend verification email') }}
            </flux:button>
        @elseif (Auth::user()->hasPendingVerificationRequest())
            <flux:badge size="sm" icon="clock">{{ __('shared.verification.requested') }}</flux:badge>
        @elseif (Auth::user()->accountBadge() === null)
            <flux:button size="sm" variant="filled" icon="check-badge" class="w-full sm:w-auto" wire:click="requestVerification" data-test="request-verification-button">
                {{ __('shared.verification.request') }}
            </flux:button>
        @endif
    </div>

    @if (session('status') === 'verification-link-sent')
        <flux:callout variant="success" icon="envelope" class="mb-6" :heading="__('A new verification link has been sent to your email address.')" />
    @endif

    <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

        <flux:input wire:model="last_name" :label="__('Last name')" type="text" autocomplete="family-name" />

        <div>
            <flux:label>{{ __('Profile picture') }}</flux:label>

            <div
                x-data="{ dragging: false }"
                x-on:dragover.prevent="dragging = true"
                x-on:dragleave.prevent="dragging = false"
                x-on:drop.prevent="dragging = false; if ($event.dataTransfer.files.length) { $refs.avatarInput.files = $event.dataTransfer.files; $refs.avatarInput.dispatchEvent(new Event('change', { bubbles: true })); }"
                :class="dragging ? 'border-(--color-accent) bg-(--color-accent)/5' : 'border-zinc-300 dark:border-zinc-700'"
                class="mt-2 rounded-xl border-2 border-dashed bg-zinc-500/5 px-6 py-8 text-center transition dark:bg-white/5"
            >
                <label for="avatar-upload" class="cursor-pointer">
                    <flux:icon.cloud-arrow-up class="mx-auto size-8 text-zinc-400" />
                    <p class="mt-2 text-sm font-semibold">{{ __('Drop files here or click to browse') }}</p>
                    <p class="mt-1 text-xs text-zinc-500">JPG, PNG, GIF · 10MB</p>
                </label>
                <input id="avatar-upload" x-ref="avatarInput" type="file" wire:model="avatar" accept="image/*" class="sr-only" />
            </div>

            @if ($avatar)
                <div class="mt-3 flex items-center gap-3 rounded-xl border border-zinc-200 bg-zinc-500/5 px-3 py-2.5 dark:border-zinc-700 dark:bg-white/5">
                    <img src="{{ $avatar->temporaryUrl() }}" alt="{{ __('Preview') }}" class="h-11 w-11 rounded-lg object-cover" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium">{{ $avatar->getClientOriginalName() }}</p>
                        <p class="text-xs text-zinc-500">{{ number_format($avatar->getSize() / 1024, 0) }} KB</p>
                    </div>
                    <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="$set('avatar', null)" :aria-label="__('Remove file')" />
                </div>
            @endif

            <flux:error name="avatar" />
        </div>

        <div>
            <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
        </div>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit" data-test="update-profile-button">
                {{ __('Save') }}
            </flux:button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>

    @if ($this->showDeleteUser)
        <livewire:pages::settings.delete-user-form />
    @endif
</section>
