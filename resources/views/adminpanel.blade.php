<x-app-layout>
    <style>
        td, th {
            text-align: center !important;
            vertical-align: middle;
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Create New User -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
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
                        <label for="is_admin" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            Make this user an Admin
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Create User') }}</x-primary-button>
                        @if (session('success'))
                            <p class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Manage Users -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Manage Users') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __("Activate or deactivate user accounts.") }}
                    </p>
                </header>

                <div class="mt-6 overflow-x-auto">
                    <div class="flex flex-wrap items-center gap-3 mb-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <form method="GET" action="{{ route('adminpanel') }}" class="flex flex-wrap gap-3 w-full">
                            <!-- Search -->
                            <x-text-input 
                                type="text" 
                                name="search" 
                                placeholder="Search users..." 
                                value="{{ request('search') }}"
                                class="w-full sm:w-64"
                            />

                            <!-- Status Filter -->
                            <select name="status" 
                                class="w-full sm:w-40 rounded-lg border-gray-300 dark:border-gray-600 
                                    bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 
                                    focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>

                            <!-- Sort Field -->
                            <select name="sort" 
                                class="w-full sm:w-40 rounded-lg border-gray-300 dark:border-gray-600 
                                    bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 
                                    focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="name" {{ request('sort')=='name' ? 'selected' : '' }}>Name</option>
                                <option value="email" {{ request('sort')=='email' ? 'selected' : '' }}>Email</option>
                                <option value="created_at" {{ request('sort')=='created_at' ? 'selected' : '' }}>Date Created</option>
                            </select>

                            <!-- Sort Order -->
                            <select name="order" 
                                class="w-full sm:w-32 rounded-lg border-gray-300 dark:border-gray-600 
                                    bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 
                                    focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="asc" {{ request('order')=='asc' ? 'selected' : '' }}>Ascending</option>
                                <option value="desc" {{ request('order')=='desc' ? 'selected' : '' }}>Descending</option>
                            </select>

                            <!-- Apply Button -->
                            <x-primary-button>
                                Apply
                            </x-primary-button>
                        </form>
                    </div>
                    <table class="rounded-lg overflow-hidden w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Name</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Email</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Status</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-gray-200 dark:bg-gray-900">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-4 py-2 text-center text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                                    <td class="px-4 py-2 text-center text-sm">
                                        <span class="{{ $user->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <x-secondary-button type="submit">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </x-secondary-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Audit Logs -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg mt-6">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Audit Logs') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __("Track admin actions performed in the system.") }}
                    </p>
                </header>

                <!-- Filters -->
                <div class="mt-6 overflow-x-auto">
                    <div class="flex flex-wrap items-center gap-3 mb-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <form method="GET" action="{{ route('adminpanel') }}" class="flex flex-wrap gap-3 w-full">
                            <!-- User Filter -->
                            <x-text-input 
                                type="text" 
                                name="log_user" 
                                placeholder="Filter by user..." 
                                value="{{ request('log_user') }}" 
                                class="w-full sm:w-64"
                            />

                            <!-- Action Filter -->
                            <x-text-input 
                                type="text" 
                                name="log_action" 
                                placeholder="Filter by action..." 
                                value="{{ request('log_action') }}" 
                                class="w-full sm:w-64"
                            />

                            <!-- Sort Field -->
                            <select name="log_sort" 
                                class="w-full sm:w-40 rounded-lg border-gray-300 dark:border-gray-600 
                                    bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 
                                    focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="created_at" {{ request('log_sort')=='created_at' ? 'selected' : '' }}>Date</option>
                                <option value="action" {{ request('log_sort')=='action' ? 'selected' : '' }}>Action</option>
                            </select>

                            <!-- Sort Order -->
                            <select name="log_order" 
                                class="w-full sm:w-32 rounded-lg border-gray-300 dark:border-gray-600 
                                    bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 
                                    focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="desc" {{ request('log_order')=='desc' ? 'selected' : '' }}>Descending</option>
                                <option value="asc" {{ request('log_order')=='asc' ? 'selected' : '' }}>Ascending</option>
                            </select>

                            <!-- Apply Button -->
                            <x-primary-button>
                                Apply
                            </x-primary-button>
                        </form>
                    </div>

                    <!-- Table -->
                    <table class="rounded-lg overflow-hidden w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">User</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Action</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Target</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">When</th>
                            </tr>
                        </thead>
                        <tbody class="divide-gray-200 dark:bg-gray-900">
                            @foreach($logs as $log)
                                <tr>
                                    <td class="px-4 py-2 text-center text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->user->name ?? 'System' }}
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->action }}
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->target?->email }}
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $logs->appends(request()->except('logs_page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>