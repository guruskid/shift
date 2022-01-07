<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class testController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'today' => NULL
        ]);
    }
}
