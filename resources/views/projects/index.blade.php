<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Projects') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6">
        {{-- Page Title --}}
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Projects</h1>

            {{-- Admin-only: Create Project Button --}}
            @if(auth()->user()->is_admin)
                <a href="{{ route('projects.create') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    + Create Project
                </a>
            @endif
        </div>

        {{-- Projects List --}}
        <div class="space-y-3">
            @forelse($projects as $project)
                <div class="border p-4 rounded shadow flex justify-between items-center bg-white dark:bg-gray-800">
                    <div>
                        <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200">{{ $project->name }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $project->status }}</p>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('projects.show', $project->id) }}" 
                           class="text-blue-600 dark:text-blue-400 hover:underline">View</a>

                        {{-- Admin-only: Edit & Delete --}}
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('projects.edit', $project->id) }}" 
                               class="text-green-600 dark:text-green-400 hover:underline">Edit</a>

                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this project?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 dark:text-red-400 hover:underline">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400">No projects found.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>