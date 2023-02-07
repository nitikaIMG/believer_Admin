<?php 
namespace App\Http\Controllers;
use DB;
use Session;
use bcrypt;
use Config;
use Redirect;
use App\Helpers\Helpers;
use Hash;
use Mail;
use File;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
class AddcashController extends Controller {
	
    public function geturl(){
		return asset('');
	}
	
	public function accessrules(){
		header('Access-Control-Allow-Origin: *'); 
		header("Access-Control-Allow-Credentials: true");
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Authorization');
		header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
		date_default_timezone_set('Asia/Kolkata'); 
	}
	
    
	public function addcash_bonus(Request $request){
	    if ($request->isMethod('post')){
	        $rules = array(
				'amt_range' => 'required',
				'percentage'=>'required',
			);
			$validator = Validator::make(request()->all(), $rules);
			if($validator->fails()){
					return Redirect::back()
						->withErrors($validator)
						->withInput(request()->except('password'));
			}			
			
			$input = request()->all();
			$check = DB::table('addcash_bonus')->where('amt_range',$input['amt_range'])->first();
			if(empty($check)){
                $input['amt_range'] = $input['amt_range'];
                $input['percentage'] = $input['percentage'];
                unset($input['_token']);
    	        DB::connection('mysql2')->table('addcash_bonus')->insert($input);
    	        return redirect()->back()->with('success','Bonus Percentage save successfully');
			}else{
    	        return redirect()->back()->with('danger','Amount range is already exist');
			}
    	        
	    }
	    return view('addcashbonus.addcashbonus');
	}

	public function viewaddcashbonus(){
		$getbonus = DB::table('addcash_bonus')->get();
		return view('addcashbonus.viewaddcashbonus',compact('getbonus'));
	}

	public function delleteaddcashbonus(Request $request,$id){
		$id = unserialize(base64_decode($id));
		$getbonus = DB::table('addcash_bonus')->where('id',$id)->delete();
		return redirect()->back()->with('danger','Bonus Deleted Successfully');
	}

	public function editbonus(Request $request,$id){
		$id = unserialize(base64_decode($id));
		if($request->isMethod('post')){
			$input = $request->all();
			unset($input['_token']);
			DB::table('addcash_bonus')->where('id',$id)->update($input);
			return redirect()->action('AddcashController@viewaddcashbonus')->with('success','Bonus Has Been Updated');
		}else{
			$getdata = DB::table('addcash_bonus')->where('id',$id)->first();
			return view('addcashbonus.edit_addcashbonus',compact('getdata'));
		}
	}
	
	
	
	
}
?>