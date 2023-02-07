<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Session;
use App\Helpers\Helpers;
class ContestFullDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(){
        $this->middleware('auth');
    }

    public function fulldetail1(Request $request){

        $f_type = request()->get('fantasy_type');
        $f_type = !empty($f_type) ? $f_type : 'Cricket';

       date_default_timezone_set('Asia/Kolkata');
        $allchallenges = array();
        if(request()->has('matchid')){
          $matchid=request('matchid');
          if($matchid!=""){
             $allchallenges = DB::table('listmatches')
                                ->join('series', 'series.id', 'listmatches.series')
                                ->join('teams as t1','t1.id','=','listmatches.team1')
                                ->join('teams as t2','t2.id','=','listmatches.team2')
                                ->where('listmatches.series',$matchid)
                                ->where('listmatches.fantasy_type', $f_type)
                                ->orderBy('listmatches.start_date', 'desc')
                                ->select('series.name as series_name', 'listmatches.*', 't1.team as teamdata1_team', 't2.team as teamdata2_team')
                                ->paginate(10);
             
          }

        }
        $currentdate = date('Y-m-d h:i:s');
        $findalllistmatches = DB::table('series')->select('*')->where('fantasy_type',$f_type)->where('status', 'opened')->orderBY('created_at','ASC')->get();
        
        return view('contest_detail.contest_full_detail',compact('findalllistmatches','allchallenges'));
      }


    public function allcontests($matchkey){
        $getdata = DB::table('matchchallenges')->where('matchkey',$matchkey)->orderBy('joinedusers','desc')->get();
        $finddata = DB::table('matchchallenges')->where('matchkey',$matchkey)->first();
        $matchfinalstatus = DB::table('listmatches')->where('matchkey',$matchkey)->value('final_status');

          $output1 = "";
          $output1 .='"Sno.",';
          $output1 .='"Win Amount",';
          $output1 .='"League Size",';
          $output1 .='"Entry Fee",';
          $output1 .='"Contest Type",';
          $output1 .='"League Type",';
          $output1 .='"Multi Entry",';
          $output1 .='"Is Running",';
          $output1 .='"Joined Users",';
          $output1 .="\n";

          if( !empty($getdata) ) {

            $count=1;

            foreach($getdata as $get){
              $output1 .='"'.$count.'",';
              $output1 .='"'.$get->win_amount.'",';
              $output1 .='"'.$get->maximum_user.'",';
              $output1 .='"'.$get->entryfee.'",';
              $output1 .='"'.$get->contest_type.'",';
              if($get->confirmed_challenge==1){
                $output1 .='"Confirmed League",';
              }else{
                $output1 .='"Not Confirmed",';
              }
              if($get->multi_entry==1){
                $output1 .='"Yes",';
              }else{
                $output1 .='"No",';
              }
              if($get->is_running==1){
                $output1 .='"Yes",';
              }else{
                $output1 .='"No",';
              }
              $output1 .='"'.$get->joinedusers.'",';
              $output1 .="\n";
              $count++;
            }
          }

          if( !empty($_GET['download_btn_clicked']) ) {

              $filename =  "Details-allcontest.csv";
              header('Content-type: application/csv');
              header('Content-Disposition: attachment; filename='.$filename);
              echo $output1;
              exit;
          }

        return  view('contest_detail.allcontest',compact('getdata','finddata','matchfinalstatus'));
      }

    public function allusers($challengeid,$matchkey){
      //$data = DB::connection('mysql2')->table('joinedleauges')->where('challengeid',$challengeid)->get();
      return view('contest_detail.all_users',compact('challengeid','matchkey'));
    }
    public function allwinners($challengeid,$matchkey){
      //$data = DB::connection('mysql2')->table('joinedleauges')->where('challengeid',$challengeid)->get();
      return view('contest_detail.all_winners',compact('challengeid','matchkey'));
    }

    public function viewjoinusers_datatable(Request $request){
        $getdata=$request->all();
        $challengeid=$getdata['challngeid'];
        $matchkeyid=$getdata['matchkey'];
        $getchafinal = DB::table('finalresults')->where('challengeid',$challengeid)->first();
      
        if(!empty($getchafinal)){
            $columns = array(
                0 => 'finalresults.rank',
                1 => 'userid',
                2 => 'registerusers.email',
                3 => 'registerusers.mobile',
                4 => 'finalresults.matchkey',
                5 => 'refercode',
                6 => 'transaction_id',
                7 => 'finalresults.id',
                8 => 'finalresults.id',
                9 => 'finalresults.created_at',
                10 => 'finalresults.points',
                11 => 'finalresults.points',
            );
        }else{
            $columns = array(
                0 => 'joinedleauges.id',
                1 => 'userid',
                2 => 'registerusers.email',
                3 => 'registerusers.mobile',
                4 => 'joinedleauges.matchkey',
                5 => 'refercode',
                6 => 'transaction_id',
                7 => 'joinedleauges.id',
                8 => 'joinedleauges.id',
                9 => 'joinedleauges.created_at',
                10 => 'joinedleauges.points',
                11 => 'joinedleauges.points',
            );
        }
        
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $matchtype = DB::table('matchchallenges')->where('id',$challengeid)->first();

        if($matchtype->fantasy_type=='Duo'){

          $query = DB::table('joinedleauges')->join('registerusers','registerusers.id','=','joinedleauges.userid');
        $query1 = DB::table('joinedleauges')->join('registerusers','registerusers.id','=','joinedleauges.userid');
        if(!empty($getchafinal)){
            $titles = $query->where('joinedleauges.challengeid',$challengeid)
            ->leftjoin('finalresults','finalresults.joinedid','=','joinedleauges.id')
            ->select('registerusers.team','registerusers.email','finalresults.amount','joinedleauges.duopoints as points','finalresults.transaction_id','finalresults.rank','joinedleauges.teamid','joinedleauges.userid','registerusers.id as Uid','joinedleauges.player_id')->offset($start)->limit($limit)->orderBy($order,$dir)->get();

        $totalTitles = $query1->where('joinedleauges.challengeid',$challengeid)->leftjoin('finalresults','finalresults.joinedid','=','joinedleauges.id')->select('registerusers.username','registerusers.email','registerusers.team','finalresults.amount','finalresults.points','finalresults.transaction_id','finalresults.rank','joinedleauges.teamid','joinedleauges.userid','registerusers.id as Uid')->count();

        }else{
            $totalTitles = $query1->where('joinedleauges.challengeid',$challengeid)->select('registerusers.username','registerusers.email','registerusers.team','joinedleauges.teamid','joinedleauges.userid','joinedleauges.duopoints as points','registerusers.id as Uid')->count();
            $titles = $query->where('joinedleauges.challengeid',$challengeid)->select('registerusers.username','registerusers.email','registerusers.team','joinedleauges.teamid','joinedleauges.userid','registerusers.id as Uid','joinedleauges.player_id')->offset($start)->limit($limit)->orderBy($order,$dir)->get();
        }

        }else{
          $query = DB::table('joinedleauges')->join('registerusers','registerusers.id','=','joinedleauges.userid')->join('jointeam','jointeam.id','=','joinedleauges.teamid');
        $query1 = DB::table('joinedleauges')->join('registerusers','registerusers.id','=','joinedleauges.userid');
        if(!empty($getchafinal)){
            $titles = $query->where('joinedleauges.challengeid',$challengeid)
            ->leftjoin('finalresults','finalresults.joinedid','=','joinedleauges.id')
            ->select('registerusers.team','registerusers.email','finalresults.amount','jointeam.points','finalresults.transaction_id','finalresults.rank','joinedleauges.teamid','joinedleauges.userid','registerusers.id as Uid','joinedleauges.player_id')->offset($start)->limit($limit)->orderBy($order,$dir)->get();

        $totalTitles = $query1->where('joinedleauges.challengeid',$challengeid)->leftjoin('finalresults','finalresults.joinedid','=','joinedleauges.id')->select('registerusers.username','registerusers.email','registerusers.team','finalresults.amount','finalresults.points','finalresults.transaction_id','finalresults.rank','joinedleauges.teamid','joinedleauges.userid','registerusers.id as Uid')->count();

        }else{
            $totalTitles = $query1->where('joinedleauges.challengeid',$challengeid)->select('registerusers.username','registerusers.email','registerusers.team','joinedleauges.teamid','joinedleauges.userid','jointeam.points','registerusers.id as Uid')->count();
            $titles = $query->where('joinedleauges.challengeid',$challengeid)->select('registerusers.username','registerusers.email','registerusers.team','joinedleauges.teamid','joinedleauges.userid','registerusers.id as Uid','joinedleauges.player_id')->offset($start)->limit($limit)->orderBy($order,$dir)->get();
        }
        }
        
        
        $totalFiltered = $totalTitles;
        if (!empty($titles)) {
            $data = array();
            if($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
              $count = $totalFiltered - $start;
            } else {
              $count = $start + 1;
            }
            foreach ($titles as $title) {
                $bb=action('RegisteruserController@getuserdetails',$title->userid);
               $aa ='<a href="'.$bb.'" style="text-decoration:underline;">'.$title->userid.'';
                $confirm= "return confirm('Are you sure you want to delete this data?')";
                $edit =action('ContestFullDetailController@user_team',[$title->teamid,$matchkeyid,$title->Uid]);
                $delete =action('RegisteruserController@viewtransactions',[$title->userid,'cid='.$challengeid]);
                $nestedData['s_no'] = $count;
                

                if($matchtype->fantasy_type=='Duo'){
                  $nestedData['action'] = '<a target="blank" class="btn btn-sm btn-info w-35px h-35px" data-toggle="tooltip" title="View Transaction" href="'.$delete.'"><i class="fas fa-eye"></i></a>';
                  $matchplayername = DB::table('matchplayers')->where('matchkey',$matchkeyid)->where('playerid',$title->player_id)->first();
                  $nestedData['selectedplayer']= '<div class="text-white bg-primary rounded-pill ml-1 col px-2 py-1 my-1" style="text-align: center; font-weight: 600;">'. $matchplayername->name .' </div>';
                }else{
                  $nestedData['action'] = '<a class="btn btn-sm btn-success w-35px h-35px" data-toggle="tooltip" title="View Team" href="'.$edit.'" style=""><i class="fas fa-users"></i></a><a target="blank" class="btn btn-sm btn-info w-35px h-35px" data-toggle="tooltip" title="View Transaction" href="'.$delete.'"><i class="fas fa-eye"></i></a>';
                  $nestedData['selectedplayer']= '';
                }
                $nestedData['userid'] = $aa;
                $nestedData['teamname'] = $title->team;
                $nestedData['email'] = $title->email;
                if(!empty($getchafinal)){
                    $nestedData['rank'] = $title->rank;
                    $nestedData['transaction_id'] = $title->transaction_id;
                    $nestedData['points'] = $title->points;
                    $nestedData['amount'] = $title->amount;
                }else{
                    $nestedData['rank'] = 0;
                    $nestedData['transaction_id'] = 0;
                    $nestedData['points'] = 0;
                    $nestedData['amount'] = 0;
                }
                $data[] = $nestedData;
                if($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
                  $count -= 1;
                } else {
                  $count += 1;
                }
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalTitles),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );
        echo json_encode($json_data);
    }
    public function viewjoinwinners_datatable(Request $request){
        $getdata=$request->all();
        $challengeid=$getdata['challngeid'];
        $matchkeyid=$getdata['matchkey'];
        $getchafinal = DB::table('finalresults')->where('challengeid',$challengeid)->first();
      
        if(!empty($getchafinal)){
          $columns = array(
              0 => 'finalresults.rank',
              1 => 'userid',
              2 => 'registerusers.email',
              3 => 'registerusers.mobile',
              4 => 'finalresults.matchkey',
              5 => 'refercode',
              6 => 'transaction_id',
              7 => 'finalresults.id',
              8 => 'finalresults.id',
              9 => 'finalresults.created_at',
              10 => 'finalresults.points',
              11 => 'finalresults.points',
          );
        
          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');
        
          $query = DB::table('joinedleauges')->join('registerusers','registerusers.id','=','joinedleauges.userid')->join('jointeam','jointeam.id','=','joinedleauges.teamid');
          $query1 = DB::table('joinedleauges')->join('registerusers','registerusers.id','=','joinedleauges.userid');
          // if(!empty($getchafinal)){
            $titles = $query->where('joinedleauges.challengeid',$challengeid)
              ->join('finalresults','finalresults.joinedid','=','joinedleauges.id')
              ->select('registerusers.team','registerusers.email','finalresults.amount','jointeam.points','finalresults.transaction_id','finalresults.rank','joinedleauges.teamid','joinedleauges.userid','registerusers.id as Uid')->offset($start)->limit($limit)->orderBy($order,$dir)->get();

            $totalTitles = $query1->where('joinedleauges.challengeid',$challengeid)->join('finalresults','finalresults.joinedid','=','joinedleauges.id')->select('registerusers.username','registerusers.email','registerusers.team','finalresults.amount','finalresults.points','finalresults.transaction_id','finalresults.rank','joinedleauges.teamid','joinedleauges.userid','registerusers.id as Uid')->count();

          // }
        
          $totalFiltered = $totalTitles;
          if (!empty($titles->toArray())) {
              $data = array();
              if($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
                $count = $totalFiltered - $start;
              } else {
                $count = $start + 1;
              }
              foreach ($titles as $title) {
                  $bb=action('RegisteruserController@getuserdetails',$title->userid);
                 $aa ='<a href="'.$bb.'" style="text-decoration:underline;">'.$title->userid.'';
                  $confirm= "return confirm('Are you sure you want to delete this data?')";
                  $edit =action('ContestFullDetailController@user_team',[$title->teamid,$matchkeyid,$title->Uid]);
                  $delete =action('RegisteruserController@viewtransactions',[$title->userid,'cid='.$challengeid]);
                  $nestedData['s_no'] = $count;
                  $nestedData['action'] = '<a class="btn btn-sm btn-success w-35px h-35px" data-toggle="tooltip" title="View Team" href="'.$edit.'" style=""><i class="fas fa-users"></i></a>
                                          <a target="blank" class="btn btn-sm btn-info w-35px h-35px" data-toggle="tooltip" title="View Transaction" href="'.$delete.'"><i class="fas fa-eye"></i></a>';
                  $nestedData['userid'] = $aa;
                  $nestedData['teamname'] = $title->team;
                  $nestedData['email'] = $title->email;
                  if(!empty($getchafinal)){
                      $nestedData['rank'] = $title->rank;
                      $nestedData['transaction_id'] = $title->transaction_id;
                      $nestedData['points'] = $title->points;
                      $nestedData['amount'] = $title->amount;
                  }
                  $data[] = $nestedData;
                  if($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
                    $count -= 1;
                  } else {
                    $count += 1;
                  }
              }
          }
          $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalTitles),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
          );
        }else{

          $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval(0),
            "recordsFiltered" => intval(0),
            "data" => [],
          );
        }
        
        echo json_encode($json_data);
    }

     public function user_team($teamid,$matchkeyid,$Uid){
        $data = DB::table('jointeam')->where('id',$teamid)->first();

        $getdata = DB::table('jointeam')->where('id',$teamid)->get();
        if(!empty($data))
        {
        $captain = DB::table('players')->where('id',$data->captain)->first();
        
        $vice_captain = DB::table('players')->where('id',$data->vicecaptain)->first();

        $player = explode(',',$data->players);
        

        }
        return view('contest_detail.users_team',compact('data','captain','vice_captain','getdata','player','matchkeyid','Uid'));
    }
    public function changeteam($teamid,$matchkeyid,$Uid){
      $teamarray = DB::table('jointeam')->where('id',$teamid)->first();
      $selectedplayers = explode(',',$teamarray->players);

      $data = DB::table('matchplayers')
        ->where('matchkey',$matchkeyid)
        ->whereIn('playerid', $selectedplayers)
        ->join('players','players.id','=','matchplayers.playerid')
        ->join('teams','teams.id','players.team')
        ->orderBy('matchplayers.vplaying','desc')
        ->select('teams.team', DB::raw('count(teams.team) as team_count'))
        ->groupBy('teams.team')
        ->get();
        
        // dd($data);

        return view('contest_detail.user_changeteam',compact('matchkeyid','Uid','teamid', 'data'));
    }
      
    public function changeteam_datatable(Request $request){
        $getdata=$request->all();
        $matchkeyid = $getdata['matchkey'];
        $Uid_id = $getdata['Uid'];
        $teamid = $getdata['teamid'];
        $teamarray = DB::table('jointeam')->where('id',$teamid)->first();
        $selectedplayers = explode(',',$teamarray->players);
        // dump($teamid);
        $columns = array(
                0 => 'id',
                1 => 'name',
                2 => 'role',
                3 => 'matchplayers.credit',
                4 => 'matchplayers.role',
                5 => 'matchplayers.points',
                6 => 'matchplayers.credit',
                7 => 'matchplayers.playingstatus',
            );
        $totalTitles = DB::table('matchplayers')->where('matchkey',$matchkeyid)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $titles = DB::table('matchplayers')->where('matchkey',$matchkeyid)->join('players','players.id','=','matchplayers.playerid')->join('teams','teams.id','players.team')->orderBy('matchplayers.vplaying','desc')->select('matchplayers.*','players.image','teams.team')
        ->orderBy('role', 'ASC')
        ->get();
        
        $totalFiltered = $totalTitles;
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {
                if($title->image==''){
                    $imagelogo = asset('/logo.png');
                    $img = '<img src="'.$imagelogo.'" style="width:70px;height:70px;">';
                }else{
                    $imagelogo = asset('/players/'.$title->image);
                    $img = '<img src="'.$imagelogo.'" style="width:70px;height:70px;">';
                }
                
            $batman =0;
            $a=0;
            $bowler=0;
            $allrounder=0;
            $keeper=0;
            $b="";

            if($title->role=='batsman'){
            $batman= 'selected';
            }

            if($title->credit==""){
              $a = 0;
          }else{
            $a = $title->credit;
           }

           if($title->role=='bowler'){
            $bowler= 'selected'; 
           }

           if($title->role=='allrounder'){
            $allrounder= 'selected'; 
          }

          if($title->role=='keeper'){
          $keeper= 'selected'; 
          }
                $assplay= (in_array($title->playerid, $selectedplayers)) ? 'checked' : 'unchecked';
                $b = '<input type="checkbox" name="playing[]" '.$assplay.' value="'.$title->playerid.'" data-credit="'.$title->credit.'" data-team="'.$title->team.'">';
                $nestedData['s_no'] = $count;
                $nestedData['image']= $img;
                $nestedData['name'] = $title->name.' '.(($title->playerid==$teamarray->vicecaptain)?'(VC)':(($title->playerid==$teamarray->captain)?'(C)':''));
                $nestedData['team'] = $title->team;
                $nestedData['role'] = $title->role;
                $nestedData['points'] = $title->points;
                $nestedData['credit'] = $title->credit;
                $nestedData['in_playing_11'] = $title->playingstatus == 1 ? 'Yes' : 'No';
                $nestedData['action'] = $b;
                $data[] = $nestedData;
                $count++;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalTitles),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );
        echo json_encode($json_data);
    }
    
    public function update_change_team($teamid,$matchkeyid,$Uid, Request $request){
      $data = $request->all();
      unset($data['_token']);
      if(!empty($data['playing']) && count($data['playing']) == 11){     
        $ds = json_encode($data);

        return view('contest_detail.user_selectvc',compact('ds','matchkeyid','Uid','teamid'));
      }else{
        if( !empty($data['playing']) and count($data['playing']) < 11){
            return redirect()->back()->with('danger','Select 11 Players '.count($data['playing']).' Selected');
        }else if( !empty($data['playing']) and count($data['playing']) > 11){
            return redirect()->back()->with('danger','Select only 11 Players '.count($data['playing']).' Selected');
        } else {
            return redirect()->action('ContestFullDetailController@changeteam', [$teamid, $matchkeyid, $Uid]);
        }
         
      }
    }
    public function update_changeteam_datatable(Request $request){
        $getdata=$request->all();
        $matchkeyid = $getdata['matchkey'];
        $Uid_id = $getdata['Uid'];
        $teamid = $getdata['teamid'];
        $teamarray = DB::table('jointeam')->where('id',$teamid)->first();
        $selectedplayers = explode(',',$teamarray->players);
        $players = json_decode($getdata['players'],true);
        // dd($getdata['players']);
        // dump($selectedplayers);
        $columns = array(
                0 => 'id',
                1 => 'name',
                2 => 'role',
                3 => 'matchplayers.credit',
                4 => 'matchplayers.role',
                5 => 'matchplayers.points',
                6 => 'matchplayers.credit',
                7 => 'matchplayers.playingstatus',
            );
        $totalTitles = DB::table('matchplayers')->where('matchkey',$matchkeyid)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $titles = DB::table('matchplayers')->where('matchkey',$matchkeyid)->join('players','players.id','=','matchplayers.playerid')->join('teams','teams.id','players.team')->orderBy('matchplayers.vplaying','desc')->select('matchplayers.*','players.image','teams.team')->orderBy($order, $dir)->get();
        
        $totalFiltered = $totalTitles;
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {
                // dump()
                if(!in_array($title->playerid,$players['playing'])){
                    continue;
                }
                if($title->image==''){
                    $imagelogo = asset('logo.png');
                    $img = '<img src="'.$imagelogo.'" style="width:70px;height:70px;">';
                }else{
                    $imagelogo = asset('players/'.$title->image);
                    $img = '<img src="'.$imagelogo.'" style="width:70px;height:70px;">';
                }
                
            $batman =0;
            $a=0;
            $bowler=0;
            $allrounder=0;
            $keeper=0;
            $b="";

            if($title->role=='batsman'){
            $batman= 'selected';
            }

            if($title->credit==""){
                $a = 0;
            }else{
                $a = $title->credit;
             }

             if($title->role=='bowler'){
                $bowler= 'selected'; 
             }

             if($title->role=='allrounder'){
              $allrounder= 'selected'; 
            }

            if($title->role=='keeper'){
            $keeper= 'selected'; 
            }   
                
                $js = "$(this).parent().siblings().children('input[name=playingvc]').attr({'disabled':true})";
                $js1= "$(this).parent().siblings().children('input[name=playingc]').attr({'disabled':true})";
                // $js = "alert($(this).prop('checked'));";
                $assplay= (in_array($title->playerid, $selectedplayers)) ? 'checked' : 'unchecked';
                $b = '<input type="hidden" name="playing[]" '.$assplay.' value="'.$title->playerid.'" >&nbsp;<input type="radio" name="playingvc" value="'.$title->playerid.'" onchange="getdata1(),'.$js1.'">';
                $nestedData['s_no'] = $count;
                $nestedData['image']= $img;
                $nestedData['name'] = $title->name.' '.(($title->playerid==$teamarray->vicecaptain)?'(VC)':(($title->playerid==$teamarray->captain)?'(C)':''));
                $nestedData['team'] = $title->team;
                $nestedData['role'] = $title->role;
                $nestedData['points'] = $title->points;
                $nestedData['credit'] = $title->credit;
                $nestedData['VC'] = $b;
                $nestedData['C'] = '<input type="radio" name="playingc" value="'.$title->playerid.'" onchange="getdata(),'.$js.'">';
                $data[] = $nestedData;
                $count++;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalTitles),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );
        echo json_encode($json_data);
    }
    public function update_change_team2($teamid,$matchkeyid,$Uid, Request $request){
        $data = $request->all();
        $input['players'] = implode(',',$data['playing']);
        $input['vicecaptain'] = $data['playingvc'];
        $input['captain'] = $data['playingc'];
        $input['points'] = 0;
        if(!empty($input['players']) && !empty($input['vicecaptain']) && !empty($input['captain'])){
            foreach ($data['playing'] as $value) {
                
                $playerpoint = DB::table('result_points')->where('matchkey',$matchkeyid)->where('playerid',$value)->first(['total']);
                
                if($value==$input['vicecaptain']){
                    $input['points'] += $playerpoint->total*1.5;
                }else if($value==$input['captain']){
                    $input['points'] += $playerpoint->total*2;
                }else{
                    $input['points'] += $playerpoint->total;
                }
            }
            $input['points'] = floor($input['points']);
            DB::table('jointeam')->where('id',$teamid)->update($input);
            return redirect()->action('ContestFullDetailController@user_team',[$teamid,$matchkeyid,$Uid])->with('Team has been updated');
        }else{
            if(!empty($input['vicecaptain'])){
                return redirect()->back()->with('danger','Select Vice Captain');
            }else if(!empty($input['captain'])){
                return redirect()->back()->with('danger','Select Captain');
            }
           
        }
    }


    public function leaderboard(Request $request){

      $f_type = request()->get('fantasy_type');
      $f_type = !empty($f_type) ? $f_type : 'Cricket';

     date_default_timezone_set('Asia/Kolkata');
      $allchallenges = array();
      if(request()->has('matchid')){
        $matchid=request('matchid');
        if($matchid!=""){
           $allchallenges = DB::table('series')
                              // ->join('series', 'series.id', 'listmatches.series')
                              // ->join('teams as t1','t1.id','=','listmatches.team1')
                              // ->join('teams as t2','t2.id','=','listmatches.team2')
                              ->where('series.id',$matchid)
                              // ->where('listmatches.fantasy_type', $f_type)
                              ->orderBy('series.start_date', 'desc')
                              ->select('series.id as series', 'series.*','series.name as series_name', 'series.has_leaderboard', 'series.winning_status');
                              // ->first();
                              // ->paginate(10);
           
          if(request()->has('is_live')){
              $is_live=request('is_live');
              if($is_live!=""){
                  $allchallenges = $allchallenges
                                      ->where('series.status',$is_live);
                  
              }

          }
  
          $allchallenges = $allchallenges
                              ->first();
        }

      }
      
      $currentdate = date('Y-m-d h:i:s');
      $findalllistmatches = DB::table('series')->select('*')->where('fantasy_type',$f_type)->where('status', 'opened')->orderBY('created_at','ASC')
  ->where('has_leaderboard', 'yes');

      if(request()->has('is_live')){
          $is_live=request('is_live');
          if($is_live!=""){
              $findalllistmatches = $findalllistmatches
                                  ->where('series.status',$is_live);
              
          }

      }

      $findalllistmatches = $findalllistmatches
                          ->get();
      
      return view('contest_detail.leaderboard',compact('findalllistmatches','allchallenges'));
  }
  
  public function leaderboard_rank(Request $request){

  // increase maximum execution time
  ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
  ini_set('max_execution_time', '0'); // for infinite time of execution 

      $f_type = request()->get('fantasy_type');
      $f_type = !empty($f_type) ? $f_type : 'Cricket';

     date_default_timezone_set('Asia/Kolkata');
      $allchallenges = array();
      if(request()->has('matchid')){
        $matchid=request('matchid');
        if($matchid!=""){
          //  $allchallenges = $this->leaderboard_calculate($matchid);
           
          //  $getdata = $allchallenges;
              
          $getdata = DB::table('series_leaderboard')
                          ->where('series_leaderboard.series_id',$matchid)
                          ->join('registerusers','registerusers.id','=','series_leaderboard.userid')
                          ->select(DB::raw('sum(points) as totalpoints'),'series_leaderboard.*','registerusers.image','registerusers.team')->groupBy('series_leaderboard.userid')->orderBy('totalpoints','desc')->get();

           $geturl = Helpers::geturl();

           $Json = [];
          if(!empty($getdata)){

              // $getdata = json_decode($getdata);

              $Json = [];
              // echo "<pre>";print_r($getdata);die;
              // if(!empty($getdata)){
              $ii=0;
              $ioo=1;
              foreach($getdata as $val){
                  $userdata = DB::table('registerusers')->where('id',$val->userid)->first();
                  if(!empty($userdata)){
                      
                      $Json[$ii]['userid'] = $userdata->id;
                      $Json[$ii]['team'] = $userdata->team;
                      $result= DB::table('registerusers')->where('id',$val->userid)->select('image')->first();
                      if(empty($result->image) or $result->image == 'null'){
                          $Json[$ii]['image'] = $geturl.'/'.Helpers::settings()->user_image ?? '';
                      }else{
                          if( @GetImageSize($result->image) ) {
                              $Json[$ii]['image'] = $result->image;
                          } else {
                              $Json[$ii]['image'] = $geturl.'user_image.png';
                          }
                      }
                      $Json[$ii]['points'] = number_format($val->totalpoints, 2, '.', '');
                      $Json[$ii]['rank'] = $ioo;
                      $Json[$ii]['userno'] = 0;
                      
                      $ii++;
                      $ioo++;
                  }
              }
          }

          // array_multisort(array_column($Json, 'userno'), SORT_ASC, $Json);
          array_multisort(array_column($Json,'userno'),SORT_ASC,array_column($Json,'points'),SORT_DESC,$Json);

        }

      }

      $allchallenges = ($Json);

      $currentdate = date('Y-m-d h:i:s');
      $findalllistmatches = DB::table('series')->select('*')->where('fantasy_type',$f_type)->where('status', 'opened')->orderBY('created_at','ASC')->get();
      
      return view('contest_detail.leaderboard_rank',compact('findalllistmatches','allchallenges'));
  }

  // top highest team points only 
public function leaderboard_calculate($series)
  {
      $input = request()->all();
      // $user = Helpers::isAuthorize($request);
      $geturl = Helpers::geturl();
      // $series=$input['series_id'];
      $getdata=DB::table('listmatches')
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
          ->get();

  if( !empty($getdata) ) {
    foreach ($getdata as $key => $data) {

      $getdata1=DB::table('listmatches')
            ->where('series',$series)
            ->where('launch_status','=','launched')
                          ->where('listmatches.final_status','!=','IsAbandoned')->where('listmatches.final_status','!=','IsCanceled')
                          ->where('listmatches.status','!=','notstarted')
            // ->where('final_status','=','winnerdeclared')
            ->select('listmatches.series','listmatches.name','listmatches.matchkey')->get();

      $total = 0;
      if(!empty($getdata1)){
        foreach($getdata1 as $val1){

          $total_points_match_wise = DB::table('jointeam')
                        ->where('jointeam.matchkey',$val1->matchkey)
                        ->where('jointeam.userid',$data->userid)
                        ->join('joinedleauges','joinedleauges.teamid','jointeam.id')
                        // ->join('finalresults','finalresults.joinedid','joinedleauges.id')
                        ->join('matchchallenges','matchchallenges.id','joinedleauges.challengeid')
                        ->join('contest_category','contest_category.id','matchchallenges.contest_cat')
                        ->select('jointeam.userid','jointeam.points','jointeam.teamnumber')
                        ->orderBy('jointeam.points','desc')
                        // ->where('contest_category.name','LEADERBOARD')
                                                  ->where('contest_category.has_leaderboard_points','yes')
                        // ->groupBy('jointeam.matchkey')
                        ->first();

          if(!empty($total_points_match_wise)){ 
            $total += $total_points_match_wise->points;
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

  // $leaderboard = array();
  // $leaderboard['series'] = $series;
  // $leaderboard['data'] = $getdata;

  // $is_already_exists = DB::table('leaderboard')
  // 						->where('series', $series)
  // 						->exists();
  
  // if($is_already_exists) {
  // 	DB::connection('mysql2')->table('leaderboard')
  // 		->where('series', $series)
  // 		->update($leaderboard);
  // } else {
  // 	DB::connection('mysql2')
  // 	->table('leaderboard')
  // 	->insert($leaderboard);
  // }

  // return 1;

  return $getdata;
  }
  
public function distribute_winning_amount_series_leaderboard($id){
    
      if( 
          !empty(
              request()->get('masterpassword')
          )   
          and
          auth()->user()->masterpassword == request()->get('masterpassword')
      ) {

          // correct masterpassword

      } else {
          return redirect()
                  ->back()
                  ->with('error', 'Invalid masterpassword');
      }

      $abcdd = DB::table('series')
              ->where('id',$id)                
              ->where('winning_status', '0')
              ->first();
      if(!empty($abcdd)){
          
          // $allchallenges = $this->leaderboard_calculate($id);
           
          // $getdata = $allchallenges;
              
          $getdata = DB::table('series_leaderboard')
                          ->where('series_leaderboard.series_id',$id)
                          ->join('registerusers','registerusers.id','=','series_leaderboard.userid')
                          ->select(DB::raw('sum(points) as totalpoints'),'series_leaderboard.*','registerusers.image','registerusers.team')->groupBy('series_leaderboard.userid')->orderBy('totalpoints','desc')->get();

          
          $geturl = Helpers::geturl();

          $Json = [];

          if(!empty($getdata)){

              // $getdata = json_decode($getdata);

              $Json = [];
              
              $ii=0;
              $ioo=1;

              foreach($getdata as $val){
                  $userdata = DB::table('registerusers')->where('id',$val->userid)->first();
                  if(!empty($userdata)){
                      
                      $Json[$ii]['userid'] = $userdata->id;
                      $Json[$ii]['team'] = $userdata->team;
                      $result= DB::table('registerusers')->where('id',$val->userid)->select('image')->first();
                      if(empty($result->image) or $result->image == 'null'){
                          $Json[$ii]['image'] = $geturl.'/'.Helpers::settings()->user_image ?? '';
                      }else{
                          if( @GetImageSize($result->image) ) {
                              $Json[$ii]['image'] = $result->image;
                          } else {
                              $Json[$ii]['image'] = $geturl.'user_image.png';
                          }
                      }
                      $Json[$ii]['points'] = number_format($val->totalpoints, 2, '.', '');
                      $Json[$ii]['rank'] = $ioo;
                      $Json[$ii]['userno'] = 0;
                      
                      $ii++;
                      $ioo++;
                  }
              }
          }

          // array_multisort(array_column($Json, 'userno'), SORT_ASC, $Json);
          array_multisort(array_column($Json,'userno'),SORT_ASC,array_column($Json,'points'),SORT_DESC,$Json);

          $joinedusers = $Json;
          
          if(!empty($joinedusers)){
              
              $seriespricecards =DB::table('seriespricecards')->where('series_id',$id)->select('min_position','max_position','price'
              /*, 'bonus' */
              )
              ->get();
              $seriespricecards = $seriespricecards->toArray();
              if(!empty($seriespricecards)){
                  foreach($seriespricecards as $prccrd){
                      $min_position=$prccrd->min_position;
                      $max_position=$prccrd->max_position;
                      for($i=$min_position;$i<$max_position;$i++){
                          $prc_arr[$i+1]['price']=$prccrd->price;
                          // $prc_arr[$i+1]['bonus']=$prccrd->bonus ?? 0;
                      }
                  }
              }
              else{
                  $prc_arr[1]['price']=0;
                  // $prc_arr[1]['bonus']=0;
              }
              
              // get the number of users //
              $user_points = array();
              if(!empty($joinedusers)){
                  $lp=0;
                  foreach($joinedusers as $jntm){
                      $user_points[$lp]['id']=$jntm['userid'];
                      $user_points[$lp]['points']=$jntm['points'];
                      $user_points[$lp]['joinedid']=$jntm['userid'];
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
                      $final_poin_user[$ps['joinedid'][0]]['amount']=$prc_arr[$ps['min']]['price'];
                      // $final_poin_user[$ps['joinedid'][0]]['bonus']=$prc_arr[$ps['min']]['bonus'] ?? 0;
                      $final_poin_user[$ps['joinedid'][0]]['rank']=$ps['min'];
                      $final_poin_user[$ps['joinedid'][0]]['userid']=$ps['id'][0];
                  }
                  else{
                      $ttl=0;$avg_ttl=0;
                      for($jj=$ps['min'];$jj<=$ps['max'];$jj++){
                          $sm=0;
                          if(isset($prc_arr[$jj])){
                              $sm=$prc_arr[$jj]['price'];
                          }
                          $ttl=$ttl+$sm;
                      }
                      $avg_ttl=$ttl/$ps['count'];
                      foreach($ps['joinedid'] as $keyuser=>$fnl){
                          $final_poin_user[$fnl]['points']=$ks;
                          $final_poin_user[$fnl]['amount']=$avg_ttl;
                          // $final_poin_user[$fnl]['bonus']=0;
                          $final_poin_user[$fnl]['rank']=$ps['min'];
                          $final_poin_user[$fnl]['userid']=$ps['id'][$keyuser];
                      }
                  }
              }
              
              // dd($final_poin_user);
              if(!empty($final_poin_user)){
                  foreach($final_poin_user as $fpuskjoinid=>$fpusv){
                      $fpusk = $fpusv['userid'];
                          
                      $winningdata = DB::table('seriesfinalresults')
                                      ->where('seriesid',$id)
                                      ->where('userid',$fpusk)
                                      ->first();
                      
                      if(empty($winningdata)){
                          $fres = array();
                          $challengeid = 0;
                          $seriesid = $id;
                          $transactionidsave = 'WIN-'.rand(1000,99999).$challengeid.$fpuskjoinid;
                          $fres['userid'] = $fpusk;
                          $fres['points'] = str_replace("'", "", $fpusv['points']);
                          $fres['amount'] = round($fpusv['amount'],2);
                          // $fres['bonus'] = round(($fpusv['bonus'] ?? 0),2);
                          $fres['rank'] = $fpusv['rank'];
                          $fres['matchkey'] = 0;
                          $fres['challengeid'] = $challengeid;
                          $fres['seriesid'] = $seriesid;
                          $fres['transaction_id'] = $transactionidsave;
                          $fres['joinedid'] = $fpuskjoinid;
                          $findalreexist = DB::table('seriesfinalresults')
                                              ->where('seriesid',$id)
                                              ->where('userid',$fpusk)
                                              ->select('id')->first();
                          if(empty($findalreexist)){
                              DB::connection('mysql2')->table('seriesfinalresults')->insert($fres);
                              $finduserbalance = DB::table('userbalance')->where('user_id',$fpusk)->select('balance','winning','bonus')->first();
                              if(!empty($finduserbalance)){
                                  
                                  if($fpusv['amount']>10000){
                                      $datatr = array();
                                      $dataqs = array();
                                      $tdsdata['tds_amount'] = (31.2/100)*$fpusv['amount'];
                                      $tdsdata['amount'] = $fpusv['amount'];
                                      $remainingamount = $fpusv['amount']-$tdsdata['tds_amount'];
                                      $tdsdata['userid'] = $fpusk;
                                      $tdsdata['challengeid'] = 0;
                                      DB::connection('mysql2')->table('tdsdetails')->insert($tdsdata);
                                      $fpusv['amount'] = $remainingamount;
                                      //user balance//
                                      $registeruserdetails = DB::table('registerusers')->where('id',$fpusk)->first();
                                      $findlastow = DB::table('userbalance')->where('user_id',$fpusk)->first();
                                      $dataqs['winning'] = number_format($findlastow->winning+$fpusv['amount'],2, ".", "");
                                      // $dataqs['bonus'] = number_format(($findlastow->bonus ?? 0)+$fpusv['bonus'],2, ".", "");
                                      
                                      DB::connection('mysql2')->table('userbalance')->where('id',$findlastow->id)->update($dataqs);
                                      //transactions entry//
                                      $datatr['transaction_id'] = $transactionidsave;;
                                      $datatr['type'] = 'Leaderboard Winning Amount';
                                      $datatr['transaction_by'] = Helpers::settings()->short_name ?? '';
                                      $datatr['amount'] = $fres['amount'];
                                      // $datatr['bonus'] = $fres['bonus'] ?? 0;
                                      $datatr['paymentstatus'] = 'confirmed';
                                      $datatr['challengeid'] = $challengeid;
                                      $datatr['win_amt'] = $fres['amount'];
                                      // $datatr['bonus_amt'] = $fres['bonus'];
                                      // $datatr['bal_bonus_amt'] = $dataqs['bonus'];
                                      $datatr['bal_win_amt'] = $dataqs['winning'];
                                      $datatr['bal_fund_amt'] = $finduserbalance->balance;
                                      $datatr['userid'] = $fpusk;
                                      $datatr['total_available_amt'] = $finduserbalance->balance+$dataqs['winning']+$finduserbalance->bonus;
                                      DB::connection('mysql2')->table('transactions')->insert($datatr);
                                      
                                      $datanot['title'] = 'You won leaderboard winning Rs.'.$fpusv['amount'].' and 31.2% amount of '.$tdsdata['amount'].' deducted due to TDS.';
                                      $datanot['userid'] = $fpusk;
                                      DB::connection('mysql2')->table('notifications')->insert($datanot);
                                      //push notifications//
                                      $titleget = 'Congrats! You won a series leaderboard.';
                                      Helpers::sendnotification($titleget,$datanot['title'],'',$fpusk);
                                  }else{
                                      $datatr = array();
                                      $dataqs = array();
                                      //user balance//
                                      $registeruserdetails = DB::table('registerusers')->where('id',$fpusk)->first();

                                      $findlastow = DB::table('userbalance')->where('user_id',$fpusk)->first();
                                      $dataqs['winning'] =  number_format($findlastow->winning+$fpusv['amount'],2, ".", "");
                                      // $dataqs['bonus'] =  number_format(($findlastow->bonus ?? 0)+$fpusv['bonus'],2, ".", "");
                                      DB::connection('mysql2')->table('userbalance')->where('id',$findlastow->id)->update($dataqs);
                                      if($fpusv['amount']>0){
                                          //transactions entry//
                                          $datatr['transaction_id'] = $transactionidsave;;
                                          $datatr['type'] = 'Leaderboard Winning Amount';
                                          $datatr['transaction_by'] = Helpers::settings()->short_name ?? '';
                                          $datatr['amount'] = $fpusv['amount'];
                                          // $datatr['bonus'] = $fpusv['bonus'] ?? 0;
                                          $datatr['paymentstatus'] = 'confirmed';
                                          $datatr['challengeid'] = $challengeid;
                                          $datatr['win_amt'] = $fpusv['amount'];
                                          // $datatr['bal_bonus_amt'] = $dataqs['bonus'];
                                          $datatr['bal_win_amt'] = $dataqs['winning'];
                                          $datatr['bal_fund_amt'] = $finduserbalance->balance;
                                          $datatr['userid'] = $fpusk;
                                          $datatr['total_available_amt'] = $finduserbalance->balance+$dataqs['winning']+$finduserbalance->bonus;
                                          DB::connection('mysql2')->table('transactions')->insert($datatr);
                                      
                                          //notifications entry//
                                          $datanot['title'] = 'You won leaderboard winning Rs.'.$fpusv['amount'];
                                          $datanot['userid'] = $fpusk;
                                          DB::connection('mysql2')->table('notifications')->insert($datanot);
                                          //push notifications//
                                          $titleget = 'Congrats! You Won a series leaderboard!';
                                          Helpers::sendnotification($titleget,$datanot['title'],'',$fpusk);
                                      }
                                  }
                              }
                          }
                      }
                  }
              }

              DB::connection('mysql2')
              ->table('series')
              ->where('id',$id)                
              ->update([
                  'winning_status' => '1'
              ]);

              return redirect()
                  ->back()
                  ->with('success', 'Leaderboard winning distributed successfully');
          }

          return redirect()
                  ->back()->with('error', 'No one Any User Joined This Leaderboard');
                  
      }else{
          return redirect()
                  ->back()
                  ->with('error', 'Winning of this series leaderboard has been already distributed');
      }
}

  public function leaderboard_winning_rank(Request $request){

      $f_type = request()->get('fantasy_type');
      $f_type = !empty($f_type) ? $f_type : 'Cricket';

     date_default_timezone_set('Asia/Kolkata');
      $allchallenges = array();
      if(request()->has('matchid')){
        $matchid=request('matchid');
        if($matchid!=""){
           $allchallenges = DB::table('seriesfinalresults')
                          ->where('seriesid',$matchid)
                          ->join('registerusers', 'registerusers.id', 'seriesfinalresults.userid')
                          ->select(
                              'seriesfinalresults.*', 
                              'registerusers.team',
                              'registerusers.image'
                          )
                          ->get();

        }

      }

      $currentdate = date('Y-m-d h:i:s');
      $findalllistmatches = DB::table('series')->select('*')->where('fantasy_type',$f_type)->where('status', 'opened')->orderBY('created_at','ASC')->get();
      
      return view('contest_detail.leaderboard_winning_rank',compact('findalllistmatches','allchallenges'));
  }

}
