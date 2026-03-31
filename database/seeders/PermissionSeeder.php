<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'user_view',
            'user_create', 
            'user_edit',
            'user_delete',
            
            // Role Management
            'role_view',
            'role_create',
            'role_edit',
            'role_delete',
            
            // Permission Management
            'permission_view',
            'permission_assign',
            
            // Dashboard
            'dashboard_view',
            
            // Settings
            'settings_view',
            'settings_edit',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        $user = Role::create(['name' => 'User', 'guard_name' => 'web']);

        // Assign all permissions to Super Admin
        $superAdmin->givePermissionTo(Permission::all());

        // Assign permissions to Admin
        $admin->givePermissionTo([
            'user_view', 'user_create', 'user_edit',
            'role_view',
            'dashboard_view',
            'settings_view'
        ]);

        // Assign permissions to Manager
        $manager->givePermissionTo([
            'user_view',
            'dashboard_view'
        ]);

        // Assign permissions to User
        $user->givePermissionTo([
            'dashboard_view'
        ]);

        // Create a Super Admin user (optional)
        $superAdminUser = User::create([
            'username' => 'superadmin',
            'full_name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'status' => 1
        ]);
        $superAdminUser->assignRole('Super Admin');

        // Create an Admin user
        $adminUser = User::create([
            'username' => 'admin',
            'full_name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'status' => 1
        ]);
        $adminUser->assignRole('Admin');
    }
}