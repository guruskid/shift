<?php

namespace App\Http\Controllers\ApiV2\Manager;

use App\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    public function addNew(Request $request) {
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

        $data['image'] = $filenametostore;
        $data['posted_by'] = Auth::user()->id;


        Announcement::create($data);

        return response()->json([
            'success' => true,
            'message' => "Announcement posted successfully"
        ],200);
    }

    public function getAllAnnouncement() {
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

    public function deleteAnnouncement($id) {
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
            'message' => "Announcement deleted successfully",
        ],200);
    }
}
