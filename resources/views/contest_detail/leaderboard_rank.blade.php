@extends('main')

@section('heading')
    Leaderboard Manager
@endsection('heading')

@section('sub-heading')
    View Leaderboard Rank
@endsection('sub-heading')

@section('content')
<?php
use App\Helpers\Helpers;
?>

<div class="card mb-4">
    <div class="card-header">
        <div class="row w-100 align-items-center mx-0">
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View All Leaderboard Rank</div>
            
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
                        <th>User Team</th>
                        <th>User Image</th>
                        <th>User Points</th>
                        <th>User Rank</th>
                        
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>User Team</th>
                        <th>User Image</th>
                        <th>User Points</th>
                        <th>User Rank</th>
                        
                    </tr>
                </tfoot>
                <tbody>
                  <?php
                    $i=1;
                    foreach($allchallenges as $challenge){
                    //   $data = DB::table('matchchallenges')->where('matchkey',$allchallenges->matchkey)->count();
                  ?>
                  <tr>
                        <td><?php echo $i;?></td>
                        
                        <td><?php echo $challenge['team'];?></td>
                        
                        <td>
                            <img src="<?php echo $challenge['image'];?>"
                            width="100" />
                        </td>
                        
                        <td><?php echo $challenge['points'];?></td>
                        
                        <td><?php echo $challenge['rank'];?></td>
                        
                    </tr>

                     <?php $i++;
                    }
                    ?>
                </tbody>
            </table>
             
            
            <?php } else {
                echo "<div>
                        <h6 class='text-danger'>No Data found</h6>
                    </div>";
                }?>
        </div>
    </div>
</div>

<style>
    /* .dataTables_info, .dataTables_paginate, .dataTables_length {
        display: none;
    } */
</style>
@endsection('content')
