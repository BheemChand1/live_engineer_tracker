<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Engineer;
use App\Models\Task;
use App\Models\EngineerLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get dashboard statistics
        $totalEngineers = Engineer::count();
        $activeEngineers = Engineer::whereHas('logs', function ($query) {
            $query->whereDate('login_at', today())
                  ->whereNull('logout_at');
        })->count();
        
        $pendingTasks = Task::where('status', 'pending')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $completedTasks = Task::where('status', 'completed')->count();
        $overdueTasks = Task::whereNotNull('due_date')
                           ->where('due_date', '<', now())
                           ->where('status', '!=', 'completed')
                           ->count();

        // Get recent tasks
        $recentTasks = Task::with('engineer')
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        // Get active engineers with their latest locations
        $activeEngineersWithLocation = Engineer::with(['latestLocation', 'user'])
                                              ->whereHas('logs', function ($query) {
                                                  $query->whereDate('login_at', today())
                                                        ->whereNull('logout_at');
                                              })
                                              ->get();

        // Get today's work summary
        $todayLogs = EngineerLog::with('engineer')
                               ->whereDate('login_at', today())
                               ->get();

        return view('admin.dashboard', compact(
            'totalEngineers',
            'activeEngineers',
            'pendingTasks',
            'inProgressTasks',
            'completedTasks',
            'overdueTasks',
            'recentTasks',
            'activeEngineersWithLocation',
            'todayLogs'
        ));
    }

    /**
     * Get live tracking data for AJAX requests.
     */
    public function getLiveData()
    {
        $activeEngineers = Engineer::with(['latestLocation', 'user'])
                                  ->whereHas('logs', function ($query) {
                                      $query->whereDate('login_at', today())
                                            ->whereNull('logout_at');
                                  })
                                  ->get();

        return response()->json([
            'engineers' => $activeEngineers->map(function ($engineer) {
                return [
                    'id' => $engineer->id,
                    'name' => $engineer->name,
                    'latitude' => $engineer->latestLocation?->latitude,
                    'longitude' => $engineer->latestLocation?->longitude,
                    'last_update' => $engineer->latestLocation?->recorded_at,
                ];
            })
        ]);
    }

    /**
     * Display live tracking page.
     */
    public function liveTracking()
    {
        $activeEngineers = Engineer::with(['user.currentLocation'])
                                  ->where('status', 'active')
                                  ->whereHas('logs', function ($query) {
                                      $query->whereDate('login_at', today())
                                            ->whereNull('logout_at');
                                  })
                                  ->get();

        $totalEngineers = Engineer::count();

        return view('admin.live-tracking', compact('activeEngineers', 'totalEngineers'));
    }

    /**
     * Get engineers locations for API calls.
     */
    public function getEngineersLocations()
    {
        $activeEngineers = Engineer::with(['user', 'latestLocation', 'activeTasks'])
                                  ->where('status', 'active')
                                  ->whereHas('logs', function ($query) {
                                      $query->whereDate('login_at', today())
                                            ->whereNull('logout_at');
                                  })
                                  ->get();

        $engineersData = $activeEngineers->map(function ($engineer) {
            $location = $engineer->latestLocation;
            $activeTasks = $engineer->activeTasks->count();
            $isOnline = $location && $location->recorded_at->diffInMinutes(now()) <= 15;
            
            return [
                'id' => $engineer->user_id,
                'name' => $engineer->name,
                'phone' => $engineer->phone ?? 'N/A',
                'latitude' => $location?->latitude,
                'longitude' => $location?->longitude,
                'last_updated' => $location?->recorded_at?->diffForHumans() ?? 'Never',
                'is_online' => $isOnline,
                'active_tasks' => $activeTasks,
                'status' => $engineer->status,
            ];
        });

        $stats = [
            'online_count' => $engineersData->where('is_online', true)->count(),
            'tracking_count' => $engineersData->where('latitude')->count(),
            'active_tasks' => $engineersData->sum('active_tasks'),
            'total_engineers' => $engineersData->count(),
        ];

        return response()->json([
            'engineers' => $engineersData->values(),
            'stats' => $stats,
            'last_update' => now()->toISOString(),
        ]);
    }
}
