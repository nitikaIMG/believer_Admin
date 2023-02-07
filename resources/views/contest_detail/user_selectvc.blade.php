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
          <form method="post"team1 action="{{ action('ContestFullDetailController@update_change_team2',[$teamid,$matchkeyid,$Uid]) }}">
                {{csrf_field()}}
                <button type="submit" class="btn btn-primary pull-right">Update</button>
        
            
          <input type="hidden"  id="matchkeyid" value="<?php echo $matchkeyid;?>">
          <input type="hidden"  id="Uid" value="<?php echo $Uid;?>">
          <input type="hidden"  id="teamid" value="<?php echo $teamid;?>">
          <table id="changeteam" class="table table-striped table-bordered dataTable no-footer dtr-inline" cellspacing="0" width="100%" role="grid" aria-describedby="demo-dt-basic_info" style="width: 100%;">
            <thead>
            <tr>
              <div class="fff"></div>
              <th rowspan="2">Sno.</th>
            <th rowspan="2">Image</th>
            <th rowspan="2">Players Name</th>
            <th rowspan="2">Team Name</th>
            <th rowspan="2">Role</th>
            <th rowspan="2">Points</th>
            <th colspan="2">Action</th>
              
            </tr>
            <tr>
              <div class="fff"></div>
            <th>VC</th>
            <th>C</th>
              
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
   var players = '<?php echo $ds; ?>';
    $.fn.dataTable.ext.errMode = 'none';
        $('#changeteam').DataTable({
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/update_changeteam_datatable');?>',
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}",matchkey:matchkey,Uid:Uid,teamid:teamid,players:players}
                   },
                "lengthMenu": [[50,100,1000,10000 ], [50,100,1000,10000]],
            "columns": [
                { "data": "s_no" },
                { "data": "image" },
                { "data": "name" },
                { "data": "team" },
                { "data": "role" },
                { "data": "points" },
                { "data": "VC" },
                { "data": "C" },
            ]  

        });
        
});
</script>
<script>
  function getdata(){
    $('input[name=playingvc]').attr('disabled',false);
  }
  function getdata1(){
    $('input[name=playingc]').attr('disabled',false);
  }
</script>
@endsection