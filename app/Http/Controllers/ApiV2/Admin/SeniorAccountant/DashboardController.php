<?php

namespace App\Http\Controllers\ApiV2\Admin\SeniorAccountant;

use App\AccountantTimeStamp;
use App\Http\Controllers\ApiV2\Admin\SpotLightController;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\NairaWallet;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class DashboardController extends Controller
{
    public function overView(Request $request)
    {


        $monthly = Carbon::now('Africa/Lagos')->startOfMonth();


        $walletTotal = NairaWallet::sum('amount');

        $withdrawalCharges = NairaTransaction::where('created_at', '>=',  $monthly)->sum('charge');
        $withdrawalCharges = NairaTransaction::where('type', 'withdrawal')->where('created_at', '>=',  $monthly)->sum('charge');

        $credit_txns = NairaTransaction::whereIn('transaction_type_id', [5, 16, 17])->where('created_at', '>=',  $monthly)->sum('amount');
        $debit_txns = NairaTransaction::whereIn('transaction_type_id', [4, 6])->where('created_at', '>=',  $monthly)->sum('amount');




        $overview = [
            'users_naira_wallet' => number_format($walletTotal, '0', '.', ','),
            'monthly' => [
                'monthly_withdrawal_charge' => $withdrawalCharges,
                'monthly_credits' => $credit_txns ,
                'monthly_debits' => $debit_txns,
            ],



            'accountant' => [
                'currently_active' => SpotLightController::accountantOnRole(),
                'last_active' => self::lastAccountantOnRole(),
            ],
            // 'number_of_new_users' => SpotLightController::getUsersByDays()
        ];
        return response()->json([
            'success' => true,
            'data' => $overview,
        ], 200);
    }


    public function analytics(){

          //For analytics

          $weekly = Carbon::now('Africa/Lagos')->startOfWeek();
          $monthly = Carbon::now('Africa/Lagos')->startOfMonth();
          $quarterly = Carbon::now('Africa/Lagos')->startOfMonth()->subMonth(3);
          $yearly = Carbon::now('Africa/Lagos')->startOfYear();


        if(request('frame') == 'monthly' ){

                 $crypto_total = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('created_at' , '>=',  $monthly)->sum('amount_paid');
            $crypto_count = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('created_at', '>=', $monthly)->count();

            $airtime = UtilityTransaction::where('type', 'Recharge card purchase')->where('created_at', '>=',  $monthly)->where('status', 'success')->sum('amount');
            $airtimeCount = UtilityTransaction::where('type', 'Recharge card purchase')->where('created_at', '>=', $monthly)->where('status', 'success')->count();
            $data = UtilityTransaction::where('type', 'Data purchase')->where('created_at', '>=',  $monthly)->where('status', 'success')->sum('amount');
            $dataCount = UtilityTransaction::where('type', 'Data purchase')->where('created_at', '>=', $monthly)->where('status', 'success')->count();
            $electricity = UtilityTransaction::where('type', 'Electricity purchase')->orWhere('type', 'Power')->where('created_at', '>=',  $monthly)->where('status', 'success')->sum('amount');
            $electricityCount = UtilityTransaction::where('type', 'Electricity purchase')->orWhere('type', 'Power')->where('created_at', '>=',  $monthly)->where('status', 'success')->count();

        }

        if(request('frame') == 'yearly'){
            $crypto_total = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('created_at', '>=',  $yearly)->sum('amount_paid');
            $crypto_count = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('created_at', '>=',  $yearly)->count();

            $airtime = UtilityTransaction::where('type', 'Recharge card purchase')->where('created_at', '>=',  $yearly)->where('status', 'success')->sum('amount');
            $airtimeCount = UtilityTransaction::where('type', 'Recharge card purchase')->where('created_at', '>=',  $yearly)->where('status', 'success')->count();

            $data = UtilityTransaction::where('type', 'Data purchase')->where('created_at', '>=',  $yearly)->where('status', 'success')->sum('amount');
            $dataCount = UtilityTransaction::where('type', 'Data purchase')->where('created_at', '>=',  $yearly)->where('status', 'success')->count();

            $electricity = UtilityTransaction::where('type', 'Electricity purchase')->orWhere('type', 'Power')->where('created_at', '>=',  $yearly)->where('status', 'success')->sum('amount');
            $electricityCount = UtilityTransaction::where('type', 'Electricity purchase')->orWhere('type', 'Power')->where('created_at', '>=',  $yearly)->where('status', 'success')->count();

        }

        if(request('frame') == 'quarterly'){

            $crypto_total = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('created_at', '>=',  $quarterly)->sum('amount_paid');
            $crypto_count = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('created_at', '>=',  $quarterly)->count();

            $airtime = UtilityTransaction::where('type', 'Recharge card purchase')->where('created_at', '>=',  $quarterly)->where('status', 'success')->sum('amount');
            $airtimeCount = UtilityTransaction::where('type', 'Recharge card purchase')->where('created_at', '>=',  $quarterly)->where('status', 'success')->count();
            $data = UtilityTransaction::where('type', 'Data purchase')->where('created_at', '>=',  $quarterly)->where('status', 'success')->sum('amount');
            $dataCount = UtilityTransaction::where('type', 'Data purchase')->where('created_at', '>=',  $quarterly)->where('status', 'success')->count();
            $electricity = UtilityTransaction::where('type', 'Electricity purchase')->orWhere('type', 'Power')->where('created_at', '>=',  $quarterly)->where('status', 'success')->sum('amount');
            $electricityCount = UtilityTransaction::where('type', 'Electricity purchase')->orWhere('type', 'Power')->where('created_at', '>=',  $quarterly)->where('status', 'success')->count();

        }


        if(request('frame') == 'weekly'){
            $crypto_total = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('created_at', '>=',  $weekly)->sum('amount_paid');
            $crypto_count = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('created_at', '>=',  $weekly)->count();
            $airtime = UtilityTransaction::where('type', 'Recharge card purchase')->where('created_at', '>=',  $weekly)->where('status', 'success')->sum('amount');
            $airtimeCount = UtilityTransaction::where('type', 'Recharge card purchase')->where('created_at', '>=',  $weekly)->where('status', 'success')->count();
            $data = UtilityTransaction::where('type', 'Data purchase')->where('created_at', '>=',  $weekly)->where('status', 'success')->sum('amount');
            $dataCount = UtilityTransaction::where('type', 'Data purchase')->where('created_at', '>=',  $weekly)->where('status', 'success')->count();
            $electricity = UtilityTransaction::where('type', 'Electricity purchase')->orWhere('type', 'Power')->where('created_at', '>=',  $weekly)->where('status', 'success')->sum('amount');
            $electricityCount = UtilityTransaction::where('type', 'Electricity purchase')->orWhere('type', 'Power')->where('created_at', '>=',  $weekly)->where('status', 'success')->count();

        }

        return response()->json([
            'success' => true,
            'totals' => [
                'crypto' => $crypto_total,
                'crypto_count' => $crypto_count,
                'airtime' => $airtime,
                'airtime_count' => $airtimeCount,
                'data' => $data,
                'data_count' => $dataCount,
                'electricity' => $electricity,
                'electricity_count' => $electricityCount,

            ]
            ]);
    }


    public function recentTransactions() {

        $transactions = NairaTrade::with('user', 'naira_transactions')->latest('id')->paginate(10);


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
        ->paginate(5);

        return response()->json([
            'success' => true,
            'data' => $tranx,
            'paybridge' => $transactions,
        ],200);
    }



//Stataement of Account of Each uses

    public function transPerUser($id)
    {
        $tranx = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
        // ->join('naira_wallets', 'transactions.user_id', '=', 'naira_wallets.id')
            ->select('first_name', 'last_name', 'username', 'dp', 'transactions.id', 'user_id', 'card as transaction', 'amount_paid as amount', 'transactions.amount as value', DB::raw('0 as prv_bal'), DB::raw('0 as cur_bal'), 'transactions.status', DB::raw('date(transactions.created_at) as date', 'transactions.created_at as created_at'))
        ;
        $tranx2 = DB::table('naira_transactions')
            ->join('users', 'naira_transactions.user_id', '=', 'users.id')
            ->select('first_name', 'last_name', 'username', 'dp', 'naira_transactions.id', 'user_id', 'type as transaction', 'amount_paid', 'naira_transactions.amount as value', 'previous_balance as prv_bal', 'current_balance as cur_bal', 'naira_transactions.status', DB::raw('date(naira_transactions.created_at) as date', 'naira_transactions.created_at as created_at'));

        $mergeTbl = $tranx->unionAll($tranx2);
        DB::table(DB::raw("({$mergeTbl->toSql()}) AS mg"))->mergeBindings($mergeTbl);

        $tranx = $mergeTbl
            ->where('transactions.user_id', [$id])
            ->orderBy('date', 'desc');

        return response()->json([
            'success' => true,
            'data' => $tranx,
        ], 200);
    }







    public static function lastAccountantOnRole()
    {
        $stamp = AccountantTimeStamp::whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->with('user')->latest()->first();

        // dd($stamp);
        $acct_name = '';

        if ($stamp) {
            $acctn = $stamp->user;
            $acct_name = $acctn->first_name . ' ' . $acctn->last_name;
        }

        $acctn = $stamp->user;

        $stamp = AccountantTimeStamp::where(['user_id' => $acctn->id])->latest()->first();

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

        return [
            'staff_name' => $acctn->first_name . ' ' . $acctn->last_name,
            'last_active' => Carbon::createFromTimeString($stamp->inactiveTime)->diffForHumans(),
            'opening_balance' => $opening_balance,
            'closing_balance' => 00,
            'total_paid_out' => [
                'amount' => $wtrade->sum('amount'),
                'count' => $wtrade->count(),
            ],
            'total_deposit' => [
                'amount' => $dtrade->sum('amount'),
                'count' => $dtrade->count(),
            ],
            'current_balance' => $current_balance,
            'pending_withdrawal' => [
                'amount' => $pending_withdrawal->sum('amount'),
                'count' => $pending_withdrawal->count(),
            ],
        ];
    }
}
