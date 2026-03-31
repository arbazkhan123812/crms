<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {

        $this->middleware('permission:users_save', ['only' => ['save_user']]);

        $this->middleware('permission:users_delete', ['only' => ['delete_user']]);
    }
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        return view('Admin.Users.index', compact(['users', 'roles']));
    }
    public function save_user(Request $request)
    {
        $id = $request->input('id');

        $data = [
            'username'  => $request->input('username'),
            'full_name' => $request->input('full_name'),
            'email'     => $request->input('email'),
            'status'    => $request->input('status'),
        ];

        if ($request->input('password')) {
            $data['password'] = bcrypt($request->input('password'));
        }

        if ($id) {

         

            $user = User::find($id);
            $user->update($data);

            if ($request->has('role_id')) {
                $role = Role::find($request->input('role_id'));

                $user->syncRoles($role->name);
            }

            return response()->json(['success' => true, 'message' => 'User Updated successfully!']);
        } else {

            $user = User::create($data);

            if ($request->has('role_id')) {
                $user->assignRole($request->input('role_id'));
            }

            return response()->json(['success' => true, 'message' => 'User saved successfully!']);
        }
    }
    public function delete_user($id)
    {
        $delete = User::destroy($id);

        if ($delete) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }
    }
    public function get_user($id)
    {
        $user = User::find($id);

        if ($user) {
            echo json_encode(['success' => true, 'data' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to get user.']);
        }
    }
}
