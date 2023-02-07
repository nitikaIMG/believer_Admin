@extends('main')

@section('heading')
    Match Manager
@endsection('heading')

@section('sub-heading')
    Update Playing XI
@endsection('sub-heading')

@section('content')


<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
            {{ Form::open(array('action' => 'PlayingController@updateplaying11', 'method' => 'get', 'id' => "matform" ))}}
                  <?php

                    $mkey = $matchkey = '';$team1 = 0;$team2 = 0;
                    if(!empty($_GET['matchkey'])){
                    $mkey = $matchkey = $_GET['matchkey'];
                    }
                    if(isset($teamdata)){
                        $team1 = $teamdata->team1;
                        $team2 = $teamdata->team2;
                    }
                    $fantasy_type = '';
                    if(!empty($_GET['fantasy_type'])){
                    $fantasy_type = $_GET['fantasy_type'];
                    }
                  ?>
                
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0 align-items-center">
                            <div class="col-md col-12">
                                <div class="form-group my-3">
                                    <label class="control-label" for="first-name">Match Name <span class="required">*</span></label>
                                    <select required="required" name="matchkey" class="form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Match" data-hide-disabled="true" id="matckey" onchange="matcke();">
                                        <option value="" disabled>Select match</option>
                                        @if(!empty($listmatch))
                                        @foreach($listmatch as $val)
                                            <option <?php if($matchkey == $val->matchkey){ echo 'selected'; } ?> value="{{ $val->matchkey }}">{{ $val->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-auto col-12 mt-md-4 pt-md-1 text-right">
                                @if(!empty($matchkey))
                                    <a href="{{ action('PlayingController@launchplaying',$matchkey) }}" class="btn btn-sm btn-primary text-uppercase" data-toggle="tooltip" title="Launch Playing 11" onclick="check_is_selected()"><i class="far fa-rocket-launch"></i> &nbsp;Launch</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                </div>

            {{Form::close()}}
        </div>
      </div>
    </div>

<div class="card mb-4">
    <div class="card-header">
        <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">
            @if(isset($teamdata))
                {{ $teamdata->t1name ?? 'team1' }}
            @else
                Team 1
            @endif
        </div>
        <div class="col-md-auto col-12 px-md-3 px-0 text-center">
            @if(!empty($team1))
                <button type="submit" class="btn btn-sm btn-primary pull-right" form="team1">Update</button>
            @endif
        </div>
    </div>
    <div class="card-body">

    @include('alert_msg')

      <form method="post" action="{{ action('PlayingController@upp1',[$mkey,$team1]) }}" id="team1">
        {{csrf_field()}}
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="player1table" width="100%" cellspacing="0">
                <thead>
            <tr>

              <th>S No.</th>
              <th>Player Name</th>
              <th>Player Role</th>
              <th>Player Credit</th>
              <th>Action</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
                  <th>S No.</th>
                  <th>Player Name</th>
                  <th>Player Role</th>
                  <th>Player Credit</th>
                  <th>Action</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</form>
</div>

<div class="card mb-4">
    <div class="card-header">
        <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">
            @if(isset($teamdata))
                {{ $teamdata->t2name ?? 'team2' }}
            @else
                Team 2
            @endif
        </div>
        <div class="col-md-auto col-12 px-md-3 px-0 text-center">
            @if(!empty($team2))
                <button type="submit" class="btn btn-sm btn-primary pull-right" form="team2">Update</button>
            @endif
        </div>
    </div>
    <div class="card-body">
      <form method="post" action="{{ action('PlayingController@upp2',[$mkey,$team2]) }}" id="team2">
            {{csrf_field()}}
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="player2table" width="100%" cellspacing="0">
                <thead>
            <tr>

              <th>S No.</th>
              <th>Player Name</th>
              <th>Player Role</th>
              <th>Player Credit</th>
              <th>Action</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
                  <th>S No.</th>
                  <th>Player Name</th>
                  <th>Player Role</th>
                  <th>Player Credit</th>
                  <th>Action</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</form>
</div>

<style>
    .highlight{
  background: #ddd;
}
table tr td{
  border: solid thin #ccc;
}
</style>
<script>
    $(function() {
  $('td:first-child input').change(function() {
    $(this).closest('tr').toggleClass("highlight", this.checked);
  });
});
</script>
<script type="text/javascript">
function matcke(){
    $('#matform').submit();
}
    $(document).ready(function() {

    $.fn.dataTable.ext.errMode = 'none';

        $('#player1table').DataTable({
            'bFilter':false,
             "processing": true,
             "sAjaxSource":'<?php echo asset('my-admin/match_player1/'.$mkey.'/'.$team1);?>',
             "dom": 'lBfrtip',
             "lengthMenu": [[50,100,1000,10000 ], [50,100,1000,10000]],
             "buttons": [
                {
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        'pdf',
                        'print'
                    ]
                }
            ]
        });

});
</script>
<script type="text/javascript">
    $(document).ready(function() {

    $.fn.dataTable.ext.errMode = 'none';

        $('#player2table').DataTable({
            'bFilter':false,
             "processing": true,
             "ajax":{
                     "url": '<?php echo asset('my-admin/match_player2/'.$mkey.'/'.$team2);?>',
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
             "dom": 'lBfrtip',
             "lengthMenu": [[50,100,1000,10000 ], [50,100,1000,10000]],
             "buttons": [
                {
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        'pdf',
                        'print'
                    ]
                }
            ]
        });

});
</script>


<script>


function get_fantasy_matches(value){
    var fantasy_type = value;
    $.ajax({
    type:'POST',
    url:'<?php echo asset('my-admin/get_fantasy_matches');?>',
    data:'_token=<?php echo csrf_token();?>&fantasy_type='+fantasy_type,
    success:function(data){
        $('#matckey').html('<option value="">Select Match</option>');

        for(var i = 0; i < data.length; i++) {
            // alert(data[i]['id']);
            $('#matckey').append('<option value="'+data[i]['matchkey']+'">'+data[i]['name']+'</option>');
        }

    }
    });
}

$(window).ready(
    function() {
        var fantasy_type = $('#fantasy_type').val();
        get_fantasy_matches(fantasy_type);
    }
);
</script>

@endsection('content')
