<?php

namespace App\Http\Controllers\Admin;

use App\FlaggedTransactions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\GeneralTemplateOne;
use Illuminate\Support\Facades\Auth;

class FlaggedTransactionsController extends Controller
{
    public function index($type = NULL)
    {
        $show_data = true;
        $transactions = NULL;

        if($type == NULL){
            $show_data = false;
        }

        if($type == 'bulkCredit'){
            $transactions = FlaggedTransactions::with('naira_transaction','transaction','user')->where('type','Bulk Credit')->get();
        }

        if($type == 'withdrawal'){
            $transactions = FlaggedTransactions::with('naira_transaction','nairaTrade','user')->where('type','Withdrawal')->get();
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
        $daily_total = $user->nairaTransactions()->whereDate('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        $daily_rem = $user->daily_max - ($daily_total + $amount);

        //check Monthly
        $monthly_total = $user->nairaTransactions()->whereYear('created_at', now())->whereMonth('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        $monthly_rem = $user->monthly_max - ($monthly_total + $amount);

        return [
            'daily' => $daily_rem,
            'monthly' => $monthly_rem
        ];
    }
}
