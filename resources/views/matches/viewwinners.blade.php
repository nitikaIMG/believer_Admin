@extends('main')

@section('heading')
    Result Manager
@endsection('heading')

@section('sub-heading')
    Winners Listing
@endsection('sub-heading')

@section('content')
<div class="card mb-4">
    <div class="card-header">Winners Listing</div>
    <div class="card-body">
        <div class="datatable table-responsive overflow-auto">

            @include('alert_msg')

            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>ChallengeId</th>
                        <th>IsJoin</th>
                        <th>IsRefund</th>
                        <th>Iswin</th>
                        <th>Userid</th>
                        <th>JoinDate</th>
                        <th>RefundDate</th>
                        <th>WinDate</th>
                        <th>UserEmail</th>
                        <th>Username</th>
                        <th>TeamRank</th>
                        <th>RefundAmount</th>
                        <th>Winamount</th>
                        <th>TotalPoints</th>
                        <th>ChallengeName</th>
                        <th>EntryFee</th>
                        <th>ConfirmedLeague</th>
                        <th>UserTeamName</th>
                        <th>Bonus %</th>
                        <th>MaximumUsers</th>
                        <th>IsPrivate</th>
                        <th>MaxNumberofwinners</th>
                        <th>Joined users</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>ChallengeId</th>
                        <th>IsJoin</th>
                        <th>IsRefund</th>
                        <th>Iswin</th>
                        <th>Userid</th>
                        <th>JoinDate</th>
                        <th>RefundDate</th>
                        <th>WinDate</th>
                        <th>UserEmail</th>
                        <th>Username</th>
                        <th>TeamRank</th>
                        <th>RefundAmount</th>
                        <th>Winamount</th>
                        <th>TotalPoints</th>
                        <th>ChallengeName</th>
                        <th>EntryFee</th>
                        <th>ConfirmedLeague</th>
                        <th>UserTeamName</th>
                        <th>Bonus %</th>
                        <th>MaximumUsers</th>
                        <th>IsPrivate</th>
                        <th>MaxNumberofwinners</th>
                        <th>Joined users</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                      $sno=0; $chngeid=false;
                      if(!empty($finduserjoinedleauges)){
                        foreach($finduserjoinedleauges as $joindetail){
                          $sno++;
                          $findifrefunds = array();
                          $findfinalresults = array();
                          $findifrefunds = DB::table('refunds')->where('userid',$joindetail->userid)->where('challengeid',$joindetail->challengeid)->first();
                          $findfinalresults = DB::table('finalresults')->where('userid',$joindetail->userid)->where('challengeid',$joindetail->challengeid)->where('joinedid',$joindetail->id)->first();
                          
                    ?>
                    <tr>
                        <td><?php echo $sno;?></td>
                        <td><?php echo $joindetail->challengeid;?></td>
                        <td>1</td>
                        <td><?php if(!empty($findifrefunds)){ echo 1;}else{ echo 0;}?></td>
                        <td><?php if(!empty($findfinalresults)){ echo 1;}else{ 0;}?></td>
                        <td><?php echo $joindetail->userid;?></td>
                        <td><?php if(!empty($joindetail->created_at)){ echo $joindetail->created_at;}else{ echo 'not available';}?></td>
                        <td><?php if(!empty($findifrefunds)){ echo $findifrefunds->created_at;}else{ echo 'not available';}?></td>
                        <td><?php if(!empty($findfinalresults)){ echo $findfinalresults->created_at;}else{ echo 'not available';}?></td>
                        <td><?php if(!empty($joindetail->email)){echo $joindetail->email;}else{ echo 'not available';}?></td>
                        <td><?php if(!empty($joindetail->username)){echo $joindetail->username;}else{ echo 'not available';}?></td>
                        <td><?php if(!empty($findfinalresults)){ echo $findfinalresults->rank;}else{ echo 'not available';}?></td>
                        <td><?php if(!empty($findifrefunds)){ echo $findifrefunds->amount;}else{ echo 0;}?></td>
                        <td><?php if(!empty($findfinalresults)){ echo $findfinalresults->amount;}else{ echo 0;}?></td>
                        <td><?php echo $joindetail->points;?></td>
                        <?php
                          $cname="";
                            if($joindetail->win_amount==0){
                              $cname  = 'Net practice';
                            }else{
                              $cname  = 'Win Rs.'.$joindetail->win_amount;
                            }
                        ?>
                        <td class="sorting"><?php echo $cname;?></td>
                        <td><?php echo $joindetail->entryfee;?></td>
                        <td><?php if($joindetail->confirmed_challenge==1){ echo 'Yes';}else{ echo 'No'; }?></td>
                        <td><?php echo $joindetail->team;?></td>
                        <td><?php echo $joindetail->bonus_percentage;?></td>
                        <td><?php echo $joindetail->maximum_user;?></td>
                        <td><?php if($joindetail->is_private==1){ echo 'Yes';}else{ echo 'No'; }?></td>
                        <td><?php
                          $findpricecards = DB::table('matchpricecards')->where('challenge_id',$joindetail->challengeid)->get();
                          $winners=0;
                          if(!empty($findpricecards)){
                            foreach($findpricecards as $prc){
                              $winners+=$prc->winners;
                            }
                          }else{
                            $winners=1;
                          }
                        ?><?php echo $winners; ?></td>
                        <td><?php echo $joindetail->joinedusers;?></td>
                    </tr>
                    <?php
                          }
                        }
                      ?>
            </table>
            {{ $finduserjoinedleauges->links() }}
        </div>
    </div>
</div>
@endsection