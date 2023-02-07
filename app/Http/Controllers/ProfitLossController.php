<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ProfitLossController extends Controller
{

    public function view_profit_loss(Request $request)
    {

        $f_type = request()->get('fantasy_type');
        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $data = $request->all();

        $query = DB::table('profitloss')->join('listmatches', 'listmatches.matchkey', 'profitloss.matchkey')
            ->select('listmatches.fantasy_type as fantasy_type', 'profitloss.*')
        // ->where('listmatches.fantasy_type',$f_type)
            ->orderBy('profitloss.start_date', 'desc');

        // search for the series name //
        if (isset($_GET['name'])) {
            $name = $data['name'];
            if ($name != "") {
                $query->where('profitloss.name', 'LIKE', '%' . $name . '%');
            }
        }

        if (request()->has('startdate')) {

            $start_date = request('startdate');
            if ($start_date != "") {
                $query->whereDate('profitloss.start_date', '>=', date('Y-m-d', strtotime($start_date)));
            }

            if (request()->has('enddate')) {

                $end_date = request('enddate');
                if ($end_date == $start_date) {
                    $query->whereDate('profitloss.start_date', '>=', date('Y-m-d', strtotime($end_date)));
                }
            }
        }

        if (request()->has('enddate')) {

            $end_date = request('enddate');
            if ($end_date != "") {
                $query->whereDate('profitloss.start_date', '<=', date('Y-m-d', strtotime($end_date)));
            }
        }

        if (request()->has("option")) {
            $option = request("option");
            if ($option != "") {

                if ($option == 1) {

                    $date = Carbon::today()->toDateString();

                    $query->where('profitloss.start_date', $date);

                } else if ($option == 2) {

                    $today = Carbon::today()->toDateString();

                    $date = Carbon::today()->subDays(6)->toDateString();

                    $query->whereBetween('profitloss.start_date', [$date, $today]);

                } else if ($option == 3) {

                    $today = Carbon::today()->toDateString();

                    $date = Carbon::today()->subDays(29)->toDateString();

                    $query->whereBetween('profitloss.start_date', [$date, $today]);

                }

            }
        }

        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;

        $titles = $query->get();

        $profit = $loss = 0;

        if (!empty($titles)) {
            foreach ($titles as $title) {
                $amount_profit_or_loss = $title->amount_profit_or_loss;
                $pandl = $title->profit_or_loss;
                if ($pandl == 'Loss') {
                    $loss += $amount_profit_or_loss;
                } else {
                    $profit += $amount_profit_or_loss;
                }
            }
        }
        return view('profit_loss.view_profit_loss', compact('profit', 'loss'));

    }

    public function view_daily_report(Request $request)
    {
        $amt = DB::table('daily_report')->select(DB::raw('SUM(net_amount)as net_amount'), DB::raw('SUM(total_received)as total_received'), DB::raw('SUM(total_withdraw)as total_withdraw'), DB::raw('SUM(cashfreeRper)as cashfreeRper'), DB::raw('SUM(cashfreeWper)as cashfreeWper'), DB::raw('SUM(profit)as profit'), DB::raw('SUM(loss)as loss'))->first();
        // dd($amt);
        return view('profit_loss.daily_report', compact('amt'));
    }
    public function view_daily_report_dt(Request $request)
    {
        // dd('hello');
        $columns = array(
            0 => 'id',
            1 => 'report_date',
        );
        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $f_type = request()->get('fantasy_type');
        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $query = DB::table('daily_report');
        if (request()->has('startdate')) {

            $start_date = request('startdate');
            if ($start_date != "") {
                $query->whereDate('daily_report.report_date', '>=', date('Y-m-d', strtotime($start_date)));
            }

            if (request()->has('enddate')) {

                $end_date = request('enddate');
                if ($end_date == $start_date) {
                    $query->whereDate('daily_report.report_date', '>=', date('Y-m-d', strtotime($end_date)));
                }
            }
        }
        if (request()->has('enddate')) {

            $end_date = request('enddate');
            if ($end_date != "") {
                $query->whereDate('daily_report.report_date', '<=', date('Y-m-d', strtotime($end_date)));
            }
        }
        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        $titles = $query->offset($start)->limit($limit)->orderBy('report_date', 'DESC')->get();
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {

                $nestedData['id'] = $count;
                $nestedData['report_date'] = $title->report_date;

                $nestedData['net_amount'] = $title->net_amount;

                $nestedData['total_received'] = $title->total_received;

                $nestedData['total_withdraw'] = $title->total_withdraw;

                $nestedData['cashfreeRper'] = $title->cashfreeRper;

                $nestedData['cashfreeWper'] = $title->cashfreeWper;

                $nestedData['profit'] = $title->profit;

                $nestedData['loss'] = $title->loss;

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

    public function downloaddaywisereport(Request $request)
    {
        $output1 = "";
        $output1 .= '"S.No.",';
        $output1 .= '"Date",';
        $output1 .= '"Net Amount",';
        $output1 .= "\n";

        $query = DB::table('daily_report');

        if (request()->has('startdate')) {
            $start_date = request()->get('startdate');
            if ($start_date != "") {
                $query = $query->whereDate('report_date', '>=', date('Y-m-d', strtotime($start_date)));
            }
        }

        // Search for End Date
        if (request()->has('enddate')) {
            $end_date = request()->get('enddate');
            if ($end_date != "") {
                $query = $query->whereDate('report_date', '<=', date('Y-m-d', strtotime($end_date)));
            }
        }
        $getlist = $query->orderBy('report_date', 'ASC')->get();
        if (!empty($getlist->toArray())) {
            $count = 1;
            foreach ($getlist as $post) {

                $output1 .= '"' . $count . '",';
                $output1 .= '"' . $post->report_date . '",';
                $output1 .= '"' . $post->net_amount . '",';
                $output1 .= "\n";
                $count++;
            }
        }
        $filename = "Day wise profit-loss Report.csv";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output1;
        exit;
    }

    // public function updatereport(){
    //     $datee = date('Y-m-d');
    //     $sponserdate = date('Y-m-d', strtotime($datee . ' -1 day'));
    //     $query = DB::table('listmatches')
    //                 ->where('final_status', 'winnerdeclared')
    //                 // ->whereDate('start_date','>=',$sponserdate)
    //                 ->get();

    //     foreach($query as $value){

    //             $data = array();
    //             $matchkey =  $data['matchkey'] = $value->matchkey;
    //             $data['name'] = $value->name;
    //             $data['start_date'] = $value->start_date;

    //             $challenges = DB::table('matchchallenges')->where('matchkey', $matchkey)->select('id', 'entryfee', 'is_bonus', 'bonus_percentage')->get();
    //             $admin_amt_received = 0;

    //             $bonus = 0;
    //             if( !empty($challenges->toArray()) ) {
    //                    foreach($challenges as $challenge) {
    //                         $challenge_joined_users = DB::table('joinedleauges')->where('challengeid', $challenge->id)->count();
    //                          # Admin Amt Received
    //                         if($challenge->is_bonus) {
    //                             $bonus_allowed = ($challenge->entryfee * $challenge->bonus_percentage) / 100;
    //                             $remaining_entryfee = $challenge->entryfee - $bonus_allowed;
    //                             $admin_amt_received += $remaining_entryfee * $challenge_joined_users;
    //                          } else {
    //                             $admin_amt_received += $challenge->entryfee * $challenge_joined_users;
    //                         }

    //                         $alljoinedchallenge = DB::table('joinedleauges')->where('challengeid',$challenge->id)->select('id')->get();
    //                         if(!empty($alljoinedchallenge)){
    //                             foreach($alljoinedchallenge as $jleauge) {

    //                                 $refund_jleauge = DB::table('refunds')->where('matchkey', $value->matchkey)->where('joinid', $jleauge->id)->select('amount')->first();

    //                                 if( !empty($refund_jleauge) ) {
    //                                    $leaugestransactions = DB::table('leaugestransactions')->where('joinid', $jleauge->id)->where('bonus', '!=', 0)->select('bonus')->first();

    //                                    if( !empty($leaugestransactions) ) {
    //                                        $bonus += $leaugestransactions->bonus;
    //                                    }

    //                                 }
    //                             }

    //                         }
    //                     }
    //                 }

    //             $data['invested_amt'] = number_format($admin_amt_received, 2, '.', '');

    //             $winning_amt = DB::table('finalresults')->where('matchkey', $matchkey)->sum('amount');

    //             $data['win_amt'] = number_format($winning_amt, 2, '.', '');

    //             $refund_amt = DB::table('refunds')->where('matchkey', $value->matchkey)->sum('amount');

    //             $refund_amt = $refund_amt - $bonus;

    //             $bonus = 0;

    //             $data['refund_amt'] =  number_format($refund_amt, 2, '.', '');

    //             $youtuber_bonus = DB::table('youtuber_bonus')->where('matchkey', $value->matchkey)->sum('amount');

    //             $data['youtuber_bonus'] =  number_format($youtuber_bonus, 2, '.', '');
    //             $distributed = ($winning_amt + $refund_amt + $youtuber_bonus);
    //             $p_and_l = $admin_amt_received - $distributed;
    //             $amount_profit_or_loss = $admin_amt_received > $distributed ? $admin_amt_received - $distributed : $distributed - $admin_amt_received;

    //             if($p_and_l < 0) {
    //                 $data['profit_or_loss'] = 'Loss';
    //             }
    //             elseif($p_and_l == 0) {
    //                 $data['profit_or_loss'] = 'None';
    //             }else {
    //                 $data['profit_or_loss'] = 'Profit';
    //             }
    //             $data['amount_profit_or_loss'] = number_format($amount_profit_or_loss, 2, '.', '');
    //              $val = DB::table('profitloss')->where('matchkey',$value->matchkey)->first();
    //         if(empty($val)){
    //             DB::connection('mysql2')->table('profitloss')->insert($data);
    //         }else{
    //             DB::connection('mysql2')->table('profitloss')->where('matchkey',$matchkey)->update($data);
    //         }
    //             $ff['report_status'] = 1;
    //             DB::connection('mysql2')->table('listmatches')->where('matchkey',$matchkey)->update($ff);

    //     }
    // }

    public function updatereport()
    {

        $datee = date('Y-m-d');
        $sponserdate = date('Y-m-d', strtotime($datee . ' -1 day'));
        $query = DB::table('listmatches')
            ->where('final_status', 'winnerdeclared')
            ->whereDate('start_date', '>=', $sponserdate)
            ->get();
        // $query = DB::connection('mysql2')->table('listmatches')
        //         ->where('matchkey', '39609')
        //         ->get();
        // dump($query);
        foreach ($query as $value) {

            $data = array();
            $matchkey = $data['matchkey'] = $value->matchkey;
            $data['name'] = $value->name;
            $data['start_date'] = $value->start_date;

            $challenges = DB::table('matchchallenges')
                ->where('matchkey', $matchkey)
                ->where('status', '!=', 'canceled')
                ->select('id', 'entryfee', 'is_bonus', 'bonus_percentage')
                ->get();

            $admin_amt_received = 0;

            $bonus = 0;
            if (!empty($challenges->toArray())) {
                foreach ($challenges as $challenge) {

                    # recorrect
                    $real_money = DB::table('leaugestransactions')
                        ->where('challengeid', $challenge->id)
                        ->select(
                            DB::raw('sum(balance) as balance'),
                            DB::raw('sum(winning) as winning'),
                            DB::raw('sum(bonus) as bonus')
                        )
                        ->first();

                    $bonus = $bonus + $real_money->bonus;

                    # Admin Amt Received
                    $actual_received = ($real_money->winning + $real_money->balance);

                    $admin_amt_received += $actual_received;

                }
            }

            $data['invested_amt'] = number_format($admin_amt_received, 2, '.', '');

            $winning_amt = DB::table('finalresults')->where('matchkey', $matchkey)->sum('amount');
            // $tds_amount = DB::table('tdsdetails')->where('matchkey', $matchkey)->sum('tds_amount');
            $winn = $winning_amt; // - $tds_amount;
            $data['win_amt'] = number_format($winn, 2, '.', '');
            // $data['tds_amt'] = number_format($tds_amount, 2, '.', '');

            $refund_amt = DB::table('refunds')->where('matchkey', $value->matchkey)->sum('amount');

            $refund_amt = $refund_amt;

            $bonus = 0;

            $data['refund_amt'] = number_format($refund_amt - $bonus, 2, '.', '');

            $youtuber_bonus = DB::table('youtuber_bonus')->where('matchkey', $value->matchkey)->sum('amount');

            $data['youtuber_bonus'] = number_format($youtuber_bonus, 2, '.', '');
            $distributed = ($winning_amt + $youtuber_bonus);
            $p_and_l = $admin_amt_received - $distributed;
            $amount_profit_or_loss = $admin_amt_received > $distributed ? $admin_amt_received - $distributed : $distributed - $admin_amt_received;

            if ($p_and_l < 0) {
                $data['profit_or_loss'] = 'Loss';
            } elseif ($p_and_l == 0) {
                $data['profit_or_loss'] = 'None';
            } else {
                $data['profit_or_loss'] = 'Profit';
            }
            $data['amount_profit_or_loss'] = number_format($amount_profit_or_loss, 2, '.', '');
            $val = DB::table('profitloss')->where('matchkey', $value->matchkey)->first();
            if (empty($val)) {
                dump($val);
                DB::connection('mysql2')->table('profitloss')->insert($data);
            } else {
                DB::connection('mysql2')->table('profitloss')->where('matchkey', $matchkey)->update($data);
            }
            $ff['report_status'] = 1;
            DB::connection('mysql2')->table('listmatches')->where('matchkey', $matchkey)->update($ff);

        }
        return redirect()->back()->with('success','Profit Loss Succesfully Created');
    }

    public function view_profit_loss_dt(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
        );

        $data = $request->all();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $f_type = request()->get('fantasy_type');
        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $query = DB::table('profitloss')->join('listmatches', 'listmatches.matchkey', 'profitloss.matchkey')
            ->select('listmatches.fantasy_type as fantasy_type', 'profitloss.*')
        // ->where('listmatches.fantasy_type',$f_type)
            ->orderBy('profitloss.start_date', 'desc');

        // search for the series name //
        if (isset($_GET['name'])) {
            $name = $data['name'];
            if ($name != "") {
                $query->where('profitloss.name', 'LIKE', '%' . $name . '%');
            }
        }

        if (request()->has('startdate')) {

            $start_date = request('startdate');
            if ($start_date != "") {
                $query->whereDate('profitloss.start_date', '>=', date('Y-m-d', strtotime($start_date)));
            }

            if (request()->has('enddate')) {

                $end_date = request('enddate');
                if ($end_date == $start_date) {
                    $query->whereDate('profitloss.start_date', '>=', date('Y-m-d', strtotime($end_date)));
                }
            }
        }

        if (request()->has('enddate')) {

            $end_date = request('enddate');
            if ($end_date != "") {
                $query->whereDate('profitloss.start_date', '<=', date('Y-m-d', strtotime($end_date)));
            }
        }

        if (request()->has("option")) {
            $option = request("option");
            if ($option != "") {

                # Today
                if ($option == 1) {

                    $date = Carbon::today()->toDateString();

                    $query->where('profitloss.start_date', $date);

                }
                # Week
                else if ($option == 2) {

                    $today = Carbon::today()->toDateString();

                    $date = Carbon::today()->subDays(6)->toDateString();

                    $query->whereBetween('profitloss.start_date', [$date, $today]);

                }
                # Month
                else if ($option == 3) {

                    $today = Carbon::today()->toDateString();

                    $date = Carbon::today()->subDays(29)->toDateString();

                    $query->whereBetween('profitloss.start_date', [$date, $today]);

                }

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
            $count = 1;
            foreach ($titles as $title) {

                $nestedData['id'] = $count;
                $nestedData['name'] = $title->name;

                $nestedData['start_date'] = date('Y-m-d', strtotime($title->start_date));

                $nestedData['invested_amt'] = $title->invested_amt;

                $nestedData['win_amt'] = $title->win_amt;

                $nestedData['refund_amt'] = $title->refund_amt;

                // $nestedData['youtuber_bonus'] = $title->youtuber_bonus. '<br/> <a href="'.asset('my-admin/youtuber_bonus').'?matchkey='.$title->matchkey.'" class="btn btn-primary">View Report</a>';

                $nestedData['youtuber_bonus'] = $title->youtuber_bonus;

                $nestedData['profit_or_loss'] = $title->profit_or_loss;

                $nestedData['amount_profit_or_loss'] = $title->amount_profit_or_loss;

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

    public function youtuber_bonus(Request $request)
    {

        $matchkey = request()->get('matchkey');

        return view('profit_loss.youtuber_bonus', compact('matchkey'));

    }

    public function youtuber_bonus_dt(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'username',
        );

        $data = $request->all();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $f_type = request()->get('fantasy_type');
        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $query = DB::table('youtuber_bonus')
            ->join('registerusers', 'registerusers.id', 'youtuber_bonus.userid')
            ->where('youtuber_bonus.matchkey', request('matchkey'))
            ->groupBy('youtuber_bonus.userid')
            ->select('youtuber_bonus.userid', DB::raw("sum(youtuber_bonus.amount) as sum_amt"), 'registerusers.username');

        // search for the series name //
        if (isset($_GET['amount'])) {
            $amount = $_GET['amount'];
            if ($amount != "") {
                $query->where('youtuber_bonus.amount', $amount);
            }
        }

        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;

        $titles = $query->get();

        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {

                $nestedData['id'] = $count;
                $nestedData['name'] = $title->username;

                $nestedData['sum_amt'] = number_format($title->sum_amt, 2, '.', '');
                $nestedData['action'] = '<a href="' . asset('my-admin/youtuber_bonus_detail') . '?userid=' . $title->userid . '&matchkey=' . request('matchkey') . '" class="btn btn-primary">View Report</a>';

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

    public function youtuber_bonus_detail(Request $request)
    {

        $matchkey = request()->get('matchkey');
        $userid = request()->get('userid');

        return view('profit_loss.youtuber_bonus_detail', compact('matchkey', 'userid'));

    }

    public function youtuber_bonus_detail_dt(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'username',
        );

        $data = $request->all();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $f_type = request()->get('fantasy_type');
        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $query = DB::table('youtuber_bonus')
            ->join('matchchallenges', 'matchchallenges.id', 'youtuber_bonus.challengeid')
            ->join('contest_category', 'contest_category.id', 'matchchallenges.contest_cat')
            ->where('youtuber_bonus.matchkey', request('matchkey'))
            ->where('youtuber_bonus.userid', request('userid'))
            ->groupBy('youtuber_bonus.challengeid')
            ->select(
                'matchchallenges.entryfee',
                'contest_category.name',
                'matchchallenges.win_amount',
                DB::raw("count(youtuber_bonus.fromid) as joined_youtuber_users"),
                DB::raw("sum(youtuber_bonus.amount) as profit")
            );

        // search for the series name //
        if (isset($_GET['entryfee'])) {
            $entryfee = $_GET['entryfee'];
            if ($entryfee != "") {
                $query->where('matchchallenges.entryfee', $entryfee);
            }
        }

        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;

        $titles = $query->get();

        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {

                $nestedData['id'] = $count;
                $nestedData['entryfee'] = $title->entryfee;

                $nestedData['name'] = $title->name;
                $nestedData['win_amount'] = number_format($title->win_amount, 2, '.', '');
                $nestedData['joined_youtuber_users'] = $title->joined_youtuber_users;
                $nestedData['profit'] = number_format($title->profit, 2, '.', '');

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
