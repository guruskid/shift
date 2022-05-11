<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SystemSettings;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{

    public function dropdownForRole()
    {
        $user = User::where('role','!=',1)->distinct()->get(['role']);
        foreach ($user as $u) {
            switch ($u->role) {
                case 999:
                    $u->role_name = "Super Administrator";
                    break;
                case 888:
                    $u->role_name = "Sales Representative";
                    break;
                case 889:
                    $u->role_name = "Senior Accountant";
                    break;  
                case 777:
                    $u->role_name = "Junior Accountant";
                    break;
                case 559:
                    $u->role_name = "Marketing Personnel";
                    break; 
                case 557:
                    $u->role_name = "Business Developer";
                    break;
                case 666:
                    $u->role_name = "Manager";
                    break;
                case 444:
                    $u->role_name = "Chinese Operator";
                    break; 
                case 449:
                    $u->role_name = "Chinese Administrator";
                    break;      
                default:
                $u->role_name = "";
                    break;
            }
        }
        return $user;
    }
    public function showUser()
    {
        $roleDropdown = $this->dropdownForRole();
        if(Auth::user())
        {
            return response()->json([
                'success' => true,
                'dropdown' => $roleDropdown,
                'user_details' => Auth::user(),
            ], 200);
        }
        
        return response()->json([
            'success' => false,
            'dropdown' => $roleDropdown,
            'message' => 'Not Logged In'
        ], 401);
        
    }

    public function editUser(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'password' => 'required',
            'username' => 'required',
            'role' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $user = Auth::user();
        $user->first_name = $r->first_name;
        $user->last_name = $r->last_name;
        if($r->email != $user->email)
        {
            if(User::where('email',$r->email)->count() >=1)
            {
                return response()->json([
                    'success' => false,
                    'message' => "Email is in use",
                ], 401);
            }   
            $user->email = $r->email;
        }
        $user->email = $r->email;
        $user->phone = $r->phone;
        if($r->username != $user->username)
        {
            if(User::where('username',$r->username)->count() >=1)
            {
                return response()->json([
                    'success' => false,
                    'message' => "username is in use",
                ], 401);
            }
            $user->username = $r->username;
        }
        $user->password = Hash::make($r->password);
        $user->role = $r->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Updated',
            'user_details' => Auth::user(),
        ], 200);
    }

    public function MembersOfStaff()
    {
        $user = User::where('role','!=',1)->get();
        return response()->json([
            'success' => true,
            'users' => $user,
        ], 200);
    }

    public function showStaff($id)
    {
        $user = User::find($id);
        $roleDropdown = $this->dropdownForRole();
        return response()->json([
            'success' => true,
            'dropdown' => $roleDropdown,
            'user' => $user,
        ], 200);
    }

    public function editStaff(Request $r){
        $validate = Validator::make($r->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'password' => 'required',
            'username' => 'required',
            'role' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $user = User::where('id',$r->id)->first();
       

        
        $user->first_name = $r->first_name;
        $user->last_name = $r->last_name;
        if($r->email != $user->email)
        {
            if(User::where('email',$r->email)->count() >=1)
            {
                return response()->json([
                    'success' => false,
                    'message' => "Email is in use",
                ], 401);
            }   
            $user->email = $r->email;
        }
        $user->email = $r->email;
        $user->phone = $r->phone;
        if($r->username != $user->username)
        {
            if(User::where('username',$r->username)->count() >=1)
            {
                return response()->json([
                    'success' => false,
                    'message' => "username is in use",
                ], 401);
            }
            $user->username = $r->username;
        }
        $user->password = Hash::make($r->password);
        $user->role = $r->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Updated',
            'user_details' => $user,
        ], 200);
    }

    public function removeUser($id)
    {
        User::where('id',$id)->update([
            'role' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Staff Deleted',
        ], 200);
    }

    public function roleSelection()
    {
        $roleDropdown = $this->dropdownForRole();
        return response()->json([
            'success' => true,
            'dropdown' => $roleDropdown,
        ], 200);
    }

    public function addStaff(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required',
            'password' => 'required',
            'username' => 'required|unique:users,username',
            'role' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        User::create([
            'first_name' => $r->first_name,
            'last_name' => $r->last_name,
            'email' => $r->email,
            'phone' => $r->phone,
            'password' => $r->password,
            'username' => Hash::make($r->username),
            'role' => $r->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Staff Data Added",
        ], 200);
        
    }

    public function settings()
    {
        $settings = SystemSettings::get();
        $settingsData = array();
        foreach ($settings as $key => $value) {
            $settingsData[$value['settings_name']] = $value;
        }

        $data['settings'] = $settingsData;

        return response()->json([
            'success' => true,
            'settings' => $data,
        ], 200);
    }

    public function updateSettings(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'value' => 'required',
            'notice' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }
        $inputs = $request->all();
        $value = ($inputs['value'] == 'true') ? 1 : 0;
        $setting = SystemSettings::where('settings_name',$inputs['name'])
        ->update(['settings_value' =>  $value, 'notice' => $inputs['notice']]);

        if ($setting) {
            return response()->json([
                'success' => true,
                'message' => 'Your Settings have been updated successfully',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => "An error occurred, please try again!",
        ], 401);
    }
}