<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    
    protected $fillable = [
        'name', 'email', 'phone', 'website', 'industry', 'type',
        'annual_revenue', 'no_of_employees', 'street', 'city', 'state',
        'country', 'zip_code', 'description', 'converted_from_lead', 'created_by'
    ];

    public function convertedFromLead()
    {
        return $this->belongsTo(Lead::class, 'converted_from_lead');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}