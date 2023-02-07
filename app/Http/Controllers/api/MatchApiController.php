<?php
namespace App\Http\Controllers\api;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CricketapiController;
use App\Http\Controllers\EntityCricketapiController;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;

class MatchApiController extends Controller
{

    /************* API RELATED TO MATCH ************/

    /*api to get all the match details */
    /**
     * @return json
     * @Url: /api/getmatchdetails/
     * @Method: GET
     * @Parameters
     *
     *         matchkey : "text"
     *       auth_key: "text" in header(Authorization)
     *
     *
     */
    public function getmatchdetails(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $users = Helpers::isAuthorize($request);
        $geturl = Helpers::geturl();
        $input = $request->all();
        if ($request->get('auth_key')) {
            unset($input['auth_key']);
        }
        $matchkey = $request->get('matchkey');
        $findmatches = DB::connection('mysql')->table('listmatches')
            ->join('series', 'series.id', 'listmatches.series')
            ->join('teams as t1', 't1.id', '=', 'listmatches.team1')
            ->join('teams as t2', 't2.id', '=', 'listmatches.team2')
            ->where('listmatches.matchkey', $matchkey)
            ->orderBy('listmatches.start_date', 'desc')
            ->select('series.name as series_name', 'listmatches.*', 't1.short_name as teamdata1_short_name', 't2.short_name as teamdata2_short_name', 't1.logo as teamdata1_logo', 't2.logo as teamdata2_logo')
            ->get();

        $Json = array();
        if (!empty($findmatches)) {
            $i = 0;
            foreach ($findmatches as $match) {
                $countquery = DB::connection('mysql')->table('listmatches');
                if ($matchkey != "") {
                    $countquery = $countquery->where('matchkey', $match->matchkey);
                }

                $i = 0;
                $count = 0;
                $countallchallenges = $countquery->where('status', 'opened')->orderBY('win_amount', 'DESC')->count();
                $Json[$i]['totalcontest'] = $countallchallenges;
                $Json[$i]['id'] = $match->id;
                $Json[$i]['name'] = $match->name;
                $Json[$i]['format'] = $match->format;
                $Json[$i]['seriesname'] = $match->series_name;
                $Json[$i]['team1name'] = $match->teamdata1_short_name;
                $Json[$i]['team2name'] = $match->teamdata2_short_name;
                $Json[$i]['matchkey'] = $match->matchkey;
                if ($match->status != 'notstarted') {
                    if ($match->status == 'started') {
                        $Json[$i]['matchstatus'] = $match->status;
                    } else {
                        $Json[$i]['matchstatus'] = $match->final_status;
                    }
                } else {
                    $Json[$i]['matchstatus'] = $match->status;
                    $mlocktime = date('Y-m-d H:i:s', strtotime($match->start_date));
                    if (date('Y-m-d H:i:s') >= $mlocktime) {
                        $Json[$i]['matchstatus'] = 'started';
                    } else {
                        $Json[$i]['matchstatus'] = 'notstarted';
                    }
                }

                if ($match->teamdata1_logo != "") {
                    // $Json[$i]['team1logo'] = $geturl . 'public/' . $match->teamdata1_logo;
                    $c = count(explode(':', $match->teamdata1_logo));;
                    if ($c >= 2) {
                        $Json[$i]['team1logo'] = $match->teamdata1_logo;
                    } else {
                        $Json[$i]['team1logo'] = $geturl . '/public/' . $match->teamdata1_logo;
                    }
                } else {
                    $Json[$i]['team1logo'] = $geturl . 'public/logo.png';
                }
                if ($match->teamdata2_logo != "") {
                    // $Json[$i]['team2logo'] = $geturl . 'public/' . $match->teamdata2_logo;
                    $c = count(explode(':', $match->teamdata2_logo));;
                    if ($c >= 2) {
                        $Json[$i]['team2logo'] = $match->teamdata2_logo;
                    } else {
                        $Json[$i]['team2logo'] = $geturl . '/public/' . $match->teamdata2_logo;
                    }
                } else {
                    $Json[$i]['team2logo'] = $geturl . 'public/logo.png';
                }
                $Json[$i]['time_start'] = date('Y-m-d H:i:s', strtotime($match->start_date));
                $i++;
            }
        } else {
            $Json[0]['status'] = false;
        }
        echo json_encode($Json);
        die;

    }
    // to get the match list//
    /*api to get all the match details */
    /**
     * @return json
     * @Url: /api/getmatchlist/
     * @Method: GET
     * @Parameters
     *
     *       auth_key: "text" in header(Authorization)
     *
     *
     */
    public function getmatchlist(Request $request)
    {
        Helpers::setHeader(200);
        $users = Helpers::isAuthorize($request);
        Helpers::timezone();
        $geturl = Helpers::geturl();
        $locktime = Carbon::now();
        $input = $request->all();
        $userid = $users->id;
        $currenttimme = date('Y-m-d H:i:s');
        $lastdays = date('Y-m-d H:i:s', strtotime('+15days', strtotime($currenttimme)));

        $query = DB::connection('mysql')->table('listmatches');
        if (isset($_GET['series'])) {
            $series = $_GET['series'];
            $query = $query->where('series', $series);
        }
        $findmatches = $query->join('series', 'listmatches.series', '=', 'series.id')
            ->join('teams as team1', 'team1.id', '=', 'listmatches.team1')
            ->join('teams as team2', 'team2.id', '=', 'listmatches.team2')
            ->whereBetween('listmatches.start_date', [$currenttimme, $lastdays])
            ->where('listmatches.final_status', '!=', 'IsCanceled')
            ->where('listmatches.final_status', '!=', 'IsAbandoned')
            ->where('series.status', 'opened')
            ->select('listmatches.id as listmatchid', 'team1.short_name as teamname1', 'team2.short_name as teamname2', 'team1.team as team1fullname', 'team2.team as team2fullname', 'team1.color as team1color', 'team2.color as team2color', 'team1.logo as team1logo', 'team2.logo as team2logo', 'listmatches.series as seriesid', 'series.name as seriesname', 'listmatches.name', 'listmatches.start_date', 'listmatches.format', 'listmatches.matchkey', 'listmatches.final_status', 'listmatches.launch_status', 'listmatches.playing11_status', 'listmatches.fantasy_type')->orderBY('listmatches.start_date', 'ASC')
            ->get();

        $Json = array();
        if (!empty($findmatches)) {
            $i = 0;
            $matchshow = 0;
            foreach ($findmatches as $match) {
                $Json[$i]['id'] = $match->listmatchid;
                $Json[$i]['name'] = $match->name;
                $Json[$i]['format'] = $match->format;
                $Json[$i]['series'] = $match->seriesid;
                $Json[$i]['seriesname'] = $match->seriesname;
                $Json[$i]['team1name'] = strtoupper($match->teamname1);
                $Json[$i]['team2name'] = strtoupper($match->teamname2);
                $Json[$i]['team1fullname'] = strtoupper($match->team1fullname);
                $Json[$i]['team2fullname'] = strtoupper($match->team2fullname);
                $Json[$i]['matchkey'] = $match->matchkey;
                $Json[$i]['type'] = $match->fantasy_type;
                $Json[$i]['winnerstatus'] = $match->final_status;
                $Json[$i]['playing11_status'] = $match->playing11_status;
                if (!empty($match->team1color)) {
                    $Json[$i]['team1color'] = $match->team1color;
                } else {
                    $Json[$i]['team1color'] = '#ffffff';
                }
                if (!empty($match->team2color)) {
                    $Json[$i]['team2color'] = $match->team2color;
                } else {
                    $Json[$i]['team2color'] = '#ffffff';
                }
                if ($match->team1logo != "") {
                    // $Json[$i]['team1logo'] = $geturl . 'public/' . $match->team1logo;
                    $c = count(explode(':', $match->team1logo));;
                    if ($c >= 2) {
                        $Json[$i]['team1logo'] = $match->team1logo;
                    } else {
                        $Json[$i]['team1logo'] = $geturl . '/public/' . $match->team1logo;
                    }
                } else {
                    $Json[$i]['team1logo'] = $geturl . 'public/logo.png';
                }
                if ($match->team2logo != "") {
                    // $Json[$i]['team2logo'] = $geturl . 'public/' . $match->team2logo;
                    $c = count(explode(':', $match->team2logo));;
                    if ($c >= 2) {
                        $Json[$i]['team2logo'] = $match->team2logo;
                    } else {
                        $Json[$i]['team2logo'] = $geturl . '/public/' . $match->team2logo;
                    }
                } else {
                    $Json[$i]['team2logo'] = $geturl . 'public/logo.png';
                }
                $locktime = date('Y-m-d H:i:s', strtotime($match->start_date));
                if (date('Y-m-d H:i:s') >= $locktime) {
                    $Json[$i]['matchopenstatus'] = 'closed';
                } else {
                    $matchshow++;
                    $Json[$i]['matchopenstatus'] = 'opened';
                }
                $Json[$i]['time_start'] = date('Y-m-d H:i:s', strtotime($match->start_date));
                $Json[$i]['launch_status'] = $match->launch_status;
                $Json[$i]['locktime'] = $locktime;
                if (isset($_GET['userid'])) {
                    $finduserinfo = DB::connection('mysql')->table('registerusers')->where('id', $id)->select('id')->first();
                    if (!empty($finduserinfo)) {
                        $getid = $finduserinfo->id;
                        $findjointeam = DB::connection('mysql')->table('jointeam')->where('userid', $getid)->where('matchkey', $match->matchkey)->orderBY('id', 'DESC')->get();
                        if (!empty($findjointeam)) {
                            $Json[$i]['createteamnumber'] = $findjointeam[0]->teamnumber + 1;
                        } else {
                            $Json[$i]['createteamnumber'] = 1;
                        }
                    }
                }

                $totalTeams = DB::connection('mysql')->table('jointeam')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->count();

                $Json[$i]['totalTeams'] = $totalTeams;

                $totalcontest = DB::connection('mysql')->table('joinedleauges')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->groupBy('challengeid')
                    ->count();

                $Json[$i]['totalcontest'] = $totalcontest;

                $Json[$i]['status'] = true;
                $i++;
            }
        } else {
            $Json[0]['status'] = false;
        }
        return response()->json($Json);
        die;
//         }
    }

    // public function allmatchplayers(Request $request){
    //     $data=$request->all();
    //     $geturl=Helpers::geturl();
    //     $users=Helpers::isAuthorize($request);
    //     if(!empty($users))
    //     {
    //    $matchkey = $request->get('matchkey');
    //    $players = DB::connection('mysql')->table('matchplayers')->where('matchkey',$matchkey)->orderBy('points','DESC')->select('playerid','points')->get();
    //    if(!empty($players)){
    //        $Json = [];
    //        $i=0;
    //        foreach($players as $player){
    //   $resultpoints = DB::connection('mysql')->table('result_points')
    //                    ->where('result_points.matchkey',$matchkey)
    //                    ->where('result_points.playerid',$player->playerid)
    //                    ->join('matchplayers','matchplayers.playerid','=','result_points.playerid')->join('players','players.id','=','matchplayers.playerid')
    //                    ->select('result_points.*','players.image as playersimage','matchplayers.credit','matchplayers.name','matchplayers.points','result_points.startingpoints as startingpoints')
    
    //                    ->first();

    //            if(!empty($resultpoints)){
    //                if($resultpoints->startingpoints){
    //                $Json[$i]['player_name'] = $resultpoints->name;
    //                if(!empty($resultpoints->playersimage)){
    //                     $Json[$i]['playerimage'] = asset('public/'.$resultpoints->playersimage);
    //                 }else{
    //                     $Json[$i]['playerimage'] = asset('public/'.Helpers::settings()->player_image ?? '');
    //                 }
    //                $Json[$i]['credit'] = $resultpoints->credit;
    //                $Json[$i]['playerid'] = $resultpoints->playerid;
    //                $Json[$i]['startingpoints'] = $resultpoints->startingpoints;
    //                $Json[$i]['runs'] = $resultpoints->runs;
    //                $Json[$i]['fours'] = $resultpoints->fours;
    //                $Json[$i]['sixs'] = $resultpoints->sixs;
    //                $Json[$i]['strike_rate'] = $resultpoints->strike_rate;
    //                $Json[$i]['century'] = $resultpoints->century+$resultpoints->halfcentury;
    //                $Json[$i]['halfcentury'] = $resultpoints->halfcentury;
    //                $Json[$i]['wickets'] = $resultpoints->wickets;
    //                $Json[$i]['maidens'] = $resultpoints->maidens;
    //                $Json[$i]['economy_rate'] = $resultpoints->economy_rate;
    //                $Json[$i]['runouts'] = $resultpoints->runouts;
    //                $Json[$i]['catch_points'] = $resultpoints->catch;
    //                $Json[$i]['stumping'] = $resultpoints->stumping;
    //                $Json[$i]['thrower'] = $resultpoints->thrower;
    //                $Json[$i]['hitter'] = $resultpoints->hitter;
    //                 $Json[$i]['bonus'] = $resultpoints->bonus;
    //                $Json[$i]['negative'] = $resultpoints->negative;
    //                $Json[$i]['total'] = $resultpoints->total;

    //               $finduselectthisplayer = DB::connection('mysql')->table('jointeam')->where('matchkey',$matchkey)->select('players')->get();
    //                 $countlenght = count($finduselectthisplayer);
    //                 $allplayers=array();$countplayer=0;
    //                 if(!empty($finduselectthisplayer)){
    //                     foreach($finduselectthisplayer as $pp){
    //                         $fplayers = array();
    //                         $fplayers = explode(',',$pp->players);
    //                         foreach($fplayers as $fpl){
    //                         $allplayers[] = $fpl;
    //                         }
    //                     }
    //                 }
    //                 if(!empty($allplayers)){
    //                     foreach($allplayers as $pl){
    //                         if($pl==$player->playerid){
    //                         $countplayer++;
    //                         }
    //                     }
    //                 }
    //                 if($countplayer>0){
    //                     $countper = round(($countplayer/$countlenght)*100,2);
    //                 }else{
    //                     $countper = 0;
    //                 }
    //                 $Json[$i]['selectper'] = $countper.'%';
    //                 $i++;
    //            }
    //            }

    //        }
    //        return response()->json($Json);
    //    }

    //     }
    // }
    // public function allmatchplayers(Request $request)
    // {
    //     $data = $request->all();
    //     $geturl = Helpers::geturl();
    //     $users = Helpers::isAuthorize($request);
    //     if (!empty($users)) {
    //         $matchkey = $request->get('matchkey');
    //         $total_teams = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->count();
    //         $players = DB::connection('mysql')->table('matchplayers')->where('matchkey', $matchkey)->orderBy('points', 'DESC')->select('playerid', 'points')->get();
    //         if (!empty($players)) {
    //             $Json = [];
    //             $i = 0;
    //             foreach ($players as $player) {
    //                 // $resultpoints = DB::connection('mysql')->table('result_points')
    //                 //            ->where('result_points.matchkey',$matchkey)
    //                 //            ->where('result_points.playerid',$player->playerid)
    //                 //            ->join('matchplayers','matchplayers.playerid','=','result_points.playerid')->join('players','players.id','=','matchplayers.playerid')
    //                 //            ->select('result_points.*','players.image as playersimage','matchplayers.credit','matchplayers.name','matchplayers.points','result_points.startingpoints as startingpoints')
    //                 //            ->first();
    //                 $resultp = DB::connection('mysql')->table('result_points')
    //                     ->where('result_points.matchkey', $matchkey)
    //                     ->where('result_points.playerid', $player->playerid)
    //                     ->join('matchplayers', 'matchplayers.playerid', '=', 'result_points.playerid')->join('players', 'players.id', '=', 'matchplayers.playerid')
    //                     ->select('players.image as playersimage', 'matchplayers.credit', 'matchplayers.name', 'matchplayers.points', 'result_points.startingpoints as startingpoints')
    //                     ->first();
    //                 $resultpoints = DB::connection('mysql2')
    //                     ->table('result_points')
    //                     ->leftjoin('result_matches', 'result_matches.id', '=', 'result_points.resultmatch_id')
    //                     ->leftjoin('listmatches', 'listmatches.matchkey', '=', 'result_points.matchkey')
    //                     ->where('result_points.playerid', $player->playerid)
    //                     ->select(DB::raw('sum(result_points.fours) as totalfours'),
    //                         DB::raw('sum(result_points.sixs) as totalsixs'),
    //                         DB::raw('sum(result_points.runs) as totalruns'),
    //                         DB::raw('sum(result_points.strike_rate) as totalstrike_rate'),
    //                         DB::raw('sum(result_points.century) as totalcentury'),
    //                         DB::raw('sum(result_points.halfcentury) as totalhalfcentury'),
    //                         DB::raw('sum(result_points.wickets) as totalwickets'),
    //                         DB::raw('sum(result_points.maidens) as totalmaidens'),
    //                         DB::raw('sum(result_points.economy_rate) as totaleconomy_rate'),
    //                         DB::raw('sum(result_points.runouts) as totalrunouts'),
    //                         DB::raw('sum(result_points.catch) as totalcatch'),
    //                         DB::raw('sum(result_points.stumping) as totalstumping'),
    //                         DB::raw('sum(result_points.thrower) as totalthrower'),
    //                         DB::raw('sum(result_points.hitter) as totalhitter'),
    //                         DB::raw('sum(result_points.bonus) as totalbonus'),
    //                         DB::raw('sum(result_points.negative) as totalnegative'),
    //                         DB::raw('sum(result_points.total) as totaltotal'),
    //                         'result_points.matchkey', 'result_matches.starting11', 'result_points.startingpoints',
    //                         'listmatches.name', 'result_matches.innings', 'result_matches.batting', 'result_matches.bowling',
    //                         'result_matches.duck', 'result_points.playerid')
    //                     ->where('result_points.matchkey', $matchkey)
    //                     ->orderBy('result_matches.innings', 'ASC')
    //                     ->orderBy('result_points.total', 'ASC')
    //                     ->first();
    //                 // dump($player->playerid);
    //                 // dump($resultpoints);continue;
    //                 if (!empty($resultpoints)) {
    //                     if (empty($resultp)) {continue;}
    //                     if ($resultpoints->startingpoints) {
    //                         // try{
    //                         $Json[$i]['player_name'] = $resultp->name;
    //                         // }catch(\Exception $e){
    //                         //     dd($player->playerid);
    //                         // }

    //                         if (!empty($resultpoints->playersimage)) {
    //                             $Json[$i]['playerimage'] = asset('public/' . $resultp->playersimage);
    //                         } else {
    //                             $Json[$i]['playerimage'] = asset('public/' . Helpers::settings()->player_image ?? '');
    //                         }
    //                         $Json[$i]['credit'] = $resultp->credit;
    //                         $Json[$i]['playerid'] = $resultpoints->playerid;
    //                         $Json[$i]['startingpoints'] = $resultpoints->startingpoints;
    //                         $Json[$i]['runs'] = $resultpoints->totalruns;
    //                         $Json[$i]['fours'] = $resultpoints->totalfours;
    //                         $Json[$i]['sixs'] = $resultpoints->totalsixs;
    //                         $Json[$i]['strike_rate'] = $resultpoints->totalstrike_rate;
    //                         $Json[$i]['century'] = $resultpoints->totalcentury + $resultpoints->totalhalfcentury;
    //                         $Json[$i]['halfcentury'] = $resultpoints->totalhalfcentury;
    //                         $Json[$i]['wickets'] = $resultpoints->totalwickets;
    //                         $Json[$i]['maidens'] = $resultpoints->totalmaidens;
    //                         $Json[$i]['economy_rate'] = $resultpoints->totaleconomy_rate;
    //                         $Json[$i]['runouts'] = $resultpoints->totalrunouts;
    //                         $Json[$i]['catch_points'] = $resultpoints->totalcatch;
    //                         $Json[$i]['stumping'] = $resultpoints->totalstumping;
    //                         $Json[$i]['thrower'] = $resultpoints->totalthrower;
    //                         $Json[$i]['hitter'] = $resultpoints->totalhitter;
    //                         $Json[$i]['bonus'] = $resultpoints->totalbonus;
    //                         $Json[$i]['negative'] = $resultpoints->totalnegative;
    //                         $Json[$i]['total'] = $resultpoints->totaltotal;

    //                         $total_selected_captain = DB::connection('mysql')->table('jointeam')
    //                             ->where('matchkey', $matchkey)
    //                             ->where('captain', $resultpoints->playerid)
    //                             ->count();

    //                         $captain_per = ($total_selected_captain > 0) ? number_format((($total_selected_captain / $total_teams) * 100), 2, ".", "") : 0;

    //                         $Json[$i]['captain_selection_percentage'] = $captain_per == '100.00' ? '100' : $captain_per;

    //                         $total_selected_vice_captain = DB::connection('mysql')->table('jointeam')
    //                             ->where('matchkey', $matchkey)
    //                             ->where('vicecaptain', $resultpoints->playerid)
    //                             ->count();

    //                         $vice_captain_per = ($total_selected_vice_captain > 0) ? number_format((($total_selected_vice_captain / $total_teams) * 100), 2, ".", "") : 0;

    //                         $Json[$i]['vice_captain_selection_percentage'] = $vice_captain_per == '100.00' ? '100' : $vice_captain_per;

    //                         $finduselectthisplayer = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->select('players')->get();
    //                         $countlenght = count($finduselectthisplayer);
    //                         $allplayers = array();
    //                         $countplayer = 0;
    //                         if (!empty($finduselectthisplayer)) {
    //                             foreach ($finduselectthisplayer as $pp) {
    //                                 $fplayers = array();
    //                                 $fplayers = explode(',', $pp->players);
    //                                 foreach ($fplayers as $fpl) {
    //                                     $allplayers[] = $fpl;
    //                                 }
    //                             }
    //                         }
    //                         if (!empty($allplayers)) {
    //                             foreach ($allplayers as $pl) {
    //                                 if ($pl == $player->playerid) {
    //                                     $countplayer++;
    //                                 }
    //                             }
    //                         }
    //                         if ($countplayer > 0) {
    //                             $countper = round(($countplayer / $countlenght) * 100, 2);
    //                         } else {
    //                             $countper = 0;
    //                         }
    //                         $Json[$i]['selectper'] = $countper . '%';
    //                         $i++;
    //                     }
    //                 }

    //             }
    //             return response()->json($Json);
    //         }
    //     }
    // }
    public function allmatchplayers(Request $request)
    {
        // Log::info('allmatchplayers');
        $data = $request->all();
        $geturl = Helpers::geturl();
        $users = Helpers::isAuthorize($request);
        $input = $request->all();
        if ($request->get('matchkey')) {
            $getmatchkey = $request->get('matchkey');
            $total_teams = DB::connection('mysql')->table('jointeam')->where('matchkey', $getmatchkey)->count();
            $matchplayers = DB::connection('mysql')->table('matchplayers')->where('matchkey', $getmatchkey)->join('players', 'players.id', '=', 'matchplayers.playerid')->leftjoin('teams', 'teams.id', '=', 'players.team')->select('players.player_name as playername', 'matchplayers.playerid', 'matchplayers.credit', 'players.image as playerimage', 'players.id as plid', 'players.role as prole', 'teams.team as teamname', 'teams.short_name','matchplayers.selection_per','matchplayers.captainselected','matchplayers.vcaptainselected')->get();
            $i = 0;
            $findmatchname = DB::connection('mysql')->table('listmatches')->where('matchkey', $getmatchkey)->select('name')->first();
            $Json = array();
            if (!empty($findmatchname)) {
                if (!empty($matchplayers)) {
                    $i = 0;
                    foreach ($matchplayers as $player) {

                        // $findresult_points = DB::connection('mysql')->table('result_points')->leftjoin('result_matches','result_matches.id','=','result_points.resultmatch_id')->leftjoin('listmatches','listmatches.matchkey','=','result_points.matchkey')->where('result_points.playerid',$player->playerid)->select(DB::raw('sum(result_points.fours) as totalfours'),DB::raw('sum(result_points.sixs) as totalsixs'),DB::raw('sum(result_points.runs) as totalruns'),DB::raw('sum(result_points.strike_rate) as totalstrike_rate'),DB::raw('sum(result_points.century) as totalcentury'),DB::raw('sum(result_points.halfcentury) as totalhalfcentury'),DB::raw('sum(result_points.wickets) as totalwickets'),DB::raw('sum(result_points.maidens) as totalmaidens'),DB::raw('sum(result_points.economy_rate) as totaleconomy_rate'),DB::raw('sum(result_points.runouts) as totalrunouts'),DB::raw('sum(result_points.catch) as totalcatch'),DB::raw('sum(result_points.stumping) as totalstumping'),DB::raw('sum(result_points.thrower) as totalthrower'),DB::raw('sum(result_points.hitter) as totalhitter'),DB::raw('sum(result_points.bonus) as totalbonus'),DB::raw('sum(result_points.negative) as totalnegative'),DB::raw('sum(result_points.total) as totaltotal'),'result_points.matchkey','result_matches.starting11','result_points.startingpoints','listmatches.name','result_matches.innings','result_matches.batting','result_matches.bowling','result_matches.duck')->where('result_points.matchkey',$getmatchkey)->where('result_matches.starting11',1)->orderBy('result_matches.innings','ASC')->orderBy('result_points.total','ASC')->first();
                        $findresult_points = DB::connection('mysql')->table('result_points')
                            ->leftjoin('result_matches', 'result_matches.id', '=', 'result_points.resultmatch_id')
                            ->leftjoin('listmatches', 'listmatches.matchkey', '=', 'result_points.matchkey')
                            ->where('result_points.playerid', $player->playerid)
                            ->select(
                                DB::raw('sum(result_points.fours) as totalfours'),
                                DB::raw('sum(result_points.sixs) as totalsixs'),
                                DB::raw('sum(result_points.runs) as totalruns'),
                                DB::raw('sum(result_points.strike_rate) as totalstrike_rate'),
                                DB::raw('sum(result_points.century) as totalcentury'),
                                DB::raw('sum(result_points.halfcentury) as totalhalfcentury'),
                                DB::raw('sum(result_points.thirtypoints) as totalthirtypoints'),
                                DB::raw('sum(result_points.wicketbonuspoint) as totalwicketbonuspoint'),
                                DB::raw('sum(result_points.wickets) as totalwickets'),
                                DB::raw('sum(result_points.maidens) as totalmaidens'),
                                DB::raw('sum(result_points.economy_rate) as totaleconomy_rate'),
                                DB::raw('sum(result_points.runouts) as totalrunouts'),
                                DB::raw('sum(result_points.catch) as totalcatch'),
                                DB::raw('sum(result_points.stumping) as totalstumping'),
                                DB::raw('sum(result_points.thrower) as totalthrower'),
                                DB::raw('sum(result_points.hitter) as totalhitter'),
                                DB::raw('sum(result_points.bonus) as totalbonus'),
                                DB::raw('sum(result_points.negative) as totalnegative'),
                                DB::raw('sum(result_points.total) as totaltotal'),
                                'result_points.matchkey', 'result_matches.starting11', 'result_points.startingpoints',
                                'listmatches.name', 'result_matches.innings', 'result_matches.batting', 'result_matches.bowling',
                                'result_matches.duck'
                            )
                            ->where('result_points.matchkey', $getmatchkey)
                            ->where('result_matches.starting11', 1)
                            ->orderBy('result_matches.innings', 'ASC')
                            ->orderBy('result_points.total', 'ASC')
                            ->first();
                        if ($findresult_points->starting11 == 1) {
                            $Json[$i]['credit'] = $player->credit;
                            $Json[$i]['playerid'] = $player->playerid;
                            $Json[$i]['role'] = $player->prole;
                            // $finduselectthisplayer = DB::connection('mysql')->table('jointeam')->where('matchkey', $getmatchkey)->select('players')->get();
                            // $countlenght = count($finduselectthisplayer);
                            // $allplayers = array();
                            // $countplayer = 0;
                            // if (!empty($finduselectthisplayer)) {
                            //     foreach ($finduselectthisplayer as $pp) {
                            //         $fplayers = array();
                            //         $fplayers = explode(',', $pp->players);
                            //         foreach ($fplayers as $fpl) {
                            //             $allplayers[] = $fpl;
                            //         }
                            //     }
                            // }
                            // if (!empty($allplayers)) {
                            //     foreach ($allplayers as $pl) {
                            //         if ($pl == $player->plid) {
                            //             $countplayer++;
                            //         }
                            //     }
                            // }
                            // if ($countplayer > 0) {
                            //     $countper = round(($countplayer / $countlenght) * 100, 2);
                            // } else {
                            //     $countper = 0;
                            // }
                            // $Json[$i]['selectper'] = $countper . '%';
                            $Json[$i]['selectper'] =  $player->selection_per.'%';
                            //   if($player->batting==1 || $player->bowling==1){
                            $Json[$i]['player_name'] = $player->playername;
                            if (!empty($player->playerimage)) {
                                $Json[$i]['playerimage'] = asset('/' . $player->playerimage);
                            } else {
                                $Json[$i]['playerimage'] = asset('images/defaultimage.png');
                            }
                            // $Json[$i]['matchname'] = $findresult_points->name;
                            $Json[$i]['duck'] = $findresult_points->duck;
                            $number = $findresult_points->innings;
                            $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
                            if (($number % 100) >= 11 && ($number % 100) <= 13) {
                                $abbreviation = $number . 'th';
                            } else {
                                $abbreviation = $number . $ends[$number % 10];
                            }

                            // $Json[$i]['innings'] =ucwords($player->teamname).' '. $abbreviation .' inning';
                            $Json[$i]['startingpoints'] = $findresult_points->startingpoints;
                            $Json[$i]['runs'] = $findresult_points->totalruns;
                            $Json[$i]['fours'] = $findresult_points->totalfours;
                            $Json[$i]['sixs'] = $findresult_points->totalsixs;
                            $Json[$i]['strike_rate'] = $findresult_points->totalstrike_rate;
                            $Json[$i]['thirtypoints'] = $findresult_points->totalthirtypoints;
                            $Json[$i]['halfcentury'] = $findresult_points->totalhalfcentury;
                            $Json[$i]['century'] = $findresult_points->totalcentury;
                            $Json[$i]['wickets'] = $findresult_points->totalwickets;
                            $Json[$i]['lbw_bowled'] = $findresult_points->totalwicketbonuspoint;
                            $Json[$i]['maidens'] = $findresult_points->totalmaidens;
                            $Json[$i]['economy_rate'] = $findresult_points->totaleconomy_rate;
                            //     $Json[$i]['catch'] = $findresult_points->totalcatch;
                            $Json[$i]['catch_points'] = $findresult_points->totalcatch;
                            //     $Json[$i]['stumping'] = $findresult_points->totalstumping+$findresult_points->totalthrower+$findresult_points->totalhitter;
                            $Json[$i]['stumping'] = $findresult_points->totalstumping;
                            $Json[$i]['thrower'] = $findresult_points->totalthrower;
                            $Json[$i]['runout'] = $findresult_points->totalthrower + $findresult_points->totalhitter;
                            $Json[$i]['hitter'] = $findresult_points->totalhitter;
                            $Json[$i]['bonus'] = $findresult_points->totalbonus;
                            $Json[$i]['negative'] = $findresult_points->totalnegative;
                            $Json[$i]['total'] = $findresult_points->totaltotal;
                            // $total_selected_captain = DB::connection('mysql')->table('jointeam')
                            //     ->where('matchkey', $getmatchkey)
                            //     ->where('captain', $player->playerid)
                            //     ->count();

                            // $captain_per = ($total_selected_captain > 0) ? number_format((($total_selected_captain / $total_teams) * 100), 2, ".", "") : 0;

                            // $Json[$i]['captain_selection_percentage'] = $captain_per == '100.00' ? '100' : $captain_per;

                            // $total_selected_vice_captain = DB::connection('mysql')->table('jointeam')
                            //     ->where('matchkey', $getmatchkey)
                            //     ->where('vicecaptain', $player->playerid)
                            //     ->count();

                            // $vice_captain_per = ($total_selected_vice_captain > 0) ? number_format((($total_selected_vice_captain / $total_teams) * 100), 2, ".", "") : 0;

                            // $Json[$i]['vice_captain_selection_percentage'] = $vice_captain_per == '100.00' ? '100' : $vice_captain_per;
                            // $finduselectthisplayer = DB::connection('mysql')->table('jointeam')->where('matchkey', $getmatchkey)->select('players')->get();
                            // $countlenght = count($finduselectthisplayer);
                            // $allplayers = array();
                            // $countplayer = 0;
                            // if (!empty($finduselectthisplayer)) {
                            //     foreach ($finduselectthisplayer as $pp) {
                            //         $fplayers = array();
                            //         $fplayers = explode(',', $pp->players);
                            //         foreach ($fplayers as $fpl) {
                            //             $allplayers[] = $fpl;
                            //         }
                            //     }
                            // }
                            // if (!empty($allplayers)) {
                            //     foreach ($allplayers as $pl) {
                            //         if ($pl == $player->playerid) {
                            //             $countplayer++;
                            //         }
                            //     }
                            // }
                            // if ($countplayer > 0) {
                            //     $countper = round(($countplayer / $countlenght) * 100, 2);
                            // } else {
                            //     $countper = 0;
                            // }

                            // $Json[$i]['matchname'] = $findmatchname->name;
                            // $Json[$i]['runouts'] = $findresult_points->totalrunouts;
                            $Json[$i]['captain_selection_percentage'] = $player->captainselected;
                            $Json[$i]['total_selected_vice_captain'] = $player->vcaptainselected;
                            $Json[$i]['status'] = 1;
                            $i++;
                        }

                    }
                } else {
                    $Json[0]['status'] = 0;
                }
            } else {
                $Json[0]['status'] = 0;
                $i++;
            }
            return response()->json($Json);
            die;
        }
    }
    /************ API RELATED TO PLAYERS **********/

    // to get the players of match//
    /**
     * @return json
     * @Url: /api/getallplayers/
     * @Method: GET
     * @Parameters
     *
     *         matchkey: "text"
     *       auth_key: "text" in header(Authorization)
     *
     *
     */
    public function getallplayers(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $input = $request->all();
            $id = $user->id;
            unset($input['auth_key']);
            $matchkey = $request->get('matchkey');
            $Json = array();
            $findmatch = DB::connection('mysql')->table('listmatches')
                ->join('teams as t1', 't1.id', '=', 'listmatches.team1')
                ->join('teams as t2', 't2.id', '=', 'listmatches.team2')
                ->where('listmatches.matchkey', $matchkey)
                ->orderBy('listmatches.start_date', 'desc')
                ->select('listmatches.*', 't1.short_name as teamdata1_short_name', 't2.short_name as teamdata2_short_name')
                ->first();

            if (!empty($findmatch)) {
                $team1 = $findmatch->team1;
                $team2 = $findmatch->team2;
                $getseries = $findmatch->series;
                $allmatchkey = array();
                $findallmatches = DB::connection('mysql')->table('listmatches')->where('series', $getseries)->select('matchkey')->get();
                if (!empty($findallmatches)) {
                    foreach ($findallmatches as $matches) {
                        $allmatchkey[] = $matches->matchkey;
                    }
                }
                $findpplayers = DB::connection('mysql')->table('matchplayers')
                    ->join('players', 'players.id', 'matchplayers.playerid')
                    ->where('matchplayers.matchkey', $matchkey)
                    ->orderBy('players.points', 'DESC')
                    ->select('matchplayers.*', 'players.id as matchplayers_playerid_id',
                        'players.team as matchplayers_playerid_team', 'players.player_name as matchplayers_playerid_player_name', 'players.players_key as matchplayers_playerid_players_key', 'players.image as matchplayers_playerid_image','matchplayers.selection_per','matchplayers.captainselected','matchplayers.vcaptainselected')
                    ->get();
                if (!empty($findpplayers)) {
                    $i = 0;
                    foreach ($findpplayers as $pp) {
                        $findallplayers = DB::connection('mysql')->table('matchplayers')->whereIn('matchkey', $allmatchkey)->where('playerid', $pp->matchplayers_playerid_id)->select(DB::raw("SUM(points) as totalpoints"))->get();
                        if (($pp->matchplayers_playerid_team == $team2) || ($pp->matchplayers_playerid_team == $team1)) {
                            $Json[$i]['id'] = $pp->matchplayers_playerid_id;
                            $Json[$i]['name'] = $pp->matchplayers_playerid_player_name;
                            $Json[$i]['role'] = $pp->role;
                            $Json[$i]['credit'] = $pp->credit;
                            $Json[$i]['playingstatus'] = $pp->playingstatus;
                            $Json[$i]['playerkey'] = $pp->matchplayers_playerid_players_key;
                            if ($pp->matchplayers_playerid_image != "") {
                                $Json[$i]['image'] = $geturl . 'public/' . $pp->matchplayers_playerid_image;
                            } else {
                                $Json[$i]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                            }
                            if (!empty($findallplayers[0]->totalpoints)) {
                                $Json[$i]['points'] = $findallplayers[0]->totalpoints;
                            } else {
                                $Json[$i]['points'] = 0;
                            }
                            $Json[$i]['isSelected'] = false;
                            if ($pp->matchplayers_playerid_team == $team1) {
                                $Json[$i]['team'] = 'team1';
                                $Json[$i]['teamname'] = strtoupper($findmatch->teamdata1_short_name);
                            }
                            if ($pp->matchplayers_playerid_team == $team2) {
                                $Json[$i]['team'] = 'team2';
                                $Json[$i]['teamname'] = strtoupper($findmatch->teamdata2_short_name);
                            }

                            # captain_selection_percentage vice_captain_selection_percentage

                            // $total_teams = DB::connection('mysql')->table('jointeam')->where('matchkey', $findmatch->matchkey)->count();

                            // $total_selected_captain = DB::connection('mysql')->table('jointeam')
                            //     ->where('matchkey', $findmatch->matchkey)
                            //     ->where('captain', $pp->matchplayers_playerid_id)
                            //     ->count();

                            // $captain_per = ($total_selected_captain > 0) ? number_format((($total_selected_captain / $total_teams) * 100), 2, ".", "") : 0;

                            // $Json[$i]['captain_selection_percentage'] = $captain_per == '100.00' ? '100' : $captain_per;

                            // $total_selected_vice_captain = DB::connection('mysql')->table('jointeam')
                            //     ->where('matchkey', $findmatch->matchkey)
                            //     ->where('vicecaptain', $pp->matchplayers_playerid_id)
                            //     ->count();

                            // $vice_captain_per = ($total_selected_vice_captain > 0) ? number_format((($total_selected_vice_captain / $total_teams) * 100), 2, ".", "") : 0;

                            // $Json[$i]['vice_captain_selection_percentage'] = $vice_captain_per == '100.00' ? '100' : $vice_captain_per;

                            // # end captain_selection_percentage vice_captain_selection_percentage

                            // # particular player selection
                            // $total_selected_players = DB::connection('mysql')->table('jointeam')
                            //     ->where('matchkey', $findmatch->matchkey)
                            //     ->select('players')
                            //     ->get();

                            // $total_selected_this_player = 0;
                            // foreach ($total_selected_players as $players) {

                            //     $exp = explode(',', $players->players);

                            //     if (in_array($pp->matchplayers_playerid_id, $exp)) {
                            //         $total_selected_this_player += 1;
                            //     }

                            // }

                            // if ($total_selected_this_player > 0 and $total_teams > 0) {

                            //     $players_per = ($total_selected_this_player > 0) ? number_format((($total_selected_this_player / $total_teams) * 100), 2, ".", "") : 0;

                            // } else {
                            //     $players_per = '0';
                            // }

                            // $Json[$i]['player_selection_percentage'] = $players_per == '100.00' ? '100' : $players_per;
                            # particular player selection

                            $Json[$i]['player_selection_percentage'] =$pp->selection_per;
                            $Json[$i]['captain_selection_percentage'] = $pp->captainselected;;
                            $Json[$i]['vice_captain_selection_percentage'] =$pp->vcaptainselected;;
                            $i++;
                        }
                    }
                }
            }

            $gtlastranks = array_multisort(array_column($Json, 'player_selection_percentage'), SORT_DESC, $Json);
            //     $gtlastranks = Helpers::multid_sort($Json, 'player_selection_percentage');
            //     dd($Json);
            return response()->json($Json);
            die;
        }
    }
    // to get the info of a player//
    /*  URL :- /api/getPlayerInfo
    Method :- post
    Heder :- Authorization -> Bearer auth_key(from registerusers table)
    Params :- playerid, matchkey
     */
    public function getPlayerInfo(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();

        $user = Helpers::isAuthorize($request);
        $Json = array();
        if ($user) {
            $input = $request->all();
            $id = $user->id;
            unset($input['auth_key']);
            $playerid = $request->get('playerid');
            $matchkey = $request->get('matchkey');
            $findseries = DB::connection('mysql')->table('listmatches')->where('matchkey', $matchkey)->select('series')->first();
            $findplayerdetails = DB::connection('mysql')->table('matchplayers')
                ->join('players', 'players.id', 'matchplayers.playerid')
                ->join('teams', 'teams.id', 'players.team')
                ->where('matchplayers.matchkey', $matchkey)
                ->where('matchplayers.playerid', $playerid)
                ->select('matchplayers.*', 'players.id as matchplayers_playerid_id',
                    'players.team as matchplayers_playerid_team', 'players.player_name as matchplayers_playerid_player_name', 'players.players_key as matchplayers_playerid_players_key', 'players.image as matchplayers_playerid_image', 'players.battingstyle as matchplayers_playerid_battingstyle', 'players.bowlingstyle as matchplayers_playerid_bowlingstyle', 'players.battingstyle as matchplayers_playerid_battingstyle', 'players.country as matchplayers_playerid_country', 'players.dob as matchplayers_playerid_dob', 'teams.short_name as matchplayers_playerid_players_team_short_name')
                ->first();
            $findallplayers = DB::connection('mysql')->table('matchplayers')
                ->join('listmatches', 'listmatches.matchkey', '=', 'matchplayers.matchkey')->where('playerid', $findplayerdetails->playerid)
                ->where('listmatches.series', $findseries->series)
                ->select(DB::raw("SUM(points) as totalpoints"))
                ->get();

            if (!empty($findallplayers[0]->totalpoints)) {
                $Json['playerpoints'] = $findallplayers[0]->totalpoints;
            } else {
                $Json['playerpoints'] = 0;
            }
            if (!empty($findplayerdetails)) {
                $Json['playername'] = $findplayerdetails->matchplayers_playerid_player_name;
                $Json['playerkey'] = $findplayerdetails->matchplayers_playerid_players_key;
                $Json['playercredit'] = $findplayerdetails->credit;
                if (!empty($findplayerdetails->matchplayers_playerid_battingstyle)) {
                    $Json['battingstat'] = $findplayerdetails->matchplayers_playerid_battingstyle;
                } else {
                    $Json['battingstat'] = 'test';
                }
                if (!empty($findplayerdetails->matchplayers_playerid_bowlingstyle)) {
                    $Json['bowlerstat'] = $findplayerdetails->matchplayers_playerid_bowlingstyle;
                } else {
                    $Json['bowlerstat'] = 'test';
                }
                if (!empty($findplayerdetails->matchplayers_playerid_country)) {
                    $Json['playercountry'] = $findplayerdetails->matchplayers_playerid_country;
                } else {
                    $Json['playercountry'] = 'test';
                }
                if (!empty($findplayerdetails->matchplayers_playerid_dob)) {
                    $Json['playerdob'] = $findplayerdetails->matchplayers_playerid_dob;
                } else {
                    $Json['playerdob'] = '1970-01-01';
                }
                $Json['team'] = $findplayerdetails->matchplayers_playerid_players_team_short_name;
                if ($findplayerdetails->matchplayers_playerid_image == "") {
                    $Json['playerimage'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                } else {
                    $Json['playerimage'] = $geturl . 'public/' . $findplayerdetails->matchplayers_playerid_image;
                }
                if ($findplayerdetails->role == 'allrounder') {
                    $Json['playerrole'] = 'All Rounder';
                } else if ($findplayerdetails->role == 'keeper') {
                    $Json['playerrole'] = 'Wicket Keeper';
                } else {
                    $Json['playerrole'] = ucwords($findplayerdetails->role);
                }
                $findallmatches = DB::connection('mysql')->table('listmatches')->join('matchplayers', 'matchplayers.matchkey', '=', 'listmatches.matchkey')->where('matchplayers.playerid', $playerid)->join('players', 'players.id', '=', 'matchplayers.playerid')->where('listmatches.series', $findseries->series)->select('listmatches.name as name', 'listmatches.matchkey', 'listmatches.start_date', 'matchplayers.playerid as player_id', 'listmatches.team1 as team1', 'listmatches.team2 as team2')->groupBy('listmatches.matchkey')->get();

                $aa = $findallmatches->toArray();

                $i = 0;
                $totalpoints = 0;
                if (!empty($aa)) {
                    foreach ($findallmatches as $player) {
                        $ttlpoint = DB::connection('mysql')->table('result_points')->where('playerid', $player->player_id)->where('matchkey', $player->matchkey)->first();
                        if (!empty($ttlpoint)) {
                            $totalpoints = $ttlpoint->total;
                        } else {
                            $totalpoints = 0;
                        }
                        $team1data = DB::connection('mysql')->table('teams')->where('id', $player->team1)->select('short_name')->first();
                        $team2data = DB::connection('mysql')->table('teams')->where('id', $player->team2)->select('short_name')->first();
                        if ((!empty($team1data)) && (!empty($team2data))) {
                            $Json['matches'][$i]['shortname'] = $team1data->short_name . ' vs ' . $team2data->short_name;
                        }
                        $Json['matches'][$i]['matchname'] = $player->name;
                        $Json['matches'][$i]['matchdate'] = date('d M, Y', strtotime($player->start_date));
                        $Json['matches'][$i]['total_points'] = $totalpoints;
                        // $finduselectthisplayer = DB::connection('mysql')->table('jointeam')->where('matchkey', $player->matchkey)->select('players')->get();

                        // $countlenght = count($finduselectthisplayer);
                        // $allplayers = array();
                        // $countplayer = 0;
                        // $a = $finduselectthisplayer->toArray();
                        // if (!empty($a)) {
                        //     foreach ($finduselectthisplayer as $pp) {
                        //         $fplayers = array();
                        //         $fplayers = explode(',', $pp->players);
                        //         foreach ($fplayers as $fpl) {
                        //             $allplayers[] = $fpl;
                        //         }
                        //     }
                        // }

                        // if (!empty($allplayers)) {
                        //     foreach ($allplayers as $pl) {
                        //         if ($pl == $player->player_id) {
                        //             $countplayer++;
                        //         }
                        //     }
                        // }
                        // if ($countplayer > 0) {
                        //     $countper = round(($countplayer / $countlenght) * 100, 2);
                        // } else {
                        //     $countper = 0;
                        // }
                        // $Json['matches'][$i]['selectper'] = $countper . '%';
                        $Json['matches'][$i]['selectper'] = '0%';
                        $i++;}
                } else {
                    $Json['matches'] = [];
                }
            }
            return response()->json(array($Json));
            die;
        }
    }

    /**************** Api Related To Team ******************/

    // create team//
    /*
    URL :- /api/createmyteam
    Method :- Post
    Header :- Authorization -> Bearer auth_key(from registerusers table)
    Params :-matchkey, player_type, teamnumber, players, captain, vicecaptain
     */

    public function createmyteam(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            if ($request->isMethod('post')) {
                $input = $request->all();

                $userid = $user->id;
                unset($input['auth_key']);
                $matchkey = $request->get('matchkey');
                // $player_type = $request->get('player_type');
                $teamnumber = $request->get('teamnumber');
                $players = $request->get('players');
                $captain = $request->get('captain');
                $vicecaptain = $request->get('vicecaptain');
                $json = array();
                $playersarray = array_filter(explode(',', $players));
                $playersarrays = implode(',', $playersarray);

                if (count($playersarray) < 11) {
                    $Json['success'] = false;
                    $Json['message'] = "Please choose 11 players";
                    return response()->json($Json);
                    die;
                }

                $findallplayerdetails = DB::connection('mysql')->table('matchplayers')->whereIn('playerid', $playersarray)->where('matchkey', $matchkey)->select('credit')->get();
                if (!empty($findallplayerdetails)) {
                    $allc = 0;
                    foreach ($findallplayerdetails as $pcred) {
                        $allc += $pcred->credit;
                    }
                    if ($allc > 100) {
                        $Json['success'] = false;
                        $Json['message'] = "Credit exceeded";
                        return response()->json($Json);
                        die;
                    }
                }
                $findmatchdetails = DB::connection('mysql')->table('listmatches')->where('matchkey', $matchkey)->select('start_date')->first();
                if (!empty($findmatchdetails)) {
                    $getcurrentdate = date('Y-m-d H:i:s');
                    $matchlockdate = date('Y-m-d H:i:s', strtotime($findmatchdetails->start_date));
                    if ($getcurrentdate > $matchlockdate) {
                        $Json['success'] = false;
                        $Json['message'] = "match closed";
                        return response()->json($Json);
                        die;
                    }
                }
                $data['userid'] = $userid;
                $data['matchkey'] = $matchkey;
                $data['teamnumber'] = $teamnumber;
                $data['players'] = $players;
                // $data['player_type'] = $player_type;
                $data['captain'] = $captain;
                $data['vicecaptain'] = $vicecaptain;
                date_default_timezone_set('Asia/Kolkata');

                $findlastteam = DB::connection('mysql')->table('jointeam')->where('userid', $data['userid'])
                    ->where('matchkey', $matchkey)
                // ->where('player_type',$player_type)
                    ->orderBy('teamnumber', 'DESC')->select('*')->get();
                // check for duplicate//
                if (!empty($findlastteam)) {
                    foreach ($findlastteam as $lteam) {
                        if ($lteam->captain == $data['captain'] && $lteam->vicecaptain == $data['vicecaptain']) {
                            $allplayersget = explode(',', $lteam->players);
                            $nowplayers = explode(',', $data['players']);
                            $result = array_intersect($nowplayers, $allplayersget);
                            if (count($allplayersget) == count($result)) {
                                $Json['message'] = 'You cannot create the same team.';
                                $Json['teamid'] = 0;
                                $Json['success'] = false;
                                return response()->json($Json);
                                die;
                            }
                        }
                    }
                }
                $findcreateteam = DB::connection('mysql')->table('jointeam')->where('userid', $userid)->where('matchkey', $matchkey)
                // ->where('player_type',$player_type)
                    ->where('teamnumber', $teamnumber)->first();
                if (!empty($findcreateteam)) {
                    $data['created_at'] = $findcreateteam->created_at;
                    DB::connection('mysql2')->table('jointeam')->where('id', $findcreateteam->id)->update($data);
                    $getteamid = $findcreateteam->id;
                } else {
                    $findlastteam = DB::connection('mysql')->table('jointeam')->where('userid', $userid)->where('matchkey', $matchkey)
                    // ->where('player_type',$player_type)
                        ->orderBy('teamnumber', 'DESC')->select('teamnumber')->first();
                    if (!empty($findlastteam)) {
                        $finnewteamnumber = $findlastteam->teamnumber + 1;
                        if ($finnewteamnumber <= 99) {
                            $data['teamnumber'] = $finnewteamnumber;
                        } else {
                            $Json['message'] = 'Limit exceed';
                            $Json['success'] = false;
                            return response()->json($Json);
                            die;
                        }
                    } else {
                        $data['teamnumber'] = 1;
                    }
                    if ($data['teamnumber'] <= 99) {
                        $getteamid = DB::connection('mysql2')->table('jointeam')->insertGetId($data);
                    } else {
                        $Json['message'] = 'Limit exceed';
                        $Json['success'] = false;
                        return response()->json($Json);
                        die;
                    }
                }
                $Json['success'] = true;
                $Json['message'] = 'Team Created Successfully';
                $Json['teamid'] = $getteamid;
                if (!empty($request->get('type') == 'IOS')) {
                    return response()->json(array($Json));
                } else {
                    return response()->json($Json);
                    die;
                }
            }
        }
    }
    // to get my all teams//
    /*
    URL :- /api/getMyTeams
    Method :- GET
    Header :- Authorization -> Bearer auth_key(from registerusers table)
    Params :- matchkey
     */

    public function getMyTeams(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();

        $user = Helpers::isAuthorize($request);
        if ($user) {
            $input = $request->all();
            $userid = $user->id;
            unset($input['auth_key']);
            $matchkey = $request->get('matchkey');
            $challengeid = $request->get('challengeid');
            $query = DB::connection('mysql')->table('jointeam');
            $query = $query->where('matchkey', $matchkey)->where('userid', $userid);
            if ($request->get('challengeid')) {
                if (!empty($challengeid)) {
                    $getchallenge = DB::connection('mysql')->table('matchchallenges')->where('id', $challengeid)->select('c_type')->first();
                    if (!empty($getchallenge->c_type)) {
                        $type = $getchallenge->c_type;
                        $query = $query->where('player_type', $type);
                    }
                }
            }
            if (!empty($request->get('type'))) {
                $type = $request->get('type');
                $query = $query->where('player_type', $type);
            }
            $findjointeam = $query->orderBy('teamnumber', 'ASC')->select('id', 'teamnumber', 'captain', 'vicecaptain', 'players', 'player_type', 'points')->get();

            $Json = array();
            $i = 0;
            if (count($findjointeam) > 0) {
                foreach ($findjointeam as $jointeams) {
                    $Json[$i]['teamnumber'] = $jointeams->teamnumber;
                    $Json[$i]['teamid'] = $jointeams->id;
                    $Json[$i]['points'] = $jointeams->points;
                    $Json[$i]['player_type'] = $jointeams->player_type;
                    $joinleaug = DB::connection('mysql')->table('joinedleauges')->where('challengeid', $challengeid)->where('teamid', $jointeams->id)->first();
                    if (!empty($joinleaug)) {
                        $Json[$i]['isSelected'] = true;
                    } else {
                        $Json[$i]['isSelected'] = false;
                    }
                    $boplayerdetails = DB::connection('mysql')->table('matchplayers')
                        ->join('players', 'players.id', 'matchplayers.playerid')
                        ->join('teams', 'teams.id', 'players.team')
                        ->where('matchplayers.matchkey', $matchkey)
                        ->where('matchplayers.playerid', $jointeams->captain)
                        ->select('matchplayers.*', 'players.player_name as matchplayers_playerid_player_name', 'players.image as matchplayers_playerid_image')
                        ->first();
                    //echo '<pre>';print_r($boplayerdetails);die;
                    if (!empty($boplayerdetails)) {
                        $Json[$i]['captain'] = $boplayerdetails->matchplayers_playerid_player_name;
                        if ($boplayerdetails->matchplayers_playerid_image != "") {
                            $Json[$i]['captainimage'] = $geturl . 'public/' . $boplayerdetails->matchplayers_playerid_image;
                        } else {
                            $Json[$i]['captainimage'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                        }
                    }
                    $boplayerdetails1 = DB::connection('mysql')->table('matchplayers')
                        ->join('players', 'players.id', 'matchplayers.playerid')
                        ->join('teams', 'teams.id', 'players.team')
                        ->where('matchplayers.matchkey', $matchkey)
                        ->where('matchplayers.playerid', $jointeams->vicecaptain)
                        ->select('matchplayers.*', 'players.player_name as matchplayers_playerid_player_name', 'players.image as matchplayers_playerid_image')
                        ->first();
                    if (!empty($boplayerdetails1)) {
                        $Json[$i]['vicecaptain'] = $boplayerdetails1->matchplayers_playerid_player_name;
                        if ($boplayerdetails1->matchplayers_playerid_image != "") {
                            $Json[$i]['vicecaptainimage'] = $geturl . 'public/' . $boplayerdetails1->matchplayers_playerid_image;
                        } else {
                            $Json[$i]['vicecaptainimage'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                        }
                    }
                    $nowplayers = explode(',', $jointeams->players);
                    $findmatch = DB::connection('mysql')->table('listmatches')->where('matchkey', $matchkey)->first();
                    $playerss = DB::connection('mysql')->table('matchplayers')
                        ->join('players', 'players.id', 'matchplayers.playerid')
                        ->join('teams', 'teams.id', 'players.team')
                        ->where('matchplayers.matchkey', $matchkey)
                        ->whereIn('matchplayers.playerid', $nowplayers)
                        ->select('matchplayers.*', 'players.player_name as matchplayers_playerid_player_name', 'players.image as matchplayers_playerid_image', 'players.team as matchplayers_playerid_team')
                        ->get();
                    $btco = 0;
                    $btco1 = 0;
                    $btco2 = 0;
                    $btco3 = 0;
                    $btco4 = 0;
                    foreach ($playerss as $bowler1) {

                        if ($bowler1->role == 'batsman') {

                            $btco++;
                        }
                        if ($bowler1->role == 'bowler') {

                            $btco1++;
                        }
                        if ($bowler1->role == 'allrounder') {

                            $btco2++;
                        }
                        if ($bowler1->matchplayers_playerid_team == $findmatch->team1) {
                            $btco3++;
                        }
                        if ($bowler1->matchplayers_playerid_team == $findmatch->team2) {
                            $btco4++;
                        }
                    }
                    $j = 0;
                    $Json[$i]['batsmancount'] = $btco;
                    $Json[$i]['bowlercount'] = $btco1;
                    $Json[$i]['allroundercount'] = $btco2;
                    $Json[$i]['team1count'] = $btco3;
                    $Json[$i]['team2count'] = $btco4;
                    foreach ($playerss as $bowler) {
                        // dd($bowler);
                        $Json[$i]['player'][$j]['id'] = $bowler->playerid;
                        $Json[$i]['player'][$j]['name'] = $bowler->matchplayers_playerid_player_name;
                        $Json[$i]['player'][$j]['role'] = $bowler->role;
                        $Json[$i]['player'][$j]['credit'] = $bowler->credit;
                        $Json[$i]['player'][$j]['playingstatus'] = $bowler->playingstatus;
                        if ($bowler->matchplayers_playerid_team == $findmatch->team1) {
                            $Json[$i]['player'][$j]['team'] = 'team1';
                        }
                        if ($bowler->matchplayers_playerid_team == $findmatch->team2) {
                            $Json[$i]['player'][$j]['team'] = 'team2';
                        }
                        if ($bowler->matchplayers_playerid_image != "") {
                            $Json[$i]['player'][$j]['image'] = $geturl . 'public/' . $bowler->matchplayers_playerid_image;
                        } else {
                            $Json[$i]['player'][$j]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                        }
                        $vicecaptain = 0;
                        $captain = 0;
                        if ($jointeams->vicecaptain == $bowler->playerid) {
                            $vicecaptain = 1;
                        }
                        if ($jointeams->captain == $bowler->playerid) {
                            $captain = 1;
                        }
                        $Json[$i]['player'][$j]['vicecaptain'] = $vicecaptain;
                        $Json[$i]['player'][$j]['captain'] = $captain;
                        $findlastow = array();
                        $findlastow = DB::connection('mysql')->table('matchplayers')->where('id', $bowler->playerid)->select('points')->first();
                        $currentpoints = $bowler->points;
                        // if ($captain == 1) {
                        //     $currentpoints = $currentpoints * 2;
                        // } else if ($vicecaptain == 1) {
                        //     $currentpoints = $currentpoints * 1.5;
                        // }
                        $Json[$i]['player'][$j]['points'] = $currentpoints;

                        $j++;
                    }
                    if ($request->get('challengeid')) {
                        $findifwithteam = DB::connection('mysql')->table('joinedleauges')->where('challengeid', $request->get('challengeid'))->where('teamid', $jointeams->id)->where('userid', $userid)->first();
                        if (!empty($findifwithteam)) {
                            $Json[$i]['isSelected'] = true;
                        }
                    }
                    $Json[$i]['status'] = 1;
                    $i++;
                }
            }
            // else{
            //     $Json['status'] = 0;
            //     $Json['msg'] = 'Sorry,no data available!';

            // }
            return response()->json($Json);
            die;
        }
    }
    // to get my team
    /*
    URL :- /api/myteam
    Method :- GET
    Header :- Authorization -> Bearer auth_key(from registerusers table)
    Params :- matchkey
     */

    public function myteam(Request $request)
    {
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $input = $request->all();
            $userid = $user->id;
            unset($input['auth_key']);
            Helpers::accessrules();
            $matchkey = $_GET['matchkey'];
            $findjointeam = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->where('userid', $userid)->orderBy('teamnumber', 'ASC')->select('id', 'teamnumber', 'player_type')->get();

            $Json = array();
            if (count($findjointeam) > 0) {
                $i = 0;
                foreach ($findjointeam as $jointeams) {
                    $Json[$i]['teamnumber'] = $jointeams->teamnumber;
                    $Json[$i]['teamid'] = $jointeams->id;
                    $Json[$i]['player_type'] = $jointeams->player_type;

                    $jointeam = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->where('userid', $userid)->where('teamnumber', $jointeams->teamnumber)->where('player_type', $jointeams->player_type)->select('*')->get();

                    if (!empty($jointeam)) {
                        foreach ($jointeam as $team) {
                            $playersarr = explode(',', $team->players);
                            $findmatch = DB::connection('mysql')->table('listmatches')->where('matchkey', $team->matchkey)->first();

                            $boplayerdetails = DB::connection('mysql')->table('matchplayers')
                                ->join('players', 'players.id', 'matchplayers.playerid')
                                ->join('teams', 'teams.id', 'players.team')
                                ->where('matchplayers.matchkey', $team->matchkey)
                                ->whereIn('matchplayers.playerid', $playersarr)
                                ->select('matchplayers.*', 'players.player_name as matchplayers_playerid_player_name', 'players.image as matchplayers_playerid_image', 'players.team as matchplayers_playerid_team')
                                ->get();

                            $a = $boplayerdetails->toArray();
                            if (!empty($findmatch)) {
                                if (!empty($a)) {
                                    $j = 0;
                                    foreach ($boplayerdetails as $bowler) {
                                        $Json[$i]['player'][$j]['id'] = $bowler->playerid;
                                        $Json[$i]['player'][$j]['name'] = $bowler->matchplayers_playerid_player_name;
                                        $Json[$i]['player'][$j]['role'] = $bowler->role;
                                        $Json[$i]['player'][$j]['credit'] = $bowler->credit;
                                        if ($bowler->matchplayers_playerid_team == $findmatch->team1) {
                                            $Json[$i]['player'][$j]['team'] = 'team1';
                                        }
                                        if ($bowler->matchplayers_playerid_team == $findmatch->team2) {
                                            $Json[$i]['player'][$j]['team'] = 'team2';
                                        }
                                        if ($bowler->matchplayers_playerid_image != "") {
                                            $Json[$i]['player'][$j]['image'] = $geturl . 'public/' . $bowler->matchplayers_playerid_image;
                                        } else {
                                            $Json[$i]['player'][$j]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                                        }
                                        $vicecaptain = 0;
                                        $captain = 0;
                                        if ($team->vicecaptain == $bowler->playerid) {
                                            $vicecaptain = 1;
                                        }
                                        if ($team->captain == $bowler->playerid) {
                                            $captain = 1;
                                        }
                                        $Json[$i]['player'][$j]['vicecaptain'] = $vicecaptain;
                                        $Json[$i]['player'][$j]['captain'] = $captain;
                                        $findlastow = array();
                                        $findlastow = DB::connection('mysql')->table('players')->where('id', $bowler->playerid)->select('points')->first();
                                        $currentpoints = $findlastow->points;
                                        if ($captain == 1) {
                                            $currentpoints = $currentpoints * 2;
                                        } else if ($vicecaptain == 1) {
                                            $currentpoints = $currentpoints * 1.5;
                                        }
                                        $Json[$i]['player'][$j]['points'] = $currentpoints;
                                        $j++;
                                    }
                                }
                            } else {
                                $json = [];
                            }
                        }
                    }
                    $i++;
                }
            } else {
                $json = [];
            }
            return response()->json($Json);
            die;
        }
    }
    //to view all teams
    /*
    URL :- /api/viewteam
    Method :- GET
    Header :-  Authorization ->    Bearer auth_key (from registerusers table)
    Params :- matchkey, teamid, teamnumber
     */

    public function viewteam(Request $request)
    {
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $input = $request->all();
            $userid = $user->id;
            unset($input['auth_key']);
            $teamid = $request->get('teamid');
            $query = DB::connection('mysql')->table('jointeam');
            if ($request->get('teamid')) {
                $query = $query->where('id', $teamid);

            }
            if ($request->get('teamnumber') && $request->get('matchkey')) {
                $query = $query->where('matchkey', $request->get('matchkey'))->where('teamnumber', $request->get('teamnumber'));
            }
            $jointeam = $query->select('*')->get();

            $Json = array();
            if (count($jointeam) > 0) {
                foreach ($jointeam as $team) {
                    $playersarr = explode(',', $team->players);
                    $boplayerdetails = DB::connection('mysql')->table('matchplayers')
                        ->join('players', 'players.id', 'matchplayers.playerid')
                        ->join('teams', 'teams.id', 'players.team')
                        ->where('matchplayers.matchkey', $team->matchkey)
                        ->whereIn('matchplayers.playerid', $playersarr)
                        ->select('matchplayers.*', 'players.player_name as matchplayers_playerid_player_name', 'players.image as matchplayers_playerid_image', 'players.team as matchplayers_playerid_team')
                        ->get();

                    if (count($boplayerdetails) > 0) {
                        $j = 0;
                        foreach ($boplayerdetails as $bowler) {
                            $findmatch = DB::connection('mysql')->table('listmatches')
                                ->join('teams as t1', 't1.id', '=', 'listmatches.team1')
                                ->join('teams as t2', 't2.id', '=', 'listmatches.team2')
                                ->where('listmatches.matchkey', $jointeam[0]->matchkey)
                                ->select('listmatches.*', 't1.short_name as teamdata1_short_name', 't2.short_name as teamdata2_short_name')
                                ->first();

                            $Json[$j]['id'] = $bowler->playerid;
                            $Json[$j]['name'] = $bowler->matchplayers_playerid_player_name;
                            $Json[$j]['isSelected'] = false;
                            $Json[$j]['role'] = $bowler->role;
                            $Json[$j]['credit'] = $bowler->credit;
                            $Json[$j]['points'] = $bowler->points;
                            $Json[$j]['ppoints'] = $bowler->points;
                            $Json[$j]['playingstatus'] = $bowler->playingstatus;
                            if (!empty($findmatch)) {
                                if ($bowler->matchplayers_playerid_team == $findmatch->team1) {
                                    $Json[$j]['team'] = 'team1';
                                    $Json[$j]['teamname'] = $findmatch->teamdata1_short_name;
                                } else {
                                    $Json[$j]['team'] = 'team2';
                                    $Json[$j]['teamname'] = $findmatch->teamdata2_short_name;
                                }
                            } else {
                                $Json[$j]['team'] = '';
                                $Json[$j]['teamname'] = '';

                            }
                            $captain = 0;
                            if ($team->captain == $bowler->playerid) {
                                $captain = 1;
                            }
                            $Json[$j]['captain'] = $captain;
                            $vcaptain = 0;
                            if ($team->vicecaptain == $bowler->playerid) {
                                $vcaptain = 1;
                            }
                            $Json[$j]['vicecaptain'] = $vcaptain;
                            if ($bowler->matchplayers_playerid_image != "") {
                                $Json[$j]['image'] = $geturl . 'public/' . $bowler->matchplayers_playerid_image;
                            } else {
                                $Json[$j]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                            }
                            $Json[$j]['status'] = true;
                            $j++;

                        }
                    } else {
                        $Json['success'] = false;
                        $Json['message'] = 'Sorry,no data availables!';
                    }
                }
            } else {
                $Json['success'] = false;
                $Json['message'] = 'Sorry,no data available!';
            }
            return response()->json($Json);
            die;
        }
    }
// live scores of a match //
    public function livescores(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        unset($input['auth_key']);
        $matchkey = $data['matchkey'] = $request->get('matchkey');
        $challengeid = $data['challengeid'] = $request->get('challengeid');
        $Json = array();
        $i = 0;
        /* find the match details */
        $listmatchdetail = DB::connection('mysql')->table('listmatches')->where('matchkey', $request->get('matchkey'))->select('final_status', 'status')->first();
        $findmatchchallenge = DB::connection('mysql')->table('matchchallenges')->where('id', $request->get('challengeid'))->first();
        $price_card_last_rank = DB::connection('mysql')->table('matchpricecards')->where('challenge_id', $request->get('challengeid'))->orderBy('id', 'DESC')->value('max_position') ?? 1;
        // dd($price_card_last_rank);
        if (!empty($listmatchdetail)) {
            if ($listmatchdetail->final_status == 'winnerdeclared') {
                $key = 'joinedteam_' . $matchkey . '_' . $userid;

                $resjoinedteams = DB::connection('mysql')->table('joinedleauges')
                    ->where('joinedleauges.challengeid', $challengeid)
                    ->join('registerusers', 'registerusers.id', '=', 'joinedleauges.userid')
                    ->join('jointeam', 'jointeam.id', '=', 'joinedleauges.teamid')
                    ->leftJoin('finalresults', function ($join) {
                        $join->on('finalresults.joinedid', '=', 'joinedleauges.id');
                    })
                    ->orderBy('jointeam.points', 'DESC')
                    ->select('joinedleauges.challengeid', 'registerusers.team', 'registerusers.email', 'registerusers.image', 'jointeam.teamnumber', 'jointeam.points', 'jointeam.lastpoints', 'jointeam.player_type', 'joinedleauges.id as jid', 'joinedleauges.userid', 'joinedleauges.teamid', 'joinedleauges.refercode', 'finalresults.amount')
                    ->get();
            } else {
                $resjoinedteams = DB::connection('mysql')->table('joinedleauges')->where('joinedleauges.challengeid', $challengeid)
                    ->join('registerusers', 'registerusers.id', '=', 'joinedleauges.userid')
                    ->join('jointeam', 'jointeam.id', '=', 'joinedleauges.teamid')
                    ->orderBy('jointeam.points', 'DESC')
                    ->select(DB::raw('joinedleauges.challengeid'), 'registerusers.team', 'registerusers.image', 'registerusers.email', 'jointeam.teamnumber', 'jointeam.points', 'jointeam.lastpoints', 'jointeam.player_type', 'joinedleauges.id as jid', 'joinedleauges.userid', 'joinedleauges.teamid')->get();

            }
            $gtlastranks = array();
            $pdfname = "";
            $userrank = "";
            $getcurrentrankarray = array();
            $ss = 0;
            $a = $resjoinedteams->toArray();

            if (!empty($a)) {
                foreach ($resjoinedteams as $pleauges) {
                    $gtlastranks[$ss]['lastpoints'] = $pleauges->lastpoints;
                    $gtlastranks[$ss]['userid'] = $pleauges->userid;
                    $gtlastranks[$ss]['userjoinid'] = $pleauges->jid;
                    $getcurrentrankarray[$ss]['points'] = $pleauges->points;
                    $getcurrentrankarray[$ss]['userid'] = $pleauges->userid;
                    $getcurrentrankarray[$ss]['userjoinid'] = $pleauges->jid;
                    $getcurrentrankarray[$ss]['player_type'] = $pleauges->player_type;
                    $ss++;
                }
            }
            $gtlastranks = Helpers::multid_sort($gtlastranks, 'lastpoints');
            if (!empty($gtlastranks)) {
                $getuserlastrank = array();
                $lr = 0;
                $lrsno = 0;
                $uplus = 0;
                foreach ($gtlastranks as $lrnk) {
                    if (in_array($lrnk['lastpoints'], array_column($getuserlastrank, 'points'))) { // search value in the array
                        $lrsno++;
                        $lrsno = $lrsno + $uplus;
                        $uplus = 0;
                    } else {
                        $lrsno++;
                    }
                    $getuserlastrank[$lr]['rank'] = $lrsno;
                    $getuserlastrank[$lr]['points'] = $lrnk['lastpoints'];
                    $getuserlastrank[$lr]['userid'] = $lrnk['userid'];
                    $getuserlastrank[$lr]['userjoinid'] = $lrnk['userjoinid'];
                    $lr++;

                }
            }
            //get current ranks//
            $gtcurranks = Helpers::multid_sort($getcurrentrankarray, 'points');
            if (!empty($gtcurranks)) {
                $getusercurrank = array();
                $cur = 0;
                $currsno = 0;
                $plus = 0;
                foreach ($gtcurranks as $curnk) {
                    if (!in_array($curnk['points'], array_column($getusercurrank, 'points'))) { // search value in the array
                        $currsno++;
                        $currsno = $currsno + $plus;
                        $plus = 0;
                    } else {
                        $plus++;
                    }
                    $getusercurrank[$cur]['rank'] = $currsno;
                    $getusercurrank[$cur]['points'] = $curnk['points'];
                    $getusercurrank[$cur]['userid'] = $curnk['userid'];
                    $getusercurrank[$cur]['userjoinid'] = $curnk['userjoinid'];
                    $cur++;
                }
            }
            if (!empty($a)) {
                $k = 0;
                $userrank = 1;
                $userslistsno = -1;
                $userrankarray = array();
                $pdfname = "";
                foreach ($resjoinedteams as $jointeam) {
                    if ($jointeam->team != "") {
                        $Json[$i]['jointeams'][$k]['teamname'] = ucwords($jointeam->team);
                    } else {
                        $Json[$i]['jointeams'][$k]['teamname'] = Helpers::settings()->project_name ?? '';
                    }
                    $Json[$i]['jointeams'][$k]['teamid'] = $jointeam->teamid;
                    $Json[$i]['jointeams'][$k]['teamnumber'] = $jointeam->teamnumber;

                    $result = DB::connection('mysql')->table('registerusers')->where('id', $jointeam->userid)->select('image')->first();

                    if (empty($result->image) or $result->image == 'null') {

                        $Json[$i]['jointeams'][$k]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                    } else {
                        $Json[$i]['jointeams'][$k]['image'] = 'public/'.$result->image;
                    }
                    if ($jointeam->userid == $userid) {
                        $Json[$i]['team_number_get'] = $jointeam->teamnumber;
                    }
                    $Json[$i]['jointeams'][$k]['points'] = $jointeam->points;
                    $Json[$i]['jointeams'][$k]['player_type'] = $jointeam->player_type;
                    $getuserindexinglast = Helpers::searchByValue($getuserlastrank, 'userjoinid', $jointeam->jid);
                    $getlastrank = $getuserlastrank[$getuserindexinglast]['rank'];
                    $getuserindexingcurent = Helpers::searchByValue($getusercurrank, 'userjoinid', $jointeam->jid);
                    $getcurrentrank = $getusercurrank[$getuserindexingcurent]['rank'];
                    $Json[$i]['jointeams'][$k]['getcurrentrank'] = $getcurrentrank;
                    if ($getcurrentrank <= $price_card_last_rank) {
                        $Json[$i]['jointeams'][$k]['winning_zone'] = 'yes';
                    } else {
                        $Json[$i]['jointeams'][$k]['winning_zone'] = 'no';
                    }
                    if ($getlastrank < $getcurrentrank) {
                        $Json[$i]['jointeams'][$k]['arrowname'] = 'down-arrow';
                    } else if ($getlastrank == $getcurrentrank) {
                        $Json[$i]['jointeams'][$k]['arrowname'] = 'equal-arrow';
                    } else if ($getlastrank > $getcurrentrank) {
                        $Json[$i]['jointeams'][$k]['arrowname'] = 'up-arrow';
                    }
                    $Json[$i]['jointeams'][$k]['userjoinid'] = $jointeam->jid;
                    $Json[$i]['jointeams'][$k]['userid'] = $jointeam->userid;
                    if ($jointeam->userid == $userid) {
                        $Json[$i]['jointeams'][$k]['is_show'] = true;
                        $Json[$i]['jointeams'][$k]['userno'] = $userslistsno;
                        if ($findmatchchallenge->pdf_created == 1) {
                            $pdfname = Config::get('constants.PROJECT_URL') . '/pdffolders/join-challenges-' . $findmatchchallenge->id . '.pdf';
                        }
                        $userrankarray[] = $getcurrentrank;
                    } else {
                        $Json[$i]['jointeams'][$k]['is_show'] = false;
                        $Json[$i]['jointeams'][$k]['userno'] = 0;
                    }
                    if (isset($jointeam->amount)) {
                        if ($jointeam->amount != "" && $jointeam->amount != null) {
                            // $Json[$i]['jointeams'][$k]['winingamount'] = number_format($jointeam->amount,2,'.','');
                            $Json[$i]['jointeams'][$k]['winingamount'] = number_format(floor($jointeam->amount*100) /100,2,'.','');
                        } else {
                            $Json[$i]['jointeams'][$k]['winingamount'] = "";
                        }
                    } else {
                        $Json[$i]['jointeams'][$k]['winingamount'] = "";
                    }
                    $k++;
                }
                array_multisort(array_column($Json[$i]['jointeams'], 'userno'), SORT_ASC, array_column($Json[$i]['jointeams'], 'points'), SORT_DESC, $Json[$i]['jointeams']);
            }
            if (!empty($userrankarray)) {
                $userrank = min($userrankarray);
            }
            $Json[$i]['userrank'] = $userrank;
            $Json[$i]['pdfname'] = $pdfname;
            $Json[$i]['success'] = true;
            $i++;
            return response()->json($Json);
            die;
        } else {
            return response()->json($Json);
            die;
        }
    }


// live scores of a match //
    public function livescores_sdf(Request $request){
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        // $geturl1 = Helpers::geturl();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid =  $data['userid'] = $user->id;
        unset($input['auth_key']);
        $matchkey = $data['matchkey'] =  $request->get('matchkey');
        $challengeid =  $data['challengeid'] =  $request->get('challengeid');
        $Json=array();$i=0;
        /* find the match details */
        $listmatchdetail = DB::connection('mysql')->table('listmatches')->where('matchkey', $request->get('matchkey'))->select('final_status', 'status')->first();
        $findmatchchallenge = DB::connection('mysql')->table('matchchallenges')->where('id', $request->get('challengeid'))->first();
        // $listmatchdetail =Listmatches::where('matchkey',$request->get('matchkey'))->select('final_status','status')->first();
        // $findmatchchallenge =MatchChallenges::where('id',$request->get('challengeid'))->first();
        if(!empty($listmatchdetail)){
            if($listmatchdetail->final_status=='winnerdeclared'){
                $key='joinedteam_'.$matchkey.'_'.$userid;

                $resjoinedteams = DB::select('SELECT `joinedleauges`.`challengeid`, `registerusers`.`team`, `registerusers`.`email`, `registerusers`.`image`, `registerusers`.`gender`, `jointeam`.`teamnumber`, `jointeam`.`points`, `jointeam`.`lastpoints`, `jointeam`.`player_type`, `joinedleauges`.`id` as `jid`, `joinedleauges`.`userid`, `joinedleauges`.`teamid`, `joinedleauges`.`refercode`, `finalresults`.`amount`, `finalresults`.`prize_money`,`finalresults`.`bonus`, (RANK() OVER (ORDER BY `jointeam`.`points` DESC)) AS CURRENTRANK,(RANK() OVER (ORDER BY `jointeam`.`lastpoints` DESC)) AS LASTRANK from `joinedleauges` inner join `registerusers` on `registerusers`.`id` = `joinedleauges`.`userid` inner join `jointeam` on `jointeam`.`id` = `joinedleauges`.`teamid` left join `finalresults` on `finalresults`.`joinedid` = `joinedleauges`.`id` where `joinedleauges`.`challengeid` = '.$challengeid.' ORDER BY `CURRENTRANK` ASC');
            }
            else{
                $resjoinedteams =  DB::select('SELECT `joinedleauges`.`challengeid`, `registerusers`.`team`, `registerusers`.`image`, `registerusers`.`gender`, `registerusers`.`email`, `jointeam`.`teamnumber`, `jointeam`.`points`, `jointeam`.`lastpoints`, `jointeam`.`player_type`, `joinedleauges`.`id` as `jid`, `joinedleauges`.`userid`, `joinedleauges`.`teamid` , (RANK() OVER (ORDER BY `jointeam`.`points` DESC)) AS CURRENTRANK,(RANK() OVER (ORDER BY `jointeam`.`lastpoints` DESC)) AS LASTRANK from `joinedleauges` inner join `registerusers` on `registerusers`.`id` = `joinedleauges`.`userid` inner join `jointeam` on `jointeam`.`id` = `joinedleauges`.`teamid` where `joinedleauges`.`challengeid` = '.$challengeid.' ORDER BY `CURRENTRANK` ASC');
                
            }
            $gtlastranks = array();
            $pdfname = "";
            $userrank = "";
            $getcurrentrankarray = array();
            $ss = 0;
            if(!empty($resjoinedteams)){
                $k=0;$userrank = 1;$userslistsno=-1; $userrankarray = array();$pdfname="";
                foreach($resjoinedteams as $jointeam){
                    if($jointeam->team!=""){
                        $Json[$i]['jointeams'][$k]['teamname'] = ucwords($jointeam->team);
                    }
                    else{
                        $Json[$i]['jointeams'][$k]['teamname'] = "mysure11";
                    }
                    $Json[$i]['jointeams'][$k]['teamid'] = $jointeam->teamid;
                    $Json[$i]['jointeams'][$k]['teamnumber'] = $jointeam->teamnumber;

                    if (empty($result->image) or $result->image == 'null') {

                        $Json[$i]['jointeams'][$k]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                    } else {
                        $Json[$i]['jointeams'][$k]['image'] = 'public/'.$result->image;
                    }
                    if($jointeam->userid==  $userid){
                        $Json[$i]['team_number_get'] = $jointeam->teamnumber;
                    }
                    $Json[$i]['jointeams'][$k]['points'] = $jointeam->points;
                    $Json[$i]['jointeams'][$k]['player_type'] = $jointeam->player_type;
                    // $getuserindexinglast =Helpers::searchByValue($getuserlastrank,'userjoinid',$jointeam->jid);
                    $getlastrank = $jointeam->LASTRANK;
                    // $getuserindexingcurent =Helpers::searchByValue($getusercurrank,'userjoinid',$jointeam->jid);
                    $getcurrentrank = $jointeam->CURRENTRANK;
                    $Json[$i]['jointeams'][$k]['getcurrentrank'] = $getcurrentrank;
                    if($getlastrank<$getcurrentrank){
                        $Json[$i]['jointeams'][$k]['arrowname'] = 'down-arrow';
                    }   
                    else if($getlastrank==$getcurrentrank){
                        $Json[$i]['jointeams'][$k]['arrowname'] = 'equal-arrow';
                    }
                    else if($getlastrank>$getcurrentrank){
                        $Json[$i]['jointeams'][$k]['arrowname'] = 'up-arrow';
                    }
                    $Json[$i]['jointeams'][$k]['userjoinid'] = $jointeam->jid;
                    $Json[$i]['jointeams'][$k]['userid'] = $jointeam->userid;
                    if($jointeam->userid==$userid){
                        $Json[$i]['jointeams'][$k]['is_show'] = true;
                        $Json[$i]['jointeams'][$k]['userno'] =$userslistsno;
                        if($findmatchchallenge->pdf_created==1 ){
                            $pdfname = Config::get('constants.PROJECT_URL').'/pdffolders/join-challenges-'.$findmatchchallenge->id.'.pdf';
                        }
                        $userrankarray[] = $getcurrentrank;
                    }
                    else{
                        $Json[$i]['jointeams'][$k]['is_show'] = false;
                        $Json[$i]['jointeams'][$k]['userno'] = 0;
                    }
                    // if($findmatchchallenge->contest_winning_type=='price'){
                        
                        if(isset($jointeam->amount)){
                            if($jointeam->amount!="" && $jointeam->amount!=null){
                                if($jointeam->bonus>0){
                                    $ttl_amount =  $jointeam->amount+$jointeam->bonus;
                                    $Json[$i]['jointeams'][$k]['winingamount']  = round($ttl_amount).'B.';
                                }else{
                                    $Json[$i]['jointeams'][$k]['winingamount']  = round($jointeam->amount);
                                }
                            }else{
                                $Json[$i]['jointeams'][$k]['winingamount']="";
                            }
                        }else{
                            $Json[$i]['jointeams'][$k]['winingamount']="";
                        }
                    // }else{
                    //     if($jointeam->prize_money!='' && $jointeam->prize_money!=null){
                    //         $Json[$i]['jointeams'][$k]['winingamount']  = $jointeam->prize_money;
                    //     }else{
                    //         $Json[$i]['jointeams'][$k]['winingamount']  ='';
                    //     }
                        
                    // }
                    $k++;
                }
                array_multisort(array_column($Json[$i]['jointeams'],'userno'),SORT_ASC,array_column($Json[$i]['jointeams'],'points'),SORT_DESC,$Json[$i]['jointeams']);
            }
            if(!empty($userrankarray)){
                $userrank = min($userrankarray);
            }
            $Json[$i]['userrank'] = $userrank;
            $Json[$i]['pdfname'] = $pdfname;
            $Json[$i]['status'] = 1;
            $Json[$i]['success'] = true;
            $i++;
            return response()->json($Json);
            die;
        }else{
            return response()->json($Json);
            die; 
        }
    }

    public function getlivescores(Request $request)
    {
        $data = $request->all();
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        $geturl = Helpers::geturl();
        $users = Helpers::isAuthorize($request);
        $type = $request->get('type');
        $userid = $data['userid'] = $users->id;
        unset($data['auth_key']);
        $matchkey = $data['matchkey'] = $request->get('matchkey');
        $Json = array();
        if (!empty($matchkey)) {

            $data1 = DB::connection('mysql')->table('matchruns')->where('matchkey', $matchkey)->first();
            if (!empty($data1)) {
                $textover1 = 0;
                $textover2 = 0;
                $textrun1 = 0;
                $textrun2 = 0;
                $textwicket1 = 0;
                $textwicket2 = 0;
                $over1 = explode(',', $data1->overs1);
                $over2 = explode(',', $data1->overs2);
                $wicket1 = explode(',', $data1->wickets1);
                $wicket2 = explode(',', $data1->wickets2);
                $runs1 = explode(',', $data1->runs1);
                $runs2 = explode(',', $data1->runs2);
                if (!empty($over1[1])) {$textover1 = $over1[1];}
                if (!empty($over2[1])) {$textover2 = $over2[1];}
                if (!empty($runs1[1])) {$textrun1 = $runs1[1];}
                if (!empty($runs2[1])) {$textrun2 = $runs2[1];}
                if (!empty($wicket1[1])) {$textwicket1 = $wicket1[1];}
                if (!empty($wicket2[1])) {$textwicket2 = $wicket2[1];}
                $Json['Team1'] = $data1->teams1;
                $Json['Team2'] = $data1->teams2;
                $Json['Team1_Totalovers1'] = $over1[0];
                $Json['Team1_Totalovers2'] = $textover1;
                $Json['Team1_Totalruns1'] = $runs1[0];
                $Json['Team1_Totalruns2'] = $textrun1;
                $Json['Team1_Totalwickets1'] = $wicket1[0];
                $Json['Team1_Totalwickets2'] = $textwicket1;
                $Json['Team2_Totalwickets1'] = $wicket2[0];
                $Json['Team2_Totalwickets2'] = $textwicket2;
                $Json['Team2_Totalovers1'] = $over2[0];
                $Json['Team2_Totalovers2'] = $textover2;
                $Json['Team2_Totalruns1'] = $runs2[0];
                $Json['Team2_Totalruns2'] = $textrun2;
                $Json['Winning_Status'] = $data1->winning_status;
            } else {
                if (!empty($data1->teams1)) {
                    $Json['Team1'] = $data1->teams1;
                } else {
                    $Json['Team1'] = '';
                }
                if (!empty($data1->teams2)) {
                    $Json['Team2'] = $data1->teams2;
                } else {
                    $Json['Team2'] = '';
                }
                $Json['Team1_Totalovers1'] = 0;
                $Json['Team1_Totalovers2'] = 0;
                $Json['Team1_Totalruns1'] = 0;
                $Json['Team1_Totalruns2'] = 0;
                $Json['Team1_Totalwickets1'] = 0;
                $Json['Team1_Totalwickets2'] = 0;
                $Json['Team2_Totalwickets1'] = 0;
                $Json['Team2_Totalwickets2'] = 0;
                $Json['Team2_Totalovers1'] = 0;
                $Json['Team2_Totalovers2'] = 0;
                $Json['Team2_Totalruns1'] = 0;
                $Json['Team2_Totalruns2'] = 0;
                $Json['Winning_Status'] = '';
            }
        }
        return response()->json($Json);
        die;
    }

    public function getuserjoined(Request $request)
    {
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $challengeid = $request->get('challengeid');
            $Json = array();
            $i = 0;
            $joinedusers = DB::connection('mysql')->table('joinedleauges')
                ->join('registerusers', 'registerusers.id', '=', 'joinedleauges.userid')
                ->join('jointeam', 'jointeam.id', '=', 'joinedleauges.teamid')->where('joinedleauges.challengeid', $challengeid)
                ->select('jointeam.teamnumber', 'joinedleauges.id as jid', 'joinedleauges.teamid', 'registerusers.id as rid', 'registerusers.team', 'registerusers.image', 'registerusers.username', 'registerusers.email')
                ->get();
            $userno = 0;
            if (!empty($joinedusers)) {
                foreach ($joinedusers as $join) {
                    if ($join->rid == $user->id) {
                        $userno = 1;
                    }
                    $Json[$i]['username'] = $join->username;
                    $Json[$i]['teamid'] = $join->teamid;
                    $Json[$i]['teamnumber'] = $join->teamnumber;
                    $Json[$i]['teamname'] = $join->team;
                    $Json[$i]['joinid'] = $join->jid;
                    $Json[$i]['email'] = $join->email;
                    $result = DB::connection('mysql')->table('registerusers')->where('id', $user->id)->select('image')->first();
                    if (empty($result)) {
                        $Json[$i]['image'] = $geturl . 'public/' . Helpers::settings()->user_image ?? '';
                    } else {
                        $Json[$i]['image'] = $geturl . 'public/' . $result->image;
                    }
                    $Json[$i]['userno'] = $userno;

                    $i++;
                }
            }
            array_multisort(array_column($Json, 'userno'), SORT_DESC, $Json);
            return response()->json($Json);
            die;
        }
    }

    public function fantasyscorecards(Request $request)
    {
        $input = $request->all();
        if ($request->get('matchkey')) {
            $getmatchkey = $request->get('matchkey');
            $matchplayers = DB::connection('mysql')->table('matchplayers')
                ->where('matchkey', $getmatchkey)
                ->join('players', 'players.id', '=', 'matchplayers.playerid')
                ->leftjoin('teams', 'teams.id', '=', 'players.team')
                ->select('players.player_name as playername', 'matchplayers.playerid', 'players.image as playerimage', 'players.id as plid', 'teams.team as teamname', 'teams.short_name')->get();
            $i = 0;
            $findmatchname = DB::connection('mysql')->table('listmatches')->where('matchkey', $getmatchkey)->select('name')->first();
            $Json = array();

            if (!empty($findmatchname)) {
                if (!empty($matchplayers)) {
                    $i = 0;
                    foreach ($matchplayers as $player) {

                        $findresult_points = DB::connection('mysql')->table('result_points')
                            ->leftjoin('result_matches', 'result_matches.id', '=', 'result_points.resultmatch_id')
                            ->leftjoin('listmatches', 'listmatches.matchkey', '=', 'result_points.matchkey')
                            ->where('result_points.playerid', $player->playerid)
                            ->select(
                                DB::raw('sum(result_points.fours) as totalfours'), DB::raw('sum(result_points.sixs) as totalsixs'), DB::raw('sum(result_points.runs) as totalruns'), DB::raw('sum(result_points.strike_rate) as totalstrike_rate'), DB::raw('sum(result_points.century) as totalcentury'), DB::raw('sum(result_points.halfcentury) as totalhalfcentury'), DB::raw('sum(result_points.wickets) as totalwickets'), DB::raw('sum(result_points.maidens) as totalmaidens'), DB::raw('sum(result_points.economy_rate) as totaleconomy_rate'), DB::raw('sum(result_points.runouts) as totalrunouts'), DB::raw('sum(result_points.catch) as totalcatch'), DB::raw('sum(result_points.stumping) as totalstumping'), DB::raw('sum(result_points.thrower) as totalthrower'), DB::raw('sum(result_points.hitter) as totalhitter'), DB::raw('sum(result_points.bonus) as totalbonus'), DB::raw('sum(result_points.negative) as totalnegative'), DB::raw('sum(result_points.total) as totaltotal'), 'result_points.matchkey', 'result_matches.starting11', 'result_points.startingpoints', 'listmatches.name', 'result_matches.innings', 'result_matches.batting', 'result_matches.bowling', 'result_matches.duck', 'result_matches.hitter', 'result_matches.thrower')
                            ->where('result_points.matchkey', $getmatchkey)
                            ->where('result_matches.starting11', 1)
                            ->orderBy('result_matches.innings', 'ASC')
                            ->orderBy('result_points.total', 'ASC')
                            ->first();

                        if ($findresult_points->starting11 == 1) {
                            // $finduselectthisplayer = DB::connection('mysql')->table('jointeam')->where('matchkey', $getmatchkey)->select('players')->get();
                            // $countlenght = count($finduselectthisplayer);
                            // $allplayers = array();
                            // $countplayer = 0;
                            // if (!empty($finduselectthisplayer)) {
                            //     foreach ($finduselectthisplayer as $pp) {
                            //         $fplayers = array();
                            //         $fplayers = explode(',', $pp->players);
                            //         foreach ($fplayers as $fpl) {
                            //             $allplayers[] = $fpl;
                            //         }
                            //     }
                            // }
                            // if (!empty($allplayers)) {
                            //     foreach ($allplayers as $pl) {
                            //         if ($pl == $player->plid) {
                            //             $countplayer++;
                            //         }
                            //     }
                            // }
                            // if ($countplayer > 0) {
                            //     $countper = round(($countplayer / $countlenght) * 100, 2);
                            // } else {
                            //     $countper = 0;
                            // }
                            // $Json[$i]['selectper'] = $countper . '%';
                            $Json[$i]['selectper'] = '0%';
                            //   if($player->batting==1 || $player->bowling==1){
                            $Json[$i]['playername'] = $player->playername;
                            if (!empty($player->playerimage)) {
                                $Json[$i]['playerimage'] = asset('/' . $player->playerimage);
                            } else {
                                $Json[$i]['playerimage'] = asset('/' . Helpers::settings()->player_image ?? '');
                            }
                            $Json[$i]['matchname'] = $findresult_points->name;
                            $Json[$i]['duck'] = $findresult_points->duck;
                            $number = $findresult_points->innings;
                            $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
                            if (($number % 100) >= 11 && ($number % 100) <= 13) {
                                $abbreviation = $number . 'th';
                            } else {
                                $abbreviation = $number . $ends[$number % 10];
                            }

                            $Json[$i]['innings'] = ucwords($player->teamname) . ' ' . $abbreviation . ' inning';
                            $Json[$i]['startingpoints'] = $findresult_points->startingpoints;
                            $Json[$i]['runs'] = $findresult_points->totalruns;
                            $Json[$i]['fours'] = $findresult_points->totalfours;
                            $Json[$i]['sixs'] = $findresult_points->totalsixs;
                            $Json[$i]['strike_rate'] = $findresult_points->totalstrike_rate;
                            $Json[$i]['century'] = $findresult_points->totalhalfcentury + $findresult_points->totalcentury;
                            $Json[$i]['wickets'] = $findresult_points->totalwickets;
                            $Json[$i]['maidens'] = $findresult_points->totalmaidens;
                            $Json[$i]['economy_rate'] = $findresult_points->totaleconomy_rate;
                            $Json[$i]['catch'] = $findresult_points->totalcatch;
                            $Json[$i]['catchpoints'] = $findresult_points->totalcatch;
                            $Json[$i]['stumping'] = $findresult_points->totalstumping + $findresult_points->totalthrower + $findresult_points->totalhitter;
                            $Json[$i]['bonus'] = $findresult_points->totalbonus;
                            $Json[$i]['negative'] = $findresult_points->totalnegative;
                            $Json[$i]['total'] = $findresult_points->totaltotal;
                            $Json[$i]['matchname'] = $findmatchname->name;
                            $Json[$i]['matchshortname'] = $player->short_name;
                            $Json[$i]['hitter'] = $findresult_points->hitter;
                            $Json[$i]['thrower'] = $findresult_points->thrower;
                            $Json[$i]['success'] = true;
                            $i++;
                        }

                    }
                } else {
                    $Json[0]['success'] = false;
                }
            } else {
                $Json[0]['success'] = false;
                $i++;
            }
            return response()->json($Json);
            die;
        }
    }

    public function getjointeamplayers(Request $request)
    {
        $data = $request->all();
        $geturl = Helpers::geturl();
        $users = Helpers::isAuthorize($request);
        $teamid = $request->get('teamid');
        $matchkey = $request->get('matchkey');
        $jointeam = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->where('id', $teamid)->first();
        if (!empty($jointeam)) {
            $Json = [];
            $players = explode(',', $jointeam->players);
            $i = 0;
            foreach ($players as $player) {
                $resultpoints = DB::connection('mysql')->table('result_points')
                    ->where('result_points.matchkey', $matchkey)
                    ->where('result_points.playerid', $player)
                    ->join('matchplayers', 'matchplayers.playerid', '=', 'result_points.playerid')->join('players', 'players.id', '=', 'matchplayers.playerid')
                    ->select('result_points.*', 'players.image as playersimage', 'matchplayers.credit', 'matchplayers.name', 'matchplayers.points')
                    ->first();

                if (!empty($resultpoints)) {
                    $Json[$i]['player_name'] = $resultpoints->name;
                    if ($jointeam->captain == $player) {
                        $Json[$i]['captain'] = 1;
                    } else {
                        $Json[$i]['captain'] = 0;
                    }
                    if ($jointeam->vicecaptain == $player) {
                        $Json[$i]['vicecaptain'] = 1;
                    } else {
                        $Json[$i]['vicecaptain'] = 0;
                    }
                    if (!empty($resultpoints->playersimage)) {
                        $Json[$i]['playerimage'] = asset('/' . $resultpoints->playersimage);
                    } else {
                        $Json[$i]['playerimage'] = asset('/' . Helpers::settings()->player_image ?? '');
                    }
                    $Json[$i]['credit'] = $resultpoints->credit;
                    $Json[$i]['playerid'] = $resultpoints->playerid;
                    $Json[$i]['startingpoints'] = $resultpoints->startingpoints;
                    $Json[$i]['runs'] = $resultpoints->runs;
                    $Json[$i]['fours'] = $resultpoints->fours;
                    $Json[$i]['sixs'] = $resultpoints->sixs;
                    $Json[$i]['strike_rate'] = $resultpoints->strike_rate;
                    $Json[$i]['century'] = $resultpoints->century;
                    $Json[$i]['halfcentury'] = $resultpoints->halfcentury;
                    $Json[$i]['thirtypoints'] = $resultpoints->thirtypoints;
                    $Json[$i]['wickets'] = $resultpoints->wickets;
                    $Json[$i]['lbw_bowled'] = $resultpoints->wicketbonuspoint;
                    $Json[$i]['maidens'] = $resultpoints->maidens;
                    $Json[$i]['economy_rate'] = $resultpoints->economy_rate;
                    // $Json[$i]['runouts'] = $resultpoints->runouts;
                    $Json[$i]['catch_points'] = $resultpoints->catch;
                    $Json[$i]['stumping'] = $resultpoints->stumping;
                    $Json[$i]['thrower'] = $resultpoints->thrower;
                    $Json[$i]['hitter'] = $resultpoints->hitter;
                    $Json[$i]['runout'] = $resultpoints->thrower + $resultpoints->hitter;
                    $Json[$i]['bonus'] = $resultpoints->bonus;
                    $Json[$i]['negative'] = $resultpoints->negative;
                    $Json[$i]['total'] = $resultpoints->total;

                    // $finduselectthisplayer = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->select('players')->get();
                    // $countlenght = count($finduselectthisplayer);
                    // $allplayers = array();
                    // $countplayer = 0;
                    // if (!empty($finduselectthisplayer)) {
                    //     foreach ($finduselectthisplayer as $pp) {
                    //         $fplayers = array();
                    //         $fplayers = explode(',', $pp->players);
                    //         foreach ($fplayers as $fpl) {
                    //             $allplayers[] = $fpl;
                    //         }
                    //     }
                    // }
                    // if (!empty($allplayers)) {
                    //     foreach ($allplayers as $pl) {
                    //         if ($pl == $player) {
                    //             $countplayer++;
                    //         }
                    //     }
                    // }
                    // if ($countplayer > 0) {
                    //     $countper = round(($countplayer / $countlenght) * 100, 2);
                    // } else {
                    //     $countper = 0;
                    // }
                    // $Json[$i]['selectper'] = $countper . '%';
                    $Json[$i]['selectper'] = '0%';
                    $i++;
                }

            }
            return response()->json($Json);
        }
    }

    public function getjointeamplayers1(Request $request)
    {
        $teamid = $request->get('teamid');
        $matchkey = $request->get('matchkey');
        $jointeam = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->where('id', $teamid)->first();
        $Json = [];
        if (!empty($jointeam)) {
            $players = explode(',', $jointeam->players);
            $i = 0;
            foreach ($players as $player) {
                $resultpoints = DB::connection('mysql')->table('result_points')
                    ->where('result_points.matchkey', $matchkey)
                    ->leftjoin('result_matches', 'result_matches.id', '=', 'result_points.resultmatch_id')
                    ->where('result_points.startingpoints', '<>', 0)
                    ->where('result_points.playerid', $player)
                    ->join('matchplayers', 'matchplayers.playerid', '=', 'result_points.playerid')->join('players', 'players.id', '=', 'matchplayers.playerid')
                    ->select(
                        DB::raw('sum(result_points.fours) as totalfours'), DB::raw('sum(result_points.sixs) as totalsixs'), DB::raw('sum(result_points.runs) as totalruns'), DB::raw('sum(result_points.strike_rate) as totalstrike_rate'), DB::raw('sum(result_points.century) as totalcentury'), DB::raw('sum(result_points.halfcentury) as totalhalfcentury'), DB::raw('sum(result_points.wickets) as totalwickets'), DB::raw('sum(result_points.maidens) as totalmaidens'), DB::raw('sum(result_points.economy_rate) as totaleconomy_rate'), DB::raw('sum(result_points.runouts) as totalrunouts'), DB::raw('sum(result_points.catch) as totalcatch'), DB::raw('sum(result_points.stumping) as totalstumping'), DB::raw('sum(result_points.thrower) as totalthrower'), DB::raw('sum(result_points.hitter) as totalhitter'), DB::raw('sum(result_points.bonus) as totalbonus'), DB::raw('sum(result_points.negative) as totalnegative'), DB::raw('sum(result_points.total) as totaltotal'), 'players.image as playersimage', 'result_points.playerid', 'result_points.startingpoints', 'matchplayers.credit', 'matchplayers.name', 'matchplayers.points', 'players.*', 'matchplayers.*'
                    )
                    ->first();
                if (!empty($resultpoints)) {
                    $Json[$i]['player_name'] = $resultpoints->name;
                    if ($jointeam->captain == $player) {
                        $Json[$i]['captain'] = 1;
                    } else {
                        $Json[$i]['captain'] = 0;
                    }
                    if ($jointeam->vicecaptain == $player) {
                        $Json[$i]['vicecaptain'] = 1;
                    } else {
                        $Json[$i]['vicecaptain'] = 0;
                    }
                    if (!empty($resultpoints->playersimage)) {
                        $Json[$i]['playerimage'] = asset('uploads/players/' . $resultpoints->playersimage);
                    } else {
                        $Json[$i]['playerimage'] = asset('/' . Helpers::settings()->player_image ?? '');
                    }
                    $Json[$i]['credit'] = $resultpoints->credit;
                    $Json[$i]['playerid'] = $resultpoints->playerid;
                    $Json[$i]['startingpoints'] = $resultpoints->startingpoints;
                    $Json[$i]['runs'] = $resultpoints->totalruns;
                    $Json[$i]['fours'] = $resultpoints->totalfours;
                    $Json[$i]['sixs'] = $resultpoints->totalsixs;
                    $Json[$i]['strike_rate'] = $resultpoints->totalstrike_rate;
                    $Json[$i]['century'] = $resultpoints->totalcentury;
                    $Json[$i]['halfcentury'] = $resultpoints->totalhalfcentury;
                    $Json[$i]['wickets'] = $resultpoints->totalwickets;
                    $Json[$i]['maidens'] = $resultpoints->totalmaidens;
                    $Json[$i]['economy_rate'] = $resultpoints->totaleconomy_rate;
                    $Json[$i]['runouts'] = $resultpoints->totalrunouts;
                    $Json[$i]['catch_points'] = $resultpoints->totalcatch;
                    $Json[$i]['stumping'] = $resultpoints->totalstumping;
                    $Json[$i]['thrower'] = $resultpoints->totalthrower;
                    $Json[$i]['hitter'] = $resultpoints->totalhitter;
                    $Json[$i]['bonus'] = $resultpoints->totalbonus;
                    $Json[$i]['negative'] = $resultpoints->totalnegative;
                    $Json[$i]['total'] = $resultpoints->totaltotal;

                    // $finduselectthisplayer = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->select('players')->get();
                    // $countlenght = count($finduselectthisplayer);
                    // $allplayers = array();
                    // $countplayer = 0;
                    // if (!empty($finduselectthisplayer)) {
                    //     foreach ($finduselectthisplayer as $pp) {
                    //         $fplayers = array();
                    //         $fplayers = explode(',', $pp->players);
                    //         foreach ($fplayers as $fpl) {
                    //             $allplayers[] = $fpl;
                    //         }
                    //     }
                    // }
                    // if (!empty($allplayers)) {
                    //     foreach ($allplayers as $pl) {
                    //         if ($pl == $player) {
                    //             $countplayer++;
                    //         }
                    //     }
                    // }
                    // if ($countplayer > 0) {
                    //     $countper = round(($countplayer / $countlenght) * 100, 2);
                    // } else {
                    //     $countper = 0;
                    // }
                    $Json[$i]['selectper'] = '0%';
                    $i++;

                } else {
                    return response()->json($Json);die;
                }

            }
            return response()->json($Json);die;
        } else {
            return response()->json($Json);die;
        }

    }

    public function dreamteam(Request $request)
    {
        $data = $request->all();
        $geturl = Helpers::geturl();
        $users = Helpers::isAuthorize($request);
        if (!empty($users)) {
            $matchkey = $request->get('matchkey');
            $alljoinedteam = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->select('points')->get();

            $allteam = array();
            foreach ($alljoinedteam as $value) {
                $allteam[] = $value->points;
            }

            if (!empty($allteam)) {
                $point = max($allteam);
            } else {
                $point = 0;
            }

            $jointeam = DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->where('points', $point)->select('players', 'captain', 'vicecaptain')->get();
            $Json = array();
            foreach ($jointeam as $team) {
                $playersarr = explode(',', $team->players);

                $boplayerdetails = DB::connection('mysql')->table('matchplayers')
                    ->join('players', 'players.id', 'matchplayers.playerid')
                    ->join('teams', 'teams.id', 'players.team')
                    ->where('matchplayers.matchkey', $matchkey)
                    ->whereIn('matchplayers.playerid', $playersarr)
                    ->select('matchplayers.*', 'players.player_name as matchplayers_playerid_player_name', 'players.image as matchplayers_playerid_image', 'players.team as matchplayers_playerid_team')
                    ->get();
                if (count($boplayerdetails) > 0) {
                    $j = 0;
                    foreach ($boplayerdetails as $bowler) {
                        $findmatch = DB::connection('mysql')->table('listmatches')
                            ->join('teams as t1', 't1.id', '=', 'listmatches.team1')
                            ->join('teams as t2', 't2.id', '=', 'listmatches.team2')
                            ->where('listmatches.matchkey', $matchkey)
                            ->select('listmatches.*', 't1.short_name as teamdata1_short_name', 't2.short_name as teamdata2_short_name')
                            ->first();
                        $Json[$j]['id'] = $bowler->playerid;
                        $Json[$j]['name'] = $bowler->matchplayers_playerid_player_name;
                        $Json[$j]['isSelected'] = false;
                        $Json[$j]['role'] = $bowler->role;
                        $Json[$j]['credit'] = $bowler->credit;
                        $Json[$j]['points'] = $bowler->points;
                        if (!empty($findmatch)) {
                            if ($bowler->matchplayers_playerid_team == $findmatch->team1) {
                                $Json[$j]['team'] = 'team1';
                                $Json[$j]['teamname'] = $findmatch->teamdata1_short_name;
                            } else {
                                $Json[$j]['team'] = 'team2';
                                $Json[$j]['teamname'] = $findmatch->teamdata2_short_name;
                            }
                        } else {
                            $Json[$j]['team'] = '';
                            $Json[$j]['teamname'] = '';

                        }
                        $captain = 0;
                        if ($team->captain == $bowler->playerid) {
                            $captain = 1;
                        }
                        $Json[$j]['captain'] = $captain;
                        $vcaptain = 0;
                        if ($team->vicecaptain == $bowler->playerid) {
                            $vcaptain = 1;
                        }
                        $Json[$j]['vicecaptain'] = $vcaptain;
                        if ($bowler->matchplayers_playerid_image != "") {
                            $Json[$j]['image'] = $geturl . 'public/' . $bowler->matchplayers_playerid_image;
                        } else {
                            $Json[$j]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                        }
                        $Json[$j]['status'] = 1;
                        $j++;

                    }
                } else {
                    $Json['status'] = 0;
                    $Json['msg'] = 'Sorry,no data available!';
                }
            }
            return response($Json);
            die;
        }
    }

    public function teamcompare(Request $request)
    {
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $user_id = $user->id;
        $teamid1 = $request->get('teamid1');
        $teamid2 = $request->get('teamid2');
        $matchkey = $request->get('matchkey');
        $oppnentid = $request->get('oppnentid');
        $challengeid = $request->get('challengeid');
        $Json = array();
        if ($request->isMethod('post')) {
            $selfuser = DB::connection('mysql')->table('registerusers')->where('id', $user_id)->first();
            $getleaugesdata_1 = DB::connection('mysql')->table('joinedleauges')->where('teamid', $teamid1)->where('userid', $user_id)->where('challengeid', $challengeid)->where('matchkey', $matchkey)->first();
            if (!empty($getleaugesdata_1)) {
                $getleaugesdata_2 = DB::connection('mysql')->table('joinedleauges')->where('teamid', $teamid2)->where('userid', $oppnentid)->where('challengeid', $challengeid)->where('matchkey', $matchkey)->first();

                if (!empty($getleaugesdata_2)) {
                    $teams_1 = DB::connection('mysql')->table('jointeam')->where('id', $getleaugesdata_1->teamid)->where('matchkey', $matchkey)->first();
                    // echo"<pre>";print_r($teams_1);
                    if (!empty($teams_1)) {
                        $teams_2 = DB::connection('mysql')->table('jointeam')->where('id', $getleaugesdata_2->teamid)->where('matchkey', $matchkey)->first();
                        $plys11 = 0;
                        $u = 0;
                        $c = 0;
                        $vc = 0;
                        if (!empty($teams_2)) {
                            $getusedetails = DB::connection('mysql')->table('registerusers')->where('id', $oppnentid)->first();
                            //User data
                            if (!empty($getusedetails)) {
                                if ($getusedetails->image != "") {
                                    if (substr($getusedetails->image, 0, 4) == "http") {
                                        $img = (!empty($getusedetails->image)) ? $getusedetails->image : $geturl . 'public/user_image.png';
                                    } else {
                                        $img = (!empty($getusedetails->image)) ? $getusedetails->image : $geturl . 'public/user_image.png';
                                    }
                                } else {
                                    $img = (!empty($getusedetails->image)) ? $getusedetails->image : $geturl . 'public/user_image.png';
                                }

                                //changes

                                $Json['Users'][$u]['userteam_name_1'] = ($selfuser->team != "") ? $selfuser->team : 0;
                                $Json['Users'][$u]['userimage_1'] = ($selfuser->image != "") ? $selfuser->image : $geturl . "public/user_image.png";
                                $Json['Users'][$u]['user_id_1'] = $user_id;
                                $Json['Users'][$u]['Matchpoints_1'] = $teams_1->points;
                                $Json['Users'][$u]['userteam_name_2'] = ($getusedetails->team != "") ? $getusedetails->team : 0;
                                $Json['Users'][$u]['userimage_2'] = $img;
                                $Json['Users'][$u]['user_id_2'] = $getusedetails->id;
                                $Json['Users'][$u]['Matchpoints_2'] = $teams_2->points;
                            }
                            //Captain data
                            $capt_2 = DB::connection('mysql')->table('players')->where('players.id', $teams_2->captain)->join('teams', 'teams.id', 'players.team')->join('matchplayers', 'matchplayers.playerid', 'players.id')->where('matchkey', $matchkey)->select('players.*', 'teams.short_name', 'matchplayers.points as p_points')->first();

                            //change
                            $Json['Captain'][$c]['playerid_2'] = $capt_2->id;
                            $Json['Captain'][$c]['player_2'] = $capt_2->player_name;
                            $Json['Captain'][$c]['team_2'] = $capt_2->short_name;
                            $Json['Captain'][$c]['role_2'] = $capt_2->role;
                            $Json['Captain'][$c]['points_2'] = $capt_2->p_points;
                            $Json['Captain'][$c]['userid_2'] = $teams_2->userid;
                            $Json['Captain'][$c]['player_image_2'] = ($capt_2->image != "") ? $geturl . 'public/players/' . $capt_2->image : $geturl . "public/player_image.png";

                            $capt_1 = DB::connection('mysql')->table('players')->where('players.id', $teams_1->captain)->join('teams', 'teams.id', 'players.team')->join('matchplayers', 'matchplayers.playerid', 'players.id')->where('matchkey', $matchkey)->select('players.*', 'teams.short_name', 'matchplayers.points as p_points', 'matchplayers.playingstatus')->first();

                            //change
                            $Json['Captain'][$c]['playerid_1'] = $capt_1->id;
                            $Json['Captain'][$c]['player_1'] = $capt_1->player_name;
                            $Json['Captain'][$c]['team_1'] = $capt_1->short_name;
                            $Json['Captain'][$c]['role_1'] = $capt_1->role;
                            $Json['Captain'][$c]['points_1'] = $capt_1->p_points;
                            $Json['Captain'][$c]['userid_1'] = $teams_1->userid;
                            $Json['Captain'][$c]['player_image_1'] = ($capt_1->image != "") ? $geturl . 'public/players/' . $capt_1->image : $geturl . "public/player_image.png";
                            //Vicecaptain data
                            $capt_2 = DB::connection('mysql')->table('players')->where('players.id', $teams_2->vicecaptain)->join('teams', 'teams.id', 'players.team')->join('matchplayers', 'matchplayers.playerid', 'players.id')->where('matchkey', $matchkey)->select('players.*', 'teams.short_name', 'matchplayers.points as ppoints')->first();

                            //change
                            $Json['Vicecaptain'][$vc]['playerid_2'] = $capt_2->id;
                            $Json['Vicecaptain'][$vc]['player_2'] = $capt_2->player_name;
                            $Json['Vicecaptain'][$vc]['team_2'] = $capt_2->short_name;
                            $Json['Vicecaptain'][$vc]['role_2'] = $capt_2->role;
                            $Json['Vicecaptain'][$vc]['points_2'] = $capt_2->ppoints;
                            $Json['Vicecaptain'][$vc]['userid_2'] = $teams_2->userid;
                            $Json['Vicecaptain'][$vc]['player_image_2'] = ($capt_2->image != "") ? $geturl . 'public/players/' . $capt_2->image : $geturl . "public/player_image.png";

                            $capt_1 = DB::connection('mysql')->table('players')->where('players.id', $teams_1->vicecaptain)->join('teams', 'teams.id', 'players.team')->join('matchplayers', 'matchplayers.playerid', 'players.id')->where('matchkey', $matchkey)->select('players.*', 'teams.short_name', 'matchplayers.points as ppoints')->first();

                            //chamge
                            $Json['Vicecaptain'][$vc]['playerid_1'] = $capt_1->id;
                            $Json['Vicecaptain'][$vc]['player_1'] = $capt_1->player_name;
                            $Json['Vicecaptain'][$vc]['team_1'] = $capt_1->short_name;
                            $Json['Vicecaptain'][$vc]['role_1'] = $capt_1->role;
                            $Json['Vicecaptain'][$vc]['points_1'] = $capt_1->ppoints;
                            $Json['Vicecaptain'][$vc]['userid_1'] = $teams_1->userid;
                            $Json['Vicecaptain'][$vc]['player_image_1'] = ($capt_1->image != "") ? $geturl . 'public/players/' . $capt_1->image : $geturl . "public/player_image.png";

                            //Common Player
                            $players_1 = explode(',', $teams_1->players); //users  team
                            $players_2 = explode(',', $teams_2->players); //oppnent  team

                            $cp_vc_1 = [$teams_1->captain, $teams_1->vicecaptain];
                            $get_p1 = array_diff($players_1, $cp_vc_1);

                            $cp_vc_2 = [$teams_2->captain, $teams_2->vicecaptain];
                            $get_p2 = array_diff($players_2, $cp_vc_2);

                            $result = array_intersect($get_p1, $get_p2);
                            if (!empty($result)) {
                                $z = 0;
                                $x = 0;
                                foreach ($result as $res) {
                                    $getplyrname = DB::connection('mysql')->table('players')->where('players.id', $res)->join('teams', 'teams.id', 'players.team')->join('matchplayers', 'matchplayers.playerid', 'players.id')->where('matchkey', $matchkey)->select('players.*', 'teams.short_name', 'matchplayers.points as ppoints')->first();
                                    // $z = ($x!=0)?($z+1):$x;
                                    if (in_array($res, $players_2)) {
                                        $Json['Common'][$z]['playerid'] = $getplyrname->id;
                                        $Json['Common'][$z]['player'] = $getplyrname->player_name;
                                        $Json['Common'][$z]['team'] = $getplyrname->short_name;
                                        $Json['Common'][$z]['role'] = $getplyrname->role;
                                        $Json['Common'][$z]['points'] = $getplyrname->ppoints;
                                        $Json['Common'][$z]['player_image'] = ($getplyrname->image != "") ? $geturl . 'public/' . $getplyrname->image : ($geturl . 'public/' . Helpers::settings()->player_image ?? '');
                                    }
                                    $z++;
                                }
                            }

                            $result_2 = array_diff($get_p2, $get_p1);
                            $result_1 = array_diff($get_p1, $get_p2);
                            if (!empty($result_2)) {
                                if (!empty($result_1)) {
                                    $y = 0;
                                    $e = 0;
                                    $result_2 = array_values($result_2);
                                    $result_1 = array_values($result_1);
                                    foreach (array_values($result_2) as $res22) {
                                        // foreach($result_1 as $res11){

                                        $getplyrnameunq = DB::connection('mysql')->table('players')->where('players.id', $res22)->join('teams', 'teams.id', 'players.team')->join('matchplayers', 'matchplayers.playerid', 'players.id')->where('matchkey', $matchkey)->select('players.*', 'teams.short_name', 'matchplayers.points as ppoints')->first();

                                        $getplyrnameunq11 = DB::connection('mysql')->table('players')->where('players.id', $result_1[$y])->join('teams', 'teams.id', 'players.team')->join('matchplayers', 'matchplayers.playerid', 'players.id')->where('matchkey', $matchkey)->select('players.*', 'teams.short_name', 'matchplayers.points as ppoints')->first();
                                        if (!empty($getplyrnameunq)) {
                                            if (!empty($getplyrnameunq11)) {
                                                if (in_array($res22, $get_p2) || in_array($res11, $get_p1)) {

                                                    //change
                                                    $Json['unique'][$y]['playerid_2'] = $getplyrnameunq->id;
                                                    $Json['unique'][$y]['player_2'] = $getplyrnameunq->player_name;
                                                    $Json['unique'][$y]['team_2'] = $getplyrnameunq->short_name;
                                                    $Json['unique'][$y]['role_2'] = $getplyrnameunq->role;
                                                    $Json['unique'][$y]['points_2'] = $getplyrnameunq->ppoints;
                                                    $Json['unique'][$y]['userid_2'] = $teams_2->userid;
                                                    $Json['unique'][$y]['player_image_2'] = ($getplyrnameunq->image != "") ? $geturl . 'public/' . $getplyrnameunq->image : ($geturl . 'public/' . Helpers::settings()->player_image ?? '');

                                                    //change
                                                    $Json['unique'][$y]['playerid_1'] = $getplyrnameunq11->id;
                                                    $Json['unique'][$y]['player_1'] = $getplyrnameunq11->player_name;
                                                    $Json['unique'][$y]['team_1'] = $getplyrnameunq11->short_name;
                                                    $Json['unique'][$y]['role_1'] = $getplyrnameunq11->role;
                                                    $Json['unique'][$y]['points_1'] = $getplyrnameunq11->ppoints;
                                                    $Json['unique'][$y]['userid_1'] = $teams_2->userid;
                                                    $Json['unique'][$y]['player_image_1'] = ($getplyrnameunq11->image != "") ? $geturl . 'public/' . $getplyrnameunq11->image : ($geturl . 'public/' . Helpers::settings()->player_image ?? '');
                                                    // $geturl.'public/'.Helpers::settings()->player_image ?? '';
                                                    // $geturl.'public/'.$getplyrnameunq11->image;

                                                }
                                            }
                                        }
                                        // }
                                        $e++;
                                        $y++;
                                    }
                                }
                            }

                            return response()->json(array($Json));
                            die;
                        }
                    }
                }
            }
        } else {

            $Json['status'] = false;
            $Json['msg'] = 'Unauthorized Request.';
            return response()->json($Json);
            die;
        }
    }

    // public function getleaderboard(Request $request)
    //    {
    //        $input = $request->all();
    //        $user = Helpers::isAuthorize($request);
    //        $geturl = Helpers::geturl();
    //        $series=$input['series_id'];
    //        $getdata=DB::connection('mysql')->table('listmatches')->where('series',$series)->where('launch_status','=','launched')->join('jointeam','jointeam.matchkey','=','listmatches.matchkey')->select('listmatches.final_status','jointeam.userid','listmatches.series',DB::raw('sum(points) as totalpoints'))->groupBy('jointeam.userid')->orderBy('totalpoints','desc')->get();
    //        $Json = [];

    //        if(!empty($getdata)){
    //            $ii=0;
    //            $ioo=1;
    //            foreach($getdata as $val){
    //                $userdata = DB::connection('mysql')->table('registerusers')->where('id',$val->userid)->first();
    //                if(!empty($userdata)){
    //                    if($userdata->id==$user->id){
    //                        $Json[$ii]['userid'] = $user->id;
    //                        $Json[$ii]['team'] = $userdata->team;
    //                     $result= DB::connection('mysql')->table('registerusers')->where('id',$user->id)->select('image')->first();
    //                     if(empty($result->image) or $result->image == 'null'){
    //                         $Json[$ii]['image'] = $geturl.'public/'.Helpers::settings()->user_image ?? '';
    //                     }else{

    //                         if( @GetImageSize($result->image) ) {
    //                             $Json[$ii]['image'] = $result->image;
    //                         } else {
    //                             $Json[$ii]['image'] = $geturl.'public/user_image.png';
    //                         }
    //                     }
    //                        $Json[$ii]['points'] = number_format($val->totalpoints, 2, '.', '');
    //                        $Json[$ii]['rank'] = $ioo;
    //                        $Json[$ii]['userno'] = -1;
    //                    }else{
    //                        $Json[$ii]['userid'] = $userdata->id;
    //                        $Json[$ii]['team'] = $userdata->team;
    //                     $result= DB::connection('mysql')->table('registerusers')->where('id',$val->userid)->select('image')->first();
    //                     if(empty($result->image) or $result->image == 'null'){
    //                         $Json[$ii]['image'] = $geturl.'public/'.Helpers::settings()->user_image ?? '';
    //                     }else{
    //                        if( @GetImageSize($result->image) ) {
    //                             $Json[$ii]['image'] = $result->image;
    //                         } else {
    //                             $Json[$ii]['image'] = $geturl.'public/user_image.png';
    //                         }
    //                     }
    //                        $Json[$ii]['points'] = number_format($val->totalpoints, 2, '.', '');
    //                        $Json[$ii]['rank'] = $ioo;
    //                        $Json[$ii]['userno'] = 0;
    //                    }

    //                    if($val->final_status=='winnerdeclared'){
    //                            // $key='joinedteam_'.$matchkey.'_'.$userid;

    //                            // Cache::remember($key, (60*24*30), function() use ($challengeid) {
    //                         $resjoinedteams = DB::connection('mysql')->table('joinedleauges')
    //                                             ->join('registerusers','registerusers.id','=','joinedleauges.userid')
    //                                             ->join('jointeam','jointeam.id','=','joinedleauges.teamid')
    //                                                ->leftJoin('finalresults',function ($join){
    //                                                 $join->on('finalresults.joinedid', '=' , 'joinedleauges.id');
    //                                             })
    //                                             ->orderBy('jointeam.points','DESC')
    //                                             ->select('joinedleauges.challengeid','registerusers.team','registerusers.email','registerusers.image','jointeam.teamnumber','jointeam.points','jointeam.lastpoints','jointeam.player_type','joinedleauges.id as jid','joinedleauges.userid','joinedleauges.teamid','joinedleauges.refercode','finalresults.amount')
    //                                             ->get();
    //                            //     });
    //                        }
    //                        else{
    //                         $resjoinedteams =  DB::connection('mysql')->table('joinedleauges')
    //                                             ->join('registerusers','registerusers.id','=','joinedleauges.userid')
    //                                             ->join('jointeam','jointeam.id','=','joinedleauges.teamid')->orderBy('jointeam.points','DESC')
    //                                             ->select('joinedleauges.challengeid','registerusers.team','registerusers.image','registerusers.email','jointeam.teamnumber','jointeam.points','jointeam.lastpoints','jointeam.player_type','joinedleauges.id as jid','joinedleauges.userid','joinedleauges.teamid')
    //                                             ->get();

    //                        }
    //                        $gtlastranks = array();
    //                        $pdfname = "";
    //                        $userrank = "";
    //                        $getcurrentrankarray = array();
    //                    $ss = 0;
    //                        $a = $resjoinedteams->toArray();

    //                        if(!empty($a)){
    //                            foreach($resjoinedteams as $pleauges){
    //                                $gtlastranks[$ss]['lastpoints'] = $pleauges->lastpoints;
    //                                $gtlastranks[$ss]['userid'] = $pleauges->userid;
    //                                $gtlastranks[$ss]['userjoinid'] = $pleauges->jid;
    //                                $getcurrentrankarray[$ss]['points'] = $pleauges->points;
    //                                $getcurrentrankarray[$ss]['userid'] = $pleauges->userid;
    //                                $getcurrentrankarray[$ss]['userjoinid'] = $pleauges->jid;
    //                                $getcurrentrankarray[$ss]['player_type'] = $pleauges->player_type;
    //                                $ss++;
    //                            }
    //                        }
    //                    $gtlastranks = Helpers::multid_sort($gtlastranks, 'lastpoints');
    //                    if(!empty($gtlastranks)){
    //                        $getuserlastrank=array();
    //                        $lr=0;$lrsno = 0;$uplus=0;
    //                        foreach($gtlastranks as $lrnk){
    //                            if(in_array($lrnk['lastpoints'], array_column($getuserlastrank, 'points'))) { // search value in the array
    //                                $lrsno++;
    //                                $lrsno = $lrsno+$uplus;
    //                                $uplus=0;
    //                            }else{
    //                                $lrsno++;
    //                            }
    //                            $getuserlastrank[$lr]['rank'] = $lrsno;
    //                            $getuserlastrank[$lr]['points'] = $lrnk['lastpoints'];
    //                            $getuserlastrank[$lr]['userid'] = $lrnk['userid'];
    //                            $getuserlastrank[$lr]['userjoinid'] = $lrnk['userjoinid'];
    //                            $lr++;

    //                        }
    //                    }
    //                    //get current ranks//
    //                    $gtcurranks = Helpers::multid_sort($getcurrentrankarray, 'points');
    //                    if(!empty($gtcurranks)){
    //                        $getusercurrank=array();
    //                        $cur=0;$currsno = 0;$plus=0;
    //                        foreach($gtcurranks as $curnk){
    //                            if(!in_array($curnk['points'], array_column($getusercurrank, 'points'))){ // search value in the array
    //                                $currsno++;
    //                                $currsno = $currsno+$plus;
    //                                $plus=0;
    //                            }
    //                            else{
    //                                $plus++;
    //                            }
    //                            $getusercurrank[$cur]['rank'] = $currsno;
    //                            $getusercurrank[$cur]['points'] = $curnk['points'];
    //                            $getusercurrank[$cur]['userid'] = $curnk['userid'];
    //                            $getusercurrank[$cur]['userjoinid'] = $curnk['userjoinid'];
    //                            $cur++;
    //                        }
    //                    }

    //                    $getuserindexinglast =Helpers::searchByValue($getuserlastrank,'userjoinid',$a[0]->jid);
    //                 $getlastrank = $getuserlastrank[$getuserindexinglast]['rank'];
    //                 $getuserindexingcurent =Helpers::searchByValue($getusercurrank,'userjoinid',$a[0]->jid);
    //                 $getcurrentrank = $getusercurrank[$getuserindexingcurent]['rank'];
    //                 $Json[$ii]['getcurrentrank'] = $getcurrentrank;

    //                 if($getlastrank<$getcurrentrank){
    //                     $Json[$ii]['arrowname'] = 'down-arrow';
    //                 }
    //                 else if($getlastrank==$getcurrentrank){
    //                     $Json[$ii]['arrowname'] = 'equal-arrow';
    //                 }
    //                 else if($getlastrank>$getcurrentrank){
    //                     $Json[$ii]['arrowname'] = 'up-arrow';
    //                 }

    //                    $ii++;
    //                    $ioo++;
    //                }
    //            }
    //        }
    //        array_multisort(array_column($Json, 'userno'), SORT_ASC, $Json);
    //        return response()->json($Json);
    //    }

    public function getleaderboard(Request $request)
    {
        $input = $request->all();
        $user = Helpers::isAuthorize($request);
        $geturl = Helpers::geturl();
        $series = $input['series_id'];
        $getdata = DB::connection('mysql')->table('listmatches')
                    ->join('series','series.id','listmatches.series')
                    ->where('listmatches.series', $series)
                    ->where('series.end_date','>=',date('Y-m-d H:i:s'))
                    ->where('listmatches.launch_status', '=', 'launched')
                    ->where('listmatches.final_status', '=', 'winnerdeclared')
                    ->join('jointeam', 'jointeam.matchkey', '=', 'listmatches.matchkey')
                    ->join('registerusers', 'registerusers.id', 'jointeam.userid')
                    ->select('listmatches.final_status', 'jointeam.userid', 'listmatches.series', DB::raw('sum(points) as totalpoints'), 'registerusers.id as rid', 'registerusers.team as rteam', 'registerusers.image as rimage')
                    ->groupBy('jointeam.userid')
                    ->orderBy('totalpoints', 'desc')
                    ->get();
        $Json = [];
        if (!empty($getdata)) {
            $ii = 0;
            $ioo = 1;
            $self = '';
            $self_in = 0;
            foreach ($getdata as $val) {
                if (!empty($val)) {
                    if ($val->rid == $user->id) {
                        $Json[$ii]['userid'] = $user->id;
                        $Json[$ii]['team'] = $val->rteam;
                        if (empty($val->rimage) or $val->rimage == 'null') {
                            $Json[$ii]['image'] = $geturl . 'public/' . Helpers::settings()->user_image ?? '';
                        } else {

                            // if( @GetImageSize($val->rimage) ) {
                            $Json[$ii]['image'] = 'public/'.$val->rimage;
                            // } else {
                            //     $Json[$ii]['image'] = $geturl.'public/user_image.png';
                            // }
                        }
                        $Json[$ii]['points'] = number_format($val->totalpoints, 2, '.', '');
                        $Json[$ii]['rank'] = $ioo;
                        $Json[$ii]['userno'] = -1;
                        $self = $Json[$ii];
                        $self_in = $ii;
                    } else {
                        $Json[$ii]['userid'] = $val->rid;
                        $Json[$ii]['team'] = $val->rteam;
                        if (empty($val->rimage) or $val->rimage == 'null') {
                            $Json[$ii]['image'] = $geturl . 'public/' . Helpers::settings()->user_image ?? '';
                        } else {
                            // if( @GetImageSize($val->rimage) ) {
                            $Json[$ii]['image'] = $val->rimage;
                            // } else {
                            //     $Json[$ii]['image'] = $geturl.'public/user_image.png';
                            // }
                        }
                        $Json[$ii]['points'] = number_format($val->totalpoints, 2, '.', '');
                        $Json[$ii]['rank'] = $ioo;
                        $Json[$ii]['userno'] = 0;
                    }
                    $ii++;
                    $ioo++;
                }
            }
        }
        array_multisort(array_column($Json, 'rank'), SORT_ASC, $Json);
        if ($self != '') {
            $js = array_unshift($Json, $self);
            unset($Json[$self_in + 1]);
            $Json = array_values($Json);
        }
        return response()->json($Json);
    }

    public function getleaderboard_challenge(Request $request)
    {
        $input = $request->all();
        $user = Helpers::isAuthorize($request);
        $geturl = Helpers::geturl();
        $challengeid = request()->get('challenge_id');
        // $series=$input['series_id'];
        $getdata = DB::connection('mysql')->table('listmatches')
            ->where('launch_status', '=', 'launched')
            ->join('jointeam', 'jointeam.matchkey', '=', 'listmatches.matchkey')
            ->join('matchchallenges', 'matchchallenges.matchkey', '=', 'listmatches.matchkey')
            ->where('matchchallenges.id', $challengeid)
            ->select('jointeam.userid', 'jointeam.id as teamid', 'jointeam.teamnumber', 'listmatches.series', DB::raw('sum(points) as totalpoints'), 'matchchallenges.win_amount')
            ->groupBy('jointeam.userid')
            ->orderBy('totalpoints', 'desc')
            ->get();
        $Json = [];
        if (!empty($getdata)) {
            $ii = 0;
            $ioo = 1;
            foreach ($getdata as $val) {
                // echo '<pre>';print_r($challengeid);
                // echo '<pre>';print_r($val);die;
                $userdata = DB::connection('mysql')->table('registerusers')->where('id', $val->userid)->first();
                // $joindata = DB::connection('mysql')->table('joinedleauges')
                // ->where('userid',$val->userid)
                // ->where('challengeid',$challengeid)
                // ->where('teamid',$val->teamid)->select('id')->first();
                if (!empty($userdata)) {
                    if ($userdata->id == $user->id) {
                        // $Json[$ii]['jid'] = $joindata->id;
                        $Json[$ii]['userid'] = $user->id;
                        $Json[$ii]['team'] = $userdata->team;
                        //         $result= DB::connection('mysql')->table('registerusers')->where('id',$user->id)->select('image')->first();
                        $Json[$ii]['image'] = (!empty($userdata->image)) ? 'public/'.$userdata->image : $geturl . 'public/avtar1.png';
                        $Json[$ii]['points'] = $val->totalpoints;
                        $Json[$ii]['rank'] = $ioo;
                        $Json[$ii]['userno'] = -1;
                    } else {
                        // $Json[$ii]['jid'] = $joindata->id;
                        $Json[$ii]['userid'] = $userdata->id;
                        $Json[$ii]['team'] = $userdata->team;
                        //         $result= DB::connection('mysql')->table('registerusers')->where('id',$val->userid)->select('image')->first();
                        $Json[$ii]['image'] = (!empty($userdata->image)) ? 'public/'.$userdata->image : $geturl . 'public/avtar1.png';
                        $Json[$ii]['points'] = $val->totalpoints;
                        $Json[$ii]['rank'] = $ioo;
                        $Json[$ii]['userno'] = 0;
                    }

                    $Json[$ii]['win_amount'] = $val->win_amount;
                    $Json[$ii]['teamnumber'] = $val->teamnumber;
                    $Json[$ii]['teamid'] = $val->teamid;

                    $ii++;
                    $ioo++;
                }
            }
        }
        array_multisort(array_column($Json, 'userno'), SORT_ASC, $Json);
        return response()->json($Json);
    }

    public function getleaderboardbyuser(Request $request)
    {
        $input = $request->all();
        $geturl = Helpers::geturl();
        $series = $input['series_id'];
        $usserid = $input['userid'];
        $Json = array();
        $getdata = DB::connection('mysql')->table('listmatches')->where('series', $series)->where('launch_status', '=', 'launched')->where('final_status', '=', 'winnerdeclared')->select('listmatches.series', 'listmatches.name', 'listmatches.matchkey')->get();
        if (!empty($getdata)) {
            $ii = 0;
            foreach ($getdata as $val) {
                $teamjoin = DB::connection('mysql')->table('jointeam')->where('matchkey', $val->matchkey)->where('userid', $usserid)->select('jointeam.userid', 'jointeam.points', 'jointeam.teamnumber')->orderBy('points', 'desc')->get();
                // echo'<pre>'; print_r($teamjoin);
                if (!empty($teamjoin)) {
                    $userdata = DB::connection('mysql')->table('registerusers')->where('id', $usserid)->first();
                    if (!empty($userdata)) {
                        foreach ($teamjoin as $teamjoins) {
                            $Json[$ii]['userid'] = $userdata->id;
                            $Json[$ii]['short_name'] = $val->name;
                            $Json[$ii]['series'] = $val->series;
                            $Json[$ii]['team'] = $userdata->team;
                            if (!empty($userdata->image)) {
                                $Json[$ii]['image'] = $userdata->image;
                            } else {
                                $Json[$ii]['image'] = asset('/' . Helpers::settings()->player_image ?? '');
                            }
                            $Json[$ii]['points'] = $teamjoins->points;
                            $Json[$ii]['teamnumber'] = $teamjoins->teamnumber;
                            $ii++;
                        }
                    }
                }
            }
            return response()->json($Json);
        }
    }


    public function getmatchlistagain(Request $request)
    {
        Helpers::setHeader(200);
        $users = Helpers::isAuthorize($request);
        Helpers::timezone();
        $geturl = Helpers::geturl();
        $locktime = Carbon::now();
        $input = $request->all();
        $userid = $users->id;
        $currenttimme = date('Y-m-d H:i:s');
        $lastdays = date('Y-m-d H:i:s', strtotime('+15days', strtotime($currenttimme)));

        $query = DB::connection('mysql')->table('listmatches');
        if (isset($_GET['series'])) {
            $series = $_GET['series'];
            $query = $query->where('series', $series);
        }
        $findmatches = $query->join('series', 'listmatches.series', '=', 'series.id')
            ->join('teams as team1', 'team1.id', '=', 'listmatches.team1')
            ->join('teams as team2', 'team2.id', '=', 'listmatches.team2')
            ->whereBetween('listmatches.start_date', [$currenttimme, $lastdays])
            ->where('listmatches.final_status', '!=', 'IsCanceled')
            ->where('listmatches.final_status', '!=', 'IsAbandoned')
            ->where('series.status', 'opened')
            ->where('listmatches.launch_status', 'launched')
            ->select('listmatches.id as listmatchid', 'team1.short_name as teamname1', 'team2.short_name as teamname2', 'team1.team as team1fullname', 'team2.team as team2fullname', 'team1.color as team1color', 'team2.color as team2color', 'team1.logo as team1logo', 'team2.logo as team2logo', 'listmatches.series as seriesid', 'series.name as seriesname', 'listmatches.name', 'listmatches.start_date', 'listmatches.format', 'listmatches.matchkey', 'listmatches.final_status', 'listmatches.launch_status', 'listmatches.playing11_status', 'listmatches.fantasy_type', 'listmatches.second_inning_status', 'listmatches.real_matchkey', 'listmatches.tosswinner_team', 'listmatches.toss_decision','listmatches.match_notification','listmatches.tbl_order')
            ->orderBY('listmatches.start_date', 'ASC')
            ->get();

        $Json = array();
        if (!empty($findmatches)) {
            $i = 0;
            $matchshow = 0;
            $matchcollection = collect($findmatches)->groupby('tbl_order');
            if(isset($matchcollection[0])){
                $newarry = $matchcollection[0];
                unset($matchcollection[0]);
                $matchcollection = $matchcollection->sortKeys();
                $matchcollection->push($newarry);
                $findmatches =array();
                foreach($matchcollection->toArray() as $value){
                    foreach($value as $aj){
                        array_push($findmatches,$aj);
                    }
                }
            }
			
            foreach ($findmatches as $match) {
                if ($match->launch_status == "launched" && DB::connection('mysql')->table('matchchallenges')->where('matchkey', $match->matchkey)->doesntExist()) {
                    continue;
                }
                $arr_param = '';
                if ($match->second_inning_status == 2) {
                    $arr_param = 'second_inning';
                } else {
                    $arr_param = 'upcoming';
                }
                $Json[$arr_param][$i]['id'] = $match->listmatchid;
                $Json[$arr_param][$i]['name'] = $match->name;
                $Json[$arr_param][$i]['format'] = $match->format;
                $Json[$arr_param][$i]['series'] = $match->seriesid;

                $believerstatus = DB::connection('mysql')->table('matchplayers')
                    ->where('matchkey', $match->matchkey)
                    ->where('forduo',1)->first();
                    if(!empty($believerstatus)){
                        $Json[$arr_param][$i]['believer_status'] = 1;
                    }else{
                        $Json[$arr_param][$i]['believer_status'] = 0;
                    }
                
                // if($match->second_inning_status==0 || $match->second_inning_status==1){
                $Json[$arr_param][$i]['seriesname'] = $match->seriesname;
                // }else{
                //     $Json[$arr_param][$i]['seriesname'] = $match->seriesname.'- 2nd Inning Match';
                // }

                $Json[$arr_param][$i]['team1name'] = strtoupper($match->teamname1);
                $Json[$arr_param][$i]['team2name'] = strtoupper($match->teamname2);
                $Json[$arr_param][$i]['team1fullname'] = strtoupper($match->team1fullname);
                $Json[$arr_param][$i]['team2fullname'] = strtoupper($match->team2fullname);
                $Json[$arr_param][$i]['matchkey'] = $match->matchkey;
                if(!empty($match->tosswinner_team)){
                    $tossteamname = DB::connection('mysql')->table('teams')->where('team_key',$match->tosswinner_team)->first();
                }
                $Json[$arr_param][$i]['tosswinner_team'] = isset($tossteamname) ? strtoupper($tossteamname->short_name) : strtoupper(NULL);
                if($match->toss_decision==1){
                    $Json[$arr_param][$i]['toss_decision'] = 'BAT';
                }elseif($match->toss_decision==2){
                    $Json[$arr_param][$i]['toss_decision'] = 'BOWL'; 
                }else{
                    $Json[$arr_param][$i]['toss_decision'] = 'TOSS PENDING';
                }

                if(!empty($users)){
                    $getData = DB::connection('mysql')->table('setmatchreminder')->where('user_id',$users->id)->where('matchkey',$match->matchkey)->exists();
                    if(!empty($getData)){
                        $Json[$arr_param][$i]['setReminder'] =1;
                    }else{
                        $Json[$arr_param][$i]['setReminder'] =0;
                    }
                }else{
                    $Json[$arr_param][$i]['setReminder'] =0;
                }
                
                $Json[$arr_param][$i]['type'] = $match->fantasy_type;
                $Json[$arr_param][$i]['winnerstatus'] = $match->final_status;
                $Json[$arr_param][$i]['playing11_status'] = $match->playing11_status;
                $Json[$arr_param][$i]['match_notification'] = $match->match_notification ?? "";
                $Json[$arr_param][$i]['second_inning_status'] = $match->second_inning_status;

                if (!empty($match->team1color)) {
                    $Json[$arr_param][$i]['team1color'] = $match->team1color;
                } else {
                    $Json[$arr_param][$i]['team1color'] = '#ffffff';
                }
                if (!empty($match->team2color)) {
                    $Json[$arr_param][$i]['team2color'] = $match->team2color;
                } else {
                    $Json[$arr_param][$i]['team2color'] = '#ffffff';
                }
                if ($match->team1logo != "") {

                    // if ($match->team1logo) {
                    //     $Json[$arr_param][$i]['team1logo'] = $geturl . '/' . $match->team1logo;
                    // } else {
                    //     $Json[$arr_param][$i]['team1logo'] = $geturl . 'team_image.png';
                    // }
                    
                    $c = count(explode(':', $match->team1logo));
					if ($c >= 2) {
						$Json[$arr_param][$i]['team1logo'] = $match->team1logo;
					} else {
						$Json[$arr_param][$i]['team1logo'] = $geturl .'public/'. $match->team1logo;
					}

                } else {
                    $Json[$arr_param][$i]['team1logo'] = $geturl .'public/'. $match->team1logo;
                }
                if ($match->team2logo != "") {

                    $c = count(explode(':', $match->team2logo));;
					if ($c >= 2) {
						$Json[$arr_param][$i]['team2logo'] =$match->team2logo;
					} else {
						$Json[$arr_param][$i]['team2logo'] = $geturl .'public/'. $match->team2logo;
					}

                    // if ($match->team2logo) {
                    //     $Json[$arr_param][$i]['team2logo'] = $geturl . '/' . $match->team2logo;
                    // } else {
                    //     $Json[$arr_param][$i]['team2logo'] = $geturl . 'team_image.png';
                    // }

                } else {
                    $Json[$arr_param][$i]['team2logo'] = $geturl.'public/'. 'team_image.png';
                }
                $locktime = date('Y-m-d H:i:s', strtotime($match->start_date));
                if (date('Y-m-d H:i:s') >= $locktime) {
                    $Json[$arr_param][$i]['matchopenstatus'] = 'closed';
                } else {
                    $matchshow++;
                    $Json[$arr_param][$i]['matchopenstatus'] = 'opened';
                }
                $Json[$arr_param][$i]['time_start'] = date('Y-m-d H:i:s', strtotime($match->start_date));
                $Json[$arr_param][$i]['launch_status'] = $match->launch_status;
                $Json[$arr_param][$i]['locktime'] = $locktime;
                if (isset($_GET['userid'])) {
                    $finduserinfo = DB::connection('mysql')->table('registerusers')->where('id', $id)->select('id')->first();
                    if (!empty($finduserinfo)) {
                        $getid = $finduserinfo->id;
                        $findjointeam = DB::connection('mysql')->table('jointeam')->where('userid', $getid)->where('matchkey', $match->matchkey)->orderBY('id', 'DESC')->get();
                        if (!empty($findjointeam)) {
                            $Json[$arr_param][$i]['createteamnumber'] = $findjointeam[0]->teamnumber + 1;
                        } else {
                            $Json[$arr_param][$i]['createteamnumber'] = 1;
                        }
                    }
                }

                $totalTeams = DB::connection('mysql')->table('jointeam')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->get();

                $Json[$arr_param][$i]['totalTeams'] = count($totalTeams);

                $totalcontest = DB::connection('mysql')->table('joinedleauges')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->groupBy('challengeid')
                    ->get();

                $Json[$arr_param][$i]['totalcontest'] = count($totalcontest);

                $maximum_winning_amount = DB::connection('mysql')->table('matchchallenges')
                    ->where('matchkey', $match->matchkey)
                    ->where('maximum_user','>=' ,25)
                    ->orderBy('win_amount', 'DESC')
                    ->value('win_amount');

                $giveaway = DB::connection('mysql')->table('matchchallenges')
                ->where('matchkey', $match->matchkey)
                ->where('bonus_percentage', 100)->where('win_amount','!=', 0)
                ->first();
                $giveaway1 = DB::connection('mysql')->table('matchchallenges')
                ->where('matchkey', $match->matchkey)
                ->where('entryfee', 0)->where('win_amount','!=', 0)
                ->first();
                if(!empty($giveaway)){
                    $Json[$arr_param][$i]['giveAway']=1;
                }elseif(!empty($giveaway1)){
                    $Json[$arr_param][$i]['giveAway']=1;
                }else{
                    $Json[$arr_param][$i]['giveAway']=0;
                }
                $Json[$arr_param][$i]['maximum_winning_amount'] = $maximum_winning_amount;
                $ch = DB::connection('mysql')->table('matchchallenges')->where('matchkey', $match->matchkey)->first([DB::raw('SUM(maximum_user)as max_users'), DB::raw('SUM(joinedusers)as joinedusers')]);
                // dd($ch);
                $Json[$arr_param][$i]['max_users'] = $ch->max_users;
                $Json[$arr_param][$i]['total_joined'] = $ch->joinedusers;

                $Json[$arr_param][$i]['status'] = true;
                $i++;
            }
        }

        //joined matches
        $findmatchescric = DB::connection('mysql')->table('joinedleauges')
            ->join('listmatches', 'listmatches.matchkey', '=', 'joinedleauges.matchkey')
            ->join('series', 'series.id', '=', 'listmatches.series')
            ->where('listmatches.fantasy_type', 'Cricket')
            ->where('listmatches.launch_status', 'launched')
            ->where('joinedleauges.userid', $userid)
            ->join('teams as t1', 't1.id', '=', 'listmatches.team1')
            ->join('teams as t2', 't2.id', '=', 'listmatches.team2')
            ->select('joinedleauges.*', 'listmatches.matchkey', 'listmatches.series', 'series.name as seriesname', 'listmatches.name', 'listmatches.team1', 'listmatches.team2', 't1.team as team1fullname', 't2.team as team2fullname', 'listmatches.start_date', 'listmatches.status', 'listmatches.launch_status', 'listmatches.final_status', 't1.logo as team1logo', 't2.logo as team2logo', 't1.color as team1color', 't2.color as team2color', 't2.short_name as team2short_name', 't1.short_name as team1short_name', 'listmatches.fantasy_type', 'series.id as series_id', 'listmatches.playing11_status', 'listmatches.second_inning_status','listmatches.match_notification')
            ->orderBY('listmatches.start_date', 'DESC')
            ->groupBy('joinedleauges.matchkey')
            ->limit(3)
            ->get();
        $j = 0;
        if (!empty($findmatchescric->toArray())) {
            foreach ($findmatchescric as $match) {
                $joineduserss = DB::connection('mysql')->table('joinedleauges')->where('joinedleauges.matchkey', $match->matchkey)->join('matchchallenges', 'matchchallenges.id', '=', 'joinedleauges.challengeid')
                    ->where('matchchallenges.status', '!=', 'canceled')->where('joinedleauges.userid', $userid)->groupBy('joinedleauges.challengeid')->select('joinedleauges.id')->get();
                $jj = count($joineduserss);
                if ($jj == 0) {continue;}
                $Json['joined'][$j]['matchkey'] = $match->matchkey;
                $Json['joined'][$j]['name'] = $match->name;
                $Json['joined'][$j]['team1display'] = $match->team1short_name;
                $Json['joined'][$j]['team2display'] = $match->team2short_name;
                $Json['joined'][$j]['team1fullname'] = strtoupper($match->team1fullname);
                $Json['joined'][$j]['team2fullname'] = strtoupper($match->team2fullname);
                $Json['joined'][$j]['start_date'] = date('Y-m-d H:i:s', strtotime($match->start_date));
                $locktimes = date('Y-m-d H:i:s', strtotime($match->start_date));
                if (date('Y-m-d H:i:s') >= $locktimes) {
                    $Json['joined'][$j]['status'] = 'closed';
                    $Json['joined'][$j]['match_notification'] = '';
                } else {
                    $Json['joined'][$j]['status'] = 'opened';
                    $Json['joined'][$j]['match_notification'] = $match->match_notification ?? "";
                }
                $Json['joined'][$j]['launch_status'] = $match->launch_status;
                $Json['joined'][$j]['final_status'] = $match->final_status;
                if ($match->second_inning_status == 2) {
                    $Json['joined'][$j]['series_name'] = $match->seriesname . '- 2nd Inning Match';
                } else {
                    $Json['joined'][$j]['series_name'] = $match->seriesname;
                }
                // $Json['joined'][$j]['series_name'] = $match->seriesname;
                $Json['joined'][$j]['type'] = $match->fantasy_type;
                $Json['joined'][$j]['series_id'] = $match->series_id;

                $Json['joined'][$j]['joinedcontest'] = $jj;
                if (!empty($match->team1color)) {
                    $Json['joined'][$j]['team1color'] = $match->team1color;
                } else {
                    $Json['joined'][$j]['team1color'] = '#FFFFFF';
                }
                if (!empty($match->team2color)) {
                    $Json['joined'][$j]['team2color'] = $match->team2color;
                } else {
                    $Json['joined'][$j]['team2color'] = '#FFFFFF';
                }

                if ($match->team1logo != "") {
                    // $Json['joined'][$j]['team1logo'] = $geturl.'public/'.$match->team1logo;

                    if (@GetImageSize($geturl . 'public/' . $match->team1logo)) {
                        $c = count(explode(':', $match->team1logo));;
                        if ($c >= 2) {
                            $Json['joined'][$j]['team1logo'] = $match->team1logo;
                        } else {
                            $Json['joined'][$j]['team1logo'] = $geturl . '/public/' . $match->team1logo;
                        }
                        // $Json['joined'][$j]['team1logo'] = $geturl . 'public/' . $match->team1logo;
                    } else {
                        $Json['joined'][$j]['team1logo'] = $geturl . 'public/team_image.png';
                    }
                } else {
                    $Json['joined'][$j]['team1logo'] = $geturl . 'public/team_image.png';
                }
                if ($match->team2logo != "") {
                    // $Json['joined'][$j]['team2logo'] = $geturl.'/'.$match->team2logo;

                    if (@GetImageSize($geturl . 'public/' . $match->team2logo)) {
                        $c = count(explode(':', $match->team2logo));;
                        if ($c >= 2) {
                            $Json['joined'][$j]['team2logo'] = $match->team2logo;
                        } else {
                            $Json['joined'][$j]['team2logo'] = $geturl . '/public/' . $match->team2logo;
                        }
                        // $Json['joined'][$j]['team2logo'] = $geturl . 'public/' . $match->team2logo;
                    } else {
                        $Json['joined'][$j]['team2logo'] = $geturl . 'public/team_image.png';
                    }
                } else {
                    $Json['joined'][$j]['team2logo'] = $geturl . 'public/team_image.png';
                }
                $Json['joined'][$j]['available_status'] = 1;

                $totalTeams = DB::connection('mysql')->table('jointeam')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->get();

                $Json['joined'][$j]['totalTeams'] = count($totalTeams);

                $totalcontest = DB::connection('mysql')->table('joinedleauges')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->groupBy('challengeid')
                    ->get();

                $Json['joined'][$j]['totalcontest'] = count($totalcontest);

                $maximum_winning_amount = DB::connection('mysql')->table('matchchallenges')
                    ->where('matchkey', $match->matchkey)
                    ->orderBy('win_amount', 'DESC')
                    ->value('win_amount');

                $Json['joined'][$j]['maximum_winning_amount'] = $maximum_winning_amount;

                $total_winning_amount = DB::connection('mysql')->table('finalresults')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->sum('amount');

                $Json['joined'][$j]['total_winning_amount'] = floor($total_winning_amount);
                $Json['joined'][$j]['playing11_status'] = $match->playing11_status;
                $j++;
            }
        } else {
            $Json['joined'] = array();
        }

        if (empty($Json['upcoming'])) {
            $Json['upcoming'] = array();
        } else {
            $Json['upcoming'] = array_values($Json['upcoming']);
        }
        if (empty($Json['second_inning'])) {
            $Json['second_inning'] = array();
        } else {
            $Json['second_inning'] = array_values($Json['second_inning']);
        }

        if (empty($Json['joined'])) {
            $Json['joined'] = array();
        }
        return response()->json(array($Json));
        die;
        //         }
    }

    //setReminder
    public function setMatchReminder(Request $request)
    {
     Helpers::setHeader(200);
     $users=Helpers::isAuthorize($request);
     $input= $request->all();
     $getData = DB::connection('mysql')->table('setmatchreminder')->where('user_id',$users->id)->where('matchkey',$input['matchkey'])->first();
     if(empty($getData)){
         $input['user_id']=$users->id; 
         DB::connection('mysql2')->table('setmatchreminder')->insert($input);
     }else{
         if($input['status']==2){
             DB::connection('mysql2')->table('setmatchreminder')->where('id',$getData->id)->delete();
         }
     }
     $Json['success'] = true;
     $Json['msg'] = "You've successfully changed your notification preferences.";
     return response()->json($Json);
     die;
    }


    // public function getmatchlistagain(Request $request)
    // {
    //     Helpers::setHeader(200);
    //     $users = Helpers::isAuthorize($request);
    //     Helpers::timezone();
    //     $geturl = Helpers::geturl();
    //     $locktime = Carbon::now();
    //     $input = $request->all();
    //     $userid = $users->id;
    //     $currenttimme = date('Y-m-d H:i:s');
    //     $lastdays = date('Y-m-d H:i:s', strtotime('+15days', strtotime($currenttimme)));

    //     $query = DB::connection('mysql')->table('listmatches');
    //     if (isset($_GET['series'])) {
    //         $series = $_GET['series'];
    //         $query = $query->where('series', $series);
    //     }
    //     $query = $query->join('series', 'listmatches.series', '=', 'series.id')
    //     $findmatches = $query
    //         ->join('teams as team1', 'team1.id', '=', 'listmatches.team1')
    //         ->join('teams as team2', 'team2.id', '=', 'listmatches.team2')
    //         ->whereBetween('listmatches.start_date', [$currenttimme, $lastdays])
    //         ->where('listmatches.final_status', '!=', 'IsCanceled')
    //         ->where('listmatches.final_status', '!=', 'IsAbandoned')
    //         ->select('listmatches.id as listmatchid', 'team1.short_name as teamname1', 'team2.short_name as teamname2', 'team1.team as team1fullname', 'team2.team as team2fullname', 'team1.color as team1color', 'team2.color as team2color', 'team1.logo as team1logo', 'team2.logo as team2logo', 'listmatches.series as seriesid', 'listmatches.name', 'listmatches.start_date', 'listmatches.format', 'listmatches.matchkey', 'listmatches.final_status', 'listmatches.launch_status', 'listmatches.playing11_status', 'listmatches.fantasy_type', 'listmatches.second_inning_status', 'listmatches.real_matchkey', 'listmatches.tosswinner_team', 'listmatches.toss_decision','listmatches.match_notification','listmatches.series_Name as seriesname')
    //         ->orderBY('listmatches.tbl_order', 'DESC')
    //         ->orderBY('listmatches.start_date', 'ASC')
    //         ->get();
    //         dd($findmatches);
    //     $Json = array();
    //     if (!empty($findmatches)) {
    //         $i = 0;
    //         $matchshow = 0;
    //         foreach ($findmatches as $match) {
    //             if ($match->launch_status == "launched" && DB::connection('mysql')->table('matchchallenges')->where('matchkey', $match->matchkey)->doesntExist()) {
    //                 continue;
    //             }
    //             $arr_param = '';
    //             if ($match->second_inning_status == 2) {
    //                 $arr_param = 'second_inning';
    //             } else {
    //                 $arr_param = 'upcoming';
    //             }
    //             $Json[$arr_param][$i]['id'] = $match->listmatchid;
    //             $Json[$arr_param][$i]['name'] = $match->name;
    //             $Json[$arr_param][$i]['format'] = $match->format;
    //             $Json[$arr_param][$i]['series'] = $match->seriesid;
    //             // if($match->second_inning_status==0 || $match->second_inning_status==1){
    //             $Json[$arr_param][$i]['seriesname'] = $match->seriesname;
    //             // }else{
    //             //     $Json[$arr_param][$i]['seriesname'] = $match->seriesname.'- 2nd Inning Match';
    //             // }

    //             $Json[$arr_param][$i]['team1name'] = strtoupper($match->teamname1);
    //             $Json[$arr_param][$i]['team2name'] = strtoupper($match->teamname2);
    //             $Json[$arr_param][$i]['team1fullname'] = strtoupper($match->team1fullname);
    //             $Json[$arr_param][$i]['team2fullname'] = strtoupper($match->team2fullname);
    //             $Json[$arr_param][$i]['matchkey'] = $match->matchkey;
    //             $Json[$arr_param][$i]['tosswinner_team'] = ($match->tosswinner_team == 'a') ? strtoupper($match->teamname1) : strtoupper($match->teamname2);
    //             $Json[$arr_param][$i]['toss_decision'] = $match->toss_decision;
    //             $Json[$arr_param][$i]['type'] = $match->fantasy_type;
    //             $Json[$arr_param][$i]['winnerstatus'] = $match->final_status;
    //             $Json[$arr_param][$i]['playing11_status'] = $match->playing11_status;
    //             $Json[$arr_param][$i]['match_notification'] = $match->match_notification ?? "";
    //             $Json[$arr_param][$i]['second_inning_status'] = $match->second_inning_status;

    //             if (!empty($match->team1color)) {
    //                 $Json[$arr_param][$i]['team1color'] = $match->team1color;
    //             } else {
    //                 $Json[$arr_param][$i]['team1color'] = '#ffffff';
    //             }
    //             if (!empty($match->team2color)) {
    //                 $Json[$arr_param][$i]['team2color'] = $match->team2color;
    //             } else {
    //                 $Json[$arr_param][$i]['team2color'] = '#ffffff';
    //             }
    //             if ($match->team1logo != "") {

    //                 if ($match->team1logo) {
    //                     $Json[$arr_param][$i]['team1logo'] = $geturl . 'public/' . $match->team1logo;
    //                 } else {
    //                     $Json[$arr_param][$i]['team1logo'] = $geturl . 'public/team_image.png';
    //                 }

    //             } else {
    //                 $Json[$arr_param][$i]['team1logo'] = $geturl . 'public/team_image.png';
    //             }
    //             if ($match->team2logo != "") {

    //                 if ($match->team2logo) {
    //                     $Json[$arr_param][$i]['team2logo'] = $geturl . 'public/' . $match->team2logo;
    //                 } else {
    //                     $Json[$arr_param][$i]['team2logo'] = $geturl . 'public/team_image.png';
    //                 }

    //             } else {
    //                 $Json[$arr_param][$i]['team2logo'] = $geturl . 'public/team_image.png';
    //             }
    //             $locktime = date('Y-m-d H:i:s', strtotime($match->start_date));
    //             if (date('Y-m-d H:i:s') >= $locktime) {
    //                 $Json[$arr_param][$i]['matchopenstatus'] = 'closed';
    //             } else {
    //                 $matchshow++;
    //                 $Json[$arr_param][$i]['matchopenstatus'] = 'opened';
    //             }
    //             $Json[$arr_param][$i]['time_start'] = date('Y-m-d H:i:s', strtotime($match->start_date));
    //             $Json[$arr_param][$i]['launch_status'] = $match->launch_status;
    //             $Json[$arr_param][$i]['locktime'] = $locktime;
    //             if (isset($_GET['userid'])) {
    //                 $finduserinfo = DB::connection('mysql')->table('registerusers')->where('id', $id)->select('id')->first();
    //                 if (!empty($finduserinfo)) {
    //                     $getid = $finduserinfo->id;
    //                     $findjointeam = DB::connection('mysql')->table('jointeam')->where('userid', $getid)->where('matchkey', $match->matchkey)->orderBY('id', 'DESC')->get();
    //                     if (!empty($findjointeam)) {
    //                         $Json[$arr_param][$i]['createteamnumber'] = $findjointeam[0]->teamnumber + 1;
    //                     } else {
    //                         $Json[$arr_param][$i]['createteamnumber'] = 1;
    //                     }
    //                 }
    //             }

    //             $totalTeams = DB::connection('mysql')->table('jointeam')
    //                 ->where('matchkey', $match->matchkey)
    //                 ->where('userid', $userid)
    //                 ->get();

    //             $Json[$arr_param][$i]['totalTeams'] = count($totalTeams);

    //             $totalcontest = DB::connection('mysql')->table('joinedleauges')
    //                 ->where('matchkey', $match->matchkey)
    //                 ->where('userid', $userid)
    //                 ->groupBy('challengeid')
    //                 ->get();

    //             $Json[$arr_param][$i]['totalcontest'] = count($totalcontest);

    //             $maximum_winning_amount = DB::connection('mysql')->table('matchchallenges')
    //                 ->where('matchkey', $match->matchkey)
    //                 ->orderBy('win_amount', 'DESC')
    //                 ->value('win_amount');
    //             $Json[$arr_param][$i]['maximum_winning_amount'] = $maximum_winning_amount;
    //             $ch = DB::connection('mysql')->table('matchchallenges')->where('matchkey', $match->matchkey)->first([DB::raw('SUM(maximum_user)as max_users'), DB::raw('SUM(joinedusers)as joinedusers')]);
    //             // dd($ch);
    //             $Json[$arr_param][$i]['max_users'] = $ch->max_users;
    //             $Json[$arr_param][$i]['total_joined'] = $ch->joinedusers;

    //             $Json[$arr_param][$i]['status'] = true;
    //             $i++;
    //         }
    //     }

    //     //joined matches
    //     $findmatchescric = DB::connection('mysql')->table('joinedleauges')
    //         ->join('listmatches', 'listmatches.matchkey', '=', 'joinedleauges.matchkey')
    //         // ->join('series', 'series.id', '=', 'listmatches.series')
    //         ->where('listmatches.fantasy_type', 'Cricket')
    //         ->where('listmatches.launch_status', 'launched')
    //         ->where('joinedleauges.userid', $userid)
    //         ->join('teams as t1', 't1.id', '=', 'listmatches.team1')
    //         ->join('teams as t2', 't2.id', '=', 'listmatches.team2')
    //         ->select('joinedleauges.*', 'listmatches.matchkey', 'listmatches.series_Name as seriesname', 'listmatches.name', 'listmatches.team1', 'listmatches.team2', 't1.team as team1fullname', 't2.team as team2fullname', 'listmatches.start_date', 'listmatches.status', 'listmatches.launch_status', 'listmatches.final_status', 't1.logo as team1logo', 't2.logo as team2logo', 't1.color as team1color', 't2.color as team2color', 't2.short_name as team2short_name', 't1.short_name as team1short_name', 'listmatches.fantasy_type', 'listmatches.playing11_status', 'listmatches.second_inning_status','listmatches.match_notification','listmatches.series_Key as series_id')
    //         ->orderBY('listmatches.start_date', 'DESC')
    //         ->groupBy('joinedleauges.matchkey')
    //         ->limit(3)
    //         ->get();
    //     $j = 0;
    //     if (!empty($findmatchescric->toArray())) {
    //         foreach ($findmatchescric as $match) {
    //             $joineduserss = DB::connection('mysql')->table('joinedleauges')->where('joinedleauges.matchkey', $match->matchkey)->join('matchchallenges', 'matchchallenges.id', '=', 'joinedleauges.challengeid')
    //                 ->where('matchchallenges.status', '!=', 'canceled')->where('joinedleauges.userid', $userid)->groupBy('joinedleauges.challengeid')->select('joinedleauges.id')->get();
    //             $jj = count($joineduserss);
    //             if ($jj == 0) {continue;}
    //             $Json['joined'][$j]['matchkey'] = $match->matchkey;
    //             $Json['joined'][$j]['name'] = $match->name;
    //             $Json['joined'][$j]['team1display'] = $match->team1short_name;
    //             $Json['joined'][$j]['team2display'] = $match->team2short_name;
    //             $Json['joined'][$j]['team1fullname'] = strtoupper($match->team1fullname);
    //             $Json['joined'][$j]['team2fullname'] = strtoupper($match->team2fullname);
    //             $Json['joined'][$j]['start_date'] = date('Y-m-d H:i:s', strtotime($match->start_date));
    //             $locktimes = date('Y-m-d H:i:s', strtotime($match->start_date));
    //             if (date('Y-m-d H:i:s') >= $locktimes) {
    //                 $Json['joined'][$j]['status'] = 'closed';
    //                 $Json['joined'][$j]['match_notification'] = '';
    //             } else {
    //                 $Json['joined'][$j]['status'] = 'opened';
    //                 $Json['joined'][$j]['match_notification'] = $match->match_notification ?? "";
    //             }
    //             $Json['joined'][$j]['launch_status'] = $match->launch_status;
    //             $Json['joined'][$j]['final_status'] = $match->final_status;
    //             if ($match->second_inning_status == 2) {
    //                 $Json['joined'][$j]['series_name'] = $match->seriesname . '- 2nd Inning Match';
    //             } else {
    //                 $Json['joined'][$j]['series_name'] = $match->seriesname;
    //             }
    //             // $Json['joined'][$j]['series_name'] = $match->seriesname;
    //             $Json['joined'][$j]['type'] = $match->fantasy_type;
    //             $Json['joined'][$j]['series_id'] = $match->series_id;

    //             $Json['joined'][$j]['joinedcontest'] = $jj;
    //             if (!empty($match->team1color)) {
    //                 $Json['joined'][$j]['team1color'] = $match->team1color;
    //             } else {
    //                 $Json['joined'][$j]['team1color'] = '#FFFFFF';
    //             }
    //             if (!empty($match->team2color)) {
    //                 $Json['joined'][$j]['team2color'] = $match->team2color;
    //             } else {
    //                 $Json['joined'][$j]['team2color'] = '#FFFFFF';
    //             }

    //             if ($match->team1logo != "") {
    //                 // $Json['joined'][$j]['team1logo'] = $geturl.'public/'.$match->team1logo;

    //                 if (@GetImageSize($geturl . 'public/' . $match->team1logo)) {
    //                     $Json['joined'][$j]['team1logo'] = $geturl . 'public/' . $match->team1logo;
    //                 } else {
    //                     $Json['joined'][$j]['team1logo'] = $geturl . 'public/team_image.png';
    //                 }
    //             } else {
    //                 $Json['joined'][$j]['team1logo'] = $geturl . 'public/team_image.png';
    //             }
    //             if ($match->team2logo != "") {
    //                 // $Json['joined'][$j]['team2logo'] = $geturl.'public/'.$match->team2logo;

    //                 if (@GetImageSize($geturl . 'public/' . $match->team2logo)) {
    //                     $Json['joined'][$j]['team2logo'] = $geturl . 'public/' . $match->team2logo;
    //                 } else {
    //                     $Json['joined'][$j]['team2logo'] = $geturl . 'public/team_image.png';
    //                 }
    //             } else {
    //                 $Json['joined'][$j]['team2logo'] = $geturl . 'public/team_image.png';
    //             }
    //             $Json['joined'][$j]['available_status'] = 1;

    //             $totalTeams = DB::connection('mysql')->table('jointeam')
    //                 ->where('matchkey', $match->matchkey)
    //                 ->where('userid', $userid)
    //                 ->get();

    //             $Json['joined'][$j]['totalTeams'] = count($totalTeams);

    //             $totalcontest = DB::connection('mysql')->table('joinedleauges')
    //                 ->where('matchkey', $match->matchkey)
    //                 ->where('userid', $userid)
    //                 ->groupBy('challengeid')
    //                 ->get();

    //             $Json['joined'][$j]['totalcontest'] = count($totalcontest);

    //             $maximum_winning_amount = DB::connection('mysql')->table('matchchallenges')
    //                 ->where('matchkey', $match->matchkey)
    //                 ->orderBy('win_amount', 'DESC')
    //                 ->value('win_amount');

    //             $Json['joined'][$j]['maximum_winning_amount'] = $maximum_winning_amount;

    //             $total_winning_amount = DB::connection('mysql')->table('finalresults')
    //                 ->where('matchkey', $match->matchkey)
    //                 ->where('userid', $userid)
    //                 ->sum('amount');

    //             $Json['joined'][$j]['total_winning_amount'] = $total_winning_amount;
    //             $Json['joined'][$j]['playing11_status'] = $match->playing11_status;
    //             $j++;
    //         }
    //     } else {
    //         $Json['joined'] = array();
    //     }

    //     if (empty($Json['upcoming'])) {
    //         $Json['upcoming'] = array();
    //     } else {
    //         $Json['upcoming'] = array_values($Json['upcoming']);
    //     }
    //     if (empty($Json['second_inning'])) {
    //         $Json['second_inning'] = array();
    //     } else {
    //         $Json['second_inning'] = array_values($Json['second_inning']);
    //     }

    //     if (empty($Json['joined'])) {
    //         $Json['joined'] = array();
    //     }
    //     return response()->json(array($Json));
    //     die;
    //     //         }
    // }

    public function secondinningmatchlist(Request $request)
    {
        Helpers::setHeader(200);
        $users = Helpers::isAuthorize($request);
        Helpers::timezone();
        $geturl = Helpers::geturl();
        $locktime = Carbon::now();
        $input = $request->all();
        $userid = $users->id;
        $currenttimme = date('Y-m-d H:i:s');
        $lastdays = date('Y-m-d H:i:s', strtotime('+15days', strtotime($currenttimme)));

        $query = DB::connection('mysql')->table('listmatches');
        if (isset($_GET['series'])) {
            $series = $_GET['series'];
            $query = $query->where('series', $series);
        }
        $findmatches = $query->join('series', 'listmatches.series', '=', 'series.id')
            ->join('teams as team1', 'team1.id', '=', 'listmatches.team1')
            ->join('teams as team2', 'team2.id', '=', 'listmatches.team2')
            ->whereBetween('listmatches.start_date', [$currenttimme, $lastdays])
            ->where('listmatches.final_status', '!=', 'IsCanceled')
            ->where('listmatches.final_status', '!=', 'IsAbandoned')
            ->where('series.status', 'opened')
            ->where('listmatches.second_inning_status', 2)
            ->select('listmatches.id as listmatchid', 'team1.short_name as teamname1', 'team2.short_name as teamname2', 'team1.team as team1fullname', 'team2.team as team2fullname', 'team1.color as team1color', 'team2.color as team2color', 'team1.logo as team1logo', 'team2.logo as team2logo', 'listmatches.series as seriesid', 'series.name as seriesname', 'listmatches.name', 'listmatches.start_date', 'listmatches.format', 'listmatches.matchkey', 'listmatches.final_status', 'listmatches.launch_status', 'listmatches.playing11_status', 'listmatches.fantasy_type', 'listmatches.second_inning_status', 'listmatches.real_matchkey')->orderBY('listmatches.start_date', 'ASC')
            ->get();

        $Json = array();
        if (!empty($findmatches)) {
            $i = 0;
            $matchshow = 0;
            foreach ($findmatches as $match) {
                $Json['upcoming'][$i]['id'] = $match->listmatchid;
                $Json['upcoming'][$i]['name'] = $match->name;
                $Json['upcoming'][$i]['format'] = $match->format;
                $Json['upcoming'][$i]['series'] = $match->seriesid;
                $Json['upcoming'][$i]['seriesname'] = $match->seriesname;

                $Json['upcoming'][$i]['team1name'] = strtoupper($match->teamname1);
                $Json['upcoming'][$i]['team2name'] = strtoupper($match->teamname2);
                $Json['upcoming'][$i]['team1fullname'] = strtoupper($match->team1fullname);
                $Json['upcoming'][$i]['team2fullname'] = strtoupper($match->team2fullname);
                $Json['upcoming'][$i]['matchkey'] = $match->matchkey;
                $Json['upcoming'][$i]['type'] = $match->fantasy_type;
                $Json['upcoming'][$i]['winnerstatus'] = $match->final_status;
                $Json['upcoming'][$i]['playing11_status'] = $match->playing11_status;
                $Json['upcoming'][$i]['second_inning_status'] = $match->second_inning_status;

                if (!empty($match->team1color)) {
                    $Json['upcoming'][$i]['team1color'] = $match->team1color;
                } else {
                    $Json['upcoming'][$i]['team1color'] = '#ffffff';
                }
                if (!empty($match->team2color)) {
                    $Json['upcoming'][$i]['team2color'] = $match->team2color;
                } else {
                    $Json['upcoming'][$i]['team2color'] = '#ffffff';
                }
                if ($match->team1logo != "") {

                    if (@GetImageSize($geturl . 'public/' . $match->team1logo)) {
                        $Json['upcoming'][$i]['team1logo'] = $geturl . 'public/' . $match->team1logo;
                    } else {
                        $Json['upcoming'][$i]['team1logo'] = $geturl . 'public/team_image.png';
                    }

                } else {
                    $Json['upcoming'][$i]['team1logo'] = $geturl . 'public/team_image.png';
                }
                if ($match->team2logo != "") {

                    if (@GetImageSize($geturl . 'public/' . $match->team2logo)) {
                        $Json['upcoming'][$i]['team2logo'] = $geturl . 'public/' . $match->team2logo;
                    } else {
                        $Json['upcoming'][$i]['team2logo'] = $geturl . 'public/team_image.png';
                    }

                } else {
                    $Json['upcoming'][$i]['team2logo'] = $geturl . 'public/team_image.png';
                }
                $locktime = date('Y-m-d H:i:s', strtotime($match->start_date));
                if (date('Y-m-d H:i:s') >= $locktime) {
                    $Json['upcoming'][$i]['matchopenstatus'] = 'closed';
                } else {
                    $matchshow++;
                    $Json['upcoming'][$i]['matchopenstatus'] = 'opened';
                }
                $Json['upcoming'][$i]['time_start'] = date('Y-m-d H:i:s', strtotime($match->start_date));
                $Json['upcoming'][$i]['launch_status'] = $match->launch_status;
                $Json['upcoming'][$i]['locktime'] = $locktime;
                if (isset($_GET['userid'])) {
                    $finduserinfo = DB::connection('mysql')->table('registerusers')->where('id', $id)->select('id')->first();
                    if (!empty($finduserinfo)) {
                        $getid = $finduserinfo->id;
                        $findjointeam = DB::connection('mysql')->table('jointeam')->where('userid', $getid)->where('matchkey', $match->matchkey)->orderBY('id', 'DESC')->get();
                        if (!empty($findjointeam)) {
                            $Json['upcoming'][$i]['createteamnumber'] = $findjointeam[0]->teamnumber + 1;
                        } else {
                            $Json['upcoming'][$i]['createteamnumber'] = 1;
                        }
                    }
                }

                $totalTeams = DB::connection('mysql')->table('jointeam')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->get();

                $Json['upcoming'][$i]['totalTeams'] = count($totalTeams);

                $totalcontest = DB::connection('mysql')->table('joinedleauges')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->groupBy('challengeid')
                    ->get();

                $Json['upcoming'][$i]['totalcontest'] = count($totalcontest);

                $maximum_winning_amount = DB::connection('mysql')->table('matchchallenges')
                    ->where('matchkey', $match->matchkey)
                    ->orderBy('win_amount', 'DESC')
                    ->value('win_amount');

                $Json['upcoming'][$i]['maximum_winning_amount'] = $maximum_winning_amount;

                $Json['upcoming'][$i]['status'] = true;
                $i++;
            }
        }
        //joined matches
        $findmatchescric = DB::connection('mysql')->table('joinedleauges')
            ->join('listmatches', 'listmatches.matchkey', '=', 'joinedleauges.matchkey')
            ->join('series', 'series.id', '=', 'listmatches.series')
            ->where('listmatches.fantasy_type', 'Cricket')
            ->where('listmatches.launch_status', 'launched')
            ->where('joinedleauges.userid', $userid)
            ->join('teams as t1', 't1.id', '=', 'listmatches.team1')
            ->join('teams as t2', 't2.id', '=', 'listmatches.team2')
            ->select('joinedleauges.*', 'listmatches.matchkey', 'listmatches.series', 'series.name as seriesname', 'listmatches.name', 'listmatches.team1', 'listmatches.team2', 't1.team as team1fullname', 't2.team as team2fullname', 'listmatches.start_date', 'listmatches.status', 'listmatches.launch_status', 'listmatches.final_status', 't1.logo as team1logo', 't2.logo as team2logo', 't1.color as team1color', 't2.color as team2color', 't2.short_name as team2short_name', 't1.short_name as team1short_name', 'listmatches.fantasy_type', 'series.id as series_id', 'listmatches.playing11_status', 'listmatches.second_inning_status','listmatches.match_notification')
            ->orderBY('listmatches.start_date', 'DESC')
            ->groupBy('joinedleauges.matchkey')
            ->limit(3)
            ->get();
        $j = 0;
        if (!empty($findmatchescric->toArray())) {
            foreach ($findmatchescric as $match) {
                $joineduserss = DB::connection('mysql')->table('joinedleauges')->where('joinedleauges.matchkey', $match->matchkey)->join('matchchallenges', 'matchchallenges.id', '=', 'joinedleauges.challengeid')
                    ->where('matchchallenges.status', '!=', 'canceled')->where('joinedleauges.userid', $userid)->groupBy('joinedleauges.challengeid')->select('joinedleauges.id')->get();
                $jj = count($joineduserss);
                if ($jj == 0) {continue;}
                $Json['joined'][$j]['matchkey'] = $match->matchkey;
                $Json['joined'][$j]['name'] = $match->name;
                $Json['joined'][$j]['team1display'] = $match->team1short_name;
                $Json['joined'][$j]['team2display'] = $match->team2short_name;
                $Json['joined'][$j]['team1fullname'] = strtoupper($match->team1fullname);
                $Json['joined'][$j]['team2fullname'] = strtoupper($match->team2fullname);
                $Json['joined'][$j]['start_date'] = date('Y-m-d H:i:s', strtotime($match->start_date));
                $locktimes = date('Y-m-d H:i:s', strtotime($match->start_date));
                if (date('Y-m-d H:i:s') >= $locktimes) {
                    $Json['joined'][$j]['status'] = 'closed';
                    $Json['joined'][$j]['match_notification'] = '';
                } else {
                    $Json['joined'][$j]['status'] = 'opened';
                    $Json['joined'][$j]['match_notification'] = $match->match_notification ?? "";
                }
                $Json['joined'][$j]['launch_status'] = $match->launch_status;
                $Json['joined'][$j]['final_status'] = $match->final_status;
                if ($match->second_inning_status == 2) {
                    $Json['joined'][$j]['series_name'] = $match->seriesname . '- 2nd Inning Match';
                } else {
                    $Json['joined'][$j]['series_name'] = $match->seriesname;
                }
                // $Json['joined'][$j]['series_name'] = $match->seriesname;
                $Json['joined'][$j]['type'] = $match->fantasy_type;
                $Json['joined'][$j]['series_id'] = $match->series_id;

                $Json['joined'][$j]['joinedcontest'] = $jj;
                if (!empty($match->team1color)) {
                    $Json['joined'][$j]['team1color'] = $match->team1color;
                } else {
                    $Json['joined'][$j]['team1color'] = '#FFFFFF';
                }
                if (!empty($match->team2color)) {
                    $Json['joined'][$j]['team2color'] = $match->team2color;
                } else {
                    $Json['joined'][$j]['team2color'] = '#FFFFFF';
                }

                if ($match->team1logo != "") {
                    // $Json['joined'][$j]['team1logo'] = $geturl.'public/'.$match->team1logo;

                    if (@GetImageSize($geturl . 'public/' . $match->team1logo)) {
                        // $Json['joined'][$j]['team1logo'] = $geturl . 'public/' . $match->team1logo;
                        $c = count(explode(':', $match->team1logo));
                        if ($c >= 2) {
                            $Json['joined'][$j]['team1logo']  = $match->team1logo;
                        } else {
                            $Json['joined'][$j]['team1logo']  = $geturl . '/public/' . $match->team1logo;
                        }
                    } else {
                        $Json['joined'][$j]['team1logo'] = $geturl . 'public/team_image.png';
                    }
                } else {
                    $Json['joined'][$j]['team1logo'] = $geturl . 'public/team_image.png';
                }
                if ($match->team2logo != "") {
                    // $Json['joined'][$j]['team2logo'] = $geturl.'public/'.$match->team2logo;

                    if (@GetImageSize($geturl . 'public/' . $match->team2logo)) {
                        $c = count(explode(':', $match->team2logo));;
                        if ($c >= 2) {
                            $Json['joined'][$j]['team2logo']  = $match->team2logo;
                        } else {
                            $Json['joined'][$j]['team2logo']  = $geturl . '/public/' . $match->team2logo;
                        }
                        // $Json['joined'][$j]['team2logo'] = $geturl . 'public/' . $match->team2logo;
                    } else {
                        $Json['joined'][$j]['team2logo'] = $geturl . 'public/team_image.png';
                    }
                } else {
                    $Json['joined'][$j]['team2logo'] = $geturl . 'public/team_image.png';
                }
                $Json['joined'][$j]['available_status'] = 1;

                $totalTeams = DB::connection('mysql')->table('jointeam')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->get();

                $Json['joined'][$j]['totalTeams'] = count($totalTeams);

                $totalcontest = DB::connection('mysql')->table('joinedleauges')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->groupBy('challengeid')
                    ->get();

                $Json['joined'][$j]['totalcontest'] = count($totalcontest);

                $maximum_winning_amount = DB::connection('mysql')->table('matchchallenges')
                    ->where('matchkey', $match->matchkey)
                    ->orderBy('win_amount', 'DESC')
                    ->value('win_amount');

                $Json['joined'][$j]['maximum_winning_amount'] = $maximum_winning_amount;

                $total_winning_amount = DB::connection('mysql')->table('finalresults')
                    ->where('matchkey', $match->matchkey)
                    ->where('userid', $userid)
                    ->sum('amount');

                $Json['joined'][$j]['total_winning_amount'] = floor($total_winning_amount);
                $Json['joined'][$j]['playing11_status'] = $match->playing11_status;
                $j++;
            }
        } else {
            $Json['joined'] = array();
        }

        if (empty($Json['upcoming'])) {
            $Json['upcoming'] = array();
        } else {
            $Json['upcoming'] = array_values($Json['upcoming']);
        }
        if (empty($Json['second_inning'])) {
            $Json['second_inning'] = array();
        } else {
            $Json['second_inning'] = array_values($Json['second_inning']);
        }

        if (empty($Json['joined'])) {
            $Json['joined'] = array();
        }
        return response()->json(array($Json)); die;     

    }

    

    public function getSeriesWeeklyData(Request $request)
    {
        $input = $request->all();
        $user = Helpers::isAuthorize($request);
        $geturl = Helpers::geturl();
        $series=$input['series'];

        $getseriesd = DB::table('series')->where('id',$series)->first();

        if($getseriesd->has_leaderboard == 'yes'){
            $getdata=DB::connection('mysql')->table('series_leaderboard')
        		->where('series_id',$series)->join('listmatches','listmatches.matchkey','=','series_leaderboard.matchkey')								
				->join('registerusers','registerusers.id','series_leaderboard.userid')
        		->select('listmatches.final_status','series_leaderboard.userid','series_leaderboard.series_id','registerusers.id as rid','registerusers.team as rteam','registerusers.image as rimage',DB::raw('sum(series_leaderboard.points) as totalpoints'))
        		->groupBy('series_leaderboard.userid')
        		->orderBy('totalpoints','desc')
        		->get();
        }else{
            $getdata=DB::connection('mysql')->table('listmatches')
        		->where('series',$series)
        		->where('launch_status','=','launched')
        		->where('final_status','=','winnerdeclared')
        		->join('jointeam','jointeam.matchkey','=','listmatches.matchkey')
        		->join('joinedleauges','joinedleauges.teamid','jointeam.id')
				->join('matchchallenges','matchchallenges.id','joinedleauges.challengeid')
				->join('contest_category','contest_category.id','matchchallenges.contest_cat')								
				->join('registerusers','registerusers.id','jointeam.userid')
        		->select('listmatches.final_status','jointeam.userid','listmatches.series','registerusers.id as rid','registerusers.team as rteam','registerusers.image as rimage',DB::raw('sum(jointeam.points) as totalpoints'))
        		->groupBy('jointeam.userid')
        		->orderBy('totalpoints','desc')
        		->get();
        }
        

        $Json = [];
        $series_pricecard = [];
        if(!empty($getdata)){
            $getpricecard  = DB::connection('mysql')->table('seriespricecards')->where('series_id',$series)->get();
            if(!empty($getpricecard)){
                $r=0;
                foreach($getpricecard as $pricecard){
                    $series_pricecard[$r]['series_id'] = $pricecard->series_id;
                    $series_pricecard[$r]['winners'] = $pricecard->winners;
                    $series_pricecard[$r]['price'] = $pricecard->price;
                    $series_pricecard[$r]['min_position'] = $pricecard->min_position;
                    $series_pricecard[$r]['max_position'] = $pricecard->max_position;
                    $series_pricecard[$r]['total'] = $pricecard->total;
                    $series_pricecard[$r]['type'] = $pricecard->type;
                    $series_pricecard[$r]['price_percent'] = $pricecard->price_percent;
                    $series_pricecard[$r]['rank'] = $r+1;
                    $r++;
                }
            }
            
            $ii=0;
            $ioo=1;$self='';$self_in=0;
            foreach($getdata as $val){
                if(!empty($val)){
                    if($val->rid==$user->id){
                        $Json[$ii]['userid'] = $user->id;
                        $Json[$ii]['team'] = $val->rteam;
		                if(empty($val->rimage) or $val->rimage == 'null'){
		                    $Json[$ii]['image'] = $geturl.'public/'.Helpers::settings()->user_image ?? '';
		                }else{

							// if( @GetImageSize($val->rimage) ) {
								$Json[$ii]['image'] = $val->rimage;
							// } else {
							// 	$Json[$ii]['image'] = $geturl.'public/user_image.png';
							// }
		                }
                        $Json[$ii]['points'] = number_format($val->totalpoints, 2, '.', '');
                        $Json[$ii]['rank'] = $ioo;
                        $Json[$ii]['userno'] = -1;
                        $self = $Json[$ii];
                        $self_in = $ii;
                    }else{
                        $Json[$ii]['userid'] = $val->rid;
                        $Json[$ii]['team'] = $val->rteam;
		                if(empty($val->rimage) or $val->rimage == 'null'){
		                    $Json[$ii]['image'] = $geturl.'public/'.Helpers::settings()->user_image ?? '';
		                }else{
		                   // if( @GetImageSize($val->rimage) ) {
								$Json[$ii]['image'] = 'public/'.$val->rimage;
							// } else {
							// 	$Json[$ii]['image'] = $geturl.'public/user_image.png';
							// }
		                }
                        $Json[$ii]['points'] = number_format($val->totalpoints, 2, '.', '');
                        $Json[$ii]['rank'] = $ioo;
                        $Json[$ii]['userno'] = 0;
                    }
                   	$ii++;
                    $ioo++;
                }
            }
        }
            array_multisort(array_column($Json, 'rank'), SORT_ASC, $Json);
        	if($self!=''){
        		$js = array_unshift($Json, $self);
				unset($Json[$self_in+1]);
				$Json = array_values($Json);
        	}
            $my['pricecard'] = $series_pricecard;
            $my['data'] = $Json;
        return response()->json(array($my));
    }

    public function getduoallplayers(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $input = $request->all();
            $id = $user->id;
           
            $input['duotype'] = $duotype = $request->get('duotype');
            unset($input['auth_key']);
            $matchkey = $request->get('matchkey');
            $challenge_id = $request->get('challenge_id');
            $Json = array();
            $findmatch = DB::connection('mysql')->table('listmatches')
                ->join('teams as t1', 't1.id', '=', 'listmatches.team1')
                ->join('teams as t2', 't2.id', '=', 'listmatches.team2')
                ->where('listmatches.matchkey', $matchkey)
                ->orderBy('listmatches.start_date', 'desc')
                ->select('listmatches.*', 't1.short_name as teamdata1_short_name', 't2.short_name as teamdata2_short_name')
                ->first();

            if (!empty($findmatch)) {
                $team1 = $findmatch->team1;
                $team2 = $findmatch->team2;
                $getseries = $findmatch->series;
                $allmatchkey = array();
                $findallmatches = DB::connection('mysql')->table('listmatches')->where('series', $getseries)->select('matchkey')->get();
                if (!empty($findallmatches)) {
                    foreach ($findallmatches as $matches) {
                        $allmatchkey[] = $matches->matchkey;
                    }
                }
                if($duotype=='batsman'){
                    $findpplayers = DB::connection('mysql')->table('matchplayers')
                    ->join('players', 'players.id', 'matchplayers.playerid')
                    ->where('matchplayers.matchkey', $matchkey)
                    ->whereIn('matchplayers.role',['batsman','keeper'])
                    ->where('matchplayers.forduo', 1)
                    ->orderBy('players.points', 'DESC')
                    ->select('matchplayers.*', 'players.id as matchplayers_playerid_id',
                        'players.team as matchplayers_playerid_team', 'players.player_name as matchplayers_playerid_player_name', 'players.players_key as matchplayers_playerid_players_key', 'players.image as matchplayers_playerid_image')
                    ->get();
                }else{
                    $findpplayers = DB::connection('mysql')->table('matchplayers')
                    ->join('players', 'players.id', 'matchplayers.playerid')
                    ->where('matchplayers.matchkey', $matchkey)
                    ->whereIn('matchplayers.role',['bowler','allrounder'])
                    ->where('matchplayers.forduo', 1)
                    ->orderBy('players.points', 'DESC')
                    ->select('matchplayers.*', 'players.id as matchplayers_playerid_id',
                        'players.team as matchplayers_playerid_team', 'players.player_name as matchplayers_playerid_player_name', 'players.players_key as matchplayers_playerid_players_key', 'players.image as matchplayers_playerid_image')
                    ->get();
                }
                
                if (!empty($findpplayers)) {
                    $i = 0;
                    foreach ($findpplayers as $pp) {
                       
                        $findallplayers = DB::connection('mysql')->table('matchplayers')->whereIn('matchkey', $allmatchkey)->where('playerid', $pp->matchplayers_playerid_id)->select(DB::raw("SUM(points) as totalpoints"))->get();
                        if (($pp->matchplayers_playerid_team == $team2) || ($pp->matchplayers_playerid_team == $team1)) {
                            $Json[$i]['id'] = $pp->matchplayers_playerid_id;
                            $Json[$i]['name'] = $pp->matchplayers_playerid_player_name;
                            $Json[$i]['role'] = $pp->role;
                            $Json[$i]['credit'] = $pp->credit;
                            $Json[$i]['playingstatus'] = $pp->playingstatus;
                            $Json[$i]['playerkey'] = $pp->matchplayers_playerid_players_key;
                            if ($pp->matchplayers_playerid_image != "") {
                                $Json[$i]['image'] = $geturl . 'public/' . $pp->matchplayers_playerid_image;
                            } else {
                                $Json[$i]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                            }
                            if (!empty($findallplayers[0]->totalpoints)) {
                                $Json[$i]['points'] = $findallplayers[0]->totalpoints;
                            } else {
                                $Json[$i]['points'] = 0;
                            }
                            $findtplayer = DB::connection('mysql')->table('joinedleauges')->where('challengeid', $challenge_id)->where('matchkey', $matchkey)->where('player_id', $pp->matchplayers_playerid_id)->first();

                            if(!empty($findtplayer)){
                                $Json[$i]['isSelected'] = true;
                            }else{
                                $Json[$i]['isSelected'] = false;
                            }
                            
                            if ($pp->matchplayers_playerid_team == $team1) {
                                $Json[$i]['team'] = 'team1';
                                $Json[$i]['teamname'] = strtoupper($findmatch->teamdata1_short_name);
                            }
                            if ($pp->matchplayers_playerid_team == $team2) {
                                $Json[$i]['team'] = 'team2';
                                $Json[$i]['teamname'] = strtoupper($findmatch->teamdata2_short_name);
                            }

                            $i++;
                        }
                    }
                }
            }
            //     $gtlastranks = Helpers::multid_sort($Json, 'player_selection_percentage');
            //     dd($Json);
            return response()->json($Json);
            die;
        }
    }

    public function getduoleaderboard(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $input = $request->all();
            $id = $user->id;
            unset($input['auth_key']);
            $matchkey = $request->get('matchkey');
            $challenge_id = $request->get('challenge_id');
            $Json = array();
            $findplayer = DB::connection('mysql')->table('joinedleauges')->join('matchplayers', 'joinedleauges.player_id', 'matchplayers.playerid')->join('players', 'players.id', 'matchplayers.playerid')->where('joinedleauges.challengeid', $challenge_id)->where('joinedleauges.matchkey', $matchkey)->groupBy('matchplayers.playerid')->get();
            // dd($findplayer);
            if (!empty($findplayer->toArray())) {
                $i=0;
                foreach($findplayer as $val){
                    $Json[$i]['id'] = $val->player_id;
                    $Json[$i]['name'] = $val->player_name;
                    $Json[$i]['role'] = $val->role;
                    $Json[$i]['credit'] = $val->credit;
                    $Json[$i]['playingstatus'] = $val->playingstatus;
                    $Json[$i]['playerkey'] = $val->players_key;
                    if ($val->image != "") {
                        $Json[$i]['image'] = $geturl . 'public/' . $val->image;
                    } else {
                        $Json[$i]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                    }
                    $Json[$i]['points'] = $val->duopoints;
                    $i++;
                }
            }
            //     $gtlastranks = Helpers::multid_sort($Json, 'player_selection_percentage');
            //     dd($Json);
            return response()->json($Json);
            die;
        }
    }

    public function getliveduoleaderboard(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $input = $request->all();
            $id = $user->id;
            unset($input['auth_key']);
            $matchkey = $request->get('matchkey');
            $challenge_id = $request->get('challengeid');
            $Json = array();
            $findplayer = DB::connection('mysql')->table('joinedleauges')->join('matchplayers', 'joinedleauges.player_id', 'matchplayers.playerid')->join('players', 'players.id', 'matchplayers.playerid')->where('joinedleauges.challengeid', $challenge_id)->where('matchplayers.forduo', 1)->where('joinedleauges.matchkey', $matchkey)->where('joinedleauges.close_status', 1)->groupBy('matchplayers.playerid')->get();
            // dd($findplayer);
            if (!empty($findplayer->toArray())) {
                $i=0;
                $gtlastranks = array();
                $pdfname = "";
                $userrank = "";
                $getcurrentrankarray = array();
                $ss = 0;
                $a = $findplayer->toArray();
                if (!empty($a)) {
                    foreach ($findplayer as $pleauges) {
                        $gtlastranks[$ss]['lastpoints'] = $pleauges->lastduopoints;
                        $gtlastranks[$ss]['userid'] = $pleauges->userid;
                        $getcurrentrankarray[$ss]['points'] = $pleauges->duopoints;
                        $getcurrentrankarray[$ss]['userid'] = $pleauges->userid;
                        $ss++;
                    }
                }
                $gtlastranks = Helpers::multid_sort($gtlastranks, 'lastpoints');
                if (!empty($gtlastranks)) {
                    $getuserlastrank = array();
                    $lr = 0;
                    $lrsno = 0;
                    $uplus = 0;
                    foreach ($gtlastranks as $lrnk) {
                        if (in_array($lrnk['lastpoints'], array_column($getuserlastrank, 'points'))) { // search value in the array
                            $lrsno++;
                            $lrsno = $lrsno + $uplus;
                            $uplus = 0;
                        } else {
                            $lrsno++;
                        }
                        $getuserlastrank[$lr]['rank'] = $lrsno;
                        $getuserlastrank[$lr]['points'] = $lrnk['lastpoints'];
                        $getuserlastrank[$lr]['userid'] = $lrnk['userid'];
                        $lr++;
    
                    }
                }
                //get current ranks//
                $gtcurranks = Helpers::multid_sort($getcurrentrankarray, 'points');
                if (!empty($gtcurranks)) {
                    $getusercurrank = array();
                    $cur = 0;
                    $currsno = 0;
                    $plus = 0;
                    foreach ($gtcurranks as $curnk) {
                        if (!in_array($curnk['points'], array_column($getusercurrank, 'points'))) { // search value in the array
                            $currsno++;
                            $currsno = $currsno + $plus;
                            $plus = 0;
                        } else {
                            $plus++;
                        }
                        $getusercurrank[$cur]['rank'] = $currsno;
                        $getusercurrank[$cur]['points'] = $curnk['points'];
                        $getusercurrank[$cur]['userid'] = $curnk['userid'];
                        $cur++;
                    }
                }
                foreach($findplayer->unique('id') as $val){
                    $userrank = 1;
                    $userslistsno = -1;
                    $userrankarray = array();

                    $Json[$i]['id'] = $val->player_id;
                    $Json[$i]['name'] = $val->player_name;
                    $Json[$i]['role'] = $val->role;
                    $Json[$i]['credit'] = $val->credit;
                    $Json[$i]['playingstatus'] = $val->playingstatus;
                    $Json[$i]['playerkey'] = $val->players_key;
                    if ($val->image != "") {
                        $Json[$i]['image'] = $geturl . 'public/' . $val->image;
                    } else {
                        $Json[$i]['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
                    }
                    $Json[$i]['points'] = $val->duopoints;


                    $getuserindexinglast = Helpers::searchByValue($getuserlastrank, 'points', $val->duopoints);
                    $getlastrank = $getuserlastrank[$getuserindexinglast]['rank'];
                    $getuserindexingcurent = Helpers::searchByValue($getusercurrank, 'points', $val->duopoints);
                    $getcurrentrank = $getusercurrank[$getuserindexingcurent]['rank'];
                    $Json[$i]['getcurrentrank'] = $getcurrentrank;

                    $total_winning_amount = DB::connection('mysql')->table('finalresults')
                    ->where('matchkey', $matchkey)
                    ->where('challengeid', $challenge_id)
                    ->where('rank', $getcurrentrank)
                    ->first();
                    if(empty($total_winning_amount->amount)){
                        $Json[$i]['winingamount'] = '';
                    }else{
                        $Json[$i]['winingamount'] = $total_winning_amount->amount;
                    }
                    
                    if ($getlastrank < $getcurrentrank) {
                        $Json[$i]['arrowname'] = 'down-arrow';
                    } else if ($getlastrank == $getcurrentrank) {
                        $Json[$i]['arrowname'] = 'equal-arrow';
                    } else if ($getlastrank > $getcurrentrank) {
                        $Json[$i]['arrowname'] = 'up-arrow';
                    }
                    $i++;
                }
            }
            return response()->json($Json);
            die;
        }
    }

    public function matchlivedata(Request $request){
        $input = $request->all();
        $user = Helpers::isAuthorize($request);
		$match = DB::connection('mysql')->table('listmatches')->where('matchkey', $request->get('matchkey'))->first();
        
		// dd($match);

		$json = array();

		if (!empty($match)) {
            
			$matchkey = $request->get('matchkey');
            
			$real_matchkey = $match->real_matchkey;
            
			$giveresresult = EntityCricketapiController::getmatchscore($real_matchkey);

			if (!empty($giveresresult)) {

				if(
					!empty($giveresresult['response']['innings'])
				) {
					foreach(
						$giveresresult['response']['innings']
						as
						$inning
						=>
						$inning_data
					) {
						
						$json[$inning]['name'] = $inning_data['name'];
						$json[$inning]['scores'] = $inning_data['scores_full'];
						if(!empty($inning_data['batsmen'])){
							foreach($inning_data['batsmen'] as $batsman => $batsman_data) {
								
								$json[$inning]['batsmen'][$batsman]['name'] = $batsman_data['name'];
								$json[$inning]['batsmen'][$batsman]['role'] = $batsman_data['role'];
								$json[$inning]['batsmen'][$batsman]['how_out'] = $batsman_data['how_out'];
								$json[$inning]['batsmen'][$batsman]['runs'] = $batsman_data['runs'];
								$json[$inning]['batsmen'][$batsman]['balls'] = $batsman_data['balls_faced'];
								$json[$inning]['batsmen'][$batsman]['fours'] = $batsman_data['fours'];
								$json[$inning]['batsmen'][$batsman]['sixes'] = $batsman_data['sixes'];
								$json[$inning]['batsmen'][$batsman]['strike_rate'] = $batsman_data['strike_rate'];
							}
						}else{
							$json[$inning]['batsmen']=[];
						}

						$json[$inning]['extras'] = array($inning_data['extra_runs']);
						$json[$inning]['total'] = array($inning_data['equations']);
						$json[$inning]['did_not_bat'] = '';
						
						foreach($inning_data['did_not_bat'] as $did_not_bat => $did_not_bat_data) {
							$json[$inning]['did_not_bat'] .= $did_not_bat_data['name'];

							// , not at last
							if( 
								(count($inning_data['did_not_bat']) - 1) == $did_not_bat
							) {

							} else {
								$json[$inning]['did_not_bat'] .= ', ';

							}

						}
						if(!empty($inning_data['bowlers'])){
							foreach($inning_data['bowlers'] as $bowler => $bowler_data) {
								
								$json[$inning]['bowlers'][$bowler]['name'] = $bowler_data['name'];
								$json[$inning]['bowlers'][$bowler]['overs'] = $bowler_data['overs'];
								$json[$inning]['bowlers'][$bowler]['maidens'] = $bowler_data['maidens'];
								$json[$inning]['bowlers'][$bowler]['runs'] = $bowler_data['runs_conceded'];
								$json[$inning]['bowlers'][$bowler]['wickets'] = $bowler_data['wickets'];
								$json[$inning]['bowlers'][$bowler]['economy_rate'] = $bowler_data['econ'];
							}
						}else{
							$json[$inning]['bowlers']=[];
						}

						if(!empty($inning_data['fows'])){
							foreach($inning_data['fows'] as $fall_of_wicket => $fall_of_wicket_data) {
								
								$json[$inning]['fall_of_wickets'][$fall_of_wicket]['name'] = $fall_of_wicket_data['name'];
								$json[$inning]['fall_of_wickets'][$fall_of_wicket]['runs'] = $fall_of_wicket_data['runs'];
								$json[$inning]['fall_of_wickets'][$fall_of_wicket]['balls'] = $fall_of_wicket_data['balls'];
								$json[$inning]['fall_of_wickets'][$fall_of_wicket]['score_at_dismissal'] = $fall_of_wicket_data['score_at_dismissal'];
								$json[$inning]['fall_of_wickets'][$fall_of_wicket]['overs_at_dismissal'] = $fall_of_wicket_data['overs_at_dismissal'];
								$json[$inning]['fall_of_wickets'][$fall_of_wicket]['number'] = $fall_of_wicket_data['number'];
							}
						}else{
							$json[$inning]['fall_of_wickets']=[];
						}

					}	
				}
			}
        }

		return response()
				->json($json);
    }
    // public function matchlivedata(Request $request){
    //     $match = DB::connection('mysql')->table('listmatches')->where('matchkey', $request->get('matchkey'))->first();
    //     if (!empty($match)) {
    //         $matchkey = $request->get('matchkey');
    //         $real_matchkey = $match->real_matchkey;
    //         if($match->format!='test'){
    //             return $this->scorecardForOthers($match,$matchkey,$real_matchkey);
    //         }else{
    //             return $this->scorecardForTest($match,$matchkey,$real_matchkey);
    //         }
    //     }
    // }
    
    public function scorecardForOthers($match,$matchkey,$real_matchkey){
        $giveresresult = CricketapiController::getmatchdata($real_matchkey);
        if (!empty($giveresresult)) {

            $matchdata['matchkey'] = $matchkey;
            $matchdata['teams1'] = $giveresresult['data']['card']['teams']['a']['short_name'];
            $matchdata['teams2'] = $giveresresult['data']['card']['teams']['b']['short_name'];
            $batting_order = [];
            $bo = 0;
            $first_batting = (!empty($giveresresult['data']['card']['first_batting'])) ? (($giveresresult['data']['card']['first_batting'] == 'a') ? 'a' : 'b') : null;
            $t = $giveresresult['data']['card']['batting_order'];
            $ti = 0;
            $ts = [];
            foreach ($t as $tkey => $tvalue) {
                $ts[$ti] = $tvalue[0];
                $tsi[$ti] = $tvalue[0] . '_' . $tvalue[1];
                $ti++;
            }
            if (!empty($first_batting)) {
                $ts = [$first_batting, ($first_batting == 'a') ? 'b' : 'a'];
                $teams = $ts;
                $tmi = 0;
                $innData = $giveresresult['data']['card']['innings'];
                foreach ($teams as $tm) {
                    $teamlinup[$tmi]['playing_XI'] = $giveresresult['data']['card']['teams'][$tm]['match']['playing_xi'];
                    $tmi++;
                }
                $i = 0;
                foreach ($teams as $key1 => $value1) {
                    $j = 0;
                    $inn = ($i < 2) ? 1 : 2;
                    foreach ($teamlinup[$i]['playing_XI'] as $key => $value) {
                        $pdata = DB::connection('mysql')->table('result_matches')
                            ->join('players', 'players.id', 'result_matches.player_id')
                            ->where('result_matches.innings', $inn)
                            ->where('result_matches.player_key', $value)
                            ->where('result_matches.match_key', $matchkey)
                            ->first(['players.player_name', 'result_matches.*']);
                        if (isset($giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]) && empty($pdata)) {
                            $teamlinup[$i]['matchformat'] = $match->format;
                            $teamlinup[$i]['totalruns'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['runs'];
                            $teamlinup[$i]['totalwickets'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['wickets'];
                            if ($match->format == '100-ball') {
                                $teamlinup[$i]['totalovers'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['balls'];
                            } else {
                                $teamlinup[$i]['totalovers'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['overs'];
                            }

                            // $teamlinup[$i]['teamname'] = $giveresresult['data']['card']['teams'][$teams[$i]]['short_name'];
                            $teamlinup[$i]['teamname'] = $this->getTeamName($giveresresult['data']['card']['teams'][$teams[$i]]['key'],$match);
                            $teamlinup[$i]['totalfours'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['fours'];
                            $teamlinup[$i]['totalsixes'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['sixes'];
                            $teamlinup[$i]['totalextras'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['extras'];
                            $teamlinup[$i]['totalwide'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['wide'];
                            $teamlinup[$i]['totalnoball'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['noball'];
                            $teamlinup[$i]['totallegbye'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['legbye'];
                            $teamlinup[$i]['totalbye'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['bye'];
                            $teamlinup[$i]['totalpenalty'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['penalty'];
                            $teamlinup[$i]['inning'] = $inn;
                            continue;
                        }else {
                            $teamlinup[$i]['matchformat'] = $match->format;
                            $teamlinup[$i]['totalruns'] = 0;
                            $teamlinup[$i]['totalwickets'] = 0;
                            if ($match->format == '100-ball') {
                                $teamlinup[$i]['totalovers'] = 0;
                            } else {
                                $teamlinup[$i]['totalovers'] = 0;
                            }
                            // $teamlinup[$i]['teamname'] = $giveresresult['data']['card']['teams'][$teams[$i]]['short_name'];
                            $teamlinup[$i]['teamname'] = $this->getTeamName($giveresresult['data']['card']['teams'][$teams[$i]]['key'],$match);
                            $teamlinup[$i]['totalfours'] = 0;
                            $teamlinup[$i]['totalsixes'] = 0;
                            $teamlinup[$i]['totalextras'] = 0;
                            $teamlinup[$i]['totalwide'] = 0;
                            $teamlinup[$i]['totalnoball'] = 0;
                            $teamlinup[$i]['totallegbye'] = 0;
                            $teamlinup[$i]['totalbye'] = 0;
                            $teamlinup[$i]['totalpenalty'] = 0;
                            $teamlinup[$i]['inning'] = $inn;
                        }
                        if (isset($giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn])) {
                            $teamlinup[$i]['matchformat'] = $match->format;
                            $teamlinup[$i]['totalruns'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['runs'];
                            $teamlinup[$i]['totalwickets'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['wickets'];
                            if ($match->format == '100-ball') {
                                $teamlinup[$i]['totalovers'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['balls'];
                            } else {
                                $teamlinup[$i]['totalovers'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['overs'];
                            }
                            // $teamlinup[$i]['teamname'] = $giveresresult['data']['card']['teams'][$teams[$i]]['short_name'];
                            $teamlinup[$i]['teamname'] = $this->getTeamName($giveresresult['data']['card']['teams'][$teams[$i]]['key'],$match);
                            $teamlinup[$i]['totalfours'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['fours'];
                            $teamlinup[$i]['totalsixes'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['sixes'];
                            $teamlinup[$i]['totalextras'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['extras'];
                            $teamlinup[$i]['totalwide'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['wide'];
                            $teamlinup[$i]['totalnoball'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['noball'];
                            $teamlinup[$i]['totallegbye'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['legbye'];
                            $teamlinup[$i]['totalbye'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['bye'];
                            $teamlinup[$i]['totalpenalty'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['penalty'];
                            $teamlinup[$i]['inning'] = $inn;

                            $teamlinup[$i]['batting_players'][$pdata->player_key] = [
                                'id' => $pdata->player_id,
                                'player_name' => $pdata->player_name,
                                'player_key' => $pdata->player_key,
                                'innings' => $pdata->innings,
                                'batting' => $pdata->batting,
                                'runs' => $pdata->runs,
                                'six' => $pdata->six,
                                'fours' => $pdata->fours,
                                'bball' => $pdata->bball,
                                'strike_rate' => number_format($pdata->strike_rate, 2, '.', ''),
                                'batting_status' => ($pdata->out_str != 'not out') ? $pdata->out_str : (($pdata->bball != 0) ? 'Not Out' : ''),
                                'playing_status' => ($pdata->bball != 0) ? 1 : 0,
                            ];
                            if($pdata->balls!=0){
                                $rbp =  number_format($pdata->grun / $pdata->balls, 2, '.', '');
                            }else{
                                $rbp = 0;
                            }
                            if ($pdata->bowling == 1) {
                                $teamlinup[$i]['bowling_players'][] = [
                                    'id' => $pdata->player_id,
                                    'player_name' => $pdata->player_name,
                                    'innings' => $pdata->innings,
                                    'wicket' => $pdata->wicket,
                                    'overs' => $pdata->overs,
                                    'runs' => $pdata->grun,
                                    'maiden_over' => $pdata->maiden_over,
                                    'balls' => $pdata->balls,
                                    'economy_rate' => number_format($pdata->economy_rate, 2, '.', ''),
                                    'rpb' => $rbp,
                                    'balldots' => $pdata->balldots,
                                    'extra' => $pdata->extra,
                                ];
                            }
                            if (!array_key_exists('bowling_players', $teamlinup[$i])) {
                                $teamlinup[$i]['bowling_players'] = [];
                            }
                            $j++;
                        }

                    }

                    $batsmans = (isset($giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn])) ? $giveresresult['data']['card']['innings'][$teams[$i] . '_' . $inn]['batting_order'] : [];
                    $teamlinup[$i]['batsmans'] = array();
                    if (array_key_exists('batting_players', $teamlinup[$i])) {
                        if (!empty($batsmans)) {
                            foreach ($batsmans as $key => $value) {
                                if (array_key_exists($value, $teamlinup[$i]['batting_players'])) {
                                    $teamlinup[$i]['batsmans'][$value] = $teamlinup[$i]['batting_players'][$value];
                                    unset($teamlinup[$i]['batting_players'][$value]);
                                }

                            }
                        }
                    } else {
                        $teamlinup[$i]['batting_players'] = [];
                        if (!array_key_exists('bowling_players', $teamlinup[$i])) {
                            $teamlinup[$i]['bowling_players'] = [];
                        }
                    }

                    $teamlinup[$i]['batting_players'] = array_values(array_merge($teamlinup[$i]['batsmans'], $teamlinup[$i]['batting_players']));
                    unset($teamlinup[$i]['batsmans']);
                    $i++;
                }
                $tmj = 0;
                foreach ($teams as $tm) {
                    unset($teamlinup[$tmj]['playing_XI']);
                    switch ($tmj) {
                        case '0':
                            $json[$tmj] = $teamlinup[$tmj];
                            $json[$tmj]['bowling_players'] = $teamlinup[$tmj + 1]['bowling_players'];
                            break;
                        case '1':
                            $json[$tmj] = $teamlinup[$tmj];
                            $json[$tmj]['bowling_players'] = $teamlinup[$tmj - 1]['bowling_players'];
                            break;
                        case '2':
                            $json[$tmj] = $teamlinup[$tmj];
                            $json[$tmj]['bowling_players'] = $teamlinup[$tmj + 1]['bowling_players'];
                            break;
                        case '3':
                            $json[$tmj] = $teamlinup[$tmj];
                            $json[$tmj]['bowling_players'] = $teamlinup[$tmj - 1]['bowling_players'];
                            break;
                        default:
                            break;
                    }

                    $tmj++;
                }
                return response()->json(collect($json)->reverse()->values()->toArray());
            } else {
                return response()->json([]);
            }
        }
    }
    
    public function scorecardForTest($match,$matchkey,$real_matchkey){
        $giveresresult = CricketapiController::getmatchdata($real_matchkey);
        if (!empty($giveresresult)) {

            $matchdata['matchkey'] = $matchkey;
            $matchdata['teams1'] = $giveresresult['data']['card']['teams']['a']['short_name'];
            $matchdata['teams2'] = $giveresresult['data']['card']['teams']['b']['short_name'];
            $batting_order = [];
            $bo = 0;
            $first_batting = (!empty($giveresresult['data']['card']['first_batting'])) ? (($giveresresult['data']['card']['first_batting'] == 'a') ? 'a' : 'b') : null;
            $t = $giveresresult['data']['card']['batting_order'];
            $ti = 0;
            $ts = [];
            foreach ($t as $tkey => $tvalue) {
                $ts[$ti] = $tvalue[0];
                $tsi[$ti] = $tvalue[0] . '_' . $tvalue[1];
                $ti++;
            }
            if (!empty($first_batting)) {
                $innc=0;
                while ($innc <= 1) {
                    $ts = [$first_batting, ($first_batting == 'a') ? 'b' : 'a'];
                    $teams[] = $ts;
                    $innc++;
                }
                $teams = call_user_func_array('array_merge', $teams);
                $tmi = 0;
                $innData = $giveresresult['data']['card']['innings'];
                while ($tmi <= 3) {
                    foreach ($teams as $tm) {
                        $teamlinup[$tmi]['playing_XI'] = $giveresresult['data']['card']['teams'][$tm]['match']['playing_xi'];
                        $tmi++;
                    }
                }
                $i = 0;
                while ($i <= 3) {
                    $j = 0;
                    $inn = ($i < 2) ? 1 : 2;
                    foreach ($teamlinup[$i]['playing_XI'] as $key => $value) {
                        $pdata = DB::connection('mysql')->table('result_matches')
                            ->join('players', 'players.id', 'result_matches.player_id')
                            ->where('result_matches.innings', $inn)
                            ->where('result_matches.player_key', $value)
                            ->where('result_matches.match_key', $matchkey)
                            ->first(['players.player_name', 'result_matches.*']);
                        if (isset($giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]) && empty($pdata)) {
                            $teamlinup[$i]['matchformat'] = $match->format;
                            $teamlinup[$i]['totalruns'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['runs'];
                            $teamlinup[$i]['totalwickets'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['wickets'];
                            if ($match->format == '100-ball') {
                                $teamlinup[$i]['totalovers'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['balls'];
                            } else {
                                $teamlinup[$i]['totalovers'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['overs'];
                            }
                            $teamlinup[$i]['teamname'] = $giveresresult['data']['card']['teams'][$teams[$i]]['short_name'];
                            $teamlinup[$i]['totalfours'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['fours'];
                            $teamlinup[$i]['totalsixes'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['sixes'];
                            $teamlinup[$i]['totalextras'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['extras'];
                            $teamlinup[$i]['totalwide'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['wide'];
                            $teamlinup[$i]['totalnoball'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['noball'];
                            $teamlinup[$i]['totallegbye'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['legbye'];
                            $teamlinup[$i]['totalbye'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['bye'];
                            $teamlinup[$i]['totalpenalty'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['penalty'];
                            $teamlinup[$i]['inning'] = $inn;
                            continue;
                        }else {
                            $teamlinup[$i]['matchformat'] = $match->format;
                            $teamlinup[$i]['totalruns'] = 0;
                            $teamlinup[$i]['totalwickets'] = 0;
                            if ($match->format == '100-ball') {
                                $teamlinup[$i]['totalovers'] = 0;
                            } else {
                                $teamlinup[$i]['totalovers'] = 0;
                            }
                            $teamlinup[$i]['teamname'] = $giveresresult['data']['card']['teams'][$teams[$i]]['short_name'];
                            $teamlinup[$i]['totalfours'] = 0;
                            $teamlinup[$i]['totalsixes'] = 0;
                            $teamlinup[$i]['totalextras'] = 0;
                            $teamlinup[$i]['totalwide'] = 0;
                            $teamlinup[$i]['totalnoball'] = 0;
                            $teamlinup[$i]['totallegbye'] = 0;
                            $teamlinup[$i]['totalbye'] = 0;
                            $teamlinup[$i]['totalpenalty'] = 0;
                            $teamlinup[$i]['inning'] = $inn;
                        }
                        if (isset($giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn])) {
                            $teamlinup[$i]['matchformat'] = $match->format;
                            $teamlinup[$i]['totalruns'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['runs'];
                            $teamlinup[$i]['totalwickets'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['wickets'];
                            if ($match->format == '100-ball') {
                                $teamlinup[$i]['totalovers'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['balls'];
                            } else {
                                $teamlinup[$i]['totalovers'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['overs'];
                            }
                            $teamlinup[$i]['teamname'] = $giveresresult['data']['card']['teams'][$teams[$i]]['short_name'];
                            $teamlinup[$i]['totalfours'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['fours'];
                            $teamlinup[$i]['totalsixes'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['sixes'];
                            $teamlinup[$i]['totalextras'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['extras'];
                            $teamlinup[$i]['totalwide'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['wide'];
                            $teamlinup[$i]['totalnoball'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['noball'];
                            $teamlinup[$i]['totallegbye'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['legbye'];
                            $teamlinup[$i]['totalbye'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['bye'];
                            $teamlinup[$i]['totalpenalty'] = $giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn]['penalty'];
                            $teamlinup[$i]['inning'] = $inn;
                            if($pdata->balls==0){
                                $rpb =0;
                            }else{
                                $rpb =number_format($pdata->grun / $pdata->balls, 2, '.', '');
                            }
                            $teamlinup[$i]['batting_players'][$pdata->player_key] = [
                                'id' => $pdata->player_id,
                                'player_name' => $pdata->player_name,
                                'player_key' => $pdata->player_key,
                                'innings' => $pdata->innings,
                                'batting' => $pdata->batting,
                                'runs' => $pdata->runs,
                                'six' => $pdata->six,
                                'fours' => $pdata->fours,
                                'bball' => $pdata->bball,
                                'strike_rate' => number_format($pdata->strike_rate, 2, '.', ''),
                                'batting_status' => ($pdata->out_str != 'not out') ? $pdata->out_str : (($pdata->bball != 0) ? 'Not Out' : ''),
                                'playing_status' => ($pdata->bball != 0) ? 1 : 0,
                            ];
                            if ($pdata->bowling == 1) {
                                $teamlinup[$i]['bowling_players'][] = [
                                    'id' => $pdata->player_id,
                                    'player_name' => $pdata->player_name,
                                    'innings' => $pdata->innings,
                                    'wicket' => $pdata->wicket,
                                    'overs' => $pdata->overs,
                                    'runs' => $pdata->grun,
                                    'maiden_over' => $pdata->maiden_over,
                                    'balls' => $pdata->balls,
                                    'economy_rate' => number_format($pdata->economy_rate, 2, '.', ''),
                                    'rpb' => $rpb,
                                    'balldots' => $pdata->balldots,
                                    'extra' => $pdata->extra,
                                ];
                            }
                            if (!array_key_exists('bowling_players', $teamlinup[$i])) {
                                $teamlinup[$i]['bowling_players'] = [];
                            }
                            $j++;
                        }
                    }

                    $batsmans = (isset($giveresresult['data']['card']['innings'][$teams[$i] . "_" . $inn])) ? $giveresresult['data']['card']['innings'][$teams[$i] . '_' . $inn]['batting_order'] : [];
                    $teamlinup[$i]['batsmans'] = array();
                    if (array_key_exists('batting_players', $teamlinup[$i])) {
                        if (!empty($batsmans)) {
                            foreach ($batsmans as $key => $value) {
                                if (array_key_exists($value, $teamlinup[$i]['batting_players'])) {
                                    $teamlinup[$i]['batsmans'][$value] = $teamlinup[$i]['batting_players'][$value];
                                    unset($teamlinup[$i]['batting_players'][$value]);
                                }
                            }
                        }
                    } else {
                        $teamlinup[$i]['batting_players'] = [];
                        if (!array_key_exists('bowling_players', $teamlinup[$i])) {
                            $teamlinup[$i]['bowling_players'] = [];
                        }
                    }

                    $teamlinup[$i]['batting_players'] = array_values(array_merge($teamlinup[$i]['batsmans'], $teamlinup[$i]['batting_players']));
                    unset($teamlinup[$i]['batsmans']);
                    $i++;
                }
                $tmj = 0;
                while ($tmj <= 3) {
                    unset($teamlinup[$tmj]['playing_XI']);
                    switch ($tmj) {
                        case '0':
                            $json[$tmj] = $teamlinup[$tmj];
                            $json[$tmj]['bowling_players'] = $teamlinup[$tmj + 1]['bowling_players'];
                            break;
                        case '1':
                            $json[$tmj] = $teamlinup[$tmj];
                            $json[$tmj]['bowling_players'] = $teamlinup[$tmj - 1]['bowling_players'];
                            break;
                        case '2':
                            $json[$tmj] = $teamlinup[$tmj];
                            $json[$tmj]['bowling_players'] = $teamlinup[$tmj + 1]['bowling_players'];
                            break;
                        case '3':
                            $json[$tmj] = $teamlinup[$tmj];
                            $json[$tmj]['bowling_players'] = $teamlinup[$tmj - 1]['bowling_players'];
                            break;
                        default:
                            break;
                    }
                    $tmj++;
                }
                return response()->json($json);
            } else {
                return response()->json([]);
            }

        }
    }

    public function series_leaderboard() {

		// increase maximum execution time
		ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
		ini_set('max_execution_time', '0'); // for infinite time of execution 
		
		$currentdate = date('Y-m-d');
		
		$seriesfind = DB::connection('mysql')->table('series')
						->select('series.*')
						->where('series.status','opened')
						->where('series.end_date','>=',$currentdate)
						->orderBy('series.end_date','DESC')
						->where('series.has_leaderboard', 'yes')
						->join('listmatches', 'listmatches.series', 'series.id')
						->groupBy('series.id')
						->where('listmatches.launch_status', 'launched')
						->get();
			
		$Json=array();
		$i=0;
		if(count($seriesfind)>0){
			foreach($seriesfind as $series){
				$this->leaderboard($series->id);
			}
		}

		return 1;
	}

	public function series_leaderboard_match_wise($matchkey) {
	
		// increase maximum execution time
		ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
		ini_set('max_execution_time', '0'); // for infinite time of execution 
	
		$input = request()->all();
        
		$geturl = Helpers::geturl();
        
		$getdata = DB::connection('mysql')->table('listmatches')
					->where('listmatches.matchkey',$matchkey)
					->where('launch_status','=','launched')
					->where('listmatches.final_status','!=','IsAbandoned')->where('listmatches.final_status','!=','IsCanceled')
					->where('listmatches.status','!=','notstarted')
					->join('jointeam','jointeam.matchkey','=','listmatches.matchkey')
					->join('joinedleauges','joinedleauges.teamid','jointeam.id')
					->join('registerusers','registerusers.id','jointeam.userid')
					->select(
					'jointeam.userid','listmatches.series',DB::raw('sum(jointeam.points) as totalpoints'),'registerusers.id as rid','registerusers.team as rteam','registerusers.image as rimage', 'joinedleauges.challengeid', 'listmatches.matchkey')
					->groupBy('jointeam.userid')
					->orderBy('totalpoints','desc')
					->get();

		if( !empty($getdata) ) {
			foreach ($getdata as $key => $data) {

				$getdata1=DB::connection('mysql')->table('listmatches')
							->where('listmatches.matchkey',$matchkey)
							->where('launch_status','=','launched')
							->where('listmatches.final_status','!=','IsAbandoned')->where('listmatches.final_status','!=','IsCanceled')
							->where('listmatches.status','!=','notstarted')
							->select('listmatches.series','listmatches.name','listmatches.matchkey', 'listmatches.*')
							->get();
				
				$total = 0;
				if(!empty($getdata1)){
					foreach($getdata1 as $val1){

						$total_points_match_wise = DB::connection('mysql')->table('jointeam')
													->where('jointeam.matchkey',$val1->matchkey)
													->where('jointeam.userid',$data->userid)
													->join('joinedleauges','joinedleauges.teamid','jointeam.id')
													->join('matchchallenges','matchchallenges.id','joinedleauges.challengeid')
													->join('contest_category','contest_category.id','matchchallenges.contest_cat')
													->select('jointeam.userid','jointeam.points','jointeam.teamnumber', 'jointeam.id as teamid')
													->orderBy('jointeam.points','desc')
													->where('matchchallenges.status','!=','canceled')
													->first();

						if(!empty($total_points_match_wise)){ 
							$total += $total_points_match_wise->points;
							
							// points must not update after winner declared
							if($val1->final_status == 'winnerdeclared') {
								
								$listmatches_data = array();

								$listmatches_data['leaderboard_status'] = 2;

								DB::connection('mysql2')
								->table('listmatches')
								->where('matchkey', $val1->matchkey)
								->update($listmatches_data);
							}

							$userpoints['userid']= $data->userid;
							$userpoints['matchkey']= $val1->matchkey;
							$userpoints['series_id']= $val1->series;
							
							$userpoints['points']= $total_points_match_wise->points;
							$userpoints['teamid']= $total_points_match_wise->teamid;
							$userpoints['teamnumber']= $total_points_match_wise->teamnumber;
							
							// points must not update after winner declared
						
							$checkUserExists= DB::connection('mysql')->table('series_leaderboard')
												->where('matchkey',$val1->matchkey)
												->where('userid',$data->userid)
												->exists();
							
							if($checkUserExists==0){
								DB::connection('mysql2')
								->table('series_leaderboard')->insert($userpoints);
							} else {
								DB::connection('mysql2')
								->table('series_leaderboard')
								->where('matchkey',$val1->matchkey)
								->where('userid',$data->userid)
								->update($userpoints);
							}
							
						}

					}
				}

				if($total == 0) {
					unset($getdata[$key]);
				}

				$data->totalpoints = $total;
			}
		}

		return back()
				->with('success', 'Series Leaderboard points updated');
    }	
	
	// top highest team points only 
	public function leaderboard($series)
    {
        $input = request()->all();
        // $user = Helpers::isAuthorize($request);
        $geturl = Helpers::geturl();
        // $series=$input['series_id'];
        $getdata=DB::connection('mysql')->table('listmatches')
        		->where('series',$series)
        		->where('launch_status','=','launched')
				->where('listmatches.final_status','!=','IsAbandoned')->where('listmatches.final_status','!=','IsCanceled')
        		->where('listmatches.status','!=','notstarted')
        		// ->where('final_status','=','winnerdeclared')
        		->join('jointeam','jointeam.matchkey','=','listmatches.matchkey')
        		->join('joinedleauges','joinedleauges.teamid','jointeam.id')
        		// ->join('finalresults','finalresults.joinedid','joinedleauges.id')
        		->join('registerusers','registerusers.id','jointeam.userid')
        		->select(
				// 'listmatches.final_status',
				'jointeam.userid','listmatches.series',DB::raw('sum(jointeam.points) as totalpoints'),'registerusers.id as rid','registerusers.team as rteam','registerusers.image as rimage', 'joinedleauges.challengeid', 'listmatches.matchkey')
        		->groupBy('jointeam.userid')
        		->orderBy('totalpoints','desc')
        		// ->limit(100)
				->get();

		if( !empty($getdata) ) {
			foreach ($getdata as $key => $data) {

				$getdata1=DB::connection('mysql')->table('listmatches')
							->where('series',$series)
							->where('launch_status','=','launched')
							->where('listmatches.final_status','!=','IsAbandoned')->where('listmatches.final_status','!=','IsCanceled')
							->where('listmatches.status','!=','notstarted')
							// ->where('final_status','=','winnerdeclared')
							->select('listmatches.series','listmatches.name','listmatches.matchkey', 'listmatches.*')
							->get();

				$total = 0;
				if(!empty($getdata1)){
					foreach($getdata1 as $val1){

						$total_points_match_wise = DB::connection('mysql')->table('jointeam')
													->where('jointeam.matchkey',$val1->matchkey)
													->where('jointeam.userid',$data->userid)
													->join('joinedleauges','joinedleauges.teamid','jointeam.id')
													// ->join('finalresults','finalresults.joinedid','joinedleauges.id')
													->join('matchchallenges','matchchallenges.id','joinedleauges.challengeid')
													->join('contest_category','contest_category.id','matchchallenges.contest_cat')
													->select('jointeam.userid','jointeam.points','jointeam.teamnumber', 'jointeam.id as teamid')
													->orderBy('jointeam.points','desc')
													->where('contest_category.has_leaderboard_points','yes')
													// ->groupBy('jointeam.matchkey')
													->first();

						if(!empty($total_points_match_wise)){ 
							$total += $total_points_match_wise->points;
							
							// points must not update after winner declared
							if($val1->final_status == 'winnerdeclared') {
								
								$listmatches_data = array();

								$listmatches_data['leaderboard_status'] = 2;

								DB::connection('mysql2')
								->table('listmatches')
								->where('matchkey', $val1->matchkey)
								->update($listmatches_data);
							}

							$userpoints['userid']= $data->userid;
							$userpoints['matchkey']= $val1->matchkey;
							$userpoints['series_id']= $series;
							
							$userpoints['points']= $total_points_match_wise->points;
							$userpoints['teamid']= $total_points_match_wise->teamid;
							$userpoints['teamnumber']= $total_points_match_wise->teamnumber;
							
							// points must not update after winner declared
							if($val1->final_status == 'winnerdeclared') {
							
							} else {
								$checkUserExists= DB::connection('mysql')->table('series_leaderboard')
													->where('matchkey',$val1->matchkey)
													->where('userid',$data->userid)
													->exists();
								
								if($checkUserExists==0){
									DB::connection('mysql2')
									->table('series_leaderboard')->insert($userpoints);
								} else {
									DB::connection('mysql2')
									->table('series_leaderboard')
									->where('matchkey',$val1->matchkey)
									->where('userid',$data->userid)
									->update($userpoints);
								}
							}

						}

					}
				}

				if($total == 0) {
					unset($getdata[$key]);
				}

				$data->totalpoints = $total;
			}
		}

        $getdata = ($getdata->sortByDesc('totalpoints'));

		// $series_leaderboard = array();
		// $series_leaderboard['series_id'] = $series;
		// $series_leaderboard['data'] = $getdata;

		// $is_already_exists = DB::connection('mysql')->table('series_leaderboard')
		// 						->where('series_id', $series)
		// 						->exists();
		
		// if($is_already_exists) {
		// 	DB::connection('mysql2')
		// 		->table('series_leaderboard')
		// 		->where('series_id', $series)
		// 		->update($series_leaderboard);
		// } else {
		// 	DB::connection('mysql2')
		// 	->table('series_leaderboard')
		// 	->insert($series_leaderboard);
		// }

		// return 1;

		return $getdata;
    }

}
