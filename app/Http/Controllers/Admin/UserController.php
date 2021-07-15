<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use App\User;
use App\Verification;
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

    public function verifications()
    {
        $verifications = Verification::where('status', 'waiting')->latest()->get();

        return view('admin.user_verifications', compact('verifications'));
    }

    public function verify(Verification $verification)
    {
        if ($verification->type == 'ID Card') {
            $verification->user->idcard_verified_at = now();
        } elseif($verification->type == 'Address') {
            $verification->user->address_verified_at = now();
        }
        
        $verification->user->save();
        $verification->status = 'success';
        $verification->save();

        Notification::create([
            'user_id' => $verification->user->id,
            'title' => 'Verification successful',
            'body' => 'Congratulations, your '. $verification->type . ' has been verified',
        ]);

        return back()->with(['success' => 'User verified']);
    }

    public function cancelVerification(Verification $verification)
    {
        $verification->status = 'failed';
        $verification->save();

        Notification::create([
            'user_id' => $verification->user->id,
            'title' => 'Verification failed',
            'body' => 'Sorry, your '. $verification->type . ' could not be verified. Please check the document and try again',
        ]);

        return back()->with(['success' => 'User verification cancelled']);
    }
}
