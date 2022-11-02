<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Bank;
use App\Exports\PayBridgeTransactions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FirebasePushNotificationController;
use App\NairaTrade;
use App\NairaTransaction;
use App\NairaWallet;
use App\User;
use App\PayBridgeAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\GeneralTemplateOne;
use App\WithdrawalQueueRange;
use Illuminate\Support\Facades\Mail;
use DB;
use Excel;

class TradeNairaController extends Controller
{
    public function index()
    {
        $users = User::where('role', 777)->get();
        $users->each(function ($u) {
            $u->success = $u->agentNairaTrades()->where('status', 'success')->count();
            $u->cancelled = $u->agentNairaTrades()->where('status', 'cancelled')->count();
            $u->pending = $u->agentNairaTrades()->whereIn('status', ['pending', 'waiting'])->count();
        });

        return view('admin.trade_naira.index', compact('users'));
    }


    public function transactions(Request $request)
    {
        $paginate = true;
        $request->session()->forget(['sort_start_date', 'sort_end_date','search_tnx']);
        if (!Auth::user()->agentLimits) {
            Auth::user()->agentLimits()->create();
        }

        $show_limit = true;
        $exportTranx = NairaTrade::orderBy('created_at', 'desc')->get();
        $transactions = NairaTrade::with('naira_transactions','account')->orderBy('created_at', 'desc')
        ->where('is_dailyLimit',0)->where('is_monthlyLimit',0)->paginate(20);
        $banks = Bank::all();
        $account = Auth::user()->accounts->first();

        //?" all  deposit transactions
        $deposit = NairaTrade::where('type','deposit')
        ->where('is_dailyLimit',0)->where('is_monthlyLimit',0)->get();
        $deposit_all_tnx = $deposit->count();

        //? successful Deposit
        $deposit_success = $deposit->where('status','success');
        $deposit_success_tnx = $deposit_success->count();
        $deposit_success_amount = $deposit_success->sum('amount');

        //? unresolved Deposit
        $deposit_unresolved = $deposit->where('status','unresolved');
        $deposit_unresolved_tnx = $deposit_unresolved->count();
        $deposit_unresolved_amount = $deposit_unresolved->sum('amount');

        //? declined Deposit
        $deposit_denied = $deposit->where('status','cancelled');
        $deposit_denied_tnx = $deposit_denied->count();
        $deposit_denied_amount = $deposit_denied->sum('amount');

        //? waiting Deposit
        $deposit_waiting = $deposit->where('status','waiting');
        $deposit_waiting_tnx = $deposit_waiting->count();
        $deposit_waiting_amount = $deposit_waiting->sum('amount');


        //?" all  withdrawal transactions
        $withdrawal = NairaTrade::where('type','withdrawal')
        ->where('is_dailyLimit',0)->where('is_monthlyLimit',0)->get();
        $withdrawal_all_tnx = $withdrawal->count();

        //? successful withdrawal
        $withdrawal_success = $withdrawal->where('status','success');
        $withdrawal_success_tnx = $withdrawal_success->count();
        $withdrawal_success_amount = $withdrawal_success->sum('amount');

        $withdrawal_unresolved = $withdrawal->where('status','unresolved');
        $withdrawal_unresolved_tnx = $withdrawal_unresolved->count();
        $withdrawal_unresolved_amount = $withdrawal_unresolved->sum('amount');

        //? declined withdrawal
        $withdrawal_denied = $withdrawal->where('status','cancelled');
        $withdrawal_denied_tnx = $withdrawal_denied->count();
        $withdrawal_denied_amount = $withdrawal_denied->sum('amount');

        //? waiting withdrawal
        $withdrawal_waiting = $withdrawal->where('status','waiting');
        $withdrawal_waiting_tnx = $withdrawal_waiting->count();
        $withdrawal_waiting_amount = $withdrawal_waiting->sum('amount');

        if(isset($request['downloader']) AND $request['downloader'] == 'csv'){
            return Excel::download(new PayBridgeTransactions($exportTranx), 'PayBridgeTransactions.xlsx');
        }

        $segment = "All";
        $type = null;
        $status = null;
        return view('admin.trade_naira.transactions', compact([
            'status','type','paginate',
            'transactions', 'show_limit', 'banks', 'account','segment',
            'deposit_all_tnx','deposit_success_tnx','deposit_success_amount',
            'deposit_denied_tnx','deposit_denied_amount','deposit_waiting_tnx','deposit_waiting_amount',
            'deposit_unresolved_tnx','deposit_unresolved_amount',
            'withdrawal_all_tnx','withdrawal_success_tnx','withdrawal_success_amount',
            'withdrawal_denied_tnx','withdrawal_denied_amount','withdrawal_waiting_tnx','withdrawal_waiting_amount',
            'withdrawal_unresolved_tnx','withdrawal_unresolved_amount'

        ]));
    }

    public function transaction_type($type, $status = null, Request $request)
    {
        $paginate = true;
        $start_date = null;
        $end_date = null;
        $search = null;

        if($request->session()->has('sort_start_date')){
            $start_date = $request->session()->get('sort_start_date');
        }
        if($request->session()->has('sort_end_date')){
            $end_date = $request->session()->get('sort_end_date');
        }
        if($request->session()->has('search_tnx')){
            $search = $request->session()->get('search_tnx');
        }

        if (!Auth::user()->agentLimits) {
            Auth::user()->agentLimits()->create();
        }

        $show_limit = true;

        $banks = Bank::all();
        $account = Auth::user()->accounts->first();
        if($search != null){
            $transactions = NairaTrade::whereHas('user', function ($query) use ($search) {
                $query->where('first_name','LIKE','%'.$search.'%')
                ->orWhere('phone','LIKE','%'.$search.'%');
            })
            ->orwhere('reference','LIKE','%'.$search.'%')
            ->orderBy('created_at', 'desc')->paginate(20);
        }

        if($search == null){
            $transactions = NairaTrade::where('is_dailyLimit',0)->where('is_monthlyLimit',0);
            if(!in_array($type,['sortbydate','search']))
            {
                if($type != NULL):
                    $transactions = $transactions->where('type',$type);
                endif;

                $transactions = $transactions->with(['user' => function ($query) {
                    $query->withCount(['nairaTrades as total_trx' => function ($query) {
                        $query->where('status','success')->select(DB::raw("sum(amount) as sumt"));
                    }]);
                }]);

                $transactions = $transactions->select("*",\DB::raw('(SELECT SUM(amount)
                    FROM naira_trades as tr
                    WHERE
                    tr.user_id = naira_trades.user_id and created_at > DATE_SUB(now(), INTERVAL 2 MONTH))
                    as total_trax'))
                    // ->orderBy('created_at','asc')
                    ->orderByRaw('case when `total_trax` >= 10000000 then amount end desc')
                    ->orderByRaw('case when `total_trax` < 10000000 then created_at end asc')
                    ->orderByRaw('case when `total_trax` >= 10000000 then total_trax end desc')
                    ;

            }

            if($start_date && $end_date)
            {
                $transactions = $transactions
                ->where('updated_at','>=',$start_date)
                ->where('updated_at','<=',$end_date);
            }
            if ($status) {
                $transactions = $transactions
                ->where('status',$status);
            }

            if($status == 'unresolved')
            {
                $transactions = $transactions->orderBy('updated_at', 'ASC');
            }

            if(isset($request['downloader']) AND $request['downloader'] == 'csv'){
                return Excel::download(new PayBridgeTransactions($transactions), 'PayBridgeTransactions.csv');
            }
            $transactions = $transactions->paginate(20);
            
            
        }
            //?" all  deposit transactions
            $deposit = NairaTrade::where('type','deposit')->where('is_dailyLimit',0)->where('is_monthlyLimit',0);
            if($start_date && $end_date)
            {
                $deposit = $deposit
                ->where('updated_at','>=',$start_date)
                ->where('updated_at','<=',$end_date);
            }
            $deposit = $deposit->get();
            $deposit_all_tnx = $deposit->count();


            //? successful Deposit
            $deposit_success = $deposit->where('status','success');
            $deposit_success_tnx = $deposit_success->count();
            $deposit_success_amount = $deposit_success->sum('amount');

            //? unresolved Deposit
            $deposit_unresolved = $deposit->where('status','unresolved');
            $deposit_unresolved_tnx = $deposit_unresolved->count();
            $deposit_unresolved_amount = $deposit_unresolved->sum('amount');

            //? declined Deposit
            $deposit_denied = $deposit->where('status','cancelled');
            $deposit_denied_tnx = $deposit_denied->count();
            $deposit_denied_amount = $deposit_denied->sum('amount');

            //? waiting Deposit
            $deposit_waiting = $deposit->where('status','waiting');
            $deposit_waiting_tnx = $deposit_waiting->count();
            $deposit_waiting_amount = $deposit_waiting->sum('amount');

            //? all  withdrawal transactions
            $withdrawal = NairaTrade::where('type','withdrawal')->where('is_dailyLimit',0)->where('is_monthlyLimit',0);
            if($start_date && $end_date)
            {
                $withdrawal = $withdrawal
                ->where('updated_at','>=',$start_date)
                ->where('updated_at','<=',$end_date);
            }
            $withdrawal = $withdrawal->get();
            $withdrawal_all_tnx = $withdrawal->count();



            //? successful withdrawal
            $withdrawal_success = $withdrawal->where('status','success');
            $withdrawal_success_tnx = $withdrawal_success->count();
            $withdrawal_success_amount = $withdrawal_success->sum('amount');

            //? unresolved Withdrawal
            $withdrawal_unresolved = $withdrawal->where('status','unresolved');
            $withdrawal_unresolved_tnx = $withdrawal_unresolved->count();
            $withdrawal_unresolved_amount = $withdrawal_unresolved->sum('amount');

            //? declined withdrawal
            $withdrawal_denied = $withdrawal->where('status','cancelled');
            $withdrawal_denied_tnx = $withdrawal_denied->count();
            $withdrawal_denied_amount = $withdrawal_denied->sum('amount');



            //? waiting withdrawal
            $withdrawal_waiting = $withdrawal->where('status','waiting');
            $withdrawal_waiting_tnx = $withdrawal_waiting->count();
            $withdrawal_waiting_amount = $withdrawal_waiting->sum('amount');


        //? end top bars
        $segment = $status." ".$type;

        if($status == 'cancelled'){
            $segment = "Declined $type";
        }
        if ($type == NULL){
            $segment = "All";
        }

        return view('admin.trade_naira.transactions', compact(
            'type', 'status','start_date','end_date','paginate',
            'transactions', 'show_limit', 'banks', 'account','segment',
             'deposit_all_tnx','deposit_success_tnx','deposit_success_amount',
            'deposit_denied_tnx','deposit_denied_amount','deposit_waiting_tnx','deposit_waiting_amount',
            'deposit_unresolved_tnx','deposit_unresolved_amount',
            'withdrawal_all_tnx','withdrawal_success_tnx','withdrawal_success_amount',
            'withdrawal_denied_tnx','withdrawal_denied_amount','withdrawal_waiting_tnx','withdrawal_waiting_amount',
            'withdrawal_unresolved_amount','withdrawal_unresolved_tnx',
             ));
    }

    public function sort_transaction_type(Request $request)
    {
        $request->session()->forget(['search_tnx']);
        $request->session()->put('sort_start_date',str_replace("T"," ",$request->start));
        $request->session()->put('sort_end_date',str_replace("T"," ",$request->end));

        return $this->transaction_type($request->type,$request->status,$request);

    }

    public function search_transaction(Request $request)
    {
        $request->session()->put('search_tnx',$request->search);

        return $this->transaction_type($request->type,$request->status,$request);
    }

    public function accounts() {
        $accounts = PayBridgeAccount::all();
        return view('admin.trade_naira.accounts', compact('accounts'));
    }

    public function withdrawal_queue() {
        $ranges = WithdrawalQueueRange::all();
        return view('admin.trade_naira.withdrawal_queue'
        , compact('ranges')
        );
    }

    public function add_withdrawal_queue(Request $request) {
        $data = $request->except('_token');
        WithdrawalQueueRange::create($data);
        return redirect()->back()->with(["success" => 'Range added']);
    }

    public function update_withdrawal_queue(Request $request) {
        $account = WithdrawalQueueRange::find($request['id']);
        $account->pending_requests = $request['pending_requests'];
        $account->pay_time = $request['pay_time'];
        $account->save();
        return redirect()->back()->with(["success" => 'Range Updated']);

    }

    public function addAccount(Request $request) {
        $data = $request->except('_token');
        PayBridgeAccount::create($data);
        return redirect()->back()->with(["success" => 'Account added']);
    }

    public function updateAccount(Request $request) {
        if(Auth::user()->role == 777)
        {
            $data = $request->except('_token');
            $account = PayBridgeAccount::find($request['id']);
            $account->status = $request['status'];
            $account->save();
            return redirect()->back()->with(["success" => 'Account Updated']);
        }
        $data = $request->except('_token');
        $account = PayBridgeAccount::find($request['id']);
        $account->account_name = $request['account_name'];
        $account->bank_name = $request['bank_name'];
        $account->account_number = $request['account_number'];
        $account->account_type = $request['account_type'];
        $account->status = $request['status'];
        $account->save();
        return redirect()->back()->with(["success" => 'Account Updated']);
    }

    public function updateBankDetails(Request $request)
    {
        $a = Account::find($request->id);
        if ($a->user_id != Auth::user()->id) {
            return redirect()->back()->with(["error" => 'Invalid Operation']);
        }
        $bank = Bank::find($request->bank_id);
        $a->account_name = $request->account_name;
        $a->bank_name = $bank->name;
        $a->account_number = $request->account_number;
        $a->bank_id = $bank->id;
        $a->save();

        return redirect()->back()->with(["success" => 'Details updated']);
    }

    public function agentTransactions(User $user, Request $request)
    {
        $paginate = true;
        $show_limit = true;
        $request->session()->forget(['sort_start_date', 'sort_end_date','search_tnx']);
        if (!Auth::user()->agentLimits) {
            Auth::user()->agentLimits()->create();
        }
        $transactions =    $user->agentNairaTrades()->orderBy('created_at', 'desc')->paginate(20);
        $banks = Bank::all();
        $account = Auth::user()->accounts->first();
        foreach ($transactions as $t) {
            if ($t->type == 'withdrawal') {
                $a = Account::find($t->account_id);
                if($a){
                    $acct = $a['account_name'] . ', ' . $a['bank_name'] . ', ' . $a['account_number'];
                    $t->acct_details = $acct;
                }
            }
            $current_prev_bal = NairaTransaction::where('reference',$t->reference)->latest()->first();
            if(isset($current_prev_bal))
            {
                $t->prev_bal = $current_prev_bal->previous_balance;
                $t->current_bal = $current_prev_bal->current_balance;
            }

        }
         //? top bars
        //?" all  deposit transactions
        $deposit = $user->agentNairaTrades()->where('type','deposit')->get();
        $deposit_all_tnx = $deposit->count();

        //? successful Deposit
        $deposit_success = $user->agentNairaTrades()->where('type','deposit')->where('status','success')->get();
        $deposit_success_tnx = $deposit_success->count();
        $deposit_success_amount = $deposit_success->sum('amount');


        //? declined Deposit
        $deposit_denied = $user->agentNairaTrades()->where('type','deposit')->where('status','cancelled')->get();
        $deposit_denied_tnx = $deposit_denied->count();
        $deposit_denied_amount = $deposit_denied->sum('amount');

        //? waiting Deposit
        $deposit_waiting = $user->agentNairaTrades()->where('type','deposit')->where('status','waiting')->get();
        $deposit_waiting_tnx = $deposit_waiting->count();
        $deposit_waiting_amount = $deposit_waiting->sum('amount');


        //?" all  withdrawal transactions
        $withdrawal = $user->agentNairaTrades()->where('type','withdrawal')->get();
        $withdrawal_all_tnx = $withdrawal->count();

        //? successful withdrawal
        $withdrawal_success = $user->agentNairaTrades()->where('type','withdrawal')->where('status','success')->get();
        $withdrawal_success_tnx = $withdrawal_success->count();
        $withdrawal_success_amount = $withdrawal_success->sum('amount');

        //? declined withdrawal
        $withdrawal_denied = $user->agentNairaTrades()->where('type','withdrawal')->where('status','cancelled')->get();
        $withdrawal_denied_tnx = $withdrawal_denied->count();
        $withdrawal_denied_amount = $withdrawal_denied->sum('amount');

        //? waiting withdrawal
        $withdrawal_waiting = $user->agentNairaTrades()->where('type','withdrawal')->where('status','waiting')->get();
        $withdrawal_waiting_tnx = $withdrawal_waiting->count();
        $withdrawal_waiting_amount = $withdrawal_waiting->sum('amount');


        $segment = "All";
        $type = null;
        $status = null;
        return view('admin.trade_naira.transactions', compact([
            'status','type','paginate',
            'transactions', 'show_limit', 'banks', 'account','segment',
            'deposit_all_tnx','deposit_success_tnx','deposit_success_amount',
            'deposit_denied_tnx','deposit_denied_amount','deposit_waiting_tnx','deposit_waiting_amount',
            'withdrawal_all_tnx','withdrawal_success_tnx','withdrawal_success_amount',
            'withdrawal_denied_tnx','withdrawal_denied_amount','withdrawal_waiting_tnx','withdrawal_waiting_amount'

        ]));

        $show_limit = true;

        return view('admin.trade_naira.transactions', compact('transactions', 'show_limit'));
    }

    public function setLimits(Request $request)
    {
        $data = $request->validate([
            'min' => 'required|min:0',
            'max' => 'required|min:0',
        ]);

        Auth::user()->agentLimits()->update($data);

        return back()->with(['success' => 'Limits uppdated']);
    }

    public function viewTransactions(NairaTrade $transaction, Request $request)
    {
        $type = $transaction->type;
        $status = $transaction->status;
        $t = $transaction;
        return view('admin.trade_naira.view', compact([
            't','type','status'
        ]));
    }

    public function assignStatusAction(Request $request, NairaTrade $transaction){
        if($request->id != $transaction->id){
            return back()->with(['error' => 'Error Invalid Action']);
        }
        
            if($transaction->type == 'withdrawal'){
                //Approve
                if($request->status == 'approve'){
                    $withdrawal =  $this->confirmSell($request, $transaction);
                    $status = $withdrawal['status'];
                    $message = $withdrawal['message'];
                    return back()->with([$status => $message]);
                }
                //decline 
                if($request->status == 'decline'){
                    $withdrawal =  $this->declineTrade($request, $transaction);
                    $status = $withdrawal['status'];
                    $message = $withdrawal['message'];
                    return back()->with([$status => $message]);
                }
                //unresolved
                if($request->status == 'unresolved'){
                    $withdrawal =  $this->unresolvedTrade($request, $transaction);
                    $status = $withdrawal['status'];
                    $message = $withdrawal['message'];
                    return back()->with([$status => $message]);
                }

            } else {
               if($request->status == 'approve'){
                    $deposit =  $this->confirm($request, $transaction);
                    $status = $deposit['status'];
                    $message = $deposit['message'];
                    return back()->with([$status => $message]);
                }
                //decline 
                if($request->status == 'decline'){
                    $deposit =  $this->declineTrade($request, $transaction);
                    $status = $deposit['status'];
                    $message = $deposit['message'];
                    return back()->with([$status => $message]);
                }
            }
    }

    public function unresolvedTrade(Request $request, NairaTrade $transaction)
    {
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return [
                'status' => 'error',
                'message' => 'Incorrect pin'
            ];
        }

        if ($transaction->status != 'waiting') {
            return [
                'status' => 'error',
                'message' => 'Invalid transaction'
            ];
        }
        $account = $transaction->account;

        $title = "WITHDRAWAL UPDATE!";
        $msg ="Your withdrawal transaction of ₦".number_format($transaction->amount)." is being processed, Please hold on it might take a little while. Kindly contact support for more information.";

        $bankDetails = '<b>('.$account->account_name.', '.$account->bank_name.', '. $account->account_number.')</b>';
        $body = "<div style='text-align:justify'>";
        $body .= "Your payment of ₦".number_format($transaction->amount) ." has been sent to your account $bankDetails.<br><br>Kindly note If you don’t receive payment after 24 hours, please reach out to your bank.";
        $body .= "</div>";

        $fcm_id = $transaction->user->fcm_id;
        if (isset($fcm_id)) {
            try {
                FirebasePushNotificationController::sendPush($fcm_id,$title,$msg);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        $transaction->status = 'unresolved';
        $transaction->save();

        self::sendMail($title, $body, $transaction);
        return [
            'status' => 'success',
            'message' => 'updated Successfully'
        ];
    }

    public static function declinedMailData($reason, NairaTrade $nairaTrade)
    {
        $reasonData = null;
        $bankDetails = NULL;
        $timeData = NULL;
        $body = NULL;


        if($nairaTrade->type == 'withdrawal'):
            $account = $nairaTrade->account;
            $bankDetails = '<b>('.$account->account_name.', '.$account->bank_name.', '. $account->account_number.')</b>';
        endif;

        $account = $nairaTrade->account;

        switch ($reason) {
            case 'Bank network issues':
                $reasonData = "of bank network issues";
                $body .= "<div style='text-align:justify'>";
                $body.= "We just noticed network issues with your receiving bank $bankDetails, please try again in 3 hours time or if it's urgent select another account and try again";
                $body .= "</div>";
                $timeData = 'definite';
                break;
            case 'Exceeded bank limit':
                $reasonData = "your bank account has exceeded it's limit";
                $body .= "<div style='text-align:justify'>";
                $body .= "We just noticed your account $bankDetails has reached it's limit, please try again in 3 hours time or if it's urgent select another account and try again";
                $body .= "</div>";
                $timeData = 'definite';
                break;
            case 'Incorrect bank details':
                $reasonData = "of incorrect bank details";
                $body .= "<div style='text-align:justify'>";
                $body .= "For the security of your funds we have temporarily suspended this account details $bankDetails as a result of incorrect Bank details, please select an account with the appropriate details to continue your transaction";
                $body .= "</div>";
                $timeData = 'indefinite';
                break;
            case 'A mismatch in name':
                $reasonData = "of a mismatch in name";
                $body .= "<div style='text-align:justify'>";
                $body .= "For the security of your funds we have temporarily suspended this account details $bankDetails as a result of a mismatch in name, please select an account detail that matches the name which you created your account with";
                $body .= "</div>";
                $timeData = 'indefinite';
                break;
            case 'payment not received':
                $body .= "<div style='text-align:justify'>";
                $body .= "<b>Your deposit was declined because your transfer has not been received, please confirm with your bank or please resend the deposit<b>";
                $body .= "</div>";
                $timeData = null;
                break;
            default:
            
                break;
        }
        return[
            'reason' => $reasonData,
            'body' => $body,
            'timeFrame' => $timeData,
        ];
    }

    public static function sendMail($title, $body, NairaTrade $nairaTrade)
    {
        $user = $nairaTrade->user;

        $btn_text = '';
        $btn_url = '';
        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);
        try {
            Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
        } catch (\Throwable $th) {
            \Log::info($th);
        }
    }
    public function declineTrade(Request $request, NairaTrade $transaction)
    {
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return [
                'status' => 'error',
                'message' => 'Incorrect pin'
            ];
        }

        if ($transaction->status != 'waiting') {
            return [
                'status' => 'error',
                'message' => 'Invalid transaction'
            ];
        }
        $reasonData = self::declinedMailData($request->reason, $transaction);
        $reason = $reasonData['reason'];
        $body= $reasonData['body'];
        $timeFrame  =  $reasonData['timeFrame'];


        $title = "DEPOSIT UPDATE!";
        $msg ="Your deposit transaction of ₦".number_format($transaction->amount)." was declined. Kindly contact support for more information.";

        $nt = NairaTransaction::where('reference', $transaction->reference)->first();
        

        if ($transaction->type == 'withdrawal') {
            $account = $transaction->account;
            self::activateAccountDuration($timeFrame, $account);

            # credit the user
            $user_wallet = $nt->user->nairaWallet;
            $user_wallet->amount += $nt->amount;
            $user_wallet->save();

            //Send back the charges
            $transfer_charges_wallet = NairaWallet::where('account_number', 0000000001)->first();
            $transfer_charges_wallet->amount -= $nt->charge;
            $transfer_charges_wallet->save();

            $title = "WITHDRAWAL UPDATE!";
            $msg ="Your withdrawal transaction of ₦".number_format($transaction->amount)." was declined. This is because $reason. Kindly contact support for more information.";
        }

        // Firebase Push Notification
        $fcm_id = $nt->user->fcm_id;
        if (isset($fcm_id)) {
            try {
                FirebasePushNotificationController::sendPush($fcm_id,$title,$msg);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if ($nt) {
            $nt->status = 'failed';
            $nt->save();
        }

        $transaction->status = 'cancelled';
        $transaction->save();

        self::sendMail($title, $body, $transaction);
        return [
            'status' => 'success',
            'message' => 'Transaction cancelled'
        ];
    }

    public function refundTrade(Request $request, NairaTrade $transaction)
    {
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return back()->with(['error' => 'Incorrect pin']);
        }

        if ($transaction->status != 'success') {
            // return back()->with(['error' => 'Invalid transaction']);
            return back()->with(['error' => 'Invalid transaction']);
        }

        $nt = NairaTransaction::where('reference', $transaction->reference)->first();

        if ($transaction->status == 'success') {
            if ($transaction->type == 'withdrawal') {

                // return $nt->user->nairaWallet->id;

                $ref = \Str::random(3) . time();

                $n = new NairaTransaction();
                $n->reference = $ref;
                $n->amount = $nt->amount;
                $n->amount_paid = $nt->amount_paid;
                $n->user_id = $nt->user->id;
                $n->type = 'refund';
                $n->previous_balance = $nt->previous_balance;
                $n->current_balance = $nt->current_balance;
                $n->charge = $nt->charge;
                $n->transfer_charge = $nt->transfer_charge;
                $n->transaction_type_id = 18;
                $n->cr_wallet_id = $nt->cr_wallet_id;
                $n->cr_acct_name = $nt->cr_acct_name;
                $n->narration = 'Withdrawal Refund ' . $ref;
                $n->trans_msg = '';
                $n->cr_user_id = $nt->dr_user_id;
                $n->dr_user_id = $nt->cr_user_id;
                $n->status = 'success';
                $n->save();

                $nt->user->nairaWallet->amount += $nt->amount;
                $nt->user->nairaWallet->save();

                //Send back the charges
                $transfer_charges_wallet = NairaWallet::where('account_number', 0000000001)->first();
                $transfer_charges_wallet->amount -= $nt->charge;
                $transfer_charges_wallet->save();

            }else {

                $ref = \Str::random(3) . time();

                $n = new NairaTransaction();
                $n->reference = $ref;
                $n->amount = $nt->amount;
                $n->amount_paid = $nt->amount_paid;
                $n->user_id = $nt->user->id;
                $n->type = 'deposit';
                $n->previous_balance = $nt->user->nairaWallet->amount;
                $n->current_balance = $nt->user->nairaWallet->amount - $nt->amount;
                $n->charge = $nt->charge;
                $n->transfer_charge = $nt->transfer_charge;
                $n->transaction_type_id = 18;
                $n->cr_wallet_id = $nt->cr_wallet_id;
                $n->cr_acct_name = $nt->cr_acct_name;
                $n->narration = 'Deposit Refund ' . $ref;
                $n->trans_msg = '';
                $n->cr_user_id = $nt->dr_user_id;
                $n->dr_user_id = $nt->cr_user_id;
                $n->status = 'success';
                $n->save();

                # debit the user
                $user_wallet = $nt->user->nairaWallet;
                $user_wallet->amount -= $nt->amount;
                $user_wallet->save();

            }
        }

        if ($nt) {
            $nt->status = 'success';
            $nt->save();
        }

        $transaction->status = 'cancelled';
        $transaction->save();

        return back()->with(['success' => 'Transaction refunded']);
    }

    public function confirm(Request $request, NairaTrade $transaction)
    {
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return [
                'status' => 'error',
                'message' => 'Incorrect pin'
            ];
        }

        $user = $transaction->user;
        $user_wallet = $transaction->user->nairaWallet;

        if ($transaction->status != 'waiting') {
            return [
                'status' => 'error',
                'message' => 'Invalid transaction'
            ];
        }

        $nt = NairaTransaction::where('reference', $transaction->reference)->first();

        if ($nt) {
            $nt->previous_balance = $user_wallet->amount;
            $nt->current_balance = $user_wallet->amount + $transaction->amount;
            $nt->trans_msg = 'This transaction was handled by ' . Auth::user()->first_name;
            $nt->status = 'success';
            $nt->save();
        }

        $user_wallet->amount += $transaction->amount;
        $user_wallet->save();

        $transaction->status = 'success';
        $transaction->save();
        //?mail for deposit successfull
        $title = 'Pay-Bridge deposit successful
        ';
        $body ="Your naria wallet has been credited with <b>₦".number_format($transaction->amount)."</b><br><br>
        <b>
        Reference Number: $transaction->reference<br><br>
        Date: ".date("Y-m-d; h:ia")."<br><br>
        Account Balance: ₦".number_format($user_wallet->amount)."
        </b>";


        $btn_text = '';
        $btn_url = '';

        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);
        try {
            Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
        } catch (\Throwable $th) {
            \Log::info($th);
        }

        $title = "DEPOSIT UPDATE!";
        $msg ="Your deposit transaction of ₦".number_format($transaction->amount)." was successful.";
        // Firebase Push Notification
        $fcm_id = $user->fcm_id;
        if (isset($fcm_id)) {
            try {
                FirebasePushNotificationController::sendPush($fcm_id,$title,$msg);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return [
            'status' => 'success',
            'message' => 'Transaction confirmed'
        ];
    }

    public function confirmSell(Request $request, NairaTrade $transaction)
    {
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return [
                'status' => 'error',
                'message' => 'Incorrect pin'
            ];
        }

        $user = $transaction->user;
        $user_wallet = $transaction->user->nairaWallet;


        $agent = User::where(['role' => 777, 'status' => 'active', 'id'=> $transaction->agent_id])->first();
        $user_account = Account::find($transaction->account_id);
        $paybridge_account = PayBridgeAccount::where(['status' => 'active', 'account_type' => 'withdrawal'])->first();


        if (!in_array($transaction->status,['waiting','unresolved'])) {
            return [
                'status' => 'error',
                'message' => 'Invalid transaction'
            ];
        }

        $nt = NairaTransaction::where('reference', $transaction->reference)->first();

        if ($nt) {
            $nt->trans_msg = 'This transaction was handled by ' . Auth::user()->first_name;
            $nt->status = 'success';
            $nt->save();
        }

        $transaction->status = 'success';
        $transaction->save();
        //? mail for withdrawal
        $title = 'Pay-Bridge withdrawal(successful)
        ';

        $body ="You have successfully withdrawn the sum of ₦".number_format($transaction->amount)." to ".$user_account->account_name."<br>
        ( ".$user_account->bank_name.", ".$user_account->account_number." ). <br><br>
        <b>
            Pay-Bridge Agent: ".$paybridge_account->account_name."<br><br>
            Bank Name: ".$paybridge_account->bank_name."<br><br>
            Status: <span style='color:green'>Success</span><br><br>

            Reference Number: $transaction->reference <br><br>
            Date: ".date("Y-m-d; h:ia")."<br><br>
            Account Balance: ₦".number_format($user_wallet->amount)."
        </b>
        ";


        $btn_text = '';
        $btn_url = '';

        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);
        try {
            Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
        } catch (\Throwable $th) {
            \Log::info($th);
        }

        $msg ="You have successfully withdrawn the sum of ₦".number_format($transaction->amount)." to ".$user_account->account_name."( ".$user_account->bank_name.", ".$user_account->account_number.")";

        $title = "WITHDRAWAL UPDATE!";
        $msg ="Your withdrawal transaction of ₦".number_format($transaction->amount)." was successful.";

        // Firebase Push Notification
        $fcm_id = $user->fcm_id;
        if (isset($fcm_id)) {
            try {
                FirebasePushNotificationController::sendPush($fcm_id,$title,$msg);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return [
            'status' => 'success',
            'message' => 'Transaction confirmed'
        ];
    }

    public function topup(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required',
            'pin' => 'required',
            'user_id' => 'required'
        ]);

        $user = User::find($data['user_id']);
        $user_wallet = $user->nairaWallet;

        if (!in_array($user->role, [777])) {
            return back()->with(['error' => 'Invalid user account']);
        }

        if (!Hash::check($data['pin'], Auth::user()->nairaWallet->password)) {
            return back()->with(['error' => 'Incorrect pin']);
        }


        //create txn
        $prev_bal = $user_wallet->amount;
        $user_wallet->amount += $data['amount'];
        $user_wallet->save();

        $nt = new NairaTransaction();
        $nt->reference = time();
        $nt->amount = $data['amount'];
        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;
        $nt->transfer_charge = 0;
        $nt->sms_charge = 0;
        $nt->amount_paid = $data['amount'];
        $nt->transaction_type_id = 7;
        $nt->user_id = $user->id;
        $nt->type = 'naira wallet';
        $nt->dr_user_id = Auth::user()->id;
        $nt->cr_user_id = $user->id;
        $nt->dr_acct_name = Auth::user()->first_name;
        $nt->cr_acct_name = $user->first_name;
        $nt->narration = 'Dantown topup';
        $nt->trans_msg = 'Inhouse shit';
        $nt->status = 'success';
        $nt->save();


        return back()->with(['success' => 'Account credited successfully']);
    }

    public function deduct(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required',
            'pin' => 'required',
            'user_id' => 'required'
        ]);

        $user = User::find($data['user_id']);
        $user_wallet = $user->nairaWallet;

        if (!in_array($user->role, [777])) {
            return back()->with(['error' => 'Invalid user account']);
        }

        if (!Hash::check($data['pin'], Auth::user()->nairaWallet->password)) {
            return back()->with(['error' => 'Incorrect pin']);
        }


        //create txn
        $prev_bal = $user_wallet->amount;
        $user_wallet->amount -= $data['amount'];
        $user_wallet->save();

        $nt = new NairaTransaction();
        $nt->reference = time();
        $nt->amount = $data['amount'];
        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;
        $nt->transfer_charge = 0;
        $nt->sms_charge = 0;
        $nt->amount_paid = $data['amount'];
        $nt->transaction_type_id = 8;
        $nt->user_id = $user->id;
        $nt->type = 'naira wallet';
        $nt->dr_user_id = $user->id;
        $nt->cr_user_id = Auth::user()->id;
        $nt->dr_wallet_id = $user_wallet->id;
        $nt->dr_acct_name = $user->first_name;
        $nt->narration = 'Dantown duduction';
        $nt->trans_msg = 'Inhouse shit';
        $nt->status = 'success';
        $nt->save();

        return back()->with(['success' => 'Account debited successfully']);
    }

    public static function activateAccountDuration($duration, Account $account)
    {
        if($duration == 'indefinite'){
            $account->update([
                'status'=>'in-active',
            ]);
        } else {
            $account->update([
                'activateBy' => now()->addHours(3),
            ]);
        }
    }
}
