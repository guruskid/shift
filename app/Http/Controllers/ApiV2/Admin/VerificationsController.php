<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Verification;
use App\VerificationLimit;
use Illuminate\Support\Facades\DB;
use App\Mail\GeneralTemplateOne;
use App\Notification;
use Illuminate\Support\Facades\Mail;

class VerificationsController extends Controller
{
     // junior accountant dashboard

     public function overview(Request $req)
     {

         $totalUsers = User::count();
         $data['unverfied_users'] = $unverified = User::where('phone_verified_at', null)->count();
         $data['level_one_verfied_user'] = $level1 = User::where('phone_verified_at', '!=', null)->where('address_verified_at', null)->where('idcard_verified_at', null)->count();
         $data['level_two_verfied_user'] = $level2 = User::where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', null)->count();
         $data['level_three_verfied_user'] = $level3 =  User::where('phone_verified_at', '!=', null)->where('address_verified_at', '!=', null)->where('idcard_verified_at', '!=', null)->count();
         $data['level_one_verfied_user_percent'] =  round($level1 / $totalUsers * 100, 2);
         $data['level_two_verfied_user_percent'] = round($level2 / $totalUsers * 100, 2);
         $data['level_three_verfied_user_percent'] = round($level3 / $totalUsers * 100, 2);
         $data['unverfied_user_percent'] = round($unverified / $totalUsers * 100, 2);

        $verfications = Verification::whereHas('user');
        if ($req->year && $req->month) {
            $verfications = $verfications->whereYear('created_at', $req->year)->whereMonth('created_at', $req->month);
        }

        if($req->level){
            $verfications = $verfications->where('type', $req->level);
        }
         $data['users'] = $verfications->with(
            ['user' => function ($query) {
            $query->select('id','first_name', "last_name", 'username', 'created_at AS signup_date');
        },
        'verifiedUserBy' => function ($query) {
            $query->select('id', 'first_name', 'last_name');
        }],
        )->select('user_id', 'verified_by', "created_at AS verification_date", 'status', 'type')->orderBy('id', 'desc')->paginate(25);



         return response()->json([
             'success' => true,
             'data' => $data,
         ], 200);
     }


     public function approveVerification(Verification $verification){
        DB::beginTransaction();
            try {

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
                }

                $verification->user->save();
                $verification->status = 'success';
                $verification->save();

                Notification::create([
                    'user_id' => $verification->user->id,
                    'title' => 'Verification successful',
                    'body' => 'Congratulations, your ' . $verification->type . ' has been verified',
                ]);




                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'User verified',
                ],200);
            } catch (\Exception $th) {
                //throw $th;
                DB::rollback();
                return response([
                    'message' => $th->getMessage(),
                    'success' => 'false'
                ], 400);

            }

     }


     public function declineVerification(Request $dt, Verification $verification){
        DB::beginTransaction();
        try {



            if ($dt->type == 'Address') {
                $title = "LEVEL 2 VERIFICATION DENIED";
                $bodyTitle = 'level 2 verification';
                if ($dt->reason == 'Uploaded a wrong information') {
                    $dt->reason = 'You uploaded a wrong information';
                    $suggestion = 'You are required to upload your bank statement that contains both your home address and name on Dantown.
                    ';
                } elseif ($dt->reason == 'Unclear uploaded document') {
                    $dt->reason = 'The document you uploaded is not unclear';
                    $suggestion = 'Please upload a clear image of your bank statement where your name and home address is clearly visible.
                    ';
                } elseif ($dt->reason == 'Full image of the document was not uploaded') {
                    $dt->reason = 'The full image of the document was not uploaded';
                    $suggestion = 'The image of the bank statement uploaded has some missing data. Please upload the full image of the statement.
                    ';
                } elseif ($dt->reason == 'A mismatch of information') {
                    $dt->reason = 'There is a mismatch of information';
                    $suggestion = 'Please ensure that the address you filled matches that on the bank statement you uploaded.
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
            This is because: <br><b>" . $dt->reason . "</b> <br><br><b>" . $suggestion . "</b><br><br>

            Please send an email to <a style='text-decoration:none' href='mailto:support@godantown.com'>support@godantown.com</a> if you have questions or complaints";
            $name = ($verification->user->first_name == " ") ? $verification->user->username : $verification->user->first_name;
            $name = str_replace(' ', '', $name);
            $firstname = ucfirst($name);

            $btn_text = '';
            $btn_url = '';

            // dd($paragraph);

            Mail::to($verification->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

            $verification->status = 'failed';
            $verification->save();

            Notification::create([
                'user_id' => $verification->user->id,
                'title' => 'Verification failed',
                'body' => 'Sorry, your ' . $verification->type . ' could not be verified. Please check the document and try again',
            ]);


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User verification cancelled',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' =>  $th->getMessage()
            ], 400);

        }
     }
}
