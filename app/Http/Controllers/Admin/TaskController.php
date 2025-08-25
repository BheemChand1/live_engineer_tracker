<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Engineer;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::with(['engineer']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_address', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Engineer filter
        if ($request->has('engineer') && $request->engineer) {
            $query->where('engineer_id', $request->engineer);
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(15)->withQueryString();
        $engineers = Engineer::where('status', 'active')->get();

        return view('admin.tasks.index', compact('tasks', 'engineers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $engineers = Engineer::where('status', 'active')->get();
        return view('admin.tasks.create', compact('engineers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date|after:now',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'device_type' => 'nullable|string|max:255',
            'estimated_hours' => 'nullable|numeric|min:0',
            'engineer_id' => 'nullable|exists:engineers,user_id',
        ]);

        Task::create($validated);

        return redirect()->route('admin.tasks.index')
                        ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load('engineer');
        $engineers = Engineer::where('status', 'active')->get();
        return view('admin.tasks.show', compact('task', 'engineers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $engineers = Engineer::where('status', 'active')->get();
        return view('admin.tasks.edit', compact('task', 'engineers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'device_type' => 'nullable|string|max:255',
            'estimated_hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'engineer_id' => 'nullable|exists:engineers,user_id',
        ]);

        // Update timestamps based on status changes
        if ($validated['status'] === 'in_progress' && $task->status !== 'in_progress') {
            $validated['started_at'] = now();
        } elseif ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
            if (!$task->started_at) {
                $validated['started_at'] = now();
            }
        }

        $task->update($validated);

        return redirect()->route('admin.tasks.index')
                        ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
                        ->with('success', 'Task deleted successfully.');
    }

    /**
     * Assign task to an engineer.
     */
    public function assign(Request $request, Task $task)
    {
        $request->validate([
            'engineer_id' => 'required|exists:engineers,user_id'
        ]);

        $task->update([
            'engineer_id' => $request->engineer_id,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Task assigned successfully.');
    }

    /**
     * Unassign task from engineer.
     */
    public function unassign(Task $task)
    {
        $task->update([
            'engineer_id' => null,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Task unassigned successfully.');
    }
}
