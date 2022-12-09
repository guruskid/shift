<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserRatingController extends Controller
{
    public function store(Request $request){
            $data = Validator::make($request->all(), [
                'rate' => "required|numeric",
                "text" => "sometimes|string"
            ]);
            if( $data->fails()){
                return response()->json([
                    "message" => $data->errors()
                ],422);
            }

            UserRating::create([
                'user_id'=> auth()->user()->id,
                $data
            ]);

            return response()->json([
                "success" => true,
                "message" => "User rate created"
            ],201);
    }
}
