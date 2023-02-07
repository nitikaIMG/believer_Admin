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
		
		// $findalllistmatches = DB::table('series')
		// 						->where('series.status','=','opened')
		// 						->where('series.fantasy_type',$f_type)
		// 						->join('listmatches','listmatches.series','=','series.id')
		// 						->select(
		// 							'series.name as series_name',
		// 							'series.id as series_id',
		// 							'listmatches.name as listmatches_title',
		// 							'series.start_date as created_at',
		// 							'series.end_date as end_date',
		// 							'listmatches.matchkey as listmatches_matchkey'
		// 						)
		// 						->groupBY('series.id')
		// 						->orderBy('end_date','DESC')
		// 						->get();
		
		$findalllistmatches = DB::table('series')
								->where('series.fantasy_type',$f_type)
								->where('series.status','!=','closed')
								->join('listmatches','listmatches.series','=','series.id')
								->select(
									'series.name as series_name',
									'series.id as series_id',
									'listmatches.name as listmatches_title',
									'series.start_date as created_at',
									'series.end_date as end_date',
									'listmatches.matchkey as listmatches_matchkey'
								)
								->groupBY('series.id')
								->orderBy('end_date','DESC')
								->get();

		return view('matches.match_result',compact('findalllistmatches'));
	}	

	public function match_detail($id){
		// dd($id);
		// $findalllistmatches = DB::table('series')
		// 						->join('listmatches','listmatches.series','=','series.id')
		// 						->join('matchchallenges','matchchallenges.matchkey','=','listmatches.matchkey')
		// 						->where('series.id',$id)
		// 						->where('listmatches.launch_status','launched')
		// 						->select(
		// 							'matchchallenges.id as matchchallenges_id',
		// 							'listmatches.name as listmatches_title',
		// 							'listmatches.start_date as listmatches_start_date',
		// 							'listmatches.matchkey as listmatches_matchkey',
		// 							'series.id as series_id',
		// 							'listmatches.status as listmatches_status',
		// 							'listmatches.launch_status as listmatches_launch_status',
		// 							'listmatches.final_status as listmatches_final_status', 
		// 							DB::raw('count(matchchallenges.matchkey) as total_challenge'), 
		// 							DB::raw('sum(matchchallenges.joinedusers) as total_joinedusers')
		// 						)
		// 						->groupBY('listmatches.matchkey')
		// 						->orderBy('listmatches.start_date','DESC')
		// 						->get();

		$findalllistmatches = DB::table('series')
								->join('listmatches','listmatches.series','=','series.id')
								->join('matchchallenges','matchchallenges.matchkey','=','listmatches.matchkey')
								->where('series.id',$id)
								->where('listmatches.launch_status','launched')
								->select(
									'matchchallenges.id as matchchallenges_id',
									'listmatches.name as listmatches_title',
									'listmatches.start_date as listmatches_start_date',
									'listmatches.matchkey as listmatches_matchkey',
									'series.id as series_id',
									'listmatches.status as listmatches_status',
									'listmatches.launch_status as listmatches_launch_status',
									'listmatches.final_status as listmatches_final_status', 
									DB::raw('count(matchchallenges.matchkey) as total_challenge'), 
									DB::raw('sum(matchchallenges.joinedusers) as total_joinedusers')
								)
								->groupBY('listmatches.matchkey')
								->orderBy('listmatches.start_date','DESC')
								->get();
								// dd($findalllistmatches);

		return view('matches.match_detail',compact('findalllistmatches'));
		}

		public function updatescores($matchkey){
			$findmatchtype = DB::table('listmatches')->where('matchkey',$matchkey)->select('format','team1','team2','real_matchkey','second_inning_status')->first();

			
			if($findmatchtype->second_inning_status==2){
				// dd($findmatchtype);
				$getdata = $this->updatescoreparticularinning($findmatchtype->real_matchkey, $matchkey);
			}else{
				$getdata = $this->getscoresupdates($findmatchtype->real_matchkey,$matchkey);
			}
		
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
						$throwpoint = $thrower*6;
						$hittspoints = $hitter*6;
						
						if($overs>=1){
							if($erate<7){
								$economypoints = 6;
							}
							else if($erate>=7 && $erate<=7.99){
								$economypoints = 4;
							}
							else if($erate>=8 && $erate<=9){
								$economypoints = 2;
							}
							else if($erate>=14 && $erate<=15){
								$economypoints = -2;
							}
							else if($erate>=15.1 && $erate<=16){
								$economypoints = -4;
							}
							else if($erate>=16){
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
							$wicketbonuspoint=$wicketbonuspoint;
						}
						else{
							$startingpoint = 0;
							$wicketbonuspoint=0;
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
						$throwpoint = $thrower*6;
						$hittspoints = $hitter*6;
						
						
					}

					if($row->starting11==1){
						$economypoints = (int) $economypoints;
						
						$result['batting_points'] = $runpoints+$sixpoints+$boundrypoints+$strikePoint+$halcenturypoints+$centuryPoints+$thirtypoints;
						$result['fielding_points'] = $catchpoint+$stpoint+$throwpoint+$hittspoints;
						$result['bowling_points'] = $wkpoints+$maidenpoints+$economypoints+$wicketbonuspoint;

						
						$total_points = $result['total_points'] = $startingpoint+$runpoints+$sixpoints+$thirtypoints+$halcenturypoints+$centuryPoints+$boundrypoints+$strikePoint+$catchpoint+$stpoint+$wkpoints+$maidenpoints+$economypoints+$duckpoint+$hittspoints+$throwpoint+$wicketbonuspoint;
						
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
				ResultController::duouserpoints($match_key);
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

		public function duouserpoints($match_key){
			// dd('aj');
			$joinlist =DB::connection('mysql')->table('joinedleauges')->where('matchkey',$match_key)->where('fantasy_type','Duo')->get();
			if(!empty($joinlist)){
				foreach($joinlist as $row2){
					$matchplayers = DB::connection('mysql')->table('result_matches')->where('match_key',$match_key)->where('player_id',$row2->player_id)->first();

					$matchplayers1 = DB::connection('mysql')->table('result_matches')->where('innings',2)->where('match_key',$match_key)->where('player_id',$row2->player_id)->first();
					if(!empty($matchplayers1)){
						if(!empty($matchplayers)){
							DB::connection('mysql')->table('joinedleauges')->where('matchkey',$match_key)->where('fantasy_type','Duo')->where('player_id',$row2->player_id)->update(['duopoints'=>$matchplayers->total_points+$matchplayers1->total_points,'lastduopoints'=>$row2->duopoints]);
						}
					}else{
						if(!empty($matchplayers)){
							DB::connection('mysql')->table('joinedleauges')->where('matchkey',$match_key)->where('fantasy_type','Duo')->where('player_id',$row2->player_id)->update(['duopoints'=>$matchplayers->total_points,'lastduopoints'=>$row2->duopoints]);
						}
					}

					
				}
			}
		}
	public function viewwinners($matchkey){
		$findwinners = array();
		$finduserjoinedleauges = DB::connection('mysql')->table('joinedleauges')->where('joinedleauges.matchkey',$matchkey)->join('jointeam','jointeam.id','=','joinedleauges.teamid')->join('matchchallenges','matchchallenges.id','=','joinedleauges.challengeid')->join('registerusers','registerusers.id','=','joinedleauges.userid')->orderBy('joinedleauges.challengeid','ASC')->select('matchchallenges.win_amount','matchchallenges.entryfee','matchchallenges.joinedusers','matchchallenges.is_private','matchchallenges.bonus_percentage','matchchallenges.confirmed_challenge','matchchallenges.maximum_user','joinedleauges.*','registerusers.username as username','registerusers.email','registerusers.team','jointeam.points')->get();
		return view('matches.viewwinners',compact('finduserjoinedleauges'));
	}
		//---------------------- Show playing11 ------------------//
	public function showplaying($real_match_key,$match_key){
        $checkmath = DB::connection('mysql')->table('listmatches')->where('matchkey',$match_key)
        ->select('matchkey','playing11_status','status')->first();
        // echo '<pre>';print_r($checkmath);die;

        if(!empty($checkmath)){
        	$statstarted = $checkmath->status;
    		// $giveresresult = CricketapiController::getmatchdata($real_match_key);
    		$giveresresult = EntityCricketapiController::getmatchplayers($real_match_key);
    		$giveresresult1 = EntityCricketapiController::match_info($real_match_key);
    		// if($real_match_key==51162){
    		// 	dump($giveresresult);
    		// 	dd($giveresresult1);
    		// }
            $giveresresult = $giveresresult['response'];
            $giveresresult1 = $giveresresult1['response'];
    				// echo '<pre>';print_r($giveresresult);
                if(isset($giveresresult['teama']['squads'])){
                    if(!empty($giveresresult['teama']['squads'])){
                        $mynewqq = $giveresresult['teama']['squads'];
                        $teamaa = array();
                        $teamkey = $giveresresult['teama']['team_id'];
                        foreach($mynewqq as $vall1){
                        	if($vall1['playing11']=='true'){
                        		$teamaplayingxi[] = $vall1['player_id'];
                        		$getid = DB::table('players')->join('matchplayers','matchplayers.playerid','=','players.id')->where('matchplayers.matchkey',$match_key)->where('players.players_key',$vall1['player_id'])->select('matchplayers.id','playingstatus')->first();
	                            if(!empty($getid)){
	                                $dddjdh['playingstatus'] = 1;
	                                DB::connection('mysql2')->table('matchplayers')->where('id',$getid->id)->update($dddjdh); 
	                                $findplayerhits = DB::connection('mysql')->table('result_matches')->where('result_matches.player_key',$vall1['player_id'])->where('result_matches.match_key',$match_key)->where('result_matches.innings','1')->select('result_matches.id')->first();
	                                if(!empty($findplayerhits)){
									     $datahits['startingpoints']='4';
										 DB::connection('mysql2')->table('result_points')->where('resultmatch_id',$findplayerhits->id)->where('matchkey',$match_key)->update($datahits);
										 $datahitss['starting11']='1';
										 DB::connection('mysql2')->table('result_matches')->where('id',$findplayerhits->id)->where('match_key',$match_key)->update($datahitss);
									}
	                            }else{
	                            	$findmatchexist = DB::connection('mysql')->table('teams')->where('team_key',$teamkey)->select('id')->first();
									if(!empty($findmatchexist)){
		                            	$getplayer = DB::connection('mysql')->table('players')->where('players_key',$vall1['player_id'])->where('team',$findmatchexist->id)->first();
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
	            }
    	               
	            if(isset($giveresresult['teamb']['squads'])){
                    if(!empty($giveresresult['teamb']['squads'])){
                        $mynewqq1 = $giveresresult['teamb']['squads'];
                        $team2key = $giveresresult['teamb']['team_id'];
                        foreach($mynewqq1 as $vall1){
                        	if($vall1['playing11']=='true'){
                        		$teambplayingxi[] = $vall1['player_id'];
                            	$getid = DB::connection('mysql')->table('players')->join('matchplayers','matchplayers.playerid','=','players.id')->where('matchplayers.matchkey',$match_key)->where('players.players_key',$vall1['player_id'])->select('matchplayers.id')->first();
	                            if(!empty($getid)){
	                                $dddjdh['playingstatus'] = 1;
	                                DB::connection('mysql2')->table('matchplayers')->where('id',$getid->id)->update($dddjdh);
	                                $findplayerhits = DB::connection('mysql')->table('result_matches')->where('result_matches.player_key',$vall1['player_id'])->where('result_matches.match_key',$match_key)->where('result_matches.innings','1')->select('result_matches.id')->first();
									if(!empty($findplayerhits)){
									    $dataahitss['startingpoints']='4';
										DB::connection('mysql2')->table('result_points')->where('resultmatch_id',$findplayerhits->id)->where('matchkey',$match_key)->update($dataahitss);
										 $datahitsss['starting11']='1';
										 DB::connection('mysql2')->table('result_matches')->where('id',$findplayerhits->id)->where('match_key',$match_key)->update($datahitsss);
									}
	                            }else{
	                            	$findmatchexist = DB::connection('mysql')->table('teams')->where('team_key',$team2key)->select('id')->first();
									if(!empty($findmatchexist)){
		                            	$getplayer = DB::connection('mysql')->table('players')->where('players_key',$vall1['player_id'])->where('team',$findmatchexist->id)->first();
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
                }
                // dump($teamaplayingxi);
                // dd($teambplayingxi);
				if(!empty($teamaplayingxi) && !empty($teambplayingxi)){
	                $newplaying_xi= array_merge($teamaplayingxi,$teambplayingxi);
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
                if(!empty($teamaplayingxi) || !empty($teambplayingxi)){
        			$playingstat['playing11_status'] = '1';
        			$playingstat['tosswinner_team'] = $giveresresult1['toss']['winner'];
        			$playingstat['toss_decision'] = $giveresresult1['toss']['decision'];
                    DB::connection('mysql2')->table('listmatches')->where('matchkey',$match_key)->update($playingstat);
                }
        	// }
        	if($statstarted == 'notstarted'){
        		$checkmathw =  DB::connection('mysql')->table('matchplayers')->where('matchplayers.matchkey',$match_key)->where('playingstatus','1')->select('id')->count();
			    $checknotify = DB::table('playingnotification')->where('matchkey',$match_key)->first();
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
 //    public function update_results_of_matches(){
	// 	date_default_timezone_set('Asia/Kolkata'); 
	// 	$findmatchexist =DB::connection('mysql')->table('listmatches')->where('fantasy_type','Cricket')
	// 	->whereDate('start_date','<=',date('Y-m-d'))->where('launch_status','launched')
	// 	->where('final_status','!=','winnerdeclared')->where('status','!=','completed')->get();
	// 	// 		$findmatchexist =DB::connection('mysql')->table('listmatches')->where('matchkey','c.match.bcc_vs_pkcc.87f08')->get();
		
	// 	if(!empty($findmatchexist)){
			
	// 		foreach($findmatchexist as $val){
	// 			$match_type = $val->format;
	// 			$getcurrentdate = date('Y-m-d H:i:s');
	// 			$matchtimings = date('Y-m-d H:i:s',strtotime($val->start_date));
	// 			$matchtimings1 = date('Y-m-d H:i:s', strtotime( '-55 minutes', strtotime($val->start_date)));
				

	// 			if($getcurrentdate>$matchtimings1){
						
	// 				$match_key=$val->matchkey;
	// 				$real_match_key=$val->real_matchkey;
	// 				$this->showplaying($real_match_key,$match_key);
	// 			}
	// 			if($getcurrentdate>=$matchtimings){
	// 				$match_key=$val->matchkey;
	// 				$real_match_key=$val->real_matchkey;
	// 				if($val->second_inning_status==0 || $val->second_inning_status==1){
	// 					$this->getscoresupdates($real_match_key,$match_key);
	// 				}
	// 				if($val->second_inning_status==2){
	// 					$this->updatescoreparticularinning($real_match_key,$match_key);
	// 				}
	// 			}
	// 		}
	// 	  return 'completed';
	// 	}
	// }

	 public function update_results_of_matches()
    {
        date_default_timezone_set('Asia/Kolkata');
        $findmatchexist = DB::connection('mysql')->table('listmatches')->where('fantasy_type', 'Cricket')
            ->whereDate('start_date', '<=', date('Y-m-d'))->where('launch_status', 'launched')
            ->where('final_status', '!=','IsAbandoned')
            ->where('final_status', '!=','IsCanceled')
            ->where('final_status', '!=', 'winnerdeclared')->where('status', '!=', 'completed')->get();
//         $findmatchexist =DB::connection('mysql')->table('listmatches')->where('matchkey','c.match.bcc_vs_pkcc.87f08')->get();
		            // echo "<pre>";print_r($findmatchexist);die;
        if (!empty($findmatchexist)) {

            foreach ($findmatchexist as $val) {
                $match_type = $val->format;
                $getcurrentdate = date('Y-m-d H:i:s');
                $matchtimings = date('Y-m-d H:i:s', strtotime($val->start_date));
                $matchtimings1 = date('Y-m-d H:i:s', strtotime('-55 minutes', strtotime($val->start_date)));

                if ($getcurrentdate > $matchtimings1) {

                    $match_key = $val->matchkey;
                    $real_match_key = $val->real_matchkey;
                    $this->showplaying($real_match_key, $match_key);
                }
                if ($getcurrentdate >= $matchtimings) {
                    $match_key = $val->matchkey;
                    $real_match_key = $val->real_matchkey;
                    if ($val->second_inning_status == 0 || $val->second_inning_status == 1) {
                        $this->getscoresupdates($real_match_key, $match_key);
                    }
                    if ($val->second_inning_status == 2) {
                        $this->updatescoreparticularinning($real_match_key, $match_key);
                    }
                }
            }
            return 'completed';
        }
    }
    
	public function getscoresupdates($real_matchkey,$match_key){
		date_default_timezone_set('Asia/Kolkata'); 
		$m_status = [1=>'notstarted',2=>'completed',3=>'started',4=>'completed'];
		$findmatchtype =DB::table('listmatches')->where('matchkey',$match_key)->select('final_status','format')->first();
		$giveresresult = EntityCricketapiController::getmatchscore($real_matchkey);
		// echo '<pre>';print_r($giveresresult);die;
		$giveresresult = $giveresresult['response'];
		// echo '<pre>';print_r($giveresresult);die;
		$teamainnKey = array();
    	$teambinnKey =array();
		if(!empty($giveresresult) && $giveresresult!='Invalid request'){
	    	$checkpre =DB::connection('mysql2')->table('matchruns')->where('matchkey',$match_key)->first();
           	if(empty($checkpre)){
                $matchdata['matchkey'] = $match_key;
                $matchdata['teams1'] = $giveresresult['teama']['short_name'];
                $matchdata['teams2'] = $giveresresult['teamb']['short_name'];
               	if(!empty($giveresresult['result'])){
                    $matchdata['winning_status'] = $giveresresult['result'];
               	}else{
                   	$matchdata['winning_status']=0;
               	}
                if(isset($giveresresult['innings']) && !empty($giveresresult['innings'])){
                	
                	if(count($giveresresult['innings'])>2){
                		foreach ($giveresresult['innings'] as $k => $value) {
                			if($value['batting_team_id']==$giveresresult['teama']['team_id']){
                				$teamainnKey[] = $giveresresult['innings'][$k];
                			}elseif ($value['batting_team_id']==$giveresresult['teamb']['team_id']) {
                				$teambinnKey[] = $giveresresult['innings'][$k];
                			}
                		}
                	}else{
                		$key1 = ''.(array_search($giveresresult['teama']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
                		$key2 = ''.(array_search($giveresresult['teamb']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
                		// $teamainnKey[] = $giveresresult['innings'][$key1];
                		// $teambinnKey[] = $giveresresult['innings'][$key2];
                		$teamainnKey[] = ($key1!='')?$giveresresult['innings'][$key1]:[];
                		$teambinnKey[] = ($key2!='')?$giveresresult['innings'][$key2]:[];
                	}

                   	$gettestscore1 = 0;
                   	$gettestscore2 = 0;
                   	$gettestwicket1 = 0;
                   	$gettestwicket2 = 0;
                   	$gettestover1 = 0;
                   	$gettestover2 = 0;
                   	if(isset($teambinnKey[1]) && !empty($teambinnKey[1])){
               	     	$gettestscore2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['runs']:0;
                   	    $gettestscore1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['runs']:0;
                   	    $gettestwicket1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['wickets']:0;
                   	    $gettestwicket2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['wickets']:0;
                   	    $gettestover1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['overs']:0;
                   	    $gettestover2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['overs']:0;
                   	}
                   	if(empty($gettestwicket1)){
                   	     $matchdata['wickets1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0;
                   	}else{
                   	    $matchdata['wickets1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0).','.$gettestwicket1;
                   	}
                   	if(empty($gettestwicket2)){
                   	     $matchdata['wickets2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0;
                   	}else{
                   	    $matchdata['wickets2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0).','.$gettestwicket2;
                   	}
                   	if(empty($gettestover1)){
                   	     $matchdata['overs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0;
                   	}else{
                   	    $matchdata['overs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0).','.$gettestover1;
                   	}
                   	if(empty($gettestover2)){
                   	     $matchdata['overs2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0;
                   	}else{
                   	    $matchdata['overs2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0).','.$gettestover2;
                   	}
                   	if(empty($gettestscore1)){
                   	     $matchdata['runs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0;
                   	}else{
                   	    $matchdata['runs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0).','.$gettestscore1;
                   	}
                   	if(empty($gettestscore2)){
                   	     $matchdata['runs2'] =(!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0;
                   	}else{
                   	    $matchdata['runs2'] =((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0).','.$gettestscore2;
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
               	DB::connection('mysql')->table('matchruns')->insert($matchdata);
       		}else{
       			$matchdata['matchkey'] = $match_key;
                $matchdata['teams1'] = $giveresresult['teama']['short_name'];
                $matchdata['teams2'] = $giveresresult['teamb']['short_name'];
               	if(!empty($giveresresult['result'])){
                    $matchdata['winning_status'] = $giveresresult['result'];
               	}else{
                   	$matchdata['winning_status']=0;
               	}
				//    echo '<pre>';print_r($giveresresult);die;
               	if(isset($giveresresult['innings']) && !empty($giveresresult['innings'])){
               		$teamainnKey = array();
                	$teambinnKey =array();
                	if(count($giveresresult['innings'])>2){
                		foreach ($giveresresult['innings'] as $k => $value) {
                			if($value['batting_team_id']==$giveresresult['teama']['team_id']){
                				$teamainnKey[] = $giveresresult['innings'][$k];
                			}elseif ($value['batting_team_id']==$giveresresult['teamb']['team_id']) {
                				$teambinnKey[] = $giveresresult['innings'][$k];
                			}
                		}
                	}else{
                		$key1 = ''.(array_search($giveresresult['teama']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
                		$key2 = ''.(array_search($giveresresult['teamb']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
                		// $teamainnKey[] = $giveresresult['innings'][$key1];
                		// $teambinnKey[] = $giveresresult['innings'][$key2];
                		$teamainnKey[] = ($key1!='')?$giveresresult['innings'][$key1]:[];
                		$teambinnKey[] = ($key2!='')?$giveresresult['innings'][$key2]:[];
                	}
                    $gettestscore1 = 0;
                   	$gettestscore2 = 0;
                   	$gettestwicket1 = 0;
                   	$gettestwicket2 = 0;
                   	$gettestover1 = 0;
                   	$gettestover2 = 0;
                   	if(isset($teambinnKey[1]) && !empty($teambinnKey[1])){
               	     	// $gettestscore2 = $teambinnKey[1]['equations']['runs'];
                   	  //   $gettestscore1 = $teamainnKey[1]['equations']['runs'];
                   	  //   $gettestwicket1 = $teamainnKey[1]['equations']['wickets'];
                   	  //   $gettestwicket2 = $teambinnKey[1]['equations']['wickets'];
                   	  //   $gettestover1 = $teamainnKey[1]['equations']['overs'];
                   	  //   $gettestover2 = $teambinnKey[1]['equations']['overs'];
                   	    $gettestscore2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['runs']:0;
                   	    $gettestscore1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['runs']:0;
                   	    $gettestwicket1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['wickets']:0;
                   	    $gettestwicket2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['wickets']:0;
                   	    $gettestover1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['overs']:0;
                   	    $gettestover2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['overs']:0;
                   	}
                   	if(empty($gettestwicket1)){
                   	     $matchdata1['wickets1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0;
                   	}else{
                   	    $matchdata1['wickets1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0).','.$gettestwicket1;
                   	}
                   	if(empty($gettestwicket2)){
                   	     $matchdata1['wickets2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0;
                   	}else{
                   	    $matchdata1['wickets2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0).','.$gettestwicket2;
                   	}
                   	if(empty($gettestover1)){
                   	     $matchdata1['overs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0;
                   	}else{
                   	    $matchdata1['overs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0).','.$gettestover1;
                   	}
                   	if(empty($gettestover2)){
                   	     $matchdata1['overs2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0;
                   	}else{
                   	    $matchdata1['overs2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0).','.$gettestover2;
                   	}
                   	if(empty($gettestscore1)){
                   	     $matchdata1['runs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0;
                   	}else{
                   	    $matchdata1['runs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0).','.$gettestscore1;
                   	}
                   	if(empty($gettestscore2)){
                   	     $matchdata1['runs2'] =(!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0;
                   	}else{
                   	    $matchdata1['runs2'] =((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0).','.$gettestscore2;
                   	}
               	}else{
                   	$matchdata1['wickets1'] = 0;
                   	$matchdata1['wickets2'] = 0;
                   	$matchdata1['overs1'] = 0;
                   	$matchdata1['overs2'] = 0;
                   	$matchdata1['runs1'] = 0;
                   	$matchdata1['runs2'] = 0;
               	}
               	// dd($matchdata1);
               	DB::connection('mysql')->table('matchruns')->where('matchkey',$match_key)->update($matchdata1);
           	}
		    // End Of ankit code
			$mainarrayget = $giveresresult;
			$getmtdatastatus['status'] = $m_status[$mainarrayget['status']];
			if($getmtdatastatus['status']=='completed' && $findmatchtype->final_status=='pending'){
				$getmtdatastatus['final_status'] = 'IsReviewed';
			}
			DB::connection('mysql')->table('listmatches')->where('matchkey',$match_key)->update($getmtdatastatus);
			$playin = DB::connection('mysql2')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('playingstatus',1)->pluck('players.players_key')->toArray();
			// $finalplayingteams = array();
			// if(!empty($findteams)){
			// 	foreach($findteams as $tp){
			// 		if(isset($tp['match']['playing_xi'])){
			// 			$findpl = $tp['match']['playing_xi'];
			// 			if(!empty($findpl)){
			// 				foreach($findpl as $fl){
			// 					$finalplayingteams[] = $fl;
			// 				}
			// 			}
			// 		}
			// 	}
			// }
			if(isset($mainarrayget['players'])){
				$players = array_column($mainarrayget['players'], 'pid');
				$giveres = $players;
				// dump($players);
				$matchplayers =DB::connection('mysql2')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('matchkey',$match_key)->select('matchplayers.*','players.players_key','players.role as playerrole')->get();
				
				$a =$matchplayers->toArray();

				if(!empty($a)){
					$innplayers = [];$t = '';$f=1;$j=1;
					foreach ($matchplayers as $kp => $player) {
						$pid = $player->playerid;
						// dump($pid);
						// $playr =DB::connection('mysql')->table('players')->where('id',$pid)->select('players_key')->first();
						$value = $player->players_key;
						$i = 1;
						
						foreach ($teamainnKey as $ak => $teama) {
							// if($value==159 || $value==49706){
								$datasv=array();	$runs = 0;		$fours = 0;		$six = 0;		$duck=0;		$maiden_over=0;		$wicket = 0;
								$catch=0;			$runouts = 0;	$stumbed = 0;	$batdots = 0;	$balldots = 0;	$miletone_run = 0;	$bball = 0;
								$grun = 0;			$balls = 0;		$bballs = 0;	$extra = 0;		$overs = 0;
								
								$bat = (isset($teama['batsmen']))?''.(array_search($value, array_column($teama['batsmen'], 'batsman_id'))):'';
								if($bat!=''){						
									$innplayers[$value][$i]['batting'] = $teama['batsmen'][$bat];
								}else{
									if(!isset($innplayers[$value][$i]['batting'])){
										$innplayers[$value][$i]['batting'] = [];
									}
									$bowl = (isset($teama['bowlers']))?''.(array_search($value, array_column($teama['bowlers'], 'bowler_id'))):'';
									$field = (isset($teama['fielder']))?''.(array_search($value, array_column($teama['fielder'], 'fielder_id'))):'';
									$innplayers[$value][$i]['bowling'] = ($bowl!='')?$teama['bowlers'][$bowl]:[];
									$innplayers[$value][$i]['fielding'] = ($field!='')?$teama['fielder'][$field]:[];
								}
								// dump($innplayers[$value]);continue;
								$batb = (isset($teambinnKey[$ak]['batsmen']))?''.(array_search($value, array_column($teambinnKey[$ak]['batsmen'], 'batsman_id'))):'';
								if($batb!=''){	
									$innplayers[$value][$i]['batting'] = $teambinnKey[$ak]['batsmen'][$batb];
								}else{
									if(!isset($innplayers[$value][$i]['batting'])){
										$innplayers[$value][$i]['batting'] = [];
									}
									if(empty($innplayers[$value][$i]['bowling'])){
										$bowlb = (isset($teambinnKey[$ak]['bowlers']))?''.(array_search($value, array_column($teambinnKey[$ak]['bowlers'], 'bowler_id'))):'';
										$innplayers[$value][$i]['bowling'] = ($bowlb!='')?$teambinnKey[$ak]['bowlers'][$bowlb]:[];
									}
									if(empty($innplayers[$value][$i]['fielding'])){
										$fieldb = (isset($teambinnKey[$ak]['fielder']))?''.(array_search($value, array_column($teambinnKey[$ak]['fielder'], 'fielder_id'))):'';
										$innplayers[$value][$i]['fielding'] = ($fieldb!='')?$teambinnKey[$ak]['fielder'][$fieldb]:[];
									}
									
									// dd($innplayers[$value][$i]['bowling']);
								}

								$play = $innplayers[$value][$i];
								// dump($play);
							
								// dump($playin);
								if(!empty($play['batting']) || !empty($play['bowling']) || !empty($play['fielding'])){
									if(in_array($value,$playin)){
										$datasv['starting11']=1;
									}
									// $datasv['starting11']=1;
									if(!empty($play['batting'])){
										if(isset($play['batting']['strike_rate'])){
											$datasv['batting'] = 1;
											$datasv['strike_rate'] = $play['batting']['strike_rate'];
										}
										else{
											$datasv['batting'] = 0;
										}
										/* runs points */
										if(isset($play['batting']['runs'])){
											$datasv['runs'] = $runs = $runs +  $play['batting']['runs'];
										}else{
											$datasv['runs'] =0;
										}
										/* fours points */
										
										if(isset($play['batting']['fours'])){
											$datasv['fours'] = $fours = $fours + $play['batting']['fours'];
										}
										if(isset($play['batting']['balls_faced'])){
											$datasv['bball'] = $bball = $bball + $play['batting']['balls_faced'];
											}
										/* sixes Points */
										
										if(isset($play['batting']['sixes'])){
											$datasv['six'] = $six = $six + $play['batting']['sixes'];
										}
										if(!empty($play['batting']['dismissal'])){

											if($player->playerrole!='bowler'){
												if(($runs == 0) && ($play['batting']['dismissal'] != '')){
													$datasv['duck'] = $duck = 1;
												}else{
													$datasv['duck'] = $duck = 0;
												}
											}else{
												$datasv['duck'] = $duck = 0;
											}
											if($play['batting']['dismissal'] != ''){
												$datasv['out_str'] = $play['batting']['how_out'];
											}else{
												$datasv['out_str'] = 'not out';
											}
										}
										if(isset($batting['dots'])){
											$datasv['battingdots'] = $batdots = $batdots + $play['batting']['run0'];
										}

											if($play['batting']['dismissal']=='lbw' || $play['batting']['dismissal']=='bowled'){
											
												$wbowlerkey = $play['batting']['bowler_id'];
												// echo "<pre>";print_r($wbowlerkey);
												
												$bowlerplayersid =DB::connection('mysql2')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('players.players_key',$wbowlerkey)->where('matchkey',$match_key)->value('matchplayers.playerid');
												// $wplayerid = array_search($wbowlerkey, array_column($a, 'playerid'));
												if(!empty($bowlerplayersid)){
													$datasv['wplayerid']= $bowlerplayersid;
												}
											}
										$datasv['wicket_type']= $play['batting']['dismissal'];
										
									}
									// if($value==44747){
									// 	dd($play);
									// }
									// bowling points //
									if(!empty($play['bowling'])){

										$bowling = $play['bowling'];
										$datasv['bowling'] = 1;
										$datasv['economy_rate'] = $bowling['econ'];
										$datasv['maiden_over'] = $maiden_over = $maiden_over + $bowling['maidens'];
										$datasv['wicket'] = $wicket = $wicket + $bowling['wickets'];
										$datasv['overs'] = $overs = $overs + $bowling['overs'];
										$datasv['grun'] = $grun = $grun + $bowling['runs_conceded'];
										$datasv['balldots'] = $balldots = $balldots + (!empty($bowling['run0']))?$bowling['run0']:0;
										$datasv['balls'] = $balls = $balls + ($overs*6);
										if(!empty($bowling['noballs']) && !empty($bowling['wides'])){
											$datasv['extra'] = $extra = $extra + ($bowling['noballs']+$bowling['wides']);
										}
										
										// dd($bowling);
										
									}

									// fielding points //
									if(!empty($play['fielding'])){
										$fielding = $play['fielding'];
										$datasv['catch'] = $catch = $catch + $fielding['catches'];
										if($fielding['runout_direct_hit']==0){
											$datasv['hitter'] = $fielding['runout_catcher'];
											$datasv['thrower'] = $fielding['runout_thrower'];
										}else{
											$datasv['thrower'] = 1;
											$datasv['hitter'] = 1;
										}
										$datasv['stumbed'] = $stumbed = $stumbed + $fielding['stumping'];
									}
									$datasv['match_key'] =$match_key;
									$datasv['player_key'] =$value;
									$datasv['player_id'] =$pid;
									$datasv['innings'] =$i;
									$findplayerex = DB::connection('mysql2')->table('result_matches')->where('player_key',$value)->where('match_key',$match_key)->where('innings',$i)->select('id')->first();
									if(!empty($findplayerex)){
										// dump($findplayerex->id);
										// $t .= ','.$value;
										// dump($datasv);
										DB::connection('mysql')->table('result_matches')->where('id',$findplayerex->id)->update($datasv);
									}else{
										// dump($datasv);
										DB::connection('mysql')->table('result_matches')->insert($datasv);
									}
								}else{
									if(in_array($value,$playin)){
										$datasvs['starting11']=1;
									}else{
										$datasvs['starting11']=0;
									}
									$datasvs['out_str'] = 'not out';
								    $datasvs['match_key'] =$match_key;
									$datasvs['player_key'] =$value;
									$datasvs['player_id'] =$pid;
									$datasvs['innings'] =$i;
									$findplayerex = DB::connection('mysql2')->table('result_matches')->where('player_key',$value)->where('match_key',$match_key)->where('innings',$i)->select('id')->first();
									if(!empty($findplayerex)){
										// dump('else');
										DB::connection('mysql')->table('result_matches')->where('id',$findplayerex->id)->update($datasvs);
									}else{
										DB::connection('mysql')->table('result_matches')->insert($datasvs);
									}
								}

							// }
							
							
							$i++; 
						}
						// foreach ($teambinnKey as $ak => $teamb) {
							// 	$bat = array_search($value, array_column($teamb['batsmen'], 'batsman_id'));
							// 	if($bat!=''){						
							// 		$innplayers[$value][$j]['batting'] = $teamb['batsmen'][$bat];
							// 	}else{
							// 		if(!isset($innplayers[$value][$j]['batting'])){
							// 			$innplayers[$value][$j]['batting'] = [];
							// 		}
							// 		$bowl = array_search($value, array_column($teamb['bowlers'], 'bowler_id'));
							// 		$field = array_search($value, array_column($teamb['fielder'], 'fielder_id'));
							// 		$innplayers[$value][$j]['bowling'] = ($bowl!='')?$teamb['bowlers'][$bowl]:[];
							// 		$innplayers[$value][$j]['fielding'] = ($field!='')?$teamb['fielder'][$field]:[];
							// 	}
							// 	$j++;
						// }
					

					}
					// dd($playin)
					// die;

						// dd($innplayers);
					// die;
					$showpoints = ResultController::player_point($match_key,$findmatchtype->format,$real_matchkey);
				}
			}
			
			// die;
			// dd($innplayers);
		}
		return 1;
	}

	public function refund_amount(){
		date_default_timezone_set('Asia/Kolkata');
		$current = date('Y-m-d H:i:s');
		$match_time = date('Y-m-d H:i:s', strtotime( '-15 minutes', strtotime($current)));
		// $match_time = date('Y-m-d H:i:s', strtotime($current));
		$findmatches = DB::connection('mysql')->table('listmatches')->where('start_date', '<=' , $match_time)->where('status','started')->select('matchkey')->get();
		// dd($findmatches);
		if(!empty($findmatches)){
			foreach ($findmatches as $value){
				$match_challenges = DB::connection('mysql')->table('matchchallenges')->where('matchkey',$value->matchkey)->where('status','!=','canceled')->select('confirmed_challenge','id','joinedusers','maximum_user','matchkey','entryfee','fantasy_type','status')->get();
			    $match_challenges = $match_challenges->toArray();
			    if(!empty($match_challenges)){
				    foreach ($match_challenges as  $value1) {
						$totaljoineduser = DB::connection('mysql')->table('joinedleauges')->where('matchkey',$value->matchkey)->where('challengeid',$value1->id)->count();
						if($value1->maximum_user >= $totaljoineduser){
							if($value1->confirmed_challenge == 0 && $value1->status!='closed'){
							    $getresponse = $this->refundprocess($value1->id,$value1->entryfee,$value->matchkey,'challenge cancel');
							    if($getresponse==true){
							        $data['status'] = 'canceled';
							        DB::connection('mysql2')->table('matchchallenges')->where('id',$value1->id)->update($data);
							    }
							}
							// Here contest will cancel if contest is confirmed or joined users are equal to 1 only ***************
							// ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
							elseif($value1->confirmed_challenge == 1 && $totaljoineduser==1){
								$getresponse = $this->refundprocess($value1->id,$value1->entryfee,$value->matchkey,'challenge cancel');
								if($getresponse==true){
									$data['status'] = 'canceled';
									DB::connection('mysql2')->table('matchchallenges')->where('id',$value1->id)->update($data);
								}
							}

							if($value1->maximum_user <= 3 OR $totaljoineduser<=3){
								$getTeam = DB::table('joinedleauges')->join('jointeam', 'joinedleauges.teamid', '=', 'jointeam.id')->where('joinedleauges.matchkey',$value->matchkey)->where('joinedleauges.challengeid',$value1->id)->select('joinedleauges.*','jointeam.players','jointeam.captain','jointeam.vicecaptain')->get();
								
								if(isset($getTeam[1]) &&  $getTeam[0]->captain==$getTeam[1]->captain && $getTeam[0]->vicecaptain==$getTeam[1]->vicecaptain){
									$allplayersget = explode(',',$getTeam[0]->players);
									$nowplayers  = explode(',',$getTeam[1]->players);
									$result = array_intersect($nowplayers, $allplayersget);
									if(count($allplayersget)==count($result)){
										$getresponse = $this->refundprocess($value1->id,$value1->entryfee,$value->matchkey,'challenge cancel');
										if($getresponse==true){
											$data['status'] = 'canceled';
											DB::connection('mysql2')->table('matchchallenges')->where('id',$value1->id)->update($data);
										}
									}
								}else if(isset($getTeam[2]) && $getTeam[1]->captain==$getTeam[2]->captain && $getTeam[1]->vicecaptain==$getTeam[2]->vicecaptain){
									$allplayersget = explode(',',$getTeam[1]->players);
									$nowplayers  = explode(',',$getTeam[2]->players);
									$result = array_intersect($nowplayers, $allplayersget);
									if(count($allplayersget)==count($result)){
										$getresponse = $this->refundprocess($value1->id,$value1->entryfee,$value->matchkey,'challenge cancel');
										if($getresponse==true){
											$data['status'] = 'canceled';
											DB::connection('mysql2')->table('matchchallenges')->where('id',$value1->id)->update($data);
										}
									}
		
								}else if(isset($getTeam[2]) && $getTeam[0]->captain==$getTeam[2]->captain && $getTeam[0]->vicecaptain==$getTeam[2]->vicecaptain){
									$allplayersget = explode(',',$getTeam[2]->players);
									$nowplayers  = explode(',',$getTeam[0]->players);
									$result = array_intersect($nowplayers, $allplayersget);
									if(count($allplayersget)==count($result)){
										$getresponse = $this->refundprocess($value1->id,$value1->entryfee,$value->matchkey,'challenge cancel');
										if($getresponse==true){
											$data['status'] = 'canceled';
											DB::connection('mysql2')->table('matchchallenges')->where('id',$value1->id)->update($data);
										}
									}
								}
							} 
							// dump($value1->id);
								$this->duocontestuserrefund($value1);
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
	    $abcdd = DB::connection('mysql')->table('listmatches')->where('matchkey',$matchkey)->where('final_status','!=','winnerdeclared')->where('final_status','!=','IsAbandoned')->where('final_status','!=','IsCanceled')->first();
	 if(!empty($abcdd)){
	     $allchallenges =DB::connection('mysql')->table('matchchallenges')->where('matchkey',$matchkey)
		->where('status','!=','canceled')->select('id','win_amount','contest_type','winning_percentage','pricecard_type','entryfee','id','joinedusers','is_bonus','bonus_percentage','maximum_user','confirmed_challenge')->get();

		

		if(!empty($allchallenges)){ 
			foreach($allchallenges as $challenge){
			    $totaljoineduser = DB::connection('mysql')->table('joinedleauges')->where('matchkey',$matchkey)->where('challengeid',$challenge->id)->count();
				 if($challenge->maximum_user > $totaljoineduser){
    				if($challenge->confirmed_challenge == 0){
    				    $getresponse = $this->refundprocess($challenge->id,$challenge->entryfee,$matchkey,'challenge cancel');
    				    if($getresponse==true){
    				        $data['status'] = 'canceled';
    				        DB::connection('mysql2')->table('matchchallenges')->where('id',$challenge->id)->update($data);
    				    }
    				}
					// Here contest will cancel if contest is confirmed or joined users are equal to 1 only ***************
    				// ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    				elseif($challenge->confirmed_challenge == 1 && $totaljoineduser==1){
    				    $getresponse = $this->refundprocess($challenge->id,$challenge->entryfee,$matchkey,'challenge cancel');
    				    if($getresponse==true){
    				        $data['status'] = 'canceled';
    				        DB::connection('mysql2')->table('matchchallenges')->where('id',$challenge->id)->update($data);
    				    }
    				}


					if($challenge->maximum_user <= 3OR  $totaljoineduser<=3 ){
						$getTeam = DB::connection('mysql')->table('joinedleauges')->join('jointeam', 'joinedleauges.teamid', '=', 'jointeam.id')->where('joinedleauges.matchkey',$matchkey)->where('joinedleauges.challengeid',$challenge->id)->select('joinedleauges.*','jointeam.players','jointeam.captain','jointeam.vicecaptain')->get();
						if(isset($getTeam[1]) &&  $getTeam[0]->captain==$getTeam[1]->captain && $getTeam[0]->vicecaptain==$getTeam[1]->vicecaptain){
							$allplayersget = explode(',',$getTeam[0]->players);
							$nowplayers  = explode(',',$getTeam[1]->players);
							$result = array_intersect($nowplayers, $allplayersget);
							if(count($allplayersget)==count($result)){
								$getresponse = $this->refundprocess($value1->id,$value1->entryfee,$value->matchkey,'challenge cancel');
								if($getresponse==true){
									$data['status'] = 'canceled';
									DB::connection('mysql2')->table('matchchallenges')->where('id',$value1->id)->update($data);
								}
							}
						}else if(isset($getTeam[2]) && $getTeam[1]->captain==$getTeam[2]->captain && $getTeam[1]->vicecaptain==$getTeam[2]->vicecaptain){
							$allplayersget = explode(',',$getTeam[1]->players);
							$nowplayers  = explode(',',$getTeam[2]->players);
							$result = array_intersect($nowplayers, $allplayersget);
							if(count($allplayersget)==count($result)){
								$getresponse = $this->refundprocess($value1->id,$value1->entryfee,$value->matchkey,'challenge cancel');
								if($getresponse==true){
									$data['status'] = 'canceled';
									DB::connection('mysql2')->table('matchchallenges')->where('id',$value1->id)->update($data);
								}
							}

						}else if(isset($getTeam[2]) && $getTeam[0]->captain==$getTeam[2]->captain && $getTeam[0]->vicecaptain==$getTeam[2]->vicecaptain){
							$allplayersget = explode(',',$getTeam[2]->players);
							$nowplayers  = explode(',',$getTeam[0]->players);
							$result = array_intersect($nowplayers, $allplayersget);
							if(count($allplayersget)==count($result)){
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
		
		$allchallenges =DB::connection('mysql')->table('matchchallenges')->where('matchkey',$matchkey)->where('status','!=','canceled')->select('win_amount','contest_type','winning_percentage','pricecard_type','entryfee','id','joinedusers','is_bonus','bonus_percentage','win_amount_2','contest_cat','fantasy_type')->get();
		
		if(!empty($allchallenges)){ 
			foreach($allchallenges as $challenge){
				if($challenge->fantasy_type=='Duo'){
					$joinedusers = DB::connection('mysql')->table('joinedleauges')->join('matchchallenges','matchchallenges.id','=','joinedleauges.challengeid')->where('joinedleauges.matchkey',$matchkey)->where('joinedleauges.challengeid',$challenge->id)->select('joinedleauges.userid','joinedleauges.duopoints as points','joinedleauges.id as jid')->get();
				}else{
					$joinedusers = DB::connection('mysql')->table('joinedleauges')->join('matchchallenges','matchchallenges.id','=','joinedleauges.challengeid')->where('joinedleauges.matchkey',$matchkey)->where('joinedleauges.challengeid',$challenge->id)->join('jointeam','jointeam.id','=','joinedleauges.teamid')->select('joinedleauges.userid','points','joinedleauges.id as jid')->get();
				}
				$joinedusers = $joinedusers->toArray();
				if(!empty($joinedusers)){
					// calculate the price cards //
					if($challenge->contest_type=='Amount'){
						$prc_arr = array();
						if($challenge->pricecard_type=='Amount'){
							$matchpricecards =DB::connection('mysql')->table('matchpricecards')->where('challenge_id',$challenge->id)->select('min_position','max_position','price')->get();
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
							$matchpricecards =DB::connection('mysql')->table('matchpricecards')->where('challenge_id',$challenge->id)->select('min_position','max_position','price_percent')->get();
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
						$gtjnusers = $totaljoineduser;
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
								$winningdata = DB::connection('mysql')->table('finalresults')->where('joinedid',$fpuskjoinid)->first();
							if(empty($winningdata)){
								$fres = array();
								$listmatches = DB::connection('mysql')->table('listmatches')->where('matchkey',$matchkey)->select('series','name')->first();
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
								$fres['amount'] = number_format(floor($fpusv['amount']*100)/100,'2','.','');
								$fres['rank'] = $fpusv['rank'];
								$fres['matchkey'] = $matchkey;
								$fres['challengeid'] = $challengeid;
								$fres['seriesid'] = $seriesid;
								$fres['transaction_id'] = $transactionidsave;
								$fres['joinedid'] = $fpuskjoinid;
								$findalreexist = DB::connection('mysql')->table('finalresults')->where('joinedid',$fpuskjoinid)->where('userid',$fpusk)->select('id')->first();
								if(empty($findalreexist)){
									DB::connection('mysql2')->table('finalresults')->insert($fres);
									$finduserbalance = DB::connection('mysql')->table('userbalance')->where('user_id',$fpusk)->first();
									if(!empty($finduserbalance)){
										
										if($fpusv['amount']>10000){
											$datatr = array();
											$dataqs = array();
											$tdsdata['tds_amount'] = (31.2/100)*($fres['amount']/100);
											$tdsdata['amount'] = $fpusv['amount'];
											$remainingamount = $fres['amount']-$tdsdata['tds_amount'];
											$tdsdata['userid'] = $fpusk;
											$tdsdata['challengeid'] = $challenge->id;
											DB::connection('mysql2')->table('tdsdetails')->insert($tdsdata);
											$fpusv['amount'] = $remainingamount;
											//user balance//
											$registeruserdetails = DB::table('registerusers')->where('id',$fpusk)->first();
											$findlastow = DB::table('userbalance')->where('user_id',$fpusk)->first();
											$dataqs['winning'] = number_format($findlastow->winning+$fres['amount'],2, ".", "");
											
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
											
											$datanot['title'] = 'You won amount Rs.'.$fres['amount'].' and 31.2% amount of '.$tdsdata['amount'].' deducted due to TDS.';
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
											$win_amount = $fres['amount'];
											$type = 'normal';

											// ResultController::refer_winning_bonus($matchkey, $challenge_id, $player_id, $joinid, $user_id, $win_amount, $type);
											# refer winning bonus
										}else{
											$datatr = array();
											$dataqs = array();
											//user balance//
											$registeruserdetails = DB::connection('mysql')->table('registerusers')->where('id',$fpusk)->first();

											$findlastow = DB::connection('mysql')->table('userbalance')->where('user_id',$fpusk)->first();
											$dataqs['winning'] =  number_format($findlastow->winning+$fres['amount'],2, ".", "");
											DB::connection('mysql2')->table('userbalance')->where('id',$findlastow->id)->update($dataqs);
											if($fpusv['amount']>0){
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
											
												//notifications entry//
												$datanot['title'] = 'You won amount Rs.'.$fres['amount'];
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
												$win_amount = $fres['amount'];
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


		public function duocontestuserrefund($value1){
			if($value1->fantasy_type == "Duo"){
				$getTeam = DB::table('joinedleauges')->where('joinedleauges.matchkey',$value1->matchkey)->where('joinedleauges.challengeid',$value1->id)->get();
				if(!empty($getTeam->toArray())){
					foreach($getTeam as $challengeteam){						
						$checkplayer = DB::table('matchplayers')->where('matchkey',$challengeteam->matchkey)->where('playerid',$challengeteam->player_id)->first();
						if($checkplayer->playingstatus!=1){
							$leaugestransactions = DB::table('leaugestransactions')->where('user_id',$challengeteam->userid)->where('matchkey',$challengeteam->matchkey)->where('challengeid',$challengeteam->challengeid)->first();
							if(!empty($leaugestransactions)){
								$refund_data = DB::connection('mysql')->table('refunds')->where('joinid',$leaugestransactions->joinid)->select('id')->first();
								// dd($refund_data);
								if(empty($refund_data)){
									$entry_fee = $leaugestransactions->bonus+$leaugestransactions->balance+$leaugestransactions->winning;;
									$last_row = DB::connection('mysql')->table('userbalance')->where('user_id',$challengeteam->userid)->first();
									if(!empty($last_row)){
										$data_bal['balance'] = number_format($last_row->balance+$leaugestransactions->balance,2, ".", "");
										$data_bal['winning'] = number_format($last_row->winning+$leaugestransactions->winning,2, ".", "");
										$data_bal['bonus'] =  number_format($last_row->bonus+$leaugestransactions->bonus,2, ".", "");
										$data_bal['referral_income'] =  number_format($last_row->referral_income+$leaugestransactions->referral_income,2, ".", "");
					
										DB::connection('mysql2')->table('userbalance')->where('id',$last_row->id)->update($data_bal);
										$refund_data['userid']=$challengeteam->userid;
										$refund_data['amount']=$entry_fee;
										$refund_data['joinid']=$leaugestransactions->joinid;
										$refund_data['challengeid']=$challengeteam->challengeid ;
										$refund_data['reason']='Team Not In PlayingXI';
										$refund_data['matchkey']=$challengeteam->matchkey;
										$transaction_id= (Helpers::settings()->short_name ?? ''.'-').rand(100,999).time().'-'.$challengeteam->userid;
										$refund_data['transaction_id']=$transaction_id;
										DB::connection('mysql2')->table('refunds')->insert($refund_data);
										DB::connection('mysql2')->table('joinedleauges')->where('joinedleauges.matchkey',$value1->matchkey)->where('joinedleauges.challengeid',$value1->id)->where('joinedleauges.userid',$challengeteam->userid)->update(['close_status'=>0]);
										$getjoineduser = DB::connection('mysql')->table('matchchallenges')->where('matchkey',$value1->matchkey)->where('status','!=','canceled')->where('id',$challengeteam->challengeid)->first();
										// dd($getjoineduser);
										DB::connection('mysql')->table('matchchallenges')->where('matchkey',$value1->matchkey)->where('status','!=','canceled')->where('id',$challengeteam->challengeid)->update(['joinedusers'=>$getjoineduser->joinedusers-1,'status'=>'opened']);
										$data_trans['transaction_id'] = $transaction_id;
										$data_trans['type'] = 'Refund';
										$data_trans['transaction_by'] = Helpers::settings()->short_name ?? '';
										$data_trans['amount'] = $entry_fee;
										$data_trans['paymentstatus'] = 'confirmed';
										$data_trans['challengeid'] = $challengeteam->challengeid;
										$data_trans['bonus_amt'] = $leaugestransactions->bonus;
										$data_trans['win_amt'] = $leaugestransactions->winning;
										$data_trans['addfund_amt'] = $leaugestransactions->balance;
										$data_trans['referral_amt'] = $leaugestransactions->referral_income;
										$data_trans['bal_bonus_amt'] = $data_bal['bonus'];
										$data_trans['bal_win_amt'] = $data_bal['winning'];
										$data_trans['bal_fund_amt'] = $data_bal['balance'];
										$data_trans['bal_referral_amt'] = $data_bal['referral_income'];
										$data_trans['userid'] = $challengeteam->userid;
										$data_trans['total_available_amt'] = $data_bal['balance']+$data_bal['winning']+$data_bal['bonus']+$data_bal['referral_income'];
										DB::connection('mysql2')->table('transactions')->insert($data_trans);
										//notifications//
										$totalentryfee = $leaugestransactions->bonus+$leaugestransactions->balance+$leaugestransactions->winning;
										$datan['title'] = 'Refund Amount of Rs.'.$totalentryfee.' for challenge PlayingXI Not Availabele';
										$datan['userid'] = $challengeteam->userid;
										DB::connection('mysql2')->table('notifications')->insert($datan);
										//push notifications//
										$titleget = 'Refund Amount!';
										Helpers::sendnotification($titleget,$datan['title'],'',$challengeteam->userid);
										//end push notifications//
									}
								}
							}
						}
					}
				}
			}
		}




		public function team_points($matchkey){
			$findmatchdetails = DB::table('listmatches')->where('matchkey',$matchkey)->first();
			$match_scores = DB::table('result_matches')->join('players','players.players_key','=','result_matches.player_key')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_matches.match_key',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_matches.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.short_name as team_short_name','teams.team_key as teams_team_key','matchplayers.playerid as matchplayers_playerid')->get(); 
			$match_points = DB::table('result_points')->join('players','players.id','=','result_points.playerid')->join('result_matches','result_matches.id','=','result_points.resultmatch_id')->join('matchplayers','matchplayers.playerid','=','players.id')->join('teams','teams.id','=','players.team')->where('result_points.matchkey',$matchkey)->where('matchplayers.matchkey',$matchkey)->orderBy('result_matches.innings')->orderBy('players.team')->select('result_points.*','matchplayers.matchkey as matchplayers_matchkey','matchplayers.points as matchplayers_points','matchplayers.role as matchplayers_role','matchplayers.name as matchplayers_name','players.players_key as players_player_key','players.team as players_team','teams.team as teams_team','teams.team_key as teams_team_key','result_matches.innings','teams.short_name as team_short_name')->get();

			return view('matches.team_points',compact('match_scores','match_points','findmatchdetails'));
		}


	public function updatescoreparticularinning($real_matchkey,$match_key){
		date_default_timezone_set('Asia/Kolkata'); 
		$m_status = [1=>'notstarted',2=>'completed',3=>'started',4=>'completed'];
		$findmatchtype =DB::connection('mysql2')->table('listmatches')->where('matchkey',$match_key)->first();
		$giveresresult = EntityCricketapiController::getmatchscore($real_matchkey);
		// dd($giveresresult);
		$giveresresult = $giveresresult['response'];
		$teamainnKey = array();
    	$teambinnKey =array();
		if(!empty($giveresresult)){
			// to update the score
			
		  	$checkpre =DB::connection('mysql2')->table('matchruns')->where('matchkey',$match_key)->first();
			if(empty($checkpre)){
                $matchdata['matchkey'] = $match_key;
                $matchdata['teams1'] = $giveresresult['teama']['short_name'];
                $matchdata['teams2'] = $giveresresult['teamb']['short_name'];
               	if(!empty($giveresresult['result'])){
                    $matchdata['winning_status'] = $giveresresult['result'];
               	}else{
                   	$matchdata['winning_status']=0;
               	}
               	if(isset($giveresresult['innings']) && !empty($giveresresult['innings'])){
               		$teamainnKey = array();
                	$teambinnKey =array();
                	if(count($giveresresult['innings'])>2){
                		foreach ($giveresresult['innings'] as $k => $value) {
                			if($value['batting_team_id']==$giveresresult['teama']['team_id']){
                				$teamainnKey[] = $giveresresult['innings'][$k];
                			}elseif ($value['batting_team_id']==$giveresresult['teamb']['team_id']) {
                				$teambinnKey[] = $giveresresult['innings'][$k];
                			}
                		}
                	}else{
                		$key1 = ''.(array_search($giveresresult['teamb']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
                		$key2 = ''.(array_search($giveresresult['teama']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
                		// $teamainnKey[] = $giveresresult['innings'][$key1];
                		// $teambinnKey[] = $giveresresult['innings'][$key2];
                		$teamainnKey[] = ($key1!='')?$giveresresult['innings'][$key1]:[];
                		$teambinnKey[] = ($key2!='')?$giveresresult['innings'][$key2]:[];
                	}
                    $gettestscore1 = 0;
                   	$gettestscore2 = 0;
                   	$gettestwicket1 = 0;
                   	$gettestwicket2 = 0;
                   	$gettestover1 = 0;
                   	$gettestover2 = 0;
                   	if(isset($teambinnKey[1]) && !empty($teambinnKey[1])){
               	     	// $gettestscore2 = $teambinnKey[1]['equations']['runs'];
                   	  //   $gettestscore1 = $teamainnKey[1]['equations']['runs'];
                   	  //   $gettestwicket1 = $teamainnKey[1]['equations']['wickets'];
                   	  //   $gettestwicket2 = $teambinnKey[1]['equations']['wickets'];
                   	  //   $gettestover1 = $teamainnKey[1]['equations']['overs'];
                   	  //   $gettestover2 = $teambinnKey[1]['equations']['overs'];
                   	    $gettestscore2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['runs']:0;
                   	    $gettestscore1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['runs']:0;
                   	    $gettestwicket1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['wickets']:0;
                   	    $gettestwicket2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['wickets']:0;
                   	    $gettestover1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['overs']:0;
                   	    $gettestover2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['overs']:0;
                   	}
                   	if(empty($gettestwicket1)){
                   	     $matchdata['wickets1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0;
                   	}else{
                   	    $matchdata['wickets1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0).','.$gettestwicket1;
                   	}
                   	if(empty($gettestwicket2)){
                   	     $matchdata['wickets2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0;
                   	}else{
                   	    $matchdata['wickets2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0).','.$gettestwicket2;
                   	}
                   	if(empty($gettestover1)){
                   	     $matchdata['overs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0;
                   	}else{
                   	    $matchdata['overs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0).','.$gettestover1;
                   	}
                   	if(empty($gettestover2)){
                   	     $matchdata['overs2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0;
                   	}else{
                   	    $matchdata['overs2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0).','.$gettestover2;
                   	}
                   	if(empty($gettestscore1)){
                   	     $matchdata['runs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0;
                   	}else{
                   	    $matchdata['runs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0).','.$gettestscore1;
                   	}
                   	if(empty($gettestscore2)){
                   	     $matchdata['runs2'] =(!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0;
                   	}else{
                   	    $matchdata['runs2'] =((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0).','.$gettestscore2;
                   	}
               	}else{
                   	$matchdata['wickets1'] = 0;
                   	$matchdata['wickets2'] = 0;
                   	$matchdata['overs1'] = 0;
                   	$matchdata['overs2'] = 0;
                   	$matchdata['runs1'] = 0;
                   	$matchdata['runs2'] = 0;
               	}
               	DB::connection('mysql')->table('matchruns')->insert($matchdata);
       		}else{
       			$matchdata1['matchkey'] = $match_key;
                $matchdata1['teams1'] = $giveresresult['teama']['short_name'];
                $matchdata1['teams2'] = $giveresresult['teamb']['short_name'];
               	if(!empty($giveresresult['result'])){
                    $matchdata1['winning_status'] = $giveresresult['result'];
               	}else{
                   	$matchdata1['winning_status']=0;
               	}
               	if(isset($giveresresult['innings']) && !empty($giveresresult['innings'])){
               		$teamainnKey = array();
                	$teambinnKey =array();
                	if(count($giveresresult['innings'])>2){
                		foreach ($giveresresult['innings'] as $k => $value) {
                			if($value['batting_team_id']==$giveresresult['teama']['team_id']){
                				$teamainnKey[] = $giveresresult['innings'][$k];
                			}elseif ($value['batting_team_id']==$giveresresult['teamb']['team_id']) {
                				$teambinnKey[] = $giveresresult['innings'][$k];
                			}
                		}
                	}else{
                		$key1 = ''.(array_search($giveresresult['teamb']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
                		$key2 = ''.(array_search($giveresresult['teama']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
                		// $teamainnKey[] = $giveresresult['innings'][$key1];
                		// $teambinnKey[] = $giveresresult['innings'][$key2];
                		$teamainnKey[] = ($key1!='')?$giveresresult['innings'][$key1]:[];
                		$teambinnKey[] = ($key2!='')?$giveresresult['innings'][$key2]:[];
                	}
                    $gettestscore1 = 0;
                   	$gettestscore2 = 0;
                   	$gettestwicket1 = 0;
                   	$gettestwicket2 = 0;
                   	$gettestover1 = 0;
                   	$gettestover2 = 0;
                   	if(isset($teambinnKey[1]) && !empty($teambinnKey[1])){
               	     	// $gettestscore2 = $teambinnKey[1]['equations']['runs'];
                   	  //   $gettestscore1 = $teamainnKey[1]['equations']['runs'];
                   	  //   $gettestwicket1 = $teamainnKey[1]['equations']['wickets'];
                   	  //   $gettestwicket2 = $teambinnKey[1]['equations']['wickets'];
                   	  //   $gettestover1 = $teamainnKey[1]['equations']['overs'];
                   	  //   $gettestover2 = $teambinnKey[1]['equations']['overs'];
                   	    $gettestscore2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['runs']:0;
                   	    $gettestscore1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['runs']:0;
                   	    $gettestwicket1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['wickets']:0;
                   	    $gettestwicket2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['wickets']:0;
                   	    $gettestover1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['overs']:0;
                   	    $gettestover2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['overs']:0;
                   	}
                   	if(empty($gettestwicket1)){
                   	     $matchdata1['wickets1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0;
                   	}else{
                   	    $matchdata1['wickets1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0).','.$gettestwicket1;
                   	}
                   	if(empty($gettestwicket2)){
                   	     $matchdata1['wickets2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0;
                   	}else{
                   	    $matchdata1['wickets2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0).','.$gettestwicket2;
                   	}
                   	if(empty($gettestover1)){
                   	     $matchdata1['overs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0;
                   	}else{
                   	    $matchdata1['overs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0).','.$gettestover1;
                   	}
                   	if(empty($gettestover2)){
                   	     $matchdata1['overs2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0;
                   	}else{
                   	    $matchdata1['overs2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0).','.$gettestover2;
                   	}
                   	if(empty($gettestscore1)){
                   	     $matchdata1['runs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0;
                   	}else{
                   	    $matchdata1['runs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0).','.$gettestscore1;
                   	}
                   	if(empty($gettestscore2)){
                   	     $matchdata1['runs2'] =(!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0;
                   	}else{
                   	    $matchdata1['runs2'] =((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0).','.$gettestscore2;
                   	}
               	}else{
                   	$matchdata1['wickets1'] = 0;
                   	$matchdata1['wickets2'] = 0;
                   	$matchdata1['overs1'] = 0;
                   	$matchdata1['overs2'] = 0;
                   	$matchdata1['runs1'] = 0;
                   	$matchdata1['runs2'] = 0;
               	}
               	// dd($matchdata1);
               	DB::connection('mysql')->table('matchruns')->where('matchkey',$match_key)->update($matchdata1);
           	}
			
			$mainarrayget = $giveresresult;
			$getmtdatastatus['status'] = $m_status[$mainarrayget['status']];
			if($getmtdatastatus['status']=='completed' && $findmatchtype->final_status=='pending'){
				$getmtdatastatus['final_status'] = 'IsReviewed';
			}
			DB::connection('mysql')->table('listmatches')->where('matchkey',$match_key)->update($getmtdatastatus);
			$secondinning = 0;
			if(isset($giveresresult['innings'])){
				$key1 = ''.(array_search($giveresresult['teama']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
				$key2 = ''.(array_search($giveresresult['teamb']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
				if(!empty($key1) || $key1=='1'){
					$secondinning = 1;
						$teamainnKey = [];
                		$teamainnKey[] = ($key1!='' && $key1=='1')?$giveresresult['innings'][$key1]:[];
				}else{
					if(!empty($key2) || $key2=='1'){
						$secondinning = 1;
						$teambinnKey = [];
                		$teambinnKey[] = ($key2!='' && $key2=='1')?$giveresresult['innings'][$key2]:[];
					}else{
						return 2;
					}
				}
			}else{	
				return 3;
			}
			
			$playin = DB::connection('mysql2')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('playingstatus',1)->pluck('players.players_key')->toArray();

			if(isset($mainarrayget['players'])){
				$players = array_column($mainarrayget['players'], 'pid');
				$giveres = $players;
				$matchplayers =DB::connection('mysql2')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('matchkey',$match_key)->select('matchplayers.*','players.players_key','players.role as playerrole')->get();
				
				$a =$matchplayers->toArray();
				// dd(count($a));
				if(!empty($a)){
					$innplayers = [];$t = '';$f=1;$j=1;
					foreach ($matchplayers as $kp => $player) {
						$pid = $player->playerid;
						// dump($pid);
						// $playr =DB::connection('mysql')->table('players')->where('id',$pid)->select('players_key')->first();
						$value = $player->players_key;
						$i = 1;
						$cu = 0;
						foreach ($teamainnKey as $ak => $teama) {
							// if($value==159 || $value==49706){
								$datasv=array();	$runs = 0;		$fours = 0;		$six = 0;		$duck=0;		$maiden_over=0;		$wicket = 0;
								$catch=0;			$runouts = 0;	$stumbed = 0;	$batdots = 0;	$balldots = 0;	$miletone_run = 0;	$bball = 0;
								$grun = 0;			$balls = 0;		$bballs = 0;	$extra = 0;		$overs = 0;
								
								$bat = (isset($teama['batsmen']))?''.(array_search($value, array_column($teama['batsmen'], 'batsman_id'))):'';
								if($bat!=''){						
									$innplayers[$value][$i]['batting'] = $teama['batsmen'][$bat];
								}else{
									if(!isset($innplayers[$value][$i]['batting'])){
										$innplayers[$value][$i]['batting'] = [];
									}
									$bowl = (isset($teama['bowlers']))?''.(array_search($value, array_column($teama['bowlers'], 'bowler_id'))):'';
									$field = (isset($teama['fielder']))?''.(array_search($value, array_column($teama['fielder'], 'fielder_id'))):'';
									$innplayers[$value][$i]['bowling'] = ($bowl!='')?$teama['bowlers'][$bowl]:[];
									$innplayers[$value][$i]['fielding'] = ($field!='')?$teama['fielder'][$field]:[];
								}
								// dump($innplayers[$value]);continue;
								$batb = (isset($teambinnKey[$ak]['batsmen']))?''.(array_search($value, array_column($teambinnKey[$ak]['batsmen'], 'batsman_id'))):'';
								if($batb!=''){	
									$innplayers[$value][$i]['batting'] = $teambinnKey[$ak]['batsmen'][$batb];
								}else{
									if(!isset($innplayers[$value][$i]['batting'])){
										$innplayers[$value][$i]['batting'] = [];
									}
									if(empty($innplayers[$value][$i]['bowling'])){
										$bowlb = (isset($teambinnKey[$ak]['bowlers']))?''.(array_search($value, array_column($teambinnKey[$ak]['bowlers'], 'bowler_id'))):'';
										$innplayers[$value][$i]['bowling'] = ($bowlb!='')?$teambinnKey[$ak]['bowlers'][$bowlb]:[];
									}
									if(empty($innplayers[$value][$i]['fielding'])){
										$fieldb = (isset($teambinnKey[$ak]['fielder']))?''.(array_search($value, array_column($teambinnKey[$ak]['fielder'], 'fielder_id'))):'';
										$innplayers[$value][$i]['fielding'] = ($fieldb!='')?$teambinnKey[$ak]['fielder'][$fieldb]:[];
									}
									
									// dd($innplayers[$value][$i]['bowling']);
								}

								$play = $innplayers[$value][$i];
								// dump($play);
							
								// dump($playin);
								if(!empty($play['batting']) || !empty($play['bowling']) || !empty($play['fielding'])){
									if(in_array($value,$playin)){
										$datasv['starting11']=1;
									}
									// $datasv['starting11']=1;
									if(!empty($play['batting'])){
										if(isset($play['batting']['strike_rate'])){
											$datasv['batting'] = 1;
											$datasv['strike_rate'] = $play['batting']['strike_rate'];
										}
										else{
											$datasv['batting'] = 0;
										}
										/* runs points */
										if(isset($play['batting']['runs'])){
											$datasv['runs'] = $runs = $runs +  $play['batting']['runs'];
										}else{
											$datasv['runs'] =0;
										}
										/* fours points */
										
										if(isset($play['batting']['fours'])){
											$datasv['fours'] = $fours = $fours + $play['batting']['fours'];
										}
										if(isset($play['batting']['balls_faced'])){
											$datasv['bball'] = $bball = $bball + $play['batting']['balls_faced'];
											}
										/* sixes Points */
										
										if(isset($play['batting']['sixes'])){
											$datasv['six'] = $six = $six + $play['batting']['sixes'];
										}
										if(!empty($play['batting']['dismissal'])){

											if($player->playerrole!='bowler'){
												if(($runs == 0) && ($play['batting']['dismissal'] != '')){
													$datasv['duck'] = $duck = 1;
												}else{
													$datasv['duck'] = $duck = 0;
												}
											}else{
												$datasv['duck'] = $duck = 0;
											}
											if($play['batting']['dismissal'] != ''){
												$datasv['out_str'] = $play['batting']['how_out'];
											}else{
												$datasv['out_str'] = 'not out';
											}
										}
										if(isset($batting['dots'])){
											$datasv['battingdots'] = $batdots = $batdots + $play['batting']['run0'];
										}
										if($play['batting']['dismissal']=='lbw' || $play['batting']['dismissal']=='bowled'){
										
											$wbowlerkey = $play['batting']['bowler_id'];
											// echo "<pre>";print_r($wbowlerkey);
											
											$bowlerplayersid =DB::connection('mysql2')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('players.players_key',$wbowlerkey)->where('matchkey',$match_key)->value('matchplayers.playerid');
											// $wplayerid = array_search($wbowlerkey, array_column($a, 'playerid'));
											if(!empty($bowlerplayersid)){
												$datasv['wplayerid']= $bowlerplayersid;
											}
										}
										$datasv['wicket_type']= $play['batting']['dismissal'];
									}
									// bowling points //
									if(!empty($play['bowling'])){

										$bowling = $play['bowling'];
										$datasv['bowling'] = 1;
										$datasv['economy_rate'] = $bowling['econ'];
										$datasv['maiden_over'] = $maiden_over = $maiden_over + $bowling['maidens'];
										$datasv['wicket'] = $wicket = $wicket + $bowling['wickets'];
										$datasv['overs'] = $overs = $overs + $bowling['overs'];
										$datasv['grun'] = $grun = $grun + $bowling['runs_conceded'];
										$datasv['balldots'] = $balldots = $balldots + (!empty($bowling['run0']))?$bowling['run0']:0;
										$datasv['balls'] = $balls = $balls + ($overs*6);
										$datasv['extra'] = $extra = $extra + ($bowling['noballs']+$bowling['wides']);
										
									}

									// fielding points //
									if(!empty($play['fielding'])){
										$fielding = $play['fielding'];
										$datasv['catch'] = $catch = $catch + $fielding['catches'];
										if($fielding['runout_direct_hit']==0){
											$datasv['hitter'] = $fielding['runout_catcher'];
											$datasv['thrower'] = $fielding['runout_thrower'];
										}else{
											$datasv['thrower'] = 1;
											$datasv['hitter'] = 1;
										}
										$datasv['stumbed'] = $stumbed = $stumbed + $fielding['stumping'];
									}
									$datasv['match_key'] =$match_key;
									$datasv['player_key'] =$value;
									$datasv['player_id'] =$pid;
									$datasv['innings'] =$i;
									$findplayerex = DB::connection('mysql2')->table('result_matches')->where('player_key',$value)->where('match_key',$match_key)->where('innings',$i)->select('id')->first();
									if(!empty($findplayerex)){
										DB::connection('mysql')->table('result_matches')->where('id',$findplayerex->id)->update($datasv);
									}else{
										DB::connection('mysql')->table('result_matches')->insert($datasv);
									}
								}else{
									if(in_array($value,$playin)){
										$datasvs['starting11']=1;
									}else{
										$datasvs['starting11']=0;
									}
									$datasvs['out_str'] = 'not out';
								    $datasvs['match_key'] =$match_key;
									$datasvs['player_key'] =$value;
									$datasvs['player_id'] =$pid;
									$datasvs['innings'] =$i;
									$findplayerex = DB::connection('mysql2')->table('result_matches')->where('player_key',$value)->where('match_key',$match_key)->where('innings',$i)->select('id')->first();
									if(!empty($findplayerex)){
										// dump('else');
										DB::connection('mysql')->table('result_matches')->where('id',$findplayerex->id)->update($datasvs);
									}else{
										// dump($datasvs);
										DB::connection('mysql')->table('result_matches')->insert($datasvs);
									}
								}

							// }
							
							
							$i++; 
						}
					

					}
					$showpoints = ResultController::player_point($match_key,$findmatchtype->format,$real_matchkey);
				}
			}
		}
		return 1;
	}
	
	public function getplayerpercentage($matchkey){
        $finduselectthisplayer = DB::table('jointeam')->where('matchkey', $matchkey)->get(['players','vicecaptain','captain']);
        if(!empty($finduselectthisplayer->toArray())){
            $teamscount = $finduselectthisplayer->count();
            $allselectedids = $finduselectthisplayer->pluck('players')->join(',');
            $allpids = collect(explode(',',$allselectedids))->countBy();
            
            $vcap = $finduselectthisplayer->pluck('vicecaptain')->countBy()->transform(function($item, $key) use ($teamscount){
                    return ['vcaptain'=>round((($item/$teamscount)*100),2)];
                });
            $cap = $finduselectthisplayer->pluck('captain')->countBy()->transform(function($item, $key) use ($teamscount){
                return ['captain'=>round((($item/$teamscount)*100),2)];
            });
            $vcap = $vcap->all();
            $cap = $cap->all();
            $data = [];
            $pids = $allpids->transform(function($item, $key) use ($teamscount,$vcap, $cap,$data){
                if(collect($vcap)->get($key)){
                    $data['vcaptainselected'] = $vcap[$key]['vcaptain'];
                }
                if(collect($cap)->get($key)){
                    $data['captainselected'] = $cap[$key]['captain'];
                }
                $data['selection_per'] = round((($item/$teamscount)*100),2);
                return $data;
            });
            $test = $pids->every(function($value, $key) use ($matchkey) {
                    DB::table('matchplayers')->where('matchkey',$matchkey)->where('playerid',$key)->update($value);
                    return $value;
                });
        }
        
    }
    public function gettransform(){
        $matches = DB::table('listmatches')->where('launch_status','launched')->where('status','notstarted')->where('final_status','pending')->get(['matchkey']);
        if(!empty($matches->toArray())){
            foreach($matches as $match){
                $this->getplayerpercentage($match->matchkey);
            }
        }
    }

	// public function updatescoreparticularinning($real_matchkey,$match_key){
	// 	date_default_timezone_set('Asia/Kolkata'); 
	// 	$m_status = [1=>'notstarted',2=>'completed',3=>'started',4=>'completed'];
	// 	$findmatchtype =DB::connection('mysql2')->table('listmatches')->where('matchkey',$match_key)->first();
	// 	$giveresresult = EntityCricketapiController::getmatchscore($real_matchkey);
	// 	// dd($giveresresult);
	// 	$giveresresult = $giveresresult['response'];
	// 	$teamainnKey = array();
    // 	$teambinnKey =array();
	// 	if(!empty($giveresresult)){
	// 		// to update the score
			
	// 	  	$checkpre =DB::connection('mysql2')->table('matchruns')->where('matchkey',$match_key)->first();
	// 		if(empty($checkpre)){
    //             $matchdata['matchkey'] = $match_key;
    //             $matchdata['teams1'] = $giveresresult['teama']['short_name'];
    //             $matchdata['teams2'] = $giveresresult['teamb']['short_name'];
    //            	if(!empty($giveresresult['result'])){
    //                 $matchdata['winning_status'] = $giveresresult['result'];
    //            	}else{
    //                	$matchdata['winning_status']=0;
    //            	}
    //            	if(isset($giveresresult['innings']) && !empty($giveresresult['innings'])){
    //            		$teamainnKey = array();
    //             	$teambinnKey =array();
    //             	if(count($giveresresult['innings'])>2){
    //             		foreach ($giveresresult['innings'] as $k => $value) {
    //             			if($value['batting_team_id']==$giveresresult['teama']['team_id']){
    //             				$teamainnKey[] = $giveresresult['innings'][$k];
    //             			}elseif ($value['batting_team_id']==$giveresresult['teamb']['team_id']) {
    //             				$teambinnKey[] = $giveresresult['innings'][$k];
    //             			}
    //             		}
    //             	}else{
    //             		$key1 = ''.(array_search($giveresresult['teamb']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
    //             		$key2 = ''.(array_search($giveresresult['teama']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
    //             		// $teamainnKey[] = $giveresresult['innings'][$key1];
    //             		// $teambinnKey[] = $giveresresult['innings'][$key2];
    //             		$teamainnKey[] = ($key1!='')?$giveresresult['innings'][$key1]:[];
    //             		$teambinnKey[] = ($key2!='')?$giveresresult['innings'][$key2]:[];
    //             	}
    //                 $gettestscore1 = 0;
    //                	$gettestscore2 = 0;
    //                	$gettestwicket1 = 0;
    //                	$gettestwicket2 = 0;
    //                	$gettestover1 = 0;
    //                	$gettestover2 = 0;
    //                	if(isset($teambinnKey[1]) && !empty($teambinnKey[1])){
    //            	     	// $gettestscore2 = $teambinnKey[1]['equations']['runs'];
    //                	  //   $gettestscore1 = $teamainnKey[1]['equations']['runs'];
    //                	  //   $gettestwicket1 = $teamainnKey[1]['equations']['wickets'];
    //                	  //   $gettestwicket2 = $teambinnKey[1]['equations']['wickets'];
    //                	  //   $gettestover1 = $teamainnKey[1]['equations']['overs'];
    //                	  //   $gettestover2 = $teambinnKey[1]['equations']['overs'];
    //                	    $gettestscore2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['runs']:0;
    //                	    $gettestscore1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['runs']:0;
    //                	    $gettestwicket1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['wickets']:0;
    //                	    $gettestwicket2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['wickets']:0;
    //                	    $gettestover1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['overs']:0;
    //                	    $gettestover2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['overs']:0;
    //                	}
    //                	if(empty($gettestwicket1)){
    //                	     $matchdata['wickets1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0;
    //                	}else{
    //                	    $matchdata['wickets1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0).','.$gettestwicket1;
    //                	}
    //                	if(empty($gettestwicket2)){
    //                	     $matchdata['wickets2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0;
    //                	}else{
    //                	    $matchdata['wickets2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0).','.$gettestwicket2;
    //                	}
    //                	if(empty($gettestover1)){
    //                	     $matchdata['overs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0;
    //                	}else{
    //                	    $matchdata['overs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0).','.$gettestover1;
    //                	}
    //                	if(empty($gettestover2)){
    //                	     $matchdata['overs2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0;
    //                	}else{
    //                	    $matchdata['overs2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0).','.$gettestover2;
    //                	}
    //                	if(empty($gettestscore1)){
    //                	     $matchdata['runs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0;
    //                	}else{
    //                	    $matchdata['runs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0).','.$gettestscore1;
    //                	}
    //                	if(empty($gettestscore2)){
    //                	     $matchdata['runs2'] =(!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0;
    //                	}else{
    //                	    $matchdata['runs2'] =((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0).','.$gettestscore2;
    //                	}
    //            	}else{
    //                	$matchdata['wickets1'] = 0;
    //                	$matchdata['wickets2'] = 0;
    //                	$matchdata['overs1'] = 0;
    //                	$matchdata['overs2'] = 0;
    //                	$matchdata['runs1'] = 0;
    //                	$matchdata['runs2'] = 0;
    //            	}
    //            	DB::connection('mysql')->table('matchruns')->insert($matchdata);
    //    		}else{
    //    			$matchdata1['matchkey'] = $match_key;
    //             $matchdata1['teams1'] = $giveresresult['teama']['short_name'];
    //             $matchdata1['teams2'] = $giveresresult['teamb']['short_name'];
    //            	if(!empty($giveresresult['result'])){
    //                 $matchdata1['winning_status'] = $giveresresult['result'];
    //            	}else{
    //                	$matchdata1['winning_status']=0;
    //            	}
    //            	if(isset($giveresresult['innings']) && !empty($giveresresult['innings'])){
    //            		$teamainnKey = array();
    //             	$teambinnKey =array();
    //             	if(count($giveresresult['innings'])>2){
    //             		foreach ($giveresresult['innings'] as $k => $value) {
    //             			if($value['batting_team_id']==$giveresresult['teama']['team_id']){
    //             				$teamainnKey[] = $giveresresult['innings'][$k];
    //             			}elseif ($value['batting_team_id']==$giveresresult['teamb']['team_id']) {
    //             				$teambinnKey[] = $giveresresult['innings'][$k];
    //             			}
    //             		}
    //             	}else{
    //             		$key1 = ''.(array_search($giveresresult['teamb']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
    //             		$key2 = ''.(array_search($giveresresult['teama']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
    //             		// $teamainnKey[] = $giveresresult['innings'][$key1];
    //             		// $teambinnKey[] = $giveresresult['innings'][$key2];
    //             		$teamainnKey[] = ($key1!='')?$giveresresult['innings'][$key1]:[];
    //             		$teambinnKey[] = ($key2!='')?$giveresresult['innings'][$key2]:[];
    //             	}
    //                 $gettestscore1 = 0;
    //                	$gettestscore2 = 0;
    //                	$gettestwicket1 = 0;
    //                	$gettestwicket2 = 0;
    //                	$gettestover1 = 0;
    //                	$gettestover2 = 0;
    //                	if(isset($teambinnKey[1]) && !empty($teambinnKey[1])){
    //            	     	// $gettestscore2 = $teambinnKey[1]['equations']['runs'];
    //                	  //   $gettestscore1 = $teamainnKey[1]['equations']['runs'];
    //                	  //   $gettestwicket1 = $teamainnKey[1]['equations']['wickets'];
    //                	  //   $gettestwicket2 = $teambinnKey[1]['equations']['wickets'];
    //                	  //   $gettestover1 = $teamainnKey[1]['equations']['overs'];
    //                	  //   $gettestover2 = $teambinnKey[1]['equations']['overs'];
    //                	    $gettestscore2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['runs']:0;
    //                	    $gettestscore1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['runs']:0;
    //                	    $gettestwicket1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['wickets']:0;
    //                	    $gettestwicket2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['wickets']:0;
    //                	    $gettestover1 = (isset($teamainnKey[1])) ? $teamainnKey[1]['equations']['overs']:0;
    //                	    $gettestover2 = (isset($teambinnKey[1])) ? $teambinnKey[1]['equations']['overs']:0;
    //                	}
    //                	if(empty($gettestwicket1)){
    //                	     $matchdata1['wickets1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0;
    //                	}else{
    //                	    $matchdata1['wickets1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['wickets']:0).','.$gettestwicket1;
    //                	}
    //                	if(empty($gettestwicket2)){
    //                	     $matchdata1['wickets2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0;
    //                	}else{
    //                	    $matchdata1['wickets2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['wickets']:0).','.$gettestwicket2;
    //                	}
    //                	if(empty($gettestover1)){
    //                	     $matchdata1['overs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0;
    //                	}else{
    //                	    $matchdata1['overs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['overs']:0).','.$gettestover1;
    //                	}
    //                	if(empty($gettestover2)){
    //                	     $matchdata1['overs2'] = (!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0;
    //                	}else{
    //                	    $matchdata1['overs2'] = ((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['overs']:0).','.$gettestover2;
    //                	}
    //                	if(empty($gettestscore1)){
    //                	     $matchdata1['runs1'] = (!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0;
    //                	}else{
    //                	    $matchdata1['runs1'] = ((!empty($teamainnKey[0]))?$teamainnKey[0]['equations']['runs']:0).','.$gettestscore1;
    //                	}
    //                	if(empty($gettestscore2)){
    //                	     $matchdata1['runs2'] =(!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0;
    //                	}else{
    //                	    $matchdata1['runs2'] =((!empty($teambinnKey[0]))?$teambinnKey[0]['equations']['runs']:0).','.$gettestscore2;
    //                	}
    //            	}else{
    //                	$matchdata1['wickets1'] = 0;
    //                	$matchdata1['wickets2'] = 0;
    //                	$matchdata1['overs1'] = 0;
    //                	$matchdata1['overs2'] = 0;
    //                	$matchdata1['runs1'] = 0;
    //                	$matchdata1['runs2'] = 0;
    //            	}
    //            	// dd($matchdata1);
    //            	DB::connection('mysql')->table('matchruns')->where('matchkey',$match_key)->update($matchdata1);
    //        	}
			
	// 		$mainarrayget = $giveresresult;
	// 		// dd($mainarrayget);
	// 		$getmtdatastatus['status'] = $m_status[$mainarrayget['status']];
	// 		if($getmtdatastatus['status']=='completed' && $findmatchtype->final_status=='pending'){
	// 			$getmtdatastatus['final_status'] = 'IsReviewed';
	// 		}
	// 		// dd($getmtdatastatus);
	// 		DB::connection('mysql')->table('listmatches')->where('matchkey',$match_key)->update($getmtdatastatus);
	// 		$secondinning = 0;
	// 		if(isset($giveresresult['innings'])){
	// 			$key1 = ''.(array_search($giveresresult['teama']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
	// 			$key2 = ''.(array_search($giveresresult['teamb']['team_id'], array_column($giveresresult['innings'], 'batting_team_id')));
	// 			if(!empty($key1) || $key1=='1'){
	// 				$secondinning = 1;
	// 					$teamainnKey = [];
    //             		$teamainnKey[] = ($key1!='' && $key1=='1')?$giveresresult['innings'][$key1]:[];
	// 			}else{
	// 				if(!empty($key2) || $key2=='1'){
	// 					$secondinning = 1;
	// 					$teambinnKey = [];
    //             		$teambinnKey[] = ($key2!='' && $key2=='1')?$giveresresult['innings'][$key2]:[];
	// 				}else{
	// 					return 2;
	// 				}
	// 			}
	// 		}else{	
	// 			return 3;
	// 		}
			
	// 		$playin = DB::connection('mysql2')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('playingstatus',1)->pluck('players.players_key')->toArray();

	// 		if(isset($mainarrayget['players'])){
	// 			$players = array_column($mainarrayget['players'], 'pid');
	// 			$giveres = $players;
	// 			$matchplayers =DB::connection('mysql2')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('matchkey',$match_key)->select('matchplayers.*','players.players_key','players.role as playerrole')->get();
				
	// 			$a =$matchplayers->toArray();
	// 			// dd(count($a));
	// 			if(!empty($a)){
	// 				$innplayers = [];$t = '';$f=1;$j=1;
	// 				foreach ($matchplayers as $kp => $player) {
	// 					$pid = $player->playerid;
	// 					// dump($pid);
	// 					// $playr =DB::connection('mysql')->table('players')->where('id',$pid)->select('players_key')->first();
	// 					$value = $player->players_key;
	// 					$i = 1;
	// 					$cu = 0;
	// 					foreach ($teamainnKey as $ak => $teama) {
	// 						// if($value==159 || $value==49706){
	// 							$datasv=array();	$runs = 0;		$fours = 0;		$six = 0;		$duck=0;		$maiden_over=0;		$wicket = 0;
	// 							$catch=0;			$runouts = 0;	$stumbed = 0;	$batdots = 0;	$balldots = 0;	$miletone_run = 0;	$bball = 0;
	// 							$grun = 0;			$balls = 0;		$bballs = 0;	$extra = 0;		$overs = 0;
								
	// 							$bat = (isset($teama['batsmen']))?''.(array_search($value, array_column($teama['batsmen'], 'batsman_id'))):'';
	// 							if($bat!=''){						
	// 								$innplayers[$value][$i]['batting'] = $teama['batsmen'][$bat];
	// 							}else{
	// 								if(!isset($innplayers[$value][$i]['batting'])){
	// 									$innplayers[$value][$i]['batting'] = [];
	// 								}
	// 								$bowl = (isset($teama['bowlers']))?''.(array_search($value, array_column($teama['bowlers'], 'bowler_id'))):'';
	// 								$field = (isset($teama['fielder']))?''.(array_search($value, array_column($teama['fielder'], 'fielder_id'))):'';
	// 								$innplayers[$value][$i]['bowling'] = ($bowl!='')?$teama['bowlers'][$bowl]:[];
	// 								$innplayers[$value][$i]['fielding'] = ($field!='')?$teama['fielder'][$field]:[];
	// 							}
	// 							// dump($innplayers[$value]);continue;
	// 							$batb = (isset($teambinnKey[$ak]['batsmen']))?''.(array_search($value, array_column($teambinnKey[$ak]['batsmen'], 'batsman_id'))):'';
	// 							if($batb!=''){	
	// 								$innplayers[$value][$i]['batting'] = $teambinnKey[$ak]['batsmen'][$batb];
	// 							}else{
	// 								if(!isset($innplayers[$value][$i]['batting'])){
	// 									$innplayers[$value][$i]['batting'] = [];
	// 								}
	// 								if(empty($innplayers[$value][$i]['bowling'])){
	// 									$bowlb = (isset($teambinnKey[$ak]['bowlers']))?''.(array_search($value, array_column($teambinnKey[$ak]['bowlers'], 'bowler_id'))):'';
	// 									$innplayers[$value][$i]['bowling'] = ($bowlb!='')?$teambinnKey[$ak]['bowlers'][$bowlb]:[];
	// 								}
	// 								if(empty($innplayers[$value][$i]['fielding'])){
	// 									$fieldb = (isset($teambinnKey[$ak]['fielder']))?''.(array_search($value, array_column($teambinnKey[$ak]['fielder'], 'fielder_id'))):'';
	// 									$innplayers[$value][$i]['fielding'] = ($fieldb!='')?$teambinnKey[$ak]['fielder'][$fieldb]:[];
	// 								}
									
	// 								// dd($innplayers[$value][$i]['bowling']);
	// 							}

	// 							$play = $innplayers[$value][$i];
	// 							// dump($play);
							
	// 							// dump($playin);
	// 							if(!empty($play['batting']) || !empty($play['bowling']) || !empty($play['fielding'])){
	// 								if(in_array($value,$playin)){
	// 									$datasv['starting11']=1;
	// 								}
	// 								// $datasv['starting11']=1;
	// 								if(!empty($play['batting'])){
	// 									if(isset($play['batting']['strike_rate'])){
	// 										$datasv['batting'] = 1;
	// 										$datasv['strike_rate'] = $play['batting']['strike_rate'];
	// 									}
	// 									else{
	// 										$datasv['batting'] = 0;
	// 									}
	// 									/* runs points */
	// 									if(isset($play['batting']['runs'])){
	// 										$datasv['runs'] = $runs = $runs +  $play['batting']['runs'];
	// 									}else{
	// 										$datasv['runs'] =0;
	// 									}
	// 									/* fours points */
										
	// 									if(isset($play['batting']['fours'])){
	// 										$datasv['fours'] = $fours = $fours + $play['batting']['fours'];
	// 									}
	// 									if(isset($play['batting']['balls_faced'])){
	// 										$datasv['bball'] = $bball = $bball + $play['batting']['balls_faced'];
	// 										}
	// 									/* sixes Points */
										
	// 									if(isset($play['batting']['sixes'])){
	// 										$datasv['six'] = $six = $six + $play['batting']['sixes'];
	// 									}
	// 									if(!empty($play['batting']['dismissal'])){

	// 										if($player->playerrole!='bowler'){
	// 											if(($runs == 0) && ($play['batting']['dismissal'] != '')){
	// 												$datasv['duck'] = $duck = 1;
	// 											}else{
	// 												$datasv['duck'] = $duck = 0;
	// 											}
	// 										}else{
	// 											$datasv['duck'] = $duck = 0;
	// 										}
	// 										if($play['batting']['dismissal'] != ''){
	// 											$datasv['out_str'] = $play['batting']['how_out'];
	// 										}else{
	// 											$datasv['out_str'] = 'not out';
	// 										}
	// 									}
	// 									if(isset($batting['dots'])){
	// 										$datasv['battingdots'] = $batdots = $batdots + $play['batting']['run0'];
	// 									}
	// 									if($play['batting']['dismissal']=='lbw' || $play['batting']['dismissal']=='bowled'){
										
	// 										$wbowlerkey = $play['batting']['bowler_id'];
	// 										// echo "<pre>";print_r($wbowlerkey);
											
	// 										$bowlerplayersid =DB::connection('mysql2')->table('matchplayers')->join('players','players.id','=','matchplayers.playerid')->where('players.players_key',$wbowlerkey)->where('matchkey',$match_key)->value('matchplayers.playerid');
	// 										// $wplayerid = array_search($wbowlerkey, array_column($a, 'playerid'));
	// 										if(!empty($bowlerplayersid)){
	// 											$datasv['wplayerid']= $bowlerplayersid;
	// 										}
	// 									}
	// 									$datasv['wicket_type']= $play['batting']['dismissal'];
	// 								}
	// 								// bowling points //
	// 								if(!empty($play['bowling'])){

	// 									$bowling = $play['bowling'];
	// 									$datasv['bowling'] = 1;
	// 									$datasv['economy_rate'] = $bowling['econ'];
	// 									$datasv['maiden_over'] = $maiden_over = $maiden_over + $bowling['maidens'];
	// 									$datasv['wicket'] = $wicket = $wicket + $bowling['wickets'];
	// 									$datasv['overs'] = $overs = $overs + $bowling['overs'];
	// 									$datasv['grun'] = $grun = $grun + $bowling['runs_conceded'];
	// 									$datasv['balldots'] = $balldots = $balldots + (!empty($bowling['run0']))?$bowling['run0']:0;
	// 									$datasv['balls'] = $balls = $balls + ($overs*6);
	// 									$datasv['extra'] = $extra = $extra + ($bowling['noballs']+$bowling['wides']);
										
	// 								}

	// 								// fielding points //
	// 								if(!empty($play['fielding'])){
	// 									$fielding = $play['fielding'];
	// 									$datasv['catch'] = $catch = $catch + $fielding['catches'];
	// 									if($fielding['runout_direct_hit']==0){
	// 										$datasv['hitter'] = $fielding['runout_catcher'];
	// 										$datasv['thrower'] = $fielding['runout_thrower'];
	// 									}else{
	// 										$datasv['thrower'] = 1;
	// 										$datasv['hitter'] = 1;
	// 									}
	// 									$datasv['stumbed'] = $stumbed = $stumbed + $fielding['stumping'];
	// 								}
	// 								$datasv['match_key'] =$match_key;
	// 								$datasv['player_key'] =$value;
	// 								$datasv['player_id'] =$pid;
	// 								$datasv['innings'] =$i;
	// 								$findplayerex = DB::connection('mysql2')->table('result_matches')->where('player_key',$value)->where('match_key',$match_key)->where('innings',$i)->select('id')->first();
	// 								if(!empty($findplayerex)){
	// 									DB::connection('mysql')->table('result_matches')->where('id',$findplayerex->id)->update($datasv);
	// 								}else{
	// 									DB::connection('mysql')->table('result_matches')->insert($datasv);
	// 								}
	// 							}else{
	// 								if(in_array($value,$playin)){
	// 									$datasvs['starting11']=1;
	// 								}else{
	// 									$datasvs['starting11']=0;
	// 								}
	// 								$datasvs['out_str'] = 'not out';
	// 							    $datasvs['match_key'] =$match_key;
	// 								$datasvs['player_key'] =$value;
	// 								$datasvs['player_id'] =$pid;
	// 								$datasvs['innings'] =$i;
	// 								$findplayerex = DB::connection('mysql2')->table('result_matches')->where('player_key',$value)->where('match_key',$match_key)->where('innings',$i)->select('id')->first();
	// 								if(!empty($findplayerex)){
	// 									// dump('else');
	// 									DB::connection('mysql')->table('result_matches')->where('id',$findplayerex->id)->update($datasvs);
	// 								}else{
	// 									// dump($datasvs);
	// 									DB::connection('mysql')->table('result_matches')->insert($datasvs);
	// 								}
	// 							}

	// 						// }
							
							
	// 						$i++; 
	// 					}
					

	// 				}
	// 			}
	// 		}
	// 	}
	// 	return 1;
	// }

	



}