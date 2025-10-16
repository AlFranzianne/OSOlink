<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DependentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LeaveController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/upload', [ProfileController::class, 'upload'])->name('profile.upload');
    Route::delete('/profile/remove', [ProfileController::class, 'remove'])->name('profile.remove');

    // Profile Dependents
    Route::get('/dependents/create', [DependentController::class, 'create'])->name('create-dependent');
    Route::get('/dependents/{dependent}/edit', [DependentController::class, 'edit'])->name('dependents.edit');
    Route::delete('/dependents/{dependent}', [DependentController::class, 'destroy'])->name('dependents.destroy');
    Route::post('/dependents', [DependentController::class, 'store'])->name('dependents.store');
    Route::get('/dependents', [DependentController::class, 'index'])->name('dependents.index');
    Route::put('/dependents/{dependent}', [DependentController::class, 'update'])->name('dependents.update');

    // Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects/{project}/comments', [ProjectController::class, 'addComment'])->name('projects.comments.store');
    Route::post('/projects/{project}/timelogs', [ProjectController::class, 'addTimeLog'])->name('projects.addTimeLog');

    Route::middleware('admin')->group(function () {
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::post('/projects/{project}/assign-user', [ProjectController::class, 'assignUser'])->name('projects.assignUser');
        Route::delete('/projects/{project}/remove-user/{user}', [ProjectController::class, 'removeUser'])->name('projects.removeUser');
        Route::post('/projects/{project}/set-permission', [ProjectController::class, 'setPermission'])->name('projects.setPermission');
    });

    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/timelogs/{timeLog}/edit', [ProjectController::class, 'editTimeLog'])->name('projects.editTimeLog');
    Route::put('/projects/{project}/timelogs/{timeLog}', [ProjectController::class, 'updateTimeLog'])->name('projects.updateTimeLog');
    Route::delete('/projects/{project}/timelogs/{timeLog}', [ProjectController::class, 'deleteTimeLog'])->name('projects.deleteTimeLog');

    // Payroll

    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll');
    Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
    Route::get('/payroll/{payroll}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
    Route::put('/payroll/{payroll}', [PayrollController::class, 'update'])->name('payroll.update');
    Route::delete('/payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');

    // Leaves
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{leave}/edit', [LeaveController::class, 'edit'])->name('leaves.edit');
    Route::put('/leaves/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::resource('leaves', \App\Http\Controllers\LeaveController::class);
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    Route::post('/leaves/{leave}/pending', [LeaveController::class, 'pending'])->name('leaves.pending');
    Route::get('/leaves/{id}', [LeaveController::class, 'show'])->name('leaves.show');
});

// Admin panel routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/adminpanel/admin', [AdminController::class, 'index'])->name('adminpanel.admin');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::patch('/admin/users/{user}/toggle', [AdminController::class, 'toggleStatus'])->name('admin.users.toggle');
    Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
    Route::get('/admin/users/{user}', [AdminController::class, 'show'])->name('admin.users.show');
    Route::patch('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
});

require __DIR__.'/auth.php';
