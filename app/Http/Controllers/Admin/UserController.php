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
        // if(!Auth::check()){

        //     $this->middleware('auth');
        // }
    }
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        return view('Admin.Users.index', compact(['users', 'roles']));
    }
    public function save_user(Request $request)
    {
        $user = new User();

        $id =  $request->input('id');
        $data = [
            'username' => $request->input('username'),
            'full_name' => $request->input('full_name'),
            'email'    => $request->input('email'),
            'role_id'  => $request->input('role_id'),
            'status'   => $request->input('status'),
        ];


        if (!empty($id)) {
            $data['id'] = $id;
        }
        if ($request->input('password')) {
            $data['password'] = password_hash($request->input('password'), PASSWORD_BCRYPT);
        }
        if ($id) {
            $update = User::find($id);
            $update->update($data);
            echo json_encode(['success' => true, 'message' => 'User Updated successfully!']);
        } else if (!$id) {
            $user->create($data);
            echo json_encode(['success' => true, 'message' => 'User saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed.']);
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
