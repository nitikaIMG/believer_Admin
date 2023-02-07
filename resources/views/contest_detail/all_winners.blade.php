@extends('main')

@section('heading')
    Full Series Detail
@endsection('heading')

@section('sub-heading')
    View All Joined Users
@endsection('sub-heading')

@section('content')


<div class="card mb-4">
    <div class="card-header">View Joined Users</div>
    <div class="card-body">
            
            
            @include('alert_msg')
            
            <input type="hidden"  id="challngeid" value="<?php echo $challengeid;?>">
            <input type="hidden"  id="matchkey" value="<?php echo $matchkey;?>">
            <div class="datatable table-responsive overflow-auto">
            <table class="table table-bordered table-striped table-hover text-nowrap" id="userssstable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-toggle="tooltip" title="Serial Number">#</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th data-toggle="tooltip" title="Pan Verify Status">P. V. S.</th>
                        <th data-toggle="tooltip" title="Bank Verify Status">B. V. S.</th>
                        <th data-toggle="tooltip" title="Mobile Verify Status">M. V. S.</th>
                        <th data-toggle="tooltip" title="Email Verify Status">E. V. S.</th>
                        <th>Rank</th>
                        <th>Transaction ID</th>
                        <th>Points</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th data-toggle="tooltip" title="Serial Number">#</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th data-toggle="tooltip" title="Pan Verify Status">P. V. S.</th>
                        <th data-toggle="tooltip" title="Bank Verify Status">B. V. S.</th>
                        <th data-toggle="tooltip" title="Mobile Verify Status">M. V. S.</th>
                        <th data-toggle="tooltip" title="Email Verify Status">E. V. S.</th>
                        <th>Rank</th>
                        <th>Transaction ID</th>
                        <th>Points</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
<script type="text/javascript">
    $("#userssstable").draggable({
      cursor: "move",
      containment: "table-bordered",
      stop: function() {
        if($("#userssstable").position().left < 1){
            example = $('#userssstable').width();
            table = $('.table-bordered').width()
            rightx = example-table;
            $("#userssstable").css("left", $("#userssstable").position().left);
            if($("#userssstable").position().left< -(rightx+3)){
              $("#userssstable").css("left", "auto");
            }
            $("#userssstable").css("right", (rightx+3));

            // alert($('#userssstable').width());

          }else{
            $("#userssstable").css("left", "0px");
          }
          $("#userssstable").css("top", "0px");
          $("#userssstable").css("bottom", "0px");
      }
  });
  </script>
<script type="text/javascript">
    $(document).ready(function() {    
   var challngeid = document.getElementById('challngeid').value;
   var matchkey = document.getElementById('matchkey').value;
    $.fn.dataTable.ext.errMode = 'none';
        $('#userssstable').DataTable({
            'bFilter':false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/viewjoinwinners_datatable');?>?&challngeid='+challngeid+'&matchkey='+matchkey,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "s_no" },
                { "data": "username" },
                { "data": "email" },
                { "data": "mobile" },
                { "data": "pan_verify" },
                { "data": "bank_verify" },
                { "data": "email_verify" },
                { "data": "mobile_verify" },
                { "data": "rank" },
                { "data": "transaction_id" },
                { "data": "points" },
                { "data": "amount" },
                { "data": "action" }
            ]  

        });
        
});
</script>
@endsection('content')
