@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit Department
                </h3>
                <p class="text-muted mb-0">Update department information</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0" style="font-weight: 500;">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                Department Information
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                @csrf
@if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Department Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $department->code) }}" required>
                            @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Department Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $department->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Parent Department</label>
                            <select class="form-control form-control-sm" id="parent_id" name="parent_id">
                                <option value="">None (Root Department)</option>
                                @foreach($parentDepartments as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $department->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Department Manager</label>
                            <select class="form-control form-control-sm" id="manager_id" name="manager_id">
                                <option value="">Select Manager</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id', $department->manager_id) == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control form-control-sm" id="description" name="description" rows="3">{{ old('description', $department->description) }}</textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                    {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active Status</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-4 mb-0 text-right">
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-light btn-sm px-4">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-5 ml-2">
                        <i class="fas fa-save mr-1"></i> Update Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection