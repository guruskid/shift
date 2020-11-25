<?php

namespace App\Http\Controllers\Admin;

use App\BitcoinWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RestApis\Blockchain\Constants;

class BitcoinWalletController extends Controller
{

    public function __construct()
    {
        $this->instance = $instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
    }

    public function webhooks()
    {
        $callback = route('user.wallet-webhook');
        $result = $this->instance->webhookBtcCreateAddressTransaction()->create(Constants::$BTC_TESTNET, $callback, 'mggWuRKhgwwGmdoDizJyxEJEhLt84Rv2jh', 3);
        $result = $this->instance->webhookBtcCreateAddressTransaction()->create(Constants::$BTC_TESTNET, $callback, 'mntuQZQ6ErrBtRjHf26nWfHepPx2g8p63W', 3);
        dd($result);
    }

    public function wallets()
    {
        $wallets = BitcoinWallet::latest()->get();
        /* $result = $this->instance->walletApiBtcListWallets()->getHd(Constants::$BTC_TESTNET); */
        /* $result = $this->instance->walletApiBtcDeleteWallet()->deleteHd(Constants::$BTC_TESTNET,'Dantown HD Wallet 1'); */

        /* dd($result);  */

        return view('admin.bitcoin_wallet.wallets', compact(['wallets']));
    }

    public function createHdWallet(Request $r)
    {
        $data = $r->validate([
            'wallet_password' => 'required|min:10|confirmed',
            'account_password' => 'required',
            'name' => 'required|string|unique:bitcoin_wallets,name'
        ]);

        if (!Hash::check($data['account_password'], Auth::user()->password)) {
            return back()->with(['error' => 'Wrong Account password']);
        }
       $password = Hash::make($data['wallet_password']);
       /*   $result = $this->instance->walletApiBtcCreateAddress()->createHd(Constants::$BTC_TESTNET, $data['name'], $password,1);
        dd($result->payload->addresses[0]->path); */

        try {
            $result = $this->instance->walletApiBtcCreateAddress()->createHd(Constants::$BTC_TESTNET, $data['name'], $password,1);
            $wallet = new BitcoinWallet();
            $address = $result->payload->addresses[0];
            $wallet->user_id = 1;
            $wallet->path = $address->path;
            $wallet->address = $address->address;
            $wallet->type = 'primary';
            $wallet->name = $data['name'];
            $wallet->password = $password;
            $wallet->balance = 0;
            $wallet->primary_wallet_id = 0;
            $wallet->save();

            $callback = route('user.wallet-webhook');

            $result = $this->instance->webhookBtcCreateAddressTransaction()->create(Constants::$BTC_TESTNET, $callback, $wallet->address, 6);

        } catch (\Throwable  $e) {
            report($e);
            return back()->with(['error' => 'An error occured, please try again' ]);
        }
        return back()->with(['success' => 'HD wallet created successfully']);
    }
}
