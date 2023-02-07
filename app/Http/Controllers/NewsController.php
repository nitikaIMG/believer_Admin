<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Str;
use File;
use Session;

use App\Helpers\Helpers;

use Illuminate\Support\Facades\Storage;
class NewsController extends Controller
{
	
    public function news(){
    	return view('news.add');
	}

	public function add_news(Request $request){
		$this->validate($request,[
	    	'type' => 'required',
	    	'title' => 'required',
	    	'image' => 'required|image',
	    ]);
		$input = $request->all();
		if($request->file('image')){
			$image =  Storage::disk('public_folder')->putFile('images_news',$input['image'], 'public');
        	$input['image'] = $image;
		}
		
		DB::connection('mysql2')->table('news')->insert([
			'title'=>$request->title,
			'slug'=>Str::slug($request->title),
			'type'=>$request->type,
			'description'=>$request->description,
			'image'=>$image,
		]);
		return redirect()->action('NewsController@news')->with('success','News save successfully');
	}

	public function edit_news($id)
	{
		$getid = unserialize(base64_decode($id));
		$sidebanner = DB::table('news')->where('id',$getid)->first();
		return view('news.edit',compact('sidebanner'));
	}

	public function update_news(Request $request,$id)
	{
		$id = unserialize(base64_decode($id));
		$this->validate($request,[
	       'type' => 'required',
	       'title' => 'required',
	    ]);
		$getid = $id;
		$input = $request->all();
		if($request->file('image')){
            $image = $request->file('image');
            $hii =  Storage::disk('public_folder')->putFile('images_news',$input['image'], 'public');
            $data['image']  = $hii;
            // delete old image
            $oldimage = DB::table('news')->where('id',$id)->first();
            if(!empty($oldimage)){
	            $filename= $oldimage->image;
	            Storage::disk('public_folder')->delete($filename);
	    	}
    	}
		$data['type'] = $input['type'];
		$data['title'] = $input['title'];
		$data['description'] = $input['description'];

		DB::connection('mysql2')->table('news')->where('id',$getid)->update($data);
		$id = base64_encode(serialize($id));
		return redirect()->action('NewsController@edit_news',$id)->with('success','News update successfully');

}

	public function view_news(){
		return view('news.view');
	}

	public function view_news_table(){
		$data = DB::table('news')->orderBy('id','desc')->get();

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
				$edit =action('NewsController@edit_news',$id);
				$delete =action('NewsController@delete_news',$id);

				$onclick = "delete_sweet_alert('".$delete."', 'Are you sure you want to delete this data?')";


                $app='<a class="btn btn-sm text-uppercase mr-2 btn-danger w-35px h-35px"  onclick="'.$onclick.'" title="Delete"><i class="fal fa-trash-alt"></i></a>';
                $data=array(
                    $i,
					$fmatch->title,
                    ucwords($fmatch->type),

                    $img,
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

	public function delete_news($id){
	    $getid = unserialize(base64_decode($id));
		$oldimage = DB::table('news')->where('id',$getid)->first();
		$filename= $oldimage->image;
		$filenamep = public_path().'/images_news/'.$filename;
		File::delete($filenamep);

		DB::connection('mysql2')->table('news')->where('id',$getid)->delete();
		return redirect()->action('NewsController@view_news')->with('success','News delete successfully');
	}
}
