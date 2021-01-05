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
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    //All new functionalities will follow the conventional method, new controllers for each model and stored in the admin folder
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


        $transactions = Transaction::latest()->get()->take(5);
        $waiting_transactions = Transaction::where('status', 'waiting')->get()->take(5);
        $in_progress_transactions = Transaction::where('status', 'in progress')->get()->take(5);
        $approved_transactions = Transaction::where('status', 'approved')->get()->take(5);

        $users = User::latest()->get()->take(4);
        $verified_users = User::where('email_verified_at', '!=', null )->count();
        $notifications = Notification::where('user_id', 0)->latest()->get()->take(5);
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

        $transfer_charge = NairaWallet::where('account_number', 0000000001)->first()->amount;
        $sms_charge = NairaWallet::where('account_number', 0000000002)->first()->amount;

        $charges = $transfer_charge + $sms_charge;
        $old_charges = NairaTransaction::sum('charge');

        $withdraw_txns = NairaTransaction::where('transaction_type_id', 3)->sum('amount');
        $airtime_txns = NairaTransaction::where('transaction_type_id', 9)->sum('amount');
        $buy_txns_wallet = NairaTransaction::where('transaction_type_id', 5)->sum('amount');

        $g_txns = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->latest()->get()->take(4);

        $c_txns = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->latest()->get()->take(4);

        $n_txns = NairaTransaction::latest()->get()->take(4);

        /* Get count of transactions from when an agent was last activated */
        $au = Auth::user();
        $a_w_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'waiting')->count();
        $a_i_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'in progress')->count();
        $a_s_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'success')->count();
        $a_a_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'approved')->count();
        $all_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->count();


        if (Auth::user()->role == 999) { //Super admin
            return view( 'admin.super_dashboard',
                compact([
                    'transactions', 'users', 'verified_users', 'users_count', 'notifications', 'usersChart',
                    'withdraw_txns', 'airtime_txns', 'buy_txns_wallet',
                    'g_txns', 'c_txns', 'n_txns',
                    'buyCash', 'sellCash', 'buyCount', 'sellCount',
                    'pBuyCash', 'pSellCash', 'pBuyCount', 'pSellCount',
                    'users_wallet_balance', 'rubies_balance', 'company_balance', 'charges', 'old_charges'
                ])
            );
        } else if (Auth::user()->role == 888) { // sales rep
            return view(
                'admin.dashboard',
                compact([
                    'transactions', 'waiting_transactions', 'in_progress_transactions',
                    'users', 'users_count', 'notifications', 'usersChart',
                    'a_w_c', 'a_s_c', 'a_a_c', 'a_i_c',
                    'buyCash', 'sellCash', 'buyCount', 'sellCount',
                    'pBuyCash', 'pSellCash', 'pBuyCount', 'pSellCount', 'users_wallet_balance', 'rubies_balance', 'company_balance'
                ])
            );
        } else if (Auth::user()->role == 889 || Auth::user()->role == 777) { //Accountants
            return view(
                'admin.accountant_dashboard',
                compact([
                    'transactions', 'approved_transactions', 'users', 'users_count', 'notifications', 'usersChart',
                    'withdraw_txns', 'airtime_txns', 'buy_txns_wallet',
                    'g_txns', 'c_txns', 'n_txns',
                    'buyCash', 'sellCash', 'buyCount', 'sellCount',
                    'pBuyCash', 'pSellCash', 'pBuyCount', 'pSellCount',
                    'users_wallet_balance', 'rubies_balance', 'company_balance', 'charges', 'old_charges'
                ])
            );
        } else if (Auth::user()->role == 666) { //Manager
            return view(
                'admin.manager_dashboard',
                compact([
                    'transactions', 'users',
                    'g_txns', 'c_txns', 'n_txns'
                ])
            );
        }
    }

    /* public function cards()
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



    public function getCard($id)
    {
        $card = Card::find($id);
        return response()->json($card);
    }

    public function deleteCard($id)
    {
        $card = Card::find($id);
        return response()->json($card->delete());
    } */

    /* public function rates()
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
    } */

    /* TRANSACTIONS */

    public function transactions()
    {
        /* $transactions = Transaction::latest()->paginate(1000); */
        $transactions = [];
        $segment = 'All';

        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function buyTransac()
    {
        $transactions = Transaction::where('type', 'buy')->latest()->paginate(1000);
        $segment = 'Buy';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function sellTransac()
    {
        $transactions = Transaction::where('type', 'sell')->latest()->paginate(1000);
        $segment = 'Sell';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function txnByStatus($status)
    {
        $transactions = Transaction::where('status', $status)->latest()->paginate(1000);
        $segment = $status;
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

        return view('admin.assigned-transactions', compact(['transactions']));
    }

    public function assetTransac($id)
    {
        $transactions = Transaction::whereHas('asset', function ($query) use ($id) {
            $query->where('is_crypto', $id);
        })->latest()->paginate(10);

        $segment = 'Crypto';
        if ($id == 1) {
            $segment = 'Gift Card';
        }


        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function assetTransactionsSortByDate(Request $request)
    {

        $data = $request->validate([
            'start' => 'required|date|string',
            'end' => 'required|date|string',
        ]);
        $transactions = Transaction::where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end'])->paginate(200);
        $segment = Carbon::parse($data['start'])->format('D d M y') . ' - ' . Carbon::parse($data['end'])->format('D d M Y') . ' Asset';

        return view('admin.transactions', compact(['segment', 'transactions']));
    }

    public function getTransac($id)
    {
        $transac = Transaction::find($id);
        return response()->json($transac);
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

    public function updateTransaction($id, $status) //to accept or decline a new transaction
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
        /* Mail::to($user->email)->send(new DantownNotification($title, $body)); */
        return response()->json(['success' => true]);
    }


    public function walletTransactions($id = null)
    {
        if ($id == null) {
            $transactions = NairaTransaction::latest()->paginate(1000);
            $segment = 'All Wallet';
            $total = $transactions->sum('amount');
        } else {
            $transactions = NairaTransaction::where('transaction_type_id', $id)->paginate(1000);
            $segment = TransactionType::find($id)->name;
            $total = $transactions->sum('amount');
        }

        return view('admin.naira_transactions', compact(['segment', 'transactions', 'total']));
    }
    public function walletTransactionsSortByDate(Request $request)
    {

        $data = $request->validate([
            'start' => 'required|date|string',
            'end' => 'required|date|string',
        ]);
        $transactions = NairaTransaction::where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end'])->paginate(1000);
        $segment = Carbon::parse($data['start'])->format('D d M y') . ' - ' . Carbon::parse($data['end'])->format('D d M Y') . ' Wallet';
        $total = $transactions->sum('amount');

        return view('admin.naira_transactions', compact(['segment', 'transactions', 'total']));
    }

    public function adminWallet()
    {
        $n = Auth::user()->nairaWallet;
        if (!$n) {
            return redirect()->route('user.portfolio')->with(['error' => 'No Naira wallet associated to this account']);
        }
        $credit_txns = NairaTransaction::whereIn('transaction_type_id', [5, 16, 17])->latest()->paginate(1000);
        $debit_txns = NairaTransaction::whereIn('transaction_type_id', [4, 6])->latest()->paginate(1000);

        return view('admin.admin_wallet', compact(['n',  'credit_txns', 'debit_txns']));
    }


    /* Old transfer charges before the database was remodified to include seperate wallets for charges */
    public function oldTransferCharges(Request $r)
    {
        if (!$r->start || !$r->end ) {
            $transactions = NairaTransaction::latest()->get();
            $total = $transactions->sum('charge');
        }else{

            $transactions = NairaTransaction::where('created_at', '>=', $r->start)->where('created_at', '<=', $r->end)->get();
            $total = $transactions->sum('charge');
        }


        return view('admin._charges', compact(['transactions', 'total']));
    }
    public function transferCharges(Request $r)
    {
        $transfer_charge = NairaWallet::where('account_number', 0000000001)->first();
        $sms_charge = NairaWallet::where('account_number', 0000000002)->first();

        $transfer_charges_txns = NairaTransaction::where('transfer_charge', '!=', 0)->latest()->paginate(1000);
        $sms_charges_txns = NairaTransaction::where('sms_charge', '!=', 0)->latest()->paginate(1000);


        return view('admin.charges', compact(['transfer_charge', 'sms_charge', 'transfer_charges_txns', 'sms_charges_txns']));
    }

    public function clearTransferCharges(Request $r)
    {
        if (Hash::check($r->password, Auth::user()->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong password entered']);
        }

        $transfer_charge = NairaWallet::where('account_number', 0000000001)->first();
        $transfer_charge->amount = 0;
        $transfer_charge->save();

        return back()->with(['success' => 'Transfer charges cleared successfully']);
    }

    public function clearSmsCharges(Request $r)
    {
        if (Hash::check($r->password, Auth::user()->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong password entered']);
        }

        $sms_charge = NairaWallet::where('account_number', 0000000002)->first();
        $sms_charge->amount = 0;
        $sms_charge->save();

        return back()->with(['success' => 'SMS charges cleared successfully']);
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

    public function verifiedUsers()
    {
        $users = User::where('email_verified_at', '!=', null)->get();
        return view('admin.users', compact(['users']));
    }

    public function user($id, $email)
    {
        $user = User::find($id);
        $transactions = $user->transactions;

        $wallet_txns = NairaTransaction::where('cr_user_id', $user->id)->orWhere('dr_user_id', $user->id)->orderBy('id', 'desc')->paginate(20);
        $dr_total = 0;
        $cr_total = 0;
        foreach ($wallet_txns as $t) {
            if ($t->cr_user_id == $user->id) {
                $t->trans_type = 'Credit';
                $cr_total += $t->amount;
            } else {
                $t->trans_type = 'Debit';
                $dr_total += $t->amount;
            }
        }

        return view('admin.user', compact(['user', 'transactions', 'wallet_txns', 'dr_total', 'cr_total']));
    }

    public function searchUser(Request $r)
    {
        $users = User::where('email', 'like', '%' . $r->q . '%')->orWhere('first_name', 'like', '%' . $r->q . '%')->orWhere('last_name', 'like', '%' . $r->q . '%')->paginate(20);
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
