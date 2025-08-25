@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-satellite-dish me-2"></i>Live Employee Tracking</h2>
                <div class="btn-group">
                    <button class="btn btn-primary" onclick="refreshLocations()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                    <button class="btn btn-success" onclick="toggleAutoRefresh()">
                        <i class="fas fa-play me-2"></i><span id="auto-refresh-text">Start Auto</span>
                    </button>
                    <button class="btn btn-info" onclick="centerMapOnAll()">
                        <i class="fas fa-expand me-2"></i>Fit All
                    </button>
                </div>
            </div>

            <!-- Status Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="online-count">{{ $activeEngineers->count() }}</h4>
                                    <p class="mb-0">Online Now</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x"></i>
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
                                    <h4 class="mb-0" id="tracking-count">{{ $activeEngineers->whereNotNull('latestLocation')->count() }}</h4>
                                    <p class="mb-0">GPS Active</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-map-marker-alt fa-2x"></i>
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
                                    <h4 class="mb-0" id="tasks-progress">
                                        {{ $activeEngineers->sum(function($engineer) { 
                                            return $engineer->assignedTasks->where('status', 'in_progress')->count(); 
                                        }) }}
                                    </h4>
                                    <p class="mb-0">Tasks Active</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-tasks fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="total-engineers">{{ App\Models\Engineer::where('status', 'active')->count() }}</h4>
                                    <p class="mb-0">Total Active</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-map-marked-alt me-2"></i>Real-Time Location Map
                            </h5>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="toggleHeatmap()">
                                    <i class="fas fa-fire me-1"></i>Heatmap
                                </button>
                                <button class="btn btn-outline-info" onclick="toggleTrails()">
                                    <i class="fas fa-route me-1"></i>Trails
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="map" style="height: 600px; border-radius: 8px;"></div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Map updates automatically every 30 seconds. Green markers = Online, Gray = Offline
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Active Engineers List -->
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-broadcast-tower me-2"></i>Live Status
                            </h5>
                            <span class="badge bg-success" id="last-update">Just now</span>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <div id="engineers-list">
                                @if($activeEngineers->count() > 0)
                                    @foreach($activeEngineers as $engineer)
                                        <div class="engineer-item mb-3 p-3 border rounded position-relative"
                                             data-engineer-id="{{ $engineer->user_id }}"
                                             data-lat="{{ $engineer->latestLocation?->latitude }}"
                                             data-lng="{{ $engineer->latestLocation?->longitude }}">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3">
                                                    <span class="badge bg-success rounded-circle p-2 position-relative">
                                                        {{ strtoupper(substr($engineer->name, 0, 2)) }}
                                                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                                                            <span class="visually-hidden">Online</span>
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $engineer->name }}</h6>
                                                    <p class="mb-1 small text-muted">
                                                        <i class="fas fa-phone me-1"></i>{{ $engineer->phone }}
                                                    </p>
                                                    
                                                    <!-- Work Session Info -->
                                                    @php
                                                        $todayLog = $engineer->logs()->whereDate('login_at', today())->whereNull('logout_at')->first();
                                                    @endphp
                                                    
                                                    @if($todayLog)
                                                        <p class="mb-1 small">
                                                            <i class="fas fa-clock text-info me-1"></i>
                                                            Working since {{ $todayLog->login_at->format('H:i') }}
                                                            <span class="text-success">({{ $todayLog->login_at->diffForHumans(null, true) }})</span>
                                                        </p>
                                                    @endif
                                                    
                                                    <!-- Current Task -->
                                                    @php
                                                        $currentTask = $engineer->assignedTasks->where('status', 'in_progress')->first();
                                                    @endphp
                                                    
                                                    @if($currentTask)
                                                        <p class="mb-1 small">
                                                            <i class="fas fa-tasks text-warning me-1"></i>
                                                            <strong>Current:</strong> {{ Str::limit($currentTask->title, 25) }}
                                                        </p>
                                                    @endif
                                                    
                                                    <!-- Location Status -->
                                                    @if($engineer->latestLocation)
                                                        <p class="mb-0 small">
                                                            <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                            GPS: {{ $engineer->latestLocation->recorded_at->diffForHumans() }}
                                                            <button class="btn btn-sm btn-link p-0 ms-1" onclick="focusOnEngineer({{ $engineer->user_id }})">
                                                                <i class="fas fa-crosshairs"></i>
                                                            </button>
                                                        </p>
                                                    @else
                                                        <p class="mb-0 small text-muted">
                                                            <i class="fas fa-map-marker-alt me-1"></i>No GPS data
                                                        </p>
                                                    @endif
                                                    
                                                    <!-- Tasks Summary -->
                                                    <div class="mt-2">
                                                        @php
                                                            $pending = $engineer->assignedTasks->where('status', 'pending')->count();
                                                            $inProgress = $engineer->assignedTasks->where('status', 'in_progress')->count();
                                                            $completed = $engineer->assignedTasks->where('status', 'completed')->count();
                                                        @endphp
                                                        
                                                        <span class="badge bg-secondary me-1">{{ $pending }} pending</span>
                                                        <span class="badge bg-info me-1">{{ $inProgress }} active</span>
                                                        <span class="badge bg-success">{{ $completed }} done</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Action Buttons -->
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @if($engineer->phone)
                                                            <li><a class="dropdown-item" href="tel:{{ $engineer->phone }}">
                                                                <i class="fas fa-phone me-2"></i>Call
                                                            </a></li>
                                                        @endif
                                                        <li><a class="dropdown-item" href="{{ route('admin.engineers.show', $engineer) }}">
                                                            <i class="fas fa-user me-2"></i>View Profile
                                                        </a></li>
                                                        @if($engineer->latestLocation)
                                                            <li><a class="dropdown-item" href="#" onclick="focusOnEngineer({{ $engineer->user_id }})">
                                                                <i class="fas fa-crosshairs me-2"></i>Find on Map
                                                            </a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                                        <h6 class="text-muted">No Engineers Online</h6>
                                        <p class="text-muted small">Engineers will appear here when they clock in</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>Tracking Stats
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h5 class="text-success mb-0" id="avg-response-time">2.3m</h5>
                                    <small class="text-muted">Avg Response</small>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-info mb-0" id="coverage-area">85%</h5>
                                    <small class="text-muted">Area Coverage</small>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Auto-refresh:</small>
                                <span class="badge bg-success" id="refresh-status">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let map;
        let engineerMarkers = {};
        let autoRefreshInterval;
        let isAutoRefreshActive = true;
        let lastUpdateTime = new Date();

        // Initialize map
        document.addEventListener('DOMContentLoaded', function () {
            initializeMap();
            loadEngineersOnMap();
            startAutoRefresh();
        });

        function initializeMap() {
            // Default center (you can change this to your city/area)
            const defaultLat = 40.7128;
            const defaultLng = -74.0060;

            map = L.map('map').setView([defaultLat, defaultLng], 12);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Add scale control
            L.control.scale().addTo(map);
        }

        function loadEngineersOnMap() {
            // Clear existing markers
            Object.values(engineerMarkers).forEach(marker => map.removeLayer(marker));
            engineerMarkers = {};

            @foreach($activeEngineers as $engineer)
                @if($engineer->latestLocation)
                    addEngineerMarker(
                        {{ $engineer->user_id }},
                        {{ $engineer->latestLocation->latitude }},
                        {{ $engineer->latestLocation->longitude }},
                        '{{ $engineer->name }}',
                        '{{ $engineer->phone ?? "N/A" }}',
                        '{{ $engineer->latestLocation->recorded_at->diffForHumans() }}',
                        true
                    );
                @endif
            @endforeach

            // Fit map to show all markers if any exist
            if (Object.keys(engineerMarkers).length > 0) {
                const group = new L.featureGroup(Object.values(engineerMarkers));
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        function addEngineerMarker(engineerId, lat, lng, name, phone, lastUpdate, isOnline) {
            const markerColor = isOnline ? 'green' : 'gray';
            const icon = L.divIcon({
                html: `
                    <div style="
                        background-color: ${markerColor};
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 12px;
                    ">
                        ${name.split(' ').map(n => n[0]).join('').toUpperCase()}
                    </div>
                `,
                className: 'custom-engineer-marker',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const marker = L.marker([lat, lng], { icon }).addTo(map);
            
            // Create popup content
            const popupContent = `
                <div class="engineer-popup">
                    <h6 class="mb-2">${name}</h6>
                    <p class="mb-1 small"><i class="fas fa-phone me-1"></i>${phone}</p>
                    <p class="mb-1 small"><i class="fas fa-clock me-1"></i>Last update: ${lastUpdate}</p>
                    <p class="mb-2 small"><i class="fas fa-signal me-1"></i>Status: <span class="badge bg-${isOnline ? 'success' : 'secondary'}">${isOnline ? 'Online' : 'Offline'}</span></p>
                    <div class="btn-group btn-group-sm w-100">
                        <a href="tel:${phone}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-phone"></i>
                        </a>
                        <button class="btn btn-outline-info btn-sm" onclick="getDirections(${lat}, ${lng})">
                            <i class="fas fa-directions"></i>
                        </button>
                    </div>
                </div>
            `;
            
            marker.bindPopup(popupContent);
            engineerMarkers[engineerId] = marker;

            // Add click event to highlight in sidebar
            marker.on('click', function() {
                highlightEngineerInSidebar(engineerId);
            });

            return marker;
        }

        function loadEngineerLocations() {
            fetch('/admin/api/engineers/locations')
                .then(response => response.json())
                .then(data => {
                    updateMapMarkers(data);
                    updateLastRefreshTime();
                })
                .catch(error => {
                    console.error('Error loading engineer locations:', error);
                    showNotification('Error refreshing data', 'error');
                });
        }

        function updateMapMarkers(engineers) {
            // Clear existing markers
            Object.values(engineerMarkers).forEach(marker => {
                map.removeLayer(marker);
            });
            engineerMarkers = {};

            // Add new markers
            engineers.forEach(engineer => {
                if (engineer.latitude && engineer.longitude) {
                    addEngineerMarker(
                        engineer.id,
                        engineer.latitude,
                        engineer.longitude,
                        engineer.name,
                        engineer.phone,
                        engineer.last_updated,
                        engineer.is_online
                    );
                }
            });

            // Fit map to show all markers if any exist
            if (Object.keys(engineerMarkers).length > 0) {
                const group = new L.featureGroup(Object.values(engineerMarkers));
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        function updateLastRefreshTime() {
            lastUpdateTime = new Date();
            const element = document.getElementById('last-update');
            if (element) {
                element.textContent = 'Just now';
            }
        }

        function startAutoRefresh() {
            if (!isAutoRefreshActive) {
                isAutoRefreshActive = true;
                const refreshBtn = document.getElementById('auto-refresh-text');
                const statusElement = document.getElementById('refresh-status');
                
                if (refreshBtn) refreshBtn.textContent = 'Stop Auto';
                if (statusElement) {
                    statusElement.textContent = 'Active';
                    statusElement.className = 'badge bg-success';
                }
                
                autoRefreshInterval = setInterval(() => {
                    loadEngineerLocations();
                    updateRelativeTime();
                }, 30000); // Refresh every 30 seconds
            }
        }

        function stopAutoRefresh() {
            if (isAutoRefreshActive) {
                isAutoRefreshActive = false;
                const refreshBtn = document.getElementById('auto-refresh-text');
                const statusElement = document.getElementById('refresh-status');
                
                if (refreshBtn) refreshBtn.textContent = 'Start Auto';
                if (statusElement) {
                    statusElement.textContent = 'Stopped';
                    statusElement.className = 'badge bg-secondary';
                }
                
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }
            }
        }

        function toggleAutoRefresh() {
            if (isAutoRefreshActive) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
            }
        }

        function updateRelativeTime() {
            const now = new Date();
            const diffMinutes = Math.floor((now - lastUpdateTime) / 1000 / 60);
            const element = document.getElementById('last-update');
            
            if (element) {
                if (diffMinutes === 0) {
                    element.textContent = 'Just now';
                } else if (diffMinutes === 1) {
                    element.textContent = '1 min ago';
                } else {
                    element.textContent = `${diffMinutes} mins ago`;
                }
            }
        }

        function centerMapOnAll() {
            if (Object.keys(engineerMarkers).length > 0) {
                const group = new L.featureGroup(Object.values(engineerMarkers));
                map.fitBounds(group.getBounds().pad(0.1));
            } else {
                showNotification('No engineers with GPS data to display', 'warning');
            }
        }

        function focusOnEngineer(engineerId) {
            const marker = engineerMarkers[engineerId];
            if (marker) {
                map.setView(marker.getLatLng(), 16);
                marker.openPopup();
                highlightEngineerInSidebar(engineerId);
            }
        }

        function highlightEngineerInSidebar(engineerId) {
            // Remove previous highlights
            document.querySelectorAll('.engineer-item').forEach(item => {
                item.classList.remove('border-primary', 'bg-light');
            });
            
            // Highlight current engineer
            const engineerItem = document.querySelector(`[data-engineer-id="${engineerId}"]`);
            if (engineerItem) {
                engineerItem.classList.add('border-primary', 'bg-light');
                engineerItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function getDirections(lat, lng) {
            const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
            window.open(url, '_blank');
        }

        function showNotification(message, type = 'info') {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }

        function refreshLocations() {
            const btn = event.target;
            const icon = btn.querySelector('i');

            // Add spinning animation
            icon.classList.add('fa-spin');
            btn.disabled = true;

            loadEngineerLocations();

            // Remove spinning animation after 2 seconds
            setTimeout(() => {
                icon.classList.remove('fa-spin');
                btn.disabled = false;
            }, 2000);
        }

        // Update relative times every minute
        setInterval(updateRelativeTime, 60000);

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'r':
                        e.preventDefault();
                        refreshLocations();
                        break;
                    case ' ':
                        e.preventDefault();
                        toggleAutoRefresh();
                        break;
                }
            }
        });
    </script>

    <style>
        .engineer-popup {
            min-width: 200px;
        }

        .engineer-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .engineer-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .engineer-item.border-primary {
            border-color: #0d6efd !important;
            background-color: #f8f9fa !important;
        }

        .custom-engineer-marker {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .badge {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #map {
                height: 400px !important;
            }
            
            .engineer-item {
                margin-bottom: 0.5rem !important;
                padding: 0.75rem !important;
            }
        }
    </style>
@endsection