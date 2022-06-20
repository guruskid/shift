<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\UtilityTransaction;

class pulseAnalyticsController extends Controller
{
    public function pulseTransactionAnalytics($startDate = null,$endDate = null,$transaction_type=null,$transaction_duration=null)
    {
        if($startDate == null){
            $startDate = now()->format('Y-m-d');
        }
        if($endDate == null){
            $endDate = now()->format('Y-m-d');
        }

        $total_transaction = Transaction::where('status','success')->whereDate('created_at','>',$startDate)->whereDate('created_at','<',$endDate)->get();
        $total_tnx_no = $total_transaction->count();
        $total_asset_value = $total_transaction->sum('amount');
        $total_card_price = $total_transaction->sum('card_price');
        $total_cash_value = $total_transaction->sum('amount_paid');
        $total_utility = UtilityTransaction::where('status','success')->whereDate('created_at','>',$startDate)->whereDate('created_at','<',$endDate)->count();

        $transaction_table = $this->checkDuration($transaction_duration,$transaction_type);
        return response()->json([
            'success' => true,
            'total_transaction_number' => number_format($total_tnx_no),
            'total_asset_value' => number_format($total_asset_value),
            'total_card_price' => number_format($total_card_price),
            'total_cash_value' => number_format($total_cash_value),
            'total_utility' => number_format($total_utility),
            'transaction_table' => $transaction_table->paginate(10),
        ], 200);

    }

    public function checkDuration($transaction_duration,$transaction_type)
    {
            if($transaction_type == null AND $transaction_duration == null)
            {
                $transaction_table = $this->weeklyTransactionTable('Crypto');
            }
            if($transaction_duration == 'Weekly')
            {
                $transaction_table = $this->weeklyTransactionTable($transaction_type);
            }
            if($transaction_duration == 'Quarterly')
            {
                $transaction_table = $this->QuarterTransactionTable($transaction_type);
            }
            if($transaction_duration == 'Monthly')
            {
                $transaction_table = $this->MonthlyTransactionTable($transaction_type);
            }
            if($transaction_duration == 'Annually')
            {
                $transaction_table = $this->AnnuallyTransactionTable($transaction_type);
            }
            return $transaction_table;
    }

    public function weeklyTransactionTable($transaction_type)
    {
        return $this->AnnuallyMonthlyWeeklyTransactionTable($transaction_type,'W-Y');
    }
    public function QuarterTransactionTable($transaction_type)
    {
        if($transaction_type == 'Naira')
        {
            $All_transactions = NairaTrade::query();
        }
        if($transaction_type == 'Crypto')
        {
            $All_transactions = Transaction::query();
        }
        if($transaction_type == 'Others')
        {
            $All_transactions = UtilityTransaction::query();
        }

        $All_transactions = $All_transactions->with('user')->where('status','success')
        ->orderBy('created_at','asc')->get()
        ->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format("M-Y");
        });
        $QuarterlyMonths = $All_transactions->chunk(3);
        $previous_total = 0;
        foreach ($QuarterlyMonths as $QM_data) {
                
                $Level_1 = 0;
                $Level_2 = 0;
                $Level_3 = 0;
                $total = 0;
            foreach ($QM_data as $QM_data_key => $QM_data_values) {
                $duration[] = $QM_data_key;
                $total += $QM_data_values->count();
                foreach ($QM_data_values as $QMv) {
                 if(isset($QMv->user))
                 {
                     if($QMv->user->phone_verified_at != null AND $QMv->user->address_verified_at == null AND $QMv->user->idcard_verified_at == null)
                     {
                         $Level_1 ++;
                     }
                     if($QMv->user->phone_verified_at != null AND $QMv->user->address_verified_at != null AND $QMv->user->idcard_verified_at == null)
                     {
                         $Level_2 ++;
                     }
                     if($QMv->user->phone_verified_at != null AND $QMv->user->address_verified_at != null AND $QMv->user->idcard_verified_at != null)
                     {
                         $Level_3 ++;
                     }
                     
                 }
                }
            }
            if(count($duration) == 3){
                $duration_value = "$duration[0] - $duration[2]";
            }
            if(count($duration) == 2){
                $duration_value = "$duration[0] - $duration[2]";
            }
            if(count($duration) == 1){
                $duration_value = "$duration[0]";
            }
            // dd($duration);
                $QM_data->duration = $duration_value;
                $QM_data->L1_percentage = ($Level_1/$total)*100;
                $QM_data->L2_percentage = ($Level_2/$total)*100;
                $QM_data->L3_percentage = ($Level_3/$total)*100;
                $QM_data->successful_transactions = $total;
                $QM_data->date = $QMv->created_at;
                $duration = array();
                if($previous_total != 0){
                    $per_diff = (($total - $previous_total )/ $previous_total)*100;
                }else{
                    $per_diff = 0;
                }
                $QM_data->percentage_difference = round($per_diff,2);
                $previous_total = $total;
            
        }
        $previous_total = 0;
         foreach ($QuarterlyMonths as $key => $value) {
             $value->previous_percentage_difference = $previous_total;
             $previous_total = $value->percentage_difference;
         }
        return $QuarterlyMonths->sortByDesc('date');
    }
    public function MonthlyTransactionTable($transaction_type)
    {
        return $this->AnnuallyMonthlyWeeklyTransactionTable($transaction_type,'M-Y');
    }
    public function AnnuallyTransactionTable($transaction_type)
    {
        return $this->AnnuallyMonthlyWeeklyTransactionTable($transaction_type,'Y');
    }

    public function AnnuallyMonthlyWeeklyTransactionTable($transaction_type,$duration)
    {
        if($transaction_type == 'Naira')
        {
            $byweek = NairaTrade::query();
        }
        if($transaction_type == 'Crypto')
        {
            $byweek = Transaction::query();
        }
        if($transaction_type == 'Others')
        {
            $byweek = UtilityTransaction::query();
        }
        
        $byweek = $byweek->with('user')->orderBy('created_at','asc')->where('status','success')->get()
         ->groupBy(function($date) use ($duration) {
             return Carbon::parse($date->created_at)->format($duration);
         });
         $previous_total = 0;
         foreach ($byweek as $key => $value) {
             $total = $value->count();
             $Level_1 = 0;
             $Level_2 = 0;
             $Level_3 = 0;
             foreach($value as $v){
                 if(isset($v->user))
                 {
                     if($v->user->phone_verified_at != null AND $v->user->address_verified_at == null AND $v->user->idcard_verified_at == null)
                     {
                         $Level_1 ++;
                     }
                     if($v->user->phone_verified_at != null AND $v->user->address_verified_at != null AND $v->user->idcard_verified_at == null)
                     {
                         $Level_2 ++;
                     }
                     if($v->user->phone_verified_at != null AND $v->user->address_verified_at != null AND $v->user->idcard_verified_at != null)
                     {
                         $Level_3 ++;
                     }
                 }
                 $value->date = $v->created_at;
    
                 
             }
             $percentage_L1 = ($Level_1/$total)*100;
             $percentage_L2 = ($Level_2/$total)*100;
             $percentage_L3 = ($Level_3/$total)*100;
    
             $value->duration = ($duration == 'W-Y') ? "Week ".$key:$key;
             $value->L1_percentage = $percentage_L1;
             $value->L2_percentage = $percentage_L2;
             $value->L3_percentage = $percentage_L3;
             $value->successful_transactions = $total;
             if($previous_total != 0){
             $per_diff = (($total - $previous_total )/ $previous_total)*100;
             }
             else{
                 $per_diff = 0;
             }
             $value->percentage_difference = round($per_diff,2);
             $previous_total = $total;
         }
         $previous_total = 0;
         foreach ($byweek as $key => $value) {
             $value->previous_percentage_difference = $previous_total;
             $previous_total = $value->percentage_difference;
         }
         return $byweek->sortByDesc('date');


    }


}
