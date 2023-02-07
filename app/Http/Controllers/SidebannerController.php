<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;

use File;
use Session;

use App\Helpers\Helpers;

use Illuminate\Support\Facades\Storage;
class SidebannerController extends Controller
{
	
    public function sidebanner(){
    	$matches = DB::table('listmatches')->where('launch_status','launched')->where('status','notstarted')->where('start_date','>=',date('Y-m-d H:i:s'))->get(['matchkey','name']);
		return view('sidebanner.add',compact('matches'));
	}

	public function add_sidebanner(Request $request){
		$this->validate($request,[
	    	'type' => 'required',
	    	'image' => 'required|image',
	    ]);
		$input = $request->all();
		if($request->file('image')){
			$image =  Storage::disk('public_folder')->putFile('images_sidebanner',$input['image'], 'public');
        	$input['image'] = $image;
		}
		
		if( $input['type'] == 'others') {
			$input['url'] = $input['url'];
		} else {
			$input['url'] = '';
		}
		if(isset($input['match'])){
			$data['matchkey'] = $input['match'];
		}
		unset($input['_token'],$input['match']);
		DB::connection('mysql2')->table('sidebanner')->insert($input);
		return redirect()->action('SidebannerController@sidebanner')->with('success','Side banner save successfully');
	}

	public function edit_sidebanner($id)
	{
		$getid = unserialize(base64_decode($id));
		$sidebanner = DB::table('sidebanner')->where('id',$getid)->first();
		$matches = DB::table('listmatches')->where('launch_status','launched')->where('status','notstarted')->where('start_date','>=',date('Y-m-d H:i:s'))->get(['matchkey','name']);
		return view('sidebanner.edit',compact('sidebanner','matches'));
	}

	public function update_sidebanner(Request $request,$id)
	{
		$id = unserialize(base64_decode($id));
		$this->validate($request,[
	       'type' => 'required',
	    ]);
		$getid = $id;
		$input = $request->all();
		if($request->file('image')){
            $image = $request->file('image');
            $hii =  Storage::disk('public_folder')->putFile('images_sidebanner',$input['image'], 'public');
            $data['image']  = $hii;
            // delete old image
            $oldimage = DB::table('sidebanner')->where('id',$id)->first();
            if(!empty($oldimage)){
	            $filename= $oldimage->image;
	            Storage::disk('public_folder')->delete($filename);
	    	}
    	}
		$data['type'] = $input['type'];

		if( $input['type'] == 'others') {
			$data['url'] = $input['url'];
		} else {
			$data['url'] = '';
		}
		if(isset($input['match'])){
			$data['matchkey'] = $input['match'];
		}
		DB::connection('mysql2')->table('sidebanner')->where('id',$getid)->update($data);
		$id = base64_encode(serialize($id));
		return redirect()->action('SidebannerController@edit_sidebanner',$id)->with('success','Side banner update successfully');

}

	public function view_sidebanner(){
		return view('sidebanner.view');
	}

	public function view_sidebanner_table(){
		$data = DB::table('sidebanner')->orderBy('id','desc')->get();

		$i=1;$JsonFinal=array();
        if(!empty($data))
        {
            foreach ($data as $fmatch)
            {
				 $link = Storage::disk('public_folder')->url($fmatch->image);

				$id=base64_encode(serialize($fmatch->id));
				
				$imagelogo = asset('public/'.Helpers::settings()->logo ?? '');
                $default_img = 'this.src="'.$imagelogo.'"';

				$img = "<img src='".$link."' class='w-150px rounded-10 shadow border border-primary' onerror='".$default_img."'>";
				$edit =action('SidebannerController@edit_sidebanner',$id);
				$delete =action('SidebannerController@delete_sidebanner',$id);

				$onclick = "delete_sweet_alert('".$delete."', 'Are you sure you want to delete this data?')";


                $app='<a class="btn btn-sm text-uppercase mr-2 btn-danger w-35px h-35px"  onclick="'.$onclick.'" title="Delete"><i class="fal fa-trash-alt"></i></a>';
                $data=array(
                    $i,
                    ucwords($fmatch->type),

                    $img,
                    $fmatch->url,
                    '<a class="btn btn-sm text-uppercase mr-2 btn-primary w-35px h-35px" title="Edit" href="'.$edit.'"> <i class="fas fa-pencil" aria-hidden="true"></i></a>
                    '.$app.'',

                );
                $i++;
                $JsonFinal[]=$data;
            }
        }

        $jsonFinal1 = json_encode(array('data' => $JsonFinal));
        echo $jsonFinal1;
        die;
	}

	public function delete_sidebanner($id){
	    $getid = unserialize(base64_decode($id));
		$oldimage = DB::table('sidebanner')->where('id',$getid)->first();
		$filename= $oldimage->image;
		$filenamep = public_path().'/images_sidebanner/'.$filename;
		File::delete($filenamep);

		DB::connection('mysql2')->table('sidebanner')->where('id',$getid)->delete();
		return redirect()->action('SidebannerController@view_sidebanner')->with('success','Side Banner delete successfully');
	}
}
