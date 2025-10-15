<section>
    <header class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Your Dependents') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __("Update your account's dependents information.") }}
            </p>
        </div>
        <a href="{{ route('create-dependent') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 border border-transparent 
                           rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 
                           dark:hover:bg-indigo-800 focus:bg-indigo-700 dark:focus:bg-indigo-800 focus:outline-none 
                           focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            + Create Dependent
        </a>
    </header>

    <div class="mt-6 overflow-x-auto">
        <table class="rounded-lg overflow-hidden w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Name</th>
                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Relationship</th>
                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Date of Birth</th>
                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-gray-200 dark:bg-gray-900 text-sm">
                @forelse ($dependents as $dependent)
                    <tr>
                        <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">{{ $dependent->name }}</td>
                        <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">{{ $dependent->relationship }}</td>
                        <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($dependent->date_of_birth)->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-200">
                            <a href="{{ route('dependents.edit', $dependent) }}" class="text-green-600 dark:text-green-400 hover:underline">Edit</a>
                            <form action="{{ route('dependents.destroy', $dependent) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">No dependents found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>