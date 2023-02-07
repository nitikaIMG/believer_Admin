<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request; 
use Config;
use Redirect;
use DB;
use Session;
use Carbon\Carbon;
use Hash;
use App\Helpers\Helpers;
use App\Http\Controllers\api\UserApiController;

class AutoTeamController extends Controller{
  
  // Generate code section
    public function generateTeamname($username){
        $number = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charcode =substr($username, 0, 6);
        $numbercode = substr(str_shuffle(str_repeat($number, 2)), 0, 2);
        $refer_code = $charcode.$numbercode;
        $findifExist = DB::table('registerusers')->where('team',$username)->count();
        if($findifExist==0){
            return $refer_code;
        }
        else{
            $this->generateTeamname($username);
        }
    }
    public function generateAltraffleCode($username){
        $number = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charcode =substr($username, 0, 3);
        $numbercode = substr(str_shuffle(str_repeat($number, 3)), 0, 3);
        $refer_code = (Helpers::settings()->short_name ?? '').$charcode.$numbercode;
        $findifExist = DB::table('registerusers')->where('refer_code',$refer_code)->count();
            if($findifExist==0){
                return $refer_code;
            }
            else{
                $this->generateAltraffleCode($username);
            }
    }
    public function updateteam(){
      $result1= DB::table('joinedleauges')->where('registerusers.user_status','0')->where('matchkey','c.match.vaw_vs_waw.35d58')->join('registerusers','registerusers.id','joinedleauges.userid')->select('joinedleauges.id')->get();
      echo "<pre>";print_r($result1);die;

    }
    public function add_admin_teams($matchkey, Request $request){
      $first = DB::table('registerusers')->whereNull('image');
      echo '<pre>';print_r($first);die;
        $findmatch = DB::table('listmatches')->where('matchkey',$matchkey)->first();
      $matchplayers= DB::table('matchplayers')->where('matchkey',$matchkey)->get();
      if($request->isMethod('post')){
            $input= $request->all();
              $data['teamnumber']= 1;
            $result= DB::table('adminteams')->where('matchkey',$matchkey)->orderBy('id','desc')->first();
            if(!empty($result)){
                $data['teamnumber']=$result->teamnumber;
            }
            $data['players'] = implode(',' ,$input['id']);
            $data['captain']= $input['captain'];
            $data['vicecaptain']= $input['vice_captain'];
            $data['matchkey']= $matchkey;
            DB::connection('mysql2')->table('adminteams')->insert($data);
            return redirect()->action('AutoTeamController@view_admin_user')->with('success','Successfully added new team');
      }else{
         return view('team.addadminteams',compact('matchplayers','findmatch'));
      }
    }
    public function view_admin_user(){
        return view('user.view_admin_user');
    }
    public function admin_user_datatable(Request $request){
      $columns = array(
        0 => 'id',
        1 => 'team',
        2 => 'email',
        3 => 'mobile',
        4 => 'status',
        5 => 'refer_code',
        6 => 'created_at',
        7 => 'updated_at'
      );
       $datata=$request->all();
       $limit = $request->input('length');
       $start = $request->input('start');
       $order = $columns[$request->input('order.0.column')];
       $dir = $request->input('order.0.dir');
    
       $query = DB::table('registerusers');
        $query=$query->join('user_verify', function ($join) {
            $join->on('registerusers.id', '=', 'user_verify.userid')
                 ->where('registerusers.user_status', '!=', '0')->select('user_verify.mobile_verify as mobile_verify','user_verify.email_verify as email_verify','user_verify.pan_verify as pan_verify','user_verify.bank_verify as bank_verify','user_verify.userid as userid','registerusers.*');
        });
       if(request()->has('name')){
        $name=request('name');
        if($name!=""){
          $query->where('registerusers.team', 'LIKE', '%'.$name.'%');
        }
      }
      if(request()->has('email')){
        $email=request('email');
        if($email!=""){
          $query->where('registerusers.email', 'LIKE', '%'.$email.'%');
        }
      }
      if(request()->has('code')){
        $code=request('code');
        if($code!=""){
          $query->where('special_refer.code', 'LIKE', '%'.$code.'%');
        }
      }
      if(request()->has('status')){
        $status=request('status');
        if($status!=""){
            if($status == 'pan'){
                $query->where('user_verify.pan_verify',-1);
            }elseif($status == 'bank'){
                $query->where('user_verify.bank_verify',-1);
            }else{
                $query->where('registerusers.status',$status);
            }
        }
      }
      if(request()->has('mobile')){
        $mobile=request('mobile');
        if($mobile!=""){
          $query->where('registerusers.mobile',$mobile);
        }
      }
      $totalTitles = $query->count();
      $totalFiltered = $totalTitles;
      $titles = $query->offset($start)->limit($limit)->orderBy('registerusers.id', $dir)->get();
      if (!empty($titles)) {
        $data = array();
        $count = 1;
        foreach ($titles as $title) {
    
          $id= base64_encode(serialize($title->id));
          if($title->mobile_verify == 0 ){
            $mobile_v = "<i class='fa fa-mobile' style='font-size: 23px;'></i><i class='fa fa-close' style='font-size: 20px;margin-left: 8px;color:red'></i><span style='font-weight: 700;margin-left: 5px;'>|<span>";
          }elseif($title->mobile_verify == 1 ){
            $mobile_v = "<i class='fa fa-mobile' style='font-size: 23px;'></i><i class='fa fa-check-circle-o' style='font-size: 20px;color:green;margin-left: 8px;'></i><span style='font-weight: 700;margin-left: 5px;'>|<span>";
          }
    
          if($title->email_verify == 0 ){
            $email_v="<i class='fa fa-envelope-o' style='font-size: 20px;margin-left: 5px;'></i> <i class='fa fa-close' style='font-size: 20px;margin-left: 8px;color:red'></i><span style='font-weight: 700;margin-left: 5px;'>|<span>";
          }elseif($title->email_verify == 1 ){
            $email_v="<i class='fa fa-envelope-o' style='font-size: 20px;margin-left: 5px;'></i> <i class='fa fa-check-circle-o' style='font-size: 20px;color:green;margin-left: 8px;'></i><span style='font-weight: 700;margin-left: 5px;'>|<span>";
          }
          
          if($title->pan_verify == -1 ){
            $pan_v = "<i class='fa fa-id-card-o' style='font-size: 20px;margin-left: 5px;'></i>  <i class='fa fa-question-circle' style='font-size: 20px;'></i><span style='font-weight: 700;margin-left: 5px;'>|<span>";
          }elseif($title->pan_verify == 0 ){
            $pan_v = "<i class='fa fa-id-card-o' style='font-size: 20px;margin-left: 5px;'></i>  <i class='fa fa-check-circle-o' style='color:orange;font-size: 20px;margin-left: 8px;'></i><span style='font-weight: 700;margin-left: 5px;'>|<span>";
          }elseif($title->pan_verify == 1 ){
            $pan_v = "<i class='fa fa-id-card-o' style='font-size: 20px;margin-left: 5px;'></i>  <i class='fa fa-check-circle-o' style='font-size: 20px;margin-left: 8px;color:green;'></i><span style='font-weight: 700;margin-left: 5px;'>|<span>";
          }elseif($title->pan_verify == 2 ){
            $pan_v = "<i class='fa fa-id-card-o' style='font-size: 20px;margin-left: 5px;'></i>  <i class='fa fa-close' style='font-size: 20px;margin-left: 8px;color:red'></i><span style='font-weight: 700;margin-left: 5px;'>|<span>";
          }
    
    
          if($title->bank_verify == -1 ){
            $bank_v = "<i class='fa fa-university' style='font-size: 20px;margin-left: 5px;'></i> <i class='fa fa-question-circle' style='font-size: 20px;'></i>";
          }elseif($title->bank_verify == 0 ){
            $bank_v = "<i class='fa fa-university' style='font-size: 20px;margin-left: 5px;'></i> <i class='fa fa-check-circle-o' style='color:orange;font-size: 20px;margin-left: 8px;'></i>";
          }elseif($title->bank_verify == 1 ){
           $bank_v = "<i class='fa fa-university' style='font-size: 20px;margin-left: 5px;'></i> <i class='fa fa-check-circle-o' style='font-size: 20px;margin-left: 8px;color:green;'></i>";
         }elseif($title->bank_verify == 2 ){
          $bank_v = "<i class='fa fa-university' style='font-size: 20px;margin-left: 5px;'></i> <i class='fa fa-close' style='font-size: 20px;margin-left: 8px;color:red'></i>";
        }
    
        
        $a=""; $b="";  $c="";
        $a = action('RegisteruserController@viewtransactions',$title->id);
        $b = action('RegisteruserController@updateuserstatus',[$title->id,'deactivated']);
        $c = action('RegisteruserController@updateuserstatus',[$title->id,'activated']);
        $f = action('RegisteruserController@edituserdetails',base64_encode(serialize($title->id)));
        $bb=action('RegisteruserController@getuserdetails',$title->id);
        $aa ='<a href="'.$bb.'" style="text-decoration:underline;">'.$title->id.'';
        if(strtolower($title->status)!='activated')
        {
          $d = "<a  class='dropdown-item waves-light waves-effect' href='".$c."'>Activate</a>";
        }
        else
        {
          $d ="<a class='dropdown-item waves-light waves-effect' href='".$b."'>Block</a>";
        }
        $button = '<div class="btn-group dropdown">
                    <button class="btn-sm btn btn-primary btn-active-pink dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button" aria-expanded="true">
                        Action <i class="dropdown-caret"></i>
                    </button>
                    <ul class="dropdown-menu" style="opacity: 1;">
                        <li><a class="dropdown-item waves-light waves-effect" href="'.$a.'">User Transactions</a></li>
                        <li>'.$d.'</li>
                        <li><a class="dropdown-item waves-light waves-effect" href="'.$f.'">Edit User Details</a></li>
                    </ul>
                </div>';
          $countrefers = 0; $referamounts =0;$splcode='';
        $countrefers= DB::table('registerusers')->where('refer_id',$title->id)->count();
        
        $referamount = DB::table('transactions')->where('userid',$title->id)->where('type','Referred Signup bonus')->select(DB::raw('sum(transactions.bonus_amt) as bonus_amts'))->first();
        if(!empty($referamount->bonus_amts)){
          $referamounts = $referamount->bonus_amts;
        }
        if(!empty($title->bonus)){
          $countdspl = $title->bonus;
          $splcode = $title->bcode;
        }else{
            $countdspl=0;
        }
        $nestedData['id'] = $aa;
        $nestedData['team'] = $title->team;
        $nestedData['email'] = $title->email;
        $nestedData['mobile'] = $title->mobile;
        $nestedData['verification'] =$mobile_v.$email_v.$pan_v.$bank_v;
        $nestedData['total_refers'] = '<a href="'.action('RegisteruserController@allrefer',$title->id).'">'.$countrefers.'</a>';
        $nestedData['refer_amount'] = $referamounts;
        $nestedData['splrefer_amount'] = $countdspl;
        $nestedData['splcode'] = $splcode;
        $nestedData['refercode'] = $title->refer_code;
        $nestedData['action'] = $button;
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
    public function addjoinedusers($id, Request $request){
        if($request->isMethod('post')) {
            $input= $request->all();
            $challengeid = unserialize(base64_decode($id));
            $joinedusers= $input['joinedusers'];
            unset($input['_token']);
            $findchallenge = DB::table('matchchallenges')->where('id',$challengeid)->first();
            if(!empty($findchallenge)){
              $maximumusers= $findchallenge->maximum_user;
              $joined_users = $findchallenge->joinedusers;
              $joindata=$joined_users+$joinedusers;
              if($findchallenge->status=='closed'){
                return redirect()->back()->with('danger','league closed');
              }
              if($joinedusers > $maximumusers){
                return redirect()->back()->with('danger','Joined user should be less than maximum users ');
              }
              if($joindata > $maximumusers){
                return redirect()->back()->with('danger','Joined user should be less than maximum users ');
              }
              $matchkey=$findchallenge->matchkey;
              $findentryfee=$findchallenge->entryfee;
              $match= DB::table('listmatches')->where('launch_status','launched')->where('matchkey',$matchkey)->first();
              $userdata= DB::table('registerusers')->where('user_status','!=','0')->where('user_status','!=','Telangana')->where('user_status','!=','Orissa')->where('user_status','!=','Assam')->limit($maximumusers)->select('id','team','image')->get();
              $userarray=array();
            if(!empty($userdata->toArray())) {
              foreach($userdata as $uss){
                $userarray[]= $uss->id;
              }
            }
        $ttlusers= count($userarray);
        if($ttlusers < $input['joinedusers']){
          return redirect()->back()->with('danger','Please add more users!');
        }
        shuffle($userarray);
        if(!empty($userarray)) {
          for ($jkd=0; $jkd < $input['joinedusers']; $jkd++) {
            $userid= $userarray[$jkd];
            $findchallengess = DB::table('matchchallenges')->where('id',$challengeid)->first();
            $finduserbalance = DB::table('userbalance')->where('user_id',$userid)->first();
            if($findchallengess->joinedusers < $maximumusers){
               // echo '<pre>';print_r($maximumusers);die;
              if(!empty($finduserbalance)){
                $findusablebalance = number_format($finduserbalance->balance+$finduserbalance->winning,2, ".", "");
                    if($findusablebalance < $findentryfee){
                      $baldata['balance']='100000';
                       DB::connection('mysql2')->table('userbalance')->where('user_id',$userid)->update($baldata);
                      $finduserbalances = DB::table('userbalance')->where('user_id',$userid)->first();
                      $findusablebalance = number_format($finduserbalances->balance+$finduserbalances->winning,2, ".", "");
                    }
                if($findusablebalance >= $findentryfee){
                    $findexistornot =DB::table('joinedleauges')->where('userid',$userid)->where('challengeid',$challengeid)->where('matchkey',$matchkey)->select('teamid')->get();
                    $ab = $findexistornot->toArray();
                    $teamid=0;
                    if(!empty($ab)){
                      if($findchallenge->multi_entry!=0){
                        $this->joinleauge($userid, $challengeid, $matchkey, $findchallenge, $finduserbalance,$teamid);
                      }
                    }else{
                      $this->joinleauge($userid, $challengeid, $matchkey, $findchallenge, $finduserbalance,$teamid);
                    }
                }
              }
            }
          }
        }
        return redirect()->back()->with('success','League joined');
      }
    }
  }
  
  
  public function joinleauge($userid, $challengeid, $matchkey, $findchallenge,$finduserbalance,$teamid){
    Helpers::timezone();
    DB::beginTransaction();
    $geturl = Helpers::geturl();
    $userid =  $data['userid']=$datass['userid']= $userid;
    $matchkey = $data['matchkey'] = $datass['matchkey'] =  $matchkey;
    $challengeid =  $data['challengeid'] = $datass['challengeid'] = $challengeid;
    $teamid =  $data['teamid'] =$datass['teamid'] = $teamid;
    $getteamnumber= DB::table('joinedleauges')->where('userid',$userid)->where('challengeid',$challengeid)->where('matchkey',$matchkey)->where('teamid',$teamid)->select('teamnumbercount')->first();
    if(!empty($getteamnumber)){
      $data['teamnumbercount'] =$datass['teamnumbercount'] =$getteamnumber->teamnumbercount+1;
    }
    $Json = array();
    //Generate random code//
    $refercode = $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $refercode = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < 4; $i++){
      $refercode.= $characters[mt_rand(0, $max)];
    }
    $data['refercode'] =$datass['refercode'] = (Helpers::settings()->short_name ?? '').'-'.$refercode.'-'.time();
      //check for leauge closed or not //
      $dataused = array();
      $dataleft = array();
      $totalbonus=0;
      $findentryfee = $findchallenge->entryfee;
      /* find the current balance of users*/
      $dataleft['bonus'] = $findbonusforuser = number_format($finduserbalance->bonus,2, ".", "");
      $dataleft['winning'] = number_format($finduserbalance->winning,2, ".", "");
      $dataleft['balance'] = number_format($finduserbalance->balance,2, ".", "");
      $usedbonus = 0;
      $canusedbonus = 0;
      $totalwining = $canusedwining = number_format($finduserbalance->winning,2, ".", "");
      $totalbalance = $canusedbalance = number_format($finduserbalance->balance,2, ".", "");
      $totbalan = number_format($finduserbalance->bonus + $finduserbalance->winning + $finduserbalance->balance,2, ".", "");
      $transactiondata['cons_bonus'] = $dataused['bonus'] =$findbonusforuser;
      $reminingfee = $findentryfee;
            
      if($reminingfee>0){
       
        if($canusedbalance>=$reminingfee){
          $reminingbalance = $canusedbalance-$reminingfee;
          $dataleft['balance'] = number_format($reminingbalance,2, ".", "");
          $transactiondata['cons_amount'] =  $dataused['balance'] = $reminingfee;
          $reminingfee=0;
        }
        else{
          $dataleft['balance'] = 0;
          $reminingfee = $reminingfee-$canusedbalance;
          $transactiondata['cons_amount'] = $dataused['balance'] = $canusedbalance;
        }
      }

      if($reminingfee>0){
        if($canusedwining>=$reminingfee){
          $reminingwining = $canusedwining-$reminingfee;
          $dataleft['winning'] = number_format($reminingwining,2, ".", "");
          $transactiondata['cons_win'] = $dataused['winning'] = $reminingfee;
          $reminingfee=0;
        }
        else{
          $dataleft['winning'] = 0;
          $reminingfee = $reminingfee-$canusedwining;
          $transactiondata['cons_win'] = $dataused['winning'] = $canusedwining;
        }
      }
      
      // find transaction id//
      $tranid = (Helpers::settings()->short_name ?? '').'-'.$findchallenge->id.'-'.time();
      // to enter in joined leauges table//
      $data['transaction_id'] =$datass['transaction_id'] = (Helpers::settings()->short_name ?? '').'-JL-'.$tranid.'-'.$userid;
      //insert leauge entry//
      $getinsertid = DB::connection('mysql2')->table('joinedleauges')->insertGetId($data);
      $data['refercode'] =$datass['refercode'] = $refercode.''.$getinsertid;
      DB::connection('mysql2')->table('joinedleauges')->where('id',$getinsertid)->update($data);
      $datass['joinid']=$getinsertid;
      DB::connection('mysql2')->table('joininfo')->insert($datass);

      //entry in leauges transactions//
      $dataused['matchkey'] = $matchkey;
      $dataused['user_id'] = $userid;
      $dataused['challengeid'] = $challengeid;
      $dataused['joinid'] = $getinsertid;
      DB::connection('mysql2')->table('leaugestransactions')->insert($dataused);
      //updatewallet table//
      DB::connection('mysql2')->table('userbalance')->where('user_id',$userid)->update($dataleft);
      $findnowamount = DB::table('userbalance')->where('user_id',$userid)->first();
      //end deduct money section//
      //start entry in transaction table//
      $transactiondata['type'] = 'Contest Joining Fee';
      $transactiondata['amount'] = $findentryfee;
      $transactiondata['total_available_amt'] = $totbalan - $findentryfee;
      $transactiondata['transaction_by'] = 'wallet';
      $transactiondata['challengeid'] = $challengeid;
      $transactiondata['userid'] = $userid;
      $transactiondata['paymentstatus'] = 'confirmed';
      $transactiondata['bal_bonus_amt'] = $findnowamount->bonus;
      $transactiondata['bal_win_amt'] = $findnowamount->winning;
      $transactiondata['bal_fund_amt'] = $findnowamount->balance;
      $transactiondata['transaction_id'] = (Helpers::settings()->short_name ?? '').'-JL-'.$tranid.'-'.$userid;

      DB::connection('mysql2')->table('transactions')->insert($transactiondata);
      $nextteamcount = $findchallenge->joinedusers+1;
      $updatedata['joinedusers'] = $nextteamcount;
      if($findchallenge->contest_type=='Amount'){
        if($updatedata['joinedusers']==$findchallenge->maximum_user){
          //close challenge//
          $updatedata['status']='closed';
          if($findchallenge->is_running==1){
            //new duplicate challenge//
            $newentry = json_decode(json_encode($findchallenge), true);
            unset($newentry['id']);
            unset($newentry['joinedusers']);
            $getcid =DB::connection('mysql2')->table('matchchallenges')->insertGetId($newentry);
            $findpricecards =DB::table('matchpricecards')->where('challenge_id',$findchallenge->id)->get();
            if(!empty($findpricecards)){
              foreach($findpricecards as $pricec){
                $pdata=array();
                $pdata = json_decode(json_encode($pricec), true);
                unset($pdata['id']);
                $pdata['challenge_id'] = $getcid;
                DB::connection('mysql2')->table('matchpricecards')->insert($pdata);
              }
            }
          }
        }else{
          $updatedata['status']='opened';
          $updatedata['matchkey']=$findchallenge->matchkey;
        }
      }else{
        $updatedata['status']='opened';
      }
      $findchallenge->status =  $updatedata['status'];
      $findchallenge->joinedusers = $updatedata['joinedusers'];

      $update_challenge = (array) $findchallenge;

      unset(
        $update_challenge['created_at'],
        $update_challenge['updated_at']
      );

      $is_updated = DB::connection('mysql2')->table('matchchallenges')
                      ->where('id', $findchallenge->id)
                      ->update($update_challenge);

      if ($findchallenge->status == $updatedata['status']) {
        DB::commit();
        DB::connection('mysql2')->table('matchchallenges')->where('id',$challengeid)->update($updatedata);
        return 'success';
      }else{
        DB::rollBack();
      }
  }
  public function AutoTeam()
  {
    date_default_timezone_set('Asia/Kolkata');
    $locktime = Carbon::now();
    // echo $locktime;
    $totallaunchmatch= DB::table('listmatches')->where('launch_status','launched')->where('start_date','<=',$locktime)->where('final_status','!=','winnerdeclared')->where('status','!=','completed')->get();
    
    if(!empty($totallaunchmatch->toArray())){
      foreach ($totallaunchmatch as $value) {
        $matchkey= $value->matchkey;
        $getcurrentdate = date('Y-m-d H:i');
        $matchtimings1 = date('Y-m-d H:i', strtotime($value->start_date));
        if($getcurrentdate==$matchtimings1){
          $joinedleagues= DB::table('joinedleauges')->where('matchkey',$value->matchkey)->where('teamid','0')->get();
          if(!empty($joinedleagues->toArray())){
            foreach ($joinedleagues as $leauge) {
              $joinid= $leauge->id;
              $userid= $leauge->userid;
              $matchkey= $leauge->matchkey;
              $totalmatchplayers = DB::table('matchplayers')->where('matchplayers.playingstatus','1')->where('matchplayers.matchkey',$value->matchkey)->join('players','players.id','matchplayers.playerid')->select('matchplayers.*','players.team')->get();
              $team1= $value->team1;
                $team2= $value->team2;
                $dd=0;
              if(!empty($totalmatchplayers->toArray())){
                $totalmatchplayersa=array();
                if(!empty($totalmatchplayers->toArray())){
                  foreach ($totalmatchplayers as $matchplayer) {
                    $totalmatchplayersa[$dd]['id']= $matchplayer->id;
                    $totalmatchplayersa[$dd]['matchkey']= $matchplayer->matchkey;
                    $totalmatchplayersa[$dd]['playerid']= $matchplayer->playerid;
                    $totalmatchplayersa[$dd]['points']= $matchplayer->points;
                    $totalmatchplayersa[$dd]['role']= $matchplayer->role;
                    $totalmatchplayersa[$dd]['credit']= $matchplayer->credit;
                    $totalmatchplayersa[$dd]['name']= $matchplayer->name;
                    $totalmatchplayersa[$dd]['playingstatus']= $matchplayer->playingstatus;
                    $totalmatchplayersa[$dd]['team']= $matchplayer->team;
                    $dd++;
                  }
                }
              }else{
                 $totalmatchplayers = DB::table('matchplayers')->where('matchplayers.matchkey',$value->matchkey)->join('players','players.id','matchplayers.playerid')->select('matchplayers.*','players.team')->get();
                $totalmatchplayersa=array();
                if(!empty($totalmatchplayers->toArray())){
                  foreach ($totalmatchplayers as $matchplayer) {
                    $totalmatchplayersa[$dd]['id']= $matchplayer->id;
                    $totalmatchplayersa[$dd]['matchkey']= $matchplayer->matchkey;
                    $totalmatchplayersa[$dd]['playerid']= $matchplayer->playerid;
                    $totalmatchplayersa[$dd]['points']= $matchplayer->points;
                    $totalmatchplayersa[$dd]['role']= $matchplayer->role;
                    $totalmatchplayersa[$dd]['credit']= $matchplayer->credit;
                    $totalmatchplayersa[$dd]['name']= $matchplayer->name;
                    $totalmatchplayersa[$dd]['playingstatus']= $matchplayer->playingstatus;
                    $totalmatchplayersa[$dd]['team']= $matchplayer->team;
                    $dd++;
                  }
                }
              }
              shuffle($totalmatchplayersa);
              if(!empty($totalmatchplayersa)){
                $ii=0;$vicecap1=array();$cap1=array();
                $teamaa=0;
                $teambb=0;
                $bat1=0;
                $bowl1=0;
                $all1=0;
                $wk1=0;
                $credit1=0;
                $allbatsmen1 = array();
                $allbowler1 = array();
                $allrounder1 = array();
                $allkeeper1 = array();
                $array11 = array();
                $array21 = array();
                $finalarray1 = array();
                $ttlcredit1= 0;
                foreach ($totalmatchplayersa as $player1) {
                  if($credit1 <= 100){
                      // if($player1['credit'] <= '9'){
                      if((($player1['team'] == $team1) && ($teamaa <= '6')) || (($player1['team'] == $team2) && ($teambb <= '6'))){
                          if(($player1['role'] == 'batsman') &&($bat1 < '4')){
                              $credit1 = $credit1 + $player1['credit'];
                            if($credit1 <= 100){
                                $allbatsmen1[] = $player1['playerid']; 
                                $bat1++;
                                if($player1['team'] == $team1){
                                    $teamaa++;
                                }
                                if($player1['team'] == $team2){
                                    $teambb++;
                                }
                            }else{
                               $credit1 = $credit1 - $player1['credit']; 
                            }
                          }
                          if(($player1['role'] == 'bowler') &&($bowl1 < '4')){
                              $credit1 = $credit1 + $player1['credit'];
                              if($credit1 <= 100){
                                  $allbowler1[] = $player1['playerid']; 
                                  $bowl1++;
                                  if($player1['team'] == $team1){
                                      $teamaa++;
                                  }
                                  if($player1['team'] == $team2){
                                      $teambb++;
                                  }
                              }else{
                                 $credit1 = $credit1 - $player1['credit']; 
                              }    
                          }  
                          if(($player1['role'] == 'allrounder') &&($all1 < '2')){
                              $credit1 = $credit1 + $player1['credit'];
                              if($credit1 <= 100){
                                  $allrounder1[] = $player1['playerid']; 
                                  $all1++;
                                  if($player1['team'] == $team1){
                                      $teamaa++;
                                  }
                                  if($player1['team'] == $team2){
                                      $teambb++;
                                  }
                              }else{
                                 $credit1 = $credit1 - $player1['credit']; 
                              }
                          }
                          if(($player1['role'] == 'keeper') &&($wk1 < '1')){
                            $credit1 = $credit1 + $player1['credit'];
                            if($credit1 <= 100){
                                $allkeeper1[] = $player1['playerid']; 
                                $wk1++;
                                if($player1['team'] == $team1){
                                    $teamaa++;
                                }
                                if($player1['team'] == $team2){
                                    $teambb++;
                                }
                            }else{
                               $credit1 = $credit1 - $player1['credit']; 
                            }
                          }
                        }
                      // }
                    }
                    
                    $player_type='classic';
                    $findlastteam =DB::table('jointeam')->where('userid',$userid)->where('matchkey',$matchkey)->where('player_type',$player_type)->orderBy('teamnumber','DESC')->select('*')->get();
                        // check for duplicate//
                    if(!empty($findlastteam)){
                        foreach($findlastteam as $lteam){
                            if($lteam->captain==$cap1 && $lteam->vicecaptain==$vicecap1){
                                $allplayersget = explode(',',$lteam->players);
                                $nowplayers  = explode(',',$data1['players']);
                                $result = array_intersect($nowplayers, $allplayersget);
                                if(count($allplayersget)==count($result)){
                                  exit;
                                }
                            }
                        }
                    }
                    $findlastteam1s =DB::table('jointeam')->where('userid',$userid)->where('matchkey',$matchkey)->where('player_type',$player_type)->orderBy('teamnumber','DESC')->select('teamnumber')->first();
                    if(!empty($findlastteam1s)){
                      $finnewteamnumber = $findlastteam1s->teamnumber+1;
                      if($finnewteamnumber<11){
                        $data1['teamnumber'] = $finnewteamnumber;
                      }else{
                        break;
                      }
                    }else{
                      $data1['teamnumber'] = 1;
                    }
                    $ii++;
                }
                $array11 = array_merge($allbatsmen1,$allbowler1);
                $array21 = array_merge($allrounder1,$allkeeper1);
                $finalarray1 = array_merge($array11,$array21);
                $array111 = array_merge($allbatsmen1,$allrounder1);
                $finalarray122 = array_merge($allkeeper1,$array111);
                shuffle($finalarray1);
                shuffle($finalarray122);
                $totalmatchplayer = DB::table('matchplayers')->where('matchplayers.matchkey',$matchkey)->whereIn('playerid',$finalarray122)->select('matchplayers.playerid','matchplayers.credit')->limit(2)->orderBy('credit','Desc')->get();
                $data1['captain'] = $totalmatchplayer[0]->playerid;
                $data1['vicecaptain'] = $totalmatchplayer[1]->playerid;
                $data1['userid'] = $userid;
                $data1['matchkey'] = $matchkey;
                $data1['players'] = implode(',',$finalarray1);
                $data1['player_type'] = $player_type;
                $findlastteam =DB::table('jointeam')->where('userid',$userid)->where('matchkey',$matchkey)->where('player_type',$player_type)->orderBy('teamnumber','DESC')->select('*')->get();
                if(!empty($findlastteam)){
                    foreach($findlastteam as $lteam){
                        if($lteam->captain==$finalarray1[0] && $lteam->vicecaptain==$finalarray1[1]){
                            $allplayersget = explode(',',$lteam->players);
                            $nowplayers  = explode(',',$data1['players']);
                            $result = array_intersect($nowplayers, $allplayersget);
                            if(count($allplayersget)==count($result)){
                              exit;
                            }
                        }
                    }
                }
                if($data1['teamnumber']<11){
                    $getteamida = DB::connection('mysql2')->table('jointeam')->insertGetId($data1);
                    $joindata1['teamid'] = $getteamida;
                   DB::connection('mysql2')->table('joinedleauges')->where('id',$joinid)->update($joindata1);
                }
              }
            }
          }
          $findchallenge = DB::table('matchchallenges')->where('matchkey',$matchkey)->where('c_type','classic')->where('joinedusers','>=','1')->where('confirmed_challenge','1')->where('status','opened')->get();
          $ttlcreateteam=0;
          if(!empty($findchallenge->toArray())){
            foreach ($findchallenge as $challenge) {
              if($challenge->joinedusers<=$challenge->maximum_user){
                $matchkey=  $challenge->matchkey;
                $challengeid=  $challenge->id;
                $findentryfee = $challenge->entryfee;
                $maximumusers= $challenge->maximum_user;
                $joined_users = $challenge->joinedusers;
                $userdata= DB::table('registerusers')->where('user_status','!=','0')->where('user_status','!=','Telangana')->where('user_status','!=','Orissa')->where('user_status','!=','Assam')->select('id','team','image')->get();
                $userarray=array();
                if(!empty($userdata->toArray())) {
                  foreach($userdata as $uss){
                      $userarray[]= $uss->id;
                  }
                }
                $ttlcreateteam= $challenge->maximum_user-$challenge->joinedusers;

                  if(!empty($userarray)) {
                    for ($jk=0; $jk < $ttlcreateteam; $jk++) {
                      shuffle($userarray);
                      if(!empty($userarray[$jk])){
                        $userid= $userarray[$jk];
                        $findchallengess = DB::table('matchchallenges')->where('id',$challengeid)->first();
                        $finduserbalance = DB::table('userbalance')->where('user_id',$userid)->first();
                       
                        if($findchallengess->joinedusers <= $maximumusers){
                           // echo '<pre>';print_r($maximumusers);die;
                          if(!empty($finduserbalance)){
                            $findusablebalance = number_format($finduserbalance->balance+$finduserbalance->winning,2, ".", "");
                              if($findusablebalance < $findentryfee){
                                $baldata['balance']='100000';
                                 DB::connection('mysql2')->table('userbalance')->where('user_id',$userid)->update($baldata);
                                $finduserbalances = DB::table('userbalance')->where('user_id',$userid)->first();
                                $findusablebalance = number_format($finduserbalances->balance+$finduserbalances->winning,2, ".", "");
                              }
                              if($findusablebalance >= $findentryfee){
                                  $totalmatchplayers = DB::table('matchplayers')->where('matchplayers.playingstatus','1')->where('matchplayers.matchkey',$value->matchkey)->join('players','players.id','matchplayers.playerid')->select('matchplayers.*','players.team')->get();
                              if(!empty($totalmatchplayers->toArray())){
                                $team1= $value->team1;
                                $team2= $value->team2;
                                $d=0;
                                $totalmatchplayersa=array();
                                  if(!empty($totalmatchplayers->toArray())){
                                    foreach ($totalmatchplayers as $matchplayer) {
                                      $totalmatchplayersa[$d]['id']= $matchplayer->id;
                                      $totalmatchplayersa[$d]['matchkey']= $matchplayer->matchkey;
                                      $totalmatchplayersa[$d]['playerid']= $matchplayer->playerid;
                                      $totalmatchplayersa[$d]['points']= $matchplayer->points;
                                      $totalmatchplayersa[$d]['role']= $matchplayer->role;
                                      $totalmatchplayersa[$d]['credit']= $matchplayer->credit;
                                      $totalmatchplayersa[$d]['name']= $matchplayer->name;
                                      $totalmatchplayersa[$d]['playingstatus']= $matchplayer->playingstatus;
                                      $totalmatchplayersa[$d]['team']= $matchplayer->team;
                                      $d++;
                                    }
                                  }
                                }else{
                                  $totalmatchplayers = DB::table('matchplayers')->where('matchplayers.matchkey',$value->matchkey)->join('players','players.id','matchplayers.playerid')->select('matchplayers.*','players.team')->get();
                                  $team1= $value->team1;
                                  $team2= $value->team2;
                                  $d=0;
                                  $totalmatchplayersa=array();
                                    if(!empty($totalmatchplayers->toArray())){
                                      foreach ($totalmatchplayers as $matchplayer) {
                                        $totalmatchplayersa[$d]['id']= $matchplayer->id;
                                        $totalmatchplayersa[$d]['matchkey']= $matchplayer->matchkey;
                                        $totalmatchplayersa[$d]['playerid']= $matchplayer->playerid;
                                        $totalmatchplayersa[$d]['points']= $matchplayer->points;
                                        $totalmatchplayersa[$d]['role']= $matchplayer->role;
                                        $totalmatchplayersa[$d]['credit']= $matchplayer->credit;
                                        $totalmatchplayersa[$d]['name']= $matchplayer->name;
                                        $totalmatchplayersa[$d]['playingstatus']= $matchplayer->playingstatus;
                                        $totalmatchplayersa[$d]['team']= $matchplayer->team;
                                        $d++;
                                      }
                                    }
                                  }
                                 shuffle($totalmatchplayersa);
                                  if(!empty($totalmatchplayersa)){
                                      $i=0;$vicecap=array();$cap=array();
                                      $teama=0;
                                      $teamb=0;
                                      $bat=0;
                                      $bowl=0;
                                      $all=0;
                                      $wk=0;
                                      $credit=0;
                                      $allbatsmen = array();
                                      $allbowler = array();
                                      $allrounder = array();
                                      $allkeeper = array();
                                      $array1 = array();
                                      $array2 = array();
                                      $finalarray = array();
                                      $ttlcredit= 0;
                                      foreach ($totalmatchplayersa as $player) {
                                        if($credit <= 100){
                                          // if($player['credit'] <= '9'){
                                              if((($player['team'] == $team1) && ($teama <= '6')) || (($player['team'] == $team2) && ($teamb <= '6'))){
                                                  if(($player['role'] == 'batsman') &&($bat < '4')){
                                                      $credit = $credit + $player['credit'];
                                                      if($credit <= 100){
                                                          $allbatsmen[] = $player['playerid']; 
                                                          $bat++;
                                                          if($player['team'] == $team1){
                                                              $teama++;
                                                          }
                                                          if($player['team'] == $team2){
                                                              $teamb++;
                                                          }
                                                      }else{
                                                         $credit = $credit - $player['credit']; 
                                                      }
                                                  }
                                                  if(($player['role'] == 'bowler') &&($bowl < '4')){
                                                      $credit = $credit + $player['credit'];
                                                      if($credit <= 100){
                                                          $allbowler[] = $player['playerid']; 
                                                          $bowl++;
                                                          if($player['team'] == $team1){
                                                              $teama++;
                                                          }
                                                          if($player['team'] == $team2){
                                                              $teamb++;
                                                          }
                                                      }else{
                                                         $credit = $credit - $player['credit']; 
                                                      }    
                                                  }  
                                                  if(($player['role'] == 'allrounder') &&($all < '2')){
                                                      $credit = $credit + $player['credit'];
                                                      if($credit <= 100){
                                                          $allrounder[] = $player['playerid']; 
                                                          $all++;
                                                          if($player['team'] == $team1){
                                                              $teama++;
                                                          }
                                                          if($player['team'] == $team2){
                                                              $teamb++;
                                                          }
                                                      }else{
                                                         $credit = $credit - $player['credit']; 
                                                      }
                                                  }
                                                  if(($player['role'] == 'keeper') &&($wk < '1')){
                                                      $credit = $credit + $player['credit'];
                                                      if($credit <= 100){
                                                          $allkeeper[] = $player['playerid']; 
                                                          $wk++;
                                                          if($player['team'] == $team1){
                                                              $teama++;
                                                          }
                                                          if($player['team'] == $team2){
                                                              $teamb++;
                                                          }
                                                      }else{
                                                         $credit = $credit - $player['credit']; 
                                                      }
                                                  }
                                              }
                                          // }
                                      }
                                      $player_type='classic';
                                      $findlastteam1 =DB::table('jointeam')->where('userid',$userid)->where('matchkey',$matchkey)->where('player_type',$player_type)->orderBy('teamnumber','DESC')->select('teamnumber')->first();
                                      if(!empty($findlastteam1)){
                                        $finnewteamnumber = $findlastteam1->teamnumber+1;
                                        if($finnewteamnumber<11){
                                          $data['teamnumber'] = $finnewteamnumber;
                                        }else{
                                          break;
                                        }
                                      }else{
                                        $data['teamnumber'] = 1;
                                      }
                                      $i++;
                                    }
                                    $array1 = array_merge($allbatsmen,$allbowler);
                                    $array2 = array_merge($allrounder,$allkeeper);
                                    $array1123 = array_merge($allbatsmen,$allrounder);
                                    $finalarray223 = array_merge($array1123,$allkeeper);
                                    $finalarray = array_merge($array1,$array2);
                                    shuffle($finalarray);
                                    shuffle($finalarray223);
                                    $totalmatchplayer = DB::table('matchplayers')->where('matchplayers.matchkey',$matchkey)->whereIn('playerid',$finalarray223)->select('matchplayers.playerid','matchplayers.credit')->limit(2)->orderBy('credit','Desc')->get();
                                    $data['captain'] = $totalmatchplayer[0]->playerid;
                                    $data['vicecaptain'] = $totalmatchplayer[1]->playerid;
                                    $data['userid'] = $userid;
                                    $data['matchkey'] = $matchkey;
                                    $data['players'] = implode(',',$finalarray);
                                    $data['player_type'] = $player_type;
                                    $findlastteam =DB::table('jointeam')->where('userid',$userid)->where('matchkey',$matchkey)->where('player_type',$player_type)->orderBy('teamnumber','DESC')->select('*')->get();
                                    if(!empty($findlastteam)){
                                        foreach($findlastteam as $lteam){
                                            if($lteam->captain==$finalarray[0] && $lteam->vicecaptain==$finalarray[1]){
                                                $allplayersget = explode(',',$lteam->players);
                                                $nowplayers  = explode(',',$data['players']);
                                                $result = array_intersect($nowplayers, $allplayersget);
                                                if(count($allplayersget)==count($result)){
                                                  exit;
                                                }
                                            }
                                        }
                                    }
                                    if($data['teamnumber']<11){
                                        $getteamid = DB::connection('mysql2')->table('jointeam')->insertGetId($data);
                                        $findexistornot =DB::table('joinedleauges')->where('userid',$userid)->where('challengeid',$challengeid)->where('matchkey',$matchkey)->select('teamid')->get();
                                     $ab = $findexistornot->toArray();
                                    if(!empty($ab)){
                                        if($challenge->multi_entry!=0){
                                           $this->joinleauge($userid, $challengeid, $matchkey, $challenge, $finduserbalance,$getteamid);
                                        }
                                    }else{
                                      $this->joinleauge($userid, $challengeid, $matchkey, $challenge, $finduserbalance,$getteamid);
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
            }
          }
         
        }
      }
    }
  }
  public function xyz()
  {
    $result= DB::table('registerusers')->select('id')->get();
    $avataar= DB::table('avatar')->select('id')->get();
    foreach ($avataar as $key) {
      $data[]=$key->id;
    }
    foreach ($result as $value) {
      shuffle($data);
      $datas['user_id']= $value->id;
      $datas['avatar_id']= $data[0];
      DB::connection('mysql2')->table('user_avatar')->insert($datas);
    }
    
  }
   public function AutoSelectTeam(Request $request){
    date_default_timezone_set('Asia/Kolkata');
    $findmatchexist =DB::table('listmatches')->whereDate('start_date','<=',date('Y-m-d'))->where('launch_status','launched')->where('final_status','!=','winnerdeclared')->where('status','!=','completed')->get();

    if(!empty($findmatchexist)){
      foreach($findmatchexist as $val){
        $getcurrentdate = date('Y-m-d H:i');
        $matchtimings1 = date('Y-m-d H:i', strtotime( '+5 minutes', strtotime($val->start_date)));
        $matchtimings = date('Y-m-d H:i:s',strtotime($val->start_date));
        if($getcurrentdate==$matchtimings1){
          // 
          $match_key=$val->matchkey;
          $matchchallenges = DB::table('matchchallenges')->where('matchkey',$match_key)->select('id')->get();
          if(!empty($matchchallenges)){
            foreach($matchchallenges as $challenge){
              $resjoinedteams =  DB::table('joinedleauges')->where('registerusers.user_status','0')->where('joinedleauges.challengeid',$challenge->id)->join('registerusers','registerusers.id','=','joinedleauges.userid')->join('jointeam','jointeam.id','=','joinedleauges.teamid')->orderBy('jointeam.points','DESC')->select(DB::raw('SQL_CACHE  joinedleauges.challengeid'),'registerusers.team','registerusers.image','registerusers.email','jointeam.teamnumber','jointeam.points','jointeam.lastpoints','jointeam.player_type','jointeam.players','joinedleauges.id as jid','joinedleauges.userid','joinedleauges.teamid')->get();
              $gtlastranks = array();
              
              $pdfname = "";
              $userrank = "";
              $getcurrentrankarray = array();
              $ss = 0;
              $a = $resjoinedteams->toArray();
              if(!empty($a)){
                foreach($resjoinedteams as $pleauges){
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
              $gtcurranks = Helpers::multid_sort($getcurrentrankarray, 'points');
              if(!empty($gtcurranks)){
                $getusercurrank=array();
                $cur=0;$currsno = 0;$plus=0;
                foreach($gtcurranks as $curnk){
                  if(!in_array($curnk['points'], array_column($getusercurrank, 'points'))){ // search value in the array
                    $currsno++;
                    $currsno = $currsno+$plus;
                    $plus=0;
                  }
                  else{
                    $plus++;
                  }
                  $getusercurrank[$cur]['rank'] = $currsno;
                  $getusercurrank[$cur]['points'] = $curnk['points'];
                  $getusercurrank[$cur]['userid'] = $curnk['userid'];
                  $getusercurrank[$cur]['userjoinid'] = $curnk['userjoinid'];
                  $cur++;
                }
              }
              $rankwiseteam=array();
              $joineduser=array();
              $i=0;
              
              if(!empty($getusercurrank)){
                foreach($getusercurrank as $currank){
                  if($currank['rank']<=5){
                    $playersdata=  DB::table('joinedleauges')->where('joinedleauges.id',$currank['userjoinid'])->join('registerusers','registerusers.id','=','joinedleauges.userid')->join('jointeam','jointeam.id','=','joinedleauges.teamid')->orderBy('jointeam.points','DESC')->select('jointeam.players','jointeam.id')->first();
                    $joineduser['team'][$i]=$playersdata->players.','.$playersdata->id;
                    
                  $i++;}
                }
              }
             // 
              $usersdata=  DB::table('joinedleauges')->where('registerusers.user_status','!=','0')->where('joinedleauges.challengeid',$challenge->id)->join('registerusers','registerusers.id','=','joinedleauges.userid')->join('jointeam','jointeam.id','=','joinedleauges.teamid')->orderBy('jointeam.points','DESC')->select(DB::raw('joinedleauges.challengeid'),'registerusers.team','registerusers.image','registerusers.email','jointeam.teamnumber','jointeam.points','jointeam.lastpoints','jointeam.player_type','jointeam.captain','jointeam.vicecaptain','jointeam.players','joinedleauges.id as jid','joinedleauges.userid','joinedleauges.teamid')->get();
              if(!empty($usersdata->toArray())){
                foreach($usersdata as $joined){
                  shuffle($joineduser['team']);
                  shuffle($joineduser['team']);
                  
                  $joindatasd= $joingetplayers=explode(',', $joineduser['team'][0]);
                  // die;
                  unset($joingetplayers['11']);
                  if(!empty($joingetplayers)){
                    $teamidcap= $joindatasd['11'];
                    $caparray=array();
                    foreach ($joingetplayers as $keyid => $value) {
                      $findteamid =DB::table('jointeam')->where('id',$teamidcap)->select('captain','vicecaptain')->first();
                      if(!empty($findteamid)){
                        if($value==$findteamid['captain']){
                          $caparray['captain']=$joingetplayers[$keyid];
                          unset($joingetplayers[$keyid]);
                        }
                        if($value==$findteamid['vicecaptain']){
                          $caparray['vicecaptain']=$joingetplayers[$keyid];
                          unset($joingetplayers[$keyid]);
                        }
                      }
                    }
                  }
                  $totalmatchplayer = DB::table('matchplayers')->where('matchplayers.matchkey',$match_key)->whereIn('playerid',$joingetplayers)->select('matchplayers.credit','matchplayers.role','matchplayers.playerid',DB::raw('sum(credit) as totalcredit'))->orderBy('credit','ASC')->first();
                  $k=0;
                  $joingetplayers=array_values($joingetplayers);
                  if(!empty($totalmatchplayer)){
                    foreach ($joingetplayers as $key =>  $getlowplayer){
                      if($totalmatchplayer->playerid==$getlowplayer){
                        $ttlemainingcredit= $totalmatchplayer->totalcredit-$totalmatchplayer->credit;
                        $remainingcredit= 100-$ttlemainingcredit;
                        $notinplayerlist = DB::table('matchplayers')->where('matchplayers.matchkey',$match_key)->whereNotIn('playerid',$joingetplayers)->where('credit','<=',$remainingcredit)->where('role',$totalmatchplayer->role)->where('playingstatus','1')->select('matchplayers.credit','matchplayers.role','matchplayers.playerid')->orderBy('credit','DESC')->first();
                        if(!empty($notinplayerlist)){
                          $joingetplayers[$key]=$notinplayerlist->playerid;
                          $updatejointeam['players']= implode(',', $joingetplayers);
                          $updatejointeam['players']= $updatejointeam['players'].','.$caparray['captain'].','.$caparray['vicecaptain'];
                          $updatejointeam['captain']=$caparray['captain'];
                          $updatejointeam['vicecaptain']=$caparray['vicecaptain'];
                          DB::connection('mysql2')->table('jointeam')->where('id',$joined->teamid)->update($updatejointeam);
                        }
                      }
                      $k++;
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

  public function AdminAutoTeam($matchkey,$userid){
      $totallaunchmatch= DB::table('listmatches')->where('matchkey',$matchkey)->select('team1','team2')->first();
    if(!empty($totallaunchmatch)){
            $totalmatchplayers = DB::table('matchplayers')->where('matchplayers.playingstatus','1')->where('matchplayers.matchkey',$matchkey)->join('players','players.id','matchplayers.playerid')->select('matchplayers.*','players.team')->get();
            $team1= $totallaunchmatch->team1;
            $team2= $totallaunchmatch->team2;
            $dd=0;
      $totalmatchplayersa=array();
      if(!empty($totalmatchplayers->toArray())){
        foreach ($totalmatchplayers as $matchplayer) {
          $totalmatchplayersa[$dd]['id']= $matchplayer->id;
          $totalmatchplayersa[$dd]['matchkey']= $matchplayer->matchkey;
          $totalmatchplayersa[$dd]['playerid']= $matchplayer->playerid;
          $totalmatchplayersa[$dd]['points']= $matchplayer->points;
          $totalmatchplayersa[$dd]['role']= $matchplayer->role;
          $totalmatchplayersa[$dd]['credit']= $matchplayer->credit;
          $totalmatchplayersa[$dd]['name']= $matchplayer->name;
          $totalmatchplayersa[$dd]['playingstatus']= $matchplayer->playingstatus;
          $totalmatchplayersa[$dd]['team']= $matchplayer->team;
          $dd++;
        }
      }else{
        $totalmatchplayers = DB::table('matchplayers')->where('matchplayers.matchkey',$matchkey)->join('players','players.id','matchplayers.playerid')->select('matchplayers.*','players.team')->get();
        $totalmatchplayersa=array();
        if(!empty($totalmatchplayers->toArray())){
          foreach ($totalmatchplayers as $matchplayer) {
            $totalmatchplayersa[$dd]['id']= $matchplayer->id;
            $totalmatchplayersa[$dd]['matchkey']= $matchplayer->matchkey;
            $totalmatchplayersa[$dd]['playerid']= $matchplayer->playerid;
            $totalmatchplayersa[$dd]['points']= $matchplayer->points;
            $totalmatchplayersa[$dd]['role']= $matchplayer->role;
            $totalmatchplayersa[$dd]['credit']= $matchplayer->credit;
            $totalmatchplayersa[$dd]['name']= $matchplayer->name;
            $totalmatchplayersa[$dd]['playingstatus']= $matchplayer->playingstatus;
            $totalmatchplayersa[$dd]['team']= $matchplayer->team;
            $dd++;
          }
        }
      }
      shuffle($totalmatchplayersa);
      if(!empty($totalmatchplayersa)){
        $ii=0;$vicecap1=array();$cap1=array();
        $teamaa=0;
        $teambb=0;
        $bat1=0;
        $bowl1=0;
        $all1=0;
        $wk1=0;
        $credit1=0;
        $allbatsmen1 = array();
        $allbowler1 = array();
        $allrounder1 = array();
        $allkeeper1 = array();
        $array11 = array();
        $array21 = array();
        $finalarray1 = array();
        $ttlcredit1= 0;
        foreach ($totalmatchplayersa as $player1) {
          if($credit1 <= 100){
            if($player1['credit'] <= '9'){
            if((($player1['team'] == $team1) && ($teamaa <= '6')) || (($player1['team'] == $team2) && ($teambb <= '6'))){
              if(($player1['role'] == 'batsman') &&($bat1 < '4')){
                $credit1 = $credit1 + $player1['credit'];
              if($credit1 <= 100){
                $allbatsmen1[] = $player1['playerid']; 
                $bat1++;
                if($player1['team'] == $team1){
                  $teamaa++;
                }
                if($player1['team'] == $team2){
                  $teambb++;
                }
              }else{
                 $credit1 = $credit1 - $player1['credit']; 
              }
              }
              if(($player1['role'] == 'bowler') &&($bowl1 < '4')){
                $credit1 = $credit1 + $player1['credit'];
                if($credit1 <= 100){
                  $allbowler1[] = $player1['playerid']; 
                  $bowl1++;
                  if($player1['team'] == $team1){
                    $teamaa++;
                  }
                  if($player1['team'] == $team2){
                    $teambb++;
                  }
                }else{
                 $credit1 = $credit1 - $player1['credit']; 
                }    
              }  
              if(($player1['role'] == 'allrounder') &&($all1 < '2')){
                $credit1 = $credit1 + $player1['credit'];
                if($credit1 <= 100){
                  $allrounder1[] = $player1['playerid']; 
                  $all1++;
                  if($player1['team'] == $team1){
                    $teamaa++;
                  }
                  if($player1['team'] == $team2){
                    $teambb++;
                  }
                }else{
                 $credit1 = $credit1 - $player1['credit']; 
                }
              }
              if(($player1['role'] == 'keeper') &&($wk1 < '1')){
              $credit1 = $credit1 + $player1['credit'];
              if($credit1 <= 100){
                $allkeeper1[] = $player1['playerid']; 
                $wk1++;
                if($player1['team'] == $team1){
                  $teamaa++;
                }
                if($player1['team'] == $team2){
                  $teambb++;
                }
              }else{
                 $credit1 = $credit1 - $player1['credit']; 
              }
              }
            }
            }
          }
          
          $player_type='classic';
          $findlastteam =DB::table('jointeam')->where('userid',$userid)->where('matchkey',$matchkey)->where('player_type',$player_type)->orderBy('teamnumber','DESC')->select('*')->get();
            // check for duplicate//
          if(!empty($findlastteam)){
            foreach($findlastteam as $lteam){
              if($lteam->captain==$cap1 && $lteam->vicecaptain==$vicecap1){
                $allplayersget = explode(',',$lteam->players);
                $nowplayers  = explode(',',$data1['players']);
                $result = array_intersect($nowplayers, $allplayersget);
                if(count($allplayersget)==count($result)){
                  exit;
                }
              }
            }
          }
          $findlastteam1s =DB::table('jointeam')->where('userid',$userid)->where('matchkey',$matchkey)->where('player_type',$player_type)->orderBy('teamnumber','DESC')->select('teamnumber')->first();
          if(!empty($findlastteam1s)){
            $finnewteamnumber = $findlastteam1s->teamnumber+1;
            if($finnewteamnumber<11){
            $data1['teamnumber'] = $finnewteamnumber;
            }else{
            break;
            }
          }else{
            $data1['teamnumber'] = 1;
          }
          $ii++;
        }
        $array11 = array_merge($allbatsmen1,$allbowler1);
        $array21 = array_merge($allrounder1,$allkeeper1);
        $finalarray1 = array_merge($array11,$array21);
        shuffle($finalarray1);
        $totalmatchplayer = DB::table('matchplayers')->where('matchplayers.matchkey',$matchkey)->whereIn('playerid',$finalarray1)->select('matchplayers.playerid','matchplayers.credit')->limit(2)->orderBy('credit','Desc')->get();
        $data1['userid'] = $userid;
        $data1['matchkey'] = $matchkey;
        $data1['players'] = implode(',',$finalarray1);
        $data1['player_type'] = $player_type;
        $data1['captain'] = $totalmatchplayer[0]->playerid;
        $data1['vicecaptain'] = $totalmatchplayer[1]->playerid;
        $findlastteam =DB::table('jointeam')->where('userid',$userid)->where('matchkey',$matchkey)->where('player_type',$player_type)->orderBy('teamnumber','DESC')->select('*')->get();
        if(!empty($findlastteam)){
          foreach($findlastteam as $lteam){
            if($lteam->captain==$finalarray1[0] && $lteam->vicecaptain==$finalarray1[1]){
              $allplayersget = explode(',',$lteam->players);
              $nowplayers  = explode(',',$data1['players']);
              $result = array_intersect($nowplayers, $allplayersget);
              if(count($allplayersget)==count($result)){
                exit;
              }
            }
          }
        }
        if($data1['teamnumber']<11){
          $getteamida = DB::connection('mysql2')->table('jointeam')->insertGetId($data1);
          return $getteamida;
        }
      }
    }
  }
}