<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Helpers\Htmlhelpersemail;
use App\Http\Controllers\api\UserApiController;
use CfPayout;
use Config;
use DB;
use Illuminate\Http\Request;
use Redirect;

class VerificationController extends Controller
{

    // Pan verification section
    public function verifypan()
    {
        return view('verify.viewpandetails');
    }

    public function verifypan_table(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'username',
            2 => 'pan_name',
            3 => 'pan_number',
            4 => 'pan_dob',
            5 => 'image',
            6 => 'status',
            7 => 'created_at',
            8 => 'updated_at',
            9 => 'updated_at',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('pancard')->join('registerusers', 'registerusers.id', '=', 'pancard.userid')->where('user_verify.email_verify', '1')->where('user_verify.mobile_verify', '1')->join('user_verify', 'user_verify.userid', '=', 'pancard.userid')->select('registerusers.email', 'registerusers.mobile', 'registerusers.username', 'registerusers.id as rid', 'registerusers.image as imagess', 'pancard.*', 'user_verify.userid', 'user_verify.email_verify', 'user_verify.mobile_verify', 'user_verify.pan_verify', 'user_verify.bank_verify', 'pancard.image as pimage');
        if (request()->has('name')) {
            $name = request('name');
            if ($name != "") {
                $query->where('registerusers.username', 'LIKE', '%' . $name . '%');
            }
        }
        if (request()->has('email')) {
            $email = request('email');
            if ($email != "") {
                $query->where('registerusers.email', 'LIKE', '%' . $email . '%');
            }
        }
        if (request()->has('status')) {
            $status = request('status');
            if ($status != "") {
                $query->where('pancard.status', $status);
            }
        }
        if (request()->has('mobile')) {
            $mobile = request('mobile');
            if ($mobile != "") {
                $query->where('registerusers.mobile', $mobile);
            }
        }
        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        $titles = $query->orderBY('pancard.updated_at', 'DESC')->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        if (!empty($titles)) {
            $data = array();
            if (request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
                $count = $totalFiltered - $start;
            } else {
                $count = $start + 1;
            }
            foreach ($titles as $title) {
                $bb = action('RegisteruserController@getuserdetails', $title->rid);
                $a = '<a href="' . $bb . '" style="text-decoration:underline;">' . $title->rid . '';
                $b = action('VerificationController@viewpandetails', $title->id);
                // $ext = pathinfo($title->image, PATHINFO_EXTENSION);
                $imagelogo = asset('public/uploads/pancard/' . $title->image);

                if (file_exists(public_path('uploads/pancard/' . $title->image))) {
                    $imagelogo = asset('public/uploads/pancard/' . $title->image);
                } else {
                    $imagelogo = asset('public/'.Helpers::settings()->logo ?? '');
                }

                $default = asset('public/'. Helpers::settings()->logo ?? '');
                $default_img = "this.src='" . $default . "'";

                // if($ext=='pdf'){
                // $ex = '<i class="fa fa-file-pdf-o" style="color:red;font-size:30px;"></i>';
                // }else{
                $ex = '<a href="' . $imagelogo . '" target="_blank"><img src="' . $imagelogo . '" class="w-40px h-40px shadow rounded"  onerror="' . $default_img . '"></a>';
                // }
                if ($title->status == '1') {
                    $sta = 'Verified';
                } elseif ($title->status == '0') {
                    $sta = 'Pending';
                } elseif ($title->status == '2') {
                    $sta = 'Cancel';
                }

                $nestedData['sno'] = $count;
                $nestedData['id'] = $a;
                $nestedData['username'] = ucwords($title->username);
                $nestedData['email'] = $title->email;
                $nestedData['mobile'] = $title->mobile;
                $nestedData['pan_dob'] = date('d-m-Y', strtotime($title->pan_dob));
                $nestedData['pan_number'] = $title->pan_number;
                $nestedData['image'] = $ex;
                $nestedData['status'] = $sta;
                $nestedData['comment'] = $title->comment;
                $nestedData['upload'] = $title->updated_at;
                $nestedData['action'] = '<a href="' . $b . '" class="btn btn-sm btn-primary w-35px h-35px text-uppercase text-nowrap"><i class="fas fa-eye"></i></a>';

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

    public function viewpandetails($id)
    {
        $pancarddetails = DB::table('pancard')->leftjoin('registerusers', 'pancard.userid', '=', 'registerusers.id')->select('pancard.*', 'registerusers.email', 'registerusers.username')->where('pancard.id', $id)->first();
        $region = DB::table('region')->get();
        if (!empty($pancarddetails)) {
            return view('verify.viewfullpandetails', compact('pancarddetails', 'region'));
        } else {
            return Redirect::back()->with('danger', 'The detail of this user is not present.');
        }
    }

    public function editpandetails($id)
    {
        $editpandetails = DB::table('pancard')->where('id', $id)->first();
        return view('verify.editpandetails', compact('editpandetails'));
    }

    public function updatepantatus(Request $request)
    {
        $status = "";
        if ($request->isMethod('post')) {
            $input = $request->all();
            $id = $input['id'];
            $status = $input['status'];
            if (isset($input['pan_name'])) {
                $input['pan_name'] = strtoupper($input['pan_name']);
            }
            if (isset($input['pan_number'])) {
                $input['pan_number'] = strtoupper($input['pan_number']);
            }
            if (isset($input['pan_dob'])) {
                $input['pan_dob'] = date('Y-m-d', strtotime($input['pan_dob']));
            }
        }
        if ($status != "") {
            if ($status == 1) {
                $st = 'Verified';
            }
        }
        unset($input['_token']);
        $req['pan_verify'] = $status;
        DB::connection('mysql2')->table('pancard')->where('id', $id)->update($input);
        $findlastow = DB::table('pancard')->where('id', $id)->first();
        if (!empty($findlastow)) {
            $userid = $findlastow->userid;
        }
        $finduserbonus = DB::table('user_verify')->leftjoin('registerusers', 'user_verify.userid', '=', 'registerusers.id')->select('user_verify.*', 'registerusers.email', 'registerusers.team')->where('user_verify.userid', $userid)->select('*')->first();
        if ($status == 1) {
            $st = 'Verified';
            $datamessage['content'] = '';
            $datamessage['email'] = $finduserbonus->email;
            $datamessage['subject'] = 'Believer11 -  PAN Verification Successful';
            $datamessage['content'] .= '<p style="padding-left: 23px;"><strong>Hello ' . ucwords($finduserbonus->team) . ' </strong></p>';
            $datamessage['content'] .= '<p style="padding-left: 23px;float:center;color:green;">Your PAN Card request is approved successfully!!</p>';
            $datamessage['content'] .= '<p></p>';

            // $content=Helpers::Mailbody1($datamessage['content'],$datamessage['email']);
            $content = Htmlhelpersemail::panapprove_email($finduserbonus->team);
            Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $content);
            // Helpers::mailSmtpSend($datamessage);
        } else if ($status == 2) {
            $st = 'rejected';
            $datamessage['email'] = $finduserbonus->email;
            $datamessage['subject'] = 'Believer11   -  PAN Verification Failed';
            $datamessage['content'] = '<p><strong>Hello ' . ucwords($finduserbonus->team) . ' </strong></p>';
            $datamessage['content'] .= '<p>PAN Card Documents uploaded by you have not been approved</p>';
            $datamessage['content'] .= '<p>Reason: <strong>' . $input['comment'] . '</strong></p>';
            $datamessage['content'] .= '<p></p>';
            $datamessage['content'] .= '<p>You need not worry. You can edit and re-submit your documents for verification again.</p>';
            // $content=Helpers::Mailbody1($datamessage['content'],$datamessage['email']);
            $content = Htmlhelpersemail::panreject_email($finduserbonus->team);
            // echo "<pre>";print_r($content);die;
            // Helpers::mailSmtpSend($datamessage['subject'],$datamessage['email'],$content);
            Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $content);
        }
        if ($st == 'Verified') {
            $reqs['username'] = $findlastow->pan_name;
            $reqs['dob'] = date('Y-m-d', strtotime($findlastow->pan_dob));
            $req['pan_verify'] = 1;

            if ($finduserbonus->panbonus == 0) {
                UserApiController::getbonus($userid, 'pan');
            }
            DB::connection('mysql2')->table('user_verify')->where('userid', $userid)->update($req);
            DB::connection('mysql2')->table('registerusers')->where('id', $userid)->update($reqs);
        } else if ($st == 'rejected') {
            $reqsss['pan_verify'] = '2';
            DB::connection('mysql2')->table('user_verify')->where('userid', $userid)->update($reqsss);
        }
        $notificationdata['userid'] = $userid;
        $notificationdata['title'] = 'Your PAN card verification request is ' . $st;
        DB::connection('mysql2')->table('notifications')->insert($notificationdata);
        //push notifications//
        $titleget = 'Pan Request!';
        $msg = $data21['title'] = 'Your pan request is' . $st;
        $result = Helpers::sendnotification($titleget, $msg, '', $userid);
        return Redirect::back()->with('success', 'PAN Card Request is ' . $st . '!');
    }

    // bank detail section

    public function verifybankaccount()
    {
        return view('verify.verifybankdetails');
    }
    public function verifybankaccount_table(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'userid',
            2 => 'accno',
            3 => 'ifsc',
            4 => 'bankname',
            5 => 'bankbranch',
            6 => 'state',
            7 => 'type',
            8 => 'image',
            9 => 'comment',
            10 => 'status',
            11 => 'created_at',
            12 => 'updated_at',
            13 => 'updated_at',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('bank')->join('registerusers', 'registerusers.id', '=', 'bank.userid')->join('user_verify', 'user_verify.userid', '=', 'bank.userid')->where('user_verify.pan_verify', '1')->where('user_verify.mobile_verify', '1')->where('user_verify.email_verify', '1')->select('registerusers.email', 'registerusers.username', 'registerusers.mobile', 'user_verify.mobile_verify', 'user_verify.email_verify', 'user_verify.pan_verify', 'bank.*', 'registerusers.id as rid', 'bank.status');
        if (request()->has('name')) {
            $name = request('name');
            if ($name != "") {
                $query->where('registerusers.username', 'LIKE', '%' . $name . '%');
            }
        }
        if (request()->has('email')) {
            $email = request('email');
            if ($email != "") {
                $query->where('registerusers.email', 'LIKE', '%' . $email . '%');
            }
        }
        if (request()->has('status')) {
            $status = request('status');
            if ($status != "") {
                $query->where('bank.status', $status);
            }
        }
        if (request()->has('mobile')) {
            $mobile = request('mobile');
            if ($mobile != "") {
                $query->where('registerusers.mobile', $mobile);
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

            if (request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
                $count = $totalFiltered - $start;
            } else {
                $count = $start + 1;
            }
            foreach ($titles as $title) {
                $default = asset('public/'.Helpers::settings()->logo ?? '');
                $default_img = "this.src='" . $default . "'";

                $bb = action('RegisteruserController@getuserdetails', $title->rid);
                $a = '<a href="' . $bb . '" style="text-decoration:underline;">' . $title->rid . '</a>';
                $id = $title->id;
                $b = action('VerificationController@viewbankdetails', $id);
                $ext = pathinfo($title->image, PATHINFO_EXTENSION);
                $aa = asset('public/uploads/bank/' . $title->image);
                if ($ext == 'pdf') {
                    $ex = '<i class="fa fa-file-pdf-o" style="color:red;font-size:30px;"></i>';
                } else {
                    $ex = '<a href="' . $aa . '" target="_blank"><img class="w-40px h-40px shadow rounded" src="' . $aa . '" onerror="' . $default_img . '"></a>';
                }
                if ($title->status == '1') {
                    $sta = 'Verified';
                } elseif ($title->status == '0') {
                    $sta = 'Pending';
                } elseif ($title->status == '2') {
                    $sta = 'Cancel';
                }
                $nestedData['sno'] = $count;
                $nestedData['id'] = $a;
                $nestedData['username'] = ucwords($title->accountholder);

                $nestedData['email'] = $title->email;
                $nestedData['mobile'] = $title->mobile;
                // $nestedData['upi_id'] = $title->upi_id;
                $nestedData['accno'] = $title->accno;
                $nestedData['ifsc'] = $title->ifsc;
                $nestedData['bankname'] = $title->bankname;
                $nestedData['bankbranch'] = $title->bankbranch;
                $nestedData['state'] = $title->state;
                $nestedData['type'] = $title->type;
                $nestedData['image'] = $ex;
                $nestedData['status'] = $sta;
                $nestedData['upload'] = $title->updated_at;
                $nestedData['action'] = '<a href="' . $b . '" class="btn btn-sm btn-primary w-35px h-35px text-uppercase text-nowrap"><i class="fas fa-eye"></i></a>';

                $data[] = $nestedData;

                if (request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
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
        // dd($titles->toArray());
    }

    public function viewbankdetails($id)
    {
        $pancarddetails = DB::table('bank')->leftjoin('registerusers', 'registerusers.id', '=', 'bank.userid')->leftjoin('user_verify', 'registerusers.id', '=', 'user_verify.userid')->select('bank.*', 'registerusers.username', 'registerusers.email', 'user_verify.bank_verify')->where('bank.id', $id)->first();
        $region = DB::table('region')->get();
        if (!empty($pancarddetails)) {
            return view('verify.viewbankdetails', compact('pancarddetails', 'region'));
        } else {
            return Redirect::back();
        }
    }

    public function editbankdetails(Request $request, $id)
    {
        $editpandetails = DB::table('bank')->where('id', $id)->first();
        return view('verify.editbankdetails', compact('editpandetails'));
    }

    public function updatebanktatus(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            $id = $input['id'];
            $status = $input['status'];
            unset($input['_token']);

            $req['bank_verify'] = $status;
            if (isset($input['accno'])) {
                $input['accno'] = strtoupper($input['accno']);
            }
            if (isset($input['accountholder'])) {
                $input['accountholder'] = strtoupper($input['accountholder']);
            }
            if (isset($input['ifsc'])) {
                $input['ifsc'] = strtoupper($input['ifsc']);
            }
            if (isset($input['bankname'])) {
                $input['bankname'] = strtoupper($input['bankname']);
            }
            if (isset($input['bankbranch'])) {
                $input['bankbranch'] = strtoupper($input['bankbranch']);
            }
            if (isset($input['state'])) {
                $input['state'] = $input['state'];
            }
            DB::connection('mysql2')->table('bank')->where('id', $id)->update($input);
            $findlastow = DB::table('bank')->where('id', $id)->first();
            if (!empty($findlastow)) {
                $userid = $findlastow->userid;
            }
            $finduserbonus = DB::table('user_verify')->leftjoin('registerusers', 'registerusers.id', '=', 'user_verify.userid')->select('user_verify.bankbonus', 'user_verify.userid', 'registerusers.team', 'registerusers.email')->where('user_verify.userid', $userid)->first();
            $st = '';
            if ($status == 1) {
                $st = 'Verified';
                $datamessage['content'] = '';
                $datamessage['email'] = $finduserbonus->email;
                $datamessage['subject'] = 'Believer11   -  Bank Account Verification Successful';
                $datamessage['content'] .= '<p style="padding-left: 23px;"><strong>Hello ' . ucwords($finduserbonus->team) . ' </strong></p>';
                $datamessage['content'] .= '<p style="padding-left: 23px;float:center;color:green;">Your PAN Card request is approved successfully!!</p>';
                $datamessage['content'] .= '<p></p>';

                $content = Htmlhelpersemail::bankapprove_email($finduserbonus->team);
                Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $content);
            }
            if ($status == 2) {
                $st = 'rejected';
                $datamessage['email'] = $finduserbonus->email;
                $datamessage['subject'] = 'Believer11   -  Bank Account Verification Failed';
                $datamessage['content'] = '<p><strong>Hello ' . ucwords($finduserbonus->team) . ' </strong></p>';
                $datamessage['content'] .= '<p>Bank Card Documents uploaded by you have not been approved</p>';
                $datamessage['content'] .= '<p>Reason: <strong>' . $input['comment'] . '</strong></p>';
                $datamessage['content'] .= '<p></p>';
                $datamessage['content'] .= '<p>You need not worry. You can edit and re-submit your documents for verification again.</p>';
                $datamessage['content'] .= '<p><a href="' . Config::get('constants.FRONT_PROJECT_URL') . 'verifyaccount" style="padding: 10px 20px;background: rgb(198,29,35);display: inline-block;text-transform: uppercase;font-size: 13px;font-weight: 700;border: 2px solid rgb(182,20,26);color: rgb(255,255,255);">Edit Now</a></p>';
                $datamessage['content'] .= '<p>Do it right this time & enjoy 1-click withdrawals forever!!</p>';
                // $content=Helpers::Mailbody1($datamessage['content'], $datamessage['email']);
                $content = Htmlhelpersemail::bankrejected_email($finduserbonus->team);
                Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $content);
                // Helpers::mailSmtpSend($datamessage);
            }
            if ($st == 'Verified') {
                $req['bankbonus'] = 1;
                $reqs['state'] = $findlastow->state;
                if ($finduserbonus->bankbonus == 0) {
                    UserApiController::getbonus($userid, 'bank');
                    DB::connection('mysql2')->table('registerusers')->where('id', $userid)->update($reqs);
                }
                $result = DB::table('registerusers')->where('id', $userid)->select('*')->first();
                // dd($result)
                // if(!empty($result)){
                //   if($result->refer_id!=0 && $finduserbonus->referbonus==0){
                //       $userdata = DB::table('userbalance')->where('user_id',$result->refer_id)->first();
                //       $referbons = DB::table('general_tabs')->where('type','=','refer_bonus')->first();
                //       if(!empty($userdata)){
                //           $datainseert['user_id'] = $result->refer_id;
                //           $datainseert['bonus'] = $userdata->bonus+$referbons->amount;
                //           DB::connection('mysql2')->table('userbalance')->where('user_id',$result->refer_id)->update($datainseert);
                //           $transactionsdata['transaction_id'] = Helpers::settings()->short_name ?? ''.'-EBONUS-'.rand(1000,9999);
                //           $transactionsdata['type'] = 'Referred Signup bonus';
                //           /* entry in refered table */
                //           $bonus_refered['userid'] = $result->id;
                //           $bonus_refered['fromid'] =$result->refer_id;
                //           $bonus_refered['amount'] = $referbons->amount;
                //           $bonus_refered['type'] = 'Refer signup bonus';
                //           $bonus_refered['txnid'] = $transactionsdata['transaction_id'];
                //           DB::connection('mysql2')->table('bonus_refered')->insert($bonus_refered);
                //           $getbonusrefer['referbonus'] = 1;
                //           DB::connection('mysql2')->table('user_verify')->where('userid',$result->refer_id)->update($getbonusrefer);
                //       }
                //       $findlastow = DB::table('userbalance')->where('user_id',$result->refer_id)->first();
                //       if(!empty($findlastow)){
                //           $total_available_amt = $findlastow->balance+$findlastow->winning+$findlastow->bonus;
                //           $bal_fund_amt = $findlastow->balance;
                //           $bal_win_amt = $findlastow->winning;
                //           $bal_bonus_amt = $findlastow->bonus;
                //       }
                //       $transactionsdata['transaction_by'] = Helpers::settings()->project_name ?? '';
                //       $transactionsdata['amount'] = $referbons->amount;
                //       $transactionsdata['userid'] = $result->refer_id;
                //       $transactionsdata['bonus_amt'] = $referbons->amount;
                //       $transactionsdata['paymentstatus'] = 'confirmed';
                //       $transactionsdata['bal_fund_amt'] = $bal_fund_amt;
                //       $transactionsdata['bal_win_amt'] = $bal_win_amt;
                //       $transactionsdata['bal_bonus_amt'] = $bal_bonus_amt;
                //       $transactionsdata['total_available_amt'] = $total_available_amt;
                //       DB::connection('mysql2')->table('transactions')->insert($transactionsdata);
                //       $data21['userid']=$result->refer_id;
                //       $data21['seen']=0;
                //       $titleget="Congratulations!";
                //       $type1="individual";
                //       $msg  =  $data21['title']='You have got ₹ ' .$referbons->amount.' for referring your friend on '.Helpers::settings()->project_name ?? ''.' app.';
                //       DB::connection('mysql2')->table('notifications')->insert($data21);
                //      Helpers::sendnotification($titleget,$msg,'',$result->refer_id);
                //   }
                // }
            }
            DB::connection('mysql2')->table('user_verify')->where('userid', $userid)->update($req);
            $notificationdata['userid'] = $userid;
            $notificationdata['seen'] = '0';
            $notificationdata['title'] = 'Your Bank documents request is ' . $st;
            DB::connection('mysql2')->table('notifications')->insert($notificationdata);
            //push notifications//
            $titleget = 'Bank Documents Verification Request is ' . $st;
            Helpers::sendnotification($titleget, $notificationdata['title'], '', $userid);
            return Redirect::back()->with('success', 'Bank request has been ' . $st . '!');
        }
    }
    // withdrawal request section
    public function withdraw_amount()
    {
        return view('verify.withdraw_amount');
    }
    public function withdrawl_amount_table(Request $request)
    {
        $columns = array(
            0 => 'registerusers.id',
            1 => 'accno',
            2 => 'accno',
            3 => 'withdraw_amount',
            4 => 'username',
            5 => 'withdraw_request_id',
            6 => 'ifsc',
            7 => 'bankname',
            8 => 'bankbranch',
            9 => 'email',
            10 => 'mobile',
            11 => 'withdraw_request',
            12 => 'created_at',
            13 => 'mobile',
            14 => 'transfer_id',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query = DB::table('withdraw')->join('registerusers', 'registerusers.id', '=', 'withdraw.user_id')->join('bank', 'bank.userid', '=', 'registerusers.id')->join('pancard', 'pancard.userid', '=', 'registerusers.id')->select('registerusers.id as reg_id', 'bank.id as bank_id', 'pancard.id as pan_id', 'withdraw.*', 'withdraw.id as withdraw_id', 'withdraw.status as withdraw_status', 'withdraw.amount as withdraw_amount', 'withdraw.created_at as withdraw_request', 'registerusers.activation_status as reg_status', 'bank.status as bank_status', 'pancard.status as pan_status', 'registerusers.username as username', 'bank.ifsc as ifsc', 'bank.bankname as bankname', 'bank.bankbranch as bankbranch', 'bank.accno as ano', 'registerusers.email as email', 'registerusers.mobile as mobile', 'registerusers.id as rid', 'registerusers.email', 'registerusers.mobile');

        if (request()->has('start_date')) {
            $start_date = request('start_date');
            if ($start_date != "") {
                $start_date = date('Y-m-d H:i:s', strtotime('-30 minutes', strtotime(request('start_date'))));
                $query->whereDate('withdraw.created_at', '>=', date('Y-m-d', strtotime($start_date)));
            }
        }
        if (request()->has('end_date')) {
            $end_date = request('end_date');
            if ($end_date != "") {
                $query->whereDate('withdraw.created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }
        }
        if (request()->has('email')) {
            $email = request('email');
            if ($email != "") {
                $dataa = $query->where('registerusers.email', 'LIKE', '%' . $email . '%')->get();
            }
        }
        if (request()->has('mobile')) {
            $mobile = request('mobile');

            if ($mobile != "") {
                $query->where('registerusers.mobile', 'LIKE', '%' . $mobile . '%');
            }
        }
        if (request()->has('userid')) {
            $userid = request('userid');

            if ($userid != "") {
                $query->where('registerusers.id', 'LIKE', '%' . $userid . '%');
            }
        }
        $status = request('status');
        if (request()->has('status')) {
            $status = request('status');
            if ($status != "") {
                $query->where('withdraw.status', $status);
                if ($status == 0) {
                    $query->orderBY('withdraw.created_at', 'DESC');
                } else {
                    $query->orderBY('withdraw.approved_date', 'DESC');
                }
            }
        }
        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        $titles = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        //echo '<pre>';print_r($titles);die;
        if (!empty($titles)) {
            $data = array();
            if (request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
                $count = $totalFiltered - $start;
            } else {
                $count = $start + 1;
            }
            foreach ($titles as $title) {
                {
                    $bb = action('RegisteruserController@getuserdetails', $title->rid);
                    $aa = '<a href="' . $bb . '" style="text-decoration:underline;">' . $title->rid . '';
                    $b = action('VerificationController@details', $title->reg_id);
                    $a = '<a href="' . $b . '">' . $title->reg_id . '';
                    $e = action('VerificationController@approve');
                    $f = action('VerificationController@remark');
                    if ($title->approved_date != null) {
                        $c = date('d-M-Y', strtotime($title->approved_date));
                    } else {
                        $c = '';
                    }

                    if ($title->withdraw_status == 0) {
                        $d = '
              <div class="row comment-modal-views comment-modal' . $title->withdraw_id . ' position-fixed top-0 left-0 right-0 bottom-0 w-100 h-100 m-auto z-index-10 align-items-center justify-content-center' . $title->withdraw_id . '" style="display: none;">
              <form class="mt-4 align-self-start form-horizontal form-label-left w-100 m-auto bg-white p-4" action="' . $e . '" method="post">' . csrf_field() . '
              <input type="text" class="form-control mb-4" name="comment" placeholder="Enter your comment" required>
              <input type="hidden" name="id" value="' . $title->withdraw_id . '">
              <input type="hidden" name="uid" value="' . $title->reg_id . '">
              <input type="hidden" name="amount" value="' . $title->withdraw_amount . '">
              <input type="submit" value="Approve" name="approve" maxlength="20" class="btn btn-sm btn-info">
              <input type="submit" value="Cancel" name="cancel" maxlength="20" class="btn btn-sm btn-danger">
              <input type="button" onclick="show_hide_comment(' . $title->withdraw_id . ')" value="Close" name="cancel" maxlength="20" class="btn btn-sm btn-warning">
            </form>
              </div>
              <button class="btn w-35px h-35px mr-1 btn-orange text-uppercase btn-sm" onclick="show_hide_comment(' . $title->withdraw_id . ')"><i class="fas fa-pencil"></i></button>';
                    } else {
                        $d = $title->comment;
                    }
                    if ($title->remark == "") {
                        $remark = '<form class="form-horizontal form-label-left" action="' . $f . '" method="post">' . csrf_field() . '
              <input type="text" class="" name="remark" width="70%" required>
              <input type="hidden" name="id" value="' . $title->withdraw_id . '">
              <input type="submit" value="Submit" name="submit" maxlength="20" class="btn btn-info" style="margin-top:3px;">
            </form>';
                    } else {
                        $remark = $title->remark;
                    }
                    $revq = asset('my-admin/updatereview/' . $title->id . '/1');
                    if ($title->review == 0) {
                        $rev = '<a href="' . $revq . '" style=""><i class="fa fa-star-o"></i>';
                    } else {
                        $revqs = asset('my-admin/updatereview/' . $title->id . '/0');
                        $rev = '<a href="' . $revqs . '" style=""><i class="fa fa-star"></i>';
                    }
                    $revq = asset('my-admin/updatesuspect/' . $title->id . '/1');
                    if ($title->suspect == 0) {
                        $rev1 = '<a href="' . $revq . '" style=""><i class="fa fa-star-o"></i>';
                    } else {
                        $revqs = asset('my-admin/updatesuspect/' . $title->id . '/0');
                        $rev1 = '<a href="' . $revqs . '" style="color:red;"><i class="fa fa-star"></i>';
                    }
                    //echo '<pre>';print_r($title);die;
                    $nestedData['sno'] = $count;
                    $nestedData['userid'] = $aa;
                    $nestedData['acc_no'] = $title->ano;
                    $nestedData['rev'] = $rev . ' ' . $rev1;
                    $nestedData['withdraw_amount'] = $title->withdraw_amount;
                    $nestedData['username'] = strtoupper($title->username);
                    $nestedData['withdraw_request_id'] = $title->withdraw_request_id;
                    $nestedData['transfer_id'] = $title->transfer_id;
                    $nestedData['ifsc'] = $title->ifsc;
                    $nestedData['bankname'] = $title->bankname;
                    $nestedData['bankbranch'] = $title->bankbranch;
                    $nestedData['email'] = $title->email;
                    $nestedData['mobile'] = $title->mobile;
                    $nestedData['withdraw_request'] = date('d-M-Y', strtotime($title->withdraw_request));
                    $nestedData['approved_date'] = $c;
                    $nestedData['with_type'] = $title->with_type;
                    $nestedData['comment'] = $d;
                    $nestedData['remark'] = $remark;

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
    public function updatereview($id, $staus)
    {
        $data['review'] = $staus;
        DB::connection('mysql2')->table('withdraw')->where('id', $id)->update($data);
        return Redirect::back()->with('success', 'Update Review successfully!');
    }
    public function updatesuspect($id, $staus)
    {
        $data['suspect'] = $staus;
        DB::connection('mysql2')->table('withdraw')->where('id', $id)->update($data);
        return Redirect::back()->with('success', 'Update Review successfully!');
    }
    public function details($id)
    {
        $allplayers = DB::table('registerusers')->where('registerusers.id', $id)->get();

        $bank = DB::table('bank')->join('registerusers', 'registerusers.id', '=', 'bank.userid')->where('registerusers.id', $id)->where('bank.status', '0')->where('registerusers.pan_verify', '1')->where('registerusers.mobile_verify', '1')->where('registerusers.email_verify', '1')->select('registerusers.email', 'registerusers.username', 'registerusers.mobile_verify', 'registerusers.email_verify', 'registerusers.pan_verify', 'bank.*')->get();

        $pancard = DB::table('pancard')->join('registerusers', 'registerusers.id', '=', 'pancard.userid')->where('registerusers.id', $id)->where('pancard.status', '0')->where('registerusers.pan_verify', '0')->where('registerusers.mobile_verify', '1')->where('registerusers.email_verify', '1')->select('registerusers.email', 'registerusers.username', 'registerusers.mobile_verify', 'registerusers.email_verify', 'pancard.*')->get();

        return view('registerusers.details', compact('allplayers', 'bank', 'pancard'));
    }

    public function approve(Request $request){
        if ($request->isMethod('post')) {
            $input = $request->all();

            $uid = $input['uid'];
            $amount = $input['amount'];
            $finduserdetails = DB::table('withdraw')->where('withdraw.id', $input['id'])->join('registerusers', 'registerusers.id', '=', 'withdraw.user_id')->select('user_id', 'registerusers.email', 'registerusers.team', 'withdraw_request_id', 'registerusers.mobile','with_type')->first();
            if (!empty($input['approve']) && $finduserdetails->with_type=='instant') {
                if ($input['approve'] == 'Approve') {
                    $data['comment'] = $input['comment'];
                    $data['approved_date'] = date('Y-m-d');
                    $bank_detail = DB::connection('mysql')->table('bank')->where('userid', $uid)->first();
                    // to check the pan or bank verifications
                    $findverification = DB::connection('mysql')->table('user_verify')->where('userid', $uid)->first();
                    if (!empty($findverification)) {
                        if ($findverification->pan_verify != 1) {
                            // $msgg['msg'] = "Please first complete your PAN verification process to withdraw this amount.";
                            // $msgg['status'] = false;
                            // return response()->json(array($msgg));
                            return Redirect::back()->with('danger', 'Sorry, Pan verification is Pending!');
                            die;
                        }
                        if ($findverification->bank_verify != 1) {
                            // $msgg['msg'] = "Please first complete your Bank verification process to withdraw this amount.";
                            // $msgg['status'] = false;
                            // return response()->json(array($msgg));
                            return Redirect::back()->with('danger', 'Sorry, Bank verification is Pending!');
                            die;
                        }
                    } else {
                        // $msgg['status'] = false;
                        // $msgg['msg'] = 'Sorry, no data available!';
                        // return response()->json(array($msgg));
                        return Redirect::back()->with('danger', 'Sorry, no data available!');
                        // die;
                    }
                    
                    // cashfree payout - praveen
                    
                    include(app_path(). '/cashfree/cfpayout.inc.php');
                            
                    $clientId = "CF162723C79A44PQE0NUFQ6GCJR0";
                    $clientSecret = "29e3ad3c01376317b4783fe806538d17ae5b501d";
    
                    $stage = "PROD";
    
                    $authParams["clientId"] = $clientId;
                    $authParams["clientSecret"] = $clientSecret;
                    $authParams["stage"] = $stage;
    
                    try {
                        $payout = new CfPayout($authParams);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        echo "\n";  
                        die();
                    }
                    // dd($finduserdetails);
                    $beneficiary = [];
                    $beneficiary["beneId"] = $finduserdetails->user_id.''.$bank_detail->accno;
                    $beneficiary["name"] = $bank_detail->accountholder;
                    $beneficiary["email"] = $finduserdetails->email;
                    $beneficiary["phone"] = $finduserdetails->mobile;
                    $beneficiary["bankAccount"] = $bank_detail->accno;
                    $beneficiary["ifsc"] = $bank_detail->ifsc;
                    $beneficiary["address1"] = "India";
                    $beneficiary["city"] = "";
                    $beneficiary["state"] = "";
                    $beneficiary["pincode"] = "";
                    $response = $payout->addBeneficiary($beneficiary);
                    // echo "<pre>"; print_r($response); die;
                    $checkbal = $payout->getBalance();
                    // dd($checkbal);
                    if($checkbal['available']<$amount){
                        return Redirect::back()->with('danger','Insufficient Balance In your Cashfree Wallet');
                    }
                    if(($response['status']=='SUCCESS' && $response['subCode']==200) || ($response['status']=='ERROR' && $response['subCode']==409)){
                        $transfer = [];
                        $transferss = [];
                        $transferss['beneId'] =  $transfer["beneId"] = $beneficiary["beneId"];
                        if($amount >= 100 OR $amount<=199){
                            $transferss['amount'] =   $transfer["amount"] = number_format(($amount),2, ".", "")-5;
                        }
                        if($amount >=200){
                            $transferss['amount'] =   $transfer["amount"] = number_format(($amount),2, ".", "")-10;
                        }
                        $transferss['transferId'] = $transfer["transferId"] = time().$input['id'];
                        $transferss['remarks'] = $transfer["remarks"] = "Transfer request from Payout kit";
                        $transferss['withdrawid'] = $input['id'];
                        $data['beneId'] = $beneficiary["beneId"];
                        $data['transfer_id'] = $transfer["transferId"] ;
                        $data['approved_date']=date('Y-m-d H:i:s');
                        $data['status']=3;    //request sent to the cashfree
                        DB::connection('mysql2')->table('withdraw')->where('id', $input['id'])->update($data);
                        $checkwithdrawamount = DB::connection('mysql2')->table('withdraw_requests')->where('withdrawid',$input['id'])->first();
                        if(empty($checkwithdrawamount)){
                            DB::connection('mysql2')->table('withdraw_requests')->insert($transferss);
                            $ajay = $payout->requestTransfer($transfer);
                        }else{
                            return Redirect::back()->with('danger','Already Settle Withdraw Request!');
                        }
                        
                        $datamessage['email'] = $finduserdetails->email;
                        $datamessage['subject'] = 'Withdraw Request approved';
                        $datamessage['content']='<p><strong>Hello '.ucwords($finduserdetails->team).' </strong></p>';
                        $datamessage['content'].='<p>Your withdraw request has been approved. </p>';
                        $datamessage['content'].='<p>Money will come to your account in 2 to 3 days. </p>';
                        //$content.='<p><strong>'.$input['comment'].'</strong></p>';
                        $datamessage['content'].='<p></p>';
                        $content=Helpers::Mailbody1($datamessage['content'], $datamessage['email']);
                        Helpers::mailsentFormat($datamessage['email'],$datamessage['subject'],$content);
                    }
                    else{
                        return Redirect::back()->with('danger','Oops something went wrong!');
                    }
    
                    return redirect()
                            ->back()
                            ->with('success', 'Withdraw request approved and sent to cashfree for processing');
          
                    
                }
            }else if (!empty($input['approve']) && $finduserdetails->with_type=='normal') {
                if ($input['approve'] == 'Approve') {
                    $data['comment'] = $input['comment'];
                    $data['approved_date'] = date('Y-m-d');
                    $data['status'] = 1;
                    $bank_detail = DB::connection('mysql')->table('bank')->where('userid', $uid)->first();
                    // to check the pan or bank verifications
                    $findverification = DB::connection('mysql')->table('user_verify')->where('userid', $uid)->first();
                    if (!empty($findverification)) {
                        if ($findverification->pan_verify != 1) {
                            // $msgg['msg'] = "Please first complete your PAN verification process to withdraw this amount.";
                            // $msgg['status'] = false;
                            // return response()->json(array($msgg));
                            return Redirect::back()->with('danger', 'Sorry, Pan verification is Pending!');
                            die;
                        }
                        if ($findverification->bank_verify != 1) {
                            // $msgg['msg'] = "Please first complete your Bank verification process to withdraw this amount.";
                            // $msgg['status'] = false;
                            // return response()->json(array($msgg));
                            return Redirect::back()->with('danger', 'Sorry, Bank verification is Pending!');
                            die;
                        }
                    } else {
                        // $msgg['status'] = false;
                        // $msgg['msg'] = 'Sorry, no data available!';
                        // return response()->json(array($msgg));
                        return Redirect::back()->with('danger', 'Sorry, no data available!');
                        // die;
                    }
                   
                    $rowCOllection = DB::connection('mysql2')->table('withdraw')->where('id', $input['id'])->update($data);
                    $findtransactiondate = DB::table('transactions')->where('transaction_id', $finduserdetails->withdraw_request_id)->first();
                    $tdata['paymentstatus'] = 'Confirmed';
                    $tdata['created_at'] = date('Y-m-d');
                    $findtransactiondetails = DB::connection('mysql2')->table('transactions')->where('transaction_id', $finduserdetails->withdraw_request_id)->update($tdata);
                    // // //mail//
                    $datamessage['email'] = $finduserdetails->email;
                    $datamessage['subject'] = 'Withdraw Request approved';
                    $datamessage['content'] = '<p><strong>Hello ' . ucwords($finduserdetails->team) . ' </strong></p>';
                    $datamessage['content'] .= '<p>Your withdrawal request of ₹' . $input['amount'] . ' has been approved successfully.</p>';

                    $datamessage['content'] .= '<p></p>';
                    // $content=Helpers::Mailbody1($datamessage['content'], $datamessage['email']);
                    Helpers::mailSmtpSend($datamessage);

                    // //notifications//
                    $notificationdata['userid'] = $finduserdetails->user_id;
                    $notificationdata['title'] = 'Withdraw Request Approved successfully of amount ₹' . $input['amount'];
                    DB::connection('mysql2')->table('notifications')->insert($notificationdata);

                    // // //push notifications//
                    $titleget = 'Withdrawal Request Approved!';
                    Helpers::sendnotification($titleget, $notificationdata['title'], '', $finduserdetails->user_id);
                    //end push notifications//

                    // //message show//
                    return Redirect::back()->with('success', 'Withdraw Request Approved successfully!');

                }
            } else if ($input['cancel'] == 'Cancel') {
                $datas['comment'] = $input['comment'];
                $datas['approved_date'] = date('Y-m-d');
                $datas['status'] = 2;
                $rowCOllection = DB::connection('mysql2')->table('withdraw')->where('id', $input['id'])->update($datas);
                $getdata = DB::table('withdraw')->where('id', $input['id'])->first();

                //mail//
                date_default_timezone_set('Asia/Kolkata');
                $currentwinning = DB::table('userbalance')->where('user_id', $uid)->first();
                $userrbal['winning'] = $currentwinning->winning + $input['amount'];
                DB::connection('mysql2')->table('userbalance')->where('user_id', $uid)->update($userrbal);
                $tdataa['paymentstatus'] = 'Cancel';
                $tdataa['created_at'] = date('Y-m-d H:i:s');
                $tdataa['transaction_id'] = (Helpers::settings()->short_name ?? '') . '-Cancel-' . time();
                $tdataa['transaction_by'] = (Helpers::settings()->short_name ?? '') . '';
                $tdataa['amount'] = $amount;
                $tdataa['win_amt'] = $amount;
                $tdataa['bal_win_amt'] = $userrbal['winning'];
                $tdataa['bal_fund_amt'] = $currentwinning->balance;
                $tdataa['bal_bonus_amt'] = $currentwinning->bonus;
                $tdataa['total_available_amt'] = $userrbal['winning'] + $currentwinning->balance + $currentwinning->bonus;
                $tdataa['userid'] = $uid;
                $tdataa['type'] = 'withdraw cancel';
                $findtransactiondetails = DB::connection('mysql2')->table('transactions')->insert($tdataa);
                $datamessage['content'] = '';
                $datamessage['email'] = $finduserdetails->email;
                $datamessage['subject'] = 'Withdraw Request Canceled';
                $datamessage['content'] = '<p><strong>Hello ' . ucwords($finduserdetails->team) . '</strong></p>';
                $datamessage['content'] .= '<p>Your withdrawal request of ₹' . $input['amount'] . ' has been Canceled.</p>';
                $datamessage['content'] .= '<p>' . $input['comment'] . '</p>';
                $datamessage['content'] .= '<p></p>';
                //  $content=Helpers::Mailbody1($datamessage['content'], $datamessage['email']);
                Helpers::mailSmtpSend($datamessage);
                $data21['userid'] = $finduserdetails->user_id;
                $data21['seen'] = 0;
                $titleget = "Withdrawal Request Canceled!";
                $type = "individual";
                $msg = $data21['title'] = "Your withdrawal request of ₹" . $input['amount'] . " has been Canceled";
                DB::connection('mysql2')->table('notifications')->insert($data21);
                $result = Helpers::sendnotificationind($titleget, $msg, '', $finduserdetails->user_id);
                return Redirect::back()->with('danger', 'Withdraw Request Canceled!');
            }
        }
    }

    // public function approve(Request $request)
    // {
    //     if ($request->isMethod('post')) {
    //         $input = $request->all();

    //         $uid = $input['uid'];
    //         $amount = $input['amount'];
    //         $finduserdetails = DB::table('withdraw')->where('withdraw.id', $input['id'])->join('registerusers', 'registerusers.id', '=', 'withdraw.user_id')->select('user_id', 'registerusers.email', 'registerusers.team', 'withdraw_request_id', 'registerusers.mobile')->first();
    //         if (!empty($input['approve'])) {
    //             if ($input['approve'] == 'Approve') {
    //                 $data['comment'] = $input['comment'];
    //                 $data['approved_date'] = date('Y-m-d');
    //                 $data['status'] = 1;
    //                 $bank_detail = DB::connection('mysql')->table('bank')->where('userid', $uid)->first();
    //                 //==========================================Cashfree Payout work =============================================================
    //                 include app_path() . '/cashfree/cfpayout.inc.php';
    //                 $clientId = "CF124830C3MK38IIJL0LN7LPN54G";
    //                 $clientSecret = "d11e703b31c417e9dc83600ec4cee01ff9b8009c";

    //                 $stage = "PROD";
    //                 // $stage = "TEST";

    //                 $authParams["clientId"] = $clientId;
    //                 $authParams["clientSecret"] = $clientSecret;
    //                 $authParams["stage"] = $stage;

    //                 try {
    //                     $payout = new CfPayout($authParams);
    //                 } catch (Exception $e) {
    //                     echo $e->getMessage();
    //                     echo "\n";
    //                     die();
    //                 }

    //                 $beneficiary = [];
    //                 $beneficiary["beneId"] = $finduserdetails->user_id . '' . $bank_detail->accno;
    //                 $beneficiary["name"] = $bank_detail->accountholder;
    //                 $beneficiary["email"] = $finduserdetails->email;
    //                 $beneficiary["phone"] = $finduserdetails->mobile;
    //                 $beneficiary["bankAccount"] = $bank_detail->accno;
    //                 $beneficiary["ifsc"] = $bank_detail->ifsc;
    //                 $beneficiary["address1"] = "India";
    //                 $beneficiary["city"] = "";
    //                 $beneficiary["state"] = "";
    //                 $beneficiary["pincode"] = "";
    //                 $cashfreeBalance = $payout->getBalance();
    //                 if ($cashfreeBalance['available'] >= $amount) {
    //                     // LOG::info($cashfreeBalance);
    //                     $sts = $payout->getBeneficiary($beneficiary["beneId"]);
    //                     // LOG::info($sts);
    //                     if (($sts['status'] == 'SUCCESS' && $sts['subCode'] == 200)) {
    //                         $transfer = [];
    //                         $transferss = [];
    //                         $transferss['beneId'] = $transfer["beneId"] = $beneficiary["beneId"];
    //                         $transferss['amount'] = $transfer["amount"] = number_format(($amount), 2, ".", "");
    //                         $transferss['transferId'] = $transfer["transferId"] = time() . $input['id'];
    //                         $transferss['remarks'] = $transfer["remarks"] = "Transfer request from Payout kit";
    //                         $transferss['withdrawid'] = $input['id'];
    //                         $data['beneId'] = $beneficiary["beneId"];
    //                         $data['transfer_id'] = $transfer["transferId"];
    //                         $data['approved_date'] = date('Y-m-d H:i:s');
    //                         $data['status'] = 1; //request sent to the cashfree
    //                         DB::connection('mysql2')->table('withdraw')->where('id', $input['id'])->update($data);
    //                         DB::connection('mysql2')->table('withdraw_requests')->insert($transferss);
    //                         $ps = $payout->requestTransfer($transfer);
    //                         // LOG::info($ps);
    //                     } else {
    //                         $response = $payout->addBeneficiary($beneficiary);
    //                         //echo "<pre>"; print_r($response); die;
    //                         if ($response['status'] == 'SUCCESS' && $response['subCode'] == 200) {
    //                             // LOG::info($response);
    //                             $transfer = [];
    //                             $transferss = [];
    //                             $transferss['beneId'] = $transfer["beneId"] = $beneficiary["beneId"];
    //                             $transferss['amount'] = $transfer["amount"] = number_format(($amount), 2, ".", "");
    //                             $transferss['transferId'] = $transfer["transferId"] = time() . $input['id'];
    //                             $transferss['remarks'] = $transfer["remarks"] = "Transfer request from Payout kit";
    //                             $transferss['withdrawid'] = $input['id'];
    //                             $data['beneId'] = $beneficiary["beneId"];
    //                             $data['transfer_id'] = $transfer["transferId"];
    //                             $data['approved_date'] = date('Y-m-d H:i:s');
    //                             $data['status'] = 1; //request sent to the cashfree
    //                             DB::connection('mysql2')->table('withdraw')->where('id', $input['id'])->update($data);
    //                             DB::connection('mysql2')->table('withdraw_requests')->insert($transferss);
    //                             $payout->requestTransfer($transfer);
    //                         } else {
    //                             return Redirect::back()->with('danger', 'Oops something went wrong!');
    //                         }
    //                     }
    //                 }
    //                 // ============================================================================================================================
    //                 $rowCOllection = DB::connection('mysql2')->table('withdraw')->where('id', $input['id'])->update($data);
    //                 $findtransactiondate = DB::table('transactions')->where('transaction_id', $finduserdetails->withdraw_request_id)->first();
    //                 $tdata['paymentstatus'] = 'Confirmed';
    //                 $tdata['created_at'] = date('Y-m-d');
    //                 $findtransactiondetails = DB::connection('mysql2')->table('transactions')->where('transaction_id', $finduserdetails->withdraw_request_id)->update($tdata);
    //                 //mail//
    //                 $datamessage['email'] = $finduserdetails->email;
    //                 $datamessage['subject'] = 'Believer11   - Withdrawal Request Approved';
    //                 $datamessage['content'] = '<p><strong>Hello ' . ucwords($finduserdetails->team) . ' </strong></p>';
    //                 $datamessage['content'] .= '<p>Your withdrawal request of ₹' . $input['amount'] . ' has been approved successfully.</p>';

    //                 $datamessage['content'] .= '<p></p>';
    //                 // $content=Helpers::Mailbody1($datamessage['content'], $datamessage['email']);
    //                 //   Helpers::mailSmtpSend($datamessage);
    //                 $content = Htmlhelpersemail::withdrawApprove_email($finduserdetails->team, $input['amount']);
    //                 Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $content);

    //                 //notifications//
    //                 $notificationdata['userid'] = $finduserdetails->user_id;
    //                 $notificationdata['title'] = 'Withdraw Request Approved successfully of amount ₹' . $input['amount'];
    //                 DB::connection('mysql2')->table('notifications')->insert($notificationdata);

    //                 //push notifications//
    //                 $titleget = 'Believer11   - Withdrawal Request Approved';
    //                 Helpers::sendnotification($titleget, $notificationdata['title'], '', $finduserdetails->user_id);
    //                 //end push notifications//

    //                 //message show//
    //                 return Redirect::back()->with('success', 'Withdraw Request Approved successfully!');
    //             }
    //         } else if ($input['cancel'] == 'Cancel') {
    //             $datas['comment'] = $input['comment'];
    //             $datas['approved_date'] = date('Y-m-d');
    //             $datas['status'] = 2;
    //             $rowCOllection = DB::connection('mysql2')->table('withdraw')->where('id', $input['id'])->update($datas);
    //             $getdata = DB::table('withdraw')->where('id', $input['id'])->first();

    //             //mail//
    //             date_default_timezone_set('Asia/Kolkata');
    //             $currentwinning = DB::table('userbalance')->where('user_id', $uid)->first();
    //             $userrbal = [];
    //             if ($getdata->witdrawfrom == 'referral') {
    //                 $userrbal['referral_income'] = $currentwinning->referral_income + $input['amount'];
    //             } else {
    //                 $userrbal['winning'] = $currentwinning->winning + $input['amount'];
    //             }
    //             //  $userrbal['winning'] = $currentwinning->winning + $input['amount'];
    //             DB::connection('mysql2')->table('userbalance')->where('user_id', $uid)->update($userrbal);
    //             $tdataa['paymentstatus'] = 'Cancel';
    //             $tdataa['created_at'] = date('Y-m-d H:i:s');
    //             $tdataa['transaction_id'] = (Helpers::settings()->short_name ?? '') . '-Cancel-' . time();
    //             $tdataa['transaction_by'] = (Helpers::settings()->short_name ?? '') . '';
    //             $tdataa['amount'] = $amount;
    //             if ($getdata->witdrawfrom == 'referral') {
    //                 //  dd($userrbal['referral_income']);
    //                 $tdataa['referral_amt'] = $amount;
    //                 $tdataa['bal_referral_amt'] = $userrbal['referral_income'];
    //                 $tdataa['bal_win_amt'] = $currentwinning->winning;
    //                 $tdataa['total_available_amt'] = $userrbal['referral_income'] + $currentwinning->balance + $currentwinning->bonus + $currentwinning->winning;
    //             } else {
    //                 $tdataa['win_amt'] = $amount;
    //                 $tdataa['bal_win_amt'] = $userrbal['winning'];
    //                 $tdataa['bal_referral_amt'] = $currentwinning->referral_income;
    //                 $tdataa['total_available_amt'] = $userrbal['winning'] + $currentwinning->balance + $currentwinning->bonus + $currentwinning->referral_income;
    //             }

    //             //  $tdataa['bal_win_amt'] = $userrbal['winning'];
    //             $tdataa['bal_fund_amt'] = $currentwinning->balance;
    //             $tdataa['bal_bonus_amt'] = $currentwinning->bonus;
    //             //  $tdataa['total_available_amt'] = $userrbal['winning'] +$currentwinning->balance + $currentwinning->bonus;
    //             $tdataa['userid'] = $uid;
    //             $tdataa['type'] = 'withdraw cancel';
    //             $findtransactiondetails = DB::connection('mysql2')->table('transactions')->insert($tdataa);
    //             $datamessage['content'] = '';
    //             $datamessage['email'] = $finduserdetails->email;
    //             $datamessage['subject'] = 'Believer11   - Withdrawal Request Canceled';
    //             $datamessage['content'] = '<p><strong>Hello ' . ucwords($finduserdetails->team) . '</strong></p>';
    //             $datamessage['content'] .= '<p>Your withdrawal request of ₹' . $input['amount'] . ' has been Canceled.</p>';
    //             $datamessage['content'] .= '<p>' . $input['comment'] . '</p>';
    //             $datamessage['content'] .= '<p></p>';
    //             //  $content=Helpers::Mailbody1($datamessage['content'], $datamessage['email']);
    //             // Helpers::mailSmtpSend($datamessage);
    //             $content = Htmlhelpersemail::withdrawRejected_email($finduserdetails->team, $input['amount']);
    //             Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $content);
    //             $data21['userid'] = $finduserdetails->user_id;
    //             $data21['seen'] = 0;
    //             $titleget = "Withdrawal Request Canceled!";
    //             $type = "individual";
    //             $msg = $data21['title'] = "Your withdrawal request of ₹" . $input['amount'] . " has been Canceled";
    //             DB::connection('mysql2')->table('notifications')->insert($data21);
    //             $result = Helpers::sendnotification($titleget, $msg, '', $finduserdetails->user_id);
    //             return Redirect::back()->with('danger', 'Withdraw Request Canceled!');
    //         }
    //     }
    // }

    public function remark(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            $data['remark'] = $input['remark'];
            DB::connection('mysql2')->table('withdraw')->where('id', $input['id'])->update($data);
            return Redirect::back()->with('success', 'Remark added successfully!');
        }
    }
    public function approveprofilestatus(Request $request, $id)
    {
        $input['profile_image_status'] = '1';
        DB::connection('mysql2')->table('registerusers')->where('id', $id)->update($input);
        $findlastow = DB::table('registerusers')->where('id', $id)->select('email', 'team')->first();
        $req['profile_image_verify'] = 1;
        DB::connection('mysql2')->table('user_verify')->where('userid', $id)->update($req);
        $st = 'Verified';
        $datamessage['content'] = '';
        $datamessage['email'] = $findlastow->email;
        $datamessage['subject'] = 'your Profile Image Request has been successfully Approved';
        $datamessage['content'] .= '<p style="padding-left: 23px;"><strong>Hello ' . ucwords($findlastow->team) . ' </strong></p>';
        $datamessage['content'] .= '<p style="padding-left: 23px;float:center;color:green;">Your Profile image request is approved successfully!!</p>';
        $datamessage['content'] .= '<p></p>';

        $content = Helpers::Mailbody1($datamessage['content'], $datamessage['email']);
        Helpers::mailSmtpSend($datamessage);
        $notificationdata['userid'] = $id;
        $notificationdata['title'] = 'Your Profile Image verification request is ' . $st;
        DB::connection('mysql2')->table('notifications')->insert($notificationdata);
        //push notifications//
        $titleget = 'Profile Image Request!';
        $msg = $data21['title'] = 'Your Profile Image request is' . $st;
        $result = Helpers::sendnotification($titleget, $msg, '', $id);
        return Redirect::back()->with('success', 'Profile Image Request is ' . $st . '!');
    }

    public function downloadwithdrawalrequest()
    {
        $datawithdrawal = DB::table('withdraw')->join('registerusers', 'registerusers.id', '=', 'withdraw.user_id')->join('bank', 'bank.userid', '=', 'registerusers.id')->join('pancard', 'pancard.userid', '=', 'registerusers.id')->select('registerusers.id as reg_id', 'bank.id as bank_id', 'pancard.id as pan_id', 'withdraw.*', 'withdraw.id as withdraw_id', 'withdraw.status as withdraw_status', 'withdraw.amount as withdraw_amount', 'withdraw.created_at as withdraw_request', 'registerusers.activation_status as reg_status', 'bank.status as bank_status', 'pancard.status as pan_status', 'registerusers.username as username', 'bank.ifsc as ifsc', 'bank.bankname as bankname', 'bank.bankbranch as bankbranch', 'bank.accno as ano', 'registerusers.email as email', 'registerusers.mobile as mobile', 'registerusers.id as rid', 'registerusers.email', 'registerusers.mobile', 'bank.state')->where('withdraw.status', '0')->orderBY('withdraw.created_at', 'DESC')->get();
        $output1 = "";
        $output1 .= '"From A/C No.",';
        $output1 .= '"A/C No",';
        $output1 .= '"Beneficiary Name",';
        $output1 .= '"Amount",';
        $output1 .= '"Payment Mode",';
        $output1 .= '"Date",';
        $output1 .= '"IFSC CODE",';
        $output1 .= '"Payable Location",';
        $output1 .= '"Print Location",';
        $output1 .= '"Mobile No",';
        $output1 .= '"Mail ID",';
        $output1 .= '"Bank Name",';
        $output1 .= '"Withdraw Request Id",';
        $output1 .= '"Beneficiary Address",';
        $output1 .= '"Remark",';
        $output1 .= "\n";
        if (!empty($datawithdrawal->toArray())) {
            $count = 1;
            foreach ($datawithdrawal as $get) {
                $output1 .= '"065005501749",';
                $output1 .= '"' . $get->ano . '",';
                $output1 .= '"' . $get->username . '",';
                $output1 .= '"' . $get->withdraw_amount . '",';
                $output1 .= '"Bank",';
                $output1 .= '"' . date('d M Y', strtotime($get->withdraw_request)) . '",';
                $output1 .= '"' . $get->ifsc . '",';
                $output1 .= '"' . $get->state . '",';
                $output1 .= '"' . $get->bankbranch . '",';
                $output1 .= '"' . $get->mobile . '",';
                $output1 .= '"' . $get->email . '",';
                $output1 .= '"' . $get->bankname . '",';
                $output1 .= '"' . $get->withdraw_request_id . '",';
                $output1 .= '"' . $get->state . '",';
                $output1 .= '"Test",';
                $output1 .= "\n";
                $count++;
            }
        }
        $filename = "Details-usertransactiondetails.csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output1;
        exit;

    }

    public function downloadwithdrawaldata()
    {
        $datawithdrawal = DB::table('withdraw')
            ->join('registerusers', 'registerusers.id', '=', 'withdraw.user_id')
            ->join('bank', 'bank.userid', '=', 'registerusers.id')
            ->select('withdraw.*', 'withdraw.id as withdraw_id', 'withdraw.status as withdraw_status', 'withdraw.amount as withdraw_amount', 'withdraw.created_at as withdraw_request', 'bank.status as bank_status', 'registerusers.username as username', 'bank.ifsc as ifsc', 'bank.bankname as bankname', 'bank.accno as ano', 'registerusers.email as remail', 'registerusers.mobile as rmobile', 'registerusers.id as rid');

        if (request()->has('start_date')) {
            $start_date = request('start_date');
            if ($start_date != "") {
                $start_date = date('Y-m-d H:i:s', strtotime('-30 minutes', strtotime(request('start_date'))));
                $datawithdrawal->whereDate('withdraw.created_at', '>=', date('Y-m-d', strtotime($start_date)));
            }
        }
        if (request()->has('end_date')) {
            $end_date = request('end_date');
            if ($end_date != "") {
                $datawithdrawal->whereDate('withdraw.created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }
        }
        if (request()->has('email')) {
            $email = request('email');
            if ($email != "") {
                $dataa = $datawithdrawal->where('registerusers.email', 'LIKE', '%' . $email . '%')->get();
            }
        }
        if (request()->has('mobile')) {
            $mobile = request('mobile');

            if ($mobile != "") {
                $datawithdrawal->where('registerusers.mobile', 'LIKE', '%' . $mobile . '%');
            }
        }
        if (request()->has('userid')) {
            $userid = request('userid');

            if ($userid != "") {
                $datawithdrawal->where('registerusers.id', 'LIKE', '%' . $userid . '%');
            }
        }
        $status = request('status');
        if (request()->has('status')) {
            $status = request('status');
            if ($status != "") {
                $datawithdrawal->where('withdraw.status', $status);
                if ($status == 0) {
                    $datawithdrawal->orderBY('withdraw.created_at', 'DESC');
                } else {
                    $datawithdrawal->orderBY('withdraw.approved_date', 'DESC');
                }
            }
        }

        $datawithdrawal = $datawithdrawal->get();

        $output1 = "";
        $output1 .= '"User Id",';
        $output1 .= '"A/C No",';
        $output1 .= '"IFSC CODE",';
        $output1 .= '"Beneficiary Name",';
        $output1 .= '"Amount",';
        $output1 .= '"Beneficiary Id",';
        $output1 .= '"Transfer ID",';
        $output1 .= '"Withdraw Request Id",';
        $output1 .= '"Comment",';
        $output1 .= '"Approved Date",';
        $output1 .= '"Status",';
        $output1 .= '"Type",';
        $output1 .= '"Withdraw From",';
        $output1 .= '"Review",';
        $output1 .= '"Suspect",';
        $output1 .= '"Created at",';
        $output1 .= "\n";
        if (!empty($datawithdrawal->toArray())) {
            $count = 1;
            foreach ($datawithdrawal as $get) {
                $output1 .= '"' . $get->rid . '",';
                $output1 .= '"' . $get->ano . '",';
                $output1 .= '"' . $get->ifsc . '",';
                $output1 .= '"' . $get->username . '",';
                $output1 .= '"' . $get->withdraw_amount . '",';
                $output1 .= '"' . $get->beneId . '",';
                $output1 .= '"' . $get->transfer_id . '",';
                $output1 .= '"' . $get->withdraw_request_id . '",';
                $output1 .= '"' . $get->comment . '",';
                $output1 .= '"' . $get->approved_date . '",';
                $output1 .= '"Approved",';
                $output1 .= '"' . $get->type . '",';
                $output1 .= '"' . $get->witdrawfrom . '",';
                $output1 .= '"' . $get->review . '",';
                $output1 .= '"' . $get->suspect . '",';
                $output1 .= '"' . $get->created_at . '",';
                $output1 .= "\n";
                $count++;
            }
        }
        $filename = "Details-withdrawData.csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output1;
        exit;

    }

    public function paytm_withdraw_amount()
    {
        return view('verify.paytm_withdraw');
    }
    public function paytm_withdrawl_amount_table(Request $request)
    {
        $columns = array(
            0 => 'registerusers.id',
            1 => 'accno',
            2 => 'paytm_number',
            3 => 'withdraw_amount',
            4 => 'username',
            5 => 'withdraw_request_id',
            6 => 'ifsc',
            7 => 'bankname',
            8 => 'bankbranch',
            9 => 'email',
            10 => 'mobile',
            11 => 'withdraw_request',
            12 => 'created_at',
            13 => 'mobile',
            14 => 'transfer_id',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query = DB::table('withdraw')->join('registerusers', 'registerusers.id', '=', 'withdraw.user_id')->select('registerusers.id as reg_id', 'withdraw.*', 'withdraw.id as withdraw_id', 'withdraw.status as withdraw_status', 'withdraw.amount as withdraw_amount', 'withdraw.created_at as withdraw_request', 'registerusers.activation_status as reg_status', 'registerusers.username as username', 'registerusers.email as email', 'registerusers.mobile as mobile', 'registerusers.id as rid', 'registerusers.email', 'registerusers.mobile', 'withdraw.paytm_number')->where('withdraw.type', 'paytm');

        if (request()->has('start_date')) {
            $start_date = request('start_date');
            if ($start_date != "") {
                $start_date = date('Y-m-d H:i:s', strtotime('-30 minutes', strtotime(request('start_date'))));
                $query->whereDate('withdraw.created_at', '>=', date('Y-m-d', strtotime($start_date)));
            }
        }
        if (request()->has('end_date')) {
            $end_date = request('end_date');
            if ($end_date != "") {
                $query->whereDate('withdraw.created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }
        }
        if (request()->has('email')) {
            $email = request('email');
            if ($email != "") {
                $dataa = $query->where('registerusers.email', 'LIKE', '%' . $email . '%')->get();
            }
        }
        if (request()->has('mobile')) {
            $mobile = request('mobile');

            if ($mobile != "") {
                $query->where('registerusers.mobile', 'LIKE', '%' . $mobile . '%');
            }
        }
        if (request()->has('userid')) {
            $userid = request('userid');

            if ($userid != "") {
                $query->where('registerusers.id', 'LIKE', '%' . $userid . '%');
            }
        }
        $status = request('status');
        if (request()->has('status')) {
            $status = request('status');
            if ($status != "") {
                $query->where('withdraw.status', $status);
                if ($status == 0) {
                    $query->orderBY('withdraw.created_at', 'DESC');
                } else {
                    $query->orderBY('withdraw.approved_date', 'DESC');
                }
            }
        }
        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        $titles = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        echo '<pre>';print_r($titles);die;
        if (!empty($titles)) {
            $data = array();
            if (request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
                $count = $totalFiltered - $start;
            } else {
                $count = $start + 1;
            }
            foreach ($titles as $title) {
                {
                    $bb = action('RegisteruserController@getuserdetails', $title->rid);
                    $aa = '<a href="' . $bb . '" style="text-decoration:underline;">' . $title->rid . '';
                    $b = action('VerificationController@details', $title->reg_id);
                    $a = '<a href="' . $b . '">' . $title->reg_id . '';
                    $e = action('VerificationController@approve');
                    $f = action('VerificationController@remark');
                    if ($title->approved_date != null) {
                        $c = date('d-M-Y', strtotime($title->approved_date));
                    } else {
                        $c = '';
                    }

                    if ($title->withdraw_status == 0) {
                        $d = '
              <div class="row comment-modal-views comment-modal' . $title->withdraw_id . ' position-fixed top-0 left-0 right-0 bottom-0 w-100 h-100 m-auto z-index-10 align-items-center justify-content-center' . $title->withdraw_id . '" style="display: none;">
              <form class="mt-4 align-self-start form-horizontal form-label-left w-100 m-auto bg-white p-4" action="' . $e . '" method="post">' . csrf_field() . '
              <input type="text" class="form-control mb-4" name="comment" placeholder="Enter your comment" required>
              <input type="hidden" name="id" value="' . $title->withdraw_id . '">
              <input type="hidden" name="uid" value="' . $title->reg_id . '">
              <input type="hidden" name="amount" value="' . $title->withdraw_amount . '">
              <input type="submit" value="Approve" name="approve" maxlength="20" class="btn btn-sm btn-info">
              <input type="submit" value="Cancel" name="cancel" maxlength="20" class="btn btn-sm btn-danger">
              <input type="button" onclick="show_hide_comment(' . $title->withdraw_id . ')" value="Close" name="cancel" maxlength="20" class="btn btn-sm btn-warning">
            </form>
              </div>
              <button class="btn w-35px h-35px mr-1 btn-orange text-uppercase btn-sm" onclick="show_hide_comment(' . $title->withdraw_id . ')"><i class="fas fa-pencil"></i></button>';
                    } else {
                        $d = $title->comment;
                    }
                    if ($title->remark == "") {
                        $remark = '<form class="form-horizontal form-label-left" action="' . $f . '" method="post">' . csrf_field() . '
              <input type="text" class="" name="remark" width="70%" required>
              <input type="hidden" name="id" value="' . $title->withdraw_id . '">
              <input type="submit" value="Submit" name="submit" maxlength="20" class="btn btn-info" style="margin-top:3px;">
            </form>';
                    } else {
                        $remark = $title->remark;
                    }
                    $revq = asset('my-admin/updatereview/' . $title->id . '/1');
                    if ($title->review == 0) {
                        $rev = '<a href="' . $revq . '" style=""><i class="fa fa-star-o"></i>';
                    } else {
                        $revqs = asset('my-admin/updatereview/' . $title->id . '/0');
                        $rev = '<a href="' . $revqs . '" style=""><i class="fa fa-star"></i>';
                    }
                    $revq = asset('my-admin/updatesuspect/' . $title->id . '/1');
                    if ($title->suspect == 0) {
                        $rev1 = '<a href="' . $revq . '" style=""><i class="fa fa-star-o"></i>';
                    } else {
                        $revqs = asset('my-admin/updatesuspect/' . $title->id . '/0');
                        $rev1 = '<a href="' . $revqs . '" style="color:red;"><i class="fa fa-star"></i>';
                    }
                    //echo '<pre>';print_r($title);die;
                    $nestedData['sno'] = $count;
                    $nestedData['userid'] = $aa;
                    $nestedData['paytm_no'] = $title->paytm_number;
                    $nestedData['rev'] = $rev . ' ' . $rev1;
                    $nestedData['withdraw_amount'] = $title->withdraw_amount;
                    $nestedData['username'] = strtoupper($title->username);
                    $nestedData['withdraw_request_id'] = $title->withdraw_request_id;
                    $nestedData['email'] = $title->email;
                    $nestedData['mobile'] = $title->mobile;
                    $nestedData['withdraw_request'] = date('d-M-Y', strtotime($title->withdraw_request));
                    $nestedData['approved_date'] = $c;
                    $nestedData['comment'] = $d;
                    $nestedData['remark'] = $remark;

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
}
