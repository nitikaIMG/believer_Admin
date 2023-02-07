<?php

namespace App\Http\Controllers;
use DB;
use Auth;
use Session;
use Mail;
use File;
use Excel;
use Socialite;
use bcrypt;
use form;
use Config;
use timthumb;
use Redirect;
use PHPExcel_IOFactory;
use App\Mail\SendMailable;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use Yajra\Datatables\Datatables;

class ReportController extends Controller{
	
	public function withdraw_report(Request $request){
        $data = DB::table('withdraw')->join('registerusers','withdraw.user_id','registerusers.id')
        ->where('user_status','0')->select('registerusers.*','withdraw.id as withdraw_id','withdraw.*');
        $start_date =""; $end_date="";
        if ($request->isMethod('get')){
               if(request()->has('startdate')){
                   $start_date = request('startdate');
                   if($start_date!=""){
                       $data =$data->whereDate('withdraw.approved_date', '>=',date('Y-m-d H:i:s',strtotime($start_date)));
                   }
               }
               // search for the end date //
               if(request()->has('enddate')){
                   $end_date = request('enddate');
                   if($end_date!=""){
                       $data =$data->whereDate('withdraw.approved_date', '<=',date('Y-m-d',strtotime($end_date)));
                   }
               }
               $sum = $data->sum('withdraw.amount');
              $data = $data->paginate(10)->withPath('?startdate='.$start_date.'&enddate='.$end_date);
                    
        }else{
            $sum = $data->sum('amount');
            $data = $data->paginate(10);
        }
        return view('report.withdraw_report',compact('data','sum'));
    }
	public function amount_report(Request $request){

		$data = DB::table('leaugestransactions')->join('registerusers','leaugestransactions.user_id','registerusers.id')
				->where('registerusers.user_status','0')->select('registerusers.*','leaugestransactions.id as amt_id','leaugestransactions.*');
		$start_date ="";$end_date="";
		if ($request->isMethod('get')){
	        if(request()->has('startdate')){
	            $start_date = request('startdate');
	            if($start_date!=""){
	                $data =$data->whereDate('leaugestransactions.created_at', '>=',date('Y-m-d H:i:s',strtotime($start_date)));
	            }
	        }
	        // search for the end date //
	        if(request()->has('enddate')){
	            $end_date = request('enddate');
	            if($end_date!=""){
	                $data =$data->whereDate('leaugestransactions.created_at', '<=',date('Y-m-d',strtotime($end_date)));
	            }
	        }
	        if(request()->has('enddate') && request()->has('startdate')){
	            $end_date = request('enddate');
	            if($end_date!=""){
	                $data =$data->whereDate('leaugestransactions.created_at', '>=',date('Y-m-d',strtotime($start_date)))
	                ->whereDate('leaugestransactions.created_at', '<=',date('Y-m-d',strtotime($end_date)));
	            }
	        }
	        $sum = $data->sum('leaugestransactions.balance');
	       $data = $data->paginate(10)->withPath('?startdate=' . $start_date . '&enddate=' . $end_date);
	       	       
		}else{
			$data = $data->paginate(10);
			$sum = DB::table('leaugestransactions')->sum('balance');
		}
		return view('report.amount_report',compact('data','sum'));
	}
	public function bonus_report(Request $request){
		$data = DB::table('transactions')->join('registerusers','transactions.userid','registerusers.id')
				->where('registerusers.user_status','0')->select('registerusers.*','transactions.id as amt_id','transactions.*');
		
		if ($request->isMethod('get')){
			$start_date ="";$end_date="";
	        if(request()->has('startdate')){
	            $start_date = request('startdate');
	            if($start_date!=""){
	                $data =$data->whereDate('transactions.updated_at', '>=',date('Y-m-d H:i:s',strtotime($start_date)));
	            }
	        }
	        if(request()->has('enddate')){
	            $end_date = request('enddate');
	            if($end_date!=""){
	                $data =$data->whereDate('transactions.updated_at', '<=',date('Y-m-d',strtotime($end_date)));
	            }
	        }
	        $sum = $data->sum('transactions.bonus_amt');
	       $data = $data->paginate(10)->withPath('?startdate=' . $start_date . '&enddate=' . $end_date);      
		}else{
			$data = $data->paginate(10);
			$sum = DB::table('transactions')->sum('bonus_amt');
		}




		return view('report.bonus_report',compact('data','sum'));
	}
	public function updatepointss($matchkey){
	 $abcdd = DB::table('listmatches')->where('matchkey',$matchkey)->first();
    	if(!empty($abcdd)){
    		$allchallenges =DB::table('matchchallenges')->where('matchkey',$matchkey)->where('status','!=','canceled')->select('win_amount','contest_type','winning_percentage','pricecard_type','entryfee','id','joinedusers','is_bonus','bonus_percentage')->get();
    		if(!empty($allchallenges)){ 
    			foreach($allchallenges as $challenge){
    				$joinedusers = DB::table('joinedleauges')->join('matchchallenges','matchchallenges.id','=','joinedleauges.challengeid')->where('joinedleauges.matchkey',$matchkey)->where('joinedleauges.challengeid',$challenge->id)->join('jointeam','jointeam.id','=','joinedleauges.teamid')->select('joinedleauges.userid','points','joinedleauges.id as jid')->get();
    			
    				$joinedusers = $joinedusers->toArray();
    				if(!empty($joinedusers)){
    				    if($challenge->contest_type=='Amount'){
						$prc_arr = array();
    						if($challenge->pricecard_type=='Amount'){
    							$matchpricecards =DB::table('matchpricecards')->where('challenge_id',$challenge->id)->select('min_position','max_position','price')->get();
    							$matchpricecards = $matchpricecards->toArray();
    							if(!empty($matchpricecards)){
    								foreach($matchpricecards as $prccrd){
    									$min_position=$prccrd->min_position;
    									$max_position=$prccrd->max_position;
    									for($i=$min_position;$i<$max_position;$i++){
    										$prc_arr[$i+1]=$prccrd->price;
    									}
    								}
    							}
    							else{
    								$prc_arr[1]=$challenge->win_amount;
    							}
    						}
    						else{
    							$matchpricecards =DB::table('matchpricecards')->where('challenge_id',$challenge->id)->select('min_position','max_position','price_percent')->get();
    							$matchpricecards = $matchpricecards->toArray();
    							if(!empty($matchpricecards)){
    								foreach($matchpricecards as $prccrd){
    									$min_position=$prccrd->min_position;
    									$max_position=$prccrd->max_position;
    									for($i=$min_position;$i<$max_position;$i++){
    										$prc_arr[$i+1]=($prccrd->price_percent/100)*($challenge->entryfee * $challenge->joinedusers);
    									}
    								}
    							}
    							else{
    								$prc_arr[1]=$challenge->win_amount;
    							}
    							
    						}
    					}
    					else if($challenge->contest_type=='Percentage'){
    						$getwinningpercentage = $challenge->winning_percentage;
    						$gtjnusers = $challenge->joinedusers;
    						$toWin = floor($gtjnusers*$getwinningpercentage/100);
    						$prc_arr=array();
    						for($i = 0; $i< $toWin; $i++){
    							$prc_arr[$i+1]=$challenge->win_amount;
    						}
    					}
    					// get the number of users //
    					$user_points = array();
    					if(!empty($joinedusers)){
    						$lp=0;
    						foreach($joinedusers as $jntm){
    							$user_points[$lp]['id']=$jntm->userid;
    							$user_points[$lp]['points']=$jntm->points;
    							$user_points[$lp]['joinedid']=$jntm->jid;
    							$lp++;
    						}
    					}
    					Helpers::sortBySubArrayValue($user_points, 'points', 'desc');
    					$poin_user = array();
    					foreach($user_points as $usr){
    						$ids_str="";
    						$userids_str="";
    						$ids_arr = array();
    						$userids_arr = array();
    						if(array_key_exists("'".$usr['points']."'",$poin_user)){
    							if(isset($poin_user["'".$usr['points']."'"]['joinedid'])){
    								$ids_str=implode(',',$poin_user["'".$usr['points']."'"]['joinedid']);
    							}
    							$ids_str=$ids_str.','.$usr['joinedid'];
    							$ids_arr=explode(',',$ids_str);
    							$poin_user["'".$usr['points']."'"]['joinedid']=$ids_arr;
    							// for user id //
    							if(isset($poin_user["'".$usr['points']."'"]['id'])){
    								$userids_str=implode(',',$poin_user["'".$usr['points']."'"]['id']);
    							}
    							$userids_str=$userids_str.','.$usr['id'];
    							$userids_arr=explode(',',$userids_str);
    							$poin_user["'".$usr['points']."'"]['id']=$userids_arr;
    							$poin_user["'".$usr['points']."'"]['points']=$usr['points'];
    						}
    						else{
    						  $poin_user["'".$usr['points']."'"]['id'][0]=$usr['id'];
    						  $poin_user["'".$usr['points']."'"]['points']=$usr['points'];
    						  $poin_user["'".$usr['points']."'"]['joinedid'][0]=$usr['joinedid'];
    						}	
    					}
    					Helpers::sortBySubArrayValue($poin_user, 'points', 'desc');
    					$win_usr=array();
    					$win_cnt=0;
    					$count=count($prc_arr);
    					foreach($poin_user as $kk=>$pu){
    						if($win_cnt < $count){
    							$win_usr[$kk]['min']=$win_cnt+1;
    							$win_cnt=$win_cnt+count($pu['joinedid']);
    							$win_usr[$kk]['max']=$win_cnt;
    							$win_usr[$kk]['count']=count($pu['joinedid']);
    							$win_usr[$kk]['joinedid']=$pu['joinedid'];
    							$win_usr[$kk]['id']=$pu['id'];
    						}
    						else{
    							break;
    						}
    					}
    					$final_poin_user=array();
    					foreach($win_usr as $ks=>$ps){
    						if($ps['count']==1){
    							$final_poin_user[$ps['joinedid'][0]]['points']=$ks;
    							$final_poin_user[$ps['joinedid'][0]]['amount']=$prc_arr[$ps['min']];
    							$final_poin_user[$ps['joinedid'][0]]['rank']=$ps['min'];
    							$final_poin_user[$ps['joinedid'][0]]['userid']=$ps['id'][0];
    						}
    						else{
    							$ttl=0;$avg_ttl=0;
    							for($jj=$ps['min'];$jj<=$ps['max'];$jj++){
    								$sm=0;
    								if(isset($prc_arr[$jj])){
    									$sm=$prc_arr[$jj];
    								}
    								$ttl=$ttl+$sm;
    							}
    							$avg_ttl=$ttl/$ps['count'];
    							foreach($ps['joinedid'] as $keyuser=>$fnl){
    								$final_poin_user[$fnl]['points']=$ks;
    								$final_poin_user[$fnl]['amount']=$avg_ttl;
    								$final_poin_user[$fnl]['rank']=$ps['min'];
    								$final_poin_user[$fnl]['userid']=$ps['id'][$keyuser];
    							}
    						}
						}
						
						if(!empty($final_poin_user)){
    						foreach($final_poin_user as $fpuskjoinid=>$fpusv){
    						    $pointsdata = array();
    						    $pointsdata['matchkey'] = $matchkey;
    						    $pointsdata['joinid'] = $fpuskjoinid;
    						    $pointsdata['points'] = $fpusv['points'];
    						    $pointsdata['amount'] = $fpusv['amount'];
    						    $pointsdata['rank'] = $fpusv['rank'];
    						    $pointsdata['userid'] = $fpusv['userid'];
    						    $joindtaa = DB::table('updatepoints')->where('joinid',$fpuskjoinid)->first();
    						    if(empty($joindtaa)){
    						    DB::connection('mysql2')->table('updatepoints')->insert($pointsdata);
    						    }else{
    						       DB::connection('mysql2')->table('updatepoints')->where('joinid',$fpuskjoinid)->update($pointsdata);
    						    }
    						}
    				    }
    				}
    			}
    		}
    		$fff = array();
    		$fff['pointsstatus'] = 1;
    		DB::connection('mysql2')->table('listmatches')->where('matchkey',$matchkey)->update($fff);
    	}
    	 return redirect()->back()->with('success','Points Updated Successfully');
	}
}