<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\ProjectPermission;
use App\Models\Comment;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // List projects
    public function index()
    {
        $user = auth()->user();

        if ($user->is_admin) {
            $projects = Project::with('creator', 'users')->get();
        } else {
            $projects = Project::with('creator', 'users')
                ->where(function ($query) use ($user) {
                    $query->whereHas('users', fn($q) => $q->where('users.id', $user->id))
                          ->orWhere('status', 'Completed')
                          ->orWhereDoesntHave('users');
                })
                ->get();
        }

        return view('projects.index', compact('projects'));
    }

    // Show create form (admin only)
    public function create()
    {
        $users = User::where('is_active', true)->where('is_admin', false)->get();
        return view('projects.create', compact('users'));
    }

    // Store project
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Not Started,In Progress,On Hold,Completed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'created_by' => Auth::id(),
        ]);

        if (!empty($validated['user_ids'])) {
            $project->users()->sync($validated['user_ids']);
        }

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Project created successfully.');
    }

    // Show project
    public function show(Project $project)
    {
        $user = auth()->user();

        if ($user->is_admin || 
            $project->status === 'Completed' ||
            !$project->users()->exists() ||
            $project->users->contains($user->id)
        ) {
            $project->load([
                'users',
                'timeLogs.user',
                'comments.user',
                'comments.replies.user',
                'permissions.user',
                'creator'
            ]);
            return view('projects.show', compact('project'));
        }

        abort(403, 'You do not have access to this project.');
    }

    // Edit project
    public function edit(Project $project)
    {
        $users = User::where('is_active', true)->where('is_admin', false)->get();
        return view('projects.edit', compact('project', 'users'));
    }

    // Update project
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Not Started,In Progress,On Hold,Completed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ]);

        if (array_key_exists('user_ids', $validated)) {
            $project->users()->sync($validated['user_ids'] ?? []);
        }

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Project updated successfully.');
    }

    // Delete project
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    // Assign/remove users
    public function assignUser(Request $request, Project $project)
    {
        $validated = $request->validate(['user_id' => 'required|exists:users,id']);
        $project->users()->syncWithoutDetaching([$validated['user_id']]);
        return back()->with('success', 'User assigned.');
    }

    public function removeUser(Project $project, User $user)
    {
        $project->users()->detach($user->id);
        return back()->with('success', 'User removed.');
    }

    // Permissions
    public function setPermission(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:Viewer,Contributor,Manager',
        ]);

        ProjectPermission::updateOrCreate(
            ['project_id' => $project->id, 'user_id' => $validated['user_id']],
            ['role' => $validated['role']]
        );

        return back()->with('success', 'Permission updated.');
    }

    // Comments (allowed even if Completed)
    public function addComment(Request $request, Project $project)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $project->comments()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Comment added.');
    }

    // Add time log
    public function addTimeLog(Request $request, Project $project)
    {
        $validated = $request->validate([
            'hours' => 'required|numeric|min:0.1',
            'work_output' => 'required|string|max:2000',
            'date' => 'required|date',
        ]);

        $project->timeLogs()->create([
            'user_id' => Auth::id(),
            'hours' => $validated['hours'],
            'work_output' => $validated['work_output'],
            'date' => $validated['date'],
        ]);

        return back()->with('success', 'Time log added.');
    }

    // Join project
    public function join(Project $project)
    {
        $user = auth()->user();

        if ($project->users->contains($user->id)) {
            return back()->with('error', 'You are already in this project.');
        }

        if ($project->status === 'Completed') {
            return back()->with('error', 'Cannot join a completed project.');
        }

        $project->users()->attach($user->id);
        return back()->with('success', 'You joined the project.');
    }

    // Leave project
    public function leave(Project $project)
    {
        $user = auth()->user();

        if (!$project->users->contains($user->id)) {
            return back()->with('error', 'You are not part of this project.');
        }

        $project->users()->detach($user->id);
        return back()->with('success', 'You left the project.');
    }

    // Edit time log (show form)
    public function editTimeLog(Project $project, TimeLog $timeLog)
    {
        $user = auth()->user();
        if (!$user->is_admin && $timeLog->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
        return view('projects.edit-timelog', compact('project', 'timeLog'));
    }

    // Update time log
    public function updateTimeLog(Request $request, Project $project, TimeLog $timeLog)
    {
        $user = auth()->user();
        if (!$user->is_admin && $timeLog->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'hours' => 'required|numeric|min:0.1',
            'work_output' => 'required|string|max:2000',
            'date' => 'required|date',
        ]);

        $timeLog->update($validated);

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Time log updated.');
    }

    // Delete time log
    public function deleteTimeLog(Project $project, TimeLog $timeLog)
    {
        $user = auth()->user();
        if (!$user->is_admin && $timeLog->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $timeLog->delete();

        return back()->with('success', 'Time log deleted.');
    }
}