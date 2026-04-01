<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:permissions_view', ['only' => ['index']]);

        // $this->middleware('permission:permissions_save', ['only' => ['savePermissions']]);

        // $this->middleware('permission:permissions_userpermissions', ['only' => ['getPermissions']]);
        // $this->middleware('permission:permissions_userpermissions', ['only' => ['getAllPermissions']]);

        // $this->middleware('permission:users_delete', ['only' => ['delete_user']]);
    }
    public function index()
    {
        $roles = Role::all();
        $users = User::all();
        $permissions = Permission::all();

        return view('Admin.Permissions.index', compact('roles', 'users', 'permissions'));
    }

    public function getRoleName($roleId)
    {
        $role = Role::find($roleId);
        if ($role) {
            return response()->json(['name' => $role->name]);
        }
        return response()->json(['name' => '']);
    }
    public function getPermissions(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if ($type === 'role') {
            $role = Role::findById($id);
            $permissions = $role->permissions->pluck('name')->toArray();

            return response()->json([
                'success' => true,
                'name' => $role->name,
                'permissions' => $permissions,
                'role_permissions' => []
            ]);
        } elseif ($type === 'user') {
            $user = User::find($id);


            // IMPORTANT: Get user's role permissions
            $userRoles = $user->roles; // User ke saare roles


            $rolePermissions = [];

            foreach ($userRoles as $role) {
                $rolePerms = $role->permissions->pluck('name')->toArray();

                $rolePermissions = array_merge($rolePermissions, $rolePerms);
            }
            $rolePermissions = array_unique($rolePermissions); // Remove duplicates

            // Get direct permissions
            $directPermissions = $user->permissions->pluck('name')->toArray();

            return response()->json([
                'success' => true,
                'name' => $user->username ?? $user->email,
                'permissions' => $directPermissions,
                'role_permissions' => $rolePermissions,
                'role_id' => $user->roles->first()?->id,
                'roles' => $user->roles->pluck('name')->toArray()
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid type']);
    }

    public function savePermissions(Request $request)
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $type = $request->input('type');

        $id = $request->input('id');
        $permissions = $request->input('permissions', []);

        if ($type === 'role') {
            $role = Role::findById($id);
            $role->syncPermissions($permissions);
            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully.'
            ]);
        } elseif ($type === 'user') {
            $user = User::find($id);
            $user->syncPermissions($permissions);
            return response()->json([
                'success' => true,
                'message' => 'User permissions updated successfully.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid type']);
    }

    public function getAllPermissions()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }
}
