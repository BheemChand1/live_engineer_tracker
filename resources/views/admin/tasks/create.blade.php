@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-plus-circle me-2"></i>Create New Task</h2>
                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Tasks
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Task Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.tasks.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Task Title <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                            id="title" name="title" value="{{ old('title') }}" required
                                            placeholder="Enter task title">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priority <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('priority') is-invalid @enderror" id="priority"
                                            name="priority" required>
                                            <option value="">Select Priority</option>
                                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium
                                            </option>
                                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High
                                            </option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                    name="description" rows="4"
                                    placeholder="Enter task description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="engineer_id" class="form-label">Assign to Engineer</label>
                                        <select class="form-select @error('engineer_id') is-invalid @enderror"
                                            id="engineer_id" name="engineer_id">
                                            <option value="">Select Engineer (Optional)</option>
                                            @foreach($engineers as $engineer)
                                                <option value="{{ $engineer->user_id }}" {{ old('engineer_id') == $engineer->user_id ? 'selected' : '' }}>
                                                    {{ $engineer->name }} - {{ $engineer->phone }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('engineer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">You can assign this later if needed</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="datetime-local"
                                            class="form-control @error('due_date') is-invalid @enderror" id="due_date"
                                            name="due_date" value="{{ old('due_date') }}">
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">Customer Name</label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                            id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                            placeholder="Enter customer name">
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="customer_phone" class="form-label">Customer Phone</label>
                                        <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror"
                                            id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}"
                                            placeholder="Enter customer phone">
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
                                    placeholder="Enter customer address">{{ old('customer_address') }}</textarea>
                                @error('customer_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="device_type" class="form-label">Device Type</label>
                                        <select class="form-select @error('device_type') is-invalid @enderror"
                                            id="device_type" name="device_type">
                                            <option value="">Select Device Type</option>
                                            <option value="laptop" {{ old('device_type') === 'laptop' ? 'selected' : '' }}>
                                                Laptop</option>
                                            <option value="desktop" {{ old('device_type') === 'desktop' ? 'selected' : '' }}>
                                                Desktop</option>
                                            <option value="mobile" {{ old('device_type') === 'mobile' ? 'selected' : '' }}>
                                                Mobile Phone</option>
                                            <option value="tablet" {{ old('device_type') === 'tablet' ? 'selected' : '' }}>
                                                Tablet</option>
                                            <option value="printer" {{ old('device_type') === 'printer' ? 'selected' : '' }}>
                                                Printer</option>
                                            <option value="other" {{ old('device_type') === 'other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                        @error('device_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                        <input type="number"
                                            class="form-control @error('estimated_hours') is-invalid @enderror"
                                            id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours') }}"
                                            step="0.5" min="0" placeholder="e.g., 2.5">
                                        @error('estimated_hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Task
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection