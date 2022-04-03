<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\Transaction;
use App\UtilityTransaction;

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
        $transactions = NairaTransaction::with('user', 'agent', 'accountant', 'pops', 'batchPops', 'asset');

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
}
