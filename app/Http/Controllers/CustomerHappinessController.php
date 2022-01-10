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
        return (new AdminController)->transactions();
    }

    public function user($id, $email)
    {
        return(new AdminController)->user($id,$email);
    }

    public function buyTransac()
    {
        return(new AdminController)->buyTransac();
    }

    public function sellTransac()
    {
        return(new AdminController)->sellTransac();
    }

    public function assetTransac($id)
    {
        return(new AdminController)->assetTransac($id);
    }

    public function assetTransactionsSortByDate(Request $request)
    {

        return (new AdminController)->assetTransactionsSortByDate($request);
    }

    public function walletTransactions($id = null)
    {
        return (new AdminController)->walletTransactions($id);
    }
    public function walletTransactionsSortByDate(Request $request)
    {
        return (new AdminController)->walletTransactionsSortByDate($request);
    }

    public function UtilityTnx(Request $request)
    {
        return (new UtilityTransactions)->index($request);
    }

    public function txnByStatus($status)
    {
        return (new AdminController)->txnByStatus($status);
    }

    public function search_tnx(Request $request)
    {
        return (new AdminController) ->search_tnx($request);
    }
}
