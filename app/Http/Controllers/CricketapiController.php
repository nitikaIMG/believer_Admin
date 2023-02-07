<?php

namespace App\Http\Controllers;

use DB;
use Session;
use bcrypt;
use Config;
use Redirect;
use Hash; 
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use DateTime;

class CricketapiController extends Controller
{
    public function accessrules(){
		header('Access-Control-Allow-Origin: *'); 
		header("Access-Control-Allow-Credentials: true");
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Authorization');
		header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
	}


	public static function getaccesstoken(){
		date_default_timezone_set("Asia/Kolkata");
		$fields = array(
      	'access_key' => '966e1790d428e48a326fc796b3a3ba06',
          'secret_key' => 'fb0cc9b025d3caf017c6396029f86ff5',
          'app_id' => 'http://imgglobalinfotech.com',
          'device_id' => '1105700391871451188'
		  );	 
		  
		$url = 'https://rest.cricketapi.com/rest/v2/auth/';
        $fields_string="";
       $d = DB::table('apitoken')->first();
		$todate = date('Y-m-d h:i:s');
		$findtoken = DB::table('apitoken')->whereDate('date','=',date('Y-m-d',strtotime($todate)))->first();
		if(!empty($findtoken)){
			$match_fields = array(
				'access_token' => $findtoken->token,
			);
		}
		else{
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			$fields_string=rtrim($fields_string, '&');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			$result_arrs = json_decode($result, true);
			$access_token = $result_arrs['auth']['access_token'];
			$match_fields = array(
				'access_token' => $access_token,
			);
			$matchtoken['token'] = $access_token; 
			DB::connection('mysql2')->table('apitoken')->where('id',1)->insert($matchtoken);
		}
		 return $match_fields;
		die;
	}

	public static function recentmatches(){
		define("EMAIL_USE_SMTP", true);
		define("EMAIL_SMTP_HOST", "ssl://smtp.zoho.com");
		define("EMAIL_SMTP_AUTH", true);
		define("EMAIL_SMTP_USERNAME", "info@fantasypower11.com");
		define("EMAIL_SMTP_PASSWORD", "fantasy@123");
		define("EMAIL_SMTP_PORT", 465);
		define("EMAIL_SMTP_ENCRYPTION", "ssl");
	    $match_fields = CricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
	    $rmatch_url="https://rest.cricketapi.com/rest/v2/recent_matches/?access_token=$access_token";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = json_decode($rmatch_result, true);
		return $rmatch_arrs['data']['cards'];
	}


	public static function responseget(){
		$match_fields = CricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
	    $rmatch_url="https://rest.cricketapi.com/rest/v2/schedule/?access_token=$access_token";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		// Execute Match request
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = gzinflate(substr($rmatch_result,10));
		$rmatch_arrs = json_decode($rmatch_arrs, true);
		return $rmatch_arrs['data']['cards'];
	} 


	public static function getscedulematches(){
     	$date = new DateTime();
        $date->modify('+ 1 month');
        $nxtmonth = $date->format("Y-m");
        $match_fields = CricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
	    $rmatch_url="https://rest.cricketapi.com/rest/v2/schedule/?access_token=$access_token";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = gzinflate(substr($rmatch_result,10));
		$rmatch_arrs = json_decode($rmatch_arrs, true);
		$matcharray = $rmatch_arrs['data']['months'][0]['days'];
		// next month //
		$rmatch_url="https://rest.cricketapi.com/rest/v2/schedule/?access_token=$access_token&date=".$nxtmonth;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = gzinflate(substr($rmatch_result,10));
		$rmatch_arrs = json_decode($rmatch_arrs, true);
		array_push($matcharray,$rmatch_arrs['data']['months'][0]['days']);
		
		return $matcharray;
	}

	public static function getmatchdetails($match_key){
		$match_fields = CricketapiController::getaccesstoken();
		$match_url = 'https://rest.cricketapi.com/rest/v2/match/'.$match_key.'/?'. http_build_query($match_fields).'&card_type=full_card';
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// 		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		return $match_arrs ;
		curl_close($ch);

	}

	public static function leaugeplayerdata($playerkey){
		$match_url = "https://rest.cricketapi.com/rest/v2/player/$playerkey/league/ipl/stats/?access_token=2s1105700391871451188s1531425225831492226";
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// 		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		return $match_arrs ;
		curl_close($ch);

	}

	public static function getmatchdetailsplayers($match_key){
	   // echo 'hii';die;
		$match_fields = CricketapiController::getaccesstoken();
		// 		$match_url = 'https://rest.cricketapi.com/rest/v2/match/'.$match_key.'/?'. http_build_query($match_fields).'&card_type=full_card';
        $match_url='http://167.71.225.28/matchesinfo/getMatchData/'.$match_key;
        // echo $match_key;die;
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// 		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		$match_arras = json_decode($match_arrs['matchdata'], true);
		// 		echo '<pre>';print_r($match_arras);die;
		return $match_arrs ;
		curl_close($ch);

	}

	public static function getseriesdetail(){
		// echo 'hii';die;
		//  $match_fields = CricketapiController::getaccesstoken();
		 // 		$match_url = 'https://rest.cricketapi.com/rest/v2/match/'.$match_key.'/?'. http_build_query($match_fields).'&card_type=full_card';
		 $match_url='http://167.71.225.28/matchesinfo/getSeriesData';
		//  echo $match_key;die;
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $match_url);
		 curl_setopt($ch, CURLOPT_POST, false);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		 // 		// Execute Match request
		 
		 $match_result = curl_exec($ch);
		 curl_close($ch);
		 $match_arrs = json_decode($match_result, true);
		 
		 $match_arras = $match_arrs['seriesdata'];
		 return $match_arras ;
		 
	}


	public static function forfull_data($match_key){
		$match_fields = CricketapiController::getaccesstoken();
		$match_url = 'https://rest.cricketapi.com/rest/v2/match/'.$match_key.'/?'. http_build_query($match_fields).'&card_type=full_card';
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = gzinflate(substr($match_result, 10));
		$match_arrs = json_decode($match_arrs, true);
		if(!empty($match_arrs)){
		    if(isset($match_arrs['data']['card']['players'])){
		        return $match_arrs['data']['card']['players'] ;
		    }
		}
		 
	} 
	public static function getplayerinfo($playerkey){
		$match_fields = CricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = 'https://rest.cricketapi.com/rest/v2/player/'.$playerkey.'/league/icc/stats/?access_token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = gzinflate(substr($match_result, 10));
		$match_arrs = json_decode($match_arrs, true);
		
		return $match_arrs['data']['player'];
	}


	public static function recentseasons(){
	    $match_fields = CricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = 'https://rest.cricketapi.com/rest/v2/recent_seasons/?access_token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = gzinflate(substr($match_result, 10));
		$match_arrs = json_decode($match_arrs, true);
		return $match_arrs;
	}


	public static function seasonmatches($sesaonkey){
	    $match_fields = CricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = 'https://rest.cricketapi.com/rest/v2/season/'.$sesaonkey.'/?access_token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = gzinflate(substr($match_result, 10));
		$match_arrs = json_decode($match_arrs, true);
		return $match_arrs;
	}
	public static function localaccesstoken(){
     	$fields = array(
	          'access_key' => '7a45f9d2b334b87836c5e45c8ecbe912',
	         'secret_key' => '8109e666a70ca141dd85e666a4d19322',
	         'app_id' => 'mysure11appid',
	         'device_id' => '1105700391871459188'
		  );
		
		  $url = 'https://rest.cricketapi.com/rest/v4/auth/';
        $fields_string="";
       	$d = DB::table('apilocaltoken')->first();

		$todate = date('Y-m-d h:i:s');
		$findtoken =  DB::table('apilocaltoken')->whereDate('date','=',date('Y-m-d',strtotime($todate)))->first();
		if(!empty($findtoken)){
			$match_fields = array(
				'access_token' => $findtoken->token,
			);
		}
		else{
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			$fields_string=rtrim($fields_string, '&');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);

			$result_arrs = json_decode($result, true);
			$access_token = $result_arrs['auth']['access_token'];
			$match_fields = array(
				'access_token' => $access_token,
			);
			$matchtoken['token'] = $access_token; 

			 DB::connection('mysql2')->table('apilocaltoken')->where('id',1)->insert($matchtoken);
		}

		 return $match_fields;
		die;
	}
	public static function coverageApi()
	{
		$match_fields = CricketapiController::localaccesstoken();
		$access_token = $match_fields['access_token'];
	    $rmatch_url="https://rest.cricketapi.com/rest/v4/coverage/?access_token=$access_token";
	    // echo $rmatch_url;die;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		// Execute Match request
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = gzinflate(substr($rmatch_result,10));
		$rmatch_arrs = json_decode($rmatch_arrs, true);
		return $rmatch_arrs;
	}
	public static function GetLocalScheduleMatch($board_key)
	{
		// $board_key= 'c.board.bcci.b13f0';
		$date = new DateTime();
        $date->modify('+0 month');
        $nxtmonth = $date->format("Y-m");
        $match_fields = CricketapiController::localaccesstoken();
		$access_token = $match_fields['access_token'];
	    $rmatch_url="https://rest.cricketapi.com/rest/v4/board/$board_key/schedule/?access_token=$access_token&month=".$nxtmonth;
	    // echo '<pre>';print_r($rmatch_url);die;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$rmatch_result = curl_exec($ch);
		curl_close($ch);

		$rmatch_arrs = gzinflate(substr($rmatch_result,10));
		$rmatch_arrs = json_decode($rmatch_arrs, true);

		return $rmatch_arrs;
	}
	public static function GetLocalRecentSchedule($season_key)
	{
        $match_fields = CricketapiController::localaccesstoken();
		$access_token = $match_fields['access_token'];
	    // $rmatch_url="http://rest.cricketapi.com/rest/v4/season/$season_key/recent_matches/?access_token=$access_token";
	    $rmatch_url="http://rest.cricketapi.com/rest/v4/season/$season_key/schedule/?access_token=$access_token";
	   	
		$ch = curl_init();	
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = gzinflate(substr($rmatch_result,10));
		$rmatch_arrs = json_decode($rmatch_arrs, true);
		return $rmatch_arrs;
	}
	public static function GetLocalRecentMatch($season_key)
	{
        $match_fields = CricketapiController::localaccesstoken();
		$access_token = $match_fields['access_token'];
	    $rmatch_url="http://rest.cricketapi.com/rest/v4/season/$season_key/recent_matches/?access_token=$access_token";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = gzinflate(substr($rmatch_result,10));
		$rmatch_arrs = json_decode($rmatch_arrs, true);
		return $rmatch_arrs;
	}
	public static function GetLocalMatchdetails($match_key)
	{
        $match_fields = CricketapiController::localaccesstoken();
		$access_token = $match_fields['access_token'];
	    $rmatch_url="http://rest.cricketapi.com/rest/v4/match/$match_key/?access_token=$access_token&card_type=metric_101";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		
		$rmatch_arrs = gzinflate(substr($rmatch_result,10));
		$rmatch_arrs = json_decode($rmatch_arrs, true);
		
		return $rmatch_arrs;
	}
	public static function GetLocalMatchplayerinfo($seasonkey,$playerkey)
	{
        $match_fields = CricketapiController::localaccesstoken();
		$access_token = $match_fields['access_token'];
	    $rmatch_url="http://rest.cricketapi.com/rest/v4/season/$seasonkey/player/$playerkey/stats/?access_token=$access_token";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		//echo "<pre>";print_r($match_key);
		//echo "<pre>";print_r($rmatch_result);die;
		$rmatch_arrs = gzinflate(substr($rmatch_result,10));
		$rmatch_arrs = json_decode($rmatch_arrs, true);
		return $rmatch_arrs;
	}
	public static function getlistmatches(){
		
		$match_url = 'http://167.71.225.28/matchesinfo/getavailablematches';
		
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		return $match_arrs ;
		curl_close($ch);
	}

	public static function getmatchdata($match_key){
		
		$match_url = 'http://167.71.225.28/matchesinfo/getMatchData/'.$match_key;
		
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);

		if(!empty($match_arrs)){
			return json_decode($match_arrs['matchdata'], true);
		}else{
			return null;
		}
		// return json_decode($match_arrs['matchdata'], true);
		// return $match_arrs;
		curl_close($ch);
	}

	public static function getmatchesdata($match_key){
		
		// $match_url = 'http://167.71.225.28/matchesinfo/getMatchData/'.$match_key;
	    $match_url = 'http://167.71.225.28/matchesinfo/requestplayerscredits/'.$match_key;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		// dd($match_arrs);
		if(!empty($match_arrs)){
			return json_decode($match_arrs['playersdata'], true);
		}else{
			return null;
		}
		// return json_decode($match_arrs['matchdata'], true);
		// return $match_arrs;
		curl_close($ch);
	}
}
