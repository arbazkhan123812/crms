<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    public const STAGES = [
        'New Inquiry',
        'Contacted & Qualified',
        'Negotiation / Quoting',
        'In Preparation',
        'Ready for Collection / Delivery',
        'Closed Won',
    ];

    public const SOURCES = [
        'Website',
        'Referral',
        'Email Campaign',
        'Social Media',
        'Cold Call',
        'Walk-in',
        'Existing Customer',
        'Other',
    ];

    protected $fillable = [
        'deal_owner',
        'amount',
        'account_id',
        'stage',
        'probability',
        'deal_source',
        'contact_id',
        'closing_date',
        'description',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'probability' => 'integer',
        'closing_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'deal_owner');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
