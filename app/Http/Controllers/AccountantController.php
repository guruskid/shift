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

        return back()->with(['success'=>'Accountant added successfully']);
    }

    public function juniorAccountants()
    {
        $users = User::whereIn('role', [777, 889])->latest()->get();

        return view('admin.accountants', compact('users'));
    }

    public function action($id, $action)
    {   
        $user = User::find($id);
        if ($action == 'remove') {
            $user->role = 1;
        }elseif(Auth::user()->role == 999 && $action == 'upgrade-to-senior'){
            $user->role = 889;
        }
        else{
            $user->status = $action;
        }
        $user->save();

        $nairaUsersWallet = NairaWallet::sum('amount');
        //* tracking user
        if(($user->role == 777))
        {
            if ($action == 'active') {
                AccountantTimeStamp::create([
                    'user_id' => $id,
                    'activeTime' => Carbon::now(),
                    'opening_balance' => $nairaUsersWallet,
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
