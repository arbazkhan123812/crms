<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAsset extends Model
{
    protected $table = 'employee_assets';

    protected $fillable = [
        'employee_id', 'asset_type', 'asset_name', 'serial_no', 
        'status', 'given_on', 'returned_on', 'remarks'
    ];

    protected $casts = [
        'given_on' => 'date',
        'returned_on' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}