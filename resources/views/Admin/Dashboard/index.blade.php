@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-tachometer-alt text-primary mr-2"></i>
                    Admin Dashboard
                </h3>
                <p class="text-muted mb-0">Welcome back, Muhammad Arbaz! Here's your organization overview.</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary text-white rounded-circle mr-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Employees</p>
                            <h4 class="mb-0">248</h4>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> +12 this month</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success text-white rounded-circle mr-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Present Today</p>
                            <h4 class="mb-0">189</h4>
                            <small class="text-muted">76% of workforce</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning text-white rounded-circle mr-3">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">On Leave</p>
                            <h4 class="mb-0">12</h4>
                            <small class="text-muted">5% of workforce</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info text-white rounded-circle mr-3">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">New Hires</p>
                            <h4 class="mb-0">8</h4>
                            <small class="text-muted">This month</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart & Quick Actions -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="fas fa-chart-line text-primary mr-2"></i>Employee Overview</h6>
                </div>
                <div class="card-body">
                    <canvas id="employeeChart" height="200"></canvas>
                </div>
            </div>
            
            <!-- Recent Activities -->
            <div class="card">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="fas fa-history text-primary mr-2"></i>Recent Activities</h6>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex">
                            <span class="badge badge-success mr-3">NEW</span>
                            <div><strong>John Doe</strong> joined as Senior Developer</div>
                            <small class="text-muted ml-auto">5 min ago</small>
                        </div>
                    </div>
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex">
                            <span class="badge badge-warning mr-3">LEAVE</span>
                            <div><strong>Sarah Khan</strong> applied for sick leave</div>
                            <small class="text-muted ml-auto">1 hour ago</small>
                        </div>
                    </div>
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex">
                            <span class="badge badge-info mr-3">UPDATE</span>
                            <div><strong>Ali Raza</strong> profile updated</div>
                            <small class="text-muted ml-auto">3 hours ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Quick Actions & Pending -->
        <div class="col-lg-4">
            <!-- Pending Approvals -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="fas fa-hourglass-half text-warning mr-2"></i>Pending Approvals</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-3 py-2 d-flex justify-content-between">
                            <span><i class="fas fa-calendar-check text-warning mr-2"></i>Leave Requests</span>
                            <span class="badge badge-warning badge-pill">5</span>
                        </div>
                        <div class="list-group-item px-3 py-2 d-flex justify-content-between">
                            <span><i class="fas fa-clock text-info mr-2"></i>Attendance Regularization</span>
                            <span class="badge badge-info badge-pill">3</span>
                        </div>
                        <div class="list-group-item px-3 py-2 d-flex justify-content-between">
                            <span><i class="fas fa-money-bill text-success mr-2"></i>Expense Claims</span>
                            <span class="badge badge-success badge-pill">7</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="card">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="fas fa-calendar text-primary mr-2"></i>Upcoming Events</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-3 py-2">
                            <div class="d-flex">
                                <div class="bg-primary text-white rounded p-1 text-center mr-3" style="width: 40px;">
                                    <div class="small">MAR</div>
                                    <div class="font-weight-bold">15</div>
                                </div>
                                <div><strong>Team Meeting</strong><br><small class="text-muted">10:00 AM</small></div>
                            </div>
                        </div>
                        <div class="list-group-item px-3 py-2">
                            <div class="d-flex">
                                <div class="bg-success text-white rounded p-1 text-center mr-3" style="width: 40px;">
                                    <div class="small">MAR</div>
                                    <div class="font-weight-bold">20</div>
                                </div>
                                <div><strong>Ahmed's Birthday</strong><br><small class="text-muted">Marketing Dept</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-stats { border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.stats-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('employeeChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Employees',
            data: [220, 235, 240, 245, 248, 248],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            tension: 0.4
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>
@endsection