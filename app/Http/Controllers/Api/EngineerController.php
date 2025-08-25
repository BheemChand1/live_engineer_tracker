<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Engineer;
use App\Models\Task;
use App\Models\EngineerLog;
use App\Models\EngineerLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EngineerController extends Controller
{
    /**
     * Engineer login and create log entry.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            if ($user->role !== 'engineer') {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $engineer = $user->engineer;
            if (!$engineer) {
                return response()->json(['message' => 'Engineer profile not found'], 404);
            }

            // Create login log
            EngineerLog::create([
                'engineer_id' => $engineer->id,
                'login_at' => now()
            ]);

            // Create API token using Sanctum's HasApiTokens trait
            $token = $user->createToken('engineer-token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'engineer' => $engineer,
                'message' => 'Login successful'
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Engineer logout and update log entry.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $engineer = $user->engineer;

        // Update the latest login log with logout time
        $latestLog = EngineerLog::where('engineer_id', $engineer->id)
                                ->whereNull('logout_at')
                                ->latest('login_at')
                                ->first();

        if ($latestLog) {
            $latestLog->update(['logout_at' => now()]);
        }

        // Revoke tokens
        $user->tokens()->delete();

        return response()->json(['message' => 'Logout successful']);
    }

    /**
     * Get engineer's assigned tasks.
     */
    public function getTasks(Request $request)
    {
        $engineer = $request->user()->engineer;
        
        $tasks = Task::where('engineer_id', $engineer->id)
                    ->orderBy('due_date', 'asc')
                    ->get();

        return response()->json(['tasks' => $tasks]);
    }

    /**
     * Update task status.
     */
    public function updateTaskStatus(Request $request, Task $task)
    {
        $engineer = $request->user()->engineer;

        // Check if task belongs to this engineer
        if ($task->engineer_id !== $engineer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,in-progress,completed'
        ]);

        $updateData = ['status' => $request->status];

        // Update timestamps based on status
        if ($request->status === 'in-progress' && $task->status !== 'in-progress') {
            $updateData['started_at'] = now();
        } elseif ($request->status === 'completed' && $task->status !== 'completed') {
            $updateData['completed_at'] = now();
            if (!$task->started_at) {
                $updateData['started_at'] = now();
            }
        }

        $task->update($updateData);

        return response()->json([
            'task' => $task->fresh(),
            'message' => 'Task status updated successfully'
        ]);
    }

    /**
     * Update engineer location.
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        $engineer = $request->user()->engineer;

        EngineerLocation::create([
            'engineer_id' => $engineer->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'recorded_at' => now()
        ]);

        return response()->json(['message' => 'Location updated successfully']);
    }

    /**
     * Get engineer's latest location.
     */
    public function getLocation(Request $request)
    {
        $engineer = $request->user()->engineer;
        $latestLocation = $engineer->latestLocation;

        return response()->json([
            'location' => $latestLocation,
            'has_location' => !is_null($latestLocation)
        ]);
    }

    /**
     * Get engineer profile.
     */
    public function getProfile(Request $request)
    {
        $engineer = $request->user()->engineer;
        $engineer->load(['tasks' => function($query) {
            $query->whereIn('status', ['pending', 'in-progress']);
        }]);

        return response()->json(['engineer' => $engineer]);
    }

    /**
     * Get engineer's work summary.
     */
    public function getWorkSummary(Request $request)
    {
        $engineer = $request->user()->engineer;

        $summary = [
            'today_hours' => $engineer->getTodayWorkHours(),
            'total_tasks' => $engineer->assignedTasks()->count(),
            'pending_tasks' => $engineer->assignedTasks()->where('status', 'pending')->count(),
            'in_progress_tasks' => $engineer->assignedTasks()->where('status', 'in_progress')->count(),
            'completed_tasks' => $engineer->assignedTasks()->where('status', 'completed')->count(),
            'is_active_today' => $engineer->isActiveToday()
        ];

        return response()->json(['summary' => $summary]);
    }

    /**
     * Clock in - start work session.
     */
    public function clockIn(Request $request)
    {
        $engineer = Auth::user()->engineer;
        
        if (!$engineer) {
            return response()->json(['message' => 'Engineer profile not found'], 404);
        }

        // Check if already clocked in today
        $existingLog = $engineer->logs()
            ->whereDate('login_at', today())
            ->whereNull('logout_at')
            ->first();

        if ($existingLog) {
            return response()->json(['message' => 'Already clocked in'], 400);
        }

        // Create new log entry
        $log = EngineerLog::create([
            'engineer_id' => $engineer->id,
            'login_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clocked in successfully',
            'log' => $log
        ]);
    }

    /**
     * Clock out - end work session.
     */
    public function clockOut(Request $request)
    {
        $engineer = Auth::user()->engineer;
        
        if (!$engineer) {
            return response()->json(['message' => 'Engineer profile not found'], 404);
        }

        // Find active log entry
        $log = $engineer->logs()
            ->whereDate('login_at', today())
            ->whereNull('logout_at')
            ->first();

        if (!$log) {
            return response()->json(['message' => 'No active work session found'], 400);
        }

        // Update logout time
        $log->update(['logout_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Clocked out successfully',
            'log' => $log
        ]);
    }

    /**
     * Update engineer profile.
     */
    public function updateProfile(Request $request)
    {
        $engineer = Auth::user()->engineer;
        
        if (!$engineer) {
            return response()->json(['message' => 'Engineer profile not found'], 404);
        }

        $request->validate([
            'phone' => 'nullable|string|max:20',
            'skills' => 'nullable|array',
        ]);

        $engineer->update([
            'phone' => $request->phone,
            'skills' => $request->skills ? json_encode($request->skills) : $engineer->skills,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'engineer' => $engineer
        ]);
    }

    /**
     * Get specific task details.
     */
    public function getTask(Request $request, Task $task)
    {
        $engineer = Auth::user()->engineer;
        
        // Check if task is assigned to this engineer
        if ($task->engineer_id !== $engineer->user_id) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json(['task' => $task]);
    }
}
