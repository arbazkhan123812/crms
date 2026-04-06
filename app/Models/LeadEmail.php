<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadEmail extends Model
{
    protected $table = 'lead_emails';
    
    protected $fillable = [
        'lead_id', 'from_email', 'to_email', 'cc', 'bcc', 
        'subject', 'body', 'status', 'sent_by'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}