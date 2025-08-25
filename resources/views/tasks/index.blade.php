<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Create Task Button -->
                <div class="mb-4">
                    <a href="{{ route('tasks.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent 
                              rounded-md font-semibold text-xs text-white uppercase tracking-widest 
                              hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 
                              focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + Create Task
                    </a>
                </div>

                <!-- Task List -->
                @if($tasks->count() > 0)
                    <table class="min-w-full border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border">Title</th>
                                <th class="px-4 py-2 border">Description</th>
                                <th class="px-4 py-2 border">Deadline</th>
                                <th class="px-4 py-2 border">Assigned User</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $task->title }}</td>
                                    <td class="px-4 py-2 border">{{ $task->description }}</td>
                                    <td class="px-4 py-2 border">{{ $task->deadline }}</td>
                                    <td class="px-4 py-2 border">{{ $task->user->name ?? 'Unassigned' }}</td>
                                    <td class="px-4 py-2 border capitalize">{{ str_replace('_',' ',$task->status) }}</td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('tasks.edit', $task) }}" 
                                           class="text-blue-600 hover:underline">Edit</a>
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:underline"
                                                    onclick="return confirm('Are you sure?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No tasks found. Create one above!</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>