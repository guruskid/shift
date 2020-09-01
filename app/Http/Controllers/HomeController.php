<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

		if($user->role == 999){
    		return redirect()->route('admin.dashboard');
        }
        elseif ($user->role == 888) {
            return redirect()->route('admin.assigned-transactions');
        }
		elseif($user->role == 1 OR $user->role == 2){
    		return redirect()->route('user.dashboard');
    	}
		else{
    		abort(404);
    	}
    }

    public function setupBank()
    {
        $banks = Bank::all();

        return view('auth.bank', compact('banks'));
    }

    public function addUserBank(Request $request)
    {
        $s = Bank::where('code', $request->bank_name)->first();
        $err = 0;

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


        Auth::user()->phone = $request->phone;
        Auth::user()->first_name = $request->account_name;
        Auth::user()->save();

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

            Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body));

            return redirect()->route('user.dashboard');
        } else {
            return redirect()->route('user.dashboard')->with(['error' => 'Oops! an error occured' . $body->responsemessage]);
        }

        return ;
    }
}
