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

class ContestController extends Controller
{
    /* --------------Contest Category---------------- */
    /* --------------View Contest Category Listing---------------- */
    public function view_contest_category()
    {
        $f_type = request()->get('fantasy_type');

        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $contest_data = DB::table('contest_category')->where('fantasy_type', $f_type)->get();

        return view('contest.view_contest_category', compact('contest_data'));
    }

    public function get_contest_category()
    {
        $f_type = request()->get('fantasy_type');

        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $contest_data = DB::table('contest_category')->where('fantasy_type', $f_type)->get();

        return $contest_data;
    }

    /* --------------Create a new Contest Category---------------- */
    public function create_category(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            $f_type = request()->get('fantasy_type');
            $dataexist = DB::table('contest_category')->where('name', $input['name'])->first();
            if (!empty($dataexist)) {
                return redirect()->back()->with('danger', 'Sorry,Category name already exist');
            } else {
                if ($request->file('image')) {
                    $image = $request->file('image');
                    $extension = $image->getClientOriginalExtension();
                    $ext = array("jpg", "jpeg", "png", 'JPG', 'JPEG', 'PNG');
                    if (!in_array($extension, $ext)) {
                        return redirect()->back()->with('danger', 'Invalid extension of file you uploaded. You can only upload image.');
                    }
                    $hii = Storage::disk('public_folder')->putFile('images_contest_category', $input['image'], 'public');
                    $input['image'] = $hii;
                }
                $input['fantasy_type'] = !empty($f_type) ? $f_type : 'Cricket';
                unset($input['_token']);
                DB::connection('mysql2')->table('contest_category')->insert($input);
                return redirect()->action('ContestController@view_contest_category')->with('success', 'Contest Category save successfully');
            }
        } else {
            return view('contest.create_category');
        }

    }

    /* --------------Edit particular Contest Category---------------- */
    public function edit_contest_category(Request $request, $id)
    {
        $getid = unserialize(base64_decode($id));
        if ($request->isMethod('post')) {
            $input = $request->all();
            $dataexist = DB::table('contest_category')->where('name', $input['name'])->where('id', '!=', $getid)->first();
            if (!empty($dataexist)) {
                return redirect()->back()->with('danger', 'Sorry,Category name already exist');
            } else {
                if ($request->file('image')) {
                    $image = $request->file('image');
                    $extension = $image->getClientOriginalExtension();
                    $ext = array("jpg", "jpeg", "png", 'JPG', 'JPEG', 'PNG');
                    if (!in_array($extension, $ext)) {
                        return redirect()->back()->with('danger', 'Invalid extension of file you uploaded. You can only upload image.');
                    }
                    $hii = Storage::disk('public_folder')->putFile('images_contest_category', $input['image'], 'public');
                    $input['image'] = $hii;
                    // delete old image
                    $oldimage = DB::table('contest_category')->where('id', $id)->first();
                    if (!empty($oldimage)) {
                        $filename = $oldimage->image;
                        Storage::disk('public_folder')->delete($filename);
                    }
                }
                $f_type = request()->get('fantasy_type');
                $input['fantasy_type'] = !empty($f_type) ? $f_type : 'Cricket';
                unset($input['_token']);
                DB::connection('mysql2')->table('contest_category')->where('id', $getid)->update($input);
                return redirect()->action('ContestController@view_contest_category')->with('success', 'Contest Category updated successfully');
            }
        } else {
            $contest = DB::table('contest_category')->where('id', $getid)->first();
            return view('contest.edit_contest_category', compact('contest'));
        }
    }

/* ------------Global Contest Controller----------------- */
    public function global_index()
    {
        return view('contest.view_all_global_contest');
    }

    public function global_index_datatable(Request $request)
    {
        $f_type = request()->get('fantasy_type');

        // $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'contest_cat',
            3 => 'entryfee',
            4 => 'win_amount',
            5 => 'maximum_user',
            6 => 'multi_entry',
            7 => 'is_running',
            8 => 'confirmed_challenge',
            9 => 'is_bonus',
            10 => 'cat_type',
        );

        if(!empty($f_type)){
            $totalTitles = DB::table('challenges')->where('fantasy_type', $f_type)->count();
        }else{
            $totalTitles = DB::table('challenges')->count();
        }
        
        $totalFiltered = $totalTitles;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $titles = DB::table('challenges')->join('contest_category', 'contest_category.id', '=', 'challenges.contest_cat')->select('challenges.*', 'contest_category.name as cat_name')->offset($start)
                // ->where('challenges.fantasy_type', $f_type)
                ->limit($limit)->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $titles = DB::table('challenges')->where('entryfee', 'LIKE', "%{$search}%")
                ->where('fantasy_type', $f_type)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = DB::table('challenges')->Where('entryfee', 'LIKE', "%{$search}%")
                ->where('fantasy_type', $f_type)
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
                $a = action('ContestController@addpricecard', base64_encode(serialize($title->id)));
                $c = action('ContestController@editglobalcontest', base64_encode(serialize($title->id)));
                $b = action('ContestController@delete_global_contest', base64_encode(serialize($title->id)));

                if ($title->contest_type == 'Amount') {
                    $edit = "<a href='" . $a . "' class='btn btn-sm btn-info w-35px h-35px text-uppercase' data-toggle='tooltip' title='Add / Edit'><i class='fas fa-plus'></i></a>";
                } else {
                    $edit = "";
                }

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
                if($title->fantasy_type=='Duo'){
                    $nestedData['fantasy_type'] = $title->fantasy_type.'('.$title->duotype.')';
                }else{
                    $nestedData['fantasy_type'] = $title->fantasy_type;
                }
                
                $nestedData['cat'] = $title->cat_name;
                $nestedData['entryfee'] = '₹ ' . $title->entryfee;
                $nestedData['win_amount'] = '₹ ' . $title->win_amount;
                $nestedData['maximum_user'] = $title->maximum_user;
                $nestedData['multi_entry'] = $resultdata;
                $nestedData['is_running'] = $resultdata1;
                $nestedData['confirmed_challenge'] = $resultdata2;
                $nestedData['is_bonus'] = $resultdata3;
                $nestedData['contest_type'] = $title->contest_type;
                $nestedData['edit'] = $edit;
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

    public function editglobalcontest($id, Request $request)
    {
        $id = unserialize(base64_decode($id));
        $challenge = DB::table('challenges')->where('id', $id)->first();
        if ($request->isMethod('post')) {
            $rules = array(
                'entryfee' => 'required',
                'win_amount' => 'required',
                'contest_type' => 'required',
                'contest_cat' => 'required',
            );
            $input = request()->all();
            $f_type = request()->get('fantasy_type');
            unset($input['_token']);
            if($f_type!='Duo'){
                if (isset($input['team_limit'])){
                    if ($input['team_limit'] == 0){
                      return redirect()->back()->with('danger','Value of multientry limit not equal to 0...');
                    }
                    else{
                      $data['multi_entry'] = 1;
                    }
                }
                if (isset($input['multi_entry'])) {
                    $input['multi_entry'] = 1;
                } else {
                    $input['multi_entry'] = 0;
                }
                if (!empty($input['multi_entry'])) {
                    $data['multi_entry'] = 1;
                    $data['multi_entry'] = $input['multi_entry'];
                    $data['team_limit'] = $input['team_limit'];
                } else {
                    $data['multi_entry'] = 0;
                    $data['multi_entry'] = 0;
                }
            }else{
                $input['confirmed_challenge'] = 1;
                $data['duotype'] = $input['duotype'];
                $input['pricecard_type']='Percentage';
            }
            
            if (isset($input['confirmed_challenge'])) {
                $input['confirmed_challenge'] = 1;
            } else {
                $input['confirmed_challenge'] = 0;
            }
            if (isset($input['is_running'])) {
                $input['is_running'] = 1;
            } else {
                $input['is_running'] = 0;
            }

            if (isset($input['maximum_user'])) {
                if ($input['maximum_user'] < 2) {

                    return redirect()->back()->with('danger', 'Value of maximum user not less than 2...');
                }
            }
            if (isset($input['winning_percentage'])) {
                if ($input['winning_percentage'] == 0) {

                    return redirect()->back()->with('danger', 'Value of winning percentage not equal to 0...');
                }
            }

            if (isset($input['bonus_percentage'])) {
                if ($input['bonus_percentage'] == 0) {

                    return redirect()->back()->with('danger', 'Value of bonus percentage not equal to 0...');
                }
            }
            if (!isset($input['bonus_percentage'])) {
                $input['bonus_percentage'] = 0;
                $input['is_bonus'] = 0;
            }
            if (!isset($input['maximum_user'])) {
                $input['maximum_user'] = 0;
            }
            if (!isset($input['winning_percentage'])) {
                $input['winning_percentage'] = 0;
            }

            if ($input['win_amount'] != $challenge->win_amount) {
                DB::connection('mysql2')->table('pricecards')->where('challenge_id', $challenge->id)->delete();
            }
            if ($input['contest_type'] == 'Percentage') {
                $input['maximum_user'] = '0';
                $input['pricecard_type'] = '0';
                $pricecarddata = DB::table('pricecards')->where('challenge_id', $challenge->id)->get();
                if (!empty($pricecarddata)) {
                    DB::connection('mysql2')->table('pricecards')->where('challenge_id', $challenge->id)->delete();
                }

            }
            if ($input['contest_type'] == 'Amount') {
                if (empty($input['pricecard_type'])) {
                    $input['pricecard_type'] = 'Amount';
                }
                $input['winning_percentage'] = '0';
            }
            if (isset($input['maximum_user'])) {
                $data['maximum_user'] = $input['maximum_user'];
            }
            if (isset($input['winning_percentage'])) {
                $data['winning_percentage'] = $input['winning_percentage'];
            }
            if (!empty($input['confirmed_challenge'])) {
                $data['confirmed_challenge'] = 1;
            } else {
                $data['confirmed_challenge'] = 0;
            }
            if (!empty($input['is_running'])) {
                $data['is_running'] = 1;
            } else {
                $data['is_running'] = 0;
            }
            if (!empty($input['is_bonus'])) {
                $data['is_bonus'] = 1;
                $data['bonus_percentage'] = $input['bonus_percentage'];
            } else {
                $data['is_bonus'] = 0;
                $data['bonus_percentage'] = 0;
            }
            
            if ($input['win_amount'] != $challenge->win_amount) {
                $checkpcards = DB::table('pricecards')->where('challenge_id', $id)->first();
                if (!empty($checkpcards)) {
                    DB::connection('mysql2')->table('pricecards')->where('challenge_id', $id)->delete();
                }
            }
            if ($input['pricecard_type'] != $challenge->pricecard_type) {
                $checkpcards = DB::table('pricecards')->where('challenge_id', $id)->first();
                if (!empty($checkpcards)) {
                    DB::connection('mysql2')->table('pricecards')->where('challenge_id', $id)->delete();
                }
            }
            $data['contest_type'] = $input['contest_type'];
            $data['pricecard_type'] = $input['pricecard_type'];
            $data['contest_cat'] = $input['contest_cat'];
            $data['entryfee'] = $input['entryfee'];
            $data['offerentryfee'] = $input['offerentryfee'];
            $data['win_amount'] = $input['win_amount'];
            if (isset($input['win_amount_2'])) {
                $data['win_amount_2'] = $input['win_amount_2'];
            }
            $f_type = request()->get('fantasy_type');
            $data['fantasy_type'] = !empty($f_type) ? $f_type : 'Cricket';
            //  dd($data);
            $getid = DB::connection('mysql2')->table('challenges')->where('id', $id)->update($data);
            if (isset($input['contest_type'])) {
                if ($input['contest_type'] == "Amount") {
                    $id1 = base64_encode(serialize($id));
                    return redirect()->action('ContestController@addpricecard', base64_encode(serialize($id)));

                } else {
                    return redirect()->back()->with('success', 'Successfully created contest!');
                }
            }

            return Redirect::back()->with('success', 'Successfully updated contest!');
        }

        if (!empty($challenge)) {

            $contest_cat = DB::table('contest_category')->select('name', 'id')->get();
            return view('contest.editglobalcontest', compact('challenge', 'contest_cat'));
        } else {
            return redirect()->action('ContestController@global_index')->withErrors('Invalid Id Provided');
        }
    }

    public function create_global(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($request->isMethod('post')) {
            $rules = array(
                'entryfee' => 'required',
                'win_amount' => 'required',
                'contest_type' => 'required',
                'contest_cat' => 'required',
            );

            $input = $request->input();
            $f_type = request()->get('fantasy_type');
            $data['type'] = 'global';
            $findchallenge = DB::table('challenges')->where('entryfee', $input['entryfee'])->where('win_amount', $input['win_amount'])->where('maximum_user', $input['maximum_user'])->where('contest_cat', $input['contest_cat'])->where('fantasy_type', $f_type)->first();
            if (!empty($findchallenge)) {
                return Redirect::back()->with('danger', 'This contest is already exist with the same winning amount, entry fees and maximum number of users.')->withInput(request()->except('password'));
            }
            if($f_type!='Duo'){
                if (isset($input['team_limit'])){
                    if ($input['team_limit'] == 0)
                    {
                      return redirect()->back()->with('danger','Value of multientry limit not equal to 0...');
                    }
                    else
                    {
                      $data['multi_entry'] = 1;
                    }
                }
                if (isset($input['multi_entry'])) {
                    $data['multi_entry'] = 1;
                    $data['multi_entry'] = $input['multi_entry'];
                    $data['team_limit'] = $input['team_limit'];
                }
            }else{
                $input['confirmed_challenge'] = 1;
                $data['duotype'] = $input['duotype'];
                $input['pricecard_type']='Percentage';
            }
            
            if (isset($input['maximum_user'])) {
                if ($input['maximum_user'] < 2) {
                    return redirect()->back()->with('danger', 'Value of maximum user not less than 2...');
                }
            }
            if (isset($input['winning_percentage'])) {
                if ($input['winning_percentage'] == 0) {
                    return redirect()->back()->with('danger', 'Value of winning percentage not equal to 0...');
                }
            }

            if (isset($input['bonus_percentage'])) {
                if ($input['bonus_percentage'] == 0) {
                    return redirect()->back()->with('danger', 'Value of bonus percentage not equal to 0...');
                }
            }

            unset($input['_token']);
            if ($input['contest_type'] == 'Percentage') {
                $input['maximum_user'] = '0';
                $input['pricecard_type'] = '0';
            }
            if (isset($input['maximum_user'])) {
                $data['maximum_user'] = $input['maximum_user'];
            }
            if (isset($input['winning_percentage'])) {
                $data['winning_percentage'] = $input['winning_percentage'];
            }
            if (isset($input['confirmed_challenge'])) {
                $data['confirmed_challenge'] = 1;
            }
            if (isset($input['is_running'])) {
                $data['is_running'] = 1;
            }
            if (isset($input['is_bonus'])) {
                $data['is_bonus'] = 1;
                $data['bonus_percentage'] = $input['bonus_percentage'];
            }
            
            $data['contest_type'] = $input['contest_type'];
            $data['pricecard_type'] = $input['pricecard_type'];
            $data['contest_cat'] = $input['contest_cat'];
            $data['entryfee'] = $input['entryfee'];
            $data['offerentryfee'] = $input['offerentryfee'];
            $data['win_amount'] = $input['win_amount'];
            if ($input['contest_type'] == 'Amount') {
                $input['winning_percentage'] = '0';
            }
            $count = 0;
            $count++;
            
            $data['fantasy_type'] = !empty($f_type) ? $f_type : 'Cricket';
            if (isset($input['win_amount_2'])) {
                $data['win_amount_2'] = $input['win_amount_2'];
            }
            $getid = DB::connection('mysql2')->table('challenges')->insertGetId($data);

            if (isset($input['contest_type'])) {
                if ($input['contest_type'] == "Amount") {
                    $id1 = base64_encode(serialize($getid));
                    return redirect()->action('ContestController@addpricecard', base64_encode(serialize($getid)))->with('success', 'Contest Created Successfully!!!');

                } else {
                    return redirect()->back()->with('success', 'Contest Created Successfully!');
                }
            }
        } else {
            $contest_cat = DB::table('contest_category')->select('name', 'id')->get();
            return view('contest.create_global_contest', compact('contest_cat'));
        }
    }

    public function addpricecard($id, Request $request)
    {

        date_default_timezone_set('Asia/Kolkata');
        $addcardid = $id;
        $id = unserialize(base64_decode($id));
        $totalpriceamounts = DB::table('pricecards')->where('challenge_id', $id)->select(DB::raw('sum(pricecards.total) as totalpriceamount'))->get();
        $findallpricecards = DB::table('pricecards')->where('challenge_id', $id)->get();
        $findchallenge = DB::table('challenges')->where('id', $id)->get();
        $findchallenge1 = DB::table('challenges')->where('id', $id)->first();
        if (!empty($findchallenge1)) {
            $d = $findchallenge1->contest_cat;
            $cat = DB::table('contest_category')->where('id', $d)->select('name')->first();
            if (!empty($findchallenge)) {
                $min_position = 0;
                $totalpriceamount = 0;
                if (count($findallpricecards)) {
                    $findminposition = DB::table('pricecards')->where('challenge_id', $id)->orderBY('id', 'DESC')->select('max_position')->first();
                    $min_position = $findminposition->max_position;
                    $totalpriceamount = $totalpriceamounts[0]->totalpriceamount;
                }
                if ($request->isMethod('post')) {
                    $price_check = request()->get('price');
                    if (isset($price_check) and $price_check == '0') {
                        return redirect()->back()->with('error', 'Amount should be greater than 0');
                    }

                    $input = request()->all();

                    if($findchallenge1->fantasy_type=='Duo'){
                        if ($input['winners'] != 1) {
                            return redirect()->back()->with('danger', 'Single User Winner Contest');
                        } 
                        if($input['price_percent']!=100){
                            return redirect()->back()->with('danger', 'Winning Amount Distribute Only Single User');
                        }
                    }


                    if ($input['winners'] == 0) {
                        return redirect()->back()->with('danger', 'Number of winner not equal to zero.');
                    } else {
                        if (isset($input['user_selection'])) {
                            if ($input['user_selection'] != 'number') {
                                $input['winners'] = round($findchallenge1->maximum_user * ($input['winners'] / 100));
                            }
                        }
                    }
                    if (!empty($input['price'])) {
                        $input['price'] = $input['price'];
                    } else {
                        $input['price'] = 0;
                        $input['price_percent'] = $input['price_percent'];
                    }

                    $input['max_position'] = $input['min_position'] + $input['winners'];
                    if (isset($input['price'])) {
                        $input['total'] = $input['price'] * $input['winners'];
                        $input['type'] = 'Amount';
                    }
                    if (!empty($input['price_percent'])) {
                        $percent_amt = ($input['price_percent'] / 100) * $findchallenge[0]->win_amount;
                        $input['total'] = $percent_amt * $input['winners'];
                        $input['type'] = 'Percentage';
                    }
                    $input['challenge_id'] = $id;
                    unset($input['_token']);
                    $countamount = $totalpriceamount + $input['total'];
                    if ($countamount > $findchallenge[0]->win_amount) {

                        return redirect()->action('ContestController@addpricecard', $addcardid)->with('danger', 'Your price cards amount is greater than the total winning amount');
                    }
                    if (!empty($findchallenge[0]->maximum_user)) {
                        if ($input['max_position'] > $findchallenge[0]->maximum_user) {
                            return redirect()->action('ContestController@addpricecard', $addcardid)->with('danger', 'You cannot add more winners.');
                        }
                    } else {
                        $per = DB::table('pricecards')->where('challenge_id', $id)->select(DB::raw('sum(pricecards.total) as totalpriceamount'))->get();
                        $aa = $per[0]->totalpriceamount + $input['total'];
                        if ($aa > 100) {
                            return redirect()->action('ContestController@addpricecard', $addcardid)->with('danger', 'You cannot add more winners.');
                        }
                    }

                    if (isset($input['user_selection'])) {
                        unset($input['user_selection']);
                    }
                    DB::connection('mysql2')->table('pricecards')->insert($input);
                    return redirect()->action('ContestController@addpricecard', base64_encode(serialize($id)))->with('success', 'price card added successfully!');
                }
            } else {
                return redirect()->action('ContestController@global_index')->with('danger', 'Invalid match Provided');
            }
        }
        return view('contest.addpricecard', compact('findallpricecards', 'addcardid', 'min_position', 'findchallenge1', 'cat'));
    }

    public function deletepricecard($id)
    {
        $id = unserialize(base64_decode($id));
        $findallpricecards = DB::table('pricecards')->where('id', $id)->first();
        if (!empty($findallpricecards)) {
            DB::connection('mysql2')->table('pricecards')->where('id', $id)->delete();
            return Redirect::back()->with('success', 'Price Card Successfully deleted');
        } else {
            return redirect()->action('ContestController@addpricecard')->with('error', 'Invalid Pricecard Provided');
        }
    }

    public function delete_global_contest($id)
    {
        $id = unserialize(base64_decode($id));
        $findchallenege = DB::connection('mysql2')->table('challenges')->where('id', $id)->delete();
        $data = DB::table('pricecards')->where('challenge_id', $id)->select('id')->get();
        $d = $data->toArray();
        if (!empty($d)) {
            $findallpricecards = DB::connection('mysql2')->table('pricecards')->where('challenge_id', $id)->delete();
        }
        return Redirect::back()->with('success', 'Global Contest Successfully deleted');
    }
/* -----------------Custom Contest Controller--------------------- */
    /************** to see the index page of custom contests ************/
    public function custom_index()
    {
        return view('contest.view_all_custom_contest');
    }
    /************** Create Custom Contests *****************/
    public function create_custom(Request $request)
    {
        $f_type = request()->get('fantasy_type');
        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $rules = array(
            'entryfee' => 'required',
            'win_amount' => 'required',
            'contest_type' => 'required',
            'contest_cat' => 'required',
        );
        date_default_timezone_set('Asia/Kolkata');
        $currentdate = date('Y-m-d h:i:s');
        $findalllistmatches = DB::table('listmatches')
            ->select('name', 'matchkey')
            ->Where('launch_status', 'launched')
            ->where('fantasy_type', $f_type)
            ->where('start_date', '>=', $currentdate)
            ->orderBY('start_date', 'ASC')
            ->get();
        if ($request->isMethod('post')) {
            $input = request()->all();

            if (isset($input['maximum_user'])) {
                if ($input['maximum_user'] < 2 || empty($input['maximum_user'])) {
                    return redirect()->back()->with('danger', 'Value of maximum user not less than 2...');
                }
            }
            if (isset($input['winning_percentage'])) {
                if ($input['winning_percentage'] == 0) {
                    return redirect()->back()->with('danger', 'Value of winning percentage not equal to 0...');
                }
            }

            if (isset($input['bonus_percentage'])) {
                if ($input['bonus_percentage'] == 0) {
                    return redirect()->back()->with('Value of bonus percentage not equal to 0...');
                }
            }
            if (!isset($input['maximum_user'])) {
                $input['maximum_user'] = 0;
            }
            if (!isset($input['winning_percentage'])) {
                $input['winning_percentage'] = 0;
            }
            if ($input['contest_type'] == 'Percentage') {
                $input['maximum_user'] = '0';
                $input['pricecard_type'] = '0';
            }
            if ($input['contest_type'] == 'Amount') {
                $input['winning_percentage'] = '0';
            }
            unset($input['_token']);

            if($f_type!='Duo'){
                if (isset($input['team_limit']))
                {
                    if ($input['team_limit'] == 0)
                    {
                        return redirect()->back()->with('danger','Value of multientry limit not equal to 0...');
                    }
                    else
                    {
                        $data['multi_entry'] = 1;
                    }
                }
                if (isset($input['multi_entry'])) {
                    $data['multi_entry'] = 1;
                    $data['multi_entry'] = $input['multi_entry'];
                    $data['team_limit'] = $input['team_limit'];
                }
            }else{
                $input['confirmed_challenge']=1;
                $data['duotype'] = $input['duotype'];
                $input['pricecard_type']='Percentage';
            }
            
            if (isset($input['maximum_user'])) {
                $data['maximum_user'] = $input['maximum_user'];
            }

            if (isset($input['created_by'])) {
                if (!empty($input['created_by'])) {
                    $data['created_by'] = $input['created_by'];
                    $data['is_private'] = 1;
                    $data['is_admin'] = 1;
                }
            }
            if (isset($input['winning_percentage'])) {
                $data['winning_percentage'] = $input['winning_percentage'];
            }
            if (isset($input['confirmed_challenge'])) {
                $data['confirmed_challenge'] = 1;
            }
            if (isset($input['is_running'])) {
                $data['is_running'] = 1;
            }
            if (isset($input['is_bonus'])) {
                $data['is_bonus'] = 1;
                $data['bonus_percentage'] = $input['bonus_percentage'];
            }
            
            $data['contest_type'] = $input['contest_type'];
            $data['pricecard_type'] = $input['pricecard_type'];
            $data['contest_cat'] = $input['contest_cat'];
            $data['entryfee'] = $input['entryfee'];
            $data['offerentryfee'] = $input['offerentryfee'];
            $data['win_amount'] = $input['win_amount'];
            $data['matchkey'] = $input['matchkey'];
            $data['status'] = 'opened';
            $data['fantasy_type'] = $f_type;
            // echo '<pre>';print_r($data);die;
            $count = 0;
            $count++;
            if (isset($input['win_amount_2'])) {
                $data['win_amount_2'] = $input['win_amount_2'];
            }
            $getid = DB::connection('mysql2')->table('matchchallenges')->insertGetId($data);

            if ($input['contest_type'] == "Amount") {
                return redirect()->action('ContestController@addmatchpricecard', base64_encode(serialize($getid)));
            } else {
                return redirect()->back()->with('success', 'Successfully created contest!');
            }
        }
        $contest_cat = DB::table('contest_category')->select('name', 'id')->where('fantasy_type', $f_type)->get();
        return view('contest.create_custom_contest', compact('findalllistmatches', 'contest_cat'));
    }

    public function editcustomcontest($id, Request $request)
    {
        $rules = array(
            'entryfee' => 'required',
            'win_amount' => 'required',
            'contest_type' => 'required',
            'contest_cat' => 'required',
        );

        $id = unserialize(base64_decode($id));
        $challenge = DB::table('matchchallenges')->where('id', $id)->first();
        if (!empty($challenge)) {
            $findmatchnames = DB::table('listmatches')->where('matchkey', $challenge->matchkey)->get();
            if ($request->isMethod('post')) {
                $input = request()->all();
                $f_type = request()->get('fantasy_type');
                unset($input['_token']);
                if($f_type!='Duo'){
                    if (isset($input['team_limit']))
                    {
                        if ($input['team_limit'] == 0)
                        {
                        return redirect()->back()->with('danger','Value of multientry limit not equal to 0...');
                        }
                        else
                        {
                            $data['multi_entry'] = 1;
                        }
                    }
                    if (isset($input['multi_entry'])) {
                        $input['multi_entry'] = 1;
                    } else {
                        $input['multi_entry'] = 0;
                    }
                    if (!empty($input['multi_entry'])) {
                        $data['multi_entry'] = 1;
                        $data['multi_entry'] = $input['multi_entry'];
                        $data['team_limit'] = $input['team_limit'];
                    } else {
                        $data['multi_entry'] = 0;
                    }
                }else{
                    $input['confirmed_challenge']=1;
                    $data['duotype'] = $input['duotype'];
                    $input['pricecard_type']='Percentage';
                }
                
                if (isset($input['confirmed_challenge'])) {
                    $input['confirmed_challenge'] = 1;
                } else {
                    $input['confirmed_challenge'] = 0;
                }
                if (isset($input['is_running'])) {
                    $input['is_running'] = 1;
                } else {
                    $input['is_running'] = 0;
                }

                if (!empty($input['maximum_user'])) {
                    if ($input['maximum_user'] < 2) {
                        return redirect()->back()->with('danger', 'Value of maximum user not less than 2...');
                    }
                }
                if (isset($input['winning_percentage'])) {
                    if ($input['winning_percentage'] == 0) {
                        return redirect()->back()->with('danger', 'Value of winning percentage not equal to 0...');
                    }
                }
                if (isset($input['bonus_percentage'])) {
                    if ($input['bonus_percentage'] == 0) {
                        return redirect()->back()->with('danger', 'Value of winning percentage not equal to 0...');
                    }
                }

                if (!isset($input['bonus_percentage'])) {
                    $input['bonus_percentage'] = 0;
                    $input['is_bonus'] = 0;
                }
                if (!isset($input['maximum_user'])) {
                    $input['maximum_user'] = 0;
                }
                if (!isset($input['winning_percentage'])) {
                    $input['winning_percentage'] = 0;
                }
                if ($input['contest_type'] == 'Percentage') {
                    $input['maximum_user'] = '0';
                    $input['pricecard_type'] = '0';
                    $pricecarddata = DB::table('matchpricecards')->where('matchkey', $challenge->matchkey)->where('challenge_id', $challenge->id)->get();
                    if (!empty($pricecarddata)) {
                        DB::connection('mysql2')->table('matchpricecards')->where('matchkey', $challenge->matchkey)->where('challenge_id', $challenge->id)->delete();
                    }
                }
                if ($input['contest_type'] == 'Amount') {
                    $input['winning_percentage'] = '0';
                }

                $input['status'] = 'opened';

                $findjoinedleauges = DB::table('joinedleauges')->where('challengeid', $id)->get();
                $a = $findjoinedleauges->toArray();
                if (!empty($a)) {
                    return redirect()->back()->with('danger', 'You cannot edit this challenge now!');
                }
                if (!empty($input['pricecard_type'])) {
                    if ($input['pricecard_type'] == 'Amount') {
                        $pricecardper = DB::table('matchpricecards')->where('matchkey', $challenge->matchkey)->where('challenge_id', $challenge->id)->where('type', 'Percentage')->get();
                        if (!empty($pricecardper)) {
                            DB::connection('mysql2')->table('matchpricecards')->where('matchkey', $challenge->matchkey)->where('challenge_id', $challenge->id)->delete();
                        }
                    } else if ($input['pricecard_type'] == 'Percentage') {
                        $pricecardamou = DB::table('matchpricecards')->where('matchkey', $challenge->matchkey)->where('challenge_id', $challenge->id)->where('type', 'Amount')->get();
                        if (!empty($pricecardamou)) {
                            DB::connection('mysql2')->table('matchpricecards')->where('matchkey', $challenge->matchkey)->where('challenge_id', $challenge->id)->delete();
                        }
                    }
                }
                if ($input['contest_type'] == 'Amount') {
                    if (empty($input['pricecard_type'])) {
                        $input['pricecard_type'] = 'Amount';
                    }
                    $input['winning_percentage'] = '0';
                }
                if (isset($input['maximum_user'])) {
                    $data['maximum_user'] = $input['maximum_user'];
                }
                if (isset($input['winning_percentage'])) {
                    $data['winning_percentage'] = $input['winning_percentage'];
                }
                if (!empty($input['confirmed_challenge'])) {
                    $data['confirmed_challenge'] = 1;
                } else {
                    $data['confirmed_challenge'] = 0;
                }
                if (!empty($input['is_running'])) {
                    $data['is_running'] = 1;
                } else {
                    $data['is_running'] = 0;
                }
                if (!empty($input['is_bonus'])) {
                    $data['is_bonus'] = 1;
                    $data['bonus_percentage'] = $input['bonus_percentage'];
                } else {
                    $data['is_bonus'] = 0;
                    $data['bonus_percentage'] = 0;
                }
                
                $data['contest_type'] = $input['contest_type'];
                $data['pricecard_type'] = $input['pricecard_type'];
                $data['contest_cat'] = $input['contest_cat'];
                $data['entryfee'] = $input['entryfee'];
                $data['offerentryfee'] = $input['offerentryfee'];
                $data['win_amount'] = $input['win_amount'];

                if (isset($input['win_amount_2'])) {
                    $data['win_amount_2'] = $input['win_amount_2'];
                }
                $rowCOllection = DB::connection('mysql2')->table('matchchallenges')->where('id', $id)->update($data);

                if (isset($input['contest_type'])) {
                    if ($input['contest_type'] == "Amount") {
                        $id1 = base64_encode(serialize($id));
                        return redirect()->action('ContestController@addmatchpricecard', base64_encode(serialize($id)));

                    } else {
                        return redirect()->back()->with('success', 'Successfully updated contest!');
                    }
                }

            }
            $contest_cat = DB::table('contest_category')->select('name', 'id')->get();
            return view('contest.editcustomcontest', compact('challenge', 'findmatchnames', 'contest_cat'));
        } else {
            return redirect()->action('ContestController@create_custom_contest')->with('danger', 'Invalid match Provided');
        }
    }
    /***** Select Match to create or see custom contests for particular match ******/
    public function create_custom_contest()
    {
        $f_type = request()->get('fantasy_type');
        // if ($f_type == '') {
        //     $f_type = 'Cricket';
        // }
        date_default_timezone_set('Asia/Kolkata');
        $allchallenges = array();
        if (request()->has('matchid')) {
            $matchid = request('matchid');
            if ($matchid != "") {
                $allchallenges = DB::table('matchchallenges')->where('matchkey', $matchid)->orderBy('is_private', 'ASC')->get();
            }
        }
        $currentdate = date('Y-m-d h:i:s');
        $findalllistmatches = DB::table('listmatches')->select('name', 'matchkey')
            // ->where('fantasy_type', $f_type)
            ->Where('launch_status', 'launched')
            ->Where('status', '!=', 'completed')
            ->where('start_date', '>=', $currentdate)
            ->orderBY('start_date', 'ASC')
            ->get();
            // dd($findalllistmatches);
        return view('contest.view_all_custom_contest', compact('findalllistmatches', 'allchallenges'));
    }

    public function get_fantasy_matches()
    {
        $f_type = !empty(request()->get('fantasy_type')) ? request()->get('fantasy_type') : 'Cricket';

        $currentdate = date('Y-m-d h:i:s');
        $findalllistmatches = DB::table('listmatches')->select('name', 'matchkey')
            ->where('fantasy_type', $f_type)
            ->Where('launch_status', 'launched')
            ->Where('status', '!=', 'completed')
            ->where('start_date', '>=', $currentdate)
            ->orderBY('start_date', 'ASC')
            ->get();

        return $findalllistmatches;
    }

    /***************** Import Default Contests *************/
    public function importdata($id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $findmatch = DB::table('listmatches')->where('matchkey', $id)->first();
        if (!empty($findmatch)) {
            $f_type = request()->get('fantasy_type');
            // $data['fantasy_type'] = !empty($f_type) ? $f_type : 'Cricket';
            $findleauges = DB::table('challenges')
                ->join('pricecards', 'pricecards.challenge_id', 'challenges.id')
                ->where('challenges.freez', 0)
                // ->where('challenges.fantasy_type', $data['fantasy_type'])
                ->get(['challenges.*']);
            $charray = array();
            if (!empty($findleauges)) {
                foreach ($findleauges as $leauge) {
                    $findchallengeexist = DB::table('matchchallenges')->where('matchkey', $id)->where('challenge_id', $leauge->id)->first();

                    if (empty($findchallengeexist)) {
                        $data['challenge_id'] = $leauge->id;
                        $data['contest_cat'] = $leauge->contest_cat;
                        $data['fantasy_type'] = $leauge->fantasy_type;
                        $data['duotype'] = $leauge->duotype;
                        $data['contest_type'] = $leauge->contest_type;
                        $data['winning_percentage'] = $leauge->winning_percentage;
                        $data['is_bonus'] = $leauge->is_bonus;
                        $data['bonus_percentage'] = $leauge->bonus_percentage;
                        $data['pricecard_type'] = $leauge->pricecard_type;
                        $data['entryfee'] = $leauge->entryfee;
                        $data['offerentryfee'] = $leauge->offerentryfee;
                        $data['win_amount'] = $leauge->win_amount;
                        $data['maximum_user'] = $leauge->maximum_user;
                        $data['status'] = 'opened';
                        $data['confirmed_challenge'] = $leauge->confirmed_challenge;
                        $data['is_running'] = $leauge->is_running;
                        $data['multi_entry'] = $leauge->multi_entry;
                        $data['team_limit'] = $leauge->team_limit;
                        $data['matchkey'] = $id;
                        $data['created_at'] = date('Y-m-d h:i:s');
                        $data['updated_at'] = date('Y-m-d h:i:s');

                        $getcid = DB::connection('mysql2')->table('matchchallenges')->insertGetId($data);

                        $findpricecrads = DB::table('pricecards')->where('challenge_id', $leauge->id)->get();
                        if (!empty($findpricecrads)) {
                            foreach ($findpricecrads as $pricec) {
                                $pdata['challenge_id'] = $getcid;
                                $pdata['matchkey'] = $id;
                                $pdata['winners'] = $pricec->winners;
                                $pdata['type'] = $pricec->type;
                                if (!empty($pricec->price)) {
                                    $pdata['price'] = $pricec->price;
                                } else {
                                    $pdata['price_percent'] = $pricec->price_percent;
                                    $pdata['price'] = 0;
                                }
                                $pdata['min_position'] = $pricec->min_position;
                                $pdata['max_position'] = $pricec->max_position;
                                $pdata['total'] = $pricec->total;
                                $pdata['created_at'] = date('Y-m-d h:i:s');
                                $pdata['updated_at'] = date('Y-m-d h:i:s');
                                DB::connection('mysql2')->table('matchpricecards')->insert($pdata);
                            }
                        }

                    }
                }
            }
            return redirect()->back()->with('success', ' Challenges imported successfully');
        } else {
            return redirect()->action('ContestController@createchallenge')->with('danger', 'Invalid match Provided');
        }
    }
    /************************ Add Match Price Card ***************/
    public function addmatchpricecard($gid, Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $addcardid = $gid;
        $id = unserialize(base64_decode($gid));
        $totalpriceamounts = DB::table('matchpricecards')->where('challenge_id', $id)->select(DB::raw('sum(matchpricecards.total) as totalpriceamount'))->get();
        $findallpricecards = DB::table('matchpricecards')->where('challenge_id', $id)->get();
        $findchallenge = DB::table('matchchallenges')->where('id', $id)->get();
        $findchallenge1 = DB::table('matchchallenges')->where('id', $id)->first();
        $d = $findchallenge1->contest_cat;
        $matchkey = $findchallenge1->matchkey;
        $cat = DB::table('contest_category')->where('id', $d)->select('name')->first();

        if (!empty($findchallenge)) {
            $min_position = 0;
            $totalpriceamount = 0;

            if (count($findallpricecards)) {

                $findminposition = DB::table('matchpricecards')->where('challenge_id', $id)->orderBY('id', 'DESC')->select('max_position')->first();
                $min_position = $findminposition->max_position;
                $totalpriceamount = $totalpriceamounts[0]->totalpriceamount;
            }

            if ($request->isMethod('post')) {
                $price_check = request()->get('price');
                if (isset($price_check) and $price_check == '0') {
                    return redirect()->back()->with('error', 'Amount should be greater than 0');
                }

                $input = request()->all();
                if($findchallenge1->fantasy_type=='Duo'){
                    if ($input['winners'] != 1) {
                        return redirect()->back()->with('danger', 'Single User Winner Contest');
                    } 
                    if($input['price_percent']!=100){
                        return redirect()->back()->with('danger', 'Winning Amount Distribute Only Single User');
                    }
                }
                if ($input['winners'] == 0) {
                    return redirect()->back()->with('danger', 'Number of winner not equal to zero.');
                } else {
                    if (isset($input['user_selection'])) {
                        if ($input['user_selection'] != 'number') {
                            $input['winners'] = round($findchallenge1->maximum_user * ($input['winners'] / 100));
                        }
                    }
                }

                $input['max_position'] = $input['min_position'] + $input['winners'];
                if (isset($input['price'])) {
                    $input['total'] = $input['price'] * $input['winners'];
                    $input['type'] = 'Amount';
                }
                if (!empty($input['price_percent'])) {
                    $percent_amt = ($input['price_percent'] / 100) * $findchallenge[0]->win_amount;
                    $input['total'] = $percent_amt * $input['winners'];
                    $input['type'] = 'Percentage';
                }
                $input['challenge_id'] = $id;
                $input['matchkey'] = $matchkey;
                unset($input['_token']);
                $countamount = $totalpriceamount + $input['total'];

                if ($countamount > $findchallenge[0]->win_amount) {
                    return redirect()->action('ContestController@addmatchpricecard', $addcardid)->with('danger', 'Your price cards amount is greater than the total wining amount');
                }
                if (!empty($findchallenge[0]->maximum_user)) {
                    if ($input['max_position'] > $findchallenge[0]->maximum_user) {
                        return redirect()->action('ContestController@addmatchpricecard', $addcardid)->with('danger', 'You cannot add more winners.');
                    }
                } else {
                    $per = DB::table('matchpricecards')->where('challenge_id', $id)->select(DB::raw('sum(matchpricecards.total) as totalpriceamount'))->get();
                    $aa = $per[0]->totalpriceamount + $input['total'];
                    if ($aa > 100) {
                        return redirect()->action('ContestController@addmatchpricecard', $addcardid)->with('danger', 'You cannot add more winners.');
                    }
                }
                $data['min_position'] = $input['min_position'];
                $data['winners'] = $input['winners'];
                if (!empty($input['price'])) {
                    $data['price'] = $input['price'];
                } else {
                    $data['price'] = 0;
                    $data['price_percent'] = $input['price_percent'];
                }

                $data['max_position'] = $input['max_position'];
                $data['total'] = $input['total'];
                $data['type'] = $input['type'];
                $data['challenge_id'] = $input['challenge_id'];
                $data['matchkey'] = $input['matchkey'];
                if (isset($input['user_selection'])) {
                    unset($input['user_selection']);
                }
                DB::connection('mysql2')->table('matchpricecards')->insert($data);
                return redirect()->action('ContestController@addmatchpricecard', base64_encode(serialize($id)))->with('success', 'price card added successfully!');
            }
        } else {
            return redirect()->action('ContestController@create_custom_contest')->with('danger', 'Invalid match Provided');
        }
        return view('contest.addmatchpricecard', compact('findallpricecards', 'addcardid', 'min_position', 'findchallenge1', 'cat'));
    }
    /************* To delete match price card *****************/
    public function deletematchpricecard($id)
    {
        $id = unserialize(base64_decode($id));
        $findallpricecards = DB::table('matchpricecards')->where('id', $id)->first();
        if (!empty($findallpricecards)) {
            DB::connection('mysql2')->table('matchpricecards')->where('id', $id)->delete();
            return Redirect::back()->with('success', 'Price Card Successfully deleted');
        } else {
            return redirect()->action('ContestController@addmatchpricecard', $id)->with('error', 'Invalid match Provided');
        }
    }
    // to delete contest category
    public function delete_contest_category($id)
    {
        $id = unserialize(base64_decode($id));
        $findata = DB::table('contest_category')->where('id', $id)->first();

        $global_cat = DB::table('challenges')->where('contest_cat', $findata->id)->get();

        if (count($global_cat) == '0') {
            if (!empty($findata)) {
                if (!empty($findata->image)) {
                    $filename = $findata->image;
                    $filenamep = public_path() . '/images_contest_category/' . $filename;
                    @unlink($filenamep);
                }
                DB::connection('mysql2')->table('contest_category')->where('id', $id)->delete();
                return redirect()->back()->with('success', 'Successfully deleted');
            } else {
                return redirect()->back()->with('error', 'sorry,failed to delete');
            }
        } else {
            return redirect()->back()->with('error', 'sorry,failed to delete,This Contest Category is used in other contest');
        }
    }
    //to multiple delete global challenges
    public function globalcat_muldelete(Request $request)
    {
        if ($request->isMethod('post')) {
            $values = $request->input('hg_cart');
            $final = explode(',', $values);
            foreach ($final as $id) {
                $teams = DB::table('challenges')->where('id', $id)->first();
                $deletedta = DB::table('pricecards')->where('challenge_id', $id)->get();
                // $a = $teams->toArray();
                $aa = $deletedta->toArray();
                if (!empty($teams)) {
                    DB::connection('mysql2')->table('challenges')->where('id', $id)->delete();
                }
                if (!empty($aa)) {
                    DB::connection('mysql2')->table('pricecards')->where('challenge_id', $teams->id)->delete();
                }
            }
            // return redirect()->back()->with('success', 'Contest Deleted Sucessfully');
            return 1;die;
        }
        // return redirect()->back()->with('success', 'Sorry,failed to delete!!');
        return 2;die;
    }

    public function delete_customcontest($id)
    {
        $id1 = unserialize(base64_decode($id));
        if (!empty($id1)) {
            $findchallenege = DB::connection('mysql2')->table('matchchallenges')->where('id', $id1)->delete();
            $data = DB::table('pricecards')->where('challenge_id', $id1)->select('id')->get();
            $d = $data->toArray();
            if (!empty($d)) {
                $findallpricecards = DB::connection('mysql2')->table('matchpricecards')->where('challenge_id', $id1)->delete();
            }
            return redirect()->back()->with('success', 'Custom Contest Successfully deleted!');
        } else {
            return redirect()->back()->with('danger', 'sorry,failed to delete');
        }

    }

    public function contestcancel($challengeid)
    {
        $val2 = DB::table('matchchallenges')->where('id', $challengeid)->first();
        // cancel the challenge //
        if (!empty($val2->joinedusers)) {
            $leaugestransactions = DB::table('leaugestransactions')->where('matchkey', $val2->matchkey)->where('challengeid', $val2->id)->get();
            foreach ($leaugestransactions as $val3) {
                $refund_data = DB::table('refunds')->where('joinid', $val3->joinid)->select('id')->first();
                if (empty($refund_data)) {
                    $entryfee = $val2->entryfee;
                    $findlastow = DB::table('userbalance')->where('user_id', $val3->user_id)->first();
                    if (!empty($findlastow)) {
                        $dataq['balance'] = $findlastow->balance + $val3->balance;
                        $dataq['winning'] = $findlastow->winning + $val3->winning;
                        $dataq['bonus'] = $findlastow->bonus + $val3->bonus;
                        $dataq['referral_income'] = $findlastow->referral_income + $val3->referral_income;
                        DB::connection('mysql2')->table('userbalance')->where('id', $findlastow->id)->update($dataq);
                        // entry in refund //
                        $refunddata['userid'] = $val3->user_id;
                        $refunddata['amount'] = $entryfee;
                        $refunddata['joinid'] = $val3->joinid;
                        $refunddata['challengeid'] = $val3->challengeid;
                        $refunddata['reason'] = 'Challenge canceled';
                        $refunddata['matchkey'] = $val3->matchkey;
                        $transaction_id = (Helpers::settings()->short_name ?? '') . '-REFUND-' . rand(100, 999) . time() . '-' . $val3->user_id;
                        $refunddata['transaction_id'] = $transaction_id;
                        DB::connection('mysql2')->table('refunds')->insert($refunddata);
                        // end entry in refund data//
                        //transactions//
                        $registeruserdetails = DB::table('registerusers')->where('id', $val3->user_id)->first();
                        $datatr['transaction_id'] = $transaction_id;
                        $datatr['type'] = 'Refund amount';
                        $datatr['transaction_by'] = Helpers::settings()->short_name ?? '';
                        $datatr['amount'] = $entryfee;
                        $datatr['paymentstatus'] = 'confirmed';
                        $datatr['challengeid'] = $val3->challengeid;
                        $datatr['bonus_amt'] = $val3->bonus;
                        $datatr['win_amt'] = $val3->winning;
                        $datatr['addfund_amt'] = $val3->balance;
                        $datatr['referral_amt'] = $val3->referral_income;
                        $datatr['bal_bonus_amt'] = $dataq['bonus'];
                        $datatr['bal_win_amt'] = $dataq['winning'];
                        $datatr['bal_fund_amt'] = $dataq['balance'];
                        $datatr['bal_referral_amt'] = $dataq['referral_income'];
                        $datatr['userid'] = $val3->user_id;
                        $datatr['total_available_amt'] = $dataq['balance'] + $dataq['winning'] + $dataq['bonus'] + $dataq['referral_income'];
                        DB::connection('mysql2')->table('transactions')->insert($datatr);
                        //notifications//
                        $datan['title'] = 'Refund Amount of Rs.' . $val2->entryfee . ' for challenge cancellation';
                        $datan['userid'] = $val3->user_id;
                        DB::connection('mysql2')->table('notifications')->insert($datan);

                        $titleget = 'Refund Amount!';
                        Helpers::sendnotification($titleget, $datan['title'], '', $val3->user_id);

                    }
                }
            }
        }
        $dasts['status'] = 'canceled';
        DB::connection('mysql2')->table('matchchallenges')->where('id', $val2->id)->update($dasts);
        return redirect::back()->with('success', 'Custom Contest Successfully Canceled');
    }

    public function view_search_contest_category(Request $request)
    {
        $get_data = $request->all();
        if (!empty($get_data['name']) or !empty(request()->get('fantasy_type'))) {
            $name = $get_data['name'];

            $f_type = request()->get('fantasy_type');

            $f_type = !empty($f_type) ? $f_type : 'Cricket';

            $contest_data = DB::table('contest_category')->where('name', 'LIKE', '%' . $name . '%')->where('fantasy_type', $f_type)->paginate('10');
        } else {
            $contest_data = DB::table('contest_category')->select('*')->paginate('10');
        }
        return view('contest.view_contest_category', compact('contest_data'));
    }

    public function freezContest($id, $status)
    {
        $id = unserialize(base64_decode($id));
        $data['freez'] = $status;
        $challegeid = DB::table('challenges')->where('id', $id)->first();
        if (!empty($challegeid)) {
            DB::connection('mysql2')->table('challenges')->where('id', $id)->update($data);
        }
        return redirect::back()->with('success', 'Status Change Successfully');
    }

    public function privatecontest()
    {

        $f_type = request()->get('fantasy_type');
        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        if ($f_type == '') {
            Auth::logout();
            Session::flush();
            return redirect('login');
        }
        date_default_timezone_set('Asia/Kolkata');
        $allchallenges = array();
        if (request()->has('matchid')) {
            $matchid = request('matchid');
            if ($matchid != "") {
                $allchallenges = DB::table('matchchallenges')->where('matchkey', $matchid)
                    ->where('fantasy_type', $f_type)
                    ->get();
            }
        }
        $currentdate = date('Y-m-d h:i:s');
        $findalllistmatches = DB::table('listmatches')->select('name', 'matchkey')
            ->where('fantasy_type', $f_type)
            ->Where('launch_status', 'launched')
            ->Where('status', '!=', 'completed')
            ->where('start_date', '>=', $currentdate)
            ->orderBY('start_date', 'ASC')
            ->get();

        $alluser = DB::table('registerusers')->where('type', 'youtuber')->select('team', 'id', 'mobile')->get();
        return view('contest.privatecontest', compact('findalllistmatches', 'alluser'));
    }

    public function submitprivatecontest(Request $request)
    {

        $f_type = request()->get('fantasy_type');
        $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $rules = array(
            'entryfee' => 'required',
            'win_amount' => 'required',
            'contest_type' => 'required',
            'contest_cat' => 'required',
        );
        date_default_timezone_set('Asia/Kolkata');
        $currentdate = date('Y-m-d h:i:s');
        $findalllistmatches = DB::table('listmatches')->select('name', 'matchkey')
            ->Where('launch_status', 'launched')
            ->where('fantasy_type', $f_type)
            ->where('start_date', '>=', $currentdate)
            ->orderBY('start_date', 'ASC')->get();
        if ($request->isMethod('post')) {
            $input = request()->all();

            if (isset($input['maximum_user'])) {
                if ($input['maximum_user'] < 2 || empty($input['maximum_user'])) {
                    return redirect()->back()->with('danger', 'Value of maximum user not less than 2...');
                }
            }
            if (isset($input['winning_percentage'])) {
                if ($input['winning_percentage'] == 0) {
                    return redirect()->back()->with('danger', 'Value of winning percentage not equal to 0...');
                }
            }

            if (isset($input['bonus_percentage'])) {
                if ($input['bonus_percentage'] == 0) {
                    return redirect()->back()->with('Value of bonus percentage not equal to 0...');
                }
            }
            if (!isset($input['maximum_user'])) {
                $input['maximum_user'] = 0;
            }
            if (!isset($input['winning_percentage'])) {
                $input['winning_percentage'] = 0;
            }
            if ($input['contest_type'] == 'Percentage') {
                $input['maximum_user'] = '0';
                $input['pricecard_type'] = '0';
            }
            if ($input['contest_type'] == 'Amount') {
                $input['winning_percentage'] = '0';
            }
            unset($input['_token']);
            if (isset($input['multientry_limit'])) {
                if ($input['multientry_limit'] == 0) {
                    return redirect()->back()->with('danger', 'Value of multientry limit not equal to 0...');
                } else {
                    $data['multi_entry'] = 1;
                }
            }
            unset($input['_token']);
            if (isset($input['maximum_user'])) {
                $data['maximum_user'] = $input['maximum_user'];
            }

            if (isset($input['userid'])) {
                if (!empty($input['userid'])) {
                    $data['created_by'] = $input['userid'];
                    $data['is_private'] = 1;
                }
            }
            if (isset($input['winning_percentage'])) {
                $data['winning_percentage'] = $input['winning_percentage'];
            }
            if (isset($input['confirmed_challenge'])) {
                $data['confirmed_challenge'] = 1;
            }
            if (isset($input['is_running'])) {
                $data['is_running'] = 1;
            }
            if (isset($input['is_bonus'])) {
                $data['is_bonus'] = 1;
                $data['bonus_percentage'] = $input['bonus_percentage'];
            }
            if (isset($input['multi_entry'])) {
                $data['multi_entry'] = 1;
                $data['multi_entry'] = $input['multi_entry'];
            }

            $userss = DB::table('registerusers')->where('id', $input['userid'])->select('team')->first();
            $joindata = array();

            $joindata['refercode'] = (Helpers::settings()->short_name ?? '') . '$-' . $input['refercode'];

            $exi = DB::table('joinedrefer')->where('matchkey', $input['matchkey'])->where('refercode', $joindata['refercode'])->first();
            if (!empty($exi)) {
                return redirect()->back()->with('danger', 'Refer code is already available for this match!');
            }
            $data['contest_type'] = $input['contest_type'];
            $data['pricecard_type'] = $input['pricecard_type'];
            $data['entryfee'] = $input['entryfee'];
            $data['offerentryfee'] = $input['offerentryfee'];
            $data['win_amount'] = $input['win_amount'];
            $data['matchkey'] = $input['matchkey'];
            $data['status'] = 'opened';
            $data['contest_cat'] = 0;
            $data['contest_name'] = $userss->team;
            $data['fantasy_type'] = $f_type;

            $count = 0;
            $count++;
            $getid = DB::connection('mysql2')->table('matchchallenges')->insertGetId($data);

            $joindata['challengeid'] = $getid;
            $joindata['matchkey'] = $input['matchkey'];

            DB::connection('mysql2')->table('joinedrefer')->insert($joindata);

            if ($input['contest_type'] == "Amount") {
                return redirect()->action('ContestController@addmatchpricecard', base64_encode(serialize($getid)));
            } else {
                return redirect()->back()->with('success', 'Successfully created contest!');
            }
        }

    }

    public function pricontest()
    {

        $f_type = request()->get('fantasy_type');
        if ($f_type == '') {
            $f_type = 'Cricket';
        }
        date_default_timezone_set('Asia/Kolkata');
        $allchallenges = array();
        if (request()->has('matchid')) {
            $matchid = request('matchid');
            if ($matchid != "") {
                $allchallenges = DB::table('joinedrefer')->join('matchchallenges', 'matchchallenges.id', 'joinedrefer.challengeid')->where('joinedrefer.matchkey', $matchid)
                    ->where('matchchallenges.fantasy_type', $f_type)->select('matchchallenges.*', 'joinedrefer.refercode as referc')
                    ->get();
            }
        }
        $currentdate = date('Y-m-d h:i:s');
        $findalllistmatches = DB::table('listmatches')->select('name', 'matchkey')
            ->where('fantasy_type', $f_type)
            ->Where('launch_status', 'launched')
            ->Where('status', '!=', 'completed')
            ->where('start_date', '>=', $currentdate)
            ->orderBY('start_date', 'ASC')
            ->get();
        return view('contest.viewprivatecontest', compact('findalllistmatches', 'allchallenges'));

    }

    public function makeConfirmed($id)
    {
        $id = unserialize(base64_decode($id));
        $data = array();
        $data['confirmed_challenge'] = 1;
        DB::connection('mysql2')->table('matchchallenges')->where('id', $id)->update($data);
        return redirect()->back()->with('success', 'Successfully confirmed contest!');
    }
    //Select Global contest
    public function selectglobalcontest(Request $request, $mid)
    {
        return view('contest.selectglobal_contest', compact('mid'));
    }

    public function selectglobcontest_datatable(Request $request)
    {
        $f_type = request()->get('fantasy_type');
        // $f_type = !empty($f_type) ? $f_type : 'Cricket';

        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'contest_cat',
            3 => 'entryfee',
            4 => 'win_amount',
            5 => 'maximum_user',
            6 => 'multi_entry',
            7 => 'is_running',
            8 => 'confirmed_challenge',
            9 => 'is_bonus',
            10 => 'cat_type',
        );

        $totalTitles = DB::table('challenges')->count();
        $totalFiltered = $totalTitles;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $titles = DB::table('challenges')->join('contest_category', 'contest_category.id', '=', 'challenges.contest_cat')->select('challenges.*', 'contest_category.name as cat_name')->offset($start)
                // ->where('challenges.fantasy_type', $f_type)
                ->limit($limit)->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $titles = DB::table('challenges')->where('entryfee', 'LIKE', "%{$search}%")
                // ->where('fantasy_type', $f_type)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = DB::table('challenges')->Where('entryfee', 'LIKE', "%{$search}%")
                // ->where('fantasy_type', $f_type)
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

                $nestedData['s_no'] = $Data11;
                $nestedData['id'] = $count;
                $nestedData['cat'] = $title->cat_name;
                $nestedData['entryfee'] = '₹ ' . $title->entryfee;
                $nestedData['win_amount'] = '₹ ' . $title->win_amount;
                $nestedData['maximum_user'] = $title->maximum_user;
                $nestedData['multi_entry'] = $resultdata;
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

    public function multiselect_globalcat(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->get('mid');
            date_default_timezone_set('Asia/Kolkata');
            $findmatch = DB::table('listmatches')->where('matchkey', $id)->first();
            if (!empty($findmatch)) {
                // $findleauges = Challenges::get();
                $values = $request->get('hg_cart');
                // $globid = explode(',',$values);
                $charray = array();
                if (!empty($values)) {
                    foreach ($values as $globids) {
                        $leauge = DB::table('challenges')->where('id', $globids)->first();
                        $findchallengeexist = DB::table('matchchallenges')->where('matchkey', $id)->where('challenge_id', $leauge->id)->first();
                        if (empty($findchallengeexist)) {
                            $data['challenge_id'] = $leauge->id;
                            $data['contest_cat'] = $leauge->contest_cat;
                            $data['contest_type'] = $leauge->contest_type;
                            $data['fantasy_type'] = $leauge->fantasy_type;
                            $data['duotype'] = $leauge->duotype;
                            $data['c_type'] = $leauge->c_type;
                            $data['winning_percentage'] = $leauge->winning_percentage;
                            $data['is_bonus'] = $leauge->is_bonus;
                            $data['bonus_percentage'] = $leauge->bonus_percentage;
                            $data['pricecard_type'] = $leauge->pricecard_type;
                            $data['entryfee'] = $leauge->entryfee;
                            $data['offerentryfee'] = $leauge->offerentryfee;
                            $data['win_amount'] = $leauge->win_amount;
                            $data['maximum_user'] = $leauge->maximum_user;
                            $data['status'] = 'opened';
                            $data['confirmed_challenge'] = $leauge->confirmed_challenge;
                            $data['is_running'] = $leauge->is_running;
                            $data['multi_entry'] = $leauge->multi_entry;
                            $data['team_limit'] = $leauge->team_limit;
                            $data['matchkey'] = $id;
                            $data['created_at'] = date('Y-m-d h:i:s');
                            $data['updated_at'] = date('Y-m-d h:i:s');

                            $getcid = DB::table('matchchallenges')->insertGetId($data);

                            $findpricecrads = DB::table('pricecards')->where('challenge_id', $leauge->id)->get();
                            if (!empty($findpricecrads)) {
                                foreach ($findpricecrads as $pricec) {
                                    $pdata['challenge_id'] = $getcid;
                                    $pdata['matchkey'] = $id;
                                    $pdata['winners'] = $pricec->winners;
                                    $pdata['type'] = $pricec->type;
                                    if (!empty($pricec->price)) {
                                        $pdata['price'] = $pricec->price;
                                    } else {
                                        $pdata['price_percent'] = $pricec->price_percent;
                                        $pdata['price'] = 0;
                                    }
                                    $pdata['min_position'] = $pricec->min_position;
                                    $pdata['max_position'] = $pricec->max_position;
                                    $pdata['total'] = $pricec->total;
                                    $pdata['created_at'] = date('Y-m-d h:i:s');
                                    $pdata['updated_at'] = date('Y-m-d h:i:s');
                                    DB::table('matchpricecards')->insert($pdata);
                                }
                            }
                        }
                    }
                }
                // return redirect()->back()->with('success',' Challenges imported successfully');
                return 1;die;
            } else {
                // return redirect()->action('ContestController@createchallenge')->with('danger','Invalid match Provided');
                return 3;die;
            }
        }
        return 2;die;
    }
}
