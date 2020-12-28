<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function freezeAccount(Request $r)
    {
        if (!Hash::check($r->pin, Auth::user()->nairaWallet->password)) {
            return back()->with(['error' => 'Wallet pin doesnt match']);
        }
        $user_wallet = User::find($r->user_id)->nairaWallet;
        $user_wallet->status = 'paused';
        $user_wallet->save();

        return back()->with(['success' => 'Naira wallet froozen successfully']);
    }

    public function activateAccount(Request $r)
    {
        if (!Hash::check($r->pin, Auth::user()->nairaWallet->password)) {
            return back()->with(['error' => 'Wallet pin doesnt match']);
        }
        $user_wallet = User::find($r->user_id)->nairaWallet;
        $user_wallet->status = 'active';
        $user_wallet->save();

        return back()->with(['success' => 'Naira wallet activated successfully']);
    }
}
