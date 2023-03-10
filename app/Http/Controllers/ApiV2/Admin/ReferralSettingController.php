<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\ReferralSettings;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Validator;

class ReferralSettingController extends Controller
{
    public function index()
    {

        $referralSettings = ReferralSettings::latest()->get();
        $evangelist = array();
        $users = User::where('referral_code', '!=', null)->get();

        foreach ($users as $user) {
            $referred = User::where('referral_code', $user->referral_code);
            if ($referred->count() > 0) {
                if(!in_array($user, $evangelist)){
                    array_push($evangelist, $user);
                }
            }
        }

        $referralPaidOut = NairaTransaction::where('type', 'referral')->sum('amount');
        $referredUsers = User::where('referred', 1)->count();

        return response()->json([
            'success' => true,
            'settings' => $referralSettings,
            'total_evangilist' => sizeof($evangelist),
            'total_amount_paid_out' => $referralPaidOut,
            'converts' => $referredUsers,
            'evangilist' => $evangelist,
        ]);

        // return view('admin.referral', compact('referralSettings'));
    }

    public function changeStatus(Request $dt)
    {
        $refStatus = ReferralSettings::where('id', $dt->id)->get()[0];

        $refStatus->ref_status = $dt->ref_status;
        $refStatus->save();
        return back()->with('success', 'Referral status changed successfully');
    }

    public function setReferral(Request $dt)
    {


        $dt->validate([
            'percent' => 'required|integer',
        ]);

        $ref = new ReferralSettings();
        $ref->ref_status = 1;
        $ref->ref_percent = $dt->percent;
        $ref->save();

        return back()->with('success', 'Referral setting was success');
    }

    public function changePercentage(Request $dt)
    {
        $dt->validate([
            'percent' => 'required|integer',
            'id' => 'required'
        ]);

        $ref = ReferralSettings::where('id', $dt->id)->get()[0];
        $ref->ref_percent = $dt->percent;
        $ref->save();

        return back()->with('success', 'Referral percent was successfully turned to '.$dt->percent.'%');
    }


    public static function status()
    {
       $rs = ReferralSettings::latest()->get();
       return ($rs->count() < 1) ? $status = '0' : $status = $rs[0]->ref_status;
    }

    public static function percent()
    {
       $rs = ReferralSettings::latest()->get();
       return ($rs->count() < 1) ? $status = '0' : $status = $rs[0]->ref_percent;
    }

    public function switch($id, $status)
    {

        if (!isset($id) || !isset($status)) {
            return response()->json([
                'success' => false,
                'message' => 'Referral settings\' Id and status is required',
            ], 401);
        }
        $switchSettings = ReferralSettings::where('id', $id)->get()[0];
        $status == 'on' ? $currentStatus = 1 : $currentStatus = 0;

        $switchSettings->ref_status = $currentStatus;
        $switchSettings->save();

        return response()->json([
            'success' => true,
            'message' => 'Referral settings was successfully turned to '.$status,
        ], 200);
    }

    public function percentage(Request $dt)
    {
        # code...
        $validate = Validator::make($dt->all(), [
            'percent' => 'required|integer',
            'id' => 'required|integer',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $percentage = ReferralSettings::where('id', $dt->id)->get()[0];
        $percentage->ref_percent = $dt->percent;
        $percentage->save();

        return response()->json([
            'success' => true,
            'message' => 'Referral percentage was successfully set to '.$dt->percent.'%',
        ], 200);
    }
}
