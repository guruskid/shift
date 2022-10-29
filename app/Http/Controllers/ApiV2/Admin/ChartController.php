<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\Transaction;
use App\User;
use Carbon\Carbon;

class ChartController extends Controller
{
    public function monthlyTransactionAnalytics()
    {
        // monthly chart
        // number of transactions chart

        $transaction_data = Transaction::select('id', 'created_at')->get()->groupBy(function($transaction_data){
            return Carbon::parse($transaction_data->created_at)->format('Y-M');
        });

        $transaction_months = [];
        $transaction_monthCount = [];

        foreach($transaction_data as $month => $values) {
           $transaction_months[] = $month;
           $transaction_monthCount[] = count($values);
        }



        /////////////////////////////////////////////////////////

        $new_user_data = User::select('id', 'created_at')->get()->groupBy(function($new_user_data){
            return Carbon::parse($new_user_data->created_at)->format('Y-M');
        });

        $new_user_months = [];
        $new_user_monthCount = [];

        foreach($new_user_data as $month => $values) {
           $new_user_months[] = $month;
           $new_user_monthCount[] = count($values);
        }

        /////////////////////////////////////////////////////////


        $unique_user_data = Transaction::with('user')->select('id', 'created_at')->get()->groupBy(function($unique_user_data){
            return Carbon::parse($unique_user_data->created_at)->format('Y-M');
        });

        $unique_user_months = [];
        $monthlyCount = [];

        // return($unique_user_data);

        foreach($unique_user_data as $month => $values) {
           if(!in_array($month, $unique_user_months)) {
            $unique_user_months[] = $month;
            $monthlyCount[] = count($values);
           }
        }


        $monthlyVenue = Transaction::select('id', 'amount', 'created_at')->where('status', 'success')->get()->groupBy(function($monthlyVenue){
            return Carbon::parse($monthlyVenue->created_at)->format('Y-M');
        });

        $monthly_data = [];
        $monthlyrevenue = [];

        // return($monthlyVenue);

        foreach($monthlyVenue as $month => $valuesw) {
            $monthly_data[] = $month;
            $monthlyrevenue[] = $valuesw->sum('amount');
        }



        // Total Accountant Payout
        $AccountantPayout = NairaTransaction::select('id', 'amount', 'created_at')->where('status', 'success')->get()->groupBy(function($AccountantPayout){
            return Carbon::parse($AccountantPayout->created_at)->format('Y-M');
        });

        $payout_month = [];
        $amount_paid_out = [];

        // return($AccountantPayout);

        foreach($AccountantPayout as $month => $valuesw) {
            $payout_month[] = $month;
            $amount_paid_out[] = $valuesw->sum('amount');
        }



        return response()->json([
            'success' => true,

            'transaction_months' => [
                'month' => $transaction_months,
                'monthCount' => $transaction_monthCount,
            ],

            'new_user_months' => [
                'month' => $new_user_months,
                'monthCount' => $new_user_monthCount,
            ],

            'unique_user_months' => [
                'data' => $monthly_data,
                'revenue' => $monthlyrevenue,
            ],

            'TotalAccountantPayout' => [
                'data' => $payout_month,
                'revenue' => $amount_paid_out,
            ],

        ], );
    }
}
