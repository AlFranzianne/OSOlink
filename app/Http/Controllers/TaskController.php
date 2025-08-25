<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('user')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $users = \App\Models\User::all();
        return view('tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $fields['title'] = strip_tags($fields['title']);
        $fields['description'] = strip_tags($fields['description'] ?? '');

        Task::create($fields);
        return redirect()->route('tasks.index');
    }

    public function edit(Task $task)
    {
        $users = \App\Models\User::all();
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $fields['title'] = strip_tags($fields['title']);
        $fields['description'] = strip_tags($fields['description'] ?? '');

        $task->update($fields);
        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }
}