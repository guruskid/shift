<?php

namespace App\Http\Controllers;

use Nahid\Talk\Facades\Talk;
use App\Account;
use App\Bank;
use Illuminate\Support\Facades\Auth;
use App\Card;
use App\Notification;
use App\Rate;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use App\Charts\UserChart;
use App\Events\NewTransaction;
use App\Mail\DantownNotification;
use App\Mail\GeneralTemplateOne;
use App\NairaTransaction;
use App\NairaWallet;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //add the auth and user middleware

    public function dashboard()
    {
        if (!Auth::user()->notificationsetting) {
            Auth::user()->notificationSetting()->create();
        }
        //$this->verificationParogress();
        \Artisan::call('naira:limit');
        $s = Auth::user()->transactions->where('status', 'success')->count();
        $w = Auth::user()->transactions->where('status', 'waiting')->count();
        $p = Auth::user()->transactions->where('status', 'in progress')->count();
        $d = Auth::user()->transactions->where('status', 'declined')->count();

        $borderColors = [
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"
        ];
        $fillColors = [
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"

        ];
        $usersChart = new UserChart;
        $usersChart->minimalist(true);
        $usersChart->labels(['Successful', 'Declined', 'Waiting']);
        $usersChart->dataset('Users by trimester', 'doughnut', [ $s, $d, $w])
            ->color($borderColors)
            ->backgroundcolor($fillColors);

        $transactions = Auth::user()->transactions->take(3);
        foreach ($transactions as $t) {
            $t->created_ats = $t->created_at->format('d M h:ia');
            $t->amount_paids = number_format($t->amount_paid);
            if ($t->status == 'approved') {
                $t->stats = 'success';
            } else {
                $t->stats = $t->status;
            }
        }
        $segment = 'All';

        $notifications = Notification::where('user_id', 0)->orderBy('created_at', 'desc')->get();
        $naira_balance = 0;
        if (Auth::user()->nairaWallet) {
            $naira_balance = Auth::user()->nairaWallet->amount;
        }

        return view('newpages.dashboard', compact(['transactions', 's', 'w', 'p', 'd', 'notifications', 'usersChart', 'naira_balance']));
    }

    public function getUser($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json([
                'success' => true,
                'user' => $user->first_name . ' '.$user->last_name
            ]);
        }

        return response()->json([
            'success' => false,
            'msg' => "User not found"
        ]);
    }


    /* Profile ajax functions */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $user->phone = $request->phone;

        return response()->json($user->save());
    }

    public function updateBank(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //         'account_name' => 'required',
        //         'name' => 'required',
        //         'account_number' => 'required',

        //     ]);

        //     if ($validator->fails()) {
        //         return response()->json([
        //             'success' => false,
        //             'msg' => $validator->errors(),
        //         ], 401);
        //     }


        // if (Auth::user()->phone_verified_at == null) {

            // $validator = Validator::make($request->all(), [
            //     'phone' => 'required',
            //     'otp' => 'required',
            // ]);
            // if ($validator->fails()) {
            //     return response()->json([
            //         'success' => false,
            //         'msg' => $validator->errors(),
            //     ], 401);
            // }

            // try {
            //     $client = new Client();
            //     $url = env('TERMII_SMS_URL') . "/otp/verify";

            //     $response = $client->request('POST', $url, [
            //         'json' => [
            //             'api_key' => env('TERMII_API_KEY'),
            //             "pin_id" => Auth::user()->phone_pin_id,
            //             "pin" => $request->otp
            //         ],
            //     ]);
            //     $body = json_decode($response->getBody()->getContents());

            //     if (!$body->verified || $body->verified != 'true') {
            //         return response()->json([
            //             'success' => false,
            //             'msg' => 'Phone verification failed. Please request for a new OTP'
            //         ]);
            //     }
            // } catch (\Exception $e) {
            //     //report($e);
            //     return response()->json([
            //         'success' => false,
            //         'msg' => 'Phone verification failed. Please request new OTP'
            //     ]);
            // }

            // Auth::user()->phone_verified_at = now();
            // Auth::user()->save();

            // \Artisan::call('naira:limit');
        // }

        $a = new Account();
        $bank = Bank::where('code', $request->bank_code)->first();
        $a->user_id = Auth::user()->id;
        $a->account_name = $request->account_name;
        $a->bank_name = $bank->name;
        $a->bank_id = $bank->id;
        $a->account_number = $request->account_number;
        $a->save();

        Auth::user()->first_name = $request->account_name;
        Auth::user()->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function getBank($id)
    {
        $bank = Account::find($id);
        if ($bank->user_id != Auth::user()->id) {
            return response()->json('Invalid operation');
        } else {
            return response()->json($bank);
        }
    }

    public function deleteBank($id)
    {
        $bank = Account::find($id);
        if ($bank->user_id != Auth::user()->id) {
            return response()->json('Invalid operation');
        } else {
            $bank->delete();
            return response()->json('Bank details deleted');
        }
    }


    /* Ajax functions end here */

    public function rates()
    {
        $buy = Rate::where('rate_type', 'buy')->orderBy('created_at', 'desc')->get();
        $sell = Rate::where('rate_type', 'sell')->orderBy('created_at', 'desc')->get();

        return view('user.rates', compact(['sell', 'buy']));
    }

    public function account()
    {

        $v_progress = 0;
        if (Auth::user()->email_verified_at && Auth::user()->phone_verified_at) {
            $v_progress += 25;
        }
        if (Auth::user()->bvn_verified_at) {
            $v_progress += 25;
        }
        if (Auth::user()->address_verified_at) {
            $v_progress += 25;
        }
        if (Auth::user()->idcard_verified_at) {
            $v_progress += 25;
        }

        Auth::user()->v_progress = $v_progress;
        Auth::user()->save();

        \Artisan::call('naira:limit');
        return view('newpages.profile');
    }


    public function profilePicture(Request $request)
    {

        $this->validate($request, [
            'dp' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048|required',
        ]);
        $user = Auth::user();
        $file = $request->dp;
        $extension = $file->getClientOriginalExtension();
        $filenametostore = $user->email . '.' . $extension;
        Storage::put('public/avatar/' . $filenametostore, fopen($file, 'r+'));
        //Resize image here
        $thumbnailpath = 'storage/avatar/' . $filenametostore;
        $img = Image::make($thumbnailpath)->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->crop(300, 300, 0, 0);
        $img->save($thumbnailpath);
        $user->dp = $filenametostore;
        $user->save();
        return redirect()->back()->with(['success' => 'Profile updated']);
    }


    public function password(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|confirmed|min:6|different:old_password'
        ]);

        if (Hash::check($request->old_password, Auth::user()->password) == false) {
            return redirect()->back()->with(['error' => 'Your current password does not match with the password you provided. Please try again.']);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with("success", "Password changed");
    }

    public function resetEmail(Request $r)
    {
        $r->validate([
            'password' => 'required',
            'new_email' => 'required|email|unique:users,email'
        ]);

        if (Hash::check($r->password, Auth::user()->password) == false) {
            return redirect()->back()->with(['error' => 'Your current password does not match with the password you provided. Please try again.']);
        }

        Auth::user()->email = $r->new_email;
        Auth::user()->email_verified_at = null;
        Auth::user()->save();
        return redirect()->back()->with("success", "Email changed");
    }

    public function transactions(Request $r)
    {
        if ($r->has('start_date') || $r->has('end_date') ) {
            $transactions = Auth::user()->transactions()->where('created_at', '>=', $r->start_date)
            ->where('created_at', '<=', $r->end_date)->latest()->get();
        }else{
            $transactions = Auth::user()->transactions()->paginate(10);
        }
        foreach ($transactions as $t) {
            $t->created_ats = $t->created_at->format('d M Y h:i a');
            $t->amount_paids = number_format($t->amount_paid);
            if ($t->status == 'approved') {
                $t->status = 'success';
            } else {
                $t->stats = $t->status;
            }
        }
        $segment = 'All';
        $value = Auth::user()->transactions->sum('amount');
        $amount = Auth::user()->transactions->sum('amount_paid');

        return view('newpages.all-transactions', compact(['transactions', 'segment', 'value', 'amount']));
    }

    public function addTransaction(Request $r)
    {

        if ($r->rate_type == 'buy') {
            if (!Auth::user()->nairaWallet || $r->pay_with != 'wallet') {
                return response()->json([
                    'success' => false,
                    'msg' => 'Please create a Dantown wallet before initiating a buy transaction',
                ]);
            }
        }

        $country = '';
        $tmp_amount = $r->amount;
        $equiv = 0;
        $status = 'waiting';

        if ($r->country == 'ngn') {
            /* convert naira to dollar to get the range */
            $tmp_amount = 10;
            $country = 'USD';
        } else {
            $country = $r->country;
        }

        $rate = Rate::where('card', $r->card)->where('rate_type', $r->rate_type)
            ->where('min', '<=', $tmp_amount)->where('max', '>=', $tmp_amount)->value($country);


        /* if it is a crypto and ngn / to get the equivalent in btc or eth */
        if ($r->country == 'ngn') {
            $value = $r->amount;
        } else {
            $value = $rate * $r->amount;
        }

        $online_agent = User::where('role', 888)->where('status', 'active')->first();
        if (!$online_agent) {
            $online_agent = User::where('role', 999)->first();
        }

        if ($value == $r->amount_paid) {
            if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 3 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 3) {
                return response()->json([
                    'success' => false,
                    'msg' => 'You cant initiate a new transaction with more than 3 waiting or processing transactions',
                ]);
            }

            if ($r->pay_with == 'wallet' && $r->rate_type == 'buy') {
                if (Auth::user()->nairaWallet->amount > $r->amount_paid) {
                    $prev_bal = Auth::user()->nairaWallet->amount;
                    Auth::user()->nairaWallet->amount -= $r->amount_paid;
                    Auth::user()->nairaWallet->save();
                    $status = 'success';
                } else {
                    return response()->json([
                        'success' => false,
                        'msg' => 'Insufficient wallet balance to complete this transaction ',
                    ]);
                }
            }

            $card_id = Card::where('name', $r->card)->first()->id;


            $t = new Transaction();
            $t->uid = uniqid();
            $t->user_email = Auth::user()->email;
            $t->user_id = Auth::user()->id;
            $t->card = $r->card;
            $t->card_id = $card_id;
            $t->type = $r->rate_type;
            $t->country = $r->country;
            $t->amount = $r->amount;
            $t->amount_paid = $r->amount_paid;
            $t->agent_id = $online_agent->id;
            $t->wallet_id = $r->wallet_id;
            $t->status = $status;
            $t->save();

            $t->user = $t->user;

            broadcast(new NewTransaction($t))->toOthers();

            if ($r->pay_with == 'wallet' && $r->rate_type == 'buy') {


                $reference = \Str::random(2) . '-' . $t->id;
                $n = NairaWallet::find(1);

                $nt = new NairaTransaction();
                $nt->reference = $reference;
                $nt->amount = $r->amount_paid;
                $nt->user_id = Auth::user()->id;
                $nt->type = 'naira wallet';


                $nt->previous_balance = $prev_bal;
                $nt->current_balance = Auth::user()->nairaWallet->amount;
                $nt->charge = 0;
                $nt->transaction_type_id = 5;


                $nt->cr_wallet_id = $n->id;
                $nt->dr_wallet_id = Auth::user()->nairaWallet->id;
                $nt->cr_acct_name = 'Dantown';
                $nt->dr_acct_name = $t->user->first_name . ' ' . $t->user->last_name;
                $nt->narration = 'Debit for buy transaction with id ' . $t->uid;
                $nt->trans_msg = 'This transaction was handled automatically ';
                $nt->dr_user_id = $t->user->id;
                $nt->cr_user_id = 1;
                $nt->status = 'success';
                $nt->save();
            }

            $title = ucwords($t->type).' '.$t->card;
            $body = 'Your order to ' . $t->type.' '.$t->card.' worth of ₦'.number_format($t->amount_paid).' has been initiated successfully';
            $not = Notification::create([
                'user_id' => Auth::user()->id,
                'title' => $title,
                'body' => $body,
            ]);
            if (Auth::user()->notificationSetting->trade_email == 1) {
                Mail::to(Auth::user()->email)->send(new DantownNotification($title, $body, 'Transaction History', route('user.transactions')));


                $title = 'Transaction Pending';

            $btn_text = '';
            $btn_url = '';

            $name = (Auth::user()->first_name == " ") ? Auth::user()->username : Auth::user()->first_name;
            $name = str_replace(' ', '', $name);
            $firstname = ucfirst($name);
            Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));


            }

            return response()->json(['success' => true, 'data' => $t]);
        }

        return response()->json([
            'success' => false,
            'msg' => 'Something seems wrong, please input your trade details and try again',
        ]);

    }

    public function viewTransac($id, $uid)
    {
        $transaction = Auth::user()->transactions()->where('id', $id)->first();

        return view('user.transaction', compact(['transaction']));
    }



    public function updateBankDetails(Request $request)
    {

        // dd('something here');
        $a = Account::find($request->id);
        if ($a->user_id != Auth::user()->id) {
            return redirect()->back()->with(["error" => 'Invalid Operation']);
        }
        $a->account_name = $request->account_name;
        $a->bank_name = $request->bank_name;
        $a->account_number = $request->account_number;
        $a->save();

        return redirect()->back()->with(["success" => 'Details updated']);
    }


    public function notifications(Request $request)
    {
        $month =  $request->input('month');
        if ($month) {
            $notifications = Auth::user()->notifications()->whereMonth('created_at', $month)->paginate(10);
        }else{
            $notifications = Auth::user()->notifications()->paginate(10);
        }

        return view('newpages.notifications', compact('notifications', 'month'));
    }


    public function readNot($id)
    {
        $n = Auth::user()->notifications->where('id', $id)->first();
        $n->is_seen = 1;
        $n->save();
        return response()->json(['success' => true]);
    }

    public function notificationSetting(Request $r)
    {
        $v = $r->value;
        switch ($r->name) {
            case 'w-s':
                Auth::user()->notificationSetting->wallet_sms = $v;
                break;
            case 'w-e':
                Auth::user()->notificationSetting->wallet_email = $v;
                break;
            case 't-s':
                Auth::user()->notificationSetting->trade_sms = $v;
                break;
            case 't-e':
                Auth::user()->notificationSetting->trade_email = $v;
                break;
            //Mobile
            case 'w-s2':
                Auth::user()->notificationSetting->wallet_sms = $v;
                break;
            case 'w-e2':
                Auth::user()->notificationSetting->wallet_email = $v;
                break;
            case 't-s2':
                Auth::user()->notificationSetting->trade_sms = $v;
                break;
            case 't-e2':
                Auth::user()->notificationSetting->trade_email = $v;
                break;

            default:
                return response()->json(["success" => false]);
                break;
        }

        Auth::user()->notificationSetting->save();
        return response()->json(["success" => true]);
    }
}
