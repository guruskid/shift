<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NariaLimitController;
use App\Mail\GeneralTemplateOne;
use App\Notification;
use App\User;
use App\Verification;
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

    public function verify(Verification $verification)
    {

        if ($verification->type == 'ID Card') {
            $verification->user->idcard_verified_at = now();
            NariaLimitController::nariaLimit($verification->user);
            $title = 'LEVEL 3 VERIFICATION SUCCESSFUL';
            $body = "Congrats " . $verification->user->first_name . ", you have successfully completed your Level 3 verification.
            Below is a breakdown of level 3 privileges. <br><br>

            <b style='color:000070'>Identity Verification<br><br>

            Daily withdrawal limit: NGN ".number_format($verification->user->daily_max)."<br><br>

            Monthly withdrawal limit: NGN ".number_format($verification->user->monthly_max)."<br><br>

            Crypto withdrawal limit: unlimited<br><br>

            Crypto deposit: Unlimited<br><br>

            Transactions: Unlimited<br></b>
            ";

            $btn_text = '';
            $btn_url = '';

            $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
            $name = explode(' ', $name);
            $firstname = ucfirst($name[0]);
            Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
        } elseif ($verification->type == 'Address') {
            $verification->user->address_verified_at = now();
            NariaLimitController::nariaLimit($verification->user);
            $title = 'LEVEL 2 VERIFICATION SUCCESSFUL';
            $body = "Congrats " . $verification->user->first_name . ", you have successfully completed your Level 2 verification.
            Below is a breakdown of level 2 privileges. <br><br>

            <b style='color:000070'>Address Verification<br><br>

            Daily withdrawal limit: NGN ".number_format($verification->user->daily_max)."<br><br>

            Monthly withdrawal limit: NGN ".number_format($verification->user->monthly_max)."<br><br>

            Crypto withdrawal limit: unlimited<br><br>

            Crypto deposit: Unlimited<br><br>

            Transactions: Unlimited<br></b>
            ";

            $btn_text = '';
            $btn_url = '';

            $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
            $name = explode(' ', $name);
            $firstname = ucfirst($name[0]);
            Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
        }

        $verification->user->save();
        $verification->status = 'success';
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
            $title = "LEVEL 2 VERIFICATION DENIED";
            $bodyTitle = 'level 2 verification';
            if ($dt->reason == 'Uploaded a wrong information') {
                $dt->reason = 'You uploaded a wrong information';
                $suggestion = 'You are required to upload your bank statement that contains your name on Dantown.
                ';
            } elseif ($dt->reason == 'Unclear uploaded document') {
                $dt->reason = 'The document you uploaded is not unclear';
                $suggestion = 'Please upload a clear image of your bank statement where your name and home address is clearly visible.
                ';
            } elseif ($dt->reason == 'Full image of the document was not uploaded') {
                $dt->reason = 'The full image of the document was not uploaded';
                $suggestion = 'The image of your bank statement you uploaded has some missing data. Please upload the full image of the statement.
                ';
            } elseif ($dt->reason == 'A mismatch of information') {
                $dt->reason = 'There is a mismatch of information';
                $suggestion = 'Please ensure that the address you inputted is similar with the address on the bank statement you uploaded.
                ';
            }
        } else {
            $title = "LEVEL 3 VERIFICATION DENIED";
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
            } elseif ($dt->reason == 'Unclear uploaded document') {
                
                $dt->reason = 'The document you uploaded is not unclear';
                $suggestion = 'Please  upload a clear image of the required document that clearly shows your name and other relevant information.
                ';
            } elseif ($dt->reason == 'Full image of the document was not uploaded') {
                
                $dt->reason = 'The full image of the document was not uploaded';
                $suggestion = 'The image of the document you uploaded has some data missing. Please upload a full image of the document.
                ';
            } elseif ($dt->reason == 'A mismatch of information') {
                
                $dt->reason = 'There is a mismatch of information';
                $suggestion = 'Please upload a document that contains your name on Dantown.
                ';
            }
        }

        $body = "We cannot proceed with your " . $bodyTitle . ".<br><br>
        This is because: <br><b>" . $dt->reason . "</b> <br><br><b>" . $suggestion."</b><br><br>
        
        Please send an email to <a style='text-decoration:none' href='mailto:support@godantown.com'>support@godantown.com</a> if you have questions or complaints";
        $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
        $name = explode(' ', $name);
        $firstname = ucfirst($name[0]);

        $btn_text = '';
        $btn_url = '';

        // dd($paragraph);

        Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
        // return back()->with(['success' => 'User verification cancelled']);

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
