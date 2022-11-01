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
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Exception;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        ],201);
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
        $payBridge = PayBridgeAccount::where('id',$id);
        $payBridgeData = $payBridge->first();
        if($payBridgeData->status == 'active')
        {
            return response()->json([
                'success' => false,
                'message' => "$payBridgeData->account_name [$payBridgeData->bank_name] is already activated",
            ],200);
        }
        $payBridgeAccount = $payBridge->update([
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => "$payBridgeData->account_name [$payBridgeData->bank_name] has been activated",
        ],200);
    }

    public function deactivateBank($id)
    {
        $payBridge = PayBridgeAccount::where('id',$id);
        $payBridgeData = $payBridge->first();

        if($payBridgeData->status == 'in-active')
        {
            return response()->json([
                'success' => false,
                'message' => "$payBridgeData->account_name [$payBridgeData->bank_name] is already deactivated",
            ],200);
        }
        $payBridgeAccount = $payBridgeData->update([
            'status' => 'in-active',
        ]);

        return response()->json([
            'success' => true,
            'message' => "$payBridgeData->account_name [$payBridgeData->bank_name] has been deactivated",
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
            'withdrawalTotal' => $p2pTabs['withdrawalTotal'],
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
            'withdrawalTotal' => $p2pTabs['withdrawalTotal'],
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
        $accDetails = array();
        foreach($accountants as $accountant)
        {
            $accDetails[] = array(
                'id' => $accountant->id,
                'is_accountant' => 1,
                'name' => $accountant->first_name ." ".$accountant->last_name,
            );
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'accountants' => $accDetails,
        ],200);
    }


    public function p2pTransactionsSUmmary(){



        $p2p = NairaTrade::whereHas("naira_transactions")->get();
        $trx =  Transaction::where('status','success')->get();
        $utility = UtilityTransaction::where('status','success')->count();
        $data['overview'] = [
            "total_p2p_transaction_number" => $p2p->count(),
            "total_asset_value" =>  number_format($trx->sum('amount')),
            "total_card_price" =>  number_format($trx->sum('card_price')),
            "total_cash_value" => number_format($trx->sum('amount_paid')),
            "total_utility" => $utility,
        ];

        return response()->json([
            'success' => true,
           'data' => $data,
        ],200);


    }

    public function p2pTransactions(Request $req){

        $transactions = NairaTrade::whereHas("naira_transactions")->with('user','account','naira_transactions','agent');


        if($req->status == "pending"){
            $transactions->where('status', 'waiting');
        }

        if($req->status == "declined"){
            $transactions->where('status', 'declined');
        }

        if($req->status == "success"){
            $transactions->where('status', 'success');
        }


        $transactions = $transactions->get();


        $data["p2p_transactions"] = NairaTradeResource::collection($transactions);




        return response()->json([
            'success' => true,
            'data' => $data,
        ],200);
    }

    public function sortP2PByAccountant($accountant_id){
     $trx = NairaTrade::whereHas("naira_transactions")->with('user','account','naira_transactions','agent')->where("agent_id", $accountant_id)->get();
        $data["p2p_transactions"] =  $data["p2p_transactions"] = NairaTradeResource::collection($trx);

        return response()->json([
            'success' => true,
            'data' => $data,
        ],200);



    }


    public function p2pAnalytics(Request $req){

        $transaction_table = "";
        if( $req->duration == null)
        {
            $transaction_table = $this->weeklyTransactionTable();
        }
        if($req->duration == 'Weekly')
        {
            $transaction_table = $this->weeklyTransactionTable();
        }
        if($req->duration == 'Quarterly')
        {
            $transaction_table = $this->QuarterTransactionTable();
        }
        if($req->duration == 'Monthly')
        {
            $transaction_table = $this->MonthlyTransactionTable();
        }
        if($req->duration == 'Annually')
        {
            $transaction_table = $this->AnnuallyTransactionTable();
        }
        // return $transaction_table;

        return response()->json([
            'success' => true,
            'data' =>$transaction_table,
        ],200);
    }



    public function QuarterTransactionTable()
    {


            $All_transactions = Transaction::query();


        //* getting all successful transactions in ascending order and grouping it into months
        $All_transactions = $All_transactions->where("status", "success")->with('user')->where('status','success')
        ->orderBy('created_at','asc')->get()
        ->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format("M-Y");
        });

        //*breaking it into sets of 3 to get the data of the three months time frame
        $QuarterlyMonths = $All_transactions->chunk(3);
        $previous_total = 0;
        foreach ($QuarterlyMonths as $QM_data) {
                //*variables to hold the verification level data
                $Level_1 = 0;
                $Level_2 = 0;
                $Level_3 = 0;
                $total = 0;
            foreach ($QM_data as $QM_data_key => $QM_data_values) {
                //*variable to store the duration in an array
                $duration[] = $QM_data_key;
                //*getting the total transaction number for each quarter
                $total += $QM_data_values->count();
                foreach ($QM_data_values as $QMv) {
                    //* getting the user verification  data for the transactions
                 if(isset($QMv->user))
                 {
                     if($QMv->user->phone_verified_at != null AND $QMv->user->address_verified_at == null AND $QMv->user->idcard_verified_at == null)
                     {
                         $Level_1 ++;
                     }
                     if($QMv->user->phone_verified_at != null AND $QMv->user->address_verified_at != null AND $QMv->user->idcard_verified_at == null)
                     {
                         $Level_2 ++;
                     }
                     if($QMv->user->phone_verified_at != null AND $QMv->user->address_verified_at != null AND $QMv->user->idcard_verified_at != null)
                     {
                         $Level_3 ++;
                     }

                 }
                }
            }
            //* assigning value to the duration
            if(count($duration) == 3){
                $duration_value = "$duration[0] - $duration[2]";
            }
            if(count($duration) == 2){
                $duration_value = "$duration[0] - $duration[1]";
            }
            if(count($duration) == 1){
                $duration_value = "$duration[0]";
            }

                $QM_data->duration = $duration_value;
                $QM_data->L1_percentage = ($Level_1/$total)*100;
                $QM_data->L2_percentage = ($Level_2/$total)*100;
                $QM_data->L3_percentage = ($Level_3/$total)*100;
                $QM_data->successful_transactions = $total;
                $QM_data->date = $QMv->created_at;
                //* destroying the data available in the duration array
                unset($duration);

                //*calculating percentage difference
                $per_diff = ($previous_total != 0) ? (($total - $previous_total )/ $previous_total)*100 : 0 ;
                $QM_data->percentage_difference = round($per_diff,2);

                //*setting the previous total with the old total
                $previous_total = $total;

        }
        //* adding the previous_percentage_difference to the payload.
        $previous_total = 0;
         foreach ($QuarterlyMonths as $key => $value) {
             $value->previous_percentage_difference = $previous_total;
             $previous_total = $value->percentage_difference;
         }
        $data = $QuarterlyMonths->sortByDesc('date');

        //*sending to the frontend the needed data.
        $export_data = array();
         foreach ($data as $value) {
            $newData = array(
                'duration' => $value->duration,
                'L1_percentage' =>$value->L1_percentage,
                'L2_percentage' => $value->L2_percentage,
                'L3_percentage' => $value->L3_percentage,
                'successful_transactions' => $value->successful_transactions,
                'percentage_difference' => $value->percentage_difference,
                'previous_percentage_difference' => $value->previous_percentage_difference,
            );
            $export_data[] = $newData;
         }
         return $export_data;
    }
    public function weeklyTransactionTable()
    {
        return $this->AnnuallyMonthlyWeeklyTransactionTable('W-Y');
    }
    public function MonthlyTransactionTable()
    {
        return $this->AnnuallyMonthlyWeeklyTransactionTable('M-Y');
    }
    public function AnnuallyTransactionTable()
    {
        return $this->AnnuallyMonthlyWeeklyTransactionTable('Y');
    }

    public function AnnuallyMonthlyWeeklyTransactionTable($duration)
    {


            $byweek = Transaction::query();



        //* getting all successful transactions in ascending order according to the duration format
        $byweek = $byweek->where("status", "success")->with('user')->orderBy('created_at','asc')->where('status','success')->get()
         ->groupBy(function($date) use ($duration) {
             return Carbon::parse($date->created_at)->format($duration);
         });
         $previous_total = 0;
         foreach ($byweek as $key => $value) {
             $total = $value->count();
             //*variables to hold the verification level data
             $Level_1 = 0;
             $Level_2 = 0;
             $Level_3 = 0;
             foreach($value as $v){
                //* getting the user verification  data for the transactions
                 if(isset($v->user))
                 {
                     if($v->user->phone_verified_at != null AND $v->user->address_verified_at == null AND $v->user->idcard_verified_at == null)
                     {
                         $Level_1 ++;
                     }
                     if($v->user->phone_verified_at != null AND $v->user->address_verified_at != null AND $v->user->idcard_verified_at == null)
                     {
                         $Level_2 ++;
                     }
                     if($v->user->phone_verified_at != null AND $v->user->address_verified_at != null AND $v->user->idcard_verified_at != null)
                     {
                         $Level_3 ++;
                     }
                 }
                 $value->date = $v->created_at;
             }
             $percentage_L1 = ($Level_1/$total)*100;
             $percentage_L2 = ($Level_2/$total)*100;
             $percentage_L3 = ($Level_3/$total)*100;

             //* assigning value to the duration
             $value->duration = ($duration == 'W-Y') ? "Week ".$key:$key;
             $value->L1_percentage = $percentage_L1;
             $value->L2_percentage = $percentage_L2;
             $value->L3_percentage = $percentage_L3;
             $value->successful_transactions = $total;

             //*calculating percentage difference
             $per_diff = ($previous_total != 0) ? (($total - $previous_total )/ $previous_total)*100 : 0 ;
             $value->percentage_difference = round($per_diff,2);
             $previous_total = $total;
         }
         $previous_total = 0;
         //* adding the previous_percentage_difference to the payload.
         foreach ($byweek as $key => $value) {
             $value->previous_percentage_difference = $previous_total;
             $previous_total = $value->percentage_difference;
         }
         $data = $byweek->sortByDesc('date');

         //*sending to the frontend the needed data.
         $export_data = array();
         foreach ($data as $key => $value) {
            $newData = array(
                'duration' => $value->duration,
                'L1_percentage' =>$value->L1_percentage,
                'L2_percentage' => $value->L2_percentage,
                'L3_percentage' => $value->L3_percentage,
                'successful_transactions' => $value->successful_transactions,
                'percentage_difference' => $value->percentage_difference,
                'previous_percentage_difference' => $value->previous_percentage_difference,
            );
            $export_data[] = $newData;
         }
         return $export_data;


    }






}
