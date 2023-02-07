<?php
namespace App\Http\Controllers;
	use DB;
	use Session;
	use bcrypt;
	use Config;
	use Redirect;
	use File;
	use Auth;
	use Hash;
	use Carbon\Carbon;
	use Illuminate\Http\Request; 
	use App\Http\Controllers\Controller; 
	use App\Http\Requests;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Support\Facades\Input;
	use App\Http\Controllers\CricketapiController;
	use App\Helpers\Helpers;

	use App\PointSystem;

class ResultController extends Controller
{
	
// result section
	public function match_result(){
		$f_type = request()->get('fantasy_type');
		
		$f_type = !empty($f_type) ? $f_type : 'Cricket';
		
		$findalllistmatches = DB::table('series')->where('series.status','=','opened')->where('series.fantasy_type',$f_type)->join('listmatches','listmatches.series','=','series.id')->select('series.name as series_name','series.id as series_id','listmatches.name as listmatches_title','series.start_date as created_at','series.end_date as end_date','listmatches.matchkey as listmatches_matchkey')->groupBY('series.id')->get();

		return view('matches.match_result',compact('findalllistmatches'));
	}	

	public function match_detail($id){
		$findalllistmatches = DB::table('series')->join('listmatches','listmatches.series','=','series.id')->join('matchchallenges','matchchallenges.matchkey','=','listmatches.matchkey')->where('series.id',$id)->where('listmatches.launch_status','launched')->select('matchchallenges.id as matchchallenges_id','listmatches.name as listmatches_title','listmatches.start_date as listmatches_start_date','listmatches.matchkey as listmatches_matchkey','series.id as series_id','listmatches.status as listmatches_status','listmatches.launch_status as listmatches_launch_status','listmatches.final_status as listmatches_final_status', DB::raw('count(matchchallenges.matchkey) as total_challenge'), DB::raw('sum(matchchallenges.joinedusers) as total_joinedusers'))->groupBY('listmatches.matchkey')->orderBy('listmatches.start_date','DESC')->get();

		return view('matches.match_detail',compact('findalllistmatches'));
		}

		public function updatescores($matchkey){
			$findmatchtype = DB::table('listmatches')->where('matchkey',$matchkey)->select('format','team1','team2','real_matchkey')->first();
		$getdata = $this->getscoresupdates($findmatchtype->real_matchkey,$matchkey);
		if($getdata==1){
			return redirect()->action('ResultController@match_points',$matchkey)->with('success','Scores has been refreshed.!');
		}else{
			return redirect()->action('ResultController@match_points',$matchkey)->with('success','Scores has been refreshed.!');
		}
		}
		public function join_users($matchkey){
			return view('matches.join_users',compact('matchkey'));
		}
		public function select_join_person(Request $request){
			if ($request->isMethod('post')){
				$input = $request->all();
				$matchkey = $input['matchkey'];
				$contest = $input['contest_type'];

				if($contest == 'Amount'){
			return view('matches.amount_based_report',compact('matchkey'));
		}
		else if($contest == 'Percentage'){
			return view('matches.percent_based_report',compact('matchkey'));
				}
			}
		}
		public function updatepoints(Request $request){
			if ($request->isMethod('post')){
				$input= $request->all();
				$matchkey = $input['matchkey'];
				$playerid = $input['playerid'];
				$field = $input['field'];
				$value = $input['value'];
				$data[$field]=$value;
				
				DB::connection('mysql2')->table('result_matches')->where('match_key',$matchkey)->where('player_id',$playerid)->update($data);
				$val = DB::table('listmatches')->where('matchkey',$matchkey)->first();
				$match_type = $val->format;
				$showpoints = ResultController::player_point($matchkey,$match_type);
				echo 1;
				die;
			}
		}

		public function player_point($match_key,$match){
			$matchplayers = DB::connection('mysql')->table('result_matches')->where('match_key',$match_key)->get();
			$aaa = $matchplayers->toArray();
			if(!empty($aaa)){
				foreach($matchplayers as $row){
					$resultmatchupdate = array();
					$result = array();
					$duck= $row->duck;
					$player_key = $row->player_key;
					$findplayerrole =DB::connection('mysql')->table('matchplayers')->where('playerid',$row->player_id)->where('matchkey',$match_key)->select('role')->first();
					$runs = $row->runs;
					$wicket = $row->wicket;
					$catch = $row->catch;
					$stumbed = $row->stumbed;
					$boundary = $row->boundary;
					$six = $row->six;
					$fours = $row->fours;
					$maiden_over = $row->maiden_over;
					$thrower = $row->thrower;
					$hitter = $row->hitter;
					$overs = $row->overs;
					$bballs = $row->bball;    //batting balls
					$erate = $row->economy_rate;
					$strikerate = $row->strike_rate;
					$extra_points = $row->extra_points;
					$startingPoint = 0;
					$throwpoint = 0;
					$hittspoints = 0;
					$duckpoint = 0;
					$wkpoints = 0;   //points for wickets
					$catchpoint = 0;
					$stpoint = 0;   // points for stumb
					$boundrypoints = 0;
					$sixpoints = 0;
					$runpoints = 0;
					$centuryPoints = 0;
					$thirtypoints = 0;
					$halcenturypoints = 0;
					$maidenpoints = 0;					
					$total_points=0;
					
					$economypoints = 0;
					$strikePoint = 0;
					$batting_points = 0;
					$bowling_points = 0;
					$fielding_points = 0;
					$wicketbonuspoint = 0;
					$wicketbonuspointdata= DB::connection('mysql2')
					->table('result_matches')->where('match_key',$match_key)->where('wplayerid', $row->player_id)->count();
			
					if(!empty($wicketbonuspointdata)){
						$wicketbonuspoint = $wicketbonuspointdata*8;
					}
					if($match == 't20'){
						if($row->starting11==1 && $row->innings==1){
							$startingpoint = 4;
						}
						else{
							$startingpoint = 0;
						}
						// batting points given //
						if($findplayerrole!='bowler'){
							if($row->duck!=0 || $row->duck!=""){
								$duckpoint = -2;
							}
						}
						$boundrypoints = $fours*1;
						$sixpoints = $six*2;
						$runpoints = $runs*1;
						if(($runs>=30) && ($runs<50)){
							$thirtypoints = 4;
						}
						else if(($runs>=50) && ($runs<100)){
							$halcenturypoints = 8;
						}
						else if($runs>=100){
							$centuryPoints = 16;
						}
						
						// give points for bowling //
						if($wicket==3){
							$wkpoints = $wkpoints+4;
						}
						if($wicket==4){
							$wkpoints = $wkpoints+8;
						}
						if($wicket >=5){
							$wkpoints = $wkpoints+16;
						}
						$wkpoints = $wkpoints+$wicket*25;
						$maidenpoints = $maiden_over*12;
						
						// fielding points //
						
						$catchpoint = $catch*8;
						if($catch>=3){
							$catchpoint = $catchpoint+4;
						}
						$stpoint = $stumbed*12;
						$throwpoint = $thrower*6;
						$hittspoints = $hitter*6;
						if($overs>=2){
							if($erate<5){
								$economypoints = 6;
							}
							else if($erate>=5 && $erate<=5.99){
								$economypoints = 4;
							}
							else if($erate>=6 && $erate<=7){
								$economypoints = 2;
							}
							else if($erate>=10 && $erate<=11){
								$economypoints = -2;
							}
							else if($erate>=11.1 && $erate<=12){
								$economypoints = -4;
							}
							else if($erate>=12){
								$economypoints = -6;
							}
						}
						if($findplayerrole!='bowler'){
							if($bballs>=10){
								if($strikerate>=60 && $strikerate<=70){
									$strikePoint = -2;
								}
								else if($strikerate>=50 && $strikerate<=59.9){
									$strikePoint = -4;
								}
								else if($strikerate<50){
									$strikePoint = -6;
								}
								else if($strikerate>=130 && $strikerate<=150){
									$strikePoint = 2;
								}
								else if($strikerate>=150.01 && $strikerate<=170){
									$strikePoint = 4;
								}
								else if($strikerate>170){
									$strikePoint = 6;
								}
							}
						}
					}
					else if($match == 't10'){
					    													
						if($row->starting11==1 && $row->innings==1){
							$startingpoint = 4;
						}
						else{
							$startingpoint = 0;
						}
						// batting points given //
						if($findplayerrole!='bowler'){
							if($row->duck!=0 || $row->duck!=""){
								$duckpoint = -2;
							}
						}
						$boundrypoints = $fours * 1;
						$sixpoints = $six * 2;
						$runpoints = $runs * 1;
						
						if(($runs>=30) && ($runs<50)){
							$halcenturypoints = 8;
						}
						else if($runs>=50){
							$centuryPoints = 16;
						}
						
						// 		$extra_points = $extra_points*1;
						
						// give points for bowling //
						if($wicket==2){
							$wkpoints = $wkpoints+8;
						}
						if($wicket >=3){
							$wkpoints = $wkpoints+16;
						}
						$wkpoints = $wkpoints+$wicket* 25;
						$maidenpoints = $maiden_over * 16;
						
						// fielding points //
						
						$catchpoint = $catch*8;
						if($catch>=3){
							$catchpoint = $catchpoint+4;
						}
						$stpoint = $stumbed*12;
						$throwpoint = $thrower*8;
						$hittspoints = $hitter*4;
						
						if($overs>=1){
							if($erate<6){
								$economypoints = 6;
							}
							else if($erate>=6 && $erate<=6.99){
								$economypoints = 4;
							}
							else if($erate>=7 && $erate<=8){
								$economypoints = 2;
							}
							else if($erate>=11 && $erate<=12){
								$economypoints = -2;
							}
							else if($erate>=12.1 && $erate<=13){
								$economypoints = -4;
							}
							else if($erate>=13){
								$economypoints = -6;
							}
						}
						
						if($findplayerrole!='bowler'){
    						if($bballs>=5){
    							if($strikerate>=70 && $strikerate<=80){
    								$strikePoint = -2;
    							}
    							else if($strikerate>=60 && $strikerate<=69.99){
    								$strikePoint = -4;
    							}
    							else if($strikerate<60){
    								$strikePoint = -6;
    							}
								else if($strikerate>=150 && $strikerate<=170){
									$strikePoint = 2;
								}
								else if($strikerate>=170.01 && $strikerate<=190){
									$strikePoint = 4;
								}
								else if($strikerate>190){
									$strikePoint = 6;
								}
    						}
						}
					}
					else if($match == 'one-day'){
													
						if($row->starting11==1 && $row->innings==1){
							$startingpoint = 4;
						}
						else{
							$startingpoint = 0;
						}
						// batting points given //
						if($findplayerrole!='bowler'){
							if($row->duck!=0 || $row->duck!=""){
								$duckpoint = -3;
							}
						}

						$boundrypoints = $fours * 1;
						$sixpoints = $six * 2;
						$runpoints = $runs * 1;
						
						if(($runs>=50) && ($runs<100)){
							$halcenturypoints = 4;
						}
						else if($runs>=100 && $runs<150){
							$centuryPoints = 8;
						}

						// give points for bowling //
						if($wicket==4){
							$wkpoints = $wkpoints+ 4;
						}
						if($wicket >=5){
							$wkpoints = $wkpoints+ 8;
						}
						$wkpoints = $wkpoints+$wicket* 25;
						$maidenpoints = $maiden_over * 4;
						
						// fielding points //
						
						$catchpoint = $catch* 8;
						if($catch>=3){
							$catchpoint = $catchpoint+4;
						}
						$stpoint = $stumbed* 12;
						$throwpoint = $thrower* 6;
						$hittspoints = $hitter* 6;
						
						if($overs>=5){
							if($erate<2.5){
								$economypoints = 6;
							}
							else if($erate>=2.5 && $erate<=3.49){
								$economypoints = 4;
							}
							else if($erate>=3.5 && $erate<=4.5){
								$economypoints = 2;
							}
							else if($erate>=7 && $erate<=8){
								$economypoints = -2;
							}
							else if($erate>=8.1 && $erate<=9){
								$economypoints =-4;
							}
							else if($erate>=9){
								$economypoints = -6;
							}
						}
						if($findplayerrole!='bowler'){
							if($bballs>=20){
								if($strikerate>=40 && $strikerate<=50){
									$strikePoint = -2;
								}
								else if($strikerate>=30 && $strikerate<=39.9){
									$strikePoint = -4;
								}
								else if($strikerate<30){
									$strikePoint = -6;
								}
								else if($strikerate>=100 && $strikerate<=120){
									$strikePoint = 2;
								}
								else if($strikerate>=120.01 && $strikerate<=140){
									$strikePoint = 4;
								}
								else if($strikerate>140){
									$strikePoint = 6;
								}
							}
						}
					}
					else {    //test
													
						if($row->starting11==1 && $row->innings==1){
							$startingpoint = 4;
						}
						else{
							$startingpoint = 0;
						}
						// batting points given //
						if($findplayerrole!='bowler'){
							if($row->duck!=0 || $row->duck!=""){
								$duckpoint = -4;
							}
						}
						
						$boundrypoints = $fours * 1;
						$sixpoints = $six * 2;
						$runpoints = $runs * 1;
						
						if(($runs>=50) && ($runs<100)){
							$halcenturypoints = 4;
						}
						else if($runs>=100 && $runs<150){
							$centuryPoints = 8;
						}
						
						// give points for bowling //
						if($wicket==4){
							$wkpoints = $wkpoints+4;
						}
						if($wicket >=5){
							$wkpoints = $wkpoints+8;
						}
						$wkpoints = $wkpoints+$wicket*16;
						
						// fielding points //
						
						$catchpoint = $catch*8;
						$stpoint = $stumbed* 12;
						$throwpoint = $thrower*8;
						$hittspoints = $hitter*4;
						
					}

					if($row->starting11==1){
						$economypoints = (int) $economypoints;
						
						$result['batting_points'] = $runpoints+$sixpoints+$boundrypoints+$strikePoint+$halcenturypoints+$centuryPoints+$thirtypoints;
						$result['fielding_points'] = $catchpoint+$stpoint+$throwpoint+$hittspoints;
						$result['bowling_points'] = $wkpoints+$maidenpoints+$economypoints+$wicketbonuspoint;
						$total_points = $result['total_points'] = $startingpoint+$runpoints+$sixpoints+$thirtypoints+$halcenturypoints+$centuryPoints+$boundrypoints+$strikePoint+$catchpoint+$stpoint+$wkpoints+$maidenpoints+$economypoints+$duckpoint+$hittspoints+$throwpoint+$extra_points+$wicketbonuspoint;
					}
					else{
						$result['batting_points'] = 0;
						$result['fielding_points'] = 0;
						$result['bowling_points'] = 0;
						$total_points = $result['total_points'] = 0;
					}
					DB::connection('mysql2')->table('result_matches')->where('player_key',$player_key)->where('match_key',$match_key)->where('innings',$row->innings)->update($result);
						//insert in result points// 
						$resultpoints['matchkey']= $match_key;
						$resultpoints['playerid']= $row->player_id;
						if($row->starting11==1){
							$resultpoints['startingpoints']= $startingpoint;
							$resultpoints['runs']= $runpoints;
							$resultpoints['fours']= $boundrypoints;
							$resultpoints['sixs']= $sixpoints;
							$resultpoints['strike_rate']= $strikePoint;
							$resultpoints['thirtypoints']= $thirtypoints;
							$resultpoints['halfcentury']= $halcenturypoints;
							$resultpoints['century']= $centuryPoints;
							$resultpoints['wickets']= $wkpoints;
							$resultpoints['maidens']= $maidenpoints;
							$resultpoints['economy_rate']= $economypoints;
							$resultpoints['catch']= $catchpoint;
							$resultpoints['wicketbonuspoint']= $wicketbonuspoint;
							$resultpoints['stumping']= $stpoint;
							$resultpoints['thrower']= $throwpoint;
							$resultpoints['hitter']= $hittspoints;
							$resultpoints['stumping']= $stpoint;
							$resultpoints['bonus']= $extra_points;
							$resultpoints['negative']= $duckpoint;
							$resultpoints['total']= $total_points;
						}else{
							$resultpoints['startingpoints']= 0;
							$resultpoints['runs']= 0;
							$resultpoints['fours']= 0;
							$resultpoints['sixs']= 0;
							$resultpoints['strike_rate']= 0;
							$resultpoints['thirtypoints']= 0;
							$resultpoints['halfcentury']= 0;
							$resultpoints['century']= 0;
							$resultpoints['wickets']= 0;
							$resultpoints['maidens']= 0;
							$resultpoints['economy_rate']= 0;
							$resultpoints['wicketbonuspoint']=0;
							$resultpoints['catch']= 0;
							$resultpoints['stumping']= 0;
							$resultpoints['thrower']= 0;
							$resultpoints['hitter']= 0;
							$resultpoints['bonus']= 0;
							$resultpoints['negative']= 0;
							$resultpoints['total']= 0;
						}
						
						$resultpoints['updated_at'] = date('Y-m-d H:i:s');
						$finde = DB::connection('mysql')->table('result_points')->where('matchkey',$match_key)->where('playerid',$row->player_id)->where('resultmatch_id',$row->id)->select('id')->first();
					
						if(empty($finde)){
							$resultpoints['resultmatch_id']= $row->id;
							DB::connection('mysql2')->table('result_points')->insert($resultpoints);
						}
						else{
							DB::connection('mysql2')->table('result_points')->where('id',$finde->id)->update($resultpoints);
						}

						// series players points
						$matchdata = DB::table('listmatches')->where('matchkey',$match_key)->select('series','id','matchkey')->first();
						$seriesid = $matchdata->series;
						$matchkey = $matchdata->matchkey;
						$player_idd = $row->player_id;
						$seriesdata = array();
						$seriesdata['seriesid'] = $seriesid;
						$seriesdata['matchkey'] = $matchkey;
						$seriesdata['playerid'] = $player_idd;
						$seriesdata['points'] = $resultpoints['total'];
						
						
						$repeateddata = DB::table('seriesplayers')->where('matchkey',$matchkey)->where('seriesid',$seriesid)->where('playerid',$player_idd)->select('id')->first();
						if(!empty($repeateddata)){
							DB::connection('mysql2')->table('seriesplayers')->where('id',$repeateddata->id)->update($seriesdata);
						}else{
							DB::connection('mysql2')->table('seriesplayers')->insert($seriesdata);
						}
					}
				$this->updateplayerpoints($match_key);
			}
		}

		public function updateplayerpoints($match_key){
			$findallplayers =DB::connection('mysql')->table('matchplayers')->where('matchkey',$match_key)->get();
			if(!empty($findallplayers)){
				foreach($findallplayers as $player){
					$findtotalpoints = DB::table('result_points')->where('matchkey',$match_key)->where('playerid',$player->playerid)->sum('result_points.total');
					$data['points'] = $findtotalpoints;
					DB::connection('mysql2')->table('matchplayers')->where('id',$player->id)->update($data);
				}
				ResultController::userpoints($match_key);
			}
		}
		
		public function userpoints($match_key){
			
			$joinlist =DB::connection('mysql')->table('jointeam')->where('matchkey',$match_key)->get();
			if(!empty($joinlist)){
				foreach($joinlist as $row2){
					$user_points = 0;
					$players = explode(',',$row2->players);
					$matchplayers = DB::connection('mysql')->table('matchplayers')->where('matchkey',$match_key)->get();
					if(!empty($matchplayers)){
						foreach($matchplayers as $row){
							$pid = $row->playerid;
							if(in_array($pid,$players)){
								if($row2->captain == $pid){
									$user_points =$user_points + ($row->points*2);
								}else if ($row2->vicecaptain== $pid){
									$user_points =$user_points + ($row->points*1.5);
								}else {
									$user_points =$user_points + $row->points;
								}
							}else{
								$user_points = $user_points;
							}
						}
					}
					if($row2->points!=$user_points){
						$result['lastpoints']=$row2->points;
					}
					$result['points']=$user_points;
					DB::connection('mysql2')->table('jointeam')->where('id',$row2->id)->update($result);
				}
			}
		}
	public function viewwinners($matchkey){
		$findwinners = array();
		$finduserjoinedleauges = DB::connection('mysql')->table('joinedleauges')->where('joinedleauges.matchkey',$matchkey)->join('jointeam','jointeam.id','=','joinedleauges.teamid')->join('matchchallenges','matchchallenges.id','=','joinedleauges.challengeid')->join('registerusers','registerusers.id','=','joinedleauges.userid')->orderBy('joinedleauges.challengeid','ASC')->select('matchchallenges.win_amount','matchchallenges.entryfee','matchchallenges.joinedusers','matchchallenges.is_private','matchchallenges.bonus_percentage','matchchallenges.confirmed_challenge','matchchallenges.maximum_user','joinedleauges.*','registerusers.username as username','registerusers.email','registerusers.team','jointeam.points')->paginate(10);
		return view('matches.viewwinners',compact('finduserjoinedleauges'));
	}
		//---------------------- Show playing11 ------------------//
	public function showplaying($real_match_key,$match_key){
        $checkmath = DB::connection('mysql')->table('listmatches')->where('matchkey',$match_key)
        ->select('matchkey','playing11_status','status')->first();
        // echo '<pre>';print_r($checkmath);die;

        if(!empty($checkmath)){
        	$statstarted = $checkmath->status;
    		$giveresresult = CricketapiController::getmatchdata($real_match_key);
    			// 		echo '<pre>';print_r($giveresresult);die;
                if(isset($giveresresult['data']['card']['teams']['a']['match']['playing_xi'])){
                    if(!empty($giveresresult['data']['card']['teams']['a']['match']['playing_xi'])){
                        $mynewqq = $giveresresult['data']['card']['teams']['a']['match']['playing_xi'];
                        $teamaa = array();
                        $teamkey = $giveresresult['data']['card']['teams']['a']['key'];
                        foreach($mynewqq as $vall1){
                            $getid = DB::table('players')->join('matchplayers','matchplayers.playerid','=','players.id')->where('matchplayers.matchkey',$match_key)->where('players.players_key',$vall1)->select('matchplayers.id','playingstatus')->first();
                            if(!empty($getid)){
                                $dddjdh['playingstatus'] = 1;
                                DB::connection('mysql2')->table('matchplayers')->where('id',$getid->id)->update($dddjdh); 
                                $findplayerhits = DB::connection('mysql')->table('result_matches')->where('result_matches.player_key',$vall1)->where('result_matches.match_key',$match_key)->where('result_matches.innings','1')->select('result_matches.id')->first();
                                if(!empty($findplayerhits)){
								     $datahits['startingpoints']='4';
									 DB::connection('mysql2')->table('result_points')->where('resultmatch_id',$findplayerhits->id)->where('matchkey',$match_key)->update($datahits);
									 $datahitss['starting11']='1';
									 DB::connection('mysql2')->table('result_matches')->where('id',$findplayerhits->id)->where('match_key',$match_key)->update($datahitss);
								}
                            }else{
                            	$findmatchexist = DB::connection('mysql')->table('teams')->where('team_key',$teamkey)->select('id')->first();
								if(!empty($findmatchexist)){
	                            	$getplayer = DB::connection('mysql')->table('players')->where('players_key',$vall1)->where('team',$findmatchexist->id)->first();
	                            	if(!empty($getplayer)){
	                            		$result =DB::connection('mysql')->table('matchplayers')->where('matchkey',$match_key)->where('playerid',$getplayer->id)->first();
                                        if(empty($result)){
		                            		$data['matchkey']=$match_key;
		                            		$data['playerid']=$getplayer->id;
		                            		$data['role']=$getplayer->role;
		                            		$data['credit']=$getplayer->credit;
		                            		$data['name']=$getplayer->player_name;
		                            		$data['playingstatus']=1;
		                            		DB::connection('mysql2')->table('matchplayers')->insert($data); 
		                            	}
	                            	}
                            	}
                            }
                        }
                    }
	            }
    	               
	            if(isset($giveresresult['data']['card']['teams']['b']['match']['playing_xi'])){
                    if(!empty($giveresresult['data']['card']['teams']['b']['match']['playing_xi'])){
                        $mynewqq1 = $giveresresult['data']['card']['teams']['b']['match']['playing_xi'];
                        $team2key = $giveresresult['data']['card']['teams']['b']['key'];
                        foreach($mynewqq1 as $vall1){
                            $getid = DB::connection('mysql')->table('players')->join('matchplayers','matchplayers.playerid','=','players.id')->where('matchplayers.matchkey',$match_key)->where('players.players_key',$vall1)->select('matchplayers.id')->first();
                            if(!empty($getid)){
                                $dddjdh['playingstatus'] = 1;
                                DB::connection('mysql2')->table('matchplayers')->where('id',$getid->id)->update($dddjdh);
                                $findplayerhits = DB::connection('mysql')->table('result_matches')->where('result_matches.player_key',$vall1)->where('result_matches.match_key',$match_key)->where('result_matches.innings','1')->select('result_matches.id')->first();
								if(!empty($findplayerhits)){
								    $dataahitss['startingpoints']='4';
									DB::connection('mysql2')->table('result_points')->where('resultmatch_id',$findplayerhits->id)->where('matchkey',$match_key)->update($dataahitss);
									 $datahitsss['starting11']='1';
									 DB::connection('mysql2')->table('result_matches')->where('id',$findplayerhits->id)->where('match_key',$match_key)->update($datahitsss);
								}
                            }else{
                            	$findmatchexist = DB::connection('mysql')->table('teams')->where('team_key',$team2key)->select('id')->first();
								if(!empty($findmatchexist)){
	                            	$getplayer = DB::connection('mysql')->table('players')->where('players_key',$vall1)->where('team',$findmatchexist->id)->first();
	                            	if(!empty($getplayer)){
	                            		$result = DB::connection('mysql')->table('matchplayers')->where('matchkey',$match_key)->where('playerid',$getplayer->id)->first();
                                        if(empty($result)){
		                            		$data['matchkey']=$match_key;
		                            		$data['playerid']=$getplayer->id;
		                            		$data['role']=$getplayer->role;
		                            		$data['credit']=$getplayer->credit;
		                            		$data['name']=$getplayer->player_name;
		                            		$data['playingstatus']=1;
		                            		DB::connection('mysql2')->table('matchplayers')->insert($data); 
		                            	}
	                            	}
	                            }
                            }
                        }
                    }
                }
				if(!empty($mynewqq) && !empty($mynewqq1)){
	                $newplaying_xi= array_merge($mynewqq1,$mynewqq);
	                $getplayers = DB::connection('mysql')->table('players')->join('matchplayers','matchplayers.playerid','=','players.id')->where('matchplayers.matchkey',$match_key)->select('players.players_key','matchplayers.id')->get();
	                if(!empty($getplayers)){
	                	$i=0;
	                	foreach($getplayers as $players){
	                		if(in_array($players->players_key, $newplaying_xi)){
	                			$datas['playingstatus']=1;
                            	DB::connection('mysql2')->table('matchplayers')->where('id',$players->id)->update($datas); 
	                		}else{
	                			$datas['playingstatus']=0;
                            	DB::connection('mysql2')->table('matchplayers')->where('id',$players->id)->update($datas); 
	                		}
	                	$i++;
	                	}
	                }
                }
                if(!empty($giveresresult['data']['card']['teams']['a']['match']['playing_xi'])){
        			$playingstat['playing11_status'] = '1';
                    DB::connection('mysql2')->table('listmatches')->where('matchkey',$match_key)->update($playingstat);
                }
                if(!empty($giveresresult['data']['card']['toss'])){
                	$toss = DB::connection('mysql2')->table('listmatches')->where('matchkey',$match_key)->value('tosswinner_team');
                	if(empty($toss)){
                		$ld['tosswinner_team'] = (isset($giveresresult['data']['card']['toss']['won']))?$giveresresult['data']['card']['toss']['won']:'';
                		$ld['toss_decision'] = (isset($giveresresult['data']['card']['toss']['decision']))?$giveresresult['data']['card']['toss']['decision']:'';
                		DB::connection('mysql2')->table('listmatches')->where('matchkey',$match_key)->update($ld);
                	}
                }
        	// }
        	if($statstarted == 'notstarted'){
        		$checkmathw =  DB::connection('mysql')->table('matchplayers')->where('matchplayers.matchkey',$match_key)->where('playingstatus','1')->select('id')->count();
			    $checknotify = DB::table('playingnotification')->where('matchkey',$match_key)->first();
			    // dd($checkmathw);
			    if(empty($checknotify)){
				    if($checkmathw == 22){
				        $mmmmmm['matchkey'] = $match_key;
				        DB::connection('mysql2')->table('playingnotification')->insert($mmmmmm);
                        $usersaray = array();
    	                $allusers = DB::connection('mysql')->table('registerusers')->where('user_status','0')->select('id')->get();
            	        $teamssdta =DB::connection('mysql')->table('listmatches')->where('matchkey',$match_key)->join('teams as t1','t1.id','=','listmatches.team1')->join('teams as t2','t2.id','=','listmatches.team2')->select('t1.short_name as t1name','t2.short_name as t2name','listmatches.playing11_status as playing11_status')->first();
            	        $msg = 'Create/Edit Your Team & Join The Contests Before The Deadline. Hurry!';
            	        $titleget = strtoupper($teamssdta->t1name) .' VS '. strtoupper($teamssdta->t2name) . ' Playing XI Out!';
    	                foreach($allusers as $users){
    	                    $usersaray[] = $users->id;
                        }
                        $regIdChunk=array_chunk($usersaray,500);
                        foreach($regIdChunk as $RegId){
                            $message_status = Helpers::sendmultiplenotification($titleget,$msg,'',$RegId);
                        }
                
    	            }
			    }
		    }
        }
	}
	public function importplayers($getdetails){
		if(!empty($getdetails)){
			$matchkikey = $getdetails['data']['card']['key'];
			$team1players=array();
			$team2players=array();
			if(isset($getdetails['data']['card']['teams']['a']['match']['players'])){
				$team1players = $getdetails['data']['card']['teams']['a']['match']['players'];
			}
			if(isset($getdetails['data']['card']['teams']['b']['match']['players'])){
				$team2players = $getdetails['data']['card']['teams']['b']['match']['players'];
			}
			if(!empty($team1players)){
				foreach($team1players as $players1){
					$playerkey = $players1;
					// insert players details which we get from api//
					$teamkey = $getdetails['data']['card']['teams']['a']['key'];
					$findmatchexist = DB::connection('mysql')->table('teams')->where('team_key',$teamkey)->select('id')->first();
					if(!empty($findmatchexist)){
						$findplayerexist =DB::connection('mysql')->table('players')->where('players_key',$players1)->where('team',$findmatchexist->id)->first();
						$data['player_name'] = $getdetails['data']['card']['players'][$players1]['fullname'];
						$data['players_key'] = $playerkey;
						$data['credit']=9;
						if(empty($findplayerexist)){
							$data['team'] = $findmatchexist->id;
							if($getdetails['data']['card']['players'][$players1]['seasonal_role']==""){
								$data['role'] = 'allrounder';
							}
							else{
								$data['role'] =  $getdetails['data']['card']['players'][$players1]['seasonal_role'];
							}
							$playerid = DB::connection('mysql2')->table('players')->insertGetId($data);
							$credit=9;
						}
						else{
							$playerid = $findplayerexist->id;
							$credit = $findplayerexist->credit;
							$data['role'] = $findplayerexist->role;
							$getdetails['data']['card']['players'][$players1]['seasonal_role']= $findplayerexist->role;
						}
						// insert players for a match//
						$findplayer1entry =DB::connection('mysql')->table('matchplayers')->where('matchkey',$matchkikey)->where('playerid',$playerid)->first();
						if(empty($findplayer1entry)){
							$matchplayerdata['matchkey'] = $matchkikey;
							$matchplayerdata['playerid'] = $playerid;
							$matchplayerdata['role'] = $data['role'];
							$matchplayerdata['name'] = $data['player_name'];
							$matchplayerdata['credit'] = $credit;
							DB::connection('mysql2')->table('matchplayers')->insert($matchplayerdata);
						}
					}
				}
			}
			if(!empty($team2players)){
				foreach($team2players as $players2){
					$playerkey2 = $players2;
					$playerid="";
					$findplayer2exist=array();
					$data=array();
					$team2key = $getdetails['data']['card']['teams']['b']['key'];
					$findmatchexist = DB::connection('mysql')->table('teams')->where('team_key',$team2key)->select('id')->first();
					if(!empty($findmatchexist)){
						$findplayer2exist =DB::connection('mysql')->table('players')->where('players_key',$players2)->where('team',$findmatchexist->id)->first();
						$data['player_name'] = $getdetails['data']['card']['players'][$players2]['fullname'];
						$data['players_key'] = $playerkey2;
						$data['credit']=9;
						if(empty($findplayer2exist)){
							$data['team'] = $findmatchexist->id;
							if($getdetails['data']['card']['players'][$players2]['seasonal_role']==""){
								$data['role'] =  'allrounder';
							}else{
								$data['role']=$getdetails['data']['card']['players'][$players2]['seasonal_role'];
							}
							$playerid =  DB::connection('mysql2')->table('players')->insertGetId($data);
							$credit=9;
						}
						else{
							$playerid = $findplayer2exist->id;
							$credit = $findplayer2exist->credit;
							$getdetails['data']['card']['players'][$players2]['seasonal_role']= $findplayer2exist->role;
							$data['role'] =  $findplayer2exist->role;
						}
						$findplayer2entry =DB::connection('mysql')->table('matchplayers')->where('matchkey',$matchkikey)->where('playerid',$playerid)->first();
						if(empty($findplayer2entry)){
							$matchplayerdata['matchkey'] = $matchkikey;
							$matchplayerdata['playerid'] = $playerid;
							if($data['role']!=""){
								$matchplayerdata['role'] = $data['role'];
							}
							$matchplayerdata['name'] = $data['player_name'];
							$matchplayerdata['credit'] = $credit;
							DB::connection('mysql2')->table('matchplayers')->insert($matchplayerdata);
						}
					}
				}
			}
		}
	}
    public function update_results_of_matches(){
		date_default_timezone_set('Asia/Kolkata'); 
		$findmatchexist =DB::connection('mysql')->table('listmatches')->where('fantasy_type','Cricket')
		->whereDate('start_date','<=',date('Y-m-d'))->where('launch_status','launched')
		->where('final_status','!=','winnerdeclared')->where('status','!=','completed')->get();
// 		$findmatchexist =DB::connection('mysql')->table('listmatches')->where('matchkey','c.match.bcc_vs_pkcc.87f08')->get();
		
		if(!empty($findmatchexist)){
			
			foreach($findmatchexist as $val){
				$match_type = $val->format;
				$getcurrentdate = date('Y-m-d H:i:s');
				$matchtimings = date('Y-m-d H:i:s',strtotime($val->start_date));
				$matchtimings1 = date('Y-m-d H:i:s', strtotime( '-55 minutes', strtotime($val->start_date)));
				

				if($getcurrentdate>$matchtimings1){
						
					$match_key=$val->matchkey;
					$real_match_key=$val->real_matchkey;
					$this->showplaying($real_match_key,$match_key);
				}
				if($getcurrentdate>=$matchtimings){
					$match_key=$val->matchkey;
					$real_match_key=$val->real_matchkey;
					if($val->second_inning_status==0 || $val->second_inning_status==1){
						$this->getscoresupdates($real_match_key,$match_key);
					}
					if($val->second_inning_status==2){
						$this->updatescoreparticularinning($real_match_key,$match_key);
					}
				}
			}
		  return 'completed';
		}
	}
	public function getscoresupdates($real_matchkey,$match_key){
			date_default_timezone_set('Asia/Kolkata'); 
			$findmatchtype =DB::table('listmatches')->where('matchkey',$match_key)->select('format','final_status')->first();
			$giveresresult = CricketapiController::getmatchdata($real_matchkey);
			if(!empty($giveresresult)){
		    $checkpre =DB::connection('mysql')->table('matchruns')->where('matchkey',$match_key)->first();
               if(empty($checkpre)){
                    $matchdata['matchkey'] = $match_key;
                    $matchdata['teams1'] = $giveresresult['data']['card']['teams']['a']['short_name'];
                    $matchdata['teams2'] = $giveresresult['data']['card']['teams']['b']['short_name'];
                   if(!empty($giveresresult['data']['card']['msgs']['result'])){
                    $matchdata['winning_status'] = $giveresresult['data']['card']['msgs']['result'];
                   }else{
                       $matchdata['winning_status']=0;
                   }
                   if(!empty($giveresresult['data']['card']['innings'])){
                       $gettestscore1 = 0;
                       $gettestscore2 = 0;
                       $gettestwicket1 = 0;
                       $gettestwicket2 = 0;
                       $gettestover1 = 0;
                       $gettestover2 = 0;
                       if(!empty($giveresresult['data']['card']['innings']['b_2'])){
                            $gettestscore2 = $giveresresult['data']['card']['innings']['b_2']['runs'];
                           $gettestscore1 = $giveresresult['data']['card']['innings']['a_2']['runs'];
                           $gettestwicket1 = $giveresresult['data']['card']['innings']['a_2']['wickets'];
                           $gettestwicket2 = $giveresresult['data']['card']['innings']['b_2']['wickets'];
                           $gettestover1 = $giveresresult['data']['card']['innings']['a_2']['overs'];
                           $gettestover2 = $giveresresult['data']['card']['innings']['b_2']['overs'];
                       }
                   if(empty($gettestwicket1)){
                        $matchdata['wickets1'] = $giveresresult['data']['card']['innings']['a_1']['wickets'];
                   }else{
                       $matchdata['wickets1'] = $giveresresult['data']['card']['innings']['a_1']['wickets'].','.$gettestwicket1;
                   }
                   if(empty($gettestwicket2)){
                        $matchdata['wickets2'] = $giveresresult['data']['card']['innings']['b_1']['wickets'];
                   }else{
                       $matchdata['wickets2'] = $giveresresult['data']['card']['innings']['b_1']['wickets'].','.$gettestwicket2;
                   }
                   if(empty($gettestover1)){
                        $matchdata['overs1'] = $giveresresult['data']['card']['innings']['a_1']['overs'];
                   }else{
                       $matchdata['overs1'] = $giveresresult['data']['card']['innings']['a_1']['overs'].','.$gettestover1;
                   }
                   if(empty($gettestover2)){
                        $matchdata['overs2'] = $giveresresult['data']['card']['innings']['b_1']['overs'];
                   }else{
                       $matchdata['overs2'] = $giveresresult['data']['card']['innings']['b_1']['overs'].','.$gettestover2;
                   }
                   if(empty($gettestscore1)){
                        $matchdata['runs1'] = $giveresresult['data']['card']['innings']['a_1']['runs'];
                   }else{
                       $matchdata['runs1'] = $giveresresult['data']['card']['innings']['a_1']['runs'].','.$gettestscore1;
                   }
                   if(empty($gettestscore2)){
                        $matchdata['runs2'] = $giveresresult['data']['card']['innings']['b_1']['runs'];
                   }else{
                       $matchdata['runs2'] = $giveresresult['data']['card']['innings']['b_1']['runs'].','.$gettestscore2;
                   }
                   }else{
                   $matchdata['winning_status'] = 0;
                   $matchdata['wickets1'] = 0;
                   $matchdata['wickets2'] = 0;
                   $matchdata['overs1'] = 0;
                   $matchdata['overs2'] = 0;
                   $matchdata['runs1'] = 0;
                   $matchdata['runs2'] = 0;
                   }
                   DB::connection('mysql2')->table('matchruns')->insert($matchdata);
               }else{
                   $matchdata1['matchkey'] = $match_key;
                   $matchdata1['teams1'] = $giveresresult['data']['card']['teams']['a']['short_name'];
                   $matchdata1['teams2'] = $giveresresult['data']['card']['teams']['b']['short_name'];
                   if(!empty($giveresresult['data']['card']['msgs']['result'])){
                   $matchdata1['winning_status'] = $giveresresult['data']['card']['msgs']['result'];
                   }else{
                       $matchdata1['winning_status'] = 0;
                   }
                   if(!empty($giveresresult['data']['card']['innings'])){
                    $gettestscore1 = 0;
                       $gettestscore2 = 0;
                       $gettestwicket1 = 0;
                       $gettestwicket2 = 0;
                       $gettestover1 = 0;
                       $gettestover2 = 0;
                       if(!empty($giveresresult['data']['card']['innings']['b_2'])){
                            $gettestscore2 = $giveresresult['data']['card']['innings']['b_2']['runs'];
                           $gettestscore1 = $giveresresult['data']['card']['innings']['a_2']['runs'];
                           $gettestwicket1 = $giveresresult['data']['card']['innings']['a_2']['wickets'];
                           $gettestwicket2 = $giveresresult['data']['card']['innings']['b_2']['wickets'];
                           $gettestover1 = $giveresresult['data']['card']['innings']['a_2']['overs'];
                           $gettestover2 = $giveresresult['data']['card']['innings']['b_2']['overs'];
                       }
                   if(empty($gettestwicket1)){
                        $matchdata1['wickets1'] = $giveresresult['data']['card']['innings']['a_1']['wickets'];
                   }else{
                       $matchdata1['wickets1'] = $giveresresult['data']['card']['innings']['a_1']['wickets'].','.$gettestwicket1;
                   }
                   if(empty($gettestwicket2)){
                        $matchdata1['wickets2'] = $giveresresult['data']['card']['innings']['b_1']['wickets'];
                   }else{
                       $matchdata1['wickets2'] = $giveresresult['data']['card']['innings']['b_1']['wickets'].','.$gettestwicket2;
                   }
                   if(empty($gettestover1)){
                        $matchdata1['overs1'] = $giveresresult['data']['card']['innings']['a_1']['overs'];
                   }else{
                       $matchdata1['overs1'] = $giveresresult['data']['card']['innings']['a_1']['overs'].','.$gettestover1;
                   }
                   if(empty($gettestover2)){
                        $matchdata1['overs2'] = $giveresresult['data']['card']['innings']['b_1']['overs'];
                   }else{
                       $matchdata1['overs2'] = $giveresresult['data']['card']['innings']['b_1']['overs'].','.$gettestover2;
                   }
                   if(empty($gettestscore1)){
                        $matchdata1['runs1'] = $giveresresult['data']['card']['innings']['a_1']['runs'];
                   }else{
                       $matchdata1['runs1'] = $giveresresult['data']['card']['innings']['a_1']['runs'].','.$gettestscore1;
                   }
                   if(empty($gettestscore2)){
                        $matchdata1['runs2'] = $giveresresult['data']['card']['innings']['b_1']['runs'];
                   }else{
                       $matchdata1['runs2'] = $giveresresult['data']['card']['innings']['b_1']['runs'].','.$gettestscore2;
                   }
                   }else{
                   $matchdata1['wickets1'] = 0;
                   $matchdata1['wickets2'] = 0;
                   $matchdata1['overs1'] = 0;
                   $matchdata1['overs2'] = 0;
                   $matchdata1['runs1'] = 0;
                   $matchdata1['runs2'] = 0;
                   }
                   DB::connection('mysql2')->table('matchruns')->where('matchkey',$match_key)->update($matchdata1);
               }
		    // End Of ankit code
				$mainarrayget = $giveresresult['data']['card'];
				$getmtdatastatus['status'] = $mainarrayget['status'];
				if($getmtdatastatus['status']=='completed' && $findmatchtype->final_status=='pending'){
					$getmtdatastatus['final_status'] = 'IsReviewed';
				}
				DB::connection('mysql2')->table('listmatches')->where('matchkey',$match_key)->update($getmtdatastatus);
				$findteams = $mainarrayget['teams'];
				$finalplayingteams = array();
				if(!empty($findteams)){
					foreach($findteams as $tp){
						if(isset($tp['match']['playing_xi'])){
							$findpl = $tp['match']['playing_xi'];
							if(!empty($findpl)){
								foreach($findpl as $fl){
									$finalplayingteams[] = $fl;
								}
							}
						}
					}
				}
				if(isset($mainarrayget['players'])){
					$giveres = $mainarrayget['players'];
					$matchplayers =DB::connection('mysql')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('matchkey',$match_key)->select('matchplayers.*','players.players_key','players.role as playerrole')->get();

					$a =$matchplayers->toArray();
					if(!empty($a)){
						$throwerarray = array();
						$hitterarray = array();
					foreach($matchplayers as $player){
						$pid = $player->playerid;
						$playr =DB::connection('mysql')->table('players')->where('id',$pid)->select('players_key')->first();
						$pact_name = $player->players_key;
						if(isset($giveres[$pact_name]['match']['innings'])){
							$inning = $giveres[$pact_name]['match']['innings'];
					       $k=1;	
						if(!empty($inning)){
							$k=1;
							foreach($inning as $key=>$innings){
								if(($key != 'superover') && ($key != 'superover_2') && ($key != 'superover_3')){
									// $throwerarray = array();
									// $hitterarray = array();
									$datasv=array();
									$runs = 0;
									$fours = 0;
									$six = 0;
									$duck=0;
									$maiden_over=0;
									$wicket = 0;
									$overs = 0;
									$catch=0;
									$runouts = 0;
									$stumbed = 0;
									$batdots = 0;$balldots = 0;$miletone_run = 0;$bball = 0;$grun = 0;$balls = 0;$bballs = 0;$extra = 0;
									if(in_array($pact_name,$finalplayingteams)){
										$datasv['starting11']=1;
									}
									else{
										$starting11=0;
									}

									// batting points //
										if(!empty($inning[$k]['batting'])){
											$batting = $inning[$k]['batting'];
											if(isset($batting['strike_rate'])){
												$datasv['batting'] = 1;
												$datasv['strike_rate'] = $batting['strike_rate'];
											}
											else{
												$datasv['batting'] = 0;
											}
										}

										/* runs points */
										if(isset($batting['runs'])){
											$datasv['runs'] = $runs = $runs +  $batting['runs'];
										}else{
											$datasv['runs'] =0;
										}
										/* fours points */
										
										if(isset($batting['fours'])){
											$datasv['fours'] = $fours = $fours + $batting['fours'];
										}
										if(isset($batting['balls'])){
											$datasv['bball'] = $bball = $bball + $batting['balls'];
											}
										/* sixes Points */
										
										if(isset($batting['sixes'])){
											$datasv['six'] = $six = $six + $batting['sixes'];
										}
										/* duck out points */
										if(isset($batting['dismissed'])){

											if($player->playerrole!='bowler'){
												if(($runs == 0) && ($batting['dismissed'] == 1)){
													$datasv['duck'] = $duck = 1;
												}else{
													$datasv['duck'] = $duck = 0;
												}
											}else{
												$datasv['duck'] = $duck = 0;
											}
											if($batting['dismissed'] == 1){
												$datasv['out_str'] = $batting['out_str'];
											}else{
												$datasv['out_str'] = 'not out';
											}
										}
										/* check for run out points */
										if(isset($batting['ball_of_dismissed'])){
											$ball_of_dismissed = $batting['ball_of_dismissed'];
											if($batting['ball_of_dismissed']['wicket_type']=='runout'){
												if(isset($ball_of_dismissed['other_fielder'])){
													if($ball_of_dismissed['other_fielder']!=""){
														$throwerarray[$k][] = $ball_of_dismissed['other_fielder'];
													}
												}
												if(isset($ball_of_dismissed['fielder'])){
													if($ball_of_dismissed['fielder']['key']!=""){
														$hitterarray[$k][] = $ball_of_dismissed['fielder']['key'];
														/* check if the player is hitter and thrower both */
														if($ball_of_dismissed['other_fielder']==""){
															$throwerarray[$k][] = $ball_of_dismissed['fielder']['key'];
														}
													}
												}
											}

											if($batting['ball_of_dismissed']['wicket_type']=='lbw' || $batting['ball_of_dismissed']['wicket_type']=='bowled'){
															
												$wbowlerkey = $batting['ball_of_dismissed']['bowler']['key'];
												// echo "<pre>";print_r($wbowlerkey);
												
												$bowlerplayersid =DB::connection('mysql2')
																	->table('matchplayers')
																	->join('players','players.id','=','matchplayers.playerid')
																	->where('players.players_key',$wbowlerkey)
																	->where('matchkey',$match_key)
																	->value('matchplayers.playerid');
												// $wplayerid = array_search($wbowlerkey, array_column($a, 'playerid'));
												if(!empty($bowlerplayersid)){
													$datasv['wplayerid']= $bowlerplayersid;
												}
											}
											$datasv['wicket_type']= $batting['ball_of_dismissed']['wicket_type'];
										}
										if(isset($batting['dots'])){
											$datasv['battingdots'] = $batdots = $batdots + $batting['dots'];
										}
									// bowling points //
										
										/* check if player is in bowling stage or not */
										if(!empty($inning[$k]['bowling'])){
											$bowling = $inning[$k]['bowling'];
											$datasv['bowling'] = 1;
											$datasv['economy_rate'] = $bowling['economy'];
										}
										/* for maiden overs */
										if(!empty($inning[$k]['bowling'])){
											if(isset($bowling['maiden_overs'])){
												$datasv['maiden_over'] = $maiden_over = $maiden_over + $bowling['maiden_overs'];
											}
										}else{
											$datasv['maiden_over'] =0;
										}
										/* for wickets */
										if(!empty($inning[$k]['bowling'])){
											if(isset($bowling['wickets'])){
												$datasv['wicket'] = $wicket = $wicket + $bowling['wickets'];
											}
										}else{
											$datasv['wicket'] =0;
										}
										/* for overs */
										if(!empty($inning[$k]['bowling'])){
											if(isset($bowling['overs'])){
												$datasv['overs'] = $overs = $overs + $bowling['overs'];
											}
										}else{
											$datasv['overs'] =0;
										}
										if(!empty($inning[$k]['bowling'])){
											if(isset($bowling['runs'])){
												$datasv['grun'] = $grun = $grun + $bowling['runs'];
											}
										}else{
											$datasv['grun'] = 0;
										}

										if(isset($bowling['dots'])){
											$datasv['balldots'] = $balldots = $balldots + $bowling['dots'];
										}
										if(!empty($inning[$k]['bowling'])){
											if(isset($bowling['balls'])){
												$datasv['balls'] = $balls = $balls + $bowling['balls'];
											}
										}else{
											$datasv['balls'] =0;
										}
										if(isset($bowling['extras'])){
											$datasv['extra'] = $extra = $extra + $bowling['extras'];
										}
									// fielding points //
										if(!empty($inning[$k]['fielding'])){
											$fielding = $inning[$k]['fielding'];
											if(isset($fielding['catches'])){
												$datasv['catch'] = $catch = $catch + $fielding['catches'];
											}
											if(isset($fielding['runouts'])){
												$datasv['runouts'] = $runouts = $runouts + $fielding['runouts'];
											}
											if(isset($fielding['stumbeds'])){
												$datasv['stumbed'] = $stumbed = $stumbed + $fielding['stumbeds'];
											}
										}
										
									// now update in result matches //
									$datasv['match_key'] =$match_key;
									$datasv['player_key'] =$pact_name;
									$datasv['player_id'] =$pid;
									$datasv['innings'] =$k;
									//
									$findplayerex = DB::table('result_matches')->where('player_key',$pact_name)->where('match_key',$match_key)->where('innings',$k)->select('id')->first();
									
									if(!empty($findplayerex)){
										DB::connection('mysql2')->table('result_matches')->where('id',$findplayerex->id)->update($datasv);
									}else{
									
										DB::connection('mysql2')->table('result_matches')->insert($datasv);
									}
									// calculate hitter and thrower//
									// if(!empty($hitterarray)){
									// 	foreach($hitterarray as $hits){
									// 		$counthits = count(array_keys($hitterarray, $hits, true));
									// 		$datahits['match_key'] =$match_key;
									// 		$datahits['player_key'] =$hits;
									// 		$datahits['innings'] =$k;
									// 		$datahits['hitter'] = $counthits;
									// 		$findplayerhits = DB::connection('mysql')->table('result_matches')->where('player_key',$hits)->where('match_key',$match_key)->where('innings',$k)->select('id')->first();
									// 		if(!empty($findplayerhits)){
											
									// 			DB::connection('mysql2')->table('result_matches')->where('id',$findplayerhits->id)->update($datahits);
									// 		}else{
											
									// 			DB::connection('mysql2')->table('result_matches')->insert($datahits);
									// 		}
									// 	}
									// }
									// if(!empty($throwerarray)){
									// 	foreach($throwerarray as $throw){
									// 		$countthrow = count(array_keys($throwerarray, $throw, true));
									// 		$datathrow['match_key'] =$match_key;
									// 		$datathrow['player_key'] =$throw;
									// 		$datathrow['innings'] =$k;
									// 		$datathrow['thrower'] = $countthrow;
									// 		$findplayerthrow = DB::connection('mysql')->table('result_matches')->where('player_key',$throw)->where('match_key',$match_key)->where('innings',$k)->select('id')->first();
									// 		if(!empty($findplayerthrow)){
									// 			DB::connection('mysql2')->table('result_matches')->where('id',$findplayerthrow->id)->update($datathrow);
									// 		}
									// 		else{
									// 			DB::connection('mysql2')->table('result_matches')->insert($datathrow);
									// 		}
									// 	}
									// }
									$k++;
								}
							}
						}else{
						    $datasvs['out_str'] = 'not out';
						    $datasvs['match_key'] =$match_key;
							$datasvs['player_key'] =$pact_name;
							$datasvs['player_id'] =$pid;
							$datasvs['innings'] =1;
							$datasvs['starting11'] =($player->playingstatus==1)?1:0;
							$findplayerex = DB::connection('mysql')->table('result_matches')->where('player_key',$pact_name)->where('match_key',$match_key)->where('innings',$k)->select('id')->first();
							if(!empty($findplayerex)){
								DB::connection('mysql2')->table('result_matches')->where('id',$findplayerex->id)->update($datasvs);
							}else{
								DB::connection('mysql2')->table('result_matches')->insert($datasvs);
							}
						}
					 }
					}
					// dump($hitterarray);
					// dd($throwerarray);
					if(!empty($hitterarray)){
						// dd($hitterarray);
						foreach($hitterarray as $hitskey => $hits){
								$innings = $hitskey;
								if(!empty($hits)){
    						        foreach($hits as $hitsv){
    						            $counthits = count(array_keys($hits, $hitsv, true));
            							$datahits['match_key'] =$match_key;
            							$datahits['player_key'] =$hitsv;
            							$datahits['innings'] =$hitskey;
            							$datahits['hitter'] = $counthits;
            							$findplayerhits = DB::connection('mysql')->table('result_matches')->where('player_key',$hitsv)->where('match_key',$match_key)->where('innings',$innings)->select('id')->first();
            							if(!empty($findplayerhits)){
            							   DB::table('result_matches')->where('id',$findplayerhits->id)->update($datahits);
            							
            							}else{
            								DB::table('result_matches')->insert($datahits);
            							}
    						        }
								}
						}
					}
					if(!empty($throwerarray)){
						foreach($throwerarray as $throwkey=>$throw){
						    $innings = $throwkey;
						    if(!empty($throw)){
						        foreach($throw as $throwv){
						            $countthrow = count(array_keys($throw, $throwv, true));
						            $datathrow['match_key'] =$match_key;
        							$datathrow['player_key'] =$throwv;
        							$datathrow['innings'] = $innings;
        							$datathrow['thrower'] = $countthrow;
        							$findplayerthrow = DB::connection('mysql')->table('result_matches')->where('player_key',$throwv)->where('match_key',$match_key)->where('innings',$innings)->select('id')->first();
        							if(!empty($findplayerthrow)){
        								DB::table('result_matches')->where('id',$findplayerthrow->id)->update($datathrow);
        							}
        							else{
        								DB::table('result_matches')->insert($datathrow);
        							}
						        }
						    }
						}
					}
					$showpoints = ResultController::player_point($match_key,$findmatchtype->format);
				}
				}
			}
			return 1;
		}
	public function refund_amount(){
		date_default_timezone_set('Asia/Kolkata');
		$current = date('Y-m-d H:i:s');
		$match_time = date('Y-m-d H:i:s', strtotime( '-1 minutes', strtotime($current)));
		// $match_time = date('Y-m-d H:i:s', strtotime($current));
		$findmatches = DB::connection('mysql')->table('listmatches')->where('start_date', '<=' , $match_time)->select('matchkey')->get();
		if(!empty($findmatches)){
			foreach ($findmatches as $value){
				$match_challenges = DB::connection('mysql')->table('matchchallenges')->where('matchkey',$value->matchkey)->where('status','!=','canceled')->select('confirmed_challenge','id','joinedusers','maximum_user','matchkey','entryfee')->get();
			    $match_challenges = $match_challenges->toArray();
			    if(!empty($match_challenges)){
				    foreach ($match_challenges as  $value1) {
						if($value1->maximum_user > $value1->joinedusers){
							if($value1->confirmed_challenge == 0){
							    $getresponse = $this->refundprocess($value1->id,$value1->entryfee,$value->matchkey,'challenge cancel');
							    if($getresponse==true){
							        $data['status'] = 'canceled';
							        DB::connection('mysql2')->table('matchchallenges')->where('id',$value1->id)->update($data);
							    }
							}
						}
					}
				}
			}
		}
		$this->joininfodeletedata();
	}

	public function updatematchfinalstatus($matchkey,$status,Request $request){
            $in = $request->all();
            $input['final_status']=$status;
			
            if(isset($in['masterpassword'])){
                if(!empty($in['masterpassword'])){
                    $password = Auth::user()->masterpassword;
                   
                    if($password != $in['masterpassword']){
                        return redirect()->back()->with('danger','Incorrect masterpassword');
                    }
                }else{
                    return redirect()->back()->with('danger','Please enter masterpassword');
                }
                
            }else{
                if($status=='winnerdeclared'){
                    if(isset($in['masterpassword'])){
                        if(!empty($in['masterpassword'])){
                            $password = Auth::user()->masterpassword;
                            if($password != $in['masterpassword']){
                                return redirect()->back()->with('danger','Incorrect masterpassword');
                            }
                        }else{
                            return redirect()->back()->with('danger','Please enter masterpassword');
                        }
                    }else{
                        return redirect()->back()->with('danger','Please enter masterpassword');
                    }
                }
            }
            $findseries =DB::table('listmatches')->where('matchkey',$matchkey)->select('series','format')->first();
			
			if($status=='IsAbandoned' || $status=='IsCanceled'){
                if($status=='IsAbandoned'){
                    $reason = 'Match abandoned';
                }else{
                    $reason = 'Match canceled';
                }
                $resultpoints = ResultController::refund_allamount($matchkey,$reason);
            }
            if($status=='winnerdeclared'){
			   
				$res = ResultController::distribute_winning_amount($matchkey);
            }
            DB::connection('mysql2')->table('listmatches')->where('matchkey',$matchkey)->update($input);
            return redirect()->action('ResultController@match_detail',$findseries->series)->with('success','Match '.$status.' successfully!');
        }
        // code rewrite by aanchal //
        
        public function refund_allamount($match_key,$reason){
            $matchchallenges =DB::table('matchchallenges')->where('matchkey',$match_key)->get();
            foreach($matchchallenges as $challenge){
                $getresponse = $this->refundprocess($challenge->id,$challenge->entryfee,$match_key,$reason);
                if($getresponse==true){
                    $data = array();
			        $data['status'] = 'canceled';
			        DB::connection('mysql2')->table('matchchallenges')->where('id',$challenge->id)->update($data);
			    }
            }
        }
        
        // refund process starts //
        public function refundprocess($challengeid,$entryfees,$matchkey,$reason){
            $leaugestransactions = DB::connection('mysql')->table('leaugestransactions')->where('matchkey',$matchkey)->where('challengeid',$challengeid)->get();
	       if(!empty($leaugestransactions)){
	        foreach ($leaugestransactions as  $value2){
            $refund_data = DB::connection('mysql')->table('refunds')->where('joinid',$value2->joinid)->select('id')->first();
            if(empty($refund_data)){
                $entry_fee = $entryfees;
				$last_row = DB::connection('mysql')->table('userbalance')->where('user_id',$value2->user_id)->first();
			    if(!empty($last_row)){
					$data_bal['balance'] = number_format($last_row->balance+$value2->balance,2, ".", "");
					$data_bal['winning'] = number_format($last_row->winning+$value2->winning,2, ".", "");
					$data_bal['bonus'] =  number_format($last_row->bonus+$value2->bonus,2, ".", "");
					$data_bal['referral_income'] =  number_format($last_row->referral_income+$value2->referral_income,2, ".", "");

				    DB::connection('mysql2')->table('userbalance')->where('id',$last_row->id)->update($data_bal);
                    $refund_data['userid']=$value2->user_id;
					$refund_data['amount']=$entry_fee;
					$refund_data['joinid']=$value2->joinid;
					$refund_data['challengeid']=$value2->challengeid ;
					$refund_data['reason']=$reason;
					$refund_data['matchkey']=$matchkey;
					$transaction_id= (Helpers::settings()->short_name ?? ''.'-').rand(100,999).time().'-'.$value2->user_id;
					$refund_data['transaction_id']=$transaction_id;
				    DB::connection('mysql2')->table('refunds')->insert($refund_data);
                    $data_trans['transaction_id'] = $transaction_id;
					$data_trans['type'] = 'Refund';
				    $data_trans['transaction_by'] = Helpers::settings()->short_name ?? '';
					$data_trans['amount'] = $entry_fee;
					$data_trans['paymentstatus'] = 'confirmed';
					$data_trans['challengeid'] = $value2->challengeid;
					$data_trans['bonus_amt'] = $value2->bonus;
					$data_trans['win_amt'] = $value2->winning;
					$data_trans['addfund_amt'] = $value2->balance;
					$data_trans['referral_amt'] = $value2->referral_income;
					$data_trans['bal_bonus_amt'] = $data_bal['bonus'];
					$data_trans['bal_win_amt'] = $data_bal['winning'];
					$data_trans['bal_fund_amt'] = $data_bal['balance'];
					$data_trans['bal_referral_amt'] = $data_bal['referral_income'];
					$data_trans['userid'] = $value2->user_id;
					$data_trans['total_available_amt'] = $data_bal['balance']+$data_bal['winning']+$data_bal['bonus']+$data_bal['referral_income'];
					DB::connection('mysql2')->table('transactions')->insert($data_trans);
				    //notifications//
				    $totalentryfee = $value2->bonus+$value2->balance+$value2->winning;
					$datan['title'] = 'Refund Amount of Rs.'.$totalentryfee.' for challenge cancellation';
					$datan['userid'] = $value2->user_id;
					DB::connection('mysql2')->table('notifications')->insert($datan);
					//push notifications//
					$titleget = 'Refund Amount!';
					Helpers::sendnotification($titleget,$datan['title'],'',$value2->user_id);
					//end push notifications//
				}
            }
            }
           }
           return true;
        }
        
	public function distribute_winning_amount($matchkey){
	    $abcdd = DB::table('listmatches')->where('matchkey',$matchkey)->where('final_status','!=','winnerdeclared')->where('final_status','!=','IsAbandoned')->where('final_status','!=','IsCanceled')->first();
	 if(!empty($abcdd)){
	     $allchallenges =DB::table('matchchallenges')->where('matchkey',$matchkey)
		->where('status','!=','canceled')->select('win_amount','contest_type','winning_percentage','pricecard_type','entryfee','id','joinedusers','is_bonus','bonus_percentage','maximum_user','confirmed_challenge')->get();

		if (!empty($allchallenges)) {
			foreach ($allchallenges as $challenge) {

				if ($challenge->maximum_user > $challenge->joinedusers OR $challenge->joinedusers < 2 ) {
					if ($challenge->confirmed_challenge == 0) {
						$getresponse = $this->refundprocess($challenge->id, $challenge->entryfee, $matchkey, 'challenge cancel');
						if ($getresponse == true) {
							$data['status'] = 'canceled';
							DB::connection('mysql2')->table('matchchallenges')->where('id', $challenge->id)->update($data);
						}
					}elseif ($challenge->confirmed_challenge == 1 && $challenge->joinedusers<2) {
						$getresponse = $this->refundprocess($challenge->id, $challenge->entryfee, $matchkey, 'challenge cancel');
						if ($getresponse == true) {
							$data['status'] = 'canceled';
							DB::connection('mysql2')->table('matchchallenges')->where('id', $challenge->id)->update($data);
						}
					}
				}
			}
		}
		
		$allchallenges =DB::table('matchchallenges')->where('matchkey',$matchkey)->where('status','!=','canceled')->select('win_amount','contest_type','winning_percentage','pricecard_type','entryfee','id','joinedusers','is_bonus','bonus_percentage','win_amount_2','contest_cat')->get();
		
		if(!empty($allchallenges)){ 
			foreach($allchallenges as $challenge){
				$joinedusers = DB::table('joinedleauges')->join('matchchallenges','matchchallenges.id','=','joinedleauges.challengeid')->where('joinedleauges.matchkey',$matchkey)->where('joinedleauges.challengeid',$challenge->id)->join('jointeam','jointeam.id','=','joinedleauges.teamid')->select('joinedleauges.userid','points','joinedleauges.id as jid')->get();
				$joinedusers = $joinedusers->toArray();
				if(!empty($joinedusers)){
					// calculate the price cards //
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
										$prc_arr[$i+1]=($prccrd->price_percent/100)*($challenge->win_amount);
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
							$fpusk = $fpusv['userid'];
								$winningdata = DB::table('finalresults')->where('joinedid',$fpuskjoinid)->first();
							if(empty($winningdata)){
								$fres = array();
								$listmatches = DB::table('listmatches')->where('matchkey',$matchkey)->select('series','name')->first();
								$challengeid = $challenge->id;
								$seriesid = $listmatches->series;
								$transactionidsave = 'WIN-'.rand(1000,99999).$challengeid.$fpuskjoinid;
								$fres['userid'] = $fpusk;
								$fres['points'] = str_replace("'", "", $fpusv['points']);
								$first_joined = [];
								//========================================================================================================================
									/* this concept is for those user who joined first in the league 
									in this concept if first joined user will win the amount as rank 1 then he will win different amount
									this concept will work for only 1 winner category

										if($challenge->contest_cat==5){
											$first_joined = DB::table('joinedleauges')->where('challengeid',$challenge->id)->orderBy('id','ASC')->pluck('id');
											if(!empty($first_joined->toArray())){
												if($first_joined[0]==$fpuskjoinid){
													$fres['amount'] = round($challenge->win_amount_2,2);
												}else{
													$fres['amount'] = round($fpusv['amount'],2);
												}
											}else{
												$fres['amount'] = round($fpusv['amount'],2);
											}
										}else{
												$fres['amount'] = round($fpusv['amount'],2);
										} */
								//==========================================================================================================================
								$fres['amount'] = round($fpusv['amount'],2);
								$fres['rank'] = $fpusv['rank'];
								$fres['matchkey'] = $matchkey;
								$fres['challengeid'] = $challengeid;
								$fres['seriesid'] = $seriesid;
								$fres['transaction_id'] = $transactionidsave;
								$fres['joinedid'] = $fpuskjoinid;
								$findalreexist = DB::table('finalresults')->where('joinedid',$fpuskjoinid)->where('userid',$fpusk)->select('id')->first();
								if(empty($findalreexist)){
									DB::connection('mysql2')->table('finalresults')->insert($fres);
									$finduserbalance = DB::table('userbalance')->where('user_id',$fpusk)->first();
									if(!empty($finduserbalance)){
										
										if($fpusv['amount']>10000){
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
											$registeruserdetails = DB::table('registerusers')->where('id',$fpusk)->first();
											$findlastow = DB::table('userbalance')->where('user_id',$fpusk)->first();
											$dataqs['winning'] = number_format($findlastow->winning+$fpusv['amount'],2, ".", "");
											
											DB::connection('mysql2')->table('userbalance')->where('id',$findlastow->id)->update($dataqs);
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
											$datatr['bal_referral_amt'] = $finduserbalance->referral_income;
											$datatr['userid'] = $fpusk;
											$datatr['total_available_amt'] = $finduserbalance->balance+$dataqs['winning']+$finduserbalance->bonus+$finduserbalance->referral_income;
											DB::connection('mysql2')->table('transactions')->insert($datatr);
											
											$datanot['title'] = 'You won amount Rs.'.$fpusv['amount'].' and 31.2% amount of '.$tdsdata['amount'].' deducted due to TDS.';
											$datanot['userid'] = $fpusk;
											DB::connection('mysql2')->table('notifications')->insert($datanot);
											//push notifications//
											$titleget = 'Congrats! You won a match.';
											Helpers::sendnotification($titleget,$datanot['title'],'',$fpusk);
											
											# refer winning bonus
											$matchkey = $matchkey;
											$challenge_id = $challengeid;
											$player_id = 0; 
											$joinid = $fpuskjoinid;
											$user_id = $fpusk;
											$win_amount = $fpusv['amount'];
											$type = 'normal';

											// ResultController::refer_winning_bonus($matchkey, $challenge_id, $player_id, $joinid, $user_id, $win_amount, $type);
											# refer winning bonus
										}else{
											$datatr = array();
											$dataqs = array();
											//user balance//
											$registeruserdetails = DB::table('registerusers')->where('id',$fpusk)->first();

											$findlastow = DB::table('userbalance')->where('user_id',$fpusk)->first();
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
												$datatr['bal_referral_amt'] = $finduserbalance->referral_income;
												$datatr['userid'] = $fpusk;
												$datatr['total_available_amt'] = $finduserbalance->balance+$dataqs['winning']+$finduserbalance->bonus+$finduserbalance->referral_income;
												DB::connection('mysql2')->table('transactions')->insert($datatr);
											
												//notifications entry//
												$datanot['title'] = 'You won amount Rs.'.$fpusv['amount'];
												$datanot['userid'] = $fpusk;
												DB::connection('mysql2')->table('notifications')->insert($datanot);
												//push notifications//
												$titleget = 'Congrats! You Won a match!';
												Helpers::sendnotification($titleget,$datanot['title'],'',$fpusk);
												
												# refer winning bonus
												$matchkey = $matchkey;
												$challenge_id = $challengeid;
												$player_id = 0; 
												$joinid = $fpuskjoinid;
												$user_id = $fpusk;
												$win_amount = $fpusv['amount'];
												$type = 'normal';

												// ResultController::refer_winning_bonus($matchkey, $challenge_id, $player_id, $joinid, $user_id, $win_amount, $type);
												# refer winning bonus
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
	    }else{
	        echo "you cannot declare winner of this match" ;
	    }
	}

	public static function refer_winning_bonus($matchkey, $challenge_id = 0, $player_id = 0, $joinid, $user_id, $win_amount, $type) {
        $refer = DB::table('registerusers')->where('id', $user_id)->value('refer_id');
                    
        if($refer) {
            $refer_winning_bonus = DB::table('general_tabs')->where('type', 'refer_winning_bonus')->value('amount');
                                    
            if($refer_winning_bonus == 0) {
                $refer_winning_bonus = 1;
            } 
            
            // $bonus = ($win_amount * $refer_winning_bonus) / 100;
            $bonus = $refer_winning_bonus;
            
            $referuserbalance = DB::table('userbalance')->where('user_id', $refer)->first();
                            
            $updateduserbalance['referral_income'] = number_format($referuserbalance->referral_income + $bonus, 2, '.', '');
            
            DB::table('userbalance')->where('user_id', $refer)->update($updateduserbalance);

			$transaction_id = 'SM11-RWB-'.time().$refer;
			$refer_winning_bonus = array();
			$refer_winning_bonus['challenge_id'] = $challenge_id;
			$refer_winning_bonus['player_id'] = $player_id;
			$refer_winning_bonus['matchkey'] = $matchkey;
			$refer_winning_bonus['joinid'] = $joinid;
			$refer_winning_bonus['user_id'] = $refer;
			$refer_winning_bonus['from_id'] = $user_id;
			$refer_winning_bonus['win_amount'] = number_format($win_amount, 2, '.', '');
			$refer_winning_bonus['transaction_id'] = $transaction_id;
			$refer_winning_bonus['amount'] = number_format($bonus, 2, '.', '');
			$refer_winning_bonus['type'] = $type;
			
			DB::table('refer_winning_bonus')->insert($refer_winning_bonus);
				
			$transactions['transaction_id'] = $transaction_id;
			$transactions['type'] = 'Refer Winning Bonus';
			$transactions['transaction_by'] = 'SM11';
			$transactions['amount'] = number_format($bonus, 2, '.', '');
			$transactions['paymentstatus'] = 'confirmed';
			$transactions['challengeid'] = $challenge_id;
			$transactions['joinid'] = $joinid;
			$transactions['referral_amt'] = $bonus;
			$transactions['bal_bonus_amt'] = number_format($referuserbalance->bonus, 2, '.', '');
			$transactions['bal_win_amt'] = number_format($referuserbalance->winning, 2, '.', '');
			$transactions['bal_fund_amt'] = number_format($referuserbalance->balance, 2, '.', '');
			$transactions['bal_referral_amt'] = number_format($updateduserbalance['referral_income'], 2, '.', '');
			$transactions['userid'] = $refer;
			$transactions['total_available_amt'] = number_format($referuserbalance->balance+$referuserbalance->bonus+$referuserbalance->winning+$updateduserbalance['referral_income'], 2, '.', '');

			DB::table('transactions')->insert($transactions);

			$notifications['title'] = 'Congrats! you received ₹'.number_format($bonus, 2, '.', '').' from your referral on his winning';
			$notifications['userid'] = $refer;
			DB::table('notifications')->insert($notifications);

			//push notifications//
			$titleget = 'Referral Winning Bonus';
			Helpers::sendnotification($titleget,$notifications['title'],'',$refer);
			//end push notifications//
        }
        
	}

	public function joininfodeletedata(){
	    $locktime = Carbon::now();
	    $findmatches = DB::table('listmatches')->Where('listmatches.status','started')->join('joininfo','listmatches.matchkey','=','joininfo.matchkey')->select('listmatches.matchkey','listmatches.id','listmatches.status')->groupBy('joininfo.matchkey')->get();
	    if(!empty($findmatches)){
	        foreach($findmatches as $match){
	                DB::connection('mysql2')->table('joininfo')->where('matchkey',$match->matchkey)->delete();
	      }
	    }
	}
	

		public function match_points($matchkey){
			$findmatchdetails = DB::table('listmatches')->where('matchkey',$matchkey)->first();
			$match_scores = DB::table('result_matches')->join('players','players.players_key','=','result_matches.player_key')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_matches.match_key',$matchkey)->where('matchplayers.matchkey',$matchkey);
			
		    if(request()->has('player_name')) {
		        $player_name = request()->get('player_name');
		        $match_scores = $match_scores->where('matchplayers.name','LIKE','%'.$player_name.'%');
		    }
		    
		    $match_scores = $match_scores->orderBy('result_matches.innings')->orderBy('players.team')->select('result_matches.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.short_name as team_short_name','teams.team_key as teams_team_key','matchplayers.playerid as matchplayers_playerid')->get(); 
			
			$match_points = DB::table('result_points')->join('players','players.id','=','result_points.playerid')->join('result_matches','result_matches.id','=','result_points.resultmatch_id')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_points.matchkey',$matchkey)->where('matchplayers.matchkey',$matchkey);

		    if(request()->has('player_name')) {
		        $player_name = request()->get('player_name');
		        $match_points = $match_points->where('matchplayers.name','LIKE','%'.$player_name.'%');
		    }
		    
		    $match_points = $match_points->orderBy('result_matches.innings')->orderBy('players.team')->select('result_points.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.team_key as teams_team_key','result_matches.innings','teams.short_name as team_short_name')->get();
		    
			return view('matches.match_points',compact('match_scores','match_points','findmatchdetails'));
		}

		public function match_score($matchkey){
			$findmatchdetails = DB::table('listmatches')->where('matchkey',$matchkey)->first();
			$match_scores = DB::table('result_matches')->join('players','players.players_key','=','result_matches.player_key')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_matches.match_key',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_matches.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.short_name as team_short_name','teams.team_key as teams_team_key','matchplayers.playerid as matchplayers_playerid')->get(); 
			$match_points = DB::table('result_points')->join('players','players.id','=','result_points.playerid')->join('result_matches','result_matches.id','=','result_points.resultmatch_id')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_points.matchkey',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_points.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.team_key as teams_team_key','result_matches.innings','teams.short_name as team_short_name')->get();

			return view('matches.match_score',compact('match_scores','match_points','findmatchdetails'));
		}
		public function batting_points($matchkey){
			$findmatchdetails = DB::table('listmatches')->where('matchkey',$matchkey)->first();
			$match_scores = DB::table('result_matches')->join('players','players.players_key','=','result_matches.player_key')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_matches.match_key',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_matches.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.short_name as team_short_name','teams.team_key as teams_team_key','matchplayers.playerid as matchplayers_playerid')->get(); 
			$match_points = DB::table('result_points')->join('players','players.id','=','result_points.playerid')->join('result_matches','result_matches.id','=','result_points.resultmatch_id')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_points.matchkey',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_points.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.team_key as teams_team_key','result_matches.innings','teams.short_name as team_short_name')->get();

			return view('matches.batting_points',compact('match_scores','match_points','findmatchdetails'));
		}
		public function bowling_points($matchkey){
			$findmatchdetails = DB::table('listmatches')->where('matchkey',$matchkey)->first();
			$match_scores = DB::table('result_matches')->join('players','players.players_key','=','result_matches.player_key')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_matches.match_key',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_matches.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.short_name as team_short_name','teams.team_key as teams_team_key','matchplayers.playerid as matchplayers_playerid')->get(); 
			$match_points = DB::table('result_points')->join('players','players.id','=','result_points.playerid')->join('result_matches','result_matches.id','=','result_points.resultmatch_id')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_points.matchkey',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_points.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.team_key as teams_team_key','result_matches.innings','teams.short_name as team_short_name')->get();

			return view('matches.bowling_points',compact('match_scores','match_points','findmatchdetails'));
		}
		public function fielding_points($matchkey){
			$findmatchdetails = DB::table('listmatches')->where('matchkey',$matchkey)->first();
			$match_scores = DB::table('result_matches')->join('players','players.players_key','=','result_matches.player_key')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_matches.match_key',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_matches.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.short_name as team_short_name','teams.team_key as teams_team_key','matchplayers.playerid as matchplayers_playerid')->get(); 
			$match_points = DB::table('result_points')->join('players','players.id','=','result_points.playerid')->join('result_matches','result_matches.id','=','result_points.resultmatch_id')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_points.matchkey',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_points.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.team_key as teams_team_key','result_matches.innings','teams.short_name as team_short_name')->get();

			return view('matches.fielding_points',compact('match_scores','match_points','findmatchdetails'));
		}
		public function team_points($matchkey){
			$findmatchdetails = DB::table('listmatches')->where('matchkey',$matchkey)->first();
			$match_scores = DB::table('result_matches')->join('players','players.players_key','=','result_matches.player_key')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_matches.match_key',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_matches.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.short_name as team_short_name','teams.team_key as teams_team_key','matchplayers.playerid as matchplayers_playerid')->get(); 
			$match_points = DB::table('result_points')->join('players','players.id','=','result_points.playerid')->join('result_matches','result_matches.id','=','result_points.resultmatch_id')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_points.matchkey',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_points.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.team_key as teams_team_key','result_matches.innings','teams.short_name as team_short_name')->get();

			return view('matches.team_points',compact('match_scores','match_points','findmatchdetails'));
		}

	public function updatescoreparticularinning($real_match_key,$match_key){
		date_default_timezone_set('Asia/Kolkata'); 
		$findmatchtype =DB::connection('mysql')->table('listmatches')->where('matchkey',$match_key)->select('format','team1','team2')->first();
		$giveresresult = CricketapiController::getmatchdata($real_match_key);
		if(empty($giveresresult['data'])){
			$giveresresult= CricketapiController::GetLocalMatchdetails($real_match_key);
		}
	
		if(!empty($giveresresult)){
			// to update the score
			
		  $checkpre =DB::connection('mysql')->table('matchruns')->where('matchkey',$match_key)->first();
		   if(empty($checkpre)){
				$matchdata['matchkey'] = $match_key;
				$matchdata['teams1'] = $giveresresult['data']['card']['teams']['a']['short_name'];
				$matchdata['teams2'] = $giveresresult['data']['card']['teams']['b']['short_name'];
			   if(!empty($giveresresult['data']['card']['msgs']['result'])){
				$matchdata['winning_status'] = $giveresresult['data']['card']['msgs']['result'];
			   }else{
				   $matchdata['winning_status']=0;
			   }
			   if(!empty($giveresresult['data']['card']['innings'])){
				   $gettestscore1 = 0;
				   $gettestscore2 = 0;
				   $gettestwicket1 = 0;
				   $gettestwicket2 = 0;
				   $gettestover1 = 0;
				   $gettestover2 = 0;
				   if(!empty($giveresresult['data']['card']['innings']['b_2'])){
						$gettestscore2 = $giveresresult['data']['card']['innings']['b_2']['runs'];
					   $gettestscore1 = $giveresresult['data']['card']['innings']['a_2']['runs'];
					   $gettestwicket1 = $giveresresult['data']['card']['innings']['a_2']['wickets'];
					   $gettestwicket2 = $giveresresult['data']['card']['innings']['b_2']['wickets'];
					   $gettestover1 = $giveresresult['data']['card']['innings']['a_2']['overs'];
					   $gettestover2 = $giveresresult['data']['card']['innings']['b_2']['overs'];
				   }
			   if(empty($gettestwicket1)){
					$matchdata['wickets1'] = $giveresresult['data']['card']['innings']['a_1']['wickets'];
			   }else{
				   $matchdata['wickets1'] = $giveresresult['data']['card']['innings']['a_1']['wickets'].','.$gettestwicket1;
			   }
			   if(empty($gettestwicket2)){
					$matchdata['wickets2'] = $giveresresult['data']['card']['innings']['b_1']['wickets'];
			   }else{
				   $matchdata['wickets2'] = $giveresresult['data']['card']['innings']['b_1']['wickets'].','.$gettestwicket2;
			   }
			   if(empty($gettestover1)){
					$matchdata['overs1'] = $giveresresult['data']['card']['innings']['a_1']['overs'];
			   }else{
				   $matchdata['overs1'] = $giveresresult['data']['card']['innings']['a_1']['overs'].','.$gettestover1;
			   }
			   if(empty($gettestover2)){
					$matchdata['overs2'] = $giveresresult['data']['card']['innings']['b_1']['overs'];
			   }else{
				   $matchdata['overs2'] = $giveresresult['data']['card']['innings']['b_1']['overs'].','.$gettestover2;
			   }
			   if(empty($gettestscore1)){
					$matchdata['runs1'] = $giveresresult['data']['card']['innings']['a_1']['runs'];
			   }else{
				   $matchdata['runs1'] = $giveresresult['data']['card']['innings']['a_1']['runs'].','.$gettestscore1;
			   }
			   if(empty($gettestscore2)){
					$matchdata['runs2'] = $giveresresult['data']['card']['innings']['b_1']['runs'];
			   }else{
				   $matchdata['runs2'] = $giveresresult['data']['card']['innings']['b_1']['runs'].','.$gettestscore2;
			   }
			   }else{
			   $matchdata['winning_status'] = 0;
			   $matchdata['wickets1'] = 0;
			   $matchdata['wickets2'] = 0;
			   $matchdata['overs1'] = 0;
			   $matchdata['overs2'] = 0;
			   $matchdata['runs1'] = 0;
			   $matchdata['runs2'] = 0;
			   }
			   DB::connection('mysql2')->table('matchruns')->insert($matchdata);
		   }else{
			   $matchdata1['matchkey'] = $match_key;
			   $matchdata1['teams1'] = $giveresresult['data']['card']['teams']['a']['short_name'];
			   $matchdata1['teams2'] = $giveresresult['data']['card']['teams']['b']['short_name'];
			   if(!empty($giveresresult['data']['card']['msgs']['result'])){
			   $matchdata1['winning_status'] = $giveresresult['data']['card']['msgs']['result'];
			   }else{
				   $matchdata1['winning_status'] = 0;
			   }
			   if(!empty($giveresresult['data']['card']['innings'])){
				$gettestscore1 = 0;
				   $gettestscore2 = 0;
				   $gettestwicket1 = 0;
				   $gettestwicket2 = 0;
				   $gettestover1 = 0;
				   $gettestover2 = 0;
				   if(!empty($giveresresult['data']['card']['innings']['b_2'])){
						$gettestscore2 = $giveresresult['data']['card']['innings']['b_2']['runs'];
					   $gettestscore1 = $giveresresult['data']['card']['innings']['a_2']['runs'];
					   $gettestwicket1 = $giveresresult['data']['card']['innings']['a_2']['wickets'];
					   $gettestwicket2 = $giveresresult['data']['card']['innings']['b_2']['wickets'];
					   $gettestover1 = $giveresresult['data']['card']['innings']['a_2']['overs'];
					   $gettestover2 = $giveresresult['data']['card']['innings']['b_2']['overs'];
				   }
			   if(empty($gettestwicket1)){
					$matchdata1['wickets1'] = $giveresresult['data']['card']['innings']['a_1']['wickets'];
			   }else{
				   $matchdata1['wickets1'] = $giveresresult['data']['card']['innings']['a_1']['wickets'].','.$gettestwicket1;
			   }
			   if(empty($gettestwicket2)){
					$matchdata1['wickets2'] = $giveresresult['data']['card']['innings']['b_1']['wickets'];
			   }else{
				   $matchdata1['wickets2'] = $giveresresult['data']['card']['innings']['b_1']['wickets'].','.$gettestwicket2;
			   }
			   if(empty($gettestover1)){
					$matchdata1['overs1'] = $giveresresult['data']['card']['innings']['a_1']['overs'];
			   }else{
				   $matchdata1['overs1'] = $giveresresult['data']['card']['innings']['a_1']['overs'].','.$gettestover1;
			   }
			   if(empty($gettestover2)){
					$matchdata1['overs2'] = $giveresresult['data']['card']['innings']['b_1']['overs'];
			   }else{
				   $matchdata1['overs2'] = $giveresresult['data']['card']['innings']['b_1']['overs'].','.$gettestover2;
			   }
			   if(empty($gettestscore1)){
					$matchdata1['runs1'] = $giveresresult['data']['card']['innings']['a_1']['runs'];
			   }else{
				   $matchdata1['runs1'] = $giveresresult['data']['card']['innings']['a_1']['runs'].','.$gettestscore1;
			   }
			   if(empty($gettestscore2)){
					$matchdata1['runs2'] = $giveresresult['data']['card']['innings']['b_1']['runs'];
			   }else{
				   $matchdata1['runs2'] = $giveresresult['data']['card']['innings']['b_1']['runs'].','.$gettestscore2;
			   }
			   }else{
			   $matchdata1['wickets1'] = 0;
			   $matchdata1['wickets2'] = 0;
			   $matchdata1['overs1'] = 0;
			   $matchdata1['overs2'] = 0;
			   $matchdata1['runs1'] = 0;
			   $matchdata1['runs2'] = 0;
			   }
			   DB::connection('mysql2')->table('matchruns')->where('matchkey',$match_key)->update($matchdata1);
		   }
			
			$mainarrayget = $giveresresult['data']['card'];
			$getmtdatastatus['status'] = $mainarrayget['status'];
			if($getmtdatastatus['status']=='completed'){
				$getmtdatastatus['final_status'] = 'IsReviewed';
			}
			DB::connection('mysql2')->table('listmatches')->where('matchkey',$match_key)->update($getmtdatastatus);
			$findteams = $mainarrayget['teams'];
			$finalplayingteams = array();
			if(!empty($findteams)){
				foreach($findteams as $tp){
					if(isset($tp['match']['playing_xi'])){
						$findpl = $tp['match']['playing_xi'];
						if(!empty($findpl)){
							foreach($findpl as $fl){
								$finalplayingteams[] = $fl;
							}
						}
					}
				}
			}
			$secondinning=  $giveresresult['data']['card']['batting_order'][1][0];
			if($secondinning=='a'){
				$team1= $findmatchtype->team1;
			}else{
				$team1= $findmatchtype->team2;
			}
			if($team1==$findmatchtype->team1){
				$team2=$findmatchtype->team2;
			}else{
				$team2=$findmatchtype->team1;
			}
			if(isset($mainarrayget['players'])){
				$giveres = $mainarrayget['players'];
				$matchplayers =  DB::connection('mysql')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')
				->where('matchkey',$match_key)->select('matchplayers.*','players.players_key','players.role as playerrole','players.team')->get();
				// echo'<pre>';print_r($matchplayers);die;
				$a =$matchplayers->toArray();
				if(!empty($a)){
					$throwerarray = array();
					$hitterarray = array();
					
				foreach($matchplayers as $player){
					$pid = $player->playerid;
					$playr =DB::connection('mysql')->table('players')->where('id',$pid)->select('players_key')->first();
					$pact_name = $player->players_key;
					if(isset($giveres[$pact_name]['match']['innings'])){
						$inning = $giveres[$pact_name]['match']['innings'];
					   $k=1;	
					if(!empty($inning)){
						$k=1;
							$datasv=array();
							$runs = 0;
							$fours = 0;
							$six = 0;
							$duck=0;
							$maiden_over=0;
							$wicket = 0;
							$overs = 0;
							$catch=0;
							$runouts = 0;
							$stumbed = 0;
							$batdots = 0;$balldots = 0;$miletone_run = 0;$bball = 0;$grun = 0;$balls = 0;$bballs = 0;$extra = 0;
							if(in_array($pact_name,$finalplayingteams)){
								$datasv['starting11']=1;
							}
							else{
								$starting11=0;
							}
							if($player->team==$team1){
							// batting points //
								if(!empty($inning[$k]['batting'])){
									$batting = $inning[$k]['batting'];
									if(isset($batting['strike_rate'])){
										$datasv['batting'] = 1;
										$datasv['strike_rate'] = $batting['strike_rate'];
									}
									else{
										$datasv['batting'] = 0;
									}
								}

								/* runs points */
								if(isset($batting['runs'])){
									$datasv['runs'] = $runs = $runs +  $batting['runs'];
								}else{
									$datasv['runs'] =0;
								}
								/* fours points */
								
								if(isset($batting['fours'])){
									$datasv['fours'] = $fours = $fours + $batting['fours'];
								}
								if(isset($batting['balls'])){
									$datasv['bball'] = $bball = $bball + $batting['balls'];
									}
								/* sixes Points */
								
								if(isset($batting['sixes'])){
									$datasv['six'] = $six = $six + $batting['sixes'];
								}
								/* duck out points */
								if(isset($batting['dismissed'])){

									if($player->playerrole!='bowler'){
										if(($runs == 0) && ($batting['dismissed'] == 1)){
											$datasv['duck'] = $duck = 1;
										}else{
											$datasv['duck'] = $duck = 0;
										}
									}else{
										$datasv['duck'] = $duck = 0;
									}
									if($batting['dismissed'] == 1){
										$datasv['out_str'] = $batting['out_str'];
									}else{
										$datasv['out_str'] = 'not out';
									}
								}
								/* check for run out points */
							
								if(isset($batting['ball_of_dismissed'])){
									$ball_of_dismissed = $batting['ball_of_dismissed'];
									if($batting['ball_of_dismissed']['wicket_type']=='runout'){
									
										if(isset($ball_of_dismissed['other_fielder'])){
											if($ball_of_dismissed['other_fielder']!=""){
												$throwerarray[$k][] = $ball_of_dismissed['other_fielder'];
											}
										}
										if(isset($ball_of_dismissed['fielder'])){
											if($ball_of_dismissed['fielder']['key']!=""){
												$hitterarray[$k][] = $ball_of_dismissed['fielder']['key'];
												/* check if the player is hitter and thrower both */
												if($ball_of_dismissed['other_fielder']==""){
													$throwerarray[$k][] = $ball_of_dismissed['fielder']['key'];
												}
											}
										}
										
									}
								}
								if(isset($batting['dots'])){
									$datasv['battingdots'] = $batdots = $batdots + $batting['dots'];
								}
								
							}
								
							// bowling points //
							if($player->team==$team2){	
								/* check if player is in bowling stage or not */
								if(!empty($inning[$k]['bowling'])){
									$bowling = $inning[$k]['bowling'];
									$datasv['bowling'] = 1;
									$datasv['economy_rate'] = $bowling['economy'];
								}
								$datasv['out_str'] = 'not out';
								/* for maiden overs */
								if(!empty($inning[$k]['bowling'])){
									if(isset($bowling['maiden_overs'])){
										$datasv['maiden_over'] = $maiden_over = $maiden_over + $bowling['maiden_overs'];
									}
								}else{
									$datasv['maiden_over'] =0;
								}
								/* for wickets */
								if(!empty($inning[$k]['bowling'])){
									if(isset($bowling['wickets'])){
										$datasv['wicket'] = $wicket = $wicket + $bowling['wickets'];
									}
								}else{
									$datasv['wicket'] =0;
								}
								/* for overs */
								if(!empty($inning[$k]['bowling'])){
									if(isset($bowling['overs'])){
										$datasv['overs'] = $overs = $overs + $bowling['overs'];
									}
								}else{
									$datasv['overs'] =0;
								}
								if(!empty($inning[$k]['bowling'])){
									if(isset($bowling['runs'])){
										$datasv['grun'] = $grun = $grun + $bowling['runs'];
									}
								}else{
									$datasv['grun'] = 0;
								}

								if(isset($bowling['dots'])){
									$datasv['balldots'] = $balldots = $balldots + $bowling['dots'];
								}
								if(!empty($inning[$k]['bowling'])){
									if(isset($bowling['balls'])){
										$datasv['balls'] = $balls = $balls + $bowling['balls'];
									}
								}else{
									$datasv['balls'] =0;
								}
								if(isset($bowling['extras'])){
									$datasv['extra'] = $extra = $extra + $bowling['extras'];
								}
							// fielding points //
								if(!empty($inning[$k]['fielding'])){
									$fielding = $inning[$k]['fielding'];
									if(isset($fielding['catches'])){
										$datasv['catch'] = $catch = $catch + $fielding['catches'];
									}
									if(isset($fielding['runouts'])){
										$datasv['runouts'] = $runouts = $runouts + $fielding['runouts'];
									}
									if(isset($fielding['stumbeds'])){
										$datasv['stumbed'] = $stumbed = $stumbed + $fielding['stumbeds'];
									}
								}
								
							}
							// now update in result matches //
							$datasv['match_key'] =$match_key;
							$datasv['player_key'] =$pact_name;
							$datasv['player_id'] =$pid;
							$datasv['innings'] =$k;
							$datasv['hitter'] = 0;
							$datasv['thrower'] = 0;
							// echo '<pre>';print_r($datasv);die;
							$findplayerex = DB::connection('mysql')->table('result_matches')->where('player_key',$pact_name)->where('match_key',$match_key)->where('innings',$k)->select('id')->first();
			
							if(!empty($findplayerex)){
								DB::connection('mysql2')->table('result_matches')->where('id',$findplayerex->id)->update($datasv);
							}else{
								DB::connection('mysql2')->table('result_matches')->insert($datasv);
							}
							// calculate hitter and thrower//
							
							$k++;
						 //   }	
			// 			}
						}else{
							$datasvs['out_str'] = 'not out';
							$datasvs['match_key'] =$match_key;
							$datasvs['player_key'] =$pact_name;
							$datasvs['player_id'] =$pid;
							$datasvs['innings'] =1;
							$findplayerex = DB::connection('mysql')->table('result_matches')->where('player_key',$pact_name)->where('match_key',$match_key)->where('innings',$k)->select('id')->first();
							if(!empty($findplayerex)){
								DB::connection('mysql2')->table('result_matches')->where('id',$findplayerex->id)->update($datasvs);
							}else{
								DB::connection('mysql2')->table('result_matches')->insert($datasvs);
							}
						}
						
					}else{
						$datasvs['out_str'] = 'not out';
						$datasvs['match_key'] =$match_key;
						$datasvs['player_key'] =$pact_name;
						$datasvs['player_id'] =$pid;
						$datasvs['innings'] =1;
						$findplayerex = DB::connection('mysql')->table('result_matches')->where('player_key',$pact_name)->where('match_key',$match_key)->where('innings',$k)->select('id')->first();
						if(!empty($findplayerex)){
							DB::connection('mysql2')->table('result_matches')->where('id',$findplayerex->id)->update($datasvs);
						}else{
							DB::connection('mysql2')->table('result_matches')->insert($datasvs);
						}
					}
				 }
				}
				if(!empty($hitterarray)){
					foreach($hitterarray as $hitskey=>$hits){
							$innings = $hitskey;
							if(!empty($hits)){
								foreach($hits as $hitsv){
									$counthits = count(array_keys($hits, $hitsv, true));
									$datahits['match_key'] =$match_key;
									$datahits['player_key'] =$hitsv;
									$datahits['innings'] =$hitskey;
									$datahits['hitter'] = $counthits;
									$findplayerhits = DB::connection('mysql')->table('result_matches')->where('player_key',$hitsv)->where('match_key',$match_key)->where('innings',$innings)->select('id')->first();
									if(!empty($findplayerhits)){
									   DB::connection('mysql2')->table('result_matches')->where('id',$findplayerhits->id)->update($datahits);
									
									}else{
										DB::connection('mysql2')->table('result_matches')->insert($datahits);
									}
								}
							}
					}
				}
				if(!empty($throwerarray)){
					foreach($throwerarray as $throwkey=>$throw){
						$innings = $throwkey;
						if(!empty($throw)){
							foreach($throw as $throwv){
								$countthrow = count(array_keys($throw, $throwv, true));
								$datathrow['match_key'] =$match_key;
								$datathrow['player_key'] =$throwv;
								$datathrow['innings'] = $innings;
								$datathrow['thrower'] = $countthrow;
								$findplayerthrow = DB::connection('mysql')->table('result_matches')->where('player_key',$throwv)->where('match_key',$match_key)->where('innings',$innings)->select('id')->first();
								if(!empty($findplayerthrow)){
									DB::connection('mysql2')->table('result_matches')->where('id',$findplayerthrow->id)->update($datathrow);
								}
								else{
									DB::connection('mysql2')->table('result_matches')->insert($datathrow);
								}
							}
						}
					}
				}
				$showpoints = ResultController::player_point($match_key,$findmatchtype->format);
			}
	//		}
		}
		return 1;
	}

}