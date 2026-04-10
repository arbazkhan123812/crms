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

    public function notes()
    {
        return $this->hasMany(CrmNote::class, 'entity_id')
            ->where('entity_type', 'account')
            ->orderBy('created_at', 'desc');
    }

    public function tasks()
    {
        return $this->hasMany(CrmTask::class, 'entity_id')
            ->where('entity_type', 'account')
            ->orderBy('due_date', 'asc');
    }

    public function calls()
    {
        return $this->hasMany(CrmCall::class, 'entity_id')
            ->where('entity_type', 'account')
            ->orderBy('call_date', 'desc');
    }

    public function meetings()
    {
        return $this->hasMany(CrmMeeting::class, 'entity_id')
            ->where('entity_type', 'account')
            ->orderBy('meeting_date', 'desc');
    }

    public function addNote($note)
    {
        return $this->notes()->create([
            'entity_type' => 'account',
            'note' => $note,
            'created_by' => auth()->id(),
        ]);
    }

    public function addTask($subject, $description, $assignedTo, $dueDate = null)
    {
        return $this->tasks()->create([
            'entity_type' => 'account',
            'subject' => $subject,
            'description' => $description,
            'assigned_to' => $assignedTo,
            'due_date' => $dueDate,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);
    }

    public function addCall($callType, $callPurpose, $notes, $duration, $status, $callDate, $callOwner = null)
    {
        return $this->calls()->create([
            'entity_type' => 'account',
            'call_type' => $callType,
            'call_purpose' => $callPurpose,
            'notes' => $notes,
            'duration' => $duration,
            'status' => $status,
            'call_date' => $callDate,
            'called_by' => auth()->id(),
            'call_owner' => $callOwner ?: auth()->id(),
        ]);
    }

    public function scheduleMeeting($title, $description, $meetingType, $location, $meetingLink, $meetingDate, $duration, $assignedTo)
    {
        return $this->meetings()->create([
            'entity_type' => 'account',
            'title' => $title,
            'description' => $description,
            'meeting_type' => $meetingType,
            'location' => $location,
            'meeting_link' => $meetingLink,
            'meeting_date' => $meetingDate,
            'duration' => $duration,
            'status' => 'scheduled',
            'assigned_to' => $assignedTo,
            'created_by' => auth()->id(),
        ]);
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
