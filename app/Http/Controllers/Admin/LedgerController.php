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
        foreach ($users as $user) {
            $user->ledger = UserController::ledgerBalance($user->id)->getData();
        }

        $extra_data = [];

        return view('admin.ledger.index', compact(['users', 'extra_data']));
    }

    public function negative()
    {

        $users = collect();
        User::latest()->chunk(500, function ($us) use ($users) {
            foreach ($us as $user) {
                $user->ledger = UserController::ledgerBalance($user->id)->getData();
                if ($user->ledger->balance < 0) {
                    $users->push($user);
                }
            }
        });

        $users = $users->paginate(200);

        $extra_data = [
            [
                'name' => "Total count",
                'value' => $users->count(),
            ]
        ];

        return view('admin.ledger.index', compact(['users', 'extra_data']));
    }
}
