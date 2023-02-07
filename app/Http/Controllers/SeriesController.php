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

class SeriesController extends Controller
{
    public function create(Request $request){
        
        date_default_timezone_set('Asia/Kolkata');
            if($request->isMethod('post')){
            $finds=DB::table('series')->where('name',$request->seriesname)->first();
            if(!empty($finds)){
                return redirect()->back()->with('danger','This series name already exist!');
            }
           $current = date('Y/m/d h:i');
            $start=date('Y/m/d',strtotime($request->startdate));
            $end=date('Y/m/d',strtotime($request->enddate));
            if($end<$current){

                return redirect()->back()->with('danger','End Date Should be after today.');
            }
            if($end<$start){
                    return redirect()->back()->with('danger','End Date Should be after start date.');
                }
            $data['name']=$request->seriesname;
            $data['start_date']=$request->startdate;
            $data['end_date']=$request->enddate;
            $data['has_leaderboard']=$request->has_leaderboard;
            $data['status']='opened';

    		DB::connection('mysql2')->table('series')->insert($data);
            return redirect()->back()->with('success','Series Added Successfully');
    	}else{
    	return view('series.create_series');
    	}
    }

     // to display all the series //
    public function index(){
        return view('series.view_series');
    }

    // for the datatable of all the series //
    public function series_datatable(Request $request){
        $f_type =   'Cricket';
        date_default_timezone_set('Asia/Kolkata');
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'start_date',
            3 => 'end_date',
            4 => 'status',
            5 => 'created_at',
            6 => 'updated_at',
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query = DB::table('series');
         // search for the series name //

        if(isset($_GET['name'])){
           $name=$_GET['name'];
           if($name!=""){
              $query=  $query->where('name', 'LIKE', '%'.$name.'%');
            }
        }
        // search for the start date //
        if(request()->has('start_date')){
            $start_date = request('start_date');
            if($start_date!=""){
                $query =$query->whereDate('start_date', '>=',date('Y-m-d',strtotime($start_date)));
            }
            // search for the end date //
            if(request()->has('end_date')){
                $end_date = request('end_date');
                if($end_date == $start_date){
                    $query =$query->whereDate('start_date', '>=',date('Y-m-d',strtotime($end_date)));
                }
            }
        }
        if(request()->has('end_date')){
            $end_date = request('end_date');
            if($end_date!=""){
                $query =$query->whereDate('end_date', '<=',date('Y-m-d',strtotime($end_date)));
            }
        }

        if(
            $request->input('order.0.column') == 0 and $request->input('order.0.dir') == 'asc'
        ) {
            $query = $query
                    // ->whereIn('is_paid_member', ["request", "yes", "no"])
                    // ->orderBy(DB::raw('FIELD(is_paid_member, "request", "yes", "no")'))
                    ->orderBy('created_at', 'desc');
        } else {
            $query = $query->orderBy($order, $dir);
        }

        $count = $query->count();
        $titles = $query->select('id','name','start_date','end_date','status', 'has_leaderboard')
                ->offset($start)
                ->limit($limit)
                ->get();

        $totalTitles = $count;
        $totalFiltered = $totalTitles;

        if($request->input('order.0.column') == '0' && $request->input('order.0.dir') == 'desc') {
	    	$count = $totalTitles - $start;
	    } else {
	    	$count = $start + 1;
	    }

        if (!empty($titles)) {
            $data = array();


            foreach ($titles as $title) {
                $edit =action('SeriesController@edit',base64_encode(serialize($title->id)));

                $st =action('SeriesController@updateseriesstatus',[base64_encode(serialize($title->id)),'closed']);
                $stt =action('SeriesController@updateseriesstatus',[base64_encode(serialize($title->id)),'opened']);
                
                $delete_confirmation = "return confirm('Are you sure?');";

                $onclick = "delete_sweet_alert('".$st."', 'Are you sure?')";

                if($title->status == 'closed'){
                    $statuss = '<a class="dropdown-item waves-light waves-effect" href="'.$stt.'">Activate Series</a>';
                }else{
                     $statuss = '<a class="dropdown-item waves-light waves-effect" onclick="'.$onclick.'">Inactivate Series</a>';
                }

                $start_date =date('d/m/y',strtotime($title->start_date));
                $end_date =date('d/m/y',strtotime($title->end_date));
                $ths = date('h:',strtotime($title->start_date));
                $the = date('h:',strtotime($title->end_date));

                $tms = date('i',strtotime($title->start_date));
                $tme = date('i',strtotime($title->end_date));


                $nestedData['id'] = $count;
                $nestedData['action'] = '<div class="dropdown">
                <button class="btn btn-sm btn-primary btn-active-pink dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button" aria-expanded="true">
                    Action <i class="dropdown-caret"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item waves-light waves-effect" href="'.$edit.'">Edit</a></li>
                    <li>'.$statuss.'</li>
                </ul>
            </div>';
                $nestedData['name'] = $title->name;
                $nestedData['start_date'] = '<span class="font-weight-bold text-success">'.$start_date.' </span><span class="font-weight-bold text-primary ml-2">'.$ths.'</span><span class="font-weight-bold"></span> <span class="font-weight-bold text-primary ml-1"> '.$tms.'</span><span class="font-weight-bold">min</span>';
                $nestedData['end_date'] = '<span class="font-weight-bold text-success">'.$end_date.' </span><span class="font-weight-bold text-primary ml-2">'.$ths.'</span><span class="font-weight-bold"></span> <span class="font-weight-bold text-primary ml-1"> '.$tms.'</span><span class="font-weight-bold">min</span>';

                $status = ($title->status == 'opened') ? 'text-success' : 'text-danger';

                $nestedData['status'] = '<span class="font-weight-bold '.$status.'">'. $title->status. '</span>';

                $nestedData['has_leaderboard'] = $title->has_leaderboard;

                if($title->has_leaderboard == 'yes'){

                    $a = action('SeriesController@addmatchpricecard',base64_encode(serialize($title->id)));

                    $edit ="<a href='".$a."' class='btn btn-sm btn-info w-35px h-35px text-uppercase' data-toggle='tooltip' title='Add / Edit Price Card'><i class='fas fa-plus'></i></a>";
                }else{
                    $edit = "";
                }
                
                $nestedData['has_leaderboard'] .= '<br/>' . $edit;

                $data[] = $nestedData;

                if( $request->input('order.0.column') == '0' and $request->input('order.0.dir') == 'desc') {
	            	$count--;
	            } else {
	            	$count++;
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
    //to edit the series //
    public function edit(Request $request,$id){
        date_default_timezone_set('Asia/Kolkata');
        $series_id = unserialize(base64_decode($id));

        if($request->isMethod('post')){
            $current=date('Y-m-d');
            $start=$request->startdate;
            $end=$request->enddate;
             if($end<$current){

                return redirect()->back()->with('danger','End Date Should be after today.');
            }
            if($end<$start){
                    return redirect()->back()->with('danger','End Date Should be after start date.');
                }
            $finds=DB::table('series')->where('id','!=',$series_id)->where('name',$request->seriesname)->first();
            if(!empty($finds)){
                return redirect()->back()->with('danger','This series name already exist!');
            }
            $data['name']=$request->seriesname;
            $data['start_date']=$request->startdate;
            $data['end_date']=$request->enddate;
            $data['has_leaderboard']=$request->has_leaderboard;


            $data['updated_at']=date('Y-m-d h:i:s');
            DB::connection('mysql2')->table('series')->where('id','=',$series_id)->update($data);
            return redirect()->back()->with('success','Series Edited Successfully');
        }else{
         $data = DB::table('series')->where('id','=',$series_id)->first();
        return view('series.edit_series',compact('data'));
        }
    }
    // to update the series status //
    public function updateseriesstatus($id,$status){
        date_default_timezone_set('Asia/Kolkata');
        $id = unserialize(base64_decode($id));
        $series = DB::table('series')->where('id',$id)->first();
        if(!empty($series)){
            $data['status'] = $status;
            $data['updated_at']= date('Y-m-d h:i:s');
            DB::connection('mysql2')->table('series')->where('id',$id)->update($data);
        }
        return redirect()->action('SeriesController@index')->with('success','Series is successfully updated');
    }

    /************************ Add Match Price Card ***************/
  public function addmatchpricecard($gid, Request $request){
        date_default_timezone_set('Asia/Kolkata');
        $addcardid = $gid;
        $id = unserialize(base64_decode($gid));
        $totalpriceamounts = DB::table('seriespricecards')->where('series_id',$id)->select(DB::raw('sum(seriespricecards.total) as totalpriceamount'))->get();
        $findallpricecards = DB::table('seriespricecards')->where('series_id',$id)->get();
        $findchallenge = DB::table('series')->where('id',$id)->get();
        $findchallenge1 = DB::table('series')->where('id',$id)->first();
        // $d = $findchallenge1->contest_cat;
        // $matchkey = $findchallenge1->matchkey;
        // $cat = DB::table('contest_category')->where('id',$d)->select('name')->first();

        if(!empty($findchallenge)){
            $min_position=0;
            $totalpriceamount=0;

            if(count($findallpricecards)){

                $findminposition = DB::table('seriespricecards')->where('series_id',$id)->orderBY('id','DESC')->select('max_position')->first();
                $min_position = $findminposition->max_position;
                $totalpriceamount = $totalpriceamounts[0]->totalpriceamount;
            }

            if($request->isMethod('post')){
              $price_check = request()->get('price');
              if(isset($price_check) and $price_check == '0') {
                return redirect()->back()->with('error', 'Amount should be greater than 0');
              }

                $input = request()->all();
                
                if($input['winners'] == 0){
                    return redirect()->back()->with('danger','Number of winner not equal to zero.');
                }

                $input['max_position'] = $input['min_position']+$input['winners'];
                 if(isset($input['price'])){
                  $input['total'] = $input['price']*$input['winners'];
                  $input['type'] ='Amount';
                }
                if(!empty($input['price_percent'])){
                  $percent_amt = ($input['price_percent']/100)*$findchallenge[0]->win_amount;
                  $input['total'] = $percent_amt*$input['winners'];
                  $input['type'] ='Percentage';
                }
                $input['series_id'] = $id;
                // // $input['matchkey'] = $matchkey;
                unset($input['_token']);
                $countamount = $totalpriceamount+$input['total'];

         if(!empty($findchallenge[0]->maximum_user)){
            //   if($input['max_position'] > $findchallenge[0]->maximum_user){
            //     return redirect()->action('SeriesController@addmatchpricecard',$addcardid)->with('danger','You cannot add more winners.');
            //   }
            }else{
               $per = DB::table('seriespricecards')->where('series_id',$id)->select(DB::raw('sum(seriespricecards.total) as totalpriceamount'))->get();
              $aa = $per[0]->totalpriceamount + $input['total'];
            //   if($aa > 100){
            //     return redirect()->action('SeriesController@addmatchpricecard',$addcardid)->with('danger','You cannot add more winners.');
            //     }
              }
              $data['min_position']=$input['min_position'];
              $data['winners']=$input['winners'];
              if(!empty($input['price'])){
                  $data['price']=$input['price'];
              }else{
                  $data['price']=0;
                  $data['price_percent']=$input['price_percent'];
              }
              
              $data['max_position']=$input['max_position'];
              $data['total']=$input['total'];
              $data['type']=$input['type'];
              $data['series_id']=$input['series_id'];
            // //   $data['matchkey']=$input['matchkey'];
              
                DB::connection('mysql2')->table('seriespricecards')->insert($data);
                return redirect()->action('SeriesController@addmatchpricecard',base64_encode(serialize($id)))->with('success','price card added successfully!');
            }
        }else{
            return redirect()->action('SeriesController@create_custom_contest')->with('danger','Invalid match Provided');
        }
        return view('series.addmatchpricecard',compact('findallpricecards','addcardid','min_position','findchallenge1'));
  }

  /************* To delete match price card *****************/
  public function deletematchpricecard($id){
      $id = unserialize(base64_decode($id));
    $findallpricecards = DB::table('seriespricecards')->where('id',$id)->first();
    if(!empty($findallpricecards)){
      DB::connection('mysql2')->table('seriespricecards')->where('id',$id)->delete();
      return Redirect::back()->with('success','Price Card Successfully deleted');
    }else{
      return redirect()->action('SeriesController@addmatchpricecard',$id)->with('error','Invalid match Provided');
    }
  }
}
