<?php

namespace App\Http\Controllers;

use App\ChatMessages;
use App\Http\Controllers\Admin\UtilityTransactions;
use App\UtilityTransaction;
use App\Transaction;
use App\NairaTransaction;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;

class CustomerHappinessController extends Controller
{
    public function index()
    {
        $g_txns = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->latest()->get()->take(4);

        $c_txns = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->latest()->get()->take(4);

        $n_txns = NairaTransaction::latest()->get()->take(4);
        $tickets = Ticket::with('subcategories','user')->latest('id')->limit(4)->get();
        $total_user = User::all()->count();
        return view('admin.customer_happiness.index',compact(['g_txns','c_txns','n_txns','tickets','total_user']));
    }

    public function QuarterlyInactive()
    {
        //?getting difference for users transactions 
    }

    public function chatDetails($status = null,$ticket = null)
    {
        if($status == "New"){
            $ticketNo = $ticket != null ? $ticket : null;
            $userTicketsList = Ticket::with('user')->where('status','open')->latest()->get();
            if($ticketNo){
                $chatMessages = ChatMessages::with('user')->where('ticket_no', $ticketNo)->get();
                return view('admin.customer_happiness.chat', compact(['userTicketsList','ticketNo','chatMessages']));
            }
            $chatMessages = null;
            return view('admin.customer_happiness.chat', compact(['userTicketsList','ticketNo','chatMessages'])); 
        }

        if($status == "Close"){
            $ticketNo = $ticket != null ? $ticket : null;
            $userTicketsList = Ticket::with('user')->where('status','close')->latest()->get();
            if($ticketNo){
                $chatMessages = ChatMessages::with('user')->where('ticket_no', $ticketNo)->get();
                return view('admin.customer_happiness.chat', compact(['userTicketsList','ticketNo','chatMessages']));
            }
            $chatMessages = null;
            return view('admin.customer_happiness.chat', compact(['userTicketsList','ticketNo','chatMessages'])); 
        }

        if($status == "Closed"){
            $ticketNo = $ticket != null ? $ticket : null;
            $tickedDetails = Ticket::where('ticketNo',$ticketNo)->first();
            $tickedDetails->update([
                'status' => 'close',
            ]);
            return redirect()->route('customerHappiness.chatdetails', 'New');
            
        }

        if($status == "Opened"){
            $ticketNo = $ticket != null ? $ticket : null;
            $tickedDetails = Ticket::where('ticketNo',$ticketNo)->first();
            $tickedDetails->update([
                'status' => 'open',
            ]);
            return redirect()->route('customerHappiness.chatdetails', 'Close');
            
        }

    }

    public function chat(Request $request)
    {
        $is_agent = Auth::user()->role == '555' ? 1 : 0;
        ChatMessages::create([
            'ticket_no' => $request->ticketNo,
            'user_id' => Auth::user()->id,
            'message' => $request->message,
            'is_agent' => $is_agent,
        ]);

        return back();
    }

    //! Transaction
    public function transactions()
    {
        $transactions = Transaction::latest('id')->paginate(1000);
        $segment = 'All';

        return view('admin.transactions', compact(['transactions', 'segment']));
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

        if ($user->btcWallet) {
            $btc_wallet = $user->btcWallet;

            $client = new Client();

            $url = env('TATUM_URL') . '/ledger/account/' . $btc_wallet->account_id;
            $res = $client->request('GET', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')]
            ]);
            $res = json_decode($res->getBody());
            $btc_wallet->balance = $res->balance->availableBalance;


            $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
            $get_txns = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                "json" => ["id" => $btc_wallet->account_id]
            ]);

            $btc_transactions = json_decode($get_txns->getBody());
            foreach ($btc_transactions as $t) {
                $x = \Str::limit($t->created, 10, '');
                $time = \Carbon\Carbon::parse((int)$x);
                $t->created = $time->setTimezone('Africa/Lagos');
            }
        }else {
            $btc_wallet = null;
            $btc_transactions = [];
        }

        return view('admin.user', compact(['user', 'transactions', 'wallet_txns', 'btc_wallet', 'btc_transactions', 'dr_total', 'cr_total']));
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

    public function assetTransac($id)
    {
        $transactions = Transaction::whereHas('asset', function ($query) use ($id) {
            $query->where('is_crypto', $id);
        })->latest()->paginate(10);

        $segment = 'Gift Card';
        if ($id == 1) {
            $segment = 'Crypto';
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

    public function walletTransactions($id = null)
    {
        if ($id == null) {
            $transactions = NairaTransaction::latest()->orderBy('created_at','desc')->paginate(1000);
            $segment = 'All Wallet';
            $total = $transactions->sum('amount');
            
        } else {
            $transactions = NairaTransaction::where('transaction_type_id', $id)->orderBy('created_at','desc')->paginate(1000);
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

    public function UtilityTnx(Request $request)
    {
        $transactions['transactions'] = UtilityTransaction::whereNotNull('id')->orderBy('created_at', 'desc');

        $data = $request->validate([
            'start' => 'date|string',
            'end' => 'date|string',
        ]);

        if (!empty($data)) {
            $transactions['transactions'] = $transactions['transactions']->where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end']);   
        }

        $transactions['transactions'] = $transactions['transactions']->paginate(200);
        return view('admin.utility-transactions',$transactions);
    }

    public function txnByStatus($status)
    {
        $transactions = Transaction::where('status', $status)->latest()->paginate(1000);
        $segment = $status;
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function search_tnx(Request $request)
    {
        // dd($request);
        if($request->segment == 'All')
        {
            $search = $request->search;
            $conditions = ['uid','card','type','country','card_type','status'];                         
            $transactions = Transaction::where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)                                
                    $query->orWhere($column, 'like',"%{$search}%")
                    ->orWhereHas('user', function($q) use($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                        });      
            })->paginate(100); 
            

            $segment = $request->segment;

            return view('admin.transactions', compact(['transactions', 'segment']));
        }
        if($request->segment == 'Buy' || $request->segment == 'Sell')
        {
            $status = $request->segment;
            $search = $request->search;
            $conditions = ['uid','card','country','card_type','status'];                         
            $transactions = Transaction::where('type', $status)->latest()->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)                                
                    $query->orWhere($column, 'like',"%{$search}%")
                    ->orWhereHas('user', function($q) use($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                        });      
            })->paginate(100); 
            

            $segment = $status;
            return view('admin.transactions', compact(['transactions', 'segment']));
        }
        
        if($request->segment == 'Utility')
        {
            $search = $request->search;
            $conditions = ['reference_id','amount','type','status'];                         
            $transactions = UtilityTransaction::whereNotNull('id')->orderBy('created_at', 'desc')->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)                                
                    $query->orWhere($column, 'like',"%{$search}%")
                    ->orWhereHas('user', function($q) use($search) {
                            $q->where('first_name', 'like', '%' . $search . '%');
                        });      
            })->paginate(100); 
            return view('admin.utility-transactions', compact('transactions'));
        }
        if($request->segment == 'Gift Card' || $request->segment == 'Crypto')
        {
            $id = $request->segment == 'Gift Card' ? 0:1;
            $search = $request->search;
            $conditions = ['uid','card','type','country','card_type','status'];
            $transactions = Transaction::whereHas('asset', function ($query) use ($id) {
                $query->where('is_crypto', $id);
            })->latest()->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)                                
                    $query->orWhere($column, 'like',"%{$search}%")
                    ->orWhereHas('user', function($q) use($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                        });      
            })->paginate(100);
    
            $segment = $request->segment;
            return view('admin.transactions', compact(['transactions', 'segment']));
        }

        if($request->segment == 'All Wallet')
        {
            $search = $request->search;
            $conditions = ['reference','status'];
            $transactions = NairaTransaction::latest()->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)                                
                    $query->orWhere($column, 'like',"%{$search}%")
                    ->orWhereHas('user', function($q) use($search) {
                            $q->where('first_name', 'like', '%' . $search . '%');
                        })->orWhereHas('transactionType', function($q) use($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });      
            })->paginate(100);;
            $segment = 'All Wallet';
            $total = NairaTransaction::latest()->sum('amount');

            
            return view('admin.naira_transactions', compact(['segment', 'transactions', 'total']));
        }   
        if($request->segment == 'success' || $request->segment == 'approved' || $request->segment == 'in progress'
        || $request->segment == 'waiting'|| $request->segment == 'declined' || $request->segment == 'failed')
        {
            $search = $request->search;
            $conditions = ['uid','card','type','country','card_type'];
            $status = $request->segment;

            $transactions = Transaction::where('status', $status)->latest()->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)                                
                    $query->orWhere($column, 'like',"%{$search}%")
                    ->orWhereHas('user', function($q) use($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                        });      
                    })->paginate(100);
            $segment = $status;
            return view('admin.transactions', compact(['transactions', 'segment']));
        }
    }
}
