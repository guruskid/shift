<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiV2\Admin\ComplianceFraudResource;
use App\User;
use App\VerificationLimit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ComplianceFraudController extends Controller
{
    private static $usd_rate;
    public function __construct()
    {
        self::$usd_rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
    }
    public function index($type = null)
    {
        $type = ($type == null) ? 'NGN' : $type;
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        return $this->loadData($startDate, $endDate, $type);
    }

    public function sorting(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'month' => 'required',
            'year' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $type = $request->type;
        $month = $request->month;
        $year = $request->year;

        $type= ($type == null) ? "NGN" : $type;

        $startDate = Carbon::createFromDate($year,$month,1);
        $endDate = Carbon::createFromDate($year,$month,1)->endOfMonth();

        return $this->loadData($startDate, $endDate, $type);
    }

    public function loadData($start, $end, $type)
    {  
        $verificationLimit = VerificationLimit::orderBy('created_at','DESC')->get(['level','monthly_widthdrawal_limit']);
        $usd_rate = self::$usd_rate;
        $users = User::with('transactions','nairaTrades','utilityTransaction')->get();

        $userData = ComplianceFraudResource::sortCollection($users, $start, $end, $usd_rate, $type, $verificationLimit);
        $userData = collect($userData)->sortByDesc('DebitAmount');

        $data = array();
        foreach($userData as $ud)
        {
           $data[] =  [
                'id' => $ud['id'],
                'signupDate' => $ud['signupDate'],
                'maximumWithdrawal' => $ud['maximumWithdrawal'],
                'DebitCount' => $ud['DebitCount'],
                'DebitAmount' => $ud['DebitAmount'],
                'CreditCount' => $ud['CreditCount'],
                'CreditAmount' => $ud['CreditAmount'],
                'VerificationLevel' => $ud['Verification'],
            ];
        }
        return response()->json([
            'success' => true,
            'usersData' => $data,
        ], 200);
    }
}
