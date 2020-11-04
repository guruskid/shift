<?php

namespace App\Http\Controllers\Admin;

use App\Card;
use App\Events\CustomNotification;
use App\Events\TransactionUpdated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\DantownNotification;
use App\Notification;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AssetTransactionController extends Controller
{
    public function editTransaction(Request $r)
    {
        $t = Transaction::find($r->id);
        $t->card = Card::find($r->card_id)->name;
        $t->card_id = $r->card_id;
        $t->type = $r->trade_type;
        $t->country = $r->country;
        $t->amount = $r->amount;
        $t->amount_paid = $r->amount_paid;
        $t->status = $r->status;
        $t->last_edited = Auth::user()->email;
        $t->save();

        $user = $t->user;
        if ($t->status == 'approved') {
            $t->stats = 'success';
        } else {
            $t->stats = $t->status;
        }

        $body = 'The status of your transaction to  ' . $t->type . ' ' . $t->card .
         ' worth of ₦' . number_format($t->amount_paid) . ' has been updated to ' .$t->stats;
        $title = 'Transaction update';
        $not = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);

        broadcast(new TransactionUpdated($user))->toOthers();
        if ($t->status == 'success' && $t->user->notificationSetting->trade_email == 1 ) {
            $title = 'Transaction Successful';
            Mail::to($user->email)->send(new DantownNotification($title, $body, 'Go to Wallet', route('user.naira-wallet')));
        }

        /* Broadcast to all active accountants if approved for payment */
        if ($r->status == 'approved') {
            $accountants = User::where(['role' => 777, 'status' => 'active'])->orWhere(['role' => 889, 'status' => 'active'])->get();
            $message = 'A new transaction has been approved for payment '.$t->id;
            foreach ($accountants as $acct ) {
                broadcast(new CustomNotification($acct, $message))->toOthers();
            }
        }


        return redirect()->back()->with(['success' => 'Transaction updated']);
    }
}
