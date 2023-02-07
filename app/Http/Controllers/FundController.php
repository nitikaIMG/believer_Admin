<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helpers;
use DB;
use Redirect;
use Session;
class FundController extends Controller
{
	
	// Paytm section

	public function paytm(){
	   // echo 'hii';die;
	   return view('fund.paytm');
	}

	public function paytmtable(Request $request){
		$columns = array(
            0 => 'id',
            1 => 'userid',
            2 => 'registerusers.username',
            3 => 'mobile',
            4 => 'transaction_by',
            5 => 'transaction_id',
            6 => 'tdate',
            7 => 'tamt'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('paymentprocess')->join('registerusers','paymentprocess.userid','=','registerusers.id')->where('paymentmethod','=','paytm')->select('paymentprocess.userid as id','registerusers.username as name','registerusers.mobile as mobile','paymentprocess.paymentmethod as transaction','paymentprocess.orderid as tid','paymentprocess.created_at as tdate','paymentprocess.amount as tamt','registerusers.id as rid');
			if(request()->has('playername')){
				$name = request('playername');
				if($name !=""){
					$query->where('registerusers.username', 'LIKE', '%'.$name.'%');
				}
			}

			if(request()->has('mobile')){
				$mobile = request('mobile');
				if($mobile != ""){
					$query->where('registerusers.mobile', 'LIKE','%'.$mobile.'%');
				}
			}

			if(request()->has('startdate')){
					$start_date = request('startdate');
					
					if($start_date!=""){
						$start_date = date('Y-m-d H:i:s', strtotime('-1 hours', strtotime(request('startdate'))));
						$query->whereDate('paymentprocess.created_at', '>=',date('Y-m-d h:i:s',strtotime($start_date)));
					}
				}

				if(request()->has('enddate')){
					$end_date = request('enddate');
					if($end_date!=""){
						$query->whereDate('paymentprocess.created_at', '<=',date('Y-m-d H:i:s',strtotime($end_date)));
					}
				}
				$totalTitles = $query->count();
		        $totalFiltered = $totalTitles;
				$titles = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
		            if (!empty($titles)) {
		                $data = array();

		                if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                	$count = $totalFiltered - $start;
		                } else {
		                	$count = $start + 1;
		                }

		                foreach ($titles as $title) {
		                	$bb=action('RegisteruserController@getuserdetails',$title->rid);
              				$a ='<a href="'.$bb.'" style="text-decoration:underline;">'.$title->rid.'';
		                	$nestedData['sno'] = $count;
		                    $nestedData['id'] = $a;
		                    $nestedData['name'] =$title->name;
		                    $nestedData['mobile'] =$title->mobile;
		                    $nestedData['transaction'] =$title->transaction;
		                    $nestedData['tid'] =$title->tid;
		                    $nestedData['tdate'] =date('d-m-Y h:i:s',strtotime($title->tdate));
		                    $nestedData['tamt'] =$title->tamt;
		                    $data[] = $nestedData;

		                    if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                    	$count -= 1;
		                    } else {
		                    	$count += 1;
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

		// Net Banking Section
		public function netbanking(){
    	return view('fund.netbanking');
    	}

    	public function netbankingtable(Request $request){
		$columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'name',
            3 => 'mobile',
            4 => 'transaction',
            5 => 'tid',
            6 => 'tdate',
            7 => 'tamt',
            8 => 'rid'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('transactions')->join('registerusers','transactions.userid','=','registerusers.id')->where('transaction_by','=','nb')->select('transactions.userid as id','registerusers.username as name','registerusers.mobile as mobile','transactions.transaction_by as transaction','transactions.transaction_id as tid','transactions.created_at as tdate','transactions.amount as tamt','registerusers.id as rid');
			if(request()->has('playername')){
				$name = request('playername');
				if($name !=""){
					$query->where('registerusers.username', 'LIKE', '%'.$name.'%');
				}
			}

			if(request()->has('mobile')){
				$mobile = request('mobile');
				if($mobile != ""){
					$query->where('registerusers.mobile', 'LIKE','%'.$mobile.'%');
				}
			}

			if(request()->has('startdate')){
					$start_date = request('startdate');
					
					if($start_date!=""){
						$start_date = date('Y-m-d H:i:s', strtotime('-1 hours', strtotime(request('startdate'))));
						$query->whereDate('transactions.created_at', '>=',date('Y-m-d h:i:s',strtotime($start_date)));
					}
				}

				if(request()->has('enddate')){
					$end_date = request('enddate');
					if($end_date!=""){
						$query->whereDate('transactions.created_at', '<=',date('Y-m-d H:i:s',strtotime($end_date)));
					}
				}
				$totalTitles = $query->count();
		            $totalFiltered = $totalTitles;
				$titles = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
		            
		            if (!empty($titles)) {
		                $data = array();
		                if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                	$count = $totalFiltered - $start;
		                } else {
		                	$count = $start + 1;
		                }

		                foreach ($titles as $title) {
		                	$bb=action('RegisteruserController@getuserdetails',$title->rid);
              				$a ='<a href="'.$bb.'" style="text-decoration:underline;">'.$title->rid.'';
		                	$nestedData['sno'] = $count;
		                    $nestedData['id'] = $a;
		                    $nestedData['name'] =$title->name;
		                    $nestedData['mobile'] =$title->mobile;
		                    $nestedData['transaction'] =$title->transaction;
		                    $nestedData['tid'] =$title->tid;
		                    $nestedData['tdate'] =date('d-m-Y h:i:s',strtotime($title->tdate));
		                    $nestedData['tamt'] =$title->tamt;
		                    $data[] = $nestedData;

		                    if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                    	$count -= 1;
		                    } else {
		                    	$count += 1;
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
		public function card(){
		return view('fund.card');
		}
		public function cardtable(Request $request)
		{
			$columns = array(
            0 => 'id',
            1 => 'userid',
            2 => 'type',
            3 => 'registerusers.mobile',
            4 => 'paymentprocess.paymentmethod',
            5 => 'amount',
            6 => 'paymentstatus',
            7 => 'challengeid'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('paymentprocess')->join('registerusers','paymentprocess.userid','=','registerusers.id')->Where('paymentprocess.status','=','success')->select('paymentprocess.userid as id','registerusers.username as name','registerusers.mobile as mobile','paymentprocess.paymentmethod as transaction','paymentprocess.orderid as tid','paymentprocess.updated_at as tdate','paymentprocess.amount as tamt','registerusers.id as rid');
			if(request()->has('playername')){
				$name = request('playername'); 
				if($name !=""){
					$query->where('registerusers.username', 'LIKE', '%'.$name.'%');
				}
			}

			if(request()->has('mobile')){
				$mobile = request('mobile');
				if($mobile != ""){
					$query->where('registerusers.mobile', 'LIKE','%'.$mobile.'%');
				}
			}

			if(request()->has('startdate')){
					$start_date = request('startdate');
					
					if($start_date!=""){
						$query->whereDate('paymentprocess.created_at', '>=',date('Y-m-d',strtotime($start_date)));
					}
				}

			if(request()->has('enddate')){
				$end_date = request('enddate');
				if($end_date!=""){
					$query->whereDate('paymentprocess.created_at', '<=',date('Y-m-d',strtotime($end_date)));
				}
			}
			if(request()->has('payment_method')){
				$payment_method = request('payment_method'); 
				if($payment_method !=""){
					$query->where('paymentprocess.paymentmethod', 'LIKE', '%'.$payment_method.'%');
				}
			}
			
			
			if(request()->has("option")){
				$option = request("option");
				if($option!=""){
				    
				    if($option == 1) {
    					$query->whereBetween('paymentprocess.amount', [0,500]);
				    } else if($option == 2) {
    					$query->whereBetween('paymentprocess.amount', [501,1000]);
				    } else if($option == 3) {
    					$query->whereBetween('paymentprocess.amount', [1001,2000]);
				    } else if($option == 4) {
    					$query->whereBetween('paymentprocess.amount', [2001, 5000]);
				    } else {
    					$query->where('paymentprocess.amount', '>', 5000);
				    }
				    
				}
			}
			
			
			
				$totalTitles = $query->count();
		            $totalFiltered = $totalTitles;
				$titles = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
		            if (!empty($titles)) {
						$data = array();
						if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                	$count = $totalFiltered - $start;
		                } else {
		                	$count = $start + 1;
		                }
		                foreach ($titles as $title) {
		                	$bb=action('RegisteruserController@getuserdetails',$title->rid);
              				$a ='<a href="'.$bb.'" style="text-decoration:underline;">'.$title->rid.'';
		                	$nestedData['sno'] = $count;
		                    $nestedData['id'] = $a;
		                    $nestedData['name'] =$title->name;
		                    $nestedData['mobile'] =$title->mobile;
		                    $nestedData['transaction'] = $title->transaction;
		                    $nestedData['tid'] =$title->tid;
		                    $nestedData['tdate'] =$title->tdate;
		                    $nestedData['tamt'] =$title->tamt;
							$data[] = $nestedData;
							if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                    	$count -= 1;
		                    } else {
		                    	$count += 1;
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
		public function upi(){
		return view('fund.upi');
		}

		public function upitable(Request $request){
			$columns = array(
            0 => 'id',
            1 => 'userid',
            2 => 'name',
            3 => 'mobile',
            4 => 'transaction',
            5 => 'tid',
            6 => 'tdate',
            7 => 'tamt',
            8 => 'rid',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('transactions')->join('registerusers','transactions.userid','=','registerusers.id')->where('transaction_by','=','upi')->select('transactions.userid as id','registerusers.username as name','registerusers.mobile as mobile','transactions.transaction_by as transaction','transactions.transaction_id as tid','transactions.created_at as tdate','transactions.amount as tamt','registerusers.id as rid');
			if(request()->has('playername')){
				$name = request('playername');
				if($name !=""){
					$query->where('registerusers.username', 'LIKE', '%'.$name.'%');
				}
			}

			if(request()->has('mobile')){
				$mobile = request('mobile');
				if($mobile != ""){
					$query->where('registerusers.mobile', 'LIKE','%'.$mobile.'%');
				}
			}

			if(request()->has('startdate')){
					$start_date = request('startdate');
					
					if($start_date!=""){
						$start_date = date('Y-m-d H:i:s', strtotime('-1 hours', strtotime(request('startdate'))));
						$query->whereDate('transactions.created_at', '>=',date('Y-m-d h:i:s',strtotime($start_date)));
					}
				}

				if(request()->has('enddate')){
					$end_date = request('enddate');
					if($end_date!=""){
						$query->whereDate('transactions.created_at', '<=',date('Y-m-d H:i:s',strtotime($end_date)));
					}
				}
				$totalTitles = $query->count();
		            $totalFiltered = $totalTitles;
				$titles = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
		            if (!empty($titles)) {
		                $data = array();
		                if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                	$count = $totalFiltered - $start;
		                } else {
		                	$count = $start + 1;
		                }

		                foreach ($titles as $title) {
		                	$bb=action('RegisteruserController@getuserdetails',$title->rid);
              				$a ='<a href="'.$bb.'" style="text-decoration:underline;">'.$title->rid.'';
		                	$nestedData['sno'] = $count;
		                    $nestedData['id'] = $a;
		                    $nestedData['name'] =$title->name;
		                    $nestedData['mobile'] =$title->mobile;
		                    $nestedData['transaction'] ='Cashfree';
		                    $nestedData['tid'] =$title->tid;
		                    $nestedData['tdate'] =date('d-m-Y h:i:s',strtotime($title->tdate));
		                    $nestedData['tamt'] =$title->tamt;
		                    $data[] = $nestedData;

		                    if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                    	$count -= 1;
		                    } else {
		                    	$count += 1;
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
		
		public function cashFree(){
		    return view('fund.cashFree');
		}

		public function cashFreetable(Request $request){
			$columns = array(
            0 => 'id',
            1 => 'userid',
            2 => 'name',
            3 => 'mobile',
            4 => 'transaction',
            5 => 'tid',
            6 => 'tdate',
            7 => 'tamt',
            8 => 'rid',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('transactions')->join('registerusers','transactions.userid','=','registerusers.id')->where('transaction_by','=','cashFree')->select('transactions.userid as id','registerusers.username as name','registerusers.mobile as mobile','transactions.transaction_by as transaction','transactions.transaction_id as tid','transactions.created_at as tdate','transactions.amount as tamt','registerusers.id as rid');
			if(request()->has('playername')){
				$name = request('playername');
				if($name !=""){
					$query->where('registerusers.username', 'LIKE', '%'.$name.'%');
				}
			}

			if(request()->has('mobile')){
				$mobile = request('mobile');
				if($mobile != ""){
					$query->where('registerusers.mobile', 'LIKE','%'.$mobile.'%');
				}
			}

			if(request()->has('startdate')){
					$start_date = request('startdate');
					
					if($start_date!=""){
						$start_date = date('Y-m-d H:i:s', strtotime('-1 hours', strtotime(request('startdate'))));
						$query->whereDate('transactions.created_at', '>=',date('Y-m-d h:i:s',strtotime($start_date)));
					}
				}

				if(request()->has('enddate')){
					$end_date = request('enddate');
					if($end_date!=""){
						$query->whereDate('transactions.created_at', '<=',date('Y-m-d H:i:s',strtotime($end_date)));
					}
				}
				$totalTitles = $query->count();
		            $totalFiltered = $totalTitles;
				$titles = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
		            if (!empty($titles)) {
		                $data = array();

		                if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                	$count = $totalFiltered - $start;
		                } else {
		                	$count = $start + 1;
		                }

		                foreach ($titles as $title) {
		                	$bb=action('RegisteruserController@getuserdetails',$title->rid);
              				$a ='<a href="'.$bb.'" style="text-decoration:underline;">'.$title->rid.'';
		                	$nestedData['sno'] = $count;
		                    $nestedData['id'] = $a;
		                    $nestedData['name'] =$title->name;
		                    $nestedData['mobile'] =$title->mobile;
		                    $nestedData['transaction'] =$title->transaction;
		                    $nestedData['tid'] =$title->tid;
		                    $nestedData['tdate'] =date('d-m-Y h:i:s',strtotime($title->tdate));
		                    $nestedData['tamt'] =$title->tamt;
		                    $data[] = $nestedData;

		                    if(request()->input('order.0.column') == '0' and request()->input('order.0.dir') == 'desc') {
		                    	$count -= 1;
		                    } else {
		                    	$count += 1;
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
}