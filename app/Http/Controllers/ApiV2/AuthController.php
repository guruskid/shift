<?php

namespace App\Http\Controllers\ApiV2;

use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\NairaWallet;
use App\Notification;
use App\User;
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

    public function verificationCodeEmail($email, $userId)
    {
        $otpCode = rand(1000, 9999);
        VerificationCode::create([
            'user_id' => $userId,
            'verification_code' => $otpCode
        ]);
        $title = 'Email Verification Code2';
        $body = 'is your verification code, valid for 5 minutes. to keep your account safe, do not share this code with anyone.';
        $btn_text = '';
        $btn_url = '';
        Mail::to($email)->send(new VerificationCodeMail($otpCode, $title, $body, $btn_text, $btn_url));
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required',
            // 'first_name' => 'string|required',
            // 'last_name' => 'string|required',
            'username' => 'string|required|unique:users,username',
            'country_id' => 'required|integer',
            'phone' => 'required',
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

        $user = User::create([
            'first_name' => ' ',
            'last_name' => ' ',
            'phone' => $input['phone'],
            'username' => $input['username'],
            'country_id' => $input['country_id'],
            'email' => $input['email'],
            'external_id' => $external_id,
            'password' => Hash::make($input['password']),
        ]);



        // $user->sendEmailVerificationNotification();

        $auth_user = User::find($user->id);
        $success['token'] = $user->createToken('appToken')->accessToken;
        $password = '';

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

        $user->update([
            'email_verified_at' => now()
        ]);

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
}
