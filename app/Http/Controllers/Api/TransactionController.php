<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function allTransactions()
    {
        $transactions = Auth::user()->transactions;
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}
