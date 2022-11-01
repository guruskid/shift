<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirebasePushNotificationController extends Controller {

    public static function sendPush($fcm_id, $title, $message) {
        $GOOGLE_API_KEY = env("FIREBASE_API_KEY");
		$logo = asset('admin_assets/img/logo.svg');
		$fields = array(
        	'to'		=> $fcm_id,
            'priority'	=> "high",
            "notification" => array("body" => $message, "title" => $title),
            'data'		=> array("title" =>$title, "message" =>$message, "image"=> $logo, "type"=> "none", "click_action" => "FLUTTER_NOTIFICATION_CLICK")
        );
		
        $headers = array('https://fcm.googleapis.com/fcm/send','Content-Type: application/json','Authorization: key='.$GOOGLE_API_KEY);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		
        $result = curl_exec($ch);
        if ($result === FALSE) {
            // die('Problem occurred: ' . curl_error($ch));
        }
		
        curl_close($ch);
        return $result;
    }

    public function testPush() {
       $fcm_id = 'cl8klIQ9QwWs-x6SIeFkSo:APA91bHvrydA01bpQwn2f2HfGNQgPftLGauBMi7oq-jd3a86U8PZkN1wR45tE6kqgco8cjzPGzQkYGcKzERWStgBS3JtFu0PpbpsF6hhuIIt48vIS9wUeA2Rm48i8kIAia5h0GMZAtwy';

       $fcm = self::sendPush($fcm_id, 'Test test', 'hello world');

       dd($fcm);
    }
	
}
