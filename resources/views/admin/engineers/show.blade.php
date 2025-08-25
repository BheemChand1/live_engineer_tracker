@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-user me-2"></i>Engineer Details</h2>
                    <div>
                        <a href="{{ route('admin.engineers.edit', $engineer) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-2"></i>Edit Engineer
                        </a>
                        <a href="{{ route('admin.engineers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Engineers
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Engineer Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Full Name</label>
                                            <p class="form-control-plaintext">{{ $engineer->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Email Address</label>
                                            <p class="form-control-plaintext">{{ $engineer->email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Phone Number</label>
                                            <p class="form-control-plaintext">{{ $engineer->phone }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <p class="form-control-plaintext">
                                                @if($engineer->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Skills</label>
                                    <div class="mt-2">
                                        @php
                                            $skills = json_decode($engineer->skills, true) ?? [];
                                        @endphp
                                        @if(count($skills) > 0)
                                            @foreach($skills as $skill)
                                                <span class="badge bg-info me-2 mb-1">{{ $skill }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No skills listed</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Created Date</label>
                                            <p class="form-control-plaintext">
                                                {{ $engineer->created_at->format('F d, Y \a\t h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Last Updated</label>
                                            <p class="form-control-plaintext">
                                                {{ $engineer->updated_at->format('F d, Y \a\t h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Stats</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Tasks:</span>
                                        <span class="fw-bold">{{ $engineer->user?->assignedTasks?->count() ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Completed Tasks:</span>
                                        <span
                                            class="fw-bold text-success">{{ $engineer->user?->assignedTasks?->where('status', 'completed')?->count() ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Pending Tasks:</span>
                                        <span
                                            class="fw-bold text-warning">{{ $engineer->user?->assignedTasks?->where('status', 'pending')?->count() ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>In Progress:</span>
                                        <span
                                            class="fw-bold text-info">{{ $engineer->user?->assignedTasks?->where('status', 'in_progress')?->count() ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Account Status</h5>
                            </div>
                            <div class="card-body">
                                @if($engineer->user)
                                    <div class="mb-2">
                                        <span class="badge bg-success">User Account: Active</span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">User ID: {{ $engineer->user->id }}</small>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Role: {{ ucfirst($engineer->user->role) }}</small>
                                    </div>
                                @else
                                    <span class="badge bg-warning">No User Account</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($engineer->user?->assignedTasks?->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Tasks</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(($engineer->user?->assignedTasks ?? collect())->take(5) as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection