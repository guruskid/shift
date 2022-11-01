<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\Transaction;
use App\UtilityTransaction;
use Carbon\Carbon;

class pulseAnalyticsController extends Controller
{
    public function pulseTransactionAnalytics()
    {
        $date = now();
        $total_transaction = Transaction::where('status','success')->where('created_at',$date)->get();

        $total_tnx_no = $total_transaction->count();
        $total_asset_value = $total_transaction->sum('amount');

        $total_card_price = $total_transaction->sum('card_price');
        $total_cash_value = $total_transaction->sum('amount_paid');

        $total_utility = UtilityTransaction::where('status','success')->where('created_at',$date)->count();

        return response()->json([
            'success' => true,
            'total_transaction_number' => number_format($total_tnx_no),
            'total_asset_value' => number_format($total_asset_value),
            'total_card_price' => number_format($total_card_price),
            'total_cash_value' => number_format($total_cash_value),
            'total_utility' => number_format($total_utility),
            'weekly_transaction_table' => $this->checkDuration('Weekly','Crypto'),
            'monthly_transaction_table' => $this->checkDuration('Monthly','Crypto'),
            'quarterly_transaction_table' => $this->checkDuration('Quarterly','Crypto'),
            'annually_transaction_table' => $this->checkDuration('Annually','Crypto'),
        ], 200);

    }

    public function sortTransactionAnalytics(Request $r)
    {
        $r->startDate = ($r->startDate == null) ? now() : $r->startDate." 00:00:00";
        $r->endDate = ($r->endDate == null) ? now() : $r->endDate." 23:59:59";

        $total_transaction = Transaction::where('status','success')->where('created_at','>=',$r->startDate)->where('created_at','<=',$r->endDate)->get();
        $total_tnx_no = $total_transaction->count();

        $total_asset_value = $total_transaction->sum('amount');
        $total_card_price = $total_transaction->sum('card_price');

        $total_cash_value = $total_transaction->sum('amount_paid');
        $total_utility = UtilityTransaction::where('status','success')->where('created_at','>=',$r->startDate)->where('created_at','<=',$r->endDate)->count();

        $r->transaction_type = ($r->transaction_type == null) ? 'Crypto' : $r->transaction_type;
        return response()->json([
            'success' => true,
            'total_transaction_number' => number_format($total_tnx_no),
            'total_asset_value' => number_format($total_asset_value),
            'total_card_price' => number_format($total_card_price),
            'total_cash_value' => number_format($total_cash_value),
            'total_utility' => number_format($total_utility),
            'weekly_transaction_table' => $this->checkDuration('Weekly',$r->transaction_type),
            'monthly_transaction_table' => $this->checkDuration('Monthly',$r->transaction_type),
            'quarterly_transaction_table' => $this->checkDuration('Quarterly',$r->transaction_type),
            'annually_transaction_table' => $this->checkDuration('Annually',$r->transaction_type ),
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

        //* getting all successful transactions in ascending order and grouping it into months
        $All_transactions = $All_transactions->with('user')->where('status','success')
        ->orderBy('created_at','asc')->get()
        ->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format("M-Y");
        });

        //*breaking it into sets of 3 to get the data of the three months time frame
        $QuarterlyMonths = $All_transactions->chunk(3);
        $previous_total = 0;
        foreach ($QuarterlyMonths as $QM_data) {
                //*variables to hold the verification level data
                $Level_1 = 0;
                $Level_2 = 0;
                $Level_3 = 0;
                $total = 0;
            foreach ($QM_data as $QM_data_key => $QM_data_values) {
                //*variable to store the duration in an array
                $duration[] = $QM_data_key;
                //*getting the total transaction number for each quarter
                $total += $QM_data_values->count();
                foreach ($QM_data_values as $QMv) {
                    //* getting the user verification  data for the transactions
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
            //* assigning value to the duration 
            if(count($duration) == 3){
                $duration_value = "$duration[0] - $duration[2]";
            }
            if(count($duration) == 2){
                $duration_value = "$duration[0] - $duration[1]";
            }
            if(count($duration) == 1){
                $duration_value = "$duration[0]";
            }
            
                $QM_data->duration = $duration_value;
                $QM_data->L1_percentage = ($Level_1/$total)*100;
                $QM_data->L2_percentage = ($Level_2/$total)*100;
                $QM_data->L3_percentage = ($Level_3/$total)*100;
                $QM_data->successful_transactions = $total;
                $QM_data->date = $QMv->created_at;
                //* destroying the data available in the duration array
                unset($duration);
                
                //*calculating percentage difference
                $per_diff = ($previous_total != 0) ? (($total - $previous_total )/ $previous_total)*100 : 0 ;
                $QM_data->percentage_difference = round($per_diff,2);
                
                //*setting the previous total with the old total
                $previous_total = $total;
                  
        }
        //* adding the previous_percentage_difference to the payload.
        $previous_total = 0;
         foreach ($QuarterlyMonths as $key => $value) {
             $value->previous_percentage_difference = $previous_total;
             $previous_total = $value->percentage_difference;
         }
        $data = $QuarterlyMonths->sortByDesc('date');

        //*sending to the frontend the needed data.
        $export_data = array();
         foreach ($data as $value) {
            $newData = array(
                'duration' => $value->duration,
                'L1_percentage' =>$value->L1_percentage,
                'L2_percentage' => $value->L2_percentage,
                'L3_percentage' => $value->L3_percentage,
                'successful_transactions' => $value->successful_transactions,
                'percentage_difference' => $value->percentage_difference,
                'previous_percentage_difference' => $value->previous_percentage_difference,
            );
            $export_data[] = $newData; 
         }
         return $export_data;
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
        
        //* getting all successful transactions in ascending order according to the duration format
        $byweek = $byweek->with('user')->orderBy('created_at','asc')->where('status','success')->get()
         ->groupBy(function($date) use ($duration) {
             return Carbon::parse($date->created_at)->format($duration);
         });
         $previous_total = 0;
         foreach ($byweek as $key => $value) {
             $total = $value->count();
             //*variables to hold the verification level data
             $Level_1 = 0;
             $Level_2 = 0;
             $Level_3 = 0;
             foreach($value as $v){
                //* getting the user verification  data for the transactions
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

             //* assigning value to the duration 
             $value->duration = ($duration == 'W-Y') ? "Week ".$key:$key;
             $value->L1_percentage = $percentage_L1;
             $value->L2_percentage = $percentage_L2;
             $value->L3_percentage = $percentage_L3;
             $value->successful_transactions = $total;

             //*calculating percentage difference
             $per_diff = ($previous_total != 0) ? (($total - $previous_total )/ $previous_total)*100 : 0 ;
             $value->percentage_difference = round($per_diff,2);
             $previous_total = $total;
         }
         $previous_total = 0;
         //* adding the previous_percentage_difference to the payload.
         foreach ($byweek as $key => $value) {
             $value->previous_percentage_difference = $previous_total;
             $previous_total = $value->percentage_difference;
         }
         $data = $byweek->sortByDesc('date');

         //*sending to the frontend the needed data.
         $export_data = array();
         foreach ($data as $key => $value) {
            $newData = array(
                'duration' => $value->duration,
                'L1_percentage' =>$value->L1_percentage,
                'L2_percentage' => $value->L2_percentage,
                'L3_percentage' => $value->L3_percentage,
                'successful_transactions' => $value->successful_transactions,
                'percentage_difference' => $value->percentage_difference,
                'previous_percentage_difference' => $value->previous_percentage_difference,
            );
            $export_data[] = $newData; 
         }
         return $export_data;


    }


}
