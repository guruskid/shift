<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function allTransactions()
    {
        if ($transactions = Auth::user()->transactions) {
            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No transaction yet'
            ]);
        }
    }
}
