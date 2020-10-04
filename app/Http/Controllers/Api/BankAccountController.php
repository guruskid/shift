<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function accounts()
    {
        $accts = Auth::user()->accounts;
        return response()->json([
            'success' => true,
            'data' => $accts
        ]);
    }
}
