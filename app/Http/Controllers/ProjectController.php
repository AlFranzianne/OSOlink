<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\ProjectPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // List projects: only assigned or unassigned projects for the user
    public function index()
    {
        $user = auth()->user();

        $projects = Project::with('creator', 'users')
            ->where(function ($query) use ($user) {
                $query->whereDoesntHave('users') // unassigned
                      ->orWhereHas('users', fn($q) => $q->where('users.id', $user->id));
            })->get();

        return view('projects.index', compact('projects'));
    }

    // Show create form (admin only)
    public function create()
    {
        $users = User::where('is_active', true)->where('is_admin', false)->get();
        return view('projects.create', compact('users'));
    }

    // Store project (admin only)
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

        return redirect()->route('projects.show', $project->id)->with('success', 'Project created successfully.');
    }

    // Show project details
    public function show(Project $project)
    {
        $user = auth()->user();

        if ($project->users()->exists() && !$user->is_admin && !$project->users->contains($user->id)) {
            abort(403, 'You do not have access to this project.');
        }

        $project->load('users', 'timeLogs.user', 'comments.user', 'permissions.user', 'creator');
        return view('projects.show', compact('project'));
    }

    // Edit project (admin only)
    public function edit(Project $project)
    {
        $users = User::where('is_active', true)->where('is_admin', false)->get();
        return view('projects.edit', compact('project', 'users'));
    }

    // Update project (admin only)
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

        return redirect()->route('projects.show', $project->id)->with('success', 'Project updated successfully.');
    }

    // Delete project (admin only)
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    // Assign/remove user (admin only)
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

    // Permissions (admin only)
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

    // Comments (any auth user)
    public function addComment(Request $request, Project $project)
    {
        $validated = $request->validate(['content' => 'required|string|max:2000']);

        $project->comments()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Comment added.');
    }

    // Join/Leave unassigned projects (non-admin)
    public function join(Project $project)
    {
        $user = auth()->user();

        if ($project->users()->exists()) {
            return back()->with('error', 'Cannot join a project with assigned users.');
        }

        $project->users()->attach($user->id);
        return back()->with('success', 'You joined the project.');
    }

    public function leave(Project $project)
    {
        $user = auth()->user();

        if ($project->users()->exists()) {
            return back()->with('error', 'Cannot leave a project with assigned users.');
        }

        $project->users()->detach($user->id);
        return back()->with('success', 'You left the project.');
    }
}
