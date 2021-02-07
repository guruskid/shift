<?php

namespace App\Http\Controllers\Auth;

use App\BitcoinWallet;
use App\Country;
use App\User;

use App\Http\Controllers\Controller;
use App\Jobs\RegistrationEmailJob;
use App\Mail\DantownNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
/* Mails */
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistered;
use App\Notification;
use Carbon\Carbon;
use GuzzleHttp\Client;
use RestApis\Blockchain\Constants;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/setup-bank-account';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country_id' => 'required|integer',
            'username' => 'string|required|unique:users,username'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $country = Country::find($data['country_id']);
        //$phone = $country->phonecode . (int)$data['phone'];

        $emailJob = (new RegistrationEmailJob($data['email']));
        dispatch($emailJob);

       /*  $client = new Client();
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
                "message_text" => "Your Dantown verification pin is < 1234 > This pin will be invalid after 10 minutes",
                "pin_type" => "NUMERIC"
            ],
        ]);
        $body = json_decode($response->getBody()->getContents()); */

        $user =  User::create([
            'first_name' => ' ',
            'last_name' => ' ',
            'username' => $data['username'],
            'country_id' => $data['country_id'],
            'email' => $data['email'],
            /* 'phone' => $data['phone'],
            'phone_pin_id' => $body->pinId, */
            'password' => Hash::make($data['password']),
        ]);

        if ($user->bitcoinWallet) {
            return $user;
        }

        $password = '0000';

        try {
            $instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));

            $primary_wallet = BitcoinWallet::where(['user_id' => 1, 'primary_wallet_id' => 0])->first();
            $result = $instance->walletApiBtcGenerateAddressInWallet()->createHd(Constants::$BTC_MAINNET, $primary_wallet->name, $primary_wallet->password, 1);

            $wallet = new BitcoinWallet();
            $address = $result->payload->addresses[0];
            $wallet->user_id = $user->id;
            $wallet->path = $address->path;
            $wallet->address = $address->address;
            $wallet->type = 'secondary';
            $wallet->name = $data['username'];
            $wallet->password = $password;
            $wallet->balance = 0.00000000;
            $wallet->primary_wallet_id = $primary_wallet->id;
            $wallet->save();

            $callback = route('user.wallet-webhook');
            $result = $this->instance->webhookBtcCreateAddressTransaction()->create(Constants::$BTC_MAINNET, $callback, $wallet->address, 6);
        } catch (\Exception  $e) {
            report($e);
            return $user;
        }

        $title = 'Bitcoin Wallet created';
        $msg_body = 'Congratulations your Dantown Bitcoin Wallet has been created successfully, you can now send, receive, buy and sell Bitcoins in the wallet. ';
        $not = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $msg_body,
        ]);

        Mail::to($user->email)->send(new DantownNotification($title, $msg_body, 'Go to Wallet', route('user.bitcoin-wallet')));

        return $user;
    }
}
