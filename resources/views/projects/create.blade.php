<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Project') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Create Project</h1>

        <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block font-semibold">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                       class="w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block font-semibold">Description</label>
                <textarea name="description" class="w-full border rounded p-2">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block font-semibold">Status</label>
                <select name="status" class="w-full border rounded p-2" required>
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="On Hold">On Hold</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-semibold">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full border rounded p-2">
                </div>
            </div>

            {{-- Optional: assign users at creation time --}}
            <div>
                <label class="block font-semibold">Assign Users (hold Ctrl/Cmd to multi-select)</label>
                <select name="user_ids[]" multiple class="w-full border rounded p-2">
                    @forelse($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                    @empty
                        <option disabled>No users available</option>
                    @endforelse
                </select>
                <p class="text-sm text-gray-500 mt-1">Optional — assign users now or later.</p>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Save Project
            </button>
        </form>
    </div>
</x-app-layout>
