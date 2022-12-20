<?php

namespace App\Http\Controllers\Admin;

use App\Card;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CardController extends Controller
{
    public function store(Request $r)
    {
        $this->validate($r, [
            'image' => 'image|mimes:jpeg,JPEG,png,jpg,svg|max:5048|required',
            'name' => 'string|required',
        ]);

        $card = Card::create([
            'name' => $r->name,
            'wallet_id' => $r->wallet_id,
            'buyable' => $r->buyable ? 1 : 0,
            'sellable' => $r->sellable ? 1 : 0,
            'is_crypto' => $r->is_crypto ? 1 : 0,
            'image' => 'null',
        ]);

        $file = $r->image;
        $extension = $file->getClientOriginalExtension();
        $filenametostore = $r->name . '.' . $extension;
        Storage::put('public/assets/' . $filenametostore, fopen($file, 'r+'));
        //Resize image here
        $thumbnailpath = 'storage/assets/' . $filenametostore;
        $img = Image::make($thumbnailpath)->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($thumbnailpath);
        $card->image = $filenametostore;
        $card->save();

        return redirect()->back()->with(['success' => 'Card added']);

    }

    public function editCard(Request $r)
    {

        $card = Card::find($r->card_id);
        $card->update([
            'name' => $r->name,
            'wallet_id' => $r->wallet_id,
            'buyable' => $r->buyable ? 1 : 0,
            'sellable' => $r->sellable ? 1 : 0,
            'is_crypto' => $r->is_crypto ? 1 : 0,
        ]);

        if ($r->has('image')) {
            $file = $r->image;
            $extension = $file->getClientOriginalExtension();
            $filenametostore = $r->name . '.' . $extension;
            Storage::put('public/assets/' . $filenametostore, fopen($file, 'r+'));
            //Resize image here
            // $thumbnailpath = 'storage/assets/' . $filenametostore;
            // $img = Image::make($thumbnailpath)->resize(300, null, function ($constraint) {
            //     $constraint->aspectRatio();
            // });
            // $img->save($thumbnailpath);
            $card->image = $filenametostore;
            $card->save();
        }


        return redirect()->back()->with(['success' => 'Card updated']);
    }
}
