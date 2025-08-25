@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-tasks me-2"></i>Task Details</h2>
                    <div>
                        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-2"></i>Edit Task
                        </a>
                        <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Task Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Title</label>
                                            <p class="form-control-plaintext">{{ $task->title }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Priority</label>
                                            <p class="form-control-plaintext">
                                                <span
                                                    class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'info') }} fs-6">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @if($task->description)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <p class="form-control-plaintext">{{ $task->description }}</p>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <p class="form-control-plaintext">
                                                <span
                                                    class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : ($task->status === 'cancelled' ? 'danger' : 'secondary')) }} fs-6">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Assigned Engineer</label>
                                            <p class="form-control-plaintext">
                                                @if($task->engineer)
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-primary rounded-circle p-2 me-2">
                                                            {{ strtoupper(substr($task->engineer->name, 0, 2)) }}
                                                        </span>
                                                        {{ $task->engineer->name }}
                                                    </div>
                                                @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Created Date</label>
                                            <p class="form-control-plaintext">
                                                {{ $task->created_at->format('F d, Y \a\t h:i A') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Due Date</label>
                                            <p class="form-control-plaintext">
                                                @if($task->due_date)
                                                    <span
                                                        class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : '' }}">
                                                        {{ $task->due_date->format('F d, Y \a\t h:i A') }}
                                                    </span>
                                                    @if($task->due_date->isPast() && $task->status !== 'completed')
                                                        <br><small class="text-danger"><i
                                                                class="fas fa-exclamation-triangle me-1"></i>Overdue</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No due date set</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($task->customer_name || $task->customer_phone || $task->customer_address)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($task->customer_name)
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Customer Name</label>
                                                    <p class="form-control-plaintext">{{ $task->customer_name }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        @if($task->customer_phone)
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Phone Number</label>
                                                    <p class="form-control-plaintext">
                                                        <a href="tel:{{ $task->customer_phone }}">{{ $task->customer_phone }}</a>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if($task->customer_address)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Address</label>
                                            <p class="form-control-plaintext">{{ $task->customer_address }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($task->device_type || $task->estimated_hours)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Device Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($task->device_type)
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Device Type</label>
                                                    <p class="form-control-plaintext">{{ ucfirst($task->device_type) }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        @if($task->estimated_hours)
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Estimated Hours</label>
                                                    <p class="form-control-plaintext">{{ $task->estimated_hours }} hours</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                @if(!$task->engineer_id)
                                    <form action="{{ route('admin.tasks.assign', $task) }}" method="POST" class="mb-3">
                                        @csrf
                                        <div class="mb-2">
                                            <select name="engineer_id" class="form-select" required>
                                                <option value="">Select Engineer</option>
                                                @foreach($engineers as $engineer)
                                                    <option value="{{ $engineer->user_id }}">{{ $engineer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-user-plus me-1"></i>Assign Engineer
                                        </button>
                                    </form>
                                @endif

                                <div class="d-grid gap-2">
                                    @if($task->status !== 'completed')
                                        <form action="{{ route('admin.tasks.update', $task) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check me-1"></i>Mark Completed
                                            </button>
                                        </form>
                                    @endif

                                    @if($task->status === 'pending')
                                        <form action="{{ route('admin.tasks.update', $task) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="btn btn-info btn-sm">
                                                <i class="fas fa-play me-1"></i>Start Progress
                                            </button>
                                        </form>
                                    @endif

                                    @if($task->status !== 'cancelled')
                                        <form action="{{ route('admin.tasks.update', $task) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="fas fa-times me-1"></i>Cancel Task
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Task Timeline</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Task Created</h6>
                                            <p class="timeline-text">{{ $task->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>

                                    @if($task->engineer_id && $task->updated_at != $task->created_at)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-info"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Engineer Assigned</h6>
                                                <p class="timeline-text">{{ $task->engineer->name }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($task->status === 'completed')
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Task Completed</h6>
                                                <p class="timeline-text">{{ $task->updated_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-title {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .timeline-text {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 0;
        }
    </style>
@endsection