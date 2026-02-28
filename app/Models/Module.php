<?php

namespace App\Models;

use App\Models\Operation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'guard_name'];

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
}
