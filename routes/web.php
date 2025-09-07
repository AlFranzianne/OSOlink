<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Project routes
     * - Everyone (auth users) can view projects (index, show)
     * - Only admins can create, edit, delete
     */
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

    Route::middleware('admin')->group(function () {
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Admin-only assignments and permissions
        Route::post('/projects/{project}/assign-user', [ProjectController::class, 'assignUser'])->name('projects.assignUser');
        Route::delete('/projects/{project}/remove-user/{user}', [ProjectController::class, 'removeUser'])->name('projects.removeUser');
        Route::post('/projects/{project}/set-permission', [ProjectController::class, 'setPermission'])->name('projects.setPermission');
    });

    // Comments: any authenticated user can post
    Route::post('/projects/{project}/comments', [ProjectController::class, 'addComment'])->name('projects.comments.store');

    // Join/Leave unassigned projects (non-admin users)
    Route::post('/projects/{project}/join', [ProjectController::class, 'join'])->name('projects.join');
    Route::post('/projects/{project}/leave', [ProjectController::class, 'leave'])->name('projects.leave');

    // Show project (keep LAST to avoid conflicts)
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
});

// Admin panel + user management
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/adminpanel', [AdminController::class, 'index'])->name('adminpanel');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::patch('/admin/users/{user}/toggle', [AdminController::class, 'toggleStatus'])->name('admin.users.toggle');
    Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
});

require __DIR__.'/auth.php';
