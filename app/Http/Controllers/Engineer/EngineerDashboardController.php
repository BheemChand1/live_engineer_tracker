<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Engineer;
use App\Models\Task;
use App\Models\EngineerLog;
use App\Models\EngineerLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EngineerDashboardController extends Controller
{
    /**
     * Display the engineer dashboard.
     */
    public function index()
    {
        $engineer = Auth::user()->engineer;
        
        if (!$engineer) {
            abort(403, 'Engineer profile not found.');
        }

        // Task statistics
        $pendingTasks = $engineer->assignedTasks()->where('status', 'pending')->count();
        $activeTasks = $engineer->assignedTasks()->where('status', 'in_progress')->count();
        $completedTasks = $engineer->assignedTasks()->where('status', 'completed')->count();

        // Today's tasks
        $todayTasks = $engineer->assignedTasks()
            ->whereDate('created_at', today())
            ->orWhere(function($query) {
                $query->whereNotNull('due_date')
                      ->whereDate('due_date', today());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->get();

        // Upcoming tasks (next 7 days)
        $upcomingTasks = $engineer->assignedTasks()
            ->where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '>', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date', 'asc')
            ->get();

        return view('engineer.dashboard', compact(
            'engineer', 
            'pendingTasks', 
            'activeTasks', 
            'completedTasks',
            'todayTasks',
            'upcomingTasks'
        ));
    }

    /**
     * Display engineer's tasks.
     */
    public function tasks()
    {
        $engineer = Auth::user()->engineer;
        $tasks = $engineer->tasks()->orderBy('due_date', 'asc')->paginate(10);

        return view('engineer.tasks', compact('tasks', 'engineer'));
    }

    /**
     * Update task status.
     */
    public function updateTaskStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in-progress,completed'
        ]);

        // Ensure the task belongs to the authenticated engineer
        if ($task->engineer_id !== Auth::user()->engineer->id) {
            abort(403, 'Unauthorized to update this task.');
        }

        $task->status = $request->status;

        if ($request->status === 'in-progress' && !$task->started_at) {
            $task->started_at = now();
        } elseif ($request->status === 'completed' && !$task->completed_at) {
            $task->completed_at = now();
        }

        $task->save();

        return back()->with('success', 'Task status updated successfully.');
    }

    /**
     * Record engineer login.
     */
    public function recordLogin()
    {
        $engineer = Auth::user()->engineer;

        // Check if already logged in today
        $existingLog = $engineer->logs()
            ->whereDate('login_at', today())
            ->whereNull('logout_at')
            ->first();

        if (!$existingLog) {
            EngineerLog::create([
                'engineer_id' => $engineer->id,
                'login_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Record engineer logout.
     */
    public function recordLogout()
    {
        $engineer = Auth::user()->engineer;

        $log = $engineer->logs()
            ->whereDate('login_at', today())
            ->whereNull('logout_at')
            ->latest()
            ->first();

        if ($log) {
            $log->update(['logout_at' => now()]);
        }

        return response()->json(['success' => true]);
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

        $engineer = Auth::user()->engineer;

        EngineerLocation::create([
            'engineer_id' => $engineer->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'recorded_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get engineer's location history.
     */
    public function locationHistory(Request $request)
    {
        $engineer = Auth::user()->engineer;
        $date = $request->get('date', today());

        $locations = $engineer->locations()
            ->whereDate('recorded_at', $date)
            ->orderBy('recorded_at', 'asc')
            ->get();

        return response()->json($locations);
    }
}
