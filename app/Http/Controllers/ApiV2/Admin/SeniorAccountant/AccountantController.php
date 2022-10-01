<?php

namespace App\Http\Controllers\ApiV2\Admin\SeniorAccountant;

use App\AccountantTimeStamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\Transaction;
use App\User;
use App\UtilityTransaction;

class AccountantController extends Controller
{
    public function AccountantOverview()
    {

        // getting currently active

        // $accountantTimestamp = AccountantTimeStamp::with('user')->where('inactiveTime',null)->first();
        // $accountant = $accountantTimestamp->user;
        // $activeUser = User::whereHas('roles', function($query) {
        //     $query->where('id', 1);
        // })
      $data['accountant'] =  $accountant = User::select('id','first_name','last_name','email','phone','role','status','username')->where("status", "active")->whereHas("accountantTimestamp")->with('accountantTimestamp')->first();





        $data['overview'] = [
            'number_of_trades'=> 0,
            'total_amount_deposit_p2p'=> 0,
            'total_number_withdrawal_transactions'=> 0,
            'total_number_utility_transactions'=> 0,
            'total_volume_trades'=> 0,
            'total_amount_withdrawn_p2p'=> 0,
            'total_amount_traded'=> 0,
            'total_number_transactions_p2p'=> 0,
            'total_debited_amount__utilities_transactions' => 0
        ];


        if($accountant->accountantTimestamp->count() > 0){
            $startTime = $accountant->accountantTimestamp->first()->activeTime;
            $endTime = ($accountant->accountantTimestamp->first()->inactiveTime == null) ? now() : $accountant->accountantTimestamp->first()->inactiveTime;

            $p2pTranx = NairaTrade::where('status', 'success')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->get();
            $total_number_transactions_p2p = $p2pTranx->count();
            $total_amount_deposit_p2p = $p2pTranx->where('type','deposit')->sum('amount');
            $total_amount_withdrawal_p2p = $p2pTranx->where('type','withdrawal')->sum('amount');
            $total_number_withdrawal_transactions   =  Transaction::where('status', 'success')->where('updated_at','>=',$startTime )->where('type', 'sell')->count();
            $total_number_utility_transactions = UtilityTransaction::where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->count();
            $data['overview']= [
                'number_of_trades'=> 0,
                'total_amount_deposit_p2p'=>$total_amount_deposit_p2p,
                'total_number_withdrawal_transactions'=>  $total_number_withdrawal_transactions,
                'total_number_utility_transactions'=>   $total_number_utility_transactions,
                'total_volume_trades'=> 0,
                'total_amount_withdrawn_p2p'=> $total_amount_withdrawal_p2p,
                'total_amount_traded'=> 0,
                'total_number_transactions_p2p'=>  $total_number_transactions_p2p,
                'total_debited_amount__utilities_transactions' => 0
            ];


            $data['transactions'] = "";


        }

        // }

        // $activeTime = $accountantTimestamp->activeTime;
        // $inactiveTime = $accountantTimestamp->inactiveTime;

        // Crypto Transactions
        // $cryptoTranx = Transaction::with('user')->where('status', 'success')->where('updated_at','>=',$activeTime)->get();
        // this should give you the idea to work with.

        // if(){

        // }

        return response()->json([
            'success' => true,
            'data' => $data ,
        ], 200);
    }

    public function GetActiveAccountant(){
        
    }
}
