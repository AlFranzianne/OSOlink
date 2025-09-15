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
                        <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200">
                            {{ $project->name }}
                        </h2>

                        {{-- Status --}}
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $project->status }}
                        </p>

                        {{-- Special label for completed projects visible to everyone --}}
                        @if(!$project->users->contains(auth()->id()) && $project->status === 'Completed' && !auth()->user()->is_admin)
                            <span class="inline-block mt-1 text-xs text-green-700 bg-green-100 px-2 py-0.5 rounded">
                                ✅ Completed Project – Public
                            </span>
                        @endif
                    </div>

                    <div class="flex space-x-3 items-center">
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
                        @else
                            {{-- Regular users: Join/Leave actions --}}
                            @if(!$project->users->contains(auth()->id()) && $project->status !== 'Completed')
                                {{-- Join if project has no assigned users --}}
                                @if($project->users->isEmpty())
                                    <form action="{{ route('projects.join', $project->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                                            Join
                                        </button>
                                    </form>
                                @endif
                            @elseif($project->users->contains(auth()->id()))
                                {{-- Leave if user is already assigned --}}
                                <form action="{{ route('projects.leave', $project->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                        class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600">
                                        Leave
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400">No projects found.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>