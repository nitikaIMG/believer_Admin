<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Redirect;
use Hash;
use App\Helpers\Helpers; 
use Carbon\Carbon;

class YoutuberBonusController extends Controller
{
    public $youtubers = [];
    
    public function give_youtuber_bonus() {
   
        $matchkey = request()->get('matchkey');
        
        $challenges = DB::table('matchchallenges')->where('matchkey', $matchkey)->where('status', '!=', 'canceled')->select('id', 'entryfee', 'is_bonus', 'bonus_percentage')->get();

        if( !empty($challenges->toArray()) ) {
                
            foreach($challenges as $challenge) {
                
                $challenge_joined_users = DB::table('joinedleauges')->where('challengeid', $challenge->id)->select('userid','id')->get();
                
                # Admin Amt Received
                if($challenge->is_bonus) {
                    
                    $bonus_allowed = ($challenge->entryfee * $challenge->bonus_percentage) / 100;
                    
                    $remaining_entryfee = $challenge->entryfee - $bonus_allowed;
                    
                    $admin_amt_received = $remaining_entryfee * count($challenge_joined_users);
                    
                } else {
                    
                    $admin_amt_received = $challenge->entryfee * count($challenge_joined_users);
                    
                }
                
                # Final Result Amount
                $final_result_amt = DB::table('finalresults')->where('challengeid', $challenge->id)->sum('amount');
                
                if($final_result_amt > $admin_amt_received) {
                    $profit = $final_result_amt - $admin_amt_received;
                } else {
                    $profit = $admin_amt_received - $final_result_amt;
                }
                
                # Find youtuber users
                foreach($challenge_joined_users as $user) {
                        
                    $is_youtuber = $this->is_youtuber($user->userid);
                    
                    if( $is_youtuber ) {
                        
                        $this->youtubers[$challenge->id][$is_youtuber]['count'] = !empty($this->youtubers[$challenge->id][$is_youtuber]) ? 
                                                                    $this->youtubers[$challenge->id][$is_youtuber]['count'] + 1 :
                                                                    1;
                        
                        $this->youtubers[$challenge->id][$is_youtuber]['refer'][] = $user->userid;
                        $this->youtubers[$challenge->id][$is_youtuber]['joinid'][] = $user->id;

                    }
                    
                }

                
                // # Give youtuber profit according to percentage
                if( !empty($this->youtubers) ) {

                    foreach($this->youtubers[$challenge->id] as $youtuber => $total_refer) {

                        $youtuber_percentage = DB::table('registerusers')->where('id', $youtuber)->value('percentage');
                        
                        $youtuber_profit = ( ( ($profit * $total_refer['count']) / count($challenge_joined_users) ) * $youtuber_percentage ) / 100;
                                
                        $youtuber_profit = number_format($youtuber_profit, 2, '.', '');
                        
                        $fromid = $total_refer['refer'];
                        $joinid = $total_refer['joinid'];

                        $this->give_youtuber_profit($youtuber, $youtuber_profit, $fromid, $matchkey, $challenge->id, $joinid);
                    }
                    
                    $this->youtubers = [];
                    
                }
                
            } 
            return redirect()->back()->with('success','Bonus reflected to youtubers account.'); 
        }else{
            return redirect()->back()->with('danger','No Contest available for the youtubers.');
        }
        

    }
    
    public function is_youtuber($id) {
        
        $refer = DB::table('registerusers')->where('id', $id)->value('refer_id');
        
        // $is_youtuber = DB::table('registerusers')->where('id', $refer)->where('type', 'youtuber')->exists();
        $is_youtuber = DB::table('registerusers')->where('id', $refer)->exists();
        
        return $is_youtuber ? $refer : false;
    }  
    
    public function give_youtuber_profit($youtuber, $profit, $fromid, $matchkey, $challengeid,$joinid) {
      
        $youtuber_current_wallet = DB::table('userbalance')
                                     ->where('user_id', $youtuber)
                                     ->first();
        $exist_transaction = DB::table('transactions')->whereIn('type',['Youtuber Bonus','Affliate Bonus'])->where('challengeid',$challengeid)->where('userid',$youtuber)->first();
        if(empty($exist_transaction)){
            # Make a transaction 
            $bal_fund_amt = number_format($youtuber_current_wallet->balance, 2, '.', '');;
            $bal_win_amt = number_format($youtuber_current_wallet->winning, 2, '.', '');;
            $bal_bonus_amt = number_format($youtuber_current_wallet->bonus, 2, '.', '');;
            
            $youtuber_updated_wallet['winning'] = number_format($youtuber_current_wallet->winning + $profit, 2, '.', '');;
            
            DB::connection('mysql2')->table('userbalance')->where('id', $youtuber_current_wallet->id)->update($youtuber_updated_wallet);
            
            $total_available_amt = number_format($youtuber_current_wallet->balance + $youtuber_updated_wallet['winning'] + $youtuber_current_wallet->bonus, 2, '.', '');;
            
            $transactionsdata['userid'] = $youtuber;
            $transactionsdata['type'] = 'Youtuber Bonus';
            $transactionsdata['transaction_id'] = (Helpers::settings()->short_name ?? '').'-YP-'.time().$youtuber;
            $transactionsdata['transaction_by'] = 'wallet';
            $transactionsdata['challengeid'] = $challengeid;
            $transactionsdata['amount'] = number_format($profit, 2, '.', '');;
            $transactionsdata['paymentstatus'] = 'confirmed';
            $transactionsdata['bal_fund_amt'] = number_format($bal_fund_amt, 2, '.', '');;
            $transactionsdata['bal_win_amt'] = number_format($youtuber_updated_wallet['winning'], 2, '.', '');;
            $transactionsdata['bal_bonus_amt'] = number_format($bal_bonus_amt, 2, '.', '');;        
            $transactionsdata['total_available_amt'] = number_format($total_available_amt, 2, '.', '');;
            DB::connection('mysql2')->table('transactions')->insert($transactionsdata);
        
            
            
        }
        foreach ($fromid as $key => $user) {
        
            $youtuberbonus['userid'] = $youtuber;
            $youtuberbonus['fromid'] = $user;
            $youtuberbonus['amount'] = number_format($profit / count($fromid), 2, '.', '');
            $youtuberbonus['type'] = 'Youtuber Bonus';
            $youtuberbonus['matchkey'] = $matchkey;
            $youtuberbonus['joinid'] = $joinid[$key];
            $youtuberbonus['challengeid'] = $challengeid;
            $youtuberbonus['txnid'] = (Helpers::settings()->short_name ?? '').'-YP-'.time().$youtuber;
            $existdata = DB::table('youtuber_bonus')->where('matchkey',$matchkey)->where('joinid',$joinid[$key])->first();
            if(empty($existdata)){
                DB::connection('mysql2')->table('youtuber_bonus')->insert($youtuberbonus);
            }

        }
        

    }
    
    
}
