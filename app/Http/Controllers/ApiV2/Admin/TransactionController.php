<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use DB;

class TransactionController extends Controller
{
    public function btc()
    {
        $transactions = Transaction::with('user', 'agent', 'accountant', 'pops', 'batchPops', 'asset');

        return response()->json([
            'success' => true,
            'total' => $transactions->count(),
            'total_withdrawal' => UtilityTransaction::count(),
            'total_utility' => UtilityTransaction::count(),
            'card_asset' => $transactions->sum('amount_paid'),
            'transactions' => $transactions->latest()->paginate(1000),
        ]);
    }

    public function p2p()
    {
        $transactions = NairaTransaction::with('user');

        return response()->json([
            'success' => true,
            'total_transactions' => $transactions->count(),
            'total_deposit' => NairaTrade::where('type', 'deposit')->where('status', 'success')->count(),
            'total_withdrawal' => NairaTrade::where('type', 'withdrawal')->where('status', 'success')->count(),
            'total_utility' => UtilityTransaction::count(),
            'card_asset' => $transactions->sum('amount_paid'),
            'transactions' => $transactions->latest()->paginate(1000),
            'accountants' => User::where('role', [889, 777])->get(),
        ]);
    }



    public function transactionsPerDay()
    {

        $transaction_data = NairaTrade::with('user')->WhereYear('created_at', date('Y'))->get()->groupBy(function ($transaction_data) {
            return Carbon::parse($transaction_data->created_at)->format('Y-M-d');
        });

        // return ($transaction_data);
        // dd(date('m'));

        $transaction_days = [];
        $transactionDayCount = [];
        foreach ($transaction_data as $month => $values) {
            $transaction_days[] = $month;
            $transactionDayCount[] = count($values);
        }

        return response()->json([
            'success' => true,
            'transaction_days' => $transaction_days,
            'transactionDayCount' => $transactionDayCount,
            'transaction_data' => $transaction_data,
        ]);
    }


    public function TransactionCounts($status)
    {
        if ($status == 'withdrawal') {
            $transactions = NairaTrade::where('type', $status)->where('status', 'success');
            return response()->json([
                'success' => true,
                'transaction_count' => $transactions->count(),
                'total_withdrawal' => $transactions->sum('amount'),
            ]);
        }

        $transactions = NairaTrade::where('status', $status)->count();
        return response()->json([
            'success' => true,
            'transaction_count' => $transactions,
        ]);
    }
    public function transactionsByDate(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'start' => 'required|date|string',
            'end' => 'required|date|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ]);
        }

        $transactions = NairaTrade::with('user', 'nairaWallet', 'bitcoinWallet')->where('created_at', '>=', $request['start'])->where('created_at', '<=', $request['end']);
        $transactions = $transactions->paginate(1000);

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }



    // Junior Accountant
    public function overview(Request $req)
    {





        $total_number_utility_transactions = UtilityTransaction::whereIn('status', ['success', 'pending'])->count();
        $total_assets_value = Transaction::where('status', 'success')->sum('amount');
        $total_card_price =Transaction::where('status', 'success')->sum('card_price');
        $total_cash_value= Transaction::where('status', 'success')->sum('card_price');
        $total_transactions_number = Transaction::where('status', 'success')->count();

        $data["overview"] = [
            'total_number_utility_transactions' =>   $total_number_utility_transactions,
            'total_assets_value' =>  $total_assets_value,
            'total_card_price' =>  $total_card_price,
            'total_cash_value'=> $total_cash_value,
            'total_transactions_number' => $total_transactions_number
        ];


        // recent transactions
        $tranx = DB::table('transactions')
            ->whereIn('transactions.status', ['success', 'pending'])
            ->join('users', 'transactions.user_id', '=', 'users.id');

        // naira Transaction table
        $tranx2 = DB::table('naira_transactions')
            ->join('users', 'naira_transactions.user_id', '=', 'users.id');


        if ($req->transaction_type_id) {
            $tranx2 =   $tranx2->where('naira_transactions.transaction_type_id', $req->transaction_type_id);
        }
        // filter by accountant
        if ($req->accountant_id) {
            $tranx  =   $tranx->where('transactions.accountant_id', $req->accountant_id);
        }

        if ($req->status && in_array($req->status, ['pending', 'success', 'declined'])) {
            $tranx  =   $tranx->where('transactions.status', $req->status);
            $tranx2 = $tranx2->where('naira_transactions.status', $req->status);
        }



        $tranx = $tranx->select('first_name', 'last_name', 'username', 'dp', 'transactions.id', 'user_id', 'card as transaction', 'amount_paid as amount', 'transactions.amount as value', DB::raw('0 as prv_bal'), DB::raw('0 as cur_bal'), 'transactions.status', DB::raw('date(transactions.created_at) as date', 'transactions.created_at as created_at'));
        $tranx2 = $tranx2->select('first_name', 'last_name', 'username', 'dp','naira_transactions.id', 'user_id', 'type as transaction', 'amount_paid', 'naira_transactions.amount as value', 'previous_balance as prv_bal', 'current_balance as cur_bal', 'naira_transactions.status', DB::raw('date(naira_transactions.created_at) as date', 'naira_transactions.created_at as created_at'));
        // merge table
        $mergeTbl = $tranx->unionAll($tranx2);
        DB::table(DB::raw("({$mergeTbl->toSql()}) AS mg"))->mergeBindings($mergeTbl);
        $data['transactions'] =  $mergeTbl->orderBy('id', 'desc')->paginate();
        // }



        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
