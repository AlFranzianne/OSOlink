<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('User Information') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __("View an account's information.") }}
                    </p>
                </header>

                <!-- Profile Picture -->
                <x-input-label for="profile_picture" :value="__('Profile Picture')" class="mt-4"/>
                <div class="flex items-center gap-4 mt-1">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" width="100" class="rounded-full">
                    @else
                        <img src="{{ asset('images/default-avatar.jpg') }}" alt="Default Avatar" width="100" class="rounded-full">
                    @endif
                </div>

                <div class="mt-6 space-y-6">
                    <div class="flex items-center gap-4 mt-4">
                        <!-- First Name -->
                        <div class="flex-1">
                            <x-input-label :value="__('First Name')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->first_name" disabled />
                        </div>
                        <!-- Middle Name -->
                        <div class="flex-1">
                            <x-input-label :value="__('Middle Name')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->middle_name" disabled />
                        </div>
                        <!-- Last Name -->
                        <div class="flex-1">
                            <x-input-label :value="__('Last Name')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->last_name" disabled />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <!-- Email -->
                        <div class="flex-1">
                            <x-input-label :value="__('Email')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->email" disabled />
                        </div>
                        <!-- Phone -->
                        <div class="flex-1">
                            <x-input-label :value="__('Phone')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->phone" disabled />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <!-- Gender -->
                        <div class="flex-1">
                            <x-input-label :value="__('Gender')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->gender ?? 'N/A'" disabled />
                        </div>
                        <!-- Birthday -->
                        <div class="flex-1">
                            <x-input-label :value="__('Birthday')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->birthday" disabled />
                        </div>
                    </div>

                    <div>
                        <x-input-label :value="__('Address')" />
                        <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->address" disabled />
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <!-- Country -->
                        <div class="flex-1">
                            <x-input-label :value="__('Country')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->country" disabled />
                        </div>
                        <!-- State -->
                        <div class="flex-1">
                            <x-input-label :value="__('State')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->state" disabled />
                        </div>
                        <!-- ZIP Code -->
                        <div class="flex-1">
                            <x-input-label :value="__('ZIP Code')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->zip" disabled />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <!-- Social Security ID -->
                        <div class="flex-1">
                            <x-input-label :value="__('Social Security ID')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->social_security_id" disabled />
                        </div>
                        <!-- Taxpayer ID -->
                        <div class="flex-1">
                            <x-input-label :value="__('Taxpayer ID')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->taxpayer_id" disabled />
                        </div>
                        <!-- Healthcare Insurance ID -->
                        <div class="flex-1">
                            <x-input-label :value="__('Healthcare Insurance ID')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->health_insurance_id" disabled />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <!-- Housing/Savings ID -->
                        <div class="flex-1">
                            <x-input-label :value="__('Housing/Savings ID')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->savings_id" disabled />
                        </div>
                        <!-- Bank Name -->
                        <div class="flex-1">
                            <x-input-label :value="__('Bank Name')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->bank_name" disabled />
                        </div>
                        <!-- Bank Account Number -->
                        <div class="flex-1">
                            <x-input-label :value="__('Bank Account Number')" />
                            <x-text-input class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70" type="text" :value="$user->bank_account_number" disabled />
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center gap-4 mt-4">
                            <!-- Job Type -->
                            <div class="flex-1">
                                <x-input-label for="job_type" :value="__('Job Type')" />
                                <x-text-input id="job_type" name="job_type" type="text" class="mt-1 block w-full" :value="$user->job_type"  />
                                    <x-input-error class="mt-2" :messages="$errors->get('job_type')" />
                            </div>

                            <!-- Employment Status -->
                            <div class="flex-1">
                                <x-input-label for="employment_status" :value="__('Employment Status')" />
                                <select id="employment_status" name="employment_status" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="Full-time" {{ ($user->employment_status == 'Full-time') ? 'selected' : '' }}>Full-time</option>
                                    <option value="Part-time" {{ ($user->employment_status == 'Part-time') ? 'selected' : '' }}>Part-time</option>
                                    <option value="Contractual" {{ ($user->employment_status == 'Contractual') ? 'selected' : '' }}>Contractual</option>
                                    <option value="Intern" {{ ($user->employment_status == 'Intern') ? 'selected' : '' }}>Intern</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('employment_status')" />
                            </div>

                            <!-- Hourly Rate -->
                            <div class="flex-1">
                                <x-input-label for="hourly_rate" :value="__('Hourly Rate')" />
                                <x-text-input id="hourly_rate" name="hourly_rate" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$user->hourly_rate"  />
                                <x-input-error class="mt-2" :messages="$errors->get('hourly_rate')" />
                            </div>
                        </div>
                        <div class="flex justify-between gap-4 mt-4">
                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Update User') }}</x-primary-button>
                                @if (session('update_success'))
                                    <p class="text-sm text-green-600 dark:text-green-400">{{ session('update_success') }}</p>
                                @endif
                            </div>

                            <!-- Admin Checkbox -->
                            <div class="flex items-center">
                                <input id="is_admin" name="is_admin" type="checkbox" value="1"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 {{ $user->id === Auth::id() ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-70' : '' }}"
                                {{ $user->is_admin ? 'checked' : '' }}
                                {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                <x-input-label for="is_admin" :value="__('Set user as admin')" class="ml-2" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>