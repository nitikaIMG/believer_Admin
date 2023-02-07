<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helpers; 
use App\Http\Requests;
use Redirect;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
 
use DB;
use Session;

class FaqController extends Controller
{
    
    // to create the new faq //
    public function create_faq(Request $request){
        $dataaa = DB::table('faq')->first();
    	if($request->isMethod('post')){
         $this->validate($request,[
             'question' => 'required',
             'answer'   =>  'required' 
        ]);
            $data['question']=$request->question;
            $data['ans']=$request->answer;
            
		    DB::connection('mysql2')->table('faq')->insert($data);
           
            return redirect()->back()->with('success','Faq Added Successfully');
    	}else{
    	    return view('faq.create_faq',compact('dataaa'));
    	}
    }
     // to display all the faq //
    public function index(){
        return view('faq.view_faq');
    }
    // for the datatable of all the faq //
    public function faq_datatable(Request $request){
        $columns = array(
            0 => 'id',
            1 => 'question',
            2 => 'created_at',
            3 => 'updated_at',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query = DB::table('faq');
        $titles = $query->select('id','question','created_at','updated_at')->offset($start) 
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {
                 $confirm= "return confirm('Are you sure you want to delete this data?')";
                $edit =action('FaqController@edit_faq',base64_encode(serialize($title->id)));
                $delete =action('FaqController@deletefaq',base64_encode(serialize($title->id)));

              // $nestedData['s_no'] = '<input type="checkbox" name="checkCat" class="checkbox" id="check" value="'.$title->id.'">'; 
              $onclick = "delete_sweet_alert('".$delete."', 'Are you sure you want to delete this data?')";

               $nestedData['s_no']= $count;
                 $nestedData['action'] = '<div class="btn-group dropdown">
                <button class="btn btn-primary btn-active-pink dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button" aria-expanded="true">
                    Action <i class="dropdown-caret"></i>
                </button>
                <ul class="dropdown-menu" style="opacity: 1;">
                    <li><a class="dropdown-item waves-light waves-effect" href="'.$edit.'">Edit</a></li>
                    <li> <a class="dropdown-item waves-light waves-effect"  onclick="'.$onclick.'">Delete</a></li>
                </ul>
            </div>';
                $nestedData['question'] = $title->question;
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
    //to edit the faq //
    public function edit_faq(Request $request,$id){
      
        date_default_timezone_set('Asia/Kolkata');
        $id = unserialize(base64_decode($id));

        if($request->isMethod('post')){
            $f = $request->all();
            
             unset($f['_token']);
            DB::connection('mysql2')->table('faq')->where('id','=',$id)->update($f);
            return redirect()->back()->with('success','Faq Edited Successfully');
        }else{
         $data = DB::table('faq')->where('id','=',$id)->first();
        return view('faq.edit_faq',compact('data'));
        }
    }
    
    // to delete the faq //
    public function deletefaq($gid){
        $id = unserialize(base64_decode($gid));
        $faq = DB::table('faq')->where('id',$id)->first();
        if(!empty($faq)){
			 DB::connection('mysql2')->table('faq')->where('id',$id)->delete();
			return redirect()->action('FaqController@index')->with('success','
				Faq has been deleted successfully');
        }
        else{
            return redirect()->action('FaqController@index')->with('danger','Invalid Id Provided');
        }
    }
    public function muldelete(Request $request){
   if ($request->isMethod('post')){
     $values = $request->input('hg_cart');
     $final = explode(',',$values);
     foreach($final as $id){
       $series = DB::table('series')->where('id',$id)->first();
         if(!empty($series)){
             DB::connection('mysql2')->table('series')->where('id',$id)->delete();
          }
         
         }
        echo 1; die;
         }
       echo 2; die;
    }
    
    public function getallfaq(Request $request){
        $Json=array();
        $user=Helpers::isAuthorize($request);
        if(!empty($user)){
            $i =0;
            $allfaq = DB::table('faq')->first();
            if(!empty($allfaq)){
                $Json[0]['question']=$allfaq->question;
                $i++;
            }
            return response()->json($Json);
        }
    }
}
