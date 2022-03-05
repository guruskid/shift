<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UtilityTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UtilityController extends Controller
{

    static function utility($type)
    {
        $transactions = UtilityTransaction::where('type', $type)->orderBy('created_at', 'desc')->get()->paginate(1000);
        return $transactions;
    }

    public function utilitySearch($type, $start, $end)
    {

        $transactions = UtilityTransaction::where('created_at', '>=', $end)->where('created_at', '<=', $start)->where('type', $type)->get()->paginate(100);

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

        $transactions = $this->utilitySearch($r->type, $r['end_date'], $r['start_date']);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    public function airtime()
    {
        $ut = self::utility('Recharge card purchase');
        return response()->json([
            'success' => true,
            'data' => $ut,
        ]);
    }

    public function data()
    {
        $ut = self::utility('Data purchase');
        return response()->json([
            'success' => true,
            'data' => $ut,
        ]);
    }

    public function power()
    {
        $ut = self::utility('Power');
        return response()->json([
            'success' => true,
            'data' => $ut,
        ]);
    }

    public function cable()
    {
        $ut = self::utility('Electricity purchase');
        return response()->json([
            'success' => true,
            'data' => $ut,
        ]);
    }
}
