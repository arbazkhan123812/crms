<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Operation;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
   
    public function index()
    {
        $roles = Role::all(['id', 'name']);
        $users = User::with('roles')->get(['id', 'username', 'role_id']); // adjust fields
        $modules = Module::with('operations')->get();

        return view('Admin.Permissions.index', compact('roles', 'users', 'modules'));
    }


    public function getPermissions(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if ($type === 'role') {
            $role = Role::findById($id);
            $permissions = $role->permissions->pluck('name')->map(function($name) {
                return $this->permissionNameToKey($name);
            })->toArray();

            return response()->json([
                'success' => true,
                'name' => $role->name,
                'permissions' => $permissions,
                'role_permissions' => [] 
            ]);
        }
        elseif ($type === 'user') {
            $user = User::find($id);
            $role = $user->roles->first(); 

            $directPermissions = $user->permissions->pluck('name')->map(function($name) {
                return $this->permissionNameToKey($name);
            })->toArray();

            $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->map(function($name) {
                return $this->permissionNameToKey($name);
            })->toArray();

            return response()->json([
                'success' => true,
                'name' => $user->username,
                'role_id' => $role?->id,
                'permissions' => $directPermissions,
                'role_permissions' => $rolePermissions
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid type']);
    }

  
    public function savePermissions(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $permissionKeys = $request->input('permissions', []); 

        $permissionNames = [];
        foreach ($permissionKeys as $key) {
            [$moduleId, $opId] = explode('_', $key);
            $operation = Operation::where('module_id', $moduleId)->where('id', $opId)->first();
            if ($operation) {
                $permissionNames[] = $operation->permission_name;
            }
        }

        if ($type === 'role') {
            $role = Role::findById($id);
            $role->syncPermissions($permissionNames);
            $message = 'Role permissions updated successfully.';
        }
        elseif ($type === 'user') {
            $user = User::find($id);
            $user->syncPermissions($permissionNames);
            $message = 'User direct permissions updated successfully.';
        }
        else {
            return response()->json(['success' => false, 'message' => 'Invalid type']);
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    
    public function getRoleName($roleId)
    {
        $role = Role::find($roleId);
        if ($role) {
            return response()->json(['name' => $role->name]);
        }
        return response()->json(['name' => '']);
    }

  
    public function copyRoleToUser(Request $request)
    {
        $userId = $request->input('user_id');
        $roleId = $request->input('role_id');

        $user = User::find($userId);
        $role = Role::findById($roleId);

        if (!$user || !$role) {
            return response()->json(['success' => false, 'message' => 'User or role not found']);
        }

        $permissionNames = $role->permissions->pluck('name')->toArray();
        $user->syncPermissions($permissionNames);

        return response()->json(['success' => true, 'message' => 'Role permissions copied to user successfully.']);
    }

  
    private function permissionNameToKey($permissionName)
    {
        $operation = Operation::whereHas('module', function($q) use ($permissionName) {
            $q->where('name', explode('.', $permissionName)[0] ?? '');
        })->where('name', explode('.', $permissionName)[1] ?? '')->first();

        return $operation ? $operation->module_id . '_' . $operation->id : null;
    }
}