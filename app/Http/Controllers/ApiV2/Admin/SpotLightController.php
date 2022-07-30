<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\AccountantTimeStamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\SalesTimestamp;
use App\Ticket;
use App\Transaction;
use App\User;
use App\Verification;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;

class SpotLightController extends Controller {
    
    public function stats() {
        $today = Carbon::now();
        // User
        $avgDailySignups = User::whereDate(DB::raw('date(created_at)'), '<', $today->format('Y-m-d'))
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(date(created_at)) as total"))
            ->groupBy('date');
        $avgDailySignups = $avgDailySignups->get()->pluck('total')->toArray();
        $avgDailySignups = round(array_sum($avgDailySignups) / count($avgDailySignups));
        $dailySignups = User::whereDate(DB::raw('date(created_at)'), '=', $today->format('Y-m-d'))->count();
        $dailySignupsChange = round((($dailySignups - $avgDailySignups) / $avgDailySignups) * 100);

        // Transaction
        $avgDailyTransactions = NairaTransaction::where('status','success')
            ->whereDate(DB::raw('date(created_at)'), '<', $today->format('Y-m-d'))
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(date(created_at)) as total"))
            ->groupBy('date');
        $avgDailyTransactions = $avgDailyTransactions->get()->pluck('total')->toArray();
        $avgDailyTransactions = round(array_sum($avgDailyTransactions) / count($avgDailyTransactions));
        $dailyTransactions = NairaTransaction::whereDate(DB::raw('date(created_at)'), '=', $today->format('Y-m-d'))->count();
        $dailyTransactionChange = round((($dailyTransactions - $avgDailyTransactions) / $avgDailyTransactions) * 100);

        // Verification
        $avgDailyVerifications = Verification::where('status','success')
            ->whereDate(DB::raw('date(created_at)'), '<', $today->format('Y-m-d'))
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(date(created_at)) as total"))
            ->groupBy('date');
        $avgDailyVerifications = $avgDailyVerifications->get()->pluck('total')->toArray();
        $avgDailyVerifications = (count($avgDailyVerifications) > 0)? round(array_sum($avgDailyVerifications) / count($avgDailyVerifications )) : 0;
        $dailyVerifications = Verification::whereDate(DB::raw('date(created_at)'), '=', $today->format('Y-m-d'))->count();
        $dailyVerificationsChange = round((($dailyVerifications - $avgDailyVerifications) / $avgDailyVerifications) * 100);

        // Unique Users
        $avgDailyUniqueUsers = NairaTransaction::where('status','success')
            ->whereDate(DB::raw('date(created_at)'), '<', $today->format('Y-m-d'))
            ->select(DB::raw("user_id as user"),DB::raw("count(date(created_at)) as total"))
            ->groupBy('user_id');
        $avgDailyUniqueUsers = $avgDailyUniqueUsers->get()->pluck('total')->toArray();
        $avgDailyUniqueUsers = round(array_sum($avgDailyUniqueUsers) / count($avgDailyUniqueUsers));
        $dailyUniqueUsers = NairaTransaction::whereDate(DB::raw('date(created_at)'), '=', $today->format('Y-m-d'))->count();
        $dailyUniqueUsersChange = round((($dailyUniqueUsers - $avgDailyUniqueUsers) / $avgDailyUniqueUsers) * 100);

         // Volume
         $avgDailyVolume = Transaction::where('status','success')
            ->whereDate(DB::raw('date(created_at)'), '<', $today->format('Y-m-d'))
            ->select(DB::raw("date(created_at) as date"),DB::raw("sum(amount) as total"))
            ->groupBy('created_at')
            ;
        $avgDailyVolume = $avgDailyVolume->get()->pluck('total')->toArray();
        $avgDailyVolume = round(array_sum($avgDailyVolume) / count($avgDailyVolume));
        $dailyVolume = (int)Transaction::whereDate(DB::raw('date(created_at)'), '=', $today->format('Y-m-d'))->sum('amount');
        $dailyVolumeChange = round((($dailyVolume - $avgDailyVolume) / $avgDailyVolume) * 100);

        // DAU/MAU
        $dailyActiveUsers = NairaTransaction::where('status','success')
            ->whereDate(DB::raw('date(created_at)'), '<', $today->format('Y-m-d'))
            ->select(
                DB::raw("day(created_at) as date"),
            DB::raw("user_id as user"),DB::raw("count(date(created_at)) as total"))
            ->groupBy(['user_id'
            ,'date'
        ]);
        $dailyActiveUsers = $dailyActiveUsers->get()->pluck('total')->toArray();
        $dailyActiveUsers = round(array_sum($dailyActiveUsers) / count($dailyActiveUsers));
        $mDailyActiveUsers = (int)Transaction::whereDate(DB::raw('date(created_at)'), '=', $today->format('Y-m-d'))->count();
        $mDailyActiveUsersChange = round((($mDailyActiveUsers - $dailyActiveUsers) / $dailyActiveUsers) * 100);
        // return $mDailyActiveUsersChange;

        return response()->json([
            'success' => true,
            'data' => [
                'daily_signup' => [
                    'value' => $dailySignups,
                    'change' => $dailySignupsChange
                ],
                'daily_transaction' => [
                    'value' => $dailyTransactions,
                    'change' => $dailyTransactionChange
                ],
                'daily_verification' => [
                    'value' => $dailyVerifications,
                    'change' => $dailyVerificationsChange
                ],
                'daily_unique_users' => [
                    'value' => $dailyUniqueUsers,
                    'change' => $dailyUniqueUsersChange,
                ],
                'daily_volume' => [
                    'value' => $dailyVolume,
                    'change' => $dailyVolumeChange,
                ],
                'avg_daily_signup' => [
                    'value' => $avgDailySignups,
                    'change' => 0
                ],
                'avg_daily_transaction' => [
                    'value' => $avgDailyTransactions,
                    'change' => 0
                ],
                'avg_daily_verification' => [
                    'value' => $avgDailyVerifications,
                    'change' => 0
                ],
                'avg_daily_unique_users' => [
                    'value' => $avgDailyUniqueUsers,
                    'change' => 0
                ],
                'avg_daily_volume' => [
                    'value' => $avgDailyVolume,
                    'change' => 0
                ],
            ],
        ], 200);
    }

    public function recentTransactions() {
        $tranx = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            // ->join('naira_wallets', 'transactions.user_id', '=', 'naira_wallets.id')
            ->select('transactions.id','card as transaction','amount_paid as amount','transactions.amount as value',DB::raw('0 as prv_bal'),DB::raw('0 as cur_bal'),'transactions.status',DB::raw('date(transactions.created_at) as date','transactions.created_at as created_at'));
        $tranx2 = DB::table('naira_transactions')
            ->select('id','type as transaction','amount_paid','naira_transactions.amount as value','previous_balance as prv_bal','current_balance as cur_bal','naira_transactions.status',DB::raw('date(naira_transactions.created_at) as date','naira_transactions.created_at as created_at'));

        $mergeTbl = $tranx->unionAll($tranx2);
        DB::table(DB::raw("({$mergeTbl->toSql()}) AS mg"))->mergeBindings($mergeTbl);

        $tranx = $mergeTbl
        ->orderBy('date','desc')
        ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tranx
        ],200);
    }

    public function staffOnRole() {
        $acctn = User::where(['role' => 777, 'status' => 'active'])->with('nairaWallet')->first();
        $stamp = AccountantTimeStamp::where(['user_id' => $acctn->id])->latest()->first();
        $opening_balance = $stamp->opening_balance;
        
        $wtrade = NairaTrade::where(['status' => 'success','type'=> 'withdrawal'])
        ->whereBetween('updated_at',[$stamp->activeTime,Carbon::now()])
        ->get();

        $dtrade = NairaTrade::where(['status' => 'success','type'=> 'deposit'])
        ->whereBetween('updated_at',[$stamp->activeTime,Carbon::now()])
        ->get();

        $pending_withdrawal = NairaTrade::where(['status' => 'success','type'=> 'withdrawal']);
        $paid_out = $wtrade->sum('amount');
        $current_balance = $opening_balance - $paid_out;

        $saleRep = User::where(['role' => 556, 'status' => 'active'])->with('nairaWallet')->first();
        $saleTimeStamp = SalesTimestamp::where(['user_id' => $saleRep->id])->latest()->first();

        $tranx = Transaction::where(['status' => 'success'])
        ->whereBetween('updated_at',[$saleTimeStamp->activeTime,Carbon::now()]);

        $declinedTranx = Transaction::where(['status' => 'declined'])
        ->whereBetween('updated_at',[$saleTimeStamp->activeTime,Carbon::now()]);

        $customerHappiness = User::where(['role' => 555, 'status' => 'active'])->with('nairaWallet')->first();
        $ticketsWaiting = Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'open'])->count();
        $ticketsResolved = Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'closed'])->count();
        $ticketsUnresolved = Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'waiting'])->count();

        $tranx = Transaction::where(['status' => 'success'])
        ->whereBetween('updated_at',[$saleTimeStamp->activeTime,Carbon::now()]);

        $declinedTranx = Transaction::where(['status' => 'declined'])
        ->whereBetween('updated_at',[$saleTimeStamp->activeTime,Carbon::now()]);

        return response()->json([
            'success' => true,
            'data' => [
                'accountant' => self::accountantOnRole(),
                'sales_rep' => [
                    'staff_name' => $saleRep->first_name.' '.$saleRep->last_name,
                    'total_amount' => $tranx->sum('amount_paid'),
                    'successful_transaction' => $tranx->count(),
                    'declined_transaction' => $declinedTranx->count()
                ],
                'customer_happiness' => [
                    'staff_name' => $customerHappiness->first_name.' '.$customerHappiness->last_name,
                    'waiting' => $ticketsWaiting,
                    'resolved' => $ticketsResolved,
                    'unresolved' => $ticketsUnresolved
                ]
            ]
        ],200);
    }

    public static function accountantOnRole() {
        $acctn = User::where(['role' => 777, 'status' => 'active'])->with('nairaWallet')->first();
        $stamp = AccountantTimeStamp::where(['user_id' => $acctn->id])->latest()->first();
        $opening_balance = $stamp->opening_balance;

        $wtrade = NairaTrade::where(['status' => 'success','type'=> 'withdrawal'])
        ->whereBetween('updated_at',[$stamp->activeTime,Carbon::now()])
        ->get();

        $dtrade = NairaTrade::where(['status' => 'success','type'=> 'deposit'])
        ->whereBetween('updated_at',[$stamp->activeTime,Carbon::now()])
        ->get();

        $pending_withdrawal = NairaTrade::where(['status' => 'success','type'=> 'withdrawal']);
        $paid_out = $wtrade->sum('amount');
        $current_balance = $opening_balance - $paid_out;


        return [
            'staff_name' => $acctn->first_name.' '.$acctn->last_name,
            'opening_balance' => $opening_balance,
            'closing_balance' => 00,
            'total_paid_out' => [
                'amount' => $wtrade->sum('amount'),
                'count' => $wtrade->count()
            ],
            'total_deposit'  => [
                'amount' => $dtrade->sum('amount'),
                'count' => $dtrade->count()
            ],
            'current_balance' => $current_balance ,
            'pending_withdrawal' => [
                'amount' => $pending_withdrawal->sum('amount'),
                'count'  => $pending_withdrawal->count()
            ]
        ];
    }

    public function monthlyAnalytics(Request $request) {
        $year = $request['year'];
        $month = $request['month'];

        // for ($i=0; $i < $eom; $i++) { 
        //     $dateRange[] = $eod->format('Y-m-d');
        //     $eod->subDay();
        // }

        // return $dateRange;

        // return Carbon::create()->month($month)->startOfMonth()->year($year)->format('d/m/y').' '.Carbon::create()->month($month)->endOfMonth()->year($year)->format('d');
        // return Carbon::create()->startOfMonth()->month(2);
        // return Carbon::create()->month(04);

        // return Carbon::now()->format('m');
        // return Carbon::now()->startOfMonth() .' '. Carbon::now()->endOfMonth();
        
        $tranx = Transaction::where('status','success')
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(date(created_at)) as total"))
            ->where(DB::raw('month(created_at)'), '=', $month)
            ->where(DB::raw('year(created_at)'), '=', $year)
            ->groupBy('date')
            ->get()->toArray();

        $users = User::where('status','active')
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(date(created_at)) as total"))
            ->where(DB::raw('month(created_at)'), '=', $month)
            ->where(DB::raw('year(created_at)'), '=', $year)
            ->groupBy('date')
            ->get();

        $turn_over = Transaction::where('status','success')
            ->select(DB::raw("date(created_at) as date"),DB::raw("sum(amount_paid) as total"))
            ->where(DB::raw('month(created_at)'), '=', $month)
            ->where(DB::raw('year(created_at)'), '=', $year)
            ->groupBy('date')
            ->get();

        $unique_users = Transaction::where('status','success')
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(distinct user_id) as users"))
            // ->select(DB::raw("user_id as user"),DB::raw("count(user_id) as total"))
            ->where(DB::raw('month(created_at)'), '=', $month)
            ->where(DB::raw('year(created_at)'), '=', $year)
            ->groupBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'no_of_transactions' => $this->formatData($tranx,$month,$year),
                'turn_over'   => $this->formatData($turn_over,$month,$year),
                'new_users'   => $this->formatData($users,$month,$year),
                'unique_users' => $this->formatData($unique_users,$month,$year)
            ]
        ],200);
    }

    public function otherGraph(Request $request) {
        $year = $request['year'];
        $month = $request['month'];
        
        $tranx = Transaction::where('status','success')
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(date(created_at)) as total"))
            ->where(DB::raw('month(created_at)'), '=', $month)
            ->where(DB::raw('year(created_at)'), '=', $year)
            ->groupBy('date')
            ->get();

        $users = User::where('status','active')
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(date(created_at)) as total"))
            ->where(DB::raw('month(created_at)'), '=', $month)
            ->where(DB::raw('year(created_at)'), '=', $year)
            ->groupBy('date')
            ->get();

        $turn_over = Transaction::where('status','success')
            ->select(DB::raw("date(created_at) as date"),DB::raw("sum(amount_paid) as total"))
            ->where(DB::raw('month(created_at)'), '=', $month)
            ->where(DB::raw('year(created_at)'), '=', $year)
            ->groupBy('date')
            ->get();

        $unique_users = Transaction::where('status','success')
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(distinct user_id) as users"))
            // ->select(DB::raw("user_id as user"),DB::raw("count(user_id) as total"))
            ->where(DB::raw('month(created_at)'), '=', $month)
            ->where(DB::raw('year(created_at)'), '=', $year)
            ->groupBy('date')
            ->get();


        return response()->json([
            'success' => true,
            'data' => [
                'dead_users' => $this->formatData($tranx,$month,$year),
                'resurrected_users'   => $this->formatData($turn_over,$month,$year),
                'activation_rate'   => $this->formatData($users,$month,$year),
                'retained_users' => $this->formatData($unique_users,$month,$year)
            ]
        ],200);
    }

    public function getCustomerAcquisitionCost(Request $request) {
        $validate = Validator::make($request->all(), [
            'amount' => 'required|integer',
            'range' => 'required|in:quaterly,yearly'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $from = date('m');
        $range = $request['range'];

        if ($range == 'quaterly') {
            $from = $from - 3;
            if ($from < 1) {
                $from = 1;
            }
        }

        if ($range == 'yearly') {
            $from = 1;
        }

        $now = Carbon::now()->format('m');

        $total = User::whereBetween(DB::raw('month(created_at)'),[$from,$now])
            ->where(DB::raw('year(created_at)'),date('Y'))
            ->get()->count();

        $res = $request['amount'] / $total;

        return response()->json([
            'success' => true,
            'data' => $res
        ],200);
    }

    public static function getUsersByDays() {
        $range = 30;
        $chartData = User::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get()->toArray();
        $dateRange = [];
        $chartDataByDay = [];
        foreach ($chartData as $data) {
            $chartDataByDay[$data['date']] = $data['count'];
        }
        $date = new Carbon;
        for ($i = 0; $i < $range - 1; $i++) {
            $dateString = $date->format('Y-m-d');
            if (!isset($chartDataByDay[$dateString])) {
                $chartDataByDay[$dateString] = 0;
            }
           
            $dateRange[] = [
                'date' => $dateString,
                'count' => (!isset($chartDataByDay[$dateString]))? '0' : $chartDataByDay[$dateString],
                'days' => substr($date->format('l'), 0, 3),
                'date_day' => $date->format('d')
            ];
            $date->subDay();
        }
        return ($dateRange);
    }

    public function numberOfNewUsers() {
        return response()->json([
            'success' => true,
            'data' => self::getUsersByDays()
        ],200);
    }

    public function getNewUsersByDate() {
        $date = request('date');
        if (empty($date)) {
            $date = Carbon::now()->format('Y-m-d');   
        }

        $users = User::where(DB::raw('date(created_at)'),$date)->limit(10)->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ],200);
    }

    public function formatData($tranx,$month,$year) {
        $eod = Carbon::create()->month($month)->endOfMonth()->modify('0 month')->year($year);
        $eom = Carbon::create()->month($month)->endOfMonth()->year($year)->format('d');
        $chartData = [];
        $chartDataByDay = [];

        foreach ($tranx as $data) {
            $chartDataByDay[$data['date']] = $data['total'];
        }

        for ($i=0; $i < $eom; $i++) { 
            $dateString = $eod->format('Y-m-d');
            if (!isset($chartDataByDay[$dateString])) {
                $chartDataByDay[$dateString] = 0;
            }
            $eod->subDay();
        }

        ksort($chartDataByDay);

        foreach ($chartDataByDay as $key => $value) {
            $chartData[] = [
                'date' => $key,
                'value' => $value
            ];
        }
        return $chartData;
    }
}
