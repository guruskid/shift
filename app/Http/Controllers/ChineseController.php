<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChineseController extends Controller
{
    public function transactionsChinese()
    {
        $transactions = Transaction::with('user')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->latest()->paginate(1000);
        $segment = 'All';

        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function payoutHistory()
    {
        $transactions = Transaction::with('user')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->latest()->paginate(1000);
        $segment = 'All';

        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function buyTransac()
    {
        $transactions = Transaction::where('type', 'buy')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->latest()->paginate(1000);
        $segment = 'Buy';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function sellTransac()
    {
        $transactions = Transaction::where('type', 'sell')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->latest()->paginate(1000);
        $segment = 'Sell';
        return view('admin.transactions', compact(['transactions', 'segment']));
    }


}
