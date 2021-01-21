<?php

namespace App\Http\Controllers\Auth;

use App\Country;
use App\User;

use App\Http\Controllers\Controller;
use App\Jobs\RegistrationEmailJob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
/* Mails */
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistered;
use Carbon\Carbon;
use GuzzleHttp\Client;

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
            'phone' => 'required|integer',
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
        $phone = $country->phonecode . (int)$data['phone'];

        $emailJob = (new RegistrationEmailJob($data['email']));
        dispatch($emailJob);

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
                "message_text" => "Your Dantown verification pin is < 1234 > This pin will be invalid after 10 minutes",
                "pin_type" => "NUMERIC"
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());

        return User::create([
            'first_name' => ' ',
            'last_name' => ' ',
            'username' => $data['username'],
            'country_id' => $data['country_id'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'phone_pin_id' => $body->pinId,
            'password' => Hash::make($data['password']),
        ]);
    }
}
