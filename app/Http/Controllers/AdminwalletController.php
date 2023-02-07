<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helpers;
use DB; 
use Session;
use Redirect;

class AdminwalletController extends Controller
{
    // to view all the player list //
    public function adminwallet(request $request) {
    	return view('admin/admin_wallet');
    }
    public function wallet_list(request $request){
		    $columns = array(
            0 => 'id',
            1 => 'username',
            2 => 'mobile',
            3 => 'email',
            4 => 'amount',
            5 => 'bonustype',
            6 => 'created_at',
            7 => 'description',
         );
         $limit = $request->input('length');
         $start = $request->input('start');
         $order = $columns[$request->input('order.0.column')];
         $dir = $request->input('order.0.dir');
         $query = DB::table('adminwallets')->join('registerusers','registerusers.id','=','adminwallets.userid')->select('adminwallets.*','registerusers.username','registerusers.email','adminwallets.userid','registerusers.mobile');
         
             $data = array();
             $totalTitles = $query->count();
            $totalFiltered = $totalTitles;
          $titles = $query->offset($start) 
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
      
         if (!empty($titles)) {

         	if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
         		$count = $totalFiltered - $start;
         	} else {
         		$count = $start + 1;
         	}

             foreach ($titles as $title) {
             $links=action('RegisteruserController@getuserdetails',$title->userid);
             $id= base64_encode(serialize($title->userid));
                $nestedData['id'] = $count;
               $nestedData['username'] = '<u><a class="text-decoration-none" href='.$links.'>'.ucwords($title->username).'</a></u>';
              	$nestedData['mobile'] = $title->mobile;
              	$nestedData['email'] = $title->email;
              	$nestedData['amount'] = $title->amount;
              	$nestedData['bonus_type'] = $title->bonustype;
              	$nestedData['created_at'] = "<span class='text-warning font-weight-bold'>".date('d-m-Y',strtotime($title->created_at))."</span><span class='text-success font-weight-bold'>".date(' h:i:s',strtotime($title->created_at))."</span>";
              	$nestedData['description'] = $title->description;
                 $data[] = $nestedData;

                 if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
                 	$count -= 1;
                 } else {
                 	$count += 1;
                 }

             }
        }
       $json_data = array(
           "draw" => intval($request->input('draw')),
           "recordsTotal" => intval($totalFiltered),
           "recordsFiltered" => intval($totalFiltered),
           "data" => $data,
       );
       echo json_encode($json_data);
	}
	public function giveadminwallet(request $request) {
    	return view('admin/addmoney_wallet');
    }
    public function searchadminwallet(request $request) {
    	$allusers = array();
    	$data=$request->all();
    	
        $query = DB::table('registerusers');
		// if(isset($_GET)){
			
			if(isset($_GET['email'])){
				$email=$_GET['email'];
				if($email!=""){
					$query->where('email', 'LIKE', '%'.$email.'%');
				}
			}
			if(isset($_GET['name'])){
				$name=$_GET['name'];
				if($name!=""){
					$query->where('team', 'LIKE', '%'.$name.'%');
				}
			}
			if(isset($_GET['userid'])){
				$id=$_GET['userid'];
				if($id!=""){
					$query->where('id', '=', $id);
				}
			}
		// }
        $allusers = $query->orderBY('id','DESC')->paginate(10);
        
		return view('admin/addmoney_wallet',compact('allusers'));
    }
    public function addmoneyinwallet(Request $request){
		if($request->isMethod('post')){
			DB::beginTransaction();
			$input = request()->all();
			 $rules = array(
                'userid' => 'required',
                'amount' => 'required',
                'master' => 'required'
            );
             $data=$request->all();
             
           $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
              return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
            }
           else{
				$getuserid = $input['userid'];
				$input['user_id']=$input['userid'];
				$amount = $input['amount'];
				$masterpassword = $input['master'];
				$pass = DB::table('users')->where('masterpassword','=',$masterpassword)->first();
				if($pass != "")
				{
					$finduserbalanace = DB::table('userbalance')->where('user_id',$getuserid)->first();
					$bonusbal = $finduserbalanace->bonus;
					$balancebal = $finduserbalanace->balance;
					$winningbal = $finduserbalanace->winning;
					unset($input['_token']);
					unset($input['master']);
					unset($input['userid']);
					if(($input['bonustype']=='addfund')){
						$balancebal+=$input['amount'];
						$transactiondata['addfund_amt'] = $input['amount'];
					}
					else if(($input['bonustype']=='bonus')){
						$bonusbal+=$input['amount'];
						$transactiondata['bonus_amt'] = $input['amount'];
					}else{
						$winningbal+=$input['amount'];
						$transactiondata['win_amt'] = $input['amount'];
					}
					$update['bonus'] = $bonusbal;
					$update['balance'] = $balancebal;
					$update['winning'] = $winningbal;
					$nowtotalbal = $bonusbal+$balancebal+$winningbal;
					unset($data['_token']);
					unset($data['master']);
					DB::connection('mysql2')->table('userbalance')->where('user_id',$getuserid)->update($update);
					DB::connection('mysql2')->table('adminwallets')->insert($data);
					//entry in transactions//
					$getlasttransactionid = DB::table('transactions')->select('id')->orderBy('id','DESC')->first();
					if(!empty($getlasttransactionid)){
						$tid = $getlasttransactionid->id+1;
					}else{
						$tid = 1;
					}
					if(($input['bonustype']=='addfund') || ($input['bonustype']=='Unutilized')){
						$transactiondata['type']= 'Add Fund Adjustments';
					}else if(($input['bonustype']=='bonus')){
						$transactiondata['type']= 'Bonus Adjustments';
					} else{
						$transactiondata['type'] = 'Winning Adjustment';
					}
					$transactiondata['amount'] = $input['amount'];
					$transactiondata['total_available_amt'] = $nowtotalbal;
					$transactiondata['transaction_id'] = (Helpers::settings()->short_name ?? '').'-'.$tid.time();
					$transactiondata['transaction_by'] = 'admin';
					//$transactiondata['type_id'] = "";
					$transactiondata['userid'] = $getuserid;
					$transactiondata['paymentstatus'] = 'confirmed';
					$transactiondata['bal_bonus_amt'] = $bonusbal;
					$transactiondata['bal_win_amt'] = $winningbal;
					$transactiondata['bal_fund_amt'] = $balancebal;
					DB::connection('mysql2')->table('transactions')->insert($transactiondata);
					$data21['userid']=$getuserid;
                    $data21['seen']=0;
                    $titleget="Money added to wallet";
                    $type="individual";
                    $msg  =  $data21['title']='₹'.$input['amount'].' has been added to your wallet successfully.';
                    DB::connection('mysql2')->table('notifications')->insert($data21);
                    $result=Helpers::sendnotification($titleget,$msg,'',$getuserid , $type);
					DB::commit();
					return Redirect::back()->with('success','Money has been successfully transferred to user wallet');
				}else{
					return Redirect::back()->with('danger','You Entered The Wrong Password');
				}
			}
		}
	}
	public function details($id){
		$allusers =Registeruser::join('userverification','userverification.userid','=','registerusers.id')->where('registerusers.id',$id)->select('registerusers.*','userverification.pan_verify','userverification.bank_verify')->first();
		return view('admin/usersdetails',compact('allusers','bank','pancard'));
    }

    public function deductmoneyinwallet(Request $request){
        if($request->isMethod('post')){
            DB::beginTransaction();
            $input = request()->all();
            $rules = array(
                  'userid' => 'required',
                  'amount' => 'required',
                  'master' => 'required'
              );
            $data=$request->all();
            $data['moneytype'] = 'deductmoney';
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return redirect()->back()
                          ->withErrors($validator)
                          ->withInput();
            }else{
                $getuserid = $input['userid'];
                $input['user_id']=$input['userid'];
                $amount = $input['amount'];
                $masterpassword = $input['master'];
                $pass = DB::table('users')->where('masterpassword','=',$masterpassword)->first();
                if($pass != ""){
                    $finduserbalanace = DB::table('userbalance')->where('user_id',$getuserid)->first();
                    $bonusbal = $finduserbalanace->bonus;
                    $balancebal = $finduserbalanace->balance;
                    $winningbal = $finduserbalanace->winning;
                    unset($input['_token']);
                    unset($input['master']);
                    unset($input['userid']);

                    if(($input['bonustype']=='addfund') && $balancebal >= $input['amount']){
                        $balancebal-=$input['amount'];
                        $transactiondata['cons_amount'] = $input['amount'];
                        $update['bonus'] = $bonusbal;
                        $update['balance'] = $balancebal;
                        $update['winning'] = $winningbal;
                        $nowtotalbal = $bonusbal+$balancebal+$winningbal;
                        unset($data['_token']);
                        unset($data['master']);
                        DB::connection('mysql2')->table('userbalance')->where('user_id',$getuserid)->update($update);
                        DB::connection('mysql2')->table('adminwallets')->insert($data);
                        //entry in transactions//
                        $getlasttransactionid = DB::table('transactions')->select('id')->orderBy('id','DESC')->first();
                        if(!empty($getlasttransactionid)){
                            $tid = $getlasttransactionid->id+1;
                        }else{
                            $tid = 1;
                        }
                        if($input['bonustype']=='addfund'){
                            $transactiondata['type']= 'Deduct Fund ';
                        }elseif($input['bonustype']=='bonus'){
                            $transactiondata['type'] = 'Deduct From Bonus ';
                        }elseif($input['bonustype']=='winning'){
                            $transactiondata['type'] = 'Deduct From Winning ';              
                        }
                        $transactiondata['amount'] = $input['amount'];
                        $transactiondata['total_available_amt'] = $nowtotalbal;
                        $transactiondata['transaction_id'] = (Helpers::settings()->short_name ?? '').'-'.$tid.time();
                        $transactiondata['transaction_by'] = 'admin';
                        $transactiondata['userid'] = $getuserid;
                        $transactiondata['paymentstatus'] = 'confirmed';
                        $transactiondata['bal_bonus_amt'] = $bonusbal;
                        $transactiondata['bal_win_amt'] = $winningbal;
                        $transactiondata['bal_fund_amt'] = $balancebal;
                        DB::connection('mysql2')->table('transactions')->insert($transactiondata);
                        $data21['userid']=$getuserid;
                        $data21['seen']=0;
                        $titleget="Money deduct from wallet";
                        $type="individual";
                        $msg  =  $data21['title']='₹'.$input['amount'].' has been deduct from your wallet successfully.';
                        DB::connection('mysql2')->table('notifications')->insert($data21);
                        $result=Helpers::sendnotification($titleget,$msg,'',$getuserid , $type);
                        
                        DB::commit();
                        return Redirect::back()->with('success','Money has been successfully deducted from user wallet');
                    }else if(($input['bonustype']=='bonus') && $bonusbal >= $input['amount']){
                        $bonusbal-=$input['amount'];
                        $transactiondata['cons_bonus'] = $input['amount'];
                        $update['bonus'] = $bonusbal;
                        $update['balance'] = $balancebal;
                        $update['winning'] = $winningbal;
                        $nowtotalbal = $bonusbal+$balancebal+$winningbal;
                        unset($data['_token']);
                        unset($data['master']);
                        DB::connection('mysql2')->table('userbalance')->where('user_id',$getuserid)->update($update);
                        DB::connection('mysql2')->table('adminwallets')->insert($data);
                        //entry in transactions//
                        $getlasttransactionid = DB::table('transactions')->select('id')->orderBy('id','DESC')->first();
                        if(!empty($getlasttransactionid)){
                            $tid = $getlasttransactionid->id+1;
                        }else{
                            $tid = 1;
                        }
                        if($input['bonustype']=='addfund'){
                            $transactiondata['type']= 'Deduct Fund ';
                        }elseif($input['bonustype']=='bonus'){
                            $transactiondata['type'] = 'Deduct From Bonus ';
                        }elseif($input['bonustype']=='winning'){
                            $transactiondata['type'] = 'Deduct From Winning ';              
                        }
                        $transactiondata['amount'] = $input['amount'];
                        $transactiondata['total_available_amt'] = $nowtotalbal;
                        $transactiondata['transaction_id'] = (Helpers::settings()->short_name ?? '').'-'.$tid.time();
                        $transactiondata['transaction_by'] = 'admin';
                        $transactiondata['userid'] = $getuserid;
                        $transactiondata['paymentstatus'] = 'confirmed';
                        $transactiondata['bal_bonus_amt'] = $bonusbal;
                        $transactiondata['bal_win_amt'] = $winningbal;
                        $transactiondata['bal_fund_amt'] = $balancebal;
                        DB::connection('mysql2')->table('transactions')->insert($transactiondata);
                        $data21['userid']=$getuserid;
                        $data21['seen']=0;
                        $titleget="Money deduct from wallet";
                        $type="individual";
                        $msg  =  $data21['title']='₹'.$input['amount'].' has been deducted from your wallet successfully.';
                        DB::connection('mysql2')->table('notifications')->insert($data21);
                        $result=Helpers::sendnotification($titleget,$msg,'',$getuserid , $type);
                        DB::commit();
                        return Redirect::back()->with('success','Money has been successfully deducted from user wallet');
                    }else if(($input['bonustype']=='winning') && $winningbal >= $input['amount']){
                          $winningbal-=$input['amount'];
                          $transactiondata['cons_win'] = $input['amount'];
                          $update['bonus'] = $bonusbal;
                        $update['balance'] = $balancebal;
                        $update['winning'] = $winningbal;
                        $nowtotalbal = $bonusbal+$balancebal+$winningbal;
                        unset($data['_token']);
                        unset($data['master']);
                        DB::connection('mysql2')->table('userbalance')->where('user_id',$getuserid)->update($update);
                        DB::connection('mysql2')->table('adminwallets')->insert($data);
                        //entry in transactions//
                        $getlasttransactionid = DB::table('transactions')->select('id')->orderBy('id','DESC')->first();
                        if(!empty($getlasttransactionid)){
                            $tid = $getlasttransactionid->id+1;
                        }else{
                            $tid = 1;
                        }
                        if($input['bonustype']=='addfund'){
                            $transactiondata['type']= 'Deduct Fund ';
                        }elseif($input['bonustype']=='bonus'){
                            $transactiondata['type'] = 'Deduct From Bonus ';
                        }elseif($input['bonustype']=='winning'){
                            $transactiondata['type'] = 'Deduct From Winning ';              
                        }
                        $transactiondata['amount'] = $input['amount'];
                        $transactiondata['total_available_amt'] = $nowtotalbal;
                        $transactiondata['transaction_id'] = (Helpers::settings()->short_name ?? '').'-'.$tid.time();
                        $transactiondata['transaction_by'] = 'admin';
                        $transactiondata['userid'] = $getuserid;
                        $transactiondata['paymentstatus'] = 'confirmed';
                        $transactiondata['bal_bonus_amt'] = $bonusbal;
                        $transactiondata['bal_win_amt'] = $winningbal;
                        $transactiondata['bal_fund_amt'] = $balancebal;
                        DB::connection('mysql2')->table('transactions')->insert($transactiondata);
                        $data21['userid']=$getuserid;
                        $data21['seen']=0;
                        $titleget="Money deduct from wallet";
                        $type="individual";
                        $msg  =  $data21['title']='₹'.$input['amount'].' has been deducted from your wallet successfully.';
                        DB::connection('mysql2')->table('notifications')->insert($data21);
                        $result=Helpers::sendnotification($titleget,$msg,'',$getuserid , $type);
                        DB::commit();
                        return Redirect::back()->with('success','Money has been successfully deducted from user wallet');
                    }else{
                        return Redirect::back()->with('danger','Not Available amount in this wallet');
                    }
                }else{
                    return Redirect::back()->with('danger','You Entered The Wrong Password');
                }
            }
        }
    }

}