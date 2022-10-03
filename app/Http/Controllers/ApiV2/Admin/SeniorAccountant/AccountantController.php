<?php

namespace App\Http\Controllers\ApiV2\Admin\SeniorAccountant;

use App\AccountantTimeStamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\NairaWallet;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use DB;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class AccountantController extends Controller
{
    public function AccountantOverview()
    {


        $data['accountant'] =  $accountant = User::select('id', 'first_name', 'last_name', 'email', 'phone', 'role', 'status', 'username')->where("status", "active")->whereHas("accountantTimestamp")->with('accountantTimestamp')->first();

        $data['overview'] = [
            'number_of_trades' => 0,
            'total_amount_deposit_p2p' => 0,
            'total_number_withdrawal_transactions' => 0,
            'total_number_utility_transactions' => 0,
            'total_volume_trades' => 0,
            'total_amount_withdrawn_p2p' => 0,
            'total_amount_traded' => 0,
            'total_number_transactions_p2p' => 0,
            'total_debited_amount__utilities_transactions' => 0
        ];


        if ($accountant->accountantTimestamp->count() > 0) {
            $startTime = $accountant->accountantTimestamp->first()->activeTime;
            $endTime = ($accountant->accountantTimestamp->first()->inactiveTime == null) ? now() : $accountant->accountantTimestamp->first()->inactiveTime;

            $p2pTranx = NairaTrade::where('status', 'success')->where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime)->get();
            $total_number_transactions_p2p = $p2pTranx->count();
            $total_amount_deposit_p2p = $p2pTranx->where('type', 'deposit')->sum('amount');
            $total_amount_withdrawal_p2p = $p2pTranx->where('type', 'withdrawal')->sum('amount');
            $total_number_withdrawal_transactions   =  Transaction::whereIn('status', ['success', 'pending'])->where('updated_at', '>=', $startTime)->where('type', 'sell')->count();
            $total_number_utility_transactions = UtilityTransaction::whereIn('status', ['success', 'pending'])->where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime)->count();
            $number_of_trades = Transaction::where("status", 'success')->where('updated_at', '>=', $startTime)->count();
            $total_amount_traded = Transaction::where("status", 'success')->where('updated_at', '>=', $startTime)->sum('amount_paid');
            $total_volume_trades = Transaction::where("status", 'success')->where('updated_at', '>=', $startTime)->sum('amount');
            $total_debited_amount__utilities_transactions = UtilityTransaction::whereIn('status', ['success', 'pending'])->where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime)->sum('amount');

            $data['overview'] = [
                'number_of_trades' => $number_of_trades,
                'total_amount_deposit_p2p' => $total_amount_deposit_p2p,
                'total_number_withdrawal_transactions' =>  $total_number_withdrawal_transactions,
                'total_number_utility_transactions' =>   $total_number_utility_transactions,
                'total_volume_trades' => $total_volume_trades,
                'total_amount_withdrawn_p2p' => $total_amount_withdrawal_p2p,
                'total_amount_traded' => $total_amount_traded,
                'total_number_transactions_p2p' =>  $total_number_transactions_p2p,
                'total_debited_amount__utilities_transactions' => $total_debited_amount__utilities_transactions
            ];



            // recent transactions
            $tranx = DB::table('transactions')
                ->whereIn('transactions.status', ['success', 'pending'])
                ->where('transactions.created_at', '>=', $startTime)
                ->where('transactions.created_at', '<=', $endTime)
                ->join('users', 'transactions.user_id', '=', 'users.id')
                ->select('first_name', 'last_name', 'username', 'transactions.id', 'user_id', 'card as transaction', 'amount_paid as amount', 'transactions.amount as value', DB::raw('0 as prv_bal'), DB::raw('0 as cur_bal'), 'transactions.status', DB::raw('date(transactions.created_at) as date', 'transactions.created_at as created_at'));


            // naira Transaction table
            $tranx2 = DB::table('naira_transactions')
                ->whereIn('naira_transactions.status', ['success', 'pending'])
                ->where('naira_transactions.created_at', '>=', $startTime)
                ->where('naira_transactions.created_at', '<=', $endTime)
                ->join('users', 'naira_transactions.user_id', '=', 'users.id')
                ->select('first_name', 'last_name', 'username', 'naira_transactions.id', 'user_id', 'type as transaction', 'amount_paid', 'naira_transactions.amount as value', 'previous_balance as prv_bal', 'current_balance as cur_bal', 'naira_transactions.status', DB::raw('date(naira_transactions.created_at) as date', 'naira_transactions.created_at as created_at'));

            // merge table
            $mergeTbl = $tranx->unionAll($tranx2);
            DB::table(DB::raw("({$mergeTbl->toSql()}) AS mg"))->mergeBindings($mergeTbl);
            $data['transactions'] =  $mergeTbl->orderBy('date', 'desc')->take(20)->get();
        }



        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    public function GetActiveAccountant()
    {
        $data['accountant'] = User::select('id', 'first_name', 'last_name', 'email', 'phone', 'role', 'status', 'username')->where("status", "active")->whereHas("accountantTimestamp")->first();
        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    public function getActivationHistory()
    {
        $accountant = User::whereHas('accountantTimestamp')->whereIn('role', [777, 775, 889])->with('accountantTimestamp', 'accountantTimestamp.activatedBy')->get();
        return response()->json([
            'success' => true,
            'data' => $accountant,
        ], 200);
    }


    public function deactivateAccountant($id)
    {



        $user = User::find($id);
        if (!in_array($user->role, [777, 775, 889])) {
            return response()->json([
                'success' => false,
                'message' => "$user->first_name $user->last_name is not an accountant"
            ], 401);
        }

        if (in_array($user->role, [777, 775])) {
            $nairaUsersWallet = NairaWallet::sum('amount');
            if ($user->status != 'waiting') :
                $user->status = 'waiting';
                $user->save();

                $this->deactivate($id, $nairaUsersWallet);

                return response()->json([
                    'success' => true,
                    'message' => "$user->first_name $user->last_name is deactivated"
                ], 200);
            endif;
        }

        if ($user->role == 889) {
            $counterSA = User::where(['role' => 889, 'status' => 'active', 'id' => $id])
                ->whereHas('accountantTimestamp', function ($query) {
                    $query->whereNull('inactiveTime');
                })->first();

            if ($counterSA) {
                $this->deactivate($id);
                return response()->json([
                    'success' => true,
                    'message' => "$user->first_name $user->last_name is deactivated"
                ], 200);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$user->first_name $user->last_name is already deactivated"
        ], 200);
    }


    public function activateAccountant(Request $request, $id)
    {
        $user = User::find($id);

        if (!in_array($user->role, [777, 775, 889])) {
            return response()->json([
                'success' => false,
                'message' => "$user->first_name $user->last_name is not an accountant"
            ], 401);
        }
        $nairaUsersWallet = NairaWallet::sum('amount');

        if (in_array($user->role, [777, 775])) {
            if ($user->status != 'active') :
                $user->status = 'active';
                $user->save();

                $this->activate($id, $nairaUsersWallet);

                return response()->json([
                    'success' => true,
                    'message' => "$user->first_name $user->last_name is activated"
                ], 200);
            endif;
        }

        if ($user->role == 889) {
            $counterSA = User::where(['role' => 889, 'status' => 'active', 'id' => $id])
                ->whereHas('accountantTimestamp', function ($query) {
                    $query->whereNotNull('inactiveTime');
                })->first();

            if ($counterSA) {
                $this->activate($id, $nairaUsersWallet);

                return response()->json([
                    'success' => true,
                    'message' => "$user->first_name $user->last_name is activated"
                ], 200);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$user->first_name $user->last_name is already activated"
        ], 200);
    }


    private function deactivate($id)
    {
        $accountant = AccountantTimeStamp::where('user_id', $id)->whereNull('inactiveTime')->orderBy('id', 'DESC')->first();

        if (!empty($accountant)) {
            $activeTime = $accountant->activeTime;
            $duration = Carbon::parse($activeTime)->diffInMinutes(now());
            if ($duration < 5) {
                $accountant->delete();
            } else {
                $accountant->update([
                    'inactiveTime' => Carbon::now(),
                    'deactivated_by' => Auth::id(),
                    'deactivation_date' =>  Carbon::now()
                ]);
            }
        }
    }

    private function activate($id, $amount)
    {
        $user_check = AccountantTimeStamp::where('user_id', $id)->whereNull('inactiveTime')->get();

        if ($user_check->count() <= 0) {
            AccountantTimeStamp::create([
                'user_id' => $id,
                'activeTime' => Carbon::now(),
                'opening_balance' => $amount,
                'activated_by' => Auth::id(),
                'activation_date' => Carbon::now(),
            ]);
        }
    }


    // Wallet

    public function WalletOverview(){
            $transaction =  NairaTransaction::whereHas("user")->whereIn("status", ["success", "pending"]);
       $data['naira_wallet_history'] = $transaction->with("user")->orderBy('id', 'DESC')->paginate(25);
        $data['total_commision_utilities'] = UtilityTransaction::whereIn('status', ['success', 'pending'])->sum("convenience_fee");
        $data['total_user_balance'] =$transaction->sum('current_balance');
        $data['total_withdrawal_charges'] =$transaction->sum('charge');


        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);


    }

    public function MonthlyWithdrawalCharges(){
       $transaction =  NairaTransaction::whereHas("user")->whereIn("status", ["success", "pending"])->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', date('Y'));
       $data['total_commision_utilities'] = UtilityTransaction::whereIn('status', ['success', 'pending'])->sum("convenience_fee");
       $data['total_user_balance'] = $transaction->sum('current_balance');
       $data['total_withdrawal_charges'] = $transaction->sum('charge');

       $data['this_month'] = $transaction->selectRaw("SUM(charge) as withdrawal_charges")
       ->selectRaw("count(id) as number_of_transactions")
        ->selectRaw("(DATE_FORMAT(created_at, '%d-%m-%Y')) as date")
        ->selectRaw("count('DISTINCT user_id') as unique_users")
        ->orderBy('created_at', 'DESC')
        ->groupBy(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))
        ->take(31)->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

}
