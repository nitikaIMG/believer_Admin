@extends('main')

@section('heading')
    Result Manager
@endsection('heading')

@section('sub-heading')
    Match Score
@endsection('sub-heading' )

@section('card-heading-btn')




  <?php
    if($findmatchdetails->status=='completed' &&  $findmatchdetails->final_status=='IsReviewed'){
  ?>
    <a href="<?php echo action('ResultController@updatematchfinalstatus',[$findmatchdetails->matchkey,'winnerdeclared'])?>" class="btn btn-sm text-uppercase btn-light text-primary" data-toggle="modal" data-target="#key<?php echo str_replace('.', '', $findmatchdetails->matchkey);?>"><span data-toggle="tooltip" title="Declare Winner Now"><i class="fad fa-trophy" ></i>&nbsp; Declare Winner</span></a>


  <!-- Modal -->
  <div class="modal fade" id="key<?php echo str_replace('.', '', $findmatchdetails->matchkey);?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Winner Declare</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form action="<?php echo action('ResultController@updatematchfinalstatus',[$findmatchdetails->matchkey,'winnerdeclared'])?>" method="post">
                {{csrf_field()}}
                  <div class="col-md-12 col-sm-12 form-group">
                    <label> Enter Your Master Password </label>
                      <input type="password"  name="masterpassword" class="form-control" placeholder="Enter password here">
                  </div>
                  <div class="col-md-12 col-sm-12 form-group">
                      <input type="submit" class="btn btn-info btn-sm text-uppercase" value="Submit">
                  </div>
                </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm text-uppercase" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


        <?php }else{ ?>
          <span>{{ $findmatchdetails->final_status }} </span>
        <?php }?>
          <a href="<?php echo action('ResultController@updatescores',$findmatchdetails->matchkey)?>" class="btn btn-sm text-uppercase btn-info" data-toggle="tooltip" title="Refresh Scores"><i class="far fa-undo" ></i>&nbsp; Refresh </a>
          @if($findmatchdetails->final_status=='winnerdeclared')
          <a href="<?php echo action('ProfitLossController@updatereport') ?>" class="btn btn-sm btn-success" style="margin-left:10px;">Update Profit & Loss</a>
          <a href="<?php echo action('YoutuberBonusController@give_youtuber_bonus', 'matchkey='.$findmatchdetails->matchkey)?>" class="btn btn-sm btn-success" style="margin-left:10px;">Give Youtuber Bonus</a>
          <!-- <a href="<?php //echo action('UserBonusController@give_user_bonus', 'matchkey='.$findmatchdetails->matchkey)?>" class="btn btn-sm btn-warning" style="margin-left:10px;">Give Normal User Bonus</a> -->
          
          @endif
          <!-- <a href="<?php echo action('YoutuberBonusController@give_youtuber_bonus', 'matchkey='.$findmatchdetails->matchkey)?>" class="btn btn-sm btn-success" style="margin-left:10px;">Give Youtuber Bonus</a> -->

@endsection('card-heading-btn')

@section('content')
<?php
use Carbon\Carbon;
?>
<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form action="{{asset('my-admin/match_score/'.$findmatchdetails->matchkey)}}">

                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0 align-items-end">
                            <div class="col-md">
                                <div class="form-group my-3">
                                {{ Form::label('Enter Player Name','Enter Player Name',array('class'=>'text-bold'))}}
                                <input name="player_name" placeholder="Enter Player Name" class="form-control form-control-solid" value="{{$_GET['player_name'] ?? ''}}">
                                </div>
                            </div>



                            <div class="col-md-auto text-right mb-md-3">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                              <button type="reset" value="Reset" class="btn btn-sm btn-warning text-uppercase" onclick="window.location.href='{{asset('my-admin/match_score/'.$findmatchdetails->matchkey)}}'"><i class="far fa-undo" ></i>&nbsp; Reset</buttom>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
<div class="row mx-0">
      <div class="col-lg-12">
            <ul class="row text-center bg-white shadow rounded overflow-hidden match_points_tabses list-unstyled">
              <li class="col-md p-0"><a class="py-3 text-decoration-none font-weight-bold text-uppercase w-100 d-block active" href="{{ asset('my-admin/match_score/'.$findmatchdetails->matchkey)}}">Match Score</a></li>
              <li class="col-md p-0"><a class="py-3 text-decoration-none font-weight-bold text-uppercase border-left w-100 d-block" href="{{ asset('my-admin/match_points/'.$findmatchdetails->matchkey)}}">Match Points</a></li>
              <li class="col-md p-0"><a class="py-3 text-decoration-none font-weight-bold text-uppercase border-left w-100 d-block" href="{{ asset('my-admin/batting_points/'.$findmatchdetails->matchkey)}}">BATTING</a></li>
              <li class="col-md p-0"><a class="py-3 text-decoration-none font-weight-bold text-uppercase border-left w-100 d-block" href="{{ asset('my-admin/bowling_points/'.$findmatchdetails->matchkey)}}">BOWLING</a></li>
              <li class="col-md p-0"><a class="py-3 text-decoration-none font-weight-bold text-uppercase border-left w-100 d-block" href="{{ asset('my-admin/fielding_points/'.$findmatchdetails->matchkey)}}">FIELDING</a></li>
              <li class="col-md p-0"><a class="py-3 text-decoration-none font-weight-bold text-uppercase border-left w-100 d-block" href="{{ asset('my-admin/team_points/'.$findmatchdetails->matchkey)}}">TEAM</a></li>
          </ul>
      </div>
    </div>

<div class="card mb-4">
    <div class="card-header">
        <div class="row w-100 align-items-center mx-0">
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">Match Score</div>
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button id="btn-show-all-children" class="btn btn-sm text-uppercase btn-success" type="button">Expand All</button>
                    <button id="btn-hide-all-children" class="btn btn-sm text-uppercase btn-danger" type="button">Collapse All</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('alert_msg')

            <div class="datatable table-responsive">
                    <table id="example_id" class="table table-bordered table-striped table-hover text-nowrap display" cellspacing="0" width="100%" role="grid" aria-describedby="demo-dt-basic_info" style="width: 100%;">
                     <thead>
                        <tr>

                        <th>
                            Team Name
                        </th>
                         <th>
                            Player Key
                        </th>
                        <th>
                            Player Name
                        </th>
                        <th>
                            Player Role
                        </th>
                        <th>
                            Start Point
                        </th>

                        <th>
                            Dismiss All Info
                        </th>
                        <th>
                            Batting Run
                        </th>
                        <th>
                            Batting Ball
                        </th>
                        <th>
                            Fours
                        </th>
                        <th>
                            Sixs
                        </th>

                         <th>
                            Overs
                        </th>
                        <th>
                            Maidens
                        </th>
                        <th>
                            Balling Balls
                        </th>
                        <th>
                            Balling Runs
                        </th>
                        <th>
                            Wickets
                        </th>
                        <th>
                            Zeros
                        </th>
                        <th>
                            Extra
                        </th>
                         <th>
                            Catch
                        </th>
                        <th>
                            Stumbed
                        </th>
                        <th>
                            Thrower
                        </th>
                        <th>
                            Hitter
                        </th>
                        <th>
                            Duck
                        </th>

                        <th>
                            Man Of Match
                        </th>
                        <th>
                             Batting Points
                        </th>
                        <th>
                            Bowling Points
                        </th>
                        <th>
                            Fielding Points
                        </th>
                        <th>
                            Extra Points
                        </th>
                        <th>
                            Negative Points
                        </th>
                        <th>
                            Strike Rate
                        </th>
                        <th>
                            Economy Rate
                        </th>
                </tr>
            </thead>
             <tbody>
                <?php if(!empty($match_scores)){ ?>
                <?php foreach($match_scores as $val1){ ?>
                    <tr>

                            <td class="text-uppercase font-weight-bold">
                                <?php echo $val1->team_short_name; ?>
                            </td>
                            <td>
                                <?php echo $val1->players_player_key; ?>
                            </td>
                            <td>
                                <?php echo $val1->matchplayers_name; ?>
                            </td>
                            <td>
                                <?php echo $val1->matchplayers_role; ?>
                            </td>
                            <?php   if($findmatchdetails->final_status!='winnerdeclared'){ ?>
                            <td>
                                <input type="text" value="<?php echo $val1->starting11; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','starting11',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>

                           <td>
                               <div class="table_text_marqueses">
                                   <div class="col-12 px-0"><?php echo $val1->out_str; ?></div>
                               </div>

                                {{-- <input type="text" value="<?php //echo $val1->out_str; ?>" onchange="update_points('<?php //echo $val1->matchplayers_matchkey; ?>','<?php //echo $val1->matchplayers_playerid; ?>','out_str',this.value);" class="text-center w-200px rounded-pill shadow border-dark" onkeypress="return isNumberKey(event)" readonly> --}}
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->runs; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','runs',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->bball; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','bball',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->fours; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','fours',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->six; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','six',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->overs; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','overs',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->maiden_over; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','maiden_over',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->balls; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','balls',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->grun; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','grun',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->wicket; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','wicket',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->balldots; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','balldots',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->extra; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','extra',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->catch; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','catch',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->stumbed; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','stumbed',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->thrower; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','thrower',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->hitter; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','hitter',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->duck; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','duck',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->man_of_match; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','man_of_match',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->batting_points; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','batting_points',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->bowling_points; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','bowling_points',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->fielding_points; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','fielding_points',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->extra_points; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','extra_points',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->negative_points; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','negative_points',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->strike_rate; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','strike_rate',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <td>
                                <input type="text" value="<?php echo $val1->economy_rate; ?>" onchange="update_points('<?php echo $val1->matchplayers_matchkey; ?>','<?php echo $val1->matchplayers_playerid; ?>','economy_rate',this.value);" class="text-center w-80px rounded-pill shadow border-primary" onkeypress="return isNumberKey(event)" >
                            </td>
                            <?php } else{ ?>

                            <td>
                                <?php echo $val1->starting11; ?>
                            </td>

                           <td>
                                <?php echo $val1->out_str; ?>
                            </td>
                            <td>
                                <?php echo $val1->runs; ?>
                            </td>
                            <td>
                                <?php echo $val1->bball; ?>
                            </td>
                            <td>
                                <?php echo $val1->fours; ?>
                            </td>
                            <td>
                                <?php echo $val1->six; ?>
                            </td>
                            <td>
                                <?php echo $val1->overs; ?>
                            </td>
                            <td>
                                <?php echo $val1->maiden_over; ?>
                            </td>
                            <td>
                                <?php echo $val1->balls; ?>
                            </td>
                            <td>
                                <?php echo $val1->grun; ?>
                            </td>
                            <td>
                                <?php echo $val1->wicket; ?>
                            </td>
                            <td>
                                <?php echo $val1->balldots; ?>
                            </td>
                            <td>
                                <?php echo $val1->extra; ?>
                            </td>
                            <td>
                                <?php echo $val1->catch; ?>
                            </td>
                            <td>
                                <?php echo $val1->stumbed; ?>
                            </td>
                            <td>
                                <?php echo $val1->thrower; ?>
                            </td>
                            <td>
                                <?php echo $val1->hitter; ?>
                            </td>
                            <td>
                                <?php echo $val1->duck; ?>
                            </td>
                            <td>
                                <?php echo $val1->man_of_match; ?>
                            </td>
                            <td>
                                <?php echo $val1->batting_points; ?>
                            </td>
                            <td>
                                <?php echo $val1->bowling_points; ?>
                            </td>
                            <td>
                                <?php echo $val1->fielding_points; ?>
                            </td>
                            <td>
                                <?php echo $val1->extra_points; ?>
                            </td>
                            <td>
                                <?php echo $val1->negative_points; ?>
                            </td>
                            <td>
                                <?php echo $val1->strike_rate; ?>
                            </td>
                            <td>
                                <?php echo $val1->economy_rate; ?>
                            </td>
                                <?php
                            }?>
                    </tr>
                    </tr>
                <?php } ?>
                <?php } ?>
            </tbody>


        </table>



        </div>
        </div>
    </div>



<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.js"></script>

<script>
    $(document).ready(function (){
        var table = $('#example_id').DataTable({
            'responsive': true
        });

        // Handle click on "Expand All" button
        $('#btn-show-all-children').on('click', function(){
            // Expand row details
            table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click');
        });

        // Handle click on "Collapse All" button
        $('#btn-hide-all-children').on('click', function(){
            // Collapse row details
            table.rows('.parent').nodes().to$().find('td:first-child').trigger('click');
        });
    });
</script>


      </div>






    </div>
    <!-- End container-fluid-->

   </div>
<script>
function update_points(matchkey,playerid,field,value){
  Swal.fire(value);
  $.ajax({
     type:'POST',
     url:'<?php echo asset('my-admin/updatepoints');?>',
     data:'_token=<?php echo csrf_token();?>&matchkey='+matchkey+'&playerid='+playerid+'&field='+field+'&value='+value,
     success:function(data){
       if(data==1){
         location.reload();
       }
    }
  });
}


</script>
@endsection
