<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         Permission::firstOrCreate(['name' => 'edit_user', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'add_user', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete_user', 'guard_name' => 'web']);
    }
}
