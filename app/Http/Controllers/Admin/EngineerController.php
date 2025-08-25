<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Engineer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EngineerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Engineer::with(['user', 'tasks' => function($q) {
            $q->whereIn('status', ['pending', 'in-progress']);
        }]);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('skills', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $engineers = $query->paginate(10)->withQueryString();

        return view('admin.engineers.index', compact('engineers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.engineers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:engineers,email',
            'phone' => 'required|string|max:20',
            'skills' => 'required|array|min:1',
            'skills.*' => 'string',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user account for the engineer
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'engineer',
        ]);

        // Create engineer profile
        Engineer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'skills' => $validated['skills'], // Model cast will handle JSON conversion
            'status' => $validated['status'],
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.engineers.index')
                        ->with('success', 'Engineer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Engineer $engineer)
    {
        $engineer->load(['user.assignedTasks', 'tasks', 'logs' => function($q) {
            $q->orderBy('login_at', 'desc')->limit(10);
        }, 'locations' => function($q) {
            $q->orderBy('recorded_at', 'desc')->limit(50);
        }]);

        // Get task statistics
        $taskStats = [
            'total' => $engineer->tasks->count(),
            'pending' => $engineer->tasks->where('status', 'pending')->count(),
            'in_progress' => $engineer->tasks->where('status', 'in-progress')->count(),
            'completed' => $engineer->tasks->where('status', 'completed')->count(),
        ];

        // Get work hours for the last 7 days
        $workHours = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayLogs = $engineer->logs()->whereDate('login_at', $date)->get();
            $totalMinutes = $dayLogs->sum(function($log) {
                $logout = $log->logout_at ?: now();
                return $log->login_at->diffInMinutes($logout);
            });
            $workHours[] = [
                'date' => $date->format('M d'),
                'hours' => round($totalMinutes / 60, 1)
            ];
        }

        return view('admin.engineers.show', compact('engineer', 'taskStats', 'workHours'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Engineer $engineer)
    {
        return view('admin.engineers.edit', compact('engineer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Engineer $engineer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('engineers')->ignore($engineer->id)],
            'phone' => 'required|string|max:20',
            'skills' => 'required|array|min:1',
            'skills.*' => 'string',
            'status' => 'required|in:active,inactive',
        ]);

        // Convert skills array to proper format
        $engineer->update($validated);

        // Update associated user if exists
        if ($engineer->user) {
            $engineer->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);
        }

        return redirect()->route('admin.engineers.index')
                        ->with('success', 'Engineer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Engineer $engineer)
    {
        // Delete associated user account
        if ($engineer->user) {
            $engineer->user->delete();
        }

        $engineer->delete();

        return redirect()->route('admin.engineers.index')
                        ->with('success', 'Engineer deleted successfully.');
    }

    /**
     * Toggle engineer status.
     */
    public function toggleStatus(Engineer $engineer)
    {
        $engineer->update([
            'status' => $engineer->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success', 'Engineer status updated successfully.');
    }
}
