<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

		if($user->role == 999){
    		return redirect()->route('admin.dashboard');
        }
        elseif ($user->role == 888) {
            return redirect()->route('admin.assigned-transactions');
        }
		elseif($user->role == 1 OR $user->role == 2){
    		return redirect()->route('user.dashboard');
    	}
		else{
    		abort(404);
    	}
    }

    public function setupBank()
    {
        $banks = Bank::all();

        return view('auth.bank', compact('banks'));
    }

    public function addUserBank(Request $request)
    {
        $s = Bank::where('code', $request->bank_name)->first();
        $err = 0;

        $accts = Auth::user()->accounts;
        foreach ($accts as $a ) {
            if ($a->account_number == $request->account_number && $a->bank_name == $s->name ) {
                $err += 1;
            }
        }

        if ($err == 0) {
            $a = new Account();
            $a->user_id = Auth::user()->id;
            $a->account_name = $request->account_name;
            $a->bank_name = $s->name;
            $a->account_number = $request->account_number;
            $a->save();
        }


        Auth::user()->phone = $request->phone;
        Auth::user()->first_name = $request->account_name;
        Auth::user()->save();


        return redirect()->route('user.dashboard');
    }
}
