<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use Carbon\Carbon;
use Hamcrest\Core\IsTypeOf;

class ChartController extends Controller
{
    public function monthlyAnalytics()
    {
        // monthly chart
        // number of transactions chart

        $data = Transaction::select('id', 'created_at')->get()->groupBy(function($data){
            return Carbon::parse($data->created_at)->format('M');
        });

        $months = [];
        $monthCount = [];

        foreach($data as $month => $values) {
           $months[] = $month;
           $monthCount[] = count($values);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'month' => $month,
            'monthCount' => $monthCount,
        ], 200);





        $days = [];


        $firstDay = date('d');
        $secondDay = date('d') - 1;
        $thirdDay = date('d') - 2;
        $forthDay = date('d') - 3;
        $FiftDay = date('d') - 5;
        $sixthDay = date('d') - 6;
        $seventhDay = date('d') - 7;

        $month = date('m');

        // if($firstDay == '01'){
        //     $month = date('m') - 1;
        // }


        // return $secondDay;


        $dayone = Transaction::whereYear('created_at', "=", date('Y'))
        ->whereMonth('created_at', date('m'))
        ->WhereDay('created_at', $firstDay)->count();


        $daytwo = Transaction::whereYear('created_at', "=", date('Y'))
        ->whereMonth('created_at', $month)
        ->WhereDay('created_at', $secondDay)->count();


        $dayThree = Transaction::whereYear('created_at', "=", date('Y'))
        ->whereMonth('created_at', $month)
        ->WhereDay('created_at', $thirdDay)->count();


        $dayFour = Transaction::whereYear('created_at', "=", date('Y'))
        ->whereMonth('created_at',$month)
        ->WhereDay('created_at', $forthDay)->count();


        $dayFive = Transaction::whereYear('created_at', "=", date('Y'))
        ->whereMonth('created_at',$month)
        ->WhereDay('created_at', $FiftDay)->count();


        $daySix = Transaction::whereYear('created_at', "=", date('Y'))
        ->whereMonth('created_at',$month)
        ->WhereDay('created_at', $sixthDay)->count();


        $daySeven = Transaction::whereYear('created_at', "=", date('Y'))
        ->whereMonth('created_at',$month)
        ->WhereDay('created_at', $seventhDay)->count();

        // return date($month . '/' . $daytwo  );

        return response()->json([
            '1' => $dayone,
            '2' => $daytwo,
            '3' => $dayThree,
            '4' => $dayFour,
            '5' => $dayFive,
            '6' => $daySix,
            '7' => $daySeven,
        ]);
    }
}
