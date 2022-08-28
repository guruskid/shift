<?php

namespace App\Http\Controllers;

use App\BlockfillOrder;
use App\Order;
use App\Transaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class BlockfillOrderController extends Controller
{
    public static function order(Transaction $transaction)
    {
        \Log::info($transaction->quantity);
        // return;
        $client = new Client();
        $url = env('BLOCKFILL_URL') . '/dantown/v1/order/place';
        $res = $client->request('POST', $url, [
            'json' => [
                "orderType" => "Limit",
                "price" => round($transaction->card_price),
                "quantity" => round($transaction->quantity, 6),
                "side" => "Sell",
                "symbol" => "BTC/USDT",
                "timeInForce" => "GoodTillCancel"
            ],
        ]);

        \Log::info($res->getBody());

        $currency_id = 1;
        BlockfillOrder::create([
            'currency_id' => $currency_id,
            'transaction_id' => $transaction->id,
            'type' => 'sell',
            'pair' => 'BTC/USDT',
            'quantity' => $transaction->quantity,
            'rate' => $transaction->card_price,
        ]);

        return true;

    }
}
