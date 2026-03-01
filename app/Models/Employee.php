<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    // app/Models/Employee.php

    protected $fillable = [
        'company_id',
        'user_id',
        'employee_code',
        'first_name',
        'last_name',
        'full_name',
        'middle_name',
        'gender',
        'birth_date',
        'email',
        'personal_email',
        'cnic',
        'phone',
        'alternate_phone',
        'whatsapp_number',
        'emergency_phone',
        'present_address',
        'present_address_line1',
        'present_address_line2',
        'permanent_address',
        'permanent_address_line1',
        'permanent_address_line2',
        'city',
        'state',
        'country',
        'nationality',
        'postal_code',
        'religion',
        'department_id',
        'designation_id',
        'work_location',
        'reporting_to_id',
        'is_reporting_manager',
        'employment_type',
        'employee_level',
        'joining_date',
        'confirmation_date',
        'resignation_date',
        'exit_date',
        'exit_reason',
        'profile_image',
        'status',
        'experience_status',
        'ctc',
        'seat_location',
        'extension',
        'father_name',
        'mother_name',
        'hobbies',
        'marital_status',
        'blood_group',
        'emergency_contact_name',
        'emergency_relation',
        'bank_name',
        'account_holder_name',
        'account_number',
        'basic_salary',
        'hra',
        'da',
        'conveyance',
        'medical_allowance',
        'special_allowance',
        'probation_period',
        'notice_period',
        'documents',
        'iban'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'joining_date' => 'date',
        'confirmation_date' => 'date',
        'resignation_date' => 'date',
        'exit_date' => 'date',
        'is_reporting_manager' => 'boolean',
        'ctc' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'da' => 'decimal:2',
        'conveyance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'documents' => 'array'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class,'department_id','id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function reportingTo()
    {
        return $this->belongsTo(Employee::class, 'reporting_to_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'reporting_to_id');
    }

    public function education()
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function experience()
    {
        return $this->hasMany(EmployeeExperience::class);
    }

    public function assets()
    {
        return $this->hasMany(EmployeeAsset::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function getTotalExperienceAttribute()
    {
        $totalDays = 0;
        foreach ($this->experience as $exp) {
            $from = $exp->from_date;
            $to = $exp->to_date ?? now();
            $totalDays += $from->diffInDays($to);
        }
        return floor($totalDays / 365) . ' years ' . floor(($totalDays % 365) / 30) . ' months';
    }
}
