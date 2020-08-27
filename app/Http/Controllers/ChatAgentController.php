<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use App\User;

class ChatAgentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin', 'super']);
    }

    public function chatAgents()
    {
        $users = User::where('role', 888)->orderBy('updated_at', 'desc')->paginate(10);
        foreach ($users as $user ) {
            $user->transactions = Transaction::where('agent_id', $user->id)->count();
        }

        return view('admin.chat_agents', compact('users'));
    }

    public function addChatAgent(Request $r)
    {
        $user = User::where('email', $r->email)->first();
        $user->role = 888;
        $user->status = 'waiting';
        $user->save();
        return redirect()->back()->with(['success'=>'Agent added']);
    }

    public function changeStatus($id, $action)
    {
        $user = User::find($id);
        $user->status = $action;

        return response()->json($user->save());
    }

    public function removeAgent($id)
    {
        $user = User::find($id);
        $user->status = 'declined';
        $user->role = 1;

        return response()->json($user->save());
    }
}
