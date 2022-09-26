<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiV2\Admin\ComplianceFraudResource;
use App\User;

class ComplianceFraudController extends Controller
{
    private static $usd_rate;
    public function __construct()
    {
        self::$usd_rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
    }
    public function index($type)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        return $this->loadData($startDate, $endDate, $type);
    }

    public function sorting(Request $request)
    {
        $type = $request->type;
        $month = $request->month;
        $year = $request->year;

        $startDate = Carbon::createFromDate($year,$month,1);
        $endDate = Carbon::createFromDate($year,$month,1)->endOfMonth();

        return $this->loadData($startDate, $endDate, $type);
    }

    public function loadData($start, $end, $type)
    {  
        $usd_rate = self::$usd_rate;
        $users = User::with('transactions','nairaTrades','utilityTransaction')->get();

        $userData = ComplianceFraudResource::sortCollection($users, $start, $end, $usd_rate, $type);
        $userData = collect($userData)->sortByDesc('DebitAmount');

        return response()->json([
            'success' => true,
            'usersData' => $userData,
        ], 200);
    }
}
