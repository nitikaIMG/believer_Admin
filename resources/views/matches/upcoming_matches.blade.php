@extends('main')

@section('heading')
    Match Manager
@endsection('heading')

@section('sub-heading')
    All upcoming matches
@endsection('sub-heading')

@section('card-heading-btn')
         @php
          $fantasy_type = '';

          if( !empty($_GET['fantasy_type']) ) {
              $fantasy_type = $_GET['fantasy_type'];
          }
        @endphp
        <?php
           if($fantasy_type=='Cricket' or empty($fantasy_type)){?>
           <a  href="<?php echo action('MatchController@importdatafromapi') ?>" class="btn my-1 btn-sm rounded-pill btn-light font-weight-bold text-primary" data-toggle="tooltip" title="Import Match From API"><i class="fa fa-download"></i>&nbsp; Import</a>
         <?php }elseif($fantasy_type=='Football'){?>
            <a  href="<?php echo action('FootballMatchController@GetFootballMatch') ?>" class="btn my-1 btn-sm rounded-pill btn-light font-weight-bold text-primary" data-toggle="tooltip" title="Import Match From API"><i class="fa fa-download"></i>&nbsp; Import</a>
         <?php }?>

@endsection('card-heading-btn')

@section('content')
<div class="card mb-4">
    <div class="card-header">Matches</div>
    <div class="card-body">

        @include('alert_msg')

        
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover last-btn-center text-nowrap" id="dataTable" width="100%" cellspacing="0">
                <thead>
            <tr>
              <th class="text-center">Sno.</th>
              <th class="text-center">Match</th>
              <th class="text-center">Match Time</th>
              <th class="text-center">Squad Available</th>
              <th class="text-center">Action</th>
            </tr>
            </thead>
             <tbody>
              <?php $sno=0;?>
              <?php
              // echo "<pre>";print_r($findalllistmatches);die;
               foreach($findalllistmatches as $fmatch){
                $sno++;
              ?>
                <tr role="row" class="odd">
                   <td class="text-center"><?php echo $sno;?></td>
                   <td class="text-center">
                       <div class="d-flex align-items-center">
                           <div class="col">
                               <div class="row">
                                   <div class="col-12 fs-13 text-center"><?php echo $fmatch->team1team; ?></div>
                                   <div class="col-12 text-center">
                                        <?php 
                                       
                                        if($fmatch->team1logo != ""){
                                            $c = count(explode(':', $fmatch->team1logo));
                                            if ($c >= 2) {
                                                $team1logo = $fmatch->team1logo;
                                            } else {
                                                $team1logo = '/public/' . $fmatch->team1logo;
                                            }
                                            ?>
                                            <img src="<?php echo URL::asset($team1logo)?>" class="w-40px view_team_table_images2 h-40px rounded-pill" onerror="this.src='<?php echo URL::asset('public/team_image.png')?>'">
                                        <?php }else{?>
                                            <img src="<?php echo URL::asset('public/team_image.png')?>" class="w-40px view_team_table_images2 h-40px rounded-pill">
                                        <?php }?>
                                   </div>
                               </div>
                           </div>
                           <div class="col-auto">
                               <div class="row">
                                   <div class="col-12 text-center font-weight-bold">V/S</div>
                               </div>
                           </div>
                           <div class="col">
                               <div class="row">
                                   <div class="col-12 fs-13 text-center"><?php echo $fmatch->team2team; ?></div>
                                   <div class="col-12 text-center">
                                        <?php if($fmatch->team2logo != "") {
                                            $c = count(explode(':', $fmatch->team2logo));;
                                            if ($c >= 2) {
                                                $team2logo = $fmatch->team2logo;
                                            } else {
                                                $team2logo = '/public/' . $fmatch->team2logo;
                                            }
                                            ?>
                                            <img src="<?php echo URL::asset($team2logo)?>" class="w-40px view_team_table_images2 h-40px rounded-pill" onerror="this.src='<?php echo URL::asset('public/team_image.png')?>'">
                                        <?php }else{ ?>
                                            <img src="<?php echo URL::asset('public/team_image.png')?>" class="w-40px view_team_table_images2 h-40px rounded-pill">
                                        <?php }?>
                                   </div>
                               </div>
                           </div>
                       </div>

                   </td>
                   <td class="text-center">
                       <span class="font-weight-bold text-success"><?php echo date('l,',strtotime($fmatch->start_date))?></span><br>
                       <span class="font-weight-bold text-primary"><?php echo date('d-M-y',strtotime($fmatch->start_date))?></span><br>
                       <span class="font-weight-bold text-danger"><?php echo date('h:i:s a',strtotime($fmatch->start_date))?></span>
                    </td>
                   <td class="text-center">
                     <?php if($fmatch->squadstatus =='no'){ echo '<i class="fad fa-users text-danger"></i> <span class="text-danger">NO</span>'; } else{ echo '<i class="fad fa-users text-success"></i>  <span class="text-success">YES</span>'; }?>
                   </td>
                   <td class="text-center">
                    
                         <?php
                            if($fantasy_type=='Cricket' or empty($fantasy_type)){?>
                          <?php if(($fmatch->team1team == 'TBC') || ($fmatch->team2team == 'TBC')  || ($fmatch->team1team == 'TBC A')|| ($fmatch->team2team == 'TBC B')|| ($fmatch->team1team == 'tbc') || ($fmatch->team2team == 'tbc')){
                            ?>
                             <a href="<?php echo action('MatchController@importteam',$fmatch->matchkey)?>" class="btn my-1 btn-sm btn-success w-35px h-35px" data-toggle="tooltip" title="Import Team"><i class="fas fa-users"></i></a>
                          <?php } ?>
                       <?php if($fmatch->squadstatus =='no'){
                       ?>
                          <a href="<?php echo action('MatchController@importsquad',$fmatch->matchkey)?>" class=" btn-sm btn my-1 btn-success w-35px h-35px" data-toggle="tooltip" title="Import Players"><i class="fad fa-download"></i></a>
                       <?php } else{ ?>
                          <a href="<?php echo action('MatchController@editmatch',[$fmatch->matchkey, 'fantasy_type' => 'Cricket'])?>" class="btn-sm btn my-1 btn-info w-35px h-35px" data-toggle="tooltip" title="Edit Match & series"><i class="fas fa-pencil"></i></a>
                           <?php if(!empty($fmatch->seriesname)){
                           if($fmatch->seriesname != ""){?>
                               <?php if($fmatch->launch_status !='launched'){?>
                                  <a href="<?php echo action('MatchController@launchmatch',$fmatch->matchkey)?>" class="btn-sm btn my-1 btn-primary w-35px h-35px" data-toggle="tooltip" title="Launch Match"><i class="fas fa-rocket"></i></a>
                              <?php }else{ ?>
                                  <a href="<?php echo action('MatchController@launchmatch',$fmatch->matchkey)?>" class="btn-sm btn my-1 btn-orange w-35px h-35px" data-toggle="tooltip" title="View Match"><i class="far fa-eye"></i></a></button>
                              <?php }
                           }
                         }
                       }
                              }elseif($fantasy_type=='Football'){
                        if($fmatch->squadstatus =='no'){ ?>
                          <a href="<?php echo action('FootballMatchController@GetMatchPlayers',$fmatch->matchkey)?>" class=" btn-sm btn my-1 btn-success w-35px h-35px" data-toggle="tooltip" title="Import Players" ><i class="fad fa-download"></i></a>
                       <?php } else{ ?>
                          <a href="<?php echo action('MatchController@editmatch',[$fmatch->matchkey, 'fantasy_type' => 'Football'])?>" class="btn-sm btn my-1 btn-info w-35px h-35px" data-toggle="tooltip" title="Edit Match & series"><i class="fas fa-pencil"></i></a>
                           <?php if($fmatch->seriesname != ""){?>
                               <?php if($fmatch->launch_status !='launched'){?>
                                  <a href="<?php echo action('FootballMatchController@launchmatch',$fmatch->matchkey)?>" class="btn-sm btn my-1 btn-primary w-35px h-35px" data-toggle="tooltip" title="Launch Match"><i class="fas fa-rocket"></i></a>
                              <?php }else{ ?>
                                  <a href="<?php echo action('FootballMatchController@launchmatch',$fmatch->matchkey)?>" class="btn-sm btn my-1 btn-orange w-35px h-35px" data-toggle="tooltip" title="View Match"><i class="far fa-eye"></i></a></button>
                              <?php }
                          }
                      } }?>
                   </td>
                </tr>
                <?php } ?>
            </tbody>
          <tfoot>
            <tr>
            <th class="text-center">Sno.</th>
            <th class="text-center">Match</th>
            <th class="text-center">Match Time</th>
            <th class="text-center">Squad Available</th>
            <th class="text-center">Action</th>
            </tr>
          </tfoot>
            </table>
            <div class="row mx-0">
              <div class="col-auto ml-auto">
                {{$findalllistmatches->appends(['fantasy_type' => $fantasy_type])->links()}}
              </div>
            </div>
        </div>
    </div>
</div>
<script>
    function change_fantasy() {
        $('#change_fantasy').submit();
    }
</script>
<style>
    .dataTables_info, .dataTables_paginate, .dataTables_length {
        display: none;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
        $('#dataTable').DataTable({
            'bFilter': false,
             "processing": true,
            "serverSide": true,
        });
});
</script>
@endsection('content')
