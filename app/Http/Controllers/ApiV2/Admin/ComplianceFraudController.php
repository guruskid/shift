<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\CryptoRate;
use App\FlaggedTransactions;
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
        $users = User::with('transactions','nairaTrades','utilityTransaction','nairaWallet')->get();

        $userData = ComplianceFraudResource::sortCollection($users, $start, $end, $usd_rate, $type, $verificationLimit);
        $userData = collect($userData)->sortByDesc('DebitAmount');

        $data = array();
        foreach($userData as $ud)
        {
            if( $ud['Verification'] != 'not Verified' AND $ud['DebitCount'] > 0):
            $data[] =  [
                    'id' => $ud['id'],
                    'username' => $ud['username'],
                    'name' => $ud['name'],
                    'signupDate' => $ud['signupDate'],
                    'maximumWithdrawal' => number_format($ud['maximumWithdrawal']),
                    'DebitCount' => number_format($ud['DebitCount']),
                    'DebitAmount' => number_format($ud['DebitAmount']),
                    'CreditCount' => number_format($ud['CreditCount']),
                    'CreditAmount' => number_format($ud['CreditAmount']),
                    'VerificationLevel' => $ud['Verification'],
                    'AvailableBalance' => number_format($ud['AvailableBalance']),
                    'LedgerBalance' => number_format($ud['LedgerBalance']),
                ];
            endif;
        }
        return response()->json([
            'success' => true,
            'usersData' => $data,
        ], 200);
    }

    public function getUser($id)
    {
        $verificationLimit = VerificationLimit::orderBy('created_at','DESC')->get(['level','monthly_widthdrawal_limit']);
        $usd_rate = self::$usd_rate;
        $users = User::with('transactions','nairaTrades','utilityTransaction','nairaWallet')->where('id',$id)->get();

        $userData = ComplianceFraudResource::sortCollection($users, $users[0]->created_at, now(), $usd_rate, 'NGN', $verificationLimit);
        $userData = collect($userData)->sortByDesc('DebitAmount');

        $data = array();
        foreach($userData as $ud)
        {
            if( $ud['Verification'] != 'not Verified' AND $ud['DebitCount'] > 0):
            $data[] =  [
                    'id' => $ud['id'],
                    'username' => $ud['username'],
                    'name' => $ud['name'],
                    'signupDate' => $ud['signupDate'],
                    'maximumWithdrawal' => number_format($ud['maximumWithdrawal']),
                    'DebitCount' => number_format($ud['DebitCount']),
                    'DebitAmount' => number_format($ud['DebitAmount']),
                    'CreditCount' => number_format($ud['CreditCount']),
                    'CreditAmount' => number_format($ud['CreditAmount']),
                    'VerificationLevel' => $ud['Verification'],
                    'AvailableBalance' => number_format($ud['AvailableBalance']),
                    'LedgerBalance' => number_format($ud['LedgerBalance']),
                ];
            endif;
        }
        return response()->json([
            'success' => true,
            'usersData' => $data,
        ], 200);
    }

    public function flaggedTransactions()
    {
        $allFlaggedTransactions = FlaggedTransactions::with('naira_transaction','transaction','nairaTrade','user','accountant')->get();
        return $this->loadFlaggedTransaction($allFlaggedTransactions);
    }

    public function sortFlaggedTransaction(Request $request)
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
        $month = $request->month;
        $year = $request->year;

        $startDate = Carbon::createFromDate($year,$month,1);
        $endDate = Carbon::createFromDate($year,$month,1)->endOfMonth();

        $allFlaggedTransactions = FlaggedTransactions::with('naira_transaction','transaction','nairaTrade','user','accountant')
        ->where('created_at','>=',$startDate)->where('created_at','<=',$endDate)->get();
        return $this->loadFlaggedTransaction($allFlaggedTransactions);
    }

    public function loadFlaggedTransaction($allFlaggedTransactions)
    {
        $exportData = [];
        foreach($allFlaggedTransactions as $transaction){
            $verificationLevel = $this->verificationHelper($transaction->user);
            $max_withdrawal = $this->maximumLevelMonthlyWithdrawal($verificationLevel);
            if($transaction->type=='Bulk Credit'){
                $exportData[] = [
                    'id' => $transaction->id,
                    'name' => $transaction->user->first_name." ".$transaction->user->last_name,
                    'username' => $transaction->user->username,
                    'signUpDate' => $transaction->user->created_at->format('Y-m-d'),
                    'maxWithdrawal' => $max_withdrawal,
                    'TransactionType' => $transaction->transaction->card,
                    'AmountFlagged' => $transaction->transaction->amount_paid,
                    'Accountant' => isset($transaction->accountant) ? ($transaction->accountant->first_name." ".$transaction->accountant->last_name) : 'not Available',
                    'verification' => $verificationLevel,
                ];
            } else{
                $exportData[] = [
                    'id' => $transaction->id,
                    'name' => $transaction->user->first_name." ".$transaction->user->last_name,
                    'username' => $transaction->user->username,
                    'signUpDate' => $transaction->user->created_at->format('Y-m-d'),
                    'maxWithdrawal' => $max_withdrawal,
                    'TransactionType' => "PayBridge ".$transaction->nairaTrade->type,
                    'AmountFlagged' => $transaction->nairaTrade->amount,
                    'Accountant' => isset($transaction->accountant) ? ($transaction->accountant->first_name." ".$transaction->accountant->last_name) : 'not Available',
                    'verification' => $verificationLevel,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'flagged' => $exportData,
        ], 200);
    }

    public function verificationHelper($user)
    {
        $verificationLevel = 'not Verified';

        if($user->phone_verified_at != null AND $user->address_verified_at == null AND $user->idcard_verified_at == null)
        {
            $verificationLevel = 'Level 1';
        }
        if($user->phone_verified_at != null AND $user->address_verified_at != null AND $user->idcard_verified_at == null)
        {
            $verificationLevel = 'Level 2';
        }
        if($user->phone_verified_at != null AND $user->address_verified_at != null AND $user->idcard_verified_at != null)
        {
            $verificationLevel = 'Level 3';
        }

        return $verificationLevel;
    }

    public function maximumLevelMonthlyWithdrawal($verificationHelperData)
    {
        $verificationLimit = VerificationLimit::orderBy('created_at','DESC')->get(['level','monthly_widthdrawal_limit']);
        $levelNo = 0;
        $levelMonthlyWithdrawalLimit = 0;

        switch ($verificationHelperData) {
            case 'Level 1':
                $levelNo = 1;
                break;
            case 'Level 2':
                $levelNo = 2;
                break;
            case 'Level 3':
                $levelNo = 3;
                break;
            default:
                $levelNo = 0;
                break;
        }
        if($levelNo != 0) {
            $levelMonthlyWithdrawalLimit = $verificationLimit->where('level', $levelNo)->first()->monthly_widthdrawal_limit;
        }

        return $levelMonthlyWithdrawalLimit;
    }
}
