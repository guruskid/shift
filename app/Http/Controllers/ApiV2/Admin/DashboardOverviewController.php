<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\AccountantTimeStamp;
use App\Http\Controllers\Admin\UtilityTransactions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\NairaWallet;
use App\Ticket;
use App\Transaction;
use App\User;
use App\Wallet;
use App\CryptoRate;
use App\Http\Controllers\LiveRateController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ApiV2\Admin\ComplianceFraudResource;
use App\PayBridgeAccount;
use App\UtilityTransaction;
use App\VerificationLimit;

class DashboardOverviewController extends Controller
{

    private static $usd_rate;
    public function __construct()
    {
        self::$usd_rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
    }
    public function overview()

    {

        $walletTotal = NairaWallet::sum('amount');
        $customerHappiness = User::where(['role' => 555, 'status' => 'active'])->with('nairaWallet')->first();
        $opened = 0;
        $closed = 0;
        if ($customerHappiness) {
            $opened = Ticket::where(['agent_id' => $customerHappiness->id, 'status' => 'open'])->count();
            $closed = Ticket::where(['agent_id' => $customerHappiness->id, 'status' => 'closed'])->count();
        }

        $totalOpened = Ticket::where(['status' => 'open'])->count();
        $totalClosed = Ticket::where(['status' => 'closed'])->count();

        $aFn = (isset($customerHappiness)) ? $customerHappiness->first_name : null;
        $aLn = (isset($customerHappiness)) ? $customerHappiness->lastst_name : null;

        $customerHappinessAgent = $aFn . ' ' . $aLn;

        // $opened = ($customerHappiness) ? Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'open'])->count() : 0;
        // $closed =  ($customerHappiness) ? Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'closed'])->count() : 0;

        $totalOpened = Ticket::where(['status' => 'open'])->count();
        $totalClosed = Ticket::where(['status' => 'closed'])->count();



        $totalUser = User::where("status", "active")->count();
        $deadUsersCount = SpotLightController::dead_Users_one_month_Count();
        $ressurectedUser = SpotLightController::resurrected_one_month_Users_Count();

        $overview = [
            'users_naira_wallet' => number_format($walletTotal, '0', '.', ','),
            'user_pulse' => [
                'total_user_count' =>  $totalUser,
                'dead_users' =>  round(($deadUsersCount / $totalUser) * 100, 2),
                'resurrected_user' =>  round(($ressurectedUser / $totalUser) * 100, 2),
                'new_users' => SpotLightController::new_user_percentage_one_month()
            ],
            'customer_happiness' => [
                'staff_name' => $customerHappinessAgent,
                'opened_query' => $opened,
                'closed_query' => $closed,
                'total_opened_query' => $totalOpened,
                'total_closed_query' => $totalClosed
            ],
            'accountant' => [
                'currently_active' => SpotLightController::accountantOnRole(),
                'last_active' => self::lastAccountantOnRole()
            ],
        ];
        return response()->json([
            'success' => true,
            'data' => $overview
        ], 200);
    }

    public static function lastAccountantOnRole()
    {
        $stamp = AccountantTimeStamp::whereHas('user', function ($query) {
            $query->where('status', 'waiting');
        })->with('user')->latest()->first();

        $data = [
            'staff_name' => '--',
            'last_active' =>  '--',
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

        $acct_name = '';

        if ($stamp) {
            $acctn = $stamp->user;
            $acct_name = $acctn->first_name . ' ' . $acctn->last_name;

            $opening_balance = $stamp->opening_balance;

            $wtrade = NairaTrade::where(['status' => 'success', 'type' => 'withdrawal'])
                ->whereBetween('updated_at', [$stamp->activeTime, $stamp->inactiveTime])
                ->get();

            $dtrade = NairaTrade::where(['status' => 'success', 'type' => 'deposit'])
                ->whereBetween('updated_at', [$stamp->activeTime, $stamp->inactiveTime])
                ->get();

            $pending_withdrawal = NairaTrade::where(['status' => 'success', 'type' => 'withdrawal', 'agent_id' => $acctn->id]);
            $paid_out = $wtrade->sum('amount');
            $current_balance = $opening_balance - $paid_out;
            $closing_balance = $stamp->closing_balance;

            $last_active = ($stamp->inactiveTime) ? Carbon::createFromTimeString($stamp->inactiveTime)->diffForHumans() : '';

            $data['staff_name'] =  $acctn->first_name . ' ' . $acctn->last_name;
            $data['last_active'] =  $last_active;
            $data['opening_balance'] = number_format($opening_balance, 0, '.', ',');
            $data['closing_balance'] = number_format($closing_balance, 0, '.', ',');
            $data['total_paid_out'] = [
                'amount' => number_format($wtrade->sum('amount'), 0, '.', ','),
                'count' => number_format($wtrade->count(), 0, '.', ',')
            ];
            $data['total_deposit'] = [
                'amount' => number_format($dtrade->sum('amount'), 0, '.', ','),
                'count' => number_format($dtrade->count(), 0, '.', ',')
            ];
            $data['current_balance'] = number_format($current_balance, 0, '.', ',');
            $data['pending_withdrawal'] = [
                'amount' => number_format($pending_withdrawal->sum('amount'), 0, '.', ','),
                'count'  => number_format($pending_withdrawal->count(), 0, '.', ',')
            ];
        }
        return $data;
    }

    public function getTransactionHistory()
    {
        $range = 30;
        $chartData = Transaction::select([
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
                'count' => (!isset($chartDataByDay[$dateString])) ? '0' : $chartDataByDay[$dateString],
                'day' => substr($date->format('l'), 0, 3),
                'date_day' => $date->format('d')
            ];
            $date->subDay();
        }
        return ($dateRange);
    }

    private static function p2pTransactionHistoryDetails()
    {
        $data = NairaTrade::whereHas('naira_transactions')->with("naira_transactions", "user");
        // $data = NairaTrade::whereHas('naira_transactions')->where("created_at", Carbon::today())->with("naira_transactions", "user")->latest()->limit(10)->get();
        return $data;
    }

    public function p2pTransactionHistory()
    {

        $range = 30;
        $chartData = NairaTrade::select([
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
                'count' => (!isset($chartDataByDay[$dateString])) ? '0' : $chartDataByDay[$dateString],
                'day' => substr($date->format('l'), 0, 3),
                'date_day' => $date->format('d')
            ];
            $date->subDay();
        }
        return ($dateRange);
    }

    public function getP2pTransactionHistoryByDate()
    {
        $date = request('date');
        if (empty($date)) {
            $date = Carbon::now()->format('Y-m-d');
        }

        $Prptranx = NairaTrade::where(DB::raw('date(created_at)'), $date)
            ->with(['user', 'agent', 'naria_transaction'])
            ->latest()
            ->limit(10)
            ->get();

            $tranx = $Prptranx->map(function ($item) {

                return [
                      'reference' => $item->reference,
                    'amount' => $item->naira_transactions->amount,
                    "user" => $item->user->first_name . " " . $item->user->first_name,
                    "username" => $item->user->username,
                    "dp" => $item->user->dp,
                    "previous_balance" => $item->naira_transactions->previous_balance,
                    "current_balance" => $item->naira_transactions->current_balance,
                    "date" => $item->created_at,
                    "status"  => $item->status,
                    "type" => $item->type,
                    "accountant" => $item->agent->first_name . " " . $item->user->first_name
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tranx
        ], 200);
    }

    public function getCryptoTransactionHistoryByDate()
    {
        $date = request('date');
        if (empty($date)) {
            $date = Carbon::now()->format('Y-m-d');
        }

        $tranx = Transaction::where(DB::raw('date(created_at)'), $date)
            ->with('user')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tranx
        ], 200);
    }

    public function transactionHistory($type)
    {

        // $tranx = [];
        if ($type == 'p2p') {

            $data['count'] =    $this->p2pTransactionHistoryDetails()->latest()->limit(7)->get()
                ->groupBy(function($t) {
                    return $t->created_at->format('Y-m-d');
                })
                ->map(function($d) {
                    return count($d);
                });
            // $tranx = $this->p2pTransactionHistory();
            // start
            $p2pTrx =  $this->p2pTransactionHistoryDetails()->latest()->get();
            $data['tranx'] = $p2pTrx->map(function ($item) {

                return [
                      'reference' => $item->reference,
                    'amount' => $item->naira_transactions->amount,
                    "user" => $item->user->first_name . " " . $item->user->first_name,
                    "username" => $item->user->username,
                    "dp" => $item->user->dp,
                    "previous_balance" => $item->naira_transactions->previous_balance,
                    "current_balance" => $item->naira_transactions->current_balance,
                    "date" => $item->created_at,
                    "status"  => $item->status,
                    "type" => $item->type
                ];
            });
        } elseif ($type == 'crypto') {
            $cryptoTrx =
                Transaction::whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                })
                ->whereHas('naira_transactions', function ($query) {
                    $query->select('*');
                })
                ->with("naira_transactions", "user");

                $data['count'] =  $cryptoTrx->latest()->limit(7)->get()
                ->groupBy(function($t) {
                    return $t->created_at->format('Y-m-d');
                })
                ->map(function($d) {
                    return count($d);
                });
                $pt =  $cryptoTrx->get();

                $data['tranx'] = $pt->map(function ($item) {

                    return [
                          'reference' => $item->reference,
                        'amount' => $item->naira_transactions->amount,
                        "user" => $item->user->first_name . " " . $item->user->first_name,
                        "username" => $item->user->username,
                        "dp" => $item->user->dp,
                        "previous_balance" => $item->naira_transactions->previous_balance,
                        "current_balance" => $item->naira_transactions->current_balance,
                        "date" => $item->created_at,
                        "status"  => $item->status,
                        "type" => $item->type
                    ];
        });


            // $this->getTransactionHistory();
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function usersVerification($type = '')
    {
        $users = User::get();

        $l1 = User::whereNotNull('phone_verified_at');

        $l2 = User::whereHas('verifications', function ($query) {
            $query->where(['type' => 'Address', 'status' => 'success']);
        });

        $l3 = User::whereHas('verifications', function ($query) {
            $query->where(['type' => 'ID Card', 'status' => 'success']);
        });

        $pendingL2 = User::whereHas('verifications', function ($query) {
            $query->where(['type' => 'Address', 'status' => 'waiting']);
        });

        $pendingL3 = User::whereHas('verifications', function ($query) {
            $query->where(['type' => 'ID Card', 'status' => 'waiting']);
        });

        if ($type == 'month') {
            $l1->where(DB::raw('month(created_at)'), Carbon::now()->format('m'));
            $l2->where(DB::raw('month(created_at)'), Carbon::now()->format('m'));
            $l2->where(DB::raw('month(created_at)'), Carbon::now()->format('m'));
            $pendingL2->where(DB::raw('month(created_at)'), Carbon::now()->format('m'));
            $pendingL3->where(DB::raw('month(created_at)'), Carbon::now()->format('m'));
        }

        if ($type == 'week') {
            $l1->where(DB::raw('week(created_at)'), Carbon::now()->weekOfYear);
            $l2->where(DB::raw('week(created_at)'), Carbon::now()->weekOfYear);
            $l2->where(DB::raw('week(created_at)'), Carbon::now()->weekOfYear);
            $pendingL2->where(DB::raw('week(created_at)'), Carbon::now()->weekOfYear);
            $pendingL3->where(DB::raw('week(created_at)'), Carbon::now()->weekOfYear);
        }

        if ($type == 'day') {
            $l1->where(DB::raw('day(created_at)'), Carbon::now()->format('d'));
            $l2->where(DB::raw('day(created_at)'), Carbon::now()->format('d'));
            $l2->where(DB::raw('day(created_at)'), Carbon::now()->format('d'));
            $pendingL2->where(DB::raw('day(created_at)'), Carbon::now()->format('d'));
            $pendingL3->where(DB::raw('day(created_at)'), Carbon::now()->format('d'));
        }

        return response()->json([
            'success' => true,
            'data' => [
                'pending_verifications_l2' => $pendingL2->count(),
                'pending_verifications_l3' => $pendingL3->count(),
                'level_1' => [
                    'total_users' => $users->count(),
                    'verified_users' => $l1->count(),
                    'percentage' => round(($l1->count() / $users->count()) * 100, 2)
                ],
                'level_2' => [
                    'total_users' => $users->count(),
                    'verified_users' => $l2->count(),
                    'percentage' => round(($l2->count() / $users->count()) * 100, 2)
                ],
                'level_3' => [
                    'total_users' => $users->count(),
                    'verified_users' => $l3->count(),
                    'percentage' => round(($l3->count() / $users->count()) * 100, 2)
                ]
            ]
        ], 200);
    }

    public function monthlyEarnings()
    {
        $wk = request('wk');
        $month = request('month');
        $year = request('year');
        $now = Carbon::now()->format('m') - 1;
        $dtFrom = Carbon::now();
        $dtTo = Carbon::now();

        $from = $wk - 1;
        $to = $wk;

        $queryFrom = $dtFrom->year($year)->month($month)->startOfMonth()->addWeek($from)->weekday(0);
        $queryTo = $dtTo->year($year)->month($month)->startOfMonth()->addWeek($to);

        $range = 7;
        $chartDataOldUsers = Transaction::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('SUM(amount) AS value')
        ])
            ->whereHas('user', function ($query) {
                $query->where(DB::raw('DATE(created_at)'), '<', Carbon::now()->subMonth(3));
            })
            ->whereBetween(DB::raw('DATE(created_at)'), [$queryFrom->format('Y-m-d'), $queryTo->format('Y-m-d')])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->toArray();

        $chartDataNewUsers = Transaction::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('SUM(amount) AS value')
        ])
            ->whereHas('user', function ($query) {
                $query->where(DB::raw('DATE(created_at)'), '>', Carbon::now()->subMonth(3));
            })
            ->whereBetween(DB::raw('DATE(created_at)'), [$queryFrom->format('Y-m-d'), $queryTo->format('Y-m-d')])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->toArray();


        $dateRange = [];
        $chartDataByDayOldUsers = [];
        foreach ($chartDataOldUsers as $data) {
            $chartDataByDayOldUsers[$data['date']] = $data['value'];
        }

        $chartDataByDayNewUsers = [];
        foreach ($chartDataNewUsers as $data) {
            $chartDataByDayNewUsers[$data['date']] = $data['value'];
        }

        for ($i = 0; $i < $range; $i++) {
            $dateString = $queryFrom->format('Y-m-d');
            if (!isset($chartDataByDay[$dateString])) {
                $chartDataByDay[$dateString] = 0;
            }

            $dateRange[] = [
                'date' => $dateString,
                'old_users_turnover' => (!isset($chartDataByDayOldUsers[$dateString])) ? '0' : $chartDataByDayOldUsers[$dateString],
                'new_users_turnover' => (!isset($chartDataByDayNewUsers[$dateString])) ? '0' : $chartDataByDayNewUsers[$dateString],
                'day' => substr($queryFrom->format('l'), 0, 3),
                'date_day' => $queryFrom->format('d')
            ];
            $queryFrom->addDay();
        }

        return response()->json([
            'success' => true,
            'data' => $dateRange
        ], 200);
    }

    public function summary()
    {
        $month = request('month');
        $year = request('year');

        $billsTotal = UtilityTransaction::where('status', 'success')
            ->where(DB::raw('MONTH(created_at)'), $month)
            ->where(DB::raw('YEAR(created_at)'), $year)
            ->count();

        $gcTranx = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->where('status', 'success')
            ->where(DB::raw('MONTH(created_at)'), $month)
            ->where(DB::raw('YEAR(created_at)'), $year)
            ->count();

        $cryptoTranx = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where('status', 'success')
            ->where(DB::raw('MONTH(created_at)'), $month)
            ->where(DB::raw('YEAR(created_at)'), $year)
            ->count();

        $tranx = Transaction::where('status', 'success')
            ->where(DB::raw('MONTH(created_at)'), $month)
            ->where(DB::raw('YEAR(created_at)'), $year)
            ->count();

        $uTranx = UtilityTransaction::where('status', 'success')
            ->where(DB::raw('MONTH(created_at)'), $month)
            ->where(DB::raw('YEAR(created_at)'), $year)
            ->count();

        $tranxTurnover = Transaction::where('status', 'success')
            ->where(DB::raw('MONTH(created_at)'), $month)
            ->where(DB::raw('YEAR(created_at)'), $year)
            ->sum('amount');

        $uTranxTurnover = UtilityTransaction::where('status', 'success')
            ->where(DB::raw('MONTH(created_at)'), $month)
            ->where(DB::raw('YEAR(created_at)'), $year)
            ->count('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'atleast_one_tranx' => number_format($tranx + $billsTotal, 0, '.', ','),
                'successful_tranx' => number_format($tranx + $uTranx, 0, '.', ','),
                'successful_crypto_tranx' => number_format($cryptoTranx, 0, '.', ','),
                'montly_turnover' => number_format($uTranxTurnover + $tranxTurnover, 0, '.', ','),
                'successful_giftcard_tranx' => number_format($gcTranx, 0, '.', ','),
                'total_number_of_bills_payment' => number_format($billsTotal, 0, '.', ',')
            ]
        ], 200);
    }


    public function paybridgeTransactions()
    {
        $data['paybridge_transactions'] =  NairaTransaction::select('user_id', "current_balance", "previous_balance", "status", 'type', 'amount_paid', 'amount', 'created_at')->whereHas("user")->with(['user' => function ($query) {
            $query->select("id", "first_name", "last_name", "username");
        }])->latest()->take(3)->get();

        // $data['compliance'] = ComplianceFraudController::index();



        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function recentTransactions()
    {




        $tranx = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select('first_name', 'last_name', 'username', 'dp', 'transactions.id', 'user_id', 'card as transaction', 'amount_paid as amount', 'transactions.amount as value', DB::raw('0 as prv_bal'), DB::raw('0 as cur_bal'), 'transactions.status', DB::raw('date(transactions.created_at) as date', 'transactions.created_at as created_at'));
        $tranx2 = DB::table('naira_transactions')
            ->join('users', 'naira_transactions.user_id', '=', 'users.id')
            ->select('first_name', 'last_name', 'username', 'dp', 'naira_transactions.id', 'user_id', 'type as transaction', 'amount_paid', 'naira_transactions.amount as value', 'previous_balance as prv_bal', 'current_balance as cur_bal', 'naira_transactions.status', DB::raw('date(naira_transactions.created_at) as date', 'naira_transactions.created_at as created_at'));

        $mergeTbl = $tranx->unionAll($tranx2);
        DB::table(DB::raw("({$mergeTbl->toSql()}) AS mg"))->mergeBindings($mergeTbl);

        $tranx = $mergeTbl
            ->orderBy('date', 'desc')
            ->take(3)->get();

        return response()->json([
            'success' => true,
            'data' => $tranx,
        ], 200);
    }


    public function complianceFraud()
    {
        $type =  'NGN';
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        return $this->loadData($startDate, $endDate, $type);
    }

    private function loadData($start, $end, $type)
    {
        $verificationLimit = VerificationLimit::orderBy('created_at', 'DESC')->get(['level', 'monthly_widthdrawal_limit']);
        $usd_rate = self::$usd_rate;
        $users = User::with('transactions', 'nairaTrades', 'utilityTransaction')->get();

        $userData = ComplianceFraudResource::sortCollection($users, $start, $end, $usd_rate, $type, $verificationLimit);
        $userData = collect($userData)->sortByDesc('id')->take(5);

        $data = array();
        foreach ($userData as $ud) {
            $data[] =  [

                'id' => $ud['id'],
                'name' => $ud['name'],
                "username" => $ud['username'],
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

    public function juniorAccountantSummary(Request $req)
    {
        $data['total_deposit'] = NairaTransaction::whereIn('status', ['success', 'pending'])->where('transaction_type_id', 2)->sum('amount');
        $data['total_withdrawal'] =  NairaTransaction::whereIn('status', ['success', 'pending'])->where('transaction_type_id', 3)->sum('amount');
        $data['balance'] = NairaWallet::sum('amount');


        if ($req->usd) {
            $data['current_rate'] = LiveRateController::usdtRate();
        } else {
            $data['current_rate'] = LiveRateController::usdNgn();
        }
        $data['active_accountant'] =  $accountant = User::select('first_name', 'last_name', 'role', 'status')->where("status", "active")->whereHas("accountantTimestamp")->first();

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
