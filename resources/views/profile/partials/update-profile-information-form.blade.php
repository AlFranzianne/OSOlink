<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Profile Picture -->
    <x-input-label for="profile_picture" :value="__('Profile Picture')" class="mt-4"/>
    <div class="flex items-center gap-4 mt-1">
        @if(auth()->user()->profile_picture)
            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" width="100" class="rounded-full">
        @else
            <img src="{{ asset('images/default-avatar.jpg') }}" alt="Default Avatar" width="100" class="rounded-full">
        @endif
        <div class="flex flex-col items-start gap-2">
        <form method="POST" action="{{ route('profile.upload') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="profile_picture" accept="image/*" required class="mb-2 text-white">
            <div class="flex gap-2">
                <x-primary-button>{{ __('Upload') }}</x-primary-button>
            </form>
                @if(auth()->user()->profile_picture)
                    <form method="POST" action="{{ route('profile.remove') }}">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ __('Remove') }}</x-danger-button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="flex items-center gap-4 mt-4">
            <!-- First Name -->
            <div class="flex-1">
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required autocomplete="first_name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <!-- Middle Name -->
            <div class="flex-1">
                <x-input-label for="middle_name" :value="__('Middle Name')" />
                <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name', $user->middle_name)" autocomplete="middle_name" />
                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            </div>

            <!-- Last Name -->
            <div class="flex-1">
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required autocomplete="last_name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <div class="flex items-center gap-4 mt-4">
            <!-- Email -->
            <div class="flex-1">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Phone -->
            <div class="flex-1">
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="phone" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
        </div>

        <div class="flex items-center gap-4 mt-4">
            <!-- Gender -->
            <div class="flex-1">
                <x-input-label for="gender" :value="__('Gender')" />
                <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    <option value="Prefer not to say" {{ old('gender', $user->gender) == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
            </div>

            <!-- Birthday -->
            <div class="flex-1">
                <x-input-label for="birthday" :value="__('Birthday')" />
                <x-text-input id="birthday" name="birthday" type="date" class="mt-1 block w-full" :value="old('birthday', $user->birthday)" />
                <x-input-error class="mt-2" :messages="$errors->get('birthday')" />
            </div>
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" :value="__('Address')" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" autocomplete="address" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="flex items-center gap-4 mt-4">
            <!-- Country -->
            <div class="flex-1">
                <x-input-label for="country" :value="__('Country')" />
                <select id="country" name="country" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                    <option value="">Select Country</option>
                    @foreach($countries as $code => $country)
                        @if(isset($country['name']))
                            <option value="{{ $country['name'] }}"
                                {{ old('country', $user->country) == $country['name'] ? 'selected' : '' }}>
                                {{ $country['name'] }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('country')" />
            </div>

            <!-- State -->
            <div class="flex-1">
                <x-input-label for="state" :value="__('State')" />
                <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $user->state)" autocomplete="state" />
                <x-input-error class="mt-2" :messages="$errors->get('state')" />
            </div>

            <!-- ZIP Code -->
            <div class="flex-1">
                <x-input-label for="zip" :value="__('ZIP Code')" />
                <x-text-input id="zip" name="zip" type="text" class="mt-1 block w-full" :value="old('zip', $user->zip)" autocomplete="zip" />
                <x-input-error class="mt-2" :messages="$errors->get('zip')" />
            </div>
        </div>

        <div class="flex items-center gap-4 mt-4">
            <!-- Social Security ID -->
            <div class="flex-1">
                <x-input-label for="social_security_id" :value="__('Social Security ID')" />
                <x-text-input id="social_security_id" name="social_security_id" type="text" class="mt-1 block w-full" :value="old('social_security_id', $user->social_security_id)" autocomplete="social_security_id" />
                <x-input-error class="mt-2" :messages="$errors->get('social_security_id')" />
            </div>

            <!-- Taxpayer ID -->
            <div class="flex-1">
                <x-input-label for="taxpayer_id" :value="__('Taxpayer ID')" />
                <x-text-input id="taxpayer_id" name="taxpayer_id" type="text" class="mt-1 block w-full" :value="old('taxpayer_id', $user->taxpayer_id)" autocomplete="taxpayer_id" />
                <x-input-error class="mt-2" :messages="$errors->get('taxpayer_id')" />
            </div>

            <!-- Health Insurance ID -->
            <div class="flex-1">
                <x-input-label for="health_insurance_id" :value="__('Healthcare Insurance ID')" />
                <x-text-input id="health_insurance_id" name="health_insurance_id" type="text" class="mt-1 block w-full" :value="old('health_insurance_id', $user->health_insurance_id)" autocomplete="health_insurance_id" />
                <x-input-error class="mt-2" :messages="$errors->get('health_insurance_id')" />
            </div>
        </div>

        <div class="flex items-center gap-4 mt-4">
            <!-- Housing/Savings ID -->
            <div class="flex-1">
                <x-input-label for="savings_id" :value="__('Housing/Savings ID')" />
                <x-text-input id="savings_id" name="savings_id" type="text" class="mt-1 block w-full" :value="old('savings_id', $user->savings_id)" autocomplete="savings_id" />
                <x-input-error class="mt-2" :messages="$errors->get('savings_id')" />
            </div>

            <!-- Bank Name -->
            <div class="flex-1">
                <x-input-label for="bank_name" :value="__('Bank Name')" />
                <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" :value="old('bank_name', $user->bank_name)" autocomplete="bank_name" />
                <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
            </div>

            <!-- Bank Account Number -->
            <div class="flex-1">
                <x-input-label for="bank_account_number" :value="__('Bank Account Number')" />
                <x-text-input id="bank_account_number" name="bank_account_number" type="text" class="mt-1 block w-full" :value="old('bank_account_number', $user->bank_account_number)" autocomplete="bank_account_number" />
                <x-input-error class="mt-2" :messages="$errors->get('bank_account_number')" />
            </div>
        </div>

        <!-- Save -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('update_success'))
               <p class="text-sm text-green-600 dark:text-green-400">{{ session('update_success') }}</p>
            @elseif (session('create_success'))
               <p class="text-sm text-green-600 dark:text-green-400">{{ session('create_success') }}</p>
            @elseif (session('upload_success'))
               <p class="text-sm text-green-600 dark:text-green-400">{{ session('upload_success') }}</p>
            @elseif (session('remove_success'))
               <p class="text-sm text-green-600 dark:text-green-400">{{ session('remove_success') }}</p>
            @endif
        </div>
    </form>
</section>
