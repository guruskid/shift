<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\GeneralTemplateOne;
use App\NairaTrade;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\User;
use App\Verification;
use App\VerificationLimit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function addAdmin(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'department' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'username' => 'required',
            'phone' => 'required|integer',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        if (User::where('email', $r->email)->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exists',
            ], 401);
        }

        if ($r->department == 'Junior_Accountant') {
            $role = 777;
        } elseif ($r->department == 'Senior_Accountant') {
            $role = 889;
        } elseif ($r->department == 'Customer_care') {
            $role = 333;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid department, please select from the list below: Junior_Accountant, Senior_Accountant, Customer_care',
            ], 401);
        }

        $user = new User();
        $user->first_name = $r->first_name;
        $user->last_name = $r->last_name;
        $user->email = $r->email;
        $user->password = Hash::make($r->password);
        $user->role = $role;
        $user->username = $r->username;
        $user->phone = $r->phone;
        $user->email_verified_at = now();
        $user->status = 'active';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully',
        ], 200);
    }

    public function totalUserBalance()
    {
        $total_user_balance = NairaWallet::sum('amount');
        return response()->json([
            'success' => true,
            'total_user_balance' => $total_user_balance,
        ], 200);
    }



    public function action(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'action' => 'required',
            'user_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $user = User::find($r->user_id);

        if ($r->action == 'activate') {
            $user->status = 'active';
        } elseif ($r->action == 'deactivate') {
            $user->status = 'not verified';
        } elseif ($r->action == 'delete') {
            $user->delete();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid action, please select from the list below: activate, deactivate, delete',
            ], 401);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
        ], 200);
    }

    public function customerHappiness()
    {
        return response()->json([
            'success' => true,
            'customer_happiness' => User::where('role', 555)->latest()->get(),
        ]);
    }

    public function accountant()
    {
        return response()->json([
            'success' => true,
            'accountant' => User::where('role', [889, 777])->latest()->get(),
        ]);
    }

    public function allVerification()
    {
        $verifications = Verification::where('status', 'waiting')->latest()->get();

        return response()->json([
            'success' => true,
            'verifications' => $verifications,
        ]);
    }

    public function verificationByPercentage()
    {
        # code...
        $totalUsers = User::count();

        $now = Carbon::now();

        $weeklyLevelOneVerifiedUsers = User::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'), //This will return date in format like this: 2022-01-10
            $now->endOfWeek()->format('Y-m-d')
         ])->where('phone_verified_at', '!=', NULL)->count();
        $monthlyLevelOneVerifiedUsers = User::where('phone_verified_at', '!=', NULL)->WhereYear('created_at', date('Y'))->WhereMonth('created_at', date('m'))->count();
        $yearlyLevelOneVerifiedUsers = User::where('phone_verified_at', '!=', NULL)->WhereYear('created_at', date('Y'))->count();


        $weeklyLevelTwoVerifiedUsers = User::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'), //This will return date in format like this: 2022-01-10
            $now->endOfWeek()->format('Y-m-d')
         ])->where('address_verified_at', '!=', NULL)->count();
        $monthlyLevelTwoVerifiedUsers = User::where('address_verified_at', '!=', NULL)->WhereYear('created_at', date('Y'))->WhereMonth('created_at', date('m'))->count();
        $yearlyLevelTwoVerifiedUsers = User::where('address_verified_at', '!=', NULL)->WhereYear('created_at', date('Y'))->count();



        $weeklyLevelThreeVerifiedUsers = User::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'), //This will return date in format like this: 2022-01-10
            $now->endOfWeek()->format('Y-m-d')
         ])->where('idcard_verified_at', '!=', NULL)->count();
        $monthlyLevelThreeVerifiedUsers = User::where('idcard_verified_at', '!=', NULL)->WhereYear('created_at', date('Y'))->WhereMonth('created_at', date('m'))->count();
        $yearlyLevelThreeVerifiedUsers = User::where('idcard_verified_at', '!=', NULL)->WhereYear('created_at', date('Y'))->count();

        // return $levelOneVerifiedPercentage = ($monthlyLevelOneVerifiedUsers / $totalUsers) * 100;

        return response()->json([
            'success' => true,

            'Verification_percent' => [
                'levelOne' => [
                    'weekly' => $weeklyLevelOneVerifiedUsers / $totalUsers * 100,
                    'monthly' => $monthlyLevelOneVerifiedUsers / $totalUsers * 100,
                    'Yearly' => $yearlyLevelOneVerifiedUsers / $totalUsers * 100,
                ],
                'levelTwo' => [
                    'weekly' => $weeklyLevelTwoVerifiedUsers / $totalUsers * 100,
                    'monthly' => $monthlyLevelTwoVerifiedUsers / $totalUsers * 100,
                    'Yearly' => $yearlyLevelTwoVerifiedUsers / $totalUsers * 100,
                ],
                'levelThree' => [
                    'weekly' => $weeklyLevelThreeVerifiedUsers / $totalUsers * 100,
                    'monthly' => $monthlyLevelThreeVerifiedUsers / $totalUsers * 100,
                    'Yearly' => $yearlyLevelThreeVerifiedUsers / $totalUsers * 100,
                ]
            ],
            'unverified_user' => [
                'levelTwo' => Verification::where('type', 'Address')->where('status', 'Waiting')->count(),
                'levelThree' => Verification::where('type', 'ID Card')->where('status', 'Waiting')->count(),
            ]

        ]);
    }

    public function verifyUser(Verification $verification)
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

        return response()->json([
            'success' => true,
            'message' => 'User verified',
        ]);

        // back()->with(['success' => 'User verified']);


    }


    public function cancelVerification(Request $dt, Verification $verification)
    {
        // dd($verification->user->email);


        // dd($dt->reason);
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

        return response()->json([
            'success' => true,
            'message' => 'User verification cancelled',
        ]);
    }
}
