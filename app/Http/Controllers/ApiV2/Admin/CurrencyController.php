<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    //
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'string|unique:currencies|required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $data = $request->only(['name']);
        Currency::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Currency Added'
        ]);
    }
}
