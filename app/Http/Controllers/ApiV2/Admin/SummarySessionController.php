<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SummarySessionController extends Controller
{
   public function index(){
    // continue here


    $accountant = User::whereHas("accountantTimestamp")->select('id','first_name','last_name','email','phone','role','status','username')
    // $accountant = User::where("id", auth()->id())->select('id','first_name','last_name','email','phone','role','status','username')
    ->with('accountantTimestamp')->whereIn('role',[777,775, 889])->where('status', 'active')->first();



    if(!$accountant){
        return response()->json([
            'success' => false,
            'message' =>   "No data",
        ], 200);
    }



    $startTime = $accountant->accountantTimestamp->first()->activeTime;
    $endTime = ($accountant->accountantTimestamp->first()->inactiveTime == null) ? now() : $accountant->accountantTimestamp->first()->inactiveTime;
    $data['total_number_transactions'] = Transaction::whereIn('status', ['success', 'pending'])->where('updated_at', '>=', $startTime)->count();
    $data['total_amount_withdraw_transactions'] = Transaction::whereIn('status', ['success', 'pending'])->where('updated_at', '>=', $startTime)->where('type', 'sell')->sum('amount');
    $data['total_amount_deposit_transactions'] = Transaction::whereIn('status', ['success', 'pending'])->where('updated_at', '>=', $startTime)->where('type', 'buy')->sum('amount');



     $tranx = DB::table('transactions')
     ->whereIn('transactions.status', ['success', 'pending'])
    //  ->where('transactions.created_at', '>=', $startTime)
    //  ->where('transactions.created_at', '<=', $endTime)
     ->join('users', 'transactions.user_id', '=', 'users.id')
     ->select('first_name', 'last_name', 'username', 'transactions.id', 'user_id', 'card as transaction', 'amount_paid as amount', 'transactions.amount as value', DB::raw('0 as prv_bal'), DB::raw('0 as cur_bal'), 'transactions.status', DB::raw('date(transactions.created_at) as date', 'transactions.created_at as created_at'));



 $tranx2 = DB::table('naira_transactions')
     ->whereIn('naira_transactions.status', ['success', 'pending'])
    //  ->where('naira_transactions.created_at', '>=', $startTime)
    //  ->where('naira_transactions.created_at', '<=', $endTime)
     ->join('users', 'naira_transactions.user_id', '=', 'users.id')
     ->select('first_name', 'last_name', 'username', 'naira_transactions.id', 'user_id', 'type as transaction', 'amount_paid', 'naira_transactions.amount as value', 'previous_balance as prv_bal', 'current_balance as cur_bal', 'naira_transactions.status', DB::raw('date(naira_transactions.created_at) as date', 'naira_transactions.created_at as created_at'));


 $mergeTbl = $tranx->unionAll($tranx2);
 DB::table(DB::raw("({$mergeTbl->toSql()}) AS mg"))->mergeBindings($mergeTbl);



 $data['average_waiting_time'] = '';
 $data['transactions'] = $trx = $mergeTbl->orderBy('date', 'desc')->paginate(25);


// $weekly = Carbon::now('Africa/Lagos')->startOfWeek();
// $monthly = Carbon::now('Africa/Lagos')->startOfMonth();
// $quarterly = Carbon::now('Africa/Lagos')->startOfMonth()->subMonth(3);
// $yearly = Carbon::now('Africa/Lagos')->startOfYear();





     return response()->json([
        'success' => true,
        'data' =>   $data,
    ], 200);
   }

   private function GetAverageResponseTime($data_collection)
   {
       $tnx = $data_collection;
       $avg_response = 0;

       $total = $tnx->count();
       foreach ($tnx as $t) {
           if($t->status == 'pending'){
               $avg_response += now()->diffInSeconds($t->created_at);
           }else{
               $avg_response += $t->updated_at->diffInSeconds($t->created_at);
           }
       }
       if($total == 0){
           return 0;
       }

       $average = $avg_response/$total;
       return (CarbonInterval::seconds($average)->cascade()->forHumans());

   }
}
