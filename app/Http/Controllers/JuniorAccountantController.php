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

    public function addAccountOfficer(Request $r)
    {
        $user = User::where('email',$r->userEmail)->first();

        if(!$user)
        {
            return back()->with(['error'=>'Invalid Email Try Again']);
        }

        $user->role = 775;
        $user->status = 'waiting';
        $user->save();
        return back()->with(['success'=>'Account Officer added successfully']);
    }

    public function action($id, $action)
    {   
        $user = User::find($id);
        $user->status = $action;
<<<<<<< HEAD

=======
        
        $user->save();

        $nairaUsersWallet = NairaWallet::sum('amount');
>>>>>>> 31cf241bc84e2c5fcbd45f891dfb9865d2d405eb
        if(($user->role == 775))
        {
            $nairaUsersWallet = NairaWallet::sum('amount');
            if ($action == 'active') {
<<<<<<< HEAD

=======
>>>>>>> 31cf241bc84e2c5fcbd45f891dfb9865d2d405eb
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
<<<<<<< HEAD

=======
>>>>>>> 31cf241bc84e2c5fcbd45f891dfb9865d2d405eb
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
            if($action == 'remove')
            {
                $user->role = 1;
            }
           
        }
        $user->save();
        return back()->with(['success'=>'Action Successfull']);
    }
}
