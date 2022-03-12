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
        // $setting = Setting::where('name', $request->name)->first();
        Setting::updateOrCreate(
            ['name' => $request->name],
            ['value' => $request->value]
        );
        // $setting->value = $request->value;
        // $setting->save();

        return back()->with(['success' => 'Settings updated']);
    }
}
