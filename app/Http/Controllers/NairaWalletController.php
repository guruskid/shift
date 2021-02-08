<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Mail\DantownNotification;
use App\Mail\WalletAlert;
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

            $title = 'Wallet created';
            $msg_body = 'Congratulations your Dantown Naira Wallet has been created successfully, you can now send, receive and store money in the wallet. Guess what that is not all, you can also pay bills and get airtime on our website';
            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $msg_body,
            ]);

            Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body, 'Go to Wallet', route('user.naira-wallet')));

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
    public function oldTransfer(Request $r)
    {
        return back()->with(['error' => 'Service not available']);

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
                'ref' => 'required|unique:naira_transactions,reference',
            ]);

            if ($r->amount > 50000) {
                return back()->with(['error' => 'Limit on direct transfer is ₦50,000']);
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
                'narration' => 'nullable',
                'ref' => 'required|unique:naira_transactions,reference',
            ]);

            /* get bank details */
            $bd = Account::find($r->account_id);
            if (!$bd) {
                return redirect()->back()->with(['error' => 'Error getting account details']);
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

        //Check daily limit
        $today_total = Auth::user()->nairaTransactions()->whereDate('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        if ($today_total >= Auth::user()->daily_max) {
            return redirect()->back()->with(['error' => 'Daily limit exceeded, please upgrade your account limits from the account settings page.']);
        }

        //check Monthly
        $monthly_total = Auth::user()->nairaTransactions()->whereYear('created_at', now())->whereMonth('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        if ($monthly_total >= Auth::user()->monthly_max) {
            return redirect()->back()->with(['error' => 'Monthly limit exceeded, please upgrade your account limits from the account settings page.']);
        }

        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->pin, $n->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong wallet pin entered']);
        }

        $amount = $r->amount - $charge;
        $amount_paid = $r->amount;


        if ($r->amount > $n->amount) {
            return redirect()->back()->with(['error' => 'Insufficient funds']);
        }

        if ($tid == 0) {
            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
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
                "amount" => $amount,
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
                // Mail::to(Auth::user()->email)->send(new WalletAlert($nt, 'debit'));
            }


            /* Send SMS */
            /* $token = env('SMS_TOKEN');
            $to = Auth::user()->phone;
            $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2'; */
            /* $snd_sms = $client->request('GET', $sms_url); */

            return back()->with(['success' => 'Transfer made successfully']);
        } else {
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }


    public function transfer(Request $r)
    {
        
        $r->validate([
            'account_id' => 'required',
            'pin' => 'required',
            'amount' => 'required',
            'narration' => 'nullable',
            'ref' => 'required|unique:naira_transactions,reference',
        ]);

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
            return redirect()->back()->with(['error' => 'Error getting account details']);
        }

        $acct_name = $bd->account_name;
        $bank_name = $bd->bank_name;
        $acct_num = $bd->account_number;
        $bank_code = Bank::find($bd->bank_id)->code;
        $tid = 3;

        if ($bank_code != '090175') {
            $charge = 200;
        }

        //Check daily limit
        $today_total = Auth::user()->nairaTransactions()->whereDate('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        if ($today_total >= Auth::user()->daily_max) {
            return redirect()->back()->with(['error' => 'Daily limit exceeded, please upgrade your account limits from the account settings page to continue.']);
        }

        //check Monthly
        $monthly_total = Auth::user()->nairaTransactions()->whereYear('created_at', now())->whereMonth('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        if ($monthly_total >= Auth::user()->monthly_max) {
            return redirect()->back()->with(['error' => 'Monthly limit exceeded, please upgrade your account limits from the account settings page.']);
        }

        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->pin, $n->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong wallet pin entered']);
        }

        $amount = $r->amount - $charge;
        //$amount_paid = $r->amount;

        if ($r->amount > $n->amount) {
            return redirect()->back()->with(['error' => 'Insufficient funds']);
        }

        if ($tid == 0) {
            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
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
        $nt->transfer_charge = 197;
        $nt->sms_charge = 3;
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
        if (Auth::user()->notificationSetting->wallet_email == 1) {
            // Mail::to(Auth::user()->email)->send(new WalletAlert($nt, 'debit'));
        }

        return back()->with(['success' => 'Withdrawal request made successfully']);
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
        /* $tid = 0;
        $tt = '';
        $cr_wallet_id = 0;
        $dr_wallet_id = 0; */

        if ($t->type == 'buy') {
            $user_wallet->amount += $amount;
            $user_wallet->save();
            $tid = 6;
            $tt = 'credited';
            $cr_wallet_id = $user_wallet->id;
            $cr_acct_name = $t->user->first_name;
            $dr_wallet_id = $n->id;
            $dr_acct_name = 'Dantown';
        } else if ($t->type == 'sell') {
            $user_wallet->amount -= $amount;
            $user_wallet->save();
            $tid = 16;
            $tt = 'debited';
            $cr_wallet_id = $n->id;
            $cr_acct_name = 'Dantown';
            $dr_wallet_id = $user_wallet->id;
            $dr_acct_name = $t->user->first_name;
        }


        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->amount_paid = $amount;
        $nt->user_id = $t->user->id;
        $nt->type = 'naira wallet';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;
        $nt->transaction_type_id = $tid;


        $nt->cr_wallet_id = $cr_wallet_id;
        $nt->dr_wallet_id = $dr_wallet_id;
        $nt->cr_acct_name = $cr_acct_name;
        $nt->dr_acct_name = $dr_acct_name;
        $nt->narration = 'Refund for transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was approved by ' . Auth::user()->email;

        if ($t->type == 'buy') {
            $nt->cr_user_id = $t->user->id;
            $nt->dr_user_id = 1;
        } else if ($t->type == 'sell') {
            $nt->dr_user_id = $t->user->id;
            $nt->cr_user_id = 1;
        }

        $nt->status = 'success';
        $nt->save();

        /* Update Transaction satus */
        $t->status = 'waiting';
        $t->save();

        $title = 'Dantown wallet Debit';
        $msg_body = 'Your Dantown wallet has been ' . $tt . ' with N' . $amount . ' for the refund of transaction with id ' . $t->uid;
        /* Send notification */
        $not = Notification::create([
            'user_id' => $t->user->id,
            'title' => $title,
            'body' => $msg_body
        ]);

        if ($t->user->notificationSetting->wallet_email == 1) {
            $ty =  'credit';
            if ($tt == 'debited') {
                $ty = 'debit';
            }
            Mail::to($t->user->email)->send(new WalletAlert($nt, $ty));
        }

        $client = new Client();
        $token = env('SMS_TOKEN');
        $to = $t->user->phone;
        $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        /* $snd_sms = $client->request('GET', $sms_url); */

        return back()->with(['success' => 'Refund made successfully']);
    }


    public function adminNairaRefund(Request $r)
    {
        $r->validate([
            'id' => 'required',
            'pin' => 'required',
        ]);

        $n = NairaWallet::find(1); /* Admin general Wallet */
        $t = NairaTransaction::find($r->id);
        $user_wallet = $t->user->nairaWallet;

        if (Hash::check($r->pin, $n->password) == false) {
            return back()->with(['error' => 'Wrong wallet pin']);
        }

        if (!$user_wallet) {
            return back()->with(['error' => 'User wallet not found']);
        }

        $amount = $t->amount;
        $reference = \Str::random(2) . '-' . $t->id;
        $prev_bal = $user_wallet->amount;

        $user_wallet->amount += $amount;
        $user_wallet->save();
        $tt = 'credited';


        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->amount_paid = $amount;
        $nt->user_id = $t->user->id;
        $nt->type = 'naira wallet';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;
        $nt->transaction_type_id = 18;


        $nt->cr_wallet_id = $user_wallet->id;
        $nt->dr_wallet_id = $n->id;
        $nt->dr_acct_name = 'Dantown';
        $nt->cr_acct_name = $t->user->first_name;
        $nt->narration = 'Refund for naira transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was approved by ' . Auth::user()->email;

        $nt->cr_user_id = $t->user->id;
        $nt->dr_user_id = 1;

        $nt->status = 'success';
        $nt->save();

        /* Update Transaction satus */
        $t->status = 'refunded';
        $t->save();

        $title = 'Dantown wallet Debit';
        $msg_body = 'Your Dantown wallet has been ' . $tt . ' with N' . $amount . ' for the refund of transaction with id ' . $t->reference;
        /* Send notification */
        $not = Notification::create([
            'user_id' => $t->user->id,
            'title' => $title,
            'body' => $msg_body
        ]);

        if ($t->user->notificationSetting->wallet_email == 1) {
            Mail::to($t->user->email)->send(new WalletAlert($nt, 'credit'));
        }

        $client = new Client();
        $token = env('SMS_TOKEN');
        $to = $t->user->phone;
        $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        /* $snd_sms = $client->request('GET', $sms_url); */

        return back()->with(['success' => 'Refund made successfully']);
    }

    public function query($id)
    {
        try {
            $reference = NairaTransaction::findOrFail($id)->reference;

        $client = new Client();
        $url = env('RUBBIES_API') . "/transactionquery";

        $response = $client->request('POST', $url, [
            'json' => [
                "reference" => $reference,
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        $body->requestdate  = date('d M h:ia', $body->requestdate);
        
        if ($body->responsecode != 13) {
            return response()->json([
                'success' => true,
                'data' => $body
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => $body
            ]);
        }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                //'data' => $body
            ]);
        }
    }

    public function updateStatus(Request $r)
    {
        $transaction = NairaTransaction::find($r->id);
        $transaction->status = $r->status;
        $transaction->save();
        return back()->with(['success' => 'Transaction status updated to ' . $r->status]);
    }

    public function callback(Request $r)
    {
        $nw = NairaWallet::where('account_number', $r->craccount)->first();
        $prev_bal = $nw->amount;
        $nw->amount += $r->amount;
        $nw->save();

        $ttid = 1;
        if ($nw->user->role == 999) {
            $ttid = 17;
        }

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
        $nt->amount_paid = $r->amount;
        $nt->user_id = $nw->user->id;
        $nt->type = 'bank transfer';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $nw->amount;
        $nt->charge = 0;
        $nt->transaction_type_id = $ttid;


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

        if ($nw->user->notificationSetting->wallet_email == 1) {
            Mail::to($nw->user->email)->send(new WalletAlert($nt, 'credit'));
        }

        $client = new Client();
        $token = env('SMS_TOKEN');
        $to = $nw->user->phone;
        $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        /* $snd_sms = $client->request('GET', $sms_url); */

        return true;
    }
}
