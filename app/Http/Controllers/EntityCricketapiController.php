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

class EntityCricketapiController extends Controller
{
    public function accessrules(){
		header('Access-Control-Allow-Origin: *'); 
		header("Access-Control-Allow-Credentials: true");
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Authorization');
		header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
	}

	/*  
	{
    	"status": "ok",
	    "response": {
	        "token": "1|X#aFhlzAsd",
	        "expires": "12312312312",
	    },
	    "api_version": "2.0"
	}
	
	*/
	// public static function getaccesstoken(){
		// 	date_default_timezone_set("Asia/Kolkata");
		// 	$fields = array(
		// 	      		'access_key' => '316d460109bd902f16f45e0ae93cce32',
		// 	          	'secret_key' => '1a0c9d19a5d1298e6932e8c05f9f0d42',
		// 	          	'extend'=>'1'
		// 	  		);	 
		// 	 //curl -X POST "https://rest.entitysport.com/v2/auth?access_key=YOURACCESSKEY&secret_key=YOURSECRETKEY&extend=1"
		// 	$url = 'https://rest.entitysport.com/v2/auth?';
		 //        $fields_string="";
		 //       	$d = DB::table('apitoken')->first();
		// 	$todate = date('Y-m-d h:i:s');
		// 	$findtoken = DB::table('apitoken')->whereDate('date','=',date('Y-m-d',strtotime($todate)))->first();
		// 	if(!empty($findtoken)){
		// 		$match_fields = array(
		// 			'access_token' => $findtoken->token,
		// 		);
		// 	}
		// 	else{
		// 		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		// 		$fields_string=rtrim($fields_string, '&');
		// 		$ch = curl_init();
		// 		curl_setopt($ch, CURLOPT_URL, $url);
		// 		curl_setopt($ch, CURLOPT_POST, true);
		// 		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// 		$result = curl_exec($ch);
		// 		$result_arrs = json_decode($result, true);
		// 		$access_token = $result_arrs['response']['token'];
		// 		$match_fields = array(
		// 			'access_token' => $access_token,
		// 		);
		// 		$matchtoken['token'] = $access_token; 
		// 		DB::connection('mysql2')->table('apitoken')->where('id',1)->insert($matchtoken);
		// 	}
		// 	 return $match_fields;
		// 	die;
	// }
	public static function getaccesstoken(){
		$match_fields = array(
			'access_token' => '0fc9de38d635cd6ee677e841fc838bd5',
			// 'access_token' => 'd838e55bf823bc6e6ad46ba9c7gh1106aa',
		);
		return $match_fields;
	}

	public static function getallseriesdataa(){
		$match_fields = EntityCricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = 'https://rest.entitysport.com/v2/seasons/2022/competitions?token='.$access_token.'&per_page=50&&paged=1';
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$ab['seriesdata'] = $match_result;
		DB::table('seriesdata')->insert($ab);
		$match_arrs = json_decode($match_result, true);
		return $match_arrs ;
		curl_close($ch);
	}

	// public static function compitionsList(){
	// 	$cdate = date('Y-m');
	// 	$match_fields = EntityCricketapiController::getaccesstoken();
	// 	$access_token = $match_fields['access_token'];
	// 	$match_url = 'https://rest.entitysport.com/v2/competitions?yearmonth='.$cdate.'&token='.$access_token;
	// 	// https://rest.entitysport.com/v2/competitions?yearmonth=2018-02(yyyy-mm)&paged=1&per_page=50&token=[ACCESS_TOKEN]
	// 	$ch = curl_init();
	// 	curl_setopt($ch, CURLOPT_URL, $match_url);
	// 	curl_setopt($ch, CURLOPT_POST, false);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
	// 	curl_setopt($ch,CURLOPT_ENCODING , "gzip");
	// 	// Execute Match request
	// 	$match_result = curl_exec($ch);
	// 	$match_arrs = json_decode($match_result, true);
	// 	dd($match_arrs);
	// }

	// public static function compitionMatches($cid){
	// 	//https://rest.entitysport.com/v2/competitions/{cid}/matches/?token=[ACCESS_TOKEN]&per_page=10&&paged=1
	// 	$match_fields = EntityCricketapiController::getaccesstoken();
	// 	$access_token = $match_fields['access_token'];
	// 	$match_url = 'https://rest.entitysport.com/v2/competitions/'.$cid.'/matches/?token='.$access_token;
	// 	$ch = curl_init();
	// 	curl_setopt($ch, CURLOPT_URL, $match_url);
	// 	curl_setopt($ch, CURLOPT_POST, false);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
	// 	curl_setopt($ch,CURLOPT_ENCODING , "gzip");
	// 	// Execute Match request
	// 	$match_result = curl_exec($ch);
	// 	$match_arrs = json_decode($match_result, true);
	// 	dd($match_arrs);
	// }

	public static function allUpcomingMatches($page){
		$match_fields = EntityCricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = 'https://rest.entitysport.com/v2/matches/?status=1&token='.$access_token.'&per_page=50&&paged='.$page;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		return $match_arrs;
	}

	public static function match_info($matchid){
		// https://rest.entitysport.com/v2/matches/19887/info?token=
		$match_fields = EntityCricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = 'https://rest.entitysport.com/v2/matches/'.$matchid.'/info?token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		return $match_arrs;
	}

	public static function getmatchplayers($matchid){
		$match_fields = EntityCricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = 'https://rest.entitysport.com/v2/matches/'.$matchid.'/squads?token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		return $match_arrs;
		// dd($match_arrs);
	}

	public static function playerstats($playerskey,$matchkey){
		$match_fields = EntityCricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		
	    $rmatch_url="https://rest.entitysport.com/v2/players/".$playerskey."/stats?token=".$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		// $match_arrs = json_decode($match_result, true);
		return $match_result;
		curl_close($ch);
	}

	public static function playerranks(){
		$match_fields = EntityCricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		
	    $rmatch_url="https://rest.entitysport.com/v2/iccranks?token=".$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $rmatch_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		return $match_arrs;
		curl_close($ch);
	}

	public static function getmatchscore($matchid){
		$match_fields = EntityCricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = 'https://rest.entitysport.com/v2/matches/'.$matchid.'/scorecard?token='.$access_token;
		// dd($match_url);
		// $match_url = 'https://rest.entitysport.com/v2/matches/39176/scorecard?token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		return $match_arrs;
	}
	public static function match_fantasy_points($matchid){
		// https://rest.entitysport.com/v2/matches/19887/info?token=
		$match_fields = EntityCricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = 'https://rest.entitysport.com/v2/matches/'.$matchid.'/newpoint?token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		$match_arrs = json_decode($match_result, true);
		dd($match_arrs);
	}

	public static function matchballdata($match_key){
		$match_fields = EntityCricketapiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$match_url = "https://rest.entitysport.com/v2/matches/".$match_key."/live?token=".$access_token;
		
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $match_url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_ENCODING , "gzip");
		// Execute Match request
		$match_result = curl_exec($ch);
		DB::table('balldata')->insert(['data'=>$match_result]);
		$match_arrs = json_decode($match_result, true);
		return $match_result;
		curl_close($ch);
	}
}
