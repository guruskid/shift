<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UtilityController extends Controller
{

    static function utility($type)
    {
        $transactions = UtilityTransaction::with('user', 'nairaWallet', 'bitcoinWallet')->where('type', $type)->orderBy('created_at', 'desc');
        return $transactions;
    }

    public function allUtility()
    {
        $transactions = UtilityTransaction::with('user', 'nairaWallet', 'bitcoinWallet')->orderBy('created_at', 'desc');
        return response()->json([
            'success' => true,
            'total_number_of_transactions' => Transaction::count(),
            'total_volume' => [],
            'total_cash_value' => $transactions->sum('amount'),
            'total_utilities' => $transactions->count(),
            'data' => $transactions->get()->paginate(1000)
        ]);
    }

    public function utilitySearch( $start, $end)
    {

        $transactions = UtilityTransaction::with('user', 'nairaWallet', 'bitcoinWallet')->where('created_at', '>=', $start)->where('created_at', '<=', $end)->orderBy('created_at', 'desc');
        return $transactions;
    }

    public function utilitiesSearch(Request $r)
    {

        // return now();
        $data = Validator::make($r->all(), [
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        if ($data->fails()) {
            return response()->json([
                'success' => false,
                'message' => $data->errors(),
            ], 401);
        }


        // dd(date('Y-m-d'));
        $starts = Carbon::parse($r['start_date'])->format('Y-m-d');
        $ends = Carbon::parse($r['end_date'])->format('Y-m-d');

        $transactions = $this->utilitySearch( $starts, $ends);

        // return response()->json([
        //     'success' => true,
        //     'data' => $transactions,
        // ]);

        // $transactions = UtilityTransaction::with('user', 'nairaWallet', 'bitcoinWallet')->orderBy('created_at', 'desc');
        return response()->json([
            'success' => true,
            'total_number_of_transactions' => Transaction::count(),
            'total_volume' => [],
            'total_cash_value' => $transactions->sum('amount'),
            'total_utilities' => $transactions->count(),
            'data' => $transactions->get()->paginate(1000)
        ]);
    }

    public function airtime()
    {
        $ut = self::utility('Recharge card purchase');
        // return response()->json([
        //     'success' => true,
        //     'data' => $ut,
        // ]);


        return response()->json([
            'success' => true,
            'total_number_of_transactions' => Transaction::count(),
            'total_volume' => [],
            'total_cash_value' => $ut->sum('amount'),
            'total_utilities' => $ut->count(),
            'data' => $ut->get()->paginate(1000)
        ]);
    }

    public function data()
    {
        $ut = self::utility('Data purchase');
        // return response()->json([
        //     'success' => true,
        //     'data' => $ut,
        // ]);


        return response()->json([
            'success' => true,
            'total_number_of_transactions' => Transaction::count(),
            'total_volume' => [],
            'total_cash_value' => $ut->sum('amount'),
            'total_utilities' => $ut->count(),
            'data' => $ut->get()->paginate(1000)
        ]);
    }

    public function power()
    {
        $ut = self::utility('Power');
        // return response()->json([
        //     'success' => true,
        //     'data' => $ut,
        // ]);


        return response()->json([
            'success' => true,
            'total_number_of_transactions' => Transaction::count(),
            'total_volume' => [],
            'total_cash_value' => $ut->sum('amount'),
            'total_utilities' => $ut->count(),
            'data' => $ut->get()->paginate(1000)
        ]);
    }

    public function cable()
    {
        $ut = self::utility('Electricity purchase');
        // return response()->json([
        //     'success' => true,
        //     'data' => $ut,
        // ]);


        return response()->json([
            'success' => true,
            'total_number_of_transactions' => Transaction::count(),
            'total_volume' => [],
            'total_cash_value' => $ut->sum('amount'),
            'total_utilities' => $ut->count(),
            'data' => $ut->get()->paginate(1000)
        ]);
    }


}
