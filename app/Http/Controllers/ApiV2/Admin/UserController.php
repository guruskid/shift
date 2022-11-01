<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiV2\Admin\User\UserResource;
use App\User;
use Carbon\Carbon;
use Engage\Resources\Users;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all()->paginate(1000);
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }


    public function newUsers()
    {
        # code...
        // $newUsers = User::with('transactions', 'utilityTransaction', 'accounts', 'notifications', 'nairaWallet', 'nairaTransactions', 'bitcoinWallet')->whereYear('created_at', '=', date('Y'))->whereMonth('created_at', date('m'))->get();

        // return response()->json([
        //     'success' => true,
        //     'newUsers' => $newUsers
        // ]);


        $user_data = User::WhereYear('created_at', date('Y'))->get()->groupBy(function($user_data){
            return Carbon::parse($user_data->created_at)->format('Y-M-d');
        });

        // return ($user_data);
        // dd(date('m'));

        $user_days = [];
        $userDayCount = [];
        foreach($user_data as $month => $values) {
           $user_days[] = $month;
           $userDayCount[] = count($values);
        }

        return response()->json([
            'success' => true,
            'user_days' => $user_days,
            'userDayCount' => $userDayCount,
            'users' => $user_data,
        ]);

    }

    public function user($id)
    {
        # code...
        $user = User::with('transactions', 'utilityTransaction', 'accounts', 'notifications', 'nairaWallet', 'nairaTransactions', 'bitcoinWallet', 'nairaTrades')->where('id', $id)->get();
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
    
    public function allUsers()
    {
        $users = User::with('nairaWallet')->orderBy('id','DESC')->get();
        $userData = UserResource::collection($users);

        return response()->json([
            'success' => true,
            'users' => $userData
        ]);
    }

    public function UserSort(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'month' => 'required',
            'year' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $month = $request->month;
        $year = $request->year;

        $start_date = Carbon::createFromDate($year,$month,1);
        $end_date = Carbon::createFromDate($year,$month,1)->endOfMonth();

        $users = User::with('nairaWallet')->where('created_at','>=',$start_date)->where('created_at','<=',$end_date)->orderBy('id','DESC')->get();
        $userData = UserResource::collection($users);

        return response()->json([
            'success' => true,
            'users' => $userData
        ]);
    }

    public function showUser($id)
    {
        $user = User::with('nairaWallet')->where('id',$id)->get();
        if($user->count() == 0)
        {
            return response()->json([
                'success' => false,
                'message' => 'user not found'
            ]);
        }

        $userData = UserResource::collection($user);

        return response()->json([
            'success' => true,
            'user' => $userData
        ]);
    }

    public function withholdFunds(Request $request)
    { 
        $validate = Validator::make($request->all(), [
            'user_id' => 'required',
            'amount' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $user_id = $request->user_id;
        $amount = $request->amount;

        $user = User::find($user_id);

        if(!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'no data found'
            ],401);
        }

        $name = $user->first_name . " " . $user->last_name; 
        $userWallet = $user->nairaWallet;

        if($amount > $userWallet->amount):
            return response()->json([
                'success' => false,
                'message' => "Amount higher than wallet balance"
            ],401);
        endif;

        $userWallet->amount = $userWallet->amount - $amount;
        $userWallet->withheld_amount = $userWallet->withheld_amount + $amount;
        $userWallet->save();

        return response()->json([
            'success' => true,
            'message' => number_format($amount)." has been Withheld from $name"
        ],200);
    }

    public function freezeWallet($id)
    {
        $user = User::find($id);
        

        if(!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'no data found'
            ],401);
        }

        $name = $user->first_name . " " . $user->last_name; 

        if($user->status == 'not verified')
        {
            return response()->json([
                'success' => false,
                'message' => $name." has already been deactivated"
            ],401);
        }
        $user->status = 'not verified';
        $user->save();

        $userWallet = $user->nairaWallet;
        if($userWallet):
            $userWallet->status = 'paused';
            $userWallet->save();
        endif;

        return response()->json([
            'success' => true,
            'message' => $name ." has been deactivated"
        ],200);
    }

    public function activateWallet($id)
    {
        $user = User::find($id);

        if(!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'no data found'
            ],401);
        }

        $name = $user->first_name . " " . $user->last_name; 

        if($user->status == 'active')
        {
            return response()->json([
                'success' => false,
                'message' => $name." has already been activated"
            ],401);
        }

        $user->status = 'active';
        $user->save();


        $userWallet = $user->nairaWallet;
        if($userWallet):
            $userWallet->status = 'active';
            $userWallet->save();
        endif;

        return response()->json([
            'success' => true,
            'message' => $name ." account has been activated"
        ],200);
    }

    public function clearAmount(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required',
            'amount' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $user_id = $request->user_id;
        $amount = $request->amount;

        $user = User::find($user_id);

        if(!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'no data found'
            ],401);
        }

        $name = $user->first_name . " " . $user->last_name; 
        $userWallet = $user->nairaWallet;

        if($amount > $userWallet->withheld_amount):
            return response()->json([
                'success' => false,
                'message' => "amount greater than with held amount"
            ],401);
        endif;

        $userWallet->amount = $userWallet->amount + $amount;
        $userWallet->withheld_amount = $userWallet->withheld_amount - $amount;
        $userWallet->save();

        return response()->json([
            'success' => true,
            'message' => number_format($amount)." has been returned to $name"
        ],200);
    }
}
