<?php

/**
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class Firebase {

    // sending push message to single user by firebase reg id
    public function send($to, $message) {
        $message['body'] =$message['text'];
        $fields = array(
            'to' => $to,
            'notification' => $message,
        );
        return $this->sendPushNotification($fields);
    }

    // Sending message to a topic by topic name
    public function sendToTopic($to, $message) {
        $message['body'] =$message['text'];
        $fields = array(
            'to' => '/topics/' . $to,
            'notification' => $message,
        );
        return $this->sendPushNotification($fields);
    }

    // sending push message to multiple users by firebase registration ids
    public function sendMultiple($registration_ids, $message) {
        $message['body'] =$message['text'];
        $fields = array(
            'registration_ids' => $registration_ids,
            'notification' => $message,
        );
        return $this->sendPushNotification($fields);
    }

    // function makes curl request to firebase servers
    private function sendPushNotification($fields) {
        
        require_once __DIR__ . '/config.php';
        $url = "https://fcm.googleapis.com/fcm/send";
        
        //  $registrationIds = array('dQhhjeUD9U1jksorvT-UdO:APA91bFUZmiqQ5C8CeGeJx799F1jvHNjQMH01KN2rBGp7iih6uYL1w-y-QKXjZr12ChSoqwuTadvxv5R0t-oYdU3Lqpuq4KVDglUAThH6V-KxcNXmCNzLF7xwCh-OujEdT0JBVTCdjZ8');
         
        //  $title = "Push Notification";
        //  $body = "This is body.";
        //  $notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' =>'1');
        
        //  $arrayToSend = array('registration_ids' => $registrationIds, 'notification'=>$notification,'priority'=>'high');
         $json = json_encode($fields);
        //  echo '<pre>';print_r($json);die;
         $headers = array();
         $headers[] = 'Content-Type: application/json';
         $headers[] = 'Authorization: key='. FIREBASE_API_KEY;
        
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
         curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
         curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
         //Send the request
         $result = curl_exec($ch);
         if ($result === FALSE) 
         {
            die('FCM Send Error: ' . curl_error($ch));
         }
        
         curl_close( $ch );
         return $result;
        
        
        // // echo '<pre>';print_r(json_encode($fields));
        // require_once __DIR__ . '/config.php';

        // // Set POST variables
        // $url = 'https://fcm.googleapis.com/fcm/send';

        // $headers = array(
        //     'Authorization: key=' . FIREBASE_API_KEY,
        //     'Content-Type: application/json'
        // );
        // // Open connection
        // $ch = curl_init();
        // // echo '<pre>';print_r($headers);die;
        // // Set the url, number of POST vars, POST data
        // curl_setopt($ch, CURLOPT_URL, $url);

        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // // Disabling SSL Certificate support temporarly
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // // Execute post
        // $result = curl_exec($ch);
        // if ($result === FALSE) {
        //     die('Curl failed: ' . curl_error($ch));
        // }

        // // Close connection
        // curl_close($ch);

        // return $result;
    }
}

?>