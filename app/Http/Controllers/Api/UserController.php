<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CryptoHelperController;
use App\Http\Controllers\LoginSessionController;
use App\NairaTransaction;
use App\Notification;
use App\Setting;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function dashboard()
    {
        
        \Artisan::call('naira:limit');
        $transactions = Auth::user()->transactions->take(3);
        $naira_wallet = Auth::user()->nairaWallet;
        $naira_wallet_transactions = NairaTransaction::where('cr_user_id', Auth::user()->id)->orWhere('dr_user_id', Auth::user()->id)->latest()->with('transactionType')->get();
        $notification = Notification::where('user_id', 0)->latest()->first();

        $btc_wallet = CryptoHelperController::balance(1);
        // $tron_wallet = CryptoHelperController::balance(5);
        $usdt_wallet = CryptoHelperController::balance(7);

        $total_crypto_balance = $btc_wallet->usd   + ($usdt_wallet ? $usdt_wallet->usd : 0) ;
        $total_ngn_balance = $btc_wallet->ngn  + ($usdt_wallet ? $usdt_wallet->ngn : 0) + $naira_wallet->amount;

        //* Add checker for login Session
        $loginSession = new LoginSessionController();
        $loginSession->FindSessionData(Auth::user()->id);

        return response()->json([
            'success' => true,
            'user' => Auth::user(),
            'bank_details' => Auth::user()->accounts->first(),
            'assets_transactions' => $transactions,
            'naira_wallet' => $naira_wallet,
            'naira_wallet_transactions' => $naira_wallet_transactions,
            'notification' => $notification,
            'total_crypto_balance' => $total_crypto_balance,
            'total_ngn_balance' => $total_ngn_balance,
        ]);
    }

    public function details()
    {
        $versions = [
            'ios_current' => Setting::where('name', 'ios_current')->first()->value,
            'ios_stable' => Setting::where('name', 'ios_stable')->first()->value,
            'android_current' => Setting::where('name', 'android_current')->first()->value,
            'android_stable' => Setting::where('name', 'android_stable')->first()->value,
        ];
        return response()->json([
            'success' => true,
            'data' => Auth::user(),
            'versions' => $versions,
            'bank_details' => Auth::user()->accounts()->first()
        ]);
    }

    public function updatePassword(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|confirmed|min:6|different:old_password'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (Hash::check($r->old_password, Auth::user()->password) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Your current password does not match with the password you provided. Please try again.',
            ]);
        }

        $user = Auth::user();
        $user->password = Hash::make($r->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function updateEmail(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'password' => 'required',
            'new_email' => 'required|email|unique:users,email'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (Hash::check($r->password, Auth::user()->password) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Your current password does not match with the password you provided. Please try again.',
            ]);
        }

        Auth::user()->email = $r->new_email;
        Auth::user()->email_verified_at = null;
        Auth::user()->save();
        return response()->json([
            'success' => true,
            'data' => Auth::user(),
        ]);
    }

    public function updateDp(Request $r)
    {

        if ($r->has('image')) {
            $file = $r->image;
            $folderPath = public_path('storage/avatar/');
            $image_base64 = base64_decode($file);

            $imageName = time() . uniqid() . '.png';
            $imageFullPath = $folderPath . $imageName;

            file_put_contents($imageFullPath, $image_base64);

            Auth::user()->dp = $imageName;
            Auth::user()->save();

            return response()->json([
                'success' => true,
                'data' => Auth::user(),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Image file not present'
            ]);
        }
    }

    public function uploadId(Request $r)
    {
        $user = Auth::user();
        if ($user->phone_verified_at == null)
        {
            return response()->json([
                'success' => false,
                'msg' => 'Please Verify your Phone Number'
            ]);
        }
        if(Auth::user()->address_verified_at == null){
            return response()->json([
                'success' => false,
                'msg' => 'Please Verify your Address First'
            ]);
        }
        if ($user->verifications()->where(['type' => 'ID Card', 'status' => 'Waiting'])->exists()) {
            return response()->json([
                'success' => false,
                'msg' => 'ID Card verification already in progress'
            ]);
        }

        if ($r->has('image')) {
            $file = $r->image;
            $folderPath = public_path('storage/idcards/');
            $image_base64 = base64_decode($file);

            $imageName = time() . uniqid() . '.png';
            $imageFullPath = $folderPath . $imageName;

            file_put_contents($imageFullPath, $image_base64);

            Auth::user()->id_card = $imageName;
            Auth::user()->save();

            $user->verifications()->create([
                'path' => $imageName,
                'type' => 'ID Card',
                'status' => 'Waiting'
            ]);

            return response()->json([
                'success' => true,
                'data' => Auth::user(),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Image file not present'
            ]);
        }
    }

    public function uploadAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'location' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $user = Auth::user();
        if ($user->phone_verified_at == null)
        {
            return response()->json([
                'success' => false,
                'msg' => 'Please Verify your Phone Number'
            ]);
        }
        if ($user->verifications()->where(['type' => 'Address', 'status' => 'Waiting'])->exists()) {
            return response()->json([
                'success' => false,
                'msg' => 'Address verification already in progress'
            ]);
        }

        $file = $request->image;
        $folderPath = public_path('storage/idcards/');
        $image_base64 = base64_decode($file);

        $imageName = time() . uniqid() . '.png';
        $imageFullPath = $folderPath . $imageName;

        file_put_contents($imageFullPath, $image_base64);

        Auth::user()->address_img = $request->location;
        Auth::user()->save();

        $user->verifications()->create([
            'path' => $imageName,
            'type' => 'Address',
            'status' => 'Waiting'
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Address uploaded'
        ]);
    }

    public function checkPhone($phone)
    {
        if (User::where('phone', $phone)->exists()) {
            return response()->json([
                'success' => false,
                'msg' => 'Phone number already in use'
            ]);
        } else {
            return response()->json([
                'success' => true
            ]);
        }
    }
}
