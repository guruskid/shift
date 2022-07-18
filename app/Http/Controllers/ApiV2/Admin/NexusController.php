<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Card;
use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\UtilityTransaction;
use Carbon\Carbon;

class NexusController extends Controller
{
   //TODO there is an extra part 
    public function verificationData(Request $request)
    {
        if($request->date == null){
            $date = now();
        }else{
            $date = Carbon::parse($request->date);
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
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', '==', null)->where('idcard_verified_at', '==', null);
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $L1_Utilities = UtilityTransaction::whereHas('user', function ($query){
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', '==', null)->where('idcard_verified_at', '==', null);
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
        $L1_percentage_monthly_trades = ($L1_trading_volume/$total_volume)*100;
        //* end Level 1 Verification 


         //* Level 2 Verification
         $L2_Crypto = Transaction::whereHas('user', function ($query){
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', '==', null);
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $L2_Utilities = UtilityTransaction::whereHas('user', function ($query){
            $query->where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', '==', null);
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
        $L2_percentage_monthly_trades = ($L2_trading_volume/$total_volume)*100;
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
        $L3_percentage_monthly_trades = ($L3_trading_volume/$total_volume)*100;
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
         
         
         $oldUsers_turnover_percentage = ($oldUsers_turnover/$monthly_turnover_total)*100;

          //?New Users Monthly Turnover 
          $newUsers_turnover_crypto = Transaction::whereHas('user', function ($query){
            $query->where('created_at','>',now()->subMonth(3));
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $newUsers_turnover_utilities = UtilityTransaction::whereHas('user', function ($query){
            $query->where('created_at','>',now()->subMonth(3));
         })->where('status','success')->whereMonth('created_at',$date->month)->WhereYear('created_at',$date->year)->get();

         $newUsers_turnover = ($newUsers_turnover_crypto->sum('amount') + $newUsers_turnover_utilities->sum('amount'));

         $newUsers_totaltrades = ($newUsers_turnover_crypto->count() + $newUsers_turnover_utilities->count());

         $newUsers_turnover_percentage = ($newUsers_turnover/$monthly_turnover_total)*100;

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
        ], 200);


    }
     public function NexusCrypto(Request $request)
     {
        $date = ($request->date == null) ? now()->format('Y-m-d') : Carbon::parse($request->date)->format('Y-m-d');
        $nexus_crypto = $this->NexusCards($date,1);

        return response()->json([
            'success' => true,
            'crypto' => $nexus_crypto,
        ], 200);
        
     }

     public function NexusGiftCard(Request $request)
     {
        $date = ($request->date == null) ? now()->format('Y-m-d') : Carbon::parse($request->date)->format('Y-m-d');
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