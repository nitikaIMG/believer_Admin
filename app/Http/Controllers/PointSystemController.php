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
use Hash;
use App\PointSystem;

class PointSystemController extends Controller
{
    
    public function point_system() {
        $all_point_system = PointSystem::groupBy('format')
                                    ->select('format', 'fantasy_type')
                                    ->get();
        
        return view('point_system.point_system', compact('all_point_system'));
    }
    
    public function update_point_system(Request $request){
		if ($request->isMethod('post')){
			$input =$request->all();
			$fantasy_type = $input['fantasy_type'];
			$format = $input['format'];
			$type = $input['type'];
			$field = $input['field'];
			$point = $input['point'];
			$from = $input['from'];
			$to = $input['to'];
			$below = $input['below'];
			$above = $input['above'];
			
			if(
			    !empty($from) and !empty($to)
			 ) {
			     
			    $data['point'] = $point;
			    
    			DB::connection('mysql2')->table('point_system')
    			    ->where('fantasy_type',$fantasy_type)
    			    ->where('format',$format)
    			    ->where('type',$type)
    			    ->where('from',$from)
    			    ->where('to',$to)
    			    ->update($data);
			     
			 } else if(
			     !empty($below)
			 ) {
			     
			    $data['point'] = $point;
			     
    			DB::connection('mysql2')->table('point_system')
    			    ->where('fantasy_type',$fantasy_type)
    			    ->where('format',$format)
    			    ->where('below',$below)
    			    ->update($data);
			     
			 } else if(
			     !empty($above)
			 ) {
			     
			    $data['point'] = $point;
			     
    			DB::connection('mysql2')->table('point_system')
    			    ->where('fantasy_type',$fantasy_type)
    			    ->where('format',$format)
    			    ->where('above',$above)
    			    ->update($data);
			     
			 } else {
    			     
        		$data[$field] = $point;
    			
    			DB::connection('mysql2')->table('point_system')
    			    ->where('fantasy_type',$fantasy_type)
    			    ->where('format',$format)
    			    ->where('type',$type)
    			    ->update($data);
			 }
			  
			echo 1;
			die;
		}
	}
    
}
