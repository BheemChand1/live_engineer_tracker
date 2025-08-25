@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-cog me-2"></i>Profile Settings</h2>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Profile Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Profile Information
                            </h5>
                            <p class="text-muted small mb-0">Update your account's profile information and email address.</p>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-lock me-2"></i>Update Password
                            </h5>
                            <p class="text-muted small mb-0">Ensure your account is using a long, random password to stay secure.</p>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-trash me-2"></i>Delete Account
                            </h5>
                            <p class="text-white-50 small mb-0">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <!-- Profile Sidebar -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Account Information
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <!-- User Avatar -->
                            <div class="mb-3">
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px; font-size: 32px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            </div>
                            
                            <!-- User Details -->
                            <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                            <p class="text-muted mb-2">{{ Auth::user()->email }}</p>
                            <span class="badge bg-{{ Auth::user()->role === 'admin' ? 'danger' : 'primary' }} fs-6">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                            
                            <hr>
                            
                            <!-- Account Stats -->
                            <div class="row text-center">
                                <div class="col">
                                    <h6 class="mb-0">{{ Auth::user()->created_at->format('M Y') }}</h6>
                                    <small class="text-muted">Member Since</small>
                                </div>
                            </div>
                            
                            @if(Auth::user()->role === 'admin')
                                <div class="row text-center mt-3">
                                    <div class="col-6">
                                        <h6 class="mb-0">{{ App\Models\Engineer::count() }}</h6>
                                        <small class="text-muted">Engineers</small>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="mb-0">{{ App\Models\Task::count() }}</h6>
                                        <small class="text-muted">Total Tasks</small>
                                    </div>
                                </div>
                            @else
                                @php
                                    $engineer = App\Models\Engineer::where('user_id', Auth::id())->first();
                                @endphp
                                @if($engineer)
                                    <div class="row text-center mt-3">
                                        <div class="col-6">
                                            <h6 class="mb-0">{{ $engineer->assignedTasks->where('status', 'completed')->count() }}</h6>
                                            <small class="text-muted">Completed</small>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="mb-0">{{ $engineer->assignedTasks->whereIn('status', ['pending', 'in_progress'])->count() }}</h6>
                                            <small class="text-muted">Active</small>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @if(Auth::user()->role === 'admin')
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a>
                                    <a href="{{ route('admin.engineers.index') }}" class="btn btn-outline-info">
                                        <i class="fas fa-users me-2"></i>Manage Engineers
                                    </a>
                                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-success">
                                        <i class="fas fa-tasks me-2"></i>Manage Tasks
                                    </a>
                                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-warning">
                                        <i class="fas fa-chart-bar me-2"></i>View Reports
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('engineer.dashboard') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a>
                                    <a href="#" class="btn btn-outline-info">
                                        <i class="fas fa-map-marker-alt me-2"></i>My Location
                                    </a>
                                    <a href="#" class="btn btn-outline-success">
                                        <i class="fas fa-clock me-2"></i>Time Tracking
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
