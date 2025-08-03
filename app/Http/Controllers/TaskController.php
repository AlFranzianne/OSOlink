<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function addTask(Request $request)
    {
        $incomingFields = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['description'] = strip_tags($incomingFields['description']);
        $incomingFields['user_id'] = auth('web')->id();

        Task::create($incomingFields);
        return redirect('/');
    }

    public function editTask(Task $task, Request $request)
    {
        if (auth('web')->user()->id !== $task->user_id) {
            return redirect('/');
        }

        $incomingFields = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['description'] = strip_tags($incomingFields['description']);

        $task->update($incomingFields);
        return redirect('/');
    }

    public function deleteTask(Task $task)
    {
        if (auth('web')->user()->id === $task->user_id) {
            $task->delete();
        }

        return redirect('/');
    }
}
