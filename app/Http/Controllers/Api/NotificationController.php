<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\Birthday as JobsBirthday;
use App\Notification;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $isBirthday = Auth::user()->birthday_status;
        $nots = Notification::where('user_id', 0)->orWhere('user_id', $user_id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $nots
        ]);
    }


    public function read($id)
    {
        $n = Notification::find($id);
        $n->is_seen = 1;
        $n->save();
        return response()->json(['success' => true]);
    }

    public function settings()
    {
        if (!Auth::user()->notificationsetting) {
            Auth::user()->notificationSetting()->create();
        }
        return response()->json([
            'success' => true,
            'data' => Auth::user()->notificationSetting,
        ]);

    }

    public function updateSettings(Request $r)
    {
        $v = $r->value;
        switch ($r->name) {
            case 'wallet_sms':
                Auth::user()->notificationSetting->wallet_sms = $v;
                break;
            case 'wallet_email':
                Auth::user()->notificationSetting->wallet_email = $v;
                break;
            case 'trade_sms':
                Auth::user()->notificationSetting->trade_sms = $v;
                break;
            case 'trade_email':
                Auth::user()->notificationSetting->trade_email = $v;
                break;

            default:
                return response()->json(["success" => false]);
                break;
        }

        Auth::user()->notificationSetting->save();
        return response()->json(["success" => true, 'data' => Auth::user()->notificationSetting]);
    }

}
