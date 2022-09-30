<?php

namespace App\Http\Controllers\ApiV2\Admin\SeniorAccountant;

use App\AccountantTimeStamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\UtilityTransaction;

class AccountantController extends Controller
{
    public function AccountantOverview()
    {

        // getting currently active 

        $accountantTimestamp = AccountantTimeStamp::with('user')->where('inactiveTime',null)->first();
        $accountant = $accountantTimestamp->user;

        $activeTime = $accountantTimestamp->activeTime;
        $inactiveTime = $accountantTimestamp->inactiveTime;

        // Crypto Transactions 
        $cryptoTranx = Transaction::with('user')->where('status', 'success')->where('updated_at','>=',$activeTime)->get();
        // this should give you the idea to work with.
    }
}
