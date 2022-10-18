<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\BlogCategory;
use App\BlogHeading;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    //


    public function addBlogCategory(Request $request)
    {

        $validator =  Validator::make($request->all(), [
            'title' => 'required|unique:blog_categories|max:255',
            "is_published" => "boolean"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }


        BlogCategory::create([
            "title" => $request->title,
            "is_published" => $request->is_published ?? false
        ]);


        return response()->json([
            'success' => true,
            'message' => "Blog Category created"
        ], 201);
    }

    public function updateBlogCategory(Request $request, $id)
    {
        $blogCategory = BlogCategory::find($id);
        if (is_null($blogCategory)) {
            return response()->json([
                'success' => false,
                'message' => "Blog Category does not exist"
            ], 404);
        }
        $validator =  Validator::make($request->all(), [
            'title' => 'required|max:50|unique:blog_categories,id,' . $id,
            "is_published" => "boolean|required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }


        $blogCategory->title = $request->title;
        $blogCategory->is_published = $request->is_published;
        $blogCategory->update();

        return response()->json([
            'success' => true,
            'message' => "Blog Category updated",
            "data" => $blogCategory
        ], 201);
    }

    public function deleteBlogCategory($id)
    {
        $blogCategory = BlogCategory::find($id);
        if (is_null($blogCategory)) {
            return response()->json([
                'success' => false,
                'message' => "Blog Category does not exist"
            ], 404);
        }

        $blogCategory->delete();

        return response()->json([
            'success' => true,
            'message' => "Blog Category deleted"
        ], 200);
    }



    // Blog Heading
    public function addBlogHeading(Request $request)
    {

        $validator =  Validator::make($request->all(), [
            'title' => 'required|unique:blog_headings|max:255',
            "is_published" => "boolean"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }


        BlogHeading::create([
            "title" => $request->title,
            "is_published" => $request->is_published ?? false
        ]);


        return response()->json([
            'success' => true,
            'message' => "Blog Heading created"
        ], 201);
    }

    public function updateBlogHeading(Request $request, $id)
    {
        $blogHeading = BlogHeading::find($id);
        if (is_null( $blogHeading)) {
            return response()->json([
                'success' => false,
                'message' => "Blog Heading does not exist"
            ], 404);
        }
        $validator =  Validator::make($request->all(), [
            'title' => 'required|max:50|unique:blog_headings,id,' . $id,
            "is_published" => "boolean|required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }


        $blogHeading->title = $request->title;
        $blogHeading->is_published = $request->is_published;
        $blogHeading->update();

        return response()->json([
            'success' => true,
            'message' => "Blog Heading updated",
            "data" => $blogHeading
        ], 201);
    }

    public function deleteBlogHeading($id)
    {
        $blogHeading= BlogHeading::find($id);
        if (is_null($blogHeading)) {
            return response()->json([
                'success' => false,
                'message' => "Blog Heading does not exist"
            ], 404);
        }

        $blogHeading->delete();

        return response()->json([
            'success' => true,
            'message' => "Blog Heading deleted"
        ], 200);
    }
}
