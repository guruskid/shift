<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Nahid\Talk\Facades\Talk;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function inbox()
    {
        Talk::setAuthUserId(Auth::User()->id);
        /* Talk::setAuthUserId(1); */
        $inboxes = Talk::getInbox();
        return response()->json([
            "success" => true,
            "data" => $inboxes
        ]);
    }

    public function messages($id)
    {
        /* Get messages between auth user and another user */
        Talk::setAuthUserId(Auth::User()->id);
        /* Talk::setAuthUserId(2); */
        $conversations = Talk::getConversationsByUserId($id, 0, 100000000000000);
        if ($conversations == false) {
            return response()->json(false);
        }

        $messages = $conversations->messages;
        foreach ($messages as $message ) {
            if ($message->user_id != Auth::user()->id) {
                Talk::makeSeen($message->id);
            }
        }

        return response()->json([
            "success" => true,
            "data" => $messages
        ]);
    }
}
