<?php

namespace App\Http\Controllers\Admin;

use App\FlaggedTransactions;
use App\Http\Controllers\Controller;
use App\User;

class FlaggedTransactionsController extends Controller
{
    public function index($type = NULL)
    {
        $show_data = true;
        $transactions = NULL;

        if($type == NULL OR $type == 'clearWithdrawal'){
            $type = 'clearWithdrawal';
            $transactions = FlaggedTransactions::with('naira_transaction','nairaTrade','transaction','user')->orderBy('updated_at','DESC')
            ->WhereHas('transaction', function ($query) {
                $query->where('is_flagged', 0);
            })->orWhereHas('nairaTrade', function ($query) {
                $query->where('is_flagged', 0);
            })->get();
        }

        if($type == 'bulkCredit'){
            $transactions = FlaggedTransactions::with('naira_transaction','transaction','user')->where('type','Bulk Credit')->latest()
            ->whereHas('transaction', function ($query) {
                $query->where('is_flagged', 1);
            })->get();
        }

        if($type == 'withdrawal'){
            $transactions = FlaggedTransactions::with('naira_transaction','nairaTrade','user')->where('type','Withdrawal')->latest()
            ->whereHas('nairaTrade', function ($query) {
                $query->where('is_flagged', 1);
            })->get();
        }
        return view('admin.flagged.index', compact([
            'show_data','type','transactions'
        ]));
    }

    public static function count()
    {
        $bulkCreditCount =  FlaggedTransactions::where('type','Bulk Credit')->whereHas('transaction', function ($query) {
            $query->where('is_flagged', 1);
        })->count();

        $withdrawalCount =  FlaggedTransactions::where('type','Withdrawal')->whereHas('nairaTrade', function ($query) {
            $query->where('is_flagged', 1);
        })->count();

        return ($bulkCreditCount + $withdrawalCount);
    }

    public function clear(FlaggedTransactions $flaggedTransaction)
    {
        if($flaggedTransaction->type =="Bulk Credit"){
            $flaggedTransaction->transaction->is_flagged = 0;
            $flaggedTransaction->transaction->save();
        } else{
            $flaggedTransaction->nairaTrade->is_flagged = 0;
            $flaggedTransaction->nairaTrade->save();
        }

        return back()->with(['success' => 'Transaction Cleared']);
    }

    public static function getLastTransaction($user)
    {
        $transactions = $user->transactions;
        if($transactions->count() == 0) {
            return 0;
        }

        return $transactions[0]->amount_paid;
    }

    public static function getLastWithdrawal($user)
    {
        $nairaTrades = $user->nairaTrades;
        if($nairaTrades->count() == 0) {
            return 0;
        }
        $withdrawals = $nairaTrades->where('type','withdrawal')->sortByDesc('created_at');
        if($withdrawals->count() == 0){
            return 0;
        }
        return $withdrawals[0]->amount;
    }

    public static function userDailyMonthlyLimit($user,$amount)
    {
        //check daily
        $daily_total = $user->nairaTransactions()->whereDate('created_at', now())->whereIn('status',['success','pending'])->whereIn('transaction_type_id', [3, 2])->sum('amount');
        $daily_rem = $user->daily_max - ($daily_total + $amount);

        //check Monthly
        $monthly_total = $user->nairaTransactions()->whereYear('created_at', now())->whereIn('status',['success','pending'])->whereMonth('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        $monthly_rem = $user->monthly_max - ($monthly_total + $amount);

        return [
            'daily' => $daily_rem,
            'monthly' => $monthly_rem
        ];
    }

    public static function dailyTotal($user, $amount){
        $daily = $user->nairaTransactions()->whereDate('created_at', now())->whereIn('status',['success','pending'])->whereIn('transaction_type_id', [3, 2])->sum('amount');
        $dailyTotal = $daily + $amount;
        return $dailyTotal;
    }

    public static function getCurrentAccountant()
    {
        $agents = User::where(['role' => 777, 'status' => 'active'])->with(['nairaWallet', 'accounts'])->get();

        if ($agents->count() == 0) {
            $accountantTimestampSA = User::where(['role' => 889, 'status' => 'active'])
                ->with(['nairaWallet', 'accounts'])->whereHas('accountantTimestamp', function ($query) {
                $query->whereNull('inactiveTime');
            })->get();

            $agents = $accountantTimestampSA;
        }

        if(isset($agents[0])){
            return $agents[0]->id;
        } else {
            return NULL;
        }
    }
}
