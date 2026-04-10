<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmNote extends Model
{
    protected $table = 'crm_notes';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'note',
        'type',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
