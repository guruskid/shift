<?php

namespace App\Http\Controllers\Auth;

use App\BitcoinWallet;
use App\Country;
use App\HdWallet;
use App\User;

use App\Http\Controllers\Controller;
use App\Jobs\RegistrationEmailJob;
use App\Mail\DantownNotification;
use App\Mail\GeneralTemplateOne;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
/* Mails */
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistered;
use App\NairaWallet;
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
    protected $redirectTo = '/user';

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
            'first_name' => 'string|required',
            // 'last_name' => 'string',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            /* 'country_id' => 'required|integer', */
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
        $emailJob = (new RegistrationEmailJob($data['email']));
        dispatch($emailJob);


        $username = \Str::lower($data['username']);
        $external_id = $username . '-' . uniqid();

        $user =  User::create([
            'first_name' => ' ',
            'last_name' => ' ',
            'username' => $username,
            'email' => $data['email'],
            'external_id' => $external_id,
            'password' => Hash::make($data['password']),
        ]);


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

        /* } catch (\Exception  $e) {
            report($e);
            return $user;
        } */

        $title = 'Bitcoin Wallet created';
        $msg_body = 'Congratulations your Dantown Bitcoin Wallet has been created successfully, you can now send, receive, buy and sell Bitcoins in the wallet. ';
        $not = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $msg_body,
        ]);

        $title = 'Welcome to Dantown,';
        $body = 'Congratulations '.$user->username.' on signing up on Dantown.<br>
        We are excited to share this journey with you. A new world of boundless possibilities where you can trade cryptocurrencies and gift-cards with ease.
        <br><br>

        If you have enquiries about our products or issues regarding your account, kindly contact our customer happiness team via support@godantown.com.<br>
        ';

        $btn_text = '';
        $btn_url = '';

        $name = $user->username;
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $name));


        //Mail::to($user->email)->send(new DantownNotification($title, $msg_body, 'Go to Wallet', route('user.bitcoin-wallet')));


        // $engage = new \Engage\EngageClient($_SERVER['01ce2904a334879cddb2ae7e62d019a3'], $_SERVER['4595ad59cdc1a7df2af3e4a06ce1a4da']);
        // $engage->users->identify([
        //     'id' => $user->id,
        //     'email' => $user->email,
        //     'created_at' => $user->created_at
        //   ]);
        return $user;
    }
}
