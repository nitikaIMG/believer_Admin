@extends('main')

@section('heading')
    Full Series Detail
@endsection('heading')

@section('sub-heading')
    View Users Team
@endsection('sub-heading')

@section('content')


<div class="card mb-4">
    <div class="card-header">View User Team</div>
    <div class="card-body">
                
            @include('alert_msg')
            
            <input type="hidden"  id="challngeid" value="<?php echo $challengeid ?? '';?>">
            <div class="datatable table-responsive overflow-auto">
            <table class="table table-bordered table-striped table-hover text-nowrap" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-toggle="tooltip" title="Series Number">#</th>
                        <th>Match key</th>
                        <th data-toggle="tooltip" title="Team Number">T. No.</th>
                        <th>Players</th>
                        <th>Captain</th>
                        <th>Vice Captain</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th data-toggle="tooltip" title="Series Number">#</th>
                        <th>Match key</th>
                        <th data-toggle="tooltip" title="Team Number">T. No.</th>
                        <th>Players</th>
                        <th>Captain</th>
                        <th>Vice Captain</th>
                    </tr>
                </tfoot>
                <?php
                if(count($getdata)>0){
                  $i=1;
                  foreach($getdata as $data){
                ?>
                <tbody>
                  <tr>
                    <td><span class="bg-primary fs-16 mr-1 px-2 text-white font-weight-bold rounded-pill"><?php echo $i;?></span></td>
                    <td class="font-weight-bold">{{ $data->matchkey}}</td>
                    <td class="font-weight-bold text-center"><?php echo $data->teamnumber;?></td>
                    <?php 
                       $allplayers=array();
                       $i=0;
                       $playerss=array();
                       if(!empty($player)){
                        foreach($player as $fpl){
                            $j= $i + 1;
                            $getallplayer=DB::table('players')->where('id',$fpl)->first();
                            $playerss[$i]='<div class="text-white bg-primary rounded-pill ml-1 col px-2 py-1 my-1"><span class="bg-white mr-1 px-1 text-primary font-weight-bold rounded-pill">'. $j .'</span> '. $getallplayer->player_name .' </div>';
                            $i++;
                        }
                        $gaet=implode(' ',$playerss);
                    ?>
                    <td><span><div class="row"><?php echo $gaet;?></div></span></td>
                    <?php }?>
                    <td class="font-weight-bold"><?php echo $captain->player_name;?></td>
                    <td class="font-weight-bold"><?php echo $vice_captain->player_name;?></td>
                  </tr>
                  <?php $i++;}}else{?>
                  <tr>
                    <td colspan="6" class = 'text-center'>{{asset('public/NoRecordFound.png')}}</td>
                  </tr>
                 <?php }?>
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection('content')
