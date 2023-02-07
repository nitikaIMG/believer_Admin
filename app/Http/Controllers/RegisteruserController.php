<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use Session;
use Redirect;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\api\UserApiController;
class RegisteruserController extends Controller
{
  
	// to see the view of register user //
    public function index(){
    	return view('user.view_users');
    }
    // for the datatable of the register user //
   public function view_users_datatable(Request $request){
       $columns = array(
        0 => 'id',
        1 => 'team',
        2 => 'email',
        3 => 'mobile',
        4 => 'email_verify',
        5 => 'id',
        6 => 'id',
        7 => 'refer_code'
      );
       $datata=$request->all();
       $limit = $request->input('length');
       $start = $request->input('start');
       $order = $columns[$request->input('order.0.column')];
       $dir = $request->input('order.0.dir');

       $query = DB::table('registerusers');
       $query=$query->where('user_status','0')->join('user_verify','user_verify.userid','=','registerusers.id')->leftjoin('special_refer','special_refer.id','=','registerusers.special_refer')->select('user_verify.mobile_verify as mobile_verify','user_verify.email_verify as email_verify','user_verify.pan_verify as pan_verify','user_verify.bank_verify as bank_verify','user_verify.userid as userid','registerusers.*','registerusers.id as rid');
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
      if(request()->has('userid')){
        $userid=request('userid');
        if($userid!=""){
          $query->where('registerusers.id', $userid);
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
      $titles = $query->offset($start)
            ->limit($limit)
  					->orderBy($order, $dir)
            ->get();
            
      if (!empty($titles)) {
        $data = array();

        if($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
        	$count = $totalFiltered - $start;
        } else {
        	$count = $start + 1;
        }
        foreach ($titles as $title) {
          $id= base64_encode(serialize($title->id));
          if($title->mobile_verify == 0 ){
            $mobile_v = "<i class='fad fa-mobile fs-23 text-secondary'></i><i class='fas fa-times-circle fs-15 text-danger position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->mobile_verify == 1 ){
            $mobile_v = "<i class='fad fa-mobile fs-23 text-secondary'></i><i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }

          if($title->email_verify == 0 ){
            $email_v="<i class='fad fa-envelope fs-20 ml-3 text-secondary'></i> <i class='fas fa-times-circle fs-15 text-danger position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->email_verify == 1 ){
            $email_v="<i class='fad fa-envelope fs-20 ml-3 text-secondary'></i> <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->email_verify == -1 ){
            $email_v="<i class='fad fa-envelope fs-20 ml-3 text-secondary'></i> <i class='fas fa-check-circle fs-20 text-danger ml-1'></i><span class='font-weight-bold text-light'>|<span>";
          }

          if($title->pan_verify == -1 ){
            $pan_v = "<i class='fad fa-id-card fs-20 ml-3 text-secondary'></i>  <i class='fas fa-question-circle fs-15 text-warning position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->pan_verify == 0 ){
            $pan_v = "<i class='fad fa-id-card fs-20 ml-3 text-secondary'></i>  <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->pan_verify == 1 ){
            $pan_v = "<i class='fad fa-id-card fs-20 ml-3 text-secondary'></i>  <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->pan_verify == 2 ){
            $pan_v = "<i class='fad fa-id-card fs-20 ml-3 text-secondary'></i>  <i class='fas fa-times-circle fs-15 text-danger position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }


          if($title->bank_verify == -1 ){
            $bank_v = "<i class='fad fa-university fs-20 ml-3 text-secondary'></i> <i class='fas fa-question-circle fs-15 text-warning position-relative top-n13px left-n6px rounded-pill bg-white'></i>";
          }elseif($title->bank_verify == 0 ){
            $bank_v = "<i class='fad fa-university fs-20 ml-3 text-secondary'></i> <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i>";
          }elseif($title->bank_verify == 1 ){
           $bank_v = "<i class='fad fa-university fs-20 ml-3 text-secondary'></i> <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i>";
         }elseif($title->bank_verify == 2 ){
          $bank_v = "<i class='fad fa-university fs-20 ml-3 text-secondary'></i> <i class='fas fa-times-circle fs-15 text-danger position-relative top-n13px left-n6px rounded-pill bg-white'></i>";
        }


        $a=""; $b="";  $c="";
        $a = action('RegisteruserController@viewtransactions',$title->id);
        $b = action('RegisteruserController@updateuserstatus',[$title->id,'deactivated']);
        $c = action('RegisteruserController@updateuserstatus',[$title->id,'activated']);
        $f = action('RegisteruserController@edituserdetails',base64_encode(serialize($title->rid)));
        $bb=action('RegisteruserController@getuserdetails',$title->rid);
        $ggg=action('RegisteruserController@emailverifymanually',$title->rid);
        $aa ='<a href="'.$bb.'" class="text-decoration-none"><u>'.$title->id.'</u></a>';
        if(strtolower($title->status)!='activated')
        {
          $d = "<a  class='dropdown-item waves-light waves-effect' href='".$c."'>Activate</a>";
        }
        else
        {

          $onclick = "delete_sweet_alert('".$b."', 'Are you sure?')";


          $d ='<a class="dropdown-item waves-light waves-effect" onclick="'.$onclick.'">Block</a>';
        }
        $you = ($title->type!='youtuber')?"<a href='".asset('/my-admin/youtuberstatus/'.$title->id)."' class='dropdown-item waves-light waves-effect' href='".$c."'>Activate Youtuber</a>":"<a href='".asset('/my-admin/youtuberstatus/'.$title->id)."' class='dropdown-item waves-light waves-effect' href='".$c."'>De-activate Youtuber</a>";
        if($title->email_verify =='0')
        {
          $gggh = "<li><a class='dropdown-item waves-light waves-effect' href='".$ggg."'>Verify Email</a></li>";
        }
        else
        {
          $gggh =" ";
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
        $nestedData['sno'] = $count;
        $nestedData['id'] = $aa;
        $nestedData['team'] = $title->team;
        $nestedData['email'] = $title->email;
        $nestedData['mobile'] = $title->mobile;
        $nestedData['verification'] =$mobile_v.$email_v.$pan_v.$bank_v;
        $nestedData['total_refers'] = '<a href="'.action('RegisteruserController@allrefer',$title->rid).'"><u>'.$countrefers.'</u></a>';
        $nestedData['refer_amount'] = $referamounts;
        $nestedData['splrefer_amount'] = $countdspl;
        $nestedData['splcode'] = $splcode;
        $nestedData['refercode'] = $title->refer_code;
        
        $nestedData['action'] = $button;
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
public function update_withdraw(Request $request){
    if ($request->isMethod('post')){
		$input =$request->all();
		$userid = $input['userid'];
		$withdrawamount = $input['withdrawamount'];
		if($withdrawamount<=299){
		    echo $withdrawamount;
		    echo 2;
		}else{
		    DB::connection('mysql2')->table('registerusers')->where('id',$userid)->update(['withdrawamount'=>$withdrawamount]);
		    echo 1;
		}
		die;
	}
}
public function allrefer($id){
    	return view('user.viewrefer',compact('id'));
    }
    // for the datatable of the register user //
   public function view_refer_datatable(Request $request){
       $columns = array(
        0 => 'id',
        1 => 'team',
        2 => 'email',
        3 => 'mobile',
        4 => 'status',
        5 => 'refer_code',
        6 => 'created_at'
      );
       $datata=$request->all();
       $limit = $request->input('length');
       $start = $request->input('start');
       $order = $columns[$request->input('order.0.column')];
       $dir = $request->input('order.0.dir');
        $idmm = $datata['shid'];
       $query = DB::table('registerusers')->where('refer_id',$idmm);
       $query=$query->join('user_verify','user_verify.userid','=','registerusers.id')->select('user_verify.mobile_verify as mobile_verify','user_verify.email_verify as email_verify','user_verify.pan_verify as pan_verify','user_verify.bank_verify as bank_verify','user_verify.userid as userid','registerusers.*','registerusers.id as rid');
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
          $query->where('registerusers.refer_code', 'LIKE', '%'.$code.'%');
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
      $titles = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
      if (!empty($titles)) {
        $data = array();
        $count = 1;
        foreach ($titles as $title) {

          $id= base64_encode(serialize($title->id));
          if($title->mobile_verify == 0 ){
            $mobile_v = "<i class='fad fa-mobile fs-23 text-secondary'></i><i class='fas fa-times-circle fs-15 text-danger position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->mobile_verify == 1 ){
            $mobile_v = "<i class='fad fa-mobile fs-23 text-secondary'></i><i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }

          if($title->email_verify == 0 ){
            $email_v="<i class='fad fa-envelope fs-20 ml-3 text-secondary'></i> <i class='fas fa-times-circle fs-15 text-danger position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->email_verify == 1 ){
            $email_v="<i class='fad fa-envelope fs-20 ml-3 text-secondary'></i> <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }

          if($title->pan_verify == -1 ){
            $pan_v = "<i class='fad fa-id-card fs-20 ml-3 text-secondary'></i>  <i class='fas fa-question-circle fs-15 text-warning position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->pan_verify == 0 ){
            $pan_v = "<i class='fad fa-id-card fs-20 ml-3 text-secondary'></i>  <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->pan_verify == 1 ){
            $pan_v = "<i class='fad fa-id-card fs-20 ml-3 text-secondary'></i>  <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }elseif($title->pan_verify == 2 ){
            $pan_v = "<i class='fad fa-id-card fs-20 ml-3 text-secondary'></i>  <i class='fas fa-times-circle fs-15 text-danger position-relative top-n13px left-n6px rounded-pill bg-white'></i><span class='font-weight-bold text-light'>|<span>";
          }


          if($title->bank_verify == -1 ){
            $bank_v = "<i class='fad fa-university fs-20 ml-3 text-secondary'></i> <i class='fas fa-question-circle fs-15 text-warning position-relative top-n13px left-n6px rounded-pill bg-white'></i>";
          }elseif($title->bank_verify == 0 ){
            $bank_v = "<i class='fad fa-university fs-20 ml-3 text-secondary'></i> <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i>";
          }elseif($title->bank_verify == 1 ){
           $bank_v = "<i class='fad fa-university fs-20 ml-3 text-secondary'></i> <i class='fas fa-check-circle fs-15 text-success position-relative top-n13px left-n6px rounded-pill bg-white'></i>";
         }elseif($title->bank_verify == 2 ){
          $bank_v = "<i class='fad fa-university fs-20 ml-3 text-secondary'></i> <i class='fas fa-times-circle fs-15 text-danger position-relative top-n13px left-n6px rounded-pill bg-white'></i>";
        }
        $bb=action('RegisteruserController@getuserdetails',$title->rid);
        $aa ='<a href="'.$bb.'" class="text-decoration-none">'.$title->rid.'';
    		$referamount = DB::table('transactions')->where('userid',$title->id)->where('type','Cash added')->select(DB::raw('sum(transactions.amount) as bonus_amts'))->first();
    		$referamounts = 0;
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
        $nestedData['refercode'] = $title->refer_code;
        $nestedData['balance'] = $referamounts;
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

    // to update the user status //
    public function updateuserstatus($id,$status){
        $input['id']=$id;
        $input['status']=$status;
        DB::connection('mysql2')->table('registerusers')->where('id',$id)->update($input);
        return Redirect::back()->with('success','User '.$status.' successfully!');
    }
    // to activate and de-activate the youtuber status //
    public function youtuberstatus($id){
      $user = DB::connection('mysql')->table('registerusers')->where('id',$id)->first();
      if(!empty($user)){
        if($user->type!='youtuber' && empty($user->type)){
          $input['type']='youtuber';
          DB::connection('mysql2')->table('registerusers')->where('id',$id)->update($input);
          
        }else{
          $input['type']=NULL;
          DB::connection('mysql2')->table('registerusers')->where('id',$id)->update($input);
          
        }
        return Redirect::back()->with('success','Status updated successfully');
      }
    }


    public function userswallet(){

        $query = DB::table('registerusers')->where('user_status','0');
          if(request()->has('name')){
            $name=request('name');
            if($name!=""){
              $query->where('username', 'LIKE', '%'.$name.'%');
            }
          }
          if(request()->has('email')){
            $email=request('email');
            if($email!=""){
              $query->where('email', 'LIKE', '%'.$email.'%');
            }
          }
          if(request()->has('userid')){
            $userid=request('userid');
            if($userid!=""){
              $query->where('registerusers.id',$userid);
            }
          }
          if(request()->has('mobile')){
            $mobile=request('mobile');
            if($mobile!=""){
              $query->where('mobile',$mobile);
            }
          }

          if(request()->has('team')){
            $team=request('team');
            if($team!=""){
              $query->where('team',$team);
            }
          }

        $titles = $query->join('userbalance','userbalance.user_id','=','registerusers.id')->orderBY('registerusers.id','DESC')->select(DB::raw('sum(userbalance.balance) as bal_sum'), DB::raw('sum(userbalance.winning) as win_sum'), DB::raw('sum(userbalance.bonus) as bonus_sum'), DB::raw('sum(userbalance.extracash) as extracash_sum'))->first();


        #igst, sgst or cgst total on bottom bar
        $win_sum = number_format($titles->win_sum, 2, '.', '');
        $bonus_sum = number_format($titles->bonus_sum, 2, '.', '');
        $bal_sum = number_format($titles->bal_sum, 2, '.', '');
        $extracash_sum = number_format($titles->extracash_sum, 2, '.', '');

        $total = $win_sum + $bonus_sum + $bal_sum + $extracash_sum;

      return view('user.userswallet', compact('win_sum', 'bonus_sum', 'bal_sum', 'extracash_sum','total'));
    }
  public function userswallet_table(Request $request){
      $columns = array(
            0 => 'registerusers.id',
            1 => 'registerusers.id',
            2 => 'registerusers.username',
            3 => 'registerusers.team',
            4 => 'registerusers.email',
            5 => 'registerusers.mobile',
            6 => 'registerusers.created_at',
            7 => 'userbalance.balance',
            8 => 'userbalance.winning',
            8 => 'userbalance.extracash',
            9 => 'userbalance.bonus',
            10 => 'created_at'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('userbalance')->join('registerusers','registerusers.id','=','userbalance.user_id');
          if(request()->has('name')){
            $name=request('name');
            if($name!=""){
              $query->where('username', 'LIKE', '%'.$name.'%');
            }
          }
          if(request()->has('email')){
            $email=request('email');
            if($email!=""){
              $query->where('email', 'LIKE', '%'.$email.'%');
            }
          }
          if(request()->has('userid')){
            $userid=request('userid');
            if($userid!=""){
              $query->where('registerusers.id',$userid);
            }
          }
          if(request()->has('mobile')){
            $mobile=request('mobile');
            if($mobile!=""){
              $query->where('mobile',$mobile);
            }
          }

          if(request()->has('team')){
            $team=request('team');
            if($team!=""){
              $query->where('team',$team);
            }
          }
          $totalTitles = $query->count();
            $totalFiltered = $totalTitles;

                $titles = $query->where('registerusers.user_status','0')
                    ->orderBY($order, $dir)
                    ->select('registerusers.username','registerusers.email','registerusers.mobile','registerusers.team','userbalance.*')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();


            if (!empty($titles)) {
                $data = array();

                if($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
                	$count = $totalFiltered - $start;
                } else {
                	$count = $start + 1;
                }
                foreach ($titles as $title) {
                    $id=base64_encode(serialize($title->id));
                    $b=action('RegisteruserController@getuserdetails',$title->id);
                    $totalwinnings = 0;
                    $total= 0;
                    $totalbalance= 0;
                    $totalbonus= 0;

                            $totalbalance+=$title->balance;
                    $totalwinnings+=$title->winning;
                    $totalbonus+=$title->bonus;
                    $total+=$title->bonus+$title->winning+$title->balance;

                    $a ='<a href="'.$b.'" class="text-decoration-none"><u>'.$title->user_id.'</u></a>';
                    $c =$title->bonus+$title->winning+$title->balance;

                    $nestedData['id'] = $count;
                    $nestedData['userid'] = $a;
                    $nestedData['username'] = ucwords($title->username);
                    $nestedData['team'] = ucwords($title->team);
                    $nestedData['email'] = $title->email;
                    $nestedData['mobile'] = $title->mobile;
                    $nestedData['date'] = "<span class='text-warning font-weight-bold'>".date('d-m-Y')."</span>";
                    $nestedData['balance'] ='Rs.'.$title->balance;
                    $nestedData['winning'] = 'Rs.'.$title->winning;
                    $nestedData['bonus'] = 'Rs.'.$title->bonus;
                    $nestedData['extracash'] = 'Rs.'.$title->extracash;
                    $nestedData['total'] = 'Rs.'.$c;
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
        
      public function getuserdetails($id)
      {
        $userdata=DB::table('registerusers')->where('id',$id)->first();
        $pancard=DB::table('pancard')->where('userid',$id)->first();
        $bank=DB::table('bank')->where('userid',$id)->first();
         $transaction=DB::table('userbalance')->where('user_id',$id)->first();
          return view('user.user_details',compact('userdata','pancard','bank','transaction'));
      }
    public function edituserdetails(Request $request,$id)
      {
      $geturl = Helpers::geturl();
      $getid = unserialize(base64_decode($id));
      $user = DB::table('registerusers')->where('id',$getid)->first();
      if($request->isMethod('post')){
        $data = $request->all();
        $rules = array(
                'email' => 'unique:registerusers,email,'.$getid,
                'mobile' => 'unique:registerusers,mobile,'.$getid,
                'team' => 'required|unique:registerusers,team,'.$getid,
            );
            $validator = Validator::make($data,$rules);
            if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
                }
        if($request->file('image'))
        {
          $image = $request->file('image');
                  $destination = public_path().'/uploads/user-profile/';
                  $filename = 'user'.time();
                  $data['image'] = Helpers::imageSingleUpload($image,$destination,$filename);
                   if($data['image']==''){
                      return redirect()->back()->with('danger','Invalid extension of file you uploaded. You can only upload image.');
                  }
                  $data['image'] = $geturl.'uploads/user-profile/'.$data['image'] ;
          // delete old image
          $oldlogo = DB::table('registerusers')->where('id', $data['id'])->first();
          $filename= $oldlogo->image;
          $filenamep = $filename;
          @unlink($filenamep);
          $updatedata['image'] = $data['image'];
        }
        if(!empty($data['dob'])){

          if(date('Y', strtotime($data['dob'])) > date('Y')) {
            return redirect()->back()->with('error', 'DOB must be before today');
          }

          $data['dob']= date('m/d/Y',strtotime($data['dob']));
        }
      unset($data['_token']);

      DB::connection('mysql2')->table('registerusers')->where('id',$data['id'])->update($data);
      return redirect()->action('RegisteruserController@edituserdetails',$id)->with('success','User Details edited successfully');
      }

       $pancard=DB::table('pancard')->where('userid',$getid)->select('id')->first();
        $bank=DB::table('bank')->where('userid',$getid)->select('id')->first();
      return view('user.edit_userdetails',compact('user','pancard','bank'));
      }

      public function downloadalluserdetails()
    {
      $output1 = "";
      $output1 .='"User Id",';
      $output1 .='"Team name",';
      $output1 .='"Email Id",';
      $output1 .='"User Name",';
      $output1 .='"Mobile no.",';
      $output1 .='"Gender",';
      $output1 .='"State",';
      $output1 .='"PAN Uploaded",';
      $output1 .='"Bank Uploaded",';
      $output1 .="\n";
      $query = DB::table('registerusers')->where('user_status','0')->join('user_verify','user_verify.userid','=','registerusers.id')->select('user_verify.mobile_verify as mobile_verify','user_verify.email_verify as email_verify','user_verify.pan_verify as pan_verify','user_verify.bank_verify as bank_verify','user_verify.userid as userid','registerusers.*','registerusers.id as rid');
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
      if(request()->has('mobile')){
        $mobile=request('mobile');
        if($mobile!=""){
          $query->where('registerusers.mobile', 'LIKE', '%'.$mobile.'%');
        }
      }
      if(request()->has('status')){
        $status=request('status');
        if($status!=""){
          $query->where('registerusers.status', 'LIKE', '%'.$status.'%');
        }
      }
      $getlist = $query->orderBY('registerusers.username','ASC')->get();
      if(!empty($getlist)){
        $count=1;
        foreach($getlist as $get){
          $output1 .='"'.$get->rid.'",';
          $output1 .='"'.$get->team.'",';
          $output1 .='"'.$get->email.'",';
          $output1 .='"'.$get->username.'",';
          $output1 .='"'.$get->mobile.'",';
          $output1 .='"'.$get->gender.'",';
          $output1 .='"'.$get->state.'",';
          if($get->pan_verify==1){
            $output1 .='"Yes",';
          }else{
            $output1 .='"No",';
          }
          if($get->bank_verify==1){
            $output1 .='"Yes",';
          }else{
            $output1 .='"No",';
          }
          if($get->pan_verify==1){
            $findpandetails = DB::table('pancard')->where('userid',$get->id)->first();
            if(!empty($findpandetails)){
              $output1 .='"'.$findpandetails->pan_number.'",';
            }else{
              $output1 .='"",';
            }
          }else{
            $output1 .='"",';
          }
          if($get->bank_verify==1){
            $findbankdetails = DB::table('bank')->where('userid',$get->id)->first();
            if(!empty($findbankdetails)){
              $output1 .='"'.$findbankdetails->accno.'",';
              $output1 .='"'.$findbankdetails->bankname.'",';
              $output1 .='"'.$findbankdetails->bankbranch.'",';
            }else{
              $output1 .='"",';
              $output1 .='"",';
              $output1 .='"",';
            }
          }else{
            $output1 .='"",';
            $output1 .='"",';
            $output1 .='"",';
          }
          $output1 .="\n";
          $count++;
        }
      }
      $filename =  "Details-alluserdetails.csv";
      header('Content-type: application/csv');
      header('Content-Disposition: attachment; filename='.$filename);
      echo $output1;
      exit;
    }
      public function downloadalluserwallet()
    {

      $output1 = "";
      $output1 .='"User Id",';
      $output1 .='"User name",';
      $output1 .='"Email Id",';
      $output1 .='"Mobile no.",';
      $output1 .='"Balance",';
      $output1 .='"Winning",';
      $output1 .='"Bonus",';
      $output1 .='"Total Bonus",';
      $output1 .='"Current Date",';
      $output1 .="\n";
      $query = DB::table('registerusers')->where('user_status','0')->join('userbalance','userbalance.user_id','=','registerusers.id')->select('userbalance.balance','userbalance.bonus','userbalance.winning','registerusers.email','registerusers.username','registerusers.mobile','registerusers.id as rid','userbalance.created_at');
      if(request()->has('name')){
        $name=request('name');
        if($name!=""){
          $query->where('registerusers.username', 'LIKE', '%'.$name.'%');
        }
      }
      if(request()->has('team')){
        $team=request('team');
        if($team!=""){
          $query->where('registerusers.team', 'LIKE', '%'.$team.'%');
        }
      }
      if(request()->has('email')){
        $email=request('email');
        if($email!=""){
          $query->where('registerusers.email', 'LIKE', '%'.$email.'%');
        }
      }
      if(request()->has('mobile')){
        $mobile=request('mobile');
        if($mobile!=""){
          $query->where('registerusers.mobile', 'LIKE', '%'.$mobile.'%');
        }
      }
      if(request()->has('userid')){
        $userid=request('userid');
        if($userid!=""){
          $query->where('registerusers.id', $userid);
        }
      }
      $getlist = $query->orderBY('registerusers.id','ASC')->get();
      if(!empty($getlist)){
        $count=1;
        foreach($getlist as $get){
          $ttlamount= $get->bonus+$get->winning+$get->balance;
          $output1 .='"'.$get->rid.'",';
          $output1 .='"'.$get->username.'",';
          $output1 .='"'.$get->email.'",';
          $output1 .='"'.$get->mobile.'",';
          $output1 .='"'.$get->balance.'",';
          $output1 .='"'.$get->winning.'",';
          $output1 .='"'.$get->bonus.'",';
          $output1 .='"'.$ttlamount.'",';
          $output1 .='"'.date('d-M-Y',strtotime($get->created_at)).'",';
          $output1 .="\n";
          $count++;
        }
      }
      $filename =  "Details-alluserwallet.csv";
      header('Content-type: application/csv');
      header('Content-Disposition: attachment; filename='.$filename);
      echo $output1;
      exit;
    }

    public function downloadalluserstransaction($uid){
      $output1 = "";
      $output1 .='"Sno.",';
      $output1 .='"User Id",';
      $output1 .='"Email Id",';
      $output1 .='"Date",';
      $output1 .='"Time",';
      $output1 .='"Add Bonus",';
      $output1 .='"Add Unutilized",';
      $output1 .='"Add Winnings",';
      $output1 .='"Total Add Fund",';
      $output1 .='"Consumed Bonus",';
      $output1 .='"Consumed Unutilized",';
      $output1 .='"Consumed Winnings",';
      $output1 .='"Total Consumed",';
      $output1 .='"Total Available Amount",';
      $output1 .='"Total Available Bonus",';
      $output1 .='"Transaction Type",';
      $output1 .="\n";
      $query =DB::table('transactions')->where('transactions.userid',$uid);
       if(request()->has('start_date')){
          $start_date = request('start_date');
          if($start_date!=""){
            $query->whereDate('transactions.created_at', '>=',date('Y-m-d',strtotime($start_date)));
          }
        }

        if(request()->has('end_date')){
          $end_date = request('end_date');
          if($end_date!=""){
            $query->whereDate('transactions.created_at', '<=',date('Y-m-d',strtotime($end_date)));
          }
        }

      
      $getlist = $query->orderBY('id','ASC')->get();
      if(!empty($getlist)){
        $count=1;
        foreach($getlist as $get){
          $aa=$get->win_amt+$get->addfund_amt+$get->bonus_amt;
          $bb=$get->cons_win+$get->cons_amount+$get->cons_bonus;
          $userrs = DB::table('registerusers')->where('id',$get->userid)->first();
          $output1 .='"'.$count.'",';
          $output1 .='"'.$get->userid.'",';
          $output1 .='"'.$userrs->email.'",';
          $output1 .='"'.date('Y-m-d',strtotime($get->created_at)).'",';
          $output1 .='"'.date('h:i:s a',strtotime($get->created_at)).'",';
          $output1 .='"'.$get->bonus_amt.'",';
          $output1 .='"'.$get->addfund_amt.'",';
          $output1 .='"'.$get->win_amt.'",';
          $output1 .='"'.$aa.'",';
          $output1 .='"'.$get->cons_bonus.'",';
          $output1 .='"'.$get->cons_amount.'",';
          $output1 .='"'.$get->cons_win.'",';
          $output1 .='"'.$bb.'",';
          $output1 .='"'.$get->total_available_amt.'",';
          $output1 .='"'.$get->bal_bonus_amt.'",';
          $output1 .='"'.$get->type.'",';
          $output1 .="\n";
          $count++;
        }
      }
      $filename =  "Details-usertransactiondetails.csv";
      header('Content-type: application/csv');
      header('Content-Disposition: attachment; filename='.$filename);
      echo $output1;
      exit;
}
public function emailverifymanually($id){
    $data['email_verify'] = '1';

    UserApiController::getbonus($id,'email');

     DB::connection('mysql2')->table('user_verify')->where('userid',$id)->update($data);
    return redirect()->back()->with('success','Email Verified Successfully');
}
// ==================================================================================================================
    public function viewtransactions($uid){
      $except = ['Referred Signup bonus'];
      $transaction_types = DB::table('transactions')->where('userid',$uid)->select('type')->distinct()->get()->filter(function($value,$key){
        if($value!='Referred Signup bonus'){
          return $value;
        }
        
      });
      return view('user.viewtransactions',compact('uid','transaction_types'));
    }

    public function viewtransactions_table($uid, Request $request){
      date_default_timezone_set("Asia/Kolkata");
      $columns = array(
            0 => 'id',
            1 => 'userid',
            2=>'',
            3 =>'created_at',
            4=>'',
            5 =>'bonus_amt',
            6=>'addfund_amt',
            7=>'win_amt',
            8=>'',
            9=>'cons_bonus',
            10=>'cons_amount',
            11=>'cons_win',
            12=>'',
            13=>'total_available_amt',
            14=>'bal_bonus_amt',
            15=>'type'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $arr_cr = ['Bank verification bank bonus','Email bonus','Mobile bonus','Cash added','Refund amount','Challenge Winning Amount','Refund','Pan verification pan bonus','special bonus','Youtuber Bonus','Referred Signup bonus','Winning Adjustment','Add Fund Adjustments','Refer Bonus','withdraw cancel','Amount Withdraw Failed'];
        $arr_db = ['Amount Withdraw','Contest Joining Fee'];
        $query =DB::table('transactions')->where('transactions.userid',$uid);
        if(request()->has('start_date')){
          $start_date = request('start_date');
          if($start_date!=""){
            $query->whereDate('transactions.created_at', '>=',date('Y-m-d',strtotime($start_date)));
          }
        }

        if(request()->has('end_date')){
          $end_date = request('end_date');
          if($end_date!=""){
            $query->whereDate('transactions.created_at', '<=',date('Y-m-d',strtotime($end_date)));
          }
        }
        if(request()->has('type')){
          $transaction_id = request('type');
          if($transaction_id!=""){
            $query->where('transactions.type','LIKE', '%'.$transaction_id.'%');
          }
        }
        if(request()->has('cid')){
          $cid = request('cid');
          if($cid!=""){
            $query->where('transactions.challengeid',$cid);
          }
        }

        $d = array('uid'   => $uid);
        $data = [];
         $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        $titles = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)->get();
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {
               $userrs = DB::table('registerusers')->where('id',$title->userid)->first();
               $bb=action('RegisteruserController@getuserdetails',$userrs->id);
               $aa ='<a href="'.$bb.'" class="text-decoration-none">'.$userrs->id.'';
                if(!empty($userrs)){
                    $email = $userrs->email;
                }
                if($title->type == 'Cash added'){
                  $hj = 'Cashfree';
              }else{
                $hj =  $title->type;
              }
              $nestedData['id'] = $count;
              $nestedData['userid'] = $aa;
              $nestedData['date'] ="<span class='text-warning font-weight-bold'>".date('d-m-Y',strtotime($title->created_at))."</span><span class='text-success font-weight-bold'>".date(' h:i:s',strtotime($title->created_at))."</span>";
              $nestedData['amt'] =round($title->amount,2);
              $nestedData['ttype'] = (in_array($title->type,$arr_cr)?'Credit':'Debit');
              $nestedData['treason'] = $hj;
              $nestedData['bonusA'] = round($title->bal_bonus_amt,2);
              $nestedData['bonusC'] = round($title->bonus_amt,2);
              $nestedData['bonusD'] = round($title->cons_bonus,2);
              $nestedData['winningA'] = round($title->bal_win_amt,2);
              $nestedData['winningC'] = round($title->win_amt,2);
              $nestedData['winningD'] = round($title->cons_win,2);
              $nestedData['balanceA'] = round($title->bal_fund_amt,2);
              $nestedData['balanceC'] = round($title->addfund_amt,2);
              $nestedData['balanceD'] = round($title->cons_amount,2);
              $nestedData['extracashA'] = round($title->bal_extracash_amt,2);
              $nestedData['extracashC'] = round($title->extracash_amt,2);
              $nestedData['extracashD'] = round($title->cons_extracash,2);
              $nestedData['total'] = round($title->total_available_amt,2);
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
}
