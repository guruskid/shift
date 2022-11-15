<?php

namespace App\Http\Controllers\Admin;

use App\FlaggedTransactions;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\NairaTrade;
use App\Transaction;
use App\User;

class FlaggedTransactionsController extends Controller
{
    public function index($type = NULL)
    {
        $show_data = true;
        $transactions = NULL;

        if($type == NULL OR $type == 'clearWithdrawal'){
            $type = 'clearWithdrawal';

            $clearedBulkCredit = FlaggedTransactions::with('naira_transaction','nairaTrade','transaction','user')
            ->where('type','Bulk Credit')
            ->WhereHas('transaction', function ($query) {
                $query->where('is_flagged', 0);
            })->get();
            
            $clearedWithdrawals= FlaggedTransactions::with('naira_transaction','nairaTrade','transaction','user')
            ->where('type','Withdrawal')
            ->WhereHas('nairaTrade', function ($query) {
                $query->where('is_flagged', 0);
            })->get();

            $clearedManualDeposit = FlaggedTransactions::with('naira_transaction','nairaTrade','transaction','user')
            ->where('type','Manual Deposit')
            ->WhereHas('naira_transaction', function ($query) {
                $query->where('is_flagged', 0);
            })->get();

            // merging the two collections together and sorting by updated_at
            $transactions = collect()
            ->concat($clearedBulkCredit)
            ->concat($clearedWithdrawals)
            ->concat($clearedManualDeposit)
            ->sortByDesc('updated_at');
        }

        if($type == 'bulkCredit'){
            $bulkCreditTransactionsRelationship = FlaggedTransactions::with('naira_transaction','transaction','user')
            ->where('type','Bulk Credit')
            ->whereHas('transaction', function ($query) {
                $query->where('is_flagged', 1);
            })->get();

            $bulkCreditNairaTransactionsRelationship = FlaggedTransactions::with('naira_transaction','transaction','user')
            ->where('type','Manual Deposit')
            ->WhereHas('naira_transaction', function ($query) {
                $query->where('is_flagged', 1);
            })->get();

            $transactions = collect()
            ->concat($bulkCreditTransactionsRelationship)
            ->concat($bulkCreditNairaTransactionsRelationship)
            ->sortByDesc('updated_at');
        }

        //withdrawal or bulk debit
        if($type == 'withdrawal'){
            $transactions = FlaggedTransactions::with('naira_transaction','nairaTrade','user')
            ->where('type','Withdrawal')->orderBy('updated_at','DESC')
            ->whereHas('nairaTrade', function ($query) {
                $query->where('is_flagged', 1);
            })->get();
        }

        // getting ledger balance for the transactions
        foreach($transactions as $t){
            $t->ledger = UserController::ledgerBalance($t->user->id)->getData();
        }
        return view('admin.flagged.index', compact([
            'show_data','type','transactions'
        ]));
    }

    public static function count(){

            $flaggedBulkCredit = FlaggedTransactions::with('naira_transaction','nairaTrade','transaction','user')
            ->where('type','Bulk Credit')
            ->WhereHas('transaction', function ($query) {
                $query->where('is_flagged', 1);
            })->count();
            
            $flaggedWithdrawals= FlaggedTransactions::with('naira_transaction','nairaTrade','transaction','user')
            ->where('type','Withdrawal')
            ->WhereHas('nairaTrade', function ($query) {
                $query->where('is_flagged', 1);
            })->count();

            $flaggedManualDeposit = FlaggedTransactions::with('naira_transaction','nairaTrade','transaction','user')
            ->where('type','Manual Deposit')
            ->WhereHas('naira_transaction', function ($query) {
                $query->where('is_flagged', 1);
            })->count();

            return ($flaggedBulkCredit + $flaggedWithdrawals + $flaggedManualDeposit);
    }

    public function clear(FlaggedTransactions $flaggedTransaction)
    {
        // checking bulk credit. NB it has link to the transactions table
        if($flaggedTransaction->type =="Bulk Credit"){
            $flaggedTransaction->transaction->is_flagged = 0;
            $flaggedTransaction->transaction->save();
        } 

        // checking bulk debit. NB it has link to the nairaTrade table
        if($flaggedTransaction->type =="Withdrawal"){
            $flaggedTransaction->nairaTrade->is_flagged = 0;
            $flaggedTransaction->nairaTrade->save();
        }

        //this checks if in the naira transaction table has a been flagged
        if($flaggedTransaction->naira_transaction->is_flagged == 1)
        {
            $flaggedTransaction->naira_transaction->is_flagged = 0;
            $flaggedTransaction->naira_transaction->save();
        }

        return back()->with(['success' => 'Transaction Cleared']);
    }

    public static function getLastTransaction($user_id)
    {
        $transactions = Transaction::where('user_id', $user_id)
        ->where('status','success')
        ->orderBy('id','DESC')
        ->first();
        
        if(!$transactions) {
            return 0;
        }

        return $transactions->first()->amount_paid;
    }

    public static function dailyCryptoTotalAmount($user_id, $amount){

        $daily = Transaction::where('user_id', $user_id)
        ->whereDate('created_at', now())
        ->whereIn('status',['success','waiting'])
        ->sum('amount_paid');

        $dailyTotal = $daily + $amount;
        return $dailyTotal;
    }

    public static function getLastWithdrawal($user_id)
    {
        $nairaTrades = NairaTrade::orderBy('id','DESC')
        ->where('user_id', $user_id)
        ->where('type','withdrawal')
        ->where('status','success')
        ->first();

        if(!$nairaTrades) {
            return 0;
        }

        return $nairaTrades->first()->amount;
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

    public static function userDailyMonthlyManualDeposit($user,$amount){
         //check daily
         $daily_total = $user->nairaTransactions()
         ->whereDate('created_at', now())
         ->where('status','success')
         ->where('is_manual',1)->sum('amount');
         $daily = $daily_total + $amount;
 
         //check Monthly
         $monthly_total = $user->nairaTransactions()
         ->whereYear('created_at', now())
         ->where('status','success')
         ->whereMonth('created_at', now())
         ->where('is_manual',1)->sum('amount');
         $monthly = $monthly_total + $amount;

         return [
            'daily' => $daily,
            'monthly' => $monthly
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
