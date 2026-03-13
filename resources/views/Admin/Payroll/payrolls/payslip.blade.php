<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->full_name }} - {{ $payroll->month_name }} {{ $payroll->year }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            padding: 30px;
        }
        .payslip-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4e73df;
        }
        .company-name {
            color: #4e73df;
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .payslip-title {
            color: #6c757d;
            font-size: 18px;
        }
        .employee-details {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            width: 150px;
            color: #6c757d;
        }
        .detail-value {
            font-weight: 500;
            color: #343a40;
        }
        .salary-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .salary-table th {
            background: #4e73df;
            color: white;
            padding: 10px;
            font-weight: 500;
        }
        .salary-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .total-row {
            background: #f8f9fc;
            font-weight: 500;
        }
        .net-salary {
            font-size: 20px;
            color: #28a745;
            font-weight: 500;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <button class="btn btn-primary print-btn" onclick="window.print()">
        <i class="fas fa-print mr-1"></i> Print
    </button>

    <div class="payslip-container">
        <div class="header">
            <div class="company-name">{{ config('app.name') }}</div>
            <div class="payslip-title">Salary Slip for {{ $payroll->month_name }} {{ $payroll->year }}</div>
        </div>

        <div class="employee-details">
            <div class="detail-row">
                <span class="detail-label">Employee Name:</span>
                <span class="detail-value">{{ $payroll->employee->full_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Employee Code:</span>
                <span class="detail-value">{{ $payroll->employee->employee_code }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Department:</span>
                <span class="detail-value">{{ $payroll->employee->department->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Designation:</span>
                <span class="detail-value">{{ $payroll->employee->designation->title ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Pay Period:</span>
                <span class="detail-value">{{ $payroll->month_name }} {{ $payroll->year }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Working Days:</span>
                <span class="detail-value">{{ $payroll->present_days }} / {{ $payroll->working_days }}</span>
            </div>
        </div>

        <table class="salary-table">
            <thead>
                <tr>
                    <th>Earnings</th>
                    <th class="text-right">Amount (PKR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">{{ number_format($payroll->basic, 2) }}</td>
                </tr>
                @if($payroll->hra)
                <tr>
                    <td>House Rent Allowance (HRA)</td>
                    <td class="text-right">{{ number_format($payroll->hra, 2) }}</td>
                </tr>
                @endif
                @if($payroll->da)
                <tr>
                    <td>Dearness Allowance (DA)</td>
                    <td class="text-right">{{ number_format($payroll->da, 2) }}</td>
                </tr>
                @endif
                @if($payroll->conveyance)
                <tr>
                    <td>Conveyance Allowance</td>
                    <td class="text-right">{{ number_format($payroll->conveyance, 2) }}</td>
                </tr>
                @endif
                @if($payroll->medical)
                <tr>
                    <td>Medical Allowance</td>
                    <td class="text-right">{{ number_format($payroll->medical, 2) }}</td>
                </tr>
                @endif
                @if($payroll->special)
                <tr>
                    <td>Special Allowance</td>
                    <td class="text-right">{{ number_format($payroll->special, 2) }}</td>
                </tr>
                @endif
                @if($payroll->other_earnings)
                    @foreach($payroll->other_earnings as $earning)
                    <tr>
                        <td>{{ $earning['name'] }}</td>
                        <td class="text-right">{{ number_format($earning['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                @endif
                <tr class="total-row">
                    <td>Gross Earnings</td>
                    <td class="text-right">{{ number_format($payroll->gross_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="salary-table">
            <thead>
                <tr>
                    <th>Deductions</th>
                    <th class="text-right">Amount (PKR)</th>
                </tr>
            </thead>
            <tbody>
                @if($payroll->pf)
                <tr>
                    <td>Provident Fund (PF)</td>
                    <td class="text-right">{{ number_format($payroll->pf, 2) }}</td>
                </tr>
                @endif
                @if($payroll->esi)
                <tr>
                    <td>ESI</td>
                    <td class="text-right">{{ number_format($payroll->esi, 2) }}</td>
                </tr>
                @endif
                @if($payroll->pt)
                <tr>
                    <td>Professional Tax</td>
                    <td class="text-right">{{ number_format($payroll->pt, 2) }}</td>
                </tr>
                @endif
                @if($payroll->tds)
                <tr>
                    <td>TDS</td>
                    <td class="text-right">{{ number_format($payroll->tds, 2) }}</td>
                </tr>
                @endif
                @if($payroll->other_deductions)
                    @foreach($payroll->other_deductions as $deduction)
                    <tr>
                        <td>{{ $deduction['name'] }}</td>
                        <td class="text-right">{{ number_format($deduction['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                @endif
                <tr class="total-row">
                    <td>Total Deductions</td>
                    <td class="text-right">{{ number_format($payroll->total_deductions, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="text-right mt-4">
            <div class="net-salary">
                Net Salary: PKR {{ number_format($payroll->net_salary, 2) }}
            </div>
            <div class="mt-2 text-muted">
                @if($payroll->status == 'paid')
                    Payment Date: {{ $payroll->payment_date->format('d M Y') }}
                @endif
            </div>
        </div>

        <div class="footer">
            <p>This is a computer generated document. No signature required.</p>
            <p>Generated on {{ now()->format('d M Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>