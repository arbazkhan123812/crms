@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-calendar-alt text-primary mr-2"></i>
                    Leave Types
                </h3>
                <p class="text-muted mb-0">Manage leave types and policies</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.leave-types.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Leave Type
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Days/Year</th>
                        <th>Paid</th>
                        <th>Carry Forward</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveTypes as $type)
                    <tr>
                        <td>{{ $type->code }}</td>
                        <td>{{ $type->name }}</td>
                        <td>{{ $type->days_per_year }}</td>
                        <td>
                            @if($type->is_paid)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if($type->carry_forward)
                                {{ $type->max_carry_forward ?? 'Unlimited' }}
                            @else
                                No
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $type->is_active ? 'success' : 'secondary' }}">
                                {{ $type->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.leave-types.edit', $type->id) }}" class="btn btn-sm btn-link">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection