<?php

namespace App\Http\Controllers\Api;

use App\BitcoinTransaction;
use App\Card;
use App\CardCurrency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\Notification;
// use App\Http\Controllers\BillsPaymentController as BillsPayment;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BillsPaymentController extends Controller
{
    public function nairaRate()
    {
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

        return response()->view('newpages.buyairtime', compact('card', 'rate_naira', 'btc_rate', 'providers', 'balance'));
         }

         else{
             return response()->json([
                'success' => false,
                'message' => 'Please try again later',
            ]);
        }
    }

    public function buyAirtime(Request $request)
    {

        $data = Validator::make($request->all(),[
            'network' => 'required',
            'reference' => 'required',
            'amount' => 'required',
            'rechargetype' => 'string',
            'password' => 'required'
        ]);




        if ($request->rechargetype == 'self') {
            $phone = Auth::user()->country->phonecode . Auth::user()->phone;
        } else{
            $request->validate([
                'phone' => 'required'
            ]);

            $phone = $request->phone;
        }

        if ($data->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $data->errors()
                ]);
            }

        $user = Auth::user();
        $naira_wallet = $user->nairaWallet;
        $balance = $naira_wallet->amount;
        $pin = $user->pin;
        $put_pin = $request->password;
        $hash = Hash::check($put_pin, $pin);

        if(!$hash)
        {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Pin',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        // dd($balance);

        if($request->amount > $balance){
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if($request->amount < 100){
            return response()->json([
                'success' => false,
                'message' => 'Minimium amount is ₦100',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if($request->amount > 25000){
            return response()->json([
                'success' => false,
                'message' => 'Maximium amount is ₦25000',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        $priceDeduction = $balance - $request->amount;
        $new_balance = $naira_wallet->update([
            "amount" => $priceDeduction,
        ]);

        // dd('stop here');
        $nt = new NairaTransaction();
        $nt->reference = $request->reference;
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
                'request_id' => $request->reference,
                'serviceID' => $request->network,
                'amount' => $request->amount,
                'phone' => $request->phone
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

            return response()->json([
                'success' => true,
                'response_description' => 'TRANSACTION SUCCESSFUL',
                'message' => 'Your recharge is successful'
            ]);
        }

       elseif ($body->code == 016){
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your recharge failed',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        elseif ($body->code == 021){
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is locked',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        elseif ($body->code == 022){
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is suspended',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        elseif ($body->code == 024) {
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
         }

        else{
            $nt->status ='failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Please try again later',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }
    }


    public function payElectricityVtpass(Request $r)
    {
        $data = Validator::make($r->all(),[
            'electricity_board' => 'required',
            'amount' => 'required',
            'password' => 'required',
            'metre_type' => 'required',
            'metre_number'  => 'required',
            'email'  => 'required',
            'phone_number'  => 'required'
        ]);

        if ($data->fails()){
            return response()->json([
                'success' => false,
                'message' => $data->errors()
            ]);
        }

        $user = Auth::user();
        $naira_wallet = $user->nairaWallet;
        $balance = $naira_wallet->amount;
        $pin = $user->pin;
        $put_pin = $r->password;
        $hash = Hash::check($put_pin, $pin);

        if(!$hash){
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Pin',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if($r->amount > $balance){
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if($r->amount < 500){
            return response()->json([
                'success' => false,
                'message' => 'Minimium amount is ₦500',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if($r->amount > 100000){
            return response()->json([
                'success' => false,
                'message' => 'Maximium amount is ₦100000',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
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

                return response()->json([
                    'success' => true,
                    'response_description' => 'TRANSACTION SUCCESSFUL',
                    'message' => 'Purchase made successfully'
                ]);
            }
        }else {
            return response()->json([
                'success' => false,
                'message' => 'An error occured, please try again',
                'response_description' => 'TRANSACTION FAILURE',
                'debug_response' => $response['response_description']
            ]);
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
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Incorrect Pin',
    //             'response_description' => 'TRANSACTION FAILURE',
    //         ]);
    //     }

    //     // dd($balance);

    //     if($request->amount > $balance){
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Insufficient balance',
    //             'response_description' => 'TRANSACTION FAILURE',
    //         ]);
    //     }

    //     if($request->amount < 100){
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Minimium amount is ₦100',
    //             'response_description' => 'TRANSACTION FAILURE',
    //         ]);
    //     }

    //     if($request->amount > 25000){
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Maximium amount is ₦25000',
    //             'response_description' => 'TRANSACTION FAILURE',
    //         ]);
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
    //     $url = "https://vtpass.com/api/pay";
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

    //         return response()->json([
    //             'success' => true,
    //             'response_description' => 'TRANSACTION SUCCESSFUL',
    //             'message' => 'Your recharge is successful'
    //         ]);
    //     }

    //     elseif($body->code == 016){
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "balance" => $balance,
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Your recharge failed',
    //             'response_description' => 'TRANSACTION FAILURE',
    //         ]);
    //     }

    //     elseif ($body->code == 021){
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "balance" => $balance,
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Your account is locked',
    //             'response_description' => 'TRANSACTION FAILURE',
    //         ]);
    //     }

    //     elseif ($body->code == 022){
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "balance" => $balance,
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Your account is suspended',
    //             'response_description' => 'TRANSACTION FAILURE',
    //         ]);
    //     }

    //     elseif ($body->code == 024) {
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "amount" => $balance,
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Your account is inactive',
    //             'response_description' => 'TRANSACTION FAILURE',
    //         ]);
    //      }

    //     else{
    //         $bt->status ='failed';
    //         $bt->save();
    //         $new_balance = $bitcoin_wallet->update([
    //             "balance" => $balance,
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Please try again later',
    //             'response_description' => 'TRANSACTION FAILURE',
    //         ]);
    //     }
    // }

}
