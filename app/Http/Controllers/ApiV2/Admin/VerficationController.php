<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationLimit;

class VerificationController extends Controller
{
    public function verificationHelper($user)
    {
        $verificationLevel = 'not Verified';

        if($user->phone_verified_at != null AND $user->address_verified_at == null AND $user->idcard_verified_at == null)
        {
            $verificationLevel = 'Level 1';
        }
        if($user->phone_verified_at != null AND $user->address_verified_at != null AND $user->idcard_verified_at == null)
        {
            $verificationLevel = 'Level 2';
        }
        if($user->phone_verified_at != null AND $user->address_verified_at != null AND $user->idcard_verified_at != null)
        {
            $verificationLevel = 'Level 3';
        }

        return $verificationLevel;
    }

    public function maximumLevelMonthlyWithdrawal($verificationHelperData)
    {
        $levelNo = 0;
        $levelMonthlyWithdrawalLimit = 0;

        switch ($verificationHelperData) {
            case 'Level 1':
                $levelNo = 1;
                break;
            case 'Level 2':
                $levelNo = 2;
                break;
            case 'Level 3':
                $levelNo = 3;
                break;
            
            default:
                $levelNo = 0;
                break;
        }
        if($levelNo != 0) {
            $levelMonthlyWithdrawalLimit = VerificationLimit::where('level', $levelNo)->first()->monthly_widthdrawal_limit;
        }
        
        return $levelMonthlyWithdrawalLimit;
    }
}
