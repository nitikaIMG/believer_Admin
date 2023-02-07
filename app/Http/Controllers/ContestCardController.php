<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Redirect;
use Session;

class ContestCardController extends Controller
{
    
/* ------------Card Contest Controller----------------- */

    public function create_card(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $rules = array(
                'entryfee' => 'required',
                'win_amount' => 'required',
            );

            $input = $request->input();
            $data['type'] = 'card';
            $findchallenge = DB::connection('mysql')->table('cardcontest')->where('entryfee', $input['entryfee'])->where('win_amount', $input['win_amount'])->first();
            if (!empty($findchallenge)) {
                return Redirect::back()->with('danger', 'This contest is already exist with the same winning amount, entry fees and maximum number of users.')->withInput(request()->except('password'));
            }
            if (isset($input['bonus_percentage'])) {
                if ($input['bonus_percentage'] == 0) {
                    return redirect()->back()->with('danger', 'Value of bonus percentage not equal to 0...');
                }
            }

            unset($input['_token']);
            if (isset($input['is_bonus'])) {
                $data['is_bonus'] = 1;
                $data['bonus_percentage'] = $input['bonus_percentage'];
            }
            
            $data['entryfee'] = $input['entryfee'];
            $data['offerentryfee'] = $input['offerentryfee'];
            $data['win_amount'] = $input['win_amount'];
            $count = 0;
            $count++;
            
            $getid = DB::connection('mysql2')->table('cardcontest')->insertGetId($data);

            return redirect()->back()->with('success', 'Contest Created Successfully!');
        } else {
            return view('contest.create_card_contest');
        }
    }

    public function card_index()
    {
        return view('contest.view_all_card_contest');
    }

    public function card_index_datatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'entryfee',
            3 => 'win_amount',
            4 => 'maximum_user',
            5 => 'is_bonus',
        );

        if(!empty($f_type)){
            $totalTitles = DB::connection('mysql')->table('cardcontest')->where('fantasy_type', $f_type)->count();
        }else{
            $totalTitles = DB::connection('mysql')->table('cardcontest')->count();
        }
        
        $totalFiltered = $totalTitles;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $titles = DB::connection('mysql')->table('cardcontest')->select('cardcontest.*')->offset($start)
                // ->where('challenges.fantasy_type', $f_type)
                ->limit($limit)->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $titles = DB::connection('mysql')->table('cardcontest')->where('entryfee', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = DB::connection('mysql')->table('cardcontest')->Where('entryfee', 'LIKE', "%{$search}%")
                ->count();
        }
        if (!empty($titles)) {
            $data = array();

            if ($request->input('order.0.column') == '1' and $request->input('order.0.dir') == 'desc') {
                $count = $totalFiltered - $start;
            } else {
                $count = $start + 1;
            }

            foreach ($titles as $title) {
                // $Data11 = '<input type="checkbox" name="checkCat" class="checkbox" id="checkh" value="'.$title->id.'">';
                $Data11 = '<div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input checkbox" name="checkCat" id="check' . $title->id . '" value="' . $title->id . '">
                <label class="custom-control-label" for="check' . $title->id . '"></label></div>';
                if ($title->multi_entry == '1') {
                    $resultdata = '<i class="fas fa-check text-success"></i>';
                } else {
                    $resultdata = '<i class="fas fa-times text-danger"></i>';
                }
                if ($title->is_running == '1') {
                    $resultdata1 = '<i class="fas fa-check text-success"></i>';
                } else {
                    $resultdata1 = '<i class="fas fa-times text-danger"></i>';
                }
                if ($title->confirmed_challenge == '1') {
                    $resultdata2 = '<i class="fas fa-check text-success"></i>';
                } else {
                    $resultdata2 = '<i class="fas fa-times text-danger"></i>';
                }
                if ($title->is_bonus == '1') {
                    $resultdata3 = '<i class="fas fa-check text-success"></i>';
                } else {
                    $resultdata3 = '<i class="fas fa-times text-danger"></i>';
                }
                $c = action('ContestCardController@editcardcontest', base64_encode(serialize($title->id)));
                $b = action('ContestCardController@delete_card_contest', base64_encode(serialize($title->id)));

                $onclick = "delete_sweet_alert('" . $b . "', 'Are you sure you want to delete this data?')";

                $action = '<div class="btn-group dropdown">
                <button class="btn btn-primary text-uppercase rounded-pill btn-sm btn-active-pink dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button" aria-expanded="true" style="padding:5px 11px">
                    Action <i class="dropdown-caret"></i>
                </button>
                <ul class="dropdown-menu" style="opacity: 1;">
                    <li><a class="dropdown-item waves-light waves-effect" href="' . $c . '">Edit</a></li>
                    <li> <a class="dropdown-item waves-light waves-effect" onclick="' . $onclick . '">Delete</a></li>
                </ul>
            </div>';
                $nestedData['s_no'] = $Data11;
                $nestedData['id'] = $count;
                $nestedData['entryfee'] = '₹ ' . $title->entryfee;
                $nestedData['offerentryfee'] = '₹ ' . $title->offerentryfee;
                $nestedData['win_amount'] = '₹ ' . $title->win_amount;
                $nestedData['maximum_user'] = $title->maximum_user;
                $nestedData['is_bonus'] = $resultdata3.'('.$title->bonus_percentage.'%)';
                $nestedData['action'] = $action;
                $data[] = $nestedData;
                if ($request->input('order.0.column') == '1' and $request->input('order.0.dir') == 'desc') {
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

    public function editcardcontest($id, Request $request)
    {
        $id = unserialize(base64_decode($id));
        $challenge = DB::connection('mysql')->table('cardcontest')->where('id', $id)->first();
        if ($request->isMethod('post')) {
            $rules = array(
                'entryfee' => 'required',
                'win_amount' => 'required',
            );
            $input = request()->all();
            unset($input['_token']);
            

            if (!isset($input['bonus_percentage'])) {
                $input['bonus_percentage'] = 0;
                $input['is_bonus'] = 0;
            }
            if (!empty($input['is_bonus'])) {
                $data['is_bonus'] = 1;
                $data['bonus_percentage'] = $input['bonus_percentage'];
            } else {
                $data['is_bonus'] = 0;
                $data['bonus_percentage'] = 0;
            }
            
            $data['entryfee'] = $input['entryfee'];
            $data['offerentryfee'] = $input['offerentryfee'];
            $data['win_amount'] = $input['win_amount'];
            //  dd($data);
            $getid = DB::connection('mysql2')->table('cardcontest')->where('id', $id)->update($data);

            return Redirect::back()->with('success', 'Successfully updated contest!');
        }

        if (!empty($challenge)) {
            return view('contest.editcardcontest', compact('challenge'));
        } else {
            return redirect()->action('ContestCardController@card_index')->withErrors('Invalid Id Provided');
        }
    }

    public function cardcat_muldelete(Request $request)
    {
        if ($request->isMethod('post')) {
            $values = $request->input('hg_cart');
            $final = explode(',', $values);
            foreach ($final as $id) {
                $teams = DB::connection('mysql')->table('cardcontest')->where('id', $id)->first();
                // $a = $teams->toArray();
                if (!empty($teams)) {
                    DB::connection('mysql')->table('cardcontest')->where('id', $id)->delete();
                }
            }
            // return redirect()->back()->with('success', 'Contest Deleted Sucessfully');
            return 1;die;
        }
        // return redirect()->back()->with('success', 'Sorry,failed to delete!!');
        return 2;die;
    }

    
}
