<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmMeeting extends Model
{
    protected $table = 'crm_meetings';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'title',
        'description',
        'meeting_type',
        'location',
        'meeting_link',
        'meeting_date',
        'duration',
        'status',
        'notes',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'datetime',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function complete(?string $notes = null): bool
    {
        $this->status = 'completed';

        if ($notes) {
            $this->notes = $notes;
        }

        return $this->save();
    }
}
