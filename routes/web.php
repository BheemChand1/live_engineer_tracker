<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EngineerController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Engineer\EngineerDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        // If user is already logged in, redirect to appropriate dashboard
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('engineer.dashboard');
        }
    }
    // If not logged in, redirect to login page
    return redirect()->route('login');
});



// Redirect dashboard based on user role
Route::get('/dashboard', function () {
    if (Auth::check() && Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('engineer.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Engineer Management
    Route::resource('engineers', EngineerController::class);
    Route::post('engineers/{engineer}/toggle-status', [EngineerController::class, 'toggleStatus'])->name('engineers.toggle-status');
    
    // Task Management
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    Route::post('tasks/{task}/unassign', [TaskController::class, 'unassign'])->name('tasks.unassign');
    
    // Reports
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/attendance', [ReportsController::class, 'engineerAttendance'])->name('reports.attendance');
    Route::get('reports/tasks', [ReportsController::class, 'taskCompletion'])->name('reports.tasks');
    
    // Live Tracking
    Route::get('tracking', [DashboardController::class, 'liveTracking'])->name('tracking');
    Route::get('live-tracking', [DashboardController::class, 'liveTracking'])->name('live-tracking');
    Route::get('api/engineers/locations', [DashboardController::class, 'getEngineersLocations']);
});

// Engineer Routes
Route::middleware(['auth', 'engineer'])->prefix('engineer')->name('engineer.')->group(function () {
    Route::get('/dashboard', [EngineerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tasks', [EngineerDashboardController::class, 'tasks'])->name('tasks');
    Route::patch('/tasks/{task}/status', [EngineerDashboardController::class, 'updateTaskStatus'])->name('tasks.update-status');
    
    // Location tracking
    Route::post('/login', [EngineerDashboardController::class, 'recordLogin'])->name('login');
    Route::post('/logout', [EngineerDashboardController::class, 'recordLogout'])->name('logout');
    Route::post('/location', [EngineerDashboardController::class, 'updateLocation'])->name('location.update');
    Route::get('/location/history', [EngineerDashboardController::class, 'locationHistory'])->name('location.history');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
