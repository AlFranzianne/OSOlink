<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex items-start justify-between mb-4">
                    <div class="space-y-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $project->name }}</h2>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status: {{ $project->status }}</label>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date: {{ $project->start_date }}</label>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date: {{ $project->end_date }}</label>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description: {{ $project->description }}</label>
                    </div>
                    @if(auth()->user()->is_admin)
                        <div class="flex space-x-3 ml-6">
                            <x-secondary-button>
                                <a href="{{ route('projects.edit', $project->id) }}">Edit</a>
                            </x-secondary-button>
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" 
                                onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <x-secondary-button type="submit">
                                    Delete
                                </x-secondary-button>
                            </form>
                        </div>
                    @endif
                </div>
                
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Assigned Users</h2>
                <ul class="space-y-2 mb-4">
                    @foreach($project->users as $user)
                        <li class="flex justify-between items-center border-gray-300 dark:border-gray-700 border rounded-md p-3 dark:bg-gray-900 dark:text-gray-300">
                            <div>
                                <div class="font-medium">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                            @if(auth()->user()->is_admin && $project->status !== 'Completed')
                                <form method="POST" action="{{ route('projects.removeUser', [$project->id, $user->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline" onclick="return confirm('Remove user from project?')">Remove</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>

                {{-- Assign a user (admin only, not allowed if completed) --}}
                @if(auth()->user()->is_admin && $project->status !== 'Completed')
                    <form method="POST" action="{{ route('projects.assignUser', $project->id) }}" class="mt-2 flex gap-2 mb-6">
                        @csrf
                        <select name="user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @foreach(\App\Models\User::where('is_active', true)->where('is_admin', false)->get() as $user)
                                @unless($project->users->contains($user->id))
                                    <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                                @endunless
                            @endforeach
                        </select>
                        <x-primary-button>Assign</x-primary-button>
                    </form>
                @endif

                {{-- Join/Leave project for non-admin users (disabled if completed) --}}
                @unless(auth()->user()->is_admin)
                    <div class="mb-6">
                        @if($project->status !== 'Completed')
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
                        @else
                            <p class="text-gray-500">This project is completed. You cannot join or leave.</p>
                        @endif
                    </div>
                @endunless

                {{-- Comments --}}
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Comments</h2>
                <div class="space-y-3">
                    @forelse($project->comments->whereNull('parent_id')->sortByDesc('created_at') as $comment)
                        <div class="border-gray-300 dark:border-gray-700 border rounded-md p-3 dark:bg-gray-900 dark:text-gray-300">
                            <div class="text-sm text-gray-600">
                                {{ $comment->user->name }} · 
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="mt-2">{{ $comment->content }}</div>

                            {{-- Replies --}}
                            <div class="ml-6 mt-3 space-y-2">
                                @foreach($comment->replies()->orderBy('created_at')->get() as $reply)
                                    <div class="border-l-2 pl-3">
                                        <div class="text-sm text-gray-600">
                                            {{ $reply->user->name }} · 
                                            <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="mt-1">{{ $reply->content }}</div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Reply form --}}
                            <form method="POST" action="{{ route('projects.comments.store', $project->id) }}" class="mt-2 ml-6">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <textarea name="content" rows="2" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required></textarea>
                                <div class="mt-1">
                                    <x-primary-button>Reply</x-primary-button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-500">No comments yet.</p>
                    @endforelse
                </div>

                {{-- Add comment form (always enabled) --}}
                <form method="POST" action="{{ route('projects.comments.store', $project->id) }}" class="mt-4">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add a comment</h2>
                    <textarea name="content" rows="3" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('content') }}</textarea>
                    <div class="mt-2 flex items-center gap-3">
                        <x-primary-button>Post Comment</x-primary-button>

                        <!-- Back Button -->
                        <a href="{{ route('projects.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-500 dark:bg-gray-700 border border-transparent 
                           rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 
                           dark:hover:bg-gray-600 focus:bg-gray-600 dark:focus:bg-gray-600 focus:outline-none 
                           focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back
                        </a>
                    </div>
                </form>               
            </div>
        </div>
    </div>
</x-app-layout>