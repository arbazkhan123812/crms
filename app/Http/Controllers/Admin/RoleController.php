<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
  public function __construct()
    {
        // if(!Auth::check()){
            
        //     $this->middleware('auth');
        // }
    }
    public function index()
    {
        $roles = Role::all();
        return view('Admin.Roles.index', compact(['roles']));
    }
    public function save_role(Request $request)
    {
        $role = new Role();

        $id =  $request->input('id');
        $data = [
            'name' => $request->input('name'),
            'guard_name' => $request->input('guarded_name'),
        ];


        if (!empty($id)) {
            $data['id'] = $id;
        }
        if ($id) {  
          $update = Role::find($id);
          $update->update($data);
        echo json_encode(['success' => true, 'message' => 'Role Updated successfully!']);


        } else if(!$id) {
            $role->create($data);
            echo json_encode(['success' => true, 'message' => 'Role saved successfully!']);
        }else{
         echo json_encode(['success' => false, 'message' => 'Failed.']);

        }
    }
    public function delete_role($id)
    {
        $delete = role::destroy($id);
      

        if ($delete) {
            echo json_encode(['success' => true, 'message' => 'role deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete role.']);
        }
    }
    public function get_role($id)
    {
        $role = Role::find($id);
      

        if ($role) {
            echo json_encode(['success' => true, 'data' => $role]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to get role.']);
        }
    }
}
