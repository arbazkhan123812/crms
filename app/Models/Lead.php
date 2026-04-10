<?php

namespace App\Models;

use App\Models\LeadActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Lead extends Model
{
    protected $table = 'leads';

    protected $fillable = [
        'lead_image',
        'lead_owner',
        'first_name',
        'last_name',
        'title',
        'phone',
        'mobile',
        'lead_source',
        'industry',
        'annual_revenue',
        'email_opt_out',
        'company',
        'email',
        'fax',
        'website',
        'lead_status',
        'no_of_employees',
        'rating',
        'skype_id',
        'secondary_email',
        'twitter',
        'street',
        'state',
        'country',
        'city',
        'zip_code',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'annual_revenue' => 'decimal:2',
        'email_opt_out' => 'boolean',
        'no_of_employees' => 'integer'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'lead_owner');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function notes()
    {
        return $this->hasMany(LeadNote::class)->orderBy('created_at', 'desc');
    }

    public function addNote($note)
    {
        return $this->notes()->create([
            'note' => $note,
            'created_by' => auth()->id()
        ]);
    }

    public function getAllNotes()
    {
        return $this->notes;
    }

    public function tasks()
    {
        return $this->hasMany(LeadTask::class)->orderBy('due_date', 'asc');
    }

    public function pendingTasks()
    {
        return $this->tasks()->where('status', 'pending');
    }

    public function addTask($subject, $description, $assignedTo, $dueDate = null)
    {
        return $this->tasks()->create([
            'subject' => $subject,
            'description' => $description,
            'assigned_to' => $assignedTo,
            'due_date' => $dueDate,
            'status' => 'pending',
            'created_by' => auth()->id()
        ]);
    }

    public function calls()
    {
        return $this->hasMany(LeadCall::class)->orderBy('call_date', 'desc');
    }

    public function addCall($callType, $callPurpose, $notes, $duration, $status, $callDate, $callOwner = null)
    {
        return $this->calls()->create([
            'call_type' => $callType,
            'call_purpose' => $callPurpose,
            'notes' => $notes,
            'duration' => $duration,
            'status' => $status,
            'call_date' => $callDate,
            'called_by' => auth()->id(),
            'call_owner' => $callOwner ?: auth()->id()
        ]);
    }

    public function emails()
    {
        return $this->hasMany(LeadEmail::class)->orderBy('created_at', 'desc');
    }

    public function logEmail($from, $to, $subject, $body, $cc = null, $bcc = null)
    {
        return $this->emails()->create([
            'from_email' => $from,
            'to_email' => $to,
            'cc' => $cc,
            'bcc' => $bcc,
            'subject' => $subject,
            'body' => $body,
            'status' => 'sent',
            'sent_by' => auth()->id()
        ]);
    }

    public function loggedCalls()
    {
        return $this->calls()->where('status', 'completed');
    }

    public function scheduledCalls()
    {
        return $this->calls()->where('status', 'scheduled');
    }

    public function missedCalls()
    {
        return $this->calls()->where('status', 'missed');
    }
    
    public function meetings()
    {
        return $this->hasMany(LeadMeeting::class)->orderBy('meeting_date', 'desc');
    }

    public function upcomingMeetings()
    {
        return $this->meetings()->where('meeting_date', '>=', now())->where('status', 'scheduled');
    }

    public function completedMeetings()
    {
        return $this->meetings()->where('status', 'completed');
    }

    public function scheduleMeeting($title, $description, $meetingType, $location, $meetingLink, $meetingDate, $duration, $assignedTo)
    {
        return $this->meetings()->create([
            'title' => $title,
            'description' => $description,
            'meeting_type' => $meetingType,
            'location' => $location,
            'meeting_link' => $meetingLink,
            'meeting_date' => $meetingDate,
            'duration' => $duration,
            'status' => 'scheduled',
            'assigned_to' => $assignedTo,
            'created_by' => auth()->id()
        ]);
    }
    
    public function activities()
    {
        return $this->hasMany(LeadActivity::class)->orderBy('created_at', 'desc');
    }

    public function addActivity($type, $subject, $description = null, $dueDate = null, $assignedTo = null)
    {
        return $this->activities()->create([
            'type' => $type,
            'subject' => $subject,
            'description' => $description,
            'due_date' => $dueDate,
            'assigned_to' => $assignedTo ?? auth()->id(),
            'status' => $dueDate ? 'pending' : 'completed',
            'completed_at' => !$dueDate ? now() : null
        ]);
    }

    public function getActivitiesByType($type)
    {
        return $this->activities()->where('type', $type)->get();
    }

    public function pendingActivities()
    {
        return $this->activities()->where('status', 'pending')->where('due_date', '>=', now())->get();
    }

    public function overdueActivities()
    {
        return $this->activities()->where('status', 'pending')->where('due_date', '<', now())->get();
    }
    public function isConverted()
{
    return $this->lead_status === 'Converted';
}

// Get converted account
public function convertedAccount()
{
    return $this->hasOne(Account::class, 'converted_from_lead');
}

// Get converted contact
public function convertedContact()
{
    return $this->hasOne(Contact::class, 'converted_from_lead');
}

// Convert lead to account and contact
public function convertToAccountAndContact($accountData, $contactData)
{
    DB::beginTransaction();
    try {
        // Create Account
        $account = Account::create(array_merge($accountData, [
            'converted_from_lead' => $this->id,
            'created_by' => auth()->id()
        ]));

        // Create Contact
        $contact = Contact::create(array_merge($contactData, [
            'account_id' => $account->id,
            'converted_from_lead' => $this->id,
            'created_by' => auth()->id()
        ]));

        // Update lead status
        $this->lead_status = 'Converted';
        $this->save();

        // Add activity log
        $this->addActivity(
            'conversion',
            'Lead converted to Customer',
            "Account: {$account->name}\nContact: {$contact->first_name} {$contact->last_name}",
            null,
            auth()->id()
        );

        DB::commit();
        return ['success' => true, 'account' => $account, 'contact' => $contact];
    } catch (\Exception $e) {
        DB::rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
}
