<?php

namespace App\Http\Controllers\ApiV2;

use App\ChatMessages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatMessagesController extends Controller
{
    //?showing chat
    public function Messages($ticketNo)
    {
        $messages = ChatMessages::where('ticket_no', $ticketNo)->latest()->get();
        return response()->json([
            "success" => true,
            "messages" => $messages,
        ], 200);
    }

    public function sendMessage(Request $r)
    {
        //?ticket no ,message, checking is agent
        //? agent role for customer happiness 555

        $validator = Validator::make($r->all(),[
            'ticketNo' => 'required',
            'message' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $is_agent = in_array(Auth::user()->role, [555]) ? 1 : 0;
        $chatmessage = ChatMessages::create([
            'ticket_no' => $r->ticketNo,
            'user_id' => Auth::user()->id,
            'message' => $r->message,
            'is_agent' => $is_agent,
        ]);

        return response()->json([
            'success' => true,
            'message' => $chatmessage,
        ], 201);

    }


}
