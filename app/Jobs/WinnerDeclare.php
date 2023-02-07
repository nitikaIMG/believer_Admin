<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Controllers\JobController;
use DB;
use App\Helpers\Helpers;

class WinnerDeclare implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $matchkey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($matchkey)
    {
        $this->matchkey = $matchkey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->winner_declare($this->matchkey);
        $this->update_listmatches_status($this->matchkey);
    }

    # code rewrite by praveen on date 16 july 2020
	public function winner_declare($matchkey){

        $matchkey = $this->matchkey;
        
        $match = DB::table('listmatches')
                    ->where('matchkey',$matchkey)
                    ->where('final_status','!=','winnerdeclared')
                    ->where('final_status','!=','IsAbandoned')
                    ->where('final_status','!=','IsCanceled')
                    ->select('series', 'name')
                    ->first();
        
	    if( !empty($match) ){

            $allchallenges = DB::table('matchchallenges')
                                ->where('matchkey',$matchkey)
                                ->where('status','!=','canceled')
                                ->select(
                                    'win_amount','contest_type','winning_percentage','pricecard_type','entryfee','id','joinedusers','is_bonus','bonus_percentage'
                                )
                                ->get();
            
            if(!empty($allchallenges)) { 

                foreach($allchallenges as $challenge) {

                    $joinedusers = DB::table('joinedleauges')
                                    ->join('matchchallenges','matchchallenges.id','=','joinedleauges.challengeid')
                                    ->where('joinedleauges.matchkey',$matchkey)
                                    ->where('joinedleauges.challengeid',$challenge->id)
                                    ->join('jointeam','jointeam.id','=','joinedleauges.teamid')
                                    ->select('joinedleauges.userid','points','joinedleauges.id as jid')->get();

                    $joinedusers = $joinedusers->toArray();

                    if(!empty($joinedusers)){

                        // calculate the price cards //
                        if($challenge->contest_type=='Amount'){

                            $prc_arr = array();
                            if($challenge->pricecard_type=='Amount'){

                                $matchpricecards = DB::table('matchpricecards')
                                                    ->where('challenge_id',$challenge->id)
                                                    ->select('min_position','max_position','price')->get();

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

                            } else {
                                $matchpricecards =DB::table('matchpricecards')
                                                    ->where('challenge_id',$challenge->id)
                                                    ->select('min_position','max_position','price_percent')
                                                    ->get();

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
                            $get_challenge_joinedusers = $challenge->joinedusers;
                            $toWin = floor($get_challenge_joinedusers * $getwinningpercentage / 100);
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
                                $poin_user["'".$usr['points']."'"]['joinedid'] = $ids_arr;

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
                                $final_poin_user[$ps['joinedid'][0]]['points'] = $ks;
                                $final_poin_user[$ps['joinedid'][0]]['amount'] = $prc_arr[$ps['min']];
                                $final_poin_user[$ps['joinedid'][0]]['rank'] = $ps['min'];
                                $final_poin_user[$ps['joinedid'][0]]['userid'] = $ps['id'][0];
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
                            foreach($final_poin_user as $fpuskjoinid => $fpusv){

                                $fpusk = $fpusv['userid'];
                                $winningdata = DB::table('finalresults')
                                                ->where('joinedid',$fpuskjoinid)
                                                ->select('id')
                                                ->first();

                                if(empty($winningdata)) {
                                    $fres = array();

                                    $challengeid = $challenge->id;
                                    $seriesid = $match->series;
                                    $transactionidsave = 'WIN-'.rand(1000,99999).$challengeid.$fpuskjoinid;
                                    $fres['userid'] = $fpusk;
                                    $fres['points'] = str_replace("'", "", $fpusv['points']);
                                    $fres['amount'] = round($fpusv['amount'],2);
                                    $fres['rank'] = $fpusv['rank'];
                                    $fres['matchkey'] = $matchkey;
                                    $fres['challengeid'] = $challengeid;
                                    $fres['seriesid'] = $seriesid;
                                    $fres['transaction_id'] = $transactionidsave;
                                    $fres['joinedid'] = $fpuskjoinid;

                                    $findalreexist = DB::table('finalresults')
                                                        ->where('joinedid',$fpuskjoinid)
                                                        ->where('userid',$fpusk)
                                                        ->select('id')
                                                        ->first();

                                    if(empty($findalreexist)) {

                                        DB::connection('mysql2')->table('finalresults')->insert($fres);

                                        $finduserbalance = DB::table('userbalance')
                                                            ->where('user_id',$fpusk)
                                                            ->select('balance','winning','bonus')
                                                            ->first();

                                        if(!empty($finduserbalance)) {
                                            
                                            if($fpusv['amount'] > 10000){

                                                $datatr = array();
                                                $dataqs = array();
                                                $tdsdata['tds_amount'] = (31.2/100)*$fpusv['amount'];
                                                $tdsdata['amount'] = $fpusv['amount'];
                                                $remainingamount = $fpusv['amount']-$tdsdata['tds_amount'];
                                                $tdsdata['userid'] = $fpusk;
                                                $tdsdata['challengeid'] = $challenge->id;
                                                DB::connection('mysql2')->table('tdsdetails')->insert($tdsdata);

                                                $fpusv['amount'] = $remainingamount;
                                                //user balance//
                                                $registeruserdetails = DB::table('registerusers')
                                                                        ->where('id',$fpusk)
                                                                        ->select('email')
                                                                        ->first();

                                                $findlastow = DB::table('userbalance')
                                                                ->where('user_id',$fpusk)
                                                                ->first();

                                                $dataqs['winning'] = number_format($findlastow->winning+$fpusv['amount'],2, ".", "");
                                                
                                                DB::connection('mysql2')->table('userbalance')
                                                    ->where('id',$findlastow->id)
                                                    ->update($dataqs);
                                                
                                                //transactions entry//
                                                $datatr['transaction_id'] = $transactionidsave;;
                                                $datatr['type'] = 'Challenge Winning Amount';
                                                $datatr['transaction_by'] = Helpers::settings()->short_name ?? '';
                                                $datatr['amount'] = $fres['amount'];
                                                $datatr['paymentstatus'] = 'confirmed';
                                                $datatr['challengeid'] = $challenge->id;
                                                $datatr['win_amt'] = $fres['amount'];
                                                $datatr['bal_bonus_amt'] = $finduserbalance->bonus;
                                                $datatr['bal_win_amt'] = $dataqs['winning'];
                                                $datatr['bal_fund_amt'] = $finduserbalance->balance;
                                                $datatr['userid'] = $fpusk;
                                                $datatr['total_available_amt'] = $finduserbalance->balance+$dataqs['winning']+$finduserbalance->bonus;
                                                DB::connection('mysql2')->table('transactions')->insert($datatr);
                                                
                                                $datanot['title'] = 'You won amount Rs.'.$fpusv['amount'].' and 31.2% amount of '.$tdsdata['amount'].' deducted due to TDS.';
                                                $datanot['userid'] = $fpusk;
                                                DB::connection('mysql2')->table('notifications')->insert($datanot);

                                                //push notifications//
                                                $titleget = 'Congrats! You won a match.';
                                                // Helpers::sendnotification($titleget,$datanot['title'],'',$fpusk);
                                                $challengename = 'Win-'.$challenge->win_amount;
                                                // $datamessage['email'] = $registeruserdetails->email;
                                                // $datamessage['subject'] = 'Congrats you won a challenge!';
                                                $content='<p><strong>Dear Challenger </strong></p>';
                                                $content.='<p>Congratulations. You have won a challenge of '.$challengename.' for match '.$match->name.'  with points '.$fres['points'].'. An amount of Rs. '.$fpusv['amount'].' is transfered to your wallet and 31.2% amount is deducted due to Tax deduction as per government regulations. Enjoy!</p>';
                                                    $content.='<p> For details , please check account balance.</p>';
                                                // $datamessage['content']= Helpers::Mailbody1($content);
                                                // Helpers::mailSmtpSend($datamessage);
                                            } else {
                                                $datatr = array();
                                                $dataqs = array();
                                                //user balance//
                                                $registeruserdetails = DB::table('registerusers')
                                                                        ->where('id',$fpusk)
                                                                        ->select('email')
                                                                        ->first();

                                                $findlastow = DB::table('userbalance')
                                                                ->where('user_id',$fpusk)
                                                                ->first();

                                                $dataqs['winning'] =  number_format($findlastow->winning+$fpusv['amount'],2, ".", "");

                                                DB::connection('mysql2')->table('userbalance')->where('id',$findlastow->id)->update($dataqs);

                                                if($fpusv['amount']>0){
                                                    //transactions entry//
                                                    $datatr['transaction_id'] = $transactionidsave;;
                                                    $datatr['type'] = 'Challenge Winning Amount';
                                                    $datatr['transaction_by'] = Helpers::settings()->short_name ?? '';
                                                    $datatr['amount'] = $fpusv['amount'];
                                                    $datatr['paymentstatus'] = 'confirmed';
                                                    $datatr['challengeid'] = $challenge->id;
                                                    $datatr['win_amt'] = $fpusv['amount'];
                                                    $datatr['bal_bonus_amt'] = $finduserbalance->bonus;
                                                    $datatr['bal_win_amt'] = $dataqs['winning'];
                                                    $datatr['bal_fund_amt'] = $finduserbalance->balance;
                                                    $datatr['userid'] = $fpusk;
                                                    $datatr['total_available_amt'] = $finduserbalance->balance+$dataqs['winning']+$finduserbalance->bonus;
                                                    DB::connection('mysql2')->table('transactions')->insert($datatr);
                                                
                                                    //notifications entry//
                                                    $datanot['title'] = 'You won amount Rs.'.$fpusv['amount'];
                                                    $datanot['userid'] = $fpusk;
                                                    DB::connection('mysql2')->table('notifications')->insert($datanot);

                                                    //push notifications//
                                                    $titleget = 'Congrats! You Won a match!';
                                                    // Helpers::sendnotification($titleget,$datanot['title'],'',$fpusk);
                                                        
                                                    $challengename = 'Win-'.$challenge->win_amount;
                                                    // $datamessage['email'] = $registeruserdetails->email;
                                                    // $datamessage['subject'] = 'Congrats you won a challenge!';
                                                    $content='<p><strong>Dear Challenger </strong></p>';
                                                    $content.='<p>Congratulations. You have won a challenge of '.$challengename.' for match '.$match->name.'  with points '.$fres['points'].'. An amount of Rs. '.$fpusv['amount'].' is transfered to your wallet. Enjoy!</p>';
                                                    $content.='<p> <strong> Note:- </strong> Winning amount will be credited in your wallet after tax deductions as per government regulations.</p>';
                                                    // $datamessage['content']= Helpers::Mailbody1($content);
                                                    //Helpers::mailSmtpSend($datamessage);
                                                }
                                            }
                                        }
                                        
                                    }
                                }
                            }
                        }
                    }
                }
            }

	    } else {
	        echo "you cannot declare winner of this match" ;
	    }
    }
    
    public function update_listmatches_status($matchkey)
    {
        $input['final_status'] = 'winnerdeclared';
        DB::connection('mysql2')->table('listmatches')->where('matchkey',$matchkey)->update($input);
    }
}
