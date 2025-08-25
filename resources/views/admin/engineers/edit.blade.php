@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-edit me-2"></i>Edit Engineer</h2>
                <a href="{{ route('admin.engineers.show', $engineer) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Details
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update Engineer Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.engineers.update', $engineer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $engineer->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $engineer->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $engineer->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="active" {{ old('status', $engineer->status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $engineer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 8 characters required if changing password</div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation">
                        </div>

                        <div class="mb-3">
                            <label for="skills" class="form-label">Skills</label>
                            @php
                                $currentSkills = json_decode($engineer->skills, true) ?? [];
                                $allSkills = [
                                    'Hardware Repair', 'Software Installation', 'Network Setup',
                                    'Data Recovery', 'Virus Removal', 'Laptop Repair',
                                    'Mobile Device Repair', 'Performance Optimization', 'Network Security'
                                ];
                            @endphp
                            <div class="row">
                                <div class="col-md-4">
                                    @foreach(array_slice($allSkills, 0, 3) as $skill)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skills[]" 
                                                   value="{{ $skill }}" id="skill{{ $loop->index }}"
                                                   {{ in_array($skill, old('skills', $currentSkills)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="skill{{ $loop->index }}">{{ $skill }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-4">
                                    @foreach(array_slice($allSkills, 3, 3) as $skill)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skills[]" 
                                                   value="{{ $skill }}" id="skill{{ $loop->index + 3 }}"
                                                   {{ in_array($skill, old('skills', $currentSkills)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="skill{{ $loop->index + 3 }}">{{ $skill }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-4">
                                    @foreach(array_slice($allSkills, 6, 3) as $skill)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skills[]" 
                                                   value="{{ $skill }}" id="skill{{ $loop->index + 6 }}"
                                                   {{ in_array($skill, old('skills', $currentSkills)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="skill{{ $loop->index + 6 }}">{{ $skill }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('skills')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.engineers.show', $engineer) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Engineer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
