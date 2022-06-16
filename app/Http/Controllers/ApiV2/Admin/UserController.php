<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all()->paginate(1000);
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
}
