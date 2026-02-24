<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $adminRole = Role::where('name', 'admin')->first(); // existing role fetch
        if ($adminRole) {
            $adminRole->givePermissionTo(['add_user', 'edit_user']); // assign existing permissions
        }
    }
}
