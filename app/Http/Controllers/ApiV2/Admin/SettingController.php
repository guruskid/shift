<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SystemSettings;
use App\TargetSettings;
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
            $u->role_name = $this->roleName($u->role);
        }
        return $user;
    }

    public function roleName($role_number){
        switch ($role_number) {
            case 999:
                $role_name = "Super Administrator";
                break;
            case 888:
                $role_name = "Sales Representative";
                break;
            case 889:
                $role_name = "Senior Accountant";
                break;  
            case 777:
                $role_name = "Junior Accountant";
                break;
             case 775:
                $role_name = "Account Officer";
                break;   
            case 559:
                $role_name = "Marketing Personnel";
                break; 
            case 557:
                $role_name = "Sales Personnel - Old Users";
                break;
            case 556:
                $role_name = "Sales Personnel - New Users";
                break;
            case 666:
                $role_name = "Manager";
                break;
            case 444:
                $role_name = "Chinese Operator";
                break; 
            case 449:
                $role_name = "Chinese Administrator";
                break;    
            default:
            $role_name = "";
                break;
        }
        return $role_name;
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
        foreach ($user as $u) {
            $u->role_name = $this->roleName($u->role);
        }
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
        $user = User::where('id',$id)->first();
        User::where('id',$id)->update([
            'role' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => "$user->first_name $user->last_name with the role of ".$this->roleName($user->role)." has been removed",
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
            'password' => Hash::make($r->password),
            'username' => $r->username,
            'role' => $r->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => "$r->first_name $r->last_name with the role of ".$this->roleName($r->role)." has been added",
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

    public function assignSalesTarget(Request $request)
    {
         $user = User::find($request->user_id);
         if(in_array($user()->role, [556, 557] ))
         {
             TargetSettings::updateOrCreate(
                 ['user_id' => $request->user_id],
                 ['target' => $request->target,]
                );

             return response()->json([
                'success' => true,
                'message' => "Target has been added for $user->first_name $user->last_name",
            ], 200);
         }
         return response()->json([
            'success' => false,
            'message' => "$user->first_name $user->last_name is not a of role SALES",
        ], 401);

    }
}