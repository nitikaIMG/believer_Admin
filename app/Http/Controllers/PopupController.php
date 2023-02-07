<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;

use File;
use Session;

use App\Helpers\Helpers;

class PopupController extends Controller
{
      
    public function add_popup(Request $request){
        if($request->isMethod('post')){
            $input = $request->all();
            if(!empty($input)){
                if($request->file('image')){
    	            $image = $request->file('image');
    	            $destination = public_path().'/'.'/popup_notify/';
    	            $filename = 'popup-'.time();
    	            $input['image'] = Helpers::imageSingleUpload($image,$destination,$filename);	
	            	if($input['image']==''){
	               		return redirect()->back()->with('danger','Invalid extension of file you uploaded. You can only upload image.');
	            	}
	            }
	            
                unset($input['_token']);
                DB::connection('mysql2')->table('popup_notification')->insert($input);
                return redirect()->action('PopupController@popup')->with('success','Popup insert successfully');
            }else{
    		    return redirect()->back()->with('danger','Enter th required field');
            }
        }else{
		    return view('popup_notification.add_popup');
        }
	}
	
	public function popup(Request $request){
		return view('popup_notification.view_popup');
	}
    
    public function view_popup_notification(Request $request){
        $columns = array(
            0 => 'id',
            1 => 'title',
            2 => 'image',
        );

         $totalData =  DB::table('popup_notification')->count();
         $totalFiltered = $totalData; 
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
           $query = DB::table('popup_notification');

        //Searching for Banner Show
            if($request->has('where_to_show')){
              $where_to_show = request('where_to_show');
              if($where_to_show != ""){
                $query = $query->where('where_to_show','LIKE','%'.$where_to_show.'%');
              }
            }
            

              $posts = $query->offset($start)->limit($limit)->orderBy($order,$dir)->get();
            $totalFiltered = $totalData;                
            if (!empty($posts)) {
                $data = array();
                $i = 1;
                foreach ($posts as $post) {

                  $imagelogo = asset(Helpers::settings()->logo ?? '');
                  $default_img = "this.src='".$imagelogo."'";

                  $nestedData['s_no'] = $i;
                  $nestedData['title'] = $post->title;
                  $nestedData['image'] =  '<a href="'.asset('popup_notify/'.$post->image).'" target="_blank"><img src="'.asset('popup_notify/'.$post->image).'" style="heigh:90px; width:100px;" onerror="'.$default_img.'"></a>';   

                  $onclick = "delete_sweet_alert('".asset('/my-admin/delete_popup_notification/'.$post->id)."', 'Are you sure you want to delete this data?')";
                
                  $nestedData['action'] = '<div class="dropdown profile_details_drop">
                                               <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" style="background-color: #767622;border: #767622;">Action
                                               <span class="caret"></span>
                                           </button>
                                           <ul class="dropdown-menu drp-mnu" style="min-width: 125px;">
                                               <li style="padding-left:10px;"><a href="'.action('PopupController@edit_popup_notification',$post->id).'">Edit</a></li><br>
                                               <li style="padding-left:10px;"><a onclick="'.$onclick.'">Delete</a></li>
                                            </ul>
                                        </div>';
                  $data[] = $nestedData;
                  $i++;
                }
              }
            $json_data = array(
                  "draw"            => intval($request->input('draw')),  
                  "recordsTotal"    => intval($totalData),  
                  "recordsFiltered" => intval($totalFiltered), 
                  "data"            => $data   
                );
            echo json_encode($json_data);
    }
    
	public function edit_popup_notification(Request $request,$id){
	    if($request->isMethod('post')){
	        $input = $request->all();
	        if(!empty($input)){
                if($request->file('image')){
    	            $image = $request->file('image');
    	            $destination = public_path().'/'.'/popup_notify/';
    	            $filename = 'popup-'.time();
    	            $input['image'] = Helpers::imageSingleUpload($image,$destination,$filename);	
	            	if($input['image']==''){
	               		return redirect()->back()->with('danger','Invalid extension of file you uploaded. You can only upload image.');
	            	}
	            }
                unset($input['_token']);
                DB::connection('mysql2')->table('popup_notification')->where('id',$id)->update($input);
                return redirect()->action('PopupController@popup')->with('success','Popup insert successfully');
            }else{
    		    return redirect()->back()->with('danger','Enter th required field');
            }
	    }else{
    		$sidebanner = DB::table('popup_notification')->where('id',$id)->first();
    		return view('popup_notification.edit_popup',compact('sidebanner'));
	    }		
	}


	public function delete_popup_notification($id){
		$oldimage = DB::table('popup_notification')->where('id',$id)->first();
		$filename= $oldimage->image;			
		$filenamep = public_path().'/popup_notify/'.$filename;
		File::delete($filenamep);

		DB::connection('mysql2')->table('popup_notification')->where('id',$id)->delete();
		return redirect()->action('PopupController@popup')->with('danger','Popup delete successfully');	
	}
}
