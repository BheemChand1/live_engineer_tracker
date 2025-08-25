@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tasks me-2"></i>Task Management</h2>
                <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create New Task
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.tasks.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="priority" class="form-select">
                                <option value="">All Priorities</option>
                                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="engineer_id" class="form-select">
                                <option value="">All Engineers</option>
                                @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->user_id }}" {{ request('engineer_id') == $engineer->user_id ? 'selected' : '' }}>
                                        {{ $engineer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Tasks ({{ $tasks->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Engineer</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>#{{ $task->id }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $task->title }}</strong>
                                                    @if($task->description)
                                                        <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($task->engineer)
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-primary rounded-circle p-2 me-2">
                                                            {{ strtoupper(substr($task->engineer->name, 0, 2)) }}
                                                        </span>
                                                        {{ $task->engineer->name }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : ($task->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : '' }}">
                                                        {{ $task->due_date->format('M d, Y') }}
                                                    </span>
                                                    @if($task->due_date->isPast() && $task->status !== 'completed')
                                                        <br><small class="text-danger">Overdue</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No due date</span>
                                                @endif
                                            </td>
                                            <td>{{ $task->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.tasks.show', $task) }}" 
                                                       class="btn btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.tasks.edit', $task) }}" 
                                                       class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="confirmDelete({{ $task->id }})" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <form id="delete-form-{{ $task->id }}" 
                                                      action="{{ route('admin.tasks.destroy', $task) }}" 
                                                      method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($tasks->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $tasks->withQueryString()->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Tasks Found</h5>
                            <p class="text-muted">Start by creating your first task.</p>
                            <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(taskId) {
    if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
        document.getElementById('delete-form-' + taskId).submit();
    }
}
</script>
@endsection
