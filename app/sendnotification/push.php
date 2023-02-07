<?php

/**
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class Push {

    // push message title
    private $title;
    private $message;
	private $userid;
	private $userName;
	private $senderId;
    private $image;
    private $data;
    // flag indicating whether to show the push
    // notification or not
    // this flag will be useful when perform some opertation
    // in background when push is recevied
    private $is_background;

    function __construct() {
        
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setImage($imageUrl) {
        $this->image = $imageUrl;
    }

    public function setPayload($data) {
        $this->data = $data;
    }

    public function setIsBackground($is_background) {
        $this->is_background = $is_background;
    }
	
    public function setUserId($userid) {
        $this->userid = $userid;
    }
	
    public function setuserName($userName) {
        $this->userName = $userName;
    }
    public function setsenderId($senderId) {
        $this->senderId = $senderId;
    }
    public function setNotificationType($NotificationType){
        $this->NotificationType = $NotificationType;
    }
    public function getPush() {
        $res = array();
        $res['title'] = $this->title;
        $res['is_background'] = $this->is_background;
        $res['text'] = $this->message;
        $res['image'] = $this->image;
        $res['payload'] = $this->data;
        $res['userid'] = $this->userid;
        $res['userName'] = $this->userName;
        $res['senderId'] = $this->senderId;
        $res['type'] =     $this->NotificationType;
        $res['sound'] =     'default';
        $res['badge'] =     '1';
        $res['timestamp'] = date('Y-m-d G:i:s');
        return $res;
    }

}
