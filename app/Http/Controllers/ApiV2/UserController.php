<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LiveRateController;
use App\Http\Controllers\LoginSessionController;
use App\ImageSlide;
use App\NairaTransaction;
use App\Notification;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function nairaWalletBalance()
    {
        $wallet = Auth::user()->nairaWallet;




        $client = new Client();
        $url = "https://api.coinbase.com/v2/prices/spot?currency=USD";
        $res = $client->request('GET', $url);
        $res = json_decode($res->getBody());
        $btc_rate = $res->data->amount;

        $usdPerNairaRate = LiveRateController::usdNgn();

        $usd_value = $wallet->amount/$usdPerNairaRate;
        $btc_value = $usd_value/$btc_rate;

        return response()->json([
            'success' => true,
            'ngn_value' => (int)$wallet->amount,
            'btc_value' => number_format((float)$btc_value, 8),
            'usd_value' => (int)$usd_value
        ]);
    }

    public function netWalletBalance()
    {
        $wallet = Auth::user()->nairaWallet;

        $client = new Client();
        $url = "https://api.coinbase.com/v2/prices/spot?currency=USD";
        $res = $client->request('GET', $url);
        $res = json_decode($res->getBody());
        $btc_rate = $res->data->amount;

        $usdPerNairaRate = LiveRateController::usdNgn();

        $usd_value = $wallet->amount/$usdPerNairaRate;
        $btc_value = $usd_value/$btc_rate;

        $ngn_bal = (int)$wallet->amount;
        $ngn_btc_value = number_format((float)$btc_value, 8);
        $ngn_usd_value = (int)$usd_value;

        $client = new Client();
        $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $btc_real_time = $res->value;

        $url = env('TATUM_URL') . '/tatum/rate/USD?basePair=NGN';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $naira_usd_real_time = $res->value;

        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->btcWallet->account_id . '?pageSize=50';
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $accounts = json_decode($res->getBody(), true);

        if (empty($accounts)) {
            $btc_bal = 0; // (int)$wallet->amount;
            $btc_ngn_value = 0; //number_format((float)$btc_value, 8);
            $btc_usd_value = 0; //(int)$usd_value;
        }

        $btc_balance = $accounts['balance']['availableBalance'];

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $btc_balance;
        $btc_wallet->usd = $btc_wallet->balance * $btc_real_time;

        $naira_balance = $btc_wallet->usd * $naira_usd_real_time;

        return response()->json([
            'ngn_balance' => $naira_balance + $ngn_bal,
            'btc_balance' => $btc_balance + $ngn_btc_value,
            'usd_balance' => $btc_wallet->usd + $usd_value,
        ]);
    }

    public function dashboard()
    {
        $client = new Client();
        $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $btc_real_time = $res->value;

        $url = env('TATUM_URL') . '/tatum/rate/USD?basePair=NGN';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $naira_usd_real_time = $res->value;

        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->btcWallet->account_id . '?pageSize=50';
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $accounts = json_decode($res->getBody(), true);

        if (empty($accounts)) {
            return response()->json([
                'success' => false,
                'message' => 'btc wallet not found'
            ]);
        }

        $btc_balance = $accounts['balance']['availableBalance'];

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $btc_balance;
        $btc_wallet->usd = $btc_wallet->balance * $btc_real_time;

        $naira_balance = $btc_wallet->usd * $naira_usd_real_time;

        $res = file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,litecoin,ripple,tether&vs_currencies=ngn&include_24hr_change=true");

        $data = json_decode($res, true);

        $currencies = [
            [
                'name' => 'Tether',
                'short_name' => 'USDT',
                'rate' => $data['tether']['ngn'],
                '24h_change' => $data['tether']['ngn_24h_change'],
                'img' => url('/crypto/tether.png')
            ],
            [
                'name' => 'Bitcoin',
                'short_name' => 'BTC',
                'rate' => $data['bitcoin']['ngn'],
                '24h_change' => $data['bitcoin']['ngn_24h_change'],
                'img' => url('/crypto/bitcoin.png')
            ],
            [
                'name' => 'Ethereum',
                'short_name' => 'ETH',
                'rate' => $data['ethereum']['ngn'],
                '24h_change' => $data['ethereum']['ngn_24h_change'],
                'img' => url('/crypto/ethereum.png')
            ],
            [
                'name' => 'Litecoin',
                'short_name' => 'LTC',
                'rate' => $data['litecoin']['ngn'],
                '24h_change' => $data['litecoin']['ngn_24h_change'],
                'img' => url('/crypto/litecoin.png')
            ],
            [
                'name' => 'Ripple',
                'short_name' => 'XRP',
                'rate' => $data['ripple']['ngn'],
                '24h_change' => $data['ripple']['ngn_24h_change'],
                'img' => url('/crypto/xrp.png')
            ]
        ];

        $slides = array();
        $adImages = ImageSlide::latest()->get()->take(10);
        foreach($adImages as $image){
            array_push($slides, array("image" => url('storage/slider/'.$image->image)));
        }

        $loginSession = new LoginSessionController();
        $loginSession->FindSessionData(Auth::user()->id);
        
        return response()->json([
            'success' => true,
            'btc_balance' => $btc_balance,
            'btc_balance_in_naira' => $naira_balance,
            'btc_balnace_in_usd' => $btc_wallet->usd,
            'btc_rate' => $btc_real_time,
            'featured_coins' => $currencies,
            'advert_image' => $slides
        ]);
    }

    public function summary()
    {
        $pending_trans = NairaTransaction::where('user_id', Auth::user()->id)->where('status', 'pending')->count();
        $successful_trans = NairaTransaction::where('user_id', Auth::user()->id)->where('status', 'success')->count();
        $decline_trans = NairaTransaction::where('user_id', Auth::user()->id)->where('status', 'decline')->count();

        return response()->json([
            'pending_transaction' => $pending_trans,
            'successful_transaction' => $successful_trans,
            'decline_transaction' => $decline_trans,
        ]);
    }


//Level 2 Verification

    public function uploadAddress(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'image' => 'required|mimes:application/pdf,jpeg,bmp,png|max:5000',
            'location' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $user = Auth::user();

        if(Auth::user()->phone_verified_at == null){
            return response()->json([
                'success' => false,
                'msg' => 'Please verify your phone number first'
            ]);
        }

        if ($user->verifications()->where(['type' => 'Address', 'status' => 'Waiting'])->exists()) {
            return response()->json([
                'success' => false,
                'msg' => 'Address verification already in progress'
            ]);
        }

        $file = $r->image;
        $location = $r->location;
        $extension = $file->getClientOriginalExtension();
        $filenametostore =  $user->email . uniqid() . '.' . $extension;
        Storage::put('public/idcards/' . $filenametostore, fopen($file, 'r+'));
     
    

        Auth::user()->address_img = $filenametostore;
        Auth::user()->save();

        $user->verifications()->create([
            'path' => $filenametostore,
            'type' => 'Address',
            'status' => 'Waiting',
            'location' => $location
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Address uploaded'
        ]);
    }





    //Level 3 Verification Begins Here

    public function uploadId(Request $r)
    {

        $validator = Validator::make($r->all(), [
            'image' => 'required|mimes:application/pdf,jpeg,bmp,png|max:5000',
            'id_number' => 'required',
            'id_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'msg' => $validator->errors(),
            ], 401);
        }
     
        if(Auth::user()->phone_verified_at == null){
            return response()->json([
                'success' => false,
                'msg' => 'Please verify your phone number first'
            ]);
        }
         

        if(Auth::user()->address_verified_at == null){
            return response()->json([
                'success' => false,
                'msg' => 'Please verify your address first'
            ]);
        }

        $user = Auth::user();
        

        if ($user->verifications()->where(['type' => 'ID Card', 'status' => 'Waiting'])->exists()) {
            return response()->json([
                'success' => false,
                'msg' => 'ID Card verification already in progress'
            ]);
        }

  

       
            $file = $r->image;
            $idtype = $r->idtype;
            $id_number = $r->id_number;
            $extension = $file->getClientOriginalExtension();
            $filenametostore =  $user->email . uniqid() . '.' . $extension;
            Storage::put('public/idcards/' . $filenametostore, fopen($file, 'r+'));
         
            

            Auth::user()->id_card = $filenametostore;
            Auth::user()->save();

            $user->verifications()->create([
                'path' => $filenametostore,
                'type' => 'ID Card',
                'status' => 'Waiting',
                'id_type' => $idtype,
                'id_number' => $id_number

            ]);

            return response()->json([
                'success' => true,
                'msg' => 'You have successfully uploaded your verification.',
            ]);
     


    }

  

    public function updateDp(Request $r)
    {

        if ($r->has('image')) {
            $file = $r->image;
            $folderPath = public_path('storage/avatar/');

            $extension = $file->getClientOriginalExtension();
            $filenametostore = time() . uniqid() . '.' . $extension;
            Storage::put('public/avatar/' . $filenametostore, fopen($file, 'r+'));
            $image_base64 = base64_decode($file);

            $imageName = $filenametostore;
            $imageFullPath = $folderPath . $imageName;

            // file_put_contents($imageFullPath, $image_base64);


            ////////////////////

            Auth::user()->dp = $filenametostore;
            Auth::user()->save();

            return response()->json([
                'success' => true,
                'data' => Auth::user(),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Image file not present'
            ]);
        }
    }

    public function updateBirthday(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'day' => 'required',
            'month' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $user = Auth::user();
        $user->birthday = $r->day . '/' . $r->month;
        $user->save();


        return response()->json([
            'success' => true,
            'msg' => 'Your birthday was updated successfully'
        ]);
    }

    public function profile()
    {
        $client = new Client();
        $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $btc_real_time = $res->value;

        $url = env('TATUM_URL') . '/tatum/rate/USD?basePair=NGN';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $naira_usd_real_time = $res->value;

        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->btcWallet->account_id . '?pageSize=50';
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $accounts = json_decode($res->getBody(), true);

        if (empty($accounts)) {
            return response()->json([
                'success' => false,
                'message' => 'btc wallet not found'
            ]);
        }

        $btc_balance = $accounts['balance']['availableBalance'];

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $btc_balance;
        $btc_wallet->usd = $btc_wallet->balance * $btc_real_time;

        $naira_balance = $btc_wallet->usd * $naira_usd_real_time;

        $res = file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,litecoin,ripple,tether&vs_currencies=ngn&include_24hr_change=true");

        return response()->json([
            'success' => true,
            'user' => Auth::user(),
            'btc_balance' => $btc_balance,
            'btc_balance_in_naira' => $naira_balance,
            'btc_balnace_in_usd' => $btc_wallet->usd,
            'btc_rate' => $btc_real_time,
        ]);
    }
}
