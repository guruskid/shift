<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use Illuminate\Support\Facades\Validator;

class AnnoucementController extends Controller
{
    public function allAnnouncement()
    {
        $n = Notification::orderBy('id', 'desc');
        return response()->json([
            'success' => true,
            'total_annoucement' => $n->count(),
            'data' => $n->paginate(100),
        ], 200);

    }

    public function addAnnouncement(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'title' => 'required',
            'body' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $n = new Notification();
        $n->title = $r->title;
        $n->body = $r->body;
        $n->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement added successfully',
        ], 200);
    }

    public function editAnnoucement(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'id' => 'required',
            'title' => 'required',
            'message' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $n = Notification::find($r->id);
        $n->title = $r->title;
        $n->body = $r->body;
        $n->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully',
        ], 200);
    }

    public function deleteAnnouncement(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $n = Notification::find($r->id);
        $n->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully',
        ], 200);
    }
}
