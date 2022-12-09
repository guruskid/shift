<?php

namespace App\Http\Controllers;

use App\Mail\GeneralTemplateOne;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function uploadId(Request $request)
    {
        if(Auth::user()->first_name == ' '){
            return back()->with(['error' => 'Please add your bank details to continue your level 2 verification']);
        }
        if(Auth::user()->phone_verified_at == null){
            return back()->with(['error' => 'Please verify your phone number first']);
        }
        if(Auth::user()->address_verified_at == null){
            return back()->with(['error' => 'Please verify your address first']);
        }
        $this->validate($request, [
            'id_card' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048|required',
        ]);
        $user = Auth::user();

        if ($user->verifications()->where(['type' => 'ID Card', 'status' => 'Waiting'])->exists()) {
            return back()->with(['error' => 'ID Card verification already in progress']);
        }

        $file = $request->id_card;
        $extension = $file->getClientOriginalExtension();
        $filenametostore =  $user->email . uniqid() . '.' . $extension;
        // Storage::put('public/idcards/' . $filenametostore, fopen($file, 'r+'));
        $file->move(public_path('storage/idcards/'), $filenametostore);
        $user->id_card = $filenametostore;
        $user->save();

        $user->verifications()->create([
            'path' => $filenametostore,
            'type' => 'ID Card',
            'status' => 'Waiting'
        ]);

        $title = 'LEVEL 3 VERIFICATION DOCUMENTS RECEIVED';
        $body = 'We have successfully received your document for level 3 verification.
        Your verification request is currently on-review, and you will get feedback from us within 24-48 hours.';

        $btn_text = '';
        $btn_url = '';

        // $name = $user->first_name;
        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

        return back()->with(['success' => 'Id card uploaded, please hold on while we verify your account']);
    }


    public function uploadAddress(Request $request)
    {
        if(Auth::user()->first_name == ' '){
            return back()->with(['error' => 'You must to add your bank details before proceeding for verification']);
        }

        if(Auth::user()->phone_verified_at == null){
            return back()->with(['error' => 'Please Verify your phone number first']);
        }

        $this->validate($request, [
            'address' => 'required',
            'location' => 'required',

        ]);
        $user = Auth::user();
        if ($user->verifications()->where(['type' => 'Address', 'status' => 'Waiting'])->exists()) {
            return back()->with(['error' => 'Address verification already in progress']);
        }

        $file = $request->address;
        $extension = $file->getClientOriginalExtension();
        $filenametostore =  $user->email . uniqid() . '.' . $extension;
        // Storage::put('public/storage/idcards/' . $filenametostore, fopen($file, 'r+'));
        $file->move(public_path('storage/idcards/'), $filenametostore);
        // $file->move(public_path().'/idcards/' . $filenametostore);
        $user->address_img = $request->location;
        $user->save();

        $user->verifications()->create([
            'path' => $filenametostore,
            'type' => 'Address',
            'status' => 'Waiting'
        ]);

        $title = 'LEVEL 2 VERIFICATION DOCUMENTS RECEIVED
        ';
        $body = 'We have successfully received your document for level 2 verification.
        Your verification request is currently on-review, and you will get a feedback from us within 24-48 hours.
        ';

        $btn_text = '';
        $btn_url = '';

        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
        return back()->with(['success' => 'Address uploaded, please hold on while we verify your account']);
    }

    public function queryName() {
        $bank_code = request('bank_code');
        $acct_number = request('acct_number');
        $client = new Client();
        $url = 'https://app.nuban.com.ng/api/NUBAN-AGBCLVUL544?acc_no='.$acct_number.'&bank_code='.$bank_code;
        $response = $client->request('GET', $url);
        $body = ($response->getBody()->getContents());
        $body = json_decode($body);
        if (isset($body->error)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid Account Details'
            ]);
        }
        $acct_name = $body[0]->account_name;
        $data = [
            'account_name' => $acct_name
        ];
        return response()->json([
            'success' => true,
            'data' => $data
        ],200);

    }
}
