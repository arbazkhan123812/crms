<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';
    
    protected $fillable = [
        // Contact Information
        'contact_owner', 'first_name', 'last_name', 'title', 'department',
        'lead_source', 'email', 'secondary_email', 'phone',
        'mobile', 'fax', 'date_of_birth',
        
        
        // Account Link
        'account_id',
        
        // Address
        'mailing_street', 'mailing_city', 'mailing_state', 'mailing_code', 'mailing_country',
        
        // Other
        'description', 'converted_from_lead', 'created_by'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function contactOwner()
    {
        return $this->belongsTo(User::class, 'contact_owner');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function convertedFromLead()
    {
        return $this->belongsTo(Lead::class, 'converted_from_lead');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getFullMailingAddressAttribute()
    {
        $address = [];
        if ($this->mailing_street) $address[] = $this->mailing_street;
        if ($this->mailing_city) $address[] = $this->mailing_city;
        if ($this->mailing_state) $address[] = $this->mailing_state;
        if ($this->mailing_code) $address[] = $this->mailing_code;
        if ($this->mailing_country) $address[] = $this->mailing_country;
        return implode(', ', $address);
    }
}