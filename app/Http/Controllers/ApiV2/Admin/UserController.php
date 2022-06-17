<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;

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
}
