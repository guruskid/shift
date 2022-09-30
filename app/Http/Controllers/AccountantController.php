<?php

namespace App\Http\Controllers;

use App\AccountantTimeStamp;
use App\NairaWallet;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountantController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seniorAccountant']);
    }

    public function addJunior(Request $r)
    {
        $user = User::find($r->id);
        $user->role = 777;
        $user->status = 'waiting';
        $user->save();

        return back()->with(['success' => 'Accountant added successfully']);
    }

    public function juniorAccountants()
    {
        $users = User::whereIn('role', [777, 889])->with('accountantTimestamp')->latest()->get();
        return view('admin.accountants', compact('users'));
    }

    public function seniorAccountantActivation(User $user, $action, $nairaUsersWallet)
    {

        if ($action == 'activeSA') {
            $this->activateAccountantTimestamp($user, $user->id, $nairaUsersWallet);
        } else {
            $this->deactivateAccountantTimestamp($user->id, $nairaUsersWallet);
        }
        return back()->with(['success' => 'Action Successful']);
    }

    public function action($id, $action)
    {
        $user = User::find($id);

        $nairaUsersWallet = NairaWallet::sum('amount');
        if ($action == 'activeSA' or $action == 'waitingSA') {
            return $this->seniorAccountantActivation($user, $action, $nairaUsersWallet);
        }

        if ($action == 'remove') {
            $user->role = 1;
        } elseif (Auth::user()->role == 999 && $action == 'upgrade-to-senior') {
            $user->role = 889;
        } else {
            $user->status = $action;
        }
        $user->save();

        //* tracking user
        if (($user->role == 777)) {
            if ($action == 'active') {
                $this->activateAccountantTimestamp($user, $id, $nairaUsersWallet);
            }
            if ($action == 'waiting') {
                $this->deactivateAccountantTimestamp($id, $nairaUsersWallet);
            }
        }


        return back()->with(['success' => 'Action Successfull']);
    }

    public function activateAccountantTimestamp(User $user, $id, $nairaUsersWallet)
    {
        $user_check = AccountantTimeStamp::where('user_id', $user->id)->whereNull('inactiveTime')->get();

        if ($user_check->count() <= 0) {
            AccountantTimeStamp::create([
                'user_id' => $id,
                'activeTime' => Carbon::now(),
                'opening_balance' => $nairaUsersWallet,
            ]);
        }
    }

    public function deactivateAccountantTimestamp($id, $nairaUsersWallet)
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
                    'closing_balance' => $nairaUsersWallet,
                ]);
            }
        }
    }
 
    //*this should not be here
    public function AccountantOverview($id)
    {
        $accountant = User::where("id", $id)->select('id', 'first_name', 'last_name', 'email', 'phone', 'role', 'status', 'username')
            ->with('accountantTimestamp')->whereIn('role', [777, 775, 889])->first();

        $overview = [
            "number_of_trade"  => 0,
            "total_amount_deposited_through_p2p" => 0,
            "total_number_of_withdrawal_transactions" => 0,
            "total_volumes_trades" => 0,
            "total_amount_withdrawn_through_p2p" => 0,
            "total_number_of_utilities_tranctions" => 0,
            "total_amount_traded" => 0,
            "total_number_of_transaction_through_p2p" => 0,
            "amount_debited_for_all_utilities_transactions" => 0
        ];


        if( $accountant->accountantTimestamp->count() > 0){
            $p2p = $accountant->nairaTrades;
            $countP2PTransactions =  $p2p->count();
            $countP2PWithdrawalTransactions =  $p2p->count();
            $totalP2PDesposit = $p2p->where('type','deposit')->sum('amount');
            $totalP2PWithdraw = $p2p->where('type','withdrawal')->sum('amount');


            $overview = [
                "number_of_trade"  => 0,
                "total_amount_deposited_through_p2p" => $totalP2PDesposit,
                "total_number_of_withdrawal_transactions" => $countP2PWithdrawalTransactions,
                "total_volumes_trades" => 0,
                "total_amount_withdrawn_through_p2p" =>$totalP2PWithdraw,
                "total_number_of_utilities_tranctions" => 0,
                "total_amount_traded" => 0,
                "total_number_of_transaction_through_p2p" =>$countP2PTransactions,
                "amount_debited_for_all_utilities_transactions" => 0
            ];


        }
        return response()->json([
            'success' => true,
            'summary' =>$overview,
        ], 200);





    }
}
