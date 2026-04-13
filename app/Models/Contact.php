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

    public function notes()
    {
        return $this->hasMany(CrmNote::class, 'entity_id')
            ->where('entity_type', 'contact')
            ->orderBy('created_at', 'desc');
    }

    public function tasks()
    {
        return $this->hasMany(CrmTask::class, 'entity_id')
            ->where('entity_type', 'contact')
            ->orderBy('due_date', 'asc');
    }

    public function calls()
    {
        return $this->hasMany(CrmCall::class, 'entity_id')
            ->where('entity_type', 'contact')
            ->orderBy('call_date', 'desc');
    }

    public function meetings()
    {
        return $this->hasMany(CrmMeeting::class, 'entity_id')
            ->where('entity_type', 'contact')
            ->orderBy('meeting_date', 'desc');
    }

    public function addNote($note)
    {
        return $this->notes()->create([
            'entity_type' => 'contact',
            'note' => $note,
            'created_by' => auth()->id(),
        ]);
    }

    public function addTask($subject, $description, $assignedTo, $dueDate = null)
    {
        return $this->tasks()->create([
            'entity_type' => 'contact',
            'account_id' => $this->account_id,
            'subject' => $subject,
            'description' => $description,
            'assigned_to' => $assignedTo,
            'due_date' => $dueDate,
            'status' => CrmTask::STATUS_DEFERRED,
            'priority' => CrmTask::PRIORITY_NORMAL,
            'created_by' => auth()->id(),
        ]);
    }

    public function addCall($callType, $callPurpose, $notes, $duration, $status, $callDate, $callOwner = null)
    {
        return $this->calls()->create([
            'entity_type' => 'contact',
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
            'entity_type' => 'contact',
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
