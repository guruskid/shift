<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

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
        $newUsers = User::with('transactions', 'utilityTransaction', 'accounts', 'notifications', 'nairaWallet', 'nairaTransactions', 'bitcoinWallet')->whereYear('created_at', '=', date('Y'))->whereMonth('created_at', date('m'))->get();

        return response()->json([
            'success' => true,
            'newUsers' => $newUsers
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
