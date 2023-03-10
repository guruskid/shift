<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\Bank;
use App\Country;
use App\HdWallet;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginSessionController;
use App\NairaWallet;
use App\Notification;
use App\User;
use App\UserTracking;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login()
    {
        // die('checking something here');

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            // if ($user->is_deleted == 1) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Invalid Email or Password',
            //     ], 401);
            // }
            $success['token'] = $user->createToken('appToken')->accessToken;
            //After successfull authentication, notice how I return json parameters
            \Artisan::call('naira:limit');

            // if (null !== request('fcm_id')) {
            //     $user->update([
            //         'fcm_id' => request('fcm_id'),
            //     ]);
            // }

            if (request('fcm_id') !== null) {
                $user->update([
                    'fcm_id' => request('fcm_id'),
                ]);
            }



            if ($user->role == 1) {
                $loginSession = new LoginSessionController();
                $loginSession->createSessionData($user->id);
            }

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

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'first_name' => 'required',
            // 'last_name' => 'required',
            'username' => 'string|required|unique:users,username',
            'country_id' => 'required|integer',
            'phone' => 'required',
            'password' => 'required',
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
            'first_name' => $input['first_name'],
            'phone' => $input['phone'],
            'last_name' => ' ',
            'username' => $input['username'],
            'country_id' => $input['country_id'],
            'email' => $input['email'],
            'external_id' => $external_id,
            'status' => 'active',
            'password' => Hash::make($input['password']),
            'platform' => $input['platform'],
        ]);

        $user->sendEmailVerificationNotification();

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

        UserTracking::create([
            'user_id' => $user->id,
            'Current_Cycle' => "NewUser",
            'current_cycle_count_date' => now(),
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
                            "providerCountry" => "NG",
                        ],
                        "compliant" => false,
                        "accountingCurrency" => "USD",
                    ],
                ],
            ],
        ]);

        $body = json_decode($response->getBody());

        $btc_account_id = $body[0]->id;
        $user->customer_id = $body[0]->customerId;
        $user->status = 'active';
        $user->save();

        $address_url = env('TATUM_URL') . "/offchain/account/address/batch";
        $res = $client->request('POST', $address_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "addresses" => [
                    ["accountId" => $btc_account_id],
                ],
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

        return response()->json([
            'success' => true,
            'token' => $success,
            'user' => $auth_user,
        ]);
    }

    public function saveFcm()
    {
        if (null !== request('fcm_id')) {
            $user = Auth::user();
            $user->update([
                'fcm_id' => request('fcm_id'),
            ]);
        }
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (!Hash::check($request['password'], Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => ["Incorrect Password"],
            ], 400);
        }

        Auth::user()->update(['is_deleted' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Account deleted',
        ], 200);
    }

    public function bankList()
    {
        $banks = Bank::orderBy('name', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $banks,
        ]);
    }

    public function countries()
    {
        return response()->json([
            'success' => true,
            'data' => Country::all(),
        ]);
    }

    public function getBankName(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'account_number' => 'required|integer',
            'bank_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $client = new Client();
        $url = env('RUBBIES_API') . "/nameenquiry";

        $response = $client->request('POST', $url, [
            'json' => [
                "accountnumber" => $r->account_number,
                "bankcode" => $r->bank_code,
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {
            return response()->json([
                'success' => true,
                'acct' => $body->accountname,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => $body->responsemessage,
            ]);
        }
    }

    public function verifyPhone(Request $r)
    {
        $data = $r->validate([
            'phone' => 'required|unique:users,phone',
            'otp' => 'required',
        ]);

        try {
            $client = new Client();
            $url = env('TERMII_SMS_URL') . "/otp/verify";

            $response = $client->request('POST', $url, [
                'json' => [
                    'api_key' => env('TERMII_API_KEY'),
                    "pin_id" => Auth::user()->phone_pin_id,
                    "pin" => $r->otp,
                ],
            ]);
            $body = json_decode($response->getBody()->getContents());

            if (!$body->verified || $body->verified != 'true') {
                return response()->json([
                    'success' => false,
                    'msg' => 'Phone verification failed. Please request for a new OTP',
                ]);
            }
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'msg' => 'Phone verification failed. Please request for a new OTP',
            ]);
        }

        Auth::user()->phone = $r->phone;
        Auth::user()->phone_verified_at = now();
        Auth::user()->save();
        \Artisan::call('naira:limit');

        return response()->json([
            'success' => true,
            'data' => Auth::user(),
        ]);
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
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

        $response = $client->request('POST', $url, [
            'json' => [
                'api_key' => env('TERMII_API_KEY'),
                "message_type" => "NUMERIC",
                "to" => $full_num,
                "from" => "N-Alert",
                "channel" => "dnd",
                "pin_attempts" => 4,
                "pin_time_to_live" => 10,
                "pin_length" => 6,
                "pin_placeholder" => "< 1234 >",
                "message_text" => "Your Dantown verification pin is < 1234 > This pin will be invalid after 10 minutes",
                "pin_type" => "NUMERIC",
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
            'msg' => 'An error occured while resending OTP, please try again',
        ]);
    }

    public function resendOtp()
    {
        $phone = Auth::user()->phone;
        $full_num = Auth::user()->country_id . $phone;

        $client = new Client();
        $url = env('TERMII_SMS_URL') . "/otp/send";

        $response = $client->request('POST', $url, [
            'json' => [
                'api_key' => env('TERMII_API_KEY'),
                "message_type" => "NUMERIC",
                "to" => $full_num,
                "from" => "N-Alert",
                "channel" => "dnd",
                "pin_attempts" => 4,
                "pin_time_to_live" => 10,
                "pin_length" => 6,
                "pin_placeholder" => "< 1234 >",
                "message_text" => "Your Dantown confirmation code is < 1234 >, valid for 10 minutes, one-time use only",
                "pin_type" => "NUMERIC",
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
            'msg' => 'An error occured while resending OTP, please try again',
        ]);
    }

    //New Way

    //Verify Account

    public function verifyBankName(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'bank_code' => 'required',
            'account_number' => 'required| min:10 | max:10',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'msg' => $validator->errors(),
            ], 401);
        }

        $bank_code = $request->bank_code;
        $acct_number = $request->account_number;

        $checker = Bank::where('code', $request->bank_code)->first();

        // dd($checker);
        if (!$checker) {
            return response()->json([
                'success' => false,
                'message' => 'Bank doesn\'t exist',
            ]);
        }

        $client = new Client();
        $url = 'https://app.nuban.com.ng/api/NUBAN-AGBCLVUL544?acc_no=' . $acct_number . '&bank_code=' . $bank_code;
        $response = $client->request('GET', $url);
        $body = ($response->getBody()->getContents());
        $body = json_decode($body);

        if (isset($body->error)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid Account Details',
            ]);
        }
        $acct_name = $body[0]->account_name;
        $data = [
            'account_name' => $acct_name,
        ];
        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);

    }

    public function addNewDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_name' => 'required',
            'bank_code' => 'required',
            'account_number' => 'required| min:10',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $bank = Bank::where('code', $request->bank_code)->first();

        if ($bank == null) {
            return response()->json([
                'success' => false,
                'msg' => 'Incorrect Bank',
            ]);
        }

        $addNew = new Account();
        $addNew->user_id = Auth::user()->id;
        $addNew->account_name = $request->account_name;
        $addNew->bank_name = $bank->name;
        $addNew->bank_id = $bank->id;
        $addNew->account_number = $request->account_number;

        $duplicateChecker = Account::where('user_id', Auth::user()->id)->where('bank_id', $addNew->bank_id)->first();

        //Check for different account entirely

        $initialAccount = Account::where('user_id', Auth::user()->id)->first();

        if ($initialAccount != null) {

            $fromDB = strtoupper($initialAccount->account_name);
            $incoming = strtoupper($request->account_name);

            $previous = explode(' ', $fromDB);
            $newOne = explode(' ', $incoming);

            $joinToCompare = array_intersect($previous, $newOne);

            $determiner = (count($joinToCompare) * 100 / count($previous));

            if ($determiner < 60) {

                return response()->json([
                    'success' => false,
                    'msg' => 'Account does not match the previous one',
                ]);

            }

        }

        //    dd($duplicateChecker);
        if ($duplicateChecker != null) {

            return response()->json([
                'success' => false,
                'msg' => 'Account Already added',
            ]);

        }

        $addNew->save();

        $updated = explode(' ', trim($request->account_name));

        Auth::user()->first_name = $updated[0];
        Auth::user()->last_name = strstr($request->bank_name, " ");
        Auth::user()->save();

        return response()->json([
            'success' => true,
            'user' => Auth::user(),
            'bank_accounts' => Auth::user()->accounts,
            'naira_wallet' => Auth::user()->nairaWallet,
        ]);

    }

    //This is not longer in use
    public function addBankDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required',
            'account_number' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            /* 'phone' => 'required', */
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $s = Bank::find($request->bank_id);
        if (!$s) {
            return response()->json([
                'success' => false,
                'data' => $s,
                'message' => 'Bank doesn\'t exist',
            ]);
        }
        $err = 0;

        /* Check if its duplicate */
        $accts = Auth::user()->accounts;
        foreach ($accts as $a) {
            if ($a->account_number == $request->account_number && $a->bank_name == $s->name) {
                $err += 1;
            }
        }

        if ($err == 0) {
            $a = new Account();
            $a->user_id = Auth::user()->id;
            $a->account_name = $request->first_name . ' ' . $request->last_name;
            $a->bank_name = $s->name;
            $a->bank_id = $s->id;
            $a->account_number = $request->account_number;
            $a->save();
        }

        Auth::user()->first_name = $request->first_name;
        Auth::user()->last_name = $request->last_name;
        Auth::user()->save();

        return response()->json([
            'success' => true,
            'user' => Auth::user(),
            'bank_accounts' => Auth::user()->accounts,
            'naira_wallet' => Auth::user()->nairaWallet,
        ]);

        if (Auth::user()->nairaWallet()->count() > 0) {

        }

        /* $callback = route('recieve-funds.callback');
    $client = new Client();
    $url = env('RUBBIES_API') . "/createvirtualaccount";

    $response = $client->request('POST', $url, [
    'json' => [
    'virtualaccountname' => Auth::user()->first_name,
    'amount' => '0',
    'amountcontrol' => 'VARIABLEAMOUNT',
    'daysactive' => 30,
    'minutesactive' => 30,
    'callbackurl' => $callback,
    ],
    'headers' => [
    'authorization' => env('RUBBIES_SECRET_KEY'),
    ],
    ]);
    $body = json_decode($response->getBody()->getContents());
    if ($body->responsecode == 00) {

    $naira_wallet = Auth::user()->nairaWallet()->create([
    'account_number' => $body->virtualaccount,
    'account_name' => $body->virtualaccountname,
    'bank_name' => $body->bankname,
    'bank_code' => $body->bankcode,
    'amount' => $body->amount,
    'password' => '',
    'amount_control' => $body->amountcontrol,
    ]);

    $title = 'Dantown wallet created';
    $msg_body = 'Your Dantown wallet has been created successfully, you can now send money, recieve money, pay bills and do more with your Dantown wallet. Your account number is ' . $body->virtualaccount;
    $not = Notification::create([
    'user_id' => Auth::user()->id,
    'title' => $title,
    'body' => $msg_body,
    ]);

    Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body, 'Go to wallet', route('user.naira-wallet') ));

    return response()->json([
    'success' => true,
    'user' => Auth::user(),
    'bank_accounts' => Auth::user()->accounts,
    'naira_wallet' => $naira_wallet,
    ]);
    } else {
    return response()->json([
    'success' => false,
    'message' => $body->responsemessage,
    ]);
    } */
    }

    public function sendBvnOtp($bvn)
    {
        $client = new Client();
        $url = env('RUBBIES_API') . "/verifybvn";

        $response = $client->request('POST', $url, [
            'json' => [
                "reference" => time(),
                "bvn" => $bvn,
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode != "00") {
            return response()->json([
                'success' => false,
                "msg" => $body->responsemessage,
            ]);
        }
        $phone = '';
        if (strlen($body->phoneNumber) == 11) {
            $phone = '234' . substr($body->phoneNumber, 1);
        } elseif (strlen($body->phoneNumber) == 13) {
            $phone = $body->phoneNumber;
        } else {
            $phone = $body->phoneNumber;
        }

        $client = new Client();
        $url = env('TERMII_SMS_URL') . "/otp/send";

        $response = $client->request('POST', $url, [
            'json' => [
                'api_key' => env('TERMII_API_KEY'),
                "message_type" => "NUMERIC",
                "to" => $phone,
                "from" => "N-Alert",
                "channel" => "dnd",
                "pin_attempts" => 4,
                "pin_time_to_live" => 10,
                "pin_length" => 6,
                "pin_placeholder" => "< 1234 >",
                "message_text" => "Your Dantown confirmation code is < 1234 >, valid for 10 minutes, one-time use only",
                "pin_type" => "NUMERIC",
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        //\Log::info($body);

        if ($body->status == 200) {
            Auth::user()->phone_pin_id = $body->pinId;
            Auth::user()->save();

            return response()->json([
                'success' => true,
                'msg' => 'An OTP has been sent to the phone number ' . $phone . ' to confirm your BVN',
            ]);
        }
        return response()->json([
            'success' => false,
            'msg' => "OTP could not be sent to the associated phone number",
        ]);
    }

    public function verifyBvnOtp(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'otp' => 'required',
            'bvn' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        try {
            $client = new Client();
            $url = env('TERMII_SMS_URL') . "/otp/verify";

            $response = $client->request('POST', $url, [
                'json' => [
                    'api_key' => env('TERMII_API_KEY'),
                    "pin_id" => Auth::user()->phone_pin_id,
                    "pin" => $r->otp,
                ],
            ]);
            $body = json_decode($response->getBody()->getContents());

            if (!$body->verified || $body->verified != 'true') {
                return response()->json([
                    'success' => false,
                    'msg' => "Phone verification failed. Please try again",
                ]);
            }
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'msg' => "Phone verification failed. Please request new OTP",
            ]);
        }

        Auth::user()->bvn_verified_at = now();
        Auth::user()->bvn = $r->bvn;
        Auth::user()->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function logout(Request $res)
    {
        if (Auth::user()) {
            $user = Auth::user()->token();
            $user->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Logout successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unable to Logout',
            ]);
        }
    }
}
