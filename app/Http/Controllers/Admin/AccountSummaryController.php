<?php

namespace App\Http\Controllers\Admin;

use App\AccountantTimeStamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;

class AccountSummaryController extends Controller
{
    public static function roundUpAmount($collection)
    {
        foreach($collection as $col)
        {
            $col->amount = round($col->amount);
        }
    }

     public static function GetAverageResponseTime($data_collection)
    {
        $tnx = $data_collection;
        $avg_response = 0;

        $total = $tnx->count();
        foreach ($tnx as $t) {
            if($t->status == 'pending' OR $t->status == 'unresolved'){
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

    public static function showData($all_tnx, $giftcards_totaltnx, $util_tnx, $nw_deposit_tnx, $nw_withdrawal_tnx, $nw_other_tnx, $data)
    {
        $segment = $data['segment'];
        $accountant= $data['accountant'];
        $day= $data['day'];
        $month= $data['month'];
        $accountant_name = $data['accountant_name'];

        self::roundUpAmount($all_tnx);

        //** All Crypto and GiftCard Summary */
        $all_tnx_count = $all_tnx->where('status', 'success')->count();
        $allCountBuy = $all_tnx->where('status', 'success')->where('type', 'buy')->count();
        $allCountSell = $all_tnx->where('status','success')->where('type', 'sell')->count();

        $allNairaAmountBuy = $all_tnx->where('status', 'success')->where('type', 'buy')->sum('amount_paid');
        $allNairaAmountSell = $all_tnx->where('status','success')->where('type', 'sell')->sum('amount_paid');

        //** Bitcoin */
        $bitcoin_total_tnx = $all_tnx->where('status', 'success')->where('card_id',102);

        $bitcoin_total_tnx_buy = $bitcoin_total_tnx->where('type', 'buy');
        $bitcoin_total_tnx_sell = $bitcoin_total_tnx->where('type', 'sell');

        $BTCbuyQuantity = $bitcoin_total_tnx_buy->sum('quantity');
        $BTCsellQuantity = $bitcoin_total_tnx_sell->sum('quantity');

        $BTCbuyUsdAmount = $bitcoin_total_tnx_buy->sum('amount');
        $BTCbuyNairaAmount = $bitcoin_total_tnx_buy->sum('amount_paid');

        $BTCbuyCount = $bitcoin_total_tnx_buy->count();
        $BTCsellUsdAmount = $bitcoin_total_tnx_sell->sum('amount');

        $BTCsellNairaAmount = $bitcoin_total_tnx_sell->sum('amount_paid');
        $BTCsellCount = $bitcoin_total_tnx_sell->count();

        //** USDT */
        $USDTranx = $all_tnx->where('status', 'success')->where('card_id',143);
        $USDTbuyTranx = $USDTranx->where('type', 'buy');
        $USDTsellTranx = $USDTranx->where('type', 'sell');

        $USDTbuyQuantity = $USDTbuyTranx->sum('quantity');
        $USDTsellQuantity = $USDTsellTranx->sum('quantity');

        $USDTbuyUsdAmount = $USDTbuyTranx->sum('amount');
        $USDTbuyNairaAmount = $USDTbuyTranx->sum('amount_paid');

        $USDTbuyCount = $USDTbuyTranx->count();
        $USDTsellUsdAmount = $USDTsellTranx->sum('amount');

        $USDTsellNairaAmount = $USDTsellTranx->sum('amount_paid');
        $USDTsellCount = $USDTsellTranx->count();

        //**GiftCard */
        self::roundUpAmount($giftcards_totaltnx);

        //** Sell */
        $giftcards_totaltnx_sell = $giftcards_totaltnx->where('type', 'sell');
        $giftcards_totaltnx_sell_amount = 0;

        foreach ($giftcards_totaltnx_sell as $st) {
            $giftcards_totaltnx_sell_amount += ($st->amount * $st->quantity);
        }
        $giftcards_totaltnx_sell_amount_naira = $giftcards_totaltnx_sell->sum('amount_paid');
        $giftcards_totaltnx_sell = $giftcards_totaltnx_sell->count();

        //** BUY */
        $giftcards_totaltnx_buy = $giftcards_totaltnx->where('type', 'buy');
        $giftcards_totaltnx_buy_amount = 0;

        foreach ($giftcards_totaltnx_buy as $st) {
            $giftcards_totaltnx_buy_amount += ($st->amount * $st->quantity);
        }

        $giftcards_totaltnx_buy_amount_naira = $giftcards_totaltnx_buy->sum('amount_paid');
        $giftcards_totaltnx_buy = $giftcards_totaltnx_buy->count();

        //** Utilities */
        $util_tnx = $util_tnx->sortByDesc('updated_at');
        $util_total_tnx = $util_tnx->where('status','success')->count();

        $util_tnx_amount = $util_tnx->where('status','success')->sum('amount');
        $util_tnx_fee = $util_tnx->where('status','success')->sum('convenience_fee');

        $util_amount_paid = $util_tnx->where('status','success')->sum('total');

        //** PayBridge Deposit */

        $nw_deposit_tnx = $nw_deposit_tnx->sortByDesc('updated_at');
        $nw_deposit_tnx_total = $nw_deposit_tnx->where('status','success')->count();

        $nw_deposit_amount_paid = $nw_deposit_tnx->where('status','success')->sum('amount_paid');
        $nw_deposit_tnx_charges = $nw_deposit_tnx->where('status','success')->sum('charge');
        $nw_deposit_total_amount = $nw_deposit_tnx->where('status','success')->sum('amount');

        $nw_deposit_pending_total = $nw_deposit_tnx->where('status','pending')->count();
        $nw_deposit_pending_amount = $nw_deposit_tnx->where('status','pending')->sum('amount');

        $deposit_total = NairaTransaction::latest()->where('status','pending')->where('transaction_type_id',1)->get();

        $deposit_total_pending = $deposit_total->where('status','pending')->count();
        $deposit_total_pending_amount = $deposit_total->where('status','pending')->sum('amount');
        $averageResponseTimeDeposit = self::GetAverageResponseTime($nw_deposit_tnx);

        //** PayBridge Withdrawal */

        $nw_withdrawal_tnx = $nw_withdrawal_tnx->sortByDesc('updated_at');
        $nw_withdrawal_tnx_total = $nw_withdrawal_tnx->where('status','success')->count();

        $nw_withdrawal_amount_paid = $nw_withdrawal_tnx->where('status','success')->sum('amount_paid');
        $nw_withdrawal_tnx_charges = $nw_withdrawal_tnx->where('status','success')->sum('charge');

        $nw_withdrawal_total_amount = $nw_withdrawal_tnx->where('status','success')->sum('amount');

        $nw_withdrawal_pending_total = $nw_withdrawal_tnx->where('status','pending')->count();
        $nw_withdrawal_pending_amount = $nw_withdrawal_tnx->where('status','pending')->sum('amount');

        $withdrawal_total = NairaTransaction::latest()->where('status','pending')->where('transaction_type_id',3)->get();
        $withdrawal_total_pending = $withdrawal_total->where('status','pending')->count();

        $withdrawal_total_pending_amount = $withdrawal_total->where('status','pending')->sum('amount');
        $averageResponseTimeWithdrawal = self::GetAverageResponseTime($nw_withdrawal_tnx);

        //** PayBridgeOthers */

        $nw_other_tnx = $nw_other_tnx->sortByDesc('updated_at');
        $nw_other_tnx_total = $nw_other_tnx->where('status','success')->count();

        $nw_other_amount_paid = $nw_other_tnx->where('status','success')->sum('amount_paid');
        $nw_other_tnx_charges = $nw_other_tnx->where('status','success')->sum('charge');
        $nw_other_total_amount = $nw_other_tnx->where('status','success')->sum('amount');

        $nw_other_pending_total = $nw_other_tnx->where('status','pending')->count();
        $nw_other_pending_amount = $nw_other_tnx->where('status','pending')->sum('amount');

        $other_total = NairaTransaction::latest()->where('status','pending')
        ->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3)->get();

        $other_total_pending = $other_total->where('status','pending')->count();
        $other_total_pending_amount = $other_total->where('status','pending')->sum('amount');

        $averageResponseTimeOthers = self::GetAverageResponseTime($nw_other_tnx);

        return view('admin.summary.new_pages.index', compact([
            'segment','accountant','all_tnx','all_tnx_count','day','month','allNairaAmountBuy','allNairaAmountSell','allCountBuy','allCountSell',

            'giftcards_totaltnx_buy','giftcards_totaltnx_sell','giftcards_totaltnx_buy_amount','giftcards_totaltnx_sell_amount','giftcards_totaltnx_sell_amount_naira','giftcards_totaltnx_buy_amount_naira',

            'BTCbuyCount','BTCsellCount','BTCbuyUsdAmount','BTCsellUsdAmount','BTCbuyQuantity','BTCsellQuantity','BTCsellNairaAmount','BTCbuyNairaAmount',

            'USDTbuyQuantity','USDTsellQuantity','USDTbuyUsdAmount','USDTbuyNairaAmount','USDTbuyCount','USDTsellUsdAmount','USDTsellNairaAmount','USDTsellCount',

            'util_tnx','util_total_tnx', 'util_tnx_amount', 'util_tnx_fee' , 'util_amount_paid',

            'nw_deposit_tnx','nw_deposit_tnx_total','nw_deposit_amount_paid','nw_deposit_tnx_charges','nw_deposit_total_amount',

            'nw_deposit_pending_total','nw_deposit_pending_amount','deposit_total_pending','deposit_total_pending_amount','averageResponseTimeDeposit',

            'nw_withdrawal_tnx','nw_withdrawal_tnx_total','nw_withdrawal_amount_paid','nw_withdrawal_tnx_charges','nw_withdrawal_total_amount',

            'nw_withdrawal_pending_total','nw_withdrawal_pending_amount','withdrawal_total_pending','withdrawal_total_pending_amount','averageResponseTimeWithdrawal',

            'nw_other_tnx','nw_other_tnx_total','nw_other_amount_paid','nw_other_tnx_charges','nw_other_total_amount',

            'nw_other_pending_total','nw_other_pending_amount','other_total_pending','other_total_pending_amount','averageResponseTimeOthers',
        ]));

    }

    public static function loadSummary($month, $day)
    {
        $date = date('Y').'-'.$month.'-'.$day;
        $date= Carbon::parse($date);
        $current_day_value = Carbon::parse($date);
        $segment = $current_day_value->format("M d");

        $user = Auth::user();
        $accountant = User::whereIn('role', [889,777,775])->get();

        $data = array(
            'segment'=>$segment,
            'accountant'=>$accountant,
            'day' =>$day,
            'month' => $month,
            'accountant_name' => null
        );

        //Crypto and GiftCards
        $transactions = Transaction::orderBy('created_at', 'DESC');
        $transactions = self::category_listing($user,null,$transactions,$current_day_value);
        $all_tnx = $transactions->get();

        //**GiftCards Transactions */
        $giftcards_totaltnx = $transactions->whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where('status', 'success')->get();

        //** Utility Transaction */
        $util_tnx = UtilityTransaction::whereNotNull('id')->orderBy('updated_at', 'desc');
        $util_tnx = self::category_listing($user,null,$util_tnx,$current_day_value);
        $util_tnx = $util_tnx->get();

        $payBridge = NairaTransaction::orderBy('updated_at','desc')->get();

        //** PayBridge Deposit */
        $nw_deposit_tnx = $payBridge->where('transaction_type_id',1);
        $nw_deposit_tnx = self::category_listing($user,null,$nw_deposit_tnx,$current_day_value);

        //** PayBridge Withdrawal */
        $nw_withdrawal_tnx = $payBridge->where('transaction_type_id',3);
        $nw_withdrawal_tnx = self::category_listing($user,null,$nw_withdrawal_tnx,$current_day_value);

        //** PayBridge Others */
        $nw_other_tnx = $payBridge->whereNotIn('transaction_type_id',[1,3]);
        $nw_other_tnx = self::category_listing($user,null,$nw_other_tnx,$current_day_value);

        return self::showData($all_tnx, $giftcards_totaltnx, $util_tnx, $nw_deposit_tnx, $nw_withdrawal_tnx, $nw_other_tnx, $data);

    }

    public static function category_listing($user,$accountant_timestamp,$value,$current_day_value)
    {
        if (($user->role == 777 OR $user->role == 775  ) && !empty($accountant_timestamp)) {
            $value = $value
            ->where('updated_at', '>=', $accountant_timestamp->created_at)
            ->where('updated_at', '<=', $accountant_timestamp->updated_at);
        } else {
            $value = $value
            ->where('updated_at','>=', $current_day_value->format('Y-m-d')." 00:00:00")
            ->where('updated_at','<=', $current_day_value->format('Y-m-d')." 23:59:59");
        }
        return $value;
    }

    public function sorting(Request $request)
    {
        $startDate =  $request->startdate;
        $endDate = $request->enddate;
        $accountant_name = null;

        if(!isset($request->Accountant) OR $request->Accountant == 'null' )
        {
            $user = Auth::user();
        }
        else{
            $user = User::find($request->Accountant);
            $accountant_name = $user->first_name ?: $user->email;
        }
        $accountant = User::whereIn('role', [777,775,889])->get();

        if($request->Accountant == 'null'){
            return $this->sortByDate($request,$startDate,$endDate,$accountant,$accountant_name);
        } else {
            return $this->sortByAccountant($request,$startDate,$endDate,$user,$accountant,$accountant_name);
        }
    }

    public function sortByDate($request,$startDate,$endDate,$accountant,$accountant_name)
    {
        $day= $request->day; 
        $month= $request->month;
        if($startDate != null)
        {
            $start = str_replace("T"," ",$startDate);
            $startDate = $start.":00";
            $request->startdate = $start.":00";
        }

        if($endDate != null)
        {
            $end = str_replace("T"," ",$endDate);
            $endDate = $end.":59";
            $request->enddate = $end.":59";
        }

        if($request->startdate != null)
        {
            $segment = Carbon::parse($startDate)->format('d F Y-h:ia');
            if($request->enddate != null){
                $segment .= "  To  ".Carbon::parse($endDate)->format('d F Y-h:ia');
            }
        }

        if($endDate == null)
        {
            $end = explode(" ",$startDate);
            $endDate = $end[0]." 23:59:59";
            $request->enddate = $end[0]." 23:59:59";
        }

        $data = array(
            'segment'=>$segment,
            'accountant'=>$accountant,
            'day' =>$day,
            'month' => $month,
            'accountant_name' => $accountant_name,
        );

        $transactions = Transaction::orderBy('created_at', 'DESC');
        $transactions = self::sortingByFullDate($transactions,$startDate,$endDate);
        $all_tnx = $transactions->get();

        //**GiftCards Transactions */
        $giftcards_totaltnx = $transactions->whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where('status', 'success')->get();

        //** Utility Transaction */
        $util_tnx = UtilityTransaction::whereNotNull('id')->orderBy('updated_at', 'desc');
        $util_tnx = self::sortingByFullDate($util_tnx,$startDate,$endDate);
        $util_tnx = $util_tnx->get();

        $payBridge = NairaTransaction::orderBy('updated_at','desc')->get();

        //** PayBridge Deposit */
        $nw_deposit_tnx = $payBridge->where('transaction_type_id',1);
        $nw_deposit_tnx = self::sortingByFullDate($nw_deposit_tnx,$startDate,$endDate);

        //** PayBridge Withdrawal */
        $nw_withdrawal_tnx = $payBridge->where('transaction_type_id',3);
        $nw_withdrawal_tnx = self::sortingByFullDate($nw_withdrawal_tnx,$startDate,$endDate);

        //** PayBridge Others */
        $nw_other_tnx = $payBridge->whereNotIn('transaction_type_id',[1,3]);
        $nw_other_tnx = self::sortingByFullDate($nw_other_tnx,$startDate,$endDate);

        return self::showData($all_tnx, $giftcards_totaltnx, $util_tnx, $nw_deposit_tnx, $nw_withdrawal_tnx, $nw_other_tnx, $data);
    }

    public function sortByAccountant($request,$startDate,$endDate,$user,$accountant,$accountant_name)
    {

        $day= $request->day; 
        $month= $request->month;

        if($startDate)
        {
            $start = explode("T",$startDate);
            $startDate = $start[0];
            $request->startdate = $start[0];
        }

        if($endDate)
        {
            $end = explode("T",$endDate);
            $endDate = $end[0];
            $request->enddate = $end[0];
        }

        if($startDate == "")
        {
            $startDate = date('Y')."-$month-$day";
        }

        if($request->Accountant != 'null'){
            $segment = $accountant_name." ".Carbon::parse($startDate)->format('d F Y');
        }

        if($request->startdate != null AND $request->Accountant != 'null')
        {
            $segment = $accountant_name." ".Carbon::parse($startDate)->format('d F Y');

            if($request->enddate != null){
                $segment .= " to ".Carbon::parse($endDate)->format('d F Y');
            }
        }

        $data = array(
            'segment'=>$segment,
            'accountant'=>$accountant,
            'day' =>$day,
            'month' => $month,
            'accountant_name' => $accountant_name,
        );

        if(!$endDate)
        {
            $endDate = $startDate;
            $requestDetails['enddate'] = $startDate;
        }

        $accountant_timestamp = AccountantTimeStamp::whereDate('activeTime','>=',$startDate)
        ->whereDate('activeTime','<=',$endDate)->where('user_id',$user->id)->get();

        if($user->role == 777)
        {
            if(!isset($start)){
                $start =[$startDate,"00:00"];
            }
            if(!isset($end))
            {
                $end = [$endDate,"23:59"];
            }

            return $this->juniorAccountantSort($start, $end, $segment,$accountant_timestamp,$user,$data);
        }

        $all_tnx = collect();
        $giftcards_totaltnx = collect();

        $util_tnx = collect();
        $payBridge = collect();

        foreach($accountant_timestamp as $at)
        {
            $transactions = Transaction::orderBy('created_at', 'DESC');
            $transactions = $this->sortingByAccountantTimestamp($transactions, $at->activeTime, $at->inactiveTime);
            $all_tnx = $all_tnx->concat($transactions);

            $giftTranx = Transaction::where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->orderBy('updated_at','desc');

            $giftTranx = $this->sortingByAccountantTimestamp($giftTranx, $at->activeTime, $at->inactiveTime);
            $giftcards_totaltnx = $giftcards_totaltnx->concat($giftTranx);

            $utilityTranx = UtilityTransaction::orderBy('updated_at','desc');
            $utilityTranx = $this->sortingByAccountantTimestamp($utilityTranx, $at->activeTime, $at->inactiveTime);
            $util_tnx = $util_tnx->concat($utilityTranx);

            $payBridgeTranx = NairaTransaction::orderBy('updated_at','desc');
            $payBridgeTranx = $this->sortingByAccountantTimestamp($payBridgeTranx, $at->activeTime, $at->inactiveTime);
            $payBridge = $payBridge->concat($payBridgeTranx);
        }
            $nw_deposit_tnx = $payBridge->where('transaction_type_id',1);
            $nw_withdrawal_tnx = $payBridge->where('transaction_type_id',3);
            $nw_other_tnx = $payBridge->whereNotIn('transaction_type_id',[1,3]);

            return self::showData($all_tnx, $giftcards_totaltnx, $util_tnx, $nw_deposit_tnx, $nw_withdrawal_tnx, $nw_other_tnx, $data);
        
    }

    public function juniorAccountantSort($start, $end, $segment,$accountant_timestamp,$user,$data)
    {
        if($accountant_timestamp->count() == 0){
            $accountant_timestamp = AccountantTimeStamp::whereDate('activeTime','<=',$end[0])
            ->where('user_id',$user->id)->orderBy('id','DESC')->limit(10)->get();
        }

        $start = Carbon::parse($start[0]." ".$start[1].":00");
        $end = Carbon::parse($end[0]." ".$end[1].":59");

        $all_tnx = collect();
        $giftcards_totaltnx = collect();

        $util_tnx = collect();
        $payBridge = collect();

        foreach($accountant_timestamp as $at)
        {
            $transactions = Transaction::orderBy('created_at', 'DESC');
            $transactions = $this->sortingByAccountantTimestamp($transactions, $at->activeTime, $at->inactiveTime);
            $all_tnx = $all_tnx->concat($transactions);

            $giftTranx = Transaction::where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->orderBy('updated_at','desc');

            $giftTranx = $this->sortingByAccountantTimestamp($giftTranx, $at->activeTime, $at->inactiveTime);
            $giftcards_totaltnx = $giftcards_totaltnx->concat($giftTranx);

            $utilityTranx = UtilityTransaction::orderBy('updated_at','desc');
            $utilityTranx = $this->sortingByAccountantTimestamp($utilityTranx, $at->activeTime, $at->inactiveTime);
            $util_tnx = $util_tnx->concat($utilityTranx);

            $payBridgeTranx = NairaTransaction::orderBy('updated_at','desc');
            $payBridgeTranx = $this->sortingByAccountantTimestamp($payBridgeTranx, $at->activeTime, $at->inactiveTime);
            $payBridge = $payBridge->concat($payBridgeTranx);
        }
            $all_tnx = $all_tnx->whereBetween('updated_at', [$start, $end]);
            $giftcards_totaltnx =$giftcards_totaltnx->whereBetween('updated_at', [$start, $end]);

            $util_tnx = $util_tnx->whereBetween('updated_at', [$start, $end]);
            $payBridge = $payBridge->whereBetween('updated_at', [$start, $end]);
        
            $nw_deposit_tnx = $payBridge->where('transaction_type_id',1);
            $nw_withdrawal_tnx = $payBridge->where('transaction_type_id',3);
            $nw_other_tnx = $payBridge->whereNotIn('transaction_type_id',[1,3]);

            return self::showData($all_tnx, $giftcards_totaltnx, $util_tnx, $nw_deposit_tnx, $nw_withdrawal_tnx, $nw_other_tnx, $data);
    }

    public function sortingByFullDate($value ,$start,$end)
    {
        $value = $value->where('updated_at', '>=', $start);
        if($end)
        {
            $value = $value->where('updated_at', '<=', $end);
        }

        return $value;
    }

    public function sortingByAccountantTimestamp($value, $activeTime, $inactiveTime)
    {
        if($inactiveTime == null)
        {
            $inactiveTime = now();
        }
        $value = $value->where('updated_at','>=',$activeTime)->where('updated_at', '<=', $inactiveTime)->get();

        return $value;
    }
}
