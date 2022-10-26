<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\User;

class LedgerController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(100);
        foreach ($users as $user ) {
            $user->ledger = UserController::ledgerBalance($user->id)->getData();
        }

        return view('admin.ledger.index', compact('users'));
    }
}
