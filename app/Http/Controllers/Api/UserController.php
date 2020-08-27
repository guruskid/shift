<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function accounts()
    {
         if ($accts = Auth::user()->accounts) {
            return response()->json([
                'success' => true,
                'data' => $accts
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No account linked to this account'
            ]);
        }
    }

    public function notifications()
    {
        $user_id = 1;
        $nots = Notification::where('user_id', 0)->orWhere('user_id', $user_id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $nots
        ]);
    }
}
