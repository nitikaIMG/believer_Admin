<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Redirect;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Session;
use DB;

class RegionController extends Controller
{
    
    // to create the new region //
    
    
    public function create(Request $request){
        
        date_default_timezone_set('Asia/Kolkata');
            if($request->isMethod('post')){
            $data['region']=strip_tags($request->region);
    		DB::connection('mysql2')->table('region')->insert($data);
            return redirect()->back()->with('success','Region Added Successfully');
    	}else{
    	return view('verify.add_region');
    	}
    }
    
     // to display all the region //
    public function index(){
        return view('verify.view_region');
    }
    // for the datatable of all the region //
    public function region_datatable(Request $request){
        
        date_default_timezone_set('Asia/Kolkata');
        $columns = array(
            0 => 'id',
            1 => 'region',
            2 => 'region',
            3 => 'updated_at',

        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query = DB::table('region');
        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        $titles = $query->where('fantasy_type',$f_type)->select('*')
                ->offset($start) 
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
       
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {
                $delete =action('RegionController@delete',base64_encode(serialize($title->id)));

                $onclick = "delete_sweet_alert('".$delete."', 'Are you sure you want to delete this data?')";

                $nestedData['id'] = $count;
                $nestedData['action'] = '<div class="btn-group dropdown">
                <button class="btn btn-primary btn-active-pink dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button" aria-expanded="true" style="padding: 6px 7px;">
                    Action <i class="dropdown-caret"></i>
                </button>
                <ul class="dropdown-menu" style="opacity: 1;min-width:11rem;">
                    <li><a class="dropdown-item waves-light waves-effect"  onclick="'.$onclick.'">Delete</a></li>
                   
                </ul>
            </div>';
                $nestedData['region'] = $title->region;
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
    // to update the region delete //
    public function delete($id){
        date_default_timezone_set('Asia/Kolkata');
        $id = unserialize(base64_decode($id));
        $series = DB::connection('mysql2')->table('region')->where('id',$id)->delete();
        return redirect()->back()->with('success','Region Deleted Successfully');
    }  
}
