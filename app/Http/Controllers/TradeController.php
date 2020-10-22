<?php

namespace App\Http\Controllers;

use App\Card;
use App\Http\Resources\CardResource;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function assets()
    {
        $assets = Card::where('buyable', 1)->orWhere('sellable', 1)->get();

        return response()->json($assets);
    }

    public function assetRates($trade_type, $card_name)
    {
        $card = Card::where('name', $card_name)->firstOrFail();

        $card_rates =  new CardResource($card);

        return response()->json($card_rates);
    }
}
