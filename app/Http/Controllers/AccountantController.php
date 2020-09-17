<?php

namespace App\Http\Controllers;

use App\User;
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
        return back()->with(['success'=>'Action Successfull']);
    }

}
