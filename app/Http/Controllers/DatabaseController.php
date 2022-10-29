<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Card;
use App\Rate;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin', 'super']);
        ini_set('max_execution_time', 30000);
    }

    public function username()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->first_name .= ' ' . $user->last_name;
            $user->last_name = '';
            $user->save();
        }
        echo "Successful";
    }


    public function accounts()
    {
        $accounts = Account::all();
        foreach ($accounts as $a) {
            if ($a->bank_id == null) {
                $bid = Bank::where('name', $a->bank_name)->first();
                if ($bid == null) {
                    echo $a->bank_name . '<br> ';
                } else {
                    $a->bank_id = $bid->id;
                }

                $a->save();
            }
        }

        echo "Successfull";
    }

    public function transactions()
    {
        $ts = Transaction::all();
        foreach ($ts as $t) {
            $t->user_id = User::where('email', $t->user_email)->first()->id;
            $t->save();
        }
        echo "Successfull";
    }

    public function rates()
    {
        $rates = Rate::all();
        foreach ($rates as $a) {
            $bid = Card::where('name', $a->card)->first();
            if ($bid == null) {
                echo $a->card . '<br> ';
            } else {
                $a->card_id = $bid->id;
            }

            $a->save();
        }

        echo "Successfull";
    }

    public function txns()
    {
        $txns = Transaction::all();
        /*  $txns = Transaction::where('card_id', null)->get();
        foreach ($txns as $t ) {
            echo $t->card.'<br>';
        }
        dd($txns); */

        foreach ($txns as $t) {
            $bid = Card::where('name', $t->card)->first();
            if ($bid == null) {
                if ($t->card == 'BITCOIN' || $t->card == 'BITCOINS(50-500)' || $t->card == 'BITCOINS(50-2000)' || $t->card == 'BITCOINS(10-49)' || $t->card == 'BITCOINS(2001-9999)' || $t->card == 'BITCOINS(10,000 above)') {
                    $t->card_id = 102;
                }
                if ($t->card == 'WALMART') {
                    $t->card_id = 8;
                }
                echo $t->card . '<br> ';
            } else {
                $t->card_id = $bid->id;
            }

            $t->save();
        }

        echo "Successfull";
    }
}
