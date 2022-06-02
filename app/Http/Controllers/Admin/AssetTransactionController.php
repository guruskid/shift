<?php

namespace App\Http\Controllers\Admin;

use App\BitcoinTransaction;
use App\BitcoinWallet;
use App\Card;
use App\Events\CustomNotification;
use App\Events\TransactionUpdated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\DantownNotification;
use App\Mail\GeneralTemplateOne;
use App\Mail\WalletAlert;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\Transaction;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RestApis\Blockchain\Constants;

class AssetTransactionController extends Controller
{
    public function __construct()
    {
        $this->instance = $instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
    }

    public function editTransaction(Request $r)
    {

        if (in_array(Auth::user()->role,[444,449,999]) and $r->status == 'success') {
            return $this->payTransactionChinese($r);
        }

        $actualFeedback = "";

            if($r->status == "failed"){
                $actualFeedback = $r->failfeedbackstatus;
            }elseif($r->status == "declined"){
                $actualFeedback = $r->declinefeedbackstatus;
            }

        $t = Transaction::find($r->id);

        $amount_paid = $r->amount_paid;

        // finding the commision percentage
        //?percentage diffrence
        $percentage = ($t->commission/($t->amount_paid+$t->commission));
        $commision =  $amount_paid * $percentage;
        $user_amount = $amount_paid - $commision;

        $percentage = ceil(($t->commission/((($t->card_price + $t->commission) * $t->quantity))) * 100);
        $commision = round(($percentage / 100) * $r->amount_paid,2);
        $amount_paid = round($r->amount_paid - $commision,2);

        $t->card = Card::find($r->card_id)->name;
        $t->card_id = $r->card_id;
        $t->type = $r->trade_type;
        $t->country = $r->country;
        $t->amount = $r->amount;
        $t->amount_paid = $amount_paid;
        $t->commission = $commision;
        if(Auth::user()->role !=888){
            $t->status = $r->status;
        }
        $t->feedback = $actualFeedback;
        $t->quantity = $r->quantity;
        $t->last_edited = Auth::user()->email;
        $t->save();

        $user = $t->user; //Users should see success when a transaction is approved
        if ($t->status == 'approved') {
            $t->stats = 'success';
        } else {
            $t->stats = $t->status;
        }

        $body = 'The status of your transaction to  ' . $t->type . ' ' . $t->card .
            ' worth of ₦' . number_format($t->amount_paid) . ' has been updated to ' . $t->stats;
        $title = 'Transaction update';
        $not = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);

        // broadcast(new TransactionUpdated($user))->toOthers();
        if ($t->status == 'success' && $t->user->notificationSetting->trade_email == 1) {
            $title = 'Transaction Successful';
            Mail::to($user->email)->send(new DantownNotification($title, $body, 'Go to Wallet', route('user.naira-wallet')));


            $user = Auth::user();
        $title = 'TRANSACTION PENDING - BUY
        ';
        $body ="Your order to $t->type an <b>$t->card</b> worth NGN". number_format($t->amount_paid) ." was
        <b style='color:green'>$t->stats</b> and will be debited from your naria wallet once the transaction is successful<br>
        <b>Transaction ID: $t->uid <br>
        Date: ".date("Y-m-d; h:ia")."<br><br>
        </b>
        ";


        $btn_text = '';
        $btn_url = '';

        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

    // /////////////////////////////////////////////

        }

        /* Broadcast to all active accountants if approved for payment */
        if ($r->status == 'approved') {
            $accountants = User::where(['role' => 777, 'status' => 'active'])->orWhere(['role' => 889, 'status' => 'active'])->get();
            $message = 'A new transaction has been approved for payment ' . $t->id;
            foreach ($accountants as $acct) {
                broadcast(new CustomNotification($acct, $message))->toOthers();
            }
        }


        return redirect()->back()->with(['success' => 'Transaction updated']);
    }

    public function payTransaction(Request $r)
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
        if ($t->type == 'sell') {
            $user_wallet->amount += $amount;
        } else {
            $user_wallet->amount -= $amount;
        }

        $user_wallet->save();

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->amount_paid = $amount;
        $nt->user_id = $t->user->id;
        $nt->type = 'naira wallet';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;

        if ($t->type == 'sell') {
            $nt->transaction_type_id = 4; // Sell credit User
            $nt->dr_wallet_id = $n->id;
            $nt->cr_wallet_id = $user_wallet->id;
            $nt->dr_acct_name = 'Dantown';
            $nt->cr_acct_name = $t->user->first_name;
            $nt->cr_user_id = $t->user->id;
            $nt->dr_user_id = 1;
            $type = 'Credit';
        } else {
            $nt->transaction_type_id = 5; // Buy debit user
            $nt->cr_wallet_id = $n->id;
            $nt->dr_wallet_id = $user_wallet->id;
            $nt->cr_acct_name = 'Dantown';
            $nt->dr_acct_name = $t->user->first_name;
            $nt->dr_user_id = $t->user->id;
            $nt->cr_user_id = 1;
            $type = 'Credit';
        }


        $nt->narration = 'Payment for transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was approved by ' . Auth::user()->email;
        $nt->status = 'success';
        $nt->save();

        /* Update Transaction satus */
        $t->status = 'success';
        $t->accountant_id = Auth::user()->id;
        $t->save();

        $title = 'Dantown wallet ' . $type;
        $msg_body = 'Your Dantown wallet has been ' . $type . 'ed with N' . $amount . ' desc: Payment for transaction with id ' . $t->uid;
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

        return back()->with(['success' => 'Transfer made successfully']);
    }


    public function payTransactionChinese(Request $r)
    {
        $n = NairaWallet::find(1); /* Admin general Wallet */
        $t = Transaction::find($r->id);
        $user_wallet = $t->user->nairaWallet;

        $percentage = ceil(($t->commission/((($t->card_price + $t->commission) * $t->quantity))) * 100);

        $commision = ($percentage / 100) * $r->amount_paid;
        $amount_paid = round($r->amount_paid - $commision,2);

        if ($t->status == 'success') {
            return back()->with(['error' => 'Transaction already completed']);
        }

        $t->card = Card::find($r->card_id)->name;
        $t->card_id = $r->card_id;
        $t->type = $r->trade_type;
        $t->country = $r->country;
        $t->amount = $r->amount;
        $t->amount_paid = $amount_paid;
        $t->commission = $commision;
        $t->quantity = $r->quantity;
        $t->last_edited = Auth::user()->email;
        $t->save();

        if (!$user_wallet) {
            return back()->with(['error' => 'User wallet not found']);
        }

        $amount = $t->amount_paid;
        $reference = \Str::random(2) . '-' . $t->id;

        $prev_bal = $user_wallet->amount;
        if ($t->type == 'sell') {
            $user_wallet->amount += $amount;
        } else {
            $user_wallet->amount -= $amount;
        }

        $user_wallet->save();

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->amount_paid = $amount;
        $nt->user_id = $t->user->id;
        $nt->type = 'naira wallet';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;

        if ($t->type == 'sell') {
            $nt->transaction_type_id = 4; // Sell credit User
            $nt->dr_wallet_id = $n->id;
            $nt->cr_wallet_id = $user_wallet->id;
            $nt->dr_acct_name = 'Dantown';
            $nt->cr_acct_name = $t->user->first_name;
            $nt->cr_user_id = $t->user->id;
            $nt->dr_user_id = 1;
            $type = 'Credit';
        } else {
            $nt->transaction_type_id = 5; // Buy debit user
            $nt->cr_wallet_id = $n->id;
            $nt->dr_wallet_id = $user_wallet->id;
            $nt->cr_acct_name = 'Dantown';
            $nt->dr_acct_name = $t->user->first_name;
            $nt->dr_user_id = $t->user->id;
            $nt->cr_user_id = 1;
            $type = 'Credit';
        }


        $nt->narration = 'Payment for transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was approved by ' . Auth::user()->email;
        $nt->status = 'success';
        $nt->save();

        /* Update Transaction satus */
        $t->status = 'success';
        $t->accountant_id = Auth::user()->id;
        $t->save();

        $title = 'Dantown wallet ' . $type;
        $msg_body = 'Your Dantown wallet has been ' . $type . 'ed with N' . $amount . ' desc: Payment for transaction with id ' . $t->uid;
        /* Send notification */
        $not = Notification::create([
            'user_id' => $t->user->id,
            'title' => $title,
            'body' => $msg_body
        ]);

        if ($t->user->notificationSetting->wallet_email == 1) {
            // Mail::to($t->user->email)->send(new WalletAlert($nt, 'credit'));
        }

        $client = new Client();
        $token = env('SMS_TOKEN');
        $to = $t->user->phone;
        $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        /* $snd_sms = $client->request('GET', $sms_url); */

        return back()->with(['success' => 'Transfer made successfully']);
    }


}
