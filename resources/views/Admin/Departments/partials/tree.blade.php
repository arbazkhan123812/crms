<div class="tree-node level-{{ $level }}">
    <div class="node-content">
        <div class="d-flex align-items-center">
            <div class="icon-bg-light rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                <i class="fas fa-folder text-primary"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0 mr-2" style="font-weight: 600;">{{ $department->name }}</h6>
                    <span class="badge badge-light">{{ $department->code }}</span>
                </div>
                <div class="d-flex align-items-center mt-1">
                    <small class="text-muted mr-3">
                        <i class="fas fa-user-tie mr-1"></i>
                        Manager: {{ $department->manager->full_name ?? 'Not Assigned' }}
                    </small>
                    <small class="text-muted">
                        <i class="fas fa-users mr-1"></i>
                        {{ $department->employees_count ?? 0 }} Employees
                    </small>
                </div>
            </div>
            <div class="ml-3">
                <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
    
    @if($department->children->count() > 0)
        @foreach($department->children as $child)
            @include('admin.departments.partials.tree', ['department' => $child, 'level' => $level + 1])
        @endforeach
    @endif
</div>

<style>
.icon-bg-light {
    background: #f8f9fa;
}
</style>