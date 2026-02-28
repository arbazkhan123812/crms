@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-project-diagram text-primary mr-2"></i>
                    Department Hierarchy
                </h3>
                <p class="text-muted mb-0">Visual representation of your organization structure</p>
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
                <i class="fas fa-building text-primary mr-2"></i>
                Organization Chart
            </h5>
        </div>
        <div class="card-body">
            <div class="hierarchy-tree">
                @forelse($departments as $department)
                    @include('admin.departments.partials.tree', ['department' => $department, 'level' => 0])
                @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-sitemap fa-3x mb-3"></i>
                            <h5>No Departments Found</h5>
                            <p>Add departments to see the hierarchy</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.hierarchy-tree {
    position: relative;
}
.tree-node {
    position: relative;
    margin-left: 30px;
    border-left: 2px dashed #dee2e6;
    padding-left: 20px;
    padding-bottom: 15px;
}
.tree-node.level-0 {
    margin-left: 0;
    border-left: none;
}
.tree-node .node-content {
    position: relative;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.tree-node .node-content:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    border-color: #4e73df;
}
</style>
@endsection