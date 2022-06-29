<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\BitcoinWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class bitcoinWalletController extends Controller
{
    //
    public function createHdWallet(Request $data)
    {

        $validate = Validator::make($data->all(), [
            'wallet_password' => 'required|min:10|confirmed',
            'account_password' => 'required',
            'name' => 'required|string|unique:bitcoin_wallets,name'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }



        if (!Hash::check($data['account_password'], Auth::user()->password)) {
            return back()->with(['error' => 'Wrong Account password']);
        }
        $password = Hash::make($data['wallet_password']);


        try {
            $result = $this->instance->walletApiBtcCreateAddress()->createHd(Constants::$BTC_MAINNET, $data['name'], $password, 1);
            $wallet = new BitcoinWallet();
            $address = $result->payload->addresses[0];
            $wallet->user_id = Auth::user()->id;
            $wallet->path = $address->path;
            $wallet->address = $address->address;
            $wallet->type = 'primary';
            $wallet->name = $data['name'];
            $wallet->password = $password;
            $wallet->balance = 0;
            $wallet->primary_wallet_id = 0;
            $wallet->save();

            $callback = route('user.wallet-webhook');

            $result = $this->instance->webhookBtcCreateAddressTransaction()->create(Constants::$BTC_MAINNET, $callback, $wallet->address, 6);
        } catch (\Throwable  $e) {
            report($e);
            return response()->json(['error' => 'An error occured, please try again']);
        }
        return response()->json(['success' => 'HD wallet created successfully']);
    }

    public function sendFromHd(Request $r)
    {
        $data = $r->validate([
            'primary_wallet' => 'required|integer',
            'address' => 'required',
            'amount' => 'required',
            'wallet_password' => 'required',
            /* 'pin' => 'required', */
            'account_password' => 'required',
            'fees' => 'required'
        ]);

        $total = $data['amount'] + $data['fees'];
        $primary_wallet = BitcoinWallet::find($data['primary_wallet']);

        if (!Hash::check($data['wallet_password'], $primary_wallet->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect pin primary wallet password'
            ]);
        }

        /* if (!Hash::check($data['pin'], Auth::user()->bitcoinWallet->password)) {
            return back()->with(['error' => 'Incorrect personal wallet pin']);
        } */

        if (!Hash::check($data['account_password'], Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect account password'
            ]);
        }

        if ($total > $primary_wallet->balance) {
            return response()->json([
                'success' => false,
                'error' => 'Insufficient balance'
            ]);
        }

        $old_balance = $primary_wallet->balance;
        $primary_wallet->balance -= $total;
        $primary_wallet->save();

        $btc_transaction = new BitcoinTransaction();
        $btc_transaction->user_id = 1;
        $btc_transaction->primary_wallet_id = $primary_wallet->id;
        $btc_transaction->wallet_id = $primary_wallet->address; //The wallet of the owner user
        $btc_transaction->hash = 'none';
        $btc_transaction->debit = $total;
        $btc_transaction->fee = $data['fees'];
        $btc_transaction->charge = 0;
        $btc_transaction->previous_balance = $old_balance;
        $btc_transaction->current_balance = $primary_wallet->balance;
        $btc_transaction->transaction_type_id = 21;
        $btc_transaction->counterparty = $data['address'];
        $btc_transaction->narration = 'Sending bitcoin to ' . $data['address'] . ' Authorized by ' . Auth::user()->first_name;
        $btc_transaction->confirmations = 0;
        $btc_transaction->status = 'pending';
        $btc_transaction->save();

        $outputs = new \RestApis\Blockchain\BTC\Snippets\Output();
        $input = new \RestApis\Blockchain\BTC\Snippets\Input();
        $outputs->add($data['address'], $data['amount']);
        $input->add($primary_wallet->address, $data['amount']);

        $fee = new \RestApis\Blockchain\BTC\Snippets\Fee();
        $fee->set($data['fees']);

        try {
            //update status and hash if it goes through
            $result = $this->instance->transactionApiBtcNewTransactionHdWallet()->create(Constants::$BTC_MAINNET, $primary_wallet->name, $primary_wallet->password, $outputs,  $fee);
            $btc_transaction->hash = $result->payload->txid;
            $btc_transaction->status = 'success';
            $btc_transaction->save();

            //send mail
            return response()->json([
                'success' => true,
                'mgs' => 'Bitcoin sent successfully'
            ]);
        } catch (\Exception $e) {
            report($e);
            $primary_wallet->balance = $old_balance;
            $primary_wallet->save();

            $btc_transaction->status = 'failed';
            $btc_transaction->save();
            //set the transaction status to failed

            return response()->json([
                'success' => false,
                'error' => 'An error occured while processing the transaction please confirm the details and try again'
            ]);
        }
    }

    public function setCharge(Request $r)
    {
        $data = $r->validate([
            'bitcoin_charge' => 'required',
            'bitcoin_buy_charge' => 'required',
            'bitcoin_sell_charge' => 'required',
        ]);

        $id = Setting::latest()->first()->id;
        /* $id += 3; */
        $send_charge = Setting::updateorCreate(
            [

                'name' => 'bitcoin_charge',
            ],
            [
                'value' => $data['bitcoin_charge']
            ]
        );

        $buy_charge = Setting::updateorCreate(
            [

                'name' => 'bitcoin_buy_charge',
            ],
            [
                'value' => $data['bitcoin_buy_charge']
            ]
        );

        $sell_charge = Setting::updateorCreate(
            [

                'name' => 'bitcoin_sell_charge',
            ],
            [
                'value' => $data['bitcoin_sell_charge']
            ]
        );

        return back()->with(['success' => 'Bitcoin charge set successfully']);
    }
}
