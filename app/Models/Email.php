<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table = 'emails';
    
    protected $fillable = [
        'from_email', 'to_email', 'cc', 'bcc', 'subject', 'body',
        'entity_type', 'entity_id', 'status', 'sent_by'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function entity()
    {
        if ($this->entity_type === 'account') {
            return $this->belongsTo(Account::class, 'entity_id');
        } elseif ($this->entity_type === 'contact') {
            return $this->belongsTo(Contact::class, 'entity_id');
        } elseif ($this->entity_type === 'lead') {
            return $this->belongsTo(Lead::class, 'entity_id');
        }
        return null;
    }
}