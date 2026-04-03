@extends('layout.admin')

@section('content')
<div class="content">
    <!-- PAGE HEADER -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-tachometer-alt text-primary mr-2"></i>Dashboard Overview
                </h3>
                <p class="text-muted mb-0">Welcome back! Here is what's happening with your leads today.</p>
            </div>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 custom-shadow rounded-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 font-weight-medium">Total Leads</p>
                            <h3 class="mb-0 font-weight-bold text-dark">{{ $totalLeads }}</h3>
                        </div>
                        <div class="rounded-circle text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #e0e7ff;">
                            <i class="fas fa-users fa-lg" style="color: #35394F;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 custom-shadow rounded-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 font-weight-medium">New This Month</p>
                            <h3 class="mb-0 font-weight-bold text-dark">{{ $newLeads }}</h3>
                        </div>
                        <div class="rounded-circle text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #dcfce7;">
                            <i class="fas fa-chart-line text-success fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 custom-shadow rounded-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 font-weight-medium">Qualified Leads</p>
                            <h3 class="mb-0 font-weight-bold text-dark">{{ $qualifiedLeads }}</h3>
                        </div>
                        <div class="rounded-circle text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #fef3c7;">
                            <i class="fas fa-star text-warning fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 custom-shadow rounded-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 font-weight-medium">Total Users</p>
                            <h3 class="mb-0 font-weight-bold text-dark">{{ $totalUsers }}</h3>
                        </div>
                        <div class="rounded-circle text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #e0f2fe;">
                            <i class="fas fa-user-shield text-info fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS ROW -->
    <div class="row">
        <!-- Line Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 custom-shadow rounded-lg h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-chart-area text-primary mr-2"></i>Revenue & Growth Overview</h6>
                </div>
                <div class="card-body">
                    <div id="revenueChart" style="height: 320px;"></div>
                </div>
            </div>
        </div>
        <!-- Donut Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 custom-shadow rounded-lg h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-chart-pie text-primary mr-2"></i>Leads by Status</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="leadsStatusChart" style="width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Revenue Chart (Using realistic dummy data for aesthetic)
        var revenueOptions = {
            series: [{ name: 'Income', data: [31000, 40000, 28000, 51000, 42000, 89000, 100000] },
                     { name: 'Expenses', data: [11000, 32000, 25000, 32000, 34000, 42000, 41000] }],
            chart: { height: 320, type: 'area', toolbar: { show: false }, fontFamily: 'inherit' },
            colors: ['#35394F', '#0ea5e9'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'], axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { formatter: function (value) { return "$" + value / 1000 + "k"; } } },
            legend: { position: 'top', horizontalAlign: 'right' },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 100] } }
        };
        new ApexCharts(document.querySelector("#revenueChart"), revenueOptions).render();

        // Leads Status Donut Chart (Dynamic from DB)
        var leadsStatusData = @json($leadsByStatus);
        var labels = Object.keys(leadsStatusData);
        var series = Object.values(leadsStatusData);
        
        // Fallback for empty database
        if(series.length === 0) {
            labels = ['New', 'Contacted', 'Qualified', 'Lost'];
            series = [10, 15, 5, 2];
        }

        var statusOptions = {
            series: series,
            labels: labels,
            chart: { type: 'donut', height: 320, fontFamily: 'inherit' },
            colors: ['#35394F', '#0ea5e9', '#10b981', '#f43f5e', '#f59e0b', '#64748b'],
            plotOptions: { pie: { donut: { size: '75%', labels: { show: true, name: { show: true }, value: { show: true } } } } },
            dataLabels: { enabled: false },
            legend: { position: 'bottom' },
            stroke: { show: false }
        };
        new ApexCharts(document.querySelector("#leadsStatusChart"), statusOptions).render();
    });
</script>

<style>
    .rounded-lg { border-radius: 0.75rem !important; }
    .custom-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important; }
    .font-weight-medium { font-weight: 500; }
    .font-weight-bold { font-weight: 600; }
    .page-title { font-size: 1.4rem; font-weight: 600; }
</style>
@endsection
