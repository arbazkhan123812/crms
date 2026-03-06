@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-gift text-primary mr-2"></i>
                    Holidays
                </h3>
                <p class="text-muted mb-0">Manage company holidays</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.holidays.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Holiday
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Recurring</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($holidays as $holiday)
                    <tr>
                        <td>{{ $holiday->formatted_date }}</td>
                        <td>{{ $holiday->day_name }}</td>
                        <td>{{ $holiday->name }}</td>
                        <td>{{ ucfirst($holiday->type) }}</td>
                        <td>
                            @if($holiday->is_recurring)
                                <span class="badge badge-info">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $holiday->is_active ? 'success' : 'secondary' }}">
                                {{ $holiday->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.holidays.edit', $holiday->id) }}" class="btn btn-sm btn-link">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.holidays.duplicate', $holiday->id) }}" class="btn btn-sm btn-link">
                                <i class="fas fa-copy"></i>
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