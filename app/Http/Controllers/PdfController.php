<?php
namespace App\Http\Controllers;
use DB;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LeagueController;
use App\Helpers\Helpers;
use PDF;
use URL;
use Mpdf;
use Session;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
class PdfController extends Controller {
	
	public function PermanentJoincontest(Request $request){
	    date_default_timezone_set('Asia/Kolkata');
		$current = date('Y-m-d H:i:s');
		$match_time = date('Y-m-d H:i:s', strtotime('-2 minutes', strtotime($current)));
		$findmatches = DB::table('listmatches')->where('start_date', '<=' , $match_time)->where('launch_status','launched')->select('*')->get();
		if(!empty($findmatches->toArray())){
    	foreach ($findmatches as $value) {
        		$start_date  = date('Y-m-d H:i:s', strtotime($value->start_date));
        	    $joinedteam=	DB::table('jointeam')->where('matchkey', '=' , $value->matchkey)->whereDate('created_at','<=', $start_date)->get();
				
        	    if(!empty($joinedteam->toArray())){
        	        foreach($joinedteam as $joined){
        	            $data['userid']=$joined->userid;
        	            $data['teamid']=$joined->id;
        	            $data['matchkey']=$joined->matchkey;
        	            $data['players']=$joined->players;
        	            $data['teamnumber']=$joined->teamnumber;
        	            $data['vicecaptain']=$joined->vicecaptain;
        	            $data['captain']=$joined->captain;
        	            $data['points']=$joined->points;
        	            $data['lastpoints']=$joined->lastpoints;
        	            // $data['player_type']=$joined->player_type;
        	            $getdata= DB::table('permanentjoincontest')->where('matchkey',$data['matchkey'])->where('teamid',$data['teamid'])->select('id')->first();
        	            if(empty($getdata)){
        	                 DB::connection('mysql2')->table('permanentjoincontest')->insert($data);
        	            } 
        	        }
        	    }
        	}
	    }
        
	}
	public function getPdfDownload(Request $request){
	 
	    $mpdf = new \Mpdf\Mpdf();
	    $challengeid= $request->get('challengeid');
        $matchkey= $request->get('matchkey');
        $findjoinedleauges = DB::table('joinedleauges')->where('challengeid',$challengeid)->join('registerusers','registerusers.id','=','joinedleauges.userid')->join('permanentjoincontest','permanentjoincontest.teamid','=','joinedleauges.teamid')->select('registerusers.team','registerusers.email','permanentjoincontest.players','permanentjoincontest.vicecaptain','permanentjoincontest.captain','permanentjoincontest.teamnumber','permanentjoincontest.created_at as createddate')->get();
      
        // content //
        $findmatchdetails = DB::table('listmatches')->where('matchkey',$matchkey)->select('name','format','start_date')->first();
        $challengedetails = DB::table('matchchallenges')->where('id',$challengeid)->select('matchchallenges.win_amount')->first();
        $findmatchplayers = DB::table('matchplayers')->where('matchkey',$matchkey)->select('name','playerid')->get();
        $sdfghj = $findjoinedleauges->toArray();
        $content="";
        if(!empty($sdfghj)){
		$content='<div class="col-md-12 col-sm-12" style="margin-top:20px;">
				    <div class="col-md-12 col-sm-12 text-center" style="margin-top:20px;text-align:center">
						<div class="col-md-12 col-sm-12">
						    <img src="'.URL::asset('public/'.(Helpers::settings()->logo ?? '')).'" style="width:100px;">
						</div>';
						$content.='<div class="col-md-12 col-sm-12">
							<p> <strong style="coloe:#E09D3B;">Pdf Generated On: </strong>'.$findjoinedleauges[0]->createddate.'</p>
						</div>
					</div>
				</div>';
        }
        $content.='<div class="col-md-12 col-sm-12" style="margin-top:20px;">
							<table style="width:100%" border="1">
							 <tr style="background:#3C7CC4;color:#fff;text-align:center">';
								$challengename = "";
								$challengename = 'Win-'.$challengedetails->win_amount;
								$content.='<th style="color:#fff !important;" colspan="'.(count($findmatchplayers)+1).'">'.$challengename.'( '.$findmatchdetails->name.' '.$findmatchdetails->format.' '.$findmatchdetails->start_date.')</th>
								
							  </tr>
							  <tr style="background:#ccc;color:#333;text-align:center">
								<th>Display User Name</th>';
								if(!empty($findmatchplayers)){
									foreach($findmatchplayers as $player1){
										$content.='<th>'.ucwords($player1->name).'</th>';
									}
								}
				$content.='</tr>';
				if(!empty($findjoinedleauges)){
					foreach($findjoinedleauges as $joinleauge){
						
						$content.='<tr>
							<td style="text-align:center">';
							if($joinleauge->team!=""){ 
								$content.=ucwords($joinleauge->team).'<br> ( '.$joinleauge->teamnumber.' )';
							} 
							else{
								 $content.= ucwords($joinleauge->email).'<br> ( '.$joinleauge->teamnumber.' )';
							}
							$content.='</td>';
							$jointeam = $joinleauge->players;
							$explodeplayers = explode(',',$jointeam);
							foreach($findmatchplayers as $player2){
								
								$content.='<td class="text-center" style="text-align:center;">';
								if(in_array($player2->playerid,$explodeplayers)){
									$content.= 'Y';
									
									if($player2->playerid==$joinleauge->vicecaptain){
										$content.= '(VC)';
									}
									if($player2->playerid==$joinleauge->captain){
										$content.= '(C)';
									}
								}
								$content.='</td>';
							} 
						  $content.='</tr>';
					}	
				}
				$content.='</table>
				</div>';
				ini_set("pcre.backtrack_limit", "5000000");
       $mpdf->WriteHTML($content);
        $mpdf->Output(''.(Helpers::settings()->project_name ?? '').'.pdf','D');
         
  }
}
?>