<?php

namespace App\Http\Controllers\Admin;

use App\FaqCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class FaqCategoryController extends Controller
{
    public function index(){
        $faq_category = FaqCategory::orderBy('id','DESC')->paginate(20);
        return view('admin.faq.category.index',compact(['faq_category']));
    }

    public function store(Request $request){
        try{
            $request->validate([
                'name' => 'required|string',
            ]);

            $faq_category = new FaqCategory();
            $faq_category->name = $request->name;
            $faq_category->save();

            return redirect()->back()->with('success', 'Category Added Successfully');

        }catch (ValidationException $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

    }

    public function update(Request $request){
        try{
            $request->validate([
                'id' => 'required',
                'name' => 'required|string',
            ]);

            $faq_category = FaqCategory::find($request->id);
            if(!$faq_category){
                return redirect()->back()->with('error', 'Category not found');
            }

            $faq_category->name = $request->name;
            $faq_category->save();

            return redirect()->back()->with('success', 'Category Updated Successfully');

        }catch (ValidationException $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Request $request){
        try{
            $request->validate([
                'id' => 'required',
            ]);

            $faq_category = FaqCategory::find($request->id);
            if(!$faq_category){
                return redirect()->back()->with('error', 'Category not found');
            }

            $faq_category->delete();

            return redirect()->back()->with('success', 'Category Deleted Successfully');

        }catch (ValidationException $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
