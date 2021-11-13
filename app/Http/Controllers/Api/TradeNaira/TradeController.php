<?php

namespace App\Http\Controllers\Api\TradeNaira;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTradePop;
use App\User;
use App\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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

    public function getAgent(Request $request) {

        $user = Auth::user();
        $withdrawalToday = $this->getTodaysTotalTransactions('sell');
        $withdrawalThisMonth = $this->getThisMonthTotalTransactions('sell');
        
        $agent = User::where(['role' => 777, 'status' => 'active'])->with(['nairaWallet', 'accounts'])->select('id','first_name','last_name')->inRandomOrder()->limit(1)->get();
        $user_wallet = $user->nairaWallet;

        $user_data = [
            'total_withdrawn_today' => $withdrawalToday,
            'total_withdrawn_this_month' => $withdrawalThisMonth,
            'daily_max' => $user->daily_max,
            'monthly_max' => $user->monthly_max
        ];

        $agent[0]['user'] = $user_data;

        return response()->json([
            'success' => true,
            'data' => $agent
        ]);
    }

    public function getTodaysTotalTransactions($type) {
        $user = Auth::user();
        $total = NairaTrade::where(['type' => $type, 'user_id' => $user->id,'status' => 'success'])->whereDay('created_at', date('d'))->select('amount')->sum('amount');
        return $total;
    }

    public function getThisMonthTotalTransactions($type) {
        $user = Auth::user();
        $total = NairaTrade::where(['type' => $type, 'user_id' => $user->id,'status' => 'success'])->whereMonth('created_at', date('m'))->select('amount')->sum('amount');
        return $total;
    }

    public function completeWihtdrawal(Request $request) {
        $validator = Validator::make($request->all(), [
            'agent_id'  => 'integer|required', 
            'amount'   => 'integer|required',
            'pin'      => 'integer|required|min:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return response()->json([
                'success' => false,
                'msg' => 'Incorrect wallet pin'
            ]);
        }

        $agent = User::where(['role' => 777, 'status' => 'active', 'id'=> $request->agent_id])->limit(1)->get();

        if (count($agent) < 1) {
            return response()->json([
                'success' => false,
                'message' => "Invalid agent ID",
            ]);
        }

        $account = Account::where('user_id',$request->account_id)->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => "Invalid account id",
            ]);
        }

        $ref = \Str::random(3).time();

        //create TXN here
        $txn = new NairaTrade();
        $txn->reference = $ref;
        $txn->user_id = Auth::user()->id;
        $txn->agent_id = $request->agent_id;
        $txn->amount = $request->amount;
        $txn->status = 'waiting';
        $txn->type = 'sell';
        $txn->account_id = $request->account_id;
        $txn->save();

        $user_wallet = Auth::user()->nairaWallet;
        $user_wallet->amount -= $request->amount;
        $user_wallet->save();

        return response()->json([
            'success' => true,
            'message' => "Congratulations! You have successfully withdrawn the sum of $request->amount from your Dantown naira wallet",
        ], 200);
    }

    public function completeDeposit(Request $request) {
        $validator = Validator::make($request->all(), [
            'agent_id'  => 'integer|required', 
            'amount'   => 'integer|required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $agent = User::where(['role' => 777, 'status' => 'active', 'id'=> $request->agent_id])->limit(1)->get();

        if (count($agent) < 1) {
            return response()->json([
                'success' => false,
                'message' => "Invalid agent ID",
            ]);
        }

        $ref = \Str::random(3).time();

        //create TXN here
        $txn = new NairaTrade();
        $txn->reference = $ref;
        $txn->user_id = Auth::user()->id;
        $txn->agent_id = $request->agent_id;
        $txn->amount = $request->amount;
        $txn->status = 'waiting';
        $txn->type = 'buy';
        $txn->save();

        return response()->json([
            'success' => true,
            'message' => "Congratulations! You have successfully deposited $request->amount, your Dantown wallet would be credited once payment is confirmed.",
        ], 200);
    }

    public function getStat() {
        $user = Auth::user();
        $withdrawalToday = $this->getTodaysTotalTransactions('sell');
        $withdrawalThisMonth = $this->getThisMonthTotalTransactions('sell');

        $user_data = [
            'total_withdrawn_today' => $withdrawalToday,
            'total_withdrawn_this_month' => $withdrawalThisMonth,
            'daily_max' => $user->daily_max,
            'monthly_max' => $user->monthly_max
        ];

        return $user_data;
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
            'pin' => 'string|required'
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
