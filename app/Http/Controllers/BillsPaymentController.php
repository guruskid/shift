<?php

namespace App\Http\Controllers;

use App\Card;
use App\Mail\DantownNotification;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

class BillsPaymentController extends Controller
{

    public function view()
    {
        if (!Auth::user()->nairaWallet ) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please create a Dantown wallet to continue']);
        }
        $client = new Client();

        /* Cable tv sellers */
        $url = env('RUBBIES_API') . "/billspaymentcategories";

        $response = $client->request('POST', $url, [
            'json' => [
                "categoryid" => "1"
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        $billers = $body->servicecategory;

        /* Electricity providers */
        $url = env('RUBBIES_API') . "/billspaymentcategories";
        $response = $client->request('POST', $url, [
            'json' => [
                "categoryid" => 2,
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());

        $providers = $body->servicecategory;

        $ref = \Str::random(2) . time();
        return view('newpages.rechargemenu', compact(['billers', 'providers', 'ref' ]) );
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
        $url = env('RUBBIES_API') . "/billspaymentcustomerproducts";

        $response = $client->request('POST', $url, [
            'json' => [
                "account" => $r->dec_num,
                "servicename" => $r->biller,
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        return response()->json($body);
    }
    public function CableView()
    {

        $client = new Client();
        $url = env('RUBBIES_API') . "/billers";
        $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
        $params['json'] = ["biller" => "cabletv",];
        $response = $client->post($url, $params);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == "00"){
            $providers = $body->billers;
            //endpoint not provider yet
            dd($providers);
            return view('user.electricity', compact(['providers']));
        }
        else{
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }
    public function paytv(Request $r)
    {
        $r->validate([
            'service_name' => 'required',
            'package' => 'required',
            'amount' => 'required',
            'password' => 'required',
        ]);

        $n = Auth::user()->nairaWallet;

//        if (Hash::check($r->password, $n->password) == false) {
//            return redirect()->back()->with(['error' => 'Wrong wallet pin, please contact the support team if you forgot your pin']);
//        }

        $amount = $r->amount;
        $reference = $r->ref;
        if ($amount > $n->amount) {
            return redirect()->back()->with(['error' => 'Insufficient funds']);
        }

        $client = new Client();
        $url = env('RUBBIES_API') . "/tvpurchase";
        $response = $client->request('POST', $url, [
            'json' => [
                "reference" => $reference,
                "bundleCode" => $r->package,
                "amount" => $amount,
                "phone" => Auth::user()->phone,
                "name" => $r->name,
                "servicename" => $r->service_name,
                "smartcard" => $r->card_num
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {

            $prev_bal = $n->amount;
            $n->amount -= $amount;
            $n->save();

            $nt = new NairaTransaction();
            $nt->reference = $reference;
            $nt->amount = $amount;
            $nt->user_id = Auth::user()->id;
            $nt->type = 'paytv';

            $nt->previous_balance = $prev_bal;
            $nt->current_balance = $n->amount;
            $nt->charge = (1.2/100) * $amount;
            $nt->transaction_type_id = 12;


            $nt->dr_user_id = Auth::user()->id;
            $nt->dr_wallet_id = $n->id;
            $nt->dr_acct_name = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $nt->cr_acct_name = $r->service_name;
            $nt->narration = 'Payment for cable subscription';
            $nt->trans_msg = 'done';
            $nt->save();

            $title = 'Dantown wallet Debit';
            $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for cable subscription';

            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $msg_body,
            ]);

            /* Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body)); */

            $token = env('SMS_TOKEN');
            $to = Auth::user()->phone;
            $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
            $snd_sms = $client->request('GET', $sms_url);

            return back()->with(['success' => 'Payment made successfully']);
        } else {
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }


    public function airtime(Request $r)
    {
        /* dd($r->all()); */
        $r->validate([
            'network' => 'required',
            'amount' => 'required',
            'password' => 'required',
            'reference' => 'required|unique:naira_transactions,reference',
            /* 'phone' => 'exclude_if:rechargetype,self|required', */
        ]);

        if ($r->rechargetype == 'self') {
            $phone = Auth::user()->phone;
        }else{
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

        $client = new Client();
        $url = env('RUBBIES_API') . "/airtimepurchase";
        $response = $client->request('POST', $url, [
            'json' => [
                "mobilenumber" => '07067186987',
                "product" => 'mtn',
                "amount" => '10',
                "reference" => $reference,
                "callbackurl" => $callback
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {

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

            $prev_bal = $n->amount;
            $n->amount -= $amount;
            $n->save();

            $nt = new NairaTransaction();
            $nt->reference = $reference;
            $nt->amount = $amount;
            $nt->user_id = Auth::user()->id;
            $nt->type = 'recharge card';

            $nt->previous_balance = $prev_bal;
            $nt->current_balance = $n->amount;
            $nt->charge = ($charge/100) * $amount;
            $nt->transaction_type_id = 9;


            $nt->dr_user_id = Auth::user()->id;
            $nt->dr_wallet_id = $n->id;
            $nt->dr_acct_name = $n->account_name;
            $nt->cr_acct_name = $r->network . ' ' . $r->phone;
            $nt->narration = 'Payment for recharge card';
            $nt->trans_msg = 'done';
            $nt->status = 'success';
            $nt->save();

            $title = 'Recharge card purchase';
            $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for recharge card purchase';

            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $msg_body,
            ]);

            /* Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body)); */

            $token = env('SMS_TOKEN');
            $to = Auth::user()->phone;
            $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
            $snd_sms = $client->request('GET', $sms_url);

            return back()->with(['success' => 'Recharge made successfully']);
        } else {
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }



    public function electricityView()
    {

        $client = new Client();
        $url = env('RUBBIES_API') . "/billers";
        $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
        $params['json'] = ["biller" => "electricity",];
        $response = $client->post($url, $params);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == "00"){
            $providers = $body->billers;
        return view('user.electricity', compact(['providers']));
        }
        else{
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }


    public function getElectUser(Request $r)
    {
        $client = new Client();
        $url = env('RUBBIES_API') . "/billspaymentverifycustomer";
        $response = $client->request('POST', $url, [
            'json' => [
                "account" => $r->account,
                'service_category_id' => $r->service_category_id
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        return response()->json($body);
        if ($body != null ) {
            return response()->json($body->data->name);
        }

        return response()->json('No account found');
    }

    public function payElectricity(Request $r)
    {

        $r->validate([
            'scid' => 'required',
            'provider' => 'required',
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
        $reference = $r->scid .Str::random(16);
        $client = new Client();
        $url = env('RUBBIES_API')."/billerpurchase";
        $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
        $params['json'] = [
            "reference" => $reference,
            "billercustomerid"  => $r->account,
            "productcode"=> $r->productcode,
            "amount" => $amount,
            "mobilenumber" =>Auth::user()->phone,
//            "mobilenumber" => "08142381323",
            "name" => Auth::user()->first_name,
            "billercode" => $r->billercode,
        ];
        $response = $client->post($url,$params);
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
            $nt->charge = (1/100) * $amount;
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
            $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
            $snd_sms = $client->request('GET', $sms_url);
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
        try{
            $client = new Client();
            $url = env('RUBBIES_API')."/billerquery";
            $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => env('RUBBIES_SECRET_KEY')];
            $params['json'] = [
                "reference" => $reference,
            ];
            $response = $client->post($url,$params);
            $body =  json_decode($response->getBody()->getContents());
//            dd($body,$reference,1);
        }
        catch (Exception $exception){
            return back()->with(['error' => 'Oops! ']);
        }
    }
}

