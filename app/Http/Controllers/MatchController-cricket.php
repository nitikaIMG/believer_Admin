<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CricketapiController;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Redirect;
use Session;

class MatchController extends Controller
{
    public function __construct()
    {

    }
    // to see all the upcoming matches//
    public function upcoming_matches()
    {
        date_default_timezone_set('Asia/Kolkata');
        $locktime = Carbon::now();
        $page = '';
        $currentdate = date('Y-m-d H:i:s');
        if (!empty($_GET['page'])) {
            $page = $_GET['page'];
        }
        $findalllistmatches = DB::table('listmatches')
            ->leftjoin('teams as team1', 'team1.id', '=', 'listmatches.team1')
            ->leftjoin('teams as team2', 'team2.id', '=', 'listmatches.team2')
            ->leftjoin('series', 'series.id', '=', 'listmatches.series')
            ->select('listmatches.*', 'team1.logo as team1logo', 'team2.logo as team2logo', 'team1.team as team1team',
                'team2.team as team2team', 'series.name as seriesname')
            ->where('listmatches.status', '!=', 'completed')
            ->whereNotIn('listmatches.final_status', ['IsCanceled', 'IsAbandoned'])
            ->where('listmatches.start_date', '>=', $currentdate);

        if (request()->has('fantasy_type')) {
            $fantasy_type = request('fantasy_type');
            if ($fantasy_type != "") {
                $findalllistmatches = $findalllistmatches->where('listmatches.fantasy_type', $fantasy_type);
            }
        } else {
            $findalllistmatches = $findalllistmatches->where('listmatches.fantasy_type', 'Cricket');
        }

        $findalllistmatches = $findalllistmatches->orderBy('listmatches.tbl_order','DESC')->orderBY('listmatches.start_date', 'ASC')->paginate(10);
        return view('matches.upcoming_matches', compact('findalllistmatches', 'page'));
    }
    // to import the data from the api //
    // public function importdatafromapi()
    // {
    //     date_default_timezone_set('Asia/Kolkata');
    //     $getdayslist = CricketapiController::getlistmatches();
    //     if (!empty($getdayslist)) {
    //         foreach ($getdayslist as $daylist) {
    //             if (isset($daylist['matches'])) {
    //                 $getlist = $daylist['matches'];

    //                 if (!empty($getlist)) {
    //                     foreach ($getlist as $getli) {
    //                         $cuurectdate = date('Y-m-d H:i:s', strtotime($getli['start_date']));

    //                         $matchadata = array();
    //                         $findmatchexist = DB::table('listmatches')->where('matchkey', $getli['matchkey'])->orWhere('real_matchkey', $getli['matchkey'])->first();
    //                         if (empty($findmatchexist)) {
    //                             $findsquaddetials = CricketapiController::getmatchdata($getli['matchkey']);
    //                             if (!empty($findsquaddetials)) {
    //                                 $cuurectdate = (isset($findsquaddetials['data']['card']['start_date']['timestamp'])) ? date('Y-m-d H:i:s', $findsquaddetials['data']['card']['start_date']['timestamp']) : $cuurectdate;
    //                                 $matchadata['squadstatus'] = 'no';
    //                                 $squadteam1players = array();
    //                                 $squadteam2players = array();
    //                                 if (isset($findsquaddetials['data']['card']['teams']['a']['match']['players'])) {
    //                                     $squadteam1players = $findsquaddetials['data']['card']['teams']['a']['match']['players'];
    //                                 }

    //                                 if (isset($findsquaddetials['data']['card']['teams']['b']['match']['players'])) {
    //                                     $squadteam2players = $findsquaddetials['data']['card']['teams']['b']['match']['players'];
    //                                 }

    //                                 if ((!empty($squadteam1players)) && (!empty($squadteam2players))) {
    //                                     $matchadata['squadstatus'] = 'yes';
    //                                 }
    //                             }

    //                             $team1key = $getli['teams']['a']['key'] ?? $getli['team1key'];
    //                             $team2key = $getli['teams']['b']['key'] ?? $getli['team2key'];

    //                             if (!empty($team1key) && !empty($team2key)) {
    //                                 $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
    //                                 if (empty($findteam1)) {
    //                                     $existteam1 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['a']['name'] ?? $getli['team1name'])->where('short_name', '=', $team1key)->first();
    //                                     if (empty($existteam1)) {
    //                                         $data['team_key'] = $team1key;
    //                                         $data['team'] = $getli['teams']['a']['name'] ?? $getli['team1name'];
    //                                         $data['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
    //                                         $team1id = DB::connection('mysql2')->table('teams')->insertGetId($data);
    //                                     } else {
    //                                         $team_data1['team_key'] = $team1key;
    //                                         DB::connection('mysql2')->table('teams')->where('id', '=', $existteam1->id)->update($team_data1);
    //                                         $team1id = $existteam1->id;
    //                                     }
    //                                 } else {
    //                                     $team1id = $findteam1->id;
    //                                 }

    //                                 $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
    //                                 if (empty($findteam2)) {
    //                                     $existteam2 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['b']['name'] ?? $getli['team2name'])->where('short_name', '=', $team2key)->first();
    //                                     if (empty($existteam2)) {
    //                                         $data1['team_key'] = $team2key;
    //                                         $data1['team'] = $getli['teams']['b']['name'] ?? $getli['team2name'];
    //                                         $data1['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
    //                                         $team2id = DB::connection('mysql2')->table('teams')->insertGetId($data1);
    //                                     } else {
    //                                         $team_data2['team_key'] = $team2key;
    //                                         DB::connection('mysql2')->table('teams')->where('id', '=', $existteam2->id)->update($team_data2);
    //                                         $team2id = $existteam2->id;
    //                                     }
    //                                 } else {
    //                                     $team2id = $findteam2->id;
    //                                 }
    //                                 $matchadata['status'] = $getli['status'];
    //                                 $matchadata['name'] = $getli['name'];
    //                                 $matchadata['team1'] = $team1id;
    //                                 $matchadata['team2'] = $team2id;
    //                                 $matchadata['series'] = 0;
    //                                 $matchadata['real_matchkey'] = $getli['matchkey'];
    //                                 $matchadata['format'] = $getli['format'];
    //                                 $matchadata['launch_status'] = 'pending';
    //                                 $matchadata['final_status'] = 'pending';
    //                                 $matchadata['start_date'] = $cuurectdate;
    //                                 // dd($matchadata);

    //                                 $mid = DB::connection('mysql2')->table('listmatches')->insertGetId($matchadata);

    //                                 $updatelist['matchkey'] = $mid;
    //                                 DB::connection('mysql2')->table('listmatches')->where('id', $mid)->update($updatelist);
    //                             }
    //                         } else {
    //                             $findsquaddetials = CricketapiController::getmatchdata($getli['matchkey']);

    //                             $team1key = $getli['teams']['a']['key'] ?? $getli['team1key'];
    //                             $team2key = $getli['teams']['b']['key'] ?? $getli['team2key'];

    //                             if (!empty($team1key) && !empty($team2key)) {
    //                                 $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
    //                                 if (empty($findteam1)) {
    //                                     $existteam1 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['a']['name'] ?? $getli['team1name'])->where('short_name', '=', $team1key)->first();
    //                                     if (empty($existteam1)) {
    //                                         $data['team_key'] = $team1key;
    //                                         $data['team'] = $getli['teams']['a']['name'] ?? $getli['team1name'];
    //                                         $data['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
    //                                         $team1id = DB::connection('mysql2')->table('teams')->insertGetId($data);
    //                                     } else {
    //                                         $team_data1['team_key'] = $team1key;
    //                                         DB::connection('mysql2')->table('teams')->where('id', '=', $existteam1->id)->update($team_data1);
    //                                         $team1id = $existteam1->id;
    //                                     }
    //                                 } else {
    //                                     $team1id = $findteam1->id;
    //                                 }

    //                                 $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
    //                                 if (empty($findteam2)) {
    //                                     $existteam2 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['b']['name'] ?? $getli['team2name'])->where('short_name', '=', $team2key)->first();
    //                                     if (empty($existteam2)) {
    //                                         $data1['team_key'] = $team2key;
    //                                         $data1['team'] = $getli['teams']['b']['name'] ?? $getli['team2name'];
    //                                         $data1['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
    //                                         $team2id = DB::connection('mysql2')->table('teams')->insertGetId($data1);
    //                                     } else {
    //                                         $team_data2['team_key'] = $team2key;
    //                                         DB::connection('mysql2')->table('teams')->where('id', '=', $existteam2->id)->update($team_data2);
    //                                         $team2id = $existteam2->id;
    //                                     }
    //                                 } else {
    //                                     $team2id = $findteam2->id;
    //                                 }
    //                                 $matchadata['status'] = $getli['status'];
    //                                 $matchadata['name'] = $getli['name'];
    //                                 $matchadata['team1'] = $team1id;
    //                                 $matchadata['team2'] = $team2id;
    //                                 $matchadata['format'] = $getli['format'];
    //                                 $matchadata['start_date'] = $cuurectdate;

    //                                 DB::connection('mysql2')->table('listmatches')->where('real_matchkey', $getli['matchkey'])->update($matchadata);
    //                             }
    //                         }
    //                     }
    //                 }

    //             } else {
    //                 foreach ($daylist as $daylist) {
    //                     if (isset($daylist['matches'])) {
    //                         $getlist = $daylist['matches'];

    //                         if (!empty($getlist)) {
    //                             foreach ($getlist as $getli) {
    //                                 $cuurectdate = date('Y-m-d H:i:s', strtotime($getli['start_date']['iso']));
    //                                 $cuurectdate = date('Y-m-d H:i:s', strtotime($getli['start_date']));

    //                                 $matchadata = array();
    //                                 $findmatchexist = DB::table('listmatches')->where('matchkey', $getli['matchkey'])->orWhere('real_matchkey', $getli['matchkey'])->first();

    //                                 if (empty($findmatchexist)) {

    //                                     $findsquaddetials = CricketapiController::getmatchdata($getli['matchkey']);
    //                                     if (!empty($findsquaddetials)) {
    //                                         $squadteam1players = array();
    //                                         $squadteam2players = array();
    //                                         if (isset($findsquaddetials['data']['card']['teams']['a']['match']['players'])) {
    //                                             $squadteam1players = $findsquaddetials['data']['card']['teams']['a']['match']['players'];
    //                                         }

    //                                         if (isset($findsquaddetials['data']['card']['teams']['b']['match']['players'])) {
    //                                             $squadteam2players = $findsquaddetials['data']['card']['teams']['b']['match']['players'];
    //                                         }
    //                                         $matchadata['squadstatus'] = 'no';
    //                                         if ((!empty($squadteam1players)) && (!empty($squadteam2players))) {
    //                                             $matchadata['squadstatus'] = 'yes';
    //                                         }
    //                                     }
    //                                     $team1key = $getli['teams']['a']['key'] ?? $getli['team1key'];
    //                                     $team2key = $getli['teams']['b']['key'] ?? $getli['team2key'];

    //                                     if (!empty($team1key) && !empty($team2key)) {
    //                                         $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
    //                                         if (empty($findteam1)) {
    //                                             $existteam1 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['a']['name'] ?? $getli['team1name'])->where('short_name', '=', $team1key)->first();
    //                                             if (empty($existteam1)) {
    //                                                 $data['team_key'] = $team1key;
    //                                                 $data['team'] = $getli['teams']['a']['name'] ?? $getli['team1name'];
    //                                                 $data['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
    //                                                 $team1id = DB::connection('mysql2')->table('teams')->insertGetId($data);
    //                                             } else {
    //                                                 $team_data1['team_key'] = $team1key;
    //                                                 DB::connection('mysql2')->table('teams')->where('id', '=', $existteam1->id)->update($team_data1);
    //                                                 $team1id = $existteam1->id;
    //                                             }
    //                                         } else {
    //                                             $team1id = $findteam1->id;
    //                                         }

    //                                         $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
    //                                         if (empty($findteam2)) {
    //                                             $existteam2 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['b']['name'] ?? $getli['team2name'])->where('short_name', '=', $team2key)->first();
    //                                             if (empty($existteam2)) {
    //                                                 $data1['team_key'] = $team2key;
    //                                                 $data1['team'] = $getli['teams']['b']['name'] ?? $getli['team2name'];
    //                                                 $data1['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
    //                                                 $team2id = DB::connection('mysql2')->table('teams')->insertGetId($data1);
    //                                             } else {
    //                                                 $team_data2['team_key'] = $team2key;
    //                                                 DB::connection('mysql2')->table('teams')->where('id', '=', $existteam2->id)->update($team_data2);
    //                                                 $team2id = $existteam2->id;
    //                                             }
    //                                         } else {
    //                                             $team2id = $findteam2->id;
    //                                         }
    //                                         $matchadata['status'] = $getli['status'];
    //                                         $matchadata['name'] = $getli['name'];
    //                                         $matchadata['team1'] = $team1id;
    //                                         $matchadata['team2'] = $team2id;
    //                                         $matchadata['series'] = 0;
    //                                         $matchadata['real_matchkey'] = $getli['matchkey'];
    //                                         $matchadata['format'] = $getli['format'];
    //                                         $matchadata['launch_status'] = 'pending';
    //                                         $matchadata['final_status'] = 'pending';
    //                                         $matchadata['start_date'] = $cuurectdate;

    //                                         $mid = DB::connection('mysql2')->table('listmatches')->insertGetId($matchadata);

    //                                         $updatelist['matchkey'] = $mid;
    //                                         DB::connection('mysql2')->table('listmatches')->where('id', $mid)->update($updatelist);
    //                                     }
    //                                 } else {
    //                                     $findsquaddetials = CricketapiController::getmatchdata($getli['matchkey']);

    //                                     $team1key = $getli['teams']['a']['key'] ?? $getli['team1key'];
    //                                     $team2key = $getli['teams']['b']['key'] ?? $getli['team2key'];

    //                                     if (!empty($team1key) && !empty($team2key)) {
    //                                         $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
    //                                         if (empty($findteam1)) {
    //                                             $existteam1 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['a']['name'] ?? $getli['team1name'])->where('short_name', '=', $team1key)->first();
    //                                             if (empty($existteam1)) {
    //                                                 $data['team_key'] = $team1key;
    //                                                 $data['team'] = $getli['teams']['a']['name'] ?? $getli['team1name'];
    //                                                 $data['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
    //                                                 $team1id = DB::connection('mysql2')->table('teams')->insertGetId($data);
    //                                             } else {
    //                                                 $team_data1['team_key'] = $team1key;
    //                                                 DB::connection('mysql2')->table('teams')->where('id', '=', $existteam1->id)->update($team_data1);
    //                                                 $team1id = $existteam1->id;
    //                                             }
    //                                         } else {
    //                                             $team1id = $findteam1->id;
    //                                         }

    //                                         $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
    //                                         if (empty($findteam2)) {
    //                                             $existteam2 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['b']['name'] ?? $getli['team2name'])->where('short_name', '=', $team2key)->first();
    //                                             if (empty($existteam2)) {
    //                                                 $data1['team_key'] = $team2key;
    //                                                 $data1['team'] = $getli['teams']['b']['name'] ?? $getli['team2name'];
    //                                                 $data1['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
    //                                                 $team2id = DB::connection('mysql2')->table('teams')->insertGetId($data1);
    //                                             } else {
    //                                                 $team_data2['team_key'] = $team2key;
    //                                                 DB::connection('mysql2')->table('teams')->where('id', '=', $existteam2->id)->update($team_data2);
    //                                                 $team2id = $existteam2->id;
    //                                             }
    //                                         } else {
    //                                             $team2id = $findteam2->id;
    //                                         }
    //                                         $matchadata['status'] = $getli['status'];
    //                                         $matchadata['name'] = $getli['name'];
    //                                         $matchadata['team1'] = $team1id;
    //                                         $matchadata['team2'] = $team2id;
    //                                         $matchadata['format'] = $getli['format'];
    //                                         $matchadata['start_date'] = $cuurectdate;

    //                                         DB::connection('mysql2')->table('listmatches')->where('real_matchkey', $getli['matchkey'])->update($matchadata);
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }

    //         }
    //     }
    //     return redirect()->action('MatchController@upcoming_matches')->with('success', 'Match imported successfully');

    // }

    public function importseriesdata(){
        date_default_timezone_set('Asia/Kolkata');
        $getseriesdata = CricketapiController::getseriesdetail();
        if(!empty($getseriesdata)){
            foreach($getseriesdata as $seriesdata){
                $series['series_key']=$seriesdata['series_key'];
                $series['name']=$seriesdata['name'];
                $series['start_date']=$seriesdata['start_date'];
                $series['end_date']=$seriesdata['end_date'];
                $series['status']='opened';
                $checkseries = DB::table('series')->where('series_key',$series['series_key'])->first();
                if(!empty($checkseries)){
                    DB::table('series')->where('series_key',$series['series_key'])->update($series);
                }else{
                    DB::table('series')->insert($series);
                }
            }
            return redirect()->back()->with('success','Series Successfully Imported'); 
        }

    }
    // to import the data from the api //
    public function importdatafromapi()
    {
        date_default_timezone_set('Asia/Kolkata');
        $getdayslist = CricketapiController::getlistmatches();
        if (!empty($getdayslist)) {
            foreach ($getdayslist as $daylist) {
                if (isset($daylist['matches'])) {
                    $getlist = $daylist['matches'];

                    $this->letsImport($getlist);

                } else {
                    foreach ($daylist as $daylist) {
                        if (isset($daylist['matches'])) {
                            $getlist = $daylist['matches'];

                            $this->letsImport($getlist);
                        }
                    }
                }

            }
        }
        return redirect()->action('MatchController@upcoming_matches')->with('success', 'Match imported successfully');

    }
    public function checkTeam1($team1key, $findsquaddetials, $getli)
    {
        $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id', 'short_name')->first();
        if (empty($findteam1)) {
            $existteam1 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['a']['name'] ?? $getli['team1name'])->where('short_name', '=', $team1key)->first();
            if (empty($existteam1)) {
                $data['team_key'] = $team1key;
                $data['team'] = $getli['teams']['a']['name'] ?? $getli['team1name'];
                $data['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
                $team1id = DB::connection('mysql2')->table('teams')->insertGetId($data);
                return $team1id;
            } else {
                $team_data1['team_key'] = $team1key;
                if(isset($existteam1->short_name)){
                    if ($existteam1->short_name == $team1key) {
                        $team_data1['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
                    }
                }
                
                DB::connection('mysql2')->table('teams')->where('id', '=', $existteam1->id)->update($team_data1);
                $team1id = $existteam1->id;
                return $team1id;
            }
        } else {
            $team1id = $findteam1->id;
            if ($findteam1->short_name == $team1key) {
                $team_data1['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
                DB::connection('mysql2')->table('teams')->where('id', '=', $findteam1->id)->update($team_data1);
            }
            return $team1id;
        }
    }
    public function checkTeam2($team2key, $findsquaddetials, $getli)
    {
        $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id', 'short_name')->first();
        if (empty($findteam2)) {
            $existteam2 = DB::table('teams')->select('id')->where('team', '=', $getli['teams']['b']['name'] ?? $getli['team2name'])->where('short_name', '=', $team2key)->first();
            if (empty($existteam2)) {
                $data1['team_key'] = $team2key;
                $data1['team'] = $getli['teams']['b']['name'] ?? $getli['team2name'];
                $data1['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
                $team2id = DB::connection('mysql2')->table('teams')->insertGetId($data1);
                return $team2id;
            } else {
                $team_data2['team_key'] = $team2key;
                
                if(isset($existteam2->short_name)){
                    if ($existteam2->short_name == $team2key) {
                        $team_data2['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
                    }
                }
                DB::connection('mysql2')->table('teams')->where('id', '=', $existteam2->id)->update($team_data2);
                $team2id = $existteam2->id;
                return $team2id;
            }
        } else {
            $team2id = $findteam2->id;
            if ($findteam2->short_name == $team2key) {
                $team_data2['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
                DB::connection('mysql2')->table('teams')->where('id', '=', $findteam2->id)->update($team_data2);
            }
            return $team2id;
        }
    }
    public function letsImport($getlist){
        if (!empty($getlist)) {
            foreach ($getlist as $getli) {
                // $cuurectdate = date('Y-m-d H:i:s', strtotime($getli['start_date']['iso']));
                $cuurectdate = date('Y-m-d H:i:s', strtotime($getli['start_date']));

                $matchadata = array();
                $findmatchexist = DB::table('listmatches')->where('matchkey', $getli['matchkey'])->orWhere('real_matchkey', $getli['matchkey'])->first();

                if (empty($findmatchexist)) {

                    $findsquaddetials = CricketapiController::getmatchdata($getli['matchkey']);
                    if (!empty($findsquaddetials)) {
                        $squadteam1players = array();
                        $squadteam2players = array();
                        if (isset($findsquaddetials['data']['card']['teams']['a']['match']['players'])) {
                            $squadteam1players = $findsquaddetials['data']['card']['teams']['a']['match']['players'];
                        }

                        if (isset($findsquaddetials['data']['card']['teams']['b']['match']['players'])) {
                            $squadteam2players = $findsquaddetials['data']['card']['teams']['b']['match']['players'];
                        }
                        $matchadata['squadstatus'] = 'no';
                        if ((!empty($squadteam1players)) && (!empty($squadteam2players))) {
                            $matchadata['squadstatus'] = 'yes';
                        }
                    }
                    $team1key = $getli['teams']['a']['key'] ?? $getli['team1key'];
                    $team2key = $getli['teams']['b']['key'] ?? $getli['team2key'];

                    if (!empty($team1key) && !empty($team2key)) {
                        $team1id = $this->checkTeam1($team1key, $findsquaddetials, $getli);
                        $team2id = $this->checkTeam2($team2key, $findsquaddetials, $getli);
                        $matchadata['status'] = $getli['status'];
                        $matchadata['name'] = $getli['name'];
                        $seriesid =  DB::table('series')->where('series_key',$getli['series_key'])->select('id')->first();
                        if(!empty($seriesid)){
                            $matchadata['series'] = $seriesid->id;
                            $matchadata['series_Key'] = $getli['series_key'];
                        }
                        $matchadata['team1'] = $team1id;
                        $matchadata['team2'] = $team2id;
                        $matchadata['series'] = 0;
                        $matchadata['real_matchkey'] = $getli['matchkey'];
                        $matchadata['format'] = $getli['format'];
                        $matchadata['launch_status'] = 'pending';
                        $matchadata['final_status'] = 'pending';
                        $matchadata['start_date'] = $cuurectdate;

                        $mid = DB::connection('mysql2')->table('listmatches')->insertGetId($matchadata);

                        $updatelist['matchkey'] = $mid;
                        DB::connection('mysql2')->table('listmatches')->where('id', $mid)->update($updatelist);
                    }
                } else {
                    $findsquaddetials = CricketapiController::getmatchdata($getli['matchkey']);
                    // dd($findsquaddetials);
                    $team1key = $getli['teams']['a']['key'] ?? $getli['team1key'];
                    $team2key = $getli['teams']['b']['key'] ?? $getli['team2key'];

                    if (!empty($team1key) && !empty($team2key)) {
                        $team1id = $this->checkTeam1($team1key, $findsquaddetials, $getli);
                        $team2id = $this->checkTeam2($team2key, $findsquaddetials, $getli);
                        $matchadata['status'] = $getli['status'];
                        $matchadata['name'] = $getli['name'];
                        $seriesid =  DB::table('series')->where('series_key',$getli['series_key'])->select('id')->first();
                        if(!empty($seriesid)){
                            $matchadata['series'] = $seriesid->id;
                            $matchadata['series_Key'] = $getli['series_key'];
                        }
                        
                        $matchadata['team1'] = $team1id;
                        $matchadata['team2'] = $team2id;
                        $matchadata['format'] = $getli['format'];
                        if($findmatchexist->second_inning_status!=2){
							$matchadata['start_date'] = $cuurectdate;
						}else{
							$matchadata['start_date'] = date('Y-m-d H:i:s',strtotime(' +1 hours +50 minutes',strtotime($cuurectdate)));
						}
						
						DB::connection('mysql2')->table('listmatches')->where('matchkey', $findmatchexist->matchkey)->where('real_matchkey', $getli['matchkey'])->update($matchadata);
					 }
                }
            }
        }
    }
    // to edit the match //
    public function editmatch($matchkey, Request $request)
    {
        $f_type = request()->get('fantasy_type');
        if (empty($f_type)) {
            $f_type = 'Cricket';
        }
        date_default_timezone_set('Asia/Kolkata');
        $currentdate = date('Y-m-d');
        if ($request->isMethod('post')) {
            $data = $request->all();
            $team1 = $request->team1;
            $team2 = $request->team2;
            $team1data = DB::table('teams')->where('team', '=', $team1)->select('id')->first();
            if (!empty($team1data)) {
                $data['team1'] = $team1data->id;
            }
            $team2data = DB::table('teams')->where('team', '=', $team2)->select('id')->first();
            if (!empty($team2data)) {
                $data['team2'] = $team2data->id;
            }
            unset($data['_token']);
            DB::connection('mysql2')->table('listmatches')->where('matchkey', $matchkey)->update($data);
            return redirect()->action('MatchController@upcoming_matches', ['fantasy_type' => $_POST['fantasy_type']])->with('success', 'Successfully updated match details!');
        }

        //data from listmatches and team //
        $findmatchdetails = DB::table('listmatches')->leftjoin('teams as team1', 'listmatches.team1', '=', 'team1.id')->leftjoin('teams as team2', 'listmatches.team2', '=', 'team2.id')->select('listmatches.id', 'listmatches.name', 'listmatches.team1', 'listmatches.team2', 'listmatches.matchkey', 'listmatches.start_date', 'listmatches.format', 'listmatches.series', 'team1.team as team1name', 'team2.team as team2name', 'listmatches.tbl_order','listmatches.match_notification')->where('matchkey', '=', $matchkey)->first();

        $findmatchseries = DB::table('series')->where('end_date', '>=', $currentdate)
            ->where('status', 'opened')
            ->where('fantasy_type', $f_type)
            ->get();
        return view('matches.editmatch', compact('findmatchdetails', 'findmatchseries', 'f_type'));
    }

    // to import the player if player squad is 'no' //
    public function importsquad($matchid)
    {
        $findmatch = DB::table('listmatches')->where('matchkey', $matchid)->first();
        $getdetails = CricketapiController::getmatchdata($findmatch->real_matchkey);

        if (!empty($getdetails)) {
            $matchkikey = $getdetails['data']['card']['key'];
            $team1players = $getdetails['data']['card']['teams']['a']['match']['players'];
            $team2players = $getdetails['data']['card']['teams']['b']['match']['players'];
            $team1key = $getdetails['data']['card']['teams']['a']['key'];
            $team2key = $getdetails['data']['card']['teams']['b']['key'];
            // insert team 1//
            $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
            if (empty($findteam1)) {
                $mpdata['team_key'] = $team1key;
                $mpdata['team'] = $getdetails['data']['card']['teams']['a']['name'];
                $mpdata['short_name'] = $team1key;
                $team1id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata);
            } else {
                $team1id = $findteam1->id;
            }

            // insert team 2//
            $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
            if (empty($findteam2)) {
                $mpdata1['team_key'] = $team2key;
                $mpdata1['team'] = $getdetails['data']['card']['teams']['b']['name'];
                $mpdata1['short_name'] = $team2key;
                $team2id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata1);
            } else {
                $team2id = $findteam2->id;
            }
            $matchadata['team1'] = $team1id;
            $matchadata['team2'] = $team2id;
            $matchadata['name'] = $getdetails['data']['card']['name'];
            if (!empty($team1players) && !empty($team2players)) {
                $matchadata['squadstatus'] = 'yes';
            }
            $msg = '';
            $sts = '';
            if (isset($matchadata['squadstatus'])) {
                $sts = 'success';
                $msg = 'Successfully updated!';
            } else {
                $sts = 'danger';
                $msg = 'Squad not available ';
            }
            // $matchadata['squadstatus'] = 'yes';
            DB::connection('mysql2')->table('listmatches')->where('matchkey', $matchid)->update($matchadata);
            return redirect()->action('MatchController@upcoming_matches')->with($sts, $msg);

        }
    }
    // to import the player if player squad is 'no' //
    public function importsquad_new($matchid, $page)
    {
        $getdetails = CricketapiController::getmatchdata($matchid);
        if (!empty($getdetails)) {
            $matchkikey = $getdetails['data']['card']['key'];
            $team1players = $getdetails['data']['card']['teams']['a']['match']['players'];
            $team2players = $getdetails['data']['card']['teams']['b']['match']['players'];
            $team1key = $getdetails['data']['card']['teams']['a']['key'];
            $team2key = $getdetails['data']['card']['teams']['b']['key'];
            // insert team 1//
            $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
            if (empty($findteam1)) {
                $mpdata['team_key'] = $team1key;
                $mpdata['team'] = $getdetails['data']['card']['teams']['a']['name'];
                $mpdata['short_name'] = $team1key;
                $team1id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata);
            } else {
                $team1id = $findteam1->id;
            }

            // insert team 2//
            $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
            if (empty($findteam2)) {
                $mpdata1['team_key'] = $team2key;
                $mpdata1['team'] = $getdetails['data']['card']['teams']['b']['name'];
                $mpdata1['short_name'] = $team2key;
                $team2id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata1);
            } else {
                $team2id = $findteam2->id;
            }
            $matchadata['team1'] = $team1id;
            $matchadata['team2'] = $team2id;
            $matchadata['name'] = $getdetails['data']['card']['name'];
            $matchadata['squadstatus'] = 'yes';
            DB::connection('mysql2')->table('listmatches')->where('matchkey', $matchid)->update($matchadata);
            Session::flash('message', 'Successfully updated!');
            Session::flash('alert-class', 'alert-success');
            return redirect('my-admin/upcoming_matches?page=' . $page);

        }
    }

    public function viewmatchdetails($matchid)
    {
        $findmatch = DB::table('listmatches')->where('matchkey', $matchid)->first();
        if (!empty($findmatch)) {
            if ($findmatch->launch_status != 'launched') {
                $matchid = $findmatch->real_matchkey;

                $getdetails = CricketapiController::getmatchdata($matchid);
                // $playerscredit = CricketapiController::getmatchesdata($matchid);

                if (!empty($getdetails)) {
                    $matchkikey = $getdetails['data']['card']['key'];
                    $team1players = array();
                    $team2players = array();
                    if (isset($getdetails['data']['card']['teams']['a']['match']['players'])) {
                        $team1players = $getdetails['data']['card']['teams']['a']['match']['players'];
                    }
                    if (isset($getdetails['data']['card']['teams']['b']['match']['players'])) {
                        $team2players = $getdetails['data']['card']['teams']['b']['match']['players'];
                    }
                    if (!empty($team1players) && !empty($team2players)) {
                        $matchkikey = $getdetails['data']['card']['key'];
                        $team1key = $getdetails['data']['card']['teams']['a']['key'];
                        $team2key = $getdetails['data']['card']['teams']['b']['key'];
                        // insert team 1//
                        $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
                        if (empty($findteam1)) {
                            $mpdata['team_key'] = $team1key;
                            $mpdata['team'] = $getdetails['data']['card']['teams']['a']['name'];
                            $mpdata['short_name'] = $getdetails['data']['card']['teams']['a']['short_name'] ?? $team1key;
                            $team1id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata);
                        } else {
                            $team1id = $findteam1->id;
                        }

                        // insert team 2//
                        $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
                        if (empty($findteam2)) {
                            $mpdata1['team_key'] = $team2key;
                            $mpdata1['team'] = $getdetails['data']['card']['teams']['b']['name'];
                            $mpdata1['short_name'] = $getdetails['data']['card']['teams']['b']['short_name'] ?? $team2key;
                            $team2id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata1);
                        } else {
                            $team2id = $findteam2->id;
                        }
                        $matchadata['team1'] = $team1id;
                        $matchadata['team2'] = $team2id;
                        $matchadata['name'] = $getdetails['data']['card']['name'];
                        $matchadata['squadstatus'] = 'yes';
                        DB::connection('mysql2')->table('listmatches')->where('matchkey', $matchid)->update($matchadata);
                    }
                    if (!empty($team1players)) {
                        foreach ($team1players as $players1) {
                            $playerkey = $players1;
                            $pkey = '';
                            if (!empty($playerscredit) && !empty($playerscredit['data']) && isset($playerscredit['data']['fantasy_points']) && isset($playerscredit['data']['fantasy_points'])) {
                                $pkey = array_search($players1, array_column($playerscredit['data']['fantasy_points'], 'player'));
                            }

                            // insert players details which we get from api//
                            $teamkey = $getdetails['data']['card']['teams']['a']['key'];
                            $findmatchexist = DB::table('teams')->where('team_key', $teamkey)->select('id')->first();
                            if (!empty($findmatchexist)) {
                                $findplayerexist = DB::table('players')->where('players_key', $players1)->where('team', $findmatchexist->id)->first();
                                $data['player_name'] = $getdetails['data']['card']['players'][$players1]['fullname'];
                                $data['players_key'] = $playerkey;
                                // $data['credit']=($pkey!='' || $pkey==0)?$playerscredit['data']['fantasy_points'][$pkey]['credit_value']:9;
                                $data['credit'] = 9;
                                if (empty($findplayerexist)) {
                                    $data['team'] = $findmatchexist->id;
                                    if ($getdetails['data']['card']['players'][$players1]['seasonal_role'] == "") {
                                        $data['role'] = 'allrounder';
                                    } else {
                                        $data['role'] = $getdetails['data']['card']['players'][$players1]['seasonal_role'];
                                    }
                                    $playerid = DB::connection('mysql2')->table('players')->insertGetId($data);
                                    // $credit=($pkey!='' || $pkey==0)?$playerscredit['data']['fantasy_points'][$pkey]['credit_value']:9;
                                    $credit = 9;
                                } else {
                                    $playerid = $findplayerexist->id;
                                    // $credit = ($pkey!='' || $pkey==0)?$playerscredit['data']['fantasy_points'][$pkey]['credit_value']:$findplayerexist->credit;
                                    $credit = $findplayerexist->credit;
                                    $data['role'] = $findplayerexist->role;
                                    $getdetails['data']['card']['players'][$players1]['seasonal_role'] = $findplayerexist->role;
                                }
                                // insert players for a match//
                                $findplayer1entry = DB::table('matchplayers')->where('matchkey', $findmatch->matchkey)->where('playerid', $playerid)->first();
                                if (empty($findplayer1entry)) {
                                    $matchplayerdata['matchkey'] = $findmatch->matchkey;
                                    $matchplayerdata['playerid'] = $playerid;
                                    $matchplayerdata['role'] = $data['role'];
                                    $matchplayerdata['name'] = $data['player_name'];
                                    $matchplayerdata['credit'] = $credit;
                                    DB::connection('mysql2')->table('matchplayers')->insert($matchplayerdata);
                                }
                            }
                        }
                    }
                    if (!empty($team2players)) {
                        foreach ($team2players as $players2) {
                            $playerkey2 = $players2;
                            $pkey1 = '';
                            $playerid = "";
                            $findplayer2exist = array();
                            $data = array();
                            if (!empty($playerscredit) && !empty($playerscredit['data']) && isset($playerscredit['data']['fantasy_points']) && !empty($playerscredit['data']['fantasy_points'])) {
                                $pkey1 = array_search($players2, array_column($playerscredit['data']['fantasy_points'], 'player'));
                            }
                            $team2key = $getdetails['data']['card']['teams']['b']['key'];
                            $findmatchexist = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
                            if (!empty($findmatchexist)) {
                                $findplayer2exist = DB::table('players')->where('players_key', $players2)->where('team', $findmatchexist->id)->first();
                                $data['player_name'] = $getdetails['data']['card']['players'][$players2]['fullname'];
                                $data['players_key'] = $playerkey2;
                                // $data['credit']=($pkey1!='' || $pkey1==0)?$playerscredit['data']['fantasy_points'][$pkey1]['credit_value']:9;
                                $data['credit'] = 9;
                                if (empty($findplayer2exist)) {
                                    $data['team'] = $findmatchexist->id;
                                    if ($getdetails['data']['card']['players'][$players2]['seasonal_role'] == "") {
                                        $data['role'] = 'allrounder';
                                    } else {
                                        $data['role'] = $getdetails['data']['card']['players'][$players2]['seasonal_role'];
                                    }
                                    $playerid = DB::connection('mysql2')->table('players')->insertGetId($data);
                                    // $credit=($pkey1!='' || $pkey1==0)?$playerscredit['data']['fantasy_points'][$pkey1]['credit_value']:9;
                                    $credit = 9;
                                } else {
                                    $playerid = $findplayer2exist->id;
                                    // $credit = ($pkey1!='' || $pkey1==0)?$playerscredit['data']['fantasy_points'][$pkey1]['credit_value']:$findplayer2exist->credit;
                                    $credit = $findplayer2exist->credit;
                                    $getdetails['data']['card']['players'][$players2]['seasonal_role'] = $findplayer2exist->role;
                                    $data['role'] = $findplayer2exist->role;
                                }
                                $findplayer2entry = DB::table('matchplayers')->where('matchkey', $findmatch->matchkey)->where('playerid', $playerid)->first();
                                if (empty($findplayer2entry)) {
                                    $matchplayerdata['matchkey'] = $findmatch->matchkey;
                                    $matchplayerdata['playerid'] = $playerid;
                                    if ($data['role'] != "") {
                                        $matchplayerdata['role'] = $data['role'];
                                    }
                                    $matchplayerdata['name'] = $data['player_name'];
                                    $matchplayerdata['credit'] = $credit;
                                    DB::connection('mysql2')->table('matchplayers')->insert($matchplayerdata);
                                }
                            }
                        }
                    }

                }
            }
        }
        return redirect()->back()->with('Player Successfully Deleted!');
    }
    public function updatematchdetails($matchid)
    {
        $findmatch = DB::table('listmatches')->where('matchkey', $matchid)->first();
        if (!empty($findmatch)) {
            if (date('Y-m-d H:i:s',strtotime($findmatch->start_date)) >= date('Y-m-d H:i:s')) {
                $matchid = $findmatch->real_matchkey;

                $getdetails = CricketapiController::getmatchdata($matchid);
                // $playerscredit = CricketapiController::getmatchesdata($matchid);

                if (!empty($getdetails)) {
                    $matchkikey = $getdetails['data']['card']['key'];
                    $team1players = array();
                    $team2players = array();
                    if (isset($getdetails['data']['card']['teams']['a']['match']['players'])) {
                        $team1players = $getdetails['data']['card']['teams']['a']['match']['players'];
                    }
                    if (isset($getdetails['data']['card']['teams']['b']['match']['players'])) {
                        $team2players = $getdetails['data']['card']['teams']['b']['match']['players'];
                    }
                    if (!empty($team1players) && !empty($team2players)) {
                        $matchkikey = $getdetails['data']['card']['key'];
                        $team1key = $getdetails['data']['card']['teams']['a']['key'];
                        $team2key = $getdetails['data']['card']['teams']['b']['key'];
                        // insert team 1//
                        $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
                        if (empty($findteam1)) {
                            $mpdata['team_key'] = $team1key;
                            $mpdata['team'] = $getdetails['data']['card']['teams']['a']['name'];
                            $mpdata['short_name'] = $getdetails['data']['card']['teams']['a']['short_name'] ?? $team1key;
                            $team1id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata);
                        } else {
                            $team1id = $findteam1->id;
                        }

                        // insert team 2//
                        $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
                        if (empty($findteam2)) {
                            $mpdata1['team_key'] = $team2key;
                            $mpdata1['team'] = $getdetails['data']['card']['teams']['b']['name'];
                            $mpdata1['short_name'] = $getdetails['data']['card']['teams']['b']['short_name'] ?? $team2key;
                            $team2id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata1);
                        } else {
                            $team2id = $findteam2->id;
                        }
                        $matchadata['team1'] = $team1id;
                        $matchadata['team2'] = $team2id;
                        $matchadata['name'] = $getdetails['data']['card']['name'];
                        $matchadata['squadstatus'] = 'yes';
                        DB::connection('mysql2')->table('listmatches')->where('matchkey', $matchid)->update($matchadata);
                    }
                    if (!empty($team1players)) {
                        foreach ($team1players as $players1) {
                            $playerkey = $players1;
                            $pkey = '';
                            if (!empty($playerscredit) && !empty($playerscredit['data']) && isset($playerscredit['data']['fantasy_points']) && isset($playerscredit['data']['fantasy_points'])) {
                                $pkey = array_search($players1, array_column($playerscredit['data']['fantasy_points'], 'player'));
                            }

                            // insert players details which we get from api//
                            $teamkey = $getdetails['data']['card']['teams']['a']['key'];
                            $findmatchexist = DB::table('teams')->where('team_key', $teamkey)->select('id')->first();
                            if (!empty($findmatchexist)) {
                                $findplayerexist = DB::table('players')->where('players_key', $players1)->where('team', $findmatchexist->id)->first();
                                $data['player_name'] = $getdetails['data']['card']['players'][$players1]['fullname'];
                                $data['players_key'] = $playerkey;
                                // $data['credit']=($pkey!='' || $pkey==0)?$playerscredit['data']['fantasy_points'][$pkey]['credit_value']:9;
                                $data['credit'] = 9;
                                if (empty($findplayerexist)) {
                                    $data['team'] = $findmatchexist->id;
                                    if ($getdetails['data']['card']['players'][$players1]['seasonal_role'] == "") {
                                        $data['role'] = 'allrounder';
                                    } else {
                                        $data['role'] = $getdetails['data']['card']['players'][$players1]['seasonal_role'];
                                    }
                                    $playerid = DB::connection('mysql2')->table('players')->insertGetId($data);
                                    // $credit=($pkey!='' || $pkey==0)?$playerscredit['data']['fantasy_points'][$pkey]['credit_value']:9;
                                    $credit = 9;
                                } else {
                                    $playerid = $findplayerexist->id;
                                    // $credit = ($pkey!='' || $pkey==0)?$playerscredit['data']['fantasy_points'][$pkey]['credit_value']:$findplayerexist->credit;
                                    $credit = $findplayerexist->credit;
                                    $data['role'] = $findplayerexist->role;
                                    $getdetails['data']['card']['players'][$players1]['seasonal_role'] = $findplayerexist->role;
                                }
                                // insert players for a match//
                                $findplayer1entry = DB::table('matchplayers')->where('matchkey', $findmatch->matchkey)->where('playerid', $playerid)->first();
                                if (empty($findplayer1entry)) {
                                    $matchplayerdata['matchkey'] = $findmatch->matchkey;
                                    $matchplayerdata['playerid'] = $playerid;
                                    $matchplayerdata['role'] = $data['role'];
                                    $matchplayerdata['name'] = $data['player_name'];
                                    $matchplayerdata['credit'] = $credit;
                                    DB::connection('mysql2')->table('matchplayers')->insert($matchplayerdata);
                                }
                            }
                        }
                    }
                    if (!empty($team2players)) {
                        foreach ($team2players as $players2) {
                            $playerkey2 = $players2;
                            $pkey1 = '';
                            $playerid = "";
                            $findplayer2exist = array();
                            $data = array();
                            if (!empty($playerscredit) && !empty($playerscredit['data']) && isset($playerscredit['data']['fantasy_points']) && !empty($playerscredit['data']['fantasy_points'])) {
                                $pkey1 = array_search($players2, array_column($playerscredit['data']['fantasy_points'], 'player'));
                            }
                            $team2key = $getdetails['data']['card']['teams']['b']['key'];
                            $findmatchexist = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
                            if (!empty($findmatchexist)) {
                                $findplayer2exist = DB::table('players')->where('players_key', $players2)->where('team', $findmatchexist->id)->first();
                                $data['player_name'] = $getdetails['data']['card']['players'][$players2]['fullname'];
                                $data['players_key'] = $playerkey2;
                                // $data['credit']=($pkey1!='' || $pkey1==0)?$playerscredit['data']['fantasy_points'][$pkey1]['credit_value']:9;
                                $data['credit'] = 9;
                                if (empty($findplayer2exist)) {
                                    $data['team'] = $findmatchexist->id;
                                    if ($getdetails['data']['card']['players'][$players2]['seasonal_role'] == "") {
                                        $data['role'] = 'allrounder';
                                    } else {
                                        $data['role'] = $getdetails['data']['card']['players'][$players2]['seasonal_role'];
                                    }
                                    $playerid = DB::connection('mysql2')->table('players')->insertGetId($data);
                                    // $credit=($pkey1!='' || $pkey1==0)?$playerscredit['data']['fantasy_points'][$pkey1]['credit_value']:9;
                                    $credit = 9;
                                } else {
                                    $playerid = $findplayer2exist->id;
                                    // $credit = ($pkey1!='' || $pkey1==0)?$playerscredit['data']['fantasy_points'][$pkey1]['credit_value']:$findplayer2exist->credit;
                                    $credit = $findplayer2exist->credit;
                                    $getdetails['data']['card']['players'][$players2]['seasonal_role'] = $findplayer2exist->role;
                                    $data['role'] = $findplayer2exist->role;
                                }
                                $findplayer2entry = DB::table('matchplayers')->where('matchkey', $findmatch->matchkey)->where('playerid', $playerid)->first();
                                if (empty($findplayer2entry)) {
                                    $matchplayerdata['matchkey'] = $findmatch->matchkey;
                                    $matchplayerdata['playerid'] = $playerid;
                                    if ($data['role'] != "") {
                                        $matchplayerdata['role'] = $data['role'];
                                    }
                                    $matchplayerdata['name'] = $data['player_name'];
                                    $matchplayerdata['credit'] = $credit;
                                    DB::connection('mysql2')->table('matchplayers')->insert($matchplayerdata);
                                }
                            }
                        }
                    }

                }
            }
        }
        return redirect()->back()->with('Player Successfully Deleted!');
    }
    public function launchmatch($matchkey)
    {
        $findmatchdetails = DB::table('listmatches')->leftjoin('teams as team1', 'listmatches.team1', '=', 'team1.id')->leftjoin('teams as team2', 'listmatches.team2', '=', 'team2.id')->select('listmatches.*', 'team1.id as team1id', 'team2.id as team2id', 'team1.team as team1team', 'team2.team as team2team', 'team1.logo as team1logo', 'team2.logo as team2logo')->where('matchkey', $matchkey)->first();
        $batsman1 = 0;
        $batsman2 = 0;
        $bowlers1 = 0;
        $bowlers2 = 0;
        $allrounder1 = 0;
        $allrounder2 = 0;
        $wk1 = 0;
        $wk2 = 0;
        $criteria = 1;
        if (!empty($findmatchdetails)) {
            $team1 = $findmatchdetails->team1id;
            $team2 = $findmatchdetails->team2id;
            $findallmatchplayers = DB::table('matchplayers')->leftjoin('players', 'matchplayers.playerid', '=', 'players.id')->select('matchplayers.*', 'players.team as playersteam')->where('matchkey', $matchkey)->get();
            $findplayer1details = DB::table('matchplayers')->where('matchplayers.matchkey', $findmatchdetails->matchkey)->where('players.team', $findmatchdetails->team1)->join('players', 'players.id', '=', 'matchplayers.playerid')->select('matchplayers.*', 'players.image')->get();

            $findplayer2details = DB::table('matchplayers')->where('matchplayers.matchkey', $findmatchdetails->matchkey)->where('players.team', $findmatchdetails->team2)->join('players', 'players.id', '=', 'matchplayers.playerid')->select('matchplayers.*', 'players.image')->get();

            if (!empty($findallmatchplayers)) {
                foreach ($findallmatchplayers as $matchplay) {
                    if ($matchplay->playersteam == $team1) {
                        if ($matchplay->role == 'bowler') {
                            $bowlers1++;
                        }
                        if ($matchplay->role == 'batsman') {
                            $batsman1++;
                        }
                        if ($matchplay->role == 'allrounder') {
                            $allrounder1++;
                        }
                        if ($matchplay->role == 'keeper') {
                            $wk1++;
                        }
                        if ($matchplay->role == "") {
                            $criteria = 0;
                            return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'You cannot launch this match because the role of ' . ucwords($matchplay->name) . ' is not defined.');
                        }
                    }
                    if ($matchplay->playersteam == $team2) {
                        if ($matchplay->role == 'bowler') {
                            $bowlers2++;
                        }
                        if ($matchplay->role == 'batsman') {
                            $batsman2++;
                        }
                        if ($matchplay->role == 'allrounder') {
                            $allrounder2++;
                        }
                        if ($matchplay->role == 'keeper') {
                            $wk2++;
                        }
                        if ($matchplay->role == "") {
                            $criteria = 0;
                            return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'You cannot launch this match because the role of ' . ucwords($matchplay->name) . ' is not defined.');
                        }
                    }
                }
            }
        }
        $fantasy_type = 'Cricket';
        return view('matches.launchmatch', compact('fantasy_type', 'findmatchdetails', 'batsman1', 'batsman2', 'bowlers1', 'bowlers2', 'allrounder1', 'allrounder2', 'wk1', 'wk2', 'findplayer1details', 'findplayer2details'));
    }

    public function updateduoplayer(Request $request){
        $checkduoplayer = DB::table('matchplayers')->where('matchkey',$request->matchkey)->where('id',$request->playerid)->first();
        
        // alert($totalbatsman);
        if($checkduoplayer->role=='batsman'){
            $totalbatsman = DB::table('matchplayers')->where('matchkey',$request->matchkey)->where('role','batsman')->where('forduo',1)->count();
            if($totalbatsman<4 OR $checkduoplayer->forduo==1){
                if($checkduoplayer->forduo==0){
                    DB::table('matchplayers')->where('matchkey',$request->matchkey)->where('id',$request->playerid)->update(['forduo'=>1]);
                }else{
                    DB::table('matchplayers')->where('matchkey',$request->matchkey)->where('id',$request->playerid)->update(['forduo'=>0]);
                }
                return 1;
            }else{
                return 2;
            }
        }
        if($checkduoplayer->role=='bowler'){
            $totalbowler = DB::table('matchplayers')->where('matchkey',$request->matchkey)->where('role','bowler')->where('forduo',1)->count();
            if($totalbowler<4 OR $checkduoplayer->forduo==1){
                if($checkduoplayer->forduo==0){
                    DB::table('matchplayers')->where('matchkey',$request->matchkey)->where('id',$request->playerid)->update(['forduo'=>1]);
                }else{
                    DB::table('matchplayers')->where('matchkey',$request->matchkey)->where('id',$request->playerid)->update(['forduo'=>0]);
                }
                return 1;
            }else{
                return 3;
            }
        } 
        
    }
    public function allmatches()
    {
        return view('matches.all_matches');
    }

    public function allmatches_datatable(Request $request)
    {
        $f_type = request()->get('fantasy_type');
        $columns = array(
            0 => 'id',
            1 => 'start_date',
            2 => 'name',
            3 => 'launch_status',
            4 => 'final_status',
            5 => 'status',
        );
        $data = $request->all();
        $name = $data['name'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query = DB::table('listmatches');
        // search for the series name //
        if (isset($_GET['name'])) {
            if ($name != "") {
                $query = $query->where('name', 'LIKE', '%' . $name . '%');
            }
        }
        if (isset($_GET['status'])) {
            $status = $data['status'];
            if ($status == "launched") {
                $query = $query->where('launch_status', '=', 'launched')->where('status', '!=', 'completed');
            } else if ($status == "complete") {
                $query = $query->where('launch_status', '=', 'launched')->where('status', '=', 'completed');
            } else if ($status == "pending") {
                $query = $query->where('launch_status', '=', 'launched')->where('status', '=', 'completed')->where('final_status', '!=', 'winnerdeclared');
            } else if ($status == "live") {
                $query = $query->where('status', '=', 'started');
            }
        }

        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $totalTitles = $query->where('fantasy_type', $f_type)->count();
        $totalFiltered = $totalTitles;
        $titles = $query->select('id', 'name', 'team1', 'team2', 'status', 'start_date', 'launch_status', 'final_status', 'matchkey')->where('fantasy_type', $f_type)->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        if (!empty($titles)) {
            $data = array();

            if ($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
                $count = $totalFiltered - $start;
            } else {
                $count = $start + 1;
            }

            foreach ($titles as $title) {
                $nestedData['id'] = $count;
                $nestedData['start_date'] = "<span class='text-success font-weight-bold'>" . date('d-m-Y', strtotime($title->start_date)) . "</span><span class='text-primary font-weight-bold'>" . date(' h:i:s', strtotime($title->start_date)) . "</span>";
                $nestedData['name'] = '<span class="text-dark font-weight-bold">' . $title->name . '</span>';

                if ($title->launch_status == 'pending') {
                    $status = 'text-warning';
                } else if ($title->launch_status == 'launched') {
                    $status = 'text-success';
                } else {
                    $status = 'text-dark';
                }

                $nestedData['launch_status'] = '<span class="' . $status . ' font-weight-bold">' . $title->launch_status . '</span>';

                if ($title->final_status == 'pending') {
                    $status = 'text-warning';
                } else if ($title->final_status == 'IsReviewed') {
                    $status = 'text-primary';
                } else if ($title->final_status == 'winnerdeclared') {
                    $status = 'text-success';
                } else {
                    $status = 'text-danger';
                }

                $nestedData['final_status'] = '<span class="' . $status . ' font-weight-bold">' . $title->final_status . '</span>';

                if ($title->status == 'notstarted') {
                    $status = 'text-warning';
                } else if ($title->status == 'started') {
                    $status = 'text-primary';
                } else if ($title->status == 'completed') {
                    $status = 'text-success';
                } else {
                    $status = 'text-danger';
                }

                $nestedData['status'] = '<span class="' . $status . ' font-weight-bold">' . (($title->status != 'notstarted') ? $title->status : 'Not Started') . '</span>';

                $action = '<a href="' . asset("my-admin/editmatch/" . $title->matchkey) . '" class="btn btn-sm btn-primary w-35px h-35px" data-toggle="tooltip" title="Edit Match"><i class="fad fa-pencil"></i></a>';

                if ($title->status != 'completed') {
                    $nestedData['action'] = $action;
                } else {
                    $nestedData['action'] = '';
                }
                $data[] = $nestedData;

                if ($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
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

    public function updatelogo($id, Request $request)
    {
        if ($request->isMethod('post')) {
            $findteam = DB::table('teams')->where('id', $id)->select('team', 'logo')->first();

            if (!empty($findteam)) {
                $input = $request->all();
                unset($input['_token']);
                if ($request->file('image')) {
                    $image = $request->file('image');
                    $extension = $image->getClientOriginalExtension();
                    $ext = array("png", "PNG", "jpg", 'jpeg', 'JPG', "JPEG");
                    if (!in_array($extension, $ext)) {
                        return redirect()->back()->with('danger', 'Only Images are allowed.');
                    }
                    $destinationPath = public_path() . '/images_logo/';
                    $fileName = 'team-' . rand(0000, 9999) . $findteam->team;
                    $imageName = Storage::disk('public_folder')->putFile('images_logo', $input['image'], 'public');
                    $input['image'] = $imageName;
                    if ($input['image'] == '') {
                        return redirect()->back()->with('danger', 'Invalid extension of file you uploaded. You can only upload image!');
                    }
                    if ($findteam->logo != "") {

                        Storage::disk('public_folder')->delete($findteam->logo);
                    }
                    $datateam['logo'] = $imageName;
                    DB::connection('mysql2')->table('teams')->where('id', $id)->update($datateam);
                }

                if ($request->file('image')) {
                    return Redirect::back()->with('success', 'Successfully updated the team logo!');
                } else {
                    return Redirect::back();
                }

            } else {
                return Redirect::back()->with('danger', 'Failed to update!');
            }
        }
    }

    public function launch($matchkey)
    {
        $datastatus['launch_status'] = 'launched';
        $findmatch = DB::table('listmatches')->leftjoin('teams as team1', 'listmatches.team1', '=', 'team1.id')->leftjoin('teams as team2', 'listmatches.team2', '=', 'team2.id')->select('listmatches.*', 'team1.id as team1id', 'team2.id as team2id')->where('matchkey', $matchkey)->first();
        if (!empty($findmatch)) {
            if ($findmatch->series == 0 || $findmatch->series == "") {
                return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'You cannot launch this match. Series is required in this match.');
            }
            $team1 = $findmatch->team1id;
            $team2 = $findmatch->team2id;
            $batsman1 = 0;
            $batsman2 = 0;
            $bowlers1 = 0;
            $bowlers2 = 0;
            $allrounder1 = 0;
            $allrounder2 = 0;
            $wk1 = 0;
            $wk2 = 0;
            $criteria = 1;
            $findallmatchplayers = DB::table('matchplayers')->leftjoin('players', 'matchplayers.playerid', '=', 'players.id')->select('matchplayers.*', 'players.team as playerteam')->where('matchkey', $matchkey)->get();
            if (!empty($findallmatchplayers)) {
                foreach ($findallmatchplayers as $matchplay) {
                    if ($matchplay->playerteam == $team1) {
                        if ($matchplay->role == 'bowler') {
                            $bowlers1++;
                        }
                        if ($matchplay->role == 'batsman') {
                            $batsman1++;
                        }
                        if ($matchplay->role == 'allrounder') {
                            $allrounder1++;
                        }
                        if ($matchplay->role == 'keeper') {
                            $wk1++;
                        }
                        if ($matchplay->role == "") {
                            $criteria = 0;
                            return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'You cannot launch this match because the role of ' . ucwords($matchplay->name) . ' is not defined.');
                        }
                    }
                    if ($matchplay->playerteam == $team2) {
                        if ($matchplay->role == 'bowler') {
                            $bowlers2++;
                        }
                        if ($matchplay->role == 'batsman') {
                            $batsman2++;
                        }
                        if ($matchplay->role == 'allrounder') {
                            $allrounder2++;
                        }
                        if ($matchplay->role == 'keeper') {
                            $wk2++;
                        }
                        if ($matchplay->role == "") {
                            $criteria = 0;
                            return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'You cannot launch this match because the role of ' . ucwords($matchplay->name) . ' is not defined.');
                        }
                    }
                }
                if ($bowlers1 < 3) {
                    $criteria = 0;
                    return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'Minimum 3 bowlers are required in team1 to launch this match');
                } else if ($bowlers2 < 3) {
                    $criteria = 0;
                    return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'Minimum 3 bowlers are required in team2 to launch this match');
                } else if ($batsman1 < 3) {
                    $criteria = 0;
                    return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'Minimum 3 batman are required in team1 to launch this match');
                } else if ($batsman2 < 3) {
                    $criteria = 0;
                    return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'Minimum 3 batman are required in team2 to launch this match');
                } else if ($wk1 < 1) {
                    $criteria = 0;
                    return Redirect()->action('MatchController@launchmatch', $matchkey)->withErrors('danger', 'Minimum 1 wicketkeeper is required in team1 to launch this match');
                } else if ($wk2 < 1) {
                    $criteria = 0;
                    return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'Minimum 1 wicketkeeper is required in team2 to launch this match');
                } else if ($allrounder1 < 1) {
                    $criteria = 0;
                    return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'Minimum 1 all rounder are required in team1 to launch this match');
                } else if ($allrounder2 < 1) {
                    $criteria = 0;
                    return Redirect()->action('MatchController@launchmatch', $matchkey)->with('danger', 'Minimum 1 all rounder are required in team2 to launch this match');
                }
            }

            if ($criteria == 1) {
                DB::connection('mysql2')->table('listmatches')->where('matchkey', $matchkey)->update($datastatus);
                return redirect()->action('ContestController@create_custom_contest')->with('success', 'Successfully launched this match!');
            }
        } else {
            return redirect()->action('MatchController@upcoming_matches')->with('danger', 'Invalid match Provided');
        }
    }
    public function deleteplayer($id, $matchkey)
    {
        $gid = unserialize(base64_decode($id));
        $matchkey = unserialize(base64_decode($matchkey));
        DB::connection('mysql2')->table('matchplayers')->where('id', $gid)->delete();
        return redirect()->back()->with('success', 'Successfully Deleted');
    }

    public function playerroles($id, Request $request)
    {
        $findplayerdetails = DB::table('matchplayers')->where('id', $id)->first();
        if ($request->isMethod('post')) {
            $input = $request->all();

            $data['role'] = $input['role'];
            $data['credit'] = $input['credit'];
            $data['name'] = $input['name'];
            if ($request->file('image')) {
                $image = Storage::disk('public_folder')->putFile('images_logo', $input['image'], 'public');
                $data1['image'] = $image;
                DB::connection('mysql2')->table('players')->where('id', $findplayerdetails->playerid)->update($data1);
            }
            DB::connection('mysql2')->table('matchplayers')->where('id', $id)->update($data);
            if (isset($input['global'])) {
                unset($data['name']);
                $data['player_name'] = $input['name'];
                DB::connection('mysql2')->table('players')->where('id', $findplayerdetails->playerid)->update($data);
            }
        }

        if ($request->file('image')) {
            return Redirect::back()->with('success', 'Successfully updated the team logo!');
        } else {
            return Redirect::back();
        }
    }

    public function upcomingmatch_muldelete(Request $request)
    {
        if ($request->isMethod('post')) {
            $values = $request->input('hg_cart');
            $final = explode(',', $values);
            foreach ($final as $id) {
                $teams = DB::table('listmatches')->where('id', $id)->first();
                if (!empty($teams)) {
                    DB::connection('mysql2')->table('listmatches')->where('id', $id)->delete();
                }
            }
            echo 1;die;
        }
        echo 2;die;
    }

    public function unlaunch($matchkey)
    {
        $satus['launch_status'] = 'pending';
        DB::connection('mysql2')->table('listmatches')->where('matchkey', $matchkey)->update($satus);
        return Redirect::back()->with('success', 'Successfully unlaunch the match!');
    }

    public function importteam($matchkey)
    {

        $findmatch = DB::table('listmatches')->where('matchkey', $matchkey)->first();
        $matchkey = $findmatch->real_matchkey;

        $getdetails = CricketapiController::getmatchdata($matchkey);
        if (!empty($getdetails)) {
            $matchkikey = $getdetails['data']['card']['key'];
            $team1players = $getdetails['data']['card']['teams']['a']['match']['players'];
            $team2players = $getdetails['data']['card']['teams']['b']['match']['players'];
            $team1key = $getdetails['data']['card']['teams']['a']['key'];
            $team2key = $getdetails['data']['card']['teams']['b']['key'];
            // insert team 1//
            if (!empty($team1key)) {
                $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
            }
            if (empty($findteam1)) {
                $mpdata['team_key'] = $team1key;
                $mpdata['team'] = $getdetails['data']['card']['teams']['a']['name'];
                $mpdata['short_name'] = $getdetails['data']['card']['teams']['a']['short_name'] ?? $team1key;
                $team1id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata);
            } else {
                $team1id = $findteam1->id;
            }

            // insert team 2//
            if (!empty($team2key)) {
                $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
            }
            if (empty($findteam2)) {
                $mpdata1['team_key'] = $team2key;
                $mpdata1['team'] = $getdetails['data']['card']['teams']['b']['name'];
                $mpdata1['short_name'] = $getdetails['data']['card']['teams']['b']['short_name'] ?? $team2key;
                $team2id = DB::connection('mysql2')->table('teams')->insertGetId($mpdata1);
            } else {
                $team2id = $findteam2->id;
            }

            $ts = DB::table('listmatches')->where('matchkey', $findmatch->matchkey)->first();
            $data['team1'] = $team1id;
            $data['team2'] = $team2id;
            $data['name'] = $getdetails['data']['card']['teams']['a']['name'] . ' Vs ' . $getdetails['data']['card']['teams']['b']['name'];
            DB::connection('mysql2')->table('listmatches')->where('matchkey', $findmatch->matchkey)->update($data);
            if ($ts->team1 == $team1id && $ts->team2 == $team2id) {
                return redirect()->back()->with('success', 'Successfully updated!');
            } else {
                return redirect()->back()->with('warning', 'Teams not available yet!!');
            }

        }
    }
    public static function GetLocalseason()
    {
        $boarddata = CricketapiController::coverageApi();

        if (!empty($boarddata['data']['boards'])) {
            $boarddatas = $boarddata['data']['boards'];
            foreach ($boarddatas as $bkey) {
                $result = CricketapiController::GetLocalScheduleMatch($bkey['key']);

                if (!empty($result['data']['seasons'])) {
                    $seasons = $result['data']['seasons'];
                    foreach ($seasons as $value) {
                        $recentmatches = CricketapiController::GetLocalRecentSchedule($value['key']);
                        if (!empty($recentmatches['data']['months'])) {
                            $recentmatch = $recentmatches['data']['months'];
                            foreach ($recentmatch as $months) {
                                if (!empty($months)) {
                                    $days = $months['days'];
                                    foreach ($days as $matches) {
                                        $recntmatches = $matches['matches'];
                                        if (!empty($recntmatches)) {
                                            foreach ($recntmatches as $match) {
                                                $matchadata = array();

                                                $team1key = $match['teams']['a']['key'];
                                                $team2key = $match['teams']['b']['key'];
                                                $findteam1 = DB::table('teams')->where('team_key', $team1key)->select('id')->first();
                                                if (empty($findteam1)) {
                                                    $existteam1 = DB::table('teams')->select('id')->where('team', '=', $match['teams']['a']['name'])->where('short_name', '=', $team1key)->first();
                                                    if (empty($existteam1)) {
                                                        $data['team_key'] = $team1key;
                                                        $data['team'] = $match['teams']['a']['name'];
                                                        $data['short_name'] = $team1key;
                                                        $team1id = DB::connection('mysql2')->table('teams')->insertGetId($data);
                                                    } else {
                                                        $team_data1['team_key'] = $team1key;
                                                        DB::connection('mysql2')->table('teams')->where('id', '=', $existteam1->id)->update($team_data1);
                                                        $team1id = $existteam1->id;
                                                    }
                                                } else {
                                                    $team1id = $findteam1->id;
                                                }
                                                $findteam2 = DB::table('teams')->where('team_key', $team2key)->select('id')->first();
                                                if (empty($findteam2)) {
                                                    $existteam2 = DB::table('teams')->select('id')->where('team', '=', $match['teams']['b']['name'])->where('short_name', '=', $team2key)->first();
                                                    if (empty($existteam2)) {
                                                        $data1['team_key'] = $team2key;
                                                        $data1['team'] = $match['teams']['b']['name'];
                                                        $data1['short_name'] = $team2key;
                                                        $team2id = DB::connection('mysql2')->table('teams')->insertGetId($data1);
                                                    } else {
                                                        $team_data2['team_key'] = $team2key;
                                                        DB::connection('mysql2')->table('teams')->where('id', '=', $existteam2->id)->update($team_data2);
                                                        $team2id = $existteam2->id;
                                                    }
                                                } else {
                                                    $team2id = $findteam2->id;
                                                }
                                                $cuurectdate = date('Y-m-d H:i:s', strtotime($match['start_date']['iso']));
                                                $cuurectdate = Carbon::parse($cuurectdate)->addHours(5)->addMinutes(30);
                                                $matchadata['status'] = $match['status'];
                                                $matchadata['name'] = $match['name'];
                                                $matchadata['team1'] = $team1id;
                                                $matchadata['team2'] = $team2id;
                                                $matchadata['real_matchkey'] = $match['key'];
                                                $matchadata['format'] = $match['format'];
                                                $matchadata['launch_status'] = 'pending';
                                                $matchadata['final_status'] = 'pending';
                                                $matchadata['start_date'] = $cuurectdate;
                                                $findmatchexist = DB::table('listmatches')->where('matchkey', $match['key'])->orWhere('real_matchkey', $match['key'])->select('id')->first();

                                                if (empty($findmatchexist)) {
                                                    $mid = DB::connection('mysql2')->table('listmatches')->insertGetId($matchadata);

                                                    $matchadata['matchkey'] = $mid;
                                                    DB::connection('mysql2')->table('listmatches')->where('id', $mid)->update($matchadata);

                                                } else {
                                                    $cuurectdate = date('Y-m-d H:i:s', strtotime($match['start_date']['iso']));
                                                    $cuurectdate = Carbon::parse($cuurectdate)->addHours(5)->addMinutes(30);
                                                    $matchadatad = array();
                                                    $matchadatad['start_date'] = $cuurectdate;
                                                    $matchadatad['real_matchkey'] = $match['key'];
                                                    $matchadatad['team1'] = $team1id;
                                                    $matchadatad['team2'] = $team2id;
                                                    $matchadatad['name'] = $match['name'];

                                                    DB::connection('mysql2')->table('listmatches')->where('id', $findmatchexist->id)->update($matchadatad);

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

        return Redirect::back()->with('success', 'Successfully match data from api!');
    }

    # second innings launch
    public function secondinninglaunch($matchkey)
    {

        //first step//
        // $findAllMatches = DB::table('listmatches')->get();
        // if(!empty($findAllMatches)){
        //     foreach($findAllMatches as $matches){
        //         $data['real_matchkey'] = $matches->matchkey;
        //         DB::connection('mysql2')->table('listmatches')->where('id',$matches->id)->update($data);
        //     }
        // }
        // die;

        // // second step //
        // $findAllMatches = DB::table('listmatches')->where('status','notstarted')->where('launch_status','pending')->get();
        // if(!empty($findAllMatches)){
        //     foreach($findAllMatches as $matches){
        //         $data['real_matchkey'] = $matches->matchkey;
        //         $data['matchkey'] = $matches->id;
        //         DB::connection('mysql2')->table('listmatches')->where('id',$matches->id)->update($data);
        //     }
        // }
        // die;

        $findmatch = DB::table('listmatches')->where('matchkey', $matchkey)->first();
        if (!empty($findmatch)) {
            $matchdata['type'] = $findmatch->type;
            $matchdata['name'] = $findmatch->name . ' - 2nd innings';
            $matchdata['team1'] = $findmatch->team1;
            $matchdata['team2'] = $findmatch->team2;
            //$matchdata['matchkey']=$findmatch->matchkey.$findmatch->real_matchkey;
            $matchdata['real_matchkey'] = $findmatch->real_matchkey;
            $matchdata['series'] = $findmatch->series;
            $matchdata['start_date'] = date('Y-m-d H:i:s', strtotime(' +1 hours +50 minutes', strtotime($findmatch->start_date)));
            $matchdata['status'] = $findmatch->status;
            $matchdata['squadstatus'] = $findmatch->squadstatus;
            $matchdata['format'] = $findmatch->format;
            $matchdata['launch_status'] = 'pending';
            $matchdata['final_status'] = 'pending';
            $matchdata['pdfstatus'] = 0;
            $matchdata['playing11_status'] = 0;
            $matchdata['status_overview'] = '';
            $matchdata['second_inning_status'] = 2;

            $findmatchs = DB::table('listmatches')
                ->where('real_matchkey', $matchdata['real_matchkey'])
                ->where('second_inning_status', 2)
                ->first();

            if (empty($findmatchs)) {
                $mid = DB::connection('mysql2')->table('listmatches')->insertGetId($matchdata);
                $mdata['matchkey'] = $mid;
                DB::connection('mysql2')->table('listmatches')->where('id', $mid)->update($mdata);
                $data['second_inning_status'] = 1;
                DB::connection('mysql2')->table('listmatches')->where('id', $findmatch->id)->update($data);
            }
            return redirect()->back()->with('success', 'Successfully launched second inning match!');
        }
    }
}
