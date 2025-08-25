@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-users me-2"></i>Engineers Management</h2>
                    <a href="{{ route('admin.engineers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Engineer
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Engineers</h5>
                    </div>
                    <div class="card-body">
                        @if($engineers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Skills</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($engineers as $engineer)
                                            <tr>
                                                <td>{{ $engineer->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <span class="badge bg-primary rounded-circle p-2">
                                                                {{ strtoupper(substr($engineer->name, 0, 2)) }}
                                                            </span>
                                                        </div>
                                                        {{ $engineer->name }}
                                                    </div>
                                                </td>
                                                <td>{{ $engineer->email }}</td>
                                                <td>{{ $engineer->phone }}</td>
                                                <td>
                                                    @php
                                                        $skills = json_decode($engineer->skills, true) ?? [];
                                                    @endphp
                                                    @if(count($skills) > 0)
                                                        @foreach(array_slice($skills, 0, 2) as $skill)
                                                            <span class="badge bg-info me-1">{{ $skill }}</span>
                                                        @endforeach
                                                        @if(count($skills) > 2)
                                                            <span class="badge bg-secondary">+{{ count($skills) - 2 }} more</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">No skills listed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($engineer->status === 'active')
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $engineer->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('admin.engineers.show', $engineer) }}"
                                                            class="btn btn-outline-info" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.engineers.edit', $engineer) }}"
                                                            class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger"
                                                            onclick="confirmDelete({{ $engineer->id }})" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>

                                                    <form id="delete-form-{{ $engineer->id }}"
                                                        action="{{ route('admin.engineers.destroy', $engineer) }}" method="POST"
                                                        style="display: none;">
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
                            @if($engineers->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $engineers->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Engineers Found</h5>
                                <p class="text-muted">Start by adding your first engineer to the system.</p>
                                <a href="{{ route('admin.engineers.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add First Engineer
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(engineerId) {
            if (confirm('Are you sure you want to delete this engineer? This action cannot be undone.')) {
                document.getElementById('delete-form-' + engineerId).submit();
            }
        }
    </script>
@endsection