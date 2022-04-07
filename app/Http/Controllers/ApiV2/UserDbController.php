<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserDb;
use Illuminate\Support\Facades\Validator;

class UserDbController extends Controller
{
    public function addUser(Request $r)
    {

        $validator = Validator::make($r->all(), [
            'fullname' => 'required',
            'email' => 'required|email|unique:user_dbs',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $user_db = new UserDb();
        $user_db->fullname = $r->fullname;
        $user_db->email = $r->email;
        $user_db->save();

        return response()->json([
            'success' => true,
            'user_db' => $user_db
        ]);
    }

    public function getNameAndEmail(Request $r)
    {
        return response()->json([
            'success' => true,
            'user_db' => UserDb::where('email', $r->email)->first()
        ]);
    }
}
