<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiV2\Admin\VolumeOfUsers;
use App\Http\Resources\ApiV2\Admin\WalletUserData;
use App\NairaTrade;
use App\NairaTransaction;
use App\NairaWallet;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AnalyticsController extends Controller
{
    private $usd_value;

    public function __construct()
    {
        $this->usd_value = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
    }
    
    public function Analytics()
    {

        $current_month = now()->month;
        $current_year = now()->year;

        $previous_month = now()->subMonth()->month;
        $previous_year = now()->subMonth()->year;

        $previous_start = Carbon::createFromDate($previous_year,$previous_month,1);
        $previous_end = Carbon::createFromDate($previous_year,$previous_month,1)->endOfMonth();

        $current_start = Carbon::createFromDate($current_year,$current_month,1);
        $current_end = Carbon::createFromDate($current_year,$current_month,1)->endOfMonth();


        return $this->loadData($previous_start ,$previous_end, $current_start, $current_end,$current_month,$current_year);
    }

    public function sortAnalytics(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'month' => 'required|integer',
            'year' => 'required|integer',
            'timeFrame' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }
        $month = $request->month;
        $year = $request->year;
        $timeFrame = $request->timeFrame;

        $timeFrame = ($timeFrame == null) ? 'Monthly' : $timeFrame;

        if(!in_array($timeFrame,['Monthly' ,'Quarterly','Yearly']))
        {
            return response()->json([
                'success' => false,
                'message' => 'time frame input data is wrong',
            ],401);
        }

        $timeData = $this->timeFrame($timeFrame, $month, $year);
        $currentStart = $timeData['start'];
        $currentEnd = $timeData['end'];

        $previousStart = $timeData['previousStart'];
        $previousEnd = $timeData['previousEnd'];

        return $this->loadData($previousStart ,$previousEnd, $currentStart, $currentEnd,$month,$year);

    }

    public function timeFrame($timeFrame, $month, $year)
    {  
        $startDate = null;
        $endDate = null;


        $previousStartDate = null;
        $previousEndDate = null;

        switch ($timeFrame) {
            case 'Monthly':
                $endDate = Carbon::createFromDate($year,$month,1)->endOfMonth();
                $startDate = Carbon::createFromDate($year,$month,1)->startOfMonth();

                $previousStartDate = Carbon::createFromDate($year,$month,1)->subMonth()->startOfMonth();
                $previousEndDate = Carbon::createFromDate($year,$month,1)->subMonth()->endOfMonth();
                break;
            case 'Quarterly':
                $endDate = Carbon::createFromDate($year,$month,1)->subMonths(3)->endOfMonth();
                $startDate = Carbon::createFromDate($year,$month,1)->startOfMonth();

                $previousStartDate = Carbon::createFromDate($year,$month,1)->subMonths(6)->startOfMonth();
                $previousEndDate = Carbon::createFromDate($year,$month,1)->subMonths(4)->endOfMonth();
                break;
            case 'Yearly':
                $endDate = Carbon::createFromDate($year,$month,1)->endOfYear();
                $startDate = Carbon::createFromDate($year,$month,1)->startOfYear();

                $previousStartDate = Carbon::createFromDate($year,$month,1)->subYear()->startOfYear();
                $previousEndDate = Carbon::createFromDate($year,$month,1)->subYear()->endOfYear();
                break;
            
            default:
                $endDate = Carbon::createFromDate($year,$month,1)->endOfMonth();
                $startDate = Carbon::createFromDate($year,$month,1)->startOfMonth();

                $previousStartDate = Carbon::createFromDate($year,$month,1)->subMonth()->startOfMonth();
                $previousEndDate = Carbon::createFromDate($year,$month,1)->subMonth()->endOfMonth();
                break;
        }

        return array(
            'start' => $startDate,
            'end' => $endDate,
            'previousStart' => $previousStartDate,
            'previousEnd' => $previousEndDate
        );
    }

    public function CryptoTrade($cm_cryptoTranx, $pm_cryptoTranx, $tradeType)
    {
        $cm_cryptoTranxNo = $cm_cryptoTranx->count();
        $pm_cryptoTranxNo = $pm_cryptoTranx->count();

        $perDiffCryptoTranxNo = ($pm_cryptoTranxNo != 0) ? (( $cm_cryptoTranxNo / $pm_cryptoTranxNo ) * 100) : 0;

        //NGN
        $cm_cryptoTranxVolumeNGN = $cm_cryptoTranx->sum('amount_paid');
        $pm_cryptoTranxVolumeNGN = $pm_cryptoTranx->sum('amount_paid');

        $perDiffCryptoTranxVolumeNGN = ($pm_cryptoTranxNo != 0) ?  (( $cm_cryptoTranxVolumeNGN / $pm_cryptoTranxVolumeNGN ) * 100) : 0;

        //USD
        // $cm_cryptoTranxVolumeUSD = $cm_cryptoTranx->sum('amount');
        // $pm_cryptoTranxVolumeUSD = $pm_cryptoTranx->sum('amount');

        // $perDiffCryptoTranxVolumeUSD = ( $cm_cryptoTranxVolumeUSD / $pm_cryptoTranxVolumeUSD ) * 100;

        //BTC Buy
        $BtcNo = $cm_cryptoTranx->where('card_id', 102)->count();
        //USDT Buy
        $UsdtNo = $cm_cryptoTranx->where('card_id', 143)->count();

        $BtcTradedPercentage = ($cm_cryptoTranxNo != 0) ? (( $BtcNo /  $cm_cryptoTranxNo ) * 100) : 0;
        $UsdTradedPercentage = ($cm_cryptoTranxNo != 0) ? (( $UsdtNo /  $cm_cryptoTranxNo ) * 100) : 0;

        $crypto = collect([
            'TransactionType' => 'Crypto Asset',
            'type' => $tradeType,
            'Number' => $cm_cryptoTranxNo,
            'NumberMonthlyGrowthRate' => $perDiffCryptoTranxNo,
            'products' =>['BTC','USDT'],
            'perTraded' =>[$BtcTradedPercentage, $UsdTradedPercentage],
            'volume' => $cm_cryptoTranxVolumeNGN,
            'monthGrowthRate' => $perDiffCryptoTranxVolumeNGN
        ]);

        return $crypto;
    }

    public function UtilityData($currentUtility, $previousUtility, $transaction_type)
    {
        
        $cm_UtilityNo = $currentUtility->count();
        $pm_UtilityNo = $previousUtility->count();

        $perDiffUtilityTranxNo = ($pm_UtilityNo != 0) ? (( $cm_UtilityNo / $pm_UtilityNo ) * 100) : 0;

        $cm_UtilityVolumeNGN = $currentUtility->sum('amount'); 
        $pm_UtilityVolumeNGN = $previousUtility->sum('amount');

        $perDiffUtilityTranxVolumeNGN = ($pm_UtilityVolumeNGN != 0) ? (( $cm_UtilityVolumeNGN / $pm_UtilityVolumeNGN ) * 100) : 0;
        
        // $cm_UtilityVolumeUSD = ( $cm_UtilityVolumeNGN / $usd_value ); 
        // $pm_UtilityVolumeUSD = ( $pm_UtilityVolumeNGN / $usd_value );

        // $perDiffUtilityTranxVolumeUSD = ( $cm_UtilityVolumeUSD / $pm_UtilityVolumeUSD ) * 100;

        $utility = collect([
            'TransactionType' => $transaction_type,
            'Number' => $cm_UtilityNo,
            'NumberMonthlyGrowthRate' => $perDiffUtilityTranxNo,
            'products' =>"All Networks",
            'perTraded' => ($cm_UtilityNo == 0)?0 :100,
            'volume' => $cm_UtilityVolumeNGN,
            'monthGrowthRate' => $perDiffUtilityTranxVolumeNGN
        ]);

        return $utility;
    }

    public function NairaBalance($order)
    {
        $wallets = NairaWallet::with('user')->orderBy('amount',$order)->limit(1000)->get();
        $exportData = array(
            'count' => $wallets->count(),
            'data' => WalletUserData::collection($wallets),
        );
        return $exportData;
    }

    public function averageTranxNo($current_month ,$current_year, $type)
    {
        $start = Carbon::createFromDate($current_year,$current_month,1);
        if(now()->month == $current_month AND now()->year == $current_year){
            $end = now();
        }else{
            $end = Carbon::createFromDate($current_year,$current_month,1)->endOfMonth();
        }

        $daysDiff = $start->diffInDays($end);

        $cryptoTranx = Transaction::where('status','success')->where('created_at','>=',$start)->where('created_at','<=',$end)->get();
        $utilityTranx = UtilityTransaction::where('status','success')->where('created_at','>=', $start)->where('created_at','<=',$end)->get();

        $allTranx = collect()->concat($cryptoTranx)->concat($utilityTranx);
        $allTranxCount = $allTranx->count();

        $averageTranxNo = $allTranxCount / $daysDiff;

        $averageTranxData = $this->averageTranxData($current_month ,$current_year);

        $exportData = array(
            'averageTranxNo' => $averageTranxNo,
            'data' => $averageTranxData,
        );

        return $exportData;
    }

    public function averageTranxData($current_month ,$current_year)
    {

        $end = Carbon::createFromDate($current_year,$current_month,1)->endOfMonth();
        $start = Carbon::createFromDate($current_year,$current_month,1)->endOfMonth()->subDays(200);

        $cryptoTranx = Transaction::where('status','success')->where('created_at','>=',$start)->where('created_at','<=',$end)->get();
        $utilityTranx = UtilityTransaction::where('status','success')->where('created_at','>=', $start)->where('created_at','<=',$end)->get();
        $allTranx = collect()->concat($cryptoTranx)->concat($utilityTranx)->sortByDesc('updated_at');
    
        foreach($cryptoTranx as $ct)
        {
            $ct->amountUSD = $ct->amount;
            $ct->amountNGN = $ct->amount_paid;
        }

        foreach($utilityTranx as $ut)
        {
            $ut->amountUSD = $ut->amount / $this->usd_value;
            $ut->amountNGN = $ut->amount;
        }

        $allTranx = $allTranx->groupBy(function($date) {
            return Carbon::parse($date->updated_at)->format("Y-m-d");
        });

        $dataCollection = collect([]);
        foreach($allTranx as $at => $collection)
        {
            $data = array(
                    'date' => $at,
                    'total_transactions' => $collection->count(),
                    'uniqueUsers' => $collection->groupBy('user_id')->count(),
                    'amount' => $collection->sum('amountNGN'),
                );
            $dataCollection[] = $data;
        }
        return $dataCollection;
    }

    public function HighestVolumeOfUsers($start, $end)
    {
        $transactions = Transaction::with('user')->where('status', 'success')
        ->where('created_at','>=',$start)->where('created_at','<=',$end)->get();

        $userVolume = $transactions->groupBy('user_id')->count();
        $transactionData = array();
        foreach($transactions->groupBy('user_id') as $t )
        {
            $user = $t->first()->user;
            $volume = $t->sum('amount_paid');

            $name = $user->first_name." ".$user->last_name;
            $username = $user->username;
            
            $email = $user->email;
            $signupDate = $user->created_at->format('d-m-Y H:i:s');

            $transactionData[] = array(
                'id' => $user->id,
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'volumeOfTransactions' => $volume,
                'SignUpDate' => $signupDate,
            );
        }

        $exportData = array(
            'value' => $userVolume,
            'data' => $transactionData,
        );

        return $exportData;
    }

    public function mostFrequentUsers($start, $end)
    {
        $transactionData = array();
        $transactions = Transaction::with('user')->where('status', 'success')
        ->where('created_at','>=',$start)->where('created_at','<=',$end)->get();

        $utilityTranx = UtilityTransaction::with('user')->where('status', 'success')
        ->where('created_at','>=',$start)->where('created_at','<=',$end)->get();

        $tranx = collect()->concat($transactions)->concat($utilityTranx);
        $userGroupData = $tranx->groupBy('user_id');

        foreach($userGroupData as $ugd)
        {
            $tranxNo = $ugd->count();
            if($tranxNo > 0)
            {
                $user = $ugd->first()->user;
                $name = $user->first_name." ".$user->last_name;
                $username = $user->username;
                
                $email = $user->email;
                $signupDate = $user->created_at->format('d-m-Y H:i:s');

                $transactionData[] = array(
                    'id' => $user->id,
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'frequency' => $tranxNo,
                    'SignUpDate' => $signupDate,
                );
            }
        }
        $tranxData = collect($transactionData);
        $exportData = array(
            'value' => $tranxData->count(),
            'data' => $tranxData,
        );

        return $exportData;
    }

    public function withdrawalCharges($start, $end)
    {
        $nairaTransaction = NairaTransaction::where('type','withdrawal')->where('status','success')
        ->where('created_at','>=',$start)->where('created_at','<=',$end)->get();

        $tranxCount = $nairaTransaction->count();
        $tranxAmount = $nairaTransaction->sum('charge');

        $exportData = array(
            'count' => $tranxCount,
            'amount' => $tranxAmount,
        );

        return $exportData;
    }

    public function manualCredit($start, $end)
    {
        $nairaTransaction = NairaTransaction::where('dr_user_id',1)->where('is_manual',1)
        ->where('created_at','>=',$start)->where('created_at','<=',$end)->get();

        $tranxCount = $nairaTransaction->count();
        $tranxAmount = $nairaTransaction->sum('amount');

        $exportData = array(
            'count' => $tranxCount,
            'amount' => $tranxAmount,
        );

        return $exportData;
    }

    public function manualDebit($start, $end)
    {
        $nairaTransaction = NairaTransaction::where('cr_user_id',1)->where('is_manual',1)
        ->where('created_at','>=',$start)->where('created_at','<=',$end)->get();

        $tranxCount = $nairaTransaction->count();
        $tranxAmount = $nairaTransaction->sum('amount');

        $exportData = array(
            'count' => $tranxCount,
            'amount' => $tranxAmount,
        );

        return $exportData;
    }

    public function loadData($previous_start ,$previous_end, $current_start, $current_end,$current_month,$current_year)
    {
        $usd_value = $this->usd_value;

        $currentMonthTranx = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->where('status', 'success')->where('created_at','>=',$current_start)->where('created_at','<=',$current_end)->get();

        $previousMonthTranx = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->where('status', 'success')->where('created_at','>=',$previous_start)->where('created_at','<=',$previous_end)->get();

        //Crypto Buy
        $cm_cryptoBuyTranx = $currentMonthTranx->where('type','buy');
        $pm_cryptoBuyTranx = $previousMonthTranx->where('type','buy');
        $cryptoBuy = $this->CryptoTrade($cm_cryptoBuyTranx, $pm_cryptoBuyTranx,'buy');

        //Crypto Sell
        $cm_cryptoSellTranx = $currentMonthTranx->where('type','sell');
        $pm_cryptoSellTranx = $previousMonthTranx->where('type','sell');
        $cryptoSell = $this->CryptoTrade($cm_cryptoSellTranx, $pm_cryptoSellTranx,'sell');

        // Utility Transactions
        $currentMonthUtility = UtilityTransaction::where('status', 'success')
        ->where('created_at','>=',$current_start)->where('created_at','<=',$current_end)->get();

        $previousMonthUtility = UtilityTransaction::where('status', 'success')
        ->where('created_at','>=',$previous_start)->where('created_at','<=',$previous_end)->get();

        //Airtime Utility
        $currentMonthAirtimeUtility = $currentMonthUtility->where('type','Recharge card purchase');
        $previousMonthAirtimeUtility = $previousMonthUtility->where('type','Recharge card purchase');

        $airtimeTranx = $this->UtilityData($currentMonthAirtimeUtility, $previousMonthAirtimeUtility, "Airtime Transaction");
        
        //Data Transactions
        $currentMonthDataUtility = $currentMonthUtility->where('type','Data purchase');
        $previousMonthDataUtility = $previousMonthUtility->where('type','Data purchase');

        $dataTranx = $this->UtilityData($currentMonthDataUtility, $previousMonthDataUtility, "Data Transaction");

        //Power Transactions
        $currentMonthPowerUtility = $currentMonthUtility->where('type','Electricity purchase');
        $previousMonthPowerUtility = $previousMonthUtility->where('type','Electricity purchase');

        $powerTranx = $this->UtilityData($currentMonthPowerUtility, $previousMonthPowerUtility, "Data Transaction");

        //Cable Transactions
        $currentMonthCableUtility = $currentMonthUtility->where('type','Cable subscription');
        $previousMonthCableUtility = $previousMonthUtility->where('type','Cable subscription');

        $cableTranx  = $this->UtilityData($currentMonthCableUtility, $previousMonthCableUtility, "Cable Transaction");

        $analytics = array(
            $cryptoBuy,$cryptoSell,$airtimeTranx,$dataTranx,$powerTranx,$cableTranx
        );

        //High Naira Balance 
        $highestNairaBalance = $this->NairaBalance('DESC');
        $highestNairaBalanceCount = $highestNairaBalance['count'];
        $highestNairaBalanceData = $highestNairaBalance['data'];
        
        //Lowest Naira Balance 
        $lowestNairaBalance = $this->NairaBalance('ASC');
        $lowestNairaBalanceCount = $lowestNairaBalance['count'];
        $lowestNairaBalanceData = $lowestNairaBalance['data'];

        //Average Number of Transactions
        $averageNumberTranx = $this->averageTranxNo($current_month ,$current_year, null);
        $averageTranxNo = $averageNumberTranx['averageTranxNo'];
        $averageTranxData = $averageNumberTranx['data'];

        //Highest Volume of Users
        $highestVolumeOfUsers = $this->HighestVolumeOfUsers($current_start ,$current_end);
        $highestVolumeOfUsersNo = $highestVolumeOfUsers['value'];
        $highestVolumeOfUsersData = $highestVolumeOfUsers['data'];

        //Most Frequent
        $mostFrequentUsers = $this->mostFrequentUsers($current_start ,$current_end);
        $mostFrequentUsersNo = $mostFrequentUsers['value'];
        $mostFrequentUsersData = $mostFrequentUsers['data'];

        //Withdrawal Charges
        $withdrawalCharges = $this->withdrawalCharges($current_start ,$current_end);
        $withdrawalChargesNo = $withdrawalCharges['count'];
        $withdrawalChargesAmount = $withdrawalCharges['amount'];

        //manual Credit
        $manualCredit = $this->manualCredit($current_start ,$current_end);
        $manualCreditNo = $manualCredit['count'];
        $manualCreditAmount = $manualCredit['amount'];

        //manual Debit
        $manualDebit = $this->manualDebit($current_start ,$current_end);
        $manualDebitNo = $manualDebit['count'];
        $manualDebitAmount = $manualDebit['amount'];

        return response()->json([
            'success' => true,
            'analytics' => $analytics,
            'highestNairaBalanceCount' => $highestNairaBalanceCount,
            'highestNairaBalanceData' => $highestNairaBalanceData,
            'lowestNairaBalanceCount' => $lowestNairaBalanceCount,
            'lowestNairaBalanceData' => $lowestNairaBalanceData,
            'averageTranxNo' => $averageTranxNo,
            'averageTranxData' => $averageTranxData,
            'highestVolumeOfUsersNo' => $highestVolumeOfUsersNo,
            'highestVolumeOfUsersData' => $highestVolumeOfUsersData,
            'mostFrequentUsersNo' => $mostFrequentUsersNo,
            'mostFrequentUsersData' => $mostFrequentUsersData,
            'withdrawalChargesNo' => $withdrawalChargesNo,
            'withdrawalChargesAmount' => $withdrawalChargesAmount,
            'manualCreditNo' => $manualCreditNo,
            'manualCreditAmount' => $manualCreditAmount,
            'manualDebitNo' => $manualDebitNo,
            'manualDebitAmount' => $manualDebitAmount,
        ],200);

    }

}