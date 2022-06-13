<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;

class CustomerLifeController extends Controller
{
    public function index()
    {
        $yearlyTransactionsVolume = Transaction::whereYear('created_at', "=", date('Y'))->where('status', 'success')->sum('amount');
        $yearlyTransactionsCount = Transaction::whereYear('created_at', "=", date('Y'))->where('status', 'success')->count();

        $yearlyUtilitiesVolume = UtilityTransaction::whereYear('created_at', "=", date('Y'))->where('status', 'success')->sum('amount');
        $yearlyUtilitiesCount = UtilityTransaction::whereYear('created_at', "=", date('Y'))->where('status', 'success')->sum('amount');

        $yearlyVolume = $yearlyTransactionsVolume + $yearlyUtilitiesVolume;
        $yearlyCount = $yearlyTransactionsCount + $yearlyUtilitiesCount;



        $monthlyTransactionsVolume = Transaction::whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->sum('amount');
        $monthlyTransactionsCount = Transaction::whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->count();

        $monthlyUtilitiesVolume = UtilityTransaction::whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->sum('amount');
        $monthlyUtilitiesCount = UtilityTransaction::whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->sum('amount');

        $monthlyVolume = $monthlyTransactionsVolume + $monthlyUtilitiesVolume;
        $monthlyCount = $monthlyTransactionsCount + $monthlyUtilitiesCount;



        $yearlyTransactions = Transaction::whereYear('created_at', "=", date('Y'))->where('status', 'success')->get();
        $monthlyTransactions = Transaction::whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->get();

        // return date('m');
        $yearlyUtilitiesTransactions = UtilityTransaction::whereYear('created_at', "=", date('Y'))->where('status', 'success')->get();
        $monthlyUtilitiesTransactions = UtilityTransaction::whereYear('created_at', "=", date('Y'))->whereMonth('created_at', "=", date('m'))->where('status', 'success')->get();

        $yearUniqueUsers = array();
        $monthUniqueUsers = array();
        $ampv = $monthlyVolume / $monthlyCount;
        $aapv = $yearlyVolume / $yearlyCount;


        // Yearly
        foreach($yearlyTransactions[0] as $uniqueUser) {
            if(!in_array($uniqueUser, $yearUniqueUsers)) {
                array_push($yearUniqueUsers, $uniqueUser);
            }
        }
        foreach($yearlyUtilitiesTransactions[0] as $uniqueUser) {
            if(!in_array($uniqueUser, $yearUniqueUsers)) {
                array_push($yearUniqueUsers, $uniqueUser);
            }
        }


        // Monthly
        foreach($monthlyTransactions[0] as $uniqueUser) {
            if(!in_array($uniqueUser, $yearUniqueUsers)) {
                array_push($monthUniqueUsers, $uniqueUser);
            }
        }
        foreach($monthlyUtilitiesTransactions[0] as $uniqueUser) {
            if(!in_array($uniqueUser, $yearUniqueUsers)) {
                array_push($monthUniqueUsers, $uniqueUser);
            }
        }

        sizeof($monthUniqueUsers) < 1 ? $monthUniqueUsers = 1 : $monthUniqueUsers = sizeof($monthUniqueUsers);


        // return $monthUniqueUsers;
        $ampf = $monthlyVolume / $monthUniqueUsers;
        $aapf = $yearlyCount / sizeof($yearUniqueUsers);
        $ampf2 = $monthlyCount / $monthUniqueUsers;
        $cv = $ampv * $ampf;
        $acl = $ampv * $ampf;
        $cva = $aapv * $aapf;
        $cvav = $cv * $ampf / $cva * $acl;

        return response()->json([
            'AAPV' => (float)$aapv,
            'AMPV' => (float)$ampv,
            'AAPF' => $aapf,
            'AMPF' => $ampf,
            'AMPF2' => $ampf2,
            'CVA' => $cva,
            'CV' => $cv,
            'ACL' => $acl,
            'CVAV' => $cvav

            // 'yearlyTransactionsCount1' => $yearlyTransactionsVolume,
            // 'monthlyTransactionsCount1' => $monthlyTransactionsVolume,
        ]);
    }
}
