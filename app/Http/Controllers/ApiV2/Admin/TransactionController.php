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
use Illuminate\Support\Facades\Validator;

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
            'total_deposit' => NairaTrade::where('type','deposit')->where('status','success')->count(),
            'total_withdrawal' => NairaTrade::where('type','withdrawal')->where('status','success')->count(),
            'total_utility' => UtilityTransaction::count(),
            'card_asset' => $transactions->sum('amount_paid'),
            'transactions' => $transactions->latest()->paginate(1000),
            'accountants' => User::where('role', [889, 777])->get(),
        ]);
    }



    public function transactionsPerDay()
    {

        $transaction_data = Transaction::with('user')->WhereYear('created_at', date('Y'))->get()->groupBy(function($transaction_data){
            return Carbon::parse($transaction_data->created_at)->format('Y-M-d');
        });

        // return ($transaction_data);
        // dd(date('m'));

        $transaction_days = [];
        $transactionDayCount = [];
        foreach($transaction_data as $month => $values) {
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
        if($status == 'withdrawal') {
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

        if($validate->fails()) {
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
}
