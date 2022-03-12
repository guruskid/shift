<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;

class SettingController extends Controller
{
    public static function get($name)
    {
        return Setting::where('name', $name)->first()->value ?? 0;
    }

    public static function set(Request $request)
    {
        Setting::updateOrCreate(
            ['name' => $request->name],
            ['value' => $request->value]
        );

        return back()->with(['success' => 'Settings updated']);
    }
}
