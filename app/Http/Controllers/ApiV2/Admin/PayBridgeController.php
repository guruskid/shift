<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Account;
use App\AccountantTimeStamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiV2\Admin\NairaTradeResource;
use App\NairaTrade;
use App\NairaTransaction;
use App\PayBridgeAccount;
use App\User;
use Exception;
use Illuminate\Support\Facades\Validator;

class PayBridgeController extends Controller
{
    public function index()
    {
        $payBridge_account = PayBridgeAccount::orderBy('created_at','DESC')->get();
        return response()->json([
            'success' => true,
            'accounts' => $payBridge_account,
        ],200);
    }

    public function addBank(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'account_name' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required|numeric',
            'account_type' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $accounts = new PayBridgeAccount();
        $accounts->account_name = $r->account_name;
        $accounts->bank_name = $r->bank_name;
        $accounts->account_number = $r->account_number;
        $accounts->account_type = $r->account_type;
        $accounts->	status = "in-active";
        $accounts->save();

        return response()->json([
            'success' => true,
            'message' => "Account has been created successfully",
        ],200);
    }

    public function showBank($id)
    {
        $payBridgeAccount = PayBridgeAccount::where('id',$id)->first();
        return response()->json([
            'success' => true,
            'account' => $payBridgeAccount,
        ],200);
    }

    public function editBank(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'id' => 'required',
            'account_name' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required|numeric',
            'account_type' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $payBridgeAccount = PayBridgeAccount::find($r->id);
        $payBridgeAccount->account_name = $r->account_name;
        $payBridgeAccount->bank_name = $r->bank_name;
        $payBridgeAccount->account_number = $r->account_number;
        $payBridgeAccount->account_type = $r->account_type;
        $payBridgeAccount->save();

        return response()->json([
            'success' => true,
            'message' => "Account edited successfully",
        ],200);
    }

    public function deleteBank($id)
    {
        PayBridgeAccount::find($id)->delete();
        return response()->json([
            'success' => true,
            'message' => "Account deleted successfully",
        ],200);
    }

    public function activateBank($id)
    {
        $payBridgeAccount = PayBridgeAccount::where('id',$id)->update([
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => "$payBridgeAccount->account_name [$payBridgeAccount->bank_name] has been activated",
        ],200);
    }

    public function deactivateBank($id)
    {
        $payBridgeAccount = PayBridgeAccount::where('id',$id)->update([
            'status' => 'in-active',
        ]);

        return response()->json([
            'success' => true,
            'message' => "$payBridgeAccount->account_name [$payBridgeAccount->bank_name] has been deactivated",
        ],200);
    }

    public function generateP2pTabs($transactions)
    {
        $depositTotal = $transactions->where('type','deposit')->count();
        $withdrawalTotal = $transactions->where('type','withdrawal')->count();
        
        $successDeposit = $transactions->where('type','deposit')->where('status','success')->count();
        $successWithdrawal = $transactions->where('type','withdrawal')->where('status','success')->count();

        $waitingDeposit = $transactions->where('type','deposit')->where('status','waiting')->count();
        $waitingWithdrawal = $transactions->where('type','withdrawal')->where('status','waiting')->count();

        $declinedDeposit = $transactions->where('type','deposit')->where('status','cancelled')->count();
        $declinedWithdrawal = $transactions->where('type','withdrawal')->where('status','cancelled')->count();

        $exportData = array(
            'depositTotal' => $depositTotal,
            'withdrawalTotal' => $withdrawalTotal,
            'successDeposit' => $successDeposit,
            'successWithdrawal' => $successWithdrawal,
            'waitingDeposit' => $waitingDeposit,
            'waitingWithdrawal' => $waitingWithdrawal,
            'declinedDeposit' => $declinedDeposit,
            'declinedWithdrawal' => $declinedWithdrawal,
        );

        return $exportData;
    }

    public function p2p()
    {
        $transactions = NairaTrade::with('user','account','naira_transactions','agent')->whereRaw('Date(created_at) = CURDATE()')->get();
        $p2pTabs = $this->generateP2pTabs($transactions);
        $tranxData = NairaTradeResource::collection($transactions);

        return response()->json([
            'success' => true,
            'depositTotal' => $p2pTabs['depositTotal'],
            'withdrawalTotal' => $$p2pTabs['$withdrawalTotal'],
            'successDeposit' => $p2pTabs['successDeposit'],
            'successWithdrawal' => $p2pTabs['successWithdrawal'],
            'waitingDeposit' => $p2pTabs['waitingDeposit'],
            'waitingWithdrawal' => $p2pTabs['waitingWithdrawal'],
            'declinedDeposit' => $p2pTabs['declinedDeposit'],
            'declinedWithdrawal' => $p2pTabs['declinedWithdrawal'],
            'transactions' => $tranxData,
        ],200);
    }

    public function sortByStatus($start, $end, $name)
    {
        $status_name = null;
        $transactions = null;

        switch ( $name ) {
            case 'Pending':
                $status_name = 'waiting';
                break;

            case 'Declined':
                $status_name = 'cancelled';
                break;

            case 'Successful':
                $status_name = 'success';
                break;
            
            default:
                $status_name = "All";
                break;
        }
        if( $status_name == "All" ) {
            $transactions = NairaTrade::with('user','account','naira_transactions','agent')
            ->whereDate('created_at','>=',$start)->whereDate('created_at','<=',$end)->get();

        } else {
            $transactions = NairaTrade::with('user','account','naira_transactions','agent')->where('status', $status_name)
            ->whereDate('created_at','>=',$start)->whereDate('created_at','<=',$end)->get();
        }
        return $transactions;
    }

    public function sortingByAccountantTimestamp($value, $activeTime, $inactiveTime)
    {
        if($inactiveTime == null):
            $inactiveTime = now();
        endif;

        $value = $value->where('updated_at','>=',$activeTime)->where('updated_at', '<=', $inactiveTime)->get();
        return $value;
    }

    public function sortByAccountant($start, $end, $accountant_id)
    {
        $accountant_timestamp = AccountantTimeStamp::where('user_id',$accountant_id)->whereDate('activeTime','>=',$start)->whereDate('inactiveTime','<=',$end)->get();
        $payBridgeTransactions = collect();

        foreach($accountant_timestamp as $at):
            $payBridgeTranx = NairaTrade::orderBy('updated_at','desc');
            $payBridgeTranx = $this->sortingByAccountantTimestamp($payBridgeTranx, $at->activeTime, $at->inactiveTime);
            $payBridgeTransactions = $payBridgeTransactions->concat($payBridgeTranx);
        endforeach;

        return $payBridgeTransactions->sortByDesc('updated_at');
    }

    public function p2pSorting(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $transactions = null;
        $p2pTabs = array();
        if(!isset($r->is_accountant)){
            $transactions = NairaTrade::with('user','account','naira_transactions','agent')
            ->whereDate('created_at','>=',$r->start)->whereDate('created_at','<=',$r->end)->get();

            $p2pTabs = $this->generateP2pTabs($transactions);
            $transactions = NairaTradeResource::collection($transactions);
        }

        if($r->is_accountant == 0){
            $transactions =  $this->sortByStatus($r->start, $r->end,$r->name);
            $p2pTabs = $this->generateP2pTabs($transactions);
            $transactions = NairaTradeResource::collection($transactions);
        } else{
            $transactions =  $this->sortByAccountant($r->start, $r->end,$r->id);
            $p2pTabs = $this->generateP2pTabs($transactions);
            $transactions = NairaTradeResource::collection($transactions);
        }

        return response()->json([
            'success' => true,
            'depositTotal' => $p2pTabs['depositTotal'],
            'withdrawalTotal' => $$p2pTabs['$withdrawalTotal'],
            'successDeposit' => $p2pTabs['successDeposit'],
            'successWithdrawal' => $p2pTabs['successWithdrawal'],
            'waitingDeposit' => $p2pTabs['waitingDeposit'],
            'waitingWithdrawal' => $p2pTabs['waitingWithdrawal'],
            'declinedDeposit' => $p2pTabs['declinedDeposit'],
            'declinedWithdrawal' => $p2pTabs['declinedWithdrawal'],
            'transactions' => $transactions,
        ],200);
    }

    public function loadFilter()
    {
        $status = collect([
            ['id'=>null, 'is_accountant' => 0, 'name' => 'All'],
            ['id'=>null, 'is_accountant' => 0, 'name' => 'Pending'],
            ['id'=>null, 'is_accountant' => 0, 'name' => 'Declined'],
            ['id'=>null, 'is_accountant' => 0, 'name' => 'Successful'],
        ]);

        //Accountants 
        $accountants = User::whereIn('role',['777','775','889'])->get(['id','first_name','last_name']);
        foreach($accountants as $accountant)
        {
            $accountant->is_accountant = 1;
            $accountant->name = $accountant->first_name ." ".$accountant->last_name;
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'accountants' => $accountants,
        ],200);
    }


}
