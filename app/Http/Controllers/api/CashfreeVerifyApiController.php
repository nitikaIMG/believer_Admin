<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use DB;
// use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

class CashfreeVerifyApiController extends Controller
{

      #payout
    public $clientId = 'CF54553D039X0GN4B8MUQI';
    public $clientSecret = '7ee0681f359ccfce56802821cc00781c7a556d41';
    public $env = 'prod';

    #config objs
    public $baseUrls = array(
        'prod' => 'https://payout-api.cashfree.com',
        'test' => 'https://payout-gamma.cashfree.com',
    );
    public $urls = array(
        'auth' => '/payout/v1/authorize',
        'bankValidation' => '/payout/v1/validation/bankDetails',
        'upiValidation' => '/payout/v1/validation/upiDetails',
    );
    public $bankDetails = array(
        // 'name' => 'sameera',
        // 'phone' => '9000000000',
        // 'bankAccount' => '026291800001191',
        // 'ifsc' => 'YESB0000262',
       
        // 'name' => 'praveen',
        // 'phone' => 'fghfghgfh',
        // 'bankAccount' => '917897897062334389',
        // 'ifsc' => 'PYTM012347898756',
    );
    public $upiDetails = array(
        // 'name' => 'sameera',
        // 'phone' => '9000000000',
        // 'bankAccount' => '026291800001191',
        // 'ifsc' => 'YESB0000262',
       
        'name' => 'praveen',
        'vpa' => '',
    );
   
    public $header;

    public $baseurl;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(empty(Session::get('fantasy_type'))){
            session(['fantasy_type' => 'Cricket']);
        }

        $this->header = array(
            'X-Client-Id: '.$this->clientId,
            'X-Client-Secret: '.$this->clientSecret,
            'Content-Type: application/json',
        );
       
        $this->baseurl = $this->baseUrls[$this->env];


       

    }
   
    #get auth token
    public function getToken(){
        try{
            $response = $this->post_helper('auth', null, null);
           
            return $response['data']['token'];
        }
        catch(Exception $ex){
           
            error_log('error in getting token');
            error_log($ex->getMessage());
            die();
        }

    }
      public function create_header($token){
        $header = $this->header;
        $headers = $header;
        if(!is_null($token)){
            array_push($headers, 'Authorization: Bearer '.$token);
        }
        // echo "<pre>";
        // print_r($headers);
        return $headers;
    }

    public function post_helper($action, $data, $token){
        $baseurl = $this->baseurl;
        $urls = $this->urls;
        $finalUrl = $baseurl.$urls[$action];
        $headers = $this->create_header($token);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $finalUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
       
        $r = curl_exec($ch);
         
        if(curl_errno($ch)){
            print('error in posting');
            print(curl_error($ch));
            die();
        }
       
        curl_close($ch);
        $rObj = json_decode($r, true);
        if($rObj['status'] != 'SUCCESS' || $rObj['subCode'] != '200') throw new Exception('incorrect response: '.$rObj['message']);

        return $rObj;

    }

    public function get_helper($finalUrl, $token){
        $headers = $this->create_header($token);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $finalUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
       
        $r = curl_exec($ch);
       
        if(curl_errno($ch)){
            print('error in posting');
            print(curl_error($ch));
            die();
        }
        curl_close($ch);

        $rObj = json_decode($r, true);
        //if($rObj['status'] != 'SUCCESS' || $rObj['subCode'] != '200') throw new Exception('incorrect response: '.$rObj['message']);
        return $rObj;
    }
    public function verifyBankAccount($token){
        try{
           
            $bankDetails = $this->bankDetails;
            $baseurl = $this->baseurl;
            $urls = $this->urls;

            $query_string = "?";

            foreach($bankDetails as $key => $value){
                $query_string = $query_string.$key.'='.$value.'&';
            }
           
            $finalUrl = $baseurl.$urls['bankValidation'].substr($query_string, 0, -1);
            $response = $this->get_helper($finalUrl, $token);

            return json_encode($response);
        }
        catch(Exception $ex){
            error_log('error in verifying bank account');
            error_log($ex->getMessage());
           
            return $ex->getMessage();

            // die();
        }
    }
    public function verification_bank_requests(Request $request)
    {
        $data= $request->all();
        $user = Helpers::isAuthorize($request);
        $users= DB::table('registerusers')->where('id',$user->id)->select('mobile')->first();
        if(!empty($users)){
         // to auto approve
            $this->bankDetails = array(
                'name' => $request->get('accountholder'),
                'phone' => $users->mobile,
                'bankAccount' => $request->get('accno'),
                'ifsc' => strtoupper($request->get('ifsc')),
            );
           
            $token = $this->getToken();
           
            $response = $this->verifyBankAccount($token);
           
            // echo "<pre>";print_r($response);die;
            return $response;
        }else{

        }
       
    }

}
