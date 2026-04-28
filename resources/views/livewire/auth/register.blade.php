<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:field>
            <flux:input
                wire:model="name"
                name="name"
                :label="__('Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Name')"
            />
            <flux:error name="name" />
        </flux:field>

        <!-- Last Name -->
        <flux:field>
            <flux:input
                wire:model="last_name"
                name="last_name"
                :label="__('Last name')"
                type="text"
                required
                autocomplete="name"
                :placeholder="__('Last name')"
            />
            <flux:error name="last_name" />
        </flux:field>

        <!-- Email Address -->
        <flux:field>
            <flux:input
                wire:model="email"
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />
            <flux:error name="email" />
        </flux:field>

        <!-- Password -->
        <flux:field>
            <flux:input
                wire:model="password"
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />
            <flux:error name="password" />
        </flux:field>

        <!-- Confirm Password -->
        <flux:field>
            <flux:input
                wire:model="password_confirmation"
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />
            <flux:error name="password_confirmation" />
        </flux:field>

        <!-- User Type -->
        <flux:field>
            <flux:select
                wire:model="user_type"
                name="user_type"
                :label="__('Account type')"
                required
                :placeholder="__('Select account type')"
            >
                <option value="candidate">{{ __('Soy Candidato') }}</option>
                <option value="company">{{ __('Soy Empresa') }}</option>
            </flux:select>
            <flux:error name="user_type" />
        </flux:field>

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
