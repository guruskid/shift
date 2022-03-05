<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UtilityTransaction;
use Illuminate\Support\Facades\Auth;

class UtilityController extends Controller
{

    static function utility($type)
    {
        $transactions = UtilityTransaction::where('type', $type)->orderBy('created_at', 'desc')->get()->paginate(1000);
        return $transactions;
    }

    static function utilitySearch($start, $end)
    {

        // $transactions = UtilityTransaction::whereNotNull('id')->orderBy('created_at', 'desc');
        // $type = UtilityTransaction::select('type')->distinct('type')->get();
        // $status = UtilityTransaction::select('status')->distinct('status')->get();
        // $data = $request->validate([
        //     'start' => 'date|string',
        //     'end' => 'date|string',
        // ]);

        // if (!empty($data)) {
        //     $transactions['transactions'] = $transactions['transactions']
        //     ->where('created_at', '>=', $data['start'])
        //     ->where('created_at', '<=', $data['end']);

        //     if ($request->type != 'null') {
        //         $transactions['transactions'] = $transactions['transactions']
        //         ->where('type','=',$request->type);
        //     }
        //     if ($request->status != 'null') {
        //         $transactions['transactions'] = $transactions['transactions']
        //         ->where('status','=',$request->status);
        //     }
        // }
        // $transactions = UtilityTransaction::where('type', $type)->orderBy('created_at', 'desc')->get()->paginate(1000);
        // return $transactions;
    }

    public function airtime(Request $request)
    {
        $ut = self::utility('Recharge card purchase');
        return response()->json([
            'success' => true,
            'data' => $ut,
        ]);
    }

    public function data(Request $request)
    {
        $ut = self::utility('Data purchase');
        return response()->json([
            'success' => true,
            'data' => $ut,
        ]);
    }

    public function power(Request $request)
    {
        $ut = self::utility('Power');
        return response()->json([
            'success' => true,
            'data' => $ut,
        ]);
    }

    public function cable(Request $request)
    {
        $ut = self::utility('Electricity purchase');
        return response()->json([
            'success' => true,
            'data' => $ut,
        ]);
    }
}
