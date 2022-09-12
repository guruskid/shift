<?php

namespace App\Http\Controllers\ApiV2;

use App\Http\Controllers\ApiV2\Admin\UtilityController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CryptoGiftcardTransactionResource;
use App\Http\Resources\NairaTradeResource;
use App\Http\Resources\UtilityTransactionResource;
use App\NairaTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function allCardTransactions()
    {
        $transactions = Auth::user()->transactions;
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function utilityTransactions()
    {
        $transactions = Auth::user()->utilityTransaction;
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function bitcoinWalletTransactions()
    {
        if (Auth::user()->bitcoinWallet) {
            $transactions = Auth::user()->bitcoinWallet->transactions;
        } else {
            $transactions = [];
        }


        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function nairaTransactions()
    {
        $naira_transactions = NairaTransaction::where('cr_user_id', Auth::user()->id)->orWhere('dr_user_id', Auth::user()->id)->latest()->with('transactionType')->get();
        return response()->json([
            'success' => Auth::user()->id,
            'data' => $naira_transactions
        ]);
    }

    public function AllUserTransactions()
    {
        $tranx = Auth::user()->transactions;
        $tranxData = CryptoGiftcardTransactionResource::collection($tranx);

        $utilTranx = Auth::user()->utilityTransaction;
        $utilData = UtilityTransactionResource::collection($utilTranx);

        $p2pTranx = Auth::user()->nairaTrades;
        $p2pData = NairaTradeResource::collection($p2pTranx);

        $allTranx = collect($tranxData,$utilData,$p2pData)
        ->sortByDesc('updated_at');

        $transaction = array();
        foreach($allTranx as $at)
        {
            $transaction[] = $at;
        }
        return response()->json([
            'success' => true,
            'allTransactions' => $transaction,
        ]);
    }

    public function showUserTransaction(Request $r)
    {
        if(!in_array($r->transactionType,['CryptoGiftCard','utilities','payBridge']))
        {
            return response()->json([
                'success' => false,
                'message' => "Error Wrong Transaction Type"
            ],401);
        }

        $userTranx = null;
        if($r->transactionType == 'CryptoGiftCard'):
            $userTranx = CryptoGiftcardTransactionResource::collection(Auth::user()->transactions);
        endif;

        if($r->transactionType == 'utilities'):
            $userTranx = UtilityTransactionResource::collection(Auth::user()->utilityTransaction);
        endif;

        if($r->transactionType == 'payBridge'):
            $userTranx =  NairaTradeResource::collection(Auth::user()->nairaTrades);
        endif;

        $transaction = $userTranx->where('id',$r->id)->first();
        
        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }
}
