<?php

namespace App\Http\Controllers\Api;

use App\BitcoinTransaction;
use App\Card;
use App\CardCurrency;
use App\Country;
use App\Events\CustomNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Mail\GeneralTemplateOne;
use App\NairaTransaction;
use App\Notification;
use App\UtilityTransaction;
use App\Http\Controllers\BillsPaymentController as BillsPayment;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// use GuzzleHttp\Client;

class BillsPaymentController extends Controller
{
    public function nairaRate()
    {
        $naira_wallet = Auth::user()->nairaWallet;
        $balance = $naira_wallet->amount;
        // dd($balance);
        $card = Card::find(102);
        $rates = $card->currency->first();

        $sell = CardCurrency::where([
            'card_id' => 102,
            'currency_id' => $rates->id,
            'buy_sell' => 2
        ])->first()->paymentMediums()->first();
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
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Please try again later',
            ]);
        }
    }

    public function purchase($postData = [])
    {
        $ch = curl_init(env('LIVE_VTPASS_PURCHASE_URL'));
        \curl_setopt_array($ch, [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => env('VTPASS_USERNAME') . ':' . env('VTPASS_PASSWORD'),
            CURLOPT_TIMEOUT => 120,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        return $response;
    }

    // Data Buy
    public function data()
    {
        $products = BillsPayment::getProducts('data');
        foreach ($products as $key => $value) {
            if ($value['serviceID'] == 'smile-direct') {
                unset($products[$key]);
            }
            unset($products[$key]['minimium_amount']);
            unset($products[$key]['maximum_amount']);
            unset($products[$key]['convinience_fee']);
            unset($products[$key]['product_type']);
        }

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function buyData(Request $request)
    {

        $rule = [
            'service_id' => 'required',
            'bundle'       => 'required',
            'amount'      => 'required',
            'phone'  => 'required',
            'password' => 'required'
        ];

        $data = Validator::make($request->all(), $rule);

        if ($data->fails()) {
            return response()->json([
                'success' => false,
                'message' => $data->errors()
            ]);
        }

        $phone = $request->phone;

        $user = Auth::user();

        $naira_wallet = $user->nairaWallet;
        $balance = $naira_wallet->amount;
        $pin = $user->pin;
        $put_pin = $request->password;
        $hash = Hash::check($put_pin, $pin);

        if (!$hash) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Pin',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($request->amount > $balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($request->amount < 49) {
            return response()->json([
                'success' => false,
                'message' => 'Minimium amount is ₦50',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($request->amount > 25000) {
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
        $reference = rand(111111, 999999) . time();
        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->narration = $phone . ' ' . 'Payment for mobile data';
        $nt->amount = $request->amount;
        $nt->user_id = Auth::user()->id;
        $nt->type = 'mobile data';
        $nt->previous_balance = $balance;
        $nt->current_balance = $new_balance;
        $nt->charge = 0;
        $nt->transaction_type_id = 9;


        $nt->dr_user_id = Auth::user()->id;
        $nt->dr_wallet_id = $naira_wallet->id;
        $nt->dr_acct_name = $naira_wallet->account_name;
        $nt->cr_acct_name = $request->network . ' ' . $phone;
        $nt->trans_msg = 'done';
        $nt->status = 'pending';
        $nt->save();

        $reference = rand(111111, 999999) . time();
        $postData['serviceID'] = $request->service_id;
        $postData['variation_code'] = $request->bundle;
        $postData['amount'] = $request->amount;
        $postData['phone'] = $phone;
        $postData['request_id'] = $reference;


        $body = $this->purchase($postData);

        // $data = '{"code":"000","content":{"transactions":{"status":"delivered","product_name":"Airtel Data","unique_element":"09027452545","unit_price":49.99,"quantity":1,"service_verification":null,"channel":"api","commission":2,"total_amount":47.99,"discount":null,"type":"Data Services","email":"dantownrec2@gmail.com","phone":"09012435013","name":null,"convinience_fee":0,"amount":49.99,"platform":"api","method":"api","transactionId":"16326998482745635385910878"}},"response_description":"TRANSACTION SUCCESSFUL","requestId":"4898001632699847","amount":"49.99","transaction_date":{"date":"2021-09-27 00:44:08.000000","timezone_type":3,"timezone":"Africa\/Lagos"},"purchased_code":""}';

        // $body = json_decode($data,true);

        if ($body['code'] == 000) {
            $nt->status = 'success';
            $nt->charge = $body['content']['transactions']['commission'];
            $nt->save();

            $charge = 0;
            $amount = $request->amount;
            $total_charge = $amount + $charge;

            $extras = json_encode([
                'phone' => $phone
            ]);

            UtilityTransaction::create([
                'user_id'          => Auth::user()->id,
                'reference_id'     => $reference,
                'amount'           => $amount,
                'convenience_fee'  => $charge,
                'total'            => $total_charge,
                'type'             => 'Data purchase',
                'status'           => 'success',
                'extras'           => $extras
            ]);

            $accountants = User::where(['role' => 777, 'status' => 'active'])->orWhere(['role' => 889, 'status' => 'active'])->get();
            $message = '!!! Utility Transaction Transaction !!!  A new Utility transaction has been initiated ';
            foreach ($accountants as $acct) {
                broadcast(new CustomNotification($acct, $message))->toOthers();
            }

            $title = 'Data purchase';
            $msg_body = 'Your Dantown wallet has been debited with N' . $request->amount . ' for data purchase';

            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $msg_body,
            ]);

            return response()->json([
                'success' => true,
                'response_description' => 'TRANSACTION SUCCESSFUL',
                'message' => 'Your data purchase to ' . $phone . ' was successful'
            ]);
        } elseif ($body['code'] == 016) {
            $nt->status = 'failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your data purchase failed'
            ]);
        } elseif ($body['code'] == 021) {
            $nt->status = 'failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is locked'
            ]);
        } elseif ($body['code'] == 022) {
            $nt->status = 'failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is suspended'
            ]);
        } elseif ($body['code'] == 024) {
            $nt->status = 'failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive'
            ]);
        } else {
            $nt->status = 'failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Please Try again later'
            ]);
        }
    }

    // Aritime Buy
    public function airtime()
    {
        $products = BillsPayment::getProducts('airtime');
        foreach ($products as $key => $value) {
            if ($value['serviceID'] == 'etisalat-pin') {
                unset($products[$key]);
            }
            unset($products[$key]['minimium_amount']);
            unset($products[$key]['maximum_amount']);
            unset($products[$key]['convinience_fee']);
            unset($products[$key]['product_type']);
        }

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function buyAirtime(Request $request)
    {

        $rule = [
            'service_id' => 'required',
            'amount' => 'required',
            'phone'  => 'required',
            'password' => 'required'
        ];

        $data = Validator::make($request->all(), $rule);

        if ($data->fails()) {
            return response()->json([
                'success' => false,
                'message' => $data->errors()
            ]);
        }

        $phone = $request->phone;

        $user = Auth::user();
        $naira_wallet = $user->nairaWallet;
        $balance = $naira_wallet->amount;
        $pin = $user->pin;
        $put_pin = $request->password;
        $hash = Hash::check($put_pin, $pin);

        if (!$hash) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Pin',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($request->amount > $balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($request->amount < 50) {
            return response()->json([
                'success' => false,
                'message' => 'Minimium amount is ₦50',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($request->amount > 25000) {
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
        $reference = rand(111111, 999999) . time();
        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->narration = $phone . ' ' . 'Payment for recharge card';
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
                'request_id' => $reference,
                'serviceID' => $request->service_id,
                'amount' => $request->amount,
                'phone' => $request->phone
            ]
        ]);
        $body = json_decode($response->getBody()->getContents(), true);

        // $data = '{"code":"000","content":{"transactions":{"status":"delivered","product_name":"Airtel Airtime VTU","unique_element":"09027452545","unit_price":50,"quantity":1,"service_verification":null,"channel":"api","commission":2,"total_amount":48,"discount":null,"type":"Airtime Recharge","email":"dantownrec2@gmail.com","phone":"09012435013","name":null,"convinience_fee":0,"amount":50,"platform":"api","method":"api","transactionId":"16326975138207511123747964"}},"response_description":"TRANSACTION SUCCESSFUL","requestId":"1491161632697512","amount":"50.00","transaction_date":{"date":"2021-09-27 00:05:13.000000","timezone_type":3,"timezone":"Africa\/Lagos"},"purchased_code":""}';

        // $body = json_decode($data,true);

        if ($body['code'] == 000) {
            $nt->status = 'success';
            $nt->charge = $body['content']['transactions']['commission'];
            $nt->save();

            $charge = 0;
            $amount = $request->amount;
            $total_charge = $amount + $charge;

            $extras = json_encode([
                'phone' => $phone
            ]);

            UtilityTransaction::create([
                'user_id'          => Auth::user()->id,
                'reference_id'     => $reference,
                'amount'           => $amount,
                'convenience_fee'  => $charge,
                'total'            => $total_charge,
                'type'             => 'Recharge card purchase',
                'status'           => 'success',
                'extras'           => $extras
            ]);

            $title = 'Recharge card purchase';
            $msg_body = 'Your Dantown wallet has been debited with N' . $request->amount . ' for recharge card purchase';

            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $msg_body,
            ]);

            $accountants = User::where(['role' => 777, 'status' => 'active'])->orWhere(['role' => 889, 'status' => 'active'])->get();
            $message = '!!! Utility Transaction Transaction !!!  A new Utility transaction has been initiated ';
            foreach ($accountants as $acct) {
                broadcast(new CustomNotification($acct, $message))->toOthers();
            }

            return response()->json([
                'success' => true,
                'response_description' => 'TRANSACTION SUCCESSFUL',
                'message' => 'Your airtime purchase to ' . $phone . ' was successful'
            ]);
        } elseif ($body['code'] == 016) {
            $nt->status = 'failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your recharge failed',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        } elseif ($body['code'] == 021) {
            $nt->status = 'failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is locked',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        } elseif ($body['code'] == 022) {
            $nt->status = 'failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is suspended',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        } elseif ($body['code'] == 024) {
            $nt->status = 'failed';
            $nt->save();
            $new_balance = $naira_wallet->update([
                "amount" => $balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        } else {
            $nt->status = 'failed';
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

    // Power Buy
    public function power()
    {
        $products = BillsPayment::getProducts();
        foreach ($products as $key => $value) {
            unset($products[$key]['minimium_amount']);
            unset($products[$key]['maximum_amount']);
            unset($products[$key]['convinience_fee']);
            unset($products[$key]['product_type']);
        }
        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function payElectricityVtpass(Request $r)
    {
        $charge = 0;
        $settings = GeneralSettings::getSetting('POWER_CONVENIENCE_FEE');
        if (!empty(($settings))) {
            $charge = $settings['settings_value'];
        }

        $data = Validator::make($r->all(), [
            'service_id' => 'required',
            'amount' => 'required',
            'pin' => 'required',
            'metre_type' => 'required',
            'metre_number'  => 'required',
            'email'  => 'required',
            'phone_number'  => 'required'
        ]);

        if ($data->fails()) {
            return response()->json([
                'success' => false,
                'message' => $data->errors()
            ]);
        }

        $user = Auth::user();
        $n = $user->nairaWallet;
        $balance = $n->amount;
        $pin = $user->pin;

        if (Hash::check($r->pin, $user->pin) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Pin',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($r->amount > $balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($r->amount < 100) {
            return response()->json([
                'success' => false,
                'message' => 'Minimium amount is ₦100',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($r->amount > 100000) {
            return response()->json([
                'success' => false,
                'message' => 'Maximium amount is ₦100000',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        $amount = $r->amount;

        $reference = rand(111111, 999999) . time();
        $postData['serviceID'] = $r->service_id;
        $postData['variation_code'] = $r->metre_type;
        $postData['amount'] = $r->amount;
        $postData['billersCode'] = $r->metre_number;
        $postData['phone'] = $r->phone_number;
        $postData['email'] = $r->email;
        $postData['request_id'] = $reference;

        $ch = curl_init(env('LIVE_VTPASS_PURCHASE_URL'));
        \curl_setopt_array($ch, [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => env('VTPASS_USERNAME') . ':' . env('VTPASS_PASSWORD'),
            CURLOPT_TIMEOUT => 120,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        // Test Response

        // $response = '{
        //     "code": "000",
        //     "content": {
        //         "transactions": {
        //             "status": "delivered",
        //             "product_name": "PHED - Port Harcourt Electric",
        //             "unique_element": "610124000952992",
        //             "unit_price": 100,
        //             "quantity": 1,
        //             "service_verification": null,
        //             "channel": "api",
        //             "commission": 2,
        //             "total_amount": 98,
        //             "discount": null,
        //             "type": "Electricity Bill",
        //             "email": "dantownrec2@gmail.com",
        //             "phone": "09012435013",
        //             "name": null,
        //             "convinience_fee": 0,
        //             "amount": 100,
        //             "platform": "api",
        //             "method": "api",
        //             "transactionId": "16371615982053468479917114"
        //         }
        //     },
        //     "response_description": "TRANSACTION SUCCESSFUL",
        //     "requestId": "9553101637161595",
        //     "amount": "100.00",
        //     "transaction_date": {
        //         "date": "2021-11-17 16:06:38.000000",
        //         "timezone_type": 3,
        //         "timezone": "Africa/Lagos"
        //     },
        //     "purchased_code": "Token : 41279616299856662444",
        //     "customerName": "NWOKO UDOBI DIMKPA",
        //     "address": "BLK B 152 NTA RD AFTER DO ",
        //     "meterNumber": "0124000952992",
        //     "customerNumber": "0124000952992",
        //     "token": "41279616299856662444",
        //     "tokenAmount": "100",
        //     "tokenValue": "93.02",
        //     "tariff": "56.79",
        //     "businessCenter": null,
        //     "exchangeReference": "1711202111954114",
        //     "units": "1.7",
        //     "energyAmt": "93.02",
        //     "vat": "6.98",
        //     "arrears": null,
        //     "revenueLoss": null
        // }';

        // $response = json_decode($response,true);

        if (isset($response['content']) && isset($response['content']['transactions'])) {
            if ($response['content']['transactions']['status'] == 'delivered') {
                $total_charge = $amount + $charge;

                $prev_bal = $n->amount;
                $n->amount -= $total_charge;
                $n->save();

                $nt = new NairaTransaction();
                $nt->reference = $reference;
                $nt->amount = $amount;
                $nt->user_id = Auth::user()->id;
                $nt->type = 'electricity bills';

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

                UtilityTransaction::create([
                    'user_id'          => Auth::user()->id,
                    'reference_id'     => $reference,
                    'amount'           => $amount,
                    'convenience_fee'  => $charge,
                    'total'            => $total_charge,
                    'type'             => 'Electricity purchase',
                    'status'           => 'success',
                    'extras'           => $extras
                ]);

                $accountants = User::where(['role' => 777, 'status' => 'active'])->orWhere(['role' => 889, 'status' => 'active'])->get();
                $message = '!!! Utility Transaction Transaction !!!  A new Utility transaction has been initiated ';
                foreach ($accountants as $acct) {
                    broadcast(new CustomNotification($acct, $message))->toOthers();
                }

                $phone = $r->phone_number;

                if (isset(Auth::user()->phone)) {
                    $client = new Client();
                    $url = env('TERMII_SMS_URL') . "/send";
                    $country = Country::find(Auth::user()->country_id);
                    $phone_number = $country->phonecode . $phone;

                    $response_sms = $client->request('POST', $url, [
                        'json' => [
                            'api_key' => env('TERMII_API_KEY'),
                            "type" => "plain",
                            "to" => $phone_number,
                            "from" => "N-Alert",
                            "channel" => "dnd",
                            "sms" => "Your electricity purchase from Dantown was successful. Token : " . $response['token'] . ", Units : " . $response['units'] . ", Reference code:" . $reference . "."
                        ],
                    ]);
                    $body = json_decode($response_sms->getBody()->getContents());
                }

                $title = 'Electricity purchase';
                $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for electricity recharge and N' . $charge . ' for convenience fee.
                Token: ' . $response['token'] . ',
                Unit: ' . $response['units'] . ',
                Reference code:' . $reference;

                $not = Notification::create([
                    'user_id' => Auth::user()->id,
                    'title' => $title,
                    'body' => $msg_body,
                ]);
                $body = 'Your Dantown wallet has been debited with N' . $amount . ' for electricity recharge and N'.$charge.' for convenience fee.<br><br>
                <b>Token: '.$response['token'].  '</b>,<br>
                <b>Unit: '. $response['units']. '</b>,<br>
                <b>Reference code:'. $reference;
                $btn_text = '';
                $btn_url = '';
        
                $name = (Auth::user()->first_name == " ") ? Auth::user()->username : Auth::user()->first_name;
                $name = explode(' ', $name);
                $firstname = ucfirst($name[0]);
                Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

                return response()->json([
                    'success' => true,
                    'response_description' => 'TRANSACTION SUCCESSFUL',
                    'message' => 'Purchase made successfully, Token : ' . $response['token']
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'An error occured, please try again',
                'response_description' => 'TRANSACTION FAILURE',
                'debug_response' => $response['response_description']
            ]);
        }
    }

    // Cable Buy
    public function cable()
    {
        $products = BillsPayment::getProducts("tv-subscription");
        foreach ($products as $key => $value) {
            unset($products[$key]['minimium_amount']);
            unset($products[$key]['maximum_amount']);
            unset($products[$key]['convinience_fee']);
            unset($products[$key]['product_type']);
        }

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function rechargeCable(Request $r)
    {
        $charge = 0;
        $settings = GeneralSettings::getSetting('CABLE_CONVENIENCE_FEE');
        if (!empty(($settings))) {
            // $charge = $settings['settings_value'];
        }

        $data = Validator::make($r->all(), [
            'cable_provider' => 'required',
            'subscription_plan' => 'required',
            'smartcard_number'  => 'required',
            'owner' => 'required',
            'pin' => 'required',
            'email'  => 'required',
            'phone_number'  => 'required'
        ]);

        $user = Auth::user();
        $n = $user->nairaWallet;
        $amount = $r->amount;
        $phone = $r->phone_number . '' . $r->phone;

        if ($data->fails()) {
            return response()->json([
                'success' => false,
                'message' => $data->errors()
            ]);
        }

        if (Hash::check($r->pin, $user->pin) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Pin',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        if ($amount > $n->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
                'response_description' => 'TRANSACTION FAILURE',
            ]);
        }

        $reference = rand(111111, 999999) . time();
        $postData['serviceID'] = $r->cable_provider;
        $postData['variation_code'] = $r->subscription_plan;
        $postData['billersCode'] = $r->smartcard_number;
        $postData['phone'] = $phone;
        $postData['email'] = $r->email;
        $postData['request_id'] = $reference;

        $response = $this->purchase($postData);

        if (isset($response['content']) && isset($response['content']['transactions'])) {
            if ($response['content']['transactions']['status'] == 'delivered') {
                $total_charge = $amount + $charge;

                $prev_bal = $n->amount;
                $n->amount -= $total_charge;
                $n->save();

                $nt = new NairaTransaction();
                $nt->reference = $reference;
                $nt->amount = $total_charge;
                $nt->user_id = Auth::user()->id;
                // $nt->type = 'cable';

                $nt->previous_balance = $prev_bal;
                $nt->current_balance = $n->amount;
                $nt->charge = $charge;
                $nt->transaction_type_id = 12;


                $nt->dr_user_id = Auth::user()->id;
                $nt->dr_wallet_id = $n->id;
                $nt->dr_acct_name = $n->account_name;
                $nt->cr_acct_name = $r->provider;
                $nt->narration = 'Payment for Cable Subscription';
                $nt->trans_msg = 'done';
                $nt->status = 'success';

                $extras = json_encode([
                    'type' => $response['content']['transactions']['product_name'],
                    'subscription_plan' => $r->subscription_plan,
                    'decoder_number' => $response['content']['transactions']['unique_element'],
                    'price' => $response['content']['transactions']['unit_price'],
                ]);

                $nt->extras = $extras;
                $nt->save();

                UtilityTransaction::create([
                    'user_id'          => Auth::user()->id,
                    'reference_id'     => $reference,
                    'amount'           => $amount,
                    'convenience_fee'  => $charge,
                    'total'            => $total_charge,
                    'type'             => 'Cable subscription',
                    'status'           => 'success',
                    'extras'           => $extras
                ]);

                $accountants = User::where(['role' => 777, 'status' => 'active'])->orWhere(['role' => 889, 'status' => 'active'])->get();
                $message = '!!! Utility Transaction Transaction !!!  A new Utility transaction has been initiated ';
                foreach ($accountants as $acct) {
                    broadcast(new CustomNotification($acct, $message))->toOthers();
                }

                $phone = $r->phone_number;

                if (isset(Auth::user()->phone)) {
                    $client = new Client();
                    $url = env('TERMII_SMS_URL') . "/send";
                    $country = Country::find(Auth::user()->country_id);
                    $phone_number = $country->phonecode . $phone;

                    $response_sms = $client->request('POST', $url, [
                        'json' => [
                            'api_key' => env('TERMII_API_KEY'),
                            "type" => "plain",
                            "to" => $phone_number,
                            "from" => "N-Alert",
                            "channel" => "dnd",
                            "sms" => "Your cable subscription from Dantown was successful."
                        ],
                    ]);
                    $body = json_decode($response_sms->getBody()->getContents());
                }

                $title = 'Cable subscription';
                $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for cable subscription and N' . $charge . ' for convenience fee.';

                $not = Notification::create([
                    'user_id' => Auth::user()->id,
                    'title' => $title,
                    'body' => $msg_body,
                ]);

                return response()->json([
                    'success' => true,
                    'response_description' => 'TRANSACTION SUCCESSFUL',
                    'message' => 'Your cable purchase was successful'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Oops! An error occured, please try again'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Oops! An error occured, please try again'
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
    //             'message' => 'Minimium amount is ₦25000',
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
