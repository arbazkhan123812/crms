<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadNote extends Model
{
    use HasFactory;

    protected $table = 'lead_notes';

    protected $fillable = [
        'lead_id',
        'note',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship with Lead
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    // Relationship with User who created the note
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope for internal notes
    public function scopeInternal($query)
    {
        return $query->where('type', 'internal');
    }

    // Scope for public notes
    public function scopePublic($query)
    {
        return $query->where('type', 'public');
    }
}