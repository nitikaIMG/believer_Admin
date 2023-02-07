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

class FootballApiController extends Controller
{
    public function accessrules(){
		header('Access-Control-Allow-Origin: *'); 
		header("Access-Control-Allow-Credentials: true");
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Authorization');
		header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
	}

	//get auth
	public static function getaccesstoken(){
		date_default_timezone_set("Asia/Kolkata");
		$fields = array(
          'access_key' => '42b02ba3a34a695fa54a2b5a60c7a78c',
	       'secret_key' => '8e5a717018030b287b7849459c0c52c2',
	       'app_id' => 'Mysure11football',
	       'device_id' => '129345161843560038834545'
      );
		$url = 'https://api.footballapi.com/v1/auth/';
        $fields_string="";
       $d = DB::table('footballtoken')->first();
		$todate = date('Y-m-d h:i:s');
		$findtoken = DB::table('footballtoken')->whereDate('date','=',date('Y-m-d',strtotime($todate)))->first();
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
			DB::connection('mysql2')->table('footballtoken')->where('id',1)->insert($matchtoken);
		}
		 return $match_fields;
		die;
	}
	//Get All tournament
	public static function getTournament(){
		$match_fields = FootballApiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$url='https://api.footballapi.com/v1/recent_tournaments/?access_token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Execute Match request
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = json_decode($rmatch_result, true);
		
		return $rmatch_arrs['data']['tournaments'];
	}
	//get particular tournament details
	public static function getTournamentDetails($tournamentkey){
		$match_fields = FootballApiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$url='https://api.footballapi.com/v1/tournament/'.$tournamentkey.'/?access_token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Execute Match request
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = json_decode($rmatch_result, true);
		return $rmatch_arrs['data'];
	}
	//get round details
	public static function getRoundsDetails($tournamentkey,$roundkey){
		$match_fields = FootballApiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$url='https://api.footballapi.com/v1/tournament/'.$tournamentkey.'/round-detail/'.$roundkey.'/?access_token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Execute Match request
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = json_decode($rmatch_result, true);
		return $rmatch_arrs['data'];
	}
	//get round details
	public static function getMatchDetails($matchkey){
		$match_fields = FootballApiController::getaccesstoken();
		$access_token = $match_fields['access_token'];
		$url='https://api.footballapi.com/v1/match/'.$matchkey.'/?access_token='.$access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Execute Match request
		$rmatch_result = curl_exec($ch);
		curl_close($ch);
		$rmatch_arrs = json_decode($rmatch_result, true);
		return $rmatch_arrs['data'];
	}
}
?>