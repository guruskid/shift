<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Country;
use App\Mail\DantownNotification;
use App\Notification;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function test()
    {
        $client = new Client();
        $url = env('TERMII_SMS_URL') . "/otp/send";

        $response = $client->request('POST', $url, [
            'json' => [
                'api_key' => env('TERMII_API_KEY'),
                "message_type" => "NUMERIC",
                "to" => "2348076711101",
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

        dd($body);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 999 || $user->role == 889 || $user->role == 777 || $user->role == 666) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 888) {
            return redirect()->route('admin.assigned-transactions');
        } elseif ($user->role == 1 or $user->role == 2) {
            return redirect()->route('user.dashboard');
        } else {
            abort(404);
        }
    }

    public function setupBank()
    {
        $banks = Bank::all();

        return view('auth.bank', compact('banks'));
    }

    public function phoneVerification()
    {
        return view('auth.verify_phone');
    }


    //send OTP for old users
    public function sendOtp($phone, $country_id)
    {
        if(strlen($phone) > 10 ){
            return response()->json([
                'msg' => 'Your phone number should be maximum of 10 digits and should not include the starting 0 digit'
            ]);
        }
        $client = new Client();
        $url = env('TERMII_SMS_URL') . "/otp/send";
        $country = Country::find($country_id);
        $full_num = $country->phonecode . $phone;

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
            Auth::user()->phone = $phone;
            Auth::user()->country_id = $country_id;
            Auth::user()->phone_pin_id = $body->pinId;
            Auth::user()->save();

            return response()->json([
                'success' => $phone,
            ]);
        }
        return response()->json([
            'msg' => 'An error occured while resending OTP, please try again'
        ]);
    }


    public function verifyPhone(Request $r)
    {
        $data = $r->validate([
            'phone' => 'required',
            'otp' => 'required',
            'username' => 'required|string',
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
                return back()->with(['error' => 'Phone verification failed. Please request for a new OTP']);
            }
        } catch (\Exception $e) {
            report($e);
            return back()->with(['error' => 'Phone verification failed. Please request new OTP']);
        }


        Auth::user()->phone_verified_at = now();
        Auth::user()->username = $data['username'];
        Auth::user()->save();

        return redirect()->route('user.dashboard');
    }

    public function resendOtp()
    {
        $phone = Auth::user()->phone;

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
            'msg' => 'An error occured while resending OTP, please try again'
        ]);
    }

    public function addUserBank(Request $request)
    {
        $data = $request->validate([
            'bank_code' => 'required',
            'account_number' => 'required',
            'account_name' => 'required|string',
        ]);
        $s = Bank::where('code', $request->bank_code)->first();
        $err = 0;

        //verify phone
        if ($request->has('otp')) {
            try {
                $client = new Client();
                $url = env('TERMII_SMS_URL') . "/otp/verify";

                $response = $client->request('POST', $url, [
                    'json' => [
                        'api_key' => env('TERMII_API_KEY'),
                        "pin_id" => Auth::user()->phone_pin_id,
                        "pin" => $request->otp
                    ],
                ]);
                $body = json_decode($response->getBody()->getContents());

                if ($body->verified != 'true') {

                    return back()->with(['error' => 'Phone verification failed. Please request a new OTP']);
                }
            } catch (\Exception $e) {
                report($e);
                return back()->with(['error' => 'Phone verification failed. Please request new OTP']);
            }


            Auth::user()->phone_verified_at = now();
        }


        Auth::user()->first_name = $request->account_name;
        Auth::user()->save();

        $accts = Auth::user()->accounts;
        foreach ($accts as $a) {
            if ($a->account_number == $request->account_number && $a->bank_name == $s->name) {
                $err += 1;
            }
        }

        if ($err == 0) {
            $request->validate([
                'account_number' => 'required|unique:accounts,account_number'
            ]);
            $a = new Account();
            $a->user_id = Auth::user()->id;
            $a->account_name = $request->account_name;
            $a->bank_name = $s->name;
            $a->bank_id = $s->id;
            $a->account_number = $request->account_number;
            $a->save();
        }


        if (Auth::user()->nairaWallet()->count() > 0) {
            return redirect()->route('user.dashboard');
        }

        $callback = route('recieve-funds.callback');
        $client = new Client();
        $url = env('RUBBIES_API') . "/createvirtualaccount";

        $response = $client->request('POST', $url, [
            'json' => [
                'virtualaccountname' => Auth::user()->first_name,
                'amount' => '0',
                'amountcontrol' => 'VARIABLEAMOUNT',
                'daysactive' => 300000,
                'minutesactive' => 30,
                'callbackurl' => $callback,
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {

            Auth::user()->nairaWallet()->create([
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

            Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body, 'Go to wallet', route('user.naira-wallet')));

            return redirect()->route('user.dashboard');
        } else {
            return redirect()->route('user.dashboard')->with(['error' => 'Oops! an error occured' . $body->responsemessage]);
        }

        return;
    }

    public function sendBvnOtp($bvn)
    {
        $client = new Client();
        $url = env('RUBBIES_API') . "/verifybvn";

        $response = $client->request('POST', $url, [
            'json' => [
                "reference" => time(),
                "bvn" => $bvn
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode != "00") {
            return response()->json([
                'success' => false,
                "msg" => $body->responsemessage
            ]);
        }
        $phone = '';
        if (strlen($body->phoneNumber) == 11) {
            $phone = '234'. substr($body->phoneNumber, 1);
        }elseif (strlen($body->phoneNumber) == 13) {
            $phone = $body->phoneNumber;
        }else{
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
                "pin_time_to_live" =>  10,
                "pin_length" => 6,
                "pin_placeholder" => "< 1234 >",
                "message_text" => "Your Dantown confirmation code is < 1234 >, valid for 10 minutes, one-time use only",
                "pin_type" => "NUMERIC"
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        //\Log::info($body);

        if ($body->status == 200) {
            Auth::user()->phone_pin_id = $body->pinId;
            Auth::user()->save();

            return response()->json([
                'success' => true,
            ]);
        }
        return response()->json([
            'success' => false,
            'msg' => "OTP could not be sent to the associated phone number"
        ]);
    }

    public function verifyBvnOtp(Request $r)
    {
        $data = $r->validate([
            'otp' => 'required',
            'bvn' => 'required',
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
                return back()->with(['error' => 'Phone verification failed. Please try again']);
            }
        } catch (\Exception $e) {
            report($e);
            return back()->with(['error' => 'Phone verification failed. Please request new OTP']);
        }


        Auth::user()->bvn_verified_at = now();
        Auth::user()->bvn = $data['bvn'];
        Auth::user()->save();

        return redirect()->route('user.dashboard');
    }
}
