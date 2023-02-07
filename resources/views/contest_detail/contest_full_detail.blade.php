@extends('main')

@section('heading')
    Full Series Detail
@endsection('heading')

@section('sub-heading')
    View Series Detail
@endsection('sub-heading')

@section('content')
<?php
use App\Helpers\Helpers;
?>

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('ContestFullDetailController@fulldetail1')?>">
                  <?php
                      $series="";
                      if(isset($_GET['matchid'])){
                        $series = $_GET['matchid'];
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
                            <div class="col-md-12">
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
                        <th>Match Name</th>
                        <th>First Team</th>
                        <th>Second Team</th>
                        <th>Match Key</th>
                        <th>Series Name</th>
                        <th>Match Format</th>
                        <th>Start Date</th>
                        <th>Match Status</th>
                        <th>Launch Status</th>
                        <th>Final Status</th>
                        <th>Squad Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>Match Name</th>
                        <th>First Team</th>
                        <th>Second Team</th>
                        <th>Match Key</th>
                        <th>Series Name</th>
                        <th>Match Format</th>
                        <th>Start Date</th>
                        <th>Match Status</th>
                        <th>Launch Status</th>
                        <th>Final Status</th>
                        <th>Squad Status</th>
                         <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                  <?php
                    $i=1;
                    foreach($allchallenges as $challenge){
                      $data = DB::table('matchchallenges')->where('matchkey',$challenge->matchkey)->count();
                  ?>
                  <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo $challenge->name;?></td>
                        <td><?php echo $challenge->teamdata1_team;?></td>

                        <td><?php echo $challenge->teamdata2_team;?></td>

                        <td><?php echo $challenge->matchkey;?></td>

                        <td><?php echo $challenge->series_name;?></td>

                        <td><?php echo ucwords($challenge->format);?></td>

                        <?php
                        $start_date =date('d M, Y',strtotime($challenge->start_date));
                        $ths = date('H',strtotime($challenge->start_date));
                        $tms = date('i',strtotime($challenge->start_date));
                        ?>
                        <td><span class="font-weight-bold text-success"> <?php echo  $start_date.' '.$ths.'hr '.$tms.'min';?></span></td>

                        <td><?php echo $challenge->status;?></td>

                        <td><?php echo $challenge->launch_status?></td>
                        <td><?php echo $challenge->final_status?></td>
                        <td><?php echo $challenge->squadstatus?></td>

                        <?php if($data != ''){ ?>
                        <td><a href=" <?php echo action('ContestFullDetailController@allcontests',$challenge->matchkey);?>"  class="btn btn-sm text-uppercase btn-info"><i class="fas fa-eye"></i>&nbsp; View Contests</a></td>
                      <?php }else{?>
                        <td></td>
                        <?php }?>
                    </tr>

                     <?php $i++;}?>
                </tbody>
            </table>
             {{$allchallenges->appends(['matchid' => $series ])->links()}}
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
