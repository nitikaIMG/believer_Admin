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
class TransactionController extends Controller
{
  
    public function getTransaction(){

      if(!empty($_GET['userid'])){
        $userid= $_GET['userid'];
      }else{
         $userid='';
      }
    	return view('transaction.transaction',compact('userid'));
    }
    public function viewalltransactions_table(Request $request){
      date_default_timezone_set("Asia/Kolkata");
      $columns = array(
            0 => 'id',
            1 => 'userid',
            2 => 'type',
            3 => 'transaction_id',
            4 => 'transaction_by',
            5 => 'amount',
            6 => 'paymentstatus',
            7 => 'challengeid',
            8 => 'seriesid',
            9 => 'bonus_amt',
            10 => 'win_amt',
            11=> 'addfund_amt',
            12=> 'bal_bonus_amt',
            13=> 'created_at',
            14=> 'updated_at'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query =DB::table('transactions');
        if(request()->has('start_date')){
          $start_date = request('start_date');
          if($start_date!=""){
            $query->whereDate('transactions.created_at', '>=',date('Y-m-d',strtotime($start_date)));
          }
        }
        
        if(request()->has('end_date')){
          $end_date = request('end_date');
          if($end_date!=""){
            $query->whereDate('transactions.created_at', '<=',date('Y-m-d',strtotime($end_date)));
          }
        }
        if(request()->has('userid')){
          $userid = request('userid');
          if($userid!=""){
            $query->where('transactions.userid', $userid);
          }
        }
        if(request()->has('cid')){
          $cid = request('cid');
          if($cid!=""){
            $query->where('transactions.challengeid',$cid);
          }
        }

        
        $data = [];
         $totalTitles = $query->count();
        $totalFiltered = $totalTitles;
        $titles = $query->offset($start) 
                ->limit($limit)
                ->orderBy('id', 'desc')->get();
        if (!empty($titles)) {
            $data = array();
            $count = 1;
            foreach ($titles as $title) {
              $ttlamount= $title->bonus_amt+$title->addfund_amt+$title->win_amt;
              $ttlconsamount= $title->cons_win+$title->cons_amount+$title->cons_bonus;
              $ttlamounts= '';
              $name= '';
              $entryfee= 0;
              
              $ttluserbalance= $title->total_available_amt;
              if($title->type=='Contest Joining Fee'){
                
                if($title->challengeid!='0'){
                  $matchchallenges= DB::table('matchchallenges')->where('matchchallenges.id',$title->challengeid)->join('listmatches','listmatches.matchkey','=','matchchallenges.matchkey')->select('matchchallenges.*','listmatches.name')->first();
                  if(!empty($matchchallenges)){ 
                    $challengeid=  $matchchallenges->id;
                    $name=  $matchchallenges->name;
                    $entryfee=  $matchchallenges->entryfee;
                    $win_amount=  $matchchallenges->win_amount;
                    $maximum_user=  $matchchallenges->maximum_user;
                    $matchkey=  $matchchallenges->matchkey;
                  }
                }else{
                   $challengeid= '';
                   $name= '';
                   $entryfee= '';
                   $win_amount= '';
                   $maximum_user= '';
                   $matchkey= '';
                }
                 $color= 0;
                $ttlamounts= '<a data-toggle="modal" data-target="#myModal'.$title->id.'" href="#" style="color:red;">'.round($ttlconsamount,2).'</a><div class="modal fade" id="myModal'.$title->id.'" role="dialog" style="margin-top: 150px; padding-right: 16px;" aria-modal="true">
                 <div class="modal-dialog" style="max-width: 650px;">
                   <!-- Modal content-->
                   <div class="modal-content">
                     <div class="modal-header">
                       <button type="button" class="close order-12" data-dismiss="modal">×</button>
                       <h5 class="modal-title order-1">'.$title->type.'</h5>
                     </div>
                     <div class="modal-body p-0 m-3 shadow overflow-hidden rounded" style="font-size: 13px;">
                       <table class="table mb-0 table-hover">
                       <thead class="bg-light">
                       <tr><th>Challenge Id</th>
                       <th>Match Name</th>
                       <th>Entry Fees</th>
                       <th>Win Amount</th>
                       <th>Bonus</th>
                       <th>Utilized</th>
                       <th>Winning</th>
                       </tr></thead>
                       <tbody><tr>
                         <td><a href='.asset('my-admin/allusers/'.$challengeid).' target="_blank">'.$challengeid.'</a></td>
                        <td><a href='.asset('my-admin/allcontests/'.$matchkey).' target="_blank">'.$name.'</a></td>
                        <td>'.$entryfee.'</td>
                        <td>'.$win_amount.'</td>
                        <td>'.$title->cons_bonus.'</td>
                        <td>'.$title->cons_amount.'</td>
                        <td>'.$title->cons_win.'</td>
                       </tr>
                       </tbody></table>
                     </div>
                     <div class="modal-footer py-2">
                       <button type="button" class="btn btn-secondary text-white rounded-pill" data-dismiss="modal">Close</button>
                     </div>
                   </div>
                 </div>
               </div>';
             }else if($title->type=='Challenge Winning Amount'){
              
               $finalresults= DB::table('matchchallenges')->where('matchchallenges.id',$title->challengeid)->join('finalresults','finalresults.matchkey','=','matchchallenges.matchkey')->join('listmatches','listmatches.matchkey','=','matchchallenges.matchkey')->select('finalresults.matchkey','finalresults.rank','matchchallenges.id','listmatches.name','matchchallenges.entryfee')->first();
              if(!empty($finalresults)){
                 $color= 1;
                $ttlamounts= '<a data-toggle="modal" data-target="#myModal'.$title->id.'" href="#" style="color:green;">'.round($ttlamount,2).'</a><div class="modal fade" id="myModal'.$title->id.'" role="dialog" style="margin-top: 150px; padding-right: 16px;" aria-modal="true">
                   <div class="modal-dialog" style="max-width: 650px;">
                     <!-- Modal content-->
                     <div class="modal-content">
                       <div class="modal-header">
                         <button type="button" class="close order-12" data-dismiss="modal">×</button>
                         <h5 class="modal-title order-1">'.$title->type.'</h5>
                       </div>
                       <div class="modal-body p-0 m-3 shadow overflow-hidden rounded" style="font-size: 13px;">
                         <table class="table mb-0 table-hover">
                         <thead class="bg-light">
                         <tr><th>Challenge Id</th>
                         <th>Match Name</th>
                         <th>Entry Fees</th>
                         <th>Win Amount</th>
                         <th>Rank</th>
                         <th>Bonus</th>
                         <th>Utilized</th>
                         <th>Winning</th>
                         </tr></thead>
                         <tbody><tr>
                           <td><a href='.asset('my-admin/allusers/'.$finalresults->id).' target="_blank">'.$finalresults->id.'</a></td>
                          <td><a href='.asset('my-admin/allcontests/'.$finalresults->matchkey).' target="_blank">'.$finalresults->name.'</a></td>
                          <td>'.$finalresults->entryfee.'</td>
                          <td>'.$title->amount.'</td>
                          <td>'.$finalresults->rank.'</td>
                          <td>'.$title->bonus_amt.'</td>
                          <td>'.$title->addfund_amt.'</td>
                          <td>'.$title->win_amt.'</td>
                         </tr>
                         </tbody></table>
                       </div>
                       <div class="modal-footer py-2">
                         <button type="button" class="btn btn-secondary text-white rounded-pill" data-dismiss="modal">Close</button>
                       </div>
                     </div>
                   </div>
                 </div>';
               }
             }else if($title->type=='Refund amount' || $title->type=='Refund'){
              
               $finalrefunds= DB::table('matchchallenges')->where('matchchallenges.id',$title->challengeid)->join('listmatches','listmatches.matchkey','=','matchchallenges.matchkey')->select('matchchallenges.id','listmatches.name','matchchallenges.entryfee')->first();
              if(!empty($finalrefunds)){
                 $color= 1;
                $ttlamounts= '<a data-toggle="modal" data-target="#myModal'.$title->id.'" href="#" style="color:green">'.round($ttlamount,2).'</a><div class="modal fade" id="myModal'.$title->id.'" role="dialog" style="margin-top: 150px; padding-right: 16px;" aria-modal="true">
                   <div class="modal-dialog" style="max-width: 650px;">
                     <!-- Modal content-->
                     <div class="modal-content">
                       <div class="modal-header">
                         <button type="button" class="close order-12" data-dismiss="modal">×</button>
                         <h5 class="modal-title order-1">'.$title->type.'</h5>
                       </div>
                       <div class="modal-body p-0 m-3 shadow overflow-hidden rounded" style="font-size: 13px;">
                         <table class="table mb-0 table-hover">
                         <thead class="bg-light">
                         <tr><th>Challenge Id</th>
                         <th>Match Name</th>
                         <th>Entry Fees</th>
                         <th>total Amount</th>
                         <th>Bonus</th>
                         <th>Utilized</th>
                         <th>Winning</th>
                         </tr></thead>
                         <tbody><tr>
                           <td><a href='.asset('my-admin/allusers/'.$finalrefunds->id).' target="_blank">'.$finalrefunds->id.'</a></td>
                          <td><a href='.asset('my-admin/allcontests/'.$finalrefunds->matchkey).' target="_blank">'.$finalrefunds->name.'</a></td>
                          <td>'.$finalrefunds->entryfee.'</td>
                          <td>'.$title->amount.'</td>
                          <td>'.$title->bonus_amt.'</td>
                          <td>'.$title->addfund_amt.'</td>
                          <td>'.$title->win_amt.'</td>
                         </tr>
                         </tbody></table>
                       </div>
                       <div class="modal-footer py-2">
                         <button type="button" class="btn btn-secondary text-white rounded-pill" data-dismiss="modal">Close</button>
                       </div>
                     </div>
                   </div>
                 </div>';
               }
             }elseif($title->type=='Cash added'){
              $color= 1;
                 $finalcash= DB::table('paymentprocess')->where('orderid',$title->transaction_id)->select('*')->first();
              if(!empty($finalcash)){
                $ttlamounts= '<a data-toggle="modal" data-target="#myModal'.$title->id.'" href="#" style="color:green;">'.round($ttlamount,2).'</a><div class="modal fade" id="myModal'.$title->id.'" role="dialog" style="margin-top: 150px; padding-right: 16px;" aria-modal="true">
                   <div class="modal-dialog" style="max-width: 650px;">
                     <!-- Modal content-->
                     <div class="modal-content">
                       <div class="modal-header">
                         <button type="button" class="close order-12" data-dismiss="modal">×</button>
                         <h5 class="modal-title order-1">'.$title->type.'</h5>
                       </div>
                       <div class="modal-body p-0 m-3 shadow overflow-hidden rounded" style="font-size: 13px;">
                         <table class="table mb-0 table-hover">
                         <thead class="bg-light">
                         <th>Paytm Type</th>
                         <th>Order Id</th>
                         <th>Date</th>
                         </tr></thead>
                         <tbody><tr>
                          <td>'.$finalcash->paymentmethod.'</td>
                          <td>'.$finalcash->orderid.'</td>
                          <td>'.date('d M Y', strtotime($finalcash->updated_at)).'</td>
                         </tr>
                         </tbody></table>
                       </div>
                       <div class="modal-footer py-2">
                         <button type="button" class="btn btn-secondary text-white rounded-pill" data-dismiss="modal">Close</button>
                       </div>
                     </div>
                   </div>
                 </div>';
               }
             }
                $ttlbalance= '<a data-toggle="modal" data-target="#myModal1'.$title->id.'" href="#">'.round($ttluserbalance,2).'</a><div class="modal fade" id="myModal1'.$title->id.'" role="dialog" style="margin-top: 150px; padding-right: 16px;" aria-modal="true">
                 <div class="modal-dialog" style="max-width: 650px;">
                   <!-- Modal content-->
                   <div class="modal-content">
                     <div class="modal-header">
                       <button type="button" class="close order-12" data-dismiss="modal">×</button>
                       <h5 class="modal-title order-1">'.$title->type.'</h5>
                     </div>
                     <div class="modal-body p-0 m-3 shadow overflow-hidden rounded" style="font-size: 13px;">
                       <table class="table mb-0 table-hover">
                       <thead class="bg-light">
                       <th>Bonus</th>
                       <th>Utilized</th>
                       <th>Winning</th>
                       </tr></thead>
                       <tbody><tr>
                        <td>'.$title->bal_bonus_amt.'</td>
                        <td>'.$title->bal_win_amt.'</td>
                        <td>'.$title->bal_fund_amt.'</td>
                       </tr>
                       </tbody></table>
                     </div>
                     <div class="modal-footer py-2">
                       <button type="button" class="btn btn-secondary text-white rounded-pill" data-dismiss="modal">Close</button>
                     </div>
                   </div>
                 </div>
               </div>';
               $userrs = DB::table('registerusers')->where('id',$title->userid)->first();
               $bb=action('RegisteruserController@getuserdetails',$userrs->id);
               $aa ='<a href="'.$bb.'" style="text-decoration:underline;">'.$userrs->id.'';

              
              $nestedData['userid'] = $aa;
              $nestedData['date'] =date('d M Y',strtotime($title->created_at)).' '.date('h:i:s a',strtotime($title->created_at));
              $nestedData['type'] = $title->type;
              if($ttlamounts!=''){
                if($color==1){
                  $nestedData['totalamount'] ='<p style="color:green">'.$ttlamounts.'</p>';
                }else{
                  $nestedData['totalamount'] ='<p style="color:red">'.$ttlamounts.'</p>';
                }
              }else{
                 $nestedData['totalamount'] =$title->amount;
              }
              
              $nestedData['ttlbalance'] = $ttlbalance;
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

}