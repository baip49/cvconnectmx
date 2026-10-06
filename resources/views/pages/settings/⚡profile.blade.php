<?php

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use PasswordValidationRules;
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $last_name = '';
    public string $email = '';
    public $avatar = null;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

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
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ]);

        Auth::user()->forceFill(['password' => Hash::make($validated['password'])])->save();

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
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
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <div class="my-6 flex items-center gap-4">
            @if (Auth::user()->getFilamentAvatarUrl())
                <img src="{{ Auth::user()->getFilamentAvatarUrl() }}" alt="Avatar" class="h-16 w-16 rounded-full object-cover" />
            @else
                <div class="flex h-16 w-16 items-center justify-center rounded-full text-xl font-bold text-white" style="background-color: {{ Auth::user()->avatarColorHex() }}">
                    {{ Auth::user()->initials() }}
                </div>
            @endif
            <div>
                <flux:text class="font-medium">{{ Auth::user()->name }}</flux:text>
                <flux:text class="text-sm">{{ Auth::user()->email }}</flux:text>
            </div>
        </div>

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
                    :class="dragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-500/10' : 'border-zinc-300 dark:border-zinc-700'"
                    class="mt-2 rounded-2xl border-2 border-dashed bg-zinc-500/5 px-6 py-8 text-center transition dark:bg-white/5"
                >
                    <label for="avatar-upload" class="cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-8 w-8 text-zinc-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                        <p class="mt-2 text-sm font-semibold">Drop files here or click to browse</p>
                        <p class="mt-1 text-xs text-zinc-500">JPG, PNG, GIF up to 10MB</p>
                    </label>
                    <input id="avatar-upload" x-ref="avatarInput" type="file" wire:model="avatar" accept="image/*" class="sr-only" />
                </div>

                @if ($avatar)
                    <div class="mt-3 flex items-center gap-3 rounded-2xl border border-zinc-200 bg-zinc-500/5 px-3 py-2.5 dark:border-zinc-700 dark:bg-white/5">
                        <img src="{{ $avatar->temporaryUrl() }}" alt="Preview" class="h-11 w-11 rounded-xl object-cover" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $avatar->getClientOriginalName() }}</p>
                            <p class="text-xs text-zinc-500">{{ number_format($avatar->getSize() / 1024, 0) }} KB</p>
                        </div>
                        <button type="button" wire:click="$set('avatar', null)" aria-label="Remove file" class="rounded-lg p-1.5 text-zinc-500 transition hover:bg-zinc-500/10 hover:text-zinc-800 dark:hover:text-zinc-200">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                @endif

                @error('avatar')
                    <flux:text class="mt-2 text-sm text-red-600">{{ $message }}</flux:text>
                @enderror
            </div>

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <form wire:submit="updatePassword" class="my-6 w-full space-y-6 border-t border-zinc-200 pt-6 dark:border-white/10">
            <flux:heading size="sm">{{ __('Change password') }}</flux:heading>

            <flux:input wire:model="current_password" :label="__('Current password')" type="password" required autocomplete="current-password" />

            <flux:input wire:model="password" :label="__('New password')" type="password" required autocomplete="new-password" />

            <flux:input wire:model="password_confirmation" :label="__('Confirm new password')" type="password" required autocomplete="new-password" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('Update password') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
