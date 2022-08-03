<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Card;
use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;

class NexusController extends Controller
{
    public function verificationData(Request $request)
    {
        if($request->date == null){
            $date = now();
        }else{
            $date = Carbon::parse($request->date)->addHour();
        }
        
        //*using the usd value to change the naira value to dollars
        $usd_value = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        //*total volume of transaction for the month
        $total_volume_Crypto = Transaction::where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get()->sum('amount');
        $total_volume_utilities = UtilityTransaction::where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get()->sum('amount');
        $total_volume_utilities = $total_volume_utilities/$usd_value;
        $total_volume = $total_volume_Crypto + $total_volume_utilities;

         //* Level 1 Verification
         $L1_Crypto = Transaction::whereHas('user', function ($query){
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', null)->where('idcard_verified_at', null);
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $L1_Utilities = UtilityTransaction::whereHas('user', function ($query){
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', null)->where('idcard_verified_at', null);
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

        //? monthly trading value 
        $L1_Crypto_trading_value = $L1_Crypto->groupBy('user_id')->count();
        $L1_Utilities_trading_value = $L1_Utilities->groupBy('user_id')->count();
        $L1_trading_value = ($L1_Crypto_trading_value + $L1_Utilities_trading_value);

        //? monthly trading frequency
        $L1_trading_frequency_crypto = $L1_Crypto->count();
        $L1_trading_frequency_utilities = $L1_Utilities->count();
        $L1_trading_frequency = ($L1_trading_frequency_crypto + $L1_trading_frequency_utilities);

        //?monthly trading volume
        $L1_trading_volume_crypto = $L1_Crypto->sum('amount');
        $L1_trading_volume_utilities = $L1_Utilities->sum('amount')/$usd_value;
        $L1_trading_volume = ($L1_trading_volume_crypto + $L1_trading_volume_utilities);

        //?Percentage of monthly trades 
        $L1_percentage_monthly_trades =($total_volume == 0) ? 0 : ($L1_trading_volume/$total_volume)*100;
        //* end Level 1 Verification 


         //* Level 2 Verification
         $L2_Crypto = Transaction::whereHas('user', function ($query){
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', null);
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $L2_Utilities = UtilityTransaction::whereHas('user', function ($query){
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', null);
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

        //? monthly trading value 
        $L2_Crypto_trading_value = $L2_Crypto->groupBy('user_id')->count();
        $L2_Utilities_trading_value = $L2_Utilities->groupBy('user_id')->count();
        $L2_trading_value = ($L2_Crypto_trading_value + $L2_Utilities_trading_value);

        //? monthly trading frequency
        $L2_trading_frequency_crypto = $L2_Crypto->count();
        $L2_trading_frequency_utilities = $L2_Utilities->count();
        $L2_trading_frequency = ($L2_trading_frequency_crypto + $L2_trading_frequency_utilities);

        //?monthly trading volume
        $L2_trading_volume_crypto = $L2_Crypto->sum('amount');
        $L2_trading_volume_utilities = $L2_Utilities->sum('amount')/$usd_value;
        $L2_trading_volume = ($L2_trading_volume_crypto + $L2_trading_volume_utilities);

        //?Percentage of monthly trades 
        $L2_percentage_monthly_trades = ($total_volume == 0) ? 0 : ($L2_trading_volume/$total_volume)*100;
        //* end Level 2 Verification



         //* Level 3 Verification
         $L3_Crypto = Transaction::whereHas('user', function ($query){
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', '!=', null);
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $L3_Utilities = UtilityTransaction::whereHas('user', function ($query){
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', '!=', null);
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

        //? monthly trading value 
        $L3_Crypto_trading_value = $L3_Crypto->groupBy('user_id')->count();
        $L3_Utilities_trading_value = $L3_Utilities->groupBy('user_id')->count();
        $L3_trading_value = ($L3_Crypto_trading_value + $L3_Utilities_trading_value);

        //? monthly trading frequency
        $L3_trading_frequency_crypto = $L3_Crypto->count();
        $L3_trading_frequency_utilities = $L3_Utilities->count();
        $L3_trading_frequency = ($L3_trading_frequency_crypto + $L3_trading_frequency_utilities);

        //?monthly trading volume
        $L3_trading_volume_crypto = $L3_Crypto->sum('amount');
        $L3_trading_volume_utilities = $L3_Utilities->sum('amount')/$usd_value;
        $L3_trading_volume = ($L3_trading_volume_crypto + $L3_trading_volume_utilities);

        //?Percentage of monthly trades 
        $L3_percentage_monthly_trades = ($total_volume == 0) ? 0 : ($L3_trading_volume/$total_volume)*100;
        //*End Level 3 Verification

         //?total turnover 
         $turnover_crypto = Transaction::where('status','success')->get()->sum('amount');

         $turnover_utilities = UtilityTransaction::where('status','success')->get()->sum('amount');
         $turnover_utilities = $turnover_utilities/$usd_value;

         $total_turnover = ($turnover_crypto + $turnover_utilities);

         //?monthly turnover 
         $monthly_turnover_crypto = Transaction::where('status','success')
         ->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get()->sum('amount');

         $monthly_turnover_utilities = UtilityTransaction::where('status','success')
         ->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get()->sum('amount');
         $monthly_turnover_utilities = $monthly_turnover_utilities/$usd_value;

         $monthly_turnover_total = ($monthly_turnover_crypto + $monthly_turnover_utilities);

         //?Old Users Monthly Turnover 
         $oldUsers_turnover_crypto = Transaction::whereHas('user', function ($query){
            $query->where('created_at','<',now()->subMonth(3));
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $oldUsers_turnover_utilities = UtilityTransaction::whereHas('user', function ($query){
            $query->where('created_at','<',now()->subMonth(3));
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $oldUsers_turnover = ($oldUsers_turnover_crypto->sum('amount') + $oldUsers_turnover_utilities->sum('amount'));

         $oldUsers_totaltrades = ($oldUsers_turnover_crypto->count() + $oldUsers_turnover_utilities->count());
         
         
         $oldUsers_turnover_percentage = ($monthly_turnover_total == 0) ? 0 : ($oldUsers_turnover/$monthly_turnover_total)*100;

          //?New Users Monthly Turnover 
          $newUsers_turnover_crypto = Transaction::whereHas('user', function ($query){
            $query->where('created_at','>',now()->subMonth(3));
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $newUsers_turnover_utilities = UtilityTransaction::whereHas('user', function ($query){
            $query->where('created_at','>',now()->subMonth(3));
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $newUsers_turnover = ($newUsers_turnover_crypto->sum('amount') + $newUsers_turnover_utilities->sum('amount'));

         $newUsers_totaltrades = ($newUsers_turnover_crypto->count() + $newUsers_turnover_utilities->count());

         $newUsers_turnover_percentage = ($monthly_turnover_total == 0) ? 0 : ($newUsers_turnover/$monthly_turnover_total)*100;

         $assetBreakdown = $this->assetDetailedBreakdown($date, $usd_value);
         return response()->json([
            'success' => true,
            'monthly_L1_trading_value' =>  number_format($L1_trading_value),
            'monthly_L1_trading_Frequency' =>number_format($L1_trading_frequency),
            'monthly_L1_trading_volume' =>number_format($L1_trading_volume),
            'L1_percentage_monthly_trades' => number_format($L1_percentage_monthly_trades),

            'monthly_L2_trading_value' => number_format($L2_trading_value),
            'monthly_L2_trading_Frequency' =>number_format($L2_trading_frequency),
            'monthly_L2_trading_volume' =>number_format($L2_trading_volume),
            'L2_percentage_monthly_trades' => number_format($L2_percentage_monthly_trades),

            'monthly_L3_trading_value' => number_format($L3_trading_value),
            'monthly_L3_trading_Frequency' =>number_format($L3_trading_frequency),
            'monthly_L3_trading_volume' =>number_format($L3_trading_volume),
            'L3_percentage_monthly_trades' => number_format($L3_percentage_monthly_trades),

            'turnover' => number_format($total_turnover),
            'old_user_monthly_turnover' => number_format($oldUsers_turnover),
            'old_user_monthly_trades' => number_format($oldUsers_totaltrades),
            'old_user_monthly_turnover_percentage' => number_format($oldUsers_turnover_percentage),

            'new_user_monthly_turnover' => number_format($newUsers_turnover),
            'new_user_monthly_trades' => number_format($newUsers_totaltrades),
            'new_user_monthly_turnover_percentage' => number_format($newUsers_turnover_percentage),

            'assetBreakDown' => $assetBreakdown,
        ], 200);
    }

    public function timeGraph(Request $request)
    {
      $date = ($request->date == null) ? $date = now()->format('Y-m-d') : $request->date;
        
      $data_collection = collect([]);
        $crypto_transactions = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->where('status','success')->whereDate('created_at',$date)->get();

        $crypto = $crypto_transactions->where("created_at",">=",$date." 01:01:00")->where("created_at","<=",$date." 02:00:00");

        $giftCard_transaction = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where('status','success')->whereDate('created_at',$date)->get();

        $utilities_transaction = UtilityTransaction::where('status','success')->whereDate('created_at',$date)->get();

        $airtime_data = NairaTransaction::whereIn('transaction_type_id',[9,10])->where('status','success')->whereDate('created_at',$date)->get();

        //* do a loop on the collections
        for($i = 0; $i<=23; $i++){
            $previous_time = $i - 1;
            $current_time = $i;

            if($i>= 0 AND $i <=9)
            {
                $previous_time = ($i == 0) ? 23 : "0".($i - 1);
                $current_time = "0$i";
            }

            $crypto = $crypto_transactions->where("created_at",">=",$date." $previous_time:01:00")->where("created_at","<=",$date." $current_time:00:00")->count();
            $giftcard = $giftCard_transaction->where("created_at",">=",$date." $previous_time:01:00")->where("created_at","<=",$date." $current_time:00:00")->count();
            $utility = $utilities_transaction->where("created_at",">=",$date." $previous_time:01:00")->where("created_at","<=",$date." $current_time:00:00")->count();
            $airtime_data_value = $airtime_data->where("created_at",">=",$date." $previous_time:01:00")->where("created_at","<=",$date." $current_time:00:00")->count();

            $collection = collect([ [
                "crypto"=>$crypto,
                "giftCards"=>$giftcard,
                "utility"=>$utility,
                'airtime_data'=>$airtime_data_value,
                'date' => Carbon::parse($date." $current_time:00:00")->format('ha')
            ]]);
            $data_collection = $data_collection->concat($collection);

        }

        return response()->json([
         'success' => true,
         'data' => $data_collection
     ],200);
    }

    public function assetDetailedBreakdown($date,$usd_value)
    {
      $date =  Carbon::parse($date);
      
      //?Weekly
      $weekly_start_date = Carbon::parse($date)->startOfWeek();
      $weekly_end_date = Carbon::parse($date)->endOfWeek();

      $weekly_data = $this->assetBreakdown($weekly_start_date, $weekly_end_date, $usd_value);

      //?Monthly
      $monthly_start_date = Carbon::parse($date)->startOfMonth();
      $monthly_end_date = Carbon::parse($date)->endOfMonth();

      $monthly_data = $this->assetBreakdown($monthly_start_date, $monthly_end_date, $usd_value);

      //?Annually
      $annual_start_date = Carbon::parse($date)->startOfYear();
      $annual_end_date = Carbon::parse($date)->endOfYear();

      $annual_data = $this->assetBreakdown($annual_start_date , $annual_end_date, $usd_value);

      //?Quarterly 
      $quarterly_start_date = Carbon::parse($date)->subMonth(2)->startOfMonth();
      $quarterly_end_date = Carbon::parse($date)->endOfMonth();

      $quarterly_data = $this->assetBreakdown($quarterly_start_date , $quarterly_end_date, $usd_value);


      $export_data = array(
         'Weekly' => $weekly_data,
         'Monthly' => $monthly_data,
         'Annually' => $annual_data,
         'Quarterly' =>$quarterly_data,
      );

      return $export_data;
    }

    public function assetBreakdown($start_date,$end_date,$usd_value)
    {
      $total_crypto = Transaction::where('status','success')->sum('amount');
      $total_Util = (Transaction::where('status','success')->sum('amount'))/$usd_value;

      $total = $total_crypto + $total_Util;

      $assetBreakdown_Crypto = Transaction::where('status','success')->where('created_at','>=',$start_date)->where('created_at','<=',$end_date)->get();
      $assetBreakdown_Utilities = UtilityTransaction::where('status','success')->where('created_at','>=',$start_date)->where('created_at','<=',$end_date)->get();

      $total_volume_traded_Crypto = $assetBreakdown_Crypto->sum('amount');
      $no_of_user_Crypto = $assetBreakdown_Crypto->groupBy('user_id')->count();
      $no_of_traded_asset_Crypto = $assetBreakdown_Crypto->count();

      $total_volume_traded_Util = $assetBreakdown_Utilities->sum('amount')/$usd_value;
      $no_of_user_Util = $assetBreakdown_Utilities->groupBy('user_id')->count();
      $no_of_traded_asset_Util = $assetBreakdown_Utilities->count();

      $total_volume_traded = $total_volume_traded_Crypto + $total_volume_traded_Util;
      $no_of_user = $no_of_user_Crypto + $no_of_user_Util;
      $no_of_traded_asset = $no_of_traded_asset_Crypto + $no_of_traded_asset_Util;

      $average_revenue_per_user =($no_of_user == 0) ? 0 : ($total_volume_traded / $no_of_user);
      $percentage_volume_traded = ($total == 0) ? 0 :(( $total_volume_traded / $total ) * 100);

      $export_data = array(
         'TotalVolumeOfAssetTraded' => number_format($total_volume_traded),
         'AverageRevenuePerUser' => number_format($average_revenue_per_user),
         'TotalNumberOfTheAssetTraded' => number_format($no_of_traded_asset),
         'PercentageVolume' => round($percentage_volume_traded,7)
      );
      return $export_data;
    }

     public function NexusCrypto(Request $request)
     {
        $date = ($request->date == null) ? now()->addHour()->format('Y-m-d') : Carbon::parse($request->date)->addHour()->format('Y-m-d');
        $nexus_crypto = $this->NexusCards($date,1);

        return response()->json([
            'success' => true,
            'crypto' => $nexus_crypto,
        ], 200);
        
     }

     public function NexusGiftCard(Request $request)
     {
        $date = ($request->date == null) ? now()->addHour()->format('Y-m-d') : Carbon::parse($request->date)->addHour()->format('Y-m-d');
        $nexus_giftCard = $this->NexusCards($date,0);
        return response()->json([
         'data' => $nexus_giftCard,
        ]);
        return response()->json([
            'success' => true,
            'crypto' => $nexus_giftCard,
        ], 200);

     }

     public function NexusCards($date,$is_crypto)
     {
        $nexus = Card::where('is_crypto',$is_crypto)->get();
        $export_data = array();
        foreach ($nexus as $nc) {
             $total_transaction = Transaction::where('card_id',$nc->id)->get();
             $tranx = Transaction::where('card_id',$nc->id)->whereDate('created_at',$date)->get();

             $newData = array(
               'name' => $nc->name,
               'total_volume' => number_format($tranx->sum('amount')),
               'total_number_traded' => number_format($tranx->count()),
               'percentage_volume_traded' => ($total_transaction->sum('amount') == 0) ? 0 : ($tranx->sum('amount')/$total_transaction->sum('amount'))*100,
               'asset_traded_unique_user' => $tranx->groupBy('user_id')->count()

             );
             $export_data[] = $newData;
        }
        return $export_data;
     }
}