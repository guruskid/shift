<?php

namespace App\Http\Controllers\ApiV2\Admin;


use App\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index(){
        // $notification =  Notification::whereIn('user_id', [0, Auth::user()->id])->where('is_cleared', 0);
        $notification =  Notification::where('user_id', Auth::user()->id)->where('is_cleared', 0);


        $data['notifications'] = $notification->select('title', 'id', 'is_seen', 'is_cleared', 'created_at')->orderBy('created_at', 'desc')->paginate(25);
        $data['unread_notications'] =  $notification->where('is_seen', 0)->count();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function show ($id){
        $notification =  Notification::where('user_id',  Auth::user()->id)->where('is_cleared', 0)->find($id);


        if (is_null($notification)) {
            return response()->json(["success" => false, 'message' => "notification does not exists0"], 404);
        }

            $notification->is_seen = 0;
            $notification->save();


        return response()->json([
            'success' => true,
            'data' =>  $notification
        ], 200);


    }


    public function mark_clear_all(Request $req){
        $notifications =  Notification::where('user_id',  Auth::user()->id)->where('is_cleared', 0)->where("is_seen", 0)->get();


    
        if (is_null($notifications)) {
            return response()->json(["success" => false, 'message' => "notification does not exists"], 404);
        }

        foreach ($notifications as  $notification) {
           if ($req->mark_all_as_read == 1) {
            $notification->is_seen = 1;
           }

           if ($req->clear_all == 1) {
            $notification->is_cleared = 1;
           }

           $notification->save();
        }

        return response()->json([
            'success' => true,
        ], 200);



    }




}
