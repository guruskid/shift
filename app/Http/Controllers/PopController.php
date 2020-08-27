<?php

namespace App\Http\Controllers;

use App\Pop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PopController extends Controller
{
    public function add(Request $r)
    {
        $this->validate($r, [
            'pops.*' => 'image|max:7048|required',
        ]);


        foreach ($r->pops as $file) {
            $extension = $file->getClientOriginalExtension();
            $filenametostore = time() . uniqid() . '.' . $extension;
            Storage::put('public/pop/' . $filenametostore, fopen($file, 'r+'));
            $p = new Pop();
            $p->user_id = Auth::user()->id;
            $p->transaction_id = $r->transaction_id;
            $p->path = $filenametostore;
            $p->save();
        }
        return redirect()->back()->with(['success' => 'Image uploaded' ]);
    }
}
