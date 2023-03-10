<?php

namespace App\Http\Controllers\ApiV2\Customerhappiness;

use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\NairaWallet;
use App\QueryCategory;
use App\Ticket;
use App\TicketCategory;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use App\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\FirebasePushNotificationController;
use App\Mail\GeneralTemplateOne;
use App\Notification;
use App\VerificationLimit;

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

        $tickets = Ticket::with('subcategories', 'user')->latest('id')->get();
        $c_ticket_count = Ticket::where('status', 'close')->count();
        $o_ticket_count = Ticket::where('status', 'open')->count();

        return response()->json([
            'success' => true,
            'query_summary' => $tickets,
            'query_count_closed' => $c_ticket_count,
            'query_count_open' => $o_ticket_count,

        ]);

    }

    public function newTicket(Request $req)
    {

        $req->validate([
            'description' => 'required',
            'type' => 'required',
            'channel' => 'required',
            'username' => 'required',
            'category' => 'required',
            'category_description' => 'required',
            'status' => ['required', 'in:open,close'],
        ]);

        // $agent_id = User::where('role', 555)->where('status', 'active')->first();

        $complainer_id = User::where('email', $req->username)->first();
        $user_id = $complainer_id->id;

        if (!$complainer_id) {
            $user_id = 1;

        }

        $ticket = Ticket::create([
            'username' => $req->username,
            'ticketNo' => time(),
            'user_id' => $user_id,
            'agent_id' => Auth::user()->id,
            'description' => $req->description,
            'status' => $req->status,
            'agent_name' => Auth::user()->first_name . " " . Auth::user()->last_name,
            'type' => $req->type,
            'channel' => $req->channel,
            'category' => $req->category,
            'category_description' => $req->category_description,
        ]);

        // $category = $this->getCategory($req->subcategory_id);
        // $subcartegory = $this->getSubCategory($req->subcategory_id);

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

        $ticket = Ticket::with('user')->where('status', $status)->latest('id')->get()->paginate(50);

        return response()->json([
            'success' => true,
            'query_summary' => $ticket,

        ]);

    }

    public function getList()
    {

        $ticketcategory = QueryCategory::select('name')->distinct()->get()->map(function ($thing) {
            return [
                $thing->name,

            ];
        })->toArray();

        $channel = ['Facebook', 'Instagram', 'Twitter', 'Phone Call', 'Jivo', 'Mail', 'iMessage'];
        $types = ['Enquiry', 'Complaint', 'Notice', 'Suggestion'];
        $categories = $ticketcategory;

        $status = ['close', 'open'];

        foreach ($categories as $k) {

            $result[] = array('value' => $k[0], 'label' => $k[0]);
        }

        $chAgents = User::where('role', 555)->select('first_name')->distinct()->get()->map(function ($agent) {
            return [
                $agent->first_name,
            ];
        })->toArray();

        foreach ($chAgents as $agent) {
            $agents[] = array('value' => $agent[0], 'label' => $agent[0]);
        }

        $data = (object) array('channel' => $channel, 'type' => $types, 'category' => $result, 'agents' => $agents, 'status' => $status);

        return response()->json([
            'success' => true,
            'data' => $data,

        ]);

    }

    public function listofCategories($category)
    {

        if (strpos($category, 'naira') !== false || strpos($category, 'Naira') !== false) {

            $description = QueryCategory::select('description')->where('name', 'Naira wallet and Withdrawals issues')->get()->map(function ($thing) {
                return [
                    $thing->description,

                ];
            })->toArray();

            foreach ($description as $k) {

                $result[] = array('value' => $k[0], 'label' => $k[0]);
            }
            return response()->json([
                "success" => true,
                "query" => $result,
            ], 200);
        }

        // }

        if (strpos($category, 'crypto') !== false || strpos($category, 'Crypto') !== false) {

            // $ticketcategory = QueryCategory::select('name')->where('name', 'Crypto issues')->first();
            $description = QueryCategory::select('description')->where('name', 'Crypto issues')->get()->map(function ($thing) {
                return [
                    $thing->description,

                ];
            })->toArray();

            foreach ($description as $k) {

                $result[] = array('value' => $k[0], 'label' => $k[0]);
            }
            // $data = (object)  $description;

            return response()->json([
                "success" => true,
                "query" => $result,
            ], 200);
        }

        if (strpos($category, 'gift') !== false || strpos($category, 'Gift') !== false) {

            // $ticketcategory = QueryCategory::select('name')->where('name', 'Gift Card issuess')->first();
            $description = QueryCategory::select('description')->where('name', 'Gift Card issues')->get()->map(function ($thing) {
                return [
                    $thing->description,

                ];
            })->toArray();

            foreach ($description as $k) {

                $result[] = array('value' => $k[0], 'label' => $k[0]);
            }

            return response()->json([
                "success" => true,
                "query" => $result,
            ], 200);
        }

        if (strpos($category, 'system') !== false || strpos($category, 'System') !== false) {

            // $ticketcategory = QueryCategory::select('name')->where('name', 'System and Account issues')->first();
            $description = QueryCategory::select('description')->where('name', 'System and Account issues')->get()->map(function ($thing) {
                return [
                    $thing->description,

                ];
            })->toArray();

            foreach ($description as $k) {

                $result[] = array('value' => $k[0], 'label' => $k[0]);
            }

            return response()->json([
                "success" => true,
                "query" => $result,
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "wrong filter",
            ], 404);
        }

    }

    public function sortByDay()
    {

        $date = request('date');
        if (empty($date)) {
            $date = Carbon::now()->format('Y-m-d');
        }

        $ticket = Ticket::where(DB::raw('date(created_at)'), $date)->with('user')->latest('id')->get()->paginate(20);

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

    // public function transactions()
    // {
    //     $transactions = Transaction::with('user')->latest('id')->paginate(20);

    //     return response()->json([
    //         'success' => true,
    //         'transaction' => $transactions,

    //     ]);
    // }

    public function trialTransactions1()
    {
        $nairaTransactions = NairaTransaction::whereIn('transaction_type_id', [20, 19, 24, 5, 4])->orderBy('id', 'DESC')->paginate(100);
        $transactions = Transaction::with('user')->orderBy('id', 'DESC')->paginate(100);
        $array = [];
        foreach ($nairaTransactions as $nt) {
            $tranx = $transactions->where('uid', substr($nt->narration, -13, 13))->first();
            if ($tranx) {
                $tranx->nairaTransaction = $nt;
                $array[] = $tranx;
            } else {
                $array[] = $nt;
            }
        }
        return response()->json([
            'success' => true,
            'usersData' => $array,
        ], 200);
    }

    public function transactionsAll()
    {
        $researches = DB::table('naira_transactions')
            ->whereIn('transaction_type_id', [20, 19, 24, 5, 4])
            ->join('transactions', DB::raw("SUBSTRING(naira_transactions.narration, -13, 13)"), '=', 'transactions.uid')
            ->select('transactions.id', 'transactions.uid', 'transactions.user_first_name', 'transactions.user_email', 'transactions.user_id', 'transactions.card', 'transactions.type', 'transactions.amount', 'transactions.amount_paid',
                'transactions.status', 'transactions.card_type', 'transactions.quantity', 'transactions.card_price', 'transactions.created_at', 'transactions.updated_at', 'transactions.ngn_rate',
                'naira_transactions.previous_balance', 'naira_transactions.current_balance', DB::raw("SUBSTRING(naira_transactions.narration, -13, 13) as naira_transactions_uid"))
            ->orderBy('transactions.id', 'DESC')
            ->paginate(100);

        // $researches = DB::table('naira_transactions')
        //     ->whereIn('transaction_type_id', [20, 19, 24, 5, 4])
        //     ->join('transactions', DB::raw("SUBSTRING_INDEX(naira_transactions.narration,' ', -1)"), '=', 'transactions.uid')
        //     ->select('transactions.id', 'transactions.uid', 'transactions.user_email', 'transactions.user_id', 'transactions.card', 'transactions.type', 'transactions.amount', 'transactions.amount_paid',
        //         'transactions.status', 'transactions.card_type', 'transactions.quantity', 'transactions.card_price', 'transactions.created_at', 'transactions.updated_at', 'transactions.ngn_rate',
        //         'naira_transactions.previous_balance', 'naira_transactions.current_balance', DB::raw("SUBSTRING_INDEX(naira_transactions.narration,' ', -1) as naira_transactions_uid"))
        //     ->orderBy('transactions.id', 'DESC')
        //     ->paginate(100);

        return response()->json([
            'success' => true,
            'usersData' => $researches,
        ], 200);
    }

    public function p2pTran()
    {
        $transactions = NairaTrade::with('user', 'naira_transactions')->latest('id')->paginate(20);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);
    }

    public function sortP2pbyStatus($status)
    {
        $transactions = NairaTrade::where('status', $status)->get();

        dd($transactions->status);

        if ($status != "cancelled" || $status != "pending" || $status != "success") {

            return response()->json([
                'success' => false,
                'message' => "No such status",

            ]);

        }

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);
    }

    public function searchUser($email)
    {

        $user = User::where('email', 'like', '%' . $email . '%')->get();

        return response()->json([
            'success' => true,
            'users' => $user,

        ]);

    }

    // User Section


    public function searchUserwithCount($search)
    {

        $user = User::whereHas('transactions')->withCount(['transactions'])->where('email', 'like', '%' . $search . '%')->orWhere('first_name', 'like', '%' . $search . '%')->orWhere('last_name', 'like', '%' . $search . '%')->get();

        return response()->json([
            'success' => true,
            'users' => $user,

        ]);

    }

    public function getUsers()
    {
        // $user = User::latest('id')->where('role', 1)->withCount('user_id')->get();
        // $transperuser = Transaction::All()->where('user_id', $id);
        // $user = User::whereHas('transactions', function($q) {
        //     $q->where('role', '1')->get();

        // });

        $user = User::whereHas('transactions')->withCount(['transactions'])->get()->paginate(30);

        return response()->json([
            'success' => true,
            'users' => $user,

        ]);

    }

    // Each user details

    public function transPerUser($id)
    {
        $tranx = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
        // ->join('naira_wallets', 'transactions.user_id', '=', 'naira_wallets.id')
            ->select('first_name', 'last_name', 'username', 'dp', 'transactions.id', 'user_id', 'card as transaction', 'amount_paid as amount', 'transactions.amount as value', DB::raw('0 as prv_bal'), DB::raw('0 as cur_bal'), 'transactions.status', DB::raw('date(transactions.created_at) as date', 'transactions.created_at as created_at'))
        ;
        $tranx2 = DB::table('naira_transactions')
            ->join('users', 'naira_transactions.user_id', '=', 'users.id')
            ->select('first_name', 'last_name', 'username', 'dp', 'naira_transactions.id', 'user_id', 'type as transaction', 'amount_paid', 'naira_transactions.amount as value', 'previous_balance as prv_bal', 'current_balance as cur_bal', 'naira_transactions.status', DB::raw('date(naira_transactions.created_at) as date', 'naira_transactions.created_at as created_at'));

        $mergeTbl = $tranx->unionAll($tranx2);
        DB::table(DB::raw("({$mergeTbl->toSql()}) AS mg"))->mergeBindings($mergeTbl);

        $tranx = $mergeTbl
            ->where('transactions.user_id', [$id])
            ->orderBy('date', 'desc');

        return response()->json([
            'success' => true,
            'data' => $tranx,
        ], 200);
    }

    public function userInfo($id)
    {

        $user = User::where('id', $id)->first();
        $verification = Verification::where('user_id', $id)->first();
        $nairaBalance = NairaWallet::where('user_id', $id)->first();
        $lastTraded = NairaTrade::where('user_id', $id)->latest()->first();

        if ($verification && $user->phone_verified_at != null) {
            if ($verification->type == "ID Card" && $verification->status == "success") {
                $level = 3;
            } elseif ($verification->type == "Address" && $verification->status == "success") {
                $level = 2;
            } else {
                $level = 1;
            }
        } else {
            $level = 1;
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'balance' => $nairaBalance->amount,
            'last_traded' => $lastTraded->created_at,
            'verification_level' => $level,

        ]);

    }

//Utility Transaction

    public function sortByStatus($status)
    {

        $transactions = Transaction::with('user')->where('status', $status)->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);

    }

    public function filterByType($type)
    {

        $airtime = NairaTransaction::with('utility')->where('type', 'recharge card')->with('user')->latest()->paginate(20);
        $electricity = NairaTransaction::with('utility')->where('type', 'electricity bills')->with('user')->latest()->paginate(20);
        $data = NairaTransaction::with('utility')->where('type', 'mobile data')->with('user')->latest()->paginate(20);
        $cable = NairaTransaction::with('utility')->where('type', 'cable')->with('user')->latest()->paginate(20);

        if ($type == 'giftcard') {
            $nairaTransactions = NairaTransaction::with('user')->orderBy('id', 'DESC')->paginate(100);

            $gcard = Transaction::orderBy('id', 'DESC')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            });

            // dd($gcard);
            // $transactions = Transaction::with('user')->orderBy('id', 'DESC')->paginate(100);
            $array = [];

            foreach ($nairaTransactions as $nt) {

                $giftCard_transaction = $gcard->where('uid', substr($nt->narration, -13, 13))->get();

                if ($giftCard_transaction) {
                    $giftCard_transaction->nairaTransaction = $nt;
                    $array[] = $giftCard_transaction;
                } else {
                    $array[] = $nt;
                }
            }
            return response()->json([
                'success' => true,
                'usersData' => $array,
            ], 200);
        }

        if ($type == 'power') {
            return response()->json([
                'success' => true,
                'transaction' => $electricity,

            ]);

        }
        if ($type == 'airtime') {
            return response()->json([
                'success' => true,
                'transaction' => $airtime,

            ]);
        }
        if ($type == 'data') {
            return response()->json([
                'success' => true,
                'transaction' => $data,

            ]);
        }

        if ($type == 'cable') {
            return response()->json([
                'success' => true,
                'transaction' => $cable,

            ]);
        }

        return response()->json([
            'success' => false,
            'msg' => "Wrong filter type",

        ]);

    }

    public function filterUtility($type)
    {

        $transactions = UtilityTransaction::where('type', 'LIKE', "%{$type}%")->latest('id')->paginate(20);

        return response()->json([
            'success' => true,
            'transaction' => $transactions,

        ]);

    }

    public function viewVerifications(){
        $verifications = Verification::with('user')
            ->where('status', 'waiting')
            ->latest()
            ->paginate(20);

        //adding image to be loaded to the payload
        foreach($verifications as $v){
            $v->image = URL::to('/').'/storage/'.'idcards'.'/'.$v->path;
        }
        
        return response()->json([
            'success'=> true,
            'data' => $verifications,
        ],200);
    }

    public function approveVerification(Request $request)
    {
        if($request->verification_id == NULL){
            return response()->json([
                'success' => false,
                'message' => 'Verification data cannot be null' 
            ],422);
        }
        $verification = Verification::with('user')
        ->where('id',$request->verification_id)
        ->where('status', 'waiting')
        ->first();

        if(!$verification){
            return response()->json([
                'success' => false,
                'message' => 'Verification Data does not exist' 
            ],422);
        }
        
        if ($verification->type == 'ID Card') {
            $verification->user->idcard_verified_at = now();
            $level = VerificationLimit::where('level', "3")->first();
            $title = 'LEVEL 3 VERIFICATION SUCCESSFUL';
            $body = "Congrats " . $verification->user->first_name . ", you have successfully completed your Level 3 verification.
            Below is a breakdown of level 3 privileges. <br><br>

            <b style='color:000070'>Identity Verification<br><br>

            Daily withdrawal limit: NGN " . number_format($level->daily_widthdrawal_limit) . "<br><br>

            Monthly withdrawal limit: NGN " . number_format($level->monthly_widthdrawal_limit) . "<br><br>

            Crypto withdrawal limit: " . $level->crypto_widthdrawal_limit . "<br><br>

            Crypto deposit: " . $level->crypto_deposit . "<br><br>

            Transactions: " . $level->transactions . "<br></b>
            ";

            $btn_text = '';
            $btn_url = '';

            $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
            $name = str_replace(' ', '', $name);
            $firstname = ucfirst($name);
            Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

            $title = "Level 3 verification";
            $msg = "Level 3 verification WAS SUCCESSFUL! Your daily and monthly withdrawal limit have been increased to " . number_format($level->daily_widthdrawal_limit) . " and " . number_format($level->monthly_widthdrawal_limit) . " respectively.";

            $fcm_id = $verification->user->fcm_id;
            if (isset($fcm_id)) {
                try {
                    FirebasePushNotificationController::sendPush($fcm_id, $title, $msg);
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }

        } elseif ($verification->type == 'Address') {
            $verification->user->address_verified_at = now();
            $level = VerificationLimit::where('level', "2")->first();
            $title = 'LEVEL 2 VERIFICATION SUCCESSFUL';
            $body = "Congrats " . $verification->user->first_name . ", you have successfully completed your Level 2 verification.
            Below is a breakdown of level 2 privileges. <br><br>

            <b style='color:000070'>Address Verification<br><br>

            Daily withdrawal limit: NGN " . number_format($level->daily_widthdrawal_limit) . "<br><br>

            Monthly withdrawal limit: NGN " . number_format($level->monthly_widthdrawal_limit) . "<br><br>

            Crypto withdrawal limit: " . $level->crypto_widthdrawal_limit . "<br><br>

            Crypto deposit: " . $level->crypto_deposit . "<br><br>

            Transactions: " . $level->transactions . "<br></b>
            ";

            $btn_text = '';
            $btn_url = '';

            $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
            $name = str_replace(' ', '', $name);
            $firstname = ucfirst($name);
            Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

            $title = "Level 2 verification";
            $msg = "Level 2 verification WAS SUCCESSFUL! Your daily and monthly withdrawal limit have been increased to " . number_format($level->daily_widthdrawal_limit) . " and " . number_format($level->monthly_widthdrawal_limit) . " respectively.";

            $fcm_id = $verification->user->fcm_id;
            if (isset($fcm_id)) {
                try {
                    FirebasePushNotificationController::sendPush($fcm_id, $title, $msg);
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }

        $verification->user->save();
        $verification->status = 'success';
        $verification->verified_by = Auth::user()->id;

        $verification->save();

        Notification::create([
            'user_id' => $verification->user->id,
            'title' => 'Verification successful',
            'body' => 'Congratulations, your ' . $verification->type . ' has been verified',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Verification Successful"
        ],200);
    }

    public function cancelVerification(Request $request)
    {
        if($request->verification_id == NULL OR $request->type == NULL OR $request->reason == NULL){
            return response()->json([
                'success' => false,
                'message' => 'Verification data cannot be null' 
            ],422);
        }
        $verification = Verification::with('user')
        ->where('id',$request->verification_id)
        ->first();

        if ($request->type == 'Address') {
            $title = "LEVEL 2 VERIFICATION FAILED";
            $bodyTitle = 'level 2 verification';
            $fcmNotice = "";
            if ($request->reason == 'Uploaded a wrong information') {
                $request->reason = 'You uploaded a wrong information';
                $suggestion = 'You are required to upload your bank statement that contains both your home address and name on Dantown.
                ';
                $fcmNotice = "Upload a bank statement that contains your name and the address inputted.";
            } elseif ($request->reason == 'Unclear uploaded document') {
                $request->reason = 'The document you uploaded is not unclear';
                $suggestion = 'Please upload a clear image of your bank statement where your name and home address is clearly visible.
                ';
                $fcmNotice = "Upload a visible image of your bank statement.";
            } elseif ($request->reason == 'Full image of the document was not uploaded') {
                $request->reason = 'The full image of the document was not uploaded';
                $suggestion = 'The image of the bank statement uploaded has some missing data. Please upload the full image of the statement.
                ';
                $fcmNotice = "Upload the full image of the bank statement, where the required data are visible.";
            } elseif ($request->reason == 'A mismatch of information') {
                $request->reason = 'There is a mismatch of information';
                $suggestion = 'Please ensure that the address you filled matches that on the bank statement you uploaded.
                ';
                $fcmNotice = "Ensure the address inputted and that on the bank statement are similar.";
            }
        } else {
            $title = "LEVEL 3 VERIFICATION FAILED";
            $bodyTitle = 'level 3 verification';
            if ($request->reason == 'Uploaded a wrong information') {
                $request->reason = 'You uploaded a wrong information';
                $suggestion =
                    'Please upload any national approved identity       verification document with your name.<br>
                        IDs accepted are; <br>
                        National identity card, <br>
                        NIMC slip, <br>
                        International Passport, <br>
                        Permanent Voter???s card, <br>
                        Driver???s license.<br>
                ';
                $fcmNotice = "Upload an authorized form of identification that contains your name.";
            } elseif ($request->reason == 'Unclear uploaded document') {

                $request->reason = 'The document you uploaded is not unclear';
                $suggestion = 'Please  upload a clear image of the required document that clearly shows your name and other relevant information.
                ';
                $fcmNotice = "Upload a visible image of the means of identification.";
            } elseif ($request->reason == 'Full image of the document was not uploaded') {

                $request->reason = 'The full image of the document was not uploaded';
                $suggestion = 'The image of the document you uploaded has some data missing. Please upload a full image of the document.
                ';
                $fcmNotice = "Upload the full image of the means of identification, where the required data are visible.";
            } elseif ($request->reason == 'A mismatch of information') {

                $request->reason = 'There is a mismatch of information';
                $suggestion = 'Please upload a document that contains your name on Dantown.
                ';
                $fcmNotice = "Please ensure your name matches that on the means of identification.";
            }
        }

        $body = "We cannot proceed with your " . $bodyTitle . ".<br><br>
        This is because: <br><b>" . $request->reason . "</b> <br><br><b>" . $suggestion . "</b><br><br>

        Please send an email to <a style='text-decoration:none' href='mailto:support@godantown.com'>support@godantown.com</a> if you have questions or complaints";
        $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);

        $btn_text = '';
        $btn_url = '';

        // dd($paragraph);

        Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
        // return back()->with(['success' => 'User verification cancelled']);

        $msg = "Your " . $bodyTitle . " was declined because " . $request->reason . ". Kindly " . $fcmNotice . ".";
        $fcm_id = $verification->user->fcm_id;

        if (!empty($fcm_id)) {
            try {
                FirebasePushNotificationController::sendPush($fcm_id, $title, $msg);
            } catch (\Throwable $th) {
                // throw $th;
            }
        }

        $verification->status = 'failed';

        if ($request->type == 'Address'){
            $verification->user->address_verified_at = NULL;
            $verification->user->save();
        }
        else {
            $verification->user->idcard_verified_at = NULL;
            $verification->user->save();
        }


        $verification->verified_by = Auth::user()->id;
        $verification->save();
        // dd( $verification->user->address_verified_at);

        Notification::create([
            'user_id' => $verification->user->id,
            'title' => 'Verification failed',
            'body' => 'Sorry, your ' . $verification->type . ' could not be verified. Please check the document and try again',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Verification Cancelled Successful"
        ],200);
    }
}
