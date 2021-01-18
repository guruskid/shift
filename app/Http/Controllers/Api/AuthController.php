<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\Bank;
use App\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\DantownNotification;
use App\Notification;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function login()
    {

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('appToken')->accessToken;
            //After successfull authentication, notice how I return json parameters
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
            'password' => 'required',
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

        $country = Country::find($input['country_id']);
        $phone = $country->phonecode . (int)$input['phone'];

        $client = new Client();
        $url = env('TERMII_SMS_URL') . "/otp/send";

        $response = $client->request('POST', $url, [
            'json' => [
                'api_key' => env('TERMII_API_KEY'),
                "message_type" => "NUMERIC",
                "to" => $phone,
                "from" => "Dantown",
                "channel" => "generic",
                "pin_attempts" => 4,
                "pin_time_to_live" =>  10,
                "pin_length" => 6,
                "pin_placeholder" => "< 1234 >",
                "message_text" => "Your Dantown verification pin is < 1234 > This pin will be invalid after 10 minutes",
                "pin_type" => "NUMERIC"
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());



        $user = User::create([
            'first_name' => ' ',
            'last_name' => ' ',
            'username' => $input['username'],
            'country_id' => $input['country_id'],
            'email' => $input['email'],
            'phone' => $phone,
            'phone_pin_id' => $body->pinId,
            'password' => Hash::make($input['password']),
        ]);
        $auth_user = User::find($user->id);
        $success['token'] = $user->createToken('appToken')->accessToken;
        return response()->json([
            'success' => true,
            'token' => $success,
            'user' => $auth_user
        ]);
    }

    public function bankList()
    {
        $banks = Bank::orderBy('name', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $banks
        ]);
    }

    public function countries()
    {
        return response()->json([
            'success' => true,
            'data' => Country::all()
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
                "bankcode" => $r->bank_code
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {
            return response()->json([
                'success' => true,
                'acct' => $body->accountname
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => $body->responsemessage
            ]);
        }
    }

    public function verifyPhone(Request $r)
    {
        $data = $r->validate([
            'phone' => 'required',
            'otp' => 'required',
        ]);

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


        Auth::user()->phone_verified_at = now();
        Auth::user()->save();

        return response()->json([
            'success' => true,
            'data' => Auth::user()
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
                    "pin_time_to_live" =>  10,
                    "pin_length" => 6,
                    "pin_placeholder" => "< 1234 >",
                    "message_text" => "Your Dantown verification pin is < 1234 > This pin will be invalid after 10 minutes",
                    "pin_type" => "NUMERIC"
                ],
            ]);
        $body = json_decode($response->getBody()->getContents());

        if ($body->status == 200) {
            Auth::user()->phone = $request->phone;
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


    public function addBankDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'phone' => 'required',
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
        foreach ($accts as $a ) {
            if ($a->account_number == $request->account_number && $a->bank_name == $s->name ) {
                $err += 1;
            }
        }

        if ($err == 0) {
            $a = new Account();
            $a->user_id = Auth::user()->id;
            $a->account_name = $request->account_name;
            $a->bank_name = $s->name;
            $a->bank_id = $s->id;
            $a->account_number = $request->account_number;
            $a->save();
        }

        Auth::user()->first_name = $request->account_name;
        Auth::user()->save();

        if (Auth::user()->nairaWallet()->count() > 0) {
            return response()->json([
                'success' => true,
                'user' => Auth::user(),
                'bank_accounts' => Auth::user()->accounts,
                'naira_wallet' => Auth::user()->nairaWallet,
            ]);
        }

        $callback = route('recieve-funds.callback');
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
        }
    }

    public function logout(Request $res)
    {
        if (Auth::user()) {
            $user = Auth::user()->token();
            $user->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Logout successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unable to Logout'
            ]);
        }
    }
}
