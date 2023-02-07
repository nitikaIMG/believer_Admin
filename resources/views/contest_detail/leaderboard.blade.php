@extends('main')

@section('heading')
    Leaderboard Manager
@endsection('heading')

@section('sub-heading')
    View Leaderboard
@endsection('sub-heading')

@section('content')
<?php
use App\Helpers\Helpers;
?>

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('ContestFullDetailController@leaderboard')?>">
                  <?php
                      $series="";
                      if(isset($_GET['matchid'])){
                        $series = $_GET['matchid'];
                      }

                      
                      $is_live="";
                      if(isset($_GET['is_live'])){
                        $is_live = $_GET['is_live'];
                      }
                  ?>
                @php
                    $fantasy_type = '';

                    if(isset($_GET['fantasy_type'])) {
                        $fantasy_type = $_GET['fantasy_type'];
                    }
                @endphp
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                {{ Form::label('Select Series','Select Series',array('class'=>'text-bold'))}}
                                <select class="form-control selectpicker show-tick" data-container="body" data-live-search="true" title="Select Series" data-hide-disabled="true" name="matchid" id="series" onchange="this.form.submit()">
                                  <option value="" disabled>Select Series</option>
                                  <?php
                                      if(!empty($findalllistmatches->toarray())){
                                      foreach($findalllistmatches as $matches){
                                        ?>
                                        <option value="<?php echo $matches->id; ?>"
                                         <?php if($matches->id==$series){ echo 'selected'; }?>>
                                          <?php echo ucwords($matches->name);?> </option>
                                        <?php
                                      }
                                    }
                                  ?>
                                </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    {{ Form::label('Live / End','Live / End',array('class'=>'text-bold'))}}
                                    <select class="form-control selectpicker show-tick" data-container="body" data-live-search="true" title="Live / End" data-hide-disabled="true" name="is_live" id="is_live" onchange="this.form.submit()">
                                    <option value="">Live / End</option>
                                    <option value="opened"
                                        @if($is_live == 'opened')
                                            selected
                                        @endif
                                    >Live</option>
                                    <option value="closed"
                                        @if($is_live == 'closed')
                                            selected
                                        @endif
                                    >End</option>
                                    
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>


<div class="card mb-4">
    <div class="card-header">
        <div class="row w-100 align-items-center mx-0">
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View All Series</div>
            
            @if(isset($_GET['matchid']))
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button id="btn-show-all-children" class="btn btn-sm text-uppercase btn-success" type="button">Expand All</button>
                    <button id="btn-hide-all-children" class="btn btn-sm text-uppercase btn-danger" type="button">Collapse All</button>
                </div>
            </div>
            @endif
            
        </div>
    </div>
    <div class="card-body">

        @include('alert_msg')

        <div class="datatable table-responsive w-100">
          <?php if(!empty($allchallenges)){ ?>
            <table class="table table-bordered table-striped table-hover text-nowrap" id="dataTabless" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Series Name</th>
                        
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>Series Name</th>
                        
                         <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                  <?php
                    $i=1;
                    // foreach($allchallenges as $challenge){
                    //   $data = DB::table('matchchallenges')->where('matchkey',$allchallenges->matchkey)->count();
                  ?>
                  <tr>
                        <td><?php echo $i;?></td>
                        
                        <td><?php echo $allchallenges->series_name;?></td>

                        <td>
                            <?php

                                if($allchallenges->has_leaderboard == 'yes'){

                                    $a = action('SeriesController@addmatchpricecard',base64_encode(serialize($allchallenges->series)));

                                    $edit ="<a href='".$a."' class='btn btn-sm btn-info w-35px h-35px text-uppercase' data-toggle='tooltip' title='Add / Edit Price Card'><i class='fas fa-plus'></i></a>";
                                }else{
                                    $edit = "";
                                }
                            ?>

                            <?php echo $edit; ?>

                            <?php

                                if($allchallenges->has_leaderboard == 'yes'){

                                    $a = action('ContestFullDetailController@leaderboard_rank', 'matchid='.$allchallenges->series);

                                    $edit ="<a href='".$a."' class='btn btn-sm btn-primary w-35px h-35px text-uppercase' data-toggle='tooltip' title='Check Rank'><i class='fas fa-eye'></i></a>";
                                }else{
                                    $edit = "";
                                }
                            ?>

                            <?php echo $edit; ?>

                            <!-- declare winner -->
                            <?php
                                if($allchallenges->winning_status == '0'){
                            ?>
                                <a href="<?php echo action('ContestFullDetailController@distribute_winning_amount_series_leaderboard',[$allchallenges->id])?>" class="btn btn-sm text-uppercase btn-success text-white" data-toggle="modal" data-target="#key<?php echo str_replace('.', '', $allchallenges->id);?>"><span data-toggle="tooltip" title="Declare Winner Now"><i class="fad fa-trophy" ></i>&nbsp; Declare Winner</span></a>

                                <!-- Modal -->
                                <div class="modal fade" id="key<?php echo str_replace('.', '', $allchallenges->id);?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Winner Declare</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="<?php echo action('ContestFullDetailController@distribute_winning_amount_series_leaderboard',[$allchallenges->id])?>" method="post">
                                                {{csrf_field()}}
                                                <div class="col-md-12 col-sm-12 form-group">
                                                    <label> Enter Your Master Password </label>
                                                    <input type="password"  name="masterpassword" class="form-control" placeholder="Enter password here" required>
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
                            <?php } ?>

                            <?php

                                if($allchallenges->winning_status == '1'){

                                    $a = action('ContestFullDetailController@leaderboard_winning_rank', 'matchid='.$allchallenges->series);

                                    $winners ="<a href='".$a."' class='btn btn-sm btn-warning w-35px h-35px text-uppercase' data-toggle='tooltip' title='View Winners'><i class='fas fa-eye'></i></a>";
                                }else{
                                    $winners = "";
                                }
                            ?>

                            <?php echo $winners; ?>
                        </td>
                    </tr>

                     <?php $i++;
                    // }
                    ?>
                </tbody>
            </table>
             
            
            <?php } ?>
        </div>
    </div>
</div>

<style>
    .dataTables_info, .dataTables_paginate, .dataTables_length {
        display: none;
    }
</style>
@endsection('content')
