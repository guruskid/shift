<?php

namespace App\Http\Controllers\Admin;

use App\BitcoinWallet;
use App\CryptoCurrency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Summary;
use App\Transaction;

class SummaryController extends Controller
{
    public function index($currency_id)
    {
        $today = Summary::whereDate('created_at', now())->where('crypto_currency_id', $currency_id)->first();
        if (!$today) {
            Summary::create([
                'crypto_currency_id' => $currency_id,
            ]);
        }
        $summaries = Summary::where('crypto_currency_id', $currency_id)->orderBy('id', 'desc')->paginate(30);
        $currency = CryptoCurrency::find($currency_id);
        switch ($currency_id) {
            case 1:
                $card_id = 102;
                break;
            case 2:
                $card_id = 137;
                break;
            default:
                return back()->with(['error' => 'There is an error with this page']);
                break;
        }

        return view('admin.summary.index', compact('summaries', 'currency', 'card_id'));
    }

    public function transactions(Summary $summary, $card_id)
    {
        $date = $summary->created_at;
        $sell_transactions = Transaction::where('card_id', $card_id)->where('type', 'sell')->whereDate('created_at', $date)->where('status', 'success')->get();
        $sell_btc = $sell_transactions->sum('quantity');
        $sell_usd = $sell_transactions->sum('amount');
        try {
            $sell_average = $sell_usd / $sell_btc;
        } catch (\Throwable $th) {
            $sell_average = 0;
        }

        $buy_transactions = Transaction::where('card_id', $card_id)->where('type', 'buy')->whereDate('created_at', $date)->where('status', 'success')->get();
        $buy_btc = $buy_transactions->sum('quantity');
        $buy_usd = $buy_transactions->sum('amount');
        try {
            $buy_average = $buy_usd / $buy_btc;
        } catch (\Throwable $th) {
            $buy_average = 0;
        }

        switch ($card_id) {
            case 102:
                $cur = 'BTC';
                break;
            case 137:
                $cur = 'ETH';
                break;
            default:
                return back()->with(['error' => 'There is an error with this page']);
                break;
        }

        return view('admin.summary.transactions', compact(
            'buy_transactions',
            'buy_btc',
            'buy_usd',
            'buy_average',
            'sell_transactions',
            'sell_btc',
            'sell_usd',
            'sell_average',
            'cur', 'card_id'
        ));
    }

    public function sortTransactions(Request $request, $card_id)
    {
        $data = $request->validate([
            'start' => 'required|date|string',
            'end' => 'required|date|string',
        ]);

        $sell_transactions = Transaction::where('card_id', $card_id)->where('type', 'sell')
            ->where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end'])
            ->where('status', 'success')->get();


        $sell_btc = $sell_transactions->sum('quantity');
        $sell_usd = $sell_transactions->sum('amount');
        try {
            $sell_average = $sell_usd / $sell_btc;
        } catch (\Throwable $th) {
            $sell_average = 0;
        }

        $buy_transactions = Transaction::where('card_id', $card_id)->where('type', 'buy')
            ->where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end'])
            ->where('status', 'success')->get();

        $buy_btc = $buy_transactions->sum('quantity');
        $buy_usd = $buy_transactions->sum('amount');
        try {
            $buy_average = $buy_usd / $buy_btc;
        } catch (\Throwable $th) {
            $buy_average = 0;
        }

        switch ($card_id) {
            case 102:
                $cur = 'BTC';
                break;
            case 137:
                $cur = 'ETH';
                break;
            default:
                return back()->with(['error' => 'There is an error with this page']);
                break;
        }

        return view('admin.summary.transactions', compact(
            'buy_transactions',
            'buy_btc',
            'buy_usd',
            'buy_average',
            'sell_transactions',
            'sell_btc',
            'sell_usd',
            'sell_average', 'cur', 'card_id'
        ));
    }

    public function ledgerBalance()
    {
        $wallets = BitcoinWallet::all();

        foreach ($wallets as $wallet) {
            $transactions = $wallet->transactions()->where('status', 'success')->get();
            $wallet->in = $transactions->sum('credit');
            $wallet->out = $transactions->sum('debit');

            $wallet->lbal = $wallet->in - $wallet->out;
            $wallet->diff = $wallet->balance - $wallet->lbal;
        }

        return view('admin.bitcoin_wallet.wallet_balances', compact('wallets'));
    }
}
