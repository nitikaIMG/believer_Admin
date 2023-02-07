@extends('main')

@section('heading')
    Match Manager
@endsection('heading')

@section('sub-heading')
    View All Matches
@endsection('sub-heading')

@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          {{ Form::open(array('url' => 'my-admin/allmatches', 'method' => 'get' ))}}
                  <?php
                    $name="";$status="";
                    if(isset($_GET['name'])){
                      $name = $_GET['name'];
                    }
                    if(isset($_GET['status'])){
                      $status = $_GET['status'];
                    }
                  ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">
                        
                            <div class="col-md">
                                <div class="form-group my-3">
                                {{ Form::label('Match Name','Match Name',array('class'=>'control-label text-bold'))}}
                                {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Match Name','id'=>'name1','class'=>'form-control form-control-solid'))}}

                                </div>
                            </div>

                            <div class="col-md">
                                <div class="form-group my-3">
                                   {{ Form::label('Match Status','Match Status',array('class'=>'control-label text-bold'))}}
                                    <select class="form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Status" data-hide-disabled="true" name="status" id="status">
                                    <option value="" class="d-none">Select Status</option>
                                    <option value="complete"<?php if($status=='complete'){ echo 'Selected'; }?>>Completed</option>
                                    <option value="launched"<?php if($status=='launched'){ echo 'Selected'; }?>>Launched</option>
                                    <option value="pending"<?php if($status=='pending'){ echo 'Selected'; }?>>Pending Winner</option>
                                    <option value="live"<?php if($status=='live'){ echo 'Selected'; }?>>Live Matches</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-auto mt-md-5 mt-0 text-right">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="<?php echo action('MatchController@allmatches')?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
    <div class="card-header">View All Matches</div>
    <div class="card-body">


        @include('alert_msg')


        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="allmatches_datatable" width="100%" cellspacing="0">
                <thead>
            <tr>

              <th>Sno.</th>
              <th>Start Date</th>
              <th>Match</th>
              <th>Launch Status</th>
              <th>Final Status</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th>Sno.</th>
                    <th>Start Date</th>
                    <th>Match</th>
                    <th>Launch Status</th>
                    <th>Final Status</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </tfoot>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    var name = $('#name1').val();
    var status = $('#status').val();
        $('#allmatches_datatable').DataTable({
            'bFilter': false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/allmatches_datatable');?>?name='+name+'&status='+status,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
                   "dom": 'lBfrtip',
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
           ],
            "columns": [
                { "data": "id" },
                { "data": "start_date" },
                { "data": "name" },
                { "data": "launch_status" },
                { "data": "final_status" },
                { "data": "status" },
                { "data": "action" }
            ]

        });

});
</script>
<script>
function muldelete() {
var p=[];
$.each($("input[name='checkCat']:checked"), function(){
p.push($(this).val());
});
if(p!=""){
var datavar = '_token=<?php echo csrf_token();?>&hg_cart='+p;
var ok=confirm('Are you you want to delete this data');
if(ok){
$.ajax({
          type:'POST',
          url:'<?php echo url::asset('my-admin/match_muldelete');?>',
          data:datavar,
success:function(data){
  //alert(data);
if(data==1){
window.location.reload();
}
          }
       });
}
}
else{
Swal.fire('Please Select Series to delete');
}
}
</script>
@endsection('content')
