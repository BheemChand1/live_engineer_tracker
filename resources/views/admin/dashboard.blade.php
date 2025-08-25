@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold">{{ $totalEngineers }}</div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Engineers</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold">{{ $activeEngineers }}</div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Active Today</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold">{{ $pendingTasks }}</div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Pending Tasks</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold">{{ $inProgressTasks }}</div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">In Progress</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-gear fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Live Map -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Live Engineer Tracking</h5>
                    <button class="btn btn-sm btn-primary" onclick="refreshMap()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="liveMap" class="map-container"></div>
                </div>
            </div>
        </div>

        <!-- Today's Activity -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Today's Activity</h5>
                </div>
                <div class="card-body">
                    @foreach($todayLogs as $log)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-success" style="width: 10px; height: 10px;"></div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold">{{ $log->engineer->name }}</div>
                                <small class="text-muted">
                                    Login: {{ $log->login_at->format('H:i') }}
                                    @if($log->logout_at)
                                        | Logout: {{ $log->logout_at->format('H:i') }}
                                    @else
                                        | <span class="text-success">Active</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Tasks -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Tasks</h5>
                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Engineer</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTasks as $task)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $task->title }}</div>
                                            <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                        </td>
                                        <td>
                                            @if($task->engineer)
                                                {{ $task->engineer->name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->getPriorityColor() }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->getStatusColor() }}">
                                                {{ ucfirst(str_replace('-', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($task->due_date)
                                                {{ $task->due_date->format('M d, Y H:i') }}
                                                @if($task->isOverdue())
                                                    <i class="bi bi-exclamation-triangle text-danger ms-1"></i>
                                                @endif
                                            @else
                                                <span class="text-muted">No due date</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let map;
        let engineerMarkers = [];

        function initMap() {
            // Initialize map centered on a default location
            map = L.map('liveMap').setView([40.7128, -74.0060], 10);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Load initial engineer locations
            loadEngineerLocations();
        }

        function loadEngineerLocations() {
            fetch('/admin/api/engineers/locations')
                .then(response => response.json())
                .then(data => {
                    // Clear existing markers
                    engineerMarkers.forEach(marker => map.removeLayer(marker));
                    engineerMarkers = [];

                    // Add new markers
                    data.engineers.forEach(engineer => {
                        if (engineer.latitude && engineer.longitude) {
                            const marker = L.marker([engineer.latitude, engineer.longitude])
                                .addTo(map)
                                .bindPopup(`
                                        <strong>${engineer.name}</strong><br>
                                        Last Update: ${new Date(engineer.last_update).toLocaleString()}
                                    `);

                            engineerMarkers.push(marker);
                        }
                    });

                    // Fit map to show all markers if any exist
                    if (engineerMarkers.length > 0) {
                        const group = new L.featureGroup(engineerMarkers);
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                })
                .catch(error => console.error('Error loading engineer locations:', error));
        }

        function refreshMap() {
            loadEngineerLocations();
        }

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', function () {
            initMap();

            // Auto-refresh every 30 seconds
            setInterval(loadEngineerLocations, 30000);
        });
    </script>
@endpush