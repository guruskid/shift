<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Card;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;


class CardsController extends Controller
{
    public function store(Request $r)
    {

        $validate = Validator::make($r->all(), [
            'image' => 'image|mimes:jpeg,JPEG,png,jpg,svg|max:5048|required',
            'name' => 'string|unique:cards|required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

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

        // return redirect()->back()->with(['success' => 'Card added']);

        return response()->json([
            'success' => true,
            'mgs' => 'Card added successfully'
        ]);

    }

    public function editCard(Request $r)
    {

        $validate = Validator::make($r->all(), [
            'name' => 'required',
            'card_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }


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
            $thumbnailpath = 'storage/assets/' . $filenametostore;
            $img = Image::make($thumbnailpath)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($thumbnailpath);
            $card->image = $filenametostore;
            $card->save();
        }

        return response()->json([
            'success' => true,
            'mgs' => 'Card edited successfully'
        ]);

    }

    public function deleteCard($id)
    {
        $card = Card::find($id)->delete();

        return response()->json([
            'success' => true,
            'mgs' => 'Card deleted successfully'
        ]);
    }
}
