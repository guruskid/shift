<?php

namespace App\Http\Controllers;

use App\User;
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
        return back()->with(['success'=>'Action Successfull']);
    }
}
