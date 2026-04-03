<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'leads';
    
    protected $fillable = [
        'lead_image', 'lead_owner', 'first_name', 'last_name', 'title', 'phone', 'mobile',
        'lead_source', 'industry', 'annual_revenue', 'email_opt_out', 'company', 'email',
        'fax', 'website', 'lead_status', 'no_of_employees', 'rating', 'skype_id',
        'secondary_email', 'twitter', 'street', 'state', 'country', 'city', 'zip_code', 
        'description', 'created_by', 'updated_by'
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

    public function addNote($note, $type = 'internal')
    {
        return $this->notes()->create([
            'note' => $note,
            'type' => $type,
            'created_by' => auth()->id()
        ]);
    }

    public function getAllNotes()
    {
        return $this->notes;
    }

    // Tasks relationship
    public function tasks()
    {
        return $this->hasMany(LeadTask::class)->orderBy('due_date', 'asc');
    }

    // Pending tasks
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

    
    
}