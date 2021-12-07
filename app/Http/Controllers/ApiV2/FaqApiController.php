<?php

namespace App\Http\Controllers\ApiV2;

use App\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TicketCategory;

class FaqApiController extends Controller
{
    public function index()
    {
        $faq = Faq::all();
        return response()->json([
            "status" => "Success",
            "faq" => $faq,
        ], 200);
    }

    public function getFaq($id)
    {
        $faq = Faq::find($id);
        if(empty($faq))
        {
            return response()->json([
                "status" => "Error",
                "message" => "Faq Not Found",
            ], 404); 
        }
        return response()->json([
            "status" => "success",
            "faq" => $faq,
        ], 200);
    }

    public function addFaq(Request $r)
    {
        $r->validate([
            'title' => 'required|min:2|max:225',
            'body' => 'required',
            'category' => 'required',
        ]);
        $file = $r->file('photo');
        $filename= "";
        if($file){
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid().'.'.$extension;
            Storage::put('public/faq/'.$filename, fopen($file, 'r+'));
        }
        
        $faq = Faq::create([
            'title' => $r->title,
            'body' => $r->body,
            'category' => $r->category,
            'image' => $filename,
            'link' => $r->link
        ]);
        if(empty($faq)){
            return response()->json([
                "status" => "Error",
                "message" => "Error creating an FAQ",
            ], 500);
        }
        return response()->json([
            "status" => "success",
            "faq" => $faq,
        ], 201);

    }

    public function updateFaq(Request $r)
    {
        $r->validate([
            'id' => 'required|min:1',
            'title' => 'required|min:2|max:225',
            'body' => 'required',
            'category' => 'required'
        ]);

        $faq = Faq::where('id', $r->id);
        if(!$faq){
            return response()->json([
                "status" => "Error",
                "message" => "Faq Not Found",
            ], 404);
        }
        $faq->update([
            'title' => $r->title,
            'body' => $r->body,
            'category' => $r->category
        ]);
        return response()->json([
            "status" => "success",
            "message" => "Updated Successfully"
        ], 200);
    }

    public function deleteFaq($id)
    {
        $faq = Faq::find($id);

        if(empty($faq)){
            return response()->json([
                "status" => "Error",
                "message" => "Faq Not Found"
            ], 404);
        }
        $faq->delete();
        return response()->json([
            "status" => "Success",
            "message" => "Faq Deleted"
        ], 200);

    }
}
