<?php

namespace App\Http\Controllers\Api\TradeNaira;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TradeController extends Controller
{
    public function agents()
    {
        $agents = User::where(['role' => 777, 'status' => 'active'])->get();

        return response()->json([
            'success' => true,
            'data' => $agents
        ]);
    }


    public function buyNaira(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'required',
            'amount' => 'integer|required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $agent = User::find($request->agent_id);
        $agent_wallet = $agent->nairaWallet;
        $ref = \Str::random(3).time();
        if ($agent->role != 777) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Invalid agent'
            ]);
        }

        if ($agent_wallet->amount < $request->amount) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Insufficient agent balance'
            ]);
        }

        //create TXN here
        $txn = new NairaTrade();
        $txn->reference = $ref;
        $txn->user_id = Auth::user()->id;
        $txn->agent_id = $agent->id;
        $txn->amount = $request->amount;
        $txn->status = 'waiting';
        $txn->save();

        $agent_wallet->amount -= $request->amount;
        $agent_wallet->save();

        return response()->json([
            'success' => true,
            'reference' => $ref
        ]);

    }

    public function transactions()
    {
        $transactions = Auth::user()->nairaTrades;

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }


    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required',
            'reference' => 'integer|required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $txn = NairaTrade::where('reference', $request->reference)->first();

        if (!$txn) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Transaction not found'
            ], 404);
        }

        $user = $txn->user;
        $user_wallet = $user->nairaWallet;

        if ($txn->status != 'waiting') {
            return response()->json([
                'success' =>  false,
                'msg' => 'Invalid transactionn'
            ]);
        }

        $user_wallet += $txn->amount;
        $user_wallet->save();

        $txn->status = 'success';
        $txn->save();

        return response()->json([
            'success' => true,
            'msg' => 'Transaction confirmed'
        ]);
    }
}
