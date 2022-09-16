<?php

namespace App\Http\Controllers\ApiV2\Manager;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FirebasePushNotificationController;
use App\Mail\GeneralTemplateOne;
use App\User;
use App\Verification;
use App\VerificationLimit;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function viewAll()
    {
        $verifications = Verification::with('user')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $verifications,
        ], 200);
    }

    public function usersStages()
    {

        $totalUsers = User::count();
        $unverified = User::where('phone_verified_at', null)->count();
        $unverifiedPercent = round($unverified / $totalUsers * 100, 2);
        $levelOne = User::where('phone_verified_at', '!=', null)->where('address_verified_at', null)->where('idcard_verified_at', null)->count();
        $levelOnePercent = round($levelOne / $totalUsers * 100, 2);
        $levelTwo = User::where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', null)->count();
        $levelTwoPercent = round($levelTwo / $totalUsers * 100, 2);
        $levelThree = User::where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', '!=', null)->count();
        $levelThreePercent = round($levelThree / $totalUsers * 100, 2);
        return response()->json([
            'success' => true,
            'unverified' => $unverified,
            'unverified%' => $unverifiedPercent,
            'levelOne' => $levelOne,
            'levelOne%' => $levelOnePercent,
            'levelTwo' => $levelTwo,
            'levelTwo%' => $levelTwoPercent,
            'levelThree' => $levelThree,
            'levelThree%' => $levelThreePercent,
        ], 200);

    }


    public function verifyUser(Verification $verification, $id)
    {

        if ($verification->type == 'ID Card') {
            $verification->user->id = $id;
            $verification->user->idcard_verified_at = now();
            $level = VerificationLimit::where('level', "3")->first();
            $title = 'LEVEL 3 VERIFICATION SUCCESSFUL';
            $body = "Congrats " . $verification->user->first_name . ", you have successfully completed your Level 3 verification.
            Below is a breakdown of level 3 privileges. <br><br>

            <b style='color:000070'>Identity Verification<br><br>

            Daily withdrawal limit: NGN " . number_format($level->daily_widthdrawal_limit) . "<br><br>

            Monthly withdrawal limit: NGN " . number_format($level->monthly_widthdrawal_limit) . "<br><br>

            Crypto withdrawal limit: " . $level->crypto_widthdrawal_limit . "<br><br>

            Crypto deposit: " . $level->crypto_deposit . "<br><br>

            Transactions: " . $level->transactions . "<br></b>
            ";

            $btn_text = '';
            $btn_url = '';

            $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
            $name = str_replace(' ', '', $name);
            $firstname = ucfirst($name);
            Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

            $title = "Level 3 verification";
            $msg = "Level 3 verification WAS SUCCESSFUL! Your daily and monthly withdrawal limit have been increased to " . number_format($level->daily_widthdrawal_limit) . " and " . number_format($level->monthly_widthdrawal_limit) . " respectively.";

            $fcm_id = $verification->user->fcm_id;
            if (isset($fcm_id)) {
                try {
                    FirebasePushNotificationController::sendPush($fcm_id, $title, $msg);
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }

        } elseif ($verification->type == 'Address') {
            $verification->user->address_verified_at = now();
            $level = VerificationLimit::where('level', "2")->first();
            $title = 'LEVEL 2 VERIFICATION SUCCESSFUL';
            $body = "Congrats " . $verification->user->first_name . ", you have successfully completed your Level 2 verification.
            Below is a breakdown of level 2 privileges. <br><br>

            <b style='color:000070'>Address Verification<br><br>

            Daily withdrawal limit: NGN " . number_format($level->daily_widthdrawal_limit) . "<br><br>

            Monthly withdrawal limit: NGN " . number_format($level->monthly_widthdrawal_limit) . "<br><br>

            Crypto withdrawal limit: " . $level->crypto_widthdrawal_limit . "<br><br>

            Crypto deposit: " . $level->crypto_deposit . "<br><br>

            Transactions: " . $level->transactions . "<br></b>
            ";

            $btn_text = '';
            $btn_url = '';

            $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
            $name = str_replace(' ', '', $name);
            $firstname = ucfirst($name);
            Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

            $title = "Level 2 verification";
            $msg = "Level 2 verification WAS SUCCESSFUL! Your daily and monthly withdrawal limit have been increased to " . number_format($level->daily_widthdrawal_limit) . " and " . number_format($level->monthly_widthdrawal_limit) . " respectively.";

            $fcm_id = $verification->user->fcm_id;
            if (isset($fcm_id)) {
                try {
                    FirebasePushNotificationController::sendPush($fcm_id, $title, $msg);
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }

        $verification->user->save();
        $verification->status = 'success';
        $verification->verified_by = Auth::user()->id;

        $verification->save();

        Notification::create([
            'user_id' => $verification->user->id,
            'title' => 'Verification successful',
            'body' => 'Congratulations, your ' . $verification->type . ' has been verified',
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'User has been verified',
        ], 200);
    }


}
