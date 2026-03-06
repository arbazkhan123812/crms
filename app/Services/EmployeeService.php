<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    public function generateEmployeeCode(Company $company)
    {
        $prefix = 'EMP';
        $year = date('Y');
        $lastEmployee = Employee::whereYear('created_at', $year)
                        ->latest()
                        ->first();
        
        if ($lastEmployee) {
            $lastCode = $lastEmployee->employee_code;
            $number = intval(substr($lastCode, -4)) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . '-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function createUserAccount($employee, $password = null)
    {
        $user = User::create([
            'employee_id' => $employee->id,
            'username' => $employee->first_name,
            'full_name' => $employee->full_name,
            'email' => $employee->email,
            'password' => Hash::make($password ?? 'password'),
            'is_active' => true
        ]);

        // Assign default role
        $user->assignRole('employee');

        return $user;
    }

    public function calculateAge($birthDate)
    {
        return $birthDate->age;
    }

    public function calculateExperience($joiningDate)
    {
        return $joiningDate->diffInYears(now()) . ' years ' . 
               $joiningDate->diffInMonths(now()) % 12 . ' months';
    }

    public function getReportingHierarchy(Employee $employee)
    {
        $hierarchy = [];
        $current = $employee->reportingTo;
        
        while ($current) {
            $hierarchy[] = $current;
            $current = $current->reportingTo;
        }
        
        return $hierarchy;
    }

    public function getTeamMembers(Employee $manager)
    {
        return Employee::where('reporting_to_id', $manager->id)
                      ->with(['department', 'designation'])
                      ->get();
    }

    public function uploadDocument(Employee $employee, $file, $type)
    {
        $path = $file->store('employees/' . $employee->id . '/documents', 'public');
        
        $documents = $employee->documents ?? [];
        $documents[] = [
            'type' => $type,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_at' => now()->toDateTimeString()
        ];
        
        $employee->update(['documents' => $documents]);
        
        return $path;
    }
}