<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmTask extends Model
{
    protected $table = 'crm_tasks';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'subject',
        'description',
        'assigned_to',
        'due_date',
        'status',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function complete(): bool
    {
        $this->status = 'completed';
        $this->completed_at = now();

        return $this->save();
    }
}
