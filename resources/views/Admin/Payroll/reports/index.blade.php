@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-chart-pie text-primary mr-2"></i>
                    Payroll Reports
                </h3>
                <p class="text-muted mb-0">Year {{ $year }}</p>
            </div>
            <div class="col-auto">
                <select class="form-control form-control-sm" style="width: 100px;" id="yearSelect">
                    @foreach(range(date('Y')-2, date('Y')+1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt text-primary mr-2"></i>Monthly Salary Summary</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Month</th>
                                    <th class="text-center">Employees</th>
                                    <th class="text-right">Total Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyData as $data)
                                <tr>
                                    <td>{{ $data['month'] }}</td>
                                    <td class="text-center">{{ $data['employees'] }}</td>
                                    <td class="text-right">PKR {{ number_format($data['total_salary'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="fas fa-building text-primary mr-2"></i>Department-wise Salary</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Department</th>
                                    <th class="text-center">Employees</th>
                                    <th class="text-right">Total Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departmentWise as $dept => $data)
                                <tr>
                                    <td>{{ $dept }}</td>
                                    <td class="text-center">{{ $data['count'] }}</td>
                                    <td class="text-right">PKR {{ number_format($data['total'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-chart-line text-primary mr-2"></i>Salary Trend</h6>
                    <div>
                        <button class="btn btn-sm btn-success" onclick="exportReport()">
                            <i class="fas fa-file-excel mr-1"></i> Export
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="printReport()">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salaryChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$('#yearSelect').change(function() {
    window.location.href = '{{ route("admin.payroll.reports.index") }}?year=' + $(this).val();
});

new Chart(document.getElementById('salaryChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
        datasets: [{
            label: 'Total Salary',
            data: {!! json_encode(array_column($monthlyData, 'total_salary')) !!},
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'PKR ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

function exportReport() {
    window.location.href = '{{ route("admin.payroll.reports.export") }}?year=' + $('#yearSelect').val();
}

function printReport() {
    window.print();
}
</script>
@endsection