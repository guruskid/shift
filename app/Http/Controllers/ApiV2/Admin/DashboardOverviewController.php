<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\AccountantTimeStamp;
use App\Http\Controllers\Admin\UtilityTransactions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaWallet;
use App\Ticket;
use App\Transaction;
use App\User;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\UtilityTransaction;

class DashboardOverviewController extends Controller {
    public function overview() {
        $walletTotal = NairaWallet::sum('amount');
        $customerHappiness = User::where(['role' => 555, 'status' => 'active'])->with('nairaWallet')->first();
        $opened = 0;
        $closed = 0;
        if ($customerHappiness) {
            $opened = Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'open'])->count();
            $closed = Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'closed'])->count();
        }

        $totalOpened = Ticket::where(['status'=>'open'])->count();
        $totalClosed = Ticket::where(['status'=>'closed'])->count();

        $aFn = (isset($customerHappiness)) ? $customerHappiness->first_name : null;
        $aLn = (isset($customerHappiness)) ? $customerHappiness->lastst_name : null;

        $customerHappinessAgent = $aFn.' '.$aLn;

        // $opened = ($customerHappiness) ? Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'open'])->count() : 0;
        // $closed =  ($customerHappiness) ? Ticket::where(['agent_id' => $customerHappiness->id,'status'=>'closed'])->count() : 0;

        $totalOpened = Ticket::where(['status'=>'open'])->count();
        $totalClosed = Ticket::where(['status'=>'closed'])->count();
        
        $overview = [
            'users_naira_wallet' => number_format($walletTotal,'0','.',','),
            'user_pulse' => [
                'dead_users' => '10%',
                'resurrected_user' => '12%',
                'new_users' => '14%'
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
        ],200);
    }

    public static function lastAccountantOnRole() {
        $stamp = AccountantTimeStamp::whereHas('user',function ($query) {
            $query->where('status','waiting');
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
            $acct_name = $acctn->first_name.' '.$acctn->last_name;

            $opening_balance = $stamp->opening_balance;

            $wtrade = NairaTrade::where(['status' => 'success','type'=> 'withdrawal'])
            ->whereBetween('updated_at',[$stamp->activeTime,$stamp->inactiveTime])
            ->get();

            $dtrade = NairaTrade::where(['status' => 'success','type'=> 'deposit'])
            ->whereBetween('updated_at',[$stamp->activeTime,$stamp->inactiveTime])
            ->get();

            $pending_withdrawal = NairaTrade::where(['status' => 'success','type'=> 'withdrawal', 'agent_id' => $acctn->id]);
            $paid_out = $wtrade->sum('amount');
            $current_balance = $opening_balance - $paid_out;
            $closing_balance = $stamp->closing_balance;

            $last_active = ($stamp->inactiveTime) ? Carbon::createFromTimeString($stamp->inactiveTime)->diffForHumans() : '';

            $data['staff_name'] =  $acctn->first_name.' '.$acctn->last_name;
            $data['last_active'] =  $last_active;
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

    public function getTransactionHistory() {
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
                'count' => (!isset($chartDataByDay[$dateString]))? '0' : $chartDataByDay[$dateString],
                'day' => substr($date->format('l'), 0, 3),
                'date_day' => $date->format('d')
            ];
            $date->subDay();
        }
        return ($dateRange);
    }

    public function p2pTransactionHistory() {
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
                'count' => (!isset($chartDataByDay[$dateString]))? '0' : $chartDataByDay[$dateString],
                'day' => substr($date->format('l'), 0, 3),
                'date_day' => $date->format('d')
            ];
            $date->subDay();
        }
        return ($dateRange);
    }

    public function getP2pTransactionHistoryByDate() {
        $date = request('date');
        if (empty($date)) {
            $date = Carbon::now()->format('Y-m-d');   
        }

        $tranx = NairaTrade::where(DB::raw('date(created_at)'),$date)
            ->with(['user','agent','naria_transaction'])
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tranx
        ],200);
    }

    public function getCryptoTransactionHistoryByDate() {
        $date = request('date');
        if (empty($date)) {
            $date = Carbon::now()->format('Y-m-d');   
        }

        $tranx = Transaction::where(DB::raw('date(created_at)'),$date)
            ->with('user')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tranx
        ],200);
    }

    public function transactionHistory($type) {
        $tranx = [];
        if ($type == 'p2p') {
            $tranx = $this->p2pTransactionHistory();
        }elseif ($type == 'crypto') {
            $tranx = $this->getTransactionHistory();
        }

        return response()->json([
            'success' => true,
            'data' => $tranx
        ],200);
    }

    public function usersVerification() {
        $users = User::get();
        $l1 = User::whereNotNull('phone_verified_at');
        $l2 = User::whereHas('verifications',function ($query) {
           $query->where(['type' => 'Address', 'status' => 'success']);
        });
        $l3 = User::whereHas('verifications',function ($query) {
            $query->where(['type' => 'ID Card', 'status' => 'success']);
         });

        $pendingL2 = User::whereHas('verifications',function ($query) {
            $query->where(['type' => 'Address', 'status' => 'waiting']);
        });

        $pendingL3 = User::whereHas('verifications',function ($query) {
            $query->where(['type' => 'ID Card', 'status' => 'waiting']);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'pending_verifications_l2' => $pendingL2->count(),
                'pending_verifications_l3' => $pendingL3->count(),
                'level_1' => [
                    'total_users' => $users->count(),
                    'verified_users' => $l1->count(),
                    'percentage' => round(($l1->count()/$users->count()) * 100,2)
                ],
                'level_2' => [
                    'total_users' => $users->count(),
                    'verified_users' => $l2->count(),
                    'percentage' => round(($l2->count()/$users->count()) * 100,2)
                ],
                'level_3' => [
                    'total_users' => $users->count(),
                    'verified_users' => $l3->count(),
                    'percentage' => round(($l3->count()/$users->count()) * 100,2)
                ]
            ]
        ],200);
    }

    public function monthlyEarnings() {
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
        ->whereHas('user',function ($query) {
            $query->where(DB::raw('DATE(created_at)'),'<',Carbon::now()->subMonth(3));
        })
        ->whereBetween(DB::raw('DATE(created_at)'),[$queryFrom->format('Y-m-d'),$queryTo->format('Y-m-d')])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->toArray();

        $chartDataNewUsers = Transaction::select([
                DB::raw('DATE(created_at) AS date'),
                DB::raw('SUM(amount) AS value')
            ])
            ->whereHas('user',function ($query) {
                $query->where(DB::raw('DATE(created_at)'),'>',Carbon::now()->subMonth(3));
            })
            ->whereBetween(DB::raw('DATE(created_at)'),[$queryFrom->format('Y-m-d'),$queryTo->format('Y-m-d')])
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
                'old_users_turnover' => (!isset($chartDataByDayOldUsers[$dateString]))? '0' : $chartDataByDayOldUsers[$dateString],
                'new_users_turnover' => (!isset($chartDataByDayNewUsers[$dateString]))? '0' : $chartDataByDayNewUsers[$dateString],
                'day' => substr($queryFrom->format('l'), 0, 3),
                'date_day' => $queryFrom->format('d')
            ];
            $queryFrom->addDay();
        }
        
        return response()->json([
            'success' => true,
            'data' => $dateRange
        ],200);
    }

    public function summary() {
        $month = request('month');
        $year = request('year');

        $billsTotal = UtilityTransaction::where('status','success')
        ->where(DB::raw('MONTH(created_at)'), $month)
        ->where(DB::raw('YEAR(created_at)'), $year)
        ->count();

        $gcTranx = Transaction::whereHas('asset',function ($query) {
            $query->where('is_crypto',1);
        })->where('status','success')
        ->where(DB::raw('MONTH(created_at)'), $month)
        ->where(DB::raw('YEAR(created_at)'), $year)
        ->count();

        $cryptoTranx = Transaction::whereHas('asset',function ($query) {
            $query->where('is_crypto',0);
        })->where('status','success')
        ->where(DB::raw('MONTH(created_at)'), $month)
        ->where(DB::raw('YEAR(created_at)'), $year)
        ->count();

        $tranx = Transaction::where('status','success')
        ->where(DB::raw('MONTH(created_at)'), $month)
        ->where(DB::raw('YEAR(created_at)'), $year)
        ->count();

        $uTranx = UtilityTransaction::where('status','success')
        ->where(DB::raw('MONTH(created_at)'), $month)
        ->where(DB::raw('YEAR(created_at)'), $year)
        ->count();

        $tranxTurnover = Transaction::where('status','success')
        ->where(DB::raw('MONTH(created_at)'), $month)
        ->where(DB::raw('YEAR(created_at)'), $year)
        ->sum('amount');
        
        $uTranxTurnover = UtilityTransaction::where('status','success')
        ->where(DB::raw('MONTH(created_at)'), $month)
        ->where(DB::raw('YEAR(created_at)'), $year)
        ->count('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'atleast_one_tranx' => number_format($tranx + $billsTotal,0,'.',','),
                'successful_tranx' => number_format($tranx + $uTranx,0,'.',','),
                'successful_crypto_tranx' => number_format($cryptoTranx,0,'.',','),
                'montly_turnover' => number_format($uTranxTurnover + $tranxTurnover,0,'.',','),
                'successful_giftcard_tranx' => number_format($gcTranx,0,'.',','),
                'total_number_of_bills_payment' => number_format($billsTotal,0,'.',',')
            ]
        ],200);
    }
}