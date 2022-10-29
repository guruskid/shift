<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationLimit;

class VerificationLimitController extends Controller
{
    public function index()
    {
        $levelOne = VerificationLimit::where('level', '=', 1)->get();
        $levelTwo = VerificationLimit::where('level', 2)->get();
        $levelThree = VerificationLimit::where('level', 3)->get();
        // dd($levelThree);
        return view('admin.verification_limit', compact('levelOne', 'levelTwo', 'levelThree'));
    }

    public function addLimit(Request $dt)
    {
        $dt->validate([
            'level' => 'required',
            'daily_widthdrawal_limit' => 'required',
            'monthly_widthdrawal_limit'=> 'required',
            'crypto_widthdrawal_limit'=> 'required',
            'crypto_deposit'=> 'required',
            'transactions'=> 'required',
        ]);

        $level = VerificationLimit::where('level', $dt->level);

        // dd($dt->level);

        if($level->count() < 1){
            // dd('new');
           $vLimit =  new VerificationLimit();
           $vLimit->level = $dt->level;
           $vLimit->daily_widthdrawal_limit = $dt->daily_widthdrawal_limit;
           $vLimit->monthly_widthdrawal_limit = $dt->monthly_widthdrawal_limit;
           $vLimit->crypto_widthdrawal_limit = $dt->crypto_widthdrawal_limit;
           $vLimit->crypto_deposit = $dt->crypto_deposit;
           $vLimit->transactions = $dt->transactions;
           $vLimit->save();
           return redirect()->back()->with('success', 'Verification limit created successfully');
        }else{
            $level = VerificationLimit::where('level', $dt->level)->get();
            // die($level);
            $level[0]->daily_widthdrawal_limit = $dt->daily_widthdrawal_limit;
            $level[0]->monthly_widthdrawal_limit = $dt->monthly_widthdrawal_limit;
            $level[0]->crypto_widthdrawal_limit = $dt->crypto_widthdrawal_limit;
            $level[0]->crypto_deposit = $dt->crypto_deposit;
            $level[0]->transactions = $dt->transactions;
            $level[0]->save();
            return redirect()->back()->with('success', 'Verification limit updated successfully');
        }

    }

    public function get()
    {
        return response()->json([
            "success" => true,
            "data" => VerificationLimit::get()
        ]);
    }
}
