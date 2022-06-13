<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Illuminate\Support\Facades\DB;

class PulseController extends Controller
{
    public function index()
    {
        // Unique users that has carried out  at least one transactions
        // Active user is a unique user that has carried out a transaction within a said duration

        $noMonthlyUniqueUsers = array();
        $noLastMonthlyUniqueUsers = array();
        $noQuaterlyUniqueUsers = array();
        $noPreviousQuaterlyUniqueUsers = array();
        $noDailyUniqueUsers = array();
        $monthlyUser = Transaction::whereYear('created_at', "=", date('Y'))->where('status', 'success')->whereMonth('created_at', "=", date('m'))->get();
        $dailyUser = Transaction::whereYear('created_at', "=", date('Y'))->where('status', 'success')->whereMonth('created_at', "=", date('m'))->whereDay('created_at', "=", date('d'))->get();

        $monthBeforeLast = date('m') - 2;
        $monthBeforeLast == 0 ? $monthBeforeLast = 12 : $monthBeforeLast = $monthBeforeLast;

        $threeMonthsBack = date('m') - 3;
        $threeMonthsBack == 0 ? $threeMonthsBack = 12 : $threeMonthsBack = $threeMonthsBack;


        // Previous quater

        $fourMonthsBack = date('m') - 4;
        $fourMonthsBack == 0 ? $fourMonthsBack = 12 : $fourMonthsBack = $fourMonthsBack;

        $fiveMonthsBack = date('m') - 5;
        $fiveMonthsBack == 0 ? $fiveMonthsBack = 12 : $fiveMonthsBack = $fiveMonthsBack;

        $sixMonthsBack = date('m') - 6;
        $sixMonthsBack == 0 ? $sixMonthsBack = 12 : $sixMonthsBack = $sixMonthsBack;

        $sevenMonthsBack = date('m') - 7;
        $sevenMonthsBack == 0 ? $sevenMonthsBack = 12 : $sevenMonthsBack = $sevenMonthsBack;

        //   /////////////////////////////////////////////////


        $lastMonth = date('m') - 1;
        $lastMonth == 0 ? $lastMonth = 12 : $lastMonth = $lastMonth;
        $lastMonthlyUser = Transaction::whereYear('created_at', "=", date('Y'))->where('status', 'success')->whereMonth('created_at', "=", $lastMonth)->get();




        $quarterly = date('m') - 4;
        $quarterly == 0 ? $quarterly = 12 : $quarterly = $quarterly;
        $quaterlyUser = Transaction::whereYear('created_at', "=", date('Y'))
        ->orWhereMonth('created_at', $lastMonth)
        ->orWhereMonth('created_at', $monthBeforeLast)
        ->orWhereMonth('created_at', $threeMonthsBack)
        ->orWhereMonth('created_at', $quarterly)
        ->where('status', 'success')
        ->get();


        $previousQuater = Transaction::whereYear('created_at', "=", date('Y'))
        ->orWhereMonth('created_at', $fourMonthsBack)
        ->orWhereMonth('created_at', $fiveMonthsBack)
        ->orWhereMonth('created_at', $sixMonthsBack)
        ->orWhereMonth('created_at', $sevenMonthsBack)
        ->where('status', 'success')
        ->get();



        foreach($monthlyUser as $user){
            // if(isset($user->transactions[0]->id) || isset($user->utilityTransaction[0]->id)){
                if(!in_array($user->id, $noMonthlyUniqueUsers)){
                    array_push($noMonthlyUniqueUsers, $user->id);
                }
            // }
        }

        foreach($dailyUser as $user){
            // if(isset($user->transactions[0]->id) || isset($user->utilityTransaction[0]->id)){
                if(!in_array($user->id, $noDailyUniqueUsers)){
                    array_push($noDailyUniqueUsers, $user->id);
                }
            // }
        }

        foreach($lastMonthlyUser as $user){
            // if(isset($user->transactions[0]->id) || isset($user->utilityTransaction[0]->id)){
                if(!in_array($user->id, $noLastMonthlyUniqueUsers)){
                    array_push($noLastMonthlyUniqueUsers, $user->id);
                }
            // }
        }


        foreach($quaterlyUser as $user){
            // if(isset($user->transactions[0]->id) || isset($user->utilityTransaction[0]->id)){
                if(!in_array($user->id, $noQuaterlyUniqueUsers)){
                    array_push($noQuaterlyUniqueUsers, $user->id);
                }
            // }
        }


        foreach($previousQuater as $user){
            // if(isset($user->transactions[0]->id) || isset($user->utilityTransaction[0]->id)){
                if(!in_array($user->id, $noPreviousQuaterlyUniqueUsers)){
                    array_push($noPreviousQuaterlyUniqueUsers, $user->id);
                }
            // }
        }

        // return(sizeof($noQuaterlyUniqueUsers));


        sizeof($noDailyUniqueUsers) > 0 ? $dailyActiveUsers = sizeof($noDailyUniqueUsers) : $dailyActiveUsers = 1;
        sizeof($noMonthlyUniqueUsers) > 0 ? $monthlyActiveUsers = sizeof($noMonthlyUniqueUsers) : $monthlyActiveUsers = 1;
        sizeof($noLastMonthlyUniqueUsers) > 0 ? $lastmonthlyActiveUsers = sizeof($noLastMonthlyUniqueUsers) : $lastmonthlyActiveUsers = 1;
        sizeof($noPreviousQuaterlyUniqueUsers) > 0 ? $previousQuaterlymonthlyActiveUsers = sizeof($noPreviousQuaterlyUniqueUsers) : $previousQuaterlymonthlyActiveUsers = 1;

        $activationRatio = $dailyActiveUsers / $monthlyActiveUsers * 100;
        $monthDiff = $lastmonthlyActiveUsers - $monthlyActiveUsers;
        $churnRateMonth = $monthDiff / $lastmonthlyActiveUsers * 100;






        // return($noDailyUniqueUsers);

        return response()->json([
            'activationRatio' => $activationRatio,
            'churnRateMonth' => $churnRateMonth,
        ]);


        // $yearlyTransactionsVolume = Transaction::with('user')->whereYear('created_at', "=", date('Y'))->where('status', 'success')->sum('amount');
        // $yearlyTransactionsCount = Transaction::with('user')->whereYear('created_at', "=", date('Y'))->where('status', 'success')->count();

        // $yearlyUtilitiesVolume = UtilityTransaction::with('user')->whereYear('created_at', "=", date('Y'))->where('status', 'success')->sum('amount');
        // $yearlyUtilitiesCount = UtilityTransaction::with('user')->whereYear('created_at', "=", date('Y'))->where('status', 'success')->sum('amount');

        // $yearlyVolume = $yearlyTransactionsVolume + $yearlyUtilitiesVolume;
        // $yearlyCount = $yearlyTransactionsCount + $yearlyUtilitiesCount;



        // $monthlyTransactionsVolume = Transaction::with('user')->whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->sum('amount');
        // $monthlyTransactionsCount = Transaction::with('user')->whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->count();

        // $monthlyUtilitiesVolume = UtilityTransaction::with('user')->whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->sum('amount');
        // $monthlyUtilitiesCount = UtilityTransaction::with('user')->whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->sum('amount');

        // $monthlyVolume = $monthlyTransactionsVolume + $monthlyUtilitiesVolume;
        // $monthlyCount = $monthlyTransactionsCount + $monthlyUtilitiesCount;



        // $yearlyTransactions = Transaction::with('user')->whereYear('created_at', "=", date('Y'))->where('status', 'success')->get();
        // $monthlyTransactions = Transaction::with('user')->whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->get();

        // // return date('m');
        // $yearlyUtilitiesTransactions = UtilityTransaction::with('user')->whereYear('created_at', "=", date('Y'))->where('status', 'success')->get();
        // $monthlyUtilitiesTransactions = UtilityTransaction::with('user')->whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->get();

        // $yearUniqueUsers = array();
        // $monthUniqueUsers = array();
        // $ampv = $monthlyVolume / $monthlyCount;
        // $aapv = $yearlyVolume / $yearlyCount;


        // // Yearly
        // foreach($yearlyTransactions[0] as $uniqueUser) {
        //     if(!in_array($uniqueUser, $yearUniqueUsers)) {
        //         array_push($yearUniqueUsers, $uniqueUser);
        //     }
        // }
        // foreach($yearlyUtilitiesTransactions[0] as $uniqueUser) {
        //     if(!in_array($uniqueUser, $yearUniqueUsers)) {
        //         array_push($yearUniqueUsers, $uniqueUser);
        //     }
        // }


        // // Monthly
        // foreach($monthlyTransactions[0] as $uniqueUser) {
        //     if(!in_array($uniqueUser, $yearUniqueUsers)) {
        //         array_push($monthUniqueUsers, $uniqueUser);
        //     }
        // }
        // foreach($monthlyUtilitiesTransactions[0] as $uniqueUser) {
        //     if(!in_array($uniqueUser, $yearUniqueUsers)) {
        //         array_push($monthUniqueUsers, $uniqueUser);
        //     }
        // }
    }
}
