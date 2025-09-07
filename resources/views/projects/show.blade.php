<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $project->name }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">{{ $project->name }}</h1>

        <p class="mb-2"><strong>Status:</strong> {{ $project->status }}</p>
        <p class="mb-2"><strong>Start:</strong> {{ $project->start_date }}</p>
        <p class="mb-2"><strong>End:</strong> {{ $project->end_date }}</p>
        <p class="mb-4"><strong>Description:</strong> {{ $project->description }}</p>

        {{-- Admin-only actions --}}
        @if(auth()->user()->is_admin)
            <div class="flex space-x-3 mb-6">
                <a href="{{ route('projects.edit', $project->id) }}" 
                   class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">Edit</a>

                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" 
                      onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        @endif

        {{-- Assigned users --}}
        <h3 class="text-lg font-semibold mt-6 mb-2">Assigned Users</h3>
        <ul class="space-y-2 mb-4">
            @foreach($project->users as $user)
                <li class="flex justify-between items-center border rounded p-3">
                    <div>
                        <div class="font-medium">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                    </div>

                    @if(auth()->user()->is_admin)
                        <form method="POST" action="{{ route('projects.removeUser', [$project->id, $user->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline" onclick="return confirm('Remove user from project?')">Remove</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>

        {{-- Assign a user (admin only) --}}
        @if(auth()->user()->is_admin)
            <form method="POST" action="{{ route('projects.assignUser', $project->id) }}" class="mt-2 flex gap-2 mb-6">
                @csrf
                <select name="user_id" class="border rounded p-2">
                    @foreach(\App\Models\User::where('is_active', true)->where('is_admin', false)->get() as $user)
                        @unless($project->users->contains($user->id))
                            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                        @endunless
                    @endforeach
                </select>
                <button class="px-3 py-2 bg-blue-600 text-white rounded">Assign</button>
            </form>
        @endif

        {{-- Join/Leave project for non-admin users --}}
        @unless(auth()->user()->is_admin)
            <div class="mb-6">
                @if($project->users->contains(auth()->user()->id))
                    <form method="POST" action="{{ route('projects.leave', $project->id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Leave Project
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('projects.join', $project->id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Join Project
                        </button>
                    </form>
                @endif
            </div>
        @endunless

        {{-- Comments --}}
        <h3 class="text-lg font-semibold mt-8 mb-2">Comments</h3>
        <div class="space-y-3">
            @forelse($project->comments->sortByDesc('created_at') as $comment)
                <div class="border rounded p-3">
                    <div class="text-sm text-gray-600">{{ $comment->user->name }} · <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span></div>
                    <div class="mt-2">{{ $comment->content }}</div>
                </div>
            @empty
                <p class="text-gray-500">No comments yet.</p>
            @endforelse
        </div>

        {{-- Add comment form (any auth user) --}}
        <form method="POST" action="{{ route('projects.comments.store', $project->id) }}" class="mt-4">
            @csrf
            <label class="block font-semibold">Add a comment</label>
            <textarea name="content" rows="3" class="w-full border rounded p-2" required>{{ old('content') }}</textarea>
            <div class="mt-2">
                <button class="px-3 py-2 bg-indigo-600 text-white rounded">Post Comment</button>
            </div>
        </form>

        <div class="mt-6 flex gap-4">
            <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Back</a>
            @if(auth()->user()->is_admin)
                <a href="{{ route('projects.edit', $project->id) }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Edit</a>
            @endif
        </div>
    </div>
</x-app-layout>