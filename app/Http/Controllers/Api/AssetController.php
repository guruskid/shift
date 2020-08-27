<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Rate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssetController extends Controller
{

    public function getAssets()
    {
        $assets = Card::all();
        return response()->json([
            "success" => true,
            "data" => $assets
        ]);
    }

    public function getRate(Request $request)
    {
        $rate = Rate::where('card', $request->asset)->where('rate_type', $request->trade_type)
        ->where('min', '<=', $request->amount)->where('max', '>=', $request->amount)
        ->value($request->country);
        if ($rate == null) {
            return response()->json([
                "success" => false,
                "data" => 'No rates available for selected parameters'
            ]);
        } else {
            $value = number_format($rate * $request->amount);
            return response()->json([
                "success" => true,
                "data" => $value,
                "rate" => $rate
            ]);
        }
    }

}
