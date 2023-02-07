<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Redirect;
use Hash;

class YoutuberController extends Controller
{
    /**
     * List of all Youtubers
     */
    public function view_youtuber(Request $request) {

        return view('youtuber.view_youtuber');

    }

    /**
     * Datatable of all Youtubers
     */
    public function view_youtuber_dt(Request $request) {
		$columns = array(
           0 => 'id',
	       1 => 'username',
	       2 => 'email',
           3 => 'mobile',
           4 => 'password',
           5 => 'refer_code',
           6 => 'mobile',
        );
        $input = $request->all();
        
		$limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('registerusers')->where('type', 'youtuber');
        
        if(isset($_GET['name'])){
           if($_GET['name']!=""){
              $query=$query->where('username', 'LIKE', '%'.$_GET['name'].'%');
            }
        }
        if(isset($_GET['email'])){
           if($_GET['email']!=""){
              $query=$query->where('email', 'LIKE', '%'.$_GET['email'].'%');
            }
        }
        if(isset($_GET['mobile'])){
           if($_GET['mobile']!=""){
              $query=$query->where('mobile', 'LIKE', '%'.$_GET['mobile'].'%');
            }
        }
        
        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        
        $titles = $query->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {
              
                $b = action('YoutuberController@edit_youtuber',$title->id);                
                $c = action('YoutuberController@delete_youtuber',$title->id);       
                $delete_confirmation = "return confirm('Are you sure?');";

                $onclick = "delete_sweet_alert('".$c."', 'Are you sure?')";

                $action = '<a class="btn w-35px h-35px mr-1 btn-orange text-uppercase btn-sm" data-toggle="tooltip" title="Edit" href="'.$b.'">
                                    <i class="fas fa-pencil"></i>
                                </a>
                                <a class="btn w-35px h-35px mr-1 btn-danger text-uppercase btn-sm" data-toggle="tooltip" title="Delete" onclick="'.$onclick.'">
                                    <i class="far fa-trash-alt"></i>
                                </a>';

            	$nestedData['id'] = $count;
                $nestedData['name'] = $title->username;                
                $nestedData['email'] = $title->email;                
                $nestedData['mobile'] = $title->mobile;                
                $nestedData['refer_code'] = $title->refer_code;                
                $nestedData['password'] = $title->decrypted_password;        
                $nestedData['percentage'] = $title->percentage;        

                $total_earned = DB::table('youtuber_bonus')->where('userid', $title->id)->sum('amount');   

                $nestedData['total_earned'] = number_format($total_earned, 2, '.', '');
                $nestedData['action'] = $action;
                
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
        
    public function add_youtuber(Request $request) {

        if($request->isMethod('post')) {
            
            $rules = [
                'email' => 'required|email',
                'mobile' => 'required|digits:10',
                'refer_code' => 'required|unique:registerusers,refer_code',
            ];
            
			$validator = Validator::make($request->all(), $rules);
			if($validator->fails()){
					return Redirect::back()
						->withErrors($validator)
						->withInput($request->except('password'));
			}
            
            $input = $request->all();

            $is_already_exists = DB::table('registerusers')->where('email', $input['email'])->orWhere('mobile', $input['mobile'])->select('id')->first();

	  		$input['type'] = 'youtuber';
            unset($input['_token']);

            $password = $input['password'];
            $input['password'] = bcrypt($input['password']);
            $input['decrypted_password'] = $password;

            if($is_already_exists) {
                
                DB::connection('mysql2')->table('registerusers')->where('id', $is_already_exists->id)->update($input);
                
            } else {

                $input['auth_key'] = md5(Hash::make($input['mobile']));
                $input['status'] = 'activated';
                
                $userid = DB::connection('mysql2')->table('registerusers')->insertGetId($input);
                
                # user balance
                $userbalance['user_id'] = $userid;
                DB::connection('mysql2')->table('userbalance')->insert($userbalance);
                
                # user verify
                $userverify['userid'] = $userid;
                $userverify['email_verify'] = 1;
                $userverify['mobile_verify'] = 1;
                $userverify['emailbonus'] = 1;
                $userverify['mobilebonus'] = 1;
                DB::connection('mysql2')->table('user_verify')->insert($userverify);
            }
            return redirect('my-admin/add_youtuber')->with('success', 'Youtuber Added Successfully');
        }
        else {
            return view('youtuber.add_youtuber');
        }
    }

    
    public function edit_youtuber(Request $request, $id) {
        
        if($request->isMethod('post')) {
            
            $rules = [
                'email' => 'required|email',
                'mobile' => 'required|digits:10',
            ];
            
			$validator = Validator::make($request->all(), $rules);
			if($validator->fails()){
					return Redirect::back()
						->withErrors($validator)
						->withInput($request->except('password'));
			}
            
            $input = $request->all();
            unset($input['_token']);
            
            DB::connection('mysql2')->table('registerusers')->where('id', $id)->update($input);
            return redirect('my-admin/view_youtuber')->with('success', 'Youtuber Updated Successfully');
        }
        else {
            $data = DB::table('registerusers')->where('id', $id)->first();
            return view('youtuber.edit_youtuber', compact('data'));
        }
    }

    
    public function delete_youtuber(Request $request, $id) {
        // DB::connection('mysql2')->table('registerusers')->where('id', $id)->delete();
        
        
        DB::connection('mysql2')->table('registerusers')->where('id', $id)->update(['type' => '']);
        
        return redirect()->back()->with('success', 'Youtuber deleted');        
    }

}
