<?php

namespace App\Http\Controllers;

use App\LoginSession;
use Illuminate\Http\Request;

class LoginSessionController extends Controller
{
    public function FindSessionData($id)
    {
        $checker = LoginSession::where('user_id',$id)->whereDate('created_at',now()->format('Y-m-d'))
        ->orderBy('id','desc')->first();
        if(!$checker)
        {
            $this->createSessionData($id);
        }else{
            if(now()->diffInHours($checker->created_at) >= 6){
                $this->createSessionData($id);
            }
        }
    }

    public function createSessionData($id)
    {
        LoginSession::create([
            'user_id' => $id,
        ]);
    }
}
