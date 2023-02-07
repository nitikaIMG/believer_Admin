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
use App\Helpers\Helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\CricketapiController;
class PlayingController extends Controller {
        
    public function updateplaying11(Request $request){
        $currentdate = date('Y-m-d');
        $listmatch = DB::table('listmatches')->where('start_date','>',$currentdate)->where('launch_status','launched')->where('status','notstarted')->select('name','matchkey','start_date')->get();
        if(!empty($_GET['matchkey'])){
            $teamdata = DB::table('listmatches')
                          ->join('teams as t1','t1.id','=','listmatches.team1')
                          ->join('teams as t2','t2.id','=','listmatches.team2')
                          ->where('listmatches.matchkey',$_GET['matchkey'])
                          ->where('listmatches.start_date','>',$currentdate)
                          ->where('listmatches.launch_status','launched')
                          ->select('listmatches.team1','listmatches.team2', 't1.team as t1name', 't2.team as t2name')
                          ->first();
                          
            return view('matches.updateplaying11',compact('listmatch','teamdata'));
        }
        return view('matches.updateplaying11',compact('listmatch'));
    }
    
    public function match_player1($mkey,$team1){
        $m = $mkey;
        $t = $team1;
        $findmatchdetails = DB::table('listmatches')->where('matchkey',$m)->first();

        $findplayer1details = DB::table('matchplayers')->where('matchplayers.matchkey',$findmatchdetails->matchkey)->where('players.team',$findmatchdetails->team1)->join('players','players.id','=','matchplayers.playerid')->orderBy('matchplayers.vplaying','desc')->select('matchplayers.*')->get();
        $listmatchdata = DB::table('listmatches')->where( function ($rr) use ($team1) { $rr->where('team1',$team1)->orWhere('team2',$team1); })->orderBy('id','desc')->select('id','matchkey')->first();
        $findplayer1details1 = DB::table('matchplayers')->where('playingstatus',1)->where('matchplayers.matchkey',$listmatchdata->matchkey)->where('players.team',$findmatchdetails->team1)->join('players','players.id','=','matchplayers.playerid')->select('matchplayers.playingstatus','matchplayers.playerid')->get();
        
        $chh = DB::table('matchplayers')->where('vplaying',1)->where('matchkey',$m)->first();
        if(empty($chh)){
            if(!empty($findplayer1details1)){
                foreach($findplayer1details1 as $vb){
                    $vplayupdate['vplaying'] = 1;
                    DB::connection('mysql2')->table('matchplayers')->where('matchkey',$m)->where('playerid',$vb->playerid)->update($vplayupdate);
                }
            }
        }
        $i=1;$JsonFinal=array();
    if(!empty($findplayer1details))
    {
        foreach ($findplayer1details as $value)
        {
            $delete =  action('MatchController@deleteplayer',[base64_encode(serialize($value->playerid)),base64_encode(serialize($value->matchkey))]);
            $edit = action('MatchController@playerroles',$value->id);
            $role =ucwords($value->role);

            $batman =0;
            $a=0;
            $bowler=0;
            $allrounder=0;
            $keeper=0;
            $c="";
            $b="";

            if($value->role=='batsman'){
            $batman= 'selected';
            }

            if($value->credit==""){
                $a = 0;
            }else{
                $a = $value->credit;
             }

             if($value->role=='bowler'){
                $bowler= 'selected'; 
             }

             if($value->role=='allrounder'){
              $allrounder= 'selected'; 
            }

            if($value->role=='keeper'){
            $keeper= 'selected'; 
            }
            $assplay= ($value->vplaying == 1) ? 'checked' : 'unchecked';
               $b = '<div class="custom-control custom-checkbox mb-3"><input type="checkbox" class="custom-control-input" name="playing[]" id="customControlValidation1'.$value->playerid.'" '.$assplay.' value="'.$value->playerid.'"><label class="custom-control-label" for="customControlValidation1'.$value->playerid.'"></label></div>';
            $data=array(
                 $i,
                 '<span class="font-weight-bold text-dark">'.$value->name.'</span>'.$c,
                //  '<a data-toggle="modal" data-target="#player1modal'.$i.'" style="text-decoration:underline;cursor:pointer">'.$role.'</a>'.$c,
                //  '<a data-toggle="modal" data-target="#player1modal'.$i.'"  style="text-decoration:underline;cursor:pointer">'.$a.'</a>'.$c,
                 '<span class="font-weight-bold text-orange">'.$role.'</span>'.$c,
                 '<span class="font-weight-bold text-primary">'.$a.'</span>'.$c,
                 $b,
            ); 

            $i++;
            $JsonFinal[]=$data;
        }
    }
    $jsonFinal1 = json_encode(array('data' => $JsonFinal));
    echo $jsonFinal1;
    die;
    }

    public function match_player2($mkey,$team2){
        $m = $mkey;
        $t = $team2;
    
        $findmatchdetails = DB::table('listmatches')->where('matchkey',$m)->first();

        $findplayer2details = DB::table('matchplayers')->where('matchplayers.matchkey',$findmatchdetails->matchkey)->where('players.team',$findmatchdetails->team2)->join('players','players.id','=','matchplayers.playerid')->orderBy('matchplayers.vplaying','desc')->select('matchplayers.*')->get();
        $listmatchdata = DB::table('listmatches')->where( function ($rr) use ($team2) { $rr->where('team1',$team2)->orWhere('team2',$team2); })->orderBy('id','desc')->select('id','matchkey')->first();
        $findplayer1details1 = DB::table('matchplayers')->where('playingstatus',1)->where('matchplayers.matchkey',$listmatchdata->matchkey)->where('players.team',$findmatchdetails->team2)->join('players','players.id','=','matchplayers.playerid')->select('matchplayers.playingstatus','matchplayers.playerid')->get();
        // 
        $chh = DB::table('matchplayers')->where('vplaying',1)->where('matchkey',$m)->count();
        
        if($chh != 22){
            if(!empty($findplayer1details1)){
                foreach($findplayer1details1 as $vb){
                    $vplayupdate['vplaying'] = 1;
                    DB::connection('mysql2')->table('matchplayers')->where('matchkey',$m)->where('playerid',$vb->playerid)->update($vplayupdate);
                }
            }
        }
        $i=1;$JsonFinal=array();
        if(!empty($findplayer2details))
        {
            foreach ($findplayer2details as $value)
            {
                $delete =  action('MatchController@deleteplayer',[base64_encode(serialize($value->playerid)),base64_encode(serialize($value->matchkey))]);
                $edit = action('MatchController@playerroles',$value->id);
                $role =ucwords($value->role);

                $batman =0;
                $a=0;
                $bowler=0;
                $allrounder=0;
                $keeper=0;
                $c="";
                $b="";

                if($value->role=='batsman'){
                $batman= 'selected';
                }

                if($value->credit==""){
                    $a = 0;
                }else{
                    $a = $value->credit;
                 }

                 if($value->role=='bowler'){
                    $bowler= 'selected'; 
                 }

                 if($value->role=='allrounder'){
                  $allrounder= 'selected'; 
                }

                if($value->role=='keeper'){
                $keeper= 'selected'; 
                }
                $assplay= ($value->vplaying == 1) ? 'checked' : 'unchecked';
                   $b = '<div class="custom-control custom-checkbox mb-3"><input type="checkbox" class="custom-control-input" name="playing[]" id="customControlValidation1'.$value->playerid.'" '.$assplay.' value="'.$value->playerid.'"><label class="custom-control-label" for="customControlValidation1'.$value->playerid.'"></label></div>';
                $data=array(
                     $i,
                     '<span class="font-weight-bold text-dark">'.$value->name.'</span>'.$c,
                    //  '<a data-toggle="modal" data-target="#player1modal'.$i.'" style="text-decoration:underline;cursor:pointer">'.$role.'</a>'.$c,
                    //  '<a data-toggle="modal" data-target="#player1modal'.$i.'"  style="text-decoration:underline;cursor:pointer">'.$a.'</a>'.$c,
                     '<span class="font-weight-bold text-orange">'.$role.'</span>'.$c,
                     '<span class="font-weight-bold text-primary">'.$a.'</span>'.$c,
                     $b,
                ); 

                $i++;
                $JsonFinal[]=$data;
            } 
        }
        $jsonFinal1 = json_encode(array('data' => $JsonFinal));
        echo $jsonFinal1;
        die;
    }
    
    public function upp1($mkey,$team1,Request $request){
        $input = $request->all();
        
        $findmatchdetails = DB::table('listmatches')->where('matchkey',$mkey)->first();
        if(!empty($input['playing']) and count($input['playing']) == 11){
            $uu1['vplaying'] = 0;
            $findplayer1details = DB::connection('mysql2')->table('matchplayers')->where('matchplayers.matchkey',$findmatchdetails->matchkey)->where('players.team',$findmatchdetails->team1)->join('players','players.id','=','matchplayers.playerid')->select('matchplayers.*')->update($uu1);
            foreach($input['playing'] as $val){
                $uu['vplaying'] = 1;
                DB::connection('mysql2')->table('matchplayers')->where('playerid',$val)->where('matchkey',$mkey)->update($uu);
            }
            return redirect()->back()->with('success','Successfully Updated');
        }else{
           return redirect()->back()->with('danger','Please select 11 players');
        }
    }
    
    public function upp2($mkey,$team2,Request $request){
        $input = $request->all();
        $findmatchdetails = DB::table('listmatches')->where('matchkey',$mkey)->first();

        if(!empty($input['playing']) and count($input['playing']) == 11){
            $uu1['vplaying'] = 0;
            $findplayer1details = DB::connection('mysql2')->table('matchplayers')->where('matchplayers.matchkey',$findmatchdetails->matchkey)->where('players.team',$findmatchdetails->team2)->join('players','players.id','=','matchplayers.playerid')->select('matchplayers.*')->update($uu1); 
            foreach($input['playing'] as $val){
                $uu['vplaying'] = 1;
                DB::connection('mysql2')->table('matchplayers')->where('playerid',$val)->where('matchkey',$mkey)->update($uu);
            }
            return redirect()->back()->with('success','Successfully Updated');
        }else{
           return redirect()->back()->with('danger','Please select 11 players');
        }
    }
       public function launchplaying($matchkey){
        $allplayers = DB::table('matchplayers')->where('matchkey',$matchkey)->select('vplaying','playerid','id')->get();

        $selected_count = DB::table('matchplayers')->where('matchkey',$matchkey)->where('vplaying', 1)->count();

        if($selected_count < 22) {
            return redirect()->back()->with('error', 'Please select playing XI of both teams');
        }

        if(!empty($allplayers)){
            foreach($allplayers as $play){
                $playerkeyy = DB::table('players')->where('id',$play->playerid)->select('players_key')->first();
                $playerkeyys = $playerkeyy->players_key;
                $dddjdh['playingstatus'] = $play->vplaying;
                DB::connection('mysql2')->table('matchplayers')->where('id',$play->id)->update($dddjdh);
                $datahitss = array();
                $gh = DB::table('result_matches')->where('player_key',$playerkeyys)->where('match_key',$matchkey)->select('id')->first();
                if(!empty($gh)){
                    if($play->vplaying == '1'){
                $datahitss['starting11']='1';
				DB::connection('mysql2')->table('result_matches')->where('player_key',$playerkeyys)->where('match_key',$matchkey)->update($datahitss);
				
		    	$dataahitss['startingpoints']='4';
				DB::connection('mysql2')->table('result_points')->where('resultmatch_id',$gh->id)->where('matchkey',$matchkey)->update($dataahitss);
                    }else{
                       $datahitss['starting11']='0';
				DB::connection('mysql2')->table('result_matches')->where('player_key',$playerkeyys)->where('match_key',$matchkey)->update($datahitss);
				
		    	$dataahitss['startingpoints']='0';
				DB::connection('mysql2')->table('result_points')->where('resultmatch_id',$gh->id)->where('matchkey',$matchkey)->update($dataahitss); 
                    }
                }else{
                    if($play->vplaying == '1'){
                  $datahitss['player_id']=$play->playerid;  
                  $datahitss['player_key']=$playerkeyys;  
                  $datahitss['match_key']=$matchkey;  
                  $datahitss['starting11']='1';  
                  $datahitss['innings']='1'; 
                  $hii = DB::connection('mysql2')->table('result_matches')->insertGetId($datahitss);
                  $dataahitss['startingpoints']='4';
                  $dataahitss['resultmatch_id']=$hii;
                  $dataahitss['matchkey']=$matchkey;
                  $dataahitss['playerid']=$play->playerid;
				  DB::connection('mysql2')->table('result_points')->insert($dataahitss);
                    }else{
                      $datahitss['player_id']=$play->playerid;  
                  $datahitss['player_key']=$playerkeyys;  
                  $datahitss['match_key']=$matchkey;  
                  $datahitss['starting11']='0';  
                  $datahitss['innings']='0'; 
                  $hii = DB::connection('mysql2')->table('result_matches')->insertGetId($datahitss);
                  $dataahitss['startingpoints']='0';
                  $dataahitss['resultmatch_id']=$hii;
                  $dataahitss['matchkey']=$matchkey;
                  $dataahitss['playerid']=$play->playerid;
				  DB::connection('mysql2')->table('result_points')->insert($dataahitss);  
                    }
                }
            }
            $teamssdta = DB::table('listmatches')->where('matchkey',$matchkey)->join('teams as t1','t1.id','=','listmatches.team1')->join('teams as t2','t2.id','=','listmatches.team2')->select('t1.short_name as t1name','t2.short_name as t2name','listmatches.playing11_status as playing11_status')->first();
            if($teamssdta->playing11_status != '1'){
                $playingstat['playing11_status'] = '1';
                DB::connection('mysql2')->table('listmatches')->where('matchkey',$matchkey)->update($playingstat);
            }
            return redirect()->back()->with('success','Successfully launched');
        }
    }
        
}