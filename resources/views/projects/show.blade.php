<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <!-- Project Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="space-y-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $project->name }}
                        </h2>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Status: {{ $project->status }}
                        </label>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Start Date: 
                            {{ \Carbon\Carbon::parse($project->start_date)->format('F j, Y') }}
                        </label>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            End Date: 
                            {{ \Carbon\Carbon::parse($project->end_date)->format('F j, Y') }}
                        </label>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Description: {{ $project->description }}
                        </label>
                    </div>

                    <!-- Edit for Admins -->
                    @if(auth()->user()->is_admin)
                        <div class="flex space-x-3 ml-6">
                            <x-secondary-button>
                                <a href="{{ route('projects.edit', $project->id) }}">Edit</a>
                            </x-secondary-button>
                        </div>
                    @endif
                </div>

                <!-- Assigned Users -->
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Assigned Users</h2>
                <ul class="space-y-2 mb-4">
                    @foreach($project->users as $user)
                        <li class="flex justify-between items-center border-gray-300 dark:border-gray-700 border rounded-md p-3 dark:bg-gray-900 dark:text-gray-300">
                            <div>
                                <div class="font-medium">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }} | {{ $user->job_type }}</div>
                            </div>

                            <!-- Admin can remove users (if project not completed) -->
                            @if(auth()->user()->is_admin && $project->status !== 'Completed')
                                <form method="POST" action="{{ route('projects.removeUser', [$project->id, $user->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline" onclick="return confirm('Remove user from project?')">
                                        Remove
                                    </button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <!-- Assign User (Admin only, if not completed) -->
                @if(auth()->user()->is_admin && $project->status !== 'Completed')
                    <form method="POST" action="{{ route('projects.assignUser', $project->id) }}" class="mt-2 flex gap-2 mb-6">
                        @csrf
                        <select name="user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @foreach(\App\Models\User::where('is_active', true)->where('is_admin', false)->get() as $user)
                                @unless($project->users->contains($user->id))
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }} — {{ $user->email }} — {{ $user->job_type }}</option>
                                @endunless
                            @endforeach
                        </select>
                        <x-primary-button>Assign</x-primary-button>
                    </form>
                @endif

                <div x-data="{ section: '{{ request('section', request('edit_timelog') ? 'timelogs' : 'comments') }}' }">
                    <!-- Toggle Buttons -->
                    <div class="flex gap-4 mb-6">
                        <button 
                            x-on:click="section = 'comments'"
                            class="px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest
                                focus:outline-none"
                            :class="section === 'comments' 
                                ? 'bg-indigo-600 text-white hover:bg-indigo-700' 
                                : 'bg-gray-600 text-white hover:bg-gray-700'"
                        >
                            Comments
                        </button>
                        <button 
                            x-on:click="section = 'timelogs'"
                            class="px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest
                                focus:outline-none"
                            :class="section === 'timelogs' 
                                ? 'bg-indigo-600 text-white hover:bg-indigo-700' 
                                : 'bg-gray-600 text-white hover:bg-gray-700'"
                        >
                            Time Logs
                        </button>
                    </div>

                    <!-- Comments Section -->
                    <div x-show="section === 'comments'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Comments</h2>
                        <div class="space-y-3">
                            @forelse($project->comments->whereNull('parent_id')->sortByDesc('created_at') as $comment)
                                <div class="border-gray-300 dark:border-gray-700 border rounded-md p-3 dark:bg-gray-900 dark:text-gray-300">
                                    <!-- Comment header -->
                                    <div class="text-sm text-gray-400">
                                        {{ $comment->user->first_name }} {{ $comment->user->middle_name }} {{ $comment->user->last_name }} · {{ $comment->user->job_type }} · 
                                        <span class="text-xs text-gray-400">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <!-- Comment body -->
                                    <div class="mt-2">{{ $comment->content }}</div>

                                    <!-- Replies -->
                                    <div class="ml-6 mt-3 space-y-2">
                                        @foreach($comment->replies()->orderBy('created_at')->get() as $reply)
                                            <div class="border-l-2 pl-3">
                                                <div class="text-sm text-gray-400">
                                                    {{ $reply->user->first_name }} {{ $reply->user->middle_name }} {{ $reply->user->last_name }} · {{ $reply->user->job_type }} · 
                                                    <span class="text-xs text-gray-400">
                                                        {{ $reply->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <div class="mt-1">{{ $reply->content }}</div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Reply form -->
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

                        <!-- Add comment form -->
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

                    <!-- Time Logs Section -->
                    <div x-show="section === 'timelogs'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Time Logs</h2>
                        <div class="space-y-4">
                            @foreach($project->timeLogs->sortByDesc('date') as $timeLog)
                                <div class="border-gray-300 dark:border-gray-700 border rounded-md p-4 dark:bg-gray-900 dark:text-gray-300">
                                    @if(request('edit_timelog') == $timeLog->id)
                                        <!-- Inline Edit Form -->
                                        <form method="POST" action="{{ route('projects.updateTimeLog', [$project->id, $timeLog->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <input type="number" name="hours" value="{{ old('hours', $timeLog->hours) }}" step="0.1" required 
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                                <input type="date" name="date" value="{{ old('date', $timeLog->date) }}" required min="{{ $project->start_date }}" max="{{ $project->end_date }}"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                                <textarea name="work_output" required 
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:col-span-3">{{ old('work_output', $timeLog->work_output) }}</textarea>
                                            </div>
                                            <div class="mt-4 flex gap-3">
                                                <x-primary-button>Update</x-primary-button>
                                                <a href="{{ route('projects.show', [$project->id, 'section' => 'timelogs']) }}" class="text-gray-500">Cancel</a>
                                            </div>
                                        </form>
                                    @else
                                        <!-- Normal Display -->
                                        <div class="flex justify-between items-center">
                                            <div class="text-sm text-gray-400">
                                                {{ $timeLog->user->first_name }} {{ $timeLog->user->middle_name }} {{ $timeLog->user->last_name }} · {{ $timeLog->user->job_type }} · 
                                                <span class="text-xs text-gray-400">
                                                    @if($timeLog->date)
                                                        {{ \Carbon\Carbon::parse($timeLog->date)->format('F j, Y') }}
                                                    @else
                                                        <em>No date</em>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="text-sm font-medium">
                                                {{ $timeLog->hours }} hours
                                            </div>
                                        </div>
                                        <div class="mt-2 text-sm">
                                            {{ $timeLog->work_output }}
                                        </div>
                                        @if(auth()->user()->is_admin || auth()->id() === $timeLog->user_id)
                                            <div class="mt-2 flex">
                                                <a href="{{ route('projects.show', [$project->id, 'edit_timelog' => $timeLog->id]) }}" class="text-blue-500">Edit</a>
                                                <form action="{{ route('projects.deleteTimeLog', [$project->id, $timeLog->id]) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 ml-6" onclick="return confirm('Delete this time log?')">Delete</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Add Time Log Form (Admins and Project Members) -->
                        @if(auth()->user()->is_admin || $project->users->contains(auth()->user()->id))
                            <form method="POST" action="{{ route('projects.addTimeLog', $project->id) }}" class="mt-6">
                                @csrf
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add a Time Log</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <input type="number" name="hours" step="0.1" required placeholder="Hours worked" 
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <input type="date" name="date" required min="{{ $project->start_date }}" max="{{ $project->end_date }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <textarea name="work_output" required placeholder="Work details" 
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:col-span-3"></textarea>
                                </div>
                                <div class="mt-4">
                                    <x-primary-button>Add Time Log</x-primary-button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>