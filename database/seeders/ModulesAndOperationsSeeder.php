<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModulesAndOperationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Users Module
        $usersModule = Module::create(['name' => 'users']);
        $operations = [
            ['view', 'View Users'],
            ['create', 'Create Users'],
            ['edit', 'Edit Users'],
            ['delete', 'Delete Users'],
        ];
        foreach ($operations as [$opName, $display]) {
            $op = $usersModule->operations()->create([
                'name' => $opName,
                'display_name' => $display,
            ]);
            $op->syncPermission();
        }

        // 2. Roles Module
        $rolesModule = Module::create(['name' => 'roles']);
        $operations = [
            ['view', 'View Roles'],
            ['create', 'Create Roles'],
            ['edit', 'Edit Roles'],
            ['delete', 'Delete Roles'],
        ];
        foreach ($operations as [$opName, $display]) {
            $op = $rolesModule->operations()->create([
                'name' => $opName,
                'display_name' => $display,
            ]);
            $op->syncPermission();
        }

        // 3. Permissions Module (for managing permissions)
        $permsModule = Module::create(['name' => 'permissions']);
        $operations = [
            ['view', 'View Permissions'],
            ['assign', 'Assign Permissions'],
        ];
        foreach ($operations as [$opName, $display]) {
            $op = $permsModule->operations()->create([
                'name' => $opName,
                'display_name' => $display,
            ]);
            $op->syncPermission();
        }

        // Add more modules as needed...
    }
}
