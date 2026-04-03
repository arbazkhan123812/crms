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
        $this->middleware('permission:users_view', ['only' => ['index']]);

        $this->middleware('permission:users_delete', ['only' => ['delete_user']]);
    }
    public function index()
    {
        $query = User::with('role');

        if (!Auth::user()->hasRole('Admin')) {
            $query->where('created_by', Auth::user()->id);
        }

        $users = $query->latest()->get();
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
            'role_id'    => $request->input('role_id'),
            'created_by' => Auth::user()->id
        ];

        if ($request->input('password')) {
            $data['password'] = bcrypt($request->input('password'));
        }

        if ($id) {



            $update_data = $request->only(['username', 'full_name', 'email', 'status', 'updated_by', 'role_id']);

            if ($request->input('password')) {
                $update_data['password'] = bcrypt($request->input('password'));
            }

            $update_data['updated_by'] = Auth::user()->id;

            $user = User::find($id);
            if ($user->created_by == Auth::user()->id || Auth::user()->hasRole('Admin')) {

                $user->update($update_data);
                return response()->json(['success' => true, 'message' => 'User updated successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'You can only edit your own made Users!']);
            }
            $user->update($update_data);

            if ($request->has('role_id')) {
                $role = Role::find($request->input('role_id'));

                $user->syncRoles($role->name);
            }

            return response()->json(['success' => true, 'message' => 'User Updated successfully!']);
        } else {

            $data['created_by'] = Auth::user()->id;
            $user = User::create($data);

            if ($request->has('role_id')) {
                $role = Role::find($request->input('role_id'));

                $user->assignRole($role->name);
            }

            return response()->json(['success' => true, 'message' => 'User saved successfully!']);
        }
    }
    public function delete_user($id)
    {
        $user = User::find($id);



        if ($user->created_by == Auth::user()->id ||  Auth::user()->hasRole('Admin')) {

            $user->delete();
            return response()->json(['success' => true, 'message' => 'Lead deleted successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'You can only delete your own made users!']);
        }

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
