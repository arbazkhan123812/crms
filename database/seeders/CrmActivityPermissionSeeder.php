<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CrmActivityPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $registrar = app()[\Spatie\Permission\PermissionRegistrar::class];
        $registrar->forgetCachedPermissions();

        $permissions = [
            'calls_view',
            'calls_create',
            'calls_edit',
            'calls_delete',
            'meetings_view',
            'meetings_create',
            'meetings_edit',
            'meetings_delete',
        ];

        $permissionModels = collect($permissions)
            ->map(fn ($permission) => Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]));

        $registrar->forgetCachedPermissions();

        foreach (['Super Admin', 'Admin', 'admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $role->givePermissionTo(
                    $permissionModels->where('guard_name', $role->guard_name)->all()
                );
            }
        }
    }
}
