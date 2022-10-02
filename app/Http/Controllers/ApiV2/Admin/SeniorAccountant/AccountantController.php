<?php

namespace App\Http\Controllers\ApiV2\Admin\SeniorAccountant;

use App\AccountantTimeStamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use DB;

class AccountantController extends Controller
{
    public function AccountantOverview()
    {


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
            $total_number_withdrawal_transactions   =  Transaction::whereIn('status', ['success', 'pending'])->where('updated_at','>=',$startTime )->where('type', 'sell')->count();
            $total_number_utility_transactions = UtilityTransaction::whereIn('status', ['success', 'pending'])->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->count();
            $number_of_trades = Transaction::where("status", 'success')->where('updated_at','>=',$startTime )->count();
            $total_amount_traded = Transaction::where("status", 'success')->where('updated_at','>=',$startTime)->sum('amount_paid');
            $total_volume_trades = Transaction::where("status", 'success')->where('updated_at','>=',$startTime)->sum('amount');
            $total_debited_amount__utilities_transactions = UtilityTransaction::whereIn('status', ['success', 'pending'])->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount');

            $data['overview']= [
                'number_of_trades'=> $number_of_trades,
                'total_amount_deposit_p2p'=>$total_amount_deposit_p2p,
                'total_number_withdrawal_transactions'=>  $total_number_withdrawal_transactions,
                'total_number_utility_transactions'=>   $total_number_utility_transactions,
                'total_volume_trades'=> $total_volume_trades ,
                'total_amount_withdrawn_p2p'=> $total_amount_withdrawal_p2p,
                'total_amount_traded'=>$total_amount_traded,
                'total_number_transactions_p2p'=>  $total_number_transactions_p2p,
                'total_debited_amount__utilities_transactions' => $total_debited_amount__utilities_transactions
            ];



            // recent transactions
            $tranx = DB::table('transactions')
            ->whereIn('transactions.status', ['success', 'pending'])
            ->where('transactions.created_at','>=',$startTime)
            ->where('transactions.created_at','<=',$endTime)
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select('first_name','last_name','username','transactions.id','user_id','card as transaction','amount_paid as amount','transactions.amount as value',DB::raw('0 as prv_bal'),DB::raw('0 as cur_bal'),'transactions.status',DB::raw('date(transactions.created_at) as date','transactions.created_at as created_at'))
            ;


            // naira Transaction table
            $tranx2 = DB::table('naira_transactions')
            ->whereIn('naira_transactions.status', ['success', 'pending'])
            ->where('naira_transactions.created_at','>=',$startTime)
            ->where('naira_transactions.created_at','<=',$endTime)
            ->join('users', 'naira_transactions.user_id', '=', 'users.id')
            ->select('first_name','last_name','username','naira_transactions.id','user_id','type as transaction','amount_paid','naira_transactions.amount as value','previous_balance as prv_bal','current_balance as cur_bal','naira_transactions.status',DB::raw('date(naira_transactions.created_at) as date','naira_transactions.created_at as created_at'));

            // merge table
            $mergeTbl = $tranx->unionAll($tranx2);
        DB::table(DB::raw("({$mergeTbl->toSql()}) AS mg"))->mergeBindings($mergeTbl);

        $data['transactions'] =  $mergeTbl->orderBy('date','desc')->take(20)->get();




        }



        return response()->json([
            'success' => true,
            'data' => $data ,
        ], 200);
    }

    public function GetActiveAccountant(){
       $data['accountant'] = User::select('id','first_name','last_name','email','phone','role','status','username')->where("status", "active")->whereHas("accountantTimestamp")->first();
       return response()->json([
        'success' => true,
        'data' => $data ,
       ], 200);
    }




    
}
