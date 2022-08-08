<?php

namespace App\Http\Controllers\ApiV2;

use App\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TicketCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FaqApiController extends Controller
{
    public function index()
    {

        $faq = Faq::all();
        return response()->json([
            'success' => true,
            "faq" => $faq,
        ], 200);

    }

    public function getFaq($id)
    {
        $faq = Faq::find($id);
        if (empty($faq)) {
            return response()->json([
                'success' => false,
                "message" => "Faq Not Found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            "faq" => $faq,
        ], 200);



    }

    public function addFaq(Request $r)
    {
        if(Auth::user()->role === 999){
        $validator = Validator::make($r->all(), [
            'title' => 'required|min:2|max:225',
            'body' => 'required',
            'category' => 'required',
            'icon' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $file = $r->file('photo');
        
        $filename = "";

        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            Storage::put('public/faq/' . $filename, fopen($file, 'r+'));
        }

        $faq = Faq::create([
            'title' => $r->title,
            'body' => $r->body,
            'category' => $r->category,
            'image' => $filename,
            'link' => $r->link,
            'icon' => $r->icon
        ]);

        if (empty($faq)) {
            return response()->json([
                'success' => false,
                "message" => "Error creating an FAQ",
            ], 500);
        }

        return response()->json([
            'success' => true,
            "faq" => $faq,
        ], 200);
    }
    else {
        return response()->json([
            'success' => false,
            "msg" => 'Not Authorised to add faq',
        ], 200);
    }

    }

    public function updateFaq(Request $r)
    {

        if(Auth::user()->role === 999){
        $validator = Validator::make($r->all(), [
            'id' => 'required|min:1|exists:faqs,id',
            'title' => 'required|min:2|max:225',
            'body' => 'required',
            'category' => 'required',
            'icon' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $faq = Faq::where('id', $r->id);

        if ($r->photo == null) {
            $faq->update([
                'title' => $r->title,
                'body' => $r->body,
                'category' => $r->category,
                'icon' => $r->icon
            ]);
            return response()->json([
                'success' => true,
                "message" => "Updated Successfully"
            ], 200);
        }

        $file = $r->file('photo');
        $filename = "";

        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            Storage::put('public/faq/' . $filename, fopen($file, 'r+'));
        }

        $faq->update([
            'title' => $r->title,
            'body' => $r->body,
            'category' => $r->category,
            'icon' => $r->icon,
            'image' => $filename
        ]);

        return response()->json([
            'success' => true,
            "message" => "Updated Successfully"
        ], 200);
    }
    else {
        return response()->json([
            'success' => false,
            "msg" => 'Not Authorised to update faq',
        ], 200);
    }
    }

    public function deleteFaq($id)
    {
        if(Auth::user()->role === 999){
        $faq = Faq::find($id);

        if (empty($faq)) {
            return response()->json([
                'success' => false,
                "message" => "Faq Not Found"
            ], 404);
        }
        $faq->delete();
        return response()->json([
            'success' => true,
            "message" => "Faq Deleted"
        ], 200);
    }
    else {
        return response()->json([
            'success' => false,
            "msg" => 'Not Authorised to delete faq',
        ], 200);
    }
}

}
