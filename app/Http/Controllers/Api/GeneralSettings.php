<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\SystemSettings;

class GeneralSettings extends Controller
{

    public static function getSetting($name)
    {
        $setting = SystemSettings::where('settings_name',$name)->first();
        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }
}
