<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Nahid\Talk\Facades\Talk;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        return $this->middleware(['auth']);
    }

    public function index($id)
    {
        if (Auth::user()->status != 'active') {
            return redirect()->route('admin.dashboard')->with(['error' => 'You are not eligible to visit this page, contact the super admin' ]);
        }
        Talk::setAuthUserId(Auth::User()->id);
        $inboxes = Talk::getInbox();

        $user = User::findOrFail($id);
        return view('chat',compact(['id', 'user', 'inboxes' ]));
    }

    /* get messages and users in  json */
    public function inbox()
    {
        Talk::setAuthUserId(Auth::User()->id);
        $inboxes = Talk::getInbox();
        foreach ($inboxes as $inbox) {
            $inbox->unread = Talk::getConversationsById($inbox->thread->conversation_id, 0, 100000000000000)
            ->messages->where('is_seen', 0)->where('user_id', '!=', Auth::user()->id)->count();
        }

        return response()->json($inboxes);
    }

    public function agents()
    {
        $agents = User::where('role', 999)->where('status', 'verified')->get();
        return response()->json($agents);
    }

    public function userDetails($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function userTransactions($id)
    {
        $transactions = User::find($id)->transactions->take(10);

        return response()->json($transactions);
    }
}
