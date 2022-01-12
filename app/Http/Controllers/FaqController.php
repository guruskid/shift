<?php

namespace App\Http\Controllers;

use App\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
    public function index()
    {
        $finances = Faq::where('category', 'finance')->latest()->get();
        $techs = Faq::where('category', 'tech')->latest()->get();
        $transactions = Faq::where('category', 'transactions')->latest()->get();
        return view("admin.faq", compact("finances", "techs", "transactions"));
    }

    public function getFaqs()
    {
        $finances = Faq::where('category', 'finance')->get();
        $techs = Faq::where('category', 'tech')->get();
        $transactions = Faq::where('category', 'transactions')->get();
        return response()->json([
            "finances" => $finances,
            "techs" => $techs,
            "transactions" => $transactions
        ], 200);
    }

    public function addFaq(Request $r)
    {
        $r->validate([
            'title' => 'required|min:2|max:225',
            'body' => 'required',
            'category' => 'required',
            'icon'=>'required'
        ]);
        $file = $r->file('photo');
        $filename= "";
        if($file){
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid().'.'.$extension;
            Storage::put('public/faq/'.$filename, fopen($file, 'r+'));
        }
        
        Faq::create([
            'title' => $r->title,
            'body' => $r->body,
            'category' => $r->category,
            'image' => $filename,
            'link' => $r->link,
            'icon' => $r->icon
        ]);
        return back()->with(['success' => 'FAQ added successfully ']);
    }

    public function editFaqView($id, $title)
    {
        $editfaq = Faq::where('id', $id)->get();
        $FaqsCount = Faq::where('id', $id)->count();

        if($FaqsCount < 1 ){
            return  redirect()->route("admin.faq");
        }
        return view("admin.edit-faq", compact("editfaq"));
    }

    public function updateFaq(Request $r)
    {
        $r->validate([
            'id' => 'required|min:1',
            'title' => 'required|min:2|max:225',
            'body' => 'required',
            'category' => 'required',
            'icon' =>'required'
        ]);
        if($r->photo == null){
            Faq::where('id', $r->id)->update([
                'title' => $r->title,
                'body' => $r->body,
                'category' => $r->category,
                'icon' => $r->icon
            ]);
            return back()->with(['success' => 'Updated successfully ']);
        }
        $file = $r->file('photo');
        $filename= "";
        if($file){
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid().'.'.$extension;
            Storage::put('public/faq/'.$filename, fopen($file, 'r+'));
        }
        Faq::where('id', $r->id)->update([
            'title' => $r->title,
            'body' => $r->body,
            'category' => $r->category,
            'icon' => $r->icon,
            'image' =>$filename
        ]);
        return back()->with(['success' => 'Updated successfully ']);

        
    }

    public function deleteFaq($id)
    {
        $FaqsCount = Faq::where('id', $id)->count();

        if($FaqsCount < 1 ){
            return  redirect()->route("admin.faq");
        }

        Faq::where('id', $id)->delete();
        return back()->with(['success' => 'Updated deleted']);
    }
}
