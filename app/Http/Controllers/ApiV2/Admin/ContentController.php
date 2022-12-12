<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Blog;
use App\BlogCategory;
use App\BlogHeading;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ContentController extends Controller
{
    //NB: don't assume flow ask if you need help
    public function addBlogCategory(Request $request)
    {

        $validator =  Validator::make($request->all(), [
            'title' => 'required|unique:blog_categories',
            "is_published" => "boolean"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }

        $blogCategory = New BlogCategory();
        $blogCategory->title = $request->title;
        $blogCategory->is_published = true;
        $blogCategory->save();

        // BlogCategory::create([
        //     "title" => $request->title,
        //     "is_published" => $request->is_published ?? false
        // ]);


        return response()->json([
            'success' => true,
            'message' => "Blog Category created"
        ], 201);
    }

    public function FetchCategories(){
        $data = BlogCategory::select(DB::raw('id as value, title as label, slug'))
        ->where("is_published", true)
        ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $data,
        ],200);
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
            'title' => 'required|unique:blog_categories,id,' . $id,
            // "is_published" => "boolean|required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }


        $blogCategory->title = $request->title;
        // $blogCategory->is_published = $request->is_published;
        $blogCategory->slug = Str::slug($request->title);// your parent boot not working when updating
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
            // "is_published" => "boolean"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }


        BlogHeading::create([
            "title" => $request->title,
            "is_published" => true
        ]);


        return response()->json([
            'success' => true,
            'message' => "Blog Heading created"
        ], 201);
    }

    public function fetchBlogHeadings(){
        $data = BlogHeading::select(DB::raw('id as value, title as label, slug'))
        ->where("is_published", true)
        ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
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
            'title' => 'required|unique:blog_headings,id,' . $id,
            // "is_published" => "boolean|required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 422);
        }


        $blogHeading->title = $request->title;
        // $blogHeading->is_published = $request->is_published;
        $blogHeading->slug = Str::slug($request->title);
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
        try {
            $validator =  Validator::make($request->all(), [
                'title' => 'required',
                "description" => "required",
                'image' => 'image|mimes:jpeg,JPEG,png,jpg,svg|max:5048|required',
                "body" => "required",
                // "status" => "in:draft,published",
                "blog_heading_id" => "required|exists:blog_headings,id",
                "blog_category_id" => "required|exists:blog_categories,id"
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->messages()
                ], 422);
            }
            
            $image =  $request->file('image');

            // $status = $request->status;
            // $publishedAt = null;
            // if ($status == 'published') {
                // $publishedAt = now();
            // }

            Blog::create([
                "title" => $request->title,
                "description" => $request->description,
                "body" => $request->body,
                "image" => $this->blogPostImage($image),
                // "status" => $request->status ?? "draft",
                "status" => "published",
                "published_at" => now(),
                "blog_heading_id" => $request->blog_heading_id ,
                "blog_category_id" => $request->blog_category_id,
                "author_id" => Auth::user()->id
            ]);

            return response()->json([
                'success' => true,
                'message' => "Blog created"
            ], 200);
            
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function blogPostImage($image)
    {

        $image_name = time() . "." . $image->getClientOriginalExtension();
        // $destinationPath = public_path('/thumbnail');

        // $resize_image = Image::make($image->getRealPath());
        // $resize_image->resize(300, 300, function ($constraint) {
        //     $constraint->aspectRatio();
        // })->save($destinationPath . "/" . $image_name);

        // $destinationPath = public_path('/blog/images');

        // $image->move($destinationPath, $image_name);
        Storage::put('public/blog/images/' . $image_name, fopen($image, 'r+'));
        return  $image_name;
    }


    public function fetchBlogPosts()
    {
        $data = Blog::where("status", "published")->with(
            [
                'categories' => function ($query) {
                    $query->select('id', 'title');
                },
                'headings' => function ($query) {
                    $query->select("id", "title");
                },

            ],
        )->orderBy('id','DESC')->get();

        foreach($data as $dataValues){
            $dataValues->image = URL::to('/').'/storage/'.'blog/images'.'/'.$dataValues->image;
            $dataValues->date = $dataValues->created_at->format('d M Y h:ia');
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function destroyBlog($id)
    {

        try {
            //for now this is not priority
            // $ids = explode(",", $id);
            $blog = Blog::find("id", $id);
            if($blog){
                $blog->delete();
                return response()->json([
                'success' => true,
                'message' => "blog deleted"
                ], 200);
            }


            return response()->json([
                'success' => false,
                'message' => 'blog does not exist'
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
        $data = Blog::where("id", $id)->with(
            [
                'categories' => function ($query) {
                    $query->select('id', 'title');
                },
                'headings' => function ($query) {
                    $query->select("id", "title");
                },

            ],
        )->get();

        foreach($data as $dataValues){
            $dataValues->image = URL::to('/').'/storage/'.'blog/images'.'/'.$dataValues->image;
            $dataValues->date = $dataValues->created_at->format('d M Y h:ia');
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function updateBlog(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (is_null($blog)) {
            return response()->json([
                'success' => false,
                'message' => "Blog  does not exist"
            ], 404);
        }
        $validator =  Validator::make($request->all(), [
            'title' => 'required',
            "description" => "required",
            'image' => 'image|mimes:jpeg,JPEG,png,jpg,svg|max:5048',
            "body" => "required",
            // "status" => "in:draft,published",
            "blog_heading_id" => "required|exists:blog_headings,id",
            "blog_category_id" => "required|exists:blog_categories,id"
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

        // $status = $request->status;
        // $status == 'published';
        // $publishedAt = $blog->published_at;
        // if ($status == 'published' && $blog->published_at != null) {
        //     $publishedAt = now();
        // }




        $blog->title = $request->title;
        $blog->slug = Str::slug($request->title);
        $blog->description = $request->description;
        $blog->body = $request->body;
        // $blog->status->heard = $request->title;
        // $blog->published_at =  $publishedAt;
        $blog->blog_category_id = $request->blog_category_id;
        $blog->blog_heading_id = $request->blog_heading_id;

        $blog->save();


        return response()->json([
            'success' => true,
            'data' => $blog
        ], 200);
    }
}
