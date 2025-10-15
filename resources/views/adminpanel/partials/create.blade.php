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

        <div class="flex items-center gap-4 mt-4">
            <!-- First Name -->
            <div class="flex-1">
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <!-- Middle Name (Optional) -->
            <div class="flex-1">
                <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" />
                <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            </div>

            <!-- Last Name -->
            <div class="flex-1">
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <div class="flex items-center gap-4 mt-4">
            <!-- Email -->
            <div class="flex-1">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <!-- Job Type -->
            <div class="flex-1">
                <x-input-label for="job_type" :value="__('Job Type')" />
                <x-text-input id="job_type" name="job_type" type="text" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('job_type')" />
            </div>
        </div>

        <div class="flex items-center gap-4 mt-4">
            <!-- Employment Status -->
            <div class="flex-1">
                <x-input-label for="employment_status" :value="__('Employment Status')" />
                <select id="employment_status" name="employment_status" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">Select Employment Status</option>
                    <option value="Full-time" {{ old('employment_status') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                    <option value="Part-time" {{ old('employment_status') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                    <option value="Contractual" {{ old('employment_status') == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                    <option value="Intern" {{ old('employment_status') == 'Intern' ? 'selected' : '' }}>Intern</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('employment_status')" />
            </div>

            <!-- Hourly Rate -->
            <div class="flex-1">
                <x-input-label for="hourly_rate" :value="__('Hourly Rate')" />
                <x-text-input id="hourly_rate" name="hourly_rate" type="number" step="0.01" min="0" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('hourly_rate')" />
            </div>
        </div>

        <div class="flex items-center gap-4 mt-4">
            <!-- Password -->
            <div class="flex-1">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('password')" />
            </div>

            <!-- Confirm Password -->
            <div class="flex-1">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
            </div>
        </div>

        <!-- Make Admin Checkbox -->
        <div class="flex items-center">
            <input id="is_admin" name="is_admin" type="checkbox" value="1"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <x-input-label for="is_admin" :value="__('Make this user an admin')" class="ml-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Create User') }}</x-primary-button>
            @if (session('create_success'))
                <p class="text-sm text-green-600 dark:text-green-400">{{ session('create_success') }}</p>
            @endif
        </div>
    </form>
</section>
