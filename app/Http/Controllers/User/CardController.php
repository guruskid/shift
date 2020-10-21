<?php

namespace App\Http\Controllers\User;

use App\Card;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;

class CardController extends Controller
{
    //

    public function testData(Card $card)
    {
        return response()->json([new CardResource($card)]);
    }

    public function view(Request $request, Card $card)
    {
        $fullResourceData = new CardResource($card);

        return view('user.cards.view', ['card'=> $fullResourceData]);
    }
}
