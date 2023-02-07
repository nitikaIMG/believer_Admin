<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;


use DB;
use Session;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GeneralTabsController extends Controller
{
	
    public function index(Request $request){
    	
		if($request->isMethod('post')){
		    $input = $request->all();
		    $rules = array(
                'type' => 'required',
                'amount' => 'required',
            );
            $validator = Validator::make($input,$rules);
            if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
                }
			$type = $input['type'];
			$data = DB::table('general_tabs')->where('type',$type)->first();
	    	unset($input['_token']);
			if(!empty($data)){
				DB::connection('mysql2')->table('general_tabs')->where('type',$type)->update($input);
				return redirect()->back()->with('success','updated successfully!');
			}else{
		DB::connection('mysql2')->table('general_tabs')->insert($input);
		return redirect()->back()->with('successfully','added successfully!');
		}
    }
   	$dataa = DB::table('general_tabs')->get();
    return view('general_tabs.view',compact('dataa'));
    }

    public function delete($id){
    	DB::connection('mysql2')->table('general_tabs')->where('id',$id)->delete();
    	Session::flash('message', 'Deleted Successfully!');
		Session::flash('alert-class', 'alert-success');
		return redirect()->back();
		}
		
		public function viewrefer(Request $request){
		    $input = $request->all();
    		if($request->isMethod('post')){
    		    $rules = array(
                'code' => 'required',
                'bonus' => 'required',
                'start_date' => 'required',
                'expire_date' => 'required',
                );
                $validator = Validator::make($input,$rules);
                if($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                $current=date('Y-m-d');
                $start=$request->start_date;
                $end=$request->expire_date;
                if($end<$current){
                    return redirect()->back()->with('danger','End Date Should be after today.');
                }
                if($end<$start){
                    return redirect()->back()->with('danger','End Date Should be after start date.');
                }
    			$code = $input['code'];
    			$data = DB::table('special_refer')->where('code',$code)->first();
    	    	unset($input['_token']);
    			if(!empty($data)){
    				DB::connection('mysql2')->table('special_refer')->where('code',$code)->update($input);
    				Session::flash('message', 'Updated Successfully!');
    				Session::flash('alert-class', 'alert-success');
    				return redirect()->back();
    			}else{
    		DB::connection('mysql2')->table('special_refer')->insert($input);
    		Session::flash('message', 'Added Successfully!');
    		Session::flash('alert-class', 'alert-success');
    		return redirect()->back();
    		}
        }
       	$dataa = DB::table('special_refer')->get();
        return view('general_tabs.viewrefer',compact('dataa'));
		}
		
		
    public function deleterefer($id){
    	DB::connection('mysql2')->table('special_refer')->where('id',$id)->delete();
    	Session::flash('message', 'Deleted Successfully!');
		Session::flash('alert-class', 'alert-success');
		return redirect()->back();
		}
		
}