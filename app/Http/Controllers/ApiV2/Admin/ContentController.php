<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Blog;
use App\BlogCategory;
use App\BlogHeading;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Storage;

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
        if (is_null($blogHeading)) {
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
        $blogHeading = BlogHeading::find($id);
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


    // Blog section'draft',

    public function storeBlog(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'title' => 'required|max:255',
            "description" => "required|min:100|max:250",
            'image' => 'image|mimes:jpeg,JPEG,png,jpg,svg|max:5048|required',
            "body" => "required",
            "status" => "in:draft,published",
            "blog_heading_id" => "sometimes|exists:blog_headings,id",
            "blog_category_id" => "sometimes|exists:blog_categories,id"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }
        $image =  $request->file('image');
        $status = $request->status;
        $publishedAt = null;
        if ($status == 'published') {
            $publishedAt = now();
        }
        Blog::create([
            "title" => $request->title,
            "description" => $request->description,
            "body" => $request->body,
            "image" => $this->blogPostImage($image),
            "status" => $request->status ?? "draft",
            "published_at" => $publishedAt,
            "blog_heading_id" => $request->blog_heading_id ?? null,
            "blog_category_id" => $request->blog_category_id ?? null,
            "author_id" => Auth::user()->id
        ]);

        return response()->json([
            'success' => true,
            'message' => "Blog created"
        ], 200);
    }

    private function blogPostImage($image)
    {

        $image_name = time() . "." . $image->getClientOriginalExtension();
        $destinationPath = public_path('/thumbnail');

        $resize_image = Image::make($image->getRealPath());
        $resize_image->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . "/" . $image_name);

        $destinationPath = public_path('/images');

        $image->move($destinationPath, $image_name);
        return  $image_name;
    }


    public function fetchBlogPosts()
    {
        $data['posts'] = Blog::where("status", "published")->with(
            [
                'categories' => function ($query) {
                    $query->select('id', 'title');
                },
                'headings' => function ($query) {
                    $query->select("id", "title");
                },

            ],
        )->select('id', "title", "status", "description", "blog_heading_id", "blog_category_id")->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function destroyBlog($id)
    {

        try {
            $ids = explode(",", $id);
            Blog::whereIn("id", $ids)->delete();
            return response()->json([
                'success' => true,
                'message' => "blog deleted"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function showPost($id)
    {
        $data['posts'] = Blog::where("id", $id)->with(
            [
                'categories' => function ($query) {
                    $query->select('id', 'title');
                },
                'headings' => function ($query) {
                    $query->select("id", "title");
                },

            ],
        )->select('id', "title", "status", "description", "blog_heading_id", "blog_category_id", "body")->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function updateBlog(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (is_null($$blog)) {
            return response()->json([
                'success' => false,
                'message' => "Blog  does not exist"
            ], 404);
        }
        $validator =  Validator::make($request->all(), [
            'title' => 'required|max:255',
            "description" => "required|min:100|max:250",
            'image' => 'sometimes|image|mimes:jpeg,JPEG,png,jpg,svg|max:5048',
            "body" => "required",
            "status" => "in:draft,published",
            "blog_heading_id" => "sometimes|exists:blog_headings,id",
            "blog_category_id" => "sometimes|exists:blog_categories,id"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }

        if ($image = $request->hasFile('image')) {
            $blog->image =  $this->blogPostImage($image);
        }

        $status = $request->status;
        $publishedAt = $blog->published_at;
        if ($status == 'published' && $blog->published_at != null) {
            $publishedAt = now();
        }




        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->body = $request->body;
        $blog->status->heard = $request->title;
        $blog->published_at =  $publishedAt;
        $blog->blog_category_id = $request->blog_category_id;
        $blog->blog_heading_id = $request->blog_heading_id;

        $blog->save();


        return response()->json([
            'success' => true,
            'data' => $blog
        ], 200);
    }
}