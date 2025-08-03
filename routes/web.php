<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Models\Task;

Route::get('/', function () {
    $tasks = Task::where('user_id', auth('web')->id())->get();
    return view('home', ['tasks' => $tasks]);
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/login', [UserController::class, 'login']);

Route::post('/add-task', [TaskController::class, 'addTask']);
Route::put('/edit-task/{task}', [TaskController::class, 'editTask']);
Route::delete('/delete-task/{task}', [TaskController::class, 'deleteTask']);