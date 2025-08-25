<?php

use App\Http\Controllers\Api\EngineerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Engineer Mobile API Routes
Route::middleware(['auth:sanctum'])->prefix('engineer')->name('api.engineer.')->group(function () {
    // Location tracking
    Route::post('/location', [EngineerController::class, 'updateLocation'])->name('location.update');
    Route::get('/location', [EngineerController::class, 'getLocation'])->name('location.get');
    
    // Work session management (clock in/out)
    Route::post('/login', [EngineerController::class, 'clockIn'])->name('clockin');
    Route::post('/logout', [EngineerController::class, 'clockOut'])->name('clockout');
    
    // Task management
    Route::get('/tasks', [EngineerController::class, 'getTasks'])->name('tasks.index');
    Route::put('/tasks/{task}/status', [EngineerController::class, 'updateTaskStatus'])->name('tasks.update-status');
    Route::get('/tasks/{task}', [EngineerController::class, 'getTask'])->name('tasks.show');
    
    // Engineer profile
    Route::get('/profile', [EngineerController::class, 'getProfile'])->name('profile');
    Route::put('/profile', [EngineerController::class, 'updateProfile'])->name('profile.update');
});
