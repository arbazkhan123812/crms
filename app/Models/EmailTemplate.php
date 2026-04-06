<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';
    
    protected $fillable = ['name', 'subject', 'body', 'category', 'created_by'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}