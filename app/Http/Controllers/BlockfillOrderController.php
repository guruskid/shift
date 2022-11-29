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

        // return;
        $client = new Client();
        $url = env('BLOCKFILL_URL') . '/dantown/v1/order/place';
        $res = $client->request('POST', $url, [
            'json' => [
                "orderType" => "Market",
                "quantity" => round($transaction->quantity, 6),
                "side" => $transaction->type,
                "symbol" => "BTC/USDT_TRX",
                "timeInForce" => "ImmediateOrCancel"
            ],
        ]);

        // \Log::info($res->getBody());

        $currency_id = 1;
        BlockfillOrder::create([
            'currency_id' => $currency_id,
            'transaction_id' => $transaction->id,
            'type' => $transaction->type,
            'pair' => 'BTC/USDT_TRX',
            'quantity' => $transaction->quantity,
            'usd' => $transaction->amount,
            'rate' => $transaction->card_price,
        ]);

        return true;

    }
}
