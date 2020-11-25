<?php

namespace App\Http\Controllers;

use App\BitcoinTransaction;
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

    public function test(Request $request)
    {
        \Log::info('app.requests', ['request' => $request->all()]);
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

            $callback = route('user.wallet-webhook');
            $result = $this->instance->webhookBtcCreateAddressTransaction()->create(Constants::$BTC_TESTNET, $callback, $wallet->address, 6);

        } catch (\Throwable  $e) {
            report($e);
            return back()->with(['error' => 'An error occured, please try again' ]);
        }
        return back()->with(['success' => 'Wallet created successfully']);
    }

    public function webhook(Request $request)
    {
        try {
            \Log::info('app.requests', ['request' => $request]);
            /* $btc_transaction = new BitcoinTransaction();
            $btc_transaction->user_id = 19;
            $btc_transaction->primary_wallet_id = 1;
            $btc_transaction->wallet_id = 'so '; //The wallet of the owner user
            $btc_transaction->hash = 'here ';
            $btc_transaction->credit = 2;
            $btc_transaction->fee = 0.00001; //Change to actual fee
            $btc_transaction->charge = 0.0001; //Change to feee from admin
            $btc_transaction->previous_balance = 0;
            $btc_transaction->current_balance = 0;
            $btc_transaction->transaction_type_id = 19;
            $btc_transaction->counterparty = 'Dantown Assets';
            $btc_transaction->narration = 'Approved by '.$request['confirmations'];
            $btc_transaction->confirmations = 99;
            $btc_transaction->save(); */
        } catch (\Exception $e) {
            report($e);
        }

        $btc_txn = BitcoinTransaction::where('hash', $request->txid)->first();
        if ($btc_txn == null) { //New Transaction e.g recieve
            # code...
        } else { //Old transaction like Trade payment and recieve transaction waiting for confirmation
            $btc_txn->confirmations = $request->confirmations;
            /* if confirmations are up to 6 and status is pending, Update users balance and set to success */
        }

    }
}
