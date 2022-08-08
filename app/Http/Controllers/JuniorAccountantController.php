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

        $nairaUsersWallet = NairaWallet::sum('amount');
        if(($user->role == 775))
        {
            $nairaUsersWallet = NairaWallet::sum('amount');
            if ($action == 'active') {
                AccountantTimeStamp::create([
                    'user_id' => $id,
                    'activeTime' => Carbon::now(),
                ]);
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
                        $time_stamp->update(['inactiveTime' => Carbon::now()]); 
                    }
    
                    
                }
                
            }
        }
        return back()->with(['success'=>'Action Successfull']);
    }
}
