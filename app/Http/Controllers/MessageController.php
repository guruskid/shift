<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageCreated;
use App\Events\LastMessage;
use Nahid\Talk\Facades\Talk;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MessageController extends Controller
{
    public function index($id)
    {
        /* Get all messages between the auth user by a second user id */
        Talk::setAuthUserId(Auth::User()->id);

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

        return response()->json([$messages]);
    }

    public function read($id)
    {

        /* Mark messages as read after opening  */
        Talk::setAuthUserId(Auth::User()->id);

        $conversations = Talk::getConversationsByUserId($id, 0, 100000000000000);
        $messages = $conversations->messages;
        foreach ($messages as $message ) {
            if ($message->user_id != Auth::user()->id) {
                Talk::makeSeen($message->id);
            }
        }
        return response()->json(true);
    }



    public function store(Request $request)
    {
        /* Send message to the database */
        Talk::setAuthUserId(auth()->user()->id);

        $sent = Talk::sendMessageByUserId($request->user_id, $request->body);
        $sent = $request->user()->messages->last();

        /* Broadcast the message sent to listeners */
        broadcast(new MessageCreated($sent))
            ->toOthers();


        broadcast(new LastMessage($sent))
            ->toOthers();

        return response()->json($sent);
    }

    public function pop(Request $request)
    {
        $this->validate($request, [
            'pop' => 'image|max:7048|required',
        ]);
        $file = $request->pop;
        $extension = $file->getClientOriginalExtension();
        $filenametostore = time().uniqid(). '.' . $extension;
        Storage::put('public/pop/' . $filenametostore, fopen($file, 'r+'));
        //Resize image here
        /* $thumbnailpath = 'storage/pop/' . $filenametostore;
        $img = Image::make($thumbnailpath)->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($thumbnailpath); */
        Talk::setAuthUserId(auth()->user()->id);

        $sent = Talk::sendMessageByUserId($request->user_id, $filenametostore);
        $sent = $request->user()->messages->last();
        $sent->type = 1;
        $sent->save();

        broadcast(new MessageCreated($sent))
            ->toOthers();
        broadcast(new LastMessage($sent))
            ->toOthers();

        return response()->json($sent);
    }

    public function convDetails($id)
    {
        $conversation = DB::table('conversations')->where('id', $id)->first();
        return response()->json($conversation);
    }


}
