<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;

use File;
use Session;

use Helpers;
use Illuminate\Support\Facades\Validator;

class AddpointController extends Controller
{
    public function pointt(){
        $version= DB::table('androidversion')->select('updation_points')->first();
		return view('updatepoints',compact('version'));
	}

	public function add_pointt(Request $request){
		$input = $request->all();
         $rules = array(
                'updation_points' => 'required',
            );
            $validator = Validator::make($input,$rules);
            if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
                }
		unset($input['_token']);	
		DB::connection('mysql2')->table('androidversion')->update($input);
		return redirect()->action('AddpointController@pointt')->with('success','Point save successfully');
	}
	public function version(){
		$this->accessrules();
		$findlogin = DB::table('androidversion')->first();
		if(!empty($findlogin)){
			$msgg['status'] = $findlogin->version;
			$msgg['point'] = $findlogin->updation_points;
			echo json_encode(array($msgg));die;
		}else{
			$msgg['status'] = 0;
			echo json_encode(array($msgg));die;
		}
	}
	
	public function accessrules(){
		header('Access-Control-Allow-Origin: *'); 
		header("Access-Control-Allow-Credentials: true");
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Authorization');
		header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
	}
}