<?php

namespace App\Http\Controllers\ApiV2;

use App\Account;
use App\Bank;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CryptoHelperController;
use App\Http\Controllers\LiveRateController;
use App\Http\Controllers\LoginSessionController;
use App\ImageSlide;
use App\NairaTransaction;
use App\Notification;
use App\Transaction;
use App\User;
use App\VerificationLimit;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
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

        $usd_value = $wallet->amount / $usdPerNairaRate;
        $btc_value = $usd_value / $btc_rate;

        return response()->json([
            'success' => true,
            'ngn_value' => (int) $wallet->amount,
            'btc_value' => number_format((float) $btc_value, 8),
            'usd_value' => (int) $usd_value,
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

        $usd_value = $wallet->amount / $usdPerNairaRate;
        $btc_value = $usd_value / $btc_rate;

        $ngn_bal = (int) $wallet->amount;
        $ngn_btc_value = number_format((float) $btc_value, 8);
        $ngn_usd_value = (int) $usd_value;

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
            'headers' => ['x-api-key' => env('TATUM_KEY')],
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

    public function allBalance()
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
        $response = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
        ]);
        $accounts = json_decode($response->getBody(), true);

        if (empty($accounts)) {
            return response()->json([
                'success' => false,
                'message' => 'btc wallet not found',
            ]);
        }

        $btc_balance = $accounts['balance']['availableBalance'];

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $btc_balance;
        $btc_wallet->usd = $btc_wallet->balance * $btc_real_time;

        $naira_balance = $btc_wallet->usd * $naira_usd_real_time;

        return response()->json([
            'success' => true,
            'btc_balance' => $btc_balance,
            'btc_balance_in_naira' => $naira_balance,
            'btc_balance_in_usd' => $btc_wallet->usd,
            'total' => $naira_balance,

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
            'headers' => ['x-api-key' => env('TATUM_KEY')],
        ]);
        $accounts = json_decode($res->getBody(), true);

        if (empty($accounts)) {
            return response()->json([
                'success' => false,
                'message' => 'btc wallet not found',
            ]);
        }

        $btc_balance = $accounts['balance']['availableBalance'];

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $btc_balance;
        $btc_wallet->usd = $btc_wallet->balance * $btc_real_time;

        $naira_balance = $btc_wallet->usd * $naira_usd_real_time;

        function curl_get_contents($url)
        {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        // No longer needed
        // $url = "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,litecoin,ripple,tether&vs_currencies=ngn&include_24hr_change=true";
        // $data = json_decode(curl_get_contents($url), true);

        // $currencies = [
        //     [
        //         'name' => 'Tether',
        //         'short_name' => 'USDT',
        //         'rate' => $data['tether']['ngn'],
        //         '24h_change' => $data['tether']['ngn_24h_change'],
        //         'img' => url('/crypto/tether.png'),
        //     ],
        //     [
        //         'name' => 'Bitcoin',
        //         'short_name' => 'BTC',
        //         'rate' => $data['bitcoin']['ngn'],
        //         '24h_change' => $data['bitcoin']['ngn_24h_change'],
        //         'img' => url('/crypto/bitcoin.png'),
        //     ],
        //     [
        //         'name' => 'Ethereum',
        //         'short_name' => 'ETH',
        //         'rate' => $data['ethereum']['ngn'],
        //         '24h_change' => $data['ethereum']['ngn_24h_change'],
        //         'img' => url('/crypto/ethereum.png'),
        //     ],
        //     [
        //         'name' => 'Litecoin',
        //         'short_name' => 'LTC',
        //         'rate' => $data['litecoin']['ngn'],
        //         '24h_change' => $data['litecoin']['ngn_24h_change'],
        //         'img' => url('/crypto/litecoin.png'),
        //     ],
        //     [
        //         'name' => 'Ripple',
        //         'short_name' => 'XRP',
        //         'rate' => $data['ripple']['ngn'],
        //         '24h_change' => $data['ripple']['ngn_24h_change'],
        //         'img' => url('/crypto/xrp.png'),
        //     ],
        // ];

        // //Cache for 15 minutes

        // Cache::put('coin', $currencies, 900);
        // $newFeatured = Cache::get('coin');

        $notify = array();
        $notifications = Notification::where('user_id', 0)->latest()->get()->take(5);
        foreach ($notifications as $body) {
            array_push($notify, array("notify" => $body->body));

        }

        $slides = array();
        $adImages = ImageSlide::latest()->get()->take(10);
        foreach ($adImages as $image) {
            array_push($slides, array("image" => url('storage/slider/' . $image->image)));
        }

        $loginSession = new LoginSessionController();
        $loginSession->FindSessionData(Auth::user()->id);

        $apkcurrent = "2.0.0";
        $apkstable = "2.0.0";

        $ioscurrent = "2.0.0";
        $iosstable = "2.0.0";

        $versions = [
            [

                'apkcurrent' => $apkcurrent,
                'apkstable' => $apkstable,
                'ioscurrent' => $ioscurrent,
                'iosstable' => $iosstable,

            ],
        ];
         // Total Balance
         $usdt_wallet =  Auth::user()->usdtWallet;
         $nairaWallet_balance = Auth::user()->nairaWallet->amount;

         $usdt =  $usdt_wallet ? $usdt_wallet->usd : 0 ;
         $btc =  $btc_wallet ? $btc_wallet->usd : 0;
         $naira_in_usd =  LiveRateController::usdNgn(false);

         $user_naira_wallet_balance_in_usd = $nairaWallet_balance / $naira_in_usd;
         // add user naira balance , btc balance and usdt balance
         $total_user_balance_in_usd = $usdt + $btc + $user_naira_wallet_balance_in_usd;

         // convert  user dollars balance to BTC

         $user_total_balance_in_btc = $total_user_balance_in_usd / $btc_real_time;

        return response()->json([
            'success' => true,
            'total_balances_in_btc' => $user_total_balance_in_btc,
            'btc_balance' => $btc_balance,
            'btc_balance_in_naira' => $naira_balance,
            'btc_balance_in_usd' => $btc_wallet->usd,
            'btc_rate' => $btc_real_time,
            'advert_image' => $slides,
            'notifications' => $notify,
            'version' => $versions,

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
            'image' => 'required',
            'location' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $user = Auth::user();

        if (Auth::user()->phone_verified_at == null) {
            return response()->json([
                'success' => false,
                'msg' => 'Please verify your phone number first',
            ]);
        }

        if ($user->verifications()->where(['type' => 'Address', 'status' => 'Waiting'])->exists()) {
            return response()->json([
                'success' => false,
                'msg' => 'Address verification already in progress',
            ]);
        }

        if ($r->has('image')) {
            $file = $r->image;
            $location = $r->location;
            $folderPath = storage_path('app/public/address/');

            if (!File::isDirectory($folderPath)) {

                File::makeDirectory($folderPath, 0777, true, true);

            }

            $image_base64 = base64_decode($file);

            $imageName = time() . uniqid() . '.png';
            $imageFullPath = $folderPath . $imageName;

            file_put_contents($imageFullPath, $image_base64);

            Auth::user()->address_img = $imageName;
            Auth::user()->save();

            $user->verifications()->create([
                'path' => $imageName,
                'type' => 'Address',
                'status' => 'Waiting',
                'location' => $location,
            ]);

            return response()->json([
                'success' => true,
                'data' => 'You have successfully Address for verification.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Image file not present',
            ]);
        }

    }

    //Level 3 Verification Begins Here

    public function uploadId(Request $r)
    {

        $validator = Validator::make($r->all(), [
            'image' => 'required',
            'id_number' => 'required',
            'id_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'msg' => $validator->errors(),
            ], 401);
        }

        if (Auth::user()->phone_verified_at == null) {
            return response()->json([
                'success' => false,
                'msg' => 'Please verify your phone number first',
            ]);
        }

        if (Auth::user()->address_verified_at == null) {
            return response()->json([
                'success' => false,
                'msg' => 'Please verify your address first',
            ]);
        }

        $user = Auth::user();

        if ($user->verifications()->where(['type' => 'ID Card', 'status' => 'Waiting'])->exists()) {
            return response()->json([
                'success' => false,
                'msg' => 'ID Card verification already in progress',
            ]);
        }

        if ($r->has('image')) {
            $file = $r->image;
            $idtype = $r->idtype;
            $id_number = $r->id_number;
            $folderPath = storage_path('app/public/idcards');
            if (!File::isDirectory($folderPath)) {

                File::makeDirectory($folderPath, 0777, true, true);

            }
            $image_base64 = base64_decode($file);

            $imageName = time() . uniqid() . '.png';
            $imageFullPath = $folderPath . $imageName;

            file_put_contents($imageFullPath, $image_base64);

            Auth::user()->id_card = $imageName;
            Auth::user()->save();

            $user->verifications()->create([
                'path' => $imageName,
                'type' => 'ID Card',
                'status' => 'Waiting',
                'id_type' => $idtype,
                'id_number' => $id_number,
            ]);

            return response()->json([
                'success' => true,
                'data' => 'You have successfully uploaded your verification.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Image file not present',
            ]);
        }

    }

    //Level 3 Verification Begins Here

    public function updateDp(Request $r)
    {

        if ($r->has('image')) {
            $file = $r->image;

            $folderPath = storage_path('app/public/avatar/');
            if (!File::isDirectory($folderPath)) {

                File::makeDirectory($folderPath, 0777, true, true);

            }
            $image_base64 = base64_decode($file);

            $imageName = time() . uniqid() . '.png';
            $imageFullPath = $folderPath . $imageName;

            file_put_contents($imageFullPath, $image_base64);

            Auth::user()->dp = $imageName;
            Auth::user()->save();

            return response()->json([
                'success' => true,
                'data' => 'You have successfully uploaded image.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Image file not present',
            ]);
        }

    }

    public function crypto()
    {

        $cryptoTran = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1)->where('user_id', Auth::user()->id);
        })->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $cryptoTran,
        ]);

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
            'msg' => 'Your birthday was updated successfully',
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
            'headers' => ['x-api-key' => env('TATUM_KEY')],
        ]);
        $accounts = json_decode($res->getBody(), true);

        if (empty($accounts)) {
            return response()->json([
                'success' => false,
                'message' => 'btc wallet not found',
            ]);
        }

        $btc_balance = $accounts['balance']['availableBalance'];

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $btc_balance;
        $btc_wallet->usd = $btc_wallet->balance * $btc_real_time;

        $naira_balance = $btc_wallet->usd * $naira_usd_real_time;

        // $res = file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,litecoin,ripple,tether&vs_currencies=ngn&include_24hr_change=true");

        return response()->json([
            'success' => true,
            'user' => Auth::user(),
            'btc_balance' => $btc_balance,
            'btc_balance_in_naira' => $naira_balance,
            'btc_balnace_in_usd' => $btc_wallet->usd,
            'btc_rate' => $btc_real_time,
        ]);
    }
    public function listOfBanks()
    {
        $banks = Bank::All();

        return response()->json([
            'success' => true,
            'data' => $banks,
        ]);

    }

    public function deleteBankAccount($id)
    {

        $bank = Account::find($id);
        if ($bank->user_id != Auth::user()->id) {
            return response()->json([
                'success' => false,
                'msg' => 'Not authorised',
            ]);
        } else {
            $bank->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Account detail successfully added',
            ]);
        }
    }

    public function userAccounts()
    {

        $accounts = Auth::user()->accounts;
        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);

    }

    //Verify Account

    public function verifyBankName(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'bank_code' => 'required',
            'account_number' => 'required| min:10 | max:10',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'msg' => $validator->errors(),
            ], 401);
        }

        $bank_code = $request->bank_code;
        $acct_number = $request->account_number;

        $checker = Bank::where('code', $request->bank_code)->first();

        // dd($checker);
        if (!$checker) {
            return response()->json([
                'success' => false,
                'message' => 'Bank doesn\'t exist',
            ]);
        }

        $client = new Client();
        $url = 'https://app.nuban.com.ng/api/NUBAN-AGBCLVUL544?acc_no=' . $acct_number . '&bank_code=' . $bank_code;
        $response = $client->request('GET', $url);
        $body = ($response->getBody()->getContents());
        $body = json_decode($body);

        if (isset($body->error)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid Account Details',
            ]);
        }
        $acct_name = $body[0]->account_name;
        $data = [
            'account_name' => $acct_name,
        ];
        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);

    }

    //New Way

    public function addBankAccDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_name' => 'required',
            'bank_code' => 'required',
            'account_number' => 'required| min:10',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $bank = Bank::where('code', $request->bank_code)->first();

        if ($bank == null) {
            return response()->json([
                'success' => false,
                'msg' => 'Incorrect Bank',
            ]);
        }


        $addNew = new Account();
        $addNew->user_id = Auth::user()->id;
        $addNew->account_name = $request->account_name;
        $addNew->bank_name = $bank->name;
        $addNew->bank_id = $bank->id;
        $addNew->account_number = $request->account_number;

        $duplicateChecker = Account::where('user_id', Auth::user()->id)->where('bank_id', $addNew->bank_id)->first();

        //    dd($duplicateChecker);
        if ($duplicateChecker != null) {

            return response()->json([
                'success' => false,
                'msg' => 'Account Already added',
            ]);

        }

        $addNew->save();


        $updated = explode(' ', trim($request->account_name));

        Auth::user()->first_name = $updated[0];
        Auth::user()->last_name = strstr($request->bank_name, " ");
        Auth::user()->save();

        return response()->json([
            'success' => true,
            'user' => Auth::user(),
            'bank_accounts' => Auth::user()->accounts,
            'naira_wallet' => Auth::user()->nairaWallet,
        ]);

    }

    // OLd Way

    public function addBankAccount(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'account_name' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required| min:10',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'msg' => $validator->errors(),
            ], 401);
        }

        $addNew = new Account();
        $bank = Bank::where('code', $request->bank_code)->first();
        $addNew->user_id = Auth::user()->id;
        $addNew->account_name = $request->account_name;
        $addNew->bank_name = $bank->name;
        $addNew->bank_id = $bank->id;
        $addNew->account_number = $request->account_number;
        $addNew->save();

        return response()->json([
            'success' => true,
            'msg' => 'Account added successfully',
        ]);

    }

    public function deleteUserAccount(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (Hash::check($request->password, Auth::user()->password) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Password. Please try again.',
            ]);
        }

        $updateStatus = User::where('id', Auth::user()->id)->update(['is_deleted' => 1]);

        if ($updateStatus) {
            return response()->json([
                'success' => true,
                'msg' => 'Account deleted successfully',
            ]);

        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid Operation',
            ]);
        }

    }

    public function userVerification()
    {

        $verifyLimit = VerificationLimit::All();

        return response()->json([
            'success' => true,
            'data' => $verifyLimit,
        ]);
    }

    public function userNotify(Request $r)
    {
        $month = $r->input('month');
        if ($month) {
            $notifications = Auth::user()->notifications()->whereMonth('created_at', $month)->get();
        } else {
            $notifications = Auth::user()->notifications()->get();
        }

        return response()->json([
            'success' => true,
            'time' => $month,
            'notification' => $notifications,
        ]);
    }

    public function newNotify($id)
    {
        $notify = Auth::user()->notifications->where('id', $id)->first();

        if ($notify) {
            $notify->is_seen = 1;
            $notify->save();
            return response()->json([
                'success' => true,
                'notification' => $notify,

            ]);

        } else {
            return response()->json([
                'success' => false,
                'msg' => 'invalid id',

            ]);

        }

    }

    public function clearAllNotify()
    {
        $clearAll = Auth::user()->notifications->where('user_id', Auth::id())->all();
        foreach ($clearAll as $notify) {
            $notify->is_cleared = 1;
            $notify->save();
        }
        return response()->json([
            'success' => true,
            'cleared' => 1,

        ]);
    }

    public function markAllNotify()
    {
        $markAll = Auth::user()->notifications->where('user_id', Auth::user()->id);
        foreach ($markAll as $notify) {
            $notify->is_seen = 1;
            $notify->save();
        }
        return response()->json([
            'success' => true,
            'allread' => 1,

        ]);
    }

}

