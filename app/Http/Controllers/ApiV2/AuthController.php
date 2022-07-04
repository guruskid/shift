<?php

namespace App\Http\Controllers\ApiV2;

use App\Country;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\GeneralTemplateOne;
use App\Mail\VerificationCodeMail;
use App\NairaWallet;
use App\Notification;
use App\User;
use App\UserTracking;
use App\VerificationCode;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('appToken')->accessToken;
            //After successfull authentication, notice how I return json parameters
            \Artisan::call('naira:limit');

            return response()->json([
                'success' => true,
                'token' => $success,
                'user' => $user,
                'naira_wallet' => $user->nairaWallet,
                'bank_accounts' => $user->accounts,
            ]);
        } else {
            //if authentication is unsuccessfull, notice how I return json parameters
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }
    }

    public function checkForgotPasswordEmail(Request $r)
    {


        $validator = Validator::make($r->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $userCount = User::where('email', $r->email)->count();
        $user = User::where('email', $r->email)->get();

        if($user[0]->role == 999 || $user[0]->role == 998 || $user[0]->role == 889){
            return response()->json([
                'success' => false,
                'message' => 'This account is not allowed to change password',
            ], 401);
        }

        if($userCount < 1){
            return response()->json([
                'success' => false,
                'message' => 'Email does not exist',
            ], 401);
        }
        // die($user[0]->email);
        $this->verificationCodeEmail($user[0]->email, $user[0]->id);

        return response()->json([
            'success' => true,
            'message' => 'Kindly check your email for your verification',
        ], 401);
    }


    // checkForgotPasswordOtp


    // public function forgotPassword(Request $r)
    // {
    //     $validator = Validator::make($r->all(), [
    //         'new_password' => 'required|string|min:6'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $validator->errors(),
    //         ], 401);
    //     }

    //     if (Hash::check($r->old_password, Auth::user()->password) == false) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Your current password does not match with the password you provided. Please try again.',
    //         ]);
    //     }

    //     $user = Auth::user();
    //     $user->password = Hash::make($r->new_password);
    //     $user->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => $user,
    //     ]);
    // }


    public function checkForgotPasswordOtp(Request $r)
    {
        $user = User::where('email', $r->email)->get();
        $validator = Validator::make($r->all(), [
            'otp_code' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $checkOtp = VerificationCode::where('verification_code', $r->otp_code)->where('user_id', $user[0]->id);
        $countOtp = VerificationCode::where('verification_code', $r->otp_code)->where('user_id', $user[0]->id)->count();
        if ($countOtp <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset password code'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code verification was successful'
        ]);
    }

    public function changePassword(Request $r)
    {
        $user = User::where('email', $r->email)->get();
        $validator = Validator::make($r->all(), [
            'email' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if($user[0]->role == 999 || $user[0]->role == 998 || $user[0]->role == 889){
            return response()->json([
                'success' => false,
                'message' => 'This account is not allowed to change password',
            ], 401);
        }

        $user = User::where('email', $r->email)->get();
        $user = $user[0];
        $user->password = Hash::make($r->password);
        $user->save();

        return response()->json([
            'success' => true,
            'data' =>   'Password reset was successful',
        ]);
    }

    public function updatePassword(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|different:old_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (Hash::check($r->old_password, Auth::user()->password) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Old password does not match with the password you provided. Please try again.',
            ]);
        }

        $user = Auth::user();

        if($user->role == 999 || $user->role == 998 || $user->role == 889){
            return response()->json([
                'success' => false,
                'message' => 'This account is not allowed to change password',
            ], 401);
        }


        $user->password = Hash::make($r->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function verificationCodeEmail($email, $userId)
    {
        $otpCode = rand(1000, 9999);


        VerificationCode::create([
            'user_id' => $userId,
            'verification_code' => $otpCode
        ]);
        $title = 'Email Verification Code';
        $body = 'is your verification code. This code expires after 30 minutes';
        $btn_text = '';
        $btn_url = '';
        Mail::to($email)->send(new VerificationCodeMail($otpCode, $title, $body, $btn_text, $btn_url));
    }


    public function register(Request $request)
    {

        // if(isset($refcode)){
        //     return($refcode);
        // }

        $rc = $request->refcode ?? NULL;

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required',
            // 'first_name' => 'string|required',
            // 'last_name' => 'string|required',
            'username' => 'string|required|unique:users,username',
            'country_id' => 'required|integer',
            'phone' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $input = $request->all();
        $username = \Str::lower($input['username']);
        $external_id = $username . '-' . uniqid();

        $data = [
            'first_name' => ' ',
            'last_name' => ' ',
            'phone' => $input['phone'],
            'username' => $input['username'],
            'country_id' => $input['country_id'],
            'email' => $input['email'],
            'external_id' => $external_id,
            'status' => 'active',
            'referrer' => $rc,
            'password' => Hash::make($input['password']),
            'platform' => $input['platform']
        ];

        if (isset($input['referral_code'])) {
            $ref = User::where('referral_code',$input['referral_code'])->count();
            if ($ref > 0) {
                $data['referred'] = 1;
                $data['referrer'] = $input['referral_code'];
            }
        }

        $user = User::create($data);

        // $user->sendEmailVerificationNotification();

        $auth_user = User::find($user->id);
        $success['token'] = $user->createToken('appToken')->accessToken;
        $password = '';

        UserTracking::create([
            'user_id' =>$user->id,
            'Current_Cycle' => "Active"
        ]);

        NairaWallet::create([
            'user_id' => $user->id,
            'account_number' => time(),
            'account_name' => $username,
            'bank_name' => 'Dantown',
            'bank_code' => '000000',
            'amount' => 0,
            'password' => $password,
            'amount_control' => 'VARIABLE',
        ]);

        $btc_hd = HdWallet::where('currency_id', 1)->first();
        $btc_xpub = $btc_hd->xpub;



        $client = new Client();
        $url = env('TATUM_URL') . "/ledger/account/batch";

        /* try { */
        $response = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "accounts" => [
                    [
                        "currency" => "BTC",
                        "xpub" => $btc_xpub,
                        "customer" => [
                            "accountingCurrency" => "USD",
                            "customerCountry" => "NG",
                            "externalId" => $external_id,
                            "providerCountry" => "NG"
                        ],
                        "compliant" => false,
                        "accountingCurrency" => "USD"
                    ]
                ]
            ],
        ]);

        $body = json_decode($response->getBody());


        $btc_account_id = $body[0]->id;
        $user->customer_id = $body[0]->customerId;
        $user->save();

        $address_url = env('TATUM_URL') . "/offchain/account/address/batch";
        $res = $client->request('POST', $address_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "addresses" => [
                    ["accountId" => $btc_account_id]
                ]
            ],
        ]);

        $address_body = json_decode($res->getBody());

        $user->btcWallet()->create([
            'account_id' => $btc_account_id,
            'name' => $user->username,
            'currency_id' => 1,
            'address' => $address_body[0]->address,
        ]);

        $title = 'Bitcoin Wallet created';
        $msg_body = 'Congratulations your Dantown Bitcoin Wallet has been created successfully, you can now send, receive, buy and sell Bitcoins in the wallet. ';
        $not = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $msg_body,
        ]);

        $this->verificationCodeEmail($user->email, $user->id);

        $name = $user->first_name == " " ? $user->username : $user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);

        $title = 'Welcome to Dantown,';
        $body = 'Congratulations ' . $firstname . ' on signing up on Dantown.<br>
        We are excited to share this journey with you. A new world of boundless possibilities where you can trade cryptocurrencies and gift-cards with ease.
        <br><br>

        If you have enquiries about our products or issues regarding your account, kindly contact our customer happiness team via support@godantown.com.<br>
        ';

        $btn_text = '';
        $btn_url = '';

        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));


        return response()->json([
            'success' => true,
            'token' => $success,
            'user' => $auth_user
        ]);
    }

    public function emailVerification(Request $otp)
    {
        $user = Auth::user();
        $validator = Validator::make($otp->all(), [
            'otp_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $checkOtp = VerificationCode::where('verification_code', $otp->otp_code)->where('user_id',$user->id);
        $countOtp = VerificationCode::where('verification_code', $otp->otp_code)->where('user_id',$user->id)->count();
        if($countOtp <= 0 ){
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ]);
        }


        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Your email verification was successful'
        ]);
    }

    public function resendCode()
    {
        $user = Auth::user();
        $this->verificationCodeEmail($user->email, $user->id);
        return response()->json([
            'success' => true,
            'message' => 'Email verification was sent successfully'
        ]);
    }


    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|unique:users,phone',
            'country_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $client = new Client();
        $url = env('TERMII_SMS_URL') . "/otp/send";
        $country = Country::find($request->country_id);
        $full_num = $country->phonecode . $request->phone;

        // dd($url);

            $response = $client->request('POST', $url, [
                'json' => [
                    'api_key' => env('TERMII_API_KEY'),
                    "message_type" => "NUMERIC",
                    "to" => $full_num,
                    "from" => "N-Alert",
                    "channel" => "dnd",
                    "pin_attempts" => 4,
                    "pin_time_to_live" =>  10,
                    "pin_length" => 6,
                    "pin_placeholder" => "< 1234 >",
                    "message_text" => "Your Dantown verification pin is < 1234 > This pin will be invalid after 10 minutes",
                    "pin_type" => "NUMERIC"
                ],
            ]);
        $body = json_decode($response->getBody()->getContents());

        if ($body->status == 200) {

            Auth::user()->country_id = $request->country_id;
            Auth::user()->phone_pin_id = $body->pinId;
            Auth::user()->save();

            return response()->json([
                'success' => true,
            ]);
        }
        return response()->json([
            'msg' => 'An error occured while resending OTP, please try again'
        ]);
    }

    public function verifyPhone(Request $r)
    {

        $data = Validator::make($r->all(), [
            'phone' => 'required|unique:users,phone',
            'otp' => 'required',
        ]);
        if ($data->fails()) {
            return response()->json([
                'success' => false,
                'message' => $data->errors(),
            ], 401);
        }


        try {
            $client = new Client();
            $url = env('TERMII_SMS_URL') . "/otp/verify";

            $response = $client->request('POST', $url, [
                'json' => [
                    'api_key' => env('TERMII_API_KEY'),
                    "pin_id" => Auth::user()->phone_pin_id,
                    "pin" => $r->otp
                ],
            ]);

            $body = json_decode($response->getBody()->getContents());
            if (!$body->verified || $body->verified != 'true') {
                return response()->json([
                    'success' => false,
                    'msg' => 'Phone verification failed. Please request for a new OTP'
                ]);
            }
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'msg' => 'Phone verification failed. Please request for a new OTP'
            ]);
        }

        Auth::user()->phone = $r->phone;
        Auth::user()->phone_verified_at = now();
        Auth::user()->save();
        \Artisan::call('naira:limit');

        return response()->json([
            'success' => true,
            'data' => Auth::user()
        ]);
    }


    public function resendOtp()
    {
        //  Auth::user()->email;
        $country = Country::find(Auth::user()->country_id);
        $phone = Auth::user()->phone;
        $full_num = $country->phonecode . $phone;

        $client = new Client();
        $url = env('TERMII_SMS_URL') . "/otp/send";

        // dd($url);

        $response = $client->request('POST', $url, [
            'json' => [
                'api_key' => env('TERMII_API_KEY'),
                "message_type" => "NUMERIC",
                "to" => $full_num,
                "from" => "N-Alert",
                "channel" => "dnd",
                "pin_attempts" => 4,
                "pin_time_to_live" =>  10,
                "pin_length" => 6,
                "pin_placeholder" => "< 1234 >",
                "message_text" => "Your Dantown confirmation code is < 1234 >, valid for 10 minutes, one-time use only",
                "pin_type" => "NUMERIC"
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());

        if ($body->status == 200) {
            Auth::user()->phone_pin_id = $body->pinId;
            Auth::user()->save();

            return response()->json([
                'success' => true,
            ]);
        }
        return response()->json([
            'success' => false,
            'msg' => 'An error occured while resending OTP, please try again'
        ]);
    }
}
