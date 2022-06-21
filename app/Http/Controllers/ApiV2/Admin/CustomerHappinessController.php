<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\Ticket;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Validator;

class CustomerHappinessController extends Controller
{
    public function overview()
    {
        $user = User::where('role',555)->get();
        $transactions  = $transactions = Transaction::with('user')->orderBy('updated_at', 'desc')->get();
        foreach ($user as $u) {
            $time_diff = 0;
            $date = Ticket::where('agent_id',$u->id)->get();
            foreach($date as $d)
            {
                $time_diff += $d->updated_at->diffInSeconds($d->created_at);
            }
            $time_diff = round($time_diff/$date->count());

            $u->average_response_time = CarbonInterval::seconds($time_diff)->cascade()->forHumans();
        }
        return response()->json([
            'success' => true,
            'transactions' => $transactions,
            'customerHappiness' => $user
        ], 200);

    }

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
            'message' => "You have successfully created an account for $r->first_name $r->last_name with the username \n
            Username: $r->username \n
            Password: $r->password",
        ], 200);

    }

    public function activateUser($id,$state)
    {

        if($state == 'deactivate')
        {
            User::where('id',$id)->update([
                'status' => 'waiting',
            ]);
        }
        if($state == 'activate'){
            User::where('id',$id)->update([
                'status' => 'active',
            ]);
        }
        if($state == 'deactivate' || $state == 'activate')
        {
            return response()->json([
                'success' => true,
                'message' => 'Staff Status Updated',
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Staff details and status is not available',
        ], 401);
    }

    public function index()
    {
        $transactions = NairaTransaction::with('user')->orderBy('id','desc')->limit(10)->get();
        return response()->json([
            'success' => true,
            'opened_querry' => [],
            'transactions' => $transactions,
        ], 200);
    }

    public function p2p()
    {

        return response()->json([
            'success' => true,
            'data' => NairaTrade::with('user', 'nairaWallet', 'naria_transactions')->latest()->paginate(1000),
        ]);
    }

    public function userProfile()
    {
        return response()->json([
            'success' => true,
            'data' => User::with('transactions', 'nairaWallet', 'utilityTransaction', 'nairaWallet', 'nairaTransactions', 'nairaTrades')->latest()->paginate(100),
        ]);
    }

    public function userSearch($email)
    {
        return response()->json([
            'success' => true,
            'data' => User::where('email', $email)->with('transactions', 'nairaWallet', 'utilityTransaction', 'nairaWallet', 'nairaTransactions', 'nairaTrades')->get(),
        ]);
    }

    public function customerHappinessTransactions()
    {
        return response()->json([
            'success' => true,
            'data' => Transaction::with('user')->latest()->paginate(100),
        ]);
    }
}
