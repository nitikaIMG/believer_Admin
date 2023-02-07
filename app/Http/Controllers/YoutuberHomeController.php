<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Redirect;
use Hash;

class YoutuberHomeController extends Controller
{
    /**
     * List of all Youtubers
     */
    public function home(Request $request) {

        return view('main_youtuber');

    }
    
    
    public function allrefer(){
        $id = auth()->guard('youtuber')->user()->id;
    	return view('youtuber.viewrefer',compact('id'));
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
          $titles = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
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
            $bb=action('RegisteruserController@getuserdetails',$title->rid);
            $aa ='<a href="'.$bb.'" style="text-decoration:underline;">'.$title->rid.'';
            $aa = $title->rid;
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
}
