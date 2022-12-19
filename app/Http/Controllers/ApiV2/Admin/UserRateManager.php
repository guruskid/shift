<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserRating;

class UserRateManager extends Controller
{
    public function index(){
        $data = UserRating::with('user')->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data, 
        ]);
    }
}
