<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call(PermissionSeeder::class);

        // Step 2: Roles & Assign Permissions
        $this->call(RoleSeeder::class);

        // Step 3: Users (optional)
        \App\Models\User::factory(10)->create();
        
        $adminUser = \App\Models\User::first(); // ya specific user
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
        if ($adminUser && $adminRole) {
            $adminUser->assignRole($adminRole);
        }
    }
}
