<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Mail\DantownNotification;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\Transaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class NairaWalletController extends Controller
{
    public function create(Request $r)
    {
        $callback = route('recieve-funds.callback');
        $r->validate([
            'password' => 'required|string|confirmed|min:4|max:4'
        ]);

        if (Auth::user()->nairaWallet()->count() > 0) {
            return back()->with(['error' => 'You already own a naira wallet']);
        }
        $client = new Client();
        $url = env('RUBBIES_API') . "/createvirtualaccount";

        $response = $client->request('POST', $url, [
            'json' => [
                'virtualaccountname' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'amount' => '0',
                'amountcontrol' => 'VARIABLEAMOUNT',
                'daysactive' => 300000,
                'minutesactive' => 30,
                'callbackurl' => $callback,
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {

            Auth::user()->nairaWallet()->create([
                'account_number' => $body->virtualaccount,
                'account_name' => $body->virtualaccountname,
                'bank_name' => $body->bankname,
                'bank_code' => $body->bankcode,
                'amount' => $body->amount,
                'password' => Hash::make($r->password),
                'amount_control' => $body->amountcontrol,
            ]);

            $title = 'Dantown wallet created';
            $msg_body = 'Your Dantown wallet has been created successfully, you can now send money, recieve money, pay bills and do more with your Dantown wallet. Your account number is ' . $body->virtualaccount;
            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $msg_body,
            ]);

            Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body));

            return back()->with(['success' => 'Naira wallet account opened successfully']);
        } else {
            return back()->with(['error' => 'Oops! an error occured' . $body->responsemessage]);
        }
    }


    public function banklist()
    {
        $client = new Client();
        $url = env('RUBBIES_API') . "/banklist";

        $response = $client->request('POST', $url, [
            'json' => [
                "request" => "banklist"
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        foreach ($body->banklist as $b) {
            $bank = new Bank();
            $bank->name = $b->bankname;
            $bank->code = $b->bankcode;
            $bank->save();
        }
    }


    public function changePassword(Request $request)
    {
        $n = Auth::user()->nairaWallet;

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|confirmed|min:4|max:4|different:old_password'
        ]);

        if (Hash::check($request->old_password, Auth::user()->password) == false) {
            return redirect()->back()->with(['error' => 'Your current login password does not match with the password you provided. Please try again.']);
        }

        $n->password = Hash::make($request->new_password);
        $n->save();

        return redirect()->back()->with("success", "Password changed");
    }


    public function acctDetails(Request $r)
    {

        $client = new Client();
        $url = env('RUBBIES_API') . "/nameenquiry";

        $response = $client->request('POST', $url, [
            'json' => [
                "accountnumber" => $r->acct_num,
                "bankcode" => $r->bank_name
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {
            return response()->json([
                'success' => true,
                'acct' => $body->accountname
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => $body->responsemessage
            ]);
        }
    }


    public function transfer(Request $r)
    {
        $charge = 0;
        $bank_name = '';
        $acct_name = '';
        $acct_num = '';
        $bank_code = '';
        $tid = 0;
        $reference = $r->ref;


        if ($r->trans_type == 1) {       /* Normal Transfer */
            $r->validate([
                'bank_code' => 'required',
                'acct_num' => 'required',
                'acct_name' => 'required',
                'pin' => 'required',
                'amount' => 'required',
                'narration' => 'required',
            ]);

            if ($r->amount > 50000 ) {
                return back()->with(['error' => 'Limit on direct transfer is ₦50,000' ]);
            }

            $bank_name = Bank::where('code', $r->bank_code)->first()->name;
            $acct_name = $r->acct_name;
            $acct_num = $r->acct_num;
            $bank_code = $r->bank_code;
            $tid = 2;

        } else if ($r->trans_type == 2) {   /* Withdraw transfer */
            $r->validate([
                'account_id' => 'required',
                'pin' => 'required',
                'amount' => 'required',
                'narration' => 'required',
            ]);
/*
            if ($r->amount > 300000 ) {
                return back()->with(['error' => 'Limit on withdraw transaction is ₦300,000' ]);
            } */

            /* get bank details */
            $bd = Account::where('id', $r->account_id)->first();

            $acct_name = $bd->account_name;
            $bank_name = $bd->bank_name;
            $acct_num = $bd->account_number;
            $bank_code = Bank::where('name', $bd->bank_name)->first()->code;
            $tid = 3;
        }

        if ($bank_code != '090175') {
            $charge = 80;
        }


        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->pin, $n->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong wallet pin entered']);
        }

        $amount = $r->amount + $charge;


        if ($amount > $n->amount) {
            return redirect()->back()->with(['error' => 'Insufficient funds']);
        }

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
            $prev_bal = $n->amount;
            $n->amount -= $amount;
            $n->save();

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

            $nt = new NairaTransaction();
            $nt->reference = $reference;
            $nt->amount = $amount;

            $nt->previous_balance = $prev_bal;
            $nt->current_balance = $n->amount;
            $nt->charge = 61.38;
            $nt->transaction_type_id = $tid;


            $nt->user_id = Auth::user()->id;
            $nt->type = 'naira wallet';
            $nt->dr_user_id = Auth::user()->id;
            $nt->dr_wallet_id = $n->id;
            $nt->dr_acct_name = Auth::user()->first_name;
            $nt->cr_acct_name = $acct_name . ' ' . $acct_num . " " . $bank_name;
            $nt->narration = $r->narration;
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
            Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body));


            /* Send SMS */
            $token = env('SMS_TOKEN');
            $to = Auth::user()->phone;
            $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
            $snd_sms = $client->request('GET', $sms_url);

            return back()->with(['success' => 'Transfer made successfully']);
        } else {
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }


    public function adminTransfer(Request $r)
    {
        $r->validate([
            'id' => 'required',
            'pin' => 'required',
        ]);

        $n = NairaWallet::find(1); /* Admin general Wallet */
        $t = Transaction::find($r->id);
        $user_wallet = $t->user->nairaWallet;

        if (Hash::check($r->pin, $n->password) == false) {
            return back()->with(['error' => 'Wrong wallet pin']);
        }

        if ($t->status == 'success') {
            return back()->with(['error' => 'Transaction already completed']);
        }

        if (!$user_wallet) {
            return back()->with(['error' => 'User wallet not found']);
        }

        $amount = $t->amount_paid;
        $reference = \Str::random(2) . '-' . $t->id;

        $prev_bal = $user_wallet->amount;
        $user_wallet->amount += $amount;
        $user_wallet->save();

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->user_id = $t->user->id;
        $nt->type = 'naira wallet';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;
        $nt->transaction_type_id = 4;


        $nt->dr_wallet_id = $n->id;
        $nt->cr_wallet_id = $user_wallet->id;
        $nt->dr_acct_name = 'Dantown';
        $nt->cr_acct_name = $t->user->first_name ;
        $nt->narration = 'Payment for transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was approved by ' . Auth::user()->email;
        $nt->cr_user_id = $t->user->id;
        $nt->dr_user_id = Auth::user()->id;
        $nt->status = 'success';
        $nt->save();

        /* Update Transaction satus */
        $t->status = 'success';
        $t->save();

        $title = 'Dantown wallet Credit';
        $msg_body = 'Your Dantown wallet has been credited with N' . $amount . ' from Dantown desc: Payment for transaction with id ' . $t->uid;
        /* Send notification */
        $not = Notification::create([
            'user_id' => $t->user->id,
            'title' => $title,
            'body' => $msg_body
        ]);

        Mail::to($t->user->email)->send(new DantownNotification($title, $msg_body));

        $client = new Client();
        $token = env('SMS_TOKEN');
        $to = $t->user->phone;
        $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        $snd_sms = $client->request('GET', $sms_url);

        return back()->with(['success' => 'Transfer made successfully']);
    }

    public function adminRefund(Request $r)
    {
        $r->validate([
            'id' => 'required',
            'pin' => 'required',
        ]);

        $n = NairaWallet::find(1); /* Admin general Wallet */
        $t = Transaction::find($r->id);
        $user_wallet = $t->user->nairaWallet;

        if (Hash::check($r->pin, $n->password) == false) {
            return back()->with(['error' => 'Wrong wallet pin']);
        }

        if (!$user_wallet) {
            return back()->with(['error' => 'User wallet not found']);
        }

        $amount = $t->amount_paid;
        $reference = \Str::random(2) . '-' . $t->id;
        $prev_bal = $user_wallet->amount;
        $tid = 0;
        $tt = '';

        if ($t->type == 'buy') {
            $user_wallet->amount += $amount;
            $user_wallet->save();
            $tid = 6;
            $tt = 'credited';
        }else if($t->type == 'sell') {
            $user_wallet->amount -= $amount;
            $user_wallet->save();
            $tid = 16;
            $tt = 'debited';
        }


        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->user_id = $t->user->id;
        $nt->type = 'naira wallet';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;
        $nt->transaction_type_id = $tid;


        $nt->cr_wallet_id = $n->id;
        $nt->dr_wallet_id = $user_wallet->id;
        $nt->cr_acct_name = 'Dantown';
        $nt->dr_acct_name = $t->user->first_name . ' ' . $t->user->last_name;
        $nt->narration = 'Refund for transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was approved by ' . Auth::user()->email;

        if ($t->type == 'buy') {
            $nt->cr_user_id = $t->user->id;
        }else if($t->type == 'sell'){
            $nt->dr_user_id = $t->user->id;
        }

        $nt->status = 'success';
        $nt->save();

        /* Update Transaction satus */
        $t->status = 'waiting';
        $t->save();

        $title = 'Dantown wallet Debit';
        $msg_body = 'Your Dantown wallet has been '.$tt.' with N' . $amount . ' for the refund of transaction with id ' . $t->uid;
        /* Send notification */
        $not = Notification::create([
            'user_id' => $t->user->id,
            'title' => $title,
            'body' => $msg_body
        ]);

        Mail::to($t->user->email)->send(new DantownNotification($title, $msg_body));

        $client = new Client();
        $token = env('SMS_TOKEN');
        $to = $t->user->phone;
        $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        $snd_sms = $client->request('GET', $sms_url);

        return back()->with(['success' => 'Refund made successfully']);
    }


    public function callback(Request $r)
    {
        $nw = NairaWallet::where('account_number', $r->craccount)->first();
        $prev_bal = $nw->amount;
        $nw->amount += $r->amount;
        $nw->save();

        $reference = \Str::random(2) . '-' . time();
        $msg = 'Originatoraccountnumber: ' . $r->originatoraccountnumber;
        $msg .= 'amount ' . $r->amount;
        $msg .= 'originatorname ' . $r->originatorname;
        $msg .= 'service ' . $r->service;
        $msg .= 'narration ' . $r->narration;
        $msg .= 'craccountname ' . $r->craccountname;
        $msg .= 'paymentreference ' . $r->paymentreference;
        $msg .= 'sessionid ' . $r->sessionid;
        $msg .= 'bankname ' . $r->bankname;
        $msg .= 'craccount ' . $r->craccount;
        $msg .= 'bankcode ' . $r->bankcode;

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $r->amount;
        $nt->user_id = $nw->user->id;
        $nt->type = 'bank transfer';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $nw->amount;
        $nt->charge = 0;
        $nt->transaction_type_id = 1;


        $nt->cr_user_id = $nw->user->id;
        $nt->dr_acct_name = $r->originatorname;
        $nt->cr_wallet_id = $nw->id;
        $nt->cr_acct_name = $nw->account_name;
        $nt->narration = $r->narration;
        $nt->trans_msg = $msg;
        $nt->status = 'success';
        $nt->save();

        $title = 'Dantown wallet Credit';
        $msg_body = 'Your Dantown wallet has been credited with N' . $r->amount . ' from ' . $r->originatorname . ' desc: ' . $r->narration;

        $not = Notification::create([
            'user_id' => $nw->user->id,
            'title' => $title,
            'body' => $msg_body,
        ]);

        Mail::to($nw->user->email)->send(new DantownNotification($title, $msg_body));

        $client = new Client();
        $token = env('SMS_TOKEN');
        $to = $nw->user->phone;
        $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        $snd_sms = $client->request('GET', $sms_url);

        return true;
    }
}
