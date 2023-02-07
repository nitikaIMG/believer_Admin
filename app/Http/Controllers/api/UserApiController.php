<?php

namespace App\Http\Controllers\api;

use App\Helpers\Helpers;
use App\Helpers\Htmlhelpersemail;
use App\Http\Controllers\Controller;
use App\Http\Controllers\api\CashfreeVerifyApiController;
use Crypt;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Validator;
use Mail;
use Razorpay\Api\Api;
use Validator;
use App\paytm\PaytmChecksum;
use App\paytm\Paytmdata;
use Session;

class UserApiController extends Controller
{

    public function getmainbanner()
    {
        Helpers::setHeader(200);
        Helpers::timezone();
        $geturl = Helpers::geturl();
        $json = array();
        $findsidebanner = DB::connection('mysql')->table('sidebanner')->get();
        if (!empty($findsidebanner)) {
            $i = 0;
            foreach ($findsidebanner as $value) {

                if (!empty($value->matchkey)) {
                    $findmatches = DB::connection('mysql')->table('listmatches')->join('series', 'listmatches.series', '=', 'series.id')
                        ->join('teams as team1', 'team1.id', '=', 'listmatches.team1')
                        ->join('teams as team2', 'team2.id', '=', 'listmatches.team2')
                        ->where('listmatches.final_status', '!=', 'IsCanceled')
                        ->where('listmatches.final_status', '!=', 'IsAbandoned')
                        ->where('series.status', 'opened')
                        ->where('listmatches.matchkey', $value->matchkey)
                        ->select(
                            'listmatches.id as listmatchid',
                            'team1.short_name as teamname1',
                            'team2.short_name as teamname2',
                            'team1.team as team1fullname',
                            'team2.team as team2fullname',
                            'team1.color as team1color',
                            'team2.color as team2color',
                            'team1.logo as team1logo',
                            'team2.logo as team2logo',
                            'listmatches.series as seriesid',
                            'series.name as seriesname',
                            'listmatches.name',
                            'listmatches.start_date',
                            'listmatches.format',
                            'listmatches.matchkey',
                            'listmatches.final_status',
                            'listmatches.launch_status',
                            'listmatches.playing11_status',
                            'listmatches.fantasy_type',
                            'listmatches.second_inning_status',
                            'listmatches.real_matchkey',
                            'listmatches.tosswinner_team',
                            'listmatches.toss_decision',
                            'listmatches.match_notification'
                        )
                        ->first();

                    if (!empty($findmatches)) {
                        $locktime = date('Y-m-d H:i:s', strtotime($findmatches->start_date));
                        if (date('Y-m-d H:i:s') >= $locktime) {
                            continue;
                        }
                        $matchdata['id'] = $findmatches->listmatchid;
                        $matchdata['name'] = $findmatches->name;
                        $matchdata['format'] = $findmatches->format;
                        $matchdata['series'] = $findmatches->seriesid;
                        $matchdata['seriesname'] = $findmatches->seriesname;
                        $matchdata['team1name'] = strtoupper($findmatches->teamname1);
                        $matchdata['team2name'] = strtoupper($findmatches->teamname2);
                        $matchdata['team1fullname'] = strtoupper($findmatches->team1fullname);
                        $matchdata['team2fullname'] = strtoupper($findmatches->team2fullname);
                        $matchdata['matchkey'] = $findmatches->matchkey;
                        $matchdata['tosswinner_team'] = ($findmatches->tosswinner_team == 'a') ? strtoupper($findmatches->teamname1) : strtoupper($findmatches->teamname2);
                        $matchdata['toss_decision'] = $findmatches->toss_decision;
                        $matchdata['type'] = $findmatches->fantasy_type;
                        $matchdata['winnerstatus'] = $findmatches->final_status;
                        $matchdata['playing11_status'] = $findmatches->playing11_status;
                        $matchdata['match_notification'] = $findmatches->match_notification ?? "";
                        $matchdata['second_inning_status'] = $findmatches->second_inning_status;
                        if (!empty($findmatches->team1color)) {
                            $matchdata['team1color'] = $findmatches->team1color;
                        } else {
                            $matchdata['team1color'] = '#ffffff';
                        }
                        if (!empty($findmatches->team2color)) {
                            $matchdata['team2color'] = $findmatches->team2color;
                        } else {
                            $matchdata['team2color'] = '#ffffff';
                        }
                        if ($findmatches->team1logo != "") {

                            if ($findmatches->team1logo) {
                                $matchdata['team1logo'] = $geturl . 'public/' . $findmatches->team1logo;
                            } else {
                                $matchdata['team1logo'] = $geturl . 'public/team_image.png';
                            }
                        } else {
                            $matchdata['team1logo'] = $geturl . 'public/team_image.png';
                        }
                        if ($findmatches->team2logo != "") {

                            if ($findmatches->team2logo) {
                                $matchdata['team2logo'] = $geturl . 'public/' . $findmatches->team2logo;
                            } else {
                                $matchdata['team2logo'] = $geturl . 'public/team_image.png';
                            }
                        } else {
                            $matchdata['team2logo'] = $geturl . 'public/team_image.png';
                        }

                        if (date('Y-m-d H:i:s') >= $locktime) {
                            $matchdata['matchopenstatus'] = 'closed';
                        } else {
                            // $matchshow++;
                            $matchdata['matchopenstatus'] = 'opened';
                        }
                        $matchdata['time_start'] = date('Y-m-d H:i:s', strtotime($findmatches->start_date));
                        $matchdata['launch_status'] = $findmatches->launch_status;
                        $matchdata['locktime'] = $locktime;
                        if (isset($_GET['userid'])) {
                            $finduserinfo = DB::connection('mysql')->table('registerusers')->where('id', $id)->select('id')->first();
                            if (!empty($finduserinfo)) {
                                $getid = $finduserinfo->id;
                                $findjointeam = DB::connection('mysql')->table('jointeam')->where('userid', $getid)->where('matchkey', $findmatches->matchkey)->orderBY('id', 'DESC')->get();
                                if (!empty($findjointeam)) {
                                    $matchdata['createteamnumber'] = $findjointeam[0]->teamnumber + 1;
                                } else {
                                    $matchdata['createteamnumber'] = 1;
                                }
                            }
                        }



                        $maximum_winning_amount = DB::connection('mysql')->table('matchchallenges')
                            ->where('matchkey', $findmatches->matchkey)
                            ->orderBy('win_amount', 'DESC')
                            ->value('win_amount');
                        $matchdata['maximum_winning_amount'] = $maximum_winning_amount;
                        $ch = DB::connection('mysql')->table('matchchallenges')->where('matchkey', $findmatches->matchkey)->first([DB::raw('SUM(maximum_user)as max_users'), DB::raw('SUM(joinedusers)as joinedusers')]);
                        // dd($ch);
                        $matchdata['max_users'] = $ch->max_users;
                        $matchdata['total_joined'] = $ch->joinedusers;
                    }

                    $json[$i]['image'] = $geturl . 'public/' . $value->image;
                    $json[$i]['type'] = $value->type;
                    $json[$i]['url'] = (!empty($value->url)) ? $value->url : '';
                    $json[$i]['matchkey'] = $value->matchkey;
                    $json[$i]['match'] = [$matchdata];
                } else {
                    $json[$i]['image'] = $geturl . 'public/' . $value->image;
                    $json[$i]['type'] = $value->type;
                    $json[$i]['url'] = (!empty($value->url)) ? $value->url : '';
                    $json[$i]['matchkey'] = "";
                    $json[$i]['match'] = [];
                }
                $i++;
            }
            return response()->json($json);
            die;
        }
    }
    public function getOTP()
    {
        return rand(100000, 999999);
        // return 123456;
    }
    public function invitepage()
    {
        $usertoken = $_GET['token'];
        if ($usertoken != "") {
            $finduserrefercode = DB::connection('mysql')->table('registerusers')->where('auth_key', $usertoken)->first();
            if (!empty($finduserrefercode)) {
                return view('api.invitepage', compact('finduserrefercode'));
            }
        }
    }
    /**
     * used to register a user in temporary table and send otp to user
     * No Authentication required
     * @return Response
     **/
    /**
     * @return json
     * @Url: /api/tempregisteruser/
     * @Method: POST
     * @Parameters
     *
     *      email: "email"
     *      mobile: "mobile"
     *      password: "password"
     *
     */
    public function tempregisteruser(request $request)
    {
        try {
            Helpers::setHeader(200);
            Helpers::timezone();
            $geturl = Helpers::geturl();
            $input = $request->all();
            $validator = Validator::make($input, [
                'email' => 'required|unique:registerusers|email',
                'password' => 'required|min:4|max:45',
                'mobile' => 'required|numeric|unique:registerusers|digits:10',
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(array(['success' => false, 'message' => $error]));
            } else {
                $input['code'] = $this->getOTP();
                // $input['code'] = 123456;
                $datau['code'] = $input['code'];
                $datau['email'] = $input['email'];
                $datau['mobile'] = $input['mobile'];
                $datau['password'] = Hash::make($input['password']);
                $datau['auth_key'] = md5(Hash::make($input['password']));
                if ($request->get('refercode')) {
                    /* check in the register users table */
                    $checkrefercode = DB::connection('mysql')->table('registerusers')->where('refer_code', $request->get('refercode'))->select('id')->first();
                    if (empty($checkrefercode)) {
                        $json['success'] = false;
                        $json['message'] = 'The entered referred code is not valid. Please enter some valid refer code.';
                        return response()->json(array($json));
                        die;
                    } else {
                        $datau['refer_id'] = $checkrefercode->id;
                    }
                }
                $insertid = DB::connection('mysql2')->table('tempuser')->insertGetId($datau);

                $findlogin = DB::connection('mysql')->table('tempuser')->whereId($insertid)->first();

                // $message = "Your OTP is ".$input['code']." \r\n";
                $message = $input['code'] . " is the OTP for your Believer11 account. NEVER SHARE YOUR OTP WITH ANYONE.
- FantasyBox";
                Helpers::sendTextSmsNew($message, $input['mobile']);
                $json['success'] = true;
                $json['message'] = 'OTP sent';
                $json['tempuser'] = base64_encode(serialize($insertid));
                return response()->json(array($json));
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    // public function timeCheckForOTP($type,$checkdata1){
    //     if(!empty($checkdata1->otp_times)){
    //         $otp_time = json_decode($checkdata1->otp_times,true);
    //         $timecount = explode(',', $otp_time[$type]);
    //         if(count($timecount)==3){
    //             $diff = time()-$timecount[0];
    //             if($diff<3600){
    //                 $json['success'] = false;
    //                 $json['message'] = 'Your OTP limit exceed';
    //                 return response()->json(array($json));
    //                 die;
    //             }else{
    //                 $timecount[] = ''.time();
    //                 // dump($timecount);
    //                 array_shift($timecount);
    //                 $otp_time[$type] = implode(',',$timecount);
    //                 DB::connection('mysql2')->table('registerusers')->where('id',$checkdata1->id)->update(['otp_times'=>json_encode($otp_time)]);
    //                 // dd($timecount);
    //             }
    //         }else{
    //             // dd($otp_time['resend']);
    //             $otp_time[$type] = $otp_time[$type].','.time();
    //             // dump(json_encode($otp_time));
    //             DB::connection('mysql2')->table('registerusers')->where('id',$checkdata1->id)->update(['otp_times'=>json_encode($otp_time)]);
    //         }
    //     }else{
    //         $otp_times[$type] = ''.time();
    //         DB::connection('mysql2')->table('registerusers')->where('id',$checkdata1->id)->update(['otp_times'=>json_encode($otp_times)]);
    //     }
    // }

    public function timeCheckForOTP($type, $checkdata1)
    {
        if (!empty($checkdata1->otp_times)) {
            // $type = "resend";
            $otp_time = json_decode($checkdata1->otp_times, true);
            foreach ($otp_time as $key => $time) {
                if ($type == $key) {
                    $timecount = explode(',', $time);
                    // dd($timecount);
                    if (count($timecount) == 3) {
                        $diff = time() - $timecount[0];
                        if ($diff < 3600) {
                            $json['success'] = false;
                            $json['message'] = 'Your OTP limit exceed';
                            return ($json);
                            die;
                        } else {
                            $timecount[] = '' . time();
                            // dump($timecount);
                            array_shift($timecount);
                            $otp_time[$type] = implode(',', $timecount);
                            DB::connection('mysql2')->table('registerusers')->where('id', $checkdata1->id)->update(['otp_times' => json_encode($otp_time)]);
                            // dd($timecount);
                        }
                    } else {
                        $otp_time[$type] = $otp_time[$type] . ',' . time();
                        // dd($otp_time[$type]);
                        // dd(json_encode($otp_time));
                        $aa = DB::connection('mysql2')->table('registerusers')->where('id', $checkdata1->id)->update(['otp_times' => json_encode($otp_time)]);
                    }
                } else {
                    $otp_times1[$type] = '' . time();
                    $newarray = array_merge($otp_time, $otp_times1);
                    // dd(json_encode($newarray));
                    DB::connection('mysql2')->table('registerusers')->where('id', $checkdata1->id)->update(['otp_times' => json_encode($newarray)]);
                }
            }
        } else {
            $otp_times[$type] = '' . time();
            DB::connection('mysql2')->table('registerusers')->where('id', $checkdata1->id)->update(['otp_times' => json_encode($otp_times)]);
        }
    }
    // public function resendotp(Request $request)
    // {
    //     Helpers::setHeader(200);
    //     // if($request->isMethod('post')){
    //     if ($request->get('tempuser')) {
    //         $gid = $request->get('tempuser');
    //         $id = unserialize(base64_decode($gid));
    //         $checkdata = DB::connection('mysql')->table('tempuser')->where('id', $id)->first();
    //         if (!empty($checkdata)) {
    //             $code = $checkdata->code;
    //             $mobile = $checkdata->mobile;
    //             $message = $code . " is the OTP for your Believer11 account. NEVER SHARE YOUR OTP WITH ANYONE.
    // - Believer11";

    //             Helpers::sendTextSmsNew($message, $mobile);

    //             $json['success'] = true;
    //             $json['message'] = 'OTP Send';
    //             return response()->json(array($json));
    //             die;
    //         } else {
    //             $json['success'] = false;
    //             $json['message'] = 'Invalid id provide';
    //             return response()->json(array($json));
    //             die;
    //         }
    //     }
    //     if ($request->get('username')) {
    //         $mobile = $request->get('username');
    //         $checkdata1 = DB::connection('mysql')->table('registerusers')->where('mobile', $mobile)->first();
    //         if (!empty($checkdata1)) {
    //             $abs = $this->timeCheckForOTP('resend',$checkdata1);
    //             if(!empty($abs)){
    //                 if($abs["success"]==false){
    //                     $json['success'] = false;
    //                     $json['message'] = 'Your OTP limit exceed';
    //                     return response()->json(array($json));die;
    //                 }
    //             }
    //             $code1 = $checkdata1->code;
    //             $mobile1 = $checkdata1->mobile;
    //             $message1 = $code1 . " is the OTP for your Believer11 account. NEVER SHARE YOUR OTP WITH ANYONE.
    // - Believer11";
    //             Helpers::sendTextSmsNew($message1, $mobile1);
    //             $json['success'] = true;
    //             $json['message'] = 'OTP Send';
    //             return response()->json(array($json));
    //             die;
    //         } else {
    //             $json['success'] = false;
    //             $json['message'] = 'Invalid id provide';
    //             return response()->json(array($json));
    //             die;
    //         }
    //     }
    //     if ($request->get('email')) {
    //         $email = $request->get('email');
    //         $checkdata1 = DB::connection('mysql')->table('registerusers')->where('email', $email)->first();
    //         if (!empty($checkdata1)) {
    //             $emailsubject = 'Believer11 - OTP for Authentication';
    //             $content = '<tr>
    //                     <td style="padding:20px 20px 0px 20px" align="left">
    //                     <div style="font-family:Roboto,Arial,Helvetica;font-size:15px;line-height:22px;color:#4e4e4e">Hello <strong>user</strong> Welcome to ' . (Helpers::settings()->project_name ?? '') . '.Join the best community of Fans. Come play our Fantasy Cricket.<br> To verify your email account please use this OTP <strong>' . $checkdata1->code . '</strong>
    //                     </div>
    //                     </td>
    //                 </tr>';
    //             $msg = Helpers::mailbody1($content);
    //             $datamessage['email'] =  $checkdata1->email;
    //             $datamessage['subject'] = $emailsubject;
    //             $datamessage['content'] = $msg;
    //             Helpers::mailsentFormat($checkdata1->$email, $emailsubject, $msg);
    //             $json['success'] = true;
    //             $json['message'] = 'OTP Send';
    //             return response()->json($json);
    //             die;
    //         } else {
    //             $json['success'] = false;
    //             $json['message'] = 'Invalid id provide';
    //             return response()->json($json);
    //             die;
    //         }
    //     } else {
    //         $json['success'] = false;
    //         $json['message'] = 'Unauthorized Request';
    //         return response()->json(array($json));
    //         die;
    //     }
    //     // }
    // }
    public function resendotp(Request $request)
    {
        Helpers::setHeader(200);
        // if($request->isMethod('post')){
        if ($request->get('tempuser')) {
            $gid = $request->get('tempuser');
            $id = unserialize(base64_decode($gid));
            $checkdata = DB::connection('mysql')->table('tempuser')->where('id', $id)->first();
            if (!empty($checkdata)) {
                $code = $checkdata->code;
                $mobile = $checkdata->mobile;
                $message = $code . " is the OTP for your Believer11 account. NEVER SHARE YOUR OTP WITH ANYONE.
- FantasyBox";

                Helpers::sendTextSmsNew($message, $mobile);

                $json['success'] = true;
                $json['message'] = 'OTP Send';
                return response()->json(array($json));
                die;
            } else {
                $json['success'] = false;
                $json['message'] = 'Invalid id provide';
                return response()->json(array($json));
                die;
            }
        } elseif ($request->has('username')) {
            $username = $request->get('username');
            $newmailaddress = Helpers::getnewmail($username);
            $checkdata1 = DB::connection('mysql')->table('registerusers')
                ->where(function ($query) use ($username, $newmailaddress) {
                    $query->where('email', '=', $username);
                    $query->orWhere('mobile', '=', $username);
                    $query->orwhere('email', '=', $newmailaddress);
                })->first();
            if (!empty($checkdata1)) {
                if ($username == $checkdata1->mobile) {
                    #check for mobile data
                    $abs = $this->timeCheckForOTP('resend', $checkdata1);
                    if (!empty($abs)) {
                        if ($abs["success"] == false) {
                            $json['success'] = false;
                            $json['message'] = 'Your OTP limit exceed';
                            return response()->json(array($json));
                            die;
                        }
                    }
                    $code1 = $checkdata1->code;
                    $mobile1 = $checkdata1->mobile;
                    $message1 = $code1 . " is the OTP for your Believer11 account. NEVER SHARE YOUR OTP WITH ANYONE.
- FantasyBox";
                    Helpers::sendTextSmsNew($message1, $mobile1);
                    $json['success'] = true;
                    $json['message'] = 'OTP Send';
                    return response()->json(array($json));
                    die;
                } elseif ($username == $checkdata1->email || $newmailaddress == $checkdata1->email) {
                    # check for email data of user
                    $emailsubject = 'Believer11 - OTP for Authentication';
                    $content = '<tr>
                            <td style="padding:20px 20px 0px 20px" align="left">
                            <div style="font-family:Roboto,Arial,Helvetica;font-size:15px;line-height:22px;color:#4e4e4e">Hello <strong>user</strong> Welcome to ' . (Helpers::settings()->project_name ?? '') . '.Join the best community of Fans. Come play our Fantasy Cricket.<br> To verify your email account please use this OTP <strong>' . $checkdata1->code . '</strong>
                            </div>
                            </td>
                        </tr>';
                    $msg = Helpers::mailbody1($content);
                    $msg = Htmlhelpersemail::emailotp($checkdata1->code, $checkdata1->team);
                    $datamessage['email'] = $checkdata1->email;
                    $datamessage['subject'] = $emailsubject;
                    $datamessage['content'] = $msg;
                    Helpers::mailsentFormat($checkdata1->email, $emailsubject, $msg);
                    $json['success'] = true;
                    $json['message'] = 'OTP Send on mail';
                    return response()->json([$json]);
                    die;
                } else {
                    $json['success'] = false;
                    $json['message'] = 'Invalid id provide';
                    return response()->json($json);
                    die;
                }
            }

            // $mobile = $request->get('username');
            // // $checkdata1 = DB::connection('mysql')->table('registerusers')->where('mobile', $mobile)->first();
            // if (!empty($checkdata1)) {
            //     $abs = $this->timeCheckForOTP('resend',$checkdata1);
            //     if(!empty($abs)){
            //         if($abs["success"]==false){
            //             $json['success'] = false;
            //             $json['message'] = 'Your OTP limit exceed';
            //             return response()->json(array($json));die;
            //         }
            //     }
            //     $code1 = $checkdata1->code;
            //     $mobile1 = $checkdata1->mobile;
            //     $message1 = $code1 . " is the OTP for your Believer11 account. NEVER SHARE YOUR OTP WITH ANYONE.
            // - Believer11";
            //     Helpers::sendTextSmsNew($message1, $mobile1);
            //     $json['success'] = true;
            //     $json['message'] = 'OTP Send';
            //     return response()->json(array($json));
            //     die;
            // } else {
            //     $json['success'] = false;
            //     $json['message'] = 'Invalid id provide';
            //     return response()->json(array($json));
            //     die;
            // }
        }
        // if ($request->get('email')) {
        //     $email = $request->get('email');
        //     $checkdata1 = DB::connection('mysql')->table('registerusers')->where('email', $email)->first();
        //     if (!empty($checkdata1)) {
        //         $emailsubject = 'Believer11 - OTP for Authentication';
        //         $content = '<tr>
        //                 <td style="padding:20px 20px 0px 20px" align="left">
        //                 <div style="font-family:Roboto,Arial,Helvetica;font-size:15px;line-height:22px;color:#4e4e4e">Hello <strong>user</strong> Welcome to ' . (Helpers::settings()->project_name ?? '') . '.Join the best community of Fans. Come play our Fantasy Cricket.<br> To verify your email account please use this OTP <strong>' . $checkdata1->code . '</strong>
        //                 </div>
        //                 </td>
        //             </tr>';
        //         $msg = Helpers::mailbody1($content);
        //         $datamessage['email'] =  $checkdata1->email;
        //         $datamessage['subject'] = $emailsubject;
        //         $datamessage['content'] = $msg;
        //         Helpers::mailsentFormat($checkdata1->$email, $emailsubject, $msg);
        //         $json['success'] = true;
        //         $json['message'] = 'OTP Send';
        //         return response()->json($json);
        //         die;
        //     } else {
        //         $json['success'] = false;
        //         $json['message'] = 'Invalid id provide';
        //         return response()->json($json);
        //         die;
        //     }
        // }
        else {
            $json['success'] = false;
            $json['message'] = 'Unauthorized Request';
            return response()->json(array($json));
            die;
        }
        // }
    }
    /* to genrate the refercode */
    public static function generateAltraffleCode($username)
    {

        $number = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charcode = substr($username, 0, 3);
        $numbercode = substr(str_shuffle(str_repeat($number, 3)), 0, 3);
        $refer_code = (Helpers::settings()->short_name ?? '') . '-' . $charcode . $numbercode;
        $findifExist = DB::connection('mysql')->table('registerusers')->where('refer_code', $refer_code)->count();
        if ($findifExist == 0) {
            return $refer_code;
        } else {
            $this->generateAltraffleCode();
        }
    }
    /* Api registeruser */

    /**
     * @return json
     * @Url: /api/registeruser/
     * @Method: POST
     * @Parameters
     *
     *      otp: "text"
     *      username: "text"
     *
     */
    public function registerusers(request $request)
    {
        try {
            Helpers::setHeader(200);
            Helpers::timezone();
            $geturl = Helpers::geturl();
            $formdata = $request->all();
            $validator = Validator::make($formdata, [
                'tempuser' => 'required',
                'otp' => 'required|min:4',
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(array(['success' => false, 'message' => $error]));
            } else {
                $gid = $request->get('tempuser');
                $tempid = unserialize(base64_decode($gid));
                $code = $request->get('otp');
                /* check for code in temp users */
                $findifuser = DB::connection('mysql')->table('tempuser')->where('id', $tempid)->where('code', $code)->first();
                if (empty($findifuser)) {
                    $json['success'] = false;
                    $json['message'] = 'Invalid OTP';
                    return response()->json(array($json));
                    die;
                } else {
                    $input['email'] = $findifuser->email;
                    $input['mobile'] = $getnumber = $findifuser->mobile;
                    $user_verify['mobile_verify'] = 1;
                    $input['password'] = $findifuser->password;
                    $input['auth_key'] = $findifuser->auth_key;
                    $input['refer_id'] = $findifuser->refer_id;
                    $input['status'] = 'activated';
                    $input['code'] = '';
                    $ff = DB::connection('mysql')->table('registerusers')->where('email', $findifuser->email)
                        ->where('mobile', $findifuser->mobile)->first();
                    if (empty($ff)) {
                        $getinsertid = DB::connection('mysql2')->table('registerusers')->insertGetId($input);
                        $user_verify['userid'] = $getinsertid;
                        DB::connection('mysql2')->table('user_verify')->insert($user_verify);
                        if ($request->get('appid')) {
                            $finusers = DB::connection('mysql')->table('registerusers')->where('id', $getinsertid)->select('id')->first();
                            DB::connection('mysql2')->table('androidappid')->where('user_id', $getinsertid)->delete();
                            $this->insertAppId($finusers, $request->get('appid'));
                        }
                        $bonustype = 'signup';
                        $this->registerprocess($request, $getinsertid, $bonustype);
                        DB::connection('mysql2')->table('tempuser')->where('id', $findifuser->id)->delete();
                    }



                    /* delete from temp users */
                    $json['success'] = true;
                    $json['userid'] = $input['auth_key'];
                    $json['auth_key'] = $input['auth_key'];
                    $json['message'] = 'Registration Done';
                    return response()->json(array($json));
                }
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    /* Api loginuser */
    /**
     * @return json
     * @Url: /api/loginuser/
     * @Method: POST
     * @Parameters
     *
     *      username: "text"
     *      password: "password"
     *
     */
    public function loginuser(request $request)
    {
        try {
            Helpers::setHeader(200);
            Helpers::timezone();
            $geturl = Helpers::geturl();
            $formdata = $request->all();
            $validator = Validator::make($formdata, [
                'username' => 'required',
                'password' => 'required|min:4',
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(array(['success' => false, 'auth_key' => 0, 'userid' => 0, 'message' => $error]));
            } else {
                $username = $request->get('username');
                $password = $request->get('password');
                $newmailaddress = Helpers::getnewmail($username);
                $findlogin = DB::connection('mysql')->table('registerusers')
                    ->where(function ($query) use ($username, $newmailaddress) {
                        $query->where('email', '=', $username);
                        $query->orWhere('mobile', '=', $username);
                        $query->orwhere('email', '=', $newmailaddress);
                    })->first();
                if (!empty($findlogin)) {
                    if (Hash::check($password, $findlogin->password)) {
                        if ($findlogin->status == 'activated') {
                            if ($request->get('appid')) {
                                DB::connection('mysql2')->table('androidappid')->where('user_id', $findlogin->id)->delete();
                                $this->insertAppId($findlogin, $request->get('appid'));
                            }
                            $userd = $findlogin->id . time() . rand(1000, 9999);
                            $authkey = md5(Hash::make($userd));
                            $userdata['auth_key'] = $authkey;
                            DB::connection('mysql2')->table('registerusers')->where('id', $findlogin->id)->update($userdata);
                            $json['success'] = true;
                            $json['auth_key'] = $authkey;
                            $json['userid'] = $authkey;
                            $json['type'] = !empty($findlogin->type) ? $findlogin->type . ' user' : 'normal user';
                            $json['message'] = 'login successfully';
                            return response()->json(array($json));
                        } else {
                            $json['success'] = false;
                            $json['auth_key'] = 0;
                            $json['userid'] = 0;
                            $json['message'] = 'You cannot login now in this account. Please contact to administartor.';
                            return response()->json(array($json));
                        }
                    } else {
                        $json['success'] = false;
                        $json['auth_key'] = 0;
                        $json['userid'] = 0;
                        $json['message'] = 'Invalid username or Password.';
                        return response()->json(array($json));
                    }
                } else {
                    $json['success'] = false;
                    $json['auth_key'] = 0;
                    $json['userid'] = 0;
                    $json['message'] = 'Invalid username or Password.';
                    return response()->json(array($json));
                }
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'auth_key' => 0, 'userid' => 0, 'message' => $e->getMessage()]));
        }
    }
    // public function loginuser(Request $request){
    //     try {
    //         Helpers::setHeader(200);
    //         Helpers::timezone();
    //         $geturl = Helpers::geturl();
    //         if($request->isMethod('post')){

    //             if(!empty($request->get('email')) && !empty($request->get('password'))){
    //                 if(!empty($request->get('email'))){
    //                     if(!empty($request->get('password'))){
    //                         $email = $request->get('email');
    //                         $password =$request->get('password');
    //                         $newmailaddress = Helpers::getnewmail($email);
    //                         $findlogin = DB::connection('mysql')->table('registerusers')->where(function ($query) use
    //                         ($email,$newmailaddress) {
    //                         $query->where('email', '=', $email);
    //                         $query->orwhere('email', '=', $newmailaddress);
    //                         })->first();
    //                         if(!empty($findlogin)){
    //                             if(Hash::check($password, $findlogin->password)){
    //                                 if($findlogin->status=='activated'){
    //                                     if($request->get('appid')){
    //                                         $this->insertAppId($findlogin,$request->get('appid'));
    //                                     }
    //                                     $userd = $findlogin->id.time().rand(1000,9999);
    //                                     $authkey = md5(Hash::make($userd));
    //                                     $userdata['auth_key'] = $authkey;
    //                                     DB::connection('mysql2')->table('registerusers')->where('id',$findlogin->id)->update($userdata);
    //                                     $appid = DB::connection('mysql')->table('androidappid')->where('user_id',$findlogin->id)->orderBy('created_at','DESC')->first();
    //                                     if(!empty($appid)){
    //                                         DB::connection('mysql2')->table('androidappid')->where('id','!=',$appid->id)->where('user_id',$findlogin->id)->delete();
    //                                     }
    //                                     $json['success'] = true;
    //                                     $json['auth_key'] = $authkey;
    //                                     $json['userid'] = $authkey;

    //                                     $json['type'] = !empty($findlogin->type) ? $findlogin->type . ' user' : 'normal user';

    //                                     $json['msg'] = 'login successfully';
    //                                     return response()->json(array($json));
    //                                 }
    //                                 else{
    //                                     $json['success'] = false;
    //                                     $json['auth_key'] = 0;
    //                                     $json['userid'] = 0;
    //                                     $json['msg'] = 'You cannot login now in this account. Please contact to administartor.';
    //                                     return response()->json(array($json));
    //                                 }
    //                             }else{
    //                                 $json['success'] = false;
    //                                 $json['auth_key'] = 0;
    //                                 $json['userid'] = 0;
    //                                 $json['msg'] = 'Invalid username or Password.';
    //                                 return response()->json(array($json));
    //                             }
    //                         }else{
    //                             $json['success'] = false;
    //                             $json['auth_key'] = 0;
    //                             $json['userid'] = 0;
    //                             $json['msg'] = 'Invalid username or Password';
    //                             return response()->json(array($json));
    //                         }
    //                     }else{
    //                         if(empty($request->get('email')) && empty($request->get('mobile'))){
    //                             $json['success'] = false;
    //                             $json['auth_key'] = 0;
    //                             $json['userid'] = 0;
    //                             $json['msg'] = 'Please Enter Password';
    //                             return response()->json(array($json));
    //                         }
    //                     }
    //                 }else{
    //                     if(empty($request->get('email')) && empty($request->get('mobile'))){
    //                         $json['success'] = false;
    //                         $json['auth_key'] = 0;
    //                         $json['userid'] = 0;
    //                         $json['msg'] = 'Please Enter Email Address';
    //                         return response()->json(array($json));
    //                     }
    //                 }
    //             }
    //             if($request->get('mobile')){
    //                 if(!empty($request->get('mobile'))){
    //                     $mobile = $request->get('mobile');
    //                     $findlogin = DB::connection('mysql')->table('registerusers')->where(function ($query) use ($mobile) {
    //                     $query->where('mobile', '=', $mobile);
    //                     })->first();
    //                     if(!empty($findlogin)){
    //                         // $getupdate['code'] = $code = rand(1000,9999);
    //                         $getupdate['code'] = $code = 1234;
    //                         DB::connection('mysql2')->table('registerusers')->where('mobile','=',$mobile)->update($getupdate);
    //                         $message = $code."  is the OTP for your account. NEVER SHARE YOUR OTP WITH ANYONE.";
    //                         // Helpers::sendTextSmsNew($message,$mobile);
    //                         $json['success'] = true;
    //                         $json['msg'] = 'OTP Send';
    //                         return response()->json(array($json));
    //                         die;
    //                     }else{
    //                         $json['success'] = false;
    //                         $json['auth_key'] = 0;
    //                         $json['userid'] = 0;
    //                         $json['msg'] = 'This mobile number is not registered.';
    //                         return response()->json(array($json));
    //                     }
    //                 }
    //             }
    //             if($request->get('username')){
    //                 if(!empty($request->get('username'))){
    //                     $mobile = $request->get('username');
    //                     $findlogin = DB::connection('mysql')->table('registerusers')->where(function ($query) use ($mobile) {
    //                     $query->where('mobile', '=', $mobile);
    //                     })->first();
    //                     if(!empty($findlogin)){
    //                         // $getupdate['code'] = $code = rand(1000,9999);
    //                         $getupdate['code'] = $code = 1234;
    //                         DB::connection('mysql2')->table('registerusers')->where('mobile','=',$mobile)->update($getupdate);
    //                         $message = $code."  is the OTP for your  account. NEVER SHARE YOUR OTP WITH ANYONE.";
    //                         // Helpers::sendTextSmsNew($message,$mobile);
    //                         $json['success'] = true;
    //                         $json['msg'] = 'OTP Send';
    //                         return response()->json(array($json));
    //                         die;
    //                     }else{
    //                         $json['success'] = false;
    //                         $json['auth_key'] = 0;
    //                         $json['userid'] = 0;
    //                         $json['msg'] = 'This mobile number is not registered.';
    //                         return response()->json(array($json));
    //                     }
    //                 }
    //             }
    //         }else{
    //             $json['success'] = false;
    //             $json['msg'] = 'Unauthorized Request';
    //             return response()->json(array($json));
    //             die;
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(array(['success' => false,'auth_key' => 0,'userid' => 0, 'message' => $e->getMessage()]));
    //     }
    // }

    public function loginotp(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->get('mobile') && $request->get('otp')) {
                if (!empty($request->get('mobile')) && !empty($request->get('otp'))) {
                    $mobile = $request->get('mobile');
                    $code = $request->get('otp');
                    $checkresdata = DB::connection('mysql')->table('registerusers')->where('mobile', $mobile)->where('code', $code)->first();
                    if (!empty($checkresdata)) {
                        $indataupdate['code'] = '';
                        DB::connection('mysql2')->table('registerusers')->where('mobile', $mobile)->where('code', $code)->update($indataupdate);
                        if ($checkresdata->status == 'activated') {
                            if ($request->get('appid')) {
                                $this->insertAppId($checkresdata, $request->get('appid'));
                            }
                            $userd = $checkresdata->id . time();
                            $authkey = md5(Hash::make($userd));
                            $userdata['auth_key'] = $authkey;
                            DB::connection('mysql2')->table('registerusers')->where('id', $checkresdata->id)->update($userdata);
                            $appid = DB::connection('mysql')->table('androidappid')->where('user_id', $checkresdata->id)->orderBy('created_at', 'DESC')->first();
                            if (!empty($appid)) {
                                DB::connection('mysql2')->table('androidappid')->where('id', '!=', $appid->id)->where('user_id', $checkresdata->id)->delete();
                            }
                            Helpers::setHeader(200);
                            $json['success'] = true;
                            $json['auth_key'] = $authkey;
                            $json['userid'] = $authkey;
                            $json['type'] = !empty($checkresdata->type) ? $checkresdata->type . ' user' : 'normal user';
                            $json['msg'] = 'login successfully';
                            return response()->json(array($json));
                        } else {
                            Helpers::setHeader(200);
                            $json['success'] = false;
                            $json['auth_key'] = 0;
                            $json['userid'] = 0;
                            $json['msg'] = 'You cannot login now in this account. Please contact to administartor.';
                            return response()->json(array($json));
                        }
                    } else {
                        Helpers::setHeader(200);
                        $json['success'] = false;
                        $json['msg'] = 'Invalid OTP';
                        return response()->json(array($json));
                        die;
                    }
                } else {
                    Helpers::setHeader(200);
                    $json['success'] = false;
                    $json['msg'] = 'Please Enter Mobile Number and OTP';
                    return response()->json(array($json));
                    die;
                }
            }
        } else {
            Helpers::setHeader(200);
            $json['success'] = false;
            $json['msg'] = 'Unauthorized Request';
            return response()->json(array($json));
            die;
        }
    }

    /* insert application app id */
    public function insertAppId($findlogin, $appid)
    {
        $appdata['user_id'] = $findlogin->id;
        $appdata['appkey'] = $appid;
        $findexist = DB::connection('mysql')->table('androidappid')->where('user_id', $findlogin->id)->where('appkey', $appid)->first();
        if (empty($findexist)) {
            DB::connection('mysql2')->table('androidappid')->insert($appdata);
        }
    }
    /* android version */
    public function getversion()
    {

        $version = DB::connection('mysql')->table('androidversion')->select('version', 'updation_points', 'ios')->first();
        if (!empty($version)) {
            $msgg['status'] = $version->version;
            $msgg['ios'] = $version->ios;
            $msgg['point'] = $version->updation_points;
            $msgg['success'] = true;
            echo json_encode(array($msgg));
            die;
        } else {
            $msgg['success'] = false;
            echo json_encode(array($msgg));
            die;
        }
        echo json_encode($msgg);
        die;
    }
    /*api for user full detail */
    /**
     * @return json
     * @Url: /api/userfulldetails/
     * @Method: GET
     * @Parameters
     *
     *       auth_key: "text" in header(Authorization)
     *
     *
     */
    public function userfulldetails(Request $request)
    {
        Helpers::setHeader(200);
        Helpers::timezone();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $user_id = $user->id;
        $totalbalances = 0;
        $verified = 0;
        $userdata = DB::connection('mysql')->table('registerusers')->where('id', $user_id)->first();
        if (!empty($userdata)) {
            $userfulldata = DB::connection('mysql')->table('user_verify')->where('userid', $user_id)->first();
            $findtotalbalanace = DB::connection('mysql')->table('userbalance')->where('user_id', $user_id)->first();
            if (!empty($findtotalbalanace)) {
                $totalbalances = number_format($findtotalbalanace->balance + $findtotalbalanace->winning + $findtotalbalanace->bonus + $findtotalbalanace->extracash + $findtotalbalanace->referral_income, 2, ".", "");
                $balance = $findtotalbalanace->balance;
                $referral_income = number_format($findtotalbalanace->referral_income, 2, ".", "");
                $msgg['addcashamount'] = number_format($findtotalbalanace->balance, 2, ".", "");
                $msgg['winningamount'] = number_format($findtotalbalanace->winning, 2, ".", "");
                $msgg['bonusamount'] = number_format($findtotalbalanace->bonus, 2, ".", "");
                $msgg['referral_bonus'] = number_format($findtotalbalanace->referral_income, 2, ".", "");
                $msgg['extracash'] = number_format($findtotalbalanace->extracash, 2, ".", "");
            }
            if ($userfulldata->email_verify == 1 && $userfulldata->mobile_verify == 1 && $userfulldata->pan_verify == 1 && $userfulldata->bank_verify == 1) {
                $verified = 1;
            }
            $msgg['id'] = $userdata->id;
            $msgg['username'] = $userdata->username;
            $msgg['mobile'] = ($userdata->mobile != '') ? $userdata->mobile : '9999999999';
            $msgg['email'] = ($userdata->email != '') ? $userdata->email : 'abc@gmail.com';
            $msgg['pincode'] = $userdata->pincode;
            $msgg['address'] = $userdata->address;
            $msgg['youtuberstatus'] = ($userdata->type != "") ? $userdata->type : "NA";
            if ($userdata->dob != '0000-00-00' && $userdata->dob != '') {
                $msgg['dob'] = date('d-M-Y', strtotime($userdata->dob));
            } else {
                $msgg['dob'] = '';
            }
            if ($userdata->dob == '0000-00-00' || $userdata->dob == '') {
                $msgg['DayOfBirth'] = "12";
                $msgg['MonthOfBirth'] = "10";
                $msgg['YearOfBirth'] = "1970";
            } else {
                $msgg['DayOfBirth'] = date('d', strtotime($userdata->dob));
                $msgg['MonthOfBirth'] = date('m', strtotime($userdata->dob));
                $msgg['YearOfBirth'] = date('Y', strtotime($userdata->dob));
            }
            $msgg['gender'] = $userdata->gender;
            $result = $userdata->image;
            if (empty($result)) {
                $msgg['image'] = $geturl . 'public/' . Helpers::settings()->user_image ?? '';
            } else {
                if ($result == 'null') {
                    $msgg['image'] = $geturl . 'public/' . Helpers::settings()->user_image ?? '';
                } else {
                    $msgg['image'] = $result;
                }
            }
            $msgg['walletamaount'] = $totalbalances;
            $msgg['verified'] = $verified;
            $msgg['activation_status'] = $userdata->status;
            $msgg['state'] = ucwords($userdata->state);
            $msgg['team'] = $userdata->team;
            $msgg['teamfreeze'] = ($userdata->team != "") ? 1 : 0;
            $msgg['emailfreeze'] = ($userdata->email != "") ? 1 : 0;
            $msgg['statefreeze'] = ($userfulldata->bank_verify != "") ? 1 : 0;
            $msgg['mobilefreeze'] = ($userfulldata->mobile_verify != "") ? 1 : 0;
            $msgg['dobfreeze'] = ($userfulldata->pan_verify != "") ? 1 : 0;
            $msgg['refer_code'] = $userdata->refer_code;

            $getreferuser = DB::connection('mysql')->table('registerusers')->where('id', $userdata->refer_id)->select('refer_code')->first();
            if (!empty($getreferuser)) {
                $msgg['refer_usercode'] = $getreferuser->refer_code;
            } else {
                $msgg['refer_usercode'] = 0;
            }

            $findchallenge = DB::connection('mysql')->table('joinedleauges')->where('userid', $user_id)->count();
            $msgg['totalchallenges'] = $findchallenge;
            $findmatches = DB::connection('mysql')->table('joinedleauges')->where('userid', $user_id)->distinct('matchkey')->count('matchkey');
            $msgg['totalmatches'] = $findmatches;
            $findseries = DB::connection('mysql')->table('joinedleauges')->join('listmatches', 'listmatches.matchkey', '=', 'joinedleauges.matchkey')->where('userid', $user_id)->distinct('series')->count('series');

            $msgg['totalseries'] = $findseries;
            // dd($totalwinningamt);
            $findwinners = DB::connection('mysql')->table('finalresults')->where('userid', $user_id)->count();
            $msgg['totalwinning'] = $findwinners;
            $msgg['balance'] = $balance;
            $msgg['maxContestTeams'] = 99;
            $msgg['totalbonus'] = number_format($findtotalbalanace->bonus, 2, ".", "");
            $msgg['totalwon'] = number_format($findtotalbalanace->winning, 2, ".", "");
            $msgg['totalreferral_income'] = number_format($findtotalbalanace->referral_income, 2, ".", "");
            $msgg['success'] = true;
            return response()->json(array($msgg));
            die;
        }
    }
    public function logoutuser(Request $request)
    {
        Helpers::accessrules();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $user_id = $user->id;
            $appdata['user_id'] = $user_id;
            if ($request->has('appkey')) {
                $appid = $appdata['appkey'] = $request->get('appkey');
                $findexist = DB::connection('mysql')->table('androidappid')->where('user_id', $user_id)->where('appkey', $request->get('appkey'))->first();
                if (!empty($findexist)) {
                    DB::connection('mysql2')->table('androidappid')->where('id', $findexist->id)->delete();
                }
            }
            $msgg['success'] = true;
            echo json_encode(array($msgg));
            die;
        }
    }
    /*api for uploading user profile image */
    /*
     * @return json
     * @Url: /api/imageUploadUser/
     * @Method: POST
     * @Parameters
     *
     *       image : "file"
     *       auth_key: "text" in header(Authorization)
     *
     *
     */
    public function imageUploadUser(Request $request)
    {
        try {
            Helpers::setHeader(200);
            Helpers::timezone();
            $geturl = Helpers::geturl();
            $input = $request->all();
            /*retrive auth key from header*/
            $user = Helpers::isAuthorize($request);
            $id = $user->id;
            unset($input['auth_key']);
            if ($request->get('image')) {
                $imageName = 'user-profile' . rand(1000, 9999) . '' . time() . '.jpg';
                $imsrc = base64_decode($request->get('image'));
                file_put_contents('./public/uploads/user-profile/' . $imageName, $imsrc);
                $data1['image'] = $data['image'] = $geturl . 'public/uploads/user-profile/' . $imageName;
            }
            DB::connection('mysql2')->table('registerusers')->where('id', $id)->update($data);
            Helpers::setHeader(200);
            $msgg['url'] = $data['image'];
            $msgg['success'] = true;
            $msgg['message'] = 'Your profile has been updated successfully.';
            echo json_encode(array($msgg));
            die;
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    /*Give Refer Bonus*/
    public function giveReferBonus($userid)
    {
        $finduserbonus = DB::connection('mysql')->table('user_verify')->where('userid', $userid)->select('bankbonus', 'userid', 'referbonus')->first();
        $result = DB::connection('mysql')->table('registerusers')->where('id', $userid)->select('*')->first();
        if (!empty($result)) {
            if ($result->refer_id != 0 && $finduserbonus->referbonus == 0) {
                $userdata = DB::connection('mysql')->table('userbalance')->where('user_id', $result->refer_id)->first();
                $referbons = DB::connection('mysql')->table('general_tabs')->where('type', '=', 'refer_bonus')->first();
                if (!empty($userdata)) {
                    $datainseert['user_id'] = $result->refer_id;
                    $datainseert['bonus'] = $userdata->bonus + $referbons->amount;
                    DB::connection('mysql2')->table('userbalance')->where('user_id', $result->refer_id)->update($datainseert);
                    $transactionsdata['transaction_id'] = (Helpers::settings()->short_name ?? '') . '-EBONUS-' . rand(1000, 9999);
                    $transactionsdata['type'] = 'Referred Signup bonus';
                    /* entry in refered table */
                    $bonus_refered['userid'] = $result->id;
                    $bonus_refered['fromid'] = $result->refer_id;
                    $bonus_refered['amount'] = $referbons->amount;
                    $bonus_refered['type'] = 'Refer signup bonus';
                    $bonus_refered['txnid'] = $transactionsdata['transaction_id'];
                    DB::connection('mysql2')->table('bonus_refered')->insert($bonus_refered);
                    $getbonusrefer['referbonus'] = 1;
                    DB::connection('mysql2')->table('user_verify')->where('userid', $result->id)->update($getbonusrefer);
                }
                $findlastow = DB::connection('mysql')->table('userbalance')->where('user_id', $result->refer_id)->first();
                if (!empty($findlastow)) {
                    $total_available_amt = $findlastow->balance + $findlastow->winning + $findlastow->bonus;
                    $bal_fund_amt = $findlastow->balance;
                    $bal_win_amt = $findlastow->winning;
                    $bal_bonus_amt = $findlastow->bonus;
                    $bal_referral_amt = 0;
                }
                $transactionsdata['transaction_by'] = 'SM11';
                $transactionsdata['amount'] = $referbons->amount;
                $transactionsdata['userid'] = $result->refer_id;
                $transactionsdata['referral_amt'] = $referbons->amount;
                $transactionsdata['paymentstatus'] = 'confirmed';
                $transactionsdata['bal_fund_amt'] = $bal_fund_amt;
                $transactionsdata['bal_win_amt'] = $bal_win_amt;
                $transactionsdata['bal_bonus_amt'] = $bal_bonus_amt;
                $transactionsdata['bal_referral_amt'] = $bal_referral_amt;
                $transactionsdata['total_available_amt'] = $total_available_amt;
                DB::connection('mysql2')->table('transactions')->insert($transactionsdata);
                $data21['userid'] = $result->refer_id;
                $data21['seen'] = 0;
                $titleget = "Congratulations!";
                $type1 = "individual";
                $msg = $data21['title'] = 'You have got ₹ ' . $referbons->amount . ' for referring your friend on ' . (Helpers::settings()->project_name ?? '') . ' app.';
                DB::connection('mysql2')->table('notifications')->insert($data21);
                Helpers::sendnotification($titleget, $msg, '', $result->refer_id);
            }
        }
        return true;
    }
    /*api for edit user profile */
    /**
     * @return json
     * @Url: /api/editprofile/
     * @Method: POST
     * @Parameters
     *
     *      username : "text"
     *      dob : "text"
     *      gender :"text"
     *      pincode :"number"
     *      address : "text"
     *      state : "text"
     *      team : "text"
     *      auth_key: "text" in header(Authorization)
     *
     *
     */
    public function editprofile(Request $request)
    {
        try {
            $geturl = Helpers::geturl();
            Helpers::timezone();
            Helpers::setHeader(200);
            if ($request->isMethod('post')) {
                $input = $request->all();
                /* check authentication*/
                $user = Helpers::isAuthorize($request);
                $id = $user->id;
                unset($input['auth_key']);
                $data = array();
                $msgg = array();
                if (isset($input['username'])) {
                    $data['username'] = $input['username'];
                }
                if (isset($input['dob'])) {
                    $data['dob'] = $input['dob']; //  Dob Must be in Y-m-d
                }
                if (isset($input['gender'])) {
                    $data['gender'] = $input['gender'];
                }
                if (isset($input['email'])) {
                    $data['email'] = $input['email'];
                }
                if (isset($input['mobile'])) {
                    $data['mobile'] = $input['mobile'];
                }
                if (isset($input['state'])) {
                    $data['state'] = $input['state'];
                }
                if (isset($input['address'])) {
                    $data['address'] = $input['address'];
                }
                if (isset($input['pincode'])) {
                    $data['pincode'] = $input['pincode'];
                }
                if (isset($input['team'])) {
                    // $data['team'] = str_replace(' ', '', $_POST['team']);
                    $data['team'] = $input['team'];

                    $restrictarray = ['madar', 'bhosadi', 'bhosd', 'aand', 'jhaant', 'jhant', 'fuck', 'chut', 'chod', 'gand', 'gaand', 'choot', 'faad', 'loda', 'Lauda', 'maar', '*fuck*', '*chut*', '*chod*', '*gand*', '*gaand*', '*choot*', '*faad*', '*loda*', '*Lauda*', '*maar*'];
                    if (in_array($data['team'], $restrictarray)) {
                        Helpers::setHeader(200);
                        $msgg['success'] = false;
                        $msgg['message'] = 'You cannot use abusive words in your team name';
                        echo json_encode(array($msgg));
                        die;
                    }
                    foreach ($restrictarray as $raray) {
                        if (strpos(strtolower($data['team']), $raray) !== false) {
                            Helpers::setHeader(200);
                            $msgg['success'] = false;
                            $msgg['message'] = 'You cannot use abusive words in your team name';
                            echo json_encode(array($msgg));
                            die;
                        }
                    }

                    // refer code Check
                    if ($request->get('refercode')) {
                        $refercode = $request->get('refercode');
                        $getrefercode = DB::connection('mysql')->table('registerusers')->where('refer_code', $refercode)->select('id')->first();
                        if (empty($getrefercode)) {
                            Helpers::setHeader(200);
                            $json['message'] = 'The entered refered code is not valid. Please enter some valid refer code.';
                            $json['success'] = false;
                            $json['mobile_status'] = 0;
                            return response()->json(array($json));
                        } else {
                            $data['refer_id'] = $getrefercode->id;
                        }
                    }

                    $findteamexist = DB::connection('mysql')->table('registerusers')->where('team', $data['team'])->where('id', '!=', $id)->first();
                    if (!empty($findteamexist)) {
                        Helpers::setHeader(200);
                        $msgg['success'] = false;
                        $msgg['message'] = 'This Team Name Is Already Exist. Please Use some Different Name For Your Team';
                        echo json_encode(array($msgg));
                        die;
                    }
                }
                DB::connection('mysql2')->table('registerusers')->where('id', $id)->update($data);

                Helpers::setHeader(200);
                $msgg['success'] = true;
                $msgg['message'] = "Profile updated successfully";
                echo json_encode(array($msgg));
                die;
            } else {
                Helpers::setHeader(200);
                $msgg['success'] = false;
                $msgg['message'] = 'Unauthorized request';
                echo json_encode(array($msgg));
                die;
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // api for forgot password //
    /**
     * @return json
     * @Url: /api/forgotpassword/
     * @Method: POST
     * @Parameters
     *
     *      username : "text"
     *      auth_key : no header is required
     *
     *
     */
    public function forgotpassword(Request $request)
    {
        try {
            Helpers::setHeader(200);
            if ($request->isMethod('post')) {
                $username = $request->get('username');
                $newmailaddress = Helpers::getnewmail($username);
                $finddetails = DB::connection('mysql')->table('registerusers')->where(function ($query) use ($username, $newmailaddress) {
                    $query->where('email', '=', $username);
                    $query->orWhere('email', '=', $newmailaddress);
                })->orWhere('mobile', $username)->first();
                $json = array();
                if (!empty($finddetails)) {
                    if ($finddetails->status == 'activated') {
                        // $abs = $this->timeCheckForOTP('forgot', $finddetails);
                        // if (!empty($abs)) {
                        //     if ($abs["success"] == false) {
                        //         $json['success'] = false;
                        //         $json['message'] = 'Your OTP limit exceed';
                        //         return response()->json($json);die;
                        //     }
                        // }
                        $input['code'] = $this->getOTP();
                        // $input['code'] = 123456;
                        DB::connection('mysql2')->table('registerusers')->where('id', $finddetails->id)->update($input);
                        if ($finddetails->mobile == $username) {
                            // $message = $input['code'] . " is the OTP for your " . (Helpers::settings()->project_name ?? '') . " account. NEVER SHARE YOUR OTP WITH ANYONE. " . (Helpers::settings()->project_name ?? '') . " will never call or message to ask for the OTP.";
                            $message = $input['code'] . " is the OTP for your Believer11 account. NEVER SHARE YOUR OTP WITH ANYONE.
- FantasyBox";
                            Helpers::sendTextSmsNew($message, $finddetails->mobile);
                            $json['success'] = true;
                            $json['message'] = 'OTP sent on your mobile number.';
                            return response()->json($json);
                        }
                        if ($username == $finddetails->email || $newmailaddress == $finddetails->email) {
                            /* send mail */
                            $emailsubject = 'Believer11 - OTP Authentication to Reset Password';
                            $datamessage['email'] = $username;
                            $datamessage['subject'] = $emailsubject;
                            $datamessage['content'] = Htmlhelpersemail::emailotp($input['code'], $finddetails->team);
                            // Helpers::mailSmtpSend($datamessage);
                            // Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $datamessage['content']);
                            /* end code for send mail */
                            $json['success'] = true;
                            $json['message'] = 'We have sent you an OTP on your registered email address. Please check your email and reset your password.';
                            return response()->json($json);
                        }
                    } else {
                        $json['success'] = false;
                        $json['message'] = 'Sorry you cannot reset your password now. Please contact to administrator.';
                        return response()->json($json);
                    }
                } else {
                    $json['success'] = false;
                    $json['message'] = 'You have entered invalid details to reset your password.';
                    return response()->json($json);
                }
            } else {
                $msgg['success'] = false;
                $msgg['message'] = 'Unauthorized request';
                echo json_encode($msgg);
                die;
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    /* match code to reset password */
    /**
     * @return json
     * @Url: /api/matchCodeForReset/
     * @Method: POST
     * @Parameters
     *
     *      username : "text"
     *      code : "number"
     *      auth_key : no header is required
     *
     *
     */
    public function matchCodeForReset(Request $request)
    {
        try {
            Helpers::setHeader(200);
            if ($request->isMethod('post')) {
                $input = $request->all();
                /* check authentication*/
                $username = $request->get('username');
                $username = Helpers::getnewmail($username);
                $code = $request->get('code');
                $findlogin = DB::connection('mysql')->table('registerusers')->where(function ($query) use ($username) {
                    $query->where('mobile', '=', $username);
                    $query->orwhere('email', '=', $username);
                })->where('code', $code)->first();
                if (!empty($findlogin)) {

                    $json['success'] = true;
                    $json['token'] = Crypt::encrypt($findlogin->id);
                    $json['message'] = 'Otp matched.';
                    return response()->json($json);
                    die;
                } else {
                    $json['success'] = false;
                    $json['token'] = 0;
                    $json['message'] = 'Invalid OTP.';
                    return response()->json($json);
                    die;
                }
            } else {
                $msgg['success'] = false;
                $msgg['message'] = 'Unauthorized request';
                echo json_encode($msgg);
                die;
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    /* reset your password now */
    /**
     * @return json
     * @Url: /api/resetpassword/
     * @Method: POST
     * @Parameters
     *
     *      password : "text"
     *      token : "text"
     *      code : "number"
     *      auth_key : no header is required
     *
     *
     */
    public function resetpassword(Request $request)
    {
        Helpers::setHeader(200);
        if ($request->isMethod('post')) {
            $input = $request->all();
            $gettoken = Crypt::decrypt($request->get('token'));
            $password = $request->get('password');
            $data['code'] = $request->get('code');
            $findloginforcode = DB::connection('mysql')->table('registerusers')->where('id', $gettoken)->where('code', $request->get('code'))->first();
            if (!empty($findloginforcode)) {
                $data['code'] = 0;
                $data['password'] = Hash::make($password);
                $data['auth_key'] = md5(Hash::make($password));
                $findlogin = DB::connection('mysql2')->table('registerusers')->where('id', $gettoken)->update($data);
                $json['success'] = true;
                $json['message'] = 'Change password successfully';
                return response()->json($json);
            } else {
                $json['success'] = false;
                $json['message'] = 'Invalid token';
                return response()->json($json);
            }
        } else {
            $msgg['success'] = false;
            $msgg['message'] = 'Unauthorized request';
            echo json_encode($msgg);
            die;
        }
    }
    // change password //
    /**
     * @return json
     * @Url: /api/changepassword/
     * @Method: POST
     * @Parameters
     *
     *      oldpassword : "text"
     *      newpassword : "text"
     *      confirmpassword : "text"
     *      auth_key : in header(Authorization)
     *
     *
     */
    public function changepassword(Request $request)
    {
        Helpers::setHeader(200);
        Helpers::timezone();
        if ($request->isMethod('post')) {
            $input = $request->all();
            /* check authentication*/
            $user = Helpers::isAuthorize($request);
            unset($input['auth_key']);
            $getid = $user->id;
            $newpassword = "";
            $oldpassword = $input['oldpassword'];
            $newpassword = $input['newpassword'];
            $confirmpassword = $input['confirmpassword'];
            if ($newpassword == $confirmpassword) {
                $password = $newpassword;
                $findusers = DB::connection('mysql')->table('registerusers')->where('id', $getid)->select('id', 'password')->first();
                if (!empty($findusers)) {
                    if (Hash::check($oldpassword, $findusers->password)) {
                        $data['password'] = Hash::make($password);
                        $data['auth_key'] = md5(Hash::make($password));
                        DB::connection('mysql2')->table('registerusers')->where('id', $findusers->id)->update($data);
                        $json['success'] = true;
                        $json['auth_key'] = $data['auth_key'];
                        $json['message'] = 'Password Changed Successfully';
                        return response()->json(array($json));
                    } else {
                        $json['success'] = false;
                        $json['message'] = 'Old password does not matched to previous password.';
                        return response()->json(array($json));
                    }
                } else {
                    $json['success'] = false;
                    $json['message'] = 'password is not exsited in database.';
                    return response()->json(array($json));
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'Confirm password and new not matched';
                return response()->json(array($response));
            }
        } else {
            $msgg['success'] = false;
            $msgg['message'] = 'Unauthorized request';
            echo json_encode(array($msgg));
            die;
        }
    }
    // register process //
    /**
     *
     *      this function is called by registeruser function
     *      parameters :
     *                   id : newly added user
     *
     *
     */
    public function registerprocess($request, $getinsertid, $bonustype)
    {
        // check for a partner system //
        $update['refer_code'] = (Helpers::settings()->short_name ?? '') . rand(100, 999) . $getinsertid;
        // end for partner system //
        DB::connection('mysql2')->table('registerusers')->where('id', $getinsertid)->update($update);
        // update in balance table//
        $blns['user_id'] = $getinsertid;
        $blns['balance'] = 0;
        $findifbalance = DB::connection('mysql')->table('userbalance')->where('user_id', $getinsertid)->first();
        if (empty($findifbalance)) {
            DB::connection('mysql2')->table('userbalance')->insert($blns);
        }
        // Bonus Given //
        $this->getbonus($getinsertid, $bonustype);
        if ($request->get('androidid')) {
            $this->getbonus($getinsertid, 'android');
        }
        return 1;
    }
    // change password //
    /**
     * @return json
     * @Url: /api/getbonus/
     * @Method: POST
     * @Parameters
     *
     *      auth_key : in header(Authorization)
     *
     *
     */
    public static function getbonus($userid, $type)
    {
        Helpers::timezone();
        $finduser = DB::connection('mysql')->table('user_verify')->where('userid', $userid)->first();
        $refer = DB::connection('mysql')->table('general_tabs')->select('*')->where('type', '=', 'refer_bonus')->first();
        $signup = DB::connection('mysql')->table('general_tabs')->select('*')->where('type', '=', 'signup_bonus')->first();
        $pan = DB::connection('mysql')->table('general_tabs')->select('*')->where('type', '=', 'pan_bonus')->first();
        $bank = DB::connection('mysql')->table('general_tabs')->select('*')->where('type', '=', 'bank_bonus')->first();
        $mobile = DB::connection('mysql')->table('general_tabs')->select('*')->where('type', '=', 'mobile_bonus')->first();
        $email = DB::connection('mysql')->table('general_tabs')->select('*')->where('type', '=', 'email_bonus')->first();
        $ifbonusgets = 0;
        switch ($type) {
            case ('signup'):
                $data['bonus'] = $amount = $signup->amount;
                if ($finduser->signupbonus == 0) {
                    $ifbonusgets = 1;
                }
                break;

            case ('refer');
                $data['refer'] = $amount = $refer->amount;
                if ($finduser->referbonus == 0) {
                    $ifbonusgets = 1;
                }
                break;

            case ('pan'):
                $data['bonus'] = $amount = $pan->amount;
                if ($finduser->panbonus == 0) {
                    $ifbonusgets = 1;
                }
                break;

            case ('bank'):
                $data['bonus'] = $amount = $bank->amount;
                if ($finduser->bankbonus == 0) {
                    $ifbonusgets = 1;
                }
                break;
            case ('mobile'):
                $data['bonus'] = $amount = $mobile->amount;
                if ($finduser->mobilebonus == 0) {
                    $ifbonusgets = 1;
                }
                break;
            case ('email'):
                $data['bonus'] = $amount = $email->amount;
                if ($finduser->email_verify == 0) {
                    $ifbonusgets = 1;
                }
                break;

            case ('facebook'):
                $data['bonus'] = $amount = 0;
                $ifbonusgets = 1;
                break;

            case ('twitter'):
                $data['bonus'] = $amount = 0;
                $ifbonusgets = 1;
                break;

            case ('whatsapp'):
                $data['bonus'] = $amount = 0;
                $ifbonusgets = 1;
                break;

            default:
                break;
        }
        /* bonus to main user */
        if ($ifbonusgets == 1) {
            UserApiController::givebonusToUser($ifbonusgets, $userid, $finduser, $type, $data);
        }
    }
    // common function to give the bonus to users //
    /**
     * @return json
     * @Url: /api/givebonusToUser/
     * @Method: POST
     * @Parameters
     *
     *      auth_key : in header(Authorization)
     *
     *
     */
    public static function givebonusToUser($ifbonusget, $userid, $finduser, $type, $data, $referes = '')
    {
        $getdata = array();
        $amount = $data['bonus'];
        $transactionsdata = array();
        $transactionsdata['transaction_id'] = (Helpers::settings()->short_name ?? '') . '-EBONUS-' . rand(1000, 9999);
        if ($ifbonusget == 1) {
            $userid = $getdata['userid'] = $userid;
            $userdata = array();
            $datainseert = array();
            $findlastow = array();
            $notificationdata = array();
            $userdata = DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
            if (!empty($userdata)) {
                $datainseert['user_id'] = $getdata['userid'];
                $datainseert['bonus'] = number_format($userdata->bonus + $data['bonus'], 2, ".", "");
                DB::connection('mysql2')->table('userbalance')->where('user_id', $userid)->update($datainseert);
            }
            $bal_bonus_amt = 0;
            $bal_win_amt = 0;
            $bal_fund_amt = 0;
            $total_available_amt = 0;
            $findlastow = DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
            if (!empty($findlastow)) {
                $total_available_amt = number_format($findlastow->balance + $findlastow->winning + $findlastow->bonus, 2, ".", "");
                $bal_fund_amt = number_format($findlastow->balance, 2, ".", "");
                $bal_win_amt = number_format($findlastow->winning, 2, ".", "");
                $bal_bonus_amt = number_format($findlastow->bonus, 2, ".", "");
            }
            $data21['userid'] = $transactionsdata['userid'] = $userid;
            if ($type == 'mobile') {
                $msg = $data21['title'] = 'You have got ₹' . $amount . ' for mobile bonus.';
                $transactionsdata['type'] = 'Mobile bonus';
                $getbonusu['mobilebonus'] = 1;
                DB::connection('mysql2')->table('user_verify')->where('userid', $userid)->update($getbonusu);
                $titleget = 'Mobile bonus';
            } else if ($type == 'signup') {
                $msg = $data21['title'] = 'You have got ₹' . $amount . ' for signup bonus.';
                $transactionsdata['type'] = 'signup bonus';
                $getbonusu['signupbonus'] = 1;
                DB::connection('mysql2')->table('user_verify')->where('userid', $userid)->update($getbonusu);
                $titleget = 'signup bonus';
            } else if ($type == 'email') {
                $msg = $data21['title'] = 'You have got ₹' . $amount . ' for email bonus.';
                $transactionsdata['type'] = 'Email bonus';
                $getbonusu['emailbonus'] = 1;
                DB::connection('mysql2')->table('user_verify')->where('userid', $userid)->update($getbonusu);
                $titleget = 'Email bonus';
            } else if ($type == 'android') {
                $transactionsdata['type'] = 'Application download bonus';
                $getbonusu['androidbonus'] = 1;
                DB::connection('mysql2')->table('user_verify')->where('userid', $userid)->update($getbonusu);
                $titleget = 'Android bonus';
            } else if ($type == 'pan') {
                $msg = $data21['title'] = 'You have got ₹' . $amount . ' for pan bonus.';
                $transactionsdata['type'] = ucwords($type) . ' verification pan bonus';
                $getbonusu['panbonus'] = 1;
                DB::connection('mysql2')->table('user_verify')->where('userid', $userid)->update($getbonusu);
                $titleget = 'Verify Pan bonus';
            } else if ($type == 'bank') {
                $msg = $data21['title'] = 'You have got ₹' . $amount . ' for bank bonus.';
                $transactionsdata['type'] = ucwords($type) . ' verification bank bonus';
                $getbonusu['bankbonus'] = 1;
                DB::connection('mysql2')->table('user_verify')->where('userid', $userid)->update($getbonusu);
                $titleget = 'Verify Bank bonus';
            } else if ($type == 'facebook' || $type == 'twitter' || $type == 'instagram') {
                $msg = $data21['title'] = 'You have got ₹' . $amount . ' for share social bonus.';
                $transactionsdata['type'] = 'Share via ' . ucwords($type) . ' Registration bonus';
                $titleget = 'Share social bonus';
            }
            $transactionsdata['transaction_by'] = Helpers::settings()->short_name ?? '';
            $transactionsdata['amount'] = $amount;
            $transactionsdata['bonus_amt'] = $amount;
            $transactionsdata['paymentstatus'] = 'confirmed';
            $transactionsdata['bal_fund_amt'] = $bal_fund_amt;
            $transactionsdata['bal_win_amt'] = $bal_win_amt;
            $transactionsdata['bal_bonus_amt'] = $bal_bonus_amt;
            $transactionsdata['total_available_amt'] = $total_available_amt;
            DB::connection('mysql2')->table('transactions')->insert($transactionsdata);
            DB::connection('mysql2')->table('notifications')->insert($data21);

            $result = Helpers::sendnotification($titleget, $msg, '', $userid);
        }
    }
    // social register or login //
    /**
     * @return json
     * @Url: /api/getUsableBalance/
     * @Method: POST
     * @Parameters
     *
     *      name:"text";
     *      email:"email";
     *      image:"file";
     *      dob:"date";
     *
     */
    public function socialauthentication(Request $request)
    {
        $geturl = Helpers::geturl();
        Helpers::setHeader(200);
        Helpers::timezone();
        if ($request->isMethod('post')) {
            $email = $request->get('email');
            $name = $request->get('name');

            if (empty($email) || empty($name)) {
                Helpers::setHeader(200);
                $json['userid'] = 0;
                $json['success'] = false;
                $json['message'] = 'name and email is required.';
                return response()->json($json);
            } else {
                if ($request->get('image')) {
                    if ($request->get('image') != "") {
                        $image = $request->get('image');
                    }
                }
                $newmailaddress = Helpers::getnewmail($email);
                $findlogin = DB::connection('mysql')->table('registerusers')->where(function ($q) use ($newmailaddress, $email) {
                    $q->where('email', $newmailaddress)->orWhere('email', $email);
                })->first();
                if (!empty($findlogin)) {
                    $checkverify = DB::connection('mysql')->table('user_verify')->where('userid', $findlogin->id)->first();
                    // echo '';print_r($checkverify);die;

                    if ($findlogin->status == 'activated') {
                        LOG::info($request->get('appid'));
                        if ($request->get('appid')) {
                            DB::connection('mysql2')->table('androidappid')->where('user_id', $findlogin->id)->delete();
                            $this->insertAppId($findlogin, $request->get('appid'));
                        }

                        $userd = $findlogin->id . time() . rand(1000, 9999);
                        $authkey = md5(Hash::make($userd));
                        $userdata['auth_key'] = $authkey;
                        DB::connection('mysql2')->table('registerusers')->where('id', $findlogin->id)->update($userdata);

                        Helpers::setHeader(200);
                        $json['status'] = true;
                        if ($checkverify->mobile_verify == 1) {
                            $json['mobile_status'] = 1;
                        } else {
                            $json['mobile_status'] = 0;
                        }
                        $json['userid'] = $authkey;
                        $json['success'] = true;
                        $json['message'] = 'login successfully';
                        return response()->json($json);
                    } else {
                        Helpers::setHeader(200);
                        $json['userid'] = 0;
                        $json['success'] = false;
                        $json['userid'] = $findlogin->auth_key;
                        $json['message'] = 'You cannot login now in this account. Please contact to administartor.';
                        return response()->json($json);
                    }
                } else {
                    $data['email'] = $newmailaddress;
                    $data['username'] = $name;
                    if (!empty($image)) {
                        $data['image'] = $image;
                    }
                    $data['auth_key'] = md5(Hash::make($newmailaddress));
                    $data['status'] = 'activated';
                    if ($request->get('dob')) {
                        if ($request->get('dob') != "") {
                            $data['dob'] = date('Y-m-d', strtotime($request->get('dob')));
                        }
                    }
                    $ff = DB::connection('mysql')->table('registerusers')->where('email', $request->get('email'))->first();
                    if (empty($ff)) {
                        $getinsertid = DB::connection('mysql2')->table('registerusers')->insertGetId($data);
                        if ($request->get('appid')) {
                            $findlogin = DB::connection('mysql')->table('registerusers')->where('id', $getinsertid)->first(['id']);
                            $this->insertAppId($findlogin, $request->get('appid'));
                        }
                        $user_verify['userid'] = $getinsertid;
                        DB::connection('mysql2')->table('user_verify')->insert($user_verify);
                        $inserteddata = DB::connection('mysql')->table('registerusers')->where('id', $getinsertid)->first();
                        $bonustype = 'email';
                        $this->registerprocess($request, $getinsertid, $bonustype);
                        $datad['email_verify'] = 1;
                        DB::connection('mysql2')->table('user_verify')->where('userid', $getinsertid)->update($datad);
                    }
                    Helpers::setHeader(200);
                    $json['success'] = true;
                    $json['mobile_status'] = 0;
                    $json['userid'] = $inserteddata->auth_key;
                    $json['message'] = 'login successfully';
                    return response()->json($json);
                }
            }
        } else {
            Helpers::setHeader(200);
            $json['success'] = false;
            $json['message'] = 'Unauthorized Request';
            return response()->json(array($json));
            die;
        }
    }
    /****************** Verification api ************************/

    // send pan verifiction request //
    /**
     * @return json
     * @Url: /api/panrequest/
     * @Method: POST
     * @Header: Authorization (in header)
     * @Parameters
     *
     *      pan_name:"text";
     *      pan_dob:"text";
     *      image:"file";
     *      pan_number:"text";
     *
     */
    public function panrequest(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $geturl = Helpers::geturl();
                Helpers::setHeader(200);
                $geturl = Helpers::geturl();
                $input = $request->all();
                $validator = Validator::make($input, [
                    'panname' => 'required',
                    'dob' => 'required',
                    'pannumber' => 'required',
                ]);
                if ($validator->fails()) {
                    $error = $this->validationHandle($validator->messages());
                    return response()->json(array(['success' => false, 'message' => $error]));
                } else {
                    $user = Helpers::isAuthorize($request);
                    $data = array();
                    $input = $request->all();
                    /* get all the params */
                    $id = $user->id;
                    $data['userid'] = $id;
                    $data['pan_name'] = strtoupper($request->get('panname'));
                    $data['pan_dob'] = $request->get('dob');
                    $data['pan_number'] = strtoupper($request->get('pannumber'));
                    $data['status'] = 0;
                    $findplannumber = DB::connection('mysql')->table('pancard')->where('pan_number', $data['pan_number'])->where('userid', '!=', $data['userid'])->first();
                    if (!empty($findplannumber)) {
                        $msgg['success'] = false;
                        $msgg['message'] = 'This pan card number is already exist.';
                        return response()->json(array($msgg));
                    }
                    // dd($data);
                    //upload panfile//
                    $data['image'] = "";
                    if ($request->get('image')) {
                        $imageName = 'pancard-' . rand(10000, 99999) . '' . time() . '.jpg';
                        $imsrc = base64_decode($request->get('image'));
                        $ffs = file_put_contents('./public/uploads/pancard/' . $imageName, $imsrc);
                        $data['image'] = $imageName;
                    }

                    if (file_exists(public_path('uploads/pancard/' . $imageName))) {
                        /* find if already exist and entry in pancard table*/
                        $req['pan_verify'] = '0';
                        DB::connection('mysql2')->table('user_verify')->where('userid', $data['userid'])->update($req);
                        $findexist = DB::connection('mysql')->table('pancard')->where('userid', $id)->first();
                        if (!empty($findexist)) {
                            DB::connection('mysql2')->table('pancard')->where('id', $findexist->id)->update($data);
                        } else {
                            DB::connection('mysql2')->table('pancard')->insert($data);
                        }
                        /* return final data */
                        $msgg['success'] = true;
                        $msgg['message'] = 'Your pan card request has been successfully submitted.';
                        return response()->json(array($msgg));
                    } else {
                        $msgg['success'] = false;
                        $msgg['message'] = 'Data Not updated properly please update again';
                        return response()->json(array($msgg));
                    }
                }
            } else {
                $msgg['success'] = false;
                $msgg['message'] = 'Unauthorized request';
                echo json_encode(array($msgg));
                die;
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // check for pan card details //
    /**
     * @return json
     * @Url: /api/seepandetails/
     * @Method: GET
     * @Header: Authorization (in header)
     * @Parameters
     *
     *   No parameters are required
     *
     */
    public function seepandetails(Request $request)
    {
        try {
            Helpers::setHeader(200);
            $geturl = Helpers::geturl();
            Helpers::timezone();
            $input = $request->all();
            /* check authentication*/
            $user = Helpers::isAuthorize($request);
            $id = $user->id;
            unset($input['auth_key']);
            $JSON = array();
            $pancarddetails = DB::connection('mysql')->table('pancard')->where('userid', $id)->first();
            if (!empty($pancarddetails)) {
                $JSON['panname'] = strtoupper($pancarddetails->pan_name);
                $JSON['pannumber'] = strtoupper($pancarddetails->pan_number);
                $JSON['pandob'] = date('d M ,Y', strtotime($pancarddetails->pan_dob));
                if (!empty($pancarddetails->comment)) {
                    $JSON['comment'] = $pancarddetails->comment;
                } else {
                    $JSON['comment'] = '';
                }
                $JSON['image'] = $geturl . 'public/uploads/pancard/' . $pancarddetails->image;
                $ext = pathinfo($JSON['image'], PATHINFO_EXTENSION);
                if ($ext == 'pdf') {
                    $JSON['imagetype'] = 'pdf';
                } else {
                    $JSON['imagetype'] = 'image';
                }
                $JSON['imagetype'] = $ext;
                $JSON['success'] = true;
            } else {
                $JSON['success'] = false;
            }
            return response()->json(array($JSON));
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // send bank account verifiction request //
    /**
     * @return json
     * @Url: /api/bankrequest/
     * @Method: POST
     * @Header: Authorization (in header)
     * @Parameters
     *
     *      accno:"text";
     *      ifsc:"text";
     *      image:"file";
     *      bankname:"text";
     *      bankbranch:"text";
     *      state:"text";
     *
     */
    public function bankrequest(Request $request)
    {

        try {
            if ($request->isMethod('post')) {
                $geturl = Helpers::geturl();
                Helpers::setHeader(200);
                $geturl = Helpers::geturl();
                $input = $request->all();
                $validator = Validator::make($input, [
                    'accountholder' => 'required',
                    'accno' => 'required',
                    'ifsc' => 'required',
                    'bankname' => 'required',
                    'bankbranch' => 'required',
                    'state' => 'required',
                ]);
                if ($validator->fails()) {
                    $error = $this->validationHandle($validator->messages());
                    return response()->json(array(['success' => false, 'message' => $error]));
                } else {
                    $user = Helpers::isAuthorize($request);
                    $data = array();
                    $input = $request->all();
                    $id = $user->id;
                    /* get all the params */
                    $data['userid'] = $user->id;
                    $data['accountholder'] = $request->get('accountholder');
                    $data['accno'] = $request->get('accno');
                    $data['ifsc'] = strtoupper($request->get('ifsc'));
                    $data['bankname'] = strtoupper($request->get('bankname'));
                    $data['bankbranch'] = strtoupper($request->get('bankbranch'));
                    $data['state'] = $request->get('state');
                    $data['status'] = '0';
                    //upload panfile//
                    // for andriod app//
                    $data['image'] = "";
                    if ($request->get('image')) {
                        $imageName = 'bank-' . rand(1000, 9999) . '' . time() . '.jpg';
                        $imsrc = base64_decode($request->get('image'));
                        file_put_contents('./public/uploads/bank/' . $imageName, $imsrc);
                        $data['image'] = $imageName;
                    } else {
                        $msgg['success'] = false;
                        $msgg['message'] = 'Please Update the image properly';
                        echo json_encode(array($msgg));
                        die;
                    }
                    if (file_exists(public_path('uploads/bank/' . $imageName))) {
                        /* find if already exist and entry in bank account table*/
                        $findlastow = DB::connection('mysql')->table('registerusers')->where('id', $id)->first();
                        $findexist = DB::connection('mysql')->table('bank')->where('userid', $id)->first();
                        if (!empty($findexist)) {
                            DB::connection('mysql2')->table('bank')->where('id', $findexist->id)->update($data);
                            $req['bank_verify'] = '0';
                            DB::connection('mysql2')->table('user_verify')->where('userid', $data['userid'])->update($req);
                        } else {
                            DB::connection('mysql2')->table('bank')->insert($data);
                            $req['bank_verify'] = '0';
                            DB::connection('mysql2')->table('user_verify')->where('userid', $data['userid'])->update($req);
                        }
                        /* return final data */
                        Helpers::setHeader(200);
                        $msgg['message'] = 'Your bank account request has been successfully submitted.';
                        $msgg['success'] = true;
                        return response()->json(array($msgg));
                    } else {
                        Helpers::setHeader(200);
                        $msgg['message'] = 'Data is not updated properly please update again.';
                        $msgg['success'] = false;
                        return response()->json(array($msgg));
                    }
                }
            } else {
                $msgg['success'] = false;
                $msgg['message'] = 'Unauthorized request';
                echo json_encode(array($msgg));
                die;
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // check for bank account details //
    /**
     * @return json
     * @Url: /api/seebankdetails/
     * @Method: GET
     * @Header: Authorization (in header)
     * @Parameters
     *
     *   No parameters are required
     *
     */
    public function seebankdetails(Request $request)
    {
        try {
            $geturl = Helpers::geturl();
            Helpers::setHeader(200);
            Helpers::timezone();

            $input = $request->all();
            /* check authentication*/
            $user = Helpers::isAuthorize($request);
            if ($user) {
                $id = $user->id;
                unset($input['auth_key']);
                $JSON = array();
                $pancarddetails = DB::connection('mysql')->table('bank')->where('userid', $id)->first();
                if (!empty($pancarddetails)) {
                    $JSON['accno'] = $pancarddetails->accno;
                    $JSON['accountholdername'] = $pancarddetails->accountholder;
                    $JSON['ifsc'] = strtoupper($pancarddetails->ifsc);
                    $JSON['bankname'] = strtoupper($pancarddetails->bankname);
                    $JSON['bankbranch'] = strtoupper($pancarddetails->bankbranch);
                    $JSON['state'] = strtoupper($pancarddetails->state);
                    if (!empty($pancarddetails->comment)) {
                        $JSON['comment'] = $pancarddetails->comment;
                    } else {
                        $JSON['comment'] = '';
                    }
                    $JSON['image'] = $geturl . 'public/uploads/bank/' . $pancarddetails->image;
                    $ext = pathinfo($JSON['image'], PATHINFO_EXTENSION);
                    if ($ext == 'pdf') {
                        $JSON['imagetype'] = 'pdf';
                    } else {
                        $JSON['imagetype'] = 'image';
                    }
                    $JSON['imagetype'] = $ext;
                    $JSON['success'] = true;
                } else {
                    $JSON['success'] = false;
                }
                return response()->json(array($JSON));
                die;
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // check for verification of users //
    /**
     * @return json
     * @Url: /api/allverify/
     * @Method: GET
     * @Header: authorization
     * @Parameters
     *
     *   No parameters are required
     *
     */
    public function allverify(Request $request)
    {
        try {
            Helpers::setHeader(200);
            Helpers::timezone();
            $geturl = Helpers::geturl();
            $input = $request->all();
            /* check authentication*/
            $user = Helpers::isAuthorize($request);
            $id = $user->id;
            unset($input['auth_key']);
            $userdata = DB::connection('mysql')->table('user_verify')->where('userid', $id)->first();
            $msgg['mobile_verify'] = $userdata->mobile_verify;
            $msgg['email_verify'] = $userdata->email_verify;
            $msgg['bank_verify'] = $userdata->bank_verify;
            $msgg['pan_verify'] = $userdata->pan_verify;
            // if ($userdata->profile_image_verify == '1') {
            $findemaila = DB::connection('mysql')->table('registerusers')->where('id', $user->id)->select('image')->first();
            if (!empty($findemaila->image)) {
                $msgg['image'] = $findemaila->image;
            } else {
                $msgg['image'] = $geturl . 'public/' . Helpers::settings()->player_image ?? '';
            }
            if ($msgg['email_verify'] == 1) {
                $findemail = DB::connection('mysql')->table('registerusers')->where('id', $user->id)->select('email')->first();
                $msgg['email'] = $findemail->email;
            }
            if ($msgg['mobile_verify'] == 1) {
                $findmobile = DB::connection('mysql')->table('registerusers')->where('id', $user->id)->select('mobile')->first();
                $msgg['mobile'] = $findmobile->mobile;
            }
            if ($msgg['pan_verify'] == 2) {
                $findreason = DB::connection('mysql')->table('pancard')->where('userid', $user->id)->select('comment')->first();
                $msgg['pan_comment'] = $findreason->comment;
            }
            if ($msgg['bank_verify'] == 2) {
                $findreason = DB::connection('mysql')->table('bank')->where('userid', $user->id)->select('comment')->first();
                $msgg['bank_comment'] = $findreason->comment;
            }

            echo json_encode(array($msgg));
            die;
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // to verify the phone number //
    /**
     * @return json
     * @Url: /api/verifyMobileNumber/
     * @Method: POST
     * @Header: Authorization (in header)
     * @Parameters
     *
     *   mobile : "number"
     *
     */
    public function verifyMobileNumber(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $geturl = Helpers::geturl();
                Helpers::setHeader(200);
                $geturl = Helpers::geturl();
                $input = $request->all();
                $validator = Validator::make($input, [
                    'mobile' => 'required|numeric|digits:10',
                ]);
                if ($validator->fails()) {
                    $error = $this->validationHandle($validator->messages());
                    return response()->json(array(['success' => false, 'message' => $error]));
                } else {
                    $user = Helpers::isAuthorize($request);
                    $data = array();
                    $input = $request->all();
                    $id = $user->id;
                    unset($input['auth_key']);
                    $input['mobile'] = $mobile = $request->get('mobile');
                    $input['userid'] = $id;
                    $data['status'] = '0';
                    //upload panfile//
                    $findthisemail = DB::connection('mysql')->table('registerusers')->where('mobile', $input['mobile'])->where('id', '<>', $id)->first();
                    if (!empty($findthisemail)) {
                        Helpers::setHeader(200);
                        $json['message'] = 'The mobile number you have entered is already in use.';
                        $json['success'] = false;
                        return response()->json(array($json));
                        die;
                    } else {
                        $finduserinfo = DB::connection('mysql')->table('user_verify')->where('userid', $id)->first();
                        if ($finduserinfo->mobile_verify == 1) {
                            Helpers::setHeader(200);
                            $json['message'] = 'You have already verified mobile number. You cannot change number now';
                            $json['success'] = false;
                            return response()->json(array($json));
                            die;
                        }
                        $Uuser = DB::connection('mysql')->table('registerusers')->where('id', $id)->first();
                        $abs = $this->timeCheckForOTP('verifyotp', $Uuser);
                        if (!empty($abs)) {
                            if ($abs["success"] == false) {
                                $json['success'] = false;
                                $json['message'] = 'Your OTP limit exceed';
                                return response()->json(array($json));
                                die;
                            }
                        }

                        $input['code'] = $this->getOTP();
                        // $input['code'] = 123456;
                        $message = $input['code'] . " is the OTP for your Believer11 account. NEVER SHARE YOUR OTP WITH ANYONE.
- FantasyBox";
                        Helpers::sendTextSmsNew($message, $input['mobile']);
                        $updatedata['code'] = $input['code'];
                        DB::connection('mysql2')->table('registerusers')->where('id', $id)->update($updatedata);

                        $json['message'] = 'OTP Sent.';
                        $json['success'] = true;
                        return response()->json(array($json));
                        die;
                    }
                }
            } else {
                $msgg['success'] = false;
                $msgg['message'] = 'Unauthorized request';
                echo json_encode(array($msgg));
                die;
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // to verify with the code//
    public function verifyCode(Request $request)
    {
        try {
            Helpers::setHeader(200);
            Helpers::timezone();
            if ($request->isMethod('post')) {
                $input = $request->all();
                /* check authentication*/
                $user = Helpers::isAuthorize($request);
                $id = $user->id;
                unset($input['auth_key']);
                $input['code'] = $code = $request->get('code');
                if (!empty($input['email'])) {
                    $emailverify = DB::connection('mysql')->table('user_verify')->where('email_verify', '=', 1)->where('userid', $id)->first();
                    if (!empty($emailverify)) {
                        $json['message'] = 'your email id is already Verified';
                        $json['success'] = true;
                        return response()->json(array($json));
                        die;
                    }
                } else {
                    $mobileverify = DB::connection('mysql')->table('user_verify')->where('mobile_verify', '=', 1)->where('userid', $id)->first();
                    if (!empty($mobileverify)) {
                        $json['message'] = 'your mobile no. is already Verified';
                        $json['success'] = true;
                        return response()->json(array($json));
                        die;
                    }
                }
                $finduserinfo = DB::connection('mysql')->table('registerusers')->where('id', $id)->where('code', $code)->first();
                if (!empty($finduserinfo)) {
                    if ($request->get('email') != '') {
                        $dataupdate['email_verify'] = 1;
                        $dataupdate1['email'] = $request->get('email');
                        $this->getbonus($finduserinfo->id, 'email');
                    } else if ($request->get('mobile') != '') {
                        $dataupdate['mobile_verify'] = 1;
                        $dataupdate1['mobile'] = $request->get('mobile');
                        $this->getbonus($finduserinfo->id, 'mobile');
                    }
                    $dataupdate1['code'] = "";
                    // dd($user);

                    DB::connection('mysql2')->table('user_verify')->where('userid', $id)->update($dataupdate);
                    DB::connection('mysql2')->table('registerusers')->where('id', $id)->update($dataupdate1);
                    Helpers::setHeader(200);
                    $json['type'] = !empty($finduserinfo->type) ? $finduserinfo->type . ' user' : 'normal user';
                    $json['success'] = true;
                    $json['message'] = 'Verified succcessfully';
                    $json['userid'] = $user->auth_key;
                    return response()->json(array($json));
                    die;
                } else {
                    Helpers::setHeader(200);
                    $json['message'] = 'Invalid Code';
                    $json['success'] = false;
                    return response()->json(array($json));
                    die;
                }
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // to verify the email//
    public function verifyEmail(Request $request)
    {
        try {
            Helpers::setHeader(200);
            Helpers::timezone();
            if ($request->isMethod('post')) {
                $input = $request->all();
                /* check authentication*/
                $user = Helpers::isAuthorize($request);
                $id = $user->id;
                unset($input['auth_key']);
                $input['email'] = $email = $request->get('email');
                $ifEmail = Helpers::checkEmail($input['email']);
                if ($ifEmail != true) {
                    $json['message'] = 'Please enter valid email address';
                    $json['success'] = false;
                    return response()->json(array($json));
                    die;
                }
                $findthisemail = DB::connection('mysql')->table('registerusers')->where('email', $input['email'])->where('id', '<>', $id)->first();
                if (!empty($findthisemail)) {
                    Helpers::setHeader(200);
                    $json['message'] = 'The email address you have entered is already in use.';
                    $json['success'] = false;
                    return response()->json(array($json));
                    die;
                } else {
                    $finduserinfo = DB::connection('mysql')->table('user_verify')->where('userid', $id)->first();
                    if ($finduserinfo->email_verify == 1) {
                        Helpers::setHeader(200);
                        $json['message'] = 'You have already verified email address. You cannot change your email address now';
                        $json['success'] = false;
                        return response()->json(array($json));
                        die;
                    }
                    $input['code'] = rand(100000, 999999);
                    // $input['code'] = 123456;
                    $emailsubject = 'Believer11 - OTP for Authentication';
                    $msgcontent = '<p style="padding-left: 23px;"><strong> Hello </strong></p>';
                    $msgcontent .= '<p style="padding-left: 23px;">Your Verification OTP code is<strong> ' . $input['code'] . '  </strong></p>';
                    // $content = Helpers::Mailbody1($msgcontent);
                    $content = Htmlhelpersemail::emailotp($input['code'], $user->team);

                    $datamessage['email'] = $request->get('email');
                    $datamessage['subject'] = $emailsubject;
                    $datamessage['content'] = $content;
                    $message = $content;
                    Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $message);
                    $updatedata['code'] = $input['code'];
                    DB::connection('mysql2')->table('registerusers')->where('id', $id)->update($updatedata);
                    Helpers::setHeader(200);
                    $json['message'] = 'OTP Sent.';
                    $json['success'] = true;
                    return response()->json(array($json));
                    die;
                }
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // to get my transactions //
    public function mytransactions(Request $request)
    {
        try {
            Helpers::setHeader(200);
            Helpers::timezone();
            $input = $request->all();
            $user = Helpers::isAuthorize($request);
            $getuserid = $user_id = $user->id;
            unset($input['auth_key']);
            $findlastow = DB::connection('mysql')->table('transactions')
                ->join('registerusers', 'registerusers.id', 'transactions.userid')
                ->orderBy('transactions.id', 'DESC')
                ->where('transactions.userid', $user_id)
                ->select('registerusers.team', 'transactions.*')
                ->get();
            $Json = array();
            $i = 0;
            if (!empty($findlastow)) {
                foreach ($findlastow as $val) {
                    $Json[$i]['id'] = $val->id;
                    $Json[$i]['type'] = $val->type;
                    $Json[$i]['amount'] = $val->amount;
                    $Json[$i]['status'] = 1;
                    $Json[$i]['success'] = true;
                    $Json[$i]['date_time'] = date('d M Y H:i', strtotime($val->created_at));
                    $Json[$i]['team'] = $val->team;
                    $Json[$i]['txnid'] = $val->transaction_id;
                    $i++;
                }
            } else {
                $Json[$i]['success'] = false;
                $Json[$i]['message'] = 'No transaction available';
            }
            return response()->json($Json);
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // to get my wallet details //
    public function mywalletdetails(Request $request)
    {
        try {
            $input = $request->all();
            Helpers::timezone();
            Helpers::setHeader(200);
            $user = Helpers::isAuthorize($request);
            $type = $request->get('type');
            $getuserid = $user_id = $user->id;
            unset($input['auth_key']);
            $findlastow = DB::connection('mysql')->table('userbalance')->orderBy('id', 'DESC')->where('user_id', $user_id)->first();
            if (!empty($findlastow)) {
                $bonus = $findlastow->bonus; // bonus amount in userbalance table
                $winning = $findlastow->winning; // winning amount in userbalance table
                $balance = $findlastow->balance; // deposited amount by user in userbalance table
                $referral_income = $findlastow->referral_income; // Referral income by user in userbalance table
                $extracash = $findlastow->extracash; // Extrcash by user in userbalance table

                $Json['balance'] = number_format(floatval($balance), 2, ".", "");
                $Json['winning'] = number_format(floatval($winning), 2, ".", "");
                $Json['bonus'] = number_format(floatval($bonus), 2, ".", "");
                $Json['extracash'] = number_format(floatval($extracash), 2, ".", "");
                $Json['referral_income'] = number_format(floatval($referral_income), 2, ".", "");
                $totalamount = $Json['balance'] + $Json['winning'] + $Json['bonus'] + $Json['referral_income'] + $Json['extracash'];
                $Json['totalamount'] = $totalamount;
                // to get the total joined contest//
                $findjoinedcontest = DB::connection('mysql')->table('joinedleauges')->where('userid', $getuserid)->groupBy('challengeid')->select('id')->get();
                $Json['totaljoinedcontest'] = count($findjoinedcontest);
                // to get the list of total matches //
                $findtotalmatches = DB::connection('mysql')->table('joinedleauges')->where('userid', $getuserid)->groupBy('matchkey')->select('id')->get()->count();
                $Json['totaljoinedmatches'] = $findtotalmatches;
                // to get the list of total series //
                // $findtotalseries = DB::connection('mysql')->table('joinedleauges')->join('listmatches', 'listmatches.matchkey', '=', 'joinedleauges.matchkey')->where('userid', $getuserid)->groupBy('listmatches.series')->select('listmatches.series')->get();
                // $Json['totaljoinedseries'] = count($findtotalseries);

                $totalwinningamt = DB::connection('mysql')->table('finalresults')->where('userid', $user_id)->sum('finalresults.amount');
                $Json['totaljoinedseries'] = $totalwinningamt;
                // total won contest //
                $findwoncontest = DB::connection('mysql')->table('finalresults')->where('userid', $getuserid)->groupBy('finalresults.challengeid')->select('id')->get();
                $Json['totalwoncontest'] = count($findwoncontest);
                $allverify = DB::connection('mysql')->table('user_verify')->where('userid', $user_id)->where('mobile_verify', '1')->where('email_verify', '1')->where('pan_verify', '1')->where('bank_verify', '1')->select('id')->first();
                if (!empty($allverify)) {
                    $Json['allverify'] = 1;
                } else {
                    $Json['allverify'] = 0;
                }
            } else {
                $Json['balance'] = 0;
                $Json['winning'] = 0;
                $Json['bonus'] = 0;
                $Json['referral_income'] = 0;
                $Json['extracash'] = 0;
                $Json['totalamount'] = 0;
            }
            return response()->json($Json);
            die;
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // request for withdarw//
    public function request_withdrow(Request $request)
    {
        try {
            $geturl = Helpers::geturl();
            Helpers::timezone();
            Helpers::setHeader(200);
            if ($request->isMethod('post')) {
                $input = $request->all();
                $user = Helpers::isAuthorize($request);
                $user_id = $userid = $user->id;
                unset($input['auth_key']);
                $amount = $request->get('amount');
                $with_type = $request->get('with_type');
                $withdrawfrom = $request->get('withdrawFrom');
                $data['user_id'] = $user_id;
                $data['amount'] = $amount;
                $data['with_type'] = $with_type;
                $data['withdraw_request_id'] = 'WD-' . $user_id . '-' . time();
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['witdrawfrom'] = $request->get('withdrawFrom');
                // if($request->has('type') && !empty($request->get('type'))){
                //     $data['type'] = $request->get('type');
                //     if($request->get('type')=='paytm'){
                //         $data['paytm_number'] = $request->get('paytm_number');
                //     }
                // }
                $msgg = array();
                // check for minimum amount //
                if ($amount < 100) {
                    $msgg['message'] = "Withdrawl amount should be greater than or equal to 100";
                    $msgg['success'] = false;
                    return response()->json(array($msgg));
                    die;
                }
                $date = date('Y-m-d');

                $checkwithdraw = DB::connection('mysql')->table('withdraw')->where('user_id', $user_id)->where('with_type', $with_type)->where('created_at', 'like', '%' . $date . '%')->first();

                if (!empty($checkwithdraw)) {
                    $msgg['message'] = "Only one instant and one normal withdraw in a day";
                    $msgg['success'] = false;
                    return response()->json(array($msgg));
                    die;
                } else {
                    if ($with_type == 'instant' && $amount >= 5000) {
                        $msgg['message'] = "You can withdraw max 5000";
                        $msgg['success'] = false;
                        return response()->json(array($msgg));
                        die;
                    }
                    if ($with_type == 'normal' && $amount >= 100000) {
                        $msgg['message'] = "You can withdraw max 100000";
                        $msgg['success'] = false;
                        return response()->json(array($msgg));
                        die;
                    }
                }



                // check for verification process //
                if ($request->get('type') != 'paytm') {
                    $findverification = DB::connection('mysql')->table('user_verify')->where('userid', $user_id)->first();
                    if (!empty($findverification)) {
                        if ($findverification->pan_verify != 1) {
                            $msgg['message'] = "Please first complete your PAN verification process. to withdarw this amount.";
                            $msgg['success'] = false;
                            return response()->json(array($msgg));
                            die;
                        }
                        if ($findverification->bank_verify != 1) {
                            $msgg['message'] = "Please first complete your Bank verification process to withdraw this amount.";
                            $msgg['success'] = false;
                            return response()->json(array($msgg));
                            die;
                        }
                    } else {
                        $msgg['success'] = false;
                        $msgg['message'] = 'Sorry,no data available!';
                        return response()->json(array($msgg));
                        die;
                    }
                }

                $bal_bonus_amt = 0;
                $bal_win_amt = 0;
                $bal_fund_amt = 0;
                $total_available_amt = 0;
                $findlastow = DB::connection('mysql')->table('userbalance')->where('user_id', $user_id)->first();
                if (!empty($findlastow)) {
                    $balance = 0;
                    /* check from where user wants to withdraw his money */
                    if ($withdrawfrom == 'referral') {
                        $balance = number_format($findlastow->referral_income, 2, ".", "");
                    } else {
                        $balance = number_format($findlastow->winning, 2, ".", "");
                    }

                    if ($balance >= $amount) {
                        $bal_fund_amt = $findlastow->balance;
                        $bal_win_amt = $findlastow->winning;
                        $bal_bonus_amt = $findlastow->bonus;
                        $bal_referral_amt = $findlastow->referral_income;
                        if ($withdrawfrom == 'referral') {
                            $dataq['referral_income'] = number_format($balance - $amount, 2, ".", "");
                        } else {
                            $dataq['winning'] = number_format($balance - $amount, 2, ".", "");
                        }

                        DB::connection('mysql2')->table('userbalance')->where('id', $findlastow->id)->update($dataq);
                        DB::connection('mysql2')->table('withdraw')->insert($data);
                        if ($withdrawfrom == 'referral') {
                            $total_available_amt = $findlastow->balance + $dataq['referral_income'] + $findlastow->bonus + $findlastow->winning + $findlastow->extracash;
                        } else {
                            $total_available_amt = $findlastow->balance + $dataq['winning'] + $findlastow->bonus + $findlastow->referral_income + $findlastow->extracash;
                        }
                        // $total_available_amt = $findlastow->balance + $dataq['winning'] + $findlastow->bonus;
                        $transactionsdata['userid'] = $userid;
                        $transactionsdata['type'] = 'Amount Withdraw';
                        $transactionsdata['transaction_id'] = $data['withdraw_request_id'];
                        $transactionsdata['transaction_by'] = 'wallet';
                        $transactionsdata['amount'] = $amount;

                        $transactionsdata['paymentstatus'] = 'pending';
                        $transactionsdata['withdraw_amt'] = $amount;
                        $transactionsdata['bal_fund_amt'] = $bal_fund_amt;
                        if ($withdrawfrom == 'referral') {
                            $transactionsdata['bal_referral_amt'] = $dataq['referral_income'];
                            $transactionsdata['cons_referral'] = $amount;
                            $transactionsdata['bal_win_amt'] = $findlastow->winning;
                        } else {
                            $transactionsdata['bal_win_amt'] = $dataq['winning'];
                            $transactionsdata['cons_win'] = $amount;
                            $transactionsdata['bal_referral_amt'] = $findlastow->referral_income;
                        }
                        // $transactionsdata['bal_win_amt'] = $dataq['winning'];
                        // $transactionsdata['cons_win'] = $amount;
                        $transactionsdata['bal_bonus_amt'] = $bal_bonus_amt;
                        // $transactionsdata['bal_referral_amt'] = $bal_referral_amt;

                        $transactionsdata['total_available_amt'] = $total_available_amt;
                        DB::connection('mysql2')->table('transactions')->insert($transactionsdata);
                        $msgg['message'] = "Your request for  withdrawl amount of Rs " . $amount . " is sent successfully. You will  get info about it in between 24 to 48 Hours.";
                        $msgg['success'] = true;
                        return response()->json(array($msgg));
                    } else {
                        $msgg['message'] = "You can withdraw only " . $balance . " rupees.";
                        $msgg['success'] = false;
                        return response()->json(array($msgg));
                    }
                } else {
                    $msgg['message'] = "Invalid user id.";
                    $msgg['success'] = false;
                    return response()->json(array($msgg));
                    die;
                }
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // my withdrawl list //
    public function mywithdrawlist(Request $request)
    {
        try {
            $input = $request->all();
            $user = Helpers::isAuthorize($request);
            $userid = $user->id;
            unset($input['auth_key']);
            $Json = array();
            $withdrawdata = DB::connection('mysql')->table('withdraw')->where('user_id', $userid)->get();

            if (count($withdrawdata) > 0) {
                $i = 0;
                foreach ($withdrawdata as $val) {
                    $Json[$i]['id'] = $val->id;
                    $Json[$i]['user_id'] = $val->user_id;
                    $Json[$i]['withdrawfrom'] = $val->witdrawfrom;
                    $Json[$i]['withdrawto'] = $val->type;
                    $Json[$i]['withdrawtxnid'] = $val->withdraw_request_id;
                    $Json[$i]['withdrawl_date'] = date('d-M-y h:i:a', strtotime($val->created_at));
                    if ($val->approved_date != '0000-00-00') {
                        $Json[$i]['approved_date'] = $val->approved_date;
                    } else {
                        $Json[$i]['approved_date'] = "not available";
                    }
                    if ($val->status == 0) {
                        $Json[$i]['status'] = 'Pending';
                    } else {
                        $Json[$i]['status'] = 'Approved';
                    }
                    if ($val->comment == null) {
                        $Json[$i]['comment'] = "not available";
                    } else {
                        $Json[$i]['comment'] = $val->comment;
                    }
                    $Json[$i]['amount'] = round($val->amount, 2);

                    $i++;
                }
            }
            return response()->json($Json);
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }
    // process for add fund and transfer money to his bucket //
    public function requestprocess($paymentgatewayinfo)
    {
        $finduserinfo = DB::connection('mysql')->table('paymentprocess')
            ->where('paymentprocess.status', 'pending')
            ->where(
                function ($qq) use ($paymentgatewayinfo) {
                    $qq->where('orderid', $paymentgatewayinfo['txnid'])
                        ->orWhere('returnid', $paymentgatewayinfo['txnid'])
                        ->orWhere('orderid', $paymentgatewayinfo['returnid'])
                        ->orWhere('returnid', $paymentgatewayinfo['returnid']);
                }
            )
            ->join('registerusers', 'registerusers.id', '=', 'paymentprocess.userid')
            ->select('registerusers.email', 'registerusers.mobile', 'paymentprocess.orderid', 'paymentprocess.offerid', 'registerusers.team', 'paymentprocess.amount', 'paymentprocess.paymentmethod', 'registerusers.id as userid', 'paymentprocess.id', 'paymentprocess.status', 'registerusers.refer_id')
            ->first();

        if (!empty($finduserinfo)) {
            $getdata['amount'] = $finduserinfo->amount;
            $getdata['userid'] = $finduserinfo->userid;
            $getdata['paymentby'] = $finduserinfo->paymentmethod;
            $ds['returnid'] = $paymentgatewayinfo['txnid'];
            $ds['status'] = !empty($paymentgatewayinfo['status']) ? $paymentgatewayinfo['status'] : 'success';
            DB::connection('mysql2')->table('paymentprocess')->where('id', $finduserinfo->id)->update($ds);
            /* update balance in user bucket */
            $userdata = DB::connection('mysql')->table('userbalance')->where('user_id', $getdata['userid'])->first();
            if (!empty($userdata)) {
                $datainseert['user_id'] = $getdata['userid'];
                $datainseert['balance'] = $userdata->balance + $getdata['amount'];
                DB::connection('mysql2')->table('userbalance')->where('user_id', $getdata['userid'])->update($datainseert);
            }
            /* check for offers */
            if (!empty($finduserinfo->offerid)) {
                $userid = $finduserinfo->userid;
                $offer = DB::connection('mysql')->table('offers')->where('id', $finduserinfo->offerid)->first();

                if ($offer->bonus_type == 'per') {
                    $amountt = $finduserinfo->amount * ($offer->bonus / 100);
                } else {
                    $amountt = $offer->bonus;
                }
                $userdata = array();
                $userdata = DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
                if (!empty($userdata)) {
                    $datainseert['user_id'] = $userid;
                    if ($offer->prize_type == 'realcash') {
                        $datainseert['balance'] = $userdata->balance + $amountt;
                    } else {
                        $datainseert['bonus'] = $userdata->bonus + $amountt;
                    }

                    DB::connection('mysql2')->table('userbalance')->where('user_id', $userid)->update($datainseert);
                    $bal_bonus_amt = 0;
                    $bal_win_amt = 0;
                    $bal_fund_amt = 0;
                    $total_available_amt = 0;
                    $findlastow = DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
                    if (!empty($findlastow)) {
                        $total_available_amt = $findlastow->balance + $findlastow->winning + $findlastow->bonus;
                        $bal_fund_amt = $findlastow->balance;
                        $bal_win_amt = $findlastow->winning;
                        $bal_bonus_amt = $findlastow->bonus;
                    }
                    $transactionsdata = array();
                    $transactionsdata['transaction_id'] = (Helpers::settings()->short_name ?? '') . '-EBONUS-' . rand(1000, 9999);
                    $transactionsdata['transaction_by'] = Helpers::settings()->short_name ?? '';
                    $transactionsdata['userid'] = $userid;
                    $transactionsdata['type'] = 'special bonus';
                    $transactionsdata['amount'] = $amountt;
                    if ($offer->prize_type == 'realcash') {
                        $transactionsdata['addfund_amt'] = $amountt;
                    } else {
                        $transactionsdata['bonus_amt'] = $amountt;
                    }

                    $transactionsdata['paymentstatus'] = 'confirmed';
                    $transactionsdata['bal_fund_amt'] = $bal_fund_amt;
                    $transactionsdata['bal_win_amt'] = $bal_win_amt;
                    $transactionsdata['bal_bonus_amt'] = $bal_bonus_amt;
                    $transactionsdata['total_available_amt'] = $total_available_amt;
                    DB::connection('mysql2')->table('transactions')->insert($transactionsdata);
                    $myoo['user_id'] = $userid;
                    $myoo['offer_id'] = $offer->id;
                    DB::connection('mysql2')->table('used_offer')->insert($myoo);
                    $msg = $data21['title'] = 'You have got ₹' . $amountt . ' special bonus on ' . (Helpers::settings()->project_name ?? '') . ' app.';
                    DB::connection('mysql2')->table('notifications')->insert($data21);
                    $titleget = 'special bonus';
                    $result = Helpers::sendnotification($titleget, $msg, '', $userid, '');
                }
            }

            /* entry in transactions*/
            $trdata = array();
            $trdata['type'] = 'Cash added';
            $txnid = (Helpers::settings()->short_name ?? '') . '-ADD-' . time();
            $trdata['transaction_id'] = $txnid;
            $trdata['userid'] = $getdata['userid'];
            $trdata['amount'] = $getdata['amount'];
            $trdata['addfund_amt'] = $getdata['amount'];
            $trdata['transaction_by'] = $paymentgatewayinfo['paymentby'];
            $this->transactionentry($trdata);
            return 'success';
        }
    }
    // common function for entry in transactions //
    public function transactionentry($trdata)
    {
        if (!empty($trdata)) {
            /* find for total balance now */
            $bal_bonus_amt = 0;
            $bal_win_amt = 0;
            $bal_fund_amt = 0;
            $total_available_amt = 0;
            $findlastow = DB::connection('mysql')->table('userbalance')->where('user_id', $trdata['userid'])->first();
            if (!empty($findlastow)) {
                $total_available_amt = $findlastow->balance + $findlastow->winning + $findlastow->bonus;
                $bal_fund_amt = $findlastow->balance;
                $bal_win_amt = $findlastow->winning;
                $bal_bonus_amt = $findlastow->bonus;
            }
            /* entry in transactions  table now with full array details */
            $trdata['paymentstatus'] = 'confirmed';
            $trdata['bal_fund_amt'] = $bal_fund_amt;
            $trdata['bal_win_amt'] = $bal_win_amt;
            $trdata['bal_bonus_amt'] = $bal_bonus_amt;
            $trdata['total_available_amt'] = $total_available_amt;
            DB::connection('mysql2')->table('transactions')->insert($trdata);
        }
    }
    // api for andriod apis for cash add //
    public function addcash1(Request $request)
    {
        try {
            $input = $request->all();
            $user = Helpers::isAuthorize($request);
            $userid = $user->id;
            unset($input['auth_key']);
            $getdata['amount'] = $amount = floor($request->get('amount'));
            $getdata['userid'] = $userid;
            $getdata['paymentby'] = $request->get('paymentby');
            $getdata['txnid'] = $request->get('txnid');
            $getdata['returnid'] = $request->get('returnid');
            $loginsession = DB::connection('mysql')->table('registerusers')->where('id', $userid)->first();
            $data21['userid'] = $userid;
            $data21['seen'] = 0;
            $titleget = "payment done";
            $msg = $data21['title'] = 'You have added rupees ' . $amount . ' by ' . $request->get('paymentby');
            $totalamt = DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
            $total = 0;
            if (!empty($totalamt)) {
                $total = $totalamt->bonus + $totalamt->winning + $totalamt->balance;
            }
            $Json['success'] = true;
            $Json['total_amount'] = $total;
            $Json['message'] = 'payment done';
            return response()->json(array($Json));
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    // android add bucket process //
    public function requestaddcash(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $input = $request->all();
                $user = Helpers::isAuthorize($request);
                $id = $userid = $user->id;
                unset($input['auth_key']);
                $getdata['amount'] = $amount = floor($request->get('amount'));
                $getdata['userid'] = $userid;
                $getdata['paymentby'] = $request->get('paymentby');
                $type = $request->get('type');

                /* check for the user details */
                $loginsession = DB::connection('mysql')->table('registerusers')->where('id', $id)->first();
                if (!empty($loginsession)) {
                    if ($request->get('offerid')) {
                        $offerdata = DB::connection('mysql')->table('offers')->where('id', $request->get('offerid'))->select('*')->first();
                        if (!empty($offerdata)) {
                            $max = $offerdata->maxamount;
                            $min = $offerdata->minamount;
                            if ($max >= $amount && $min <= $amount) {
                                $paymentprocess['offerid'] = $request->get('offerid');
                            }
                        }
                    }
                    $paymentdata['amount'] = $amount;
                    $paymentdata['userid'] = $loginsession->id;
                    $paymentdata['username'] = $loginsession->username;
                    $paymentdata['mobile'] = $loginsession->mobile;
                    $paymentdata['email'] = $loginsession->email;
                    $paymentdata['paymentby'] = $request->get('paymentby');
                    $paymentprocess['amount'] = $amount;
                    $paymentprocess['userid'] = $loginsession->id;
                    $paymentprocess['paymentmethod'] = $request->get('paymentby');
                    $txnid = (Helpers::settings()->short_name ?? '') . '-add-' . time() . $loginsession->id;
                    $paymentprocess['orderid'] = $txnid;
                    $Json['orderId'] = '';
                    if ($request->get('paymentby') == 'razorpay') {
                        include app_path() . '/razorpay/razorpay-php/Razorpay.php';
                        include app_path() . '/razorpay/config.php';
                        $api = new Api($keyId, $keySecret);
                        $orderData = [
                            'receipt' => $txnid,
                            'amount' => $amount * 100,
                            'currency' => 'INR',
                            'payment_capture' => 1,
                        ];
                        $razorpayOrder = $api->order->create($orderData);
                        $razorpayOrderId = $razorpayOrder['id'];
                        $Json['orderId'] = $razorpayOrderId;
                        $paymentprocess['orderid'] = $razorpayOrderId;
                    }
                    if ($request->get('paymentby') == 'paykun') {
                        $Json['orderId'] = time() . $loginsession->id;
                    }

                    DB::connection('mysql2')->table('paymentprocess')->insert($paymentprocess);
                    $Json['success'] = true;
                    $Json['txnid'] = $txnid;
                    return response()->json($Json);
                } else {
                    $Json['success'] = false;
                    $Json['txnid'] = 0;
                    return response()->json($Json);
                }
            } else {
                $Json['message'] = 'Invalid Method';
                $Json['success'] = false;
                $Json['txnid'] = 0;
                return response()->json($Json);
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    public function getPaytmCheckSum(Request $request)
    {
        header("Pragma: no-cache");
        header("Cache-Control: no-cache");
        header("Expires: 0");
        // following files need to be included
        require_once(app_path() . '/paytm/PaytmChecksum.php');

        $paytmParams = array();
        $paytmParams["body"] = array(
            "requestType"   => "Payment",
            "mid"           => $_POST["MID"],
            "websiteName"   => "DEFAULT",
            "orderId"       => $_POST["ORDER_ID"],
            "callbackUrl"   => "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=" . $_POST["ORDER_ID"],
            "txnAmount"     => array(
                "value"     => $_POST["TXN_AMOUNT"],
                "currency"  => "INR",
            ),
            "userInfo"      => array(
                "custId"    => $_POST["CUST_ID"],
            ),
        );

        $checksum = Paytmdata::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "4Te3sP2xBBeqmw4G");

        $paytmParams["head"] = array(
            "signature" => $checksum
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=ivSHQk19601147882328&orderId=" . $_POST["ORDER_ID"] . "";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);

        return $response;
        // dd($response);
        // echo json_encode($response);
        die;
    }
    public function getnotification(request $request)
    {
        try {
            $user = Helpers::isAuthorize($request);
            $currentdate = date("Y-m-d");
            // $findallnotification = DB::connection('mysql')->table('notifications')->where('userid', $user->id)->whereDate('created_at', $currentdate)->orderBy('created_at', 'DESC')->get();
            // $findallnotifications = DB::connection('mysql')->table('notifications')->where('userid', $user->id)->whereDate('created_at', '!=', $currentdate)->get();
            $findallnotifications = DB::connection('mysql')->table('notifications')->where('userid', $user->id)->orderBy('created_at', 'DESC')->get();
            $Json = array();
            $i = 0;
            $j = 0;
            $count = 0;
            if (!empty($findallnotifications->toarray())) {
                foreach ($findallnotifications as $notifications) {

                    $Json['previous'][$j]['id'] = ucfirst($notifications->id);
                    $Json['previous'][$j]['message'] = $notifications->title;
                    $Json['previous'][$j]['seen'] = $notifications->seen;
                    $Json['previous'][$j]['created_at'] = date('Y-m-d', strtotime($notifications->created_at));
                    if ($notifications->seen == 0) {
                        $data['seen'] = 1;
                        DB::connection('mysql2')->table('notifications')->update($data);
                    }
                    // $data['seen'] = 1;
                    // DB::connection('mysql2')->table('notifications')->update($data);
                    $Json['previous'][$j]['success'] = true;
                    $j++;
                }
            }
            // if (!empty($findallnotification->toarray())) {
            //     foreach ($findallnotification as $notification) {

            //         $Json['today'][$i]['id'] = ucfirst($notification->id);
            //         $Json['today'][$i]['message'] = $notification->title;
            //         $Json['today'][$i]['seen'] = $notification->seen;
            //         $Json['today'][$i]['created_at'] = date('Y-m-d', strtotime($notification->created_at));
            //         if($notification->seen==0){
            //             $data['seen'] = 1;
            //             DB::connection('mysql2')->table('notifications')->update($data);
            //         }
            //         // $data['seen'] = 1;
            //         // DB::connection('mysql2')->table('notifications')->update($data);
            //         $Json['today'][$i]['success'] = true;
            //         $i++;
            //     }
            // }
            Helpers::setHeader(200);
            return response()->json(array($Json));
            die;
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    public function getreferuser(request $request)
    {
        try {
            $user = Helpers::isAuthorize($request);
            $geturl = Helpers::geturl();
            $findreferuser = DB::connection('mysql')->table('registerusers')->where('refer_id', $user->id)->select('registerusers.id', 'registerusers.username', 'registerusers.team', 'registerusers.image', 'registerusers.mobile', 'registerusers.email', 'registerusers.created_at')->get();
            $Json = array();
            $i = 0;
            $count = 0;
            if (!empty($findreferuser)) {
                foreach ($findreferuser as $referuser) {
                    $bonref = DB::connection('mysql')->table('bonus_refered')->where('userid', $referuser->id)->first();
                    if (!empty($bonref)) {
                        $amount = $bonref->amount;
                    } else {
                        $amount = 0;
                    }
                    $Json[$i]['id'] = $referuser->id;
                    if (!empty($referuser->username)) {
                        $Json[$i]['username'] = ucfirst($referuser->username);
                    } else {
                        $Json[$i]['username'] = ucfirst($referuser->team);
                    }
                    $Json[$i]['email'] = $referuser->email;
                    $Json[$i]['amount'] = $amount;
                    $Json[$i]['image'] = $referuser->image;
                    $Json[$i]['created_at'] = date('d M Y', strtotime($referuser->created_at));
                    $Json[$i]['success'] = true;
                    $i++;
                }
                Helpers::setHeader(200);
            } else {
                Helpers::setHeader(200);
                $Json[$i]['success'] = false;
            }
            return response()->json($Json);
            die;
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    public function sendlinktouser(Request $request)
    {
        try {
            Helpers::setHeader(200);
            Helpers::accessrules();
            $mobile = $request->get('mobile');
            if (!empty($mobile)) {
                $message = 'Welcome to ' . (Helpers::settings()->project_name ?? '') . ' - Most advanced & trusted fantasy cricket game! Select your best 11 or 5 players & Win now! Link : https://' . (Helpers::settings()->project_name ?? '') . '.com/apk/' . (Helpers::settings()->project_name ?? '') . '.apk';
                Helpers::sendTextSmsNew($message, $mobile);
                $json['success'] = true;
            } else {
                $json['success'] = false;
            }
            return response()->json(array($json));
            die;
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    //   Cashfree Webhook
    public function webhook_detail(Request $request)
    {
        // Get Request data
        $input = $request->all();
        if (!empty($input)) {

            $data['data'] = json_encode($input);

            $lastid = DB::connection('mysql2')->table('payment_data')->insertGetId($data);
            $data = DB::connection('mysql')->table('payment_data')->where('id', $lastid)->first();
            LOG::info('entered');

            $input = json_decode($data->data);
            // LOG::info($input);
            $orderid = $input->orderId; # we need these
            $amount = $input->orderAmount; # we need these
            $returnid = $input->referenceId; # we need these
            $status = $input->txStatus; # we need these
            $paymentby = $input->paymentMode; # we need these
            $txMsg = $input->txMsg;
            $txTime = $input->txTime;
            $signature = $input->signature;
            $daaata = $orderid . $amount . $returnid . $status . $paymentby . $txMsg . $txTime;

            $secretkey = '2c182e6b42f9c1de8b3a59ac88a70ba95fa6985f';

            $hash_hmac = hash_hmac('sha256', $daaata, $secretkey, true);
            $computedSignature = base64_encode($hash_hmac);

            if ($signature == $computedSignature) {
                $txStatus = $input->txStatus;
                LOG::info($txStatus);

                if ($txStatus == 'SUCCESS') {
                    $orderid = $input->orderId;
                    $amount = $input->orderAmount;
                    $referenceId = $input->referenceId;
                    $paymentMode = $input->paymentMode;
                    $txTime = $input->txTime;
                    $signature = $input->signature;
                    $checkdata = DB::connection('mysql')->table('paymentprocess')->where('orderid', $orderid)->first();

                    if (!empty($checkdata)) {
                        $uid = $userid = $checkdata->userid;
                        $pstatus = $checkdata->status;
                        if ($pstatus == "pending") {
                            $getdata['amount'] = $amount = floor($amount);
                            $getdata['userid'] = $uid;
                            $getdata['returnid'] = $referenceId;
                            $paymentgatewayinfo['amount'] = $getdata['amount'];
                            $paymentgatewayinfo['txnid'] = $orderid;
                            $paymentgatewayinfo['paymentby'] = $paymentMode;
                            $paymentgatewayinfo['returnid'] = $referenceId;
                            $returnamount = $this->requestprocess($paymentgatewayinfo);
                            if ($returnamount == 'success') {
                                LOG::info($returnamount);
                                $this->giveReferBonus($userid);
                                $data21['userid'] = $userid;
                                $data21['seen'] = 0;
                                $titleget = "payment done";
                                $msg = $data21['title'] = 'You have added ₹ ' . $paymentgatewayinfo['amount'] . ' by ' . $paymentMode;
                                DB::connection('mysql2')->table('notifications')->insert($data21);
                                $result = Helpers::sendnotification($titleget, $msg, '', $userid);
                                $totalamt = DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
                                $total = 0;
                                if (!empty($totalamt)) {
                                    $total = $totalamt->bonus + $totalamt->winning + $totalamt->balance;
                                }
                                DB::connection('mysql2')->table('payment_data')->where('id', $data->id)->delete();
                                $Json['status'] = 1;
                                $Json['success'] = true;
                                $Json['total_amount'] = $total;
                                $Json['msg'] = 'payment done';
                                if (!empty($request->get('type') == 'IOS')) {
                                    return response()->json(array($Json));
                                } else {
                                    return response()->json(array($Json));
                                }
                            } else {
                                $Json['status'] = 0;
                                $Json['success'] = false;
                                $Json['msg'] = 'payment failed';
                                if (!empty($request->get('type') == 'IOS')) {
                                    return response()->json(array($Json));
                                } else {
                                    return response()->json(array($Json));
                                }
                            }
                            $json['status'] = true;
                            return response()->json(array($json));
                        }
                    } else {
                        $json['status'] = false;
                        $json['msg'] = 'Data not avilable!...';
                        return response()->json(array($json));
                    }
                }
            }
        }
        http_response_code(200);
    }

    public function webhook_detail_payu(Request $request)
    {

        // Get Request data
        $input = $request->all();
        Log::info($input);
        // dd('aj');
        if (!empty($input)) {
            $data['data'] = json_encode($input);

            $lastid = DB::connection('mysql2')->table('payment_data')->insertGetId($data);
            $data = DB::connection('mysql')->table('payment_data')->where('id', $lastid)->first();

            $input = json_decode($data->data);
            // dd($input);
            $orderid = $input->txnid; # we need these
            $amount = $input->amount; # we need these
            $status = $input->status; # we need these
            $paymentby = $input->mode; # we need these
            $customerName = $input->firstname;
            $customerEmail = $input->email;
            $customerPhone = $input->phone;
            $productInfo = $input->productinfo;
            $paymentId = $input->mihpayid;
            $bankRefNum = $input->bank_ref_no;
            // $txTime = $input->txTime;
            $signature = $input->hash;

            $key = '';

            $salt = '';



            if (isset($input->additionalCharges) && $input->additionalCharges == null) {
                $additionalCharges = $input->additionalCharges;
                $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $customerEmail . '|' . $customerName . '|' . $productInfo . '|' . $amount . '|' . $paymentId . '|' . $key;
            } else {
                $retHashSeq = $salt . '|' . $status . '|||||||||||' . $customerEmail . '|' . $customerName . '|' . $productInfo . '|' . $amount . '|' . $paymentId . '|' . $key;
            }
            $hash = hash("sha512", $retHashSeq);




            dd($hash);

            if ($signature == $hash) {
                $txStatus = $input->status;

                if ($txStatus == 'success') {
                    $orderid = $input->txnid;
                    $amount = $input->amount;
                    $referenceId = $input->mihpayid;
                    $paymentMode = $input->mode;
                    // $txTime = $input->txTime;
                    $signature = $input->hash;
                    $checkdata = DB::connection('mysql')->table('paymentprocess')->where('orderid', $orderid)->first();

                    if (!empty($checkdata)) {
                        $uid = $userid =  $checkdata->userid;
                        $pstatus = $checkdata->status;
                        if ($pstatus == "pending") {
                            $getdata['amount'] = $amount = floor($amount);
                            $getdata['userid'] = $uid;
                            $getdata['returnid'] = $referenceId;
                            $paymentgatewayinfo['amount'] = $getdata['amount'];
                            $paymentgatewayinfo['txnid'] = $orderid;
                            $paymentgatewayinfo['paymentby'] = $paymentMode;
                            $paymentgatewayinfo['returnid'] = $referenceId;
                            $returnamount = $this->requestprocess($paymentgatewayinfo);

                            if ($returnamount == 'success') {
                                $data21['userid'] = $userid;
                                $data21['seen'] = 0;
                                $titleget = "payment done";
                                $msg  =  $data21['title'] = 'You have added ₹ ' . $paymentgatewayinfo['amount'] . ' by ' . $paymentMode;
                                DB::connection('mysql2')->table('notifications')->insert($data21);
                                $result = Helpers::sendnotification($titleget, $msg, '', $userid);
                                $totalamt = DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
                                $total = 0;
                                if (!empty($totalamt)) {
                                    $total = $totalamt->bonus + $totalamt->winning + $totalamt->balance;
                                }
                                DB::connection('mysql2')->table('payment_data')->where('id', $data->id)->delete();
                                $Json['status'] = 1;
                                $Json['success'] = true;
                                $Json['total_amount'] = $total;
                                $Json['msg'] = 'payment done';
                                if (!empty($request->get('type') == 'IOS')) {
                                    return response()->json(array($Json));
                                } else {
                                    return response()->json(array($Json));
                                }
                            } else {
                                $Json['status'] = 0;
                                $Json['success'] = false;
                                $Json['msg'] = 'payment failed';
                                if (!empty($request->get('type') == 'IOS')) {
                                    return response()->json(array($Json));
                                } else {
                                    return response()->json(array($Json));
                                }
                            }
                            $json['status'] = true;
                            return response()->json(array($json));
                        }
                    } else {
                        $json['status'] = false;
                        $json['msg'] = 'Data not avilable!...';
                        return response()->json(array($json));
                    }
                }
            }
        }
        http_response_code(200);
    }

    public function paytm_webhook(Request $request)
    {
        $input = $request->all();

        file_put_contents(storage_path() . '/logs/paytm_logs/index.log', PHP_EOL . ' paytm_webhook_start', FILE_APPEND);
        if (!empty($input)) {

            $data['data'] = json_encode($input);

            file_put_contents(storage_path() . '/logs/paytm_logs/index.log', PHP_EOL . ' ' . $data['data'], FILE_APPEND);

            $lastid = DB::connection('mysql2')->table('payment_data_paytm')->insertGetId($data);
            $datas = DB::connection('mysql')->table('payment_data_paytm')->where('id', $lastid)->first();

            $input = json_decode($datas->data);

            $txStatus = $input->STATUS;

            if ($txStatus == 'TXN_SUCCESS') {

                file_put_contents(storage_path() . '/logs/paytm_logs/index.log', PHP_EOL . ' at txn_success', FILE_APPEND);

                $orderid = $input->ORDERID;
                $amount = $input->TXNAMOUNT;
                $returnid = $input->TXNID;
                $TXNID = $input->ORDERID;
                $PAYMENTMODE = $input->PAYMENTMODE;

                if (!empty($orderid)) {

                    file_put_contents(storage_path() . '/logs/paytm_logs/index.log', PHP_EOL . ' orderid', FILE_APPEND);

                    $checkdata = DB::connection('mysql')->table('paymentprocess')->where('orderid', $orderid)->first();
                    if (!empty($checkdata)) {
                        $uid =  $checkdata->userid;
                        $pstatus = $checkdata->status;
                        if ($pstatus == "pending") {
                            $userid = $uid;
                            $getdata['amount'] = $amount = floor($amount);
                            $getdata['userid'] = $uid;
                            $getdata['returnid'] = $returnid;
                            $loginsession = DB::connection('mysql')->table('registerusers')->where('id', $uid)->first();

                            if (!empty($loginsession)) {
                                $paymentdata['amount'] = $amount;
                                $paymentdata['userid'] = $loginsession->id;
                                $paymentdata['username'] = $loginsession->username;
                                $paymentdata['mobile'] = $loginsession->mobile;
                                $paymentdata['email'] = $loginsession->email;
                                $paymentdata['paymentby'] = $PAYMENTMODE;
                                Session::put('askforpayment', $paymentdata);
                            }
                            $paymentgatewayinfo['amount'] = $getdata['amount'];
                            $paymentgatewayinfo['txnid'] = $TXNID;
                            $paymentgatewayinfo['paymentby'] = $input->PAYMENTMODE;
                            $paymentgatewayinfo['returnid'] = $returnid;

                            $returnamount = $this->requestprocess($paymentgatewayinfo);

                            if ($returnamount == 'success') {

                                file_put_contents(storage_path() . '/logs/paytm_logs/index.log', PHP_EOL . ' at success', FILE_APPEND);

                                $data21['userid'] = $userid;
                                $data21['seen'] = 0;
                                $titleget = "payment done";
                                $msg = $data21['title'] = 'You have added rupees ' . $paymentgatewayinfo['amount'] . ' by ' . $request->get('paymentby');
                                DB::connection('mysql2')->table('notifications')->insert($data21);
                                $result = Helpers::sendnotification($titleget, $msg, '', $userid);
                                $totalamt = DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
                                $total = 0;

                                if (!empty($totalamt)) {
                                    $total = $totalamt->bonus + $totalamt->winning + $totalamt->balance;
                                }

                                DB::connection('mysql2')->table('payment_data')->where('id', $datas->id)->delete();

                                $Json['status'] = 1;
                                $Json['success'] = true;
                                $Json['total_amount'] = $total;
                                $Json['msg'] = 'payment done';
                                if (!empty($request->get('type') == 'IOS')) {
                                    return response()->json(array($Json));
                                } else {
                                    return response()->json(array($Json));
                                }
                            } else {
                                $Json['status'] = 0;
                                $Json['success'] = false;
                                $Json['msg'] = 'payment failed';
                                if (!empty($request->get('type') == 'IOS')) {
                                    return response()->json(array($Json));
                                } else {
                                    return response()->json(array($Json));
                                }
                            }
                            $json['status'] = true;
                            return response()->json(array($json));
                        }
                    } else {
                        $json['status'] = false;
                        $json['msg'] = 'Data not avilable!...';
                        return response()->json(array($json));
                    }
                }
            }
        }

        http_response_code(200);
    }

    # webhook for cashfree payout

    // public function popup_notify(Request $request){
    //     try{
    //         Helpers::setHeader(200);
    //         $geturl = Helpers::geturl();
    //         $json = array();
    //         $getpopup = DB::connection('mysql')->table('popup_notification')->first();
    //         if(!empty($getpopup)){
    //                 $json['id'] = $getpopup->id;
    //                 $json['title'] = $getpopup->title;
    //                 $json['image'] = $geturl.'public/popup_notify/'.$getpopup->image;
    //                 return response()->json(array($json));die;
    //         }else{
    //             $Json['success'] = false;
    //             $Json['message'] = 'Data not available';
    //             return response()->json(array($json));die;
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
    //     }
    // }

    public function popup_notify(Request $request)
    {
        try {
            Helpers::setHeader(200);
            $geturl = Helpers::geturl();
            $json = array();
            $getpopup = DB::connection('mysql')->table('popup_notification')->first();
            if (!empty($getpopup)) {
                $json['id'] = $getpopup->id;
                $json['title'] = $getpopup->title;
                $json['image'] = $geturl . 'popup_notify/' . $getpopup->image;
                return response()->json(array($json));
                die;
            } else {
                $Json['success'] = false;
                $Json['message'] = 'Data not available';
                return response()->json($json);
                die;
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    public function helpdeskmaiL(Request $request)
    {
        $user = Helpers::isAuthorize($request);
        $geturl = Helpers::geturl();
        $input = $request->all();
        if (!empty($input['fname'])) {
            if (!empty($input['contact'])) {
                if (!empty($input['email'])) {
                    if (!empty($input['reason'])) {
                        if (!empty($input['message'])) {
                            // $msg= Helpers::Mailbody($input['message'],$input['fname'],$input['lname'],$input['contact']);
                            $datamessage['email'] = $input['email'];
                            // $datamessage['content'] = $msg;
                            $subject = $input['reason'];
                            $supportmail = 'Believer11official@Gmail.Com';

                            //====================Mail body=======================================
                            $emailsubject = 'Believer11 - Feedback & Support';
                            $msgcontent = '<p style="padding-left: 23px;"><strong>Name: - </strong> ' . $input['fname'] . ' ' . $input['lname'] . '</p>';
                            $msgcontent .= '<p style="padding-left: 23px;"><strong> Email: -  </strong> ' . $input['email'] . '</p>';
                            $msgcontent .= '<p style="padding-left: 23px;"><strong> Contact: -  </strong> ' . $input['contact'] . '</p>';
                            $msgcontent .= '<p style="padding-left: 23px;"><strong> Reason of Enquiry: -  </strong> ' . $input['reason'] . '</p>';
                            $msgcontent .= '<p style="padding-left: 23px;"><strong> Message: -  </strong> ' . $input['message'] . '</p>';
                            // $msg = Helpers::HelpdeskMail($msgcontent);
                            $msg = Htmlhelpersemail::helpdesk_email($input);
                            //====================Mail body END =======================================

                            Helpers::mailsentFormat($supportmail, $subject, $msg);
                            $json['status'] = true;
                            $json['message'] = 'Feedback send Successfully';
                            return response()->json($json);
                            die;
                        }
                    }
                }
            }
        }
    }




    public function getnews(Request $request)
    {
        try {
            $user = Helpers::isAuthorize($request);
            $geturl = Helpers::geturl();

            if (!empty($request->get('id'))) {

                $news = DB::connection('mysql')->table('news')->where('id', $request->get('id'))->get();
            } elseif (!empty($request->get('type'))) {

                $news = DB::connection('mysql')->table('news')->where('type', $request->get('type'))->get();
            } else {
                $news = DB::connection('mysql')->table('news')->get();
            }
            $Json = array();
            $i = 0;
            $count = 0;
            if (!empty($news)) {
                foreach ($news as $getnews) {
                    $Json[$i]['id'] = $getnews->id;
                    $Json[$i]['title'] = $getnews->title;
                    $Json[$i]['type'] = $getnews->type;
                    $Json[$i]['description'] = $getnews->description;
                    $Json[$i]['image'] = Helpers::geturl() . 'public/' . $getnews->image;
                    $Json[$i]['created_at'] = date('d M Y', strtotime($getnews->created_at));
                    $Json[$i]['success'] = true;
                    $i++;
                }
                Helpers::setHeader(200);
            } else {
                Helpers::setHeader(200);
                $Json[$i]['success'] = false;
            }
            return response()->json($Json);
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }



    public function affiliate_program(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                Helpers::setHeader(200);
                $geturl = Helpers::geturl();
                $input = $request->all();
                $validator = Validator::make($input, [
                    'fullname' => 'required',
                    'mobile' => 'required',
                    'email' => 'required',
                ]);
                if ($validator->fails()) {
                    $error = $this->validationHandle($validator->messages());
                    return response()->json(array(['success' => false, 'message' => $error]));
                } else {
                    $user = Helpers::isAuthorize($request);
                    $data = array();
                    $input = $request->all();
                    /* get all the params */
                    $id = $user->id;
                    $data['userid'] = $id;
                    $data['full_name'] = strtoupper($request->get('fullname'));
                    $data['email'] = strtoupper($request->get('email'));
                    $data['mobile'] = $request->get('mobile');
                    $data['city'] = strtoupper($request->get('city'));
                    $data['state'] = strtoupper($request->get('state'));
                    $data['channel_type'] = strtoupper($request->get('channel_type'));
                    $data['channel_name'] = strtoupper($request->get('channel_name'));
                    $data['channel_url'] = strtoupper($request->get('channel_url'));

                    $findplannumber = DB::connection('mysql')->table('affiliate_program')->where('mobile', $data['mobile'])->where('userid', $data['userid'])->first();
                    if (!empty($findplannumber)) {
                        $msgg['success'] = false;
                        $msgg['message'] = 'User is already exist.';
                        return response()->json(array($msgg));
                    }

                    $findexist = DB::connection('mysql')->table('affiliate_program')->where('userid', $id)->first();
                    if (!empty($findexist)) {
                        DB::connection('mysql2')->table('affiliate_program')->where('id', $findexist->id)->update($data);
                    } else {
                        DB::connection('mysql2')->table('affiliate_program')->insert($data);
                    }
                    $msgg['success'] = true;
                    $msgg['message'] = 'Affiliate Details Added Successfully!';
                    echo json_encode(array($msgg));
                }
            } else {
                $msgg['success'] = false;
                $msgg['message'] = 'Unauthorized request';
                echo json_encode(array($msgg));
                die;
            }
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    # webhook for cashfree payout
    public function cashfree_payout_webhook()
    {
        date_default_timezone_set('Asia/Kolkata');
        $data = $_POST;
        $signature = $_POST["signature"];
        unset($data["signature"]); // $data now has all the POST parameters except signature
        ksort($data); // Sort the $data array based on keys
        $postData = "";
        foreach ($data as $key => $value) {
            if (strlen($value) > 0) {
                $postData .= $value;
            }
        }
        // $hash_hmac = hash_hmac('sha256', $postData, $clientSecret, true);
        $hash_hmac = hash_hmac('sha256', $postData, '29e3ad3c01376317b4783fe806538d17ae5b501d', true);

        $dta = array();
        $dta['data'] = implode('', $_POST);
        DB::connection('mysql2')->table('withdraw_webhook')->insert($dta);

        // Use the clientSecret from the oldest active Key Pair.
        $computedSignature = base64_encode($hash_hmac);

        if ($signature == $computedSignature) {
            // Proceed based on $event

            // $withdraw_data = DB::connection('mysql')->table('withdraw')->where('transfer_id', $_POST['referenceId'])->first();
            $withdraw_data = DB::connection('mysql')->table('withdraw')
                ->where('withdraw.transfer_id', $_POST['transferId'])
                ->join('registerusers', 'registerusers.id', '=', 'withdraw.user_id')
                ->select('user_id', 'registerusers.email', 'registerusers.team', 'withdraw_request_id', 'withdraw.*')
                ->first();

            if (!empty($withdraw_data)) {

                if ($_POST['event'] == 'TRANSFER_SUCCESS' || $_POST['event'] == 'TRANSFER_ACKNOWLEDGED') {

                    $upstatus['status'] = 1;
                    $upstatus['comment'] = $_POST['event'];
                    $upstatus['referenceid'] = $_POST['referenceId'];
                    $upstatus['approved_date'] = date('Y-m-d H:i:s');
                    DB::connection('mysql2')->table('withdraw')->where('transfer_id', $withdraw_data->transfer_id)->update($upstatus);

                    $gst = array();
                    $gst['status']  = 1;
                    DB::connection('mysql2')->table('gst_deduction')->where('withdrawid', $withdraw_data->id)->update($gst);
                    //update transaction
                    $transactionsdataup = array();
                    $transactionsdataup['paymentstatus'] = 'confirmed';
                    $transactionidd = DB::connection('mysql2')->table('transactions')->where('transaction_id', $withdraw_data->withdraw_request_id)->update($transactionsdataup);

                    # mail send

                    $datamessage['email'] = $withdraw_data->email;
                    $datamessage['subject'] = 'Withdraw Request approved';
                    $datamessage['content'] = '<p><strong>Hello ' . ucwords($withdraw_data->team) . ' </strong></p>';
                    $datamessage['content'] .= '<p>Your withdrawal request of ₹' . $withdraw_data->amount . ' has been approved successfully.</p>';
                    //$content.='<p><strong>'.$input['comment'].'</strong></p>';
                    $datamessage['content'] .= '<p></p>';
                    $content = Helpers::Mailbody1($datamessage['content'], $datamessage['email']);
                    Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $content);

                    $notificationdata['userid'] = $withdraw_data->user_id;
                    $notificationdata['title'] = 'Withdraw Request Approved successfully of amount ₹' . $withdraw_data->amount;
                    DB::connection('mysql2')->table('notifications')->insert($notificationdata);

                    $titleget = 'Withdrawal Request Approved!';
                    Helpers::sendnotification($titleget, $notificationdata['title'], '', $withdraw_data->user_id);
                    # mail send

                } else if ($_POST['event'] == 'TRANSFER_FAILED') {

                    $upstatus['status'] = 2;
                    $upstatus['comment'] = $_POST['reason'];
                    $upstatus['approved_date'] = date('Y-m-d H:i:s');

                    DB::connection('mysql2')->table('withdraw')->where('transfer_id', $withdraw_data->transfer_id)->update($upstatus);
                    // DB::connection('mysql')->table('gst_deduction')->where('withdrawid',$withdraw_data->id)->delete();
                    DB::connection('mysql2')->table('userbalance')->where('user_id', $withdraw_data->user_id)
                        ->increment('winning', $withdraw_data->amount);

                    //update transaction
                    $transactionsdataup = array();
                    $transactionsdataup['paymentstatus'] = 'failed';
                    $transactionidd = DB::connection('mysql2')->table('transactions')->where('transaction_id', $withdraw_data->withdraw_request_id)->update($transactionsdataup);
                } else {

                    $upstatus['status'] = 2;
                    $upstatus['comment'] = !empty($_POST['reason']) ? $_POST['reason'] : $_POST['event'];
                    $upstatus['approved_date'] = date('Y-m-d H:i:s');
                    DB::connection('mysql2')->table('withdraw')->where('transfer_id', $withdraw_data->transfer_id)->update($upstatus);
                    // DB::connection('mysql')->table('gst_deduction')->where('withdrawid',$withdraw_data->id)->delete();
                    DB::connection('mysql2')->table('userbalance')
                        ->where('user_id', $withdraw_data->user_id)
                        ->increment('winning', $withdraw_data->amount);

                    //update transaction
                    $transactionsdataup = array();
                    $transactionsdataup['paymentstatus'] = 'failed';
                    $transactionidd = DB::connection('mysql2')->table('transactions')->where('transaction_id', $withdraw_data->withdraw_request_id)->update($transactionsdataup);
                }
            }
        } else {
            // Reject this call
        }
    }

    public function getaffliateleaderboard(request $request)
    {
        try {
            $user = Helpers::isAuthorize($request);
            $geturl = Helpers::geturl();
            $youtuber = DB::connection('mysql')->table('registerusers')->where('type', 'youtuber')->get();
            $Json = array();
            $i = 0;
            $count = 0;
            if (!empty($youtuber)) {
                foreach ($youtuber as $youtuberuser) {
                    $total_earned = DB::connection('mysql')->table('youtuber_bonus')->where('userid', $youtuberuser->id)->sum('amount');
                    $Json[$i]['id'] = $youtuberuser->id;
                    $totalrefered = DB::connection('mysql')->table('registerusers')->where('refer_id', $youtuberuser->id)->count();
                    if (!empty($youtuberuser->username)) {
                        $Json[$i]['username'] = ucfirst($youtuberuser->username);
                    } else {
                        $Json[$i]['username'] = ucfirst($youtuberuser->team);
                    }
                    $Json[$i]['amount'] = number_format($total_earned, 2, ".", "");
                    $Json[$i]['totalrefered'] = $totalrefered;
                    $Json[$i]['success'] = true;
                    $i++;
                }
                Helpers::setHeader(200);
            } else {
                Helpers::setHeader(200);
                $Json[$i]['success'] = false;
            }
            // array_multisort(array_column($Json, 'amount'), SORT_DESC);
            return response()->json(collect($Json)->sortByDesc('amount')->values());
            die;
        } catch (\Exception $e) {
            return response()->json(array(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    public function affiliate_details(Request $request)
    {

        $user = Helpers::isAuthorize($request);
        $Uid = $user->id;
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $check_youtuberstatus = DB::connection('mysql')->table('registerusers')->where('id', $Uid)->where('type', 'youtuber')->first();

        if (!empty($check_youtuberstatus)) {
            $tm = collect();
            $get_referuser = DB::connection('mysql')->table('registerusers')->where('refer_id', $Uid)->get('id');

            if (!empty($get_referuser->toArray())) {
                $i = 0;
                $totalamount = 0;
                $totalmatch = 0;
                $Total_leauge = 0;
                $winning_match = 0;
                $winning_amt = 0;
                $Total_leauge1 = 0;

                foreach ($get_referuser as $get_referusers) {

                    $totalDeposit = DB::connection('mysql')->table('transactions')->where('userid', $get_referusers->id)->where('type', 'Cash added')->whereBetween('created_at', [$startdate, $enddate])->sum('amount');
                    $totalamount = $totalamount + $totalDeposit;

                    $Total_match1 = DB::connection('mysql')->table('joinedleauges')->where('userid', $get_referusers->id)->whereBetween('created_at', [$startdate, $enddate])->get();

                    $Total_match = $Total_match1->groupBy('matchkey')->count();
                    $Total_leauge1 = $Total_match1->count();

                    $winning_match1 = DB::connection('mysql')->table('finalresults')->where('userid', $get_referusers->id)->whereBetween('created_at', [$startdate, $enddate])->count();
                    $winning_match = $winning_match + $winning_match1;

                    $winning_amt1 = DB::connection('mysql')->table('youtuber_bonus')->where('userid', $Uid)->whereBetween('created_at', [$startdate, $enddate])->where('fromid', $get_referusers->id)->sum('amount');
                    $winning_amt = $winning_amt + $winning_amt1;
                    $totalmatch = $totalmatch + $Total_match;
                    $Total_leauge = $Total_leauge + $Total_leauge1;

                    $i++;
                }

                $json['Total_match'] = $Total_leauge1;
                $json['Total_teamjoin'] = $Total_leauge;
                $json['Total_deposit'] = $totalamount;
                $json['Total_winning'] = $winning_match;
                $json['affiliate_income'] = $winning_amt;
                return response()->json(array($json));
            } else {
                $json['Total_match'] = 0;
                $json['Total_teamjoin'] = 0;
                $json['Total_deposit'] = 0;
                $json['Total_winning'] = 0;
                $json['affiliate_income'] = 0;
                return response()->json(array($json));
            }
        } else {
            $json['Total_match'] = 0;
            $json['Total_teamjoin'] = 0;
            $json['Total_deposit'] = 0;
            $json['Total_winning'] = 0;
            $json['affiliate_income'] = 0;
            return response()->json(array($json));
        }
    }
}
