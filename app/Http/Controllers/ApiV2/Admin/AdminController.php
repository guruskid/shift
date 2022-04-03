<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function addAdmin(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'department' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'username' => 'required',
            'phone' => 'required|integer',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        if(User::where('email', $r->email)->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exists',
            ], 401);
        }

        if($r->department == 'Junior_Accountant') {
            $role = 777;
        }elseif($r->department == 'Senior_Accountant') {
            $role = 889;
        }elseif($r->department == 'Customer_care') {
            $role = 333;
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Invalid department, please select from the list below: Junior_Accountant, Senior_Accountant, Customer_care',
            ], 401);
        }

        $user = new User();
        $user->first_name = $r->first_name;
        $user->last_name = $r->last_name;
        $user->email = $r->email;
        $user->password = Hash::make($r->password);
        $user->role = $role;
        $user->username = $r->username;
        $user->phone = $r->phone;
        $user->email_verified_at = now();
        $user->status = 'active';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully',
        ], 200);
    }


    // public function totals()
    // {
    //     $totalNaira = NairaTrade::where('type','deposit')->where('status','success')->count();
    //     // NairaTrade::where('type','deposit')->get()
    // }

    public function action(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'action' => 'required',
            'user_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $user = User::find($r->user_id);

        if($r->action == 'activate') {
            $user->status = 'active';
        }elseif($r->action == 'deactivate') {
            $user->status = 'not verified';
        }elseif($r->action == 'delete') {
            $user->delete();
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Invalid action, please select from the list below: activate, deactivate, delete',
            ], 401);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
        ], 200);
    }

    public function customerHappiness()
    {
        return response()->json([
            'success' => true,
            'customer_happiness' => User::where('role', 555)->latest()->get(),
        ]);
    }

    public function accountant()
    {
        return response()->json([
            'success' => true,
            'accountant' => User::where('role', [889, 777])->latest()->get(),
        ]);
    }
}



