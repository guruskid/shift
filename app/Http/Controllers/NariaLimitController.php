<?php

namespace App\Http\Controllers;

use App\VerificationLimit;

class NariaLimitController extends Controller
{
    public static function nariaLimit($userDetails)
    {
        $v_progress = 0;
        if ($userDetails->email_verified_at) {
            $v_progress += 25;
        }
        if ($userDetails->phone_verified_at) {
            $v_progress += 25;
        }
        if ($userDetails->address_verified_at) {
            $v_progress += 25;
        }
        if ($userDetails->idcard_verified_at) {
            $v_progress += 25;
        }

        $userDetails->v_progress = $v_progress;

        switch ($v_progress) {
            case 25:
                $userDetails->daily_max = 0;
                $userDetails->monthly_max = 0;
                $userDetails->save();
                break;

            case 50:
                $level1 = VerificationLimit::where('level', 1)->first();
                $userDetails->daily_max = $level1->daily_widthdrawal_limit;
                $userDetails->monthly_max = $level1->monthly_widthdrawal_limit;
                $userDetails->save();
                break;

            case 75:
                $level2 = VerificationLimit::where('level', 2)->first();
                $userDetails->daily_max = $level2->daily_widthdrawal_limit;
                $userDetails->monthly_max = $level2->monthly_widthdrawal_limit;
                $userDetails->save();
                break;

            case 100:
                $level3 = VerificationLimit::where('level', 3)->first();
                $userDetails->daily_max = $level3->daily_widthdrawal_limit;
                $userDetails->monthly_max = $level3->monthly_widthdrawal_limit;
                $userDetails->save();
                break;

            default:
                $userDetails->daily_max = 0;
                $userDetails->monthly_max = 0;
                $userDetails->save();
                break;
        }

        $userDetails->save();

        return 'true';
    }
}
