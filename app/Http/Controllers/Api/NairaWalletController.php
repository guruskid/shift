<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\WalletAlert;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class NairaWalletController extends Controller
{
    public function index(){
        if (!Auth::user()->nairaWallet ) {
            return response()->json([
                'success' => false,
                'msg' => 'No Naira wallet for this account'
            ]);
        }
        $wallet = Auth::user()->nairaWallet;
        return response()->json([
            'success' => true,
            'data' => $wallet
        ]);
    }
    public function allTransactions()
    {
        $naira_transactions = NairaTransaction::where('cr_user_id', Auth::user()->id)->orWhere('dr_user_id', Auth::user()->id)->latest()->with('transactionType')->get();
        return response()->json([
            'success' => true,
            'data' => $naira_transactions
        ]);
    }

    public function updateWalletPin(Request $r)
    {
        $n = Auth::user()->nairaWallet;

        $validator = Validator::make($r->all(), [
            'account_password' => 'required',
            'new_pin' => 'required|string|confirmed|min:4|max:4|different:account_password'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (Hash::check($r->account_password, Auth::user()->password) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Your current account does not match with the password you provided. Please try again.',
            ]);
        }

        Auth::user()->pin = Hash::make($r->new_pin);
        Auth::user()->save();

        return response()->json([
            'success' => true,
            'data' => $n,
        ]);
    }

    public function oldTransfer(Request $r)
    {
        $charge = 0;
        $bank_name = '';
        $acct_name = '';
        $acct_num = '';
        $bank_code = '';
        $tid = 0;
        $reference = $r->ref;


        if ($r->trans_type == 1) {       /* Normal Transfer */
            $validator = Validator::make($r->all(), [
                'bank_code' => 'required',
                'acct_num' => 'required',
                'acct_name' => 'required',
                'pin' => 'required',
                'amount' => 'required',
                'narration' => 'required',
                'ref' => 'required|unique:naira_transactions,reference',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                ], 401);
            }

            if ($r->amount > 50000) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Limit on direct transfer is ₦50,000'
                ]);
            }

            $bank_name = Bank::where('code', $r->bank_code)->first()->name;
            $acct_name = $r->acct_name;
            $acct_num = $r->acct_num;
            $bank_code = $r->bank_code;
            $tid = 2;
        } else if ($r->trans_type == 2) {   /* Withdraw transfer */
            $validator = Validator::make($r->all(), [
                'account_id' => 'required',
                'pin' => 'required',
                'amount' => 'required',
                'narration' => 'nullable',
                'ref' => 'required|unique:naira_transactions,reference',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                ], 401);
            }
            /*
            if ($r->amount > 300000 ) {
                return back()->with(['error' => 'Limit on withdraw transaction is ₦300,000' ]);
            } */

            /* get bank details */
            $bd = Account::where('id', $r->account_id)->first();
            if (!$bd) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Error getting account details'
                ]);
            }

            $acct_name = $bd->account_name;
            $bank_name = $bd->bank_name;
            $acct_num = $bd->account_number;
            $bank_code = Bank::find($bd->bank_id)->code;
            $tid = 3;
        }

        if ($bank_code != '090175') {
            $charge = 80;
        }


        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->pin, $n->password) == false) {
            return response()->json([
                'success' => false,
                'msg' => 'Wrong wallet pin entered'
            ]);
        }

        $amount = $r->amount + $charge;
        $amount_paid = $r->amount;


        if ($amount > $n->amount) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient funds'
            ]);
        }

        if ($tid == 0) {
            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong, please try again'
            ]);
        }

        $prev_bal = $n->amount;
        $n->amount -= $amount;
        $n->save();

        $msg = 'Transaction initiated';
        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $n->amount;
        $nt->charge = $charge;
        $nt->transfer_charge = 61.38;
        $nt->sms_charge = 2.55;
        if ($charge == 0) {
            $nt->transfer_charge = 0; //overide the previous
        }
        $nt->amount_paid = $amount_paid;
        $nt->transaction_type_id = $tid;

        $nt->user_id = Auth::user()->id;
        $nt->type = 'naira wallet';
        $nt->dr_user_id = Auth::user()->id;
        $nt->dr_wallet_id = $n->id;
        $nt->dr_acct_name = Auth::user()->first_name;
        $nt->cr_acct_name = $acct_name . ' ' . $acct_num . " " . $bank_name;
        $nt->narration = $r->narration;
        $nt->trans_msg = $msg;
        $nt->status = 'pending';
        $nt->save();

        /* Credit SMS and Transfer Wallet */
        $transfer_charges_wallet = NairaWallet::where('account_number', 0000000001)->first();
        $transfer_charges_wallet->amount += $nt->transfer_charge;
        $transfer_charges_wallet->save();

        $sms_charges_wallet = NairaWallet::where('account_number', 0000000002)->first();
        $sms_charges_wallet->amount += $nt->sms_charge;
        $sms_charges_wallet->save();



        $client = new Client();
        $url = env('RUBBIES_API') . "/fundtransfer";

        $response = $client->request('POST', $url, [
            'json' => [
                "reference" => $reference,
                "amount" => $r->amount,
                "narration" => $r->narration,
                "craccountname" => $acct_name,
                "bankname" => $bank_name,
                "draccountname" => Auth::user()->first_name,
                "craccount" => $acct_num,
                "bankcode" => $bank_code
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {

            $msg =  'Status: ' . $body->transactionstatus . " ,";
            $msg .=  'amount: ' . $body->amount . " ,";
            $msg .=  'nibssresponsemessage: ' . $body->nibssresponsemessage . " ,";
            $msg .=  'requestdatetime: ' . $body->requestdatetime . " ,";
            $msg .=  'draccount: ' . $body->draccount . " ,";
            $msg .=  'sessionid: ' . $body->sessionid . " ,";
            $msg .=  'craccount: ' . $body->craccount . " ,";
            $msg .=  'reference: ' . $body->reference . " ,";
            $msg .=  'tcode: ' . $body->tcode . " ,";
            $msg .=  'responsedatetime: ' . $body->responsedatetime . " ,";
            $msg .=  'nibsscode: ' . $body->nibsscode . " ,";
            $msg .=  'customerid: ' . $body->customerid . " ,";
            $msg .=  'craccountname: ' . $body->craccountname . " ,";
            $msg .=  'bankname: ' . $body->bankname . " ,";
            $msg .=  'bankcode: ' . $body->bankcode . " ,";
            $msg .=  'username: ' . $body->username . " ";


            $nt->trans_msg = $msg;
            $nt->status = 'success';
            $nt->save();



            $title = 'Dantown wallet Debit';
            $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for transfer to ' . $body->craccountname . ' desc: ' . $r->narration;
            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $msg_body,
            ]);
            if (Auth::user()->notificationSetting->wallet_email == 1) {
                Mail::to(Auth::user()->email)->send(new WalletAlert($nt, 'debit'));
            }


            /* Send SMS */
            $token = env('SMS_TOKEN');
            $to = Auth::user()->phone;
            $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
            /* $snd_sms = $client->request('GET', $sms_url); */

            return response()->json([
                'success' => true,
                'msg' => 'Transfer made successfully'
            ]);

        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Oops! ' . $body->responsemessage
            ]);
        }
    }

    public function transfer(Request $r)
    {

        //Check If user owns a wallet
        if (Auth::user()->accounts->count() == 0) {
            return response()->json([
                'success' => false,
                'msg' => 'Please add account details to continue'
            ]);
        }

        $validator = Validator::make($r->all(), [
            'account_id' => 'required',
            'pin' => 'required',
            'amount' => 'required',
            'narration' => 'nullable',
            'ref' => 'required|unique:naira_transactions,reference',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $charge = 0;
        $bank_name = '';
        $acct_name = '';
        $acct_num = '';
        $bank_code = '';
        $tid = 0;
        $reference = $r->ref;

        /* get bank details */
        $bd = Account::find($r->account_id);
        if (!$bd) {

            return response()->json([
                'success' => false,
                'msg' => 'Error getting account details'
            ]);
        }

        $acct_name = $bd->account_name;
        $bank_name = $bd->bank_name;
        $acct_num = $bd->account_number;
        $bank_code = Bank::find($bd->bank_id)->code;
        $tid = 3;

        if ($bank_code != '090175') {
            $charge = 100;
        }

        //Check daily limit
        $today_total = Auth::user()->nairaTransactions()->whereDate('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        if ($today_total >= Auth::user()->daily_max) {
            return response()->json([
                'success' => false,
                'msg' => 'Daily limit exceeded, please upgrade your account to continue.'
            ]);
        }

        //check Monthly
        $monthly_total = Auth::user()->nairaTransactions()->whereYear('created_at', now())->whereMonth('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        if ($monthly_total >= Auth::user()->monthly_max) {
            return response()->json([
                'success' => false,
                'msg' => 'Monthly limit exceeded, please upgrade your account limits to continue.'
            ]);
        }


        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->pin, Auth::user()->pin) == false) {
            return response()->json([
                'success' => false,
                'msg' => 'Wrong wallet pin entered'
            ]);
        }

        $amount = $r->amount - $charge;
        //$amount_paid = $r->amount;

        if ($r->amount > $n->amount) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient funds'
            ]);
        }

        if ($tid == 0) {
            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong, please try again'
            ]);
        }

        $prev_bal = $n->amount;
        $n->amount -= $r->amount;
        $n->save();

        $msg = 'Transaction initiated';
        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $r->amount;

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $n->amount;
        $nt->charge = $charge;
        $nt->transfer_charge = 100;
        $nt->sms_charge = 0;
        if ($charge == 0) {
            $nt->transfer_charge = 0; //overide the previous
            $nt->sms_charge = 0;
        }
        $nt->amount_paid = $amount;
        $nt->transaction_type_id = $tid;
        $nt->user_id = Auth::user()->id;
        $nt->type = 'naira wallet';
        $nt->dr_user_id = Auth::user()->id;
        $nt->dr_wallet_id = $n->id;
        $nt->dr_acct_name = Auth::user()->first_name;
        $nt->cr_acct_name = $acct_name . ' ' . $acct_num . " " . $bank_name;
        $nt->narration = $r->narration;
        $nt->trans_msg = $msg;
        $nt->status = 'pending';
        $nt->save();

        /* Credit SMS and Transfer Wallet */
        $transfer_charges_wallet = NairaWallet::where('account_number', 0000000001)->first();
        $transfer_charges_wallet->amount += $nt->transfer_charge;
        $transfer_charges_wallet->save();

        $sms_charges_wallet = NairaWallet::where('account_number', 0000000002)->first();
        $sms_charges_wallet->amount += $nt->sms_charge;
        $sms_charges_wallet->save();

        $title = 'Dantown wallet Debit';
        $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for transfer to ' . $nt->cr_acct_name . ' desc: ' . $r->narration;
        $not = Notification::create([
            'user_id' => Auth::user()->id,
            'title' => $title,
            'body' => $msg_body,
        ]);


        return response()->json([
            'success' => true,
            'msg' => 'Your withdrawal order has been placed, it will be processed in 3-4 hours'
        ]);
    }
}
