<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <!-- Header -->
                <header class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Projects') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Manage projects and view their details.") }}
                        </p>
                    </div>

                    @if(auth()->user()->is_admin)
                        <x-secondary-button>
                            <a href="{{ route('projects.create') }}"> Create Project </a>
                        </x-secondary-button>
                    @endif
                </header>

                <!-- Projects List -->
                @forelse($projects as $project)
                    <div class="mt-4 p-4 rounded-lg overflow-hidden shadow flex justify-between items-center dark:bg-gray-900">
                        <div>
                            <!-- Project name -->
                            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200">
                                {{ $project->name }}
                            </h2>

                            <!-- Project status -->
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $project->status }}
                            </p>

                            <!-- Completed projects visible to all -->
                            @if(!$project->users->contains(auth()->id()) && $project->status === 'Completed' && !auth()->user()->is_admin)
                                <span class="inline-block mt-1 text-xs text-green-700 bg-green-100 px-2 py-0.5 rounded">
                                    ✅ Completed Project – Public
                                </span>
                            @endif
                        </div>

                        <div class="flex space-x-3 items-center">
                            <!-- View -->
                            <a href="{{ route('projects.show', $project->id) }}" 
                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                View
                            </a>

                            <!-- Edit for admins only -->
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('projects.edit', $project->id) }}" 
                                   class="text-green-600 dark:text-green-400 hover:underline">
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 dark:text-gray-400">No projects found.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>