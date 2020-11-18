<?php

namespace App\Http\Controllers;

use App\BitcoinWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RestApis\Blockchain\Constants;

class BitcoinWalletController extends Controller
{
    public function __construct()
    {
        $this->instance = $instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
    }


    public function create(Request $r)
    {
        $data = $r->validate([
            'wallet_password' => 'required|min:4|confirmed',
        ]);


       $password = Hash::make($data['wallet_password']);

        try {
            $primary_wallet = BitcoinWallet::where(['user_id' => 1, 'primary_wallet_id' => 0])->first();
            $result = $this->instance->walletApiBtcGenerateAddressInWallet()->createHd(Constants::$BTC_TESTNET, $primary_wallet->name, $primary_wallet->password,1);
            $wallet = new BitcoinWallet();
            $address = $result->payload->addresses[0];
            $wallet->user_id = Auth::user()->id;
            $wallet->path = $address->path;
            $wallet->address = $address->address;
            $wallet->type = 'secondary';
            $wallet->name = Auth::user()->first_name;
            $wallet->password = $password;
            $wallet->balance = 0;
            $wallet->primary_wallet_id = $primary_wallet->id;
            $wallet->save();

        } catch (\Throwable  $e) {
            report($e);
            return back()->with(['error' => 'An error occured, please try again' ]);
        }
        return back()->with(['success' => 'Wallet created successfully']);
    }
}
