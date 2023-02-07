@extends('main')

@section('heading')
    Player Manager
@endsection('heading')

@section('sub-heading')
    View All Players
@endsection('sub-heading')

@section('content')

   
<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('PlayerController@view_player')?>">
                  <?php
                  $team="";$playername="";$role="";$fantasy_type="";
                  if(isset($_GET['team'])){
                    $team = $_GET['team'];
                  }
                  if(isset($_GET['playername']) ){
                    $playername = $_GET['playername'];
                  }
                  if(isset($_GET['role'])){
                    $role = $_GET['role'];
                  }
                  if(isset($_GET['fantasy_type'])){
                    $fantasy_type = $_GET['fantasy_type'];
                  }
                  ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">
                        
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('Player Role','Player Role',array('class'=>' text-bold','for'=>"role" ))}}
                                <select class="form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Role" data-hide-disabled="true" name="role" id="role">
                                    <option selected value="">Select Role</option>
                                    <option value="batsman" <?php if($role=='batsman'){ echo 'selected'; }?>>Batsman</option>
                                    <option value="bowler" <?php if($role=='bowler'){ echo 'selected'; }?>>Bowler</option>
                                    <option value="allrounder" <?php if($role=='allrounder'){ echo 'selected'; }?>>All rounder</option>
                                    <option value="keeper" <?php if($role=='keeper'){ echo 'selected'; }?>>Wicket Keeper</option>
                                </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    <label for="playername" class="">Player Name</label>
                                    {{ Form::text('playername',$playername,array('value'=>$playername,'placeholder'=>'Search By Name','id'=>'playername','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    <label for="team" class="">Team Name</label>
                                     <select class="form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Team" data-hide-disabled="true" name="team" id="team">
                                          <option value="">Select Team</option>
                                          <?php
                                            if(!empty($findallteams)){
                                              foreach($findallteams as $teams){
                                                ?>
                                                <option value="<?php echo $teams->id;?>" <?php if($team==$teams->id){ echo 'selected'; }?>><?php echo ucwords($teams->team);?></option>
                                                <?php
                                              }
                                            }
                                          ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 text-right mt-4 mb-2">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="{{action('PlayerController@view_player')}}" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View All Players</div>
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                <form action="<?php echo action('PlayerController@downloadallplayerdetails');?>" method="get">
                  <input type="hidden" name="team"  value="<?php echo $team;?>">
                  <input type="hidden" name="playername"  value="<?php echo $playername;?>">
                    <input type="hidden" name="role"  value="<?php echo $role;?>">
                    <input type="hidden" name="fantasy_type"  value="<?php echo $fantasy_type;?>">
                    <button type="submit" class="btn btn-secondary text-uppercase btn-sm rounded-pill" data-toggle="tooltip" title="Download All Player Details" font-weight-600><i class="fad fa-download"></i>&nbsp; Download</a></button>
                </form>

            </div>
        </div>
    </div>
    <div class="card-body">


        @include('alert_msg')


        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover last-btn-center text-nowrap" id="view_player_datatable" width="100%" cellspacing="0">
                <thead>
            <tr>

              <th>Sno.</th>
              <th>Player Name</th>
              <th>Player key</th>
              <th>Role</th>
              <th>Cr</th>
              <th class="myclass">Image</th>
              <th class="myclass1">Action</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
                  <th>Sno.</th>
                  <th>Player Name</th>
                  <th>Player key</th>
                  <th>Role</th>
                  <th>Cr</th>
                  <th>Image</th>
                  <th>Action</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
  </script>
<script type="text/javascript">
    $(document).ready(function() {
      var playername = $('#playername').val();
      var team = $('#team').val();
      var role = $('#role').val();
    // var fantasy_type = $('#fantasy_type').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#view_player_datatable').DataTable({
            'responsive':false,
            'bFilter':false,
            "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/view_player_datatable');?>?playername='+playername+'&team='+team+'&role='+role,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "id" },
                { "data": "player_name" },
                { "data": "players_key" },
                { "data": "role" },
                { "data": "credit" },
                { "data": "image" },
                { "data": "action" }
            ]

        });

});
</script>

<script>
function updateplayer(id,role,credit){
$("#credittd"+id).html('<input type="text" class="rounded-pill shadow border border-dark form-control h-32px" value="'+credit+'" name="role" id="getcredittd'+id+'" onkeypress="return isNumberKey(event)">');
$("#updateplayer"+id).hide();
$("#saveplayer"+id).show();
}

function saveplayer(id){
var credit= $("#getcredittd"+id).val();
  if(credit>15){
    Swal.fire('You cannot enter credit of a player more than 15.');
  }else if(credit=='.' || credit==''){
    alert('Enter valid credit for player');
  }else{
    $.ajax({
      type:'POST',
      url:'<?php echo asset('my-admin/saveplayerroles');?>',
      data:'_token=<?php echo csrf_token();?>&id='+id+'&credit='+credit,
      success:function(data){
      if(data==1){
        $("#credittd"+id).html(credit);
        $("#updateplayer"+id).show();
        $("#saveplayer"+id).hide();

        $('#view_player_datatable').DataTable().ajax.reload();
      }
      }
    });
  }
}

function get_teams(value){
    var fantasy_type = value;
    $.ajax({
    type:'POST',
    url:'<?php echo asset('my-admin/get_teams');?>',
    data:'_token=<?php echo csrf_token();?>&fantasy_type='+fantasy_type,
    success:function(data){
        $('#team').html('<option value="">Select Team</option>');

        for(var i = 0; i < data.length; i++) {
            // alert(data[i]['id']);
            $('#team').append('<option value="'+data[i]['id']+'">'+data[i]['team']+'</option>');
        }

        // <option value="">Select Team</option>
    }
    });
}
$(window).ready(function() {
    var fantasy_type = $('#fantasy_type').val();
    get_teams(fantasy_type);
});
</script>

@endsection('content')
