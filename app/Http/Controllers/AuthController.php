<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    public function index()
    {
        if (Auth::check()) {
            return redirect('admin/users/index');
        }

        return view('auth.login_view', ['pageTitle' => 'Login']);
    }


    public function process_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all()
            ]);
        }


        if (Auth::attempt([
            'email' => $request->identity,
            'password' => $request->password
        ]) || Auth::attempt([
            'username' => $request->identity,
            'password' => $request->password
        ])) {

            $redirect = Auth::user()->employee_id ? '/employee/dashboard' : '/admin/users/index';

            return response()->json([
                'success' => true,
                'redirect' => url($redirect)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid Credentials'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
    public function testy(){
       $user= Auth::user();

       echo $user;

    }
}
