<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Operation extends Model
{
    protected $fillable = ['module_id', 'name', 'display_name', 'guard_name'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function getPermissionNameAttribute()
    {
        return $this->module->name . '.' . $this->name;
    }

    public function syncPermission()
    {
        $permissionName = $this->permission_name;
        Permission::findOrCreate($permissionName, $this->guard_name);
    }
}