<?php

namespace App\Http\Controllers;

use App\AccountantTimeStamp;
use App\NairaWallet;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JuniorAccountantController extends Controller
{
    public function showAccountOfficers()
    {
        $users = User::whereIn('role', [775])->latest()->get();

        return view('admin.account_officers', compact('users'));
    }

    public function action($id, $action)
    {   
        $user = User::find($id);
        $user->status = $action;
        
        $user->save();
        if(($user->role == 775))
        {
            $nairaUsersWallet = NairaWallet::sum('amount');
            if ($action == 'active') {

                $user_check = AccountantTimeStamp::where('user_id', $user->id)->whereNull('inactiveTime')->get();
                if($user_check->count() <= 0)
                {
                    AccountantTimeStamp::create([
                        'user_id' => $id,
                        'activeTime' => Carbon::now(),
                        'opening_balance' => $nairaUsersWallet,
                    ]);
                }
                
            }
            if($action == 'waiting')
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
                            'closing_balance' => $nairaUsersWallet,
                        ]); 
                    }
    
                    
                }
                
            }
        }
        return back()->with(['success'=>'Action Successfull']);
    }
}
