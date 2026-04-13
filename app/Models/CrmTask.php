<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmTask extends Model
{
    protected $table = 'crm_tasks';

    public const STATUS_DEFERRED = 'deferred';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_LOW = 'low';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'account_id',
        'subject',
        'description',
        'assigned_to',
        'due_date',
        'status',
        'priority',
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

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DEFERRED => 'Deferred',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_LOW => 'Low',
        ];
    }

    public function complete(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();

        return $this->save();
    }
}
