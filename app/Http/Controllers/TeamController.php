<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Helpers\Helpers;

use Session;
use DB;
use Illuminate\Support\Facades\Storage;
class TeamController extends Controller
{
  
    // to view all the teams //
    public function view_team(){
      return view('team.view');
    }
    // for the datatables of all the teams //
    public function view_team_datatable(Request $request){
        $columns = array(
            0 => 'id',
            1 => 'team',
            2 => 'short_name',
            3 => 'short_name',
            4 => 'created_at',
            5 => 'updated_at',
        );


        $totalTitles = DB::table('teams')->count();
        $totalFiltered = $totalTitles;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
                $query = DB::table('teams');
                if(request()->has('name')){
                   $name=request('name');
                   if($name!=""){
                   $query = $query->where('team', 'LIKE', '%'.$name.'%');
                  }
                }

                if(request()->has('fantasy_type')){
                   $fantasy_type=request('fantasy_type');
                   if($fantasy_type!=""){
                   $query = $query->where('fantasy_type',$fantasy_type);
                  }
                }
                $count = $query->where('fantasy_type', 'Cricket')->count();
               $titles =  $query->where('fantasy_type', 'Cricket')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalTitles = $count;
            $totalFiltered = $totalTitles;

            if($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
              $count = $totalFiltered - $start;
            } else {
              $count = $start + 1;
            }
        if (!empty($titles)) {
            $data = array();
            // $count = 1;
            foreach ($titles as $title) {

                $imagelogo = asset('public/'.Helpers::settings()->team_image ?? '');
                $default_img = "this.src='".$imagelogo."'";

                if($title->logo==''){
                    $img = '<img src="'.$imagelogo.'" class="w-40px view_team_table_images h-40px rounded-pill">';
                }else{

                    $imagelogo = asset('public/'.$title->logo);
                    $img = '<img src="'.$imagelogo.'" class="w-40px view_team_table_images h-40px rounded-pill" onerror="'.$default_img.'">';
                }
              $c=action('TeamController@edit_team',base64_encode(serialize($title->id)));
              $action ='<a href="'.$c.'" class="btn btn-sm btn-orange w-35px h-35px text-uppercase text-nowrap" data-toggle="tooltip" title="Edit"><i class="fad fa-pencil"></i></a>';
              //$nestedData['s_no'] = $Data1;
              $nestedData['id'] = $count;
              $nestedData['team'] = $title->team;
              $nestedData['short_name'] = $title->short_name;
              $nestedData['logo'] = $img;
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
    // to edit the teams//
    public function edit_team(Request $request,$id){
      $getid = unserialize(base64_decode($id));
      $data = DB::table('teams')->where('id',$getid)->first();
      if($request->isMethod('post')){
        $input = $request->all();

      if(!empty($input['logo'])){
        $extension = $input['logo']->getClientOriginalExtension();
        $ext = array("png", "PNG","jpg",'jpeg','JPG',"JPEG");
        if(!in_array($extension, $ext)){
          return redirect()->back()->with('danger','Only Images are allowed.');
        }
        // dd($ext);
        $hii =  Storage::disk('public_folder')->putFile('images_logo',$input['logo'], 'public');
        $dat['logo'] = $hii;

        $oldlogo = DB::table('teams')->where('id', $input['id'])->select('logo')->first();
        if(!empty($oldlogo->logo)){
          $filename= $oldlogo->logo;
          Storage::disk('public_folder')->delete($filename);
        }
      }
      $dat['team'] = $input['team'];
      $dat['short_name'] = $input['short_name'];
      $dat['color'] = $input['color'];
      DB::connection('mysql2')->table('teams')->where('id',$input['id'])->update($dat);
      return redirect()->action('TeamController@edit_team',$id)->with('success','Team edit successfully');
      }
      return view('team.edit',compact('data'));
    }
    //to delete the team
    public function delete_team($id){
      $ids=unserialize(base64_decode($id));
       $findata=DB::table('teams')->where('id',$id)->first();
       $findexist=DB::table('listmatches')->where('team1',$ids)->orWhere('team2',$ids)->get();
       if(count($findexist)=='0'){
         if(!empty($findata))
         {
            $oldlogo = DB::table('teams')->where('id', $ids)->first();
            if(!empty($oldlogo->logo)){
                $filename= $oldlogo->logo;
                Storage::disk('public_folder')->delete($filename);
              }
            DB::connection('mysql2')->table('teams')->where('id',$ids)->delete();
            Session::flash('message', 'Successfully deleted');
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
          }
    else{
      Session::flash('message', 'sorry,failed to delete');
      Session::flash('alert-class', 'alert-danger');
      return redirect()->back();
    }
     }
     else{
      Session::flash('message', 'sorry,failed to delete,this team is already exist in listmatch');
      Session::flash('alert-class', 'alert-danger');
      return redirect()->back();
     }
    }
    //to download the excel of all the teams
    public function downloadteamdata(){
      $output1 = "";
      $output1 .='"Sno.",';
      $output1 .='"Team Name",';
      $output1 .='"Team Short name",';
      $output1 .="\n";
      $query = DB::table('teams');
      if(request()->has('name')){
        $name=request('name');
        if($name!=""){
          $query->where('team', 'LIKE', '%'.$name.'%');
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
          $output1 .='"'.$get->team.'",';
          $output1 .='"'.$get->short_name.'",';
          $output1 .="\n";
          $count++;
        }
      }
      $filename =  "Details-teamdetails.csv";
      header('Content-type: application/csv');
      header('Content-Disposition: attachment; filename='.$filename);
      echo $output1;
      exit;
    }
}
