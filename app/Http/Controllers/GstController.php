<?php
namespace App\Http\Controllers;
    use DB;
    use Session;
    use bcrypt;
    use Config;
    use Redirect;
    use File;
    use Auth;
    use Hash;
    use Carbon\Carbon;
    use Illuminate\Http\Request; 
    use App\Http\Controllers\Controller; 
    use App\Http\Requests;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\Input;
    use App\Http\Controllers\CricketapiController;
    use App\Helpers\Helpers;

class GstController extends Controller
{
    
     public function invoicetransaction(request $request){
        $tran_id= $request->get('transaction_id');
        $result= DB::connection('mysql')->table('transactions')->where('transactions.id',$tran_id)->join('registerusers','registerusers.id','=','transactions.userid')->select('transactions.*','registerusers.email','registerusers.username','registerusers.state')->first();
       $Json=array();
       
       if(!empty($result)){
            if($result->type=='Contest Joining Fee'){
                $ttlcollection= DB::connection('mysql')->table('matchchallenges')->where('id',$result->challengeid)->select(DB::raw('sum(entryfee*maximum_user) AS totalcollection'),'win_amount','maximum_user')->first();
                $totalplatformfees=0;$peruserplatformfees=0;$taxablevalue=0;
                if($ttlcollection->totalcollection>0){
                    if($ttlcollection->totalcollection>$ttlcollection->win_amount){
                      $totalplatformfees= $ttlcollection->totalcollection-$ttlcollection->win_amount;
                      if($totalplatformfees>0){
                          $peruserplatformfees= $totalplatformfees/$ttlcollection->maximum_user;
                          $taxablevalue= round($peruserplatformfees/118*18,2);
                      }
                    }
                }
                
                $data= Helpers::generateInvoice($result->email, $result->amount,$result->transaction_id,$peruserplatformfees,$taxablevalue,$result->state);
                $datamessage['email']=$result->email;
                $datamessage['subject']='Here your '.(Helpers::settings()->project_name ?? '').' Invoice';
                $datamessage['content']=$data;
                
                Helpers::mailSmtpSend($datamessage);
               $Json['msg']='Transaction invioce has been sent to your registered email id!';
               $Json['success'] = true;
            }
           
       }else{
            $Json['msg']='Invalid data!';
           $Json['success'] = false;
       }
         
         return response()->json(array($Json));die;
    }

    public function Gstreport(){

            $query = DB::table('listmatches')
                        ->where('listmatches.final_status', 'winnerdeclared')
                        ->join('leaugestransactions','leaugestransactions.matchkey', 'listmatches.matchkey')
                        ->select(DB::raw('sum(leaugestransactions.bonus) as b_amt'), DB::raw('sum(leaugestransactions.winning) as w_amt'), DB::raw('sum(leaugestransactions.balance) as blnc_amt'));
                        
             // search for the series name //
            if(isset($_GET['name'])){
                $name = $_GET['name'];
               if($name!=""){
                  $query->where('listmatches.name', 'LIKE', '%'.$name.'%');
                }
            }
            
            if(request()->has('startdate')){
                
                $start_date = request('startdate');
                if($start_date!=""){
                    $query->whereDate('listmatches.start_date', '>=', date('Y-m-d', strtotime($start_date)));
                }
                
                if(request()->has('enddate')){
                    
                    $end_date = request('enddate');
                    if($end_date == $start_date){
                        $query->whereDate('listmatches.start_date', '>=', date('Y-m-d', strtotime($end_date)));
                    }
                }
            }

            if(request()->has('enddate')){
                
                $end_date = request('enddate');
                if($end_date!=""){
                    $query->whereDate('listmatches.start_date', '<=', date('Y-m-d', strtotime($end_date)));
                }
            }
            
            $titles = $query->groupBy('listmatches.matchkey')->get();
            
            $total_trading = 0;
            
            foreach($titles as $title) {
                
                $total_trading += $title->b_amt + $title->w_amt + $title->blnc_amt;
            
            }
            
            return view('gst.gstreport', compact('total_trading'));
    }

    public function view_match_list_gst_dt(Request $request) {
        $columns = array(
           0 => 'id',
           1 => 'name',
        );
        
        $data = $request->all();
        
        $name = $data['name'];
        
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $query = DB::table('listmatches')
                    ->select('name as match_name', 'listmatches.*')
                    ->where('final_status', 'winnerdeclared');
                    
         // search for the series name //
        if(isset($_GET['name'])){
           if($name!=""){
              $query->where('name', 'LIKE', '%'.$name.'%');
            }
        }
        
        if(request()->has('startdate')){
            
            $start_date = request('startdate');
            if($start_date!=""){
                $query->whereDate('start_date', '>=', date('Y-m-d', strtotime($start_date)));
            }
            
            if(request()->has('enddate')){
                
                $end_date = request('enddate');
                if($end_date == $start_date){
                    $query->whereDate('start_date', '>=', date('Y-m-d', strtotime($end_date)));
                }
            }
        }

        if(request()->has('enddate')){
            
            $end_date = request('enddate');
            if($end_date!=""){
                $query->whereDate('start_date', '<=', date('Y-m-d', strtotime($end_date)));
            }
        }
        
        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        
        $titles = $query->offset($start) 
                        ->limit($limit)
                        ->orderBy($order, 'DESC')
                        ->get();
                        
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {

                $nestedData['id'] = $count;
                $nestedData['name'] = $title->match_name;
                
                $a = action('GstController@allcontest_gst', $title->matchkey);
                
                $action ='<a href="'.$a.'" class="">View All Contest GST Report</a>';
                                            
                $nestedData['start_date'] = date('Y-m-d', strtotime($title->start_date));
                
                $joinedleauges = DB::table('joinedleauges')
                                    ->where('matchkey', $title->matchkey)
                                    ->count();

                $nestedData['total_contest'] = DB::table('matchchallenges')
                                                ->where('matchkey', $title->matchkey)
                                                ->count();
                                
                $nestedData['total_joined_users'] = $joinedleauges;
                             
                $leaugestransactions = DB::table('leaugestransactions')->where('matchkey', $title->matchkey)->select(DB::raw('sum(bonus) as b_amt'), DB::raw('sum(winning) as w_amt'), DB::raw('sum(balance) as blnc_amt'))->groupBy('matchkey')->first();

                $nestedData['amt'] = $leaugestransactions ? ($leaugestransactions->b_amt + $leaugestransactions->w_amt + $leaugestransactions->blnc_amt) : 0;

                $nestedData['amt'] = number_format($nestedData['amt'], 2, '.', '');

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
    
    public function downloadalluserinvoicegst(Request $request)
    {        
      $output1 = "";
      $output1 .='"Sno.",';
      $output1 .='"User id",';
      $output1 .='"Username",';
      $output1 .='"Email",';
      $output1 .='"Trading Amount",';
      $output1 .='"Platform Fee",';
      $output1 .='"GST No.",';
      $output1 .='"HSN",';
      $output1 .='"Invoice No.",';
      $output1 .='"SGST",';
      $output1 .='"CSGT",';
      $output1 .='"Total Amount",';
      $output1 .="\n";   
        
        $query = DB::table('listmatches')
                    ->select('matchkey', 'name')
                    ->where('final_status', 'winnerdeclared');
                    
         // search for the series name //
        if(isset($_GET['name'])){
            $name = $_GET['name'];
           if($name!=""){
              $query->where('name', 'LIKE', '%'.$name.'%');
            }
        }
        
        if(request()->has('startdate')){
            
            $start_date = request('startdate');
            if($start_date!=""){
                $query->whereDate('start_date', '>=', date('Y-m-d', strtotime($start_date)));
            }
            
            if(request()->has('enddate')){
                
                $end_date = request('enddate');
                if($end_date == $start_date){
                    $query->whereDate('start_date', '>=', date('Y-m-d', strtotime($end_date)));
                }
            }
        }

        if(request()->has('enddate')){
            
            $end_date = request('enddate');
            if($end_date!=""){
                $query->whereDate('start_date', '<=', date('Y-m-d', strtotime($end_date)));
            }
        }
        
        $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        
        $titles = $query->get();                       
      
      if(!empty($titles)){
        $count=1;
        foreach($titles as $title){

            $query = DB::table('joinedleauges')
                    ->join('registerusers','registerusers.id', 'joinedleauges.userid')
                    ->where('joinedleauges.matchkey', $title->matchkey)
                    ->select('registerusers.id as uid','registerusers.*','joinedleauges.*')
                    ->groupBy('joinedleauges.userid')
                    ->get();
            
            if (!empty($query)) {
                $data = array();
                foreach ($query as $q) {

                    $output1 .='"'.$count.'",';

                    $output1 .='"'.$q->uid.'",';
                    $output1 .='"'.$q->username.'",';
                    $output1 .='"'.$q->email.'",';

                    $total_buy_sell = DB::table('joinedleauges')
                                        ->where('userid', $q->uid)
                                        ->where('matchkey', $q->matchkey)
                                        ->count();

                    $total_shares = DB::table('joinedleauges')
                                        ->where('userid', $q->uid)
                                        ->where('matchkey', $q->matchkey)
                                        ->sum('shares');

                    $leaugestransactions = DB::table('leaugestransactions')
                                            ->where('matchkey', $q->matchkey)
                                            ->where('user_id', $q->uid)
                                            ->select(DB::raw('sum(bonus) as b_amt'), DB::raw('sum(winning) as w_amt'), DB::raw('sum(balance) as blnc_amt'))->groupBy('matchkey')
                                            ->first();

                    $nestedData['amt'] = $leaugestransactions ? ($leaugestransactions->b_amt + $leaugestransactions->w_amt + $leaugestransactions->blnc_amt) : 0;

                    $nestedData['platform_fee'] = ($nestedData['amt'] * 5) / 100;

                    $output1 .='"'.$nestedData['amt'].'",';
                    $output1 .='"'.$nestedData['platform_fee'].'",';
                                
                    $nestedData['gst_no'] = '09AAFCH0439L1ZE';
                    $nestedData['hsn'] = '999659';
                    $nestedData['invoice_no'] = 'INV00'. $count;

                    $output1 .='"'.$nestedData['gst_no'].'",';                                
                    $output1 .='"'.$nestedData['hsn'].'",';                                
                    $output1 .='"'.$nestedData['invoice_no'].'",';
                                
                    /**
                     * SGST & CGST Calculation
                     */
                    $tax = 0.0423 /* % */;
                    $amount_after_tax = ($nestedData['amt'] != '') ? ($nestedData['amt'] * $tax) : '';
                    $nine_percent_gst = ($nestedData['amt'] != '') ? ($amount_after_tax * 9) / 100 : '';

                    $nine_percent_gst = number_format((float)$nine_percent_gst, 2, ".", ""); 
                    /**
                     * SGST & CGST Calculation
                     */

                    $nestedData['SGST'] = $nine_percent_gst;
                    $nestedData['CSGT'] = $nine_percent_gst;
                    $nestedData['total_amt'] = $nestedData['platform_fee'];
                    
                    $output1 .='"'.$nine_percent_gst.'",';                                
                    $output1 .='"'.$nine_percent_gst.'",';                                
                    $output1 .='"'.$nestedData['platform_fee'].'",';
                                
                    $invoice_no = $nestedData['invoice_no'];
                    $date = date('d-m-Y');
                    $user_name = $q->username;
                    $description = 'Platform Fee';
                    $amount = $nestedData['amt'];
                    $tax = 0.0423 /* % */;
                            
                    $download_link = action('AdminwalletController@download_pdf_invoice', [$invoice_no ?? ' ', $date, $user_name ?? ' ', $description, $amount, $tax]);
                    $nestedData['action'] = '<a href="'.$download_link.'?tax_3percent='.$nestedData['platform_fee'].'" class="btn btn-primary">Download PDF</a>'; 

                    $output1 .="\n";
                    $count++;
                }
            }
        }
      }
      $filename =  "Details-alluserinvoicegst.csv";
      header('Content-type: application/csv');
      header('Content-Disposition: attachment; filename='.$filename);
      echo $output1;
      exit;
    }

     public function allcontest_gst($matchkey){
        $getdata = DB::table('matchchallenges')->where('matchkey',$matchkey)->orderBy('joinedusers','desc')->groupBy('id')->paginate(10);
        $finddata = DB::table('matchchallenges')->where('matchkey',$matchkey)->first();

        return  view('gst.allcontest_gst',compact('getdata','finddata'));
      }
}
?>