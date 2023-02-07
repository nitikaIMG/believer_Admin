<?php
namespace App\Http\Controllers\api;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use Carbon;
use DB;
use Illuminate\Http\Request;
use Log;

class ContestApiController extends Controller
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

    public function getAllContests(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $id = $user->id;
        unset($input['auth_key']);
        $input['matchkey'] = $matchkey = $request->get('matchkey');
        $input['fantasy_type'] = $fantasy_type = $request->get('fantasy_type');
        if(empty($fantasy_type)){
            $input['fantasy_type'] = $fantasy_type='Cricket'; 
        }
        $input['duotype'] = $duotype = $request->get('duotype');
        $pricecardarr = array();
        if(!empty($duotype)){
            $allchallenges = DB::table('matchchallenges')->join('contest_category', 'contest_category.id', 'matchchallenges.contest_cat')->where('matchchallenges.matchkey', $matchkey)->where('matchchallenges.fantasy_type', $fantasy_type)->where('matchchallenges.duotype', $duotype)->where('matchchallenges.status', 'opened')->where('matchchallenges.is_private', '!=', '1')->select('matchchallenges.*', 'contest_category.id as cat_id', 'contest_category.name as cat_name')->orderBY('matchchallenges.entryfee', 'ASC')->get();
        }else{
            $allchallenges = DB::table('matchchallenges')->join('contest_category', 'contest_category.id', 'matchchallenges.contest_cat')->where('matchchallenges.matchkey', $matchkey)->where('matchchallenges.fantasy_type', $fantasy_type)->where('matchchallenges.status', 'opened')->where('matchchallenges.is_private', '!=', '1')->select('matchchallenges.*', 'contest_category.id as cat_id', 'contest_category.name as cat_name')->orderBY('matchchallenges.entryfee', 'ASC')->get();
        }
        

        $resprice = DB::table('matchpricecards')->where('matchkey', $matchkey)->orderBY('min_position', 'ASC')->get();
        foreach ($resprice as $row) {
            $pricecardarr[$row->challenge_id][] = $row;
        }
        $Json = array();
        $i = 0;
        $a = $allchallenges->toArray();
        if (!empty($a)) {
            foreach ($allchallenges as $challenege) {
                $totaljoineduser = DB::connection('mysql')->table('joinedleauges')->where('challengeid',$challenege->id)->count();
                if ($challenege->maximum_user >= 0) {
                    $Json[$i]['id'] = $challenege->id;
                    $Json[$i]['catid'] = $challenege->cat_id;
                    $Json[$i]['catname'] = $challenege->cat_name;
                    $Json[$i]['name'] = 'Win Rs.' . $challenege->win_amount;
                    $Json[$i]['contest_type'] = $challenege->contest_type;
                    $Json[$i]['entryfee'] = $challenege->entryfee;
                    $Json[$i]['offerentryfee'] = $challenege->offerentryfee;
                    $Json[$i]['win_amount'] = $challenege->win_amount;
                    if(!empty($duotype)){
                        $Json[$i]['duotype'] = $duotype;
                    }else{
                        $Json[$i]['duotype'] = '';
                    }
                    
                    $Json[$i]['winning_percentage'] = $challenege->winning_percentage;
                    if ($challenege->contest_type == 'Amount') {
                        $Json[$i]['maximum_user'] = $challenege->maximum_user;
                    } else {
                        $Json[$i]['maximum_user'] = 0;
                    }
                    $Json[$i]['matchkey'] = $challenege->matchkey;
                    $Json[$i]['success'] = true;
                    $Json[$i]['joinedusers'] = $totaljoineduser;
                    $getjoinedpercentage = 0;
                    if ($challenege->contest_type == 'Amount') {
                        if ($challenege->maximum_user > 0) {
                            $getjoinedpercentage = round(($totaljoineduser / $challenege->maximum_user) * 100, 2);
                        } else {
                            $getjoinedpercentage = 0;
                        }
                    }
                    if ($challenege->contest_type == 'Percentage') {
                        $getjoinedpercentage = 100;
                    }
                    $Json[$i]['multi_entry'] = $challenege->multi_entry;
                    $Json[$i]['team_limit'] = $challenege->team_limit;
                    $Json[$i]['confirmed_challenge'] = $challenege->confirmed_challenge;
                    $Json[$i]['is_running'] = $challenege->is_running;
                    $Json[$i]['is_bonus'] = $challenege->is_bonus;
                    $Json[$i]['bonus_percentage'] = $challenege->bonus_percentage;
                    $Json[$i]['pricecard_type'] = $challenege->pricecard_type;
                    $Json[$i]['isselected'] = false;
                    $Json[$i]['isselectedid'] = "";
                    $joinedleauges = DB::table('joininfo')->where('challengeid', $challenege->id)->where('userid', $id)->select('refercode')->get();
                    $aa = $joinedleauges->toArray();
                    if (!empty($aa)) {
                        if ($challenege->multi_entry == 1 && count($joinedleauges) < $challenege->team_limit) {
                            if ($challenege->contest_type == 'Amount') {
                                if (count($joinedleauges) == $challenege->team_limit) {
                                    $Json[$i]['isselected'] = true;
                                }
                                if ($totaljoineduser == $challenege->maximum_user) {
                                    $Json[$i]['isselected'] = true;
                                }
                            } else if ($challenege->contest_type == 'Percentage') {
                                if (count($joinedleauges) == $challenege->team_limit) {
                                    $Json[$i]['isselected'] = true;
                                }
                            } else {
                                $Json[$i]['isselected'] = false;
                            }
                            $Json[$i]['refercode'] = $joinedleauges[0]->refercode;
                        } else {
                            $Json[$i]['isselected'] = true;
                            $Json[$i]['refercode'] = $joinedleauges[0]->refercode;
                        }
                    } else {
                        $Json[$i]['isselected'] = false;
                        $Json[$i]['refercode'] = "";
                    }
                    $findpricecards = (isset($pricecardarr[$challenege->id])) ? $pricecardarr[$challenege->id] : [];
                    $j = 0;
                    if (!empty($findpricecards)) {
                        $winners = 0;
                        foreach ($findpricecards as $prc) {
                            $ttlprice = $prc->total / $prc->winners;
                            $Json[$i]['price_card'][$j]['id'] = $prc->id;
                            $Json[$i]['price_card'][$j]['winners'] = $prc->winners;
                            $winners += $prc->winners;
                            if ($prc->price == 0) {
                                $ttlprice = $prc->total / $prc->winners;
                                $Json[$i]['price_card'][$j]['price'] = $ttlprice;
                                $Json[$i]['price_card'][$j]['price_percent'] = $prc->price_percent . '%';
                            } else {
                                $Json[$i]['price_card'][$j]['price'] = $prc->price;
                            }
                            if ($prc->min_position + 1 != $prc->max_position) {
                                $Json[$i]['price_card'][$j]['start_position'] = $prc->min_position + 1 . '-' . $prc->max_position;
                            } else {
                                $Json[$i]['price_card'][$j]['start_position'] = '' . $prc->max_position;
                            }
                            $Json[$i]['price_card'][$j]['total'] = $prc->total;
                            $j++;
                        }
                        $Json[$i]['totalwinners'] = $winners;
                    } else {
                        $Json[$i]['price_card'][$j]['id'] = 0;
                        $Json[$i]['price_card'][$j]['winners'] = 1;
                        $Json[$i]['price_card'][$j]['price'] = $challenege->win_amount;
                        $Json[$i]['price_card'][$j]['start_position'] = 1;
                        $Json[$i]['price_card'][$j]['total'] = $challenege->win_amount;
                        $Json[$i]['totalwinners'] = 1;
                    }

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

    public function leaugesdetails(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        date_default_timezone_set('Asia/Kolkata');
        $current = date('Y-m-d H:i:s');
        $match_time = date('Y-m-d H:i:s', strtotime($current));
        if ($user) {
            $userid = $user_id = $data['userid'] = $user->id;
            unset($input['auth_key']);
            $challengeid = $data['challengeid'] = $request->get('challengeid');
            if (!empty($challengeid)) {
                $challenege = DB::table('matchchallenges')->where('id', $challengeid)->first();
                $challenege1 = DB::table('matchchallenges')->where('id', $challengeid)->get();
                $totaljoineduser = DB::connection('mysql')->table('joinedleauges')->where('challengeid',$challengeid)->count();
                $Json = array();
                $i = 0;
                $a = $challenege1->toArray();
                if (!empty($a)) {
                    $Json[$i]['id'] = $challenege->id;
                    $Json[$i]['contest_type'] = $challenege->contest_type;
                    $Json[$i]['entryfee'] = $challenege->entryfee;
                    $Json[$i]['offerentryfee'] = $challenege->offerentryfee;
                    $Json[$i]['type'] = 'classic';
                    $Json[$i]['win_amount'] = round($challenege->win_amount);
                    $Json[$i]['winning_percentage'] = $challenege->winning_percentage;
                    $Json[$i]['maximum_user'] = $challenege->maximum_user;
                    $Json[$i]['matchkey'] = $challenege->matchkey;
                    $Json[$i]['status'] = 1;
                    $Json[$i]['joinedusers'] = $totaljoineduser;
                    $Json[$i]['multi_entry'] = $challenege->multi_entry;
                    $Json[$i]['team_limit'] = $challenege->team_limit;
                    $Json[$i]['confirmed_challenge'] = $challenege->confirmed_challenge;
                    $Json[$i]['is_running'] = $challenege->is_running;
                    $Json[$i]['is_bonus'] = $challenege->is_bonus;
                    $Json[$i]['bonus_percentage'] = $challenege->bonus_percentage;
                    $Json[$i]['pricecard_type'] = $challenege->pricecard_type;
                    $Json[$i]['isselected'] = false;
                    $Json[$i]['isselectedid'] = "";
                    $Json[$i]['bonus_type'] = ($challenege->bonus_percentage == '0') ? "Amount" : "Percentage";
                    if($challenege->fantasy_type=='Duo'){
                        $joinedleauges = DB::table('joininfo')->where('challengeid', $challenege->id)->where('joininfo.userid', $user_id)->join('registerusers', 'registerusers.id', '=', 'joininfo.userid')->orderBy('joininfo.id', 'ASC')->select('joininfo.joinid as jid', 'joininfo.teamid as teamnumber', 'joininfo.teamid', 'joininfo.refercode', 'joininfo.userid', 'registerusers.team')->get();
                    }else{
                        $joinedleauges = DB::table('joininfo')->where('challengeid', $challenege->id)->where('joininfo.userid', $user_id)->join('jointeam', 'jointeam.id', '=', 'joininfo.teamid')->join('registerusers', 'registerusers.id', '=', 'joininfo.userid')->orderBy('jointeam.teamnumber', 'ASC')->select('joininfo.joinid as jid', 'jointeam.teamnumber', 'joininfo.teamid', 'joininfo.refercode', 'joininfo.userid', 'registerusers.team')->get();
                    }
                    $aa = $joinedleauges->toArray();

                    $total_joined = count($joinedleauges);
                    $Json[$i]['total_joined'] = $total_joined;
                    $aa = $joinedleauges->toArray();
                    // DD($aa);
                    if (!empty($aa)) {
                        $Json[$i]['isuserjoin'] = 1;

                        if ($challenege->multi_entry == 1 && $total_joined < $challenege->team_limit) {
                            if ($challenege->contest_type == 'Amount') {
                                if (count($joinedleauges) == $challenege->team_limit) {
                                    $Json[$i]['isselected'] = true;
                                }
                                if ($totaljoineduser == $challenege->maximum_user) {
                                    $Json[$i]['isselected'] = true;
                                }
                            } else if ($challenege->contest_type == 'Percentage') {
                                if (count($joinedleauges) == $challenege->team_limit) {
                                    $Json[$i]['isselected'] = true;
                                }
                            } else {
                                $Json[$i]['isselected'] = false;
                            }
                            $Json[$i]['refercode'] = $joinedleauges[0]->refercode;
                        } else {
                            $Json[$i]['isselected'] = true;
                            $Json[$i]['refercode'] = $joinedleauges[0]->refercode;
                        }

                        $jt = 0;
                    } else {
                        $Json[$i]['isselected'] = false;
                        $Json[$i]['refercode'] = "";
                        $Json[$i]['isuserjoin'] = 0;
                    }
                    // get all the price cards //
                    $allpricecard = DB::table('matchpricecards')->where('matchkey', $challenege->matchkey)->where('challenge_id', $challenege->id)->orderBY('min_position', 'ASC')->get();

                    $j = 0;
                    $aaa = $allpricecard->toArray();
                    if (!empty($allpricecard)) {
                        $winners = 0;
                        foreach ($allpricecard as $prc) {
                            $Json[$i]['price_card'][$j]['id'] = $prc->id;
                            $Json[$i]['price_card'][$j]['winners'] = $prc->winners;
                            $winners += $prc->winners;
                            if ($prc->min_position + 1 != $prc->max_position) {
                                $Json[$i]['price_card'][$j]['start_position'] = $prc->min_position + 1 . '-' . $prc->max_position;
                            } else {
                                $Json[$i]['price_card'][$j]['start_position'] = '' . $prc->max_position;
                            }
                            if ($prc->price == 0) {
                                $ttlprice = $prc->total / $prc->winners;
                                $Json[$i]['price_card'][$j]['price'] = number_format($ttlprice, 2, ".", "");
                                $Json[$i]['price_card'][$j]['price_percent'] = $prc->price_percent . '%';
                            } else {
                                $Json[$i]['price_card'][$j]['price'] = $prc->price;
                            }

                            $Json[$i]['price_card'][$j]['total'] = $prc->total;
                            $j++;
                        }
                        $Json[$i]['totalwinners'] = $winners;
                    } else {
                        $Json[$i]['price_card'] = [];
                        $Json[$i]['totalwinners'] = 1;
                    }

                    if (empty($Json[$i]['price_card'])) {
                        $Json[$i]['totalwinners'] = 1;
                    }

                    // get all the joinedteams //
                    $j = 0;
                    $userno = 0;
                    $sa = DB::table('listmatches')->where('matchkey', $challenege->matchkey)->select('start_date')->first();
                    //     if($sa->start_date >= $match_time){
                    $resjoinedteams = DB::table('joininfo')->where('joininfo.challengeid', $challenege->id)
                        ->join('registerusers', 'registerusers.id', '=', 'joininfo.userid')
                        ->leftjoin('jointeam', 'jointeam.id', '=', 'joininfo.teamid')
                        ->select('registerusers.team', 'registerusers.image as image', 'registerusers.id as regid', 'joininfo.userid', 'joininfo.joinid as jid', 'joininfo.teamid', 'joininfo.teamnumbercount', 'jointeam.teamnumber')->get();

                    $aaaa = $resjoinedteams->toArray();
                    //      dd($resjoinedteams);
                    if (!empty($aaaa)) {
                        foreach ($resjoinedteams as $jointeams) {
                            $Json[$i]['jointeams'][$j]['team'] = $jointeams->team;
                            $Json[$i]['jointeams'][$j]['jid'] = $jointeams->jid;
                            $Json[$i]['jointeams'][$j]['teamnumber'] = $jointeams->teamnumber;
                            $Json[$i]['jointeams'][$j]['teamid'] = $jointeams->teamid;
                            if ($jointeams->userid == $userid) {
                                $userno++;
                                $Json[$i]['jointeams'][$j]['usernumber'] = $userno;
                            } else {
                                $Json[$i]['jointeams'][$j]['usernumber'] = 0;
                            }
                            $Json[$i]['jointeams'][$j]['image'] = (empty($jointeams->image) || $jointeams->image == 'null' || $jointeams->image == '') ? $geturl . 'public/avtar1.png' : 'public/'.$jointeams->image;

                            $j++;
                        }
                        if (!empty($Json[$i]['jointeams'])) {
                            $Json[$i]['jointeams'] = Helpers::multid_sort($Json[$i]['jointeams'], 'usernumber');
                        }
                    }

                    if (empty($Json[$i]['jointeams'])) {
                        $Json[$i]['jointeams'] = [];
                    }
                    //     }
                    $i++;

                } else {
                    $Json['status'] = 0;
                    $Json['msg'] = 'Sorry,no data available!';
                }
                return response()->json($Json);die;
            }
        }
    }

    public function updateJoinedusers()
    {
        date_default_timezone_set('Asia/Kolkata');
        $dt = Carbon\Carbon::now();

        $x = 60 / 1;

        do {
            $matchdata =  DB::connection('mysql')->table('listmatches')->where('launch_status', 'launched')->where('fantasy_type', 'Cricket')->where('final_status', 'pending')->whereDate('start_date', '<=', date('Y-m-d'))->where('status', 'notstarted')->get();

            if (!empty($matchdata)) {
                foreach ($matchdata as $val) {
                    $allchallenges =  DB::connection('mysql')->table('matchchallenges')->where('matchkey', $val->matchkey)->where('status', 'opened')->get();
                    if (!empty($allchallenges)) {
                        foreach ($allchallenges as $vall) {
                            $existdata =  DB::connection('mysql')->table('joinedleauges')->where('matchkey', $val->matchkey)->where('challengeid', $vall->id)->count();
                            if (($existdata == $vall->maximum_user) && ($vall->contest_type == 'Amount')) {
                                //close challenge//
                                $updatedatas['status'] = 'closed';
                                $updatedatas['joinedusers'] = $existdata;
                                if ($vall->is_running == 1) {
                                    //new duplicate challenge//
                                    $newentry = json_decode(json_encode($vall), true);
                                    unset($newentry['id']);
                                    unset($newentry['joinedusers']);

                                    $entryfee = $vall->entryfee;
                                    $maximum_user = $vall->maximum_user;
                                    $win_amount = $vall->win_amount;
                                    $matchkey = $vall->matchkey;
                                    $contest_type = $vall->contest_type;
                                    $created_by = $vall->created_by;
                                    $id = $vall->id;

                                    $existdata =  DB::connection('mysql')->table('matchchallenges')->where('entryfee', $entryfee)->where('maximum_user', $maximum_user)->where('win_amount', $win_amount)->where('matchkey', $matchkey)->where('contest_type', $contest_type)->where('created_by', $created_by)->where('status', 'opened')->where('id', '!=', $id)->first();
                                    if (empty($existdata)) {
                                        $getcid = DB::connection('mysql2')->table('matchchallenges')->insertGetId($newentry);
                                        $findpricecards =  DB::connection('mysql')->table('matchpricecards')->where('challenge_id', $vall->id)->get();
                                        if (!empty($findpricecards)) {
                                            foreach ($findpricecards as $pricec) {
                                                $pdata = array();
                                                $pdata = json_decode(json_encode($pricec), true);
                                                unset($pdata['id']);
                                                $pdata['challenge_id'] = $getcid;

                                                DB::connection('mysql2')->table('matchpricecards')->insert($pdata);
                                            }
                                        }
                                    }
                                     DB::connection('mysql2')->table('matchchallenges')->where('id', $vall->id)->update($updatedatas);
                                }

                                DB::connection('mysql2')->table('matchchallenges')->where('id', $vall->id)->update($updatedatas);

                            }
                        }

                    }

                }
                return 1;
            }

        } while ($x-- > 0);
        return 1;
        die;
    }
    public function updateJoinedusers11($matchkey)
    {
        date_default_timezone_set('Asia/Kolkata');
        $dt = Carbon\Carbon::now();

        //  $x=60/1;

        //  do{
        // $matchdata = DB::table('listmatches')->where('matchkey',$matchkey)->get();
        // if (!empty($matchdata)) {
        //     foreach ($matchdata as $val) {
                $allchallenges = DB::table('matchchallenges')->where('matchkey', $matchkey)->where('status', 'opened')->get();
                if (!empty($allchallenges)) {
                        foreach ($allchallenges as $vall) {
                            $existdata =  DB::connection('mysql')->table('joinedleauges')->where('matchkey', $matchkey)->where('challengeid', $vall->id)->count();
                            if (($existdata == $vall->maximum_user) && ($vall->contest_type == 'Amount')) {
                                //close challenge//
                                $updatedatas['status'] = 'closed';
                                $updatedatas['joinedusers'] = $existdata;
                                if ($vall->is_running == 1) {
                                    //new duplicate challenge//
                                    $newentry = json_decode(json_encode($vall), true);
                                    unset($newentry['id']);
                                    unset($newentry['joinedusers']);

                                    $entryfee = $vall->entryfee;
                                    $maximum_user = $vall->maximum_user;
                                    $win_amount = $vall->win_amount;
                                    $matchkey = $vall->matchkey;
                                    $contest_type = $vall->contest_type;
                                    $created_by = $vall->created_by;
                                    $id = $vall->id;

                                    $existdata =  DB::connection('mysql')->table('matchchallenges')->where('entryfee', $entryfee)->where('maximum_user', $maximum_user)->where('win_amount', $win_amount)->where('matchkey', $matchkey)->where('contest_type', $contest_type)->where('created_by', $created_by)->where('status', 'opened')->where('id', '!=', $id)->first();
                                    if (empty($existdata)) {
                                        $getcid = DB::connection('mysql2')->table('matchchallenges')->insertGetId($newentry);
                                        $findpricecards =  DB::connection('mysql')->table('matchpricecards')->where('challenge_id', $vall->id)->get();
                                        if (!empty($findpricecards)) {
                                            foreach ($findpricecards as $pricec) {
                                                $pdata = array();
                                                $pdata = json_decode(json_encode($pricec), true);
                                                unset($pdata['id']);
                                                $pdata['challenge_id'] = $getcid;

                                                DB::connection('mysql2')->table('matchpricecards')->insert($pdata);
                                            }
                                        }
                                    }
                                     DB::connection('mysql2')->table('matchchallenges')->where('id', $vall->id)->update($updatedatas);
                                }

                                DB::connection('mysql2')->table('matchchallenges')->where('id', $vall->id)->update($updatedatas);

                            }
                        }

                    }
            // }
            // return 1;
        // }

        //  }
        // while($x-- > 0);
        return 1;
        die;
    }
    public function joinleauge(Request $request)
    {

        Helpers::setHeader(200);
        Helpers::timezone();
        $user = Helpers::isAuthorize($request);
        if ($request->isMethod('post')) {
            DB::beginTransaction();
            $input = $request->all();
            $geturl = Helpers::geturl();
            $user = Helpers::isAuthorize($request);
            $userid = $data['userid'] = $datass['userid'] = $loginUserid = $user->id;
            unset($input['auth_key']);
            $matchkey = $data['matchkey'] = $datass['matchkey'] = $request->get('matchkey');
            $listmatchestartdate =  DB::connection('mysql')->table('listmatches')->where('matchkey', $matchkey)->first();

            if (!empty($listmatchestartdate)) {
                $getcurrentdate = date('Y-m-d H:i:s');
                if ($getcurrentdate > date('Y-m-d H:i:s', strtotime($listmatchestartdate->start_date))) {
                    $Json['message'] = 'match closed';
                    $Json['success'] = false;
                    echo json_encode($Json);die;
                }
            } else {
                $Json['message'] = 'no match by this key';
                $Json['success'] = false;
                echo json_encode($Json);die;
            }

            $challengeid = $data['challengeid'] = $datass['challengeid'] = $request->get('challengeid');
            $teamid = $data['teamid'] = $request->get('teamid');

            $teamids = explode(',', $teamid);

            foreach ($teamids as $teamid) {

                if (!empty($teamid)) {
                    $findteam =  DB::connection('mysql')->table('jointeam')->where('id', $teamid)->where('matchkey', $matchkey)->first();
                    if (empty($findteam)) {
                        $Json['message'] = 'Invalid Team';
                        $Json['success'] = false;
                        echo json_encode($Json);die;
                    }
                    $data['teamid'] = $datass['teamid'] = $teamid;

                    $findjoinedleauges =  DB::connection('mysql')->table('joinedleauges')->where('teamid', $teamid)->where('challengeid', $challengeid)->where('matchkey', $matchkey)->where('userid', $userid)->first();
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
                    $findchallenge =  DB::connection('mysql')->table('matchchallenges')->where('id', $challengeid)->first();
                    if (!empty($findchallenge)) {

                        //find leauges already exist or not //
                        $findexistornot =  DB::connection('mysql')->table('joinedleauges')->where('userid', $userid)->where('challengeid', $challengeid)->where('matchkey', $matchkey)->select('teamid')->get();
                        $ab = $findexistornot->toArray();
                        if (!empty($ab)) {
                            if ($findchallenge->multi_entry == 0) {
                                $Json['success'] = false;
                                $Json['message'] = 'already joined';
                                echo json_encode($Json);die;
                            } else {
                                foreach ($findexistornot as $ff) {
                                    if ($ff->teamid == $teamid) {
                                        $Json['success'] = false;
                                        $Json['message'] = 'team already joined';
                                        echo json_encode($Json);die;
                                    }
                                }
                                /* check if there is any multi entry limit */
                                if ($findchallenge->multi_entry == 1) {
                                    $userteamscount = count($findexistornot);
                                    if ($userteamscount >= $findchallenge->team_limit) {
                                        //Helpers::setHeader(200);
                                        $Json['success'] = false;
                                        $Json['message'] = 'You cannot join with more teams now.';
                                        echo json_encode($Json);die;
                                    }
                                }

                            }
                        }
                        try {
                            //check for leauge closed or not //
                            $dataused = array();
                            $dataleft = array();
                            if ($loginUserid == $loginUserid) {
                                $nextteamcount = $findchallenge->joinedusers + 1;
                                $updatedata['joinedusers'] = $nextteamcount;
                            }
                            if ($findchallenge->contest_type == 'Amount') {
                                if ($updatedata['joinedusers'] > $findchallenge->maximum_user) {
                                    //Helpers::setHeader(200);
                                    $Json['success'] = false;
                                    $Json['message'] = 'league closed';
                                    echo json_encode($Json);die;
                                }
                            }

                            //start deduct money code for join leauge//
                            $finduserbalance =  DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
                            if (!empty($finduserbalance)) {
                                $totalbonus = 0;
                                $findentryfee = $findchallenge->entryfee;
                                /* find the current balance of users*/
                                $dataleft['bonus'] = $findbonusforuser = number_format($finduserbalance->bonus, 2, ".", "");
                                $dataleft['winning'] = number_format($finduserbalance->winning, 2, ".", "");
                                $dataleft['balance'] = number_format($finduserbalance->balance, 2, ".", "");
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
                                $totalreferral = $canusedferral = number_format($finduserbalance->referral_income, 2, ".", "");
                                $totbalan = number_format($finduserbalance->bonus + $finduserbalance->winning + $finduserbalance->balance + $finduserbalance->referral_income, 2, ".", "");
                                $findusablebalance = number_format($finduserbalance->balance + $finduserbalance->winning + $finduserbalance->referral_income, 2, ".", "");
                                $reminingfee = $findentryfee;
                                //start deduct money section//
                                if ($findchallenge->is_private != 1 && $findchallenge->is_bonus == 1) {
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
                                        echo json_encode($Json);die;
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
                                    echo json_encode($Json);die;
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

                                if ($findchallenge->contest_type == 'Percentage') {
                                    $result = DB::connection('mysql2')->insert("INSERT INTO `joinedleauges` ( `userid`, `challengeid`, `teamid`, `matchkey`, `transaction_id`) SELECT " . $userid . " , " . $challengeid . ", " . $teamid . ", '" . $matchkey . "', '" . $data['transaction_id'] . "' FROM DUAL");
                                    $challenge_insert_id = DB::select('SELECT LAST_INSERT_ID()');
                                } else {
                                    $result = DB::connection('mysql2')->insert("INSERT INTO `joinedleauges` ( `userid`, `challengeid`, `teamid`, `matchkey`, `transaction_id`) SELECT " . $userid . " , " . $challengeid . ", '" . $teamid . "', '" . $matchkey . "', '" . $data['transaction_id'] . "' FROM DUAL WHERE (SELECT COUNT(*) FROM joinedleauges WHERE challengeid=$challengeid) < " . $findchallenge->maximum_user);
                                    $challenge_insert_id = DB::select('SELECT LAST_INSERT_ID()');
                                }
                                if (!empty($result)) {

                                    $getinsertid = $challenge_insert_id[0]->{"LAST_INSERT_ID()"};
                                     DB::connection('mysql2')->table('joinedleauges')->where('id', $getinsertid)->update($data);
                                    if ($getinsertid != '0') {

                                        $data['refercode'] = $datass['refercode'] = $refercode . '' . $getinsertid;
                                        DB::connection('mysql2')->table('joinedleauges')->where('id', $getinsertid)->update($data);
                                        $joinedusers =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $challengeid)->count();
                                        $updatedata['joinedusers'] = $joinedusers;
                                        DB::connection('mysql2')->table('matchchallenges')->where('id', $challengeid)->update($updatedata);
                                        $datass['joinid'] = $getinsertid;
                                        DB::connection('mysql2')->table('joininfo')->insert($datass);
                                        //entry in leauges transactions//
                                        $dataused['matchkey'] = $matchkey;
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
                                        $transactiondata['transaction_id'] = $tranid . '-' . $userid;
                                        DB::connection('mysql2')->table('transactions')->insert($transactiondata);
                                        $updatedata['status'] = 'opened';
                                        if ($findchallenge->contest_type == 'Amount') {
                                            if ($updatedata['joinedusers'] == $findchallenge->maximum_user) {
                                                //close challenge//
                                                $updatedata['status'] = 'closed';
                                                if ($findchallenge->is_running == 1) {
                                                    //new duplicate challenge//
                                                    $newentry = json_decode(json_encode($findchallenge), true);
                                                    unset($newentry['id']);
                                                    unset($newentry['joinedusers']);
                                                    $entryfee = $findchallenge->entryfee;
                                                    $maximum_user = $findchallenge->maximum_user;
                                                    $win_amount = $findchallenge->win_amount;
                                                    $matchkey = $findchallenge->matchkey;
                                                    $contest_type = $findchallenge->contest_type;
                                                    $created_by = $findchallenge->created_by;
                                                    $id = $findchallenge->id;

                                                    $existdata =  DB::connection('mysql')->table('matchchallenges')->where('entryfee', $entryfee)->where('maximum_user', $maximum_user)->where('win_amount', $win_amount)->where('matchkey', $matchkey)->where('contest_type', $contest_type)->where('created_by', $created_by)->where('status', 'opened')->where('id', '!=', $id)->first();
                                                    if (empty($existdata)) {
                                                        $getcid = DB::connection('mysql2')->table('matchchallenges')->insertGetId($newentry);
                                                        $findpricecards =  DB::connection('mysql')->table('matchpricecards')->where('challenge_id', $findchallenge->id)->get();
                                                        if (!empty($findpricecards)) {
                                                            foreach ($findpricecards as $pricec) {
                                                                $pdata = array();
                                                                $pdata = json_decode(json_encode($pricec), true);
                                                                unset($pdata['id']);
                                                                $pdata['challenge_id'] = $getcid;
                                                                DB::connection('mysql2')->table('matchpricecards')->insert($pdata);
                                                            }
                                                        }
                                                    }
                                                }
                                                DB::connection('mysql2')->table('matchchallenges')->where('id', $challengeid)->update($updatedata);
                                            }
                                        } else {
                                            $updatedata['status'] = 'opened';
                                            $updatedata['matchkey'] = $findchallenge->matchkey;
                                            DB::connection('mysql2')->table('matchchallenges')->where('id', $challengeid)->update($updatedata);
                                        }
                                    } else {
                                        $updatedata['status'] = 'opened';
                                    }
                                    $findchallenge->status = $updatedata['status'];

                                    $findchallenge->joinedusers = $updatedata['joinedusers'];

                                    DB::connection('mysql2')->table('matchchallenges')
                                        ->where('id', $challengeid)
                                        ->update($updatedata);

                                    if ($findchallenge->joinedusers == $updatedata['joinedusers'] and $findchallenge->status == $updatedata['status']) {
                                        DB::connection('mysql2')->table('matchchallenges')->where('id', $challengeid)->update($updatedata);
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
                                        $Json['joinedusers'] = $updatedata['joinedusers'];
                                        if ($findchallenge->contest_type == 'Amount') {
                                            if ($findchallenge->maximum_user > 0) {
                                                $joinper = ($updatedata['joinedusers'] / $findchallenge->maximum_user) * 100;
                                            } else {
                                                $joinper = 0;
                                            }
                                        } else {
                                            $joinper = 100;
                                        }
                                        $Json['getjoinedpercentage'] = $joinper . '%';
                                        if ($findchallenge->is_private != "" || $findchallenge->is_private != 0) {
                                            $Json['is_private'] = 1;
                                        } else {
                                            $Json['is_private'] = 0;
                                        }
                                        if ($findchallenge->multi_entry != "" || $findchallenge->multi_entry != 0) {
                                            $Json['multi_entry'] = 1;
                                        } else {
                                            $Json['multi_entry'] = 0;
                                        }
                                        $Json['refercode'] = $data['refercode'];
                                    } else {
                                        DB::rollBack();
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            DB::rollBack();
                            echo $e->getMessage();
                            // Log::info($e);
                            die;
                        }
                    } else {
                        //Helpers::setHeader(200);
                        $Json['message'] = 'no challenge by this challengeid';
                        $Json['success'] = false;
                        $Json['status'] = 0;
                        return response()->json($Json);die;

                    }}}

            echo json_encode($Json);
            die;
        } else {
            $Json['status'] = 0;
            $Json['message'] = 'Unauthorized Request';
            return response()->json($Json);die;
        }
    }

    public function joinduoleauge(Request $request)
    {

        Helpers::setHeader(200);
        Helpers::timezone();
        $user = Helpers::isAuthorize($request);
        if ($request->isMethod('post')) {
            DB::beginTransaction();
            $input = $request->all();
            $geturl = Helpers::geturl();
            $user = Helpers::isAuthorize($request);
            $userid = $data['userid'] = $datass['userid'] = $loginUserid = $user->id;
            unset($input['auth_key']);
            $matchkey = $data['matchkey'] = $datass['matchkey'] = $request->get('matchkey');
            $challengeid = $data['challengeid'] = $datass['challengeid'] = $request->get('challengeid');
            $player_id = $data['player_id'] = $datass['player_id'] = $request->get('player_id');
            $listmatchestartdate =  DB::connection('mysql')->table('listmatches')->where('matchkey', $matchkey)->first();

            if (!empty($listmatchestartdate)) {
                $getcurrentdate = date('Y-m-d H:i:s');
                if ($getcurrentdate > date('Y-m-d H:i:s', strtotime($listmatchestartdate->start_date))) {
                    $Json['message'] = 'match closed';
                    $Json['success'] = false;
                    echo json_encode($Json);die;
                }
            } else {
                $Json['message'] = 'no match by this key';
                $Json['success'] = false;
                echo json_encode($Json);die;
            }

                if (!empty($player_id)) {
                    $findplayer =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $challengeid)->where('matchkey', $matchkey)->where('player_id', $player_id)->first();
                    if (!empty($findplayer)) {
                        $Json['message'] = 'You Can Not Join With This Player';
                        $Json['success'] = false;
                        echo json_encode($Json);die;
                    }
                    $findjoinedleauges =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $challengeid)->where('matchkey', $matchkey)->where('userid', $userid)->first();
                    if (!empty($findjoinedleauges)) {
                        $Json['message'] = 'You Can Join Only Single Contest';
                        $Json['success'] = false;
                        echo json_encode($Json);die;
                    }
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
                    $findchallenge =  DB::connection('mysql')->table('matchchallenges')->where('id', $challengeid)->first();
                    if (!empty($findchallenge)) {

                        //find leauges already exist or not //
                        $findexistornot =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $challengeid)->where('matchkey', $matchkey)->where('player_id', $player_id)->first();
                        if (!empty($findexistornot)) {
                            if ($findchallenge->multi_entry == 0) {
                                $Json['success'] = false;
                                $Json['message'] = 'already joined';
                                echo json_encode($Json);die;
                            } else {
                                /* check if there is any multi entry limit */
                                if ($findchallenge->multi_entry == 1) {
                                    $Json['success'] = false;
                                    $Json['message'] = 'already joined';
                                    echo json_encode($Json);die;
                                }
                            }
                        }
                        try {
                            //check for leauge closed or not //
                            $dataused = array();
                            $dataleft = array();
                            if ($loginUserid == $loginUserid) {
                                $nextteamcount = $findchallenge->joinedusers + 1;
                                $updatedata['joinedusers'] = $nextteamcount;
                            }
                            if ($findchallenge->contest_type == 'Amount') {
                                if ($updatedata['joinedusers'] > $findchallenge->maximum_user) {
                                    //Helpers::setHeader(200);
                                    $Json['success'] = false;
                                    $Json['message'] = 'league closed';
                                    echo json_encode($Json);die;
                                }
                            }

                            //start deduct money code for join leauge//
                            $finduserbalance =  DB::connection('mysql')->table('userbalance')->where('user_id', $userid)->first();
                            if (!empty($finduserbalance)) {
                                $totalbonus = 0;
                                $findentryfee = $findchallenge->entryfee;
                                /* find the current balance of users*/
                                $dataleft['bonus'] = $findbonusforuser = number_format($finduserbalance->bonus, 2, ".", "");
                                $dataleft['winning'] = number_format($finduserbalance->winning, 2, ".", "");
                                $dataleft['balance'] = number_format($finduserbalance->balance, 2, ".", "");
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
                                $totalreferral = $canusedferral = number_format($finduserbalance->referral_income, 2, ".", "");
                                $totbalan = number_format($finduserbalance->bonus + $finduserbalance->winning + $finduserbalance->balance + $finduserbalance->referral_income, 2, ".", "");
                                $findusablebalance = number_format($finduserbalance->balance + $finduserbalance->winning + $finduserbalance->referral_income, 2, ".", "");
                                $reminingfee = $findentryfee;
                                //start deduct money section//
                                if ($findchallenge->is_private != 1 && $findchallenge->is_bonus == 1) {
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
                                        echo json_encode($Json);die;
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
                                    echo json_encode($Json);die;
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
                                $data['fantasy_type'] = $datass['fantasy_type'] = 'Duo';
                                //insert leauge entry//

                                if ($findchallenge->contest_type == 'Percentage') {
                                    $result = DB::connection('mysql2')->insert("INSERT INTO `joinedleauges` ( `userid`, `challengeid`, `player_id`, `matchkey`, `transaction_id`,`fantasy_type`) SELECT " . $userid . " , " . $challengeid . ", " . $player_id . ", '" . $matchkey . "', '" . $data['transaction_id'] . "',  '" . $data['fantasy_type'] . "' FROM DUAL");
                                    $challenge_insert_id = DB::select('SELECT LAST_INSERT_ID()');
                                } else {
                                    $result = DB::connection('mysql2')->insert("INSERT INTO `joinedleauges` ( `userid`, `challengeid`, `player_id`, `matchkey`, `transaction_id`,`fantasy_type`) SELECT " . $userid . " , " . $challengeid . ", '" . $player_id . "', '" . $matchkey . "', '" . $data['transaction_id'] . "', '" . $data['fantasy_type'] . "' FROM DUAL WHERE (SELECT COUNT(*) FROM joinedleauges WHERE challengeid=$challengeid) < " . $findchallenge->maximum_user);
                                    $challenge_insert_id = DB::select('SELECT LAST_INSERT_ID()');
                                }
                                if (!empty($result)) {

                                    $getinsertid = $challenge_insert_id[0]->{"LAST_INSERT_ID()"};
                                     DB::connection('mysql2')->table('joinedleauges')->where('id', $getinsertid)->update($data);
                                    if ($getinsertid != '0') {

                                        $data['refercode'] = $datass['refercode'] = $refercode . '' . $getinsertid;
                                        DB::connection('mysql2')->table('joinedleauges')->where('id', $getinsertid)->update($data);
                                        $joinedusers =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $challengeid)->count();
                                        $updatedata['joinedusers'] = $joinedusers;
                                        DB::connection('mysql2')->table('matchchallenges')->where('id', $challengeid)->update($updatedata);
                                        $datass['joinid'] = $getinsertid;
                                        DB::connection('mysql2')->table('joininfo')->insert($datass);
                                        //entry in leauges transactions//
                                        $dataused['matchkey'] = $matchkey;
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
                                        $transactiondata['transaction_id'] = $tranid . '-' . $userid;
                                        DB::connection('mysql2')->table('transactions')->insert($transactiondata);
                                        $updatedata['status'] = 'opened';
                                        if ($findchallenge->contest_type == 'Amount') {
                                            if ($updatedata['joinedusers'] == $findchallenge->maximum_user) {
                                                //close challenge//
                                                $updatedata['status'] = 'closed';
                                                if ($findchallenge->is_running == 1) {
                                                    //new duplicate challenge//
                                                    $newentry = json_decode(json_encode($findchallenge), true);
                                                    unset($newentry['id']);
                                                    unset($newentry['joinedusers']);
                                                    $entryfee = $findchallenge->entryfee;
                                                    $maximum_user = $findchallenge->maximum_user;
                                                    $win_amount = $findchallenge->win_amount;
                                                    $matchkey = $findchallenge->matchkey;
                                                    $contest_type = $findchallenge->contest_type;
                                                    $created_by = $findchallenge->created_by;
                                                    $id = $findchallenge->id;

                                                    $existdata =  DB::connection('mysql')->table('matchchallenges')->where('entryfee', $entryfee)->where('maximum_user', $maximum_user)->where('win_amount', $win_amount)->where('matchkey', $matchkey)->where('contest_type', $contest_type)->where('created_by', $created_by)->where('status', 'opened')->where('id', '!=', $id)->first();
                                                    if (empty($existdata)) {
                                                        $getcid = DB::connection('mysql2')->table('matchchallenges')->insertGetId($newentry);
                                                        $findpricecards =  DB::connection('mysql')->table('matchpricecards')->where('challenge_id', $findchallenge->id)->get();
                                                        if (!empty($findpricecards)) {
                                                            foreach ($findpricecards as $pricec) {
                                                                $pdata = array();
                                                                $pdata = json_decode(json_encode($pricec), true);
                                                                unset($pdata['id']);
                                                                $pdata['challenge_id'] = $getcid;
                                                                DB::connection('mysql2')->table('matchpricecards')->insert($pdata);
                                                            }
                                                        }
                                                    }
                                                }
                                                DB::connection('mysql2')->table('matchchallenges')->where('id', $challengeid)->update($updatedata);
                                            }
                                        } else {
                                            $updatedata['status'] = 'opened';
                                            $updatedata['matchkey'] = $findchallenge->matchkey;
                                            DB::connection('mysql2')->table('matchchallenges')->where('id', $challengeid)->update($updatedata);
                                        }
                                    } else {
                                        $updatedata['status'] = 'opened';
                                    }
                                    $findchallenge->status = $updatedata['status'];

                                    $findchallenge->joinedusers = $updatedata['joinedusers'];

                                    DB::connection('mysql2')->table('matchchallenges')
                                        ->where('id', $challengeid)
                                        ->update($updatedata);

                                    if ($findchallenge->joinedusers == $updatedata['joinedusers'] and $findchallenge->status == $updatedata['status']) {
                                        DB::connection('mysql2')->table('matchchallenges')->where('id', $challengeid)->update($updatedata);
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
                                        $Json['joinedusers'] = $updatedata['joinedusers'];
                                        if ($findchallenge->contest_type == 'Amount') {
                                            if ($findchallenge->maximum_user > 0) {
                                                $joinper = ($updatedata['joinedusers'] / $findchallenge->maximum_user) * 100;
                                            } else {
                                                $joinper = 0;
                                            }
                                        } else {
                                            $joinper = 100;
                                        }
                                        $Json['getjoinedpercentage'] = $joinper . '%';
                                        if ($findchallenge->is_private != "" || $findchallenge->is_private != 0) {
                                            $Json['is_private'] = 1;
                                        } else {
                                            $Json['is_private'] = 0;
                                        }
                                        if ($findchallenge->multi_entry != "" || $findchallenge->multi_entry != 0) {
                                            $Json['multi_entry'] = 1;
                                        } else {
                                            $Json['multi_entry'] = 0;
                                        }
                                        $Json['refercode'] = $data['refercode'];
                                    } else {
                                        DB::rollBack();
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            DB::rollBack();
                            echo $e->getMessage();
                            // Log::info($e);
                            die;
                        }
                    } else {
                        //Helpers::setHeader(200);
                        $Json['message'] = 'no challenge by this challengeid';
                        $Json['success'] = false;
                        $Json['status'] = 0;
                        return response()->json($Json);die;

                    }}

            echo json_encode($Json);
            die;
        } else {
            $Json['status'] = 0;
            $Json['message'] = 'Unauthorized Request';
            return response()->json($Json);die;
        }
    }

    public function myjoinedleauges(Request $request)
    {
        Helpers::timezone();
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        unset($input['auth_key']);
        $matchkey = $data['matchkey'] = $_GET['matchkey'];
        $input['fantasy_type'] = $fantasy_type = $request->get('fantasy_type');
        if(empty($fantasy_type)){
            $input['fantasy_type'] = $fantasy_type='Cricket'; 
        }
        $input['duotype'] = $duotype = $request->get('duotype');
        
            $findjoinedleauges =  DB::connection('mysql')->table('joinedleauges')->where('joinedleauges.matchkey', $matchkey)
            ->where('joinedleauges.userid', $userid)
            ->where('joinedleauges.close_status', 1)
            ->join('matchchallenges', 'matchchallenges.id', '=', 'joinedleauges.challengeid')
            ->where('matchchallenges.fantasy_type', $fantasy_type)
            ->select('joinedleauges.*', 'matchchallenges.win_amount', 'matchchallenges.is_private', 'matchchallenges.status', 'matchchallenges.is_bonus', 'matchchallenges.bonus_percentage', 'matchchallenges.winning_percentage', 'matchchallenges.contest_type', 'matchchallenges.c_type', 'matchchallenges.confirmed_challenge', 'matchchallenges.multi_entry', 'matchchallenges.joinedusers', 'matchchallenges.entryfee', 'matchchallenges.pricecard_type', 'matchchallenges.maximum_user','matchchallenges.team_limit','matchchallenges.duotype')->orderBy('matchchallenges.win_amount', 'DESC')->groupBy('challengeid')->get();
        

            // dd($findjoinedleauges);
        $aa = $findjoinedleauges->toArray();
        if (!empty($aa)) {
            $i = 0;
            $Json = array();
            $js = array();
            $challarr = $jointeamarr = $pricecardarr = [];
            foreach ($findjoinedleauges as $joined) {
                $challarr[] = $joined->challengeid;
            }
            $respricecard =  DB::connection('mysql')->table('matchpricecards')->whereIn('challenge_id', $challarr)->orderBY('min_position', 'ASC')->get();
            foreach ($respricecard as $joined) {
                $pricecardarr[$joined->challenge_id][] = $joined;
            }
            $cid = 0;
            foreach ($findjoinedleauges as $joined) {

                if ($cid == $joined->challengeid) {
                    continue;
                }
                $cid = $joined->challengeid;
                if ($joined->status != 'canceled') {
                    
                    if($fantasy_type=='Duo'){
                        $resjoinedteams =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $joined->challengeid)->select('joinedleauges.duopoints as points', 'joinedleauges.userid', 'joinedleauges.teamid as teamnumber')->orderBy('joinedleauges.duopoints', 'DESC')->get();
                    }else{
                        $resjoinedteams =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $joined->challengeid)->join('jointeam', 'jointeam.id', '=', 'joinedleauges.teamid')->select('jointeam.points', 'jointeam.userid', 'jointeam.teamnumber')->orderBy('jointeam.points', 'DESC')->get();
                    }
                    $kj = 0;
                    $getcurrentrankarray = array();
                    if (!empty($resjoinedteams)) {
                        foreach ($resjoinedteams as $pleauges) {
                            if ($pleauges->points == '') {
                                $getcurrentrankarray[$kj]['points'] = 0;
                            } else {
                                $getcurrentrankarray[$kj]['points'] = $pleauges->points;
                            }
                            $getcurrentrankarray[$kj]['userid'] = $pleauges->userid;
                            $getcurrentrankarray[$kj]['teamnumber'] = $pleauges->teamnumber;
                            $kj++;
                        }
                    }
                    $gtcurranks = Helpers::multid_sort($getcurrentrankarray, 'points');
                    // dd($resjoinedteams);
                    if (!empty($gtcurranks)) {
                        $getusercurrank = array();
                        $cur = 0;
                        $currsno = 0;
                        $plus = 0;
                        foreach ($gtcurranks as $curnk) {
                            if (!in_array($curnk['points'], array_column($getusercurrank, 'points'))) { // search value in the array
                                $currsno++;
                                $currsno = $currsno + $plus;
                                $plus = 0;
                            } else {
                                $plus++;
                            }
                            $getusercurrank[$cur]['rank'] = $currsno;
                            $getusercurrank[$cur]['points'] = $curnk['points'];
                            $getusercurrank[$cur]['userid'] = $curnk['userid'];
                            $getusercurrank[$cur]['teamnumber'] = $curnk['teamnumber'];
                            $cur++;

                        }
                        
                    }
                    
                    $newuserarray = array();
                    if (!empty($getusercurrank)) {
                        foreach ($getusercurrank as $gtuser) {
                            if ($gtuser['userid'] == $userid) {
                                $newuserarray[] = $gtuser;
                            }
                        }
                    }
                    
                    $newuserarray = Helpers::multid_sort($newuserarray, 'points');
                    // dd($newuserarray);
                    $t = 0;
                    if (!empty($newuserarray)) {
                        $Json[$i]['userrank'] = $js[$i]['rank'] = $newuserarray[0]['rank'];
                        $Json[$i]['userpoints'] = $js[$i]['points'] = $newuserarray[0]['points'];
                        $Json[$i]['userteamnumber'] = $newuserarray[0]['teamnumber'];
                        $j = 0;
                        foreach ($newuserarray as $nu) {
                            $result =  DB::connection('mysql')->table('registerusers')->where('id', $user->id)->first();
                            $Json[$i]['teams'][$j]['rank'] = $nu['rank'];
                            $Json[$i]['teams'][$j]['points'] = $nu['points'];
                            $Json[$i]['teams'][$j]['winning_amount'] = 0;
                            $Json[$i]['teams'][$j]['teamnumber'] = $nu['teamnumber'];
                            $Json[$i]['teams'][$j]['teamname'] = strtoupper($result->team);
                            if (empty($result)) {
                                $Json[$i]['teams'][$j]['image'] = $geturl . 'public/avatar1.png';
                            } else {
                                $Json[$i]['teams'][$j]['image'] = 'public/'.$result->image;
                            }
                            $j++;
                        }
                    }
                    if ($joined->win_amount != "") {
                        $Json[$i]['name'] = ucwords('Win Rs. ' . $joined->win_amount);
                    } else {
                        $Json[$i]['name'] = '';
                    }
                    $Json[$i]['teamid'] = $joined->teamid;
                    $Json[$i]['refercode'] = $joined->refercode;
                    if ($joined->win_amount != "") {
                        $Json[$i]['win_amount'] = round($joined->win_amount);
                    } else {
                        $Json[$i]['win_amount'] = 0;
                    }
                    if ($joined->is_private != '') {
                        $Json[$i]['is_private'] = $joined->is_private;
                    } else {
                        $Json[$i]['is_private'] = '';
                    }
                    if ($joined->is_bonus != '') {
                        $Json[$i]['is_bonus'] = $joined->is_bonus;
                    } else {
                        $Json[$i]['is_bonus'] = 0;
                    }
                    if ($joined->bonus_percentage != '') {
                        $Json[$i]['bonus_percentage'] = $joined->bonus_percentage;
                    } else {
                        $Json[$i]['bonus_percentage'] = 0;
                    }
                    if ($joined->winning_percentage != '') {
                        $Json[$i]['winning_percentage'] = $joined->winning_percentage;
                    } else {
                        $Json[$i]['winning_percentage'] = 0;
                    }
                    if ($joined->contest_type != '') {
                        $Json[$i]['contest_type'] = $joined->contest_type;
                    } else {
                        $Json[$i]['contest_type'] = '';
                    }
                    $winning =  DB::connection('mysql')->table('finalresults')->where('challengeid', $joined->challengeid)->where('userid', $userid)->get();
                    $haps = 0;
                    $totalwinn = 0;
                    if (!empty($winning)) {
                        foreach ($winning as $vale) {
                            $totalwinn += $vale->amount;
                        }
                    }

                    $Json[$i]['totalwinning'] = number_format($totalwinn, 2, '.', '');
                    if ($joined->confirmed_challenge != '') {
                        $Json[$i]['confirmed'] = $joined->confirmed_challenge;
                    } else {
                        $Json[$i]['confirmed'] = 0;
                    }
                    if ($joined->multi_entry != '') {
                        $Json[$i]['multi_entry'] = $joined->multi_entry;
                        $Json[$i]['team_limit'] = $joined->team_limit;
                        $Json[$i]['plus'] = '+';
                    } else {
                        $Json[$i]['multi_entry'] = 0;
                        $Json[$i]['team_limit'] = 1;
                    }
                    $Json[$i]['joinid'] = $joined->id;
                    if ($joined->joinedusers != '') {
                        $Json[$i]['joinedusers'] = $joined->joinedusers;
                    } else {
                        $Json[$i]['joinedusers'] = 0;
                    }
                    if ($joined->matchkey != '') {
                        $Json[$i]['matchkey'] = $joined->matchkey;
                    } else {
                        $Json[$i]['matchkey'] = 0;
                    }
                    $Json[$i]['challenge_id'] = $joined->challengeid;
                    if ($joined->entryfee != '') {
                        $Json[$i]['entryfee'] = number_format($joined->entryfee, 2, '.', '');
                    } else {
                        $Json[$i]['entryfee'] = 0;
                    }
                    if ($joined->pricecard_type != '') {
                        $Json[$i]['pricecard_type'] = $joined->pricecard_type;
                    } else {
                        $Json[$i]['pricecard_type'] = '';
                    }
                    if ($joined->maximum_user > 0) {
                        $joinper = ($joined->joinedusers / $joined->maximum_user) * 100;
                    } else {
                        $joinper = 100;
                    }
                    $Json[$i]['getjoinedpercentage'] = round($joinper, 2) . '%';
                    if ($joined->maximum_user != '') {
                        $Json[$i]['maximum_user'] = $joined->maximum_user;
                    } else {
                        $Json[$i]['maximum_user'] = 0;
                    }

                    if (empty($joined->duotype)) {
                        $Json[$i]['duotype'] = '';
                    } else {
                        $Json[$i]['duotype'] = $joined->duotype;
                    }
                    $findpricecards = (isset($pricecardarr[$joined->challengeid])) ? $pricecardarr[$joined->challengeid] : [];
                    if (!empty($findpricecards)) {
                        $j = 0;
                        $winners = 0;
                        foreach ($findpricecards as $prc) {
                            $Json[$i]['price_card'][$j]['winners'] = $prc->winners;
                            $winners += $prc->winners;
                            if ($prc->type == 'Percentage') {
                                if($prc->winners==0){
                                    $ttlprice = 0;
                                    $Json[$i]['price_card'][$j]['price'] = $ttlprice;
                                    $Json[$i]['price_card'][$j]['price_percent'] = $prc->price_percent . '%';
                                }else{
                                    $ttlprice = $prc->total / $prc->winners;
                                    $Json[$i]['price_card'][$j]['price'] = $ttlprice;
                                    $Json[$i]['price_card'][$j]['price_percent'] = $prc->price_percent . '%';
                                }
                                
                            } else {
                                $Json[$i]['price_card'][$j]['price'] = $prc->price;
                            }
                            if ($prc->min_position + 1 != $prc->max_position) {
                                $Json[$i]['price_card'][$j]['start_position'] = $prc->min_position + 1 . '-' . $prc->max_position;
                            } else {
                                $Json[$i]['price_card'][$j]['start_position'] = $prc->max_position;
                            }
                            $j++;
                        }
                        $Json[$i]['totalwinners'] = $winners;
                        $Json[$i]['pricecardstatus'] = 1;
                    } else {
                        $ttlwinners = 1;
                        if ($joined->contest_type == 'Percentage') {
                            $ttlwinners = ($joined->joinedusers * $joined->winning_percentage) / 100;
                            $Json[$i]['totalwinners'] = floor($ttlwinners);

                        } else {
                            $Json[$i]['totalwinners'] = $ttlwinners;
                        }
                        $Json[$i]['pricecardstatus'] = 0;
                    }
                    $findmatchstatus =  DB::connection('mysql')->table('listmatches')->where('matchkey', $matchkey)->select('final_status')->first();
                    if (!empty($findmatchstatus)) {
                        $Json[$i]['type'] = 'classic';
                        $Json[$i]['status'] = 1;
                        $Json[$i]['success'] = true;
                        $Json[$i]['matchstatus'] = $findmatchstatus->final_status;
                    } else {
                        $Json[$i]['status'] = 0;
                        $Json[$i]['success'] = false;
                        $Json[$i]['matchstatus'] = 'not available';
                    }
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

    public function switchteams(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            $geturl = Helpers::geturl();
            $user = Helpers::isAuthorize($request);
            if ($user) {
                $teamid = array();
                $joinid = array();
                $userid = $data['userid'] = $user->id;
                unset($input['auth_key']);
                $matchkey = $data['matchkey'] = $_POST['matchkey'];
                $teamid = explode(',', $_POST['teamid']);
                $joinid = explode(',', $_POST['joinid']);
                $challenge_id = $data['challengeid'] = $_POST['challengeid'];
                $findmatchdetails =  DB::connection('mysql')->table('listmatches')->where('matchkey', $matchkey)->select('start_date')->first();
                if (!empty($findmatchdetails)) {
                    $getcurrentdate = date('Y-m-d H:i:s');
                    if ($getcurrentdate >= $findmatchdetails->start_date) {
                        Helpers::setHeader(200);
                        $Json['success'] = false;
                        $Json['message'] = 'match closed';
                        echo json_encode($Json);die;
                    }
                }
                $findchallenge =  DB::connection('mysql')->table('matchchallenges')->where('id', $challenge_id)->first();
                if (!empty($findchallenge)) {
                    $i = 0;
                    foreach ($teamid as $teamvalue) {
                        $data['teamid'] = $teamvalue;
                        DB::connection('mysql2')->table('joinedleauges')->where('challengeid', $challenge_id)->where('id', '=', $joinid[$i])->update($data);
                        DB::connection('mysql2')->table('joininfo')->where('challengeid', $challenge_id)->where('joinid', '=', $joinid[$i])->update($data);

                        $i++;
                    }
                    Helpers::setHeader(200);
                    $Json['message'] = 'Team Updated';
                    $Json['success'] = true;
                    echo json_encode($Json);
                    die;
                } else {
                    Helpers::setHeader(200);
                    $Json['message'] = 'challenge not exist';
                    $Json['success'] = false;
                    echo json_encode($Json);
                    die;
                }
            }
        }
    }

    // public function getUsableBalance(Request $request){
	// 	$input = $request->all();
	// 	$geturl = Helpers::geturl();
	// 	$user = Helpers::isAuthorize($request);
	// 	if($user){ 
	// 		$userid =  $data['userid'] = $user->id;
	// 		unset($input['auth_key']);
	// 		$challengeid = $request->get('challengeid');
	// 		$team_count = $request->get('team_count');
	// 		/* to find the challenge details */
	// 		$findchallengedetails =DB::connection('mysql2')->table('matchchallenges')->where('id',$challengeid)->first();
	// 		if(!empty($findchallengedetails)){
	// 			$entryfee = $findchallengedetails->entryfee *$team_count;
	// 			$maximumuser = $findchallengedetails->maximum_user;
	// 			/* to get the balance details */
	// 			$findwalletamount = DB::connection('mysql2')->table('userbalance')->where('user_id',$userid)->first();
	// 			if(!empty($findwalletamount)){
	// 				if($findchallengedetails->bonus_percentage != 0){
	// 				$findbonus = ($findchallengedetails->bonus_percentage/100) * $findwalletamount->bonus;
	// 				}else{
	// 						$findbonus = 0;
	// 				}
	// 				$findbal = $findwalletamount->balance;
	// 				$findbonus = $findwalletamount->bonus;
	// 				$findwining = $findwalletamount->winning;
	// 				$findtotalbalance =$findwalletamount->bonus + $findwalletamount->balance+$findwalletamount->winning;
	// 				/* calculate wining amount and balance amount */
	// 				$findusablebalance = $findwalletamount->balance+$findwalletamount->winning;
	// 			}else{
	// 				$findbonus = 0;
	// 				$findbal = 0;
	// 				$findwining = 0;
	// 				$findtotalbalance = 0;
	// 				/* calculate wining amount and balance amount */
	// 				$findusablebalance = 0;
	// 			}
	// 			$findbonusamount=0;
	// 			if($findchallengedetails->is_bonus==1){
	// 				$getbonuspercentage = $findchallengedetails->bonus_percentage;
	// 				if($getbonuspercentage){
	// 					$findbonusamount = ($getbonuspercentage/100)*$entryfee;
	// 				}
	// 			}
	// 			$usedbonus=0;
	// 			if($findbonus>=$findbonusamount){ $usedbonus= $findbonusamount;
	// 				//	$findusablebalance+=$findbonusamount;
	// 				}else{
	// 					$usedbonus = $findbonus;
	// 				//	$findusablebalance+=$findbonus;
	// 				}
				
	// 			$Json['usablebalance'] = round($findusablebalance,2);
	// 			$Json['usertotalbalance'] = round($findtotalbalance,2);
	// 			$Json['entryfee'] = round($entryfee,2);
	// 			$Json['bonus'] = round($usedbonus,2);
	// 		}else{
	// 			$Json['status'] = 'Invalid details';
	// 		}
	// 		return response()->json(array($Json));
	// 		die;
	// 	}
	// }

    public function getUsableBalance(Request $request)
    {
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $userid = $data['userid'] = $user->id;
        unset($input['auth_key']);
        $challengeid = $request->get('challengeid');
        $total_team_count = $request->get('total_team_count', 1);

        /* to find the challenge details */
        $findchallengedetails =  DB::connection('mysql')->table('matchchallenges')->where('id', $challengeid)->first();
        if (!empty($findchallengedetails)) {
            $data = $this->updateJoinedusers11($findchallengedetails->matchkey);
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
                $findtotalbalance = $findwalletamount->balance + $findwalletamount->winning;
                // $findtotalbalance =$findwalletamount->bonus + $findwalletamount->balance+$findwalletamount->winning;
                /* calculate wining amount and balance amount */
                $findusablebalance = $findwalletamount->balance + $findwalletamount->winning;
            } else {
                $findbonus = 0;
                $findbal = 0;
                $findwining = 0;
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
    public function joinedmatches(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $userid = $data['userid'] = $user->id;
            unset($input['auth_key']);

            $findmatches =  DB::connection('mysql')->table('joinedleauges')
                ->join('listmatches', 'listmatches.matchkey', '=', 'joinedleauges.matchkey')
                ->join('series', 'series.id', '=', 'listmatches.series')->where('joinedleauges.userid', $userid)->where('joinedleauges.close_status', 1)
                ->join('teams as t1', 't1.id', '=', 'listmatches.team1')
                ->join('teams as t2', 't2.id', '=', 'listmatches.team2')
                ->select('joinedleauges.*', 'listmatches.matchkey', 't1.team as team1fullname', 't2.team as team2fullname', 'listmatches.series', 'series.name as seriesname', 'listmatches.name', 'listmatches.team1', 'listmatches.team2', 'listmatches.start_date', 'listmatches.status', 'listmatches.launch_status', 'listmatches.final_status', 't1.logo as team1logo', 't2.logo as team2logo', 't1.color as team1color', 't2.color as team2color', 't2.short_name as team2short_name', 't1.short_name as team1short_name', 'listmatches.fantasy_type', 'series.id as series_id', 'listmatches.playing11_status', 'listmatches.second_inning_status')
                ->orderBY('listmatches.start_date', 'DESC')
                ->groupBy('joinedleauges.matchkey')
                ->get();
            $i = 0;
            $json = array();
            if (!empty($findmatches)) {
                foreach ($findmatches as $match) {

                    $findjoinedleauges =  DB::connection('mysql')->table('joinedleauges')
                        ->join('matchchallenges', 'matchchallenges.id', '=', 'joinedleauges.challengeid')
                        ->where('joinedleauges.matchkey', $match->matchkey)
                        ->where('matchchallenges.status', '!=', 'canceled')
                        ->where('joinedleauges.userid', $userid)
                        ->select('joinedleauges.teamid', 'joinedleauges.challengeid', 'joinedleauges.id as joinid', 'joinedleauges.refercode', 'matchchallenges.*')
                        ->groupBy('joinedleauges.challengeid')
                        ->orderBy('matchchallenges.win_amount', 'DESC')
                        ->get();
                    //     echo '<pre>';print_r($findmatches);die;
                    $abd = $findjoinedleauges->toArray();
                    if (!empty($abd)) {

                        $json[$i]['matchkey'] = $match->matchkey;
                        $json[$i]['name'] = $match->name;
                        $json[$i]['team1display'] = $match->team1short_name;
                        $json[$i]['team2display'] = $match->team2short_name;
                        $json[$i]['team1fullname'] = $match->team1fullname;
                        $json[$i]['team2fullname'] = $match->team2fullname;
                        $json[$i]['start_date'] = date('Y-m-d H:i:s', strtotime($match->start_date));
                        $locktimes = date('Y-m-d H:i:s', strtotime($match->start_date));
                        if (date('Y-m-d H:i:s') >= $locktimes) {
                            $json[$i]['status'] = 'closed';
                        } else {
                            $json[$i]['status'] = 'opened';
                        }
                        $json[$i]['launch_status'] = $match->launch_status;
                        $json[$i]['final_status'] = $match->final_status;
                        if ($match->second_inning_status == 2) {
                            $json[$i]['series_name'] = $match->seriesname . '- 2nd Inning Match';
                        } else {
                            $json[$i]['series_name'] = $match->seriesname;
                        }

                        $json[$i]['type'] = $match->fantasy_type;
                        $json[$i]['series_id'] = $match->series_id;
                        $joineduserss =  DB::connection('mysql')->table('joinedleauges')
                            ->where('joinedleauges.matchkey', $match->matchkey)->join('matchchallenges', 'matchchallenges.id', '=', 'joinedleauges.challengeid')
                            ->where('matchchallenges.status', '!=', 'canceled')
                            ->where('joinedleauges.userid', $userid)
                            ->groupBy('joinedleauges.challengeid')
                            ->select('joinedleauges.id')
                            ->get();
                        $jj = count($joineduserss);
                        $json[$i]['joinedcontest'] = $jj;
                        if (!empty($match->team1color)) {
                            $json[$i]['team1color'] = $match->team1color;
                        } else {
                            $json[$i]['team1color'] = '#FFFFFF';
                        }
                        if (!empty($match->team2color)) {
                            $json[$i]['team2color'] = $match->team2color;
                        } else {
                            $json[$i]['team2color'] = '#FFFFFF';
                        }

                        if ($match->team1logo != "") {
                            $json[$i]['team1logo'] = $geturl . 'public/' . $match->team1logo;
                        } else {
                            $json[$i]['team1logo'] = $geturl . 'public/team_image.png';
                        }
                        if ($match->team2logo != "") {
                            $json[$i]['team2logo'] = $geturl . 'public/' . $match->team2logo;
                        } else {
                            $json[$i]['team2logo'] = $geturl . 'public/team_image.png';
                        }
                        $json[$i]['available_status'] = 1;

                        $totalTeams =  DB::connection('mysql')->table('jointeam')
                            ->where('matchkey', $match->matchkey)
                            ->where('userid', $userid)
                            ->get();

                        $json[$i]['totalTeams'] = count($totalTeams);

                        $totalcontest =  DB::connection('mysql')->table('joinedleauges')
                            ->where('matchkey', $match->matchkey)
                            ->where('userid', $userid)
                            ->groupBy('challengeid')
                            ->get();

                        $json[$i]['totalcontest'] = count($totalcontest);

                        $maximum_winning_amount =  DB::connection('mysql')->table('matchchallenges')
                            ->where('matchkey', $match->matchkey)
                            ->orderBy('win_amount', 'DESC')
                            ->value('win_amount');

                        $json[$i]['maximum_winning_amount'] = $maximum_winning_amount;

                        $total_winning_amount =  DB::connection('mysql')->table('finalresults')
                            ->where('matchkey', $match->matchkey)
                            ->where('userid', $userid)
                            ->sum('amount');

                        $json[$i]['total_winning_amount'] = $total_winning_amount;
                        $json[$i]['playing11_status'] = $match->playing11_status;

                        $i++;
                    }
                }
                return response()->json($json);
            } else {
                $Json[$i]['available_status'] = 0;
                return response()->json($Json);
            }

        }
    }
    // join challenge by code//
    public function joinbycode(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            $user = Helpers::isAuthorize($request);
            if ($user) {
                $userid = $user->id;
                $Json = array();
                unset($input['auth_key']);
                $invitecode = $request->get('getcode');
                $matchkey = $request->get('matchkey');
                $split = explode('-', $invitecode);
                if ($split[0] == Helpers::settings()->short_name ?? '' . '$') {
                    $findchallengedata =  DB::connection('mysql')->table('joinedrefer')->where('matchkey', $matchkey)->where('refercode', $invitecode)->first();
                    if (!empty($findchallengedata)) {
                        $challengeid = $findchallengedata->challengeid;
                    } else {
                        $Json['message'] = 'invalid code';
                        $Json['success'] = false;
                        return response()->json($Json);die;
                    }

                } else {
                    $findchallengedd =  DB::connection('mysql')->table('joinedleauges')->where('matchkey', $matchkey)->where('refercode', $invitecode)->first();
                    if (!empty($findchallengedd)) {
                        $challengeid = $findchallengedd->challengeid;
                    } else {
                        $Json['message'] = 'invalid code';
                        $Json['success'] = false;
                        return response()->json($Json);die;
                    }
                }
                $findchallenge =  DB::connection('mysql')->table('matchchallenges')->where('matchkey', $matchkey)->where('id', $challengeid)->first();
                if($findchallenge->fantasy_type!='Duo'){
                    if (!empty($findchallenge)) {
                        $joinedchll =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $findchallenge->id)->where('userid', $userid)->count();
                        if ($findchallenge->multi_entry == 1) {
    
                            $Json['multi_entry'] = 1;
                            if ($joinedchll == 11) {
                                $Json['message'] = 'already used';
                                $Json['success'] = false;
                            } elseif ($findchallenge->status == 'closed') {
                                $Json['message'] = 'Challenge closed';
                                $Json['challengeid'] = "";
                                $Json['entryfee'] = "";
                                $Json['success'] = false;
                            } else {
                                $Json['message'] = 'Challenge opened';
                                $Json['challengeid'] = $findchallenge->id;
                                $Json['entryfee'] = $findchallenge->entryfee;
                                $Json['success'] = true;
                            }
                        } else {
    
                            $Json['multi_entry'] = 0;
    
                            if ($joinedchll == 0) {
                                $Json['message'] = 'already used';
                                $Json['success'] = false;
                            }
                            if ($findchallenge->status == 'closed') {
                                $Json['message'] = 'Challenge closed';
                                $Json['challengeid'] = "";
                                $Json['entryfee'] = "";
                                $Json['success'] = false;
                            } else {
                                $Json['message'] = 'Challenge opened';
                                $Json['challengeid'] = $findchallenge->id;
                                $Json['entryfee'] = $findchallenge->entryfee;
                                $Json['success'] = true;
                            }
                        }
                    } else {
                        $Json['message'] = 'invalid code';
                        $Json['success'] = 'false';
                    }
                }else{
                    $Json['message'] = 'This code belongs to Duo';
                    $Json['success'] = 'false';   
                }
                
                echo json_encode($Json);
                die;
            }
        }
    }

    // to get all the contest under the match //
    public function getContests(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $input['matchkey'] = $matchkey = $request->get('matchkey');
        $input['user_id'] = $user_id = $user->id;
        $findallcats =  DB::connection('mysql')->table('contest_category')->orderBy('created_at', 'ASC')->get();
        $input['fantasy_type'] = $fantasy_type = $request->get('fantasy_type');
        if(empty($fantasy_type)){
            $input['fantasy_type'] = $fantasy_type='Cricket'; 
        }
        $input['duotype'] = $duotype = $request->get('duotype');
        $Json = array();
        $i = 0;
        $count = 0;
        $data = $this->updateJoinedusers11($matchkey);
        // $data = $this->updateJoinedusers();
        // if ($data == 1) {
            if (!empty($findallcats)) {
                foreach ($findallcats as $cats) {
                    $k = 0;
                    $Json[$i]['catname'] = $cats->name;
                    $Json[$i]['catid'] = $cats->id;
                    $Json[$i]['sub_title'] = $cats->sub_title;

                    // $Json[$i]['image'] = $geturl.'public/'.$cats->image;
                    $Json[$i]['image'] = (!empty($cats->image) || $cats->image != '') ? $geturl . 'public/' . $cats->image : $geturl . 'public/logo.png';
                    if(!empty($duotype)){
                        $query =  DB::connection('mysql')->table('matchchallenges')->where('matchchallenges.fantasy_type', $fantasy_type)->where('matchchallenges.duotype', $duotype);
                    }else{
                        $query =  DB::connection('mysql')->table('matchchallenges')->where('matchchallenges.fantasy_type', $fantasy_type);
                    }
                    $query = $query->where('contest_cat', $cats->id);
                    if ($matchkey != "") {
                        $query = $query->where('matchkey', $matchkey);
                    }
                    $allchallenges = $query->where(function ($qq) {$qq->where('status', 'opened');})
                        ->orderBY('entryfee', 'DESC')->get();
                    $a = $allchallenges->toArray();
                    if (!empty($a)) {
                        foreach ($allchallenges as $challenege) {
                            $count++;
                            $Json[$i]['contest'][$k]['id'] = $challenege->id;
                            $Json[$i]['contest'][$k]['name'] = 'Win Rs.' . $challenege->win_amount;
                            $Json[$i]['contest'][$k]['entryfee'] = $challenege->entryfee;
                            $Json[$i]['contest'][$k]['offerentryfee'] = $challenege->offerentryfee;
                            $Json[$i]['contest'][$k]['contest_type'] = $challenege->contest_type;
                            $Json[$i]['contest'][$k]['winning_percentage'] = $challenege->winning_percentage;
                            $Json[$i]['contest'][$k]['win_amount'] = $challenege->win_amount;
                            if(!empty($duotype)){
                                $Json[$i]['contest'][$k]['duotype'] = $duotype;
                            }else{
                                $Json[$i]['contest'][$k]['duotype'] = '';
                            }
                            $Json[$i]['contest'][$k]['matchkey'] = $challenege->matchkey;
                            $Json[$i]['contest'][$k]['status'] = 1;
                            $ttljoinedusers =  DB::connection('mysql')->table('joininfo')->where('challengeid', $challenege->id)->count();
                            $Json[$i]['contest'][$k]['joinedusers'] = $ttljoinedusers;
                            if ($challenege->contest_type == 'Amount') {
                                if ($challenege->maximum_user > 0) {
                                    $getjoinedpercentage = round(($ttljoinedusers / $challenege->maximum_user) * 100, 2);
                                }
                                $Json[$i]['contest'][$k]['maximum_user'] = $challenege->maximum_user;

                            } else {
                                $getjoinedpercentage = 100;
                                $Json[$i]['contest'][$k]['maximum_user'] = 0;

                            }
                            $Json[$i]['contest'][$k]['bonus_type'] = ($challenege->bonus_percentage == '0') ? "Amount" : "Percentage";
                            $Json[$i]['contest'][$k]['getjoinedpercentage'] = $getjoinedpercentage . '%';
                            $Json[$i]['contest'][$k]['multi_entry'] = $challenege->multi_entry;
                            $Json[$i]['contest'][$k]['team_limit'] = $challenege->team_limit;
                            $Json[$i]['contest'][$k]['confirmed_challenge'] = $challenege->confirmed_challenge;
                            $Json[$i]['contest'][$k]['is_running'] = $challenege->is_running;
                            $Json[$i]['contest'][$k]['is_bonus'] = $challenege->is_bonus;
                            $Json[$i]['contest'][$k]['bonus_percentage'] = $challenege->bonus_percentage;
                            //         $Json[$i]['contest'][$k]['multientry_limit'] = $challenege->multientry_limit;
                            $Json[$i]['contest'][$k]['pricecard_type'] = $challenege->pricecard_type;
                            $Json[$i]['contest'][$k]['isselected'] = false;
                            $Json[$i]['contest'][$k]['isselectedid'] = "";

                            $joinedleauges =  DB::connection('mysql')->table('joininfo')->where('challengeid', $challenege->id)->where('userid', $user_id)->select('refercode')->get();

                            $aa = $joinedleauges->toArray();
                            if (!empty($aa)) {
                                if ($challenege->multi_entry == 1 && count($joinedleauges) < $challenege->team_limit) {
                                    $Json[$i]['contest'][$k]['isselected'] = false;
                                    $Json[$i]['contest'][$k]['refercode'] = $joinedleauges[0]->refercode;
                                } else {
                                    $Json[$i]['contest'][$k]['isselected'] = true;
                                    $Json[$i]['contest'][$k]['refercode'] = $joinedleauges[0]->refercode;
                                }
                            } else {
                                $Json[$i]['contest'][$k]['isselected'] = false;
                                $Json[$i]['contest'][$k]['refercode'] = "";
                            }
                            $query1 =  DB::connection('mysql')->table('matchpricecards');
                            $query1 = $query1->where('matchkey', $challenege->matchkey)->where('challenge_id', $challenege->id);
                            $allpricecard = $query1->orderBY('min_position', 'ASC')->get();
                            $aaa = $allpricecard->toArray();
                            $j = 0;
                            $winners = 0;
                            if (!empty($aaa)) {

                                foreach ($allpricecard as $prc) {
                                    if ($prc->price == 0) {
                                        $ttlprice = $prc->total / $prc->winners;
                                        $Json[$i]['contest'][$k]['price_card'][$j]['price'] = $ttlprice;
                                        $Json[$i]['contest'][$k]['price_card'][$j]['price_percent'] = $prc->price_percent . '%';
                                    } else {
                                        $Json[$i]['contest'][$k]['price_card'][$j]['price'] = $prc->price;
                                    }
                                    $Json[$i]['contest'][$k]['price_card'][$j]['winners'] = $prc->winners;
                                    $winners += $prc->winners;
                                    if ($prc->min_position + 1 != $prc->max_position) {
                                        $Json[$i]['contest'][$k]['price_card'][$j]['start_position'] = $prc->min_position + 1 . '-' . $prc->max_position;
                                    } else {
                                        $Json[$i]['contest'][$k]['price_card'][$j]['start_position'] = '' . $prc->max_position;
                                    }
                                    $Json[$i]['contest'][$k]['totalwinners'] = $winners;
                                    $j++;
                                }
                            } else {
                                $Json[$i]['contest'][$k]['totalwinners'] = 1;
                                $Json[$i]['contest'][$k]['price_card'][$j]['start_position'] = 1;
                                $Json[$i]['contest'][$k]['price_card'][$j]['price'] = $challenege->win_amount;
                            }

                            $k++;
                        }

                    } else {
                        $Json[$i]['contest'] = [];
                    }
                    $i++;
                }
            // }
        }
        return response()->json($Json);
        die;
    }
    public function getContestByCategory(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $id = $user->id;
        unset($input['auth_key']);
        $category = $request->get('category_id');
        $matchkey = $request->get('matchkey');
        $player_type = $request->get('player_type');
        $i = 0;
        $k = 0;
        $query =  DB::connection('mysql')->table('matchchallenges')->where('status', 'opened');
        if ($matchkey != "") {
            if ($category != "") {
                $allchallenges = $query->where(function ($qq) {$qq->where('status', 'opened')->orWhere('status', 'closed');})->where('contest_cat', $category)->where('matchkey', $matchkey)->orderBY('entryfee', 'ASC')->get();
                $a = $allchallenges->toArray();
                if (!empty($a)) {
                    foreach ($allchallenges as $challenege) {
                        $Json[$i]['id'] = $challenege->id;
                        $Json[$i]['name'] = 'Win Rs.' . $challenege->win_amount;
                        $Json[$i]['contest_type'] = $challenege->contest_type;
                        $Json[$i]['entryfee'] = $challenege->entryfee;
                        $Json[$i]['win_amount'] = $challenege->win_amount;
                        $getjoinedpercentage = 0;
                        if ($challenege->contest_type == 'Amount') {
                            $Json[$i]['maximum_user'] = $challenege->maximum_user;
                            if ($challenege->maximum_user > 0) {
                                $getjoinedpercentage = round(($challenege->joinedusers / $challenege->maximum_user) * 100, 2);
                            }
                        } else {
                            $getjoinedpercentage = 100;
                            $Json[$i]['maximum_user'] = 0;
                        }
                        $Json[$i]['matchkey'] = $challenege->matchkey;
                        $Json[$i]['status'] = 1;
                        $Json[$i]['joinedusers'] = $challenege->joinedusers;
                        $Json[$i]['winning_percentage'] = $challenege->winning_percentage;
                        $Json[$i]['getjoinedpercentage'] = $getjoinedpercentage . '%';
                        $Json[$i]['multi_entry'] = $challenege->multi_entry;
                        $Json[$i]['team_limit'] = $challenege->team_limit;
                        $Json[$i]['confirmed_challenge'] = $challenege->confirmed_challenge;
                        $Json[$i]['is_running'] = $challenege->is_running;
                        $Json[$i]['is_bonus'] = $challenege->is_bonus;
                        $Json[$i]['bonus_percentage'] = $challenege->bonus_percentage;
                        $Json[$i]['pricecard_type'] = $challenege->pricecard_type;
                        $Json[$i]['isselected'] = false;
                        $Json[$i]['isselectedid'] = "";
                        $joinedleauges =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $challenege->id)->where('userid', $id)->select('refercode')->get();
                        $aaa = $joinedleauges->toArray();
                        if (!empty($aaa)) {
                            if ($challenege->multi_entry == 1 && count($joinedleauges) < $challenege->multi_entry) {
                                $Json[$i]['isselected'] = false;
                                $Json[$i]['refercode'] = $joinedleauges[0]->refercode;
                            } else {
                                $Json[$i]['isselected'] = true;
                                $Json[$i]['refercode'] = $joinedleauges[0]->refercode;
                            }
                        } else {
                            $Json[$i]['isselected'] = false;
                            $Json[$i]['refercode'] = "";
                        }
                        $query1 =  DB::connection('mysql')->table('matchpricecards');
                        $query1 = $query1->where('matchkey', $challenege->matchkey)->where('challenge_id', $challenege->id);
                        $allpricecard = $query1->orderBY('min_position', 'ASC')->get();
                        $j = 0;
                        $winners = 0;
                        $aa = $allpricecard->toArray();
                        if (!empty($aa)) {
                            foreach ($allpricecard as $prc) {
                                if ($prc->type == 'per') {
                                    $Json[$i]['price_card'][$j]['price'] = $prc->price . '%';
                                } else {
                                    $Json[$i]['price_card'][$j]['price'] = $prc->price;
                                }
                                $Json[$i]['price_card'][$j]['winners'] = $prc->winners;
                                $winners += $prc->winners;
                                if ($prc->min_position + 1 != $prc->max_position) {
                                    $Json[$i]['price_card'][$j]['start_position'] = $prc->min_position + 1 . '-' . $prc->max_position;
                                } else {
                                    $Json[$i]['price_card'][$j]['start_position'] = '' . $prc->max_position;
                                }
                                $j++;
                            }
                            $Json[$i]['totalwinners'] = $winners;
                        } else {
                            $Json[$i]['price_card'][$j]['id'] = 0;
                            $Json[$i]['price_card'][$j]['winners'] = 1;
                            $Json[$i]['price_card'][$j]['price'] = $challenege->win_amount;
                            $Json[$i]['price_card'][$j]['start_position'] = 1;
                            $Json[$i]['price_card'][$j]['total'] = $challenege->win_amount;
                            $Json[$i]['totalwinners'] = 1;
                        }

                        $k++;
                        $i++;
                    }
                } else {
                    $Json[$i]['success'] = 0;
                    $Json[$i]['message'] = 'Sorry,no data available!';
                }
            } else {
                $Json[$i]['success'] = 0;
                $Json[$i]['message'] = 'Please enter a Category!';
            }
        } else {
            $Json[$i]['success'] = 0;
            $Json[$i]['message'] = 'Please enter a match key!';
        }
        return response()->json($Json);die;
    }
    // public function create_private_contest(Request $request){
    //        if ($request->isMethod('post')){
    //         $input = $request->all();
    //         $user = Helpers::isAuthorize($request);
    //         if($request->get('win_amount')>10000){
    //             $Json['success'] = false;
    //             $Json['message'] = 'Winning amount should be less than 10000.';
    //             echo json_encode($Json);
    //             die;
    //         }
    //         if($request->get('maximum_user')>100){
    //             $Json['success'] = false;
    //             $Json['message'] = 'League size should be less than 100.';
    //             echo json_encode($Json);
    //             die;
    //         }
    //            $userid = $data['created_by'] = $user->id;
    //            unset($input['auth_key']);
    //         $matchkey = $data['matchkey'] =  $request->get('matchkey');
    //         $maximum_user = $data['maximum_user'] = $request->get('maximum_user');
    //         $win_amount = $data['win_amount'] = $request->get('win_amount');
    //         $entryfee = $data['entryfee'] = $request->get('entryfee');
    //         $data['status'] = 'pending';
    //         $is_private = $data['is_private'] = 1;
    //         $data['multi_entry'] = $request->get('multi_entry');
    //         $data['contest_type'] = 'Amount';
    //         $data['contest_name'] = $request->get('name');
    //         $data['contest_cat'] = '0';
    //         $Json=array();
    //         if($maximum_user<2){
    //             $Json['success'] = false;
    //             $Json['message'] = 'Invalid league details. You cannot create a league with less then two members.';
    //             echo json_encode($Json);
    //             die;
    //         }
    //         /* insert into the contests*/
    //         $challengeid =DB::connection('mysql2')->table('matchchallenges')->insertGetId($data);
    //         $dataa =  DB::connection('mysql')->table('matchchallenges')->where('id',$challengeid)->select('entryfee')->first();

    //         $Json['success'] = true;
    //         $Json['message'] = 'Challenge successfully Created.';
    //         $Json['challengeid'] = $challengeid;
    //         $Json['entryfee'] = $dataa->entryfee;
    //         echo json_encode($Json);
    //         die;
    //     }
    // }
    public function create_private_contest(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);

        if ($request->isMethod('post')) {
            $input = $request->all();
            $user = Helpers::isAuthorize($request);
            if ($request->get('win_amount') > 10000) {
                $Json['success'] = false;
                $Json['message'] = 'Winning amount should be less than 10000.';
                return response()->json($Json);
                die;
            }
            if ($request->get('maximum_user') > 100) {
                $Json['success'] = false;
                $Json['message'] = 'League size should be less than 100.';
                return response()->json($Json);
                die;
            }
            $userid = $data['created_by'] = $user->id;
            unset($input['auth_key']);
            $matchkey = $data['matchkey'] = $request->get('matchkey');
            $maximum_user = $data['maximum_user'] = $request->get('maximum_user');
            $total_re = (($request->get('win_amount') * 15) / 100) + $request->get('win_amount');
            $win_amount = $data['win_amount'] = $request->get('win_amount');
            $entryfee = $data['entryfee'] = $request->get('entryfee');
            $data['status'] = 'pending';
            $is_private = $data['is_private'] = 1;
            $data['multi_entry'] = $request->get('multi_entry');
            $data['contest_type'] = 'Amount';
            $data['contest_name'] = $request->get('name');
            $data['contest_cat'] = '0';
            $Json = array();
            if ($maximum_user < 2) {
                $Json['success'] = false;
                $Json['message'] = 'Invalid league details. You cannot create a league with less then two members.';
                return response()->json($Json);
                die;
            }
            /* insert into the contests*/
            $rec = $request->get('entryfee') * $maximum_user;
            if ($rec < $total_re) {
                $Json['success'] = false;
                $Json['message'] = 'Invalid league details. You cannot create a Free League.';
                return response()->json($Json);
                die;
            } elseif ($rec < $request->get('win_amount')) {
                $Json['success'] = false;
                $Json['message'] = 'Invalid league details. You cannot create a Free League.';
                return response()->json($Json);
                die;
            } else {
                $challengeid = DB::connection('mysql2')->table('matchchallenges')->insertGetId($data);
                $dataa =  DB::connection('mysql')->table('matchchallenges')->where('id', $challengeid)->select('entryfee')->first();

                $Json['success'] = true;
                $Json['message'] = 'Challenge successfully Created.';
                $Json['challengeid'] = $challengeid;
                $Json['entryfee'] = $dataa->entryfee;
                return response()->json($Json);
                die;
            }

        }
    }

    public function myteam_joinedcontest(Request $request)
    {
        $input = $request->all();
        $user = Helpers::isAuthorize($request);
        if ($user) {
            $userid = $user->id;
            unset($input['auth_key']);
            $matchkey = $request->get('matchkey');
            $Json = array();
            $a =  DB::connection('mysql')->table('jointeam')->where('matchkey', $matchkey)->where('userid', $userid)->count();
            $b =  DB::connection('mysql')->table('joinedleauges')->where('matchkey', $matchkey)->where('userid', $userid)->groupBy('challengeid')->select('id')->get();
            $c = count($b);
            if (!empty($a)) {
                $Json['myteams'] = $a;
            } else {
                $Json['myteams'] = 0;
            }
            if (!empty($c)) {
                $Json['mycontests'] = $c;
            } else {
                $Json['mycontests'] = 0;
            }
            echo json_encode($Json);
            die;
        }
    }
    // join challenge by code//
    public function joinbycode_without_matchkey(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            $geturl = Helpers::geturl();
            $user = Helpers::isAuthorize($request);
            if ($user) {
                $userid = $user->id;
                $Json = array();
                unset($input['auth_key']);
                $invitecode = $request->get('getcode');

                $findchallenge =  DB::connection('mysql')->table('joinedleauges')
                    ->join('matchchallenges', 'matchchallenges.id', '=', 'joinedleauges.challengeid')
                    ->where('joinedleauges.refercode', $invitecode)
                    ->select('matchchallenges.status', 'matchchallenges.entryfee', 'matchchallenges.multi_entry', 'joinedleauges.*')
                    ->first();
                if (!empty($findchallenge)) {
                    $matchkeyy = $findchallenge->matchkey;
                    $matchdetail =  DB::connection('mysql')->table('listmatches')->Where('matchkey', $matchkeyy)->select('fantasy_type')->first();
                    $joinedchll =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $findchallenge->challengeid)->where('userid', $userid)->count();
                    if ($findchallenge->multi_entry == 1) {

                        $Json['multi_entry'] = 1;
                        if ($joinedchll == 11) {
                            $Json['message'] = 'already used';
                            $Json['success'] = false;
                        } elseif ($findchallenge->status == 'closed') {
                            $Json['message'] = 'Challenge closed';
                            $Json['challengeid'] = "";
                            $Json['entryfee'] = "";
                            $Json['entryfee'] = "";
                            $Json['success'] = false;
                        } else {
                            $Json['message'] = 'Challenge opened';
                            $Json['challengeid'] = $findchallenge->challengeid;
                            $Json['entryfee'] = $findchallenge->entryfee;
                            $Json['type'] = $matchdetail->fantasy_type;
                            $Json['success'] = true;
                        }
                    } else {

                        $Json['multi_entry'] = 0;

                        if ($joinedchll == 0) {
                            $Json['message'] = 'already used';
                            $Json['success'] = false;
                        }
                        if ($findchallenge->status == 'closed') {
                            $Json['message'] = 'Challenge closed';
                            $Json['challengeid'] = "";
                            $Json['entryfee'] = "";
                            $Json['success'] = false;
                        } else {
                            $Json['message'] = 'Challenge opened';
                            $Json['challengeid'] = $findchallenge->challengeid;
                            $Json['entryfee'] = $findchallenge->entryfee;
                            $Json['success'] = true;
                        }
                    }

                    $match =  DB::connection('mysql')->table('listmatches')
                        ->where('listmatches.matchkey', $findchallenge->matchkey)
                        ->join('series', 'listmatches.series', '=', 'series.id')
                        ->join('teams as team1', 'team1.id', '=', 'listmatches.team1')
                        ->join('teams as team2', 'team2.id', '=', 'listmatches.team2')
                        ->where('listmatches.final_status', '!=', 'IsCanceled')
                        ->where('listmatches.final_status', '!=', 'IsAbandoned')
                        ->where('series.status', 'opened')
                        ->select('listmatches.id as listmatchid', 'team1.short_name as teamname1', 'team2.short_name as teamname2', 'team1.color as team1color', 'team2.color as team2color', 'team1.logo as team1logo', 'team2.logo as team2logo', 'listmatches.series as seriesid', 'series.name as seriesname', 'listmatches.name', 'listmatches.start_date', 'listmatches.format', 'listmatches.matchkey', 'listmatches.final_status', 'listmatches.launch_status', 'listmatches.playing11_status', 'listmatches.fantasy_type')->orderBY('listmatches.start_date', 'ASC')
                        ->first();

                    if (!empty($match)) {

                        $Json['type'] = $match->fantasy_type;
                        $Json['match_key'] = $findchallenge->matchkey;
                        $Json['team1name'] = strtoupper($match->teamname1);
                        $Json['team2name'] = strtoupper($match->teamname2);
                        $Json['time_start'] = date('Y-m-d H:i:s', strtotime($match->start_date));
                        if (!empty($match->team1color)) {
                            $Json['team1color'] = $match->team1color;
                        } else {
                            $Json['team1color'] = '#ffffff';
                        }
                        if (!empty($match->team2color)) {
                            $Json['team2color'] = $match->team2color;
                        } else {
                            $Json['team2color'] = '#ffffff';
                        }
                        if ($match->team1logo != "") {
                            $Json['team1logo'] = $geturl . 'public/' . $match->team1logo;
                        } else {
                            $Json['team1logo'] = $geturl . 'public/logo.png';
                        }
                        if ($match->team2logo != "") {
                            $Json['team2logo'] = $geturl . 'public/' . $match->team2logo;
                        } else {
                            $Json['team2logo'] = $geturl . 'public/logo.png';
                        }
                        $Json['series'] = $match->seriesid;
                    }

                } else {
                    $Json['message'] = 'invalid code';
                    $Json['success'] = false;
                }
                echo json_encode($Json);
                die;
            }
        }
    }

    // join challenge by code//
    public function joinbycode2(Request $request)
    {

        if ($request->isMethod('post')) {
            $input = $request->all();
            $user = Helpers::isAuthorize($request);
            if ($user) {
                $userid = $user->id;
                $Json = array();
                unset($input['auth_key']);
                $invitecode = $request->get('getcode');
                $findchallenge =  DB::connection('mysql')->table('joinedleauges')->join('matchchallenges', 'matchchallenges.id', '=', 'joinedleauges.challengeid')->where('joinedleauges.refercode', $invitecode)->select('matchchallenges.status', 'matchchallenges.entryfee', 'matchchallenges.multi_entry', 'joinedleauges.*')->first();
                if (!empty($findchallenge)) {
                    $joinedchll =  DB::connection('mysql')->table('joinedleauges')->where('challengeid', $findchallenge->challengeid)->where('userid', $userid)->count();
                    if ($findchallenge->multi_entry == 1) {
                        if ($joinedchll == 9) {
                            $Json['message'] = 'already used';
                            $Json['success'] = false;
                        } elseif ($findchallenge->status == 'closed') {
                            $Json['message'] = 'Challenge closed';
                            $Json['challengeid'] = "";
                            $Json['entryfee'] = "";
                            $Json['success'] = false;
                        } else {
                            $Json['message'] = 'Challenge opened';
                            $Json['matchkey'] = $findchallenge->matchkey;
                            $Json['challengeid'] = $findchallenge->challengeid;
                            $Json['entryfee'] = $findchallenge->entryfee;
                            $Json['multi_entry'] = $findchallenge->multi_entry;
                            $Json['success'] = true;
                        }
                    } else {
                        if ($joinedchll == 0) {
                            $Json['message'] = 'already used';
                            $Json['success'] = false;
                        }
                        if ($findchallenge->status == 'closed') {
                            $Json['message'] = 'Challenge closed';
                            $Json['challengeid'] = "";
                            $Json['entryfee'] = "";
                            $Json['success'] = false;
                        } else {
                            $Json['message'] = 'Challenge opened';
                            $Json['matchkey'] = $findchallenge->matchkey;
                            $Json['challengeid'] = $findchallenge->challengeid;
                            $Json['entryfee'] = $findchallenge->entryfee;
                            $Json['multi_entry'] = $findchallenge->multi_entry;
                            $Json['success'] = true;
                        }
                    }
                } else {
                    $Json['message'] = 'invalid code';
                    $Json['success'] = false;
                }
                echo json_encode($Json);die;
            }
        }
    }

    //For Duo

    public function getDuoplayers(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $id = $user->id;
        unset($input['auth_key']);
        $input['matchkey'] = $matchkey = $request->get('matchkey');
        $allduoplayer =  DB::connection('mysql')->table('matchplayers')->where('matchkey', $matchkey)->where('forduo', 1)->get();

        $Json = array();
        $i = 0;
        $j=0;
        $a = $allduoplayer->toArray();
        if (!empty($a)) {
            $allduobatsman =  DB::connection('mysql')->table('matchplayers')->where('matchkey', $matchkey)->where('forduo', 1)->whereIn('role',['batsman','keeper'])->get();
            $allduobowler =  DB::connection('mysql')->table('matchplayers')->where('matchkey', $matchkey)->where('forduo', 1)->whereIn('role',['bowler','allrounder'])->get();
            foreach ($allduobatsman as $duplayer) {
                $Json['batsman'][$i]['id'] = $duplayer->id;
                $Json['batsman'][$i]['playerid'] = $duplayer->playerid;
                $Json['batsman'][$i]['role'] = $duplayer->role;
                $Json['batsman'][$i]['name'] = $duplayer->name;
                $Json['batsman'][$i]['playingstatus'] = $duplayer->playingstatus;
                $playerimage =  DB::connection('mysql')->table('players')->where('id', $duplayer->playerid)->first();
                $Json['batsman'][$i]['image'] = $geturl.'public/'.$playerimage->image;
                $i++;
            }
            foreach ($allduobowler as $duplayer) {
                $Json['bowler'][$j]['id'] = $duplayer->id;
                $Json['bowler'][$j]['playerid'] = $duplayer->playerid;
                $Json['bowler'][$j]['role'] = $duplayer->role;
                $Json['bowler'][$j]['name'] = $duplayer->name;
                $Json['bowler'][$j]['playingstatus'] = $duplayer->playingstatus;
                $playerimage =  DB::connection('mysql')->table('players')->where('id', $duplayer->playerid)->first();
                $Json['bowler'][$j]['image'] = $geturl.'public/'.$playerimage->image;
                $j++;
            }
            return response()->json(array($Json)); 
        } else {
            $Json = [];
        }
        return response()->json($Json);
        die;
    }

    public function investmentSummary(Request $request)
    {
        Helpers::timezone();
        Helpers::setHeader(200);
        $input = $request->all();
        $geturl = Helpers::geturl();
        $user = Helpers::isAuthorize($request);
        $id = $user->id;
        unset($input['auth_key']);
        $input['matchkey'] = $matchkey = $request->get('matchkey');

        $alljoinedleauge =  DB::connection('mysql')->table('joinedleauges')->join('matchchallenges', 'matchchallenges.id', 'joinedleauges.challengeid')->where('joinedleauges.matchkey', $matchkey)->where('matchchallenges.status','!=','canceled')->where('joinedleauges.userid', $id)->sum('entryfee');

        $alljoinedwinning =  DB::connection('mysql')->table('finalresults')->join('matchchallenges', 'matchchallenges.id', 'finalresults.challengeid')->where('finalresults.matchkey', $matchkey)->where('matchchallenges.status','!=', 'canceled')->where('finalresults.userid', $id)->sum('amount');

        // $allrefunds =  DB::connection('mysql')->table('refunds')->where('matchkey', $matchkey)->where('userid', $id)->sum('amount');

        $Json = array();
        if($alljoinedleauge==0 && $alljoinedwinning==0){
            $Json['investment']=0;
            $Json['Winning']=0;
            $Json['profit']=0;
        }else{
            $Json['investment']=$alljoinedleauge;
            $Json['Winning']=$alljoinedwinning;
            $Json['profit']=$Json['Winning']-$Json['investment'];
        }
        return response()->json($Json);
        die;
    }

    
}
