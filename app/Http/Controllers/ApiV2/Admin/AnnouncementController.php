<?php

namespace App\Http\Controllers\ApiV2\Admin;

<<<<<<< Updated upstream
use App\Announcement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
   
    public function create(Request $request) {
        $data = $request->all();
        $validate = Validator::make($data, [
            'title' => 'required|string',
            'details' => 'string|required',
            'image' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048|required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 400);
        }

        $file = $request->image;
        $extension = $file->getClientOriginalExtension();
        $filenametostore = uniqid() . '.' . $extension;
        Storage::put('public/announcement/' . $filenametostore, fopen($file, 'r+'));
        //Resize image here
        $thumbnailpath = 'storage/announcement/' . $filenametostore;
        // $img = Image::make($thumbnailpath)->resize(300, null, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // $img->save($thumbnailpath);
        $data['image'] = $filenametostore;
        $data['posted_by'] = Auth::user()->id;

        Announcement::create($data);

        return response()->json([
            'success' => true,
            'message' => "Announcement posted successfully"
        ],200);
    }

    public function getAnnouncements() {
        $announcement = Announcement::latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $announcement
        ],200);
    }
    
    public function update(Request $request, $id) {
        $data = $request->all();
        $validate = Validator::make($data, [
            'title' => 'required|string',
            'details' => 'string|required',
            'image' => 'image|mimes:jpeg,JPEG,png,jpg|max:5048',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 400);
        }

        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found',
            ], 400);
        }

        if ($request->image) {
            $file = $request->image;
            $extension = $file->getClientOriginalExtension();
            $filenametostore = uniqid() . '.' . $extension;
            Storage::put('public/announcement/' . $filenametostore, fopen($file, 'r+'));
            $data['image'] = $filenametostore;   
        }

        $announcement->update($data);

        return response()->json([
            'success' => true,
            'message' => "Announcement updated successfully",
            'data' => $announcement
        ],200);
    }

    public function updateStatus($id,$status) {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found',
            ], 400);
        }

        if (!in_array($status,['active','inactive'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status',
            ], 400);
        }

        $announcement->update([
            'status' => $status
        ]);

        return response()->json([
            'success' => true,
            'message' => "Announcement status has been updated successfully",
            'data' => $announcement
        ],200);
    }

    public function delete($id) {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found',
            ], 400);
        }
        $announcement->delete();
        return response()->json([
            'success' => true,
            'message' => "Announcement delete successfully",
        ],200);
    }

=======
use App\ApiV2\Admin\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ApiV2\Admin\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ApiV2\Admin\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ApiV2\Admin\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ApiV2\Admin\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $announcement)
    {
        //
    }
>>>>>>> Stashed changes
}
