<?php

namespace App\Http\Controllers;

use App\Faq;
use App\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    public function index()
    {
        $faq_categories = FaqCategory::all();
        $faq = Faq::with('category')->get();
        return view("admin.faq", compact('faq_categories', 'faq'));
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

        $faq = new Faq();
        $faq->title = $r->title;
        $faq->body = $r->body;
        $faq->category_id = $r->category;
        $faq->image = $filename;
        $faq->link = $r->link;
        $faq->icon = $r->icon;
        $faq->slug = Str::slug($r->title);
        $faq->save();


        return back()->with(['success' => 'FAQ added successfully ']);
    }

    public function editFaqView($id)
    {
        $faq_categories = FaqCategory::all();
        $editfaq = Faq::with('category')->where('id', $id)->first();

        if(!$editfaq){
            return back()->with(['error' => 'No FAQ found ']);
        }

        return view("admin.edit-faq", compact(["editfaq",'faq_categories']));
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
        $faqData = Faq::find($r->id);
        if(!$faqData){
            return back()->with(['error' => 'Faq Not Found']);
        }

        $file = $r->file('photo');
        $filename= "";
        if($file){
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid().'.'.$extension;
            Storage::put('public/faq/'.$filename, fopen($file, 'r+'));
            $faqData->image = $filename;
        }

        $faqData->title = $r->title;
        $faqData->body = $r->body;
        $faqData->category_id = $r->category;
        $faqData->icon = $r->icon;
        $faqData->slug = Str::slug($r->title);
        $faqData->save();


        return back()->with(['success' => 'FAQ Updated successfully ']);
    }

    public function deleteFaq($id)
    {
        $Faq = Faq::find($id);

        if(!$Faq){
            return  redirect()->route("admin.faq");
        }

        $Faq->delete();
        return back()->with(['success' => 'FAQ Deleted Successfully']);
    }
}
