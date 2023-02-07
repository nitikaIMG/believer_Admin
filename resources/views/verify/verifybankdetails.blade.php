@extends('main')

@section('heading')
    Verification Manager
@endsection('heading')

@section('sub-heading')
    Bank Account Verification Requests List
@endsection('sub-heading')

@section('content')
<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('VerificationController@verifybankaccount')?>">
         <?php
      $status="0";$name="";$email="";$pannumber="";$mobile="";
      if(isset($_GET['email'])){
        $email = $_GET['email'];
      }
      if(isset($_GET['name'])){
        $name = $_GET['name'];
      }
      if(isset($_GET['status'])){
        $status = $_GET['status'];
      }
      if(isset($_GET['pannumber'])){
        $pannumber = $_GET['pannumber'];
      }
      if(isset($_GET['mobile'])){
        $mobile = $_GET['mobile'];
      }
     ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0 align-items-end">

                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    {{ Form::label('Status','Status',array('class'=>'control-label text-bold'))}}
                                    <select class="form-control form-control-solid p-1 selectpicker show-tick" data-container="body" data-live-search="true" title="Select Status" data-hide-disabled="true" name="status" id="status">
                                        <option value="">Select Status</option>
                                        <option value="1" <?php if($status=='1'){ echo 'selected'; }?>>Activated</option>
                                        <option value="0" <?php if($status=='0'){ echo 'selected'; }?>>Pending</option>
                                        <option value="2" <?php if($status=='2'){ echo 'selected'; }?>>Cancel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    {{ Form::label('Search By Name','Search By Name',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('name',$name,array('value'=>'','placeholder'=>'Search By Name','id'=>'name','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Search By Email','Search By Email',array('class'=>'control-label text-bold'))}}</br>
                                    {{ Form::text('email',$email,array('value'=>'','placeholder'=>'Search By Email','id'=>'email','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Search By Mobile','Search By Mobile',array('class'=>'control-label text-bold'))}}</br>
                                    {{ Form::text('mobile',$mobile,array('value'=>'','placeholder'=>'Search By Mobile','id'=>'mobile','class'=>'form-control form-control-solid', 'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');", 'maxlength' => "10", 'pattern' => "[1-9]{1}[0-9]{9}"))}}
                                </div>
                            </div>
                            <div class="col-md-auto text-right mb-md-3">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="<?php echo action('VerificationController@verifybankaccount')?>" class="btn text-uppercase rounded-pill btn-sm btn-warning"><i class="far fa-undo" ></i>&nbsp; Reset</a>


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
    <div class="card-header">Bank Account Verification Requests List</div>
    <div class="card-body">
    
        @include('alert_msg')



        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="dataTable3" width="100%" cellspacing="0">
                <thead>
            <tr>
              <th>Sno.</th>
              <th>UID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile No.</th>
              <th>Account Number</th>
              <th>IFSC Code</th>
              <th>Bank Name</th>
              <th>Branch</th>
              <th>State</th>
              <th>Image</th>
              <th>Status</th>
              <th>Uploading Date</th>
              <th>Action</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
            <th>Sno.</th>
              <th>UID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile No.</th>
              <th>Account Number</th>
              <th>IFSC Code</th>
              <th>Bank Name</th>
              <th>Branch</th>
              <th>State</th>
              <th>Image</th>
              <th>Status</th>
              <th>Uploading Date</th>
              <th>Action</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
    var status = $('#status').val();
    var name = $('#name').val();
    var email = $('#email').val();
    var mobile = $('#mobile').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#dataTable3').DataTable({
            'bFilter':false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo asset('my-admin/verifybankaccount_table');?>?status='+status+'&name='+name+'&email='+email+'&mobile='+mobile,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "sno"},
                { "data": "id"},
                { "data": "username" },
                { "data": "email" },
                { "data": "mobile" },
                { "data": "accno" },
                { "data": "ifsc" },
                { "data": "bankname" },
                { "data": "bankbranch" },
                { "data": "state" },
                { "data": "image" },
                { "data": "status" },
                { "data": "upload" },
                { "data": "action" }
            ]

        });

});
</script>
<script>
$(document).ready(function(){
$.ajaxSetup({
headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});
});

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
          url:'<?php echo asset('my-admin/muldelete');?>',
          data:datavar,
success:function(data){
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
