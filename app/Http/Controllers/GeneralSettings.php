<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\SystemSettings;

class GeneralSettings extends Controller
{

    public function index()
    {
        $settings = SystemSettings::get();
        $settingsData = array();
        foreach ($settings as $key => $value) {
            $settingsData[$value['settings_name']] = $value;
        }

        $data['settings'] = $settingsData;

        return view('admin.general_settings',$data);
    }

    public function updateSettings(Request $request)
    {
        $inputs = $request->all();
        $value = ($inputs['value'] == 'true') ? 1 : 0;
        $setting = SystemSettings::where('settings_name',$inputs['name'])->update(['settings_value' =>  $value, 'notice' => $inputs['notice']]);

        if ($setting) {
            return response()->json([
                'msg' => 'Success!!!',
                'status' => 'success'
            ]);
        }

        return response()->json([
            'msg' => 'An error occurred, please try again!',
            'status' => 'error'
        ]);
    }

    public static function getSetting($name)
    {
        $setting = SystemSettings::where('settings_name',$name)->first();
        return $setting;
    }
}
