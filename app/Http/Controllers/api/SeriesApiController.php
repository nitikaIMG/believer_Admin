<?php
namespace App\Http\Controllers\api;
use DB;
use Session;
use bcrypt;
use Config;
use Redirect;
use App\Helpers\Helpers; 
use Hash;
use Mail;
use Cache;
use Crypt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class SeriesApiController extends Controller{
	/* check for referal */
  	/**
     * @return json
     * @Url: /api/checkforrefer/
     * @Method: POST
     * @Parameters
     *     
     *		refercode : "text"
     *		auth_key : in header [Authorization]
     * 		
     *
     */
  	public function getallseries(Request $request){
		Helpers::timezone();
		Helpers::setHeader(200);
		$input = $request->all();
		$geturl = Helpers::geturl();
		
           		$currentdate = date('Y-m-d H:i:s');
				$seriesfind = DB::table('series')->select('*')->where('status','opened')->where('end_date','>=',$currentdate)->orderBy('end_date','DESC')->get();
				   
				$Json=array();
				$i=0;
				if(count($seriesfind)>0){
				foreach($seriesfind as $series){
					$Json[$i]['id'] = $series->id;
					$Json[$i]['name'] = $series->name;
					$Json[$i]['status'] = 1;
					$Json[$i]['startdate'] = date('d M Y', strtotime($series->start_date));
					$Json[$i]['starttime'] = date('H:i a', strtotime($series->start_date));
					$Json[$i]['enddate'] = date('d M Y', strtotime($series->end_date));
					$Json[$i]['endtime'] = date('H:i a', strtotime($series->end_date));
					$Json[$i]['startdatetime'] = date('Y-m-d H:i:s', strtotime($series->start_date));
					$Json[$i]['enddatetime'] = date('Y-m-d H:i:s', strtotime($series->end_date));
					$i++;
				}
			}
			else{
			    $Json['success']=false;
			    $Json['message']='Sorry,no data available!';
			}
			return response()->json($Json);
       
	}

	
}