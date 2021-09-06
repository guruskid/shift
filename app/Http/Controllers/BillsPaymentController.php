<?php

namespace App\Http\Controllers;

use App\BitcoinTransaction;
use App\Card;
use App\CardCurrency;
use App\Mail\DantownNotification;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\User;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

class BillsPaymentController extends Controller
{

    public function view()
    {
        if (!Auth::user()->nairaWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please create a Dantown wallet to continue']);
        }
        $buy_airtime = GeneralSettings::getSetting('AIRTIME_BUY');
        $sell_airtime = GeneralSettings::getSetting('AIRTIME_SELL');
        return view('newpages.rechargemenu', compact(['buy_airtime','sell_airtime']));
    }

    public function getUser(Request $details)
    {
        $client = new Client();
        $url = env('RUBBIES_API') . "/billspaymentverifier";

        $response = $client->request('POST', $url, [
            'json' => [
                "account" => $details->dec_num,
                "servicename" => $details->biller,
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        return response()->json($body);
    }

    public function getPackages(Request $r)
    {
        $client = new Client();
        $url = env('RUBBIES_API') . "/billers";
        $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
        $params['json'] = ["biller" => "cabletv",];
        $response = $client->post($url, $params);
        $body = json_decode($response->getBody()->getContents());

        if ($body->responsecode == "00") {
            $providers = $body->billers;
            return response()->json($providers);
        }
        return response()->json("Error occur");
    }


    public function CableView()
    {

        $client = new Client();
        $url = env('RUBBIES_API') . "/billers";
        $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
        $params['json'] = ["biller" => "cabletv",];
        $response = $client->post($url, $params);
        $body = json_decode($response->getBody()->getContents());

        if ($body->responsecode == "00") {
            $providers = $body->billers;
            return view('newpages.paytv', compact(['providers']));
        } else {
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }
    public function paytv(Request $r)
    {
        //uncommeting this line will make real transaction
        dd('hi,this app works,uncomment this line in the controller');
        $r->validate([
            'decoder' => 'required',
            'amount' => 'required',
            'password' => 'required',
        ]);

        $callback = route('recharge-card.callback');
        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->password, $n->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong wallet pin, please contact the support team if you forgot your pin']);
        }
        $amount = $r->amount;
        //check if he has enough money
        if ($amount > $n->amount) {
            return redirect()->back()->with(['error' => 'Insufficient funds']);
        }
        //what I remove here is below this page
        $reference = $r->billercode . Str::random(16);
        $client = new Client();
        $url = env('RUBBIES_API') . "/billerpurchase";
        $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
        $params['json'] = [
            "reference" => $reference,
            "billercustomerid"  => $r->account,
            "productcode" => $r->productcode,
            "amount" => $amount,
            "mobilenumber" => Auth::user()->phone,
            "name" => Auth::user()->first_name,
            "billercode" => $r->billercode,
        ];
        $response = $client->post($url, $params);
        $body =  json_decode($response->getBody()->getContents());

        $prev_bal = $n->amount;
        $n->amount -= $amount;
        $n->save();

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->user_id = Auth::user()->id;
        //this is not a typo below,that was how the initial developer created it,unless you
        //have access to change the db column name please dont change
        $nt->type = 'elecriciy bills';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $n->amount;
        $nt->charge = (1 / 100) * $amount;
        $nt->transaction_type_id = 11;


        $nt->dr_user_id = Auth::user()->id;
        $nt->dr_wallet_id = $n->id;
        $nt->dr_acct_name = $n->account_name;
        $nt->cr_acct_name = $r->provider;
        $nt->narration = 'Payment for Cable Subscription bill';
        $nt->trans_msg = 'done';
        $nt->status = 'success';
        $nt->save();

        $title = 'Cable Subscription purchase';
        $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for cable subscription recharge';

        $not = Notification::create([
            'user_id' => Auth::user()->id,
            'title' => $title,
            'body' => $msg_body,
        ]);

        /* Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body)); */

        $token = env('SMS_TOKEN');
        $to = Auth::user()->phone;
        //  $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        //   $snd_sms = $client->request('GET', $sms_url);
        if ($body->responsecode == 00 || $body->responsecode == "-1") {
            return back()->with(['success' => 'Purchase made successfully']);
        } else {
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }


    public function airtime(Request $r)
    {
        return back()->with(['error' => 'Currently not available']);
        $r->validate([
            'network' => 'required',
            'amount' => 'required',
            'password' => 'required',
            'reference' => 'required|unique:naira_transactions,reference',
            /* 'phone' => 'exclude_if:rechargetype,self|required', */
        ]);

        if ($r->rechargetype == 'self') {
            $phone = Auth::user()->country->phonecode . Auth::user()->phone;
        } else {
            $phone = $r->phone;
        }

        $callback = route('recharge-card.callback');
        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->password, $n->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong wallet pin, please contact the support team if you forgot your pin']);
        }

        $amount = $r->amount;
        $reference = $r->reference;
        if ($amount > $n->amount) {
            return redirect()->back()->with(['error' => 'Insufficient funds']);
        }

        $prev_bal = $n->amount;
        $n->amount -= $amount;
        $n->save();

        $charge = 0;
        switch ($r->network) {
            case 'mtn':
                $charge = 4.5;
                break;

            case 'glo':
                $charge = 4;
                break;

            case 'airtel':
                $charge = 7.5;
                break;

            case '9mobile':
                $charge = 3.5;
                break;

            default:
                $charge = 1;
                break;
        }

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->user_id = Auth::user()->id;
        $nt->type = 'recharge card';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $n->amount;
        $nt->charge = ($charge / 100) * $amount;
        $nt->transaction_type_id = 9;


        $nt->dr_user_id = Auth::user()->id;
        $nt->dr_wallet_id = $n->id;
        $nt->dr_acct_name = $n->account_name;
        $nt->cr_acct_name = $r->network . ' ' . $r->phone;
        $nt->narration = 'Payment for recharge card';
        $nt->trans_msg = 'done';
        $nt->status = 'pending';
        $nt->save();

        /* Credit Transfer Wallet */
        $transfer_charges_wallet = NairaWallet::where('account_number', 0000000001)->first();
        $transfer_charges_wallet->amount += $nt->charge;
        $transfer_charges_wallet->save();

        $client = new Client();
        $url = env('RUBBIES_API') . "/airtimepurchase";
        $response = $client->request('POST', $url, [
            'json' => [
                "mobilenumber" => $phone,
                "product" => $r->network,
                "amount" => $amount,
                "reference" => $reference,
                "callbackurl" => $callback
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {

            $nt->status = 'success';
            $nt->save();

            $title = 'Recharge card purchase';
            $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for recharge card purchase';

            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $msg_body,
            ]);

            /*Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body)); */

            /* $token = env('SMS_TOKEN');
            $to = Auth::user()->phone;
            $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
            $snd_sms = $client->request('GET', $sms_url); */

            return back()->with(['success' => 'Recharge made successfully']);
        } else {
            Log::info('User' . Auth::user()->first_name . ' bought airtime but it was declined');
            Log::info($body->responsemessage);
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }


    public function nairaRate()
    {
        // return back()->with(['error' => 'Service not available']);
        $naira_wallet = Auth::user()->nairaWallet;
        $balance = $naira_wallet->amount;
        // dd($balance);

        $card = Card::find(102);
        $rates = $card->currency-> first();

        $sell = CardCurrency::where([
            'card_id' => 102,
            'currency_id' => $rates->id,
            'buy_sell' => 2])->first()->paymentMediums()->first();
        $trade_rate = json_decode($sell->pivot->payment_range_settings);
        $rate_naira = $trade_rate[0]->rate;
        $res = json_decode(file_get_contents("http://api.coinbase.com/v2/prices/spot?currency=USD"));

        $btc_rate = $res->data->amount;
        // dd($rate_naira);

        $client = new Client((['auth' => ['dantownrec2@gmail.com', 'D@Nto99btc']]));
        $url =  "https://vtpass.com/api/service-categories";
        $response = $client->request('GET', $url);

        $body = json_decode($response->getBody()->getContents());

        // dd($body);

         if ($body->response_description == 000) {
            $providers = $body->content[0]->identifier;
            // dd($providers);

        return view('newpages.buyairtime', compact('card', 'rate_naira', 'btc_rate', 'providers', 'balance'));
         }

         elseif ($body->response_description == 021) {
            return back()->with(['error'=> 'Your account is locked']);
         }

         elseif ($body->response_description == 022) {
            return back()->with(['error'=> 'Your account is suspended']);
         }

         elseif ($body->response_description == 024) {
            return back()->with(['error'=> 'Your account is inactive']);
         }
    }

    public function buyAirtime(Request $request)
    {

        $request->validate([
            'network' => 'required',
            'reference' => 'required',
            'amount' => 'required',
            'rechargetype' => 'string',
            'password' => 'required'
        ]);





        // $vtpassRequest = Http::post('https://sandbox.vtpass.com/api/pay', [
        //     'request_id' => $request->reference,
        //     'serviceID' => $request->network,
        //     'amount' => $request->amount,
        //     'phone' => $request->phone

        // ]);

        if ($request->rechargetype == 'self') {
            $phone = Auth::user()->country->phonecode . Auth::user()->phone;
            if(!isset(Auth::user()->phone)){
                return back()->with(['error'=> 'Please add a phone number to your account']);
            }
        } else{
            $request->validate([
                'phone' => 'required'
            ]);

            $phone = $request->phone;
        }

        $naira_wallet = Auth::user()->nairaWallet;
        $balance = $naira_wallet->amount;
        $pin = $naira_wallet->password;
        $put_pin = $request->password;
        $hash = Hash::check($put_pin, $pin);

        if(!$hash)
        {
            return back()->with(['error' => 'Incorrect Pin']);
        }

        // dd($balance);

        if($request->amount > $balance){
            return back()->with(['error'=> 'Insufficient balance']);
        }

        if($request->amount < 100){
            return back()->with(['error' => 'Minimium Amount is ₦100']);
        }

        if($request->amount > 25000){
            return back()->with(['error' => 'Maximum Amount is ₦25000']);
        }

        $priceDeduction = $balance - $request->amount;
        $new_balance = $naira_wallet->update([
            "amount" => $priceDeduction,
        ]);

        // dd('stop here');
        $reference = rand(111111,999999).time();
        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->narration = $phone. ' ' . 'Payment for recharge card';
        $nt->amount = $request->amount;
        $nt->user_id = Auth::user()->id;
        $nt->type = 'recharge card';
        $nt->previous_balance = $balance;
        $nt->current_balance = $new_balance;
        $nt->charge = 0;
        $nt->transaction_type_id = 9;


        $nt->dr_user_id = Auth::user()->id;
        $nt->dr_wallet_id = $naira_wallet->id;
        $nt->dr_acct_name = $naira_wallet->account_name;
        $nt->cr_acct_name = $request->network . ' ' . $request->phone;
        $nt->trans_msg = 'done';
        $nt->status = 'pending';
        $nt->save();

        $client = new Client((['auth' => ['dantownrec2@gmail.com', 'D@Nto99btc']]));
        $url =  "https://vtpass.com/api/pay";
        $response = $client->request('POST', $url, [
            'json' => [
                // 'request_id' => Str::random(6),
                'request_id' => $reference,
                'serviceID' => $request->network,
                'amount' => $request->amount,
                'phone' => $phone
            ]
        ]);
        $body = json_decode($response->getBody()->getContents());
        // dd($body);


        if ($body->code == 000) {
            $nt->status = 'success';
            $nt->save();
            // dd('success');

            $nt->charge = $body->content->transactions->commission;
            $nt->save();

            $title = 'Recharge card purchase';
            $msg_body = 'Your Dantown wallet has been debited with N' . $request->amount . ' for recharge card purchase';

            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $msg_body,
            ]);

            //  Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body));

            $token = env('SMS_TOKEN');
            $to = Auth::user()->phone;
            $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
            $snd_sms = $client->request('GET', $sms_url);

            return back()->with(['success'=> 'Your recharge is successful']);
        }

       elseif($body->code == 016){
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return back()->with(['error'=> 'Your recharge failed']);
        }

        elseif ($body->code == 021){
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return back()->with(['error'=> 'Your account is locked']);
        }

        elseif ($body->code == 022){
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return back()->with(['error'=> 'Your account is suspended']);
        }

        elseif ($body->code == 024) {
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return back()->with(['error'=> 'Your account is inactive']);
         }

        // elseif ($body->code == 083){
        //     $nt->status ='failed';
        //     $nt->save();
        //     $new_balance = $naira_wallet->update([
        //         "amount" => $balance,
        //     ]);

        //     return back()->with(['error'=> 'System Error']);

        // }

        else{
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return back()->with(['error'=> 'Please Try again later']);

        }
}






    // public function bitcoinAirtime(Request $request)
    // {
    //     $request->validate([
    //         'network' => "required",
    //         'reference' => 'required',
    //         'amount' => "required",
    //         'rechargetype' => 'string',
    //         'password' => "required"
    //     ]);


    //     if ($request->rechargetype == 'self') {
    //         $phone = Auth::user()->country->phonecode . Auth::user()->phone;
    //     } else{
    //         $request->validate([
    //             'phone' => 'required'
    //         ]);

    //         $phone = $request->phone;
    //     }


    //     // dd($phone);

    //     $card = Card::find(102);
    //     $rates = $card->currency-> first();

    //     $sell = CardCurrency::where([
    //         'card_id' => 102,
    //         'currency_id' => $rates->id,
    //         'buy_sell' => 2])->first()->paymentMediums()->first();
    //     $trade_rate = json_decode($sell->pivot->payment_range_settings);
    //     //  dd($trade_rate);


    //     // dd($trade_rate);

    //     // $amt_usd= $request->amount/$trade_rate;

    //     $amt_usd= $request->amount/$trade_rate[0]->rate;


    //     $res = json_decode(file_get_contents("http://api.coinbase.com/v2/prices/spot?currency=USD"));
    //     // dd($res);

    //     $amt_btc = $amt_usd/$res->data->amount;

    //     $bitcoin_wallet = Auth::user()->bitcoinWallet;
    //     $balance = $bitcoin_wallet->balance;
    //     $pin = $bitcoin_wallet->password;
    //     $put_pin = $request->password;
    //     $hash = Hash::check($put_pin, $pin);

    //     if(!$hash)
    //     {
    //         return back()->with(['error' => 'Incorrect Pin']);
    //     }

    //     // dd($balance);

    //     if($amt_btc > $balance){
    //         return back()->with(['error'=> 'Insufficient balance']);
    //     }

    //     if($request->amount < 100){
    //         return back()->with(['error' => 'Minimium Amount is ₦100']);
    //     }

    //     if($request->amount > 25000){
    //         return back()->with(['error' => 'Maximum Amount is ₦25000']);
    //     }

    //     $priceDeduction = $balance - $amt_btc;
    //     $new_balance = $bitcoin_wallet->update([
    //         "balance" => $priceDeduction,
    //     ]);

    //     $bt = new BitcoinTransaction();
    //     $bt->hash = $request->reference;
    //     $bt->narration = $phone . ' ' . 'Payment for recharge card';
    //     $bt->user_id = Auth::user()->id;
    //     $bt->primary_wallet_id = 1;
    //     $bt->wallet_id = $bitcoin_wallet->address;
    //     $bt->previous_balance = $balance;
    //     $bt->current_balance = $new_balance;
    //     $bt->debit = $amt_btc;
    //     $bt->fee = 0;
    //     $bt->credit = 0;
    //     $bt->charge = 0;
    //     $bt->transaction_type_id = 9;
    //     $bt->counterparty = $phone ;
    //     $bt->confirmations = 3;
    //     $bt->status = 'pending';
    //     $bt->save();

    //     $client = new Client((['auth' => ['dantownrec2@gmail.com', 'D@Nto99btc']]));
    //     $url = "https://sandbox.vtpass.com/api/pay";
    //     $response = $client->request('POST', $url, [
    //         'json' => [
    //             // 'request_id' => Str::random(6),
    //             'request_id' => $request->reference,
    //             'serviceID' => $request->network,
    //             'amount' => $request->amount,
    //             'phone' => $request->phone
    //         ]
    //     ]);
    //     $body = json_decode($response->getBody()->getContents());
    //     // dd($body);


    //     if ($body->code == 000) {
    //         $bt->status = 'success';
    //         $bt->save();
    //         // dd('success');

    //         $naira_charge = $body->content->transactions->commission;

    //         $sell = CardCurrency::where([
    //             'card_id' => 102,
    //             'currency_id' => $rates->id,
    //             'buy_sell' => 2])->first()->paymentMediums()->first();
    //         $trade_rate = json_decode($sell->pivot->payment_range_settings);

    //         $charge_usd= $naira_charge/$trade_rate[0]->rate;

    //         $res = json_decode(file_get_contents("http://api.coinbase.com/v2/prices/spot?currency=USD"));

    //         $bt->charge = $charge_usd/$res->data->amount;
    //         $bt->save();



    //         $title = 'Recharge card purchase';
    //         $msg_body = 'Your Dantown wallet has been debited with N' . $request->amount . ' for recharge card purchase';

    //         $not = Notification::create([
    //             'user_id' => Auth::user()->id,
    //             'title' => $title,
    //             'body' => $msg_body,
    //         ]);

    //         //  Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body));

    //         $token = env('SMS_TOKEN');
    //         $to = Auth::user()->phone;
    //         $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
    //         $snd_sms = $client->request('GET', $sms_url);

    //         return back()->with(['success'=> 'Your recharge is successful']);
    //     }

    //     elseif($body->code == 016){
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "balance" => $balance,
    //         ]);

    //         return back()->with(['error'=> 'Your recharge failed']);
    //     }

    //     elseif ($body->code == 021){
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "balance" => $balance,
    //         ]);

    //         return back()->with(['error'=> 'Your account is locked']);
    //     }

    //     elseif ($body->code == 022){
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "balance" => $balance,
    //         ]);

    //         return back()->with(['error'=> 'Your account is suspended']);
    //     }

    //     elseif ($body->code == 024) {
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "amount" => $balance,
    //         ]);

    //         return back()->with(['error'=> 'Your account is inactive']);
    //      }

    //     else{
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "balance" => $balance,
    //         ]);

    //         return back()->with(['error'=> 'Please Try again later']);

    //     }
    // }





    public function electricityView()
    {

        $client = new Client();
        $url = env('RUBBIES_API') . "/billers";
        $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
        $params['json'] = ["biller" => "electricity",];
        $response = $client->post($url, $params);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == "00") {
            $providers = $body->billers;
            return view('newpages.paybills', compact(['providers']));
        } else {
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }


    public function getElectUser(Request $request)
    {
        $client = new Client();
        $url = env('RUBBIES_API') . "/billerverification";
        $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
        $params['json'] = [
            "billercode" => $request->billercode,
            "billercustomerid" => $request->billercustomerid
        ];
        $response = $client->post($url, $params);
        $body = json_decode($response->getBody()->getContents());

        if ($body != null) {
            return response()->json($body->name);
        }
        return response()->json('No account found');
    }

    public function payElectricity(Request $r)
    {
        //uncommeting this line will make real transaction
        dd('hi,this app works,uncomment this line in the BillsPaymentcontroller');

        $r->validate([
            // 'provider' => 'required',
            'account' => 'required',
            'amount' => 'required',
            'password' => 'required',
            'productcode' => 'required',
            "billercode"  => 'required'
        ]);
        $callback = route('recharge-card.callback');
        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->password, $n->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong wallet pin, please contact the support team if you forgot your pin']);
        }
        $amount = $r->amount;
        //check if he has enough money
        if ($amount > $n->amount) {
            return redirect()->back()->with(['error' => 'Insufficient funds']);
        }
        //what I remove here is below this page
        $reference = $r->scid . Str::random(16);
        // dd('hi,this app works,uncomment this line in the BillsPaymentcontroller');
        $client = new Client();
        $url = env('RUBBIES_API') . "/billerpurchase";
        $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
        $params['json'] = [
            "reference" => $reference,
            "billercustomerid"  => $r->account,
            "productcode" => $r->productcode,
            "amount" => $amount,
            "mobilenumber" => $r->phone,
            "name" => Auth::user()->first_name,
            "billercode" => $r->billercode,
        ];
        $response = $client->post($url, $params);
        $body =  json_decode($response->getBody()->getContents());

        $prev_bal = $n->amount;
        $n->amount -= $amount;
        $n->save();

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->user_id = Auth::user()->id;
        //this is not a typo below,that was how the initial developer created it,unless you
        //have access to change the db column name please dont change
        $nt->type = 'elecriciy bills';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $n->amount;
        $nt->charge = (1 / 100) * $amount;
        $nt->transaction_type_id = 11;


        $nt->dr_user_id = Auth::user()->id;
        $nt->dr_wallet_id = $n->id;
        $nt->dr_acct_name = $n->account_name;
        $nt->cr_acct_name = $r->provider;
        $nt->narration = 'Payment for Electricity bill';
        $nt->trans_msg = 'done';
        $nt->status = 'success';
        $nt->save();

        $title = 'Electricity purchase';
        $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for electricity recharge';

        $not = Notification::create([
            'user_id' => Auth::user()->id,
            'title' => $title,
            'body' => $msg_body,
        ]);

        /* Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body)); */

        $token = env('SMS_TOKEN');
        $to = Auth::user()->phone;
        //  $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        //   $snd_sms = $client->request('GET', $sms_url);
        if ($body->responsecode == 00 || $body->responsecode == "-1") {
            return back()->with(['success' => 'Purchase made successfully']);
        } else {

            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }

    public function airtimeToCash(Request $request)
    {
        return back()->with(['success' => 'Proceed to transfer the airtime']);
    }

    /* Callback for all */
    public function rechargeCallback(Request $r)
    {
        $t = NairaTransaction::where('reference', $r->reference)->first();
        if ($r->responsecode == 01) {
            $t->status == 'success';
            $n = NairaWallet::where('id', $t->dr_wallet_id)->first();
            $n->amount += $r->amount;
            $n->save();
        }
        $t->status = 'refunded';
        $t->save();
    }

    private function checkPayment($reference)
    {
        try {
            $client = new Client();
            $url = env('RUBBIES_API') . "/billerquery";
            $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
            $params['json'] = [
                "reference" => $reference,
            ];
            $response = $client->post($url, $params);
            $body =  json_decode($response->getBody()->getContents());
            //dd($body,$reference,1);
        } catch (Exception $exception) {
            return back()->with(['error' => 'Oops! ']);
        }
    }



    public function electricityRechargeView()
    {
        $content = $this->getProducts();
        if (!empty($content)) {
            $boards = $content;
            return view('newpages.electricity-recharge', compact(['boards']));
        } else {
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }

    public function getProducts($category = "electricity-bill") {
        $category = (
            ($category == "airtime") ? "airtime" :
            (($category == "data") ? "data" :
            (($category == "tv-subscription") ? "tv-subscription" :
            (($category == "electricity-bill") ? "electricity-bill" :
            (($category == "insurance") ? "insurance" : 
            (($category == "education") ? "education" :
            (($category == "funds") ? "funds" : ""))))))
        );
        
        $response = [];
        if(!empty($category)) {
            $ch = curl_init(env('LIVE_VTPASS_GET_PRODUCTS_URL').$category);
            \curl_setopt_array($ch,[
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT=> 120,    
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response,true);
            if(isset($response['response_description']) && $response['response_description'] != "000") {
                $response = [];
            }else{
                $response = $response['content'];
            }
        }
        return $response;
    }

    public function getVariations($product) { 
        $response = [];
        if(!empty($product)) {
            $ch = curl_init(env('LIVE_VTPASS_GET_VARIATIONS_URL').$product);
            \curl_setopt_array($ch,[
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT=> 120,    
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response,true);
            
            if(isset($response['response_description']) && $response['response_description'] != "000") {
                $response = [];
            }else{
                $response = isset($response['content']['varations']) ? $response['content']['varations'] : [];
            }
        }
        if(count($response) > 0) {
            $vars = [];
            foreach($response as $vr) {
                $vars[] = [
                    'variation_name' => $vr['name'],
                    'variation_code' => $vr['variation_code']
                ];
            }
            $response = $vars;
        }
        return $response;
    }

    public function payElectricityVtpass(Request $r)
    {

        $r->validate([
            'electricity_board' => 'required',
            'amount' => 'required',
            'password' => 'required',
            'metre_type' => 'required',
            'metre_number'  => 'required',
            'email'  => 'required',
            'phone_number'  => 'required'
        ]);

        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->password, $n->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong wallet pin, please contact the support team if you forgot your pin']);
        }

        $amount = $r->amount;
        if ($amount > $n->amount) {
            return redirect()->back()->with(['error' => 'Insufficient funds']);
        }

        if($r->amount < 500){
            return redirect()->back()->with(['error' => 'Minimium amount is ₦500']);
        }

        if($r->amount > 100000){
            return redirect()->back()->with(['error' => 'Maximium amount is ₦100000']);
        }

        $reference = rand(111111,999999).time();
        $postData['serviceID'] = $r->electricity_board;
        $postData['variation_code'] = $r->metre_type;
        $postData['amount'] = $r->amount;
        $postData['billersCode'] = $r->metre_number;
        $postData['phone'] = $r->phone_number;
        $postData['email'] = $r->email;
        $postData['request_id'] = $reference;

        $ch = curl_init(env('LIVE_VTPASS_PURCHASE_URL'));
        \curl_setopt_array($ch,[
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD=> env('VTPASS_USERNAME').':'.env('VTPASS_PASSWORD'),
            CURLOPT_TIMEOUT=> 120, 
            CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>$postData 
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response,true);

        // dd($response);

        if(isset($response['content']) && isset($response['content']['transactions'])) {
            if($response['content']['transactions']['status'] == 'delivered') {
                $prev_bal = $n->amount;
                $n->amount -= $amount;
                $n->save();

                $nt = new NairaTransaction();
                $nt->reference = $reference;
                $nt->amount = $amount;
                $nt->user_id = Auth::user()->id;
                $nt->type = 'elecriciy bills';

                $nt->previous_balance = $prev_bal;
                $nt->current_balance = $n->amount;
                $nt->charge = (1 / 100) * $amount;
                $nt->transaction_type_id = 11;


                $nt->dr_user_id = Auth::user()->id;
                $nt->dr_wallet_id = $n->id;
                $nt->dr_acct_name = $n->account_name;
                $nt->cr_acct_name = $r->provider;
                $nt->narration = 'Payment for Electricity bill';
                $nt->trans_msg = 'done';
                $nt->status = 'success';

                $extras = json_encode([
                    'token' => $response['token'],
                    'purchased_code' => $response['purchased_code'],
                    'units' => $response['units'],
                ]);

                $nt->extras = $extras;
                $nt->save();

                $phone = Auth::user()->country->phonecode . Auth::user()->phone;

                if (isset(Auth::user()->phone)) {
                    $client = new Client();
                    $url = env('TERMII_SMS_URL') . "/send";
                    $country = Country::find(Auth::user()->country_id);
                    $phone_number = $country->phonecode . $phone;

                    $response = $client->request('POST', $url, [
                        'json' => [
                            'api_key' => env('TERMII_API_KEY'),
                            "type" => "plain",
                            "to" => $phone_number,
                            "from" => "N-Alert",
                            "channel" => "dnd",
                            "sms" => "Your electricity purchase from Dantown was successful. Token : ".$response['token'].", Units : ".$response['units'].", Reference code:".$reference."."
                        ],
                    ]);
                    $body = json_decode($response->getBody()->getContents());   
                }

                $title = 'Electricity purchase';
                $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for electricity recharge. 
                Token: '.$response['token']. ',
                Unit: '. $response['units']. ',
                Reference code:'. $reference;

                $not = Notification::create([
                    'user_id' => Auth::user()->id,
                    'title' => $title,
                    'body' => $msg_body,
                ]);

                return back()->with(['success' => 'Purchase made successfully']);
            }
        }else {
            return back()->with(['error' => 'Oops! An error occured, please try again']);
        }
    }

    public function disabledView()
    {
        return back()->with(['error' => 'Service currently not available']);
    }
}
