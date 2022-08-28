<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\AccountantTimeStamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaWallet;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class AccountantController extends Controller
{

    public function listOfAccountants()
    {
        $listOfAccountant = User::select('id','first_name','last_name','email','phone','role','status','username')
        ->with('accountantTimestamp')->whereIn('role',[777,775, 889])->get();
        $listOfAccountant = $this->appendDataFromLastActive($listOfAccountant);

        return response()->json([
            'success' => true,
            'accountant' => $listOfAccountant
        ],200);
    }

    public function summary()
    {
        $activeUser = User::select('id','first_name','last_name','email','phone','role','status','username')
        ->with('accountantTimestamp')->whereIn('role',[777,775, 889])->where('status', 'active')->first();

        $summary = [
            'DepositCount' => "Not Available",
            'DepositAmount' => "Not Available",
            'WithdrawalCount' => "Not Available",
            'WithdrawalAmount' => "Not Available"
        ];
        
        if($activeUser->accountantTimestamp->count() > 0){
            $startTime = $activeUser->accountantTimestamp->first()->activeTime;
            $endTime = ($activeUser->accountantTimestamp->first()->inactiveTime == null) ? now() : $activeUser->accountantTimestamp->first()->inactiveTime;

            $p2pTranx = NairaTrade::where('status', 'success')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->get();

            $deposit_count = $p2pTranx->where('type','deposit')->count();
            $deposit_amount = $p2pTranx->where('type','deposit')->sum('amount');

            $withdrawal_count =  $p2pTranx->where('type','withdrawal')->count();
            $withdrawal_amount = $p2pTranx->where('type','withdrawal')->sum('amount');

            $summary = [
                'DepositCount' => $deposit_count,
                'DepositAmount' => $deposit_amount,
                'WithdrawalCount' => $withdrawal_count,
                'WithdrawalAmount' => $withdrawal_amount
            ];
        }

        return response()->json([
            'success' => true,
            'summary' => $summary,
        ], 200);
    }

    public function ChartAndTransactions()
    {
        $activeUser = User::select('id','first_name','last_name','email','phone','role','status','username')
        ->with('accountantTimestamp')->whereIn('role',[777,775, 889])->where('status', 'active')->first();

        if($activeUser->accountantTimestamp->count() <= 0)
        {
            return response()->json([
                'success' => true,
                'transactions' => "Not Available",
                'chart' => "Not Available"
            ],200);
        }
        
        $startTime = $activeUser->accountantTimestamp->first()->activeTime;
        $endTime = ($activeUser->accountantTimestamp->first()->inactiveTime == null) ? now() : $activeUser->accountantTimestamp->first()->inactiveTime;

        $p2pTranx = NairaTrade::where('status', 'success')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->get();

        $chart = $p2pTranx->groupBy(function($date){
            return Carbon::parse($date->created_at)->format('h');
        });

        $chartExportData = array();
        foreach ($chart as $key => $value) {

            $chartExportData[] = array(
                'hour' =>  $key,
                'amount' => $value->sum('amount')
            );
        }
        $transactions = $this->TransactionsDuringActiveTime($startTime, $endTime);

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
            'chart' => $chartExportData
        ],200);

    }

    public function activateAccountant(Request $r)
    {
        $validator = Validator::make($r->all(),[
            'id' => 'required|integer',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $id = $r->id;
        $user = User::find($id);

        if(!in_array($user->role, [777,775,889] ))
        {
            return response()->json([
                'success' => false,
                'message' => "$user->first_name $user->last_name is not an accountant"
            ],401);
        }
        $nairaUsersWallet = NairaWallet::sum('amount');

        if(in_array($user->role, [777,775] )){
            if($user->status != 'active'):
                $user->status = 'active';
                $user->save();

                $this->activate($id, $nairaUsersWallet);

                return response()->json([
                    'success' => true,
                    'message' => "$user->first_name $user->last_name is activated"
                ],200);
            endif;
        }

        if($user->role == 889)
        {
            $counterSA = User::where(['role' => 889, 'status' => 'active', 'id' => $id])
            ->whereHas('accountantTimestamp', function ($query){
                $query->whereNotNull('inactiveTime');
            })->first();

            if($counterSA){
                $this->activate($id, $nairaUsersWallet);

                return response()->json([
                    'success' => true,
                    'message' => "$user->first_name $user->last_name is activated"
                ],200);
            }
            
        }
        
        return response()->json([
            'success' => true,
            'message' => "$user->first_name $user->last_name is already activated"
        ],200);
    }

    public function deactivateAccountant(Request $r)
    {
        $validator = Validator::make($r->all(),[
            'id' => 'required|integer',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $id = $r->id;
        $user = User::find($id);
        if(!in_array($user->role, [777,775, 889] ))
        {
            return response()->json([
                'success' => false,
                'message' => "$user->first_name $user->last_name is not an accountant"
            ],401);
        }
        $nairaUsersWallet = NairaWallet::sum('amount');

        if(in_array($user->role, [777,775] )){
            $nairaUsersWallet = NairaWallet::sum('amount');
            if($user->status != 'waiting'):
                $user->status = 'waiting';
                $user->save();

                $this->deactivate($id, $nairaUsersWallet);

                return response()->json([
                    'success' => true,
                    'message' => "$user->first_name $user->last_name is deactivated"
                ],200);
            endif;
        }

        if($user->role == 889)
        {
            $counterSA = User::where(['role' => 889, 'status' => 'active', 'id' => $id])
            ->whereHas('accountantTimestamp', function ($query){
                $query->whereNull('inactiveTime');
            })->first();

            if($counterSA)
            {
                $this->deactivate($id, $nairaUsersWallet);
                return response()->json([
                    'success' => true,
                    'message' => "$user->first_name $user->last_name is deactivated"
                ],200);
            }

        }

        return response()->json([
            'success' => true,
            'message' => "$user->first_name $user->last_name is already deactivated"
        ],200);
    }

    public function activate($id, $amount)
    {
        $user_check = AccountantTimeStamp::where('user_id', $id)->whereNull('inactiveTime')->get();

        if( $user_check->count() <= 0 )
        {
            AccountantTimeStamp::create([
                'user_id' => $id,
                'activeTime' => Carbon::now(),
                'opening_balance' => $amount,
            ]);
        }
    }
    public function deactivate($id, $amount)
    {
        $accountant = AccountantTimeStamp::where('user_id',$id)->whereNull('inactiveTime')->orderBy('id','DESC')->first();

        if(!empty($accountant))
        {
            $activeTime = $accountant->activeTime;
            $duration = Carbon::parse($activeTime)->diffInMinutes(now());
            if($duration < 5){
                $accountant->delete();
            }
            else{
                $accountant->update([
                    'inactiveTime' => Carbon::now(),
                    'closing_balance' => $amount,
                ]); 
            }  
        }
    }

    public function appendDataFromLastActive(Collection $collection)
    {
        $all_transactions = Transaction::where('status','success')->get();
        $utilityTranx = UtilityTransaction::where('status','success')->get();
        $p2pTranx = NairaTrade::where('status','success')->get();

        $amount = NairaWallet::sum('amount');

        foreach($collection as $accountant)
        {
            if($accountant->accountantTimestamp->count() == 0)
            {
                $roles = new SettingController();
                $accountant->role_name = $roles->roleName($accountant->role);
                $latest_timeStamp = $accountant->accountantTimestamp->first();
    
                $accountant->openingBalance = 'Not Available';
                $accountant->closingBalance = 'Not Available';
    
                $accountant->totalAmountPaidOut = 'Not Available';
                $accountant->totalDeposit = 'Not Available';
    
                $accountant->pendingWithdrawal = 'Not Available';
                $accountant->CurrentBalance = 'Not Available';
    
                $accountant->activeTime = 'Not Available';
                $accountant->inactiveTime = 'Not Available';
            }
            else{
                $roles = new SettingController();
                $accountant->role_name = $roles->roleName($accountant->role);
                $latest_timeStamp = $accountant->accountantTimestamp->first();
                
                $openingBalance = $latest_timeStamp->opening_balance;
                $closingBalance = $latest_timeStamp->closing_balance;

                $accountant->openingBalance = ($openingBalance == null) ? 'Not Available' : number_format($openingBalance);
                $accountant->closingBalance = ($closingBalance == null) ? number_format($amount) : number_format($closingBalance);

                $accountant->totalAmountPaidOut = $this->totalAmountPaidOut($latest_timeStamp->activeTime,$latest_timeStamp->inactiveTime,$all_transactions,$utilityTranx,$p2pTranx);
                $accountant->totalDeposit = $this->totalDeposit($latest_timeStamp->activeTime,$latest_timeStamp->inactiveTime,$all_transactions,$p2pTranx);

                $accountant->pendingWithdrawal = $this->pendingWithdrawal($latest_timeStamp->activeTime,$latest_timeStamp->inactiveTime,$p2pTranx);
                $accountant->CurrentBalance = number_format($amount);

                $accountant->activeTime = $latest_timeStamp->activeTime;
                $accountant->inactiveTime = $latest_timeStamp->inactiveTime;
            }
        }  

        $activeUsers = $collection->where('status', 'active');
        $inActiveUsers = $collection->where('status', 'waiting');

        $value = collect()->concat($activeUsers)->concat($inActiveUsers);
        $exportData = $value->map->only(['id','first_name','last_name','email','phone','role','status','username','role_name',
        'openingBalance','closingBalance','totalAmountPaidOut','totalDeposit','pendingWithdrawal','CurrentBalance','activeTime','inactiveTime']);

        return collect($exportData);
    }

    public function totalAmountPaidOut($startTime, $endTime, Collection $tokens, Collection $utility, Collection $p2p)
    {
        if($endTime ==null)
        {
            $endTime = now();
        }

        $tranxAmount = $tokens->where('type','buy')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount_paid');
        $utilityAmount = $utility->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount');

        $p2pAmount = $p2p->where('type','withdrawal')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount');
        $export_data = $tranxAmount + $utilityAmount + $p2pAmount;

        return number_format($export_data);
    }


    public function totalDeposit($startTime, $endTime, Collection $tokens, Collection $p2p)
    {
        if($endTime ==null)
        {
            $endTime = now();
        }

        $tranxAmount = $tokens->where('type','sell')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount_paid');
        $p2pAmount = $p2p->where('type','deposit')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount');

        $export_data = $tranxAmount + $p2pAmount;

        return number_format($export_data,);
    }

    public function pendingWithdrawal($startTime, $endTime, Collection $p2p)
    {
        if($endTime ==null)
        {
            $endTime = now();
        }

        $p2pAmount = $p2p->where('type','withdrawal')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->sum('amount');
        return number_format($p2pAmount);
    }

    public function TransactionsDuringActiveTime($startTime, $endTime)
    {
        
        $tranx = Transaction::with('asset','user')->where('status','success')->where('created_at','>=',$startTime)->where('created_at','<=',$endTime)->get();
        foreach ($tranx as $value) {
            $value->name = $value['user']->first_name." ".$value['user']->last_name;
            $value->TransactionName = ($value['asset']->is_crypto == 0) ? "GiftCard" : "Crypto";
            $value->AmountNGN = $value->amount_paid;
            $value->valueUSD = $value->amount;
            $value->date = $value->created_at->format('d M y');
        }

        $exportData = $tranx->map->only(['id','name','TransactionName','card','AmountNGN','valueUSD','status','date']);
        return collect($exportData);
    }
}
