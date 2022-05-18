<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UtilityTransaction;

class pulseAnalyticsController extends Controller
{
    public function pulseTransactionAnalytics($startDate = null,$endDate = null)
    {
        if($startDate == null){
            $startDate = now()->format('Y-m-d');
        }
        if($endDate == null){
            $endDate = now()->format('Y-m-d');
        }

        $total_transaction = Transaction::where('status','success')->where('created_at','>',$startDate)->where('created_at','<',$endDate)->get();
        $total_tnx_no = $total_transaction->count();
        $total_card_price = $total_transaction->sum('card_price');
        $total_cash_value = $total_transaction->sum('amount_paid');
        $total_utility = UtilityTransaction::where('status','success')->where('created_at','>',$startDate)->where('created_at','<',$endDate)->get();

        
        return response()->json([
            'success' => true,
            'total_transaction_number' => number_format($total_tnx_no),
            'total_card_price' => number_format($total_card_price),
            'total_cash_value' => number_format($total_cash_value),
            'total_utility' => number_format($total_utility)
        ], 200);

    }

    public function weeklyTransactionTable($startDate,$endDate)
    {


        $byweek = Transaction::select('*')
        ->latest('created_at')->get()
        ->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('Y-m-d');
        });
        foreach ($byweek as $key => $value) {
            $value -> weekNO = 'Week '.$key;
        }
        dd($byweek);
    }
    public function QuarterTransactionTable($startDate,$endDate)
    {

    }
    public function MonthlyTransactionTable($startDate,$endDate)
    {

    }
    public function AnnuallyTransactionTable($startDate,$endDate)
    {
        
    }


}
