@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit me-2"></i>Edit Task</h2>
                <div>
                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-info me-2">
                        <i class="fas fa-eye me-2"></i>View Task
                    </a>
                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Tasks
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tasks.update', $task) }}" method="POST">
                @csrf
                @method('PUT')
                
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
                                            <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" name="title" value="{{ old('title', $task->title) }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                                <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High</option>
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" 
                                              placeholder="Describe the task details...">{{ old('description', $task->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ old('status', $task->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="engineer_id" class="form-label">Assigned Engineer</label>
                                            <select class="form-select @error('engineer_id') is-invalid @enderror" id="engineer_id" name="engineer_id">
                                                <option value="">No engineer assigned</option>
                                                @foreach($engineers as $engineer)
                                                    <option value="{{ $engineer->user_id }}" 
                                                            {{ old('engineer_id', $task->engineer_id) == $engineer->user_id ? 'selected' : '' }}>
                                                        {{ $engineer->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('engineer_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="due_date" class="form-label">Due Date</label>
                                            <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" 
                                                   id="due_date" name="due_date" 
                                                   value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}">
                                            @error('due_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                            <input type="number" class="form-control @error('estimated_hours') is-invalid @enderror" 
                                                   id="estimated_hours" name="estimated_hours" 
                                                   value="{{ old('estimated_hours', $task->estimated_hours) }}" 
                                                   min="0" step="0.5" placeholder="e.g., 2.5">
                                            @error('estimated_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Customer Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_name" class="form-label">Customer Name</label>
                                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                                   id="customer_name" name="customer_name" 
                                                   value="{{ old('customer_name', $task->customer_name) }}" 
                                                   placeholder="Customer full name">
                                            @error('customer_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                                   id="customer_phone" name="customer_phone" 
                                                   value="{{ old('customer_phone', $task->customer_phone) }}" 
                                                   placeholder="e.g., +1234567890">
                                            @error('customer_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="customer_address" class="form-label">Customer Address</label>
                                    <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                              id="customer_address" name="customer_address" rows="3" 
                                              placeholder="Full address including city, state, zip">{{ old('customer_address', $task->customer_address) }}</textarea>
                                    @error('customer_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Device Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="device_type" class="form-label">Device Type</label>
                                    <select class="form-select @error('device_type') is-invalid @enderror" id="device_type" name="device_type">
                                        <option value="">Select device type</option>
                                        <option value="desktop" {{ old('device_type', $task->device_type) === 'desktop' ? 'selected' : '' }}>Desktop Computer</option>
                                        <option value="laptop" {{ old('device_type', $task->device_type) === 'laptop' ? 'selected' : '' }}>Laptop</option>
                                        <option value="tablet" {{ old('device_type', $task->device_type) === 'tablet' ? 'selected' : '' }}>Tablet</option>
                                        <option value="smartphone" {{ old('device_type', $task->device_type) === 'smartphone' ? 'selected' : '' }}>Smartphone</option>
                                        <option value="printer" {{ old('device_type', $task->device_type) === 'printer' ? 'selected' : '' }}>Printer</option>
                                        <option value="server" {{ old('device_type', $task->device_type) === 'server' ? 'selected' : '' }}>Server</option>
                                        <option value="networking" {{ old('device_type', $task->device_type) === 'networking' ? 'selected' : '' }}>Networking Equipment</option>
                                        <option value="other" {{ old('device_type', $task->device_type) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('device_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Update Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Task
                                    </button>
                                    
                                    <hr>
                                    
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye me-2"></i>View Task
                                    </a>
                                    
                                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-list me-2"></i>All Tasks
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Task Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : ($task->status === 'cancelled' ? 'danger' : 'secondary')) }} fs-6 mb-2">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                    <p class="small text-muted mb-1">Created: {{ $task->created_at->format('M d, Y') }}</p>
                                    <p class="small text-muted mb-0">Last Updated: {{ $task->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($task->engineer)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Current Engineer</h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <span class="badge bg-primary rounded-circle p-3">
                                            {{ strtoupper(substr($task->engineer->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <h6 class="mb-1">{{ $task->engineer->name }}</h6>
                                    <p class="small text-muted mb-0">{{ $task->engineer->email }}</p>
                                    @if($task->engineer->phone)
                                        <p class="small text-muted mb-0">{{ $task->engineer->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
