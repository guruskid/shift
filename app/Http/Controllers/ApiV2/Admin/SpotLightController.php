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

    public function newRecentTransactions(){
        $data = Transaction::whereHas("naira_transactions")->with('naira_transactions')->latest()->paginate(20);

        return response()->json($data);


    }



    public function recentTransactions() {
        $tranx = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            // ->join('naira_wallets', 'transactions.user_id', '=', 'naira_wallets.id')
            ->select('first_name','last_name','username','dp','transactions.id','user_id','card as transaction','amount_paid as amount','transactions.amount as value',DB::raw('0 as prv_bal'),DB::raw('0 as cur_bal'),'transactions.status',DB::raw('date(transactions.created_at) as date','transactions.created_at as created_at'))
            ;
        $tranx2 = DB::table('naira_transactions')
            ->join('users', 'naira_transactions.user_id', '=', 'users.id')
            ->select('first_name','last_name','username','dp','naira_transactions.id','user_id','type as transaction','amount_paid','naira_transactions.amount as value','previous_balance as prv_bal','current_balance as cur_bal','naira_transactions.status',DB::raw('date(naira_transactions.created_at) as date','naira_transactions.created_at as created_at'));

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
        $saleTimeStamp = null;
        $repFn = '';
        $repLn = '';

        if (!empty($saleRep)) {
            $saleTimeStamp = SalesTimestamp::whereHas("user")->where(['user_id' => $saleRep->id])->latest()->first();
            $repFn = $saleRep->first_name;
            $repLn = $saleRep->last_name;
        }

        $ticketsWaiting = 0;
        $ticketsResolved = 0;
        $ticketsUnresolved = 0;
        // $saleTimeStamp = SalesTimestamp::whereHas("user")->where(['user_id' => $saleRep->id])->latest()->first();
        $tranx = "";
        $declinedTranx = "";
        if(!empty($saleRep)){
            $tranx = Transaction::where(['status' => 'success'])
            ->whereBetween('updated_at',[$saleTimeStamp->activeTime,Carbon::now()]);


            $declinedTranx = Transaction::where(['status' => 'declined'])
            ->whereBetween('updated_at',[$saleTimeStamp->activeTime,Carbon::now()]);
        }




        $customerHappiness = User::where(['role' => 555, 'status' => 'active'])->with('nairaWallet')->first();
        if ($customerHappiness) {
            $ticketsWaiting = Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'open'])->count();
            $ticketsResolved = Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'closed'])->count();
            $ticketsUnresolved = Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'waiting'])->count();
        }
        $aFn = (isset($customerHappiness)) ? $customerHappiness->first_name : null;
        $aLn = (isset($customerHappiness)) ? $customerHappiness->lastst_name : null;
        $customerHappinessAgent = $aFn.' '.$aLn;

        $tranx =  0;
        $declinedTranx = 0;
        $saleTotalAmt = 0;

        if ($saleTimeStamp) {
            $saleTotalAmt = Transaction::where(['status' => 'success'])
            ->whereBetween('updated_at',[$saleTimeStamp->activeTime,Carbon::now()])->sum('amount_paid');

            $tranx = Transaction::where(['status' => 'success'])
            ->whereBetween('updated_at',[$saleTimeStamp->activeTime,Carbon::now()])->count();

            $declinedTranx = Transaction::where(['status' => 'declined'])
            ->whereBetween('updated_at',[$saleTimeStamp->activeTime,Carbon::now()])->count();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'accountant' => self::accountantOnRole(),
                'sales_rep' => [
                    'staff_name' => $repFn.' '.$repLn,
                    'total_amount' => $saleTotalAmt,
                    'successful_transaction' => $tranx,
                    'declined_transaction' => $declinedTranx
                ],
                'customer_happiness' => [
                    'staff_name' => $customerHappinessAgent,
                    'waiting' => $ticketsWaiting,
                    'resolved' => $ticketsResolved,
                    'unresolved' => $ticketsUnresolved
                ]
            ]
        ],200);
    }

    public static function accountantOnRole() {
        $acctn = User::where(['role' => 777, 'status' => 'active'])->with('nairaWallet')->first();
        $data = [
            'staff_name' => '--',
            'opening_balance' => '--',
            'closing_balance' => '--',
            'total_paid_out' => [
                'amount' => '--',
                'count' => '--'
            ],
            'total_deposit'  => [
                'amount' => '--',
                'count' => '--'
            ],
            'current_balance' => '--',
            'pending_withdrawal' => [
                'amount' => '--',
                'count'  => '--'
            ]
        ];

        if ($acctn) {
            $stamp = AccountantTimeStamp::where(['user_id' => $acctn->id])->latest()->first();

            $opening_balance = $stamp->opening_balance;
            $closing_balance = $stamp->closing_balance;

            $wtrade = NairaTrade::where(['status' => 'success','type'=> 'withdrawal'])
                ->whereBetween('updated_at',[$stamp->activeTime,Carbon::now()])
                ->get();

            $dtrade = NairaTrade::where(['status' => 'success','type'=> 'deposit'])
                ->whereBetween('updated_at',[$stamp->activeTime,Carbon::now()])
                ->get();

            $pending_withdrawal = NairaTrade::where(['status' => 'pending','type'=> 'withdrawal']);
            $paid_out = $wtrade->sum('amount');
            $current_balance = $opening_balance - $paid_out;

            $data['staff_name'] =  $acctn->first_name.' '.$acctn->last_name;
            $data['email'] = $acctn->email;
            $data['mobile_number'] = $acctn->phone;
            $data['opening_balance'] = number_format($opening_balance,0,'.',',');
            $data['closing_balance'] = number_format($closing_balance,0,'.',',');
            $data['total_paid_out'] = [
                'amount' => number_format($wtrade->sum('amount'),0,'.',','),
                'count' => number_format($wtrade->count(),0,'.',',')
            ];
            $data['total_deposit'] = [
                'amount' => number_format($dtrade->sum('amount'),0,'.',','),
                'count' => number_format($dtrade->count(),0,'.',',')
            ];
            $data['current_balance'] = number_format($current_balance,0,'.',',');
            $data['pending_withdrawal'] = [
                'amount' => number_format($pending_withdrawal->sum('amount'),0,'.',','),
                'count'  => number_format($pending_withdrawal->count(),0,'.',',')
            ];
        }

        return $data;
    }

    public function monthlyAnalyticss(Request $request) {
        $year = $request['year'];
        $month = $request['month'];

        // $pending_withdrawal = NairaTrade::where(['status' => 'success','type'=> 'withdrawal']);
        // $paid_out = $wtrade->sum('amount');
        // $current_balance = $opening_balance - $paid_out;


        // return [
        //     'staff_name' => $acctn->first_name.' '.$acctn->last_name,
        //     'opening_balance' => $opening_balance,
        //     'closing_balance' => 00,
        //     'total_paid_out' => [
        //         'amount' => $wtrade->sum('amount'),
        //         'count' => $wtrade->count()
        //     ],
        //     'total_deposit'  => [
        //         'amount' => $dtrade->sum('amount'),
        //         'count' => $dtrade->count()
        //     ],
        //     'current_balance' => $current_balance ,
        //     'pending_withdrawal' => [
        //         'amount' => $pending_withdrawal->sum('amount'),
        //         'count'  => $pending_withdrawal->count()
        //     ]
        // ];
    }

    public function monthlyAnalytics(Request $request) {
        $year = $request['year'];
        $month = $request['month'];

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
            ->select(DB::raw("date(created_at) as date"),DB::raw("count(distinct user_id) as total"))
            // ->select(DB::raw("user_id as user"),DB::raw("count(user_id) as total"))
            ->where(DB::raw('month(created_at)'), '=', $month)
            ->where(DB::raw('year(created_at)'), '=', $year)
            ->groupBy('date')
            ->get();

        $bigData = [];

        $tranx = $this->formatData($tranx,$month,$year);
        $turn_over = $this->formatData($turn_over,$month,$year);
        $new_users = $this->formatData($users,$month,$year);
        $unique_users = $this->formatData($unique_users,$month,$year);

        for ($i=0; $i < count($tranx ); $i++) {
            $bigData[] = [
                'date' => $tranx[$i]['date'],
                'no_of_transactions' => $tranx[$i]['value'],
                'turn_over' => $turn_over[$i]['value'],
                'new_users' => $new_users[$i]['value'],
                'unique_users' => $unique_users[$i]['value']
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $bigData
        ],200);
    }

    public function graphAnalytics(Request $request) {
        $type = $request['type'];
        $bigData = [];

        if ($type == 'monthly') {
            $year = $request['year'];

            $soy = Carbon::createFromFormat('m',$request['month'])->year($year);
            for ($i=0; $i < 12; $i++) {
                $tx = Transaction::where('status','success')
                    ->where(DB::raw('month(created_at)'), '=', $soy->format('m'))
                    ->where(DB::raw('year(created_at)'), '=', $year)
                    ->count();

                $users = User::where('status','active')
                    ->where(DB::raw('month(created_at)'), '=', $soy->format('m'))
                    ->where(DB::raw('year(created_at)'), '=', $year)->count();

                $unique_users = Transaction::where('status','success')
                    ->select(DB::raw("month(created_at) as date"),DB::raw("count(distinct user_id) as total"))
                    ->where(DB::raw('month(created_at)'), '=', $soy->format('m'))
                    ->where(DB::raw('year(created_at)'), '=', $year)->first()->total;

                $bigData[] = [
                    'x_tick' => $soy->format('M'),
                    'no_of_transactions' => $tx,
                    'new_users' => $users,
                    'unique_users' => $unique_users
                ];
                $soy->addMonth();
            }
        }

        if ($type == 'day') {
            $days = $request['days'] - 1;
            $now = Carbon::now();
            $period = 1000;

            if ($days < 1) {
                $days = 1;
                $period = $period / 2;
            }

            $ticks = ceil($period/$days);

            for ($i=0; $i < $ticks; $i++) {
                if ($i > 0) {
                    $now->subDay();
                }
                $frmd = $now->format('Y-m-d');
                $from = $now->format('jS M');

                if ($days < 2) {
                    $now->subDays($days);
                }else{
                    $now->subDays($days + 1);
                }

                $tod = $now->format('Y-m-d');
                $to = $now->format('jS M');
                $tick = $to.' - '.$from;

                $tx = Transaction::where('status','success')
                    ->whereBetween('created_at',[$tod,$frmd])
                    ->count();

                $users = User::where('status','active')
                    ->whereBetween('created_at',[$tod,$frmd])
                    ->count();

                $unique_users = Transaction::where('status','success')
                    ->select(DB::raw("month(created_at) as date"),DB::raw("count(distinct user_id) as total"))
                    ->whereBetween('created_at',[$tod,$frmd])
                    ->first()->total;

                $bigData[] = [
                    'x_tick' => $tick,
                    'no_of_transactions' => $tx,
                    'new_users' => $users,
                    'unique_users' => $unique_users
                ];
            }

            $bigData = array_reverse($bigData);
        }

        if ($type == 'quarterly') {
            $startFrom = Carbon::createFromFormat('Y-m',$request['month']);
            $period = 100;

            for ($i=0; $i < 10; $i++) {
                if ($i > 0) {
                    $startFrom->subMonth();
                }
                $from = $startFrom->format('M Y');
                $to = $startFrom->subMonth(2)->format('M Y');
                $tick = $from.' - '.$to;

                $tx = Transaction::where('status','success')
                    ->whereBetween(DB::raw('date(created_at)'),[$startFrom->subMonth(2)->format('Y-m-d'),$startFrom->format('Y-m-d')])
                    ->count();

                $users = User::where('status','active')
                    ->whereBetween(DB::raw('date(created_at)'),[$startFrom->subMonth(2)->format('Y-m-d'),$startFrom->format('Y-m-d')])
                    ->count();

                $unique_users = Transaction::where('status','success')
                    ->select(DB::raw("month(created_at) as date"),DB::raw("count(distinct user_id) as total"))
                    ->whereBetween(DB::raw('date(created_at)'),[$startFrom->subMonth(2)->format('Y-m-d'),$startFrom->format('Y-m-d')])
                    ->first()->total;

                $bigData[] = [
                    'x_tick' => $tick,
                    'no_of_transactions' => $tx,
                    'new_users' => $users,
                    'unique_users' => $unique_users
                ];
            }

        }

        if ($type == 'year') {
            $year = $request['year'];
            $startFrom = Carbon::createFromFormat('Y',$request['year']);
            $period = 100;

            for ($i=0; $i < 10; $i++) {
                $tx = Transaction::where('status','success')
                    ->where(DB::raw('year(created_at)'),$startFrom->format('Y'))
                    ->count();

                $users = User::where('status','active')
                    ->where(DB::raw('year(created_at)'),$startFrom->format('Y'))
                    ->count();

                $unique_users = Transaction::where('status','success')
                    ->select(DB::raw("month(created_at) as date"),DB::raw("count(distinct user_id) as total"))
                    ->where(DB::raw('year(created_at)'),$startFrom->format('Y'))
                    ->first()->total;

                $bigData[] = [
                    'x_tick' => $startFrom->format('Y'),
                    'no_of_transactions' => $tx,
                    'new_users' => $users,
                    'unique_users' => $unique_users
                ];
                $startFrom->subYear();
            }

        }

        return response()->json([
            'success' => true,
            'data' => $bigData
        ],200);
    }

    public function turnOverGraphAnalytics(Request $request) {
        $type = $request['type'];
        $bigData = [];


        if ($type == 'monthly') {
            $year = $request['year'];

            // $soy = Carbon::now()->startOfYear();
            $soy = Carbon::createFromFormat('m',$request['month'])->year($year);


            for ($i=0; $i < 12; $i++) {
                $turn_over_dollar = Transaction::where('status','success')
                    ->select(DB::raw("date(created_at) as date"),DB::raw("sum(amount) as total"))
                    ->where(DB::raw('month(created_at)'), '=', $soy->format('m'))
                    ->where(DB::raw('year(created_at)'), '=', $year)
                    ->groupBy('date')
                    ->first();

                    $turn_over_naira = Transaction::where('status','success')
                    ->select(DB::raw("date(created_at) as date"),DB::raw("sum(amount_paid) as total"))
                    ->where(DB::raw('month(created_at)'), '=', $soy->format('m'))
                    ->where(DB::raw('year(created_at)'), '=', $year)
                    ->groupBy('date')
                    ->first();

                $bigData[] = [
                    'x_tick' => $soy->format('M'),
                    'turn_over_dollar' => (isset($turn_over_dollar->total)) ? $turn_over_dollar->total : 0,
                    'turn_over_naira' => (isset($turn_over_naira->total)) ? $turn_over_naira->total : 0
                ];
                $soy->addMonth();
            }
        }

        if ($type == 'day') {
            $days = $request['days'] - 1;
            $now = Carbon::now();
            $period = 1000;
            $ticks = ceil($period/$days);

            for ($i=0; $i < $ticks; $i++) {
                if ($i > 0) {
                    $now->subDay();
                }
                $frmd = $now->format('Y-m-d');
                $from = $now->format('jS M');
                $now->subDays($days + 1);
                $tod = $now->format('Y-m-d');
                $to = $now->format('jS M');
                $tick = $to.' - '.$from;

                $turn_over_dollar = Transaction::where('status','success')
                    ->select(DB::raw("date(created_at) as date"),DB::raw("sum(amount) as total"))
                    ->whereBetween('created_at',[$tod,$frmd])
                    ->groupBy('date')
                    ->first();

                $turn_over_naira = Transaction::where('status','success')
                ->select(DB::raw("date(created_at) as date"),DB::raw("sum(amount_paid) as total"))
                ->whereBetween('created_at',[$tod,$frmd])
                ->groupBy('date')
                ->first();

                $bigData[] = [
                    'x_tick' => $tick,
                    'turn_over_dollar' => (isset($turn_over_dollar->total)) ? $turn_over_dollar->total : 0,
                    'turn_over_naira' => (isset($turn_over_naira->total)) ? $turn_over_naira->total : 0
                ];
            }

            $bigData = array_reverse($bigData);
        }

        if ($type == 'quarterly') {
            $startFrom = Carbon::createFromFormat('Y-m',$request['month']);
            $period = 100;

            for ($i=0; $i < 10; $i++) {
                if ($i > 0) {
                    $startFrom->subMonth();
                }
                $from = $startFrom->format('M Y');
                $to = $startFrom->subMonth(2)->format('M Y');
                $tick = $from.' - '.$to;

                $turn_over_dollar = Transaction::where('status','success')
                    ->select(DB::raw("date(created_at) as date"),DB::raw("sum(amount) as total"))
                    ->whereBetween(DB::raw('date(created_at)'),[$startFrom->subMonth(2)->format('Y-m-d'),$startFrom->format('Y-m-d')])
                    ->groupBy('date')
                    ->first();

                    $turn_over_naira = Transaction::where('status','success')
                    ->select(DB::raw("date(created_at) as date"),DB::raw("sum(amount_paid) as total"))
                    ->whereBetween(DB::raw('date(created_at)'),[$startFrom->subMonth(2)->format('Y-m-d'),$startFrom->format('Y-m-d')])
                    ->groupBy('date')
                    ->first();

                $bigData[] = [
                    'x_tick' => $tick,
                    'turn_over_dollar' => (isset($turn_over_dollar->total)) ? $turn_over_dollar->total : 0,
                    'turn_over_naira' => (isset($turn_over_naira->total)) ? $turn_over_naira->total : 0
                ];
            }

        }

        if ($type == 'year') {
            $year = $request['year'];
            $startFrom = Carbon::createFromFormat('Y',$request['year']);
            $period = 100;

            for ($i=0; $i < 10; $i++) {
                $turn_over_dollar = Transaction::where('status','success')
                    ->select(DB::raw("year(created_at) as date"),DB::raw("sum(amount_paid) as total"))
                    ->where(DB::raw('year(created_at)'),$startFrom->format('Y'))
                    ->groupBy('date')
                    ->first();

                    $turn_over_naira = Transaction::where('status','success')
                    ->select(DB::raw("year(created_at) as date"),DB::raw("sum(amount) as total"))
                    ->where(DB::raw('year(created_at)'),$startFrom->format('Y'))
                    ->groupBy('date')
                    ->first();

                $bigData[] = [
                    'x_tick' => $startFrom->format('Y'),
                    'turn_over_dollar' => (isset($turn_over_dollar->total)) ? $turn_over_dollar->total : 0,
                    'turn_over_naira' => (isset($turn_over_naira->total)) ? $turn_over_naira->total : 0
                ];
                $startFrom->subYear();
            }

        }

        return response()->json([
            'success' => true,
            'data' => $bigData
        ],200);
    }

    public static function deadUsersCount() {
        $users_no_transactions = User::doesnthave('transactions')->count();
        $dead_users = User::where('status','active')
            ->with(['transactions' => function ($query) {
                $query->where(DB::raw('date(created_at)'),'<',Carbon::now()->subMonth(6)->format('Y-m-d'));
            }])->count();
        return $users_no_transactions + $dead_users;
    }

    public static function dead_Users_one_month_Count(){
        $users = User::where("status", "active")
        ->whereDoesntHave('transactions', function ($query){
            $query->where(DB::raw('date(created_at)'),'<',Carbon::now()->subMonth(1)->format('Y-m-d'));

        })

        ->count();
        return $users;
    }

    public static function new_user_percentage_one_month(){
        $differenceInpercentage = 0;
        $previousMonthUsers = User::where("status", "active")->whereMonth('created_at', now()->month - 1)->count();
        $thisMonthUsers = User::where("status", "active")->whereMonth('created_at', now()->month)->count();
if ($previousMonthUsers > 0) {
// If it has decreased then it will give you a percentage with '-'
$differenceInpercentage = ($thisMonthUsers - $previousMonthUsers) * 100 / $previousMonthUsers;
} else {
$differenceInpercentage = $thisMonthUsers > 0 ? '100%' : '0%';
}
return $differenceInpercentage;

    }

    public static function resurrectedUsersCount() {
        $users1 = User::whereHas('transactions', function ($query) {
                $query->whereNotBetween('created_at',[Carbon::now()->subMonth(6),Carbon::now()->subMonth(1)]);
            })->pluck('id');

        $users2 = User::whereHas('transactions', function ($query) {
            $query->where(DB::raw('date(created_at)'),Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('date(created_at)'),'=',Carbon::now()->format('Y-m-d'));
        })->whereIn('id',$users1)
        ->count();
        return $users2;
    }

    public static function resurrected_one_month_Users_Count() {
        $users1 = User::whereHas('transactions', function ($query) {
                $query->whereNotBetween('created_at',[Carbon::now()->subMonth(2),Carbon::now()->subMonth(1)]);
            })->pluck('id');

        $users2 = User::whereHas('transactions', function ($query) {
            $query->where(DB::raw('date(created_at)'),Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('date(created_at)'),'=',Carbon::now()->format('Y-m-d'));
        })->whereIn('id',$users1)
        ->count();
        return $users2;
    }

    public function otherGraph(Request $request) {
        $year = $request['year'];
        $month = $request['month'];
        $dead_users = self::deadUsersCount();
        $resurrected_userss = self::resurrectedUsersCount();

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

        $tranx = $this->formatData($tranx,$month,$year);
        $resurrected_users = $this->formatData($turn_over,$month,$year);
        $activation_rate = $this->formatData($users,$month,$year);
        $unique_users = $this->formatData($unique_users,$month,$year);

        $bigData = [];

        for ($i=0; $i < count($tranx ); $i++) {
            $bigData[] = [
                'date' => $tranx[$i]['date'],
                'dead_users' => $tranx[$i]['value'],
                'resurrected_users' => $resurrected_users[$i]['value'],
                'activation_rate' => $activation_rate[$i]['value'],
                'retained_users' => $unique_users[$i]['value']
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $bigData
        ],200);
    }

    public function getCustomerAcquisitionCost(Request $request) {
        // search types
        // -- Range, Days, Monthly, Quarterly, Year
        $totalUsers = User::whereNotNull('id');
        $type = $request['type'];

        if ($type == 'yearly') {
            $year = request('year');
            $amount = request('amount');
            $totalUsers = $totalUsers->where(DB::raw('year(created_at)'),$year);
        }

        if ($type == 'range') {
            $from = request('from');
            $to = request('to');
            $amount = request('amount');
            $totalUsers = $totalUsers->whereBetween(DB::raw('date(created_at)'),[$from,$to]);
        }

        if ($type == 'days') {
            $days = request('days');
            $amount = request('amount');
            $from = Carbon::now()->subDay($days)->format('Y-m-d');
            $to = Carbon::now()->format('Y-m-d');
            $totalUsers = $totalUsers->whereBetween(DB::raw('date(created_at)'),[$from,$to]);
        }

        if ($type == 'monthly') {
            $date = request('date');
            $d = Carbon::createFromDate($date);
            $month = $d->format('m');
            $year = $d->format('Y');
            $totalUsers = $totalUsers->where(DB::raw('month(created_at)'),$month)
                ->where(DB::raw('year(created_at)'),$year);
        }

        if ($type == 'quarterly') {
            $date = request('date');
            $monthFrom = Carbon::createFromDate($date)->subMonth(2);
            $monthTo = Carbon::createFromDate($date);
            $totalUsers = $totalUsers->whereBetween(DB::raw('date(created_at)'),[$monthFrom,$monthTo]);
        }

        $total = $totalUsers->count();

        $data = $request['amount'] / $total;

        return response()->json([
            'success' => true,
            'data' => $data
        ],200);
    }

    public function getCustomerAcquisitionCostByYear() {
       $year = request('year');
       $amount = request('amount');

       return Carbon::now()->year()->format('m');

       $total = User::whereBetween(DB::raw('year(created_at)'),[2001,2022])
            ->get()->count();

        $res = $amount / $total;

       return $year.' '.$amount.' '.date('Y').' '.$res;
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

        $users = User::where(DB::raw('date(created_at)'),$date)->latest()->limit(10)->get();

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

    public function globalSearch(){

        $item = request("search");
        $transaction = Transaction::query()->select("id", "user_email AS email", "card AS name", "created_at")->where("card", 'LIKE', "%".$item."%");
        $user = User::query()->select("id", "email", "first_name AS name", "created_at")->where("first_name", "LIKE", "%".$item."%");
        $result = $user->union($transaction)->latest()->paginate(25);
        return response()->json($result);
    }
}
