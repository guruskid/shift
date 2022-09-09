<?php

namespace App\Http\Controllers\ApiV2\Customerhappiness;

use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\Ticket;
use App\TicketCategory;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use App\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerHappinessController extends Controller
{

    public function overView()
    {

        {
            // $n_txns = NairaTransaction::latest()->get()->take(4);
            $view['all'] = Transaction::with('user')->whereHas('asset', function ($query) {
                $query->whereRaw('is_crypto = 1 OR is_crypto = 0');
            })->latest('id')->get()->take(5);

            $total_user = User::all()->count();

            return response()->json([
                'success' => true,
                'general' => $view,

            ]);

        }
    }

    //Transaction Section

    public function Utility(Request $request)
    {
        $transactions['transactions'] = UtilityTransaction::whereNotNull('id')->orderBy('created_at', 'desc');
        $type = UtilityTransaction::select('type')->distinct('type')->get();
        $status = UtilityTransaction::select('status')->distinct('status')->get();
        $data = $request->validate([
            'start' => 'date|string',
            'end' => 'date|string',
        ]);

        if (!empty($data)) {
            $transactions['transactions'] = $transactions['transactions']
                ->where('created_at', '>=', $data['start'])
                ->where('created_at', '<=', $data['end']);

            if ($request->type != 'null') {
                $transactions['transactions'] = $transactions['transactions']
                    ->where('type', '=', $request->type);
            }
            if ($request->status != 'null') {
                $transactions['transactions'] = $transactions['transactions']
                    ->where('status', '=', $request->status);
            }

        }
        $total = $transactions['transactions']->sum('total');

        $total_transactions = $transactions['transactions']->count();
        $total_amount = $transactions['transactions']->sum('amount');
        $total_convenience_fee = $transactions['transactions']->sum('convenience_fee');

        $transactions['transactions'] = $transactions['transactions']->paginate(200);
        // return view('admin.utility-transactions',$transactions,compact(['type','status','total',
        // 'total_transactions','total_amount','total_convenience_fee']));

        return response()->json([
            'success' => true,
            'transaction' => $transactions,
            'type' => $type,
            'status' => $status,
            'total' => $total,
            'total_transactions' => $total_transactions,
            'total_amount' => $total_amount,
            'total_convenience_fee' => $total_convenience_fee,

        ]);
    }

    //Query List Section

    public function queries()
    {

        $tickets = Ticket::with('subcategories', 'user')->latest('id')->limit(5)->get();
        $c_ticket_count = Ticket::where('status', 'close')->count();
        $o_ticket_count = Ticket::where('status', 'open')->count();
        $open_ticket = Ticket::with('user')->where('status', 'open')->latest('id')->get()->paginate(10);
        $closed_ticket = Ticket::with('user')->where('status', 'close')->latest('id')->get()->paginate(10);

        return response()->json([
            'success' => true,
            'query_summary' => $tickets,
            'query_count_closed' => $c_ticket_count,
            'query_count_open' => $o_ticket_count,
            'all_open_queries' => $open_ticket,
            'all_close_queries' => $closed_ticket,

        ]);

    }

    public function newTicket(Request $req)
    {

        $req->validate([
            'description' => 'required',
            'subcategory_id' => 'required',
            'type' => 'required',
            'channel' => 'required',
            'username' => 'required',
        ]);

        // $agent_id = User::where('role', 555)->where('status', 'active')->first();

        $ticket = Ticket::create([
            'username' => $req->username,
            'ticketNo' => time(),
            'agent_id' => Auth::user()->id,
            'description' => $req->description,
            'status' => 'open',
            'agent_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'subcategory_id' => $req->subcategory_id,
            'type' => $req->type,
            'channel' => $req->channel,
        ]);

        $category = $this->getCategory($req->subcategory_id);
        $subcartegory = $this->getSubCategory($req->subcategory_id);

        // $message = "Category: " . $category . "\n" .
        // "SubCategory: " . $subcartegory . "\n" .
        // "Description: " . $req->description;

        // ChatMessages::create([
        //     'ticket_no' => $ticket->ticketNo,
        //     'user_id' => Auth::user()->id,
        //     'message' => $message,
        //     'is_agent' => 0,
        // ]);

        if (empty($ticket)) {
            return response()->json([
                "success" => false,
                "message" => "Error in creating",
            ], 500);
        }

        return response()->json([
            "success" => true,
            "queryinfo" => $ticket,

        ], 200);
    }

    public function closeQuery($id)
    {
        $query = Ticket::find($id);
        $query->status = 'close';
        $query->closed_by = Auth::user()->id;
        $query->agent_name = Auth::user()->first_name . ' ' . Auth::user()->last_name;

        $query->save();

        return response()->json([
            "success" => true,
            'msg' => 'Query closed successfully',
        ], 200);
    }

    public function getCategory($subcartegory_id)
    {
        $category_id = TicketCategory::find($subcartegory_id)->ticket_category_id;
        return TicketCategory::find($category_id)->name;
    }

    public function getSubCategory($subcartegory_id)
    {
        return TicketCategory::find($subcartegory_id)->name;
    }

    public function querySort($status)
    {

        $ticket = Ticket::with('user')->where('status', $status)->latest('id')->get()->paginate(10);

        return response()->json([
            'success' => true,
            'query_summary' => $ticket,

        ]);

    }

    public function sortByDay()
    {

        $date = request('date');
        if (empty($date)) {
            $date = Carbon::now()->format('Y-m-d');
        }

        $ticket = Ticket::where(DB::raw('date(created_at)'), $date)->with('user')->latest('id')->get()->paginate(10);

//    dd($query);
        return response()->json([
            'success' => true,
            'querylist' => $ticket,

        ]);

    }

    public function sortByRange(Request $req)
    {

        $ticket = '';

        $rangeType = $req['rangetype'];

        if ($rangeType == 'byyear') {
            $year = request('year');
            $ticket = Ticket::where(DB::raw('date(created_at)'), $year)->with('user')->latest('id')->get();
        }

        if ($rangeType == 'byrange') {
            $start = request('start');
            $end = request('end');
            $ticket = Ticket::whereBetween(DB::raw('date(created_at)'), [$start, $end])->with('user')->latest('id')->get();

        }

        if ($rangeType == 'bymonth') {
            $date = request('date');
            $single = Carbon::createFromDate($date);
            $month = $single->format('m');
            $year = $single->format('Y');
            $ticket = Ticket::where(DB::raw('month(created_at)'), $month)->where(DB::raw('year(created_at)'), $year)->with('user')->latest('id')->get();
        }
           $data = $ticket;
        return response()->json([
            'success' => true,
            'tickets' => $data,
        ]);

    }

    public function transactions()
    {
        $transactions = Transaction::with('user')->latest('id')->paginate(10);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);
    }

    public function p2pTran()
    {
        $transactions = NairaTrade::with('user')->latest('id')->paginate(10);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);
    }

    public function sortP2pbyStatus($status)
    {
        $transactions = NairaTrade::with('user')->where('status', $status)->latest('id')->paginate(10);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);
    }

    public function searchUser($username)
    {

        $user = User::where('username', 'like', '%' . $username . '%')->orWhere('first_name', 'like', '%' . $username . '%')->get();

        return response()->json([
            'success' => true,
            'users' => $user,

        ]);

    }

    // User Section

    public function getUsers()
    {
        // $user = User::latest('id')->where('role', 1)->withCount('user_id')->get();
        // $transperuser = Transaction::All()->where('user_id', $id);
        // $user = User::whereHas('transactions', function($q) {
        //     $q->where('role', '1')->get();

        // });

        $user = User::whereHas('transactions')->withCount(['transactions'])->get()->paginate(20);

        return response()->json([
            'success' => true,
            'users' => $user,

        ]);

    }

    public function transPerUser($id)
    {
        // $count = Transaction::where('user_id', $id)->count();
        $transperuser = Transaction::where('user_id', $id)->latest('id')->paginate(10);
        // $verification = Verification::where('user_id', $id)->where('status', 'success');

        // if($verification->type == "ID Card"){
        //     $level = 3;
        // }
        // elseif ($verification->type == "Address") {
        //     $level = 2;
        // }

        // dd($verification->type);

        return response()->json([
            'success' => true,
            'transactions' => $transperuser,
            // 'numberoftrn' => $count

        ]);

    }

    // Each user details

    public function userInfo($id)
    {

        $user = User::with('nairaWallet', 'nairaTrades')->where('id', $id)->first();
        $verification = Verification::where('user_id', $id)->first();

        if ($verification->type == "ID Card" && $verification->status == "success") {
            $level = 3;
        } elseif ($verification->type == "Address" && $verification->status == "success") {
            $level = 2;
        } else {
            $level = 1;
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'verification_level' => $level,

        ]);

    }

//Utility Transaction

    public function sortByStatus($status)
    {

        $transactions = Transaction::with('user')->where('status', $status)->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);

    }

    public function filterByType($type)
    {

        $transactions = Transaction::whereHas('asset', function ($fitler) use ($type) {
            $fitler->where('is_crypto', $type);
        })->latest('id')->paginate(10);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);

    }

    public function filterUtility($type)
    {

        $transactions = UtilityTransaction::where('type', 'LIKE', "%{$type}%")->latest('id')->paginate(10);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);

    }
}
