<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'guard_name',
    ];

    public $timestamps = true; 

    protected $casts = [
        'status' => 'integer',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

}
