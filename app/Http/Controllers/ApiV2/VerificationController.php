<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\GeneralTemplateOne;
use Dotenv\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class VerificationController extends Controller
{
    public function uploadId(Request $request)
    {

        $user = Auth::user();

        $validator = FacadesValidator::make($request->all(), [
            'id_card' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048|required',
            'location' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if ($user->verifications()->where(['type' => 'ID Card', 'status' => 'Waiting'])->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'ID Card verification already in progress'
            ]);
        }

        $file = $request->id_card;
        $extension = $file->getClientOriginalExtension();
        $filenametostore =  $user->email . uniqid(). '.' . $extension;
        Storage::put('public/idcards/' . $filenametostore, fopen($file, 'r+'));
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

        $name = $user->first_name;
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $name));

        return response()->json([
            'success' => 'Id card uploaded, please hold on while we verify your account'
        ]);
    }


    public function uploadAddress(Request $request)
    {

        $validator = FacadesValidator::make($request->all(), [
            'address' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048|required',
            // 'address' => 'required',
            'location' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $user = Auth::user();
        if ($user->verifications()->where(['type' => 'Address', 'status' => 'Waiting'])->exists()) {
            return response()->json([
                'error' => 'Address verification already in progress'
            ]);
            dd('hi');
        }

        $file = $request->address;
        $extension = $file->getClientOriginalExtension();
        $filenametostore =  $user->email . uniqid(). '.' . $extension;
        Storage::put('public/idcards/' . $filenametostore, fopen($file, 'r+'));
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

        $name = $user->first_name;
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $name));
        return response()->json([
            'success' => 'Address uploaded, please hold on while we verify your account'
        ]);
    }
}

