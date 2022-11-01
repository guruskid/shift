<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ImageSlide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageSliderController extends Controller
{
    public function index()
    {
        $slider = ImageSlide::latest()->get()->take(10);
        return view('admin.image_slider', compact('slider'));
    }

    public function upload(Request $dt)
    {
        $this->validate($dt, [
            'image' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048|required',
        ]);

        $img = new ImageSlide();
        $file = $dt->image;
        $extension = $file->getClientOriginalExtension();
        $filenametostore = uniqid().time().'.' . $extension;
        Storage::put('public/slider/' . $filenametostore, fopen($file, 'r+'));

        $img->image = $filenametostore;
        $img->save();
        return redirect()->back()->with(['success' => 'Advert Image slider uploaded successfully']);
    }

    public function updateImage(Request $dt)
    {
        $this->validate($dt, [
            'image_id' => 'required',
            'image' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048|required',
        ]);

        if(Auth::user()->role != 666){
            return back()->with(['error' => 'You are not allowed to do this']);
        }

        $img = ImageSlide::where('id', $dt->image_id)->get()[0];

        $file = $dt->image;
        $extension = $file->getClientOriginalExtension();
        $filenametostore = uniqid().time().'.' . $extension;
        Storage::put('public/slider/' . $filenametostore, fopen($file, 'r+'));

        $img->image = $filenametostore;
        $img->save();
        return redirect()->back()->with(['success' => 'Advert Image slider uploaded successfully']);
    }

    public function deleteImage($id)
    {

        if(Auth::user()->role != 666){
            return back()->with(['error' => 'You are not allowed to do this']);
        }

        $img = ImageSlide::where('id', $id)->delete();

        return redirect()->back()->with(['success' => 'Advert Image slider deleted successfully']);
    }
}
