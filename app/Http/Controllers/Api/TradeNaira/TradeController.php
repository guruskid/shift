<?php

namespace App\Http\Controllers\Api\TradeNaira;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTradePop;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class TradeController extends Controller
{
    public function agents()
    {
        $agents = User::where(['role' => 777, 'status' => 'active'])->with(['nairaWallet', 'accounts'])->get();
        foreach ($agents as $a) {
            $a->successful = $a->agentNairaTrades()->where('status', 'success')->count();
            $a->declined = $a->agentNairaTrades()->where('status', 'failed')->count();
            if (!$a->agentLimits) {
                $a->agentLimits()->create();
            }
            $a->min = $a->agentLimits->min;
            $a->max = $a->agentLimits->max;
        }

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
        $min = $agent->agentLimits->min;
        $max = $agent->agentLimits->max;

        if ($request->amount < $min || $request->amount > $max ) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Trade range not met'
            ]);
        }

        //create TXN here
        $txn = new NairaTrade();
        $txn->reference = $ref;
        $txn->user_id = Auth::user()->id;
        $txn->agent_id = $agent->id;
        $txn->amount = $request->amount;
        $txn->status = 'waiting';
        $txn->type = 'buy';
        $txn->save();

        $agent_wallet->amount -= $request->amount;
        $agent_wallet->save();

        return response()->json([
            'success' => true,
            'reference' => $ref,
            'id' => $txn->id
        ]);

    }

    public function sellNaira(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'required',
            'amount' => 'integer|required',
            'pin' => 'string|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $user = Auth::user();
        $user_wallet = $user->nairaWallet;
        $agent = User::find($request->agent_id);


        $pin = $user->pin;
        $input_pin = $request->pin;
        $hash = Hash::check($input_pin, $pin);

        if(!$hash)
        {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Pin',
            ], 401);
        }
       
        $ref = \Str::random(3).time();
        if ($agent->role != 777) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Invalid agent'
            ]);
        }
        $min = $agent->agentLimits->min;
        $max = $agent->agentLimits->max;

        if ($request->amount < $min || $request->amount > $max ) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Trade range not met'
            ]);
        }

        if ($request->amount < $min || $request->amount > $max ) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Trade range not met'
            ]);
        }

        //create TXN here
        $txn = new NairaTrade();
        $txn->reference = $ref;
        $txn->user_id = Auth::user()->id;
        $txn->agent_id = $agent->id;
        $txn->amount = $request->amount;
        $txn->status = 'waiting';
        $txn->type = 'sell';
        $txn->save();

        $user_wallet->amount -= $request->amount;
        $user_wallet->save();

        return response()->json([
            'success' => true,
            'reference' => $ref,
            'id' => $txn->id
        ]);
    }

    public function transactions()
    {
        $transactions = Auth::user()->nairaTrades()->with('pops')->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function upload(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'transaction_id' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $txn = NairaTrade::find($r->transaction_id);
        if ($txn->user_id != Auth::user()->id) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid transaction'
            ]);
        }

        if ($r->has('image')) {
            $file = $r->image;
            $folderPath = public_path('storage/pop/');
            $image_base64 = base64_decode($file);

            $imageName = time() . uniqid() . '.png';
            $imageFullPath = $folderPath . $imageName;

            file_put_contents($imageFullPath, $image_base64);

            $pop = new NairaTradePop();
            $pop->path = $imageName;
            $pop->user_id = Auth::user()->id;
            $pop->transaction_id = $r->transaction_id;
            $pop->save();

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Image file not present'
            ]);
        }
    }


    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'string|required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $txn = Auth::user()->nairaTrades()->where('reference', $request->reference)->first();

        if (!$txn) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Transaction not found'
            ], 404);
        }


        if ($txn->status != 'waiting') {
            return response()->json([
                'success' =>  false,
                'msg' => 'Transaction already updated'
            ]);
        }


        $txn->status = 'pending';
        $txn->save();

        return response()->json([
            'success' => true,
            'msg' => 'Transaction updated'
        ]);
    }

    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'string|required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $txn = Auth::user()->nairaTrades()->where('reference', $request->reference)->first();

        if (!$txn) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Transaction not found'
            ], 404);
        }

        $txn->status = 'cancelled';
        $txn->save();

        return response()->json([
            'success' => true,
            'msg' => 'Transaction cancelled'
        ]);
    }
}
