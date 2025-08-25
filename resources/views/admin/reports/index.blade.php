@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h2>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="exportToCSV()">
                            <i class="fas fa-download me-2"></i>Export CSV
                        </button>
                    </div>
                </div>

                <!-- Quick Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ App\Models\Task::count() }}</h4>
                                        <p class="mb-0">Total Tasks</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-tasks fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ App\Models\Task::where('status', 'completed')->count() }}</h4>
                                        <p class="mb-0">Completed Tasks</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ App\Models\Task::where('status', 'in_progress')->count() }}</h4>
                                        <p class="mb-0">In Progress</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-spinner fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ App\Models\Engineer::where('status', 'active')->count() }}</h4>
                                        <p class="mb-0">Active Engineers</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Sections -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Task Status Distribution
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="taskStatusChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>Task Priority Distribution
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="taskPriorityChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-clock me-2"></i>Engineer Performance
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Engineer</th>
                                                <th>Active Tasks</th>
                                                <th>Completed Tasks</th>
                                                <th>Completion Rate</th>
                                                <th>Avg. Completion Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(App\Models\Engineer::with(['assignedTasks'])->get() as $engineer)
                                                @php
                                                    $activeTasks = $engineer->assignedTasks->whereIn('status', ['pending', 'in_progress'])->count();
                                                    $completedTasks = $engineer->assignedTasks->where('status', 'completed')->count();
                                                    $totalTasks = $engineer->assignedTasks->count();
                                                    $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

                                                    // Calculate average completion time
                                                    $completedTasksWithTime = $engineer->assignedTasks->where('status', 'completed')->filter(function ($task) {
                                                        return $task->started_at && $task->completed_at;
                                                    });

                                                    $avgCompletionTime = 'N/A';
                                                    if ($completedTasksWithTime->count() > 0) {
                                                        $totalMinutes = $completedTasksWithTime->sum(function ($task) {
                                                            return $task->started_at->diffInMinutes($task->completed_at);
                                                        });
                                                        $avgMinutes = $totalMinutes / $completedTasksWithTime->count();
                                                        $avgCompletionTime = round($avgMinutes / 60, 1) . ' hours';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-primary rounded-circle p-2 me-2">
                                                                {{ strtoupper(substr($engineer->name, 0, 2)) }}
                                                            </span>
                                                            {{ $engineer->name }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ $activeTasks }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">{{ $completedTasks }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: {{ $completionRate }}%"
                                                                aria-valuenow="{{ $completionRate }}" aria-valuemin="0"
                                                                aria-valuemax="100">
                                                                {{ $completionRate }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $avgCompletionTime }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Overdue Tasks
                                </h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $overdueTasks = App\Models\Task::whereNotNull('due_date')
                                        ->where('due_date', '<', now())
                                        ->where('status', '!=', 'completed')
                                        ->with('engineer')
                                        ->orderBy('due_date', 'asc')
                                        ->take(10)
                                        ->get();
                                @endphp

                                @if($overdueTasks->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($overdueTasks as $task)
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ Str::limit($task->title, 30) }}</h6>
                                                        <p class="mb-1 small text-muted">
                                                            @if($task->engineer)
                                                                Assigned to: {{ $task->engineer->name }}
                                                            @else
                                                                <span class="text-warning">Unassigned</span>
                                                            @endif
                                                        </p>
                                                        <small class="text-danger">
                                                            <i class="fas fa-clock me-1"></i>
                                                            Due: {{ $task->due_date->format('M d, Y') }}
                                                            ({{ $task->due_date->diffForHumans() }})
                                                        </small>
                                                    </div>
                                                    <span
                                                        class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if(App\Models\Task::whereNotNull('due_date')->where('due_date', '<', now())->where('status', '!=', 'completed')->count() > 10)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('admin.tasks.index', ['status' => 'overdue']) }}"
                                                class="btn btn-sm btn-outline-danger">
                                                View All Overdue Tasks
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                                        <p>No overdue tasks!</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>Recent Task Activity
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Task</th>
                                                <th>Customer</th>
                                                <th>Engineer</th>
                                                <th>Status</th>
                                                <th>Priority</th>
                                                <th>Last Updated</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(App\Models\Task::with(['engineer'])->orderBy('updated_at', 'desc')->take(10)->get() as $task)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>{{ Str::limit($task->title, 40) }}</strong>
                                                            @if($task->device_type)
                                                                <br><small
                                                                    class="text-muted">{{ ucfirst($task->device_type) }}</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($task->customer_name)
                                                            <div>
                                                                {{ $task->customer_name }}
                                                                @if($task->customer_phone)
                                                                    <br><small class="text-muted">{{ $task->customer_phone }}</small>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Not specified</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($task->engineer)
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-primary rounded-circle p-1 me-2">
                                                                    {{ strtoupper(substr($task->engineer->name, 0, 2)) }}
                                                                </span>
                                                                {{ $task->engineer->name }}
                                                            </div>
                                                        @else
                                                            <span class="text-warning">Unassigned</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : ($task->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'info') }}">
                                                            {{ ucfirst($task->priority) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $task->updated_at->format('M d, Y') }}<br>
                                                        <small
                                                            class="text-muted">{{ $task->updated_at->format('h:i A') }}</small>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.tasks.show', $task) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-list me-2"></i>View All Tasks
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Task Status Chart
        const statusCtx = document.getElementById('taskStatusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                    {{ App\Models\Task::where('status', 'pending')->count() }},
                    {{ App\Models\Task::where('status', 'in_progress')->count() }},
                    {{ App\Models\Task::where('status', 'completed')->count() }},
                        {{ App\Models\Task::where('status', 'cancelled')->count() }}
                    ],
                    backgroundColor: [
                        '#6c757d',
                        '#0dcaf0',
                        '#198754',
                        '#dc3545'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Task Priority Chart
        const priorityCtx = document.getElementById('taskPriorityChart').getContext('2d');
        const priorityChart = new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: ['Low', 'Medium', 'High'],
                datasets: [{
                    label: 'Number of Tasks',
                    data: [
                    {{ App\Models\Task::where('priority', 'low')->count() }},
                    {{ App\Models\Task::where('priority', 'medium')->count() }},
                        {{ App\Models\Task::where('priority', 'high')->count() }}
                    ],
                    backgroundColor: [
                        '#0dcaf0',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderColor: [
                        '#0dcaf0',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Export to CSV function
        function exportToCSV() {
            // This is a simple implementation - in production, you'd want to implement proper server-side CSV generation
            const data = [
                ['Task Title', 'Status', 'Priority', 'Engineer', 'Customer', 'Created Date'],
                @foreach(App\Models\Task::with(['engineer'])->get() as $task)
                    [
                    '{{ addslashes($task->title) }}',
                    '{{ $task->status }}',
                    '{{ $task->priority }}',
                    '{{ $task->engineer ? addslashes($task->engineer->name) : "Unassigned" }}',
                    '{{ $task->customer_name ? addslashes($task->customer_name) : "Not specified" }}',
                    '{{ $task->created_at->format("Y-m-d H:i:s") }}'
                    ],
                @endforeach
        ];

            let csvContent = "data:text/csv;charset=utf-8,";
            data.forEach(function (rowArray) {
                let row = rowArray.join(",");
                csvContent += row + "\r\n";
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "tasks_report_" + new Date().toISOString().split('T')[0] + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

    <style>
        @media print {

            .btn-group,
            .btn {
                display: none !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                page-break-inside: avoid;
            }

            .chart-container {
                page-break-inside: avoid;
            }
        }

        .progress {
            background-color: #e9ecef;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }
    </style>
@endsection