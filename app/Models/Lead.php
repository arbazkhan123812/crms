<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use PDO;

class Lead extends Model
{
    protected $table = 'leads';
    
    protected $fillable = [
        'lead_image', 'lead_owner', 'first_name', 'last_name', 'title', 'phone', 'mobile',
        'lead_source', 'industry', 'annual_revenue', 'email_opt_out', 'company', 'email',
        'fax', 'website', 'lead_status', 'no_of_employees', 'rating', 'skype_id',
        'secondary_email', 'twitter', 'street', 'state', 'country', 'city', 'zip_code', 'description','created_by','updated_by'
    ];

    protected $casts = [
        'annual_revenue' => 'decimal:2',
        'email_opt_out' => 'boolean',
        'no_of_employees' => 'integer'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'lead_owner');
    }
    
    public function user(){
        return $this->belongsTo(User::class,'created_by');
    }
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

}