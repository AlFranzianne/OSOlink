<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Task') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('tasks.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" required 
                               class="w-full border-gray-300 rounded mt-1">
                        @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Description</label>
                        <textarea name="description" class="w-full border-gray-300 rounded mt-1">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Deadline</label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" 
                               class="w-full border-gray-300 rounded mt-1">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Assign To</label>
                        <select name="user_id" required class="w-full border-gray-300 rounded mt-1">
                            <option value="">-- Select User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Status</label>
                        <select name="status" required class="w-full border-gray-300 rounded mt-1">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-gray-300 rounded mr-2">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Save Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>