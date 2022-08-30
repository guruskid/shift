<?php

namespace App\Http\Controllers\ApiV2\Customerhappiness;

use App\Http\Controllers\Controller;
use App\Ticket;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Illuminate\Http\Request;

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

    public function transactions()
    {
        $transactions = Transaction::with('user')->latest('id')->paginate(10);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);
    }

    public function getUsers()
    {
        $user = User::latest('id')->where('role', 1)->withCount('user_id')->get();
        // $transperuser = Transaction::All()->where('user_id', $id);

        return response()->json([
            'success' => true,
            'users' => $user,

        ]);

    }

    public function transPerUser($id)
    {
        $count = Transaction::where('user_id', $id)->count();
        $transperuser = Transaction::with($count)->where('user_id', $id)->get();



        return response()->json([
            'success' => true,
            'transactions' => $transperuser,
            // 'numberoftrn' => $count

        ]);

    }

//Utility Transaction

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


        $transactions = UtilityTransaction::where('type','LIKE', "%{$type}%")->latest('id')->paginate(10);


        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);

    }
}
