<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FirebasePushNotificationController;
use App\Http\Controllers\NariaLimitController;
use App\Mail\GeneralTemplateOne;
use App\Notification;
use App\User;
use App\Verification;
use App\VerificationLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function freezeAccount(Request $r)
    {
        if (!Hash::check($r->pin, Auth::user()->nairaWallet->password)) {
            return back()->with(['error' => 'Wallet pin doesnt match']);
        }
        $user_wallet = User::find($r->user_id)->nairaWallet;
        $user_wallet->status = 'freezeAccount';
        $user_wallet->save();

        $user = User::find($r->user_id);
        $user->status = 'not verified';
        $user->save();

        return back()->with(['success' => 'Account frozen successfully']);
    }

    public function activateAccount(Request $r)
    {
        if (!Hash::check($r->pin, Auth::user()->nairaWallet->password)) {
            return back()->with(['error' => 'Wallet pin doesnt match']);
        }
        $user_wallet = User::find($r->user_id)->nairaWallet;
        $user_wallet->status = 'active';
        $user_wallet->save();
        $user = User::find($r->user_id);
        $user->status = 'active';
        $user->save();
        return back()->with(['success' => 'Account activated successfully']);
    }

    public function verifications()
    {
        $verifications = Verification::where('status', 'waiting')->latest()->get();

        return view('admin.user_verifications', compact('verifications'));
    }

    public function verificationHistory()
    {
        $verifications = Verification::where('status', '!=', 'waiting')->latest()->paginate(50);

        return view('admin.verification_history', compact('verifications'));
    }

    public function verify(Verification $verification)
    {

        if ($verification->type == 'ID Card') {
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

        return back()->with(['success' => 'User verified']);
    }

    public function cancelVerification(Verification $verification, Request $dt)
    {
        // dd($verification->user->email);
        if ($dt->type == 'Address') {
            $title = "LEVEL 2 VERIFICATION FAILED";
            $bodyTitle = 'level 2 verification';
            $fcmNotice = "";
            if ($dt->reason == 'Uploaded a wrong information') {
                $dt->reason = 'You uploaded a wrong information';
                $suggestion = 'You are required to upload your bank statement that contains both your home address and name on Dantown.
                ';
                $fcmNotice = "Upload a bank statement that contains your name and the address inputted.";
            } elseif ($dt->reason == 'Unclear uploaded document') {
                $dt->reason = 'The document you uploaded is not unclear';
                $suggestion = 'Please upload a clear image of your bank statement where your name and home address is clearly visible.
                ';
                $fcmNotice = "Upload a visible image of your bank statement.";
            } elseif ($dt->reason == 'Full image of the document was not uploaded') {
                $dt->reason = 'The full image of the document was not uploaded';
                $suggestion = 'The image of the bank statement uploaded has some missing data. Please upload the full image of the statement.
                ';
                $fcmNotice = "Upload the full image of the bank statement, where the required data are visible.";
            } elseif ($dt->reason == 'A mismatch of information') {
                $dt->reason = 'There is a mismatch of information';
                $suggestion = 'Please ensure that the address you filled matches that on the bank statement you uploaded.
                ';
                $fcmNotice = "Ensure the address inputted and that on the bank statement are similar.";
            }
        } else {
            $title = "LEVEL 3 VERIFICATION FAILED";
            $bodyTitle = 'level 3 verification';
            if ($dt->reason == 'Uploaded a wrong information') {
                $dt->reason = 'You uploaded a wrong information';
                $suggestion =
                    'Please upload any national approved identity       verification document with your name.<br>
                        IDs accepted are; <br>
                        National identity card, <br>
                        NIMC slip, <br>
                        International Passport, <br>
                        Permanent Voter’s card, <br>
                        Driver’s license.<br>
                ';
                $fcmNotice = "Upload an authorized form of identification that contains your name.";
            } elseif ($dt->reason == 'Unclear uploaded document') {

                $dt->reason = 'The document you uploaded is not unclear';
                $suggestion = 'Please  upload a clear image of the required document that clearly shows your name and other relevant information.
                ';
                $fcmNotice = "Upload a visible image of the means of identification.";
            } elseif ($dt->reason == 'Full image of the document was not uploaded') {

                $dt->reason = 'The full image of the document was not uploaded';
                $suggestion = 'The image of the document you uploaded has some data missing. Please upload a full image of the document.
                ';
                $fcmNotice = "Upload the full image of the means of identification, where the required data are visible.";
            } elseif ($dt->reason == 'A mismatch of information') {

                $dt->reason = 'There is a mismatch of information';
                $suggestion = 'Please upload a document that contains your name on Dantown.
                ';
                $fcmNotice = "Please ensure your name matches that on the means of identification.";
            }
        }

        $body = "We cannot proceed with your " . $bodyTitle . ".<br><br>
        This is because: <br><b>" . $dt->reason . "</b> <br><br><b>" . $suggestion . "</b><br><br>

        Please send an email to <a style='text-decoration:none' href='mailto:support@godantown.com'>support@godantown.com</a> if you have questions or complaints";
        $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);

        $btn_text = '';
        $btn_url = '';

        // dd($paragraph);

        Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
        // return back()->with(['success' => 'User verification cancelled']);

        $msg = "Your " . $bodyTitle . " was declined because " . $dt->reason . ". Kindly " . $fcmNotice . ".";
        $fcm_id = $verification->user->fcm_id;
        
        if (!empty($fcm_id)) {
            try {
                FirebasePushNotificationController::sendPush($fcm_id, $title, $msg);
            } catch (\Throwable $th) {
                // throw $th;
            }
        }

        $verification->status = 'failed';
        $verification->save();

        Notification::create([
            'user_id' => $verification->user->id,
            'title' => 'Verification failed',
            'body' => 'Sorry, your ' . $verification->type . ' could not be verified. Please check the document and try again',
        ]);

        return back()->with(['success' => 'User verification cancelled']);
    }
}
