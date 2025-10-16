<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(auth()->user()->is_admin)
                <!-- Admin Stats -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Admin Dashboard</h2>
                    @php
                        $totalProjects = \App\Models\Project::count();
                        $totalUsers = \App\Models\User::count();
                        $notStarted = \App\Models\Project::where('status', 'Not Started')->count();
                        $inProgress = \App\Models\Project::where('status', 'In Progress')->count();
                        $onHold = \App\Models\Project::where('status', 'On Hold')->count();
                        $completed = \App\Models\Project::where('status', 'Completed')->count();
                        $overdue = \App\Models\Project::where('status', '!=', 'Completed')
                            ->whereDate('end_date', '<', now())->count();
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                        <div class="flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 rounded-xl p-6 shadow">
                            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalProjects }}</div>
                            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">Total Projects</div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 rounded-xl p-6 shadow">
                            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalUsers }}</div>
                            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">Total Users</div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-yellow-100 dark:bg-yellow-900 rounded-xl p-6 shadow">
                            <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $notStarted }}</div>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">Not Started</div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-blue-100 dark:bg-blue-900 rounded-xl p-6 shadow">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $inProgress }}</div>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">In Progress</div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-red-100 dark:bg-red-900 rounded-xl p-6 shadow">
                            <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $onHold }}</div>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">On Hold</div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-green-100 dark:bg-green-900 rounded-xl p-6 shadow">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $completed }}</div>
                            <div class="mt-2 text-sm text-green-700 dark:text-green-300">Completed</div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-pink-100 dark:bg-pink-900 rounded-xl p-6 shadow">
                            <div class="text-3xl font-bold text-pink-600 dark:text-pink-400">{{ $overdue }}</div>
                            <div class="mt-2 text-sm text-pink-700 dark:text-pink-300">Overdue</div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Assigned Projects Overview (for regular users) -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Your Projects</h2>
                    @php
                        $assignedProjects = Auth::user()->projects->where('status', '!=', 'Completed');
                        $totalAssigned = $assignedProjects->count();
                        $notStarted = $assignedProjects->where('status', 'Not Started')->count();
                        $inProgress = $assignedProjects->where('status', 'In Progress')->count();
                        $onHold = $assignedProjects->where('status', 'On Hold')->count();
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 mb-4">
                        <div class="flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 rounded-xl p-6 shadow">
                            <div class="text-2xl font-bold">{{ $totalAssigned }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Assigned</div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-yellow-100 dark:bg-yellow-900 rounded-xl p-6 shadow">
                            <div class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ $notStarted }}</div>
                            <div class="text-xs text-yellow-700 dark:text-yellow-300">Not Started</div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-blue-100 dark:bg-blue-900 rounded-xl p-6 shadow">
                            <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $inProgress }}</div>
                            <div class="text-xs text-blue-700 dark:text-blue-300">In Progress</div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-red-100 dark:bg-red-900 rounded-xl p-6 shadow">
                            <div class="text-lg font-semibold text-red-600 dark:text-red-400">{{ $onHold }}</div>
                            <div class="text-xs text-red-700 dark:text-red-300">On Hold</div>
                        </div>
                    </div>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($assignedProjects as $project)
                            <li class="py-2 flex justify-between items-center">
                                <a href="{{ route('projects.show', $project->id) }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                    {{ $project->name }}
                                </a>
                                <span class="text-xs px-2 py-1 rounded {{ 
                                    $project->status == 'Not Started' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 
                                    ($project->status == 'In Progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 
                                    ($project->status == 'On Hold' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300')) 
                                }}">
                                    {{ $project->status }}
                                </span>
                            </li>
                        @empty
                            <li class="py-2 text-gray-500 dark:text-gray-400">No assigned projects.</li>
                        @endforelse
                    </ul>
                </div>
            @endif

            <!-- Recent Comments -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Recent Comments</h2>
                @php
                    $recentComments = auth()->user()->is_admin
                        ? \App\Models\Comment::latest()->take(5)->get()
                        : \App\Models\Comment::whereIn('project_id', Auth::user()->projects->pluck('id'))
                            ->latest()->take(5)->get();
                @endphp
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentComments as $comment)
                        <li class="py-2 flex justify-between items-center">
                            <div>
                                <a href="{{ route('projects.show', $comment->project_id) }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $comment->project->name }}
                                </a>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($comment->content, 40) }}</span>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->user->name }} · {{ $comment->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-gray-500 dark:text-gray-400">No recent comments.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Recent Time Logs -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Recent Time Logs</h2>
                @php
                    $recentTimeLogs = auth()->user()->is_admin
                        ? \App\Models\TimeLog::latest()->take(5)->get()
                        : \App\Models\TimeLog::whereIn('project_id', Auth::user()->projects->pluck('id'))
                            ->latest()->take(5)->get();
                @endphp
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentTimeLogs as $log)
                        <li class="py-2 flex justify-between items-center">
                            <div>
                                <a href="{{ route('projects.show', $log->project_id) }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $log->project->name }}
                                </a>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($log->work_output, 40) }}</span>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $log->hours }} hrs · {{ $log->date }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-gray-500 dark:text-gray-400">No recent time logs.</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>
</x-app-layout> 