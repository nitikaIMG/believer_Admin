<?php
namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPExcel_IOFactory;
use Response;
use File;
class CronJobController extends Controller
{   

    public function setReminderNotification(){
        date_default_timezone_set('Asia/Kolkata');
        $setReminderdata = DB::connection('mysql2')->table('setmatchreminder')->where('status',0)->get();
        // $setReminderdata = DB::connection('mysql2')->table('setmatchreminder')->where('id',475)->get();

        if(!empty($setReminderdata)){
            foreach($setReminderdata as $value){
                $teamssdta =DB::connection('mysql2')->table('listmatches')->where('playing11_status',1)->where('matchkey',$value->matchkey)->join('teams as t1','t1.id','=','listmatches.team1')->join('teams as t2','t2.id','=','listmatches.team2')->select('t1.short_name as t1name','t2.short_name as t2name','listmatches.playing11_status as playing11_status','listmatches.start_date')->first();
                // echo "<pre>";print_r($value);die;
                if(!empty($teamssdta)){
                    $msg = 'Match starts at '.date('H:i A',strtotime($teamssdta->start_date)).'! Create your Believer11 now!';
                    $titleget = strtoupper($teamssdta->t1name) .' VS '. strtoupper($teamssdta->t2name) . ' Playing XI Out!';
                    $notification['userid'] = $value->user_id;
                    $notification['seen'] = 0;
                    
                   Helpers::sendnotification($titleget,$msg,'',$value->user_id);
                   
                   $data = array();
                   $data['status'] = 1;
                   DB::table('setmatchreminder')->where('id',$value->id)->update($data);
                }
            }
        }
        echo 'completed';die;
    }

    public function dailyreport(Request $request)
    {
        $date = date('Y-m-d');
        $pdate = date('Y-m-d', strtotime(date('Y-m-d') . '-5 day'));

        $data = DB::table('listmatches')->where('final_status', 'winnerdeclared')->where('fantasy_type', 'cricket')->whereDate('start_date', '>=', $pdate)->groupBy(DB::raw('DATE(start_date)'))->get(['start_date']);

        $dates = [];
        if (!empty($data->toArray())) {
            foreach ($data as $d) {

                $day = DB::table('listmatches')->join('profitloss', 'profitloss.matchkey', 'listmatches.matchkey')->where('listmatches.final_status', 'winnerdeclared')->where('listmatches.fantasy_type', 'cricket')->where('listmatches.start_date', 'LIKE', '%' . date('Y-m-d', strtotime($d->start_date)) . '%')->get(['listmatches.start_date', 'listmatches.final_status', 'listmatches.matchkey', 'profitloss.profit_or_loss', 'profitloss.amount_profit_or_loss']);
                if (!empty($day->toArray())) {
                    dump(date('Y-m-d', strtotime($d->start_date)));
                    $rem_amount = 0;
                    $profit = 0;
                    $loss = 0;
                    foreach ($day as $val) {
                        if ($val->profit_or_loss == 'Loss') {
                            $loss += $val->amount_profit_or_loss;
                        }
                        if ($val->profit_or_loss == 'Profit') {
                            $profit += $val->amount_profit_or_loss;
                        }

                    }
                    $received = DB::table('paymentprocess')->whereIn('status', ['success', 'SUCCESS'])->where('created_at', 'LIKE', '%' . date('Y-m-d', strtotime($d->start_date)) . '%')->sum('amount');
                    $withdraw = DB::table('withdraw')->where('status', 1)->where('approved_date', 'LIKE', '%' . date('Y-m-d', strtotime($d->start_date)) . '%')->sum('amount');
                    $cashfreeR = ($received * 2) / 100;
                    $cashfreeW = ($withdraw * 2) / 100;
                    $rem_amount = $profit - $loss - $cashfreeR - $cashfreeW;
                    $info['report_date'] = date('Y-m-d', strtotime($d->start_date));
                    $info['net_amount'] = number_format($rem_amount, 2, ".", "");
                    $info['total_received'] = number_format($received, 2, ".", "");
                    $info['total_withdraw'] = number_format($withdraw, 2, ".", "");
                    $info['cashfreeRper'] = number_format($cashfreeR, 2, ".", "");
                    $info['cashfreeWper'] = number_format($cashfreeW, 2, ".", "");
                    $info['profit'] = number_format($profit, 2, ".", "");
                    $info['loss'] = number_format($loss, 2, ".", "");
                    $daily_data = DB::table('daily_report')->where('report_date', 'LIKE', '%' . date('Y-m-d', strtotime($d->start_date)) . '%')->first();

                    if (!empty($daily_data)) {
                        $up = DB::connection('mysql2')->table('daily_report')->where('id', $daily_data->id)->update($info);

                    } else {
                        DB::connection('mysql2')->table('daily_report')->insert($info);
                    }

                }

            }
        }
    }

    public function carddailyreport(Request $request)
    {
        $date = date('Y-m-d');
        $pdate = date('Y-m-d', strtotime(date('Y-m-d') . '-25 day'));
        $data = DB::table('cardchallenges')->whereIn('status' ,['winnerdeclared','lefted'])->whereDate('created_at', '>=', $pdate)->groupBy(DB::raw('DATE(created_at)'))->get(['created_at']);
        $dates = [];
        if (!empty($data->toArray())) {
            foreach ($data as $d) {

                $day = DB::table('cardchallenges')->join('cardprofitloss', 'cardprofitloss.matchkey', 'cardchallenges.id')->whereIn('status' ,['winnerdeclared','lefted'])->whereDate('cardchallenges.created_at', '>=', date('Y-m-d', strtotime($d->created_at)))->get(['cardchallenges.created_at', 'cardchallenges.status', 'cardchallenges.id', 'cardprofitloss.profit_or_loss', 'cardprofitloss.amount_profit_or_loss']);
                if (!empty($day->toArray())) {
                    // print_r($day);
                    $rem_amount = 0;
                    $profit = 0;
                    $loss = 0;
                    foreach ($day as $val) {
                        if ($val->profit_or_loss == 'Loss') {
                            $loss += $val->amount_profit_or_loss;
                        }
                        if ($val->profit_or_loss == 'Profit') {
                            $profit += $val->amount_profit_or_loss;
                        }
                        // dump($val,$profit,$profit);die;

                    }
                    $received = DB::table('paymentprocess')->whereIn('status', ['success', 'SUCCESS'])->where('created_at', 'LIKE', '%' . date('Y-m-d', strtotime($d->created_at)) . '%')->sum('amount');
                    $withdraw = DB::table('withdraw')->where('status', 1)->where('approved_date', 'LIKE', '%' . date('Y-m-d', strtotime($d->created_at)) . '%')->sum('amount');
                    $cashfreeR = ($received * 2) / 100;
                    $cashfreeW = ($withdraw * 2) / 100;
                    $rem_amount = $profit - $loss - $cashfreeR - $cashfreeW;
                    $info['report_date'] = date('Y-m-d', strtotime($d->created_at));
                    $info['net_amount'] = number_format($rem_amount, 2, ".", "");
                    $info['total_received'] = number_format($received, 2, ".", "");
                    $info['total_withdraw'] = number_format($withdraw, 2, ".", "");
                    $info['cashfreeRper'] = number_format($cashfreeR, 2, ".", "");
                    $info['cashfreeWper'] = number_format($cashfreeW, 2, ".", "");
                    $info['profit'] = number_format($profit, 2, ".", "");
                    $info['loss'] = number_format($loss, 2, ".", "");
                    $daily_data = DB::table('carddaily_report')->where('report_date', 'LIKE', '%' . date('Y-m-d', strtotime($d->created_at)) . '%')->first();

                    if (!empty($daily_data)) {
                        $up = DB::connection('mysql2')->table('carddaily_report')->where('id', $daily_data->id)->update($info);

                    } else {
                        DB::connection('mysql2')->table('carddaily_report')->insert($info);
                    }

                }

            }
        }
        return redirect()->back()->with('success','Daily Report Succesfully Created');
    }

    public function uploadExcelll(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            require_once './PHPExcel/PHPExcel/IOFactory.php';
            if (isset($_FILES["excel_file"]["name"])) {
                $path = $_FILES["excel_file"]["tmp_name"];
                $object = PHPExcel_IOFactory::load($path);
                foreach ($object->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    $starti = 1;
                    $counti = 1;
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $data['transferid'] = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $data['amount'] = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $data['status'] = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $data['beneficiary'] = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $data['referenceid'] = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                        $data['date'] = $worksheet->getCellByColumnAndRow(0, $row)->getValue();

                        DB::connection('mysql2')->table('check_withdraw')->insert($data);
                    }

                }
            }
        }
    }

    public function youtubernotification()
    {
        $wonyoutubers = DB::table('youtuber_bonus')->where('status', 0)->get();
        foreach ($wonyoutubers as $value) {
            $notification['userid'] = $value->userid;
            $notification['seen'] = 0;
            $titleget = "Youtuber Profit";
            $msg = $notification['title'] = 'You have received Rs. ' . number_format($value->amount, 2, '.', '') . ' as profit from your referred users';
            Helpers::sendnotification($titleget, $msg, '', $value->userid);

            $data = array();
            $data['status'] = 1;
            DB::connection('mysql2')->table('youtuber_bonus')->where('id', $value->id)->update($data);
        }
    }
    

    // public function changeprice($challengeid)
    // {
    //     date_default_timezone_set('Asia/Kolkata');
    //     // $findchallenge = DB::table('matchchallenges')->where('id',$challengeid)->where('price_status',0)->first();
    //     $findchallenge = DB::table('matchchallenges')->where('id', $challengeid)->where('entryfee', '!=', 0)->where('pricecard_type', 'Percentage')->where('price_status', 0)->first();
    //     //   dump($findchallenge);
    //     if (!empty($findchallenge)) {
    //         LOG::info('contest Compression for Contest id------' . $findchallenge->id);
    //         // LOG::info(date('Y-m-d H:i:s'));
    //         $bonus_used = DB::table('leaugestransactions')->where('challengeid', $challengeid)->sum('bonus');
    //         $contest_winners = DB::table('matchpricecards')->where('challenge_id', $challengeid)->sum('winners');
    //         $pricecards = DB::table('matchpricecards')->where('challenge_id', $challengeid)->get();
    //         // dump(!empty($pricecards->toArray()) && $findchallenge->joinedusers>0);
    //         if (!empty($pricecards->toArray()) && $findchallenge->joinedusers > 0) {
    //             $maxusers = $findchallenge->maximum_user;
    //             dump('maxusers------' . $maxusers);
    //             $joined = $findchallenge->joinedusers;
    //             dump('joined------' . $joined);
    //             $win_amt = $findchallenge->win_amount;
    //             dump('win_amt------' . $win_amt);
    //             $entryfee = $findchallenge->entryfee;
    //             dump('entryfee------' . $entryfee);
    //             $amount_before_live = $entryfee * $maxusers;
    //             dump('amount_before_live------' . $amount_before_live);
    //             $distribution_percentage = number_format(($win_amt / $amount_before_live) * 100, 2, '.', '');
    //             dump('distribution_percentage------' . $distribution_percentage);
    //             $received_amt = $entryfee * $joined;
    //             dump('received_amt------' . $received_amt);
    //             dump('bonus used by users--------' . $bonus_used);
    //             $received_amt = $received_amt - $bonus_used;
    //             dump('received amount after bonus deduction ---------' . $received_amt);
    //             // if contest is full then contest winning amount will not compress
    //             if ($maxusers == $joined) {
    //                 $new_win_amt = $win_amt;
    //             } else {
    //                 $new_win_amt = number_format(floor((($received_amt * $distribution_percentage) / 100)), 2, '.', '');
    //             }
    //             // $new_win_amt = number_format((($received_amt*$distribution_percentage)/100),2,'.','');
    //             dump('new_win_amt------' . $new_win_amt);
    //             if ($joined > $contest_winners) {
    //                 $newwinners = $contest_winners;
    //             } else {
    //                 if ($joined == 1) {
    //                     $newwinners = 1;
    //                     $new_win_amt = $entryfee;
    //                 } else {
    //                     $newwinners = round(($joined * 40) / 100);
    //                     if ($newwinners > 0 && $newwinners < 1) {
    //                         $newwinners = 1;
    //                     }
    //                 }
    //             }

    //             dump('newwinners------' . $newwinners);
    //             $distributed_amt = 0;
    //             $distributed = [];
    //             $ranked = $pricecards->pluck('price_percent', 'max_position');
    //             $ranked_new = $ranked->toArray();
    //             dump($ranked->toArray());
    //             $ranks = array_keys($ranked->toArray());
    //             $x = $newwinners;
    //             // dd($x);
    //             $z = array_reduce($ranks, function ($max, $number) use ($x) {
    //                 dump('max----' . $number);
    //                 // Ignore if greater
    //                 if ($number >= $x) {
    //                     return $max;
    //                 }

    //                 // First one less than x
    //                 if ($max === null) {
    //                     return $number;
    //                 }

    //                 // Replace if greater
    //                 return $max < $number ? $number : $max;

    //             }, null);
    //             // dump('z');dump($z);
    //             // dd($ranks);
    //             // $key_index = $ranked_new[$ranks[array_search($z, $ranks)+1]];
    //             // $key_index = (isset($ranks[array_search($z, $ranks)+1]))?$ranked_new[$ranks[array_search($z, $ranks)+1]]:$ranked_new[$ranks[array_search($z, $ranks)]];
    //             if (empty($z)) {
    //                 $key_index = (isset($ranks[array_search($z, $ranks) + 0])) ? $ranked_new[$ranks[array_search($z, $ranks) + 0]] : $ranked_new[$ranks[array_search($z, $ranks)]];
    //                 // dump('Ranked New --------'.isset($ranks[array_search($z, $ranks)]));
    //             } else {
    //                 $key_index = (isset($ranks[array_search($z, $ranks) + 1])) ? $ranked_new[$ranks[array_search($z, $ranks) + 1]] : $ranked_new[$ranks[array_search($z, $ranks)]];
    //             }
    //             $key = array_search($key_index, $ranked_new);

    //             $data = array_filter($ranked_new, function ($v) use ($key) {return $v <= $key;}, ARRAY_FILTER_USE_KEY);
    //             dump($data);
    //             $i = 0;
    //             $rem_amount = 0;
    //             $winn = [];
    //             $ids = '';
    //             foreach ($pricecards as $key => $value) {
    //                 if ($i < count($data)) {
    //                     $amts = ($new_win_amt * $value->price_percent) / 100;
    //                     $amts_total = 0;
    //                     dump('fdgffghjhgjhg----------' . $i);
    //                     // dd($value->max_position.'--------'.$newwinners);

    //                     if ($value->max_position <= $newwinners) {
    //                         $amts_total = $amts * $value->winners;
    //                         $ids = ($ids != '') ? $ids . ',' . $value->id : $value->id;
    //                         $winn[$i]['id'] = $value->id;
    //                         $winn[$i]['min_position'] = $value->min_position;
    //                         $winn[$i]['max_position'] = $value->max_position;
    //                         $winn[$i]['winners'] = $value->winners;
    //                         $winn[$i]['amount_per'] = number_format($amts, 2, '.', '');
    //                         $winn[$i]['total'] = $amts_total;
    //                     } else {

    //                         $winuserforwinnnn = ((isset($pricecards[$i - 1])) ? $pricecards[$i - 1]->max_position : $pricecards[$i]->max_position);
    //                         $winr = ($newwinners > $winuserforwinnnn) ? $newwinners - $winuserforwinnnn : $newwinners;
    //                         // $ids = ($ids!='')?$ids.','.$value->id:$value->id;
    //                         // $winn[$i]['id'] = $value->id;
    //                         // $winn[$i]['min_position'] = $value->min_position;
    //                         // $winn[$i]['max_position'] = $value->max_position;
    //                         // $winn[$i]['winners'] = $winr;
    //                         // $winn[$i]['amount_per'] = number_format($amts,2,'.','');
    //                         // $winn[$i]['total'] = $amts*$winr;
    //                         // $amts_total = $amts*$winr;
    //                         if ($winr > 0) {
    //                             $ids = ($ids != '') ? $ids . ',' . $value->id : $value->id;
    //                             $winn[$i]['id'] = $value->id;
    //                             $winn[$i]['min_position'] = $value->min_position;
    //                             $winn[$i]['max_position'] = $value->max_position;
    //                             $winn[$i]['winners'] = $winr;
    //                             $winn[$i]['amount_per'] = number_format($amts, 2, '.', ''); // amount per user
    //                             $winn[$i]['total'] = $amts * $winr;
    //                             $amts_total = $amts * $winr;
    //                         }
    //                     }
    //                     $distributed[] = $amts_total;
    //                 } else {

    //                     break;
    //                 }
    //                 $i++;
    //             }
    //             $rem_amount = $new_win_amt - array_sum($distributed);
    //             dump('Distributed amount-----------------' . array_sum($distributed));
    //             dump('Remaining------------------' . $rem_amount);
    //             dump($winn);
    //             $single_person_amt = number_format(($rem_amount / $newwinners), 2, '.', '');
    //             dump('Profit to every winner-------------------' . $single_person_amt);
    //             $total_win = 0;
    //             $d = array_map(function ($v) use ($single_person_amt, $new_win_amt, $total_win) {
    //                 // $amt_per = ($v['winners'])?
    //                 // $v['amount_per'] = number_format(floor($v['amount_per']) + $single_person_amt,2,'.','');
    //                 $v['amount_per'] = number_format($v['amount_per'] + $single_person_amt, 2, '.', '');
    //                 $v['total'] = number_format($v['amount_per'] * $v['winners'], 2, '.', '');
    //                 $total_win += $v['total'];
    //                 $v['new_percentage'] = number_format(($v['amount_per'] / $new_win_amt) * 100, 2, '.', '');
    //                 return $v;
    //             }, $winn);
    //             dump($d);
    //             // dd($total_win);
    //             foreach ($d as $k => $val) {
    //                 if ($val['max_position'] < $newwinners) {
    //                     DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $val['total'], 'price_percent' => $val['new_percentage']]);
    //                 } else {
    //                     $w = $newwinners - $val['min_position'];
    //                     if ($joined == 1) {
    //                         DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $new_win_amt, 'price_percent' => 100, 'max_position' => $newwinners, 'winners' => $w]);
    //                         break;
    //                     } else {
    //                         DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $val['total'], 'price_percent' => $val['new_percentage'], 'max_position' => $newwinners, 'winners' => $w]);
    //                     }
    //                     //         DB::table('matchpricecards')->where('id',$val['id'])->update(['total'=>$val['total'],'price_percent'=>$val['new_percentage'],'max_position'=>$newwinners,'winners'=>$w]);
    //                 }

    //             }
    //             $ids = explode(',', $ids);
    //             $wins = array_sum(array_column($d, 'total'));
    //             dump('new winning_amount           ' . $wins);
    //             DB::table('matchpricecards')->where('challenge_id', $challengeid)->whereNotIn('id', $ids)->delete();
    //             DB::table('matchchallenges')->where('id', $challengeid)->update(['win_amount' => $wins, 'price_status' => 1]);
    //             // echo '<pre>';print_r($d);

    //         }
    //     }
    // }

    public function getallmatchestocompress(Request $request)
    {
        $list = DB::table('listmatches')->where('launch_status', 'launched')->whereIn('status', ['started', 'completed'])->whereIn('final_status', ['pending', 'IsReviewed'])->get();
        if (!empty($list->toArray())) {
            foreach ($list as $key => $value) {
                // dump($value->matchkey);
                $challenges = DB::table('matchchallenges')
                    ->join('matchpricecards', 'matchpricecards.challenge_id', 'matchchallenges.id')
                    ->where('matchchallenges.matchkey', $value->matchkey)
                    ->where('matchchallenges.fantasy_type','!=', 'Duo')
                    ->where('matchchallenges.pricecard_type', 'Percentage')
                    ->where('matchchallenges.joinedusers', '>', 0)
                    ->where('matchchallenges.price_status', 0)
                    ->select('matchchallenges.*', DB::raw('COUNT(matchpricecards.challenge_id) as totalpricecards'))
                    ->groupBy('matchchallenges.id')
                    ->having('totalpricecards', '>', 0)
                    ->get();

                if (!empty($challenges->toArray())) {
                    foreach ($challenges as $k => $val) {
                        $this->changeprice($val->id);
                    }
                }
                // dump($challenges->toArray());
            }
        }
        // dd($list);
    }

    
    // public function changePricecard($challengeid)
    // {
        //     date_default_timezone_set('Asia/Kolkata');
        //     // $findchallenge = DB::table('matchchallenges')->where('id',$challengeid)->where('price_status',0)->first();
        //     $findchallenge = DB::table('matchchallenges')->where('id', $challengeid)->where('entryfee', '!=', 0)->where('pricecard_type', 'Percentage')->where('price_status', 0)->first();
        //     //   dump($findchallenge);
        //     if (!empty($findchallenge)) {
        //         // LOG::info('contest Compression for Contest id------'.$findchallenge->id);
        //         // LOG::info(date('Y-m-d H:i:s'));
        //         $bonus_used = DB::table('leaugestransactions')->where('challengeid', $challengeid)->sum('bonus');
        //         $contest_winners = DB::table('matchpricecards')->where('challenge_id', $challengeid)->sum('winners');
        //         $pricecards = DB::table('matchpricecards')->where('challenge_id', $challengeid)->get();
        //         // dump(!empty($pricecards->toArray()) && $findchallenge->joinedusers>0);
        //         if (!empty($pricecards->toArray()) && $findchallenge->joinedusers > 0) {
        //             $maxusers = $findchallenge->maximum_user;
        //             dump('maxusers------' . $maxusers);
        //             $joined = $findchallenge->joinedusers;
        //             dump('joined------' . $joined);
        //             $win_amt = $findchallenge->win_amount;
        //             dump('win_amt------' . $win_amt);
        //             $entryfee = $findchallenge->entryfee;
        //             dump('entryfee------' . $entryfee);
        //             $amount_before_live = $entryfee * $maxusers;
        //             dump('amount_before_live------' . $amount_before_live);
        //             $distribution_percentage = number_format(($win_amt / $amount_before_live) * 100, 2, '.', '');
        //             dump('distribution_percentage------' . $distribution_percentage);
        //             $received_amt = $entryfee * $joined;
        //             dump('received_amt------' . $received_amt);
        //             dump('bonus used by users--------' . $bonus_used);
        //             $received_amt = $received_amt - $bonus_used;
        //             dump('received amount after bonus deduction ---------' . $received_amt);
        //             // if contest is full then contest winning amount will not compress
        //             if ($maxusers == $joined) {
        //                 $new_win_amt = $win_amt;
        //             } else {
        //                 $new_win_amt = number_format(floor((($received_amt * $distribution_percentage) / 100)), 2, '.', '');
        //             }
        //             // $new_win_amt = number_format((($received_amt*$distribution_percentage)/100),2,'.','');
        //             dump('new_win_amt------' . $new_win_amt);
        //             if ($joined > $contest_winners) {
        //                 $newwinners = $contest_winners;
        //             } else {
        //                 if ($joined == 1) {
        //                     $newwinners = 1;
        //                     $new_win_amt = $entryfee;
        //                 } else {
        //                     $newwinners = round(($joined * 40) / 100);
        //                     if ($newwinners > 0 && $newwinners < 1) {
        //                         $newwinners = 1;
        //                     }
        //                 }
        //             }

        //             dump('newwinners------' . $newwinners);
        //             $distributed_amt = 0;
        //             $distributed = [];
        //             $ranked = $pricecards->pluck('price_percent', 'max_position');
        //             $ranked_new = $ranked->toArray();
        //             dump($ranked->toArray());
        //             $ranks = array_keys($ranked->toArray());
        //             $x = $newwinners;
        //             // dd($x);
        //             $z = array_reduce($ranks, function ($max, $number) use ($x) {
        //                 dump('max----' . $number);
        //                 // Ignore if greater
        //                 if ($number >= $x) {
        //                     return $max;
        //                 }

        //                 // First one less than x
        //                 if ($max === null) {
        //                     return $number;
        //                 }

        //                 // Replace if greater
        //                 return $max < $number ? $number : $max;

        //             }, null);
        //             // dump('z');dump($z);
        //             // dd($ranks);
        //             // $key_index = $ranked_new[$ranks[array_search($z, $ranks)+1]];
        //             // $key_index = (isset($ranks[array_search($z, $ranks)+1]))?$ranked_new[$ranks[array_search($z, $ranks)+1]]:$ranked_new[$ranks[array_search($z, $ranks)]];
        //             if (empty($z)) {
        //                 $key_index = (isset($ranks[array_search($z, $ranks) + 0])) ? $ranked_new[$ranks[array_search($z, $ranks) + 0]] : $ranked_new[$ranks[array_search($z, $ranks)]];
        //                 // dump('Ranked New --------'.isset($ranks[array_search($z, $ranks)]));
        //             } else {
        //                 $key_index = (isset($ranks[array_search($z, $ranks) + 1])) ? $ranked_new[$ranks[array_search($z, $ranks) + 1]] : $ranked_new[$ranks[array_search($z, $ranks)]];
        //             }
        //             $key = array_search($key_index, $ranked_new);

        //             $data = array_filter($ranked_new, function ($v) use ($key) {return $v <= $key;}, ARRAY_FILTER_USE_KEY);
        //             dump($data);
        //             $i = 0;
        //             $rem_amount = 0;
        //             $winn = [];
        //             $ids = '';
        //             foreach ($pricecards as $key => $value) {
        //                 if ($i < count($data)) {
        //                     $amts = ($new_win_amt * $value->price_percent) / 100;
        //                     $amts_total = 0;
        //                     dump('fdgffghjhgjhg----------' . $i);
        //                     // dd($value->max_position.'--------'.$newwinners);

        //                     if ($value->max_position <= $newwinners) {
        //                         $amts_total = $amts * $value->winners;
        //                         $ids = ($ids != '') ? $ids . ',' . $value->id : $value->id;
        //                         $winn[$i]['id'] = $value->id;
        //                         $winn[$i]['min_position'] = $value->min_position;
        //                         $winn[$i]['max_position'] = $value->max_position;
        //                         $winn[$i]['winners'] = $value->winners;
        //                         $winn[$i]['amount_per'] = number_format($amts, 2, '.', '');
        //                         $winn[$i]['total'] = $amts_total;
        //                     } else {

        //                         $winuserforwinnnn = ((isset($pricecards[$i - 1])) ? $pricecards[$i - 1]->max_position : $pricecards[$i]->max_position);
        //                         $winr = ($newwinners > $winuserforwinnnn) ? $newwinners - $winuserforwinnnn : $newwinners;
        //                         // $ids = ($ids!='')?$ids.','.$value->id:$value->id;
        //                         // $winn[$i]['id'] = $value->id;
        //                         // $winn[$i]['min_position'] = $value->min_position;
        //                         // $winn[$i]['max_position'] = $value->max_position;
        //                         // $winn[$i]['winners'] = $winr;
        //                         // $winn[$i]['amount_per'] = number_format($amts,2,'.','');
        //                         // $winn[$i]['total'] = $amts*$winr;
        //                         // $amts_total = $amts*$winr;
        //                         if ($winr > 0) {
        //                             $ids = ($ids != '') ? $ids . ',' . $value->id : $value->id;
        //                             $winn[$i]['id'] = $value->id;
        //                             $winn[$i]['min_position'] = $value->min_position;
        //                             $winn[$i]['max_position'] = $value->max_position;
        //                             $winn[$i]['winners'] = $winr;
        //                             $winn[$i]['amount_per'] = number_format($amts, 2, '.', ''); // amount per user
        //                             $winn[$i]['total'] = $amts * $winr;
        //                             $amts_total = $amts * $winr;
        //                         }
        //                     }
        //                     $distributed[] = $amts_total;
        //                 } else {

        //                     break;
        //                 }
        //                 $i++;
        //             }
        //             $rem_amount = $new_win_amt - array_sum($distributed);
        //             dump('Distributed amount-----------------' . array_sum($distributed));
        //             dump('Remaining------------------' . $rem_amount);
        //             dump($winn);
        //             $single_person_amt = number_format(($rem_amount / $newwinners), 2, '.', '');
        //             dump('Profit to every winner-------------------' . $single_person_amt);
        //             $total_win = 0;
        //             $d = array_map(function ($v) use ($single_person_amt, $new_win_amt, $total_win) {
        //                 // $amt_per = ($v['winners'])?
        //                 // $v['amount_per'] = number_format(floor($v['amount_per']) + $single_person_amt,2,'.','');
        //                 $v['amount_per'] = number_format($v['amount_per'] + $single_person_amt, 2, '.', '');
        //                 $v['total'] = number_format($v['amount_per'] * $v['winners'], 2, '.', '');
        //                 $total_win += $v['total'];
        //                 $v['new_percentage'] = number_format(($v['amount_per'] / $new_win_amt) * 100, 2, '.', '');
        //                 return $v;
        //             }, $winn);
        //             dump($d);
        //             // dd($total_win);
        //             foreach ($d as $k => $val) {
        //                 if ($val['max_position'] < $newwinners) {
        //                     DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $val['total'], 'price_percent' => $val['new_percentage']]);
        //                 } else {
        //                     $w = $newwinners - $val['min_position'];
        //                     if ($joined == 1) {
        //                         DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $new_win_amt, 'price_percent' => 100, 'max_position' => $newwinners, 'winners' => $w]);
        //                         break;
        //                     } else {
        //                         DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $val['total'], 'price_percent' => $val['new_percentage'], 'max_position' => $newwinners, 'winners' => $w]);
        //                     }
        //                     //         DB::table('matchpricecards')->where('id',$val['id'])->update(['total'=>$val['total'],'price_percent'=>$val['new_percentage'],'max_position'=>$newwinners,'winners'=>$w]);
        //                 }

        //             }
        //             $ids = explode(',', $ids);
        //             $wins = array_sum(array_column($d, 'total'));
        //             dump('new winning_amount           ' . $wins);
        //             DB::table('matchpricecards')->where('challenge_id', $challengeid)->whereNotIn('id', $ids)->delete();
        //             DB::table('matchchallenges')->where('id', $challengeid)->update(['win_amount' => $wins, 'price_status' => 1]);
        //             // echo '<pre>';print_r($d);

        //         }
        //     }
    // }
    public function changePricecard($challengeid)
    {
        date_default_timezone_set('Asia/Kolkata');
        // $findchallenge = DB::table('matchchallenges')->where('id',$challengeid)->where('price_status',0)->first();
        $findchallenge = DB::table('matchchallenges')->where('id', $challengeid)->where('entryfee', '!=', 0)->where('pricecard_type', 'Percentage')->where('price_status', 0)->first();
        $totaljoineduser = DB::connection('mysql')->table('joinedleauges')->where('challengeid',$challengeid)->count();
        //   dump($findchallenge);
        if (!empty($findchallenge)) {
            LOG::info('contest Compression for Contest id------' . $findchallenge->id);
            // LOG::info(date('Y-m-d H:i:s'));
            $bonus_used = DB::table('leaugestransactions')->where('challengeid', $challengeid)->sum('bonus');
            $contest_winners = DB::table('matchpricecards')->where('challenge_id', $challengeid)->sum('winners');
            $pricecards = DB::table('matchpricecards')->where('challenge_id', $challengeid)->get();
            // dump(!empty($pricecards->toArray()) && $findchallenge->joinedusers>0);
            if (!empty($pricecards->toArray()) && $totaljoineduser > 0) {
                $maxusers = $findchallenge->maximum_user;
                dump('maxusers------' . $maxusers);
                $joined = $totaljoineduser;
                dump('joined------' . $joined);
                $win_amt = $findchallenge->win_amount;
                dump('win_amt------' . $win_amt);
                $entryfee = $findchallenge->entryfee;
                dump('entryfee------' . $entryfee);
                $amount_before_live = $entryfee * $maxusers;
                dump('amount_before_live------' . $amount_before_live);
                $distribution_percentage = number_format(($win_amt / $amount_before_live) * 100, 2, '.', '');
                dump('distribution_percentage------' . $distribution_percentage);
                $received_amt = $entryfee * $joined;
                dump('received_amt------' . $received_amt);
                dump('bonus used by users--------' . $bonus_used);
                $received_amt = $received_amt - $bonus_used;
                dump('received amount after bonus deduction ---------' . $received_amt);
                // if contest is full then contest winning amount will not compress
                if ($maxusers == $joined) {
                    $new_win_amt = $win_amt;
                } else {
                    $new_win_amt = number_format(floor((($received_amt * $distribution_percentage) / 100)), 2, '.', '');
                }
                // $new_win_amt = number_format((($received_amt*$distribution_percentage)/100),2,'.','');
                dump('new_win_amt------' . $new_win_amt);
                if ($joined > $contest_winners) {
                    $newwinners = $contest_winners;
                } else {
                    if ($joined == 1) {
                        $newwinners = 1;
                        $new_win_amt = $entryfee;
                    } else {
                        $newwinners = round(($joined * 40) / 100);
                        if ($newwinners > 0 && $newwinners < 1) {
                            $newwinners = 1;
                        }
                    }
                }

                dump('newwinners------' . $newwinners);
                $distributed_amt = 0;
                $distributed = [];
                $ranked = $pricecards->pluck('price_percent', 'max_position');
                $ranked_new = $ranked->toArray();
                dump($ranked->toArray());
                $ranks = array_keys($ranked->toArray());
                $x = $newwinners;
                // dd($x);
                $z = array_reduce($ranks, function ($max, $number) use ($x) {
                    dump('max----' . $number);
                    // Ignore if greater
                    if ($number >= $x) {
                        return $max;
                    }

                    // First one less than x
                    if ($max === null) {
                        return $number;
                    }

                    // Replace if greater
                    return $max < $number ? $number : $max;

                }, null);
                // dump('z');dump($z);
                // dd($ranks);
                // $key_index = $ranked_new[$ranks[array_search($z, $ranks)+1]];
                // $key_index = (isset($ranks[array_search($z, $ranks)+1]))?$ranked_new[$ranks[array_search($z, $ranks)+1]]:$ranked_new[$ranks[array_search($z, $ranks)]];
                if (empty($z)) {
                    $key_index = (isset($ranks[array_search($z, $ranks) + 0])) ? $ranked_new[$ranks[array_search($z, $ranks) + 0]] : $ranked_new[$ranks[array_search($z, $ranks)]];
                    // dump('Ranked New --------'.isset($ranks[array_search($z, $ranks)]));
                } else {
                    $key_index = (isset($ranks[array_search($z, $ranks) + 1])) ? $ranked_new[$ranks[array_search($z, $ranks) + 1]] : $ranked_new[$ranks[array_search($z, $ranks)]];
                }
                $key = array_search($key_index, $ranked_new);

                $data = array_filter($ranked_new, function ($v) use ($key) {return $v <= $key;}, ARRAY_FILTER_USE_KEY);
                dump($data);
                $i = 0;
                $rem_amount = 0;
                $winn = [];
                $ids = '';
                foreach ($pricecards as $key => $value) {
                    if ($i < count($data)) {
                        $amts = ($new_win_amt * $value->price_percent) / 100;
                        $amts_total = 0;
                        dump('fdgffghjhgjhg----------' . $i);
                        // dd($value->max_position.'--------'.$newwinners);

                        if ($value->max_position <= $newwinners) {
                            $amts_total = $amts * $value->winners;
                            $ids = ($ids != '') ? $ids . ',' . $value->id : $value->id;
                            $winn[$i]['id'] = $value->id;
                            $winn[$i]['min_position'] = $value->min_position;
                            $winn[$i]['max_position'] = $value->max_position;
                            $winn[$i]['winners'] = $value->winners;
                            $winn[$i]['amount_per'] = number_format($amts, 2, '.', '');
                            $winn[$i]['total'] = $amts_total;
                        } else {

                            $winuserforwinnnn = ((isset($pricecards[$i - 1])) ? $pricecards[$i - 1]->max_position : $pricecards[$i]->max_position);
                            $winr = ($newwinners > $winuserforwinnnn) ? $newwinners - $winuserforwinnnn : $newwinners;
                            // $ids = ($ids!='')?$ids.','.$value->id:$value->id;
                            // $winn[$i]['id'] = $value->id;
                            // $winn[$i]['min_position'] = $value->min_position;
                            // $winn[$i]['max_position'] = $value->max_position;
                            // $winn[$i]['winners'] = $winr;
                            // $winn[$i]['amount_per'] = number_format($amts,2,'.','');
                            // $winn[$i]['total'] = $amts*$winr;
                            // $amts_total = $amts*$winr;
                            if ($winr > 0) {
                                $ids = ($ids != '') ? $ids . ',' . $value->id : $value->id;
                                $winn[$i]['id'] = $value->id;
                                $winn[$i]['min_position'] = $value->min_position;
                                $winn[$i]['max_position'] = $value->max_position;
                                $winn[$i]['winners'] = $winr;
                                $winn[$i]['amount_per'] = number_format($amts, 2, '.', ''); // amount per user
                                $winn[$i]['total'] = $amts * $winr;
                                $amts_total = $amts * $winr;
                            }
                        }
                        $distributed[] = $amts_total;
                    } else {

                        break;
                    }
                    $i++;
                }
                $rem_amount = $new_win_amt - array_sum($distributed);
                dump('Distributed amount-----------------' . array_sum($distributed));
                dump('Remaining------------------' . $rem_amount);
                dump($winn);
                $single_person_amt = number_format(($rem_amount / $newwinners), 2, '.', '');
                dump('Profit to every winner-------------------' . $single_person_amt);
                $total_win = 0;
                $d = array_map(function ($v) use ($single_person_amt, $new_win_amt, $total_win) {
                    // $amt_per = ($v['winners'])?
                    // $v['amount_per'] = number_format(floor($v['amount_per']) + $single_person_amt,2,'.','');
                    $v['amount_per'] = number_format($v['amount_per'] + $single_person_amt, 2, '.', '');
                    $v['total'] = number_format($v['amount_per'] * $v['winners'], 2, '.', '');
                    $total_win += $v['total'];
                    $v['new_percentage'] = number_format(($v['amount_per'] / $new_win_amt) * 100, 2, '.', '');
                    return $v;
                }, $winn);
                dump($d);
                // dd($total_win);
                foreach ($d as $k => $val) {
                    if ($val['max_position'] < $newwinners) {
                        // DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $val['total'], 'price_percent' => $val['new_percentage']]);
                    } else {
                        $w = $newwinners - $val['min_position'];
                        if ($joined == 1) {
                            // DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $new_win_amt, 'price_percent' => 100, 'max_position' => $newwinners, 'winners' => $w]);
                            break;
                        } else {
                            // DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $val['total'], 'price_percent' => $val['new_percentage'], 'max_position' => $newwinners, 'winners' => $w]);
                        }
                        //         DB::table('matchpricecards')->where('id',$val['id'])->update(['total'=>$val['total'],'price_percent'=>$val['new_percentage'],'max_position'=>$newwinners,'winners'=>$w]);
                    }

                }
                $ids = explode(',', $ids);
                $wins = array_sum(array_column($d, 'total'));
                dump('new winning_amount           ' . $wins);
                // DB::table('matchpricecards')->where('challenge_id', $challengeid)->whereNotIn('id', $ids)->delete();
                // DB::table('matchchallenges')->where('id', $challengeid)->update(['win_amount' => $wins, 'price_status' => 1]);
                // echo '<pre>';print_r($d);

            }
        }
    }
    

    public function changeprice($challengeid)
    {
        date_default_timezone_set('Asia/Kolkata');
        // $findchallenge = DB::table('matchchallenges')->where('id',$challengeid)->where('price_status',0)->first();
        $findchallenge = DB::table('matchchallenges')->where('id', $challengeid)->where('entryfee', '!=', 0)->where('pricecard_type', 'Percentage')->where('price_status', 0)->first();
        //   dump($findchallenge);
        if (!empty($findchallenge)) {
            LOG::info('contest Compression for Contest id------' . $findchallenge->id);
            // LOG::info(date('Y-m-d H:i:s'));
            // $bonus_used = DB::table('leaugestransactions')->where('challengeid', $challengeid)->sum('bonus');
            $bonus_used = 0;
            $contest_winners = DB::table('matchpricecards')->where('challenge_id', $challengeid)->sum('winners');
            $pricecards = DB::table('matchpricecards')->where('challenge_id', $challengeid)->get();
            // dump(!empty($pricecards->toArray()) && $findchallenge->joinedusers>0);
            $totaljoineduser = DB::connection('mysql')->table('joinedleauges')->where('challengeid',$challengeid)->count();
            if (!empty($pricecards->toArray()) && $totaljoineduser > 0) {
                $maxusers = $findchallenge->maximum_user;
                dump('maxusers------' . $maxusers);
                dump('contestwinners-----------'.$contest_winners);
                $per_winners = ($contest_winners/$maxusers)*100;
                dump('Percentage of winners--------'.$per_winners);
                $joined = $totaljoineduser;
                dump('joined------' . $joined);

                $win_amt = $findchallenge->win_amount;
                dump('win_amt------' . $win_amt);
                $entryfee = $findchallenge->entryfee;
                dump('entryfee------' . $entryfee);
                $amount_before_live = $entryfee * $maxusers;
                dump('amount_before_live------' . $amount_before_live);
                $distribution_percentage = number_format(($win_amt / $amount_before_live) * 100, 2, '.', '');
                dump('distribution_percentage------' . $distribution_percentage);
                $received_amt = $entryfee * $joined;
                dump('received_amt------' . $received_amt);
                dump('bonus used by users--------' . $bonus_used);
                $received_amt = $received_amt - $bonus_used;
                dump('received amount after bonus deduction ---------' . $received_amt);
                // if contest is full then contest winning amount will not compress
                if ($maxusers == $joined) {
                    $new_win_amt = $win_amt;
                } else {
                    $new_win_amt = number_format(floor((($received_amt * $distribution_percentage) / 100)), 2, '.', '');
                }
                // $new_win_amt = number_format((($received_amt*$distribution_percentage)/100),2,'.','');
                dump('new_win_amt------' . $new_win_amt);
                if ($joined > $contest_winners) {
                    $newwinners = $contest_winners;
                } else {
                    if ($joined == 1) {
                        $newwinners = 1;
                        $new_win_amt = $entryfee;
                    } else {

                        $newwinners = round(($joined * floor($per_winners)) / 100);
                        if ($newwinners > 0 && $newwinners < 1) {
                            $newwinners = 1;
                        }
                    }
                }

                dump('newwinners------' . $newwinners);
                $distributed_amt = 0;
                $distributed = [];
                $ranked = $pricecards->pluck('price_percent', 'max_position');
                $ranked_new = $ranked->toArray();
                dump($ranked->toArray());
                $ranks = array_keys($ranked->toArray());
                $x = $newwinners;
                // dd($x);
                $z = array_reduce($ranks, function ($max, $number) use ($x) {
                    // dump('max----' . $number);
                    // Ignore if greater
                    if ($number >= $x) {
                        return $max;
                    }

                    // First one less than x
                    if ($max === null) {
                        return $number;
                    }

                    // Replace if greater
                    return $max < $number ? $number : $max;

                }, null);
                dump($z);
                // dump($z);
                // dd($ranks);
                // $key_index = $ranked_new[$ranks[array_search($z, $ranks)+1]];
                // $key_index = (isset($ranks[array_search($z, $ranks)+1]))?$ranked_new[$ranks[array_search($z, $ranks)+1]]:$ranked_new[$ranks[array_search($z, $ranks)]];
                dump(empty($z));
                if (empty($z)) {
                    $key_index = (isset($ranks[array_search($z, $ranks) + 0])) ? $ranked_new[$ranks[array_search($z, $ranks) + 0]] : $ranked_new[$ranks[array_search($z, $ranks)]];
                    // dump('Ranked New --------'.isset($ranks[array_search($z, $ranks)]));
                } else {
                    $key_index = (isset($ranks[array_search($z, $ranks) + 1])) ? $ranked_new[$ranks[array_search($z, $ranks) + 1]] : $ranked_new[$ranks[array_search($z, $ranks)]];
                    dump($key_index);
                }
                $key = array_search($key_index, $ranked_new);
                dump('key----------'.$key);
                $data = array_filter($ranked_new, function ($v) use ($key) {return $v <= $key;}, ARRAY_FILTER_USE_KEY);
                dump($data);
                $i = 0;
                $rem_amount = 0;
                $winn = [];
                $ids = '';
                foreach ($pricecards as $key => $value) {
                    if ($i < count($data)) {
                        $amts = ($new_win_amt * $value->price_percent) / 100;
                        $amts_total = 0;
                        dump('fdgffghjhgjhg----------' . $i);
                        // dd($value->max_position.'--------'.$newwinners);

                        if ($value->max_position <= $newwinners) {
                            $amts_total = $amts * $value->winners;
                            $ids = ($ids != '') ? $ids . ',' . $value->id : $value->id;
                            $winn[$i]['id'] = $value->id;
                            $winn[$i]['min_position'] = $value->min_position;
                            $winn[$i]['max_position'] = $value->max_position;
                            $winn[$i]['winners'] = $value->winners;
                            $winn[$i]['amount_per'] = number_format($amts, 2, '.', '');
                            $winn[$i]['total'] = $amts_total;
                        } else {

                            $winuserforwinnnn = ((isset($pricecards[$i - 1])) ? $pricecards[$i - 1]->max_position : $pricecards[$i]->max_position);
                            $winr = ($newwinners > $winuserforwinnnn) ? $newwinners - $winuserforwinnnn : $newwinners;
                            // $ids = ($ids!='')?$ids.','.$value->id:$value->id;
                            // $winn[$i]['id'] = $value->id;
                            // $winn[$i]['min_position'] = $value->min_position;
                            // $winn[$i]['max_position'] = $value->max_position;
                            // $winn[$i]['winners'] = $winr;
                            // $winn[$i]['amount_per'] = number_format($amts,2,'.','');
                            // $winn[$i]['total'] = $amts*$winr;
                            // $amts_total = $amts*$winr;
                            if ($winr > 0) {
                                $ids = ($ids != '') ? $ids . ',' . $value->id : $value->id;
                                $winn[$i]['id'] = $value->id;
                                $winn[$i]['min_position'] = $value->min_position;
                                $winn[$i]['max_position'] = $value->max_position;
                                $winn[$i]['winners'] = $winr;
                                $winn[$i]['amount_per'] = number_format($amts, 2, '.', ''); // amount per user
                                $winn[$i]['total'] = $amts * $winr;
                                $amts_total = $amts * $winr;
                            }
                        }
                        $distributed[] = $amts_total;
                    } else {

                        break;
                    }
                    $i++;
                }
                $rem_amount = $new_win_amt - array_sum($distributed);
                dump('Distributed amount-----------------' . array_sum($distributed));
                dump('Remaining------------------' . $rem_amount);
                dump($winn);
                $single_person_amt = number_format(($rem_amount / $newwinners), 2, '.', '');
                dump('Profit to every winner-------------------' . $single_person_amt);
                $total_win = 0;
                $d = array_map(function ($v) use ($single_person_amt, $new_win_amt, $total_win) {
                    // $amt_per = ($v['winners'])?
                    // $v['amount_per'] = number_format(floor($v['amount_per']) + $single_person_amt,2,'.','');
                    $v['amount_per'] = number_format($v['amount_per'] + $single_person_amt, 2, '.', '');
                    $v['total'] = number_format($v['amount_per'] * $v['winners'], 2, '.', '');
                    $total_win += $v['total'];
                    $v['new_percentage'] = number_format(($v['amount_per'] / $new_win_amt) * 100, 2, '.', '');
                    return $v;
                }, $winn);
                dump($d);
                // dd($total_win);
                foreach ($d as $k => $val) {
                    if ($val['max_position'] < $newwinners) {
                        DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $val['total'], 'price_percent' => $val['new_percentage']]);
                    } else {
                        $w = $newwinners - $val['min_position'];
                        if ($joined == 1) {
                            DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $new_win_amt, 'price_percent' => 100, 'max_position' => $newwinners, 'winners' => $w]);
                            break;
                        } else {
                            DB::table('matchpricecards')->where('id', $val['id'])->update(['total' => $val['total'], 'price_percent' => $val['new_percentage'], 'max_position' => $newwinners, 'winners' => $w]);
                        }
                                DB::table('matchpricecards')->where('id',$val['id'])->update(['total'=>$val['total'],'price_percent'=>$val['new_percentage'],'max_position'=>$newwinners,'winners'=>$w]);
                    }

                }
                $ids = explode(',', $ids);
                $wins = array_sum(array_column($d, 'total'));
                dump('new winning_amount           ' . $wins);
                DB::table('matchpricecards')->where('challenge_id', $challengeid)->whereNotIn('id', $ids)->delete();
                DB::table('matchchallenges')->where('id', $challengeid)->update(['win_amount' => $wins, 'price_status' => 1]);
                // echo '<pre>';print_r($d);

            }
        }
    }
    public function getWon(Request $request){
        Helpers::setHeader(200);
        Helpers::timezone();
        $geturl = Helpers::geturl();
        $input = $request->all();

        $startdate = $input['start_date'];
        $enddate = $input['end_date'];
        $limit = $input['limit'];

        $query = DB::table('transactions');
        if($input['start_date']){
                
                $start_date = $input['start_date'];
                if($start_date!=""){
                    $query->whereDate('transactions.created_at', '>=', date('Y-m-d', strtotime($start_date)));
                }
                
                if($input['end_date']){
                    
                    $end_date = $input['end_date'];
                    if($end_date == $start_date){
                        $query->whereDate('transactions.created_at', '>=', date('Y-m-d', strtotime($end_date)));
                    }
                }
            }

            if($input['end_date']){
                
                $end_date = $input['end_date'];
                if($end_date!=""){
                    $query->whereDate('transactions.created_at', '<=', date('Y-m-d', strtotime($end_date)));
                }
            }
        $totaljoinedusers = $query->where('transactions.type','Contest Joining Fee')->join('registerusers','registerusers.id','transactions.userid')->select(DB::raw('SUM(transactions.cons_amount) as consumeamount , SUM(transactions.cons_win) as consumewinning'),'transactions.userid','registerusers.email as email','registerusers.mobile as mobile','registerusers.username as username')->groupBy('transactions.userid')->get();
        $winarr = array();
        foreach($totaljoinedusers as $value){
            $winarr[$value->userid]['amount']=$value->consumeamount + $value->consumewinning;
            $winarr[$value->userid]['userid']=$value->userid;
            $winarr[$value->userid]['email']=$value->email;
            $winarr[$value->userid]['mobile']=$value->mobile;
            $winarr[$value->userid]['username']=$value->username;
        }
        $finalarray = array();
        Helpers::sortBySubArrayValue($winarr, 'amount', 'desc');
        $finalarray = array_chunk($winarr,$limit);
        $fileName = 'wonreport.csv';
        $headers = array(
                'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Disposition' => 'attachment; filename=download.csv',
                'Expires' => '0',
                'Pragma' => 'public',
            );
        if (!File::exists(public_path()."/files")) {
            File::makeDirectory(public_path() . "/files");
        }
        $filename =  public_path("files/download.csv");
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            'Userid', 'Amount', 'Email', 'Mobile', 'Username',
        ]);
    
        foreach ($finalarray[0] as $each_user) {
            fputcsv($handle, [
                $each_user['userid'],
                $each_user['amount'],
                $each_user['email'],
                $each_user['mobile'],
                $each_user['username'],
            ]);

        }
        fclose($handle);
        return Response::download($filename, "download.csv", $headers);
    }

    public function changeduopricecard(Request $request){
        date_default_timezone_set('Asia/Kolkata');
		$current = date('Y-m-d H:i:s');
		$match_time = date('Y-m-d H:i:s', strtotime( '-15 minutes', strtotime($current)));
        
		// $match_time = date('Y-m-d H:i:s', strtotime($current));
		$findmatches = DB::connection('mysql')->table('listmatches')->where('start_date', '<=' , $match_time)->where('status','started')->select('matchkey')->get();
		if(!empty($findmatches)){
			foreach ($findmatches as $value){
				$match_challenges = DB::table('matchchallenges')->join('joinedleauges','matchchallenges.id','joinedleauges.challengeid')->where('matchchallenges.matchkey', $value->matchkey)->where('joinedleauges.fantasy_type', 'Duo')->where('matchchallenges.entryfee', '!=', 0)->where('matchchallenges.price_status', 0)->get();
                
                $match_challenges = $match_challenges->toArray();
			    if(!empty($match_challenges)){
				    foreach ($match_challenges as  $value1) {
                        $totaljoineduser = DB::connection('mysql')->table('joinedleauges')->where('challengeid',$value1->challengeid)->count();
                        if($value1->maximum_user >= $totaljoineduser){
                            $entryfees = $value1->entryfee;
                            $originalwinamount = $value1->win_amount;
                            $pwinpercent =  $originalwinamount/($value1->maximum_user*$entryfees)*100;
                            $newwinningamount = ($entryfees*$totaljoineduser*$pwinpercent)/100;
                            DB::table('matchchallenges')->where('matchchallenges.matchkey', $value->matchkey)->where('matchchallenges.fantasy_type', 'Duo')->where('matchchallenges.entryfee', '!=', 0)->where('matchchallenges.id', $value1->challengeid)->update(['price_status'=>1,'win_amount'=>$newwinningamount]);
                            DB::table('matchpricecards')->where('matchpricecards.matchkey', $value->matchkey)->where('matchpricecards.challenge_id', $value1->challengeid)->update(['total'=>$newwinningamount,'price'=>$newwinningamount]);
                        }
                    }
                }
            }
        }
    }
}

