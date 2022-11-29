<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UtilityTransaction;
use App\NairaTransaction;

class UtilityTransactions extends Controller
{
    public function index(Request $request)
    {
        $transactions['transactions'] = UtilityTransaction::whereNotNull('id')->orderBy('created_at', 'desc');
        $type = UtilityTransaction::select('type')->distinct('type')->get();
        $status = UtilityTransaction::select('status')->distinct('status')->get();
        $data = $request->validate([
            'start' => 'date|string',
            'end' => 'date|string',
        ]);

        if (!empty($data)) {
            $transactions['transactions'] = $transactions['transactions']
            ->where('created_at', '>=', $data['start'])
            ->where('created_at', '<=', $data['end']);

            if ($request->type != 'null') {
                $transactions['transactions'] = $transactions['transactions']
                ->where('type','=',$request->type);
            }
            if ($request->status != 'null') {
                $transactions['transactions'] = $transactions['transactions']
                ->where('status','=',$request->status);
            }


        }
        $total = $transactions['transactions']->sum('total');

        $total_transactions = $transactions['transactions']->count();
        $total_amount = $transactions['transactions']->sum('amount');
        $total_convenience_fee = $transactions['transactions']->sum('convenience_fee');

        $transactions['transactions'] = $transactions['transactions']->paginate(200);
        return view('admin.utility-transactions',$transactions,compact(['type','status','total',
        'total_transactions','total_amount','total_convenience_fee']));
    }

    public function requery($transaction) {
        $transaction = UtilityTransaction::find($transaction);
        $nt = NairaTransaction::where('reference',$transaction->reference_id)->first();
        $wallet = $nt->user->nairaWallet;

        // return $transaction;
        $postData['request_id'] = $transaction->reference_id;
        $ch = curl_init(env('LIVE_VTPASS_REQUERY_URL'));
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

        dd($response);

        if(isset($response['content']) && isset($response['content']['transactions'])) {
            if($response['content']['transactions']['status'] == 'delivered') {
                if ($transaction->type == 'Cable subscription') {

                    try {
                        $title = 'Cable subscription';
                        $msg_body = 'Your Dantown wallet has been debited with N' . $nt->amount . ' for cable subscription and N' . $nt->charge . ' for convenience fee.';

                        $not = Notification::create([
                            'user_id' => $nt->user->id,
                            'title' => $title,
                            'body' => $msg_body,
                        ]);

                        Mail::to($nt->user->email)->send(new DantownNotification($title, $msg_body, '', ''));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                    $extras = json_encode([
                        'type' => $response['content']['transactions']['product_name'],
                        'subscription_plan' => '',
                        'decoder_number' => $response['content']['transactions']['unique_element'],
                        'price' => $response['content']['transactions']['unit_price'],
                    ]);
                }elseif ($transaction->type == 'Electricity purchase') {

                    try {
                        $body = 'Your Dantown wallet has been debited with N' . $nt->amount . ' for electricity recharge and N' . $nt->charge . ' for convenience fee.<br><br>
                        <b>Token: ' . $response['token'] . '</b>,<br>
                        <b>Unit: ' . $response['units'] . '</b>,<br>
                        <b>Reference code:' . $nt->reference;
                        $btn_text = '';
                        $btn_url = '';

                        $title = 'Electricity purchase';

                        $name = ($nt->user->first_name == " ") ? $nt->user->username : $nt->user->first_name;
                        $name = explode(' ', $name);
                        $firstname = ucfirst($name[0]);
                        Mail::to($nt->user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                    $body = 'Your Dantown wallet has been debited with N' . $nt->amount . ' for electricity recharge and N' . $nt->charge . ' for convenience fee.
                    Token: ' . $response['token'] . ',
                    Unit: ' . $response['units'] . ',
                    Reference code:' . $nt->reference;

                    $not = Notification::create([
                        'user_id' => $nt->user->id,
                        'title' => $title,
                        'body' => $body,
                    ]);

                    $extras = json_encode([
                        'token' => $response['token'],
                        'purchased_code' => $response['purchased_code'],
                        'units' => $response['units'],
                    ]);
                }
                // return $extras;
                $transaction->update([
                    'status' => 'success',
                    'extras' => $extras
                ]);

                $nt->update([
                    'status' => 'success',
                    'extras' => $extras
                ]);

                return back()->with(['success' => 'Transaction processed']);
            }else{
                if($response['content']['transactions']['status'] == 'failed') {
                    $transaction->update([
                        'status' => 'failed'
                    ]);
                    $nt->update([
                        'status' => 'failed'
                    ]);
                    $wallet->amount = $wallet->amount + $transaction->amount;
                    $wallet->save();
                    return back()->with(['success' => 'Transaction failed']);
                }else{
                    return back()->with(['success' => 'Transaction is pending requery after some time']);
                }
            }
        }else{
            if ($response['code'] == '015') {
                return back()->with(['error' => 'Invalid Request ID']);
            }
            return back()->with(['success' => 'Transaction is pending requery after some time']);
        }
    }

    // public function requery($transaction) {
    //     $transaction = UtilityTransaction::find($transaction);
    //     $nt = NairaTransaction::where('reference',$transaction->reference_id)->first();
    //     $wallet = $nt->user->nairaWallet;

    //     // return $transaction;
    //     $postData['request_id'] = $transaction->reference_id;
    //     $ch = curl_init(env('LIVE_VTPASS_REQUERY_URL'));
    //     \curl_setopt_array($ch,[
    //         CURLOPT_HEADER => false,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_USERPWD=> env('VTPASS_USERNAME').':'.env('VTPASS_PASSWORD'),
    //         CURLOPT_TIMEOUT=> 120,
    //         CURLOPT_POST=>true,
    //         CURLOPT_POSTFIELDS=>$postData
    //     ]);
    //     $response = curl_exec($ch);
    //     curl_close($ch);
    //     $response = json_decode($response,true);

    //     if(isset($response['content']) && isset($response['content']['transactions'])) {
    //         if($response['content']['transactions']['status'] == 'delivered') {
    //             if ($transaction->type == 'Cable subscription') {
    //                 $extras = json_encode([
    //                     'type' => $response['content']['transactions']['product_name'],
    //                     'subscription_plan' => '',
    //                     'decoder_number' => $response['content']['transactions']['unique_element'],
    //                     'price' => $response['content']['transactions']['unit_price'],
    //                 ]);
    //             }elseif ($transaction->type == 'Electricity purchase') {
    //                 $extras = json_encode([
    //                     'token' => $response['token'],
    //                     'purchased_code' => $response['purchased_code'],
    //                     'units' => $response['units'],
    //                 ]);
    //             }
    //             // return $extras;
    //             $transaction->update([
    //                 'status' => 'success',
    //                 'extras' => $extras
    //             ]);

    //             $nt->update([
    //                 'status' => 'success',
    //                 'extras' => $extras
    //             ]);
    //             return back()->with(['success' => 'Transaction processed']);
    //         }else{
    //             if($response['content']['transactions']['status'] == 'failed') {
    //                 $transaction->update([
    //                     'status' => 'failed'
    //                 ]);
    //                 $wallet->amount = $wallet->amount + $transaction->amount;
    //                 $wallet->save();
    //                 return back()->with(['success' => 'Transaction failed']);
    //             }else{
    //                 return back()->with(['success' => 'Transaction is pending requery after some time']);
    //             }
    //         }
    //     }else{
    //         if ($response['code'] == '015') {
    //             return back()->with(['error' => 'Invalid Request ID']);
    //         }
    //         return back()->with(['success' => 'Transaction is pending requery after some time']);
    //     }
    // }
}
