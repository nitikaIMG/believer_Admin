<?php

namespace App\Http\Controllers\api;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use Carbon;
use DB;
use Illuminate\Http\Request;
use Log;

class CardApiController extends Controller
{

    // common function to sort the teams//
    public function multid_sort($arr, $index)
    {
        $b = array();
        $c = array();

        foreach ($arr as $key => $value) {
            $b[$key] = $value[$index];
        }
        arsort($b);
        foreach ($b as $key => $value) {
            $c[] = $arr[$key];
        }
        return $c;
    }

    public function getAllcardContests(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $id = $user->id;
        unset($input['auth_key']);
        $pricecardarr = array();
        $allchallenges = DB::connection('mysql')->table('cardcontest')->get();
        $Json = array();
        $i = 0;
        $a = $allchallenges->toArray();
        if (!empty($a)) {
            foreach ($allchallenges as $challenege) {
                if ($challenege->maximum_user >= 0) {
                    $Json[$i]['id'] = $challenege->id;
                    $Json[$i]['name'] = 'Win Rs.' . $challenege->win_amount;
                    $Json[$i]['entryfee'] = $challenege->entryfee;
                    $Json[$i]['offerentryfee'] = $challenege->offerentryfee;
                    $Json[$i]['win_amount'] = $challenege->win_amount;
                    $Json[$i]['maximum_user'] = $challenege->maximum_user;
                    $Json[$i]['success'] = true;
                    $Json[$i]['is_bonus'] = $challenege->is_bonus;
                    $Json[$i]['bonus_percentage'] = $challenege->bonus_percentage;
                    $i++;
                } else {
                    $Json[$i]['success'] = false;
                }
            }
        } else {
            $Json = [];
        }
        return response()->json($Json);
        die;
    }

    public function joincardleauge(Request $request)
    {
        // die;
        Helpers::setHeader(200);
        Helpers::timezone();
        $user = Helpers::isAuthorize($request);
        if ($request->isMethod('post')) {
            // DB::beginTransaction();
            $input = $request->all();
            $geturl = Helpers::geturl();
            $user = Helpers::isAuthorize($request);
            $userid =  $loginUserid = $user->id;
            unset($input['auth_key']);
            $newchallengeid = $request->get('challengeid');
            $challengedata = DB::connection('mysql')->table('cardcontest')->where('id', $newchallengeid)->first();
            $checkchallenge = DB::connection('mysql')->table('cardchallenges')->where('challenge_id', $newchallengeid)->where('status', 'opened')->where('entryfee', $challengedata->entryfee)->where('win_amount', $challengedata->win_amount)->where('team1id', '!=', $request->teamid)->where('team2id',0)->first();
            if (empty($checkchallenge)) {
                $data1['challenge_id'] = $newchallengeid;
                $data1['entryfee'] = number_format($challengedata->entryfee, 0, ".", "");
                $data1['win_amount'] = number_format($challengedata->win_amount, 0, ".", "");
                $data1['is_bonus'] = $challengedata->is_bonus;
                $data1['bonus_percentage'] = $challengedata->bonus_percentage;
                $data1['maximum_user'] = $challengedata->maximum_user;
                $data1['user1_socketid'] = $request->socket_id;
                $data1['team1id'] = $request->teamid;
                $data1['status'] = 'opened';
                $data1['joinedusers'] = 1;
                $data1['user_id1'] = $userid;
                $challengeid = DB::connection('mysql2')->table('cardchallenges')->insertGetId($data1);
            } else {
                $data2['status'] = 'closed';
                $data2['user_id2'] = $userid;
                $data2['joinedusers'] = 2;
                $data2['user2_socketid'] = $request->socket_id;
                $data2['team2id'] = $request->teamid;
                DB::connection('mysql2')->table('cardchallenges')->where('id', $checkchallenge->id)->update($data2);
                $challengeid = $checkchallenge->id;
            }
            // $findjoinedleauges =  DB::connection('mysql')->table('joinedcardleauges')->where('challengeid', $challengeid)->where('userid', $userid)->first();
            $Json = array();
            //Generate random code//
            $refercode = $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $refercode = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < 4; $i++) {
                $refercode .= $characters[mt_rand(0, $max)];
            }
            $data['refercode'] = $datass['refercode'] = $refercode = (Helpers::settings()->short_name ?? '') . $refercode;
            //Check if match is closed or not//
            $findchallenge =  DB::connection('mysql')->table('cardchallenges')->where('id', $challengeid)->first();
            if (!empty($findchallenge)) {
                try {
                    //check for leauge closed or not //
                    $dataused = array();
                    $dataleft = array();
                    //start deduct money code for join leauge//
                    $finduserbalance =  DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
                    if (!empty($finduserbalance)) {
                        $totalbonus = 0;
                        $findentryfee = $findchallenge->entryfee;
                        /* find the current balance of users*/
                        $dataleft['bonus'] = $findbonusforuser = number_format($finduserbalance->bonus, 2, ".", "");
                        $dataleft['winning'] = number_format($finduserbalance->winning, 2, ".", "");
                        $dataleft['balance'] = number_format($finduserbalance->balance, 2, ".", "");
                        $dataleft['extracash'] = number_format($finduserbalance->extracash, 2, ".", "");
                        $dataleft['referral_income'] = number_format($finduserbalance->referral_income, 2, ".", "");
                        $totalbonus = 0;
                        $finduserbonus = 0;
                        if ($findchallenge->is_bonus == 1) {
                            $totalbonus = $finduserbonus = number_format($finduserbalance->bonus, 2, ".", "");
                        }
                        $usedbonus = 0;
                        $canusedbonus = 0;
                        $totalwining = $canusedwining = number_format($finduserbalance->winning, 2, ".", "");
                        $totalbalance = $canusedbalance = number_format($finduserbalance->balance, 2, ".", "");
                        $totalextracash = $canusedextracash = number_format($finduserbalance->extracash, 2, ".", "");
                        $totalreferral = $canusedferral = number_format($finduserbalance->referral_income, 2, ".", "");
                        $totbalan = number_format($finduserbalance->bonus + $finduserbalance->winning + $finduserbalance->balance + $finduserbalance->referral_income + $finduserbalance->extracash, 2, ".", "");
                        $findusablebalance = number_format($finduserbalance->balance + $finduserbalance->winning + $finduserbalance->referral_income + $finduserbalance->extracash, 2, ".", "");
                        $reminingfee = $findentryfee;
                        //start deduct money section//
                        if ($findchallenge->is_bonus == 1) {
                            $canu = 0;
                            $totalchalbonus = 0;
                            $totalchalbonus = ($findchallenge->bonus_percentage / 100) * $findentryfee;
                            if ($finduserbonus >= $totalchalbonus) {
                                $findusablebalance += $totalchalbonus;
                                $canu = $totalchalbonus;
                            } else {
                                $canusedd = $finduserbonus;
                                $findusablebalance += $canusedd;
                                $canu = $canusedd;
                            }
                            if ($findusablebalance < $findentryfee) {
                                //    Helpers::setHeader(200);
                                $Json['success'] = false;
                                $Json['message'] = 'insufficient balance';
                                echo json_encode($Json);
                                die;
                            }
                            if ($canu >= $findentryfee) {
                                $remainingbonus1 = $canu - $findentryfee;
                                $remainingbonus = $finduserbonus - $findentryfee;
                                $dataleft['bonus'] = number_format($remainingbonus, 2, ".", "");
                                $transactiondata['cons_bonus'] = $dataused['bonus'] = $findentryfee;
                                $reminingfee = 0;
                            } else {
                                $reminingfee = $findentryfee - $canu;
                                $remainingbonus = $finduserbonus - $canu;
                                $dataleft['bonus'] = number_format($remainingbonus, 2, ".", "");
                                $transactiondata['cons_bonus'] = $dataused['bonus'] = $canu;
                            }
                        }

                        if ($findusablebalance < $findentryfee) {
                            //    Helpers::setHeader(200);
                            $Json['success'] = false;
                            $Json['message'] = 'insufficient balance';
                            echo json_encode($Json);
                            die;
                        }
                        if ($reminingfee > 0) {
                            if ($canusedbalance >= $reminingfee) {
                                $reminingbalance = $canusedbalance - $reminingfee;
                                $dataleft['balance'] = number_format($reminingbalance, 2, ".", "");
                                $transactiondata['cons_amount'] = $dataused['balance'] = $reminingfee;
                                $reminingfee = 0;
                            } else {
                                $dataleft['balance'] = 0;
                                $reminingfee = $reminingfee - $canusedbalance;
                                $transactiondata['cons_amount'] = $dataused['balance'] = $canusedbalance;
                            }
                        }
                        if ($reminingfee > 0) {
                            if ($canusedextracash >= $reminingfee) {
                                $reminingextracash = $canusedextracash - $reminingfee;
                                $dataleft['extracash'] = number_format($reminingextracash, 2, ".", "");
                                $transactiondata['cons_extracash'] = $dataused['extracash'] = $reminingfee;
                                $reminingfee = 0;
                            } else {
                                $dataleft['extracash'] = 0;
                                $reminingfee = $reminingfee - $canusedextracash;
                                $transactiondata['cons_extracash'] = $dataused['extracash'] = $canusedextracash;
                            }
                        }
                        if ($reminingfee > 0) {
                            if ($canusedwining >= $reminingfee) {
                                $reminingwining = $canusedwining - $reminingfee;
                                $dataleft['winning'] = number_format($reminingwining, 2, ".", "");
                                $transactiondata['cons_win'] = $dataused['winning'] = $reminingfee;
                                $reminingfee = 0;
                            } else {
                                $dataleft['winning'] = 0;
                                $reminingfee = $reminingfee - $canusedwining;
                                $transactiondata['cons_win'] = $dataused['winning'] = $canusedwining;
                            }
                        }
                        if ($reminingfee > 0) {
                            if ($canusedferral >= $reminingfee) {
                                $reminingwining = $canusedferral - $reminingfee;
                                $dataleft['referral_income'] = number_format($reminingwining, 2, ".", "");
                                $transactiondata['cons_referral'] = $dataused['referral_income'] = $reminingfee;
                                $reminingfee = 0;
                            } else {
                                $dataleft['referral_income'] = 0;
                                $reminingfee = $reminingfee - $canusedferral;
                                $transactiondata['cons_referral'] = $dataused['referral_income'] = $canusedferral;
                            }
                        }

                        // find transaction id//
                        $tranid = (Helpers::settings()->short_name ?? '') . '-JL-' . $findchallenge->id . '-' . time() . rand(10, 99);
                        // to enter in joined leauges table//
                        $data['transaction_id'] = $datass['transaction_id'] = $tranid . '-' . $userid;
                        //insert leauge entry//

                        $result = DB::connection('mysql2')->insert("INSERT INTO `joinedcardleauges` ( `userid`, `challengeid`, `transaction_id`) SELECT " . $userid . " , " . $challengeid . ",'" . $data['transaction_id'] . "' FROM DUAL WHERE (SELECT COUNT(*) FROM joinedcardleauges WHERE challengeid=$challengeid) < " . $findchallenge->maximum_user);                       
                        $challenge_insert_id = DB::connection('mysql2')->select('SELECT LAST_INSERT_ID()');
                        if (!empty($result)) {
                            $getinsertid = $challenge_insert_id[0]->{"LAST_INSERT_ID()"};
                            // DB::connection('mysql2')->table('joinedcardleauges')->where('id', $getinsertid)->update($data);
                            if ($getinsertid != '0') {

                                $data['refercode'] = $datass['refercode'] = $refercode . '' . $getinsertid;
                                DB::connection('mysql2')->table('joinedcardleauges')->where('id', $getinsertid)->update($data);
                                $joinedusers =  DB::connection('mysql')->table('joinedcardleauges')->where('challengeid', $challengeid)->count();
                                $datass['joinid'] = $getinsertid;
                                DB::connection('mysql2')->table('joininfo')->insert($datass);
                                //entry in leauges transactions//
                                $dataused['matchkey'] = 0;
                                $dataused['user_id'] = $userid;
                                $dataused['challengeid'] = $challengeid;
                                $dataused['joinid'] = $getinsertid;
                                DB::connection('mysql2')->table('leaugestransactions')->insert($dataused);
                                //updatewallet table//
                                DB::connection('mysql2')->table('userbalance')->where('user_id', $userid)->update($dataleft);
                                $findnowamount =  DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
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
                                $transactiondata['bal_referral_amt'] = $findnowamount->referral_income;
                                $transactiondata['bal_extracash_amt'] = $findnowamount->extracash;
                                $transactiondata['transaction_id'] = $tranid . '-' . $userid;
                                DB::connection('mysql2')->table('transactions')->insert($transactiondata);
                            } 

                            if ($findchallenge->joinedusers > 0) {
                                DB::commit();
                                $ttlblance = $totbalan - $findentryfee;
                                $data21['userid'] = $userid;
                                $data21['seen'] = 0;
                                $titleget = "League Joined";
                                $msg = $data21['title'] = 'You have successfully joined league of Rs ' . $findentryfee;
                                DB::connection('mysql2')->table('notifications')->insert($data21);

                                //Helpers::setHeader(200);
                                $Json['message'] = 'League joined';
                                $Json['success'] = true;
                                $Json['totalbalance'] = $ttlblance;
                                $Json['challengeid'] = $challengeid;
                                $Json['joinedusers'] = $findchallenge->joinedusers;
                                $Json['refercode'] = $data['refercode'];
                                $userdetail =  DB::connection('mysql')->table('cardteams')->where('id', $request->teamid)->first();
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, 'https://believer11.com:4000/getJoinedContestUser');
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, "&newchallengeid=" . $newchallengeid . "&teamname=" . $userdetail->team);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $result = curl_exec($ch);
                                curl_close($ch);
                            } else {
                                // DB::rollBack();
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // DB::rollBack();
                    echo $e->getMessage();
                    // Log::info($e);
                    die;
                }
            } else {
                //Helpers::setHeader(200);
                $Json['message'] = 'no challenge by this challengeid';
                $Json['success'] = false;
                $Json['status'] = 0;
                return response()->json($Json);
                die;
            }

            echo json_encode($Json);
            die;
        } else {
            $Json['status'] = 0;
            $Json['message'] = 'Unauthorized Request';
            return response()->json($Json);
            die;
        }
    }

    public function getsocketuserid(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $socketid = $request->socketid;
        $findcardchallenge1 =  DB::connection('mysql')->table('cardchallenges')
            ->where('user1_socketid', $socketid)->first();
        $findcardchallenge2 =  DB::connection('mysql')->table('cardchallenges')
            ->where('user2_socketid', $socketid)->first();
        // dd($findjoinedleauges);
        if ((!empty($findcardchallenge1) && !empty($findcardchallenge1->user_id2)) or !empty($findcardchallenge2) && !empty($findcardchallenge2->user_id2)) {
            if (!empty($findcardchallenge1)) {
                $Json['userid'] = $findcardchallenge1->user_id2;
                $Json['newchallengeid'] = $findcardchallenge1->id;
                $Json['roomid'] = $findcardchallenge1->roomid;
                $amount = $this->distributewinning($findcardchallenge1->user_id2, $findcardchallenge1->id, 0);
                if (!empty($amount)) {
                    $Json['winamount'] = $amount;
                } else {
                    $Json['winamount'] = 0;
                }
                DB::connection('mysql2')->table('cardchallenges')
                    ->where('id', $findcardchallenge1->id)->update(['user1_socketid' => NULL, 'user2_socketid' => NULL]);
            } else if (!empty($findcardchallenge2)) {
                $Json['userid'] = $findcardchallenge2->user_id1;
                $Json['newchallengeid'] = $findcardchallenge2->id;
                $Json['roomid'] = $findcardchallenge2->roomid;
                $this->distributewinning($findcardchallenge2->user_id1, $findcardchallenge2->id, 0);
                DB::connection('mysql2')->table('cardchallenges')
                    ->where('id', $findcardchallenge2->id)->update(['user1_socketid' => NULL, 'user2_socketid' => NULL]);
            } else {
                $Json = [];
                return response()->json($Json);
                die;
            }
        } else {
            if (!empty($findcardchallenge1)) {
                $Json['userid'] = $findcardchallenge1->user_id1;
                $Json['newchallengeid'] = $findcardchallenge1->id;
                DB::connection('mysql2')->table('cardchallenges')
                    ->where('id', $findcardchallenge1->id)->update(['status' => 'lefted', 'user1_socketid' => NULL, 'user2_socketid' => NULL]);
            } else if (!empty($findcardchallenge2)) {
                $Json['userid'] = $findcardchallenge2->user_id1;
                $Json['newchallengeid'] = $findcardchallenge2->id;
                DB::connection('mysql2')->table('cardchallenges')
                    ->where('id', $findcardchallenge2->id)->update(['status' => 'lefted', 'user1_socketid' => NULL, 'user2_socketid' => NULL]);
            } else {
                $Json = [];
                return response()->json($Json);
                die;
            }
        }
        return response()->json($Json);
        die;
    }

    public function updatesocketid(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $type = $request->type;
        $findcardchallenge1 =  DB::connection('mysql')->table('cardchallenges')->where('id', $request->newchallengid)->first();
        if (!empty($findcardchallenge1)) {
            if ($type == 'leave') {
                $data['newchallengid'] = $request->newchallengid;
                $data['userid'] = $request->userid;
                $data['socketid'] = $request->socketid;
                DB::connection('mysql2')->table('cardchallenges')->where('id', $request->newchallengid)->update(['user1_socketid' => NULL, 'user2_socketid' => NULL]);
            } else {
                $data['newchallengid'] = $request->newchallengid;
                $data['userid'] = $request->userid;
                $data['roomid'] = $request->roomid;
                $data['socketid'] = $request->socketid;
                DB::connection('mysql2')->table('cardchallenges')->where('id', $request->newchallengid)->update(['roomid' => $request->roomid]);
            }
            $Json['success'] = true;
        } else {
            $Json['success'] = false;
        }

        return response()->json($Json);
        die;
    }

    public function getusergamedata(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $data['challengeid'] = $request->challengeid;
        $data['wonuser'] = $request->wonuser;
        $data['field'] = $request->field;
        $data['fieldvalue1'] = $request->fieldvalue1;
        $data['fieldvalue2'] = $request->fieldvalue2;
        $data['player_id1'] =  $request->player_id1;
        $data['player_id2'] = $request->player_id2;
        $data['send_user_id'] = $request->send_user_id;
        $data['receive_user_id'] = $request->receive_user_id;
        $insertid = DB::connection('mysql2')->table('usercardteam')->insertGetId($data);
        if (!empty($insertid)) {
            $player1detail = DB::connection('mysql')->table('cardplayers')->where('id', $request->player_id1)->first();
            $player2detail = DB::connection('mysql')->table('cardplayers')->where('id', $request->player_id2)->first();
            $Json['player_name1'] = $player1detail->player_name;
            $Json['player_name2'] = $player2detail->player_name;
            if ($player1detail->image != "") {
                $Json['player1image'] = $geturl.'public/'.$player1detail->image;
            } else {
                $Json['player1image'] = $geturl . 'public/team_image.png';
            }
            if ($player2detail->image != "") {
                $Json['player2image'] = $geturl.'public/'.$player2detail->image;
            } else {
                $Json['player2image'] = $geturl . 'public/team_image.png';
            }
        } else {
            $Json = [];
        }

        return response()->json($Json);
        die;
    }

    public function recentjoined(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        unset($input['auth_key']);
        $findjoinedleauges =  DB::connection('mysql')->table('joinedcardleauges')
            ->where('joinedcardleauges.userid', $userid)
            ->join('cardchallenges', 'cardchallenges.id', '=', 'joinedcardleauges.challengeid')
            ->select('cardchallenges.*')->orderBy('cardchallenges.win_amount', 'DESC')->limit(3)->get();


        // dd($findjoinedleauges);
        $aa = $findjoinedleauges->toArray();
        if (!empty($aa)) {
            $i = 0;
            $Json = array();
            foreach ($findjoinedleauges as $joined) {
                $finalresult = DB::connection('mysql')->table('cardfinalresults')->join('registerusers','registerusers.id','=','cardfinalresults.userid')->where('challengeid', $joined->id)->first();
                if(!empty($finalresult)){
                    if ($userid == $joined->user_id1) {
                        $user1data = DB::connection('mysql')->table('registerusers')->where('id', $joined->user_id1)->first();
                        $cardpoint1 = DB::connection('mysql')->table('usercardteam')->where('challengeid', $joined->id)->where('wonuser', $joined->user_id1)->count();
                        $Json[$i]['user1name'] = $user1data->team;
                        $Json[$i]['team1name'] = $user1data->team;
                        $Json[$i]['point1'] = $cardpoint1;
                        if ($user1data->image != "") {
                            $Json[$i]['image1'] = $user1data->image;
                        } else {
                            $Json[$i]['image1'] = $geturl . 'public/team_image.png';
                        }
                        $user2data = DB::connection('mysql')->table('registerusers')->where('id', $joined->user_id2)->first();
                        $cardpoint2 = DB::connection('mysql')->table('usercardteam')->where('challengeid', $joined->id)->where('wonuser', $joined->user_id2)->count();
                        $Json[$i]['user2name'] = $user2data->team;
                        $Json[$i]['team2name'] = $user2data->team;
                        $Json[$i]['point2'] = $cardpoint2;
                        if ($user2data->image != "") {
                            $Json[$i]['image2'] = $user2data->image;
                        } else {
                            $Json[$i]['image2'] = $geturl . 'public/team_image.png';
                        }
                    } else if ($userid == $joined->user_id2) {
                        $user1data = DB::connection('mysql')->table('registerusers')->where('id', $joined->user_id2)->first();
                        $cardpoint1 = DB::connection('mysql')->table('usercardteam')->where('challengeid', $joined->id)->where('wonuser', $joined->user_id2)->count();
                        $Json[$i]['user1name'] = $user1data->team;
                        $Json[$i]['team1name'] = $user1data->team;
                        $Json[$i]['point1'] = $cardpoint1;
                        if ($user1data->image != "") {
                            $Json[$i]['image1'] = $user1data->image;
                        } else {
                            $Json[$i]['image1'] = $geturl . 'public/team_image.png';
                        }
                        $user2data = DB::connection('mysql')->table('registerusers')->where('id', $joined->user_id1)->first();
                        $cardpoint2 = DB::connection('mysql')->table('usercardteam')->where('challengeid', $joined->id)->where('wonuser', $joined->user_id1)->count();
                        $Json[$i]['user2name'] = $user2data->team;
                        $Json[$i]['image2'] = $user2data->image;
                        $Json[$i]['team2name'] = $user2data->team;
                        $Json[$i]['point2'] = $cardpoint2;
                        if ($user2data->image != "") {
                            $Json[$i]['image2'] = $user2data->image;
                        } else {
                            $Json[$i]['image2'] = $geturl . 'public/team_image.png';
                        }
                    }
                    $Json[$i]['winnerteam'] = $finalresult->team;
                    $Json[$i]['win_amount'] = $finalresult->amount;
                    $Json[$i]['created_at'] = $joined->created_at;
                    $Json[$i]['entryfee'] = $joined->entryfee;
                    $Json[$i]['id'] = $joined->id;
                    $i++;
                }
            }
        } else {
            $Json = [];
            return response()->json($Json);
            die;
        }
        return response()->json($Json);
        die;
    }

    public function getjoineduserschallenge(Request $request)
    {
        Helpers::setHeader(200);
        Helpers::timezone();
        $geturl = Helpers::geturl();
        $challengeid = $request->get('newchallengeid');
        $challengedata = DB::connection('mysql')->table('cardchallenges')->where('id', $challengeid)->first();
        if (!empty($challengedata)) {
            $user1detail = DB::connection('mysql')->table('registerusers')->where('id', $challengedata->user_id1)->first();
            $user2detail = DB::connection('mysql')->table('registerusers')->where('id', $challengedata->user_id2)->first();
            // dd($challengedata);
            if (!empty($user1detail)) {
                $Json['user1']['user_id'] = $challengedata->user_id1;
                $Json['user1']['teamname'] = $user1detail->team;
                if ($user1detail->image != "") {
                    $Json['user1']['image'] = $user1detail->image;
                } else {
                    $Json['user1']['image'] = $geturl . 'public/team_image.png';
                }
            }
            if (!empty($user2detail)) {
                $Json['user2']['user_id'] = $challengedata->user_id2;
                $Json['user2']['teamname'] = $user2detail->team;
                if ($user2detail->image != "") {
                    $Json['user2']['image'] = $user2detail->image;
                } else {
                    $Json['user2']['image'] = $geturl . 'public/team_image.png';
                }
            }
            return response()->json($Json);
            die;
        }
    }

    public function updateTossStatus(Request $request)
    {
        Helpers::setHeader(200);
        Helpers::timezone();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $user->id;
        $newchallengeid = $request->get('newchallengeid');
        $tossstatus = $request->get('tossstatus');
        $challengedata = DB::connection('mysql')->table('cardchallenges')->where('id', $newchallengeid)->first();
        if (!empty($challengedata)) {
            $data['tossstatus'] = $tossstatus;

            $Json['status'] = 1;
            $Json['message'] = 'Toss Status Updated';
            $Json['user1'] = $challengedata->user_id1;
            $Json['user2'] = $challengedata->user_id2;
            $Json['outCome'] = $data['outCome'] = array("1", "2")[random_int(0, 1)];
            DB::connection('mysql2')->table('cardchallenges')->where('id', $challengedata->id)->update($data);
            // socket send to second user//
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://believer11.com:4000/sendTossStatus');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "&newchallengeid=" . $newchallengeid . "&tossstatus=" . $tossstatus . "&tossuserid=" . $userid . "&outCome=" . $Json['outCome']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            return response()->json($Json);
            die;
        } else {
            $Json['status'] = 0;
            $Json['message'] = 'Unauthorized Request';
            return response()->json($Json);
            die;
        }
    }

    public function cardjoinedmatches(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        unset($input['auth_key']);
        $findjoinedleauges =  DB::connection('mysql')->table('joinedcardleauges')
            ->where('joinedcardleauges.userid', $userid)
            ->join('cardchallenges', 'cardchallenges.id', '=', 'joinedcardleauges.challengeid')
            ->select('cardchallenges.*')->orderBy('cardchallenges.win_amount', 'DESC')->get();


        // dd($findjoinedleauges);
        $aa = $findjoinedleauges->toArray();
        if (!empty($aa)) {
            $i = 0;
            $Json = array();
            foreach ($findjoinedleauges as $joined) {
                $finalresult = DB::connection('mysql')->table('cardfinalresults')->join('registerusers','registerusers.id','=','cardfinalresults.userid')->where('challengeid', $joined->id)->first();
                if(!empty($finalresult)){
                    if ($userid == $joined->user_id1) {
                        $user1data = DB::connection('mysql')->table('registerusers')->where('id', $joined->user_id1)->first();
                        $cardpoint1 = DB::connection('mysql')->table('usercardteam')->where('challengeid', $joined->id)->where('wonuser', $joined->user_id1)->count();
                        $Json[$i]['user1name'] = $user1data->team;
                        $Json[$i]['team1name'] = $user1data->team;
                        $Json[$i]['point1'] = $cardpoint1;
                        if ($user1data->image != "") {
                            $Json[$i]['image1'] = $user1data->image;
                        } else {
                            $Json[$i]['image1'] = $geturl . 'public/team_image.png';
                        }
                        $user2data = DB::connection('mysql')->table('registerusers')->where('id', $joined->user_id2)->first();
                        $cardpoint2 = DB::connection('mysql')->table('usercardteam')->where('challengeid', $joined->id)->where('wonuser', $joined->user_id2)->count();
                        $Json[$i]['user2name'] = $user2data->team;
                        $Json[$i]['team2name'] = $user2data->team;
                        $Json[$i]['point2'] = $cardpoint2;
                        if ($user2data->image != "") {
                            $Json[$i]['image2'] = $user2data->image;
                        } else {
                            $Json[$i]['image2'] = $geturl . 'public/team_image.png';
                        }
                    } else if ($userid == $joined->user_id2) {
                        $user1data = DB::connection('mysql')->table('registerusers')->where('id', $joined->user_id2)->first();
                        $cardpoint1 = DB::connection('mysql')->table('usercardteam')->where('challengeid', $joined->id)->where('wonuser', $joined->user_id2)->count();
                        $Json[$i]['user1name'] = $user1data->team;
                        $Json[$i]['team1name'] = $user1data->team;
                        $Json[$i]['point1'] = $cardpoint1;
                        if ($user1data->image != "") {
                            $Json[$i]['image1'] = $user1data->image;
                        } else {
                            $Json[$i]['image1'] = $geturl . 'public/team_image.png';
                        }
                        $user2data = DB::connection('mysql')->table('registerusers')->where('id', $joined->user_id1)->first();
                        $cardpoint2 = DB::connection('mysql')->table('usercardteam')->where('challengeid', $joined->id)->where('wonuser', $joined->user_id1)->count();
                        $Json[$i]['user2name'] = $user2data->team;
                        $Json[$i]['image2'] = $user2data->image;
                        $Json[$i]['team2name'] = $user2data->team;
                        $Json[$i]['point2'] = $cardpoint2;
                        if ($user2data->image != "") {
                            $Json[$i]['image2'] = $user2data->image;
                        } else {
                            $Json[$i]['image2'] = $geturl . 'public/team_image.png';
                        }
                    }
                    $Json[$i]['winnerteam'] = $finalresult->team;
                    $Json[$i]['win_amount'] = $finalresult->amount;
                    $Json[$i]['created_at'] = $joined->created_at;
                    $Json[$i]['entryfee'] = $joined->entryfee;
                    $Json[$i]['id'] = $joined->id;
                    $i++;
                }
            }
        } else {
            $Json = [];
            return response()->json($Json);
            die;
        }
        return response()->json($Json);
        die;
    }

    public function getusercarddata(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        $challengeid =  $request->challengeid;
        unset($input['auth_key']);
        $findcarduser =  DB::connection('mysql')->table('usercardteam')
            ->where('challengeid', $challengeid)->get();
        $findchallengedata =  DB::connection('mysql')->table('cardchallenges')
            ->where('id', $challengeid)->first();
        $aa = $findcarduser->toArray();
        if (!empty($aa)) {
            $i = 0;
            $Json = array();
            foreach ($findcarduser as $userdata) {
                $Json['questiondata'][$i]['challengeid'] = $userdata->challengeid;
                $Json['questiondata'][$i]['field'] = $userdata->field;
                $playerdata1 = DB::connection('mysql')->table('cardplayers')->where('id', $userdata->player_id1)->first();
                $playerdata2 = DB::connection('mysql')->table('cardplayers')->where('id', $userdata->player_id2)->first();
                $Json['questiondata'][$i]['player_name1'] = $playerdata1->player_name;
                $Json['questiondata'][$i]['player_name2'] = $playerdata2->player_name;
                $Json['questiondata'][$i]['fieldvalue1'] = $userdata->fieldvalue1;
                $Json['questiondata'][$i]['fieldvalue2'] = $userdata->fieldvalue2;
                $Json['questiondata'][$i]['wonuser'] = $userdata->wonuser;
                $Json['questiondata'][$i]['userid1'] = $userdata->send_user_id;
                $Json['questiondata'][$i]['userid2'] = $userdata->receive_user_id;
                $i++;
            }
            $user1data = DB::connection('mysql')->table('registerusers')->where('id', $findchallengedata->user_id1)->first();
            $Json['user_id1'] = $findchallengedata->user_id1;
            $Json['name1'] = $user1data->team;
            if ($user1data->image != "") {
                $Json['image1'] =  $user1data->image;
            } else {
                $Json['image1'] = $geturl . 'public/team_image.png';
            }
            $user2data = DB::connection('mysql')->table('registerusers')->where('id', $findchallengedata->user_id2)->first();
            $Json['user_id2'] = $findchallengedata->user_id2;
            $Json['name2'] = $user2data->team;
            if ($user2data->image != "") {
                $Json['image2'] =  $user2data->image;
            } else {
                $Json['image2'] = $geturl . 'public/team_image.png';
            }
        } else {
            $Json = [];
            return response()->json($Json);
            die;
        }
        return response()->json($Json);
        die;
    }

    public function cardleauges(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        unset($input['auth_key']);
        $getallseries =  DB::connection('mysql')->table('cardseries')->where('status', 'yes')->get();

        $aa = $getallseries->toArray();
        if (!empty($aa)) {
            $i = 0;
            $Json = array();
            foreach ($getallseries as $oneseries) {
                $Json[$i]['id'] = $oneseries->id;
                $Json[$i]['name'] = $oneseries->name;
                $Json[$i]['series_key'] = $oneseries->series_key;
                if ($oneseries->logo != "") {
                    $Json[$i]['logo'] = $geturl.'public/'.$oneseries->logo;
                } else {
                    $Json[$i]['logo'] = $geturl . 'public/team_image.png';
                }
                $i++;
            }
        } else {
            $Json = [];
            return response()->json($Json);
            die;
        }
        return response()->json($Json);
        die;
    }

    public function cardleaugesteams(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        $series_key = $request->series_key;
        unset($input['auth_key']);
        $getallteams =  DB::connection('mysql')->table('cardteams')->join('cardseries', 'cardseries.series_key', '=', 'cardteams.series_key')->where('cardseries.id', $series_key)->select('cardteams.*')->get();
        $aa = $getallteams->toArray();
        if (!empty($aa)) {
            $i = 0;
            $Json = array();
            foreach ($getallteams as $oneteam) {
                $Json[$i]['id'] = $oneteam->id;
                $Json[$i]['name'] = $oneteam->team;
                $Json[$i]['team_key'] = $oneteam->team_key;

                if ($oneteam->logo != "") {
                    $Json[$i]['logo'] = $geturl .'public/'. $oneteam->logo;
                } else {
                    $Json[$i]['logo'] = $geturl . 'public/team_image.png';
                }
                $Json[$i]['short_name'] = $oneteam->short_name;
                $i++;
            }
        } else {
            $Json = [];
            return response()->json($Json);
            die;
        }
        return response()->json($Json);
        die;
    }

    public function cardsteamplayers(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        $teamkey = $request->id;
        unset($input['auth_key']);
        $getallplayers =  DB::connection('mysql')->table('cardplayers')->where('team', $teamkey)->where('matches', '!=', 0)->get();
        $aa = $getallplayers->toArray();
        if (!empty($aa)) {
            $i = 0;
            $Json = array();
            foreach ($getallplayers as $player) {
                $Json[$i]['id'] = $player->id;
                $Json[$i]['name'] = $player->player_name;
                $Json[$i]['players_key'] = $player->players_key;
                $Json[$i]['credit'] = $player->credit;
                $Json[$i]['matches'] = $player->matches;
                $Json[$i]['role'] = $player->role;
                $Json[$i]['notouts'] = $player->notouts;
                $Json[$i]['runs'] = $player->runs;
                $Json[$i]['highscore'] = $player->highscore;
                $Json[$i]['average'] = $player->average;
                $Json[$i]['strikerate_batting'] = $player->strikerate_batting;
                $Json[$i]['fifty'] = $player->fifty;
                $Json[$i]['hundred'] = $player->hundred;
                $Json[$i]['fours'] = $player->fours;
                $Json[$i]['sixes'] = $player->sixes;
                $Json[$i]['wickets'] = $player->wickets;
                $Json[$i]['bestfigures'] = $player->bestfigures;
                $Json[$i]['economy'] = $player->economy;
                $Json[$i]['strikerate_bowling'] = $player->strikerate_bowling;
                $Json[$i]['fourwicket'] = $player->fourwicket;
                $Json[$i]['fivewicket'] = $player->fivewicket;
                $Json[$i]['catches'] = $player->catches;
                $Json[$i]['stumping'] = $player->stumping;
                if ($player->image != "") {
                    $Json[$i]['image'] = $geturl .'public/'. $player->image;
                } else {
                    $Json[$i]['image'] = $geturl . 'public/team_image.png';
                }
                $i++;
            }
        } else {
            $Json = [];
            return response()->json($Json);
            die;
        }
        return response()->json($Json);
        die;
    }

    public function getcardUsableBalance(Request $request)
    {
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        unset($input['auth_key']);
        $challengeid = $request->get('challengeid');
        $total_team_count = $request->get('total_team_count', 1);

        /* to find the challenge details */
        $findchallengedetails =  DB::connection('mysql')->table('cardcontest')->where('id', $challengeid)->first();
        if (!empty($findchallengedetails)) {
            $entryfee = $findchallengedetails->entryfee * $total_team_count;
            $maximumuser = $findchallengedetails->maximum_user;
            /* to get the balance details */
            $findwalletamount =  DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
            if (!empty($findwalletamount)) {
                if ($findchallengedetails->bonus_percentage != 0) {
                    $findbonus = ($findchallengedetails->bonus_percentage / 100) * $findwalletamount->bonus;
                } else {
                    $findbonus = 0;
                }
                $findbal = $findwalletamount->balance;
                $findbonus = $findwalletamount->bonus;
                $findwining = $findwalletamount->winning;
                $findextracash = $findwalletamount->extracash;
                $findtotalbalance = $findwalletamount->balance + $findwalletamount->winning + $findwalletamount->extracash;
                // $findtotalbalance =$findwalletamount->bonus + $findwalletamount->balance+$findwalletamount->winning;
                /* calculate wining amount and balance amount */
                $findusablebalance = $findwalletamount->balance + $findwalletamount->winning + $findwalletamount->extracash;
            } else {
                $findbonus = 0;
                $findbal = 0;
                $findwining = 0;
                $findextracash = 0;
                $findtotalbalance = 0;
                /* calculate wining amount and balance amount */
                $findusablebalance = 0;
            }
            $findbonusamount = 0;
            if ($findchallengedetails->is_bonus == 1) {
                $getbonuspercentage = $findchallengedetails->bonus_percentage;
                if ($getbonuspercentage) {
                    $findbonusamount = ($getbonuspercentage / 100) * $entryfee;
                }
            }
            $usedbonus = 0;
            if ($findbonus >= $findbonusamount) {
                $usedbonus = $findbonusamount;
                $findusablebalance += $findbonusamount;
            } else {
                $usedbonus = $findbonus;
                $findusablebalance += $findbonus;
            }

            $Json['usablebalance'] = number_format($findtotalbalance, 2, '.', '');
            $Json['usertotalbalance'] = number_format($findusablebalance, 2, '.', '');
            $Json['entryfee'] = number_format($entryfee, 2, '.', '');
            $Json['bonus'] = number_format($usedbonus, 2, '.', '');
            // LOG::info($Json);
        } else {
            $Json['message'] = 'Invalid details';
        }
        return response()->json(array($Json));
        die;
    }

    public function refundcard_amountcontest(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $current = date('Y-m-d H:i:s');
        $match_challenges = DB::connection('mysql')->table('cardchallenges')->where('id', $request->newchallengid)->where('status', '!=', 'canceled')->where('status', '!=', 'winnerdeclared')->first();
        if (!empty($match_challenges)) {
            $getresponse = $this->refundprocess($match_challenges->id, $match_challenges->entryfee, 'challenge cancel');
            if ($getresponse == true) {
                $data['status'] = 'canceled';
                $data['user1_socketid'] = NULL;
                $data['user2_socketid'] = NULL;
                DB::connection('mysql2')->table('cardchallenges')->where('id', $match_challenges->id)->update($data);
            }
        }
    }

    public function refundprocess($challengeid, $entryfees, $reason)
    {
        $leaugestransactions = DB::connection('mysql')->table('leaugestransactions')->where('matchkey', 0)->where('challengeid', $challengeid)->get();
        if (!empty($leaugestransactions)) {
            foreach ($leaugestransactions as  $value2) {
                $refund_data = DB::connection('mysql')->table('refunds')->where('joinid', $value2->joinid)->select('id')->first();
                if (empty($refund_data)) {
                    $entry_fee = $entryfees;
                    $last_row = DB::connection('mysql')->table('userbalance')->where('user_id', $value2->user_id)->first();
                    if (!empty($last_row)) {
                        $data_bal['balance'] = number_format($last_row->balance + $value2->balance, 2, ".", "");
                        $data_bal['winning'] = number_format($last_row->winning + $value2->winning, 2, ".", "");
                        $data_bal['bonus'] =  number_format($last_row->bonus + $value2->bonus, 2, ".", "");
                        $data_bal['extracash'] =  number_format($last_row->extracash + $value2->extracash, 2, ".", "");
                        $data_bal['referral_income'] =  number_format($last_row->referral_income + $value2->referral_income, 2, ".", "");

                        DB::connection('mysql2')->table('userbalance')->where('id', $last_row->id)->update($data_bal);
                        $refund_data['userid'] = $value2->user_id;
                        $refund_data['amount'] = $entry_fee;
                        $refund_data['joinid'] = $value2->joinid;
                        $refund_data['challengeid'] = $value2->challengeid;
                        $refund_data['reason'] = $reason;
                        $refund_data['matchkey'] = 0;
                        $transaction_id = (Helpers::settings()->short_name ?? '' . '-') . rand(100, 999) . time() . '-' . $value2->user_id;
                        $refund_data['transaction_id'] = $transaction_id;
                        DB::connection('mysql2')->table('refunds')->insert($refund_data);
                        $data_trans['transaction_id'] = $transaction_id;
                        $data_trans['type'] = 'Refund';
                        $data_trans['transaction_by'] = Helpers::settings()->short_name ?? '';
                        $data_trans['amount'] = $entry_fee;
                        $data_trans['paymentstatus'] = 'confirmed';
                        $data_trans['challengeid'] = $value2->challengeid;
                        $data_trans['bonus_amt'] = $value2->bonus;
                        $data_trans['win_amt'] = $value2->winning;
                        $data_trans['addfund_amt'] = $value2->balance;
                        $data_trans['extracash_amt'] = $value2->extracash;
                        $data_trans['referral_amt'] = $value2->referral_income;
                        $data_trans['bal_bonus_amt'] = $data_bal['bonus'];
                        $data_trans['bal_win_amt'] = $data_bal['winning'];
                        $data_trans['bal_fund_amt'] = $data_bal['balance'];
                        $data_trans['bal_extracash_amt'] = $data_bal['extracash'];
                        $data_trans['bal_referral_amt'] = $data_bal['referral_income'];
                        $data_trans['userid'] = $value2->user_id;
                        $data_trans['total_available_amt'] = $data_bal['balance'] + $data_bal['winning'] + $data_bal['bonus'] + $data_bal['extracash'] + $data_bal['referral_income'];
                        DB::connection('mysql2')->table('transactions')->insert($data_trans);
                        //notifications//
                        $totalentryfee = $value2->bonus + $value2->balance + $value2->winning + $value2->extracash;
                        $datan['title'] = 'Refund Amount of Rs.' . $totalentryfee . ' for challenge cancellation';
                        $datan['userid'] = $value2->user_id;
                        DB::connection('mysql2')->table('notifications')->insert($datan);
                        //push notifications//
                        $titleget = 'Refund Amount!';
                        Helpers::sendnotification($titleget, $datan['title'], '', $value2->user_id);
                        //end push notifications//
                    }
                }
            }
        }
        return true;
    }

    public function refundOpenedContest(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $current = date('Y-m-d H:i:s');
        die;
        // $createdate = date('Y-m-d H:i:s', strtotime($current. ' -10 hours'));
        // dump($createdate);
        // $match_challengesall = DB::connection('mysql')->table('cardchallenges')->whereDate('created_at','<', $createdate)->where('status', 'opened')->whereNotIn('status', ['canceled','winnerdeclared'])->orderBy('id','DESC')->get();
        $match_challengesall = DB::connection('mysql')->table('cardchallenges')->where('status', 'opened')->orderBy('id','DESC')->get();
        // dd($match_challengesall);
        foreach($match_challengesall as $match_challenges){
            if (!empty($match_challenges)) {
                $getresponse = $this->refundprocess($match_challenges->id, $match_challenges->entryfee, 'challenge cancel');
                if ($getresponse == true) {
                    $data['status'] = 'canceled';
                    $data['user1_socketid'] = NULL;
                    $data['user2_socketid'] = NULL;
                    DB::connection('mysql2')->table('cardchallenges')->where('id', $match_challenges->id)->update($data);
                }
            }
        }
    }

    public function finalcardresult(Request $request)
    {
        $user = Helpers::isAuthorize($request);
        $userid = $fpusv['userid'] = $user->id;
        $point = $request->points;
        $amount = $this->distributewinning($userid, $request->challengeid, $point);
        if (!empty($amount)) {
            $Json['winamount'] = $amount;
        } else {
            $Json['winamount'] = 0;
        }
        return response()->json($Json);
        die;
    }

    public function distributewinning($userid, $challenegeid, $point)
    {
        $userid = $fpusv['userid'] = $userid;
        $challenge = DB::connection('mysql')->table('cardchallenges')->where('id', $challenegeid)->where('status', '!=', 'canceled')->where('status', '!=', 'lefted')->where('status', '!=', 'winnerdeclared')->first();

        if (!empty($challenge)) {
            $fpusk = $fpusv['userid'];
            $userjoindata = DB::connection('mysql')->table('joinedcardleauges')->where('challengeid', $challenegeid)->where('userid', $userid)->first();
            $winningdata = DB::connection('mysql')->table('cardfinalresults')->where('challengeid', $challenegeid)->first();
            if (empty($winningdata)) {
                $fres = array();
                $challengeid = $challenge->id;
                $seriesid = 0;
                $transactionidsave = 'WIN-' . rand(1000, 99999) . $challengeid . $userjoindata->id;
                $fres['userid'] = $fpusk;
                $fres['points'] = $point;
                $fres['amount'] = number_format(floor($challenge->win_amount * 100) / 100, '2', '.', '');
                $fres['rank'] = 1;
                $fres['matchkey'] = 0;
                $fres['challengeid'] = $challengeid;
                $fres['seriesid'] = $seriesid;
                $fres['transaction_id'] = $transactionidsave;
                $fres['joinedid'] = $userjoindata->id;
                $findalreexist = DB::connection('mysql')->table('cardfinalresults')->where('joinedid', $userjoindata->id)->where('userid', $fpusk)->select('id')->first();

                if (empty($findalreexist)) {
                    DB::connection('mysql2')->table('cardfinalresults')->insert($fres);
                    DB::connection('mysql2')->table('cardchallenges')->where('id', $challenegeid)->update(['status' => 'winnerdeclared']);
                    $finduserbalance = DB::connection('mysql')->table('userbalance')->where('user_id', $fpusk)->first();

                    if (!empty($finduserbalance)) {
                        if ($challenge->win_amount > 10000) {
                            $datatr = array();
                            $dataqs = array();
                            $tdsdata['tds_amount'] = (31.2 / 100) * ($challenge->win_amount / 100);
                            $tdsdata['amount'] = $challenge->win_amount;
                            $remainingamount = $fres['amount'] - $tdsdata['tds_amount'];
                            $tdsdata['userid'] = $fpusk;
                            $tdsdata['challengeid'] = $challenge->id;
                            DB::connection('mysql2')->table('tdsdetails')->insert($tdsdata);
                            $challenge->win_amount = $remainingamount;
                            //user balance//
                            $registeruserdetails = DB::table('registerusers')->where('id', $fpusk)->first();
                            $findlastow = DB::table('userbalance')->where('user_id', $fpusk)->first();
                            $dataqs['winning'] = number_format($findlastow->winning + $fres['amount'], 2, ".", "");

                            DB::connection('mysql2')->table('userbalance')->where('id', $findlastow->id)->update($dataqs);
                            //transactions entry//
                            $datatr['transaction_id'] = $transactionidsave;;
                            $datatr['type'] = 'Challenge Winning Amount';
                            $datatr['transaction_by'] = Helpers::settings()->short_name ?? '';
                            $datatr['amount'] = $fres['amount'];
                            $datatr['paymentstatus'] = 'confirmed';
                            $datatr['challengeid'] = $challenge->id;
                            $datatr['win_amt'] = $fres['amount'];
                            $datatr['bal_bonus_amt'] = $finduserbalance->bonus;
                            $datatr['bal_win_amt'] = $dataqs['winning'];
                            $datatr['bal_fund_amt'] = $finduserbalance->balance;
                            $datatr['bal_extracash_amt'] = $finduserbalance->extracash;
                            $datatr['bal_referral_amt'] = $finduserbalance->referral_income;
                            $datatr['userid'] = $fpusk;
                            $datatr['total_available_amt'] = $finduserbalance->balance + $dataqs['winning'] + $finduserbalance->bonus + $finduserbalance->referral_income + $finduserbalance->extracash;
                            DB::connection('mysql2')->table('transactions')->insert($datatr);

                            $datanot['title'] = 'You won amount Rs.' . $fres['amount'] . ' and 31.2% amount of ' . $tdsdata['amount'] . ' deducted due to TDS.';
                            $datanot['userid'] = $fpusk;
                            DB::connection('mysql2')->table('notifications')->insert($datanot);
                            //push notifications//
                            $titleget = 'Congrats! You won a match.';
                            Helpers::sendnotification($titleget, $datanot['title'], '', $fpusk);
                        } else {
                            $datatr = array();
                            $dataqs = array();
                            //user balance//
                            $registeruserdetails = DB::connection('mysql')->table('registerusers')->where('id', $fpusk)->first();

                            $findlastow = DB::connection('mysql')->table('userbalance')->where('user_id', $fpusk)->first();
                            $dataqs['winning'] =  number_format($findlastow->winning + $fres['amount'], 2, ".", "");
                            DB::connection('mysql2')->table('userbalance')->where('id', $findlastow->id)->update($dataqs);
                            if ($challenge->win_amount > 0) {
                                //transactions entry//
                                $datatr['transaction_id'] = $transactionidsave;
                                $datatr['type'] = 'Challenge Winning Amount';
                                $datatr['transaction_by'] = Helpers::settings()->short_name ?? '';
                                $datatr['amount'] = $fres['amount'];
                                $datatr['paymentstatus'] = 'confirmed';
                                $datatr['challengeid'] = $challenge->id;
                                $datatr['win_amt'] = $fres['amount'];
                                $datatr['bal_bonus_amt'] = $finduserbalance->bonus;
                                $datatr['bal_win_amt'] = $dataqs['winning'];
                                $datatr['bal_fund_amt'] = $finduserbalance->balance;
                                $datatr['bal_extracash_amt'] = $finduserbalance->extracash;
                                $datatr['bal_referral_amt'] = $finduserbalance->referral_income;
                                $datatr['userid'] = $fpusk;
                                $datatr['total_available_amt'] = $finduserbalance->balance + $dataqs['winning'] + $finduserbalance->bonus + $finduserbalance->referral_income + $finduserbalance->extracash;
                                DB::connection('mysql2')->table('transactions')->insert($datatr);
                                //notifications entry//
                                $datanot['title'] = 'You won amount Rs.' . $fres['amount'];
                                $datanot['userid'] = $fpusk;
                                DB::connection('mysql2')->table('notifications')->insert($datanot);
                                //push notifications//
                                $titleget = 'Congrats! You Won a match!';
                                Helpers::sendnotification($titleget, $datanot['title'], '', $fpusk);
                            }
                        }
                    }
                }
            }
            return $fres['amount'];
        }
    }
}