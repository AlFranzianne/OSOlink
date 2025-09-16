<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Add New User') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Create a new account with name, email, and password.") }}
        </p>
    </header>

    <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Make Admin Checkbox -->
        <div class="flex items-center">
            <input id="is_admin" name="is_admin" type="checkbox" value="1"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <x-input-label for="is_admin" :value="__('Make this user an admin')" class="ml-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Create User') }}</x-primary-button>
            @if (session('success'))
                <p class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
            @endif
        </div>
    </form>
</section>
