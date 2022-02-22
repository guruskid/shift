<?php

namespace App\Http\Controllers\Api\TradeNaira;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTradePop;
use App\User;
use App\Account;
use App\Events\CustomNotification;
use App\NairaTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\GeneralTemplateOne;
use App\NairaWallet;
use App\PayBridgeAccount;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

//! things to ask
/**
 * todo: 1. pb withdrawal success where is the agent viewing and accepting transactions
 * ?things that will be worked on withdrawal pending success and deposit success.
 * ?withdrawal pending is after 1 hour of no action withdrawal cancelled by system after 3 days of no action
 */
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

    public function getAgent(Request $request)
    {
        $transactiontype = $request['type'];

        $user = Auth::user();
        $withdrawalToday = $this->getTodaysTotalTransactions('sell');
        $withdrawalThisMonth = $this->getThisMonthTotalTransactions('sell');

        $agent = User::where(['role' => 777, 'status' => 'active'])->with(['nairaWallet', 'accounts'])->whereNotNull('first_name')->select('id', 'first_name', 'last_name')->limit(1)->get();
        $user_wallet = $user->nairaWallet;
        \Log::info($agent);

        $account = PayBridgeAccount::where(['status' => 'active', 'account_type' => $transactiontype])->first();

        $user_data = [
            'total_withdrawn_today' => $withdrawalToday,
            'total_withdrawn_this_month' => $withdrawalThisMonth,
            'daily_max' => $user->daily_max,
            'monthly_max' => $user->monthly_max
        ];

        unset($agent[0]['accounts']);

        $agent[0]['user'] = $user_data;
        $agent[0]['accounts'] = $account;

        return response()->json([
            'success' => true,
            'data' => $agent
        ]);
    }

    public function getTodaysTotalTransactions($type)
    {
        $user = Auth::user();
        $total = NairaTrade::where(['type' => $type, 'user_id' => $user->id, 'status' => 'success'])->whereDay('created_at', date('d'))->select('amount')->sum('amount');
        return $total;
    }

    public function getThisMonthTotalTransactions($type)
    {
        $user = Auth::user();
        $total = NairaTrade::where(['type' => $type, 'user_id' => $user->id, 'status' => 'success'])->whereMonth('created_at', date('m'))->select('amount')->sum('amount');
        return $total;
    }

    public function completeWihtdrawal(Request $request) {

        $validator = Validator::make($request->all(), [
            'agent_id'  => 'required',
            'amount'   => 'required',
            'pin'      => 'required|min:4'
        ]);

        if ($request->amount < 1000) {
            return response()->json([
                'success' => false,
                'msg' => 'Amount should be greater than N1,000'
            ]);
        }

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

        $trade = NairaTrade::where(['user_id' => Auth::user()->id, 'type' => 'withdrawal', 'status' => 'waiting'])->get();
        if (count($trade) > 0) {
            return response()->json([
                'success' => false,
                'message' => "You currently have a pending withdrawal request",
            ]);
        }

        $agent = User::where(['role' => 777, 'status' => 'active', 'id' => $request->agent_id])->first();


        $account = PayBridgeAccount::where(['status' => 'active', 'account_type' => 'withdrawal'])->first();

        $agent['accounts'] = $account;


        if (empty($agent)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid agent ID",
            ]);
        }

        $account = Account::where(['id' => $request->account_id, 'user_id' => Auth::user()->id])->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => "Invalid account id",
            ]);
        }

        if (Auth::user()->nairaWallet->amount < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => "Low wallet balance",
            ]);
        }

        $ref = \Str::random(3) . time();
        $charge = 100;

        //create TXN here
        $txn = new NairaTrade();
        $txn->reference = $ref;
        $txn->user_id = Auth::user()->id;
        $txn->agent_id = $request->agent_id;
        $txn->amount = $request->amount - $charge;
        $txn->status = 'waiting';
        $txn->type = 'withdrawal';
        $txn->account_id = $request->account_id;
        $txn->save();

        $user = Auth::user();
        $user_wallet = Auth::user()->nairaWallet;
        $user_wallet->amount -= $request->amount;
        $user_wallet->save();



        $nt = new NairaTransaction();
        $nt->reference = $ref;
        $nt->amount = $request->amount;
        $nt->amount_paid = $request->amount - $charge;
        $nt->user_id = $user->id;
        $nt->type = 'withdrawal';
        $nt->previous_balance = $user_wallet->amount + $request->amount;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = $charge;
        $nt->transfer_charge = $charge;
        $nt->transaction_type_id = 3;
        $nt->cr_wallet_id = $user_wallet->id;
        $nt->cr_acct_name = $user->first_name;
        $nt->narration = 'Withdrawal ' . $ref;
        $nt->trans_msg = '';
        $nt->cr_user_id = 1;
        $nt->dr_user_id = $user->id;
        $nt->status = 'pending';
        $nt->save();


        //Transfer the charges
        $transfer_charges_wallet = NairaWallet::where('account_number', 0000000001)->first();
        $transfer_charges_wallet->amount += $nt->charge;
        $transfer_charges_wallet->save();

        $title = 'Pay-Bridge withdrawal(pending)';
        $paybridge_account = PayBridgeAccount::where(['status' => 'active', 'account_type' => 'withdrawal'])->first();

        $body = "You have initiated a withdrawal of ₦".number_format($request->amount)." via Pay-Bridge.<br><br>
        <b>Pay-Bridge Agent: ".$paybridge_account->account_name."</b><br><br>
        <b>Bank Name: ".$paybridge_account->bank_name."</b><br><br>
        <b>Status:<span style='color: red'>pending</span></b><br><br>
        <b>Reference No : ".$ref."</b><br><br>
        <b>Date: ".date("Y-m-d; h:ia")."</b><br><br>
        <b>Account Balance: ₦".number_format(Auth::user()->nairaWallet->amount)."</b><br><br>

        <b></b><br><br>
        ";

        $btn_text = '';
        $btn_url = '';

        $name = (Auth::user()->first_name == " ") ? Auth::user()->username : Auth::user()->first_name;
        $name = explode(' ', $name);
        $firstname = ucfirst($name[0]);
        Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

        $accountants = User::where(['role' => 777, 'status' => 'active'])->orWhere(['role' => 889, 'status' => 'active'])->get();
        $message = '!!! Withdrawal Transaction !!!  A new Withdrawal transaction has been initiated ';
        foreach ($accountants as $acct) {
            // broadcast(new CustomNotification($acct, $message))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => "Congratulations! You have successfully withdrawn the sum of $request->amount from your Dantown naira wallet",
        ], 200);
    }

    public function completeDeposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_id'  => 'integer|required',
            'amount'   => 'integer|required'
        ]);

        // dd("check");

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $agent = User::where(['role' => 777, 'status' => 'active', 'id' => $request->agent_id])->limit(1)->get();

        if (count($agent) < 1) {
            return response()->json([
                'success' => false,
                'message' => "Invalid agent ID",
            ]);
        }

        $trade = NairaTrade::where(['user_id' => Auth::user()->id, 'type' => 'deposit', 'status' => 'waiting'])->get();
        if (count($trade) > 0) {
            return response()->json([
                'success' => false,
                'message' => "You currently have a pending deposit request",
            ]);
        }

        $ref = \Str::random(3) . time();

        $user = Auth::user();
        $user_wallet = $user->nairaWallet;

        //create TXN here
        $txn = new NairaTrade();
        $txn->reference = $ref;
        $txn->user_id = Auth::user()->id;
        $txn->agent_id = $request->agent_id;
        $txn->amount = $request->amount;
        $txn->status = 'waiting';
        $txn->type = 'deposit';
        $txn->save();

        $nt = new NairaTransaction();
        $nt->reference = $ref;
        $nt->amount = $request->amount;
        $nt->user_id = $user->id;
        $nt->type = 'deposit';
        $nt->previous_balance = $user_wallet->amount;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;
        $nt->transaction_type_id = 1;
        $nt->cr_wallet_id = $user_wallet->id;
        $nt->cr_acct_name = $user->first_name;
        $nt->narration = 'Deposit ' . $ref;
        $nt->trans_msg = '';
        $nt->dr_user_id = 1;
        $nt->cr_user_id = $user->id;
        $nt->status = 'pending';
        $nt->save();

        $title = 'PAY-BRIDGE DEPOSIT
         ';
        $body = "Your naria Wallet has been credited with NGN" . $request->amount . "<br>
         <b style='color: 666eb6'>Reference Number: " . $ref . "</b><br>
         <b style='color: 666eb6'>Date: " . now() . "</b><br>
         <b style='color: 666eb6'>Account Balance: NGN" . Auth::user()->nairaWallet->amount . "</b><br>
         ";

        $btn_text = '';
        $btn_url = '';

        $name = (Auth::user()->first_name == " ") ? Auth::user()->username : Auth::user()->first_name;
        $name = explode(' ', $name);
        $firstname = ucfirst($name[0]);
        //  Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));


        $accountants = User::where(['role' => 777, 'status' => 'active'])->orWhere(['role' => 889, 'status' => 'active'])->get();
        $message = '!!! Deposit Transaction !!!  A new Deposit transaction has been initiated ';
        foreach ($accountants as $acct) {
            broadcast(new CustomNotification($acct, $message))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => "Congratulations! You have successfully deposited $request->amount, your Dantown wallet would be credited once payment is confirmed.",
        ], 200);
    }

    public function getStat()
    {
        $user = Auth::user();
        $withdrawalToday = $this->getTodaysTotalTransactions('sell');
        $withdrawalThisMonth = $this->getThisMonthTotalTransactions('sell');



        $pendingWithdrawal = false;
        $pendingDeposit = false;

        $trade = NairaTrade::where(['user_id' => Auth::user()->id, 'type' => 'withdrawal', 'status' => 'waiting'])->get();
        if (count($trade) > 0) {
            $pendingWithdrawal = true;
        }


        // $getName = User::where('id', Auth::user()->id)->get();
        // if (strlen($getName[0]->first_name) < 3) {
        //     $userName = true;
        // }

        $trade = NairaTrade::where(['user_id' => Auth::user()->id, 'type' => 'deposit', 'status' => 'waiting'])->get();
        if (count($trade) > 0) {
            $pendingDeposit = true;
        }

        $user_data = [
            'total_withdrawn_today' => $withdrawalToday,
            'total_withdrawn_this_month' => $withdrawalThisMonth,
            'daily_max' => $user->daily_max,
            'monthly_max' => $user->monthly_max,
            'naira_balance' => $user->nairaWallet->amount,
            'pending_withdrawal' => $pendingWithdrawal,
            'pending_deposit' => $pendingDeposit
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
        $ref = \Str::random(3) . time();
        if ($agent->role != 777) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Invalid agent'
            ]);
        }
        $min = $agent->agentLimits->min;
        $max = $agent->agentLimits->max;

        if ($request->amount < $min || $request->amount > $max) {
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
        $txn->type = 'deposit';
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

        if (!$hash) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Pin',
            ], 401);
        }

        $ref = \Str::random(3) . time();
        if ($agent->role != 777) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Invalid agent'
            ]);
        }
        $min = $agent->agentLimits->min;
        $max = $agent->agentLimits->max;

        if ($request->amount < $min || $request->amount > $max) {
            return response()->json([
                'success' =>  false,
                'msg' => 'Trade range not met'
            ]);
        }

        if ($request->amount < $min || $request->amount > $max) {
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
        $txn->type = 'withdrawal';
        $txn->save();

        $user_wallet->amount -= $request->amount;
        $user_wallet->save();

        return response()->json([
            'success' => true,
            'reference' => $ref,
            'id' => $txn->id
        ]);
    }

    public function getTransactions()
    {
        $transactions = Auth::user()->nairaTrades()->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
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

        //?mail

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
        $user = User::find($txn->user_id);
        $title = 'PAY-BRIDGE DEPOSIT(Cancelled by the system)';
        $body = "Your Deposit of NGN$txn->amount with reference code $txn->reference has been cancelled by the system because the
        Pay-bridge agent did not receive your payment.
        <br><br>
        <span style='color:blue'>Date of transaction: $txn->created_at</span>
        <br><br>
        Kindly contact our customer happiness team via our Instagram handle @godantown or call 09068633429 If you have a complaint";

         $btn_text = '';
         $btn_url = '';

         $name = ($user->first_name == " ") ? $user->username : $user->first_name;
         $name = explode(' ', $name);
         $firstname = ucfirst($name[0]);
         Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

        return response()->json([
            'success' => true,
            'msg' => 'Transaction cancelled'
        ]);
    }

    public function accounts()
    {
        $accts = Auth::user()->accounts;
        return response()->json([
            'success' => true,
            'data' => $accts
        ]);
    }
}
