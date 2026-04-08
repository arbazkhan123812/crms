<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    
    protected $fillable = [
        // Account Information
        'account_owner', 'name', 'account_site', 'parent_account_id', 'account_number',
        'type', 'industry', 'annual_revenue', 'rating', 'no_of_employees',
        'ticker_symbol', 'ownership', 'sic_code',
        
        // Contact Information
        'email', 'phone', 'fax', 'website',
        
        // Billing Address
        'street', 'city', 'state', 'country', 'zip_code',
        
        // Shipping Address
        'shipping_street', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_code',
        
        // Other
        'description', 'converted_from_lead', 'created_by'
    ];

    protected $casts = [
        'annual_revenue' => 'decimal:2',
        'no_of_employees' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function accountOwner()
    {
        return $this->belongsTo(User::class, 'account_owner');
    }

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

    public function parentAccount()
    {
        return $this->belongsTo(Account::class, 'parent_account_id');
    }

    public function childAccounts()
    {
        return $this->hasMany(Account::class, 'parent_account_id');
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $address = [];
        if ($this->street) $address[] = $this->street;
        if ($this->city) $address[] = $this->city;
        if ($this->state) $address[] = $this->state;
        if ($this->zip_code) $address[] = $this->zip_code;
        if ($this->country) $address[] = $this->country;
        return implode(', ', $address);
    }

    public function getFullShippingAddressAttribute()
    {
        $address = [];
        if ($this->shipping_street) $address[] = $this->shipping_street;
        if ($this->shipping_city) $address[] = $this->shipping_city;
        if ($this->shipping_state) $address[] = $this->shipping_state;
        if ($this->shipping_code) $address[] = $this->shipping_code;
        if ($this->shipping_country) $address[] = $this->shipping_country;
        return implode(', ', $address);
    }
}