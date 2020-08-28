<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Card;
use App\Notification;
use App\Rate;
use App\Transaction;
use App\User;
use App\Setting;
use Illuminate\Http\Request;
use App\Charts\UserChart;
use App\Events\TransactionUpdated;
use App\Mail\DantownNotification;
use App\NairaTransaction;
use App\NairaWallet;
use App\TransactionType;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    //add the auth and admin middleware

    public function dashboard()
    {

        $buyCash = Transaction::where('status', 'success')->where('type', 'buy')->sum('amount_paid');
        $sellCash = Transaction::where('status', 'success')->where('type', 'sell')->sum('amount_paid');
        $buyCount = Transaction::where('status', 'success')->where('type', 'buy')->count();
        $sellCount = Transaction::where('status', 'success')->where('type', 'sell')->count();

        $pBuyCash = Transaction::where('status', 'success')->where('type', 'buy')->where('agent_id', Auth::user()->id)->sum('amount_paid');
        $pSellCash = Transaction::where('status', 'success')->where('type', 'sell')->where('agent_id', Auth::user()->id)->sum('amount_paid');
        $pBuyCount = Transaction::where('status', 'success')->where('type', 'buy')->where('agent_id', Auth::user()->id)->count();
        $pSellCount = Transaction::where('status', 'success')->where('type', 'sell')->where('agent_id', Auth::user()->id)->count();

        $s = Transaction::where('status', 'success')->count();
        $w = Transaction::where('status', 'waiting')->count();
        $d = Transaction::where('status', 'declined')->count();
        $f = Transaction::where('status', 'failed')->count();

        $borderColors = [
            "rgba(255, 99, 132, 1.0)",
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"
        ];
        $fillColors = [
            "rgba(255, 99, 132, 1.0)",
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"

        ];
        $usersChart = new UserChart;
        $usersChart->minimalist(true);
        $usersChart->labels(['Failed', 'Successful', 'Declined', 'Waiting']);
        $usersChart->dataset('Users by trimester', 'pie', [$f, $s, $d, $w])
            ->color($borderColors)
            ->backgroundcolor($fillColors);


        $transactions = Transaction::orderBy('created_at', 'desc')->get()->take(1);
        $users = User::latest()->get()->take(4);
        $notifications = Notification::where('user_id', 0)->orderBy('created_at', 'desc')->get()->take(5);
        $users_count = User::all()->count();




        $client = new Client();
        $url = env('RUBBIES_API') . "/balanceenquiry";

        $response = $client->request('POST', $url, [
            'json' => [
                "accountnumber" => "0140963171"
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());

        $rubies_balance = $body->balance;
        $users_wallet_balance = NairaWallet::sum('amount');
        $company_balance = $rubies_balance - $users_wallet_balance;
        $charges = NairaTransaction::sum('charge');

        $withdraw_txns = NairaTransaction::where('transaction_type_id', 3)->sum('amount');
        $airtime_txns = NairaTransaction::where('transaction_type_id', 9)->sum('amount');
        $buy_txns_wallet = NairaTransaction::where('transaction_type_id', 5)->sum('amount');

        $g_txns = Transaction::whereHas('asset', function ($query){
            $query->where('is_crypto', 0);
        } )->latest()->get()->take(4);

        $c_txns = Transaction::whereHas('asset', function ($query){
            $query->where('is_crypto', 1);
        } )->latest()->get()->take(4);

        $n_txns = NairaTransaction::latest()->get()->take(4);

        if (Auth::user()->role == 999) {
            return view(
                'admin.super_dashboard',
                compact([
                    'transactions', 'users', 'users_count', 'notifications', 'usersChart',
                    'withdraw_txns', 'airtime_txns', 'buy_txns_wallet',
                    'g_txns', 'c_txns', 'n_txns',
                    'buyCash', 'sellCash', 'buyCount', 'sellCount',
                    'pBuyCash', 'pSellCash', 'pBuyCount', 'pSellCount',
                    'users_wallet_balance', 'rubies_balance', 'company_balance', 'charges'
                ])
            );
        } else {
            return view(
                'admin.dashboard',
                compact([
                    'transactions', 'users', 'users_count', 'notifications', 'usersChart',
                    'buyCash', 'sellCash', 'buyCount', 'sellCount',
                    'pBuyCash', 'pSellCash', 'pBuyCount', 'pSellCount', 'users_wallet_balance', 'rubies_balance', 'company_balance'
                ])
            );
        }

    }

    public function cards()
    {
        $cards = Card::orderBy('id', 'desc')->get();
        return view('admin.cards', compact(['cards']));
    }

    public function addCard(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:cards',
        ]);
        $card = new Card();
        $card->name = $request->name;
        $card->is_crypto = $request->is_crypto;
        $card->wallet_id = $request->wallet_id;
        $card->save();
        return redirect()->back()->with(['success' => 'Card added']);
    }

    public function editCard(Request $r)
    {

        $card = Card::find($r->id);
        $card->name = $r->name;
        $card->wallet_id = $r->wallet_id;
        $card->is_crypto = $r->is_crypto;
        $card->save();
        return redirect()->back()->with(['success' => 'Card updated']);
    }

    public function getCard($id)
    {
        $card = Card::find($id);
        return response()->json($card);
    }

    public function deleteCard($id)
    {
        $card = Card::find($id);
        return response()->json($card->delete());
    }

    public function rates()
    {
        $buy = Rate::where('rate_type', 'buy')->orderBy('created_at', 'desc')->get();
        $sell = Rate::where('rate_type', 'sell')->orderBy('created_at', 'desc')->get();
        $cards = Card::orderBy('name', 'asc')->get(['name']);

        return view('admin.rates', compact(['buy', 'sell', 'cards']));
    }

    public function addRate(Request $request)
    {

        $rate = Rate::updateOrCreate(
            [
                'card' => $request->card,
                'rate_type' => $request->rate_type,
                'min' => $request->min,
                'max' => $request->max
            ],
            [
                'usd' => $request->usd,
                'eur' => $request->eur,
                'gbp' => $request->gbp,
                'aud' => $request->aud,
                'cad' => $request->cad,

            ]
        );
        return redirect()->back()->with(['success' => 'Rate added']);
    }

    public function getRate($id)
    {
        $rate = Rate::find($id);
        return response()->json($rate);
    }

    public function editRate(Request $r)
    {
        $rate = Rate::find($r->id);
        $rate->card = $r->card;
        $rate->rate_type = $r->rate_type;
        $rate->usd = $r->usd;
        $rate->eur = $r->eur;
        $rate->gbp = $r->gbp;
        $rate->aud = $r->aud;
        $rate->cad = $r->cad;
        $rate->min = $r->min;
        $rate->max = $r->max;
        $rate->save();
        return redirect()->back()->with(['success' => 'Rate Edited']);
    }

    public function deleteRate($id)
    {
        $rate = Rate::find($id);
        return response()->json($rate->delete());
    }

    /* TRANSACTIONS */

    public function transactions()
    {
        $transactions = Transaction::orderBy('created_at', 'desc')->paginate(20);
        $segment = 'All';

        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function buyTransac()
    {
        $transactions = Transaction::where('type', 'buy')->orderBy('created_at', 'desc')->paginate(20);
        $segment = 'Buy';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function sellTransac()
    {
        $transactions = Transaction::where('type', 'sell')->orderBy('created_at', 'desc')->paginate(20);
        $segment = 'Sell';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function successTransac()
    {
        $transactions = Transaction::where('status', 'success')->orderBy('created_at', 'desc')->paginate(20);
        $segment = 'Successfull';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function failedTransac()
    {
        $transactions = Transaction::where('status', 'failed')->orderBy('created_at', 'desc')->paginate(20);
        $segment = 'Failed';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function waitingTransac()
    {
        $transactions = Transaction::where('status', 'waiting')->orderBy('created_at', 'desc')->paginate(20);
        $segment = 'waiting';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function declinedTransac()
    {
        $transactions = Transaction::where('status', 'declined')->orderBy('created_at', 'desc')->paginate(20);
        $segment = 'Declined';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function assignedTransac()
    {
        $transactions = Auth::user()->assignedTransactions()->where('status', 'waiting')->get();
        foreach ($transactions as $t) {
            if ($t->user->accounts->count() > 0) {
                $t->bank_name = $t->user->accounts()->first()['bank_name'];
                $t->acct_num = $t->user->accounts()->first()['account_number'];
            }
        }
        /* $transactions = Transaction::where('agent_id', Auth::user()->id)->paginate(20); */

        return view('admin.assigned-transactions', compact(['transactions']));
    }

    public function getTransac($id)
    {
        $transac = Transaction::find($id);
        return response()->json($transac);
    }

    public function addTransaction(Request $r)
    {

        $t = new Transaction();
        $t->uid = uniqid();
        $t->user_email = $r->user_email;
        $t->card = $r->card;
        $t->type = $r->trade_type;
        $t->country = $r->country;
        $t->amount = $r->amount;
        $t->amount_paid = $r->amount_paid;
        $t->status = $r->status;
        $t->save();

        return redirect()->back()->with(['success' => 'Transaction added']);
    }

    public function editTransaction(Request $r)
    {

        $t = Transaction::find($r->id);
        $t->card = $r->card;
        $t->type = $r->trade_type;
        $t->country = $r->country;
        $t->amount = $r->amount;
        $t->amount_paid = $r->amount_paid;
        $t->status = $r->status;
        $t->last_edited = Auth::user()->email;
        $t->save();

        $user = User::where('email', $t->user_email)->first();
        if ($t->status == 'approved') {
            $t->stats = 'success';
        } else {
            $t->stats = $t->status;
        }
        $body = 'The status of your transaction with id ' . $t->uid . ', has been updated to ' . $t->stats;
        $title = 'Transaction update';
        $not = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);

        broadcast(new TransactionUpdated($user));
        Mail::to($user->email)->send(new DantownNotification($title, $body));

        return redirect()->back()->with(['success' => 'Transaction updated']);
    }

    public function viewTransac($id, $uid)
    {
        $transaction = Transaction::find($id);

        return view('admin.transaction', compact(['transaction']));
    }

    public function deleteTransac($id)
    {
        $rate = Transaction::find($id);
        return response()->json($rate->delete());
    }

    public function updateTransaction($id, $status)
    {
        $t = Transaction::find($id);
        $t->status = $status;
        $t->save();

        $user = User::where('email', $t->user_email)->first();
        $title = 'Transaction update';
        $body = 'The status of your transaction with id ' . $t->uid . ', has been updated to ' . $status;
        $not = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);

        broadcast(new TransactionUpdated($user));
        Mail::to($user->email)->send(new DantownNotification($title, $body));
        return response()->json(['success' => true]);
    }


    public function walletTransactions($id = null)
    {
        if ($id == null) {
            $transactions = NairaTransaction::latest()->get();
            $segment = 'All Wallet';
        }
        else{
            $transactions = NairaTransaction::where('transaction_type_id', $id)->get();
            $segment = TransactionType::find($id)->name;
        }

        return view('admin.naira_transactions', compact(['segment', 'transactions' ]));
    }

    public function adminWallet()
    {
        $n = Auth::user()->nairaWallet;
        if (!$n) {
            return redirect()->route('user.portfolio')->with(['error' => 'No Naira wallet associated to this account']);
        }
        $credit_txns = NairaTransaction::whereIn('transaction_type_id', [5, 16, 17] )->latest()->paginate(1000);
        $debit_txns = NairaTransaction::whereIn('transaction_type_id', [4, 6] )->latest()->paginate(1000);

        return view('admin.admin_wallet', compact(['n',  'credit_txns', 'debit_txns']) );
    }


    public function getUser($email)
    {
        $user = User::where('email', $email)->first();
        return response()->json($user);
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact(['users']));
    }

    public function user($id, $email)
    {
        $user = User::find($id);
        $transactions = $user->transactions;

        $wallet_txns = NairaTransaction::where('cr_user_id', $user->id)->orWhere('dr_user_id', $user->id)->orderBy('id', 'desc')->paginate(20);
        $dr_total = 0;
        $cr_total = 0;
        foreach ($wallet_txns as $t ) {
            if ($t->cr_user_id == $user->id) {
                $t->trans_type = 'Credit';
                $cr_total += $t->amount;
            } else {
                $t->trans_type = 'Debit';
                $dr_total += $t->amount;
            }

        }

        return view('admin.user', compact(['user', 'transactions', 'wallet_txns', 'dr_total', 'cr_total' ]));
    }

    public function searchUser(Request $r)
    {
        $users = User::where('email', 'like', '%'.$r->q.'%')->orWhere('first_name', 'like', '%'.$r->q.'%')->orWhere('last_name', 'like', '%'.$r->q.'%')->paginate(20);
        /* dd($users); */
        return view('admin.users', compact(['users']));
    }

    public function verify()
    {
        $users = User::where('status', 'waiting')->orderBy('updated_at', 'asc')->get();
        return view('admin.verify', compact(['users']));
    }

    public function verifyUser(Request $request)
    {
        $user = User::find($request->id);
        $user->status = $request->status;
        $user->save();

        $not = Notification::create([
            'user_id' => $user->id,
            'title' => 'Account update',
            'body' => 'The status of your account has been updated to ' . $user->status,
        ]);
        return redirect()->back()->with(['success' => 'User Status updated']);
    }

    public function walletId(Request $request)
    {
        $setting = Setting::updateOrCreate(
            [
                'name' => $request->name,
            ],
            [
                'value' => $request->value,
            ]
        );
        return redirect()->back()->with(['success' => 'Saved']);
    }

    public function notification()
    {
        $notifications = Notification::where('user_id', 0)->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.notification', compact(['notifications']));
    }

    public function addNotification(Request $r)
    {
        $n = new Notification();
        $n->title = $r->title;
        $n->body = $r->body;
        $n->save();

        return redirect()->back()->with(['success' => 'Notification sent']);
    }

    public function editNotification(Request $r)
    {
        $n = Notification::find($r->id);
        $n->title = $r->title;
        $n->body = $r->body;
        $n->save();

        return redirect()->back()->with(['success' => 'Notification updated']);
    }

    public function getNotification($id)
    {
        $not = Notification::find($id);
        return response()->json($not);
    }

    public function deleteNotification($id)
    {
        $not = Notification::find($id);
        return response()->json($not->delete());
    }


}
