@extends('main')

@section('heading')
    Full Series Detail
@endsection('heading')

@section('sub-heading')
    Change Users Team
@endsection('sub-heading')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
           <div class="card-body" style="overflow:auto;" >
           <div class="card-title">Change players</div>
           <h5 id="credit_status">Credit Left: 0</h5>

            @if( !empty($data) )
                <?php $i = 1; ?>
                @foreach($data as $d)
                Team <?php echo $i; ?>: 
                    <h5 id="<?php echo str_replace(' ', '_', $d->team); ?>">
                    {{$d->team}}
                    </h5>                    
                    <h6 id="<?php echo str_replace(' ', '_', $d->team); ?>_count">{{$d->team_count}}</h6>
                    <?php $i = $i + 1; ?>
                @endforeach
            @endif

           <hr>
           <div style="text-align:center">
             @if ($message = Session::get('success'))
                 <div class="alert alert-success">
                     <p>{{ $message }}</p>
                 </div>
             @elseif($message = Session::get('warning'))
                 <div class="alert alert-warning">
                     <p>{{ $message }}</p>
                 </div>
             @elseif($message = Session::get('danger'))
                 <div class="alert alert-danger">
                     <p>{{ $message }}</p>
                 </div>
             @endif      
          </div>
          <form method="post"team1 action="{{ action('ContestFullDetailController@update_change_team',[$teamid,$matchkeyid,$Uid]) }}" onsubmit="return check_credit_limit()">
                {{csrf_field()}}
                <button type="submit" class="btn btn-primary pull-right">continue</button>
        
            
          <input type="hidden"  id="matchkeyid" value="<?php echo $matchkeyid;?>">
          <input type="hidden"  id="Uid" value="<?php echo $Uid;?>">
          <input type="hidden"  id="teamid" value="<?php echo $teamid;?>">
          <table id="changeteam" class="table table-striped table-bordered dataTable no-footer dtr-inline" cellspacing="0" width="100%" role="grid" aria-describedby="demo-dt-basic_info" style="width: 100%;">
            <thead>
            <tr>
              <div class="fff"></div>
              <th>Sno.</th>
            <th>Image</th>
            <th>Players Name</th>
            <th>Team Name</th>
            <th>Role</th>
            <th>Points</th>
            <th>Credit</th>
            <th>In Playing 11</th>
            <th>Action</th>
            </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
          </form>
         </div>
         </div>
      </div>
    </div>
    </div>
   </div>


<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {    
   var matchkey = document.getElementById('matchkeyid').value;
   var Uid = document.getElementById('Uid').value;
   var teamid = document.getElementById('teamid').value;
    $.fn.dataTable.ext.errMode = 'none';
        $('#changeteam').DataTable({
        "processing": true,
            "serverSide": true,            
            "lengthChange": false,
            "ajax":{
                     "url": '<?php echo URL::asset('/my-admin/changeteam_datatable');?>?&matchkey='+matchkey+'&Uid='+Uid+'&teamid='+teamid,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
                "lengthMenu": [[50,100,1000,10000 ], [50,100,1000,10000]],
            "columns": [
                { "data": "s_no" },
                { "data": "image" },
                { "data": "name" },
                { "data": "team" },
                { "data": "role" },
                { "data": "points" },
                { "data": "credit" },
                { "data": "in_playing_11" },
                { "data": "action" },
            ]  

        });
        
});
</script>

<!-- check player credits before submit -->
<script>
var credit_limit = 0;
var used_credit = 0;

$(document).on('click', 'input[name="playing[]"]', function() {
    
    var player_credit = $(this).attr('data-credit');
    var player_team = $(this).attr('data-team');

    if( $(this).is(':checked') ) {

        if( credit_limit - Number(player_credit) < 0) {
            $(this).prop('checked', false);
            alert('Credit Exceeded');
        } else {
            
            last_count = $('#'+player_team.replaceAll(' ', '_')+'_count').html();

            if(Number(last_count) < 7) { // 0 to 6 == 7
                    
                used_credit += Number(player_credit);
                credit_limit -= Number(player_credit);   

                $('#'+player_team.replaceAll(' ', '_')+'_count').html(Number(last_count) + 1);
            } else  {
                $(this).prop('checked', false);
                alert('You can only choose maximum of 7 players from a team');
            }   
        }
        
    } else {
        used_credit -= Number(player_credit);

        credit_limit += Number(player_credit);

        last_count = $('#'+player_team.replaceAll(' ', '_')+'_count').html();        
        $('#'+player_team.replaceAll(' ', '_')+'_count').html(Number(last_count) - 1);    

    }

    $('#credit_status').text('Credit Left: ' + credit_limit);
});

function check_credit_limit() {
    if(used_credit <= 100) {            
        return true;
    } else {
        alert('Credit Limit Exceeded;')
        return false;
    }

}

// $(document).on('click', 'th', function() {
//     // credit_limit = 100;
//     used_credit = 0;
//     $('#credit_status').text('Credit Left: ' + credit_limit);
//     $("h6").html(0);
// });

</script>

@endsection