<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;

class VerificationController extends Controller
{
    public function uploadId(Request $request)
    {
        $this->validate($request, [
            'id_card' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048|required',
        ]);
        $user = Auth::user();

        if ($user->verifications()->where(['type' => 'ID Card', 'status' => 'Waiting'])->exists()) {
            return back()->with(['error' => 'ID Card verification already in progress']);
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

        return redirect()->back()->with(['success' => 'Id card uploaded, please hold on while we verify your account']);
    }


    public function uploadAddress(Request $request)
    {
        $this->validate($request, [
            'address' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048|required',
            'location' => 'required',

        ]);
        $user = Auth::user();

        if ($user->verifications()->where(['type' => 'Address', 'status' => 'Waiting'])->exists()) {
            return back()->with(['error' => 'Address verification already in progress']);
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

        return redirect()->back()->with(['success' => 'Address uploaded, please hold on while we verify your account']);
    }
}
