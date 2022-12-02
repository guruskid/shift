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

class AccountSummaryController extends Controller
{
    public $accountants;
    public $accountantName;
    public $showData;
    public $segment;
    public $category;
    public $day;
    public $month;

    public function __construct(){
        $this->accountants = User::whereIn('role',[889, 777, 775])->get();
        $this->showData = TRUE;
    }

    public function roundUpAmount($collection){
        foreach($collection as $col){
            $col->amount = round($col->amount);
        }
    }

    public function cryptoGiftCardTransactions($transactions , $giftCardTransactions){
        $accountantName = $this->accountantName;
        $accountant = $this->accountants;
        $showCategory = $this->category;
        $showSummary = $this->showData;
        $showData = $this->showData;
        $segment  = $this->segment;
        $month = $this->month;
        $day = $this->day;

        //** Round up Amount for transactions */
        $this->roundUpAmount($transactions);

        //** Transaction Analytics */
        $transactionCount = $transactions
        ->where('status', 'success')
        ->count();

        $transactionBuyCount = $transactions
        ->where('status', 'success')
        ->whereIn('type', ['buy','Buy'])
        ->count();

        $transactionSellCount = $transactions
        ->where('status', 'success')
        ->whereIn('type', ['sell','Sell'])
        ->count();

        $transactionBuyNairaValue = $transactions
        ->where('status', 'success')
        ->whereIn('type', ['buy','Buy'])
        ->sum('amount_paid');

        $transactionSellNairaValue = $transactions
        ->where('status', 'success')
        ->whereIn('type', ['sell','Sell'])
        ->sum('amount_paid');

        //** Crypto Analytics */

        //** BTC */
        $bitcoinTransactions = $transactions
        ->where('status', 'success')
        ->where('card_id',102);

        $bitcoinBuyTransaction = $bitcoinTransactions
        ->whereIn('type', ['buy','Buy']);

        $bitcoinSellTransaction = $bitcoinTransactions
        ->whereIn('type', ['sell','Sell']);

        //** BTC Buy Transactions */
        $bitcoinBuyCount = $bitcoinBuyTransaction
        ->count();

        $bitcoinBuyQuantity = $bitcoinBuyTransaction
        ->sum('quantity');

        $bitcoinBuyUsdValue = $bitcoinBuyTransaction
        ->sum('amount');

        $bitcoinBuyNairaValue = $bitcoinBuyTransaction
        ->sum('amount_paid');

        //** BTC Sell TRansactions */
        $bitcoinSellCount = $bitcoinSellTransaction
        ->count();

        $bitcoinSellQuantity = $bitcoinSellTransaction
        ->sum('quantity');

        $bitcoinSellUsdValue = $bitcoinSellTransaction
        ->sum('amount');

        $bitcoinSellNairaValue = $bitcoinSellTransaction
        ->sum('amount_paid');
        

        //** USDT */
        $usdtTransactions = $transactions
        ->where('status', 'success')
        ->where('card_id',143);

        $usdtBuyTransaction = $usdtTransactions
        ->whereIn('type', ['buy','Buy']);

        $usdtSellTransaction = $usdtTransactions
        ->whereIn('type', ['sell','Sell']);

        //** USDT Buy Transactions */
        $usdtBuyCount = $usdtBuyTransaction
        ->count();

        $usdtBuyQuantity = $usdtBuyTransaction
        ->sum('quantity');

        $usdtBuyUsdValue = $usdtBuyTransaction
        ->sum('amount');

        $usdtBuyNairaValue = $usdtBuyTransaction
        ->sum('amount_paid');

        //** USDT Sell TRansactions */
        $usdtSellCount = $usdtSellTransaction
        ->count();

        $usdtSellQuantity = $usdtSellTransaction
        ->sum('quantity');

        $usdtSellUsdValue = $usdtSellTransaction
        ->sum('amount');

        $usdtSellNairaValue = $usdtSellTransaction
        ->sum('amount_paid');

        //** GiftCard Transactions */
        $giftCardBuyTransaction = $giftCardTransactions
        ->whereIn('type', ['buy','Buy']);

        $giftCardSellTransaction = $giftCardTransactions
        ->whereIn('type', ['sell','Sell']);

        //** GiftCard Buy Transaction */
        $giftCardBuyCount = $giftCardBuyTransaction->count();

        $giftCardBuyUsdValue = 0;
        foreach($giftCardBuyTransaction as $gcbt){
            $giftCardBuyUsdValue += ($gcbt->amount * $gcbt->quantity); 
        }
        $giftCardBuyNairaValue = $giftCardBuyTransaction->sum('amount_paid');     
        
        //** GiftCard Sell Transactions */

        $giftCardSellCount = $giftCardSellTransaction->count();

        $giftCardSellUsdValue = 0;
        foreach($giftCardSellTransaction as $gcst){
            $giftCardSellUsdValue += ($gcst->amount * $gcst->quantity); 
        }
        $giftCardSellNairaValue = $giftCardSellTransaction->sum('amount_paid');  

        $revenueGrowth = self::percentageRevenueGrowth()->getData();
        $averageRevenuePerUniqueUsers = self::averageRevenuePerUniqueUser()->getData();
        $averageRevenuePerTransaction = self::averageRevenuePerTransaction()->getData();

        return view('admin.summary.JuniorAccountant.transaction',compact([
            'accountantName','accountant','showCategory','showSummary','showData','segment','month','day','transactions',

            'transactionCount','transactionBuyCount','transactionSellCount','transactionBuyNairaValue','transactionSellNairaValue',

            'bitcoinBuyCount','bitcoinBuyQuantity','bitcoinBuyUsdValue','bitcoinBuyNairaValue',

            'bitcoinSellCount', 'bitcoinSellQuantity', 'bitcoinSellUsdValue', 'bitcoinSellNairaValue',

            'usdtBuyCount','usdtBuyQuantity','usdtBuyUsdValue','usdtBuyNairaValue',

            'usdtSellCount','usdtSellQuantity','usdtSellUsdValue','usdtSellNairaValue',

            'giftCardBuyCount', 'giftCardBuyUsdValue', 'giftCardBuyNairaValue',

            'giftCardSellCount', 'giftCardSellUsdValue', 'giftCardSellNairaValue',

            'revenueGrowth','averageRevenuePerUniqueUsers','averageRevenuePerTransaction'
        ]));
    }

    public function utilityTransactions($utilityTransactions){
        $accountantName = $this->accountantName;
        $accountant = $this->accountants;
        $showCategory = $this->category;
        $showSummary = $this->showData;
        $showData = $this->showData;
        $segment  = $this->segment;
        $month = $this->month;
        $day = $this->day;

        $utilitiesTotalCount = $utilityTransactions
        ->where('status', 'success')
        ->count();

        $utilitiesTotalAmount = $utilityTransactions
        ->where('status', 'success')
        ->sum('amount');

        $utilitiesTotalContinenceFee = $utilityTransactions
        ->where('status', 'success')
        ->sum('convenience_fee');

        $utilitiesTotal = $utilityTransactions
        ->where('status', 'success')
        ->sum('total');

        return view('admin.summary.JuniorAccountant.transaction',compact([
            'accountantName','accountant','showCategory','showSummary','showData','segment','month','day','utilityTransactions',
            'utilitiesTotalCount','utilitiesTotalAmount','utilitiesTotalContinenceFee','utilitiesTotal'
        ]));
    } 

    public function GetAverageResponseTime($data_collection)
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

    public function payBridge($payBridgeTransactions, $id){
        $accountantName = $this->accountantName;
        $accountant = $this->accountants;
        $showCategory = $this->category;
        $showSummary = $this->showData;
        $showData = $this->showData;
        $segment  = $this->segment;
        $month = $this->month;
        $day = $this->day;

        $payBridgeTransactionsCount = $payBridgeTransactions
        ->where('status','success')
        ->count();

        $payBridgeTransactionsAmountPaid = $payBridgeTransactions
        ->where('status','success')
        ->sum('amount_paid');

        $payBridgeTransactionsCharges = $payBridgeTransactions
        ->where('status','success')
        ->sum('charge');

        $payBridgeTransactionsAmount = $payBridgeTransactions
        ->where('status','success')
        ->sum('amount');

        $payBridgeTransactionsPendingCount = $payBridgeTransactions
        ->where('status','pending')
        ->count();

        $payBridgeTransactionsPendingAmount = $payBridgeTransactions
        ->where('status','pending')
        ->sum('amount');

        $averageResponseTime = $this->GetAverageResponseTime($payBridgeTransactions);

        $pendingTotal = NairaTransaction::where('status','pending')->where('transaction_type_id',$id)->get();

        $pendingTotalCount = $pendingTotal->count();
        $pendingTotalAmount = $pendingTotal->sum('amount');

        return view('admin.summary.JuniorAccountant.transaction',compact([
            'accountantName','accountant','showCategory','showSummary','showData','segment','month','day','payBridgeTransactions',
            'payBridgeTransactionsCount','payBridgeTransactionsAmountPaid','payBridgeTransactionsCharges','payBridgeTransactionsAmount',
            'payBridgeTransactionsPendingCount','payBridgeTransactionsPendingAmount','pendingTotal','pendingTotalCount','pendingTotalAmount',
            'averageResponseTime'
        ]));
    }

    public function index($month, $day, $category){
        //** Current Date Data */
        $dateString  = now()->year . '-' . $month . '-' . $day;
        $selectedDate = new Carbon($dateString);
        $startDate = $dateString." 00:00:00";
        $endDate = $dateString." 23:59:59";

        //** setting global class variables */
        $this->category = isset($category) ? $category : NULL;
        $this->accountantName = NULL;
        $this->segment = $selectedDate->format("M d");
        $this->month = $month;
        $this->day = $day;

        return $this->sortByDateRange($startDate, $endDate);
    }

    public function sortTransactions($request){
        if($request['startdate'] == null AND $request['enddate'] == null AND $request['Accountant'] == "null"){
            return back()->with(['error' => 'Sorting Field is Empty ']);
        }
        $this->month = $request['month'];
        $this->day = $request['day'];
        $this->category = $request['category'];

        $startDate =  $request['startdate'];
        $endDate = $request['enddate'];
        $this->accountantName = NULL;

        if(isset($request['Accountant']) || $request['Accountant'] != 'null'){
            $user = User::find($request['Accountant']);
            if($user){
                $this->accountantName = $user->first_name ?: $user->email;
            }
        }

        if($request['Accountant'] == "null"){
            return $this->sortByDate($startDate, $endDate);
        } else {
            return $this->sortByAccountants($startDate, $endDate, $user);
        }
    }

    public function sortByDate($startDate,$endDate){
        if($startDate != null){
            $start = str_replace("T"," ",$startDate);
            $startDate = $start.":00";
        }

        if($endDate != null){
            $end = str_replace("T"," ",$endDate);
            $endDate = $end.":59";
        } else {
            $end = explode(" ", $startDate);
            $endDate = $end[0]." 23:59:59";
        }

        $this->segment = Carbon::parse($startDate)->format('d F Y-h:ia');
        $this->segment .= "  To  ".Carbon::parse($endDate)->format('d F Y-h:ia');
        return $this->sortByDateRange($startDate, $endDate);   
    }

    public function sortByDateRange($startDate,$endDate){
        if($this->category == 'all'){
            //** Crypto and GiftCard Transactions */
            $cryptoGiftCard = Transaction::with('user','agent','accountant')
            ->orderBy('updated_at', 'DESC')
            ->where('updated_at','>=',$startDate)
            ->where('updated_at','<=',$endDate);

            $allTransactions = $cryptoGiftCard->get();

            //** GiftCard Transactions */
            $giftCardTransactions = $cryptoGiftCard
            ->where('status', 'success')
            ->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->get();

            return $this->cryptoGiftCardTransactions($allTransactions, $giftCardTransactions);
        }

        if($this->category == 'utilities'){
            $utilityTransactions = UtilityTransaction::with('user')
            ->orderBy('updated_at', 'DESC')
            ->where('updated_at','>=',$startDate)
            ->where('updated_at','<=',$endDate)
            ->get();

            return $this->utilityTransactions($utilityTransactions);
        }

        if($this->category == "paybridge" OR $this->category == "paybridgewithdrawal"){
            $payBridgeTransactions = NairaTransaction::with('transactionType','user')
            ->orderBy('updated_at','desc')
            ->where('updated_at','>=',$startDate)
            ->where('updated_at','<=',$endDate)
            ->get();

            if ($this->category == "paybridge"){
                $id = 1;
            } elseif ($this->category == "paybridgewithdrawal"){
                $id = 3;
            }

            $payBridgeDepositWithdrawalTransactions = $payBridgeTransactions->where('transaction_type_id',$id);
            return $this->payBridge($payBridgeDepositWithdrawalTransactions, $id);
        }
    }

    public function sortByAccountants($startDate, $endDate, User $user){
        //** getting the Y-m-d format for start and end date */
        if($startDate){
            
            $start = explode("T",$startDate);
            $startDate = $start[0];

        } else {
            $startDate = date('Y')."-".$this->month."-".$this->day;
            //? passing an array of both date and time string
            $start = [$startDate,"00:00"];
        }
        if($endDate){

            $end = explode("T",$endDate);
            $endDate = $end[0];

        } else {
            $endDate = $startDate;
            $end = [$endDate,"23:59"];
        }

        //** Getting Segment Data */
        $this->segment = Carbon::parse($startDate)->format('d F Y');
        $this->segment .= " to ".Carbon::parse($endDate)->format('d F Y');

        if($startDate == $endDate){
            $this->segment = Carbon::parse($startDate)->format('d F Y');
        }


        if(in_array($user->role,[777])){
            return $this->juniorAccountantSort($start, $end, $user);
        }

        //** checking Accountant timestamp */
        $accountantTimestamp = AccountantTimeStamp::whereDate('activeTime','>=',$startDate)
        ->whereDate('activeTime','<=',$endDate)
        ->where('user_id',$user->id)
        ->get();

        if($this->category == 'all'){
            $cryptoGiftCard =  $this->sortingByTimestampRange($accountantTimestamp);
            $cryptoGiftCard['CryptoTransactions'] = $cryptoGiftCard['CryptoTransactions']->sortByDesc('updated_at');
            $cryptoGiftCard['giftCardTransactions'] = $cryptoGiftCard['giftCardTransactions']->where('status', 'success')->sortByDesc('updated_at');

            return $this->cryptoGiftCardTransactions($cryptoGiftCard['CryptoTransactions'], $cryptoGiftCard['giftCardTransactions']);
        }

        if($this->category == 'utilities'){
            $utilityTransactions =  $this->sortingByTimestampRange($accountantTimestamp);
            $utilityTransactions = $utilityTransactions->sortByDesc('updated_at');
            return $this->utilityTransactions($utilityTransactions);
        }

        if($this->category == "paybridge" OR $this->category == "paybridgewithdrawal"){
            $payBridgeTransactions =  $this->sortingByTimestampRange($accountantTimestamp);
            $payBridgeTransactions['payBridgeDepositWithdrawalTransactions'] = $payBridgeTransactions['payBridgeDepositWithdrawalTransactions']->sortByDesc('updated_at');
            

            return $this->payBridge($payBridgeTransactions['payBridgeDepositWithdrawalTransactions'], $payBridgeTransactions['id']);
        }
        
    }

    public function juniorAccountantSort($start, $end, $user){
        //** Getting the last 5 active timestamp of the junior accountant */
        $accountantTimestamp = AccountantTimeStamp::whereDate('activeTime','<=',$end[0])
        ->where('user_id',$user->id)
        ->limit(5)
        ->get();

        $start = Carbon::parse($start[0]." ".$start[1].":00");
        $end = Carbon::parse($end[0]." ".$end[1].":59");

        if($this->category == 'all'){
            $cryptoGiftCard =  $this->sortingByTimestampRange($accountantTimestamp);
            $cryptoGiftCard['CryptoTransactions'] = $cryptoGiftCard['CryptoTransactions']->whereBetween('updated_at', [$start, $end]);
            $cryptoGiftCard['giftCardTransactions'] = $cryptoGiftCard['giftCardTransactions']->whereBetween('updated_at', [$start, $end]);

            $cryptoGiftCard['CryptoTransactions'] = $cryptoGiftCard['CryptoTransactions']->sortByDesc('updated_at');
            $cryptoGiftCard['giftCardTransactions'] = $cryptoGiftCard['giftCardTransactions']->where('status', 'success')->sortByDesc('updated_at');

            return $this->cryptoGiftCardTransactions($cryptoGiftCard['CryptoTransactions'], $cryptoGiftCard['giftCardTransactions']);
        }

        if($this->category == 'utilities'){
            $utilityTransactions =  $this->sortingByTimestampRange($accountantTimestamp);
            $utilityTransactions = $utilityTransactions->whereBetween('updated_at', [$start, $end]);
            $utilityTransactions = $utilityTransactions->sortByDesc('updated_at');
            return $this->utilityTransactions($utilityTransactions);
        }

        if($this->category == "paybridge" OR $this->category == "paybridgewithdrawal"){
            $payBridgeTransactions =  $this->sortingByTimestampRange($accountantTimestamp);
            $payBridgeTransactions['payBridgeDepositWithdrawalTransactions'] = $payBridgeTransactions['payBridgeDepositWithdrawalTransactions']->whereBetween('updated_at', [$start, $end]);

            $payBridgeTransactions['payBridgeDepositWithdrawalTransactions'] = $payBridgeTransactions['payBridgeDepositWithdrawalTransactions']->sortByDesc('updated_at');
            
            return $this->payBridge($payBridgeTransactions['payBridgeDepositWithdrawalTransactions'], $payBridgeTransactions['id']);
        }
    }

    public function sortingByTimestampRange($accountantTimestamp)
    {
        if($this->category == 'all'){
            $CryptoTransactions = collect();
            $giftCardTransactions = collect();

            $cryptoGiftCard = Transaction::with('user','agent','accountant');
            foreach($accountantTimestamp as $at){
                $cryptoTranx = $this->sortingByAccountantTimestamp($cryptoGiftCard, $at->activeTime, $at->inactiveTime);
                $CryptoTransactions = $CryptoTransactions->concat($cryptoTranx);

                $giftCard = $cryptoGiftCard->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                });
                $giftCardTranx = $this->sortingByAccountantTimestamp($giftCard, $at->activeTime, $at->inactiveTime);
                $giftCardTransactions = $giftCardTransactions->concat($giftCardTranx);

            }
            return [
                'CryptoTransactions' => $CryptoTransactions,
                'giftCardTransactions' => $giftCardTransactions
            ];
        }

        if($this->category == 'utilities'){
            $utilityTransactions = collect();
            $utilities = UtilityTransaction::with('user');
            foreach($accountantTimestamp as $at){
                $utilityTranx = $this->sortingByAccountantTimestamp($utilities, $at->activeTime, $at->inactiveTime);
                $utilityTransactions = $utilityTransactions->concat($utilityTranx);
            }

            return $utilityTransactions;
        }

        if($this->category == "paybridge" OR $this->category == "paybridgewithdrawal"){
            $payBridgeTransactions = collect();
            $payBridge = NairaTransaction::with('transactionType','user');
            foreach($accountantTimestamp as $at){
                $payBridgeTranx = $this->sortingByAccountantTimestamp($payBridge, $at->activeTime, $at->inactiveTime);
                $payBridgeTransactions = $payBridgeTransactions->concat($payBridgeTranx);
            }
    
            if ($this->category == "paybridge"){
                $id = 1;
            } elseif ($this->category == "paybridgewithdrawal"){
                $id = 3;
            }

            $payBridgeDepositWithdrawalTransactions = $payBridgeTransactions->where('transaction_type_id',$id);
            return [
                'payBridgeDepositWithdrawalTransactions' => $payBridgeDepositWithdrawalTransactions,
                'id' => $id,
            ];
        }
    }

    public function sortingByAccountantTimestamp($collectionData, $activeTime, $inactiveTime){
        //** when an accountant is activated the inactive time is null meaning user is still active */
        if($inactiveTime == NULL){
            $inactiveTime = now();
        }

        //** sorting by the timestamp*/
        $collectionData = $collectionData
        ->where('updated_at','>=',$activeTime)
        ->where('updated_at', '<=', $inactiveTime)
        ->get();

        return $collectionData;
    }

    public static function percentageRevenueGrowth($sortType = NULL){
        if($sortType == NULL){
            $sortType = 'monthly';
        }
        $currentStartDate = NULL;
        $currentEndDate = NULL;

        $previousStartDate = NULL;
        $previousEndDate = NULL;

        switch ($sortType) {
            case 'weekly':
                $currentStartDate = now()->startOfWeek();
                $currentEndDate = now()->endOfWeek();

                $previousStartDate = now()->subWeek()->startOfWeek();
                $previousEndDate = now()->subWeek()->endOfWeek();
                break;

            case 'monthly':
                $currentStartDate = now()->startOfMonth();
                $currentEndDate = now()->endOfMonth();

                $previousStartDate = now()->subMonth()->startOfMonth();
                $previousEndDate = now()->subMonth()->endOfMonth();
                break;

            case 'yearly':
                $currentStartDate = now()->startOfYear();
                $currentEndDate = now()->endOfYear();

                $previousStartDate = now()->subYear()->startOfYear();
                $previousEndDate = now()->subYear()->endOfYear();
                break;

            case 'quarterly':
                $previousStartDate = now()->subMonth(5)->startOfMonth();
                $previousEndDate = now()->subMonth(3)->endOfMonth();

                $currentStartDate = now()->subMonth(2)->startOfMonth();
                $currentEndDate = now()->subMonth(0)->endOfMonth();
                break;
                
            
            default:
                $currentStartDate = now()->startOfMonth();
                $currentEndDate = now()->endOfMonth();

                $previousStartDate = now()->subMonth()->startOfMonth();
                $previousEndDate = now()->subMonth()->endOfMonth();
                break;
        }

        $currentTimeFrame = Transaction::with('asset')
        ->where('created_at','>=', $currentStartDate)
        ->where('created_at','<=', $currentEndDate)
        ->where('status','success')
        ->get();

        $previousTimeFrame = Transaction::with('asset')
        ->where('created_at','>=', $previousStartDate)
        ->where('created_at','<=', $previousEndDate)
        ->where('status','success')
        ->get();

        $previousTimeFrameAmount = 0;
        $currentTimeFrameAmount = 0;

        foreach($currentTimeFrame as $transactions){
            if($transactions->asset->is_crypto = 0){
                $currentTimeFrameAmount += $transactions->amount * $transactions->quantity;
            }else{
                $currentTimeFrameAmount += $transactions->amount;
            }
        }

        foreach($previousTimeFrame as $transactions){
            if($transactions->asset->is_crypto = 0){
                $previousTimeFrameAmount += $transactions->amount * $transactions->quantity;
            }else{
                $previousTimeFrameAmount += $transactions->amount;
            }
        }

        $percentageRevenueGrowth = ($previousTimeFrameAmount == 0) ? 0 :
         (($currentTimeFrameAmount - $previousTimeFrameAmount)/$previousTimeFrameAmount) * 100;

        return response()->json([
            'success' => 'success',
            'revenueGrowth' => round($percentageRevenueGrowth,2),
            'duration' => ucwords($sortType)
        ],200);
    }
    

    public static function averageRevenuePerUniqueUser($sortType = NULL){
        $currentStartDate = NULL;
        $currentEndDate = NULL;

        if($sortType == NULL){
            $sortType = 'monthly';
        }

        switch ($sortType) {
            case 'weekly':
                $currentStartDate = now()->startOfWeek();
                $currentEndDate = now()->endOfWeek();
                break;

            case 'monthly':
                $currentStartDate = now()->startOfMonth();
                $currentEndDate = now()->endOfMonth();
                break;

            case 'yearly':
                $currentStartDate = now()->startOfYear();
                $currentEndDate = now()->endOfYear();
                break;

            case 'quarterly':
                $currentStartDate = now()->subMonth(2)->startOfMonth();
                $currentEndDate = now()->subMonth(0)->endOfMonth();
                break;
                
            
            default:
                $currentStartDate = now()->startOfMonth();
                $currentEndDate = now()->endOfMonth();
                break;
        }

        $currentTimeFrame = Transaction::with('asset')
        ->where('created_at','>=', $currentStartDate)
        ->where('created_at','<=', $currentEndDate)
        ->where('status','success')
        ->get();

        $currentTimeFrameAmount = 0;

        foreach($currentTimeFrame as $transactions){
            if($transactions->asset->is_crypto = 0){
                $currentTimeFrameAmount += $transactions->amount * $transactions->quantity;
            }else{
                $currentTimeFrameAmount += $transactions->amount;
            }
        }

        $uniqueUsers = $currentTimeFrame->groupBy('user_id');
        $uniqueUsersCount = $uniqueUsers->count();

        $averageRevenuePerUser = ($uniqueUsersCount == 0) ? 0 : $currentTimeFrameAmount/$uniqueUsersCount;

        return response()->json([
            'success' => 'success',
            'averageRevenuePerUser' => number_format($averageRevenuePerUser),
            'duration' => ucwords($sortType)
        ],200);

    }

    public static function averageRevenuePerTransaction($sortType = NULL){
        $currentStartDate = NULL;
        $currentEndDate = NULL;

        if($sortType == NULL){
            $sortType = 'monthly';
        }

        switch ($sortType) {
            case 'weekly':
                $currentStartDate = now()->startOfWeek();
                $currentEndDate = now()->endOfWeek();
                break;

            case 'monthly':
                $currentStartDate = now()->startOfMonth();
                $currentEndDate = now()->endOfMonth();
                break;

            case 'yearly':
                $currentStartDate = now()->startOfYear();
                $currentEndDate = now()->endOfYear();
                break;

            case 'quarterly':
                $currentStartDate = now()->subMonth(2)->startOfMonth();
                $currentEndDate = now()->subMonth(0)->endOfMonth();
                break;
                
            
            default:
                $currentStartDate = now()->startOfMonth();
                $currentEndDate = now()->endOfMonth();
                break;
        }

        $currentTimeFrame = Transaction::with('asset')
        ->where('created_at','>=', $currentStartDate)
        ->where('created_at','<=', $currentEndDate)
        ->where('status','success')
        ->get();

        $currentTimeFrameAmount = 0;

        foreach($currentTimeFrame as $transactions){
            if($transactions->asset->is_crypto = 0){
                $currentTimeFrameAmount += $transactions->amount * $transactions->quantity;
            }else{
                $currentTimeFrameAmount += $transactions->amount;
            }
        }

        $transactionCount = $currentTimeFrame->count();
        $averageRevenuePerTransaction = ($transactionCount == 0) ? 0 : $currentTimeFrameAmount/$transactionCount;

        return response()->json([
            'success' => 'success',
            'averageRevenuePerTransaction' => number_format($averageRevenuePerTransaction),
            'duration' => ucwords($sortType)
        ],200);
    }

}
