<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaWallet;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AccountantController extends Controller
{
    public function index()
    {
        $listOfAccountant = User::select('id','first_name','last_name','email','phone','role','status','username')
        ->with('accountantTimestamp')->whereIn('role',[777,775])->get();
        $listOfAccountant = $this->appendDataFromLastActive($listOfAccountant);

        $activeUser = $listOfAccountant->where('status', 'active')->first();

        $startTime = $activeUser['activeTime'];
        $endTime = ($activeUser['inactiveTime'] == null) ? now() : $activeUser['inactiveTime'];

        $p2pTranx = NairaTrade::where('status', 'success')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->get();

        $deposit_count = $p2pTranx->where('type','deposit')->count();
        $deposit_amount = $p2pTranx->where('type','deposit')->sum('amount');

        $withdrawal_count =  $p2pTranx->where('type','withdrawal')->count();
        $withdrawal_amount = $p2pTranx->where('type','withdrawal')->sum('amount');

        $chart = $p2pTranx->groupBy(function($date){
            return Carbon::parse($date->created_at)->format('h');
        });

        $chartExportData = array();
        foreach ($chart as $key => $value) {

            $chartExportData[] = array(
                'hour' =>  $key,
                'amount' => $value->sum('amount')
            );
        }
        $summary = [
            'DepositCount' => $deposit_count,
            'DepositAmount' => $deposit_amount,
            'WithdrawalCount' => $withdrawal_count,
            'WithdrawalAmount' => $withdrawal_amount
        ];

        $transactions = $this->TransactionsDuringActiveTime($startTime, $endTime);

        return response()->json([
            'accountant' => $listOfAccountant,
            'summary' => $summary,
            'transactions' => $transactions,
            'chart' => $chartExportData
        ]);
    }

    public function appendDataFromLastActive(Collection $collection)
    {
        $all_transactions = Transaction::where('status','success')->get();
        $utilityTranx = UtilityTransaction::where('status','success')->get();
        $p2pTranx = NairaTrade::where('status','success')->get();

        $amount = NairaWallet::sum('amount');

        foreach($collection as $accountant)
        {
            $accountant->role_name = ($accountant->role == 777) ? 'Junior Accountant' : 'Account Officer';
            $latest_timeStamp = $accountant->accountantTimestamp->first();

            $openingBalance = $latest_timeStamp->opening_balance;
            $closingBalance = $latest_timeStamp->closing_balance;

            $accountant->openingBalance = ($openingBalance == null) ? 'Not Available' : number_format($openingBalance);
            $accountant->closingBalance = ($closingBalance == null) ? number_format($amount) : number_format($closingBalance);

            $accountant->totalAmountPaidOut = $this->totalAmountPaidOut($latest_timeStamp->activeTime,$latest_timeStamp->inactiveTime,$all_transactions,$utilityTranx,$p2pTranx);
            $accountant->totalDeposit = $this->totalDeposit($latest_timeStamp->activeTime,$latest_timeStamp->inactiveTime,$all_transactions,$p2pTranx);

            $accountant->pendingWithdrawal = $this->pendingWithdrawal($latest_timeStamp->activeTime,$latest_timeStamp->inactiveTime,$p2pTranx);
            $accountant->CurrentBalance = number_format($amount);

            $accountant->activeTime = $latest_timeStamp->activeTime;
            $accountant->inactiveTime = $latest_timeStamp->inactiveTime;
        }  

        $activeUsers = $collection->where('status', 'active');
        $inActiveUsers = $collection->where('status', 'waiting');

        $value = collect()->concat($activeUsers)->concat($inActiveUsers);
        $exportData = $value->map->only(['id','first_name','last_name','email','phone','role','status','username','role_name',
        'openingBalance','closingBalance','totalAmountPaidOut','totalDeposit','pendingWithdrawal','CurrentBalance','activeTime','inactiveTime']);

        return collect($exportData);
    }

    public function totalAmountPaidOut($startTime, $endTime, Collection $tokens, Collection $utility, Collection $p2p)
    {
        if($endTime ==null)
        {
            $endTime = now();
        }

        $tranxAmount = $tokens->where('type','buy')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount_paid');
        $utilityAmount = $utility->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount');

        $p2pAmount = $p2p->where('type','withdrawal')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount');
        $export_data = $tranxAmount + $utilityAmount + $p2pAmount;

        return number_format($export_data);
    }


    public function totalDeposit($startTime, $endTime, Collection $tokens, Collection $p2p)
    {
        if($endTime ==null)
        {
            $endTime = now();
        }

        $tranxAmount = $tokens->where('type','sell')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount_paid');
        $p2pAmount = $p2p->where('type','deposit')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount');

        $export_data = $tranxAmount + $p2pAmount;

        return number_format($export_data,);
    }

    public function pendingWithdrawal($startTime, $endTime, Collection $p2p)
    {
        if($endTime ==null)
        {
            $endTime = now();
        }

        $p2pAmount = $p2p->where('type','withdrawal')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount');
        return number_format($p2pAmount);
    }

    public function TransactionsDuringActiveTime($startTime, $endTime)
    {
        
        $tranx = Transaction::with('asset','user')->where('status','success')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->get();
        foreach ($tranx as $value) {
            $value->name = $value['user']->first_name." ".$value['user']->last_name;
            $value->TransactionName = ($value['asset']->is_crypto == 0) ? "GiftCard" : "Crypto";
            $value->AmountNGN = $value->amount_paid;
            $value->valueUSD = $value->amount;
            $value->date = $value->created_at->format('d M y');
        }

        $exportData = $tranx->map->only(['id','name','TransactionName','card','AmountNGN','valueUSD','status','date']);
        return collect($exportData);
    }
}
