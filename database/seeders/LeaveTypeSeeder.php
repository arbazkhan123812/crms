<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run()
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'AL',
                'description' => 'Paid annual vacation leave',
                'days_per_year' => 14,
                'is_paid' => true,
                'carry_forward' => true,
                'max_carry_forward' => 7,
                'requires_approval' => true,
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'description' => 'Paid sick leave',
                'days_per_year' => 10,
                'is_paid' => true,
                'carry_forward' => false,
                'requires_approval' => true,
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Casual Leave',
                'code' => 'CL',
                'description' => 'Paid casual leave for urgent matters',
                'days_per_year' => 6,
                'is_paid' => true,
                'carry_forward' => false,
                'requires_approval' => true,
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'ML',
                'description' => 'Paid maternity leave',
                'days_per_year' => 90,
                'is_paid' => true,
                'carry_forward' => false,
                'requires_approval' => true,
                'requires_document' => true,
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PL',
                'description' => 'Paid paternity leave',
                'days_per_year' => 10,
                'is_paid' => true,
                'carry_forward' => false,
                'requires_approval' => true,
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UL',
                'description' => 'Unpaid leave',
                'days_per_year' => 0,
                'is_paid' => false,
                'carry_forward' => false,
                'requires_approval' => true,
                'is_active' => true,
                'created_by' => 1
            ]
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::create($type);
        }
    }
}