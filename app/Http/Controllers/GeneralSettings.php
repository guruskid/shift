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

    public static function getSetting($name) {
        $setting = SystemSettings::where('settings_name',$name)->first();
        return $setting;
    }

    public static function getSettingValue($name) {
        $setting = SystemSettings::where('settings_name',$name)->first();
        if ($setting) {
            return $setting['settings_value'];
        }
        return 0;
    }

    public static function updateConfig(Request $request) {
        $data = $request->except('_token');
        $res = '';
        if (!isset($data['referral_active'])) {
            $res = SystemSettings::updateOrCreate([
                'settings_name'   => strtoupper('referral_active'),
                'notice' => ''
            ],[
                'settings_value'  => 0
            ]);
        }
        if (!isset($data['naira_transaction_charge'])) {
            $res = SystemSettings::updateOrCreate([
                'settings_name'   => strtoupper('naira_transaction_charge'),
                'notice' => ''
            ],[
                'settings_value'  => 0
            ]);
        }

        if (!isset($data['limit_user_withdrawal'])) {
            $res = SystemSettings::updateOrCreate([
                'settings_name'   => strtoupper('limit_user_withdrawal'),
                'notice' => ''
            ],[
                'settings_value'  => 0
            ]);
        }
        
        if (!isset($data['hara_active'])) {
            $res = SystemSettings::updateOrCreate([
                'settings_name'   => strtoupper('hara_active'),
                'notice' => ''
            ],[
                'settings_value'  => 0
            ]);
        }
        foreach ($data as $key => $value) {
            $res = SystemSettings::updateOrCreate([
                'settings_name'   => strtoupper($key),
                'notice' => ''
            ],[
                'settings_value'  => ($value == 'on') ? 1 : $value
            ]);
        }
        return back()->with(['success' => 'settings updated successfully']);
    }
}
