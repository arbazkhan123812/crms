@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit Designation
                </h3>
                <p class="text-muted mb-0">Update designation information</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.designations.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0" style="font-weight: 500;">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                Designation Information
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.designations.update', $designation) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Designation Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $designation->code) }}" required>
                            @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Designation Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $designation->title) }}" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Department</label>
                            <select class="form-control form-control-sm" id="department_id" name="department_id">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $designation->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Grade/Level</label>
                            <input type="text" class="form-control form-control-sm" id="grade" name="grade" 
                                   value="{{ old('grade', $designation->grade) }}">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Minimum Salary (PKR)</label>
                            <input type="number" class="form-control form-control-sm" id="min_salary" name="min_salary" 
                                   value="{{ old('min_salary', $designation->min_salary) }}" placeholder="50000">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Maximum Salary (PKR)</label>
                            <input type="number" class="form-control form-control-sm" id="max_salary" name="max_salary" 
                                   value="{{ old('max_salary', $designation->max_salary) }}" placeholder="150000">
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control form-control-sm" id="description" name="description" rows="3">{{ old('description', $designation->description) }}</textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                    {{ old('is_active', $designation->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active Status</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-4 mb-0 text-right">
                    <a href="{{ route('admin.designations.index') }}" class="btn btn-light btn-sm px-4">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-5 ml-2">
                        <i class="fas fa-save mr-1"></i> Update Designation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection