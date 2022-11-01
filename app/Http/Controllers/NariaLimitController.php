<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
                $userDetails->daily_max = 500000;
                $userDetails->monthly_max = 5000000;
                $userDetails->save();
                break;

            case 75:
                $userDetails->daily_max = 2000000;
                $userDetails->monthly_max = 60000000;
                $userDetails->save();
                break;

            case 100:
                $userDetails->daily_max = 10000000;
                $userDetails->monthly_max = 99000000;
                $userDetails->save();
                break;

            default:
                $userDetails->daily_max = 30000;
                $userDetails->monthly_max = 300000;
                $userDetails->save();
                break;
        }

        $userDetails->save();

        return 'true';
    }
}
