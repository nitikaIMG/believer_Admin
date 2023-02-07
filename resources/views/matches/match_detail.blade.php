@extends('main')

@section('heading')
    Result Manager
@endsection('heading')

@section('sub-heading')
    Match Listing - All Matches
@endsection('sub-heading')

@section('content')

@include('alert_msg')

<div class="card mb-4">
    <div class="card-header">Match Listing - All Matches</div>
    <div class="card-body">
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap " id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Match Name</th>
                        <th>Match Status</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>Match Name</th>
                        <th>Match Status</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                      $s_no=1;
                      if(count($findalllistmatches)>0){
                        foreach($findalllistmatches as $fmatch){
                    ?>
                    <tr>
                      <td><?php echo $s_no;?></td>
                      <td class="sorting_1">
                          <div class="row">
                            <div class="col-12 my-1"><a class="text-decoration-none text-secondary font-weight-600 fs-16" href="<?php echo action('ResultController@match_score',$fmatch->listmatches_matchkey)?>"><?php echo $fmatch->listmatches_title; ?>&nbsp;<i class="fad fa-caret-right"></i></a></div>
                            <div class="col-12 my-1"><span class="text-dark"><?php echo date('l,',strtotime($fmatch->listmatches_start_date)); ?></span><span class="text-warning"><?php echo date(' d-M-y',strtotime($fmatch->listmatches_start_date)); ?></span><span class="text-success ml-2"><?php echo date(' h:i:s a',strtotime($fmatch->listmatches_start_date)); ?></span></div>
                            <div class="col-12 my-1"><a class="text-decoration-none text-secondary font-weight-600" href="<?php  echo URL::asset('my-admin/allcontests/'.$fmatch->listmatches_matchkey) ?>">Total Contests : &nbsp; <?php echo $fmatch->total_challenge; ?>&nbsp;&nbsp;&nbsp;<i class="fad fa-caret-right"></i></a></div>
                            {{--<div class="col-12"><a class="text-decoration-none text-secondary font-weight-600" href="javascript::void()">Contest Profit/loss Report : <?php echo $fmatch->total_joinedusers; ?></a></div>--}}
                            <div class="col-12 my-1"><span class="text-decoration-none text-dark font-weight-600">Match status : <?php echo $fmatch->listmatches_final_status; ?></span></div>
                          </div>
                        </td>
                      <?php
                        // $totalmatch = DB::table('listmatches')->where('series',$fmatch->series_id)->join('matchchallenges','matchchallenges.matchkey','=','listmatches.matchkey')->get();
                      ?>
                      <td>
                          <div class="row">
                        <?php
                          if($fmatch->listmatches_final_status!='winnerdeclared'){
                        ?>
                        <?php } ?>
                        <?php
                          if($fmatch->listmatches_final_status=='pending'){
                            
                        ?>
                        <?php
                            $onclick = "delete_sweet_alert('".action('ResultController@updatematchfinalstatus',[$fmatch->listmatches_matchkey,'IsAbandoned'])."', 'Are you sure you want to Abandoned this match?')";
                        ?>
                          <div class="col-12 my-1"><a class="text-info text-decoration-none font-weight-600"  onclick="<?php echo $onclick; ?>">Is Abandoned&nbsp;<i class="fad fa-caret-right"></i></a></div>
                        <?php
                            $onclick = "delete_sweet_alert('".action('ResultController@updatematchfinalstatus',[$fmatch->listmatches_matchkey,'IsCanceled'])."', 'Are you sure you want to cancel this match?')";
                        ?>
                          <div class="col-12 my-1"><a class="text-danger text-decoration-none font-weight-600" onclick="<?php echo $onclick; ?>">Is Canceled&nbsp;<i class="fad fa-caret-right"></i></a></div>
                          <?php if($fmatch->listmatches_status=='started'){ ?>
                            <?php
                              $onclick = "delete_sweet_alert('".action('ResultController@updatematchfinalstatus',[$fmatch->listmatches_matchkey,'IsClosed'])."', 'Are you sure you want to close this match?')";
                            ?>
                            <div class="col-12 my-1"><a class="text-danger text-decoration-none font-weight-600" onclick="<?php echo $onclick; ?>">Is Closed&nbsp;<i class="fad fa-caret-right"></i></a></div>
                          <?php } ?>
                        <?php } else if($fmatch->listmatches_status == 'completed' && $fmatch->listmatches_final_status!='IsReviewed'&& $fmatch->listmatches_final_status!='winnerdeclared'){
                        ?>
                        <?php
                            $onclick = "delete_sweet_alert('".action('ResultController@updatematchfinalstatus',[$fmatch->listmatches_matchkey,'IsCanceled'])."', 'Are you sure you want to cancel this match?')";
                        ?>
                          <div class="col-12 my-1"><a class="text-danger text-decoration-none font-weight-600" onclick="<?php echo $onclick; ?>">Is Canceled&nbsp;<i class="fad fa-caret-right"></i></a></div>
                          <div class="col-12 my-1"><a class="text-warning text-decoration-none font-weight-600" href="<?php echo action('ResultController@match_points',$fmatch->listmatches_matchkey)?>">Is Reviewed&nbsp;<i class="fad fa-caret-right"></i></a></div>
                        <?php
                          }
                          else if($fmatch->listmatches_status == 'completed' && $fmatch->listmatches_final_status=='IsReviewed'){
                        ?>
                        
                          <div class="col-12 my-1"><a class="text-warning text-decoration-none font-weight-600" href="<?php echo action('ResultController@match_points',$fmatch->listmatches_matchkey)?>">Is Reviewed&nbsp;<i class="fad fa-caret-right"></i></a></div>

                          <div class="col-12 my-1"><a class="text-success text-decoration-none font-weight-600 pointer" data-toggle="modal" data-target="#keys<?php echo $s_no;?>">Is Winner Declared&nbsp;<i class="fad fa-caret-right"></i></a></div>

                          <?php
                              $onclick = "delete_sweet_alert('".action('ResultController@updatematchfinalstatus',[$fmatch->listmatches_matchkey,'IsAbandoned'])."', 'Are you sure you want to Abandoned this match?')";
                          ?>
                          <div class="col-12 my-1"><a class="text-info text-decoration-none font-weight-600" onclick="<?php echo $onclick; ?>">Is Abandoned&nbsp;<i class="fad fa-caret-right"></i></a></div>
                          <?php
                            $onclick = "delete_sweet_alert('".action('ResultController@updatematchfinalstatus',[$fmatch->listmatches_matchkey,'IsCanceled'])."', 'Are you sure you want to cancel this match?')";
                        ?>
                          <div class="col-12 my-1"><a class="text-danger text-decoration-none font-weight-600" onclick="<?php echo $onclick; ?>">Is Canceled&nbsp;<i class="fad fa-caret-right"></i></a></div>
                        <?php
                          }
                          else if ($fmatch->listmatches_status == 'completed' && $fmatch->listmatches_final_status=='IsClosed'){
                        ?>
                          <?php
                            $onclick = "delete_sweet_alert('".action('ResultController@updatematchfinalstatus',[$fmatch->listmatches_matchkey,'IsCanceled'])."', 'Are you sure you want to cancel this match?')";
                        ?>
                          <div class="col-12 my-1"><a class="text-danger text-decoration-none font-weight-600" onclick="<?php echo $onclick; ?>">Is Canceled&nbsp;<i class="fad fa-caret-right"></i></a></div>
                          <div class="col-12 my-1"><a class="text-warning text-decoration-none font-weight-600"  href="<?php echo action('ResultController@match_points',$fmatch->listmatches_matchkey)?>">Is Reviewed&nbsp;<i class="fad fa-caret-right"></i></a></div>
                        <?php
                          }else if ($fmatch->listmatches_final_status=='winnerdeclared'){
                        ?>
                          <div class="col-12 text-dark pointer"> Winner Declared</div>
                          <div class="col-12"><a class="btn btn-sm btn-success rounded-pill text-uppercase"  href="<?php echo action('ResultController@viewwinners',$fmatch->listmatches_matchkey) ?>"><i class="fad fa-eye"></i>&nbsp; View winners &nbsp;<i class="fad fa-caret-right"></i></a></div>
                        <?php
                          }
                        ?>

                        <?php
                        if($fmatch->listmatches_final_status == 'winnerdeclared'){
                        ?>
                        <div class="col-12 my-1">
                          <a 
                          href="<?php echo action('api\MatchApiController@series_leaderboard_match_wise',$fmatch->listmatches_matchkey)?>"
                          class="text-primary text-decoration-none font-weight-600" >Update Leaderboard Points <i class="fad fa-caret-right"></i></a>
                        </div>
                        <?php } ?>
                        </div>
                      </td>
                      <div id="keys<?php echo $s_no;?>" class="modal fade" role="dialog" >
                          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  w-100 h-100">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title">IsWinnerDeclared</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                              </div>
                              <div class="modal-body abcd">
                                <form action="<?php echo action('ResultController@updatematchfinalstatus',[$fmatch->listmatches_matchkey,'winnerdeclared'])?>" method="post">
                                {{csrf_field()}}
                                <div class="col-md-12 col-sm-12 form-group">
                                  <label> Enter Your Master Password </label>
                                   <input type="password"  name="masterpassword" class="form-control form-control-solid" placeholder="Enter password here">
                                </div>
                                <div class="col-auto text-right ml-auto mt-4 mb-2">
                                  <button type="submit" class="btn btn-sm btn-success text-uppercase "><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                </div>
                                </form>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" >Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    </tr>
                    <?php $s_no++;}?>
                    <?php }else{?>
                    <tr>
                      <td colspan="3" class="text-center">No Data Available</td>
                    </tr>
            <?php }?>
            </table>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
$.ajaxSetup({
headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});
});

function muldelete() {
var p=[];
$.each($("input[name='checkCat']:checked"), function(){
p.push($(this).val());
});

if(p!=""){
var datavar = '_token=<?php echo csrf_token();?>&hg_cart='+p;
var ok=confirm('Are you you want to delete this data');
if(ok){
$.ajax({
          type:'POST',
          url:'<?php echo asset('my-admin/muldelete');?>',
          data:datavar,
success:function(data){
if(data==1){
window.location.reload();
}
          }
       });
}
}
else{
Swal.fire('Please Select Series to delete');
}
}
</script>
@endsection
