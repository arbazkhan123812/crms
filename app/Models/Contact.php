<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';
    
    protected $fillable = [
        'account_id', 'first_name', 'last_name', 'title', 'email',
        'phone', 'mobile', 'skype_id', 'twitter', 'description',
        'converted_from_lead', 'created_by'
    ];

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

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}