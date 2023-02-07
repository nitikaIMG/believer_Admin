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
class OffersController extends Controller {
	
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
	
    public function getOffersandroid(){
		$this->accessrules();
		$findoffers = DB::table('offers')->where('popular','1')->orderBY('id','Asc')->get();
		$Json=array();
		if(!empty($findoffers)){
			$i=0;
			foreach($findoffers as $post){
				$url = $this->geturl();
				$Json[$i]['id'] = $post->id;
				$Json[$i]['title'] = '';
				$Json[$i]['image'] = $url.'uploads/offers/'.$post->image;
				$i++;
			}
		}
		echo json_encode($Json);
		die;
	}
	
	public function getOffers(){
		$this->accessrules();
		$findoffers = DB::table('offers')->orderBY('id','DESC')->paginate(15);
		return view('offers.viewoffer',compact('findoffers'));
	}
	
	public function addOffer(Request $request){
	    if ($request->isMethod('post')){
	        $rules = array(
				'title' => 'required',
				'minamount'=>'required',
				'maxamount'=>'required',
				'bonus_type'=>'required',
				'prize_type'=>'required',
				'bonus'=>'required',
				'offercode'=>'required',
				'start_date'=>'required',
				'expire_date'=>'required',
				'user_time'=>'required',
			);
			$validator = Validator::make(request()->all(), $rules);
			if($validator->fails()){
					return Redirect::back()
						->withErrors($validator)
						->withInput(request()->except('password'));
			}
			$current = date('Y/m/d h:i');
            $start=date('Y/m/d',strtotime($request->start_date));
            $end=date('Y/m/d',strtotime($request->expire_date));
            if($end<$current){
                
                return redirect()->back()->with('danger','Expiration Date Should be after today.');
            }
            if($end<$start){
                    return redirect()->back()->with('danger','Expiration Date Should be after start date.');
				}
			
			if(request()->get('minamount') > request()->get('maxamount')){
				return redirect()->back()->with('danger', 'Minimum amount should be less than to maximum amount.');
			}

			$input = request()->all();
			$check = DB::table('offers')->where('title',$input['title'])->first();
			if(empty($check)){
                $input['expire_date'] = date('Y-m-d',strtotime($input['expire_date']));
                $input['start_date'] = date('Y-m-d',strtotime($input['start_date']));
                $input['offercode'] = strtoupper($input['offercode']);
                unset($input['_token']);
    	        DB::connection('mysql2')->table('offers')->insert($input);
    	        return redirect()->back()->with('success','Offer save successfully');
			}else{
    	        return redirect()->back()->with('danger','This title is already in used');
			}
    	        
	    }
	    return view('offers.addoffer');
	}
	
	
	public function editoffer(Request $request,$id){
	    $ofers = DB::table('offers')->where('id',unserialize(base64_decode($id)))->first();
	    if($request->isMethod('post')){
	        $rules = array(
				'title' => 'required',
				'minamount'=>'required',
				'maxamount'=>'required',
				'bonus_type'=>'required',
				'bonus'=>'required',
				'start_date'=>'required',
				'offercode'=>'required',
				'expire_date'=>'required',
				'user_time'=>'required',
			);
			$validator = Validator::make(request()->all(), $rules);
			if($validator->fails()){
					return Redirect::back()
						->withErrors($validator)
						->withInput(request()->except('password'));
			}
			$input = request()->all();

			if(request()->get('minamount') > request()->get('maxamount')){
				return redirect()->back()->with('danger', 'Minimum amount should be less than to maximum amount.');
			}

            $input['expire_date'] = date('Y-m-d',strtotime($input['expire_date']));
            $input['start_date'] = date('Y-m-d',strtotime($input['start_date']));
            $input['offercode'] = strtoupper($input['offercode']);
			unset($input['_token']);
	        DB::connection('mysql2')->table('offers')->where('id',unserialize(base64_decode($id)))->update($input);
	        return redirect()->action('OffersController@editoffer',$id)->with('success','Offer update successfully');
	     
	    }
	    return view('offers.editoffer',compact('ofers'));
	}
	
	public function deleteoffer($id){
	    $oldimage = DB::table('offers')->where('id',unserialize(base64_decode($id)))->first();
	
	    DB::connection('mysql2')->table('offers')->where('id',unserialize(base64_decode($id)))->delete();
	    return redirect()->action('OffersController@getOffers')->with('success','Offer delete successfully');
	}
	
	
	public function offerdeposits(Request $request){
	    Helpers::timezone();
		Helpers::setHeader(200);
		$input = $request->all();
		$geturl = Helpers::geturl();
			$user = Helpers::isAuthorize($request);
           if($user){ 
               $currentdate = date('Y-m-d H:i:s');
		        $findoffers = DB::table('offers')->whereDate('start_date','<=', $currentdate)->whereDate('expire_date','>=', $currentdate)->get();
		        $Json = array();
		        if(!empty($findoffers)){
    		        $i=0;
    		        foreach($findoffers as $val){
    		                $offeruserused = DB::table('used_offer')->where('user_id',$user->id)->where('offer_id',$val->id)->count();
    		                if($offeruserused <  $val->user_time){
            		            $Json[$i]['offerid'] = $val->id;
            		            $Json[$i]['title'] = $val->title;
            		            $Json[$i]['minamount'] = $val->minamount;
            		            $Json[$i]['amount'] = $val->maxamount;
            		            $Json[$i]['offercode'] = $val->offercode;
            		            $Json[$i]['bonus'] = $val->bonus;
            		            $Json[$i]['bonus_type'] = $val->bonus_type;
            		            $Json[$i]['offercode'] = $val->offercode;
            		            $Json[$i]['start_date'] = $val->start_date;
            		            $Json[$i]['expire_date'] = $val->expire_date;
            		            $Json[$i]['used_time'] = $val->user_time;
    		                }
    		            $i++;
    		        }
		        }
		        return response()->json(array_values($Json));
           }
	}
		public function offerdepositsnew(Request $request){
	    Helpers::timezone();
		Helpers::setHeader(200);
		$input = $request->all();
		$geturl = Helpers::geturl();
			$user = Helpers::isAuthorize($request);
           if($user){ 
			   $currentdate = date('Y-m-d H:i:s');
			   
		        $findoffers = DB::table('offers')->whereDate('start_date','<=', $currentdate)->whereDate('expire_date','>=', $currentdate)->get();
		        $Json = array();
		        if(!empty($findoffers)){
    		        $i=0;
    		        foreach($findoffers as $val){
    		                $offeruserused = DB::table('used_offer')->where('user_id',$user->id)->where('offer_id',$val->id)->count();
    		                if($offeruserused <  $val->user_time){
            		            $Json[$i]['offerid'] = $val->id;
            		            $Json[$i]['title'] = $val->title;
            		            $Json[$i]['minamount'] = $val->minamount;
            		            $Json[$i]['amount'] = $val->maxamount;
            		            $Json[$i]['offercode'] = $val->offercode;
            		            $Json[$i]['bonus'] = $val->bonus;
            		            $Json[$i]['bonus_type'] = $val->bonus_type;
            		            $Json[$i]['offercode'] = $val->offercode;
            		            $Json[$i]['start_date'] = $val->start_date;
            		            $Json[$i]['expire_date'] = $val->expire_date;
            		            $Json[$i]['used_time'] = $val->user_time;
            		            $Json[$i]['description'] = $val->description;
    		                }
    		            $i++;
    		        }
		        }
		        return response()->json(array_values($Json));
           }
	}
	
	public function checkpromocode(Request $request){
	    Helpers::timezone();
		Helpers::setHeader(200);
		$input = $request->all();
		$geturl = Helpers::geturl();
			$user = Helpers::isAuthorize($request);
           	if($user){ 
               $currentdate = date('Y-m-d H:i:s');
               $promocode =  $request->get('promocode');
               $amount =  $request->get('amount');
			   $json = array();
			   $offerexist = DB::table('offers')->where('offercode',$promocode)->whereDate('start_date','<=', $currentdate)->whereDate('expire_date','>=', $currentdate)->first();
			   // dump($offerexist);
                if(!empty($offerexist)){
                	// $offerdata = DB::table('offers')->where('id', $request->get('offerid'))->select('*')->first();
                    if (!empty($offerexist)) {
                        $max = $offerexist->maxamount;
                        $min = $offerexist->minamount;
                        if ($max >= $amount && $min <= $amount) {
                        	$usedTimes = DB::table('used_offer')->where('offer_id',$offerexist->id)->where('user_id',$user->id)->count();
                        	if($usedTimes>$offerexist->user_time){
                        		$json['status'] = 0;
			                    $json['offerid'] = 0;
			                    $json['success'] = false;
			                    $json['msg'] = 'You\'ve Reached your limit';
			                    return response()->json([$json]);die;
                        	}else{
                        		$json['offerid'] = $offerexist->id;
                        		$json['status'] = 1;
			                    $json['success'] = true;
			                    $json['msg'] = 'Promo code applied';
			                    \Log::info(json_encode($json));
			                    return response()->json([$json]);die;
                        	}
                            
                        }else{
                        	$json['status'] = 0;
		                    $json['offerid'] = 0;
		                    $json['success'] = false;
		                    $json['msg'] = 'Amount should be between '.$min.' Rs to '.$max.' Rs.';
		                    return response()->json([$json]);die;
                        }
                    }
                    $json['status'] = 1;
                    $json['success'] = true;
                    $json['msg'] = 'Promo code applied';
                }else{
                    $json['status'] = 0;
                    $json['offerid'] = 0;
                    $json['success'] = false;
                    $json['msg'] = 'Promo code is not available';
                }
                return response()->json([$json]); die;
           	}
	}
}
?>