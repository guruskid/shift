<?php

namespace App\Http\Controllers\ApiV2\Manager;

use App\Faq;
use App\FaqCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    public function getAll()
    {

        $faqs = Faq::all();
        return response()->json([
            'success' => true,
            "data" => $faqs,
        ]);

    }

    //Add new Category
    public function addFaqCategory(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $category = FaqCategory::create([
            'name' => $request->name,

        ]);

        if (empty($category)) {
            return response()->json([
                'success' => false,
                "message" => "Error creating an FAQ Category",
            ], 500);
        }

        return response()->json([
            'success' => true,
            "categories" => $category,
        ], 200);

    }

    //view all Category
    public function viewCategory()
    {

        $categories = FaqCategory::all();
        return response()->json([
            'success' => true,
            "data" => $categories,
        ]);

    }

    //Delete Category

    public function deleteFaqCategory($id)
    {

        $getCategory = FaqCategory::find($id);

        if (empty($getCategory)) {
            return response()->json([
                'success' => false,
                "message" => "Category does not exist",
            ]);
        }
        $getCategory->delete();
        return response()->json([
            'success' => true,
            "message" => "Category has been deleted",
        ]);

    }

    //Add faq
    public function addNewFaq(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2|max:225',
            'body' => 'required',
            'category' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        //finding category

        $category = FaqCategory::where('name', $request->category)->first();
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category does not exist',
            ]);

        }
        $file = $request->file('image');

        $filename = "";

        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            Storage::put('public/faq/' . $filename, fopen($file, 'r+'));
        }

        $faq = Faq::create([
            'title' => $request->title,
            'body' => $request->body,
            'category' => $request->category,
            'image' => $filename,

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

//Delete Faq
    public function deleteAFaq($id)
    {

        $faq = Faq::find($id);

        if (empty($faq)) {
            return response()->json([
                'success' => false,
                "message" => "Faq Not Found",
            ]);
        }
        $faq->delete();
        return response()->json([
            'success' => true,
            "message" => "Faq has been deleted",
        ]);
    }

    //Update a Faq

    public function updateAFaq(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|min:1|exists:faqs,id',
            'title' => 'required|min:2|max:225',
            'body' => 'required',
            'category' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }


           //finding category

           $category = FaqCategory::where('name', $request->category)->first();
           if (!$category) {
               return response()->json([
                   'success' => false,
                   'message' => 'Category does not exist',
               ]);

           }


        $faq = Faq::where('id', $request->id);

        if ($request->photo == null) {
            $faq->update([
                'title' => $request->title,
                'body' => $request->body,
                'category' => $request->category,

            ]);
            return response()->json([
                'success' => true,
                "message" => "Updated Successfully",
            ], 200);
        }

        $file = $request->file('image');
        $filename = "";

        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            Storage::put('public/faq/' . $filename, fopen($file, 'r+'));
        }

        $faq->update([
            'title' => $request->title,
            'body' => $request->body,
            'category' => $request->category,
            'image' => $filename,
        ]);

        return response()->json([
            'success' => true,
            "message" => "Updated Successfully",
        ], 200);
    }

}
