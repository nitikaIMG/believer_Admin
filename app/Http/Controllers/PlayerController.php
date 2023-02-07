<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Helpers\Helpers;

use DB;
use Session;
use Redirect;

use Illuminate\Support\Facades\Storage;
class PlayerController extends Controller
{
    
    // to view all the player list //
    public function view_player(){
     $findallteams = DB::table('teams')->orderBy('team', 'ASC')->where('fantasy_type', 'Cricket')->get();
    	return view('player.view',compact('findallteams'));
    }

    public function get_teams(){

     $findallteams = DB::table('teams');

        if(request()->has('fantasy_type')){
           $fantasy_type=request('fantasy_type');
           if($fantasy_type!=""){
           $findallteams = $findallteams->where('fantasy_type',$fantasy_type);
          }
        }

    $findallteams = $findallteams->get();

	return $findallteams;
    }

    // for the datatable of the players table //
    public function view_player_datatable(Request $request){
        date_default_timezone_set('Asia/Kolkata');
    	 $columns = array(
               0 => 'id',
            1 => 'player_name',
            2 => 'players_key',
            3 => 'role',
            4 => 'credit',
            5 => 'created_at',
            6 => 'updated_at',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query = DB::table('players');
        if(request()->has('playername')){
            $name=request('playername');
            if($name!=""){
                $query =$query->where('player_name', 'LIKE', '%'.$name.'%');
            }
        }
        if(request()->has('team')){
            $team = request('team');
            if($team!=""){
                $query =$query->where('team', '=',$team);
            }
        }
        if(request()->has('role')){
            $role = request('role');
            if($role!=""){
                $query =$query->where('role', '=',$role);

            }
        }

        if(request()->has('fantasy_type')){
           $fantasy_type=request('fantasy_type');
           if($fantasy_type!=""){
           $query = $query->where('fantasy_type',$fantasy_type);
          }
        }

        $totalTitles = $query->where('fantasy_type', 'Cricket')->count();
        $totalFiltered = $totalTitles;
        $titles =  $query->where('fantasy_type', 'Cricket')
                        ->distinct('team')->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();

        if (!empty($titles)) {
            $data = array();

            if($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
                $count = $totalFiltered - $start;
            } else {
                $count = $start + 1;
            }

            foreach ($titles as $title) {
            	$role="'.$title->role.'";

                $imagelogo = asset('public/'.Helpers::settings()->player_image ?? '');
                $default_img = "this.src='".$imagelogo."'";

            	if($title->image==''){
                    $imagelogo = asset('public/'.Helpers::settings()->player_image ?? '');
                    $img = '<img src="'.$imagelogo.'"  class="w-40px view_team_table_images h-40px rounded-pill">';
                }else{
                    $imagelogo = asset('public/'.$title->image);
                    $img = '<img src="'.$imagelogo.'"  class="w-40px view_team_table_images h-40px rounded-pill" onerror="'.$default_img.'">';
                }

            	$c =action('PlayerController@edit_player',base64_encode(serialize($title->id))) ;

            	$cd = '<span id="credittd'.$title->id.'">'.$title->credit.'</span>';

                $action = '<a href="'.$c.'" class="btn btn-sm w-35px h-35px mr-1 mb-1 btn-orange" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil"></i></a>
                 <a onclick="updateplayer('.$title->id.','.$role.','.$title->credit.')" class="btn btn-sm w-35px h-35px mr-1 mb-1 btn-primary" data-toggle="tooltip" title="Update credit" id="updateplayer'.$title->id.'"><i class="fas fa-sync-alt"></i></a>
            <a onclick="saveplayer('.$title->id.')" class="btn btn-sm w-35px h-35px mr-1 mb-1 btn-success" id="saveplayer'.$title->id.'" style="display:none;" data-toggle="tooltip" title="Save credit"><i class="fad fa-save"></i></a>';


            	$nestedData['id'] = $count;
            	$nestedData['player_name'] = $title->player_name;
                $nestedData['players_key'] = $title->players_key;
                $nestedData['role'] = $title->role;
                $nestedData['credit'] = $cd;
                $nestedData['image'] = $img;

                $nestedData['action'] = $action;

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
    // to edit the details of players//
     public function edit_player(Request $request,$gid){
         date_default_timezone_set('Asia/Kolkata');
        $id = unserialize(base64_decode($gid));

         $player = DB::table('players')->where('id',$id)->first();
             if ($request->isMethod('post')){
                $input = request()->all();
                
                unset($input['_token']);
                $playerd['country'] = $input['country'];
                $playerd['credit'] = $input['credit'];
                $playerd['role'] = $input['role'];
                $playerd['player_name'] = $input['player_name'];
                $playerd['battingstyle'] = $input['battingstyle'];
                $playerd['bowlingstyle'] = $input['bowlingstyle'];
                $playerd['dob'] = date('Y-m-d',strtotime($input['dob']));
                $olddob = date('Y-m-d',strtotime('-15 year',strtotime(date('Y-m-d'))));
                if($playerd['dob']>=$olddob){
                    return redirect()->back()->with('danger','Invalid DOB for player and player should be atleast 15 years old.');
                }
                if($request->file('image'))
                {
                    $image = $request->file('image');
                    $extension = $image->getClientOriginalExtension();
                    $ext = array("jpg","jpeg","png", 'JPG','JPEG', 'PNG');
                    if(!in_array($extension, $ext)){
                      return redirect()->back()->with('danger','Invalid extension of file you uploaded. You can only upload image.');
                    }
                    $hii =  Storage::disk('public_folder')->putFile('players',$input['image'], 'public');
                    $input['image']  = $hii;
                    // delete old image
                    $oldimage = DB::table('players')->where('id',$id)->first();
                    if(!empty($oldimage)){
                    $filename= $oldimage->image;
                    Storage::disk('public_folder')->delete($filename);
                    $playerd['image'] = $input['image'];
                }
                
                } else {
                }
                $findallplay = DB::connection('mysql2')->table('players')->where('id',$id)->update($playerd);
                return Redirect::back()->with('success','Player updated!');
             }else{
               return view('player.edit',compact('player'));
        }
    }
    // manually update the credit of player //
	public function saveplayerroles(Request $request){
		if ($request->isMethod('post')){
			$input = request()->all();
			$data['credit'] = $input['credit'];
			$findplayerkey = DB::connection('mysql2')->table('players')->where('id',$input['id'])->update($data);
			echo 1;die;
		}
	}
    // to add player manually
     public function addplayermanually(Request $request){
        if ($request->isMethod('post')){
            $input = $request->all();
            $matchkikey = $input['matchkey'];
            $findplayerexist = DB::table('players')->where('players_key',$input['players_key'])->where('team',$input['team'])->first();
            $data['player_name'] = $input['player_name'];
            $data['players_key'] = $input['players_key'];
            $data['role'] =  $input['role'];
            $data['credit'] =  $input['credit'];
            if($request->file('image'))
            {
                $image=  Storage::disk('public_folder')->putFile('images_logo',$input['image'], 'public');
                $data['image'] = $image;
            }
            if(empty($findplayerexist)){
                $data['team'] = $input['team'];
                $playerid = DB::connection('mysql2')->table('players')->insertGetId($data);
                $credit=$input['credit'];
            }
            else{
                $playerid = $findplayerexist->id;
                $credit = $findplayerexist->credit;
            }

            // insert players for a match//
            $findplayer1entry = DB::table('matchplayers')->where('matchkey',$matchkikey)->where('playerid',$playerid)->first();
            if(empty($findplayer1entry)){
                $matchplayerdata['matchkey'] = $matchkikey;
                $matchplayerdata['playerid'] = $playerid;
                $matchplayerdata['role'] = $data['role'];
                $matchplayerdata['name'] = $data['player_name'];
                $matchplayerdata['credit'] = $credit;
                DB::connection('mysql2')->table('matchplayers')->insert($matchplayerdata);
            }
            return Redirect::back()->with('success','Successfully added the player!');
        }
    }
    //to download the excel of all the player list
    public function downloadallplayerdetails(){
    $output1 = "";
    $output1 .='"Sno.",';
    $output1 .='"Player Name",';
    $output1 .='"Role",';
    $output1 .='"Credit",';
    $output1 .="\n";
    $query = DB::table('players');
    if(request()->has('team')){
      $team=request('team');
      if($team!=""){
        $query->where('team', 'LIKE', '%'.$team.'%');
      }
    }
     if(request()->has('playername')){
      $playername=request('playername');
      if($playername!=""){
        $query->where('player_name', 'LIKE', '%'.$playername.'%');
      }
    }
     if(request()->has('role')){
      $role=request('role');
      if($role!=""){
        $query->where('role', 'LIKE', '%'.$role.'%');
      }
    }
    if(request()->has('fantasy_type')){
       $fantasy_type=request('fantasy_type');
       if($fantasy_type!=""){
       $query = $query->where('fantasy_type',$fantasy_type);
      }
    }
     $query = $query->where('fantasy_type', 'Cricket');

    $getlist = $query->orderBY('id','ASC')->get();
    if(!empty($getlist)){
      $count=1;
      foreach($getlist as $get){
        $output1 .='"'.$count.'",';
        $output1 .='"'.$get->player_name.'",';
        $output1 .='"'.$get->role.'",';
        $output1 .='"'.$get->credit.'",';
        $output1 .="\n";
        $count++;
      }
    }
    $filename =  "Details-playerdetails.csv";
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename='.$filename);
    echo $output1;
    exit;
  }
}
