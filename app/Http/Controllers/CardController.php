<?php

namespace App\Http\Controllers;

use DB;
use Session;
use bcrypt;
use Config;
use Redirect;
use File;
use Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\EntityCricketapiController;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Storage;


class CardController extends Controller
{
	public function __construct()
	{
	}

	public function importcarddatafromapi()
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
	}

	public function letsImport($getlist)
	{
		if (!empty($getlist)) {
			foreach ($getlist as $getli) {
				// $cuurectdate = date('Y-m-d H:i:s', strtotime($getli['start_date']['iso']));
				$cuurectdate = date('Y-m-d H:i:s', strtotime($getli['start_date']));

				$getdetails = $findsquaddetials = CricketapiController::getmatchdata($getli['matchkey']);
				if (isset($findsquaddetials['data']['card']['season']['key']) && !empty($findsquaddetials['data']['card']['season']['key'])) {
					$checkseries = DB::connection('mysql')->table('cardseries')->where('series_key', $findsquaddetials['data']['card']['season']['key'])->first();

					if (empty($checkseries)) {
						$seriesdata['series_key'] = $findsquaddetials['data']['card']['season']['key'];
						$seriesdata['name'] = $findsquaddetials['data']['card']['season']['name'];
						DB::connection('mysql2')->table('cardseries')->insert($seriesdata);
					} else {
						$seriesdata['series_key'] = $findsquaddetials['data']['card']['season']['key'];
						$seriesdata['name'] = $findsquaddetials['data']['card']['season']['name'];
						DB::connection('mysql2')->table('cardseries')->where('series_key', $findsquaddetials['data']['card']['season']['key'])->update($seriesdata);
					}

					$team1key = $getli['teams']['a']['key'] ?? $getli['team1key'];
					$team2key = $getli['teams']['b']['key'] ?? $getli['team2key'];

					if (!empty($team1key) && !empty($team2key)) {
						$team1id = $this->checkTeam1($team1key, $findsquaddetials, $getli);
						$team2id = $this->checkTeam2($team2key, $findsquaddetials, $getli);
					}
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
							$findteam1 = DB::connection('mysql')->table('cardteams')->where('team_key', $team1key)->select('id')->first();
							if (empty($findteam1)) {
								$mpdata['team_key'] = $team1key;
								$mpdata['team'] = $getdetails['data']['card']['teams']['a']['name'];
								$mpdata['short_name'] = $getdetails['data']['card']['teams']['a']['short_name'] ?? $team1key;
								$team1id = DB::connection('mysql2')->table('cardteams')->insertGetId($mpdata);
							} else {
								$team1id = $findteam1->id;
							}

							// insert team 2//
							$findteam2 = DB::connection('mysql')->table('cardteams')->where('team_key', $team2key)->select('id')->first();
							if (empty($findteam2)) {
								$mpdata1['team_key'] = $team2key;
								$mpdata1['team'] = $getdetails['data']['card']['teams']['b']['name'];
								$mpdata1['short_name'] = $getdetails['data']['card']['teams']['b']['short_name'] ?? $team2key;
								$team2id = DB::connection('mysql2')->table('cardteams')->insertGetId($mpdata1);
							} else {
								$team2id = $findteam2->id;
							}
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
								$findmatchexist = DB::connection('mysql')->table('cardteams')->where('team_key', $teamkey)->select('id')->first();
								if (!empty($findmatchexist)) {
									$findplayerexist = DB::connection('mysql')->table('cardplayers')->where('players_key', $players1)->where('team', $findmatchexist->id)->first();
									$data['player_name'] = $getdetails['data']['card']['players'][$players1]['fullname'];
									$data['players_key'] = $playerkey;
									// $data['credit']=($pkey!='' || $pkey==0)?$playerscredit['data']['fantasy_points'][$pkey]['credit_value']:9;
									$findsquaddetials = CricketapiController::getmatchdata($playerkey);
									$data['credit'] = 9;
									if (empty($findplayerexist)) {
										$data['team'] = $findmatchexist->id;
										if ($getdetails['data']['card']['players'][$players1]['seasonal_role'] == "") {
											$data['role'] = 'allrounder';
										} else {
											$data['role'] = $getdetails['data']['card']['players'][$players1]['seasonal_role'];
										}
										$playerid = DB::connection('mysql2')->table('cardplayers')->insertGetId($data);
										// $credit=($pkey!='' || $pkey==0)?$playerscredit['data']['fantasy_points'][$pkey]['credit_value']:9;
										$credit = 9;
									} else {
										$playerid = $findplayerexist->id;
										// $credit = ($pkey!='' || $pkey==0)?$playerscredit['data']['fantasy_points'][$pkey]['credit_value']:$findplayerexist->credit;
										$credit = $findplayerexist->credit;
										$data['role'] = $findplayerexist->role;
										$getdetails['data']['card']['players'][$players1]['seasonal_role'] = $findplayerexist->role;
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
								$findmatchexist = DB::connection('mysql')->table('cardteams')->where('team_key', $team2key)->select('id')->first();
								if (!empty($findmatchexist)) {
									$findplayer2exist = DB::connection('mysql')->table('cardplayers')->where('players_key', $players2)->where('team', $findmatchexist->id)->first();
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
										$playerid = DB::connection('mysql2')->table('cardplayers')->insertGetId($data);
										// $credit=($pkey1!='' || $pkey1==0)?$playerscredit['data']['fantasy_points'][$pkey1]['credit_value']:9;
										$credit = 9;
									} else {
										$playerid = $findplayer2exist->id;
										// $credit = ($pkey1!='' || $pkey1==0)?$playerscredit['data']['fantasy_points'][$pkey1]['credit_value']:$findplayer2exist->credit;
										$credit = $findplayer2exist->credit;
										$getdetails['data']['card']['players'][$players2]['seasonal_role'] = $findplayer2exist->role;
										$data['role'] = $findplayer2exist->role;
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public function checkTeam1($team1key, $findsquaddetials, $getli)
	{
		$findteam1 = DB::connection('mysql')->table('cardteams')->where('team_key', $team1key)->select('id', 'short_name')->first();
		if (empty($findteam1)) {
			$existteam1 = DB::connection('mysql')->table('cardteams')->select('id')->where('team', '=', $getli['teams']['a']['name'] ?? $getli['team1name'])->where('short_name', '=', $team1key)->first();
			if (empty($existteam1)) {
				$data['team_key'] = $team1key;
				$data['series_key'] = $findsquaddetials['data']['card']['season']['key'];
				$data['team'] = $getli['teams']['a']['name'] ?? $getli['team1name'];
				$data['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
				$team1id = DB::connection('mysql2')->table('cardteams')->insertGetId($data);
				return $team1id;
			} else {
				$team_data1['team_key'] = $team1key;
				$team_data1['series_key'] = $findsquaddetials['data']['card']['season']['key'];
				if (isset($existteam1->short_name)) {
					if ($existteam1->short_name == $team1key) {
						$team_data1['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
					}
				}

				DB::connection('mysql2')->table('cardteams')->where('id', '=', $existteam1->id)->update($team_data1);
				$team1id = $existteam1->id;
				return $team1id;
			}
		} else {
			$team1id = $findteam1->id;
			$team_data1['series_key'] = $findsquaddetials['data']['card']['season']['key'];
			if ($findteam1->short_name == $team1key) {
				$team_data1['short_name'] = $findsquaddetials['data']['card']['teams']['a']['short_name'] ?? $team1key;
				DB::connection('mysql2')->table('cardteams')->where('id', '=', $findteam1->id)->update($team_data1);
			}
			return $team1id;
		}
	}
	public function checkTeam2($team2key, $findsquaddetials, $getli)
	{
		$findteam2 = DB::connection('mysql')->table('cardteams')->where('team_key', $team2key)->select('id', 'short_name')->first();
		if (empty($findteam2)) {
			$existteam2 = DB::connection('mysql')->table('cardteams')->select('id')->where('team', '=', $getli['teams']['b']['name'] ?? $getli['team2name'])->where('short_name', '=', $team2key)->first();
			if (empty($existteam2)) {
				$data1['team_key'] = $team2key;
				$data1['team'] = $getli['teams']['b']['name'] ?? $getli['team2name'];
				$data1['series_key'] = $findsquaddetials['data']['card']['season']['key'];
				$data1['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
				$team2id = DB::connection('mysql2')->table('cardteams')->insertGetId($data1);
				return $team2id;
			} else {
				$team_data2['team_key'] = $team2key;
				$team_data2['series_key'] = $findsquaddetials['data']['card']['season']['key'];
				if (isset($existteam2->short_name)) {
					if ($existteam2->short_name == $team2key) {
						$team_data2['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
					}
				}
				DB::connection('mysql2')->table('cardteams')->where('id', '=', $existteam2->id)->update($team_data2);
				$team2id = $existteam2->id;
				return $team2id;
			}
		} else {
			$team2id = $findteam2->id;
			$team_data2['series_key'] = $findsquaddetials['data']['card']['season']['key'];
			if ($findteam2->short_name == $team2key) {
				$team_data2['short_name'] = $findsquaddetials['data']['card']['teams']['b']['short_name'] ?? $team2key;
				DB::connection('mysql2')->table('cardteams')->where('id', '=', $findteam2->id)->update($team_data2);
			}
			return $team2id;
		}
	}


	public function index()
	{
		return view('card.view_series');
	}

	// for the datatable of all the series //
	public function series_carddatatable(Request $request)
	{
		$f_type =   'Cricket';
		date_default_timezone_set('Asia/Kolkata');
		$columns = array(
			0 => 'id',
			1 => 'name',
			2 => 'status',
			3 => 'created_at',
			4 => 'updated_at',
		);
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');
		$query = DB::connection('mysql')->table('cardseries');
		// search for the series name //

		if (isset($_GET['name'])) {
			$name = $_GET['name'];
			if ($name != "") {
				$query =  $query->where('name', 'LIKE', '%' . $name . '%');
			}
		}

		if (
			$request->input('order.0.column') == 0 and $request->input('order.0.dir') == 'asc'
		) {
			$query = $query
				// ->whereIn('is_paid_member', ["request", "yes", "no"])
				// ->orderBy(DB::raw('FIELD(is_paid_member, "request", "yes", "no")'))
				->orderBy('created_at', 'desc');
		} else {
			$query = $query->orderBy($order, $dir);
		}

		$count = $query->count();
		$titles = $query->select('id', 'name', 'status')
			->offset($start)
			->limit($limit)
			->get();

		$totalTitles = $count;
		$totalFiltered = $totalTitles;

		if ($request->input('order.0.column') == '0' && $request->input('order.0.dir') == 'desc') {
			$count = $totalTitles - $start;
		} else {
			$count = $start + 1;
		}

		if (!empty($titles)) {
			$data = array();


			foreach ($titles as $title) {
				$edit = action('CardController@edit', base64_encode(serialize($title->id)));

				$nestedData['id'] = $count;
				$nestedData['action'] = '<div class="dropdown">
                <button class="btn btn-sm btn-primary btn-active-pink dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button" aria-expanded="true">
                    Action <i class="dropdown-caret"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item waves-light waves-effect" href="' . $edit . '">Edit</a></li>
                </ul>
            </div>';
				$nestedData['name'] = $title->name;

				$nestedData['status'] = '<span class="font-weight-bold ">' . $title->status . '</span>';

				$data[] = $nestedData;

				if ($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
					$count--;
				} else {
					$count++;
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
	//to edit the series //
	public function edit(Request $request, $id)
	{
		date_default_timezone_set('Asia/Kolkata');
		$series_id = unserialize(base64_decode($id));

		if ($request->isMethod('post')) {
			$current = date('Y-m-d');
			$finds = DB::connection('mysql')->table('cardseries')->where('id', '!=', $series_id)->where('name', $request->seriesname)->first();
			if (!empty($finds)) {
				return redirect()->back()->with('danger', 'This series name already exist!');
			}
			$data['name'] = $request->seriesname;
			$data['status'] = $request->status;


			$data['updated_at'] = date('Y-m-d h:i:s');
			DB::connection('mysql2')->table('cardseries')->where('id', '=', $series_id)->update($data);
			return redirect()->back()->with('success', 'Series Edited Successfully');
		} else {
			$data = DB::connection('mysql')->table('cardseries')->where('id', '=', $series_id)->first();
			return view('card.edit_series', compact('data'));
		}
	}

	// to view all the teams //
	public function view_team()
	{
		return view('card.view');
	}
	// for the datatables of all the teams //
	public function view_team_datatable(Request $request)
	{
		$columns = array(
			0 => 'cardteams.id',
			1 => 'team',
			2 => 'short_name',
			3 => 'short_name',
			4 => 'created_at',
			5 => 'updated_at',
		);


		$totalTitles = DB::connection('mysql')->table('cardteams')->join('cardseries', 'cardseries.series_key', '=', 'cardteams.series_key')->where('cardseries.status', 'yes')->count();
		$totalFiltered = $totalTitles;

		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');
		$query = DB::connection('mysql')->table('cardteams')->join('cardseries', 'cardseries.series_key', '=', 'cardteams.series_key')->where('cardseries.status', 'yes');
		if (request()->has('name')) {
			$name = request('name');
			if ($name != "") {
				$query = $query->where('team', 'LIKE', '%' . $name . '%');
			}
		}
		$count = $query->count();
		$titles =  $query->select('cardteams.*')->offset($start)
			->limit($limit)
			->orderBy($order, $dir)
			->get();

		$totalTitles = $count;
		$totalFiltered = $totalTitles;

		if ($request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
			$count = $totalFiltered - $start;
		} else {
			$count = $start + 1;
		}
		if (!empty($titles)) {
			$data = array();
			// $count = 1;
			foreach ($titles as $title) {

				$imagelogo = asset('/' . Helpers::settings()->team_image ?? '');
				$default_img = "this.src='" . $imagelogo . "'";

				if ($title->logo == '') {
					$img = '<img src="' . $imagelogo . '" class="w-40px view_team_table_images h-40px rounded-pill">';
				} else {

					$imagelogo = asset($title->logo);
					$img = '<img src="' . $imagelogo . '" class="w-40px view_team_table_images h-40px rounded-pill" onerror="' . $default_img . '">';
				}
				$c = action('CardController@edit_team', base64_encode(serialize($title->id)));
				$d = action('CardController@importplayerdata', base64_encode(serialize($title->id)));
				$action = '<a href="' . $c . '" class="btn btn-sm btn-orange w-35px h-35px text-uppercase text-nowrap" data-toggle="tooltip" title="Edit"><i class="fad fa-pencil"></i></a><a href="' . $d . '" class="btn btn-sm btn-orange w-35px h-35px text-uppercase text-nowrap" style="margin-left:3px;" data-toggle="tooltip" title="Update Player Data"><i class="fad fa-download"></i></a>';
				//$nestedData['s_no'] = $Data1;
				$nestedData['id'] = $count;
				$nestedData['team'] = $title->team;
				$nestedData['short_name'] = $title->short_name;
				$nestedData['logo'] = $img;
				$nestedData['action'] = $action;

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
	// to edit the teams//
	public function edit_team(Request $request, $id)
	{
		$getid = unserialize(base64_decode($id));
		$data = DB::connection('mysql')->table('cardteams')->where('id', $getid)->first();
		if ($request->isMethod('post')) {
			$input = $request->all();

			if (!empty($input['logo'])) {
				$extension = $input['logo']->getClientOriginalExtension();
				$ext = array("png", "PNG", "jpg", 'jpeg', 'JPG', "JPEG");
				if (!in_array($extension, $ext)) {
					return redirect()->back()->with('danger', 'Only Images are allowed.');
				}
				// dd($ext);
				$hii =  Storage::disk('public_folder')->putFile('images_logo', $input['logo'], 'public');
				$dat['logo'] = $hii;

				$oldlogo = DB::connection('mysql')->table('cardteams')->where('id', $input['id'])->select('logo')->first();
				if (!empty($oldlogo->logo)) {
					$filename = $oldlogo->logo;
					Storage::disk('public_folder')->delete($filename);
				}
			}
			$dat['team'] = $input['team'];
			$dat['short_name'] = $input['short_name'];
			$dat['color'] = $input['color'];
			DB::connection('mysql2')->table('cardteams')->where('id', $input['id'])->update($dat);
			return redirect()->action('CardController@edit_team', $id)->with('success', 'Team edit successfully');
		}
		return view('card.edit', compact('data'));
	}

	public function view_player()
	{
		$findallteams = DB::connection('mysql')->table('cardteams')->join('cardseries', 'cardseries.series_key', '=', 'cardteams.series_key')->where('cardseries.status', 'yes')->select('cardteams.*')->orderBy('team', 'ASC')->get();
		return view('card.view_player', compact('findallteams'));
	}

	public function get_cardteams()
	{

		$findallteams = DB::connection('mysql')->table('cardteams')->join('cardseries', 'cardseries.series_key', '=', 'cardteams.series_key')->where('cardseries.status', 'yes')->select('cardteams.*');
		$findallteams = $findallteams->get();

		return $findallteams;
	}

	// for the datatable of the players table //
	public function view_player_datatable(Request $request)
	{
		date_default_timezone_set('Asia/Kolkata');
		$columns = array(
			0 => 'cardplayers.id',
			1 => 'player_name',
			2 => 'players_key',
			3 => 'role',
			4 => 'credit',
			5 => 'created_at',
			6 => 'updated_at',
		);
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');
		$query = DB::connection('mysql')->table('cardplayers')->join('cardteams', 'cardteams.id', '=', 'cardplayers.team')->join('cardseries', 'cardseries.series_key', '=', 'cardteams.series_key')->where('cardseries.status', 'yes');
		if (request()->has('playername')) {
			$name = request('playername');
			if ($name != "") {
				$query = $query->where('cardplayers.player_name', 'LIKE', '%' . $name . '%');
			}
		}
		if (request()->has('team')) {
			$team = request('team');
			if ($team != "") {
				$query = $query->where('cardplayers.team', '=', $team);
			}
		}
		if (request()->has('role')) {
			$role = request('role');
			if ($role != "") {
				$query = $query->where('cardplayers.role', '=', $role);
			}
		}

		$totalTitles = $query->count();
		$totalFiltered = $totalTitles;
		$titles =  $query->select('cardplayers.*')
			->distinct('cardplayers.team')->offset($start)
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
				$role = "'.$title->role.'";

				$imagelogo = asset('/' . Helpers::settings()->player_image ?? '');
				$default_img = "this.src='" . $imagelogo . "'";

				if ($title->image == '') {
					$imagelogo = asset('/' . Helpers::settings()->player_image ?? '');
					$img = '<img src="' . $imagelogo . '"  class="w-40px view_team_table_images h-40px rounded-pill">';
				} else {
					$imagelogo = asset($title->image);
					$img = '<img src="' . $imagelogo . '"  class="w-40px view_team_table_images h-40px rounded-pill" onerror="' . $default_img . '">';
				}
				$a = asset('players/1536493694.jpg');

				$c = action('CardController@edit_player', base64_encode(serialize($title->id)));

				$cd = '<span id="credittd' . $title->id . '">' . $title->credit . '</span>';

				$action = '<a href="' . $c . '" class="btn btn-sm w-35px h-35px mr-1 mb-1 btn-orange" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil"></i></a>
                 <a onclick="updateplayer(' . $title->id . ',' . $role . ',' . $title->credit . ')" class="btn btn-sm w-35px h-35px mr-1 mb-1 btn-primary" data-toggle="tooltip" title="Update credit" id="updateplayer' . $title->id . '"><i class="fas fa-sync-alt"></i></a>
            <a onclick="saveplayer(' . $title->id . ')" class="btn btn-sm w-35px h-35px mr-1 mb-1 btn-success" id="saveplayer' . $title->id . '" style="display:none;" data-toggle="tooltip" title="Save credit"><i class="fad fa-save"></i></a>';


				$nestedData['id'] = $count;
				$nestedData['player_name'] = $title->player_name;
				$nestedData['players_key'] = $title->players_key;
				$nestedData['role'] = $title->role;
				$nestedData['credit'] = $cd;
				$nestedData['image'] = $img;

				$nestedData['action'] = $action;

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
	// to edit the details of players//
	public function edit_player(Request $request, $gid)
	{
		date_default_timezone_set('Asia/Kolkata');
		$id = unserialize(base64_decode($gid));

		$player = DB::connection('mysql')->table('cardplayers')->where('id', $id)->first();
		if ($request->isMethod('post')) {
			$input = request()->all();

			unset($input['_token']);
			$playerd['country'] = $input['country'];
			$playerd['credit'] = $input['credit'];
			$playerd['role'] = $input['role'];
			$playerd['player_name'] = $input['player_name'];
			$playerd['battingstyle'] = $input['battingstyle'];
			$playerd['bowlingstyle'] = $input['bowlingstyle'];
			$playerd['dob'] = date('Y-m-d', strtotime($input['dob']));
			$olddob = date('Y-m-d', strtotime('-15 year', strtotime(date('Y-m-d'))));
			if ($playerd['dob'] >= $olddob) {
				return redirect()->back()->with('danger', 'Invalid DOB for player and player should be atleast 15 years old.');
			}
			if ($request->file('image')) {
				$image = $request->file('image');
				$extension = $image->getClientOriginalExtension();
				$ext = array("jpg", "jpeg", "png", 'JPG', 'JPEG', 'PNG');
				if (!in_array($extension, $ext)) {
					return redirect()->back()->with('danger', 'Invalid extension of file you uploaded. You can only upload image.');
				}
				$hii =  Storage::disk('public_folder')->putFile('players', $input['image'], 'public');
				$input['image']  = $hii;
				// delete old image
				$oldimage = DB::connection('mysql')->table('cardplayers')->where('id', $id)->first();
				if (!empty($oldimage)) {
					$filename = $oldimage->image;
					Storage::disk('public_folder')->delete($filename);
					$playerd['image'] = $input['image'];
				}
			} else {
			}
			$findallplay = DB::connection('mysql2')->table('cardplayers')->where('id', $id)->update($playerd);
			return Redirect::back()->with('success', 'Player updated!');
		} else {
			return view('card.edit_player', compact('player'));
		}
	}

	public function savecardplayerroles(Request $request)
	{
		if ($request->isMethod('post')) {
			$input = request()->all();
			$data['credit'] = $input['credit'];
			$findplayerkey = DB::connection('mysql2')->table('cardplayers')->where('id', $input['id'])->update($data);
			echo 1;
			die;
		}
	}

	public function importplayerdata(Request $request, $id)
	{
		date_default_timezone_set('Asia/Kolkata');
		$getid = unserialize(base64_decode($id));

		$teamkey = DB::connection('mysql')->table('cardteams')->where('id',$getid)->first();
		$getallplayers = DB::connection('mysql')->table('cardplayers')->where('team',$getid)->get();
		foreach($getallplayers as $singleplayer){
			// dd($singleplayer);
			$leaugeplayerdata = CricketapiController::leaugeplayerdata($singleplayer->players_key);
			if(!empty($leaugeplayerdata['data']) && isset($leaugeplayerdata['data']['player']['stats']['t20'])){
                $playerdata['matches'] = $leaugeplayerdata['data']['player']['stats']['t20']['batting']['matches'];
                $playerdata['notouts'] = $leaugeplayerdata['data']['player']['stats']['t20']['batting']['not_outs'];
                $playerdata['runs'] = $leaugeplayerdata['data']['player']['stats']['t20']['batting']['runs'];
                $playerdata['highscore'] = $leaugeplayerdata['data']['player']['stats']['t20']['batting']['high_score'];
				$playerdata['average'] = number_format($leaugeplayerdata['data']['player']['stats']['t20']['batting']['average'], 2, '.', '');
                $playerdata['strikerate_batting'] = number_format($leaugeplayerdata['data']['player']['stats']['t20']['batting']['strike_rate'], 2, '.', '');
                $playerdata['fifty'] = $leaugeplayerdata['data']['player']['stats']['t20']['batting']['fifties'];
                $playerdata['hundred'] = $leaugeplayerdata['data']['player']['stats']['t20']['batting']['hundreds'];
                $playerdata['fours'] = $leaugeplayerdata['data']['player']['stats']['t20']['batting']['fours'];
                $playerdata['sixes'] = $leaugeplayerdata['data']['player']['stats']['t20']['batting']['sixes'];
                $playerdata['wickets'] = $leaugeplayerdata['data']['player']['stats']['t20']['bowling']['wickets'];
				if(isset($leaugeplayerdata['data']['player']['stats']['t20']['bowling']['best_innings_bowling'])){
					$playerdata['bestfigures'] = $leaugeplayerdata['data']['player']['stats']['t20']['bowling']['best_innings_bowling'];
				}else{
					$playerdata['bestfigures'] = 0;
				}
				$playerdata['economy'] = number_format($leaugeplayerdata['data']['player']['stats']['t20']['bowling']['economy'],2, '.', '');
				$playerdata['strikerate_bowling'] = number_format($leaugeplayerdata['data']['player']['stats']['t20']['bowling']['strike_rate'], 2, '.', '');
                $playerdata['fourwicket'] = $leaugeplayerdata['data']['player']['stats']['t20']['bowling']['four_wickets'];
                $playerdata['fivewicket'] = $leaugeplayerdata['data']['player']['stats']['t20']['bowling']['five_wickets'];
                $playerdata['catches'] = $leaugeplayerdata['data']['player']['stats']['t20']['fielding']['catches'];
                $playerdata['stumping'] = $leaugeplayerdata['data']['player']['stats']['t20']['fielding']['stumpings'];

				// dump($singleplayer);
				// dd($playerdata);
				DB::connection('mysql2')->table('cardplayers')->where('id',$singleplayer->id)->where('players_key',$singleplayer->players_key)->update($playerdata);

			}
		}
		return redirect()->back()->with('success', 'Player Data Successfully Updated');
	}

	public function view_contestresult()
	{
		return view('card.viewcontestresult');
	}

	// for the datatable of the players table //
	public function view_contestresult_datatable(Request $request)
	{
		date_default_timezone_set('Asia/Kolkata');
		$columns = array(
			0 => 'id',
			1 => 'challengeid',
			2 => 'entryfee',
			3 => 'win_amount',
			4 => 'bonus_percentage',
			5 => 'status',
			6 => 'user_id1',
			7 => 'user_id2',
			8 => 'team1id',
			9 => 'team2id',
			10 => 'created_at',
			11 => 'updated_at',
		);
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');
		$query = DB::connection('mysql')->table('cardchallenges');

		$totalTitles = $query->count();
		$totalFiltered = $totalTitles;
		$titles =  $query->select('cardchallenges.*')
			->offset($start)
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
				
				$userdetail1 = DB::connection('mysql')->table('registerusers')->where('id', $title->user_id1)->first();
            	$userdetail2 = DB::connection('mysql')->table('registerusers')->where('id', $title->user_id2)->first();
            	$cardteam1 = DB::connection('mysql')->table('cardteams')->where('id', $title->team1id)->first();
            	$cardteam2 = DB::connection('mysql')->table('cardteams')->where('id', $title->team2id)->first();
				$c = action('CardController@view_challengeresult', [$title->id]);
				$action = '<a href="' . $c . '" class="btn btn-sm w-35px h-35px mr-1 mb-1 btn-orange" data-toggle="tooltip" title="View Result"><i class="fas fa-eye"></i></a>';
				$nestedData['id'] = $count;
				$nestedData['challengeid'] = $title->id;
				$nestedData['entryfee'] = $title->entryfee;
				$nestedData['win_amount'] = $title->win_amount;
				$nestedData['bonus_percentage'] = $title->bonus_percentage;
				$nestedData['status'] = $title->status;
				$nestedData['user_id1'] = $userdetail1->team;
				$nestedData['team1id'] = $cardteam1->team;
				$winner = DB::connection('mysql')->table('cardfinalresults')->join('registerusers','registerusers.id','cardfinalresults.userid')->where('cardfinalresults.challengeid', $title->id)->first();
				if(!empty($winner)){
					$nestedData['winner'] = $winner->team;
				}else if($title->status=='canceled'){
					$nestedData['winner'] = 'Refunded';
				}
				if(!empty($userdetail2)){
					$nestedData['user_id2'] = $userdetail2->team;
					$nestedData['team2id'] = $cardteam2->team;
				}else{
					$nestedData['user_id2'] = 'Not AVailable';
					$nestedData['team2id'] = 'Not AVailable';
				}

				$nestedData['action'] = $action;

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

	public function view_challengeresult(Request $request, $id)
	{
		date_default_timezone_set('Asia/Kolkata');

		$gamedata = DB::connection('mysql')->table('usercardteam')->where('challengeid', $id)->get();
		return view('card.resultdata', compact('gamedata'));
	}

}
