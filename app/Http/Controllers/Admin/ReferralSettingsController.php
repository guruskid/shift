<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\ReferralSettings;
use Illuminate\Support\Facades\Auth;

class ReferralSettingsController extends Controller
{
    public function index()
    {
        $referralSettings = ReferralSettings::latest()->get();
        return view('admin.referral', compact('referralSettings'));
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
}
