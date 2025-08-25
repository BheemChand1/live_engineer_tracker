<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Engineer Dashboard - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .mobile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-left: 5px solid;
        }

        .stats-card.pending {
            border-left-color: #ffc107;
        }

        .stats-card.in-progress {
            border-left-color: #17a2b8;
        }

        .stats-card.completed {
            border-left-color: #28a745;
        }

        .task-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid;
        }

        .task-card.high {
            border-left-color: #dc3545;
        }

        .task-card.medium {
            border-left-color: #ffc107;
        }

        .task-card.low {
            border-left-color: #28a745;
        }

        .action-buttons {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .fab {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 10px;
            display: block;
        }

        .fab-location {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .fab-checkin {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .work-session {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .priority-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        .location-status {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.75rem;
            margin-top: 1rem;
        }

        .quick-action {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
            display: block;
            transition: transform 0.2s;
        }

        .quick-action:hover {
            transform: translateY(-2px);
            color: inherit;
            text-decoration: none;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="avatar me-3">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                    <small class="opacity-75">{{ $engineer->phone ?? 'Engineer' }}</small>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-white" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-lg"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Work Session Status -->
        @php
            $todayLog = $engineer->logs()->whereDate('login_at', today())->whereNull('logout_at')->first();
        @endphp

        <div class="location-status">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small><i class="fas fa-clock me-1"></i>Work Session</small>
                    <div class="fw-bold">
                        @if($todayLog)
                            <span class="text-success">Active since {{ $todayLog->login_at->format('H:i') }}</span>
                        @else
                            <span class="text-warning">Not started</span>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <small><i class="fas fa-map-marker-alt me-1"></i>Location</small>
                    <div class="fw-bold">
                        <span id="location-status" class="text-info">Updating...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid p-3">
        <!-- Task Statistics -->
        <div class="row">
            <div class="col-4">
                <div class="stats-card pending">
                    <div class="text-center">
                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0">{{ $pendingTasks }}</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="stats-card in-progress">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-2x text-info mb-2"></i>
                        <h3 class="mb-0">{{ $activeTasks }}</h3>
                        <small class="text-muted">Active</small>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="stats-card completed">
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h3 class="mb-0">{{ $completedTasks }}</h3>
                        <small class="text-muted">Done</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-6">
                <a href="#" class="quick-action" onclick="toggleWorkSession()">
                    <div class="d-flex align-items-center">
                        <i
                            class="fas fa-{{ $todayLog ? 'stop' : 'play' }} fa-lg me-3 text-{{ $todayLog ? 'danger' : 'success' }}"></i>
                        <div>
                            <div class="fw-bold">{{ $todayLog ? 'End Work' : 'Start Work' }}</div>
                            <small class="text-muted">{{ $todayLog ? 'Clock out' : 'Clock in' }}</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="#" class="quick-action" onclick="updateLocation()">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt fa-lg me-3 text-primary"></i>
                        <div>
                            <div class="fw-bold">Update Location</div>
                            <small class="text-muted">Share GPS</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Today's Tasks -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Today's Tasks</h5>
            <a href="#" class="btn btn-outline-primary btn-sm">View All</a>
        </div>

        @forelse($todayTasks as $task)
            <div class="task-card {{ $task->priority }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-1 flex-grow-1">{{ $task->title }}</h6>
                    <span
                        class="priority-badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }} text-white">
                        {{ ucfirst($task->priority) }}
                    </span>
                </div>

                @if($task->customer_name)
                    <p class="text-muted small mb-2">
                        <i class="fas fa-user me-1"></i>{{ $task->customer_name }}
                        @if($task->customer_phone)
                            <br><i class="fas fa-phone me-1"></i>{{ $task->customer_phone }}
                        @endif
                    </p>
                @endif

                @if($task->customer_address)
                    <p class="text-muted small mb-2">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($task->customer_address, 50) }}
                    </p>
                @endif

                <div class="d-flex justify-content-between align-items-center">
                    <span
                        class="status-badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }} text-white">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>

                    <div class="btn-group btn-group-sm" role="group">
                        @if($task->status === 'pending')
                            <button type="button" class="btn btn-outline-primary"
                                onclick="updateTaskStatus({{ $task->id }}, 'in_progress')">
                                <i class="fas fa-play"></i>
                            </button>
                        @elseif($task->status === 'in_progress')
                            <button type="button" class="btn btn-outline-success"
                                onclick="updateTaskStatus({{ $task->id }}, 'completed')">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif

                        @if($task->customer_phone)
                            <a href="tel:{{ $task->customer_phone }}" class="btn btn-outline-info">
                                <i class="fas fa-phone"></i>
                            </a>
                        @endif

                        @if($task->customer_address)
                            <button type="button" class="btn btn-outline-warning"
                                onclick="openMaps('{{ addslashes($task->customer_address) }}')">
                                <i class="fas fa-directions"></i>
                            </button>
                        @endif
                    </div>
                </div>

                @if($task->due_date)
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>Due: {{ $task->due_date->format('M d, H:i') }}
                            @if($task->due_date->isPast() && $task->status !== 'completed')
                                <span class="text-danger fw-bold">(Overdue)</span>
                            @endif
                        </small>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No tasks for today</h5>
                <p class="text-muted">Enjoy your day!</p>
            </div>
        @endforelse

        <!-- Upcoming Tasks -->
        @if($upcomingTasks->count() > 0)
            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Upcoming Tasks</h5>
                <span class="badge bg-primary">{{ $upcomingTasks->count() }}</span>
            </div>

            @foreach($upcomingTasks->take(3) as $task)
                <div class="task-card {{ $task->priority }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $task->title }}</h6>
                            @if($task->customer_name)
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-user me-1"></i>{{ $task->customer_name }}
                                </p>
                            @endif
                            @if($task->due_date)
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $task->due_date->format('M d, H:i') }}
                                </small>
                            @endif
                        </div>
                        <span
                            class="priority-badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }} text-white">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Bottom Spacing for FABs -->
        <div style="height: 100px;"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Get current location and update status
        function updateLocation() {
            if (navigator.geolocation) {
                document.getElementById('location-status').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting...';

                navigator.geolocation.getCurrentPosition(function (position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Send location to server
                    fetch('/api/engineer/location', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            latitude: latitude,
                            longitude: longitude
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('location-status').innerHTML = '<i class="fas fa-check text-success"></i> Updated';
                            setTimeout(() => {
                                document.getElementById('location-status').textContent = 'Shared';
                            }, 2000);
                        })
                        .catch(error => {
                            document.getElementById('location-status').innerHTML = '<i class="fas fa-times text-danger"></i> Failed';
                        });
                }, function (error) {
                    document.getElementById('location-status').innerHTML = '<i class="fas fa-times text-danger"></i> Denied';
                });
            } else {
                document.getElementById('location-status').textContent = 'Not supported';
            }
        }

        // Toggle work session
        function toggleWorkSession() {
            const isActive = {{ $todayLog ? 'true' : 'false' }};
            const url = isActive ? '/api/engineer/logout' : '/api/engineer/login';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('Error updating work session');
                });
        }

        // Update task status
        function updateTaskStatus(taskId, status) {
            fetch(`/api/engineer/tasks/${taskId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating task status');
                    }
                })
                .catch(error => {
                    alert('Error updating task status');
                });
        }

        // Open maps for directions
        function openMaps(address) {
            const encodedAddress = encodeURIComponent(address);
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const isAndroid = /Android/.test(navigator.userAgent);

            if (isIOS) {
                window.open(`maps://maps.google.com/maps?daddr=${encodedAddress}&amp;ll=`);
            } else if (isAndroid) {
                window.open(`geo:0,0?q=${encodedAddress}`);
            } else {
                window.open(`https://www.google.com/maps/search/?api=1&query=${encodedAddress}`);
            }
        }

        // Auto-update location every 5 minutes if work session is active
        @if($todayLog)
            setInterval(function () {
                updateLocation();
            }, 300000); // 5 minutes
        @endif

        // Update location on page load
        document.addEventListener('DOMContentLoaded', function () {
            updateLocation();
        });
    </script>
</body>

</html>