<?php

namespace App\Http\Controllers;

use App\AccountantTimeStamp;
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
            if ($action == 'active') {
                AccountantTimeStamp::create([
                    'user_id' => $id,
                    'activeTime' => Carbon::now(),
                ]);
            }
            if($action == 'waiting')
            {
                $accountant = AccountantTimeStamp::where('user_id',$id)->latest()->first();
                if(!empty($accountant))
                {
                    $time_stamp =  $accountant->where('user_id',$id)->latest()->first();

                    $startTime = $accountant->activeTime;
                    $endTime = Carbon::now();
                    $totalDuration =  Carbon::parse($startTime)->diffInMinutes($endTime);
                    if($totalDuration < 5){
                        $time_stamp->delete();
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
