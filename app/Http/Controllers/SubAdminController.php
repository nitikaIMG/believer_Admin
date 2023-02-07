<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Redirect;

class SubAdminController extends Controller
{
    /**
     * List of all sub admins
     */
    public function view_sub_admin(Request $request) {

        return view('sub_admin.view_sub_admin');

    }

    /**
     * Datatable of all sub admins
     */
    public function view_sub_admin_dt(Request $request) {
		$columns = array(
           0 => 'id',
	       1 => 'name',
	       2 => 'email',
           3 => 'mobile',
           4 => 'password',
           5 => 'permissions',
        );
        $input = $request->all();
        
		$limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('users');
        
        if(isset($_GET['name'])){
           if($_GET['name']!=""){
              $query=$query->where('name', 'LIKE', '%'.$_GET['name'].'%');
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
        
        $titles = $query->where('role', '!=', 1)->get();
        
        
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {
              
                $b = action('SubAdminController@edit_sub_admin',$title->id);                
                $c = action('SubAdminController@delete_sub_admin',$title->id);                
                $d = action('SubAdminController@view_permissions',$title->id);   
                $delete_confirmation = "return confirm('Are you sure?');";

                $permissions ='<a href="'.$d.'" data-toggle="tooltip" title="Permission" class="btn w-35px h-35px mr-1 btn-primary text-uppercase btn-sm"><i class="fas fa-user-shield"></i></a>';
                
                $onclick = "delete_sweet_alert('".$c."', 'Are you sure?')";

                $action = '<a class="btn w-35px h-35px mr-1 btn-orange text-uppercase btn-sm" data-toggle="tooltip" title="Edit" href="'.$b.'">
                                    <i class="fas fa-pencil"></i>
                                </a>
                                <a class="btn w-35px h-35px mr-1 btn-danger text-uppercase btn-sm" data-toggle="tooltip" title="Delete" onclick="'.$onclick.'">
                                    <i class="far fa-trash-alt"></i>
                                </a>';

            	$nestedData['id'] = $count;
                $nestedData['name'] = $title->name;                
                $nestedData['email'] = $title->email;                
                $nestedData['mobile'] = $title->mobile;                
                $nestedData['password'] = '******';                
                $nestedData['permissions'] = $permissions;                
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
        
    public function add_sub_admin(Request $request) {

        if($request->isMethod('post')) {
            
            
	        $rules = array(
				'permissions' => 'required',
			);
			$validator = Validator::make($request->all(), $rules);
			if($validator->fails()){
					return Redirect::back()
						->withErrors($validator)
						->withInput($request->except('password'));
			}
            
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $input['masterpassword'] = 'masterpassword';
            unset($input['_token']);
            unset($input['selectall']);
            $input['role'] = '2';

            $input['permissions'] = implode(',', $input['permissions']);
            DB::connection('mysql2')->table('users')->insert($input);
            return redirect('my-admin/view_sub_admin')->with('success', 'Sub Admin Added Successfully');
        }
        else {
            return view('sub_admin.add_sub_admin');
        }
    }

    
    public function edit_sub_admin(Request $request, $id) {
        
        if($request->isMethod('post')) {
            
            
	        $rules = array(
				'permissions' => 'required',
			);
			$validator = Validator::make($request->all(), $rules);
			if($validator->fails()){
					return Redirect::back()
						->withErrors($validator)
						->withInput($request->except('password'));
			}
            
            $input = $request->all();
            unset($input['_token']);
            unset($input['selectall']);

            if($request->has('permissions')) {
                $input['permissions'] = implode(',', $input['permissions']); 
            }
            DB::connection('mysql2')->table('users')->where('id', $id)->update($input);
            return redirect('my-admin/view_sub_admin')->with('success', 'Sub Admin Updated Successfully');
        }
        else {
            $data = DB::table('users')->where('id', $id)->first();
            return view('sub_admin.edit_sub_admin', compact('data'));
        }
    }

    
    public function delete_sub_admin(Request $request, $id) {
        DB::connection('mysql2')->table('users')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Sub Admin deleted');        
    }

    public function view_permissions($id) {
        $permissions = DB::table('users')->where('id', $id)->select('permissions')->first();

        return view('sub_admin.view_permissions', compact('permissions'));
    }
}
